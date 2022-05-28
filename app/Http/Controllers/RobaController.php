<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\Base;
use App\Models\Roba;
use App\Models\Role;
use App\Models\Template;
use App\Rules\IsUniqueRoba;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RobaController extends Controller
{
    protected function rules(Request $request)
    {
        return [
            'role_id' => ['required', new IsUniqueRoba($request)],
            'base_id' => ['required', new IsUniqueRoba($request)],
            'relit_id' => ['required', new IsUniqueRoba($request)],
        ];
    }

    function index_role(Role $role)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $robas = Roba::where('role_id', $role->id)->orderBy('base_id');
        session(['robas_previous_url' => request()->url()]);
        return view('roba/index', ['role' => $role, 'robas' => $robas->paginate(60)]);
    }


    function index_base(Base $base)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $robas = Roba::where('base_id', $base->id)->orderBy('role_id');
        session(['robas_previous_url' => request()->url()]);
        return view('roba/index', ['base' => $base, 'robas' => $robas->paginate(60)]);
    }

    function show_role(Roba $roba)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $role = Role::findOrFail($roba->role_id);
        return view('roba/show', ['type_form' => 'show', 'role' => $role, 'roba' => $roba]);
    }

    function show_base(Roba $roba)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $base = Base::findOrFail($roba->base_id);
        return view('roba/show', ['type_form' => 'show', 'base' => $base, 'roba' => $roba]);
    }

    function create_role(Role $role)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $bases = Base::where('template_id', $role->template_id);
        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $bases = $bases->orderBy($name);
        }
        $bases = $bases->get();
        return view('roba/edit',
            ['template' => $role->template, 'role' => $role, 'bases' => $bases,
            'array_relits' => GlobalController::get_array_relits($role->template)]);
    }

    function create_base(Base $base)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $roles = Role::where('template_id', $base->template_id);
        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $roles = $roles->orderBy($name);
        }
        $roles = $roles->get();
        return view('roba/edit',
            ['template' => $base->template, 'base' => $base, 'roles' => $roles,
            'array_relits' => GlobalController::get_array_relits($base->template)]);
    }

    function store(Request $request)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $request->validate($this->rules($request));

        $array_mess = [];
        $this->check($request, $array_mess);

        if (count($array_mess) > 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors($array_mess);
        }

        // установка часового пояса нужно для сохранения времени
        date_default_timezone_set('Asia/Almaty');

        $roba = new Roba($request->except('_token', '_method'));

        $this->set($request, $roba);
        //https://laravel.demiart.ru/laravel-sessions/
        if ($request->session()->has('robas_previous_url')) {
            return redirect(session('robas_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function update(Request $request, Roba $roba)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        if (!(($roba->role_id == $request->role_id) && ($roba->base_id == $request->base_id))) {
            $request->validate($this->rules($request));
        }

        $array_mess = [];
        $this->check($request, $array_mess);

        if (count($array_mess) > 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors($array_mess);
        }

        $data = $request->except('_token', '_method');

        $roba->fill($data);

        $this->set($request, $roba);

        if ($request->session()->has('robas_previous_url')) {
            return redirect(session('robas_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function check(Request $request, &$array_mess)
    {
        if ($request->is_mnmn_base_enable == true
            && ($request->is_list_base_create == false && $request->is_list_base_read == false
                && $request->is_list_base_update == false && $request->is_list_base_delete == false)) {
            $array_mess['is_mnmn_base_enable'] = trans('main.is_mnmn_base_enable_rule') . '!';
        }
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

    function set(Request $request, Roba &$roba)
    {
        $roba->role_id = $request->role_id;
        $roba->relit_id = $request->relit_id;
        $roba->base_id = $request->base_id;
        $roba->is_all_base_calcname_enable = isset($request->is_all_base_calcname_enable) ? true : false;
        $roba->is_list_base_sort_creation_date_desc = isset($request->is_list_base_sort_creation_date_desc) ? true : false;
        $roba->is_mnmn_base_enable = isset($request->is_mnmn_base_enable) ? true : false;
        $roba->is_list_base_create = isset($request->is_list_base_create) ? true : false;
        $roba->is_list_base_read = isset($request->is_list_base_read) ? true : false;
        $roba->is_list_base_update = isset($request->is_list_base_update) ? true : false;
        $roba->is_list_base_delete = isset($request->is_list_base_delete) ? true : false;
        $roba->is_list_base_used_delete = isset($request->is_list_base_used_delete) ? true : false;
        $roba->is_list_base_byuser = isset($request->is_list_base_byuser) ? true : false;
        $roba->is_edit_base_read = isset($request->is_edit_base_read) ? true : false;
        $roba->is_edit_base_update = isset($request->is_edit_base_update) ? true : false;
        $roba->is_list_base_enable = isset($request->is_list_base_enable) ? true : false;
        $roba->is_list_link_enable = isset($request->is_list_link_enable) ? true : false;
        $roba->is_body_link_enable = isset($request->is_body_link_enable) ? true : false;
        $roba->is_show_base_enable = isset($request->is_show_base_enable) ? true : false;
        $roba->is_show_link_enable = isset($request->is_show_link_enable) ? true : false;
        $roba->is_edit_link_read = isset($request->is_edit_link_read) ? true : false;
        $roba->is_edit_link_update = isset($request->is_edit_link_update) ? true : false;
        $roba->is_hier_base_enable = isset($request->is_hier_base_enable) ? true : false;
        $roba->is_hier_link_enable = isset($request->is_hier_link_enable) ? true : false;
        $roba->is_edit_email_base_create = isset($request->is_edit_email_base_create) ? true : false;
        $roba->is_edit_email_question_base_create = isset($request->is_edit_email_question_base_create) ? true : false;
        $roba->is_edit_email_base_update = isset($request->is_edit_email_base_update) ? true : false;
        $roba->is_edit_email_question_base_update = isset($request->is_edit_email_question_base_update) ? true : false;
        $roba->is_show_email_base_delete = isset($request->is_show_email_base_delete) ? true : false;
        $roba->is_show_email_question_base_delete = isset($request->is_show_email_question_base_delete) ? true : false;

        $roba->save();
    }

    function edit_role(Roba $roba)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $role = Role::findOrFail($roba->role_id);
        $bases = Base::where('template_id', $role->template_id);
        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $bases = $bases->orderBy($name);
        }
        $bases = $bases->get();
        return view('roba/edit', ['template' => $roba->role->template, 'role' => $role, 'roba' => $roba, 'bases' => $bases,
            'array_relits' => GlobalController::get_array_relits($roba->role->template)]);
    }

    function edit_base(Roba $roba)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $base = Base::findOrFail($roba->base_id);
        $roles = Role::where('template_id', $base->template_id);
        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $roles = $roles->orderBy($name);
        }
        $roles = $roles->get();
        return view('roba/edit', ['template' => $roba->role->template, 'base' => $base, 'roba' => $roba, 'roles' => $roles,
            'array_relits' => GlobalController::get_array_relits($roba->role->template)]);
    }

    function delete_question(Roba $roba)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($roba->role->template_id);
        return view('roba/show', ['type_form' => 'delete_question', 'template' => $template, 'roba' => $roba]);
    }

    function delete(Request $request, Roba $roba)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $roba->delete();

        if ($request->session()->has('robas_previous_url')) {
            return redirect(session('robas_previous_url'));
        } else {
            return redirect()->back();
        }
    }

}
