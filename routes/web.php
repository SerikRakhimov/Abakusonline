<?php
//
//use Illuminate\Support\Facades\Route;
//
///*
//|--------------------------------------------------------------------------
//| Web Routes
//|--------------------------------------------------------------------------
//|
//| Here is where you can register web routes for your application. These
//| routes are loaded by the RouteServiceProvider within a group which
//| contains the "web" middleware group. Now create something great!
//|
//*/
//
//Route::get('/', function () {
//    return view('welcome');
//});
//
//Auth::routes();
//
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\Project;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Session::regenerate();

//https://laravel.su/docs/5.0/validation
//
//public function authorize()
//{
//    $commentId = $this->route('comment');
//
//    return Comment::where('id', $commentId)
//        ->where('user_id', Auth::id())->exists();
//}

Route::get('/', function () {
    GlobalController::start_artisan();
    // массив "glo_menu_main" показывает, что четыре поля наименований хранятся в bases и items
    // ['1', '2', '3', '4'] - тут разницы нет, какие значения хранятся; главное, чтобы что-то хранилось
    //$array = ['1', '2', '3', '4'];
    //Session::put('glo_menu_main', $array);

    // массив "glo_menu_lang" показывает какие языки используются в меню
    // должно входить(не превышать) во множество 'locales'(config\app.php)
    //$array = ['ru', 'kz', 'en'];
    //Session::put('glo_menu_lang', $array);

    // массив "glo_menu_save" показывает, какие языки хранятся в bases и items
    // должно входить(не превышать) во множество массива 'glo_menu_lang'
    //$array = ['ru', 'kz', 'en'];
    //Session::put('glo_menu_save', $array);

    // текущий язык программы
    // должен совпадать с аналогичным значением в config\app.php
    Session::put('locale', 'ru');

    $user = \App\Models\User::on()->first();
    if (!$user) {
        // создать новую запись для админа, если таблица users пуста
        $user = new \App\Models\User();
        $user->name = 'admin';
        $user->email = 'admin@abakusonline.com';
        $user->password = Hash::make('admin715331');
        $user->is_admin = true;
        $user->is_moderator = true;
        $user->save();
    }

    if (env('MAIL_ENABLED') == 'yes') {
        $appname = config('app.name', 'Abakus');
        try {
            Mail::send(['html' => 'mail/login_site'], ['remote_addr' => $_SERVER['REMOTE_ADDR'],
                'http_user_agent' => $_SERVER['HTTP_USER_AGENT'], 'appname' => $appname],
                function ($message) use ($appname) {
                    $message->to(env('MAIL_TO_ADDRESS_LOG', 'log@rsb0807.kz'), '')->subject("Вход на сайт '" . $appname . "'");
                    $message->from(env('MAIL_FROM_ADDRESS', 'support@rsb0807.kz'), $appname);
                });
        } catch (Exception $exc) {
            return trans('error_sending_email') . ": " . $exc->getMessage();
        }
    }

    if (Auth::check()) {
        return view('welcome');
        //return view('home');
        //return redirect()->route('order.index_job_user');
    } else {
        return redirect()->route('login');
//        return redirect()->route('project.all_index');
    }

})
    ->name('/');

Route::get('/setlocale/{locale}', function ($locale) {

//    if (in_array($locale, \Config::get('app.locales'))) {   # Проверяем, что у пользователя выбран доступный язык
//        Session::put('locale', $locale);                    # И устанавливаем его в сессии под именем locale
//    }
    //App::setLocale($locale);
    if (in_array($locale, config('app.locales'))) {   # Проверяем, что у пользователя выбран доступный язык
        Session::put('locale', $locale);                    # И устанавливаем его в сессии под именем locale

        App::setLocale($locale);

        //app()->setLocale($locale);
        //App::currentLocale();
        //App::getLocale();
    }

    return redirect()->back()->withInput();                 # Редиректим его <s>взад</s> на ту же страницу

});

Route::get('/global/set_display/{display}', [GlobalController::class, 'set_display'])
    ->name('global.set_display')
    ->middleware('auth');

Route::get('/global/get_bases_from_relit_id/{relit_id}/{current_template_id}', [GlobalController::class, 'get_bases_from_relit_id'])
    ->name('global.get_bases_from_relit_id')
    ->middleware('auth');

Route::get('/global/get_links_from_relit_id/{relit_id}/{current_template_id}', [GlobalController::class, 'get_links_from_relit_id'])
    ->name('global.get_links_from_relit_id')
    ->middleware('auth');

Route::post('/home/glo_store', [HomeController::class, 'glo_store'])
    ->name('home.glo_store')
    ->middleware('auth');

// Templates
Route::get('/template/index', [TemplateController::class, 'index'])
    ->name('template.index')
    ->middleware('auth');

// "->middleware('auth')" не использовать
Route::get('/template/main_index', [TemplateController::class, 'main_index'])
    ->name('template.main_index');

Route::get('/template/show/{template}', [TemplateController::class, 'show'])
    ->name('template.show')
    ->middleware('auth');

Route::get('/template/create', [TemplateController::class, 'create'])
    ->name('template.create')
    ->middleware('auth');

