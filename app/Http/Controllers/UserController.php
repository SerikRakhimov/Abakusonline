<?php

namespace App\Http\Controllers;

use App\Rules\IsLatinEmail;
use App\Rules\IsLowerEmail;
use Illuminate\Support\Facades\App;
use App\Models\Link;
use App\Models\Item;
use App\Models\Main;
use App\Models\Project;
use App\Models\Access;
use App\Rules\IsLatinUser;
use App\Rules\IsLowerUser;
use App\Rules\IsOneWordUser;
use App\Rules\IsOneWordEmail;
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', new IsOneWordEmail(), new IsLatinEmail(), new IsLowerEmail()],
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

        // Похожие строки в RegisterController::create() и в UserController::store()
        // При добавлении записи/создания пользователя
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
        // Похожие строки в RegisterController::create() и в UserController::set()
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->is_admin = false;
        $user->save();
    }

    // Добавление пользователя из users в base Пользователи
    // Похожие строки в ItemController::save_main() и в UserController::save_to_project_users()
    static function save_to_project_users(User $user)
    {
        $username = $user->name;
        $email = $user->email;
        // Проверка сушествования и уникальности записи о пользователе
        $usersetup_name_link_id = env('USERSETUP_NAME_LINK_ID');
        if ($usersetup_name_link_id != '') {
            // По имени пользователя
            $main_found = Main::where('link_id', $usersetup_name_link_id)
                ->whereHas('parent_item', function ($query) use ($username) {
                    $query->where('name_lang_0', '=', $username);
                })->first();
            if (!$main_found) {
                $usersetup_email_link_id = env('USERSETUP_EMAIL_LINK_ID');
                if ($usersetup_email_link_id != '') {
                    // По e-mail
                    $main_found = Main::where('link_id', $usersetup_email_link_id)
                        ->whereHas('parent_item', function ($query) use ($username) {
                            $query->where('name_lang_0', '=', $username);
                        })->first();
                    if (!$main_found) {
                        $usersetup_base_id = env('USERSETUP_BASE_ID');
                        if ($usersetup_base_id != '') {
                            $usersetup_project_id = env('USERSETUP_PROJECT_ID');
                            if ($usersetup_project_id != '') {
                                $link_name = Link::find($usersetup_name_link_id);
                                $link_email = Link::find($usersetup_email_link_id);
                                // Нужно использовать '&&', чтобы не было ошибки "link не могу преобразовать в int"
                                if ($link_name && $link_email) {
                                    // Создание записи в base Пользователи
                                    // создать новую запись
                                    $item = new Item();
                                    $item->base_id = $usersetup_base_id;
                                    $item->project_id = $usersetup_project_id;
                                    $item->code = uniqid($item->id . '_', true);
                                    // Расчет вычисляемого наименования
                                    $item->name_lang_0 = $username;
                                    $item->name_lang_1 = $username;
                                    $item->name_lang_2 = $username;
                                    $item->name_lang_3 = $username;
                                    $item->created_user_id = $user->id;
                                    $item->updated_user_id = $user->id;
                                    // Нужно, чтобы id было
                                    $item->save();

                                    // Поиск (создание) $item->id наименования
                                    // Поиск в таблице items значение с таким же названием и base_id
                                    $item_find = Item::where('base_id', $link_name->parent_base_id)
                                        ->where('project_id', $usersetup_project_id)
                                        ->where('name_lang_0', $username)
                                        ->first();
                                    // если не найдено
                                    if (!$item_find) {
                                        // создание новой записи в items
                                        $item_find = new Item();
                                        $item_find->base_id = $link_name->parent_base_id;
                                        // Похожая строка вверху и внизу
                                        $item_find->code = uniqid($item_find->base_id . '_', true);
                                        // присваивание полям наименование строкового значение числа
                                        $i = 0;
                                        foreach (config('app.locales') as $lang_key => $lang_value) {
                                            $item_find['name_lang_' . $lang_key] = $username;
                                        }
                                        $item_find->project_id = $usersetup_project_id;
                                        // при создании записи "$item->created_user_id" заполняется
                                        $item_find->created_user_id = $user->id;
                                        $item_find->updated_user_id = $user->id;
                                        $item_find->save();

                                        // Создание новой записи в таблице mains
                                        $main = new Main();
                                        $main->link_id = $usersetup_name_link_id;
                                        $main->child_item_id = $item->id;
                                        $main->parent_item_id = $item_find->id;
                                        $main->created_user_id = $user->id;
                                        $main->updated_user_id = $user->id;
                                        $main->save();
                                    }

                                    // Поиск (создание) $item->id e-mail
                                    // Поиск в таблице items значение с таким же названием и base_id
                                    $item_find = Item::where('base_id', $link_email->parent_base_id)
                                        ->where('project_id', $usersetup_project_id)
                                        ->where('name_lang_0', $email)
                                        ->first();
                                    // если не найдено
                                    if (!$item_find) {
                                        // создание новой записи в items
                                        $item_find = new Item();
                                        $item_find->base_id = $link_email->parent_base_id;
                                        // Похожая строка вверху и внизу
                                        $item_find->code = uniqid($item_find->base_id . '_', true);
                                        // присваивание полям наименование строкового значение числа
                                        $i = 0;
                                        foreach (config('app.locales') as $lang_key => $lang_value) {
                                            $item_find['name_lang_' . $lang_key] = $email;
                                        }
                                        $item_find->project_id = $usersetup_project_id;
                                        // при создании записи "$item->created_user_id" заполняется
                                        $item_find->created_user_id = $user->id;
                                        $item_find->updated_user_id = $user->id;
                                        $item_find->save();

                                        // Создание новой записи в таблице mains
                                        $main = new Main();
                                        $main->link_id = $usersetup_email_link_id;
                                        $main->child_item_id = $item->id;
                                        $main->parent_item_id = $item_find->id;
                                        $main->created_user_id = $user->id;
                                        $main->updated_user_id = $user->id;
                                        $main->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function edit(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        return view('user/edit', ['user' => $user, 'change_password' => false]);
    }

    function change_password(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            if (GlobalController::glo_user_id() != $user->id) {
                return redirect()->route('project.all_index');
            }
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
