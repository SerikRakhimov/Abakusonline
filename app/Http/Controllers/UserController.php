<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\Item;
use App\Models\Main;
use App\Models\Project;
use App\Models\Access;
use App\Rules\IsLatinUser;
use App\Rules\IsLowerUser;
use App\Rules\IsOneWordUser;
use App\Rules\IsUniqueAccess;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected function rules()
    {
        //c Похожие строки и в RegisterController.php
        // В частности: в этом файле использовать такие проверки
        //     'password' => ['required', 'string', 'min:8'],
        //    'confirm_password' => ['min:8','same:password'],
        return [
            'name' => ['required', 'string', 'max:255', 'unique:users', new IsOneWordUser(), new IsLatinUser(), new IsLowerUser()],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['min:8', 'same:password'],
        ];
    }

    protected function name_rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:users', new IsOneWordUser(), new IsLatinUser(), new IsLowerUser()],
        ];
    }

    protected function email_rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ];
    }

    protected function password_rules()
    {
        return [
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['min:8', 'same:password'],
        ];
    }

    function index()
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $users = User::orderBy('name');
        session(['users_previous_url' => request()->url()]);
        return view('user/index', ['users' => $users->paginate(60)]);
    }

    function show(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            if (GlobalController::glo_user_id() != $user->id) {
                return redirect()->route('project.all_index');
            }
        }
        // $is_delete = true - можно удалить пользователя
        // $is_delete = false - нельзя удалить пользователя
        $is_delete = $user->isAdmin() == false;
        if ($is_delete) {
            $exists = Project::where('user_id', $user->id)->exists();
            if ($exists) {
                $is_delete = false;
            } else {
                $exists = Access::where('user_id', $user->id)->exists();
                if ($exists) {
                    $is_delete = false;
                } else {
                    $exists = Item::where('created_user_id', $user->id)->orWhere('updated_user_id', $user->id)->exists();
                    if ($exists) {
                        $is_delete = false;
                    } else {
                        $exists = Main::where('created_user_id', $user->id)->orWhere('updated_user_id', $user->id)->exists();
                        if ($exists) {
                            $is_delete = false;
                        }
                    }
                }
            }
        }
        return view('user/show', ['type_form' => 'show', 'user' => $user, 'is_delete' => $is_delete]);
    }


    function create()
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        return view('user/edit');
    }

    function store(Request $request)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $request->validate($this->rules());

        // установка часового пояса нужно для сохранения времени
        date_default_timezone_set('Asia/Almaty');


//        При добавлении записи/создания пользователя
        try {
            // начало транзакции
            DB::transaction(function ($r) use ($request) {

                $user = new User($request->except('_token', '_method'));

               // Добавление/сохранение в users
                $this->set($request, $user);

                // Добавление/сохранение в items/mains
                // В проект "Личный кабинет пользователя"
                $this->save_to_project_users($user);

            }, 3);  // Повторить три раза, прежде чем признать неудачу
            // окончание транзакции
        } catch (Exception $exc) {
            //return trans('transaction_not_completed') . ": " . $exc->getMessage();
            return view('message', ['message' => trans('main.transaction_not_completed') . ": " . $exc->getMessage()]);
        }

        //https://laravel.demiart.ru/laravel-sessions/
        if ($request->session()->has('users_previous_url')) {
            return redirect(session('users_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function update(Request $request, User $user)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        if ($user->name != $request->name) {
            $request->validate($this->name_rules());
        }
        if ($user->email != $request->email) {
            $request->validate($this->email_rules());
        }
        if ($user->password != $request->password) {
            $request->validate($this->password_rules());
        }

        $data = $request->except('_token', '_method');

        $user->fill($data);

        $this->set($request, $user);
        if (Auth::user()->isAdmin()) {
            if ($request->session()->has('users_previous_url')) {
                return redirect(session('users_previous_url'));
            } else {
                return redirect()->back();
            }
        } else {
            return redirect()->route('user.show', ['user' => $user]);
        }
    }

    function set(Request $request, User &$user)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->is_admin = false;
        $user->save();
    }

    function save_to_project_users(User $user)
    {
        //ItemController::ext_store_ext();
    }

    function edit(User $user)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        return view('user/edit', ['user' => $user, 'change_password' => false]);
    }

    function change_password(User $user)
    {
        if (GlobalController::glo_user_id() != $user->id) {
            return redirect()->route('project.all_index');
        }
        return view('user/edit', ['user' => $user, 'change_password' => true]);
    }

    function delete_question(User $user)
    {
        // Нельзя удалить пользователя Админа
        if ($user->isAdmin() == true) {
            abort(404);
        } else {
            return view('user/show', ['type_form' => 'delete_question', 'user' => $user]);
        }
    }

    function delete(Request $request, User $user)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $user->delete();

        if ($request->session()->has('users_previous_url')) {
            return redirect(session('users_previous_url'));
        } else {
            return redirect()->back();
        }
    }

}