Route::get('/template/edit/{template}', [TemplateController::class, 'edit'])
    ->name('template.edit')
    ->middleware('auth');

Route::post('/template/store', [TemplateController::class, 'store'])
    ->name('template.store')
    ->middleware('auth');

Route::put('/template/edit/{template}', [TemplateController::class, 'update'])
    ->name('template.update')
    ->middleware('auth');

Route::get('/template/delete_question/{template}', [TemplateController::class, 'delete_question'])
    ->name('template.delete_question')
    ->middleware('auth');

Route::delete('/template/delete/{template}', [TemplateController::class, 'delete'])
    ->name('template.delete')
    ->middleware('auth');

// Relits
Route::get('/relit/index/{template}', [RelitController::class, 'index'])
    ->name('relit.index')
    ->middleware('auth');

Route::get('/relit/show/{relit}', [RelitController::class, 'show'])
    ->name('relit.show')
    ->middleware('auth');

Route::get('/relit/create/{template}', [RelitController::class, 'create'])
    ->name('relit.create')
    ->middleware('auth');

Route::get('/relit/edit/{relit}', [RelitController::class, 'edit'])
    ->name('relit.edit')
    ->middleware('auth');

Route::post('/relit/store', [RelitController::class, 'store'])
    ->name('relit.store')
    ->middleware('auth');

Route::put('/relit/edit/{relit}', [RelitController::class, 'update'])
    ->name('relit.update')
    ->middleware('auth');

Route::get('/relit/delete_question/{relit}', [RelitController::class, 'delete_question'])
    ->name('relit.delete_question')
    ->middleware('auth');

Route::delete('/relit/delete/{relit}', [RelitController::class, 'delete'])
    ->name('relit.delete')
    ->middleware('auth');

// Moderations
Route::get('/moderation/index', [ModerationController::class, 'index'])
    ->name('moderation.index')
    ->middleware('auth');

Route::get('/moderation/show/{item}', [ModerationController::class, 'show'])
    ->name('moderation.show')
    ->middleware('auth');

Route::get('/moderation/edit/{item}', [ModerationController::class, 'edit'])
    ->name('moderation.edit')
    ->middleware('auth');

Route::put('/moderation/edit/{item}', [ModerationController::class, 'update'])
    ->name('moderation.update')
    ->middleware('auth');

Route::get('/moderation/delete_question/{item}', [ModerationController::class, 'delete_question'])
    ->name('moderation.delete_question')
    ->middleware('auth');

Route::delete('/moderation/delete/{item}', [ModerationController::class, 'delete'])
    ->name('moderation.delete')
    ->middleware('auth');

// Users
Route::get('/user/index', [UserController::class, 'index'])
    ->name('user.index')
    ->middleware('auth');

Route::get('/user/show/{user}', [UserController::class, 'show'])
    ->name('user.show')
    ->middleware('auth');

Route::get('/user/create', [UserController::class, 'create'])
    ->name('user.create')
    ->middleware('auth');

Route::get('/user/edit/{user}', [UserController::class, 'edit'])
    ->name('user.edit')
    ->middleware('auth');

Route::get('/user/change_password/{user}', [UserController::class, 'change_password'])
    ->name('user.change_password')
    ->middleware('auth');

Route::post('/user/store', [UserController::class, 'store'])
    ->name('user.store')
    ->middleware('auth');

Route::put('/user/edit/{user}', [UserController::class, 'update'])
    ->name('user.update')
    ->middleware('auth');

Route::get('/user/delete_question/{user}', [UserController::class, 'delete_question'])
    ->name('user.delete_question')
    ->middleware('auth');

Route::delete('/user/delete/{user}', [UserController::class, 'delete'])
    ->name('user.delete')
    ->middleware('auth');

// Roles
Route::get('/role/index/{template}', [RoleController::class, 'index'])
    ->name('role.index')
    ->middleware('auth');

Route::get('/role/show/{role}', [RoleController::class, 'show'])
    ->name('role.show')
    ->middleware('auth');

Route::get('/role/create/{template}', [RoleController::class, 'create'])
    ->name('role.create')
    ->middleware('auth');

Route::get('/role/edit/{role}', [RoleController::class, 'edit'])
    ->name('role.edit')
    ->middleware('auth');

Route::post('/role/store', [RoleController::class, 'store'])
    ->name('role.store')
    ->middleware('auth');

Route::put('/role/edit/{role}', [RoleController::class, 'update'])
    ->name('role.update')
    ->middleware('auth');

Route::get('/role/delete_question/{role}', [RoleController::class, 'delete_question'])
    ->name('role.delete_question')
    ->middleware('auth');

Route::delete('/role/delete/{role}', [RoleController::class, 'delete'])
    ->name('role.delete')
    ->middleware('auth');

// Robas
Route::get('/roba/index_role/{role}', [RobaController::class, 'index_role'])
    ->name('roba.index_role')
    ->middleware('auth');

Route::get('/roba/index_base/{base}', [RobaController::class, 'index_base'])
    ->name('roba.index_base')
    ->middleware('auth');

Route::get('/roba/show_role/{roba}', [RobaController::class, 'show_role'])
    ->name('roba.show_role')
    ->middleware('auth');

