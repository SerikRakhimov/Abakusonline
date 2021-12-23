<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\Link;
use App\Models\Roli;
use App\Models\Role;
use App\Models\Template;
use App\Rules\IsUniqueRoli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoliController extends Controller
{
    protected function rules(Request $request)
    {
        return [
            'role_id' => ['required', new IsUniqueRoli($request)],
            'link_id' => ['required', new IsUniqueRoli($request)],
        ];
    }

    function index_role(Role $role)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $rolis = Roli::where('role_id', $role->id)->orderBy('link_id');
        session(['rolis_previous_url' => request()->url()]);
        return view('roli/index', ['role' => $role, 'rolis' => $rolis->paginate(60)]);
    }


    function index_link(Link $link)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $rolis = Roli::where('link_id', $link->id)->orderBy('role_id');
        session(['rolis_previous_url' => request()->url()]);
        return view('roli/index', ['link' => $link, 'rolis' => $rolis->paginate(60)]);
    }

    function show_role(Roli $roli)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $role = Role::findOrFail($roli->role_id);
        return view('roli/show', ['type_form' => 'show', 'role' => $role, 'roli' => $roli]);
    }

    function show_link(Roli $roli)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $link = Link::findOrFail($roli->link_id);
        return view('roli/show', ['type_form' => 'show', 'link' => $link, 'roli' => $roli]);
    }

    function create_role(Role $role)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $links = $this->select_links_role($role);

        return view('roli/edit', ['role' => $role, 'links' => $links]);
    }

    function create_link(Link $link)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $roles = Role::where('template_id', $link->child_base->template_id);
        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $roles = $roles->orderBy($name);
        }
        $roles = $roles->get();
        return view('roli/edit', ['link' => $link, 'roles' => $roles]);
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

        $roli = new Roli($request->except('_token', '_method'));

        $this->set($request, $roli);
        //https://laravel.demiart.ru/laravel-sessions/
        if ($request->session()->has('rolis_previous_url')) {
            return redirect(session('rolis_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function update(Request $request, Roli $roli)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        if (!(($roli->role_id == $request->role_id) && ($roli->link_id == $request->link_id))) {
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

        $roli->fill($data);

        $this->set($request, $roli);

        if ($request->session()->has('rolis_previous_url')) {
            return redirect(session('rolis_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function check(Request $request, &$array_mess)
    {
        if ($request->is_edit_link_read  == true && $request->is_edit_link_update == true) {
            $array_mess['is_edit_link_read'] = trans('main.is_edit_link_read_rule') . '!';
        }
    }

    function set(Request $request, Roli &$roli)
    {
        $roli->role_id = $request->role_id;
        $roli->link_id = $request->link_id;
        $roli->is_list_link_enable = isset($request->is_list_link_enable) ? true : false;
        $roli->is_show_link_enable = isset($request->is_show_link_enable) ? true : false;
        $roli->is_edit_link_read = isset($request->is_edit_link_read) ? true : false;
        $roli->is_edit_link_update = isset($request->is_edit_link_update) ? true : false;
        $roli->is_hier_link_enable = isset($request->is_hier_link_enable) ? true : false;
        $roli->save();
    }

    function select_links_role(Role $role)
    {
// https://coderoad.ru/37878793/%D0%97%D0%B0%D0%BF%D1%80%D0%BE%D1%81-%D0%B4%D0%B2%D1%83%D1%85-%D1%82%D0%B0%D0%B1%D0%BB%D0%B8%D1%86-%D1%81-Laravel-%D0%B8-%D0%B4%D0%B2%D1%83%D0%BC%D1%8F-%D1%83%D1%81%D0%BB%D0%BE%D0%B2%D0%B8%D1%8F%D0%BC%D0%B8-WHERE
//        https://unetway.com/tutorial/laravel-query-builder
//        $values = DB::table('links')
//            ->join('bases as cb', 'links.child_base_id', '=', 'cb.id')
//            ->join('bases as pb', 'links.parent_base_id', '=', 'pb.id')
//            ->where('cb.template_id', $role->template_id)
//            ->orderBy('links.child_base_id')
//            ->orderBy('links.parent_base_id')
//            ->select('links.*')
//            ->orderBy('pb.name_lang_0');

//        return Link::select(DB::Raw('links.*'))
//            ->join('bases as cb', 'links.child_base_id', '=', 'cb.id')
//            ->join('bases as pb', 'links.parent_base_id', '=', 'pb.id')
//            ->where('cb.template_id', $role->template_id)
//            ->orderBy('links.child_base_id')
//            ->orderBy('links.parent_base_id')
//            ->orderBy('pb.name_lang_0');

        return Link::select(DB::Raw('links.*'))
            ->join('bases as cb', 'links.child_base_id', '=', 'cb.id')
            ->join('bases as pb', 'links.parent_base_id', '=', 'pb.id')
            ->where('cb.template_id', $role->template_id)
            ->orderBy('links.child_base_id')
            ->orderBy('links.parent_base_number')
            ->get();
    }

    function edit_role(Roli $roli)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $role = Role::findOrFail($roli->role_id);

        $links = $this->select_links_role($role);

        return view('roli/edit', ['role' => $role, 'roli' => $roli, 'links' => $links]);
    }

    function edit_link(Roli $roli)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $link = Link::findOrFail($roli->link_id);
        $roles = Role::where('template_id', $link->child_base->template_id);
        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $roles = $roles->orderBy($name);
        }
        $roles = $roles->get();
        return view('roli/edit', ['link' => $link, 'roli' => $roli, 'roles' => $roles]);
    }

    function delete_question(Roli $roli)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($roli->role->template_id);
        return view('roli/show', ['type_form' => 'delete_question', 'template' => $template, 'roli' => $roli]);
    }

    function delete(Request $request, Roli $roli)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $roli->delete();

        if ($request->session()->has('rolis_previous_url')) {
            return redirect(session('rolis_previous_url'));
        } else {
            return redirect()->back();
        }
    }

}
