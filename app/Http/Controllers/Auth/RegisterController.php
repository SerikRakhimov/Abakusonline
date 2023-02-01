<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Rules\IsLatinEmail;
use App\Rules\IsLatinUser;
use App\Rules\IsLowerEmail;
use App\Rules\IsLowerUser;
use App\Rules\IsOneWordEmail;
use App\Rules\IsOneWordUser;
use \App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'unique:users', new IsOneWordUser(), new IsLatinUser(), new IsLowerUser()],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', new IsOneWordEmail(), new IsLatinEmail(), new IsLowerEmail()],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Похожие строки в RegisterController::create() и в UserController::set()
        //$user = new User($request->except('_token', '_method'));
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => false,
        ]);
        // Похожие строки в RegisterController::create() и в UserController::store()
        // начало транзакции
        DB::transaction(function ($r) use ($user) {
            // Добавление/сохранение в items/mains
            // В проект "Личный кабинет пользователя"
            UserController::save_to_project_users($user);

        }, 3);  // Повторить три раза, прежде чем признать неудачу
        // окончание транзакции
//        return User::create([
//            'name' => $data['name'],
//            'email' => $data['email'],
//            'password' => Hash::make($data['password']),
//        ]);
        return $user;
    }
}