Route::get('/roba/show_base/{roba}', [RobaController::class, 'show_base'])
    ->name('roba.show_base')
    ->middleware('auth');

Route::get('/roba/create_role/{role}', [RobaController::class, 'create_role'])
    ->name('roba.create_role')
    ->middleware('auth');

Route::get('/roba/create_base/{base}', [RobaController::class, 'create_base'])
    ->name('roba.create_base')
    ->middleware('auth');

Route::get('/roba/edit_role/{roba}', [RobaController::class, 'edit_role'])
    ->name('roba.edit_role')
    ->middleware('auth');

Route::get('/roba/edit_base/{roba}', [RobaController::class, 'edit_base'])
    ->name('roba.edit_base')
    ->middleware('auth');

Route::post('/roba/store', [RobaController::class, 'store'])
    ->name('roba.store')
    ->middleware('auth');

Route::put('/roba/edit/{roba}', [RobaController::class, 'update'])
    ->name('roba.update')
    ->middleware('auth');

Route::get('/roba/delete_question/{roba}', [RobaController::class, 'delete_question'])
    ->name('roba.delete_question')
    ->middleware('auth');

Route::delete('/roba/delete/{roba}', [RobaController::class, 'delete'])
    ->name('roba.delete')
    ->middleware('auth');

// Sets
Route::get('/set/index/{template}', [SetController::class, 'index'])
    ->name('set.index')
    ->middleware('auth');

Route::get('/set/show/{set}', [SetController::class, 'show'])
    ->name('set.show')
    ->middleware('auth');

Route::get('/set/create/{template}', [SetController::class, 'create'])
    ->name('set.create')
    ->middleware('auth');

Route::get('/set/edit/{set}', [SetController::class, 'edit'])
    ->name('set.edit')
    ->middleware('auth');

Route::post('/set/store', [SetController::class, 'store'])
    ->name('set.store')
    ->middleware('auth');

Route::put('/set/edit/{set}', [SetController::class, 'update'])
    ->name('set.update')
    ->middleware('auth');

Route::get('/set/delete_question/{set}', [SetController::class, 'delete_question'])
    ->name('set.delete_question')
    ->middleware('auth');

Route::delete('/set/delete/{set}', [SetController::class, 'delete'])
    ->name('set.delete')
    ->middleware('auth');

// Rolis
Route::get('/roli/index_role/{role}', [RoliController::class, 'index_role'])
    ->name('roli.index_role')
    ->middleware('auth');

Route::get('/roli/index_link/{link}', [RoliController::class, 'index_link'])
    ->name('roli.index_link')
    ->middleware('auth');

Route::get('/roli/show_role/{roli}', [RoliController::class, 'show_role'])
    ->name('roli.show_role')
    ->middleware('auth');

Route::get('/roli/show_link/{roli}', [RoliController::class, 'show_link'])
    ->name('roli.show_link')
    ->middleware('auth');

Route::get('/roli/create_role/{role}', [RoliController::class, 'create_role'])
    ->name('roli.create_role')
    ->middleware('auth');

Route::get('/roli/create_link/{link}', [RoliController::class, 'create_link'])
    ->name('roli.create_link')
    ->middleware('auth');

Route::get('/roli/edit_role/{roli}', [RoliController::class, 'edit_role'])
    ->name('roli.edit_role')
    ->middleware('auth');

Route::get('/roli/edit_link/{roli}', [RoliController::class, 'edit_link'])
    ->name('roli.edit_link')
    ->middleware('auth');

Route::post('/roli/store', [RoliController::class, 'store'])
    ->name('roli.store')
    ->middleware('auth');

Route::put('/roli/edit/{roli}', [RoliController::class, 'update'])
    ->name('roli.update')
    ->middleware('auth');

Route::get('/roli/delete_question/{roli}', [RoliController::class, 'delete_question'])
    ->name('roli.delete_question')
    ->middleware('auth');

Route::delete('/roli/delete/{roli}', [RoliController::class, 'delete'])
    ->name('roli.delete')
    ->middleware('auth');

// Projects
// "->middleware('auth')" не использовать
Route::get('/project/all_index', [ProjectController::class, 'all_index'])
    ->name('project.all_index');

Route::get('/project/subs_index', [ProjectController::class, 'subs_index'])
    ->name('project.subs_index')
    ->middleware('auth');

Route::get('/project/my_index', [ProjectController::class, 'my_index'])
    ->name('project.my_index')
    ->middleware('auth');

Route::get('/project/mysubs_index', [ProjectController::class, 'mysubs_index'])
    ->name('project.mysubs_index')
    ->middleware('auth');

Route::get('/project/index_template/{template}', [ProjectController::class, 'index_template'])
    ->name('project.index_template')
    ->middleware('auth');

Route::get('/project/index_user/{user}', [ProjectController::class, 'index_user'])
    ->name('project.index_user')
    ->middleware('auth');

Route::get('/project/show_template/{project}', [ProjectController::class, 'show_template'])
    ->name('project.show_template')
    ->middleware('auth');

Route::get('/project/show_user/{project}', [ProjectController::class, 'show_user'])
    ->name('project.show_user')
    ->middleware('auth');

// "->middleware('auth')" не использовать
Route::get('/project/start/{project}/{role?}', [ProjectController::class, 'start'])
    ->name('project.start');

