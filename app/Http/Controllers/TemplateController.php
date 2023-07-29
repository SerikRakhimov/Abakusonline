<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\Template;
use App\Models\Role;
use App\Rules\IsLatinTemplate;
use App\Rules\IsLowerTemplate;
use App\Rules\IsOneWordTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    protected function account_rules()
    {
        return ['account' => ['required', 'string', 'max:255', 'unique:templates', new IsOneWordTemplate(), new IsLatinTemplate(), new IsLowerTemplate()],
        ];
    }

    protected function name_lang_0_rules()
    {
        return ['name_lang_0' => ['required', 'max:255'],
        ];
    }

    function index()
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }
        $templates = Template::withCount('projects')->withCount('roles')->withCount('bases')->withCount('sets');
        $index = array_search(App::getLocale(), config('app.locales'));
        $name = "";  // нужно, не удалять
        //$index = array_search(App::getLocale(), config('app.locales'));
        //if ($index !== false) {   // '!==' использовать, '!=' не использовать
        //    $name = 'name_lang_' . $index;
        //    $templates = $templates->orderBy($name);
        //}
        $templates = $templates->orderBy('serial_number');
        session(['templates_previous_url' => request()->url()]);
        return view('template/index', ['templates' => $templates->paginate(60)]);
    }

    function main_index()
    {
        $templates = Template::withCount('projects');
        // Используется '$is_filter_show_admin = false;'
        $is_filter_show_admin = false;
        if (Auth::check()) {
            if (!Auth::user()->isAdmin()) {
                $is_filter_show_admin = true;
            }
        } else {
            $is_filter_show_admin = true;
        }
        if ($is_filter_show_admin) {
            $templates = $templates->where('is_show_admin', false);
        }
        $index = array_search(App::getLocale(), config('app.locales'));
        $name = "";  // нужно, не удалять
        //$index = array_search(App::getLocale(), config('app.locales'));
        //if ($index !== false) {   // '!==' использовать, '!=' не использовать
        //    $name = 'name_lang_' . $index;
        //    $templates = $templates->orderBy($name);
        //}
        $templates = $templates->orderBy('serial_number');
        session(['templates_previous_url' => request()->url()]);
        return view('template/main_index', ['templates' => $templates->paginate(60)]);
    }

    function show(Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }
        return view('template/show', ['type_form' => 'show', 'template' => $template]);
    }


    function create()
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }
        return view('template/edit');
    }

    function store(Request $request)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $request->validate($this->account_rules());
        $request->validate($this->name_lang_0_rules());

        $array_mess = [];
        $this->check($request, $array_mess);

        if (count($array_mess) > 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors($array_mess);
        }

        // установка часового пояса нужно для сохранения времени
        date_default_timezone_set('Asia/Almaty');

        $template = new Template($request->except('_token', '_method'));

        $this->set($request, $template);

        // Создание админской записи в roles
        $role = new Role();
        $role->template_id = $template->id;
        $role->serial_number = 1;
        $role->is_author = true;
        // Присваиваем наименования
        $lang_save = App::getLocale();
        $i = 0;
        foreach (config('app.locales') as $lang_value) {
            App::setLocale($lang_value);
            $role['name_lang_' . $i] = trans('main.author');
            $i = $i + 1;
        }
        App::setLocale($lang_save);
        $role->save();

        //https://laravel.demiart.ru/laravel-sessions/
        if ($request->session()->has('templates_previous_url')) {
            return redirect(session('templates_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function update(Request $request, Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project . all_index');
        }

        if ($template->account != $request->account) {
            $request->validate($this->account_rules());
        }

        if ($template->name_lang_0 != $request->name_lang_0) {
            $request->validate($this->name_lang_0_rules());
        }

        $array_mess = [];
        $this->check($request, $array_mess);

        if (count($array_mess) > 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors($array_mess);
        }

        $data = $request->except('_token', '_method');

        $template->fill($data);

        $this->set($request, $template);

        if ($request->session()->has('templates_previous_url')) {
            return redirect(session('templates_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function check(Request $request, &$array_mess)
    {
        foreach (config('app.locales') as $lang_key => $lang_value) {
            $text_html_check = GlobalController::text_html_check($request['desc_lang_' . $lang_key]);
            if ($text_html_check['result'] == true) {
                $array_mess['desc_lang_' . $lang_key] = $text_html_check['message'] . '!';
            }
        }
    }

    function set(Request $request, Template &$template)
    {
        $template->serial_number = $request->serial_number;
        $template->account = $request->account;
        $template->name_lang_0 = $request->name_lang_0;
        $template->name_lang_1 = isset($request->name_lang_1) ? $request->name_lang_1 : "";
        $template->name_lang_2 = isset($request->name_lang_2) ? $request->name_lang_2 : "";
        $template->name_lang_3 = isset($request->name_lang_3) ? $request->name_lang_3 : "";

        $template->is_test = isset($request->is_test) ? true : false;
        $template->is_closed_default_value = isset($request->is_closed_default_value) ? true : false;
        $template->is_closed_default_value_fixed = isset($request->is_closed_default_value_fixed) ? true : false;
        $template->is_show_admin = isset($request->is_show_admin) ? true : false;
        $template->is_create_admin = isset($request->is_create_admin) ? true : false;

        $template->desc_lang_0 = isset($request->desc_lang_0) ? $request->desc_lang_0 : "";
        $template->desc_lang_1 = isset($request->desc_lang_1) ? $request->desc_lang_1 : "";
        $template->desc_lang_2 = isset($request->desc_lang_2) ? $request->desc_lang_2 : "";
        $template->desc_lang_3 = isset($request->desc_lang_3) ? $request->desc_lang_3 : "";

        $template->save();
    }

    function edit(Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project . all_index');
        }
        return view('template/edit', ['template' => $template]);
    }

    function delete_question(Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project . all_index');
        }
        return view('template/show', ['type_form' => 'delete_question', 'template' => $template]);
    }

    function delete(Request $request, Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project . all_index');
        }

        $template->delete();

        if ($request->session()->has('templates_previous_url')) {
            return redirect(session('templates_previous_url'));
        } else {
            return redirect()->back();
        }
    }

}
