<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\Role;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    protected function rules()
    {
        return [
            'name_lang_0' => ['required', 'max:255'],
        ];
    }

    function index(Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $roles = Role::where('template_id', $template->id)->orderBy('serial_number');
        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $roles = $roles->orderBy($name);
        }
        session(['roles_previous_url' => request()->url()]);
        return view('role/index', ['template' => $template, 'roles' => $roles->paginate(60)]);
    }

    function show(Role $role)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($role->template_id);
        return view('role/show', ['type_form' => 'show', 'template' => $template, 'role' => $role]);
    }


    function create(Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        return view('role/edit', ['template' => $template]);
    }

    function store(Request $request)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $request->validate($this->rules());

        $array_mess = [];
        $this->check($request, $array_mess);

        if (count($array_mess) > 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors($array_mess);
        }

        // установка часового пояса нужно для сохранения времени
        date_default_timezone_set('Asia/Almaty');

        $role = new Role($request->except('_token', '_method'));

        $this->set($request, $role);
        //https://laravel.demiart.ru/laravel-sessions/
        if ($request->session()->has('roles_previous_url')) {
            return redirect(session('roles_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function update(Request $request, Role $role)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        if (!($role->name_lang_0 == $request->name_lang_0)) {
            $request->validate($this->rules());
        }

        $array_mess = [];
        $this->check($request, $array_mess);

        if (count($array_mess) > 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors($array_mess);
        }

        $data = $request->except('_token', '_method');

        $role->fill($data);

        $this->set($request, $role);

        if ($request->session()->has('roles_previous_url')) {
            return redirect(session('roles_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function check(Request $request, &$array_mess)
    {
        if ($request->is_list_base_create == true && $request->is_edit_base_read == true) {
            $array_mess['is_edit_base_read'] = trans('main.is_list_base_create_rule') . '!';
        }
        if ($request->is_list_base_read  == true && ($request->is_list_base_create || $request->is_list_base_update ||$request->is_list_base_delete)) {
            $array_mess['is_list_base_read'] = trans('main.is_list_base_read_rule') . '!';
        }
        if ($request->is_list_base_delete  == false && $request->is_list_base_used_delete == true) {
            $array_mess['is_list_base_used_delete'] = trans('main.is_list_base_used_delete_rule') . '!';
        }
        if ($request->is_edit_base_read  == true && $request->is_edit_base_update == true) {
            $array_mess['is_edit_base_read'] = trans('main.is_edit_base_read_rule') . '!';
        }
        if ($request->is_edit_link_read  == true && $request->is_edit_link_update == true) {
            $array_mess['is_edit_link_read'] = trans('main.is_edit_link_read_rule') . '!';
        }
        if ($request->is_edit_email_base_create  == false && $request->is_edit_email_question_base_create == true) {
            $array_mess['is_edit_email_question_base_create'] = trans('main.is_edit_email_question_base_create_rule') . '!';
        }
        if ($request->is_edit_email_base_update  == false && $request->is_edit_email_question_base_update == true) {
            $array_mess['is_edit_email_question_base_update'] = trans('main.is_edit_email_question_base_update_rule') . '!';
        }
        if ($request->is_show_email_base_delete  == false && $request->is_show_email_question_base_delete == true) {
            $array_mess['is_show_email_question_base_delete'] = trans('main.is_show_email_question_base_delete_rule') . '!';
        }
    }

    function set(Request $request, Role &$role)
    {
        $role->template_id = $request->template_id;
        $role->serial_number = $request->serial_number;

        $role->name_lang_0 = $request->name_lang_0;
        $role->name_lang_1 = isset($request->name_lang_1) ? $request->name_lang_1 : "";
        $role->name_lang_2 = isset($request->name_lang_2) ? $request->name_lang_2 : "";
        $role->name_lang_3 = isset($request->name_lang_3) ? $request->name_lang_3 : "";

        $role->desc_lang_0 = isset($request->desc_lang_0) ? $request->desc_lang_0 : "";
        $role->desc_lang_1 = isset($request->desc_lang_1) ? $request->desc_lang_1 : "";
        $role->desc_lang_2 = isset($request->desc_lang_2) ? $request->desc_lang_2 : "";
        $role->desc_lang_3 = isset($request->desc_lang_3) ? $request->desc_lang_3 : "";

        $role->is_author = isset($request->is_author) ? true : false;
        $role->is_default_for_external = isset($request->is_default_for_external) ? true : false;
        $role->is_list_base_sndbt = isset($request->is_list_base_sndbt) ? true : false;
        $role->is_list_base_id = isset($request->is_list_base_id) ? true : false;
        $role->is_list_base_calculated = isset($request->is_list_base_calculated) ? true : false;
        $role->is_list_base_setup = isset($request->is_list_base_setup) ? true : false;
        $role->is_all_base_calcname_enable = isset($request->is_all_base_calcname_enable) ? true : false;
        $role->is_list_base_sort_creation_date_desc = isset($request->is_list_base_sort_creation_date_desc) ? true : false;
        $role->is_list_base_create = isset($request->is_list_base_create) ? true : false;
        $role->is_list_base_read = isset($request->is_list_base_read) ? true : false;
        $role->is_list_base_update = isset($request->is_list_base_update) ? true : false;
        $role->is_list_base_delete = isset($request->is_list_base_delete) ? true : false;
        $role->is_list_base_used_delete = isset($request->is_list_base_used_delete) ? true : false;
        $role->is_list_base_byuser = isset($request->is_list_base_byuser) ? true : false;
        $role->is_list_link_baselink = isset($request->is_list_link_baselink) ? true : false;
        $role->is_edit_base_read = isset($request->is_edit_base_read) ? true : false;
        $role->is_edit_base_update = isset($request->is_edit_base_update) ? true : false;
        $role->is_list_base_enable = isset($request->is_list_base_enable) ? true : false;
        $role->is_list_link_enable = isset($request->is_list_link_enable) ? true : false;
        $role->is_show_base_enable = isset($request->is_show_base_enable) ? true : false;
        $role->is_show_link_enable = isset($request->is_show_link_enable) ? true : false;
        $role->is_edit_link_read = isset($request->is_edit_link_read) ? true : false;
        $role->is_edit_link_update = isset($request->is_edit_link_update) ? true : false;
        $role->is_hier_base_enable = isset($request->is_hier_base_enable) ? true : false;
        $role->is_hier_link_enable = isset($request->is_hier_link_enable) ? true : false;
        $role->is_edit_email_base_create = isset($request->is_edit_email_base_create) ? true : false;
        $role->is_edit_email_question_base_create = isset($request->is_edit_email_question_base_create) ? true : false;
        $role->is_edit_email_base_update = isset($request->is_edit_email_base_update) ? true : false;
        $role->is_edit_email_question_base_update = isset($request->is_edit_email_question_base_update) ? true : false;
        $role->is_show_email_base_delete = isset($request->is_show_email_base_delete) ? true : false;
        $role->is_show_email_question_base_delete = isset($request->is_show_email_question_base_delete) ? true : false;

        $role->save();
    }

    function edit(Role $role)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($role->template_id);
        return view('role/edit', ['template' => $template, 'role' => $role]);
    }

    function delete_question(Role $role)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($role->template_id);
        return view('role/show', ['type_form' => 'delete_question', 'template' => $template, 'role' => $role]);
    }

    function delete(Request $request, Role $role)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $role->delete();

        if ($request->session()->has('roles_previous_url')) {
            return redirect(session('roles_previous_url'));
        } else {
            return redirect()->back();
        }
    }

}