// "->middleware('auth')" не использовать
Route::get('/project/start_check', [ProjectController::class, 'start_check'])
    ->name('project.start_check');

Route::get('/project/subs_create_form', [ProjectController::class, 'subs_create_form'])
    ->name('project.subs_create_form')
    ->middleware('auth');

Route::get('/project/subs_create/{is_request}/{project}/{role}', [ProjectController::class, 'subs_create'])
    ->name('project.subs_create')
    ->middleware('auth');

Route::get('/project/subs_delete/{project}/{role}', [ProjectController::class, 'subs_delete'])
    ->name('project.subs_delete')
    ->middleware('auth');

Route::get('/project/create_template/{template}', [ProjectController::class, 'create_template'])
    ->name('project.create_template')
    ->middleware('auth');

//Route::get('/project/create_user/{user}', [ProjectController::class, 'create_user'])
//    ->name('project.create_user')
//    ->middleware('auth');

Route::get('/project/create_template_user/{template}', [ProjectController::class, 'create_template_user'])
    ->name('project.create_template_user')
    ->middleware('auth');

Route::get('/project/edit_template/{project}', [ProjectController::class, 'edit_template'])
    ->name('project.edit_template')
    ->middleware('auth');

Route::get('/project/edit_user/{project}', [ProjectController::class, 'edit_user'])
    ->name('project.edit_user')
    ->middleware('auth');

Route::post('/project/store', [ProjectController::class, 'store'])
    ->name('project.store')
    ->middleware('auth');

Route::put('/project/edit/{project}', [ProjectController::class, 'update'])
    ->name('project.update')
    ->middleware('auth');

Route::get('/project/delete_question/{project}', [ProjectController::class, 'delete_question'])
    ->name('project.delete_question')
    ->middleware('auth');

Route::delete('/project/delete/{project}', [ProjectController::class, 'delete'])
    ->name('project.delete')
    ->middleware('auth');

Route::get('/project/calculate_bases_start/{project}/{role}', [ProjectController::class, 'calculate_bases_start'])
    ->name('project.calculate_bases_start')
    ->middleware('auth');

Route::get('/project/calculate_bases/{project}/{role}', [ProjectController::class, 'calculate_bases'])
    ->name('project.calculate_bases')
    ->middleware('auth');

// Accesses
Route::get('/access/index_project/{project}', [AccessController::class, 'index_project'])
    ->name('access.index_project')
    ->middleware('auth');

Route::get('/access/index_user/{user}', [AccessController::class, 'index_user'])
    ->name('access.index_user')
    ->middleware('auth');

Route::get('/access/index_role/{role}', [AccessController::class, 'index_role'])
    ->name('access.index_role')
    ->middleware('auth');

Route::get('/access/show_project/{access}', [AccessController::class, 'show_project'])
    ->name('access.show_project')
    ->middleware('auth');

Route::get('/access/show_user/{access}', [AccessController::class, 'show_user'])
    ->name('access.show_user')
    ->middleware('auth');

Route::get('/access/show_role/{access}', [AccessController::class, 'show_role'])
    ->name('access.show_role')
    ->middleware('auth');

Route::get('/access/create_project/{project}', [AccessController::class, 'create_project'])
    ->name('access.create_project')
    ->middleware('auth');

Route::get('/access/create_user/{user}', [AccessController::class, 'create_user'])
    ->name('access.create_user')
    ->middleware('auth');

Route::get('/access/create_role/{role}', [AccessController::class, 'create_role'])
    ->name('access.create_role')
    ->middleware('auth');

Route::get('/access/edit_project/{access}', [AccessController::class, 'edit_project'])
    ->name('access.edit_project')
    ->middleware('auth');

Route::get('/access/edit_user/{access}', [AccessController::class, 'edit_user'])
    ->name('access.edit_user')
    ->middleware('auth');

Route::get('/access/edit_role/{access}', [AccessController::class, 'edit_role'])
    ->name('access.edit_role')
    ->middleware('auth');

Route::post('/access/store', [AccessController::class, 'store'])
    ->name('access.store')
    ->middleware('auth');

Route::put('/access/edit/{access}', [AccessController::class, 'update'])
    ->name('access.update')
    ->middleware('auth');

Route::get('/access/delete_question/{access}', [AccessController::class, 'delete_question'])
    ->name('access.delete_question')
    ->middleware('auth');

Route::delete('/access/delete/{access}', [AccessController::class, 'delete'])
    ->name('access.delete')
    ->middleware('auth');

Route::get('/access/get_roles_options_from_project/{project}', [AccessController::class, 'get_roles_options_from_project'])
    ->name('access.get_roles_options_from_project')
    ->middleware('auth');

Route::get('/access/get_roles_options_from_user_project/{user}/{project}', [AccessController::class, 'get_roles_options_from_user_project'])
    ->name('access.get_roles_options_from_user_project')
    ->middleware('auth');

// Modules
Route::get('/module/index/{task}', [ModuleController::class, 'index'])
    ->name('module.index')
    ->middleware('auth');

Route::get('/module/show/{module}', [ModuleController::class, 'show'])
    ->name('module.show')
    ->middleware('auth');

