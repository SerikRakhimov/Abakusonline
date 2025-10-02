<?php

namespace App\Http\Controllers;

use App\Models\Relit;
use App\Models\Role;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class RelitController extends Controller
{
    protected function rules()
    {
        return [
            'serial_number' => ['required'],
        ];
    }

    function index(Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $relits = Relit::where('child_template_id', $template->id)->orderBy('serial_number');
        session(['relits_previous_url' => request()->url()]);
        return view('relit/index', ['template' => $template, 'relits' => $relits->paginate(60)]);
    }

    function show(Relit $relit)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($relit->child_template_id);
        return view('relit/show', ['type_form' => 'show', 'template' => $template, 'relit' => $relit]);
    }


    function create(Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $templates = null;
        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $templates = Template::orderBy($name)->get();
        } else {
            $templates = Template::all()->get();
        }
        return view('relit/edit', ['child_template' => $template, 'templates' => $templates]);
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

        $relit = new Relit($request->except('_token', '_method'));

        $this->set($request, $relit);
        //https://laravel.demiart.ru/laravel-sessions/
        if ($request->session()->has('relits_previous_url')) {
            return redirect(session('relits_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function update(Request $request, Relit $relit)
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

        $data = $request->except('_token', '_method');

        $relit->fill($data);

        $this->set($request, $relit);

        if ($request->session()->has('relits_previous_url')) {
            return redirect(session('relits_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function check(Request $request, &$array_mess)
    {
        // 'is_use_current_project' => 'Использовать текущий проект'
        if ($request->child_template_id != $request->parent_template_id
            & $request->parent_is_use_current_project == true) {
            $array_mess['parent_is_use_current_project'] = trans('main.is_equality_not_equal') . '!';
        }
    }

    function set(Request $request, Relit &$relit)
    {
        $relit->serial_number = $request->serial_number;
        $relit->child_template_id = $request->child_template_id;
        $relit->parent_template_id = $request->parent_template_id;
        $relit->parent_title_lang_0 = isset($request->parent_title_lang_0) ? $request->parent_title_lang_0 : "";
        $relit->parent_title_lang_1 = isset($request->parent_title_lang_1) ? $request->parent_title_lang_1 : "";
        $relit->parent_title_lang_2 = isset($request->parent_title_lang_2) ? $request->parent_title_lang_2 : "";
        $relit->parent_title_lang_3 = isset($request->parent_title_lang_3) ? $request->parent_title_lang_3 : "";
        $relit->parent_is_required = isset($request->parent_is_required) ? true : false;
        $relit->parent_is_use_current_project = isset($request->parent_is_use_current_project) ? true : false;

        $relit->save();
    }

    function edit(Relit $relit)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $child_template = Template::findOrFail($relit->child_template_id);
        $templates = null;
        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $templates = Template::orderBy($name)->get();
        } else {
            $templates = Template::all()->get();
        }
        return view('relit/edit', ['child_template' => $child_template, 'relit' => $relit, 'templates' => $templates]);
    }

    function delete_question(Relit $relit)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($relit->child_template_id);
        return view('relit/show', ['type_form' => 'delete_question', 'template' => $template, 'relit' => $relit]);
    }

    function delete(Request $request, Relit $relit)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $relit->delete();

        if ($request->session()->has('relits_previous_url')) {
            return redirect(session('relits_previous_url'));
        } else {
            return redirect()->back();
        }
    }

}
