<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\Template;
use App\Models\Task;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    protected function rules()
    {
        return [
            'name_lang_0' => ['required', 'max:255'],
        ];
    }

    function index(Task $task)
    {
        $template = Template::findOrFail($task->template_id);
        $modules = Module::where('task_id', $task->id);
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            switch ($index) {
                case 0:
                    $modules = $modules->orderBy('name_lang_0');
                    break;
                case 1:
                    $modules = $modules->orderBy('name_lang_1')->orderBy('name_lang_0');
                    break;
                case 2:
                    $modules = $modules->orderBy('name_lang_2')->orderBy('name_lang_0');
                    break;
                case 3:
                    $modules = $modules->orderBy('name_lang_3')->orderBy('name_lang_0');
                    break;
            }
        }
        session(['modules_previous_url' => request()->url()]);
        return view('module/index', ['template' => $template, 'task' => $task, 'modules' => $modules->paginate(60)]);
    }

    function show(Module $module)
    {
        $task = Task::findOrFail($module->task_id);
        $template = Template::findOrFail($task->template_id);
        return view('module/show', ['type_form' => 'show', 'template' => $template, 'task' => $task, 'module' => $module]);
    }

    function create(Task $task)
    {
        $template = Template::findOrFail($task->template_id);
        return view('module/edit', ['template' => $template, 'task'=>$task]);
    }

    function store(Request $request)
    {
        $request->validate($this->rules());

        // установка часового пояса нужно для сохранения времени
        date_default_timezone_set('Asia/Almaty');

        $module = new Module($request->except('_token', '_method'));
        $module->task_id = $request->task_id;

        $this->set($request, $module);
        //https://laravel.demiart.ru/laravel-sessions/
        if ($request->session()->has('modules_previous_url')) {
            return redirect(session('modules_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function update(Request $request, Module $module)
    {
        if (!($module->name_lang_0 == $request->name_lang_0)) {
            $request->validate($this->rules());
        }

        $data = $request->except('_token', '_method');

        $module->fill($data);

        $this->set($request, $module);

        if ($request->session()->has('modules_previous_url')) {
            return redirect(session('modules_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function set(Request $request, Module &$module)
    {
        $module->name_lang_0 = $request->name_lang_0;
        $module->name_lang_1 = isset($request->name_lang_1) ? $request->name_lang_1 : "";
        $module->name_lang_2 = isset($request->name_lang_2) ? $request->name_lang_2 : "";
        $module->name_lang_3 = isset($request->name_lang_3) ? $request->name_lang_3 : "";

        $module->save();
    }

    function edit(Module $module)
    {
        $task = Task::findOrFail($module->task_id);
        $template = Template::findOrFail($task->template_id);
        return view('module/edit', ['template' => $template, 'task' => $task, 'module' => $module]);
    }

    function delete_question(Module $module)
    {
        $task = Task::findOrFail($module->task_id);
        $template = Template::findOrFail($task->template_id);
        return view('module/show', ['type_form' => 'delete_question', 'template' => $template, 'task' => $task, 'module' => $module]);
    }

    function delete(Request $request, Module $module)
    {
        $module->delete();

        if ($request->session()->has('modules_previous_url')) {
            return redirect(session('modules_previous_url'));
        } else {
            return redirect()->back();
        }
    }

}