Route::get('/module/create/{task}', [ModuleController::class, 'create'])
    ->name('module.create')
    ->middleware('auth');

Route::get('/module/edit/{module}', [ModuleController::class, 'edit'])
    ->name('module.edit')
    ->middleware('auth');

Route::post('/module/store', [ModuleController::class, 'store'])
    ->name('module.store')
    ->middleware('auth');

Route::put('/module/edit/{module}', [ModuleController::class, 'update'])
    ->name('module.update')
    ->middleware('auth');

Route::get('/module/delete_question/{module}', [ModuleController::class, 'delete_question'])
    ->name('module.delete_question')
    ->middleware('auth');

Route::delete('/module/delete/{module}', [ModuleController::class, 'delete'])
    ->name('module.delete')
    ->middleware('auth');

// Bases
Route::get('/base/index/{template}', [BaseController::class, 'index'])
    ->name('base.index')
    ->middleware('auth');

Route::get('/base/show/{base}', [BaseController::class, 'show'])
    ->name('base.show')
    ->middleware('auth');

Route::get('/base/create/{template}', [BaseController::class, 'create'])
    ->name('base.create')
    ->middleware('auth');

Route::get('/base/edit/{base}', [BaseController::class, 'edit'])
    ->name('base.edit')
    ->middleware('auth');

Route::post('/base/store', [BaseController::class, 'store'])
    ->name('base.store')
    ->middleware('auth');

Route::put('/base/edit/{base}', [BaseController::class, 'update'])
    ->name('base.update')
    ->middleware('auth');

Route::get('/base/delete_question/{base}', [BaseController::class, 'delete_question'])
    ->name('base.delete_question')
    ->middleware('auth');

Route::delete('/base/delete/{base}', [BaseController::class, 'delete'])
    ->name('base.delete')
    ->middleware('auth');

Route::get('/base/getBasesAll', [BaseController::class, 'getBasesAll'])
    ->name('base.getBasesAll');

// Levels
Route::get('/level/index/{template}', [LevelController::class, 'index'])
    ->name('level.index')
    ->middleware('auth');

Route::get('/level/show/{level}', [LevelController::class, 'show'])
    ->name('level.show')
    ->middleware('auth');

Route::get('/level/create/{template}', [LevelController::class, 'create'])
    ->name('level.create')
    ->middleware('auth');

Route::get('/level/edit/{level}', [LevelController::class, 'edit'])
    ->name('level.edit')
    ->middleware('auth');

Route::post('/level/store', [LevelController::class, 'store'])
    ->name('level.store')
    ->middleware('auth');

Route::put('/level/edit/{level}', [LevelController::class, 'update'])
    ->name('level.update')
    ->middleware('auth');

Route::get('/level/delete_question/{level}', [LevelController::class, 'delete_question'])
    ->name('level.delete_question')
    ->middleware('auth');

Route::delete('/level/delete/{level}', [LevelController::class, 'delete'])
    ->name('level.delete')
    ->middleware('auth');

// Links
Route::get('/link/index', [LinkController::class, 'index'])
    ->name('link.index')
    ->middleware('auth');

Route::get('/link/show/{link}', [LinkController::class, 'show'])
    ->name('link.show')
    ->middleware('auth');

Route::get('/link/create/{base}', [LinkController::class, 'create'])
    ->name('link.create')
    ->middleware('auth');

Route::get('/link/edit/{link}/{base}', [LinkController::class, 'edit'])
    ->name('link.edit')
    ->middleware('auth');

Route::post('/link/store', [LinkController::class, 'store'])
    ->name('link.store')
    ->middleware('auth');

Route::put('/link/edit/{link}', [LinkController::class, 'update'])
    ->name('link.update')
    ->middleware('auth');

Route::get('/link/delete_question/{link}', [LinkController::class, 'delete_question'])
    ->name('link.delete_question')
    ->middleware('auth');

Route::delete('/link/delete/{link}', [LinkController::class, 'delete'])
    ->name('link.delete')
    ->middleware('auth');

Route::get('/link/get_parent_parent_related_start_link_id/{base}/{link_current?}', [LinkController::class, 'get_parent_parent_related_start_link_id'])
    ->name('link.get_parent_parent_related_start_link_id')
    ->middleware('auth');

Route::get('/link/get_parent_child_related_start_link_id/{base}/{link_current?}', [LinkController::class, 'get_parent_child_related_start_link_id'])
    ->name('link.get_parent_child_related_start_link_id')
    ->middleware('auth');

Route::get('/link/get_parent_output_calculated_table_set_id/{base}', [LinkController::class, 'get_parent_output_calculated_table_set_id'])
    ->name('link.get_parent_output_calculated_table_set_id')
    ->middleware('auth');

Route::get('/link/get_parent_enabled_boolean_value_link_id/{base}', [LinkController::class, 'get_parent_enabled_boolean_value_link_id'])
    ->name('link.get_parent_enabled_boolean_value_link_id')
    ->middleware('auth');

Route::get('/link/get_parent_selection_calculated_table_set_id/{base}', [LinkController::class, 'get_parent_selection_calculated_table_set_id'])
    ->name('link.get_parent_selection_calculated_table_set_id')
    ->middleware('auth');

