<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\Level;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LevelController extends Controller
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

        $levels = Level::where('template_id', $template->id);
        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $levels = $levels->orderBy($name);
        }
        session(['levels_previous_url' => request()->url()]);
        return view('level/index', ['template' => $template, 'levels' => $levels->paginate(60)]);
    }

    function show(Level $level)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($level->template_id);
        return view('level/show', ['type_form' => 'show', 'template' => $template, 'level' => $level]);
    }


    function create(Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        return view('level/edit', ['template' => $template]);
    }

    function store(Request $request)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }
        // Проверка не нужна, чтобы можно ввести пустые строки
        //$request->validate($this->rules());

        // установка часового пояса нужно для сохранения времени
        date_default_timezone_set('Asia/Almaty');

        $level = new Level($request->except('_token', '_method'));

        $this->set($request, $level);
        //https://laravel.demiart.ru/laravel-sessions/
        if ($request->session()->has('levels_previous_url')) {
            return redirect(session('levels_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function update(Request $request, Level $level)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        // Проверка не нужна, чтобы можно ввести пустые строки
//        if (!($level->name_lang_0 == $request->name_lang_0)) {
//            $request->validate($this->rules());
//        }

        $data = $request->except('_token', '_method');

        $level->fill($data);

        $this->set($request, $level);

        if ($request->session()->has('levels_previous_url')) {
            return redirect(session('levels_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function set(Request $request, Level &$level)
    {
        $level->template_id = $request->template_id;

        $level->name_lang_0 = isset($request->name_lang_0) ? $request->name_lang_0 : "";
        $level->name_lang_1 = isset($request->name_lang_1) ? $request->name_lang_1 : "";
        $level->name_lang_2 = isset($request->name_lang_2) ? $request->name_lang_2 : "";
        $level->name_lang_3 = isset($request->name_lang_3) ? $request->name_lang_3 : "";

        $level->save();
    }

    function edit(Level $level)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($level->template_id);
        return view('level/edit', ['template' => $template, 'level' => $level]);
    }

    function delete_question(Level $level)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($level->template_id);
        return view('level/show', ['type_form' => 'delete_question', 'template' => $template, 'level' => $level]);
    }

    function delete(Request $request, Level $level)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $level->delete();

        if ($request->session()->has('levels_previous_url')) {
            return redirect(session('levels_previous_url'));
        } else {
            return redirect()->back();
        }
    }

}