Route::get('/link/get_tree_from_link_id/{link_start}', [LinkController::class, 'get_tree_from_link_id'])
    ->name('link.get_tree_from_link_id')
    ->middleware('auth');

Route::get('/link/get_parent_base_id_from_link_id/{link}', [LinkController::class, 'get_parent_base_id_from_link_id'])
    ->name('link.get_parent_base_id_from_link_id')
    ->middleware('auth');

Route::get('/link/get_parent_base_id_from_set_id/{set}', [LinkController::class, 'get_parent_base_id_from_set_id'])
    ->name('link.get_parent_base_id_from_set_id')
    ->middleware('auth');

Route::get('/link/get_links_from_set_id_link_from_parent_base/{set_id}', [LinkController::class, 'get_links_from_set_id_link_from_parent_base'])
    ->name('link.get_links_from_set_id_link_from_parent_base')
    ->middleware('auth');

Route::get('/link/get_links_from_link_id_parent_base/{link_id}', [LinkController::class, 'get_links_from_link_id_parent_base'])
    ->name('link.get_links_from_link_id_parent_base')
    ->middleware('auth');

Route::get('/link/base_index/{base}', [LinkController::class, 'base_index'])
    ->name('link.base_index')
    ->middleware('auth');

// Items
//
//Route::get('/item/index', [ItemController::class, 'index')
//    ->name('item.index')
//    ->middleware('auth');

// "->middleware('auth')" не использовать
Route::get('/item/base_index/{base}/{project}/{role}/{relit_id}', [ItemController::class, 'base_index'])
    ->name('item.base_index');
//->middleware('auth');

//Route::get('/item/item_index/{project}/{item}/{role}/{usercode}/{relit_id}/{view_link?}/{string_link_ids_current?}/{string_item_ids_current?}/{string_all_codes_current?}/{prev_base_index_page?}/{prev_body_link_page?}/{prev_body_all_page?}/{view_ret_id?}', [ItemController::class, 'item_index'])
//    ->name('item.item_index')
//    ->middleware('auth');
Route::get('/item/item_index/{project}/{item}/{role}/{usercode}/{relit_id}/{view_link?}/{string_current?}/{prev_base_index_page?}/{prev_body_link_page?}/{prev_body_all_page?}/{view_ret_id?}', [ItemController::class, 'item_index'])
    ->name('item.item_index')
    ->middleware('auth');

Route::get('/item/show/{item}', [ItemController::class, 'show'])
    ->name('item.show')
    ->middleware('auth');

Route::get('/item/create', [ItemController::class, 'create'])
    ->name('item.create')
    ->middleware('auth');

// "->middleware('auth')" не использовать
//Route::get('/item/ext_show/{item}/{project}/{role}/{usercode}/{relit_id}/{string_link_ids_current?}/{string_item_ids_current?}/{string_all_codes_current?}/{heading?}/{base_index_page?}/{body_link_page?}/{body_all_page?}/{parent_ret_id?}/{view_link?}/{par_link?}/{parent_item?}', [ItemController::class, 'ext_show'])
//    ->name('item.ext_show');
////->middleware('auth');
Route::get('/item/ext_show/{item}/{project}/{role}/{usercode}/{relit_id}/{string_current?}/{heading?}/{base_index_page?}/{body_link_page?}/{body_all_page?}/{parent_ret_id?}/{view_link?}/{par_link?}/{parent_item?}', [ItemController::class, 'ext_show'])
    ->name('item.ext_show');
//->middleware('auth');

//Route::get('/item/ext_create/{base}/{project}/{role}/{usercode}/{relit_id}/{string_link_ids_current?}/{string_item_ids_current?}/{string_all_codes_current?}/{heading?}/{base_index_page?}/{body_link_page?}/{body_all_page?}/{parent_ret_id?}/{view_link?}/{par_link?}/{parent_item?}', [ItemController::class, 'ext_create'])
//    ->name('item.ext_create')
//    ->middleware('auth');
Route::get('/item/ext_create/{base}/{project}/{role}/{usercode}/{relit_id}/{string_current?}/{heading?}/{base_index_page?}/{body_link_page?}/{body_all_page?}/{parent_ret_id?}/{view_link?}/{par_link?}/{parent_item?}', [ItemController::class, 'ext_create'])
    ->name('item.ext_create')
    ->middleware('auth');

Route::get('/item/edit/{item}', [ItemController::class, 'edit'])
    ->name('item.edit')
    ->middleware('auth');

//Route::get('/item/ext_edit/{item}/{project}/{role}/{usercode}/{relit_id}/{string_link_ids_current?}/{string_item_ids_current?}/{string_all_codes_current?}/{heading?}/{base_index_page?}/{body_link_page?}/{body_all_page?}/{parent_ret_id?}/{view_link?}/{par_link?}/{parent_item?}', [ItemController::class, 'ext_edit'])
//    ->name('item.ext_edit')
//    ->middleware('auth');
Route::get('/item/ext_edit/{item}/{project}/{role}/{usercode}/{relit_id}/{string_current?}/{heading?}/{base_index_page?}/{body_link_page?}/{body_all_page?}/{parent_ret_id?}/{view_link?}/{par_link?}/{parent_item?}', [ItemController::class, 'ext_edit'])
    ->name('item.ext_edit')
    ->middleware('auth');

Route::post('/item/store', [ItemController::class, 'store'])
    ->name('item.store')
    ->middleware('auth');

// heading нужно, если $heading = true - нажата Добавить из "heading", false - из "body" (только при добавлении записи)
//Route::post('/item/ext_store/{base}/{project}/{role}/{usercode}/{relit_id}/{string_link_ids_current?}/{string_item_ids_current?}/{string_all_codes_current?}/{heading?}/{base_index_page?}/{body_link_page?}/{body_all_page?}/{parent_ret_id?}/{view_link?}/{par_link?}/{parent_item?}', [ItemController::class, 'ext_store'])
//    ->name('item.ext_store')
//    ->middleware('auth');
Route::post('/item/ext_store/{base}/{project}/{role}/{usercode}/{relit_id}/{string_current?}/{heading?}/{base_index_page?}/{body_link_page?}/{body_all_page?}/{parent_ret_id?}/{view_link?}/{par_link?}/{parent_item?}', [ItemController::class, 'ext_store'])
    ->name('item.ext_store')
    ->middleware('auth');

Route::put('/item/edit/{item}', [ItemController::class, 'update'])
    ->name('item.update')
    ->middleware('auth');

//Route::put('/item/ext_edit/{item}/{project}/{role}/{usercode}/{relit_id}/{string_link_ids_current?}/{string_item_ids_current?}/{string_all_codes_current?}/{heading?}/{base_index_page?}/{body_link_page?}/{body_all_page?}/{parent_ret_id?}/{view_link?}/{par_link?}/{parent_item?}', [ItemController::class, 'ext_update'])
//    ->name('item.ext_update')
//    ->middleware('auth');
Route::put('/item/ext_edit/{item}/{project}/{role}/{usercode}/{relit_id}/{string_current?}/{heading?}/{base_index_page?}/{body_link_page?}/{body_all_page?}/{parent_ret_id?}/{view_link?}/{par_link?}/{parent_item?}', [ItemController::class, 'ext_update'])
    ->name('item.ext_update')
    ->middleware('auth');

Route::get('/item/delete_question/{item}', [ItemController::class, 'delete_question'])
    ->name('item.delete_question')
    ->middleware('auth');

//  Нужно '/{par_link?}', при просмотре ext_show.php подчеркивается главная связь (из item_index.php)
//  Нужно '/{parent_item?}', в ней передается $item_id главной записи item_index (при удалении записи с body)
//Route::get('/item/ext_delete_question/{item}/{project}/{role}/{usercode}/{relit_id}/{string_link_ids_current?}/{string_item_ids_current?}/{string_all_codes_current?}/{heading?}/{base_index_page?}/{body_link_page?}/{body_all_page?}/{parent_ret_id?}/{view_link?}/{par_link?}/{parent_item?}', [ItemController::class, 'ext_delete_question'])
//    ->name('item.ext_delete_question')
//    ->middleware('auth');
Route::get('/item/ext_delete_question/{item}/{project}/{role}/{usercode}/{relit_id}/{string_current?}/{heading?}/{base_index_page?}/{body_link_page?}/{body_all_page?}/{parent_ret_id?}/{view_link?}/{par_link?}/{parent_item?}', [ItemController::class, 'ext_delete_question'])
    ->name('item.ext_delete_question')
    ->middleware('auth');

//  Нужно '/{par_link?}', при просмотре ext_show.php подчеркивается главная связь (из item_index.php)
//  Нужно '/{parent_item?}', в ней передается $item_id главной записи item_index (при удалении записи с body)
//Route::delete('/item/ext_delete/{item}/{project}/{role}/{usercode}/{relit_id}/{string_link_ids_current?}/{string_item_ids_current?}/{string_all_codes_current?}/{heading?}/{base_index_page?}/{body_link_page?}/{body_all_page?}/{parent_ret_id?}/{view_link?}/{par_link?}/{parent_item?}', [ItemController::class, 'ext_delete'])
//    ->name('item.ext_delete')
//    ->middleware('auth');
Route::delete('/item/ext_delete/{item}/{project}/{role}/{usercode}/{relit_id}/{string_current?}/{heading?}/{base_index_page?}/{body_link_page?}/{body_all_page?}/{parent_ret_id?}/{view_link?}/{par_link?}/{parent_item?}', [ItemController::class, 'ext_delete'])
    ->name('item.ext_delete')
    ->middleware('auth');

Route::post('/store_link_change', [ItemController::class, 'store_link_change'])
    ->name('item.store_link_change');

Route::get('/item/get_items_main/{base}/{project}/{role}/{relit_id}/{enable_hist_records?}/{link?}/{item?}', [ItemController::class, 'get_items_main'])
    ->name('item.get_items_main')
    ->middleware('auth');

Route::get('/item/get_items_main_options/{base}/{project}/{role}/{relit_id}/{link?}/{item?}', [ItemController::class, 'get_items_main_options'])
    ->name('item.get_items_main_options')
    ->middleware('auth');

Route::get('/item/get_items_main_code/{code}/{base}/{project}/{role}/{relit_id}/{link?}/{item?}', [ItemController::class, 'get_items_main_code'])
    ->name('item.get_items_main_code')
    ->middleware('auth');

Route::get('/item/get_items_for_link/{link}/{project}/{role}/{relit_id}', [ItemController::class, 'get_items_for_link'])
    ->name('item.get_items_for_link')
    ->middleware('auth');

Route::get('/item/get_child_items_from_parent_item/{base_start}/{item_start}/{link}', [ItemController::class, 'get_child_items_from_parent_item'])
    ->name('item.get_child_items_from_parent_item')
    ->middleware('auth');

//Route::get('/item/get_selection_child_items_from_parent_item/{link}/{item_select}', [ItemController::class, 'get_selection_child_items_from_parent_item')
//    ->name('item.get_selection_child_items_from_parent_item')
//    ->middleware('auth');

Route::get('/item/get_parent_item_from_calc_child_item/{item_start}/{link_result}/{item_calc}', [ItemController::class, 'get_parent_item_from_calc_child_item'])
    ->name('item.get_parent_item_from_calc_child_item')
    ->middleware('auth');

Route::get('/item/get_parent_item_from_output_calculated_table',
    [ItemController::class, 'get_parent_item_from_output_calculated_table'])
    ->name('item.get_parent_item_from_output_calculated_table')
    ->middleware('auth');

// Использовать знак вопроса "/{base_id?}" (web.php)
//              равенство null "$base_id = null" (ItemController.php),
// иначе ошибка в function seach_click() - open('{{route('item.browser', [')}}' ...
//Route::get('/item/browser/{link_id}/{base_id?}/{project_id?}/{role_id?}/{item_id?}/{sort_by_code?}/{save_by_code?}/{search?}', [ItemController::class, 'browser')
//    ->name('item.browser')
//    ->middleware('auth');

//Route::get('/item/browser/{link_id}/{project_id?}/{role_id?}/{item_id?}/{order_by?}/{filter_by?}/{search?}', [ItemController::class, 'browser')
//    ->name('item.browser')
//    ->middleware('auth');

Route::get('/item/browser/{link_id}/{project_id?}/{role_id?}/{relit_id?}/{item_id?}/{order_by?}/{filter_by?}/{search?}', [ItemController::class, 'browser'])
    ->name('item.browser')
    ->middleware('auth');

Route::get('/item/calculate_names/{base}/{project}', [ItemController::class, 'calculate_names'])
    ->name('item.calculate_names')
    ->middleware('auth');

Route::get('/item/recalculation_codes/{base}/{project}', [ItemController::class, 'recalculation_codes'])
    ->name('item.recalculation_codes')
    ->middleware('auth');

Route::get('/item/verify_baselink/{base}/{project}', [ItemController::class, 'verify_baselink'])
    ->name('item.verify_baselink')
    ->middleware('auth');

Route::get('/item/verify_number_values', [ItemController::class, 'verify_number_values'])
    ->name('item.verify_number_values')
    ->middleware('auth');

Route::get('/item/verify_table_texts', [ItemController::class, 'verify_table_texts'])
    ->name('item.verify_table_texts')
    ->middleware('auth');

Route::get('/item/item_from_base_code/{base}/{project}/{code}', [ItemController::class, 'item_from_base_code'])
    ->name('item.item_from_base_code')
    ->middleware('auth');

Route::get('/item/doc_download/{item}/{usercode}', [ItemController::class, 'doc_download'])
    ->name('item.doc_download')
    ->middleware('auth');

Route::get('/item/change_history/{item}', [ItemController::class, 'change_history'])
    ->name('item.change_history')
    ->middleware('auth');

// Mains

Route::get('/main/index', [MainController::class, 'index'])
    ->name('main.index')
    ->middleware('auth');

Route::get('/main/show/{main}', [MainController::class, 'show'])
    ->name('main.show')
    ->middleware('auth');

Route::get('/main/create', [MainController::class, 'create'])
    ->name('main.create')
    ->middleware('auth');

Route::get('/main/edit/{main}', [MainController::class, 'edit'])
    ->name('main.edit')
    ->middleware('auth');

Route::post('/main/store', [MainController::class, 'store'])
    ->name('main.store')
    ->middleware('auth');

Route::put('/main/edit/{main}', [MainController::class, 'update'])
    ->name('main.update')
    ->middleware('auth');

Route::get('/main/delete_question/{main}', [MainController::class, 'delete_question'])
    ->name('main.delete_question')
    ->middleware('auth');

Route::delete('/main/delete/{main}', [MainController::class, 'delete'])
    ->name('main.delete')
    ->middleware('auth');

Route::get('/main/index_item/{item}', [MainController::class, 'index_item'])
    ->name('main.index_item')
    ->middleware('auth');

Route::get('/main/index_full/{item}/{link}', [MainController::class, 'index_full'])
    ->name('main.index_full')
    ->middleware('auth');

Route::post('/store_full', [MainController::class, 'store_full'])
    ->name('main.store_full');

// steps

//Route::get('/step/run_steps/{link}', [StepController::class, 'run_steps')
//    ->name('step.run_steps')
//    ->middleware('auth');

//Auth::routes();
//Route::auth();
//Route::get('/home', 'HomeController@index')->name('home');

//Auth::routes();
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home');
