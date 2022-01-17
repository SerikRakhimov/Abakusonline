<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Link;
use App\Models\Main;
use App\Models\Project;
use App\Models\Template;
use App\Models\Set;
use App\Models\Relit;
use App\Models\Relip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    protected function rules()
    {
        return [
            'link_id' => 'exists:links,id',
            'child_item_id' => 'exists:items,id',
            'parent_item_id' => 'exists:items,id',
        ];

    }

    function index()
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $mains = Main::orderBy('link_id')->orderBy('child_item_id')->orderBy('parent_item_id');
        return view('main/index', ['mains' => $mains->paginate(60)]);

    }

    function index_item(Item $item)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

//        $child_mains = Main::all()->where('child_item_id', $item->id)->sortBy(function ($main) {
//            return $main->link->parent_base->name() . $main->parent_item->name();
//        });

        $child_mains = Main::where('child_item_id', $item->id)->sortBy(function ($main) {
            return $main->link->parent_base->name() . $main->parent_item->name();
        })->get();


        $parent_mains = Main::where('parent_item_id', $item->id)->sortBy(function ($main) {
            return $main->link->child_base->name() . $main->child_item->name();
        })->get();

        return view('main/index_item',
            ['item' => $item, 'child_mains' => $child_mains, 'parent_mains' => $parent_mains]);

    }

    function index_full(Item $item, Link $link)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

//        $item = $main->parent_item;
//        $link_head = $main->link;
        $link_head = $link;
        // исключим $link_head->id, он будет выводится в "заголовке"/"шапке" страницы
        $links = $link_head->child_base->child_links->where('id', '!=', $link_head->id);

        $mains = Main::all()->where('parent_item_id', $item->id)->where('link_id', $link_head->id)->sortBy(function ($main) {
            return $main->link->child_base->name() . $main->child_item->name();
        });

        return view('main/index_full',
            ['item' => $item, 'link_head' => $link_head, 'links' => $links, 'mains' => $mains]);
    }

    function store_full(Request $request)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $item = $request['item'];
        $link = $request['link'];

        return redirect()->route('main.index_full', ['item' => $item, 'link' => $link]);
    }

    function show(Main $main)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        return view('main/show', ['type_form' => 'show', 'main' => $main]);
    }

    function create()
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        // исключая вычисляемые поля
        return view('main/edit', ['links' => Link::all()->where('parent_is_parent_related', false)]);
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

        $main = new Main($request->except('_token', '_method'));

        $main->link_id = $request->link_id;
        $main->child_item_id = $request->child_item_id;
        $main->parent_item_id = $request->parent_item_id;

        $message_child_base_id = '';
        if (!($main->link->child_base_id == $main->child_item->base_id)) {
            $message_child_base_id = trans('main.base') . ' != ' . $main->link->child_base->name_lang_1;
        }

        $message_parent_base_id = '';
        if (!($main->link->parent_base_id == $main->parent_item->base_id)) {
            $message_parent_base_id = trans('main.base') . ' != ' . $main->link->parent_base->name_lang_1;
        }

        if (($message_child_base_id != '') || ($message_parent_base_id != '')) {
            // повторный вызов формы
            return redirect()->back()
                ->withInput()
                ->withErrors(['message_child_base_id' => $message_child_base_id, 'message_parent_base_id' => $message_parent_base_id]);
        }

        $main->save();

        return redirect()->route('main.index');
    }

    function update(Request $request, Main $main)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        // Если данные изменились - выполнить проверку
        if (!(($main->link_id == $request->link_id)
            and ($main->child_item_id == $request->child_item_id)
            and ($main->parent_item_id == $request->parent_item_id))) {
            $request->validate($this->rules());
        }

        $data = $request->except('_token', '_method');

        $main->fill($data);

        $main->link_id = $request->link_id;
        $main->child_item_id = $request->child_item_id;
        $main->parent_item_id = $request->parent_item_id;

        $message_child_base_id = '';
        if (!($main->link->child_base_id == $main->child_item->base_id)) {
            $message_child_base_id = trans('main.base') . ' != ' . $main->link->child_base->name_lang_1;
        }

        $message_parent_base_id = '';
        if (!($main->link->parent_base_id == $main->parent_item->base_id)) {
            $message_parent_base_id = trans('main.base') . ' != ' . $main->link->parent_base->name_lang_1;
        }

        if (($message_child_base_id != '') || ($message_parent_base_id != '')) {
            // повторный вызов формы
            return redirect()->back()
                ->withInput()
                ->withErrors(['message_child_base_id' => $message_child_base_id, 'message_parent_base_id' => $message_parent_base_id]);
        }

        $main->save();

        return redirect()->route('main.index');
    }

    function edit(Main $main)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        // исключая вычисляемые поля
        return view('main/edit', ['main' => $main, 'links' => Link::all()->where('parent_is_parent_related', false)]);
    }

    function delete_question(Main $main)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        return view('main/show', ['type_form' => 'delete_question', 'main' => $main]);
    }

    function delete(Main $main)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $main->delete();
        return redirect()->route('main.index');
    }

    static function get_parent_item_from_main($child_item_id, $link_id)
    {
        $item = null;
        //$main = Main::all()->where('child_item_id', $child_item_id)->where('link_id', $link_id)->first();
        //$main = Main::where(['child_item_id'=> $child_item_id, 'link_id'=> $link_id])->first();
        //$main = $cursor->where('child_item_id', $child_item_id)->where('link_id', $link_id)->first();
        $main = Main::where('child_item_id', $child_item_id)->where('link_id', $link_id)->first();
        if ($main) {
            $item = $main->parent_item;
        }
        return $item;
    }

    // вывод объекта по имени главного $item и $link
    static function view_info($child_item_id, $link_id)
    {
        // Нужно
        $item = null;
        $item_find = Item::find($child_item_id);
        $link_find = Link::find($link_id);
        $view_enable = false;
        //
        if ($item_find && $link_find) {
            // Если установлено 'Доступно от значения поля Логический'
            if ($link_find->parent_is_enabled_boolean_value) {
                $link_bool = Link::find($link_find->parent_enabled_boolean_value_link_id);
                if ($link_bool) {
                    // Находим $item_bool
                    $item_bool = self::get_parent_item_from_main($child_item_id, $link_bool->id);
                    if ($item_bool) {
                        // Если checked, то показывать поле
                        if ($item_bool->boolval()['value']) {
                            $view_enable = true;
                        } else {
                            $view_enable = false;
                        }
                    }
                }
            } else {
                $view_enable = true;
            }
            // Иначе возвращается $item = null
            if ($view_enable == true) {
                // Выводить связанное поле
                if ($link_find->parent_is_parent_related == true) {
                    $link_related_result = Link::find($link_find->parent_parent_related_result_link_id);
                    if ($link_related_result) {
                        $item = ItemController::get_parent_item_from_calc_child_item($item_find, $link_find, true)['result_item'];
                    }
                    // Выводить поле вычисляемой таблицы
                } elseif ($link_find->parent_is_output_calculated_table_field == true) {
                    $item = ItemController::get_item_from_parent_output_calculated_table($item_find, $link_find);
                    // Иначе - обычный вывод поля по $child_item_id, $link_id
                } else {
                    $item = self::get_parent_item_from_main($child_item_id, $link_id);
                }
            }
        }
        return $item;
    }

    // Вывод проекта по $link и $current_project
    // Функции calc_link_project(), calc_set_project(), calc_relit_children_projects() похожи
    static function calc_link_project(Link $link, Project $current_project)
    {
        $project = null;
        if ($link->parent_relit_id == 0){
            // Возвращается текущий проект
            $project = $current_project;
        }
        else{
            // Поиск взаимосвязанного проекта
            $relit = Relit::find($link->parent_relit_id);
            if ($relit){
                $relip = Relip::where('relit_id', $relit->id)->where('child_project_id', $current_project->id)->first();
                if ($relip){
                    $project = $relip->parent_project;
                }
            }
        }
        if ($project == null){
            dd(trans('main.check_project_properties_projects_parents_are_not_set') . '!');
            //return view('message', ['message' => trans('main.info_user_changed')]);
            //return('Не найден проект');
        }
        return $project;
    }

        // вывод проекта по $set и $current_project
        // Функции calc_link_project(), calc_set_project(), calc_relit_children_projects() похожи
        static function calc_set_project(Set $set, Project $current_project)
        {
            $project = null;
            if ($set->relit_to_id == 0){
                // Возвращается текущий проект
                $project = $current_project;
            }
            else{
                // Поиск взаимосвязанного проекта
                $relit = Relit::find($set->relit_to_id);
                if ($relit){
                    $relip = Relip::where('relit_id', $relit->id)->where('child_project_id', $current_project->id)->first();
                    if ($relip){
                        $project = $relip->parent_project;
                    }
                }
            }
            if ($project == null){
                dd(trans('main.check_project_properties_projects_parents_are_not_set') . '!');
            }
            return $project;
        }
    
        // вывод проекта по $relit и $current_project
        // Функции calc_link_project(), calc_set_project(), calc_relit_children_projects() похожи
        static function calc_relit_children_id_projects(Relit $relit, Project $current_project)
        {
            // Поиск взаимосвязанных детских проектов
            $children_id_projects = Relip::select(DB::Raw('relips.child_project_id as project_id'))
            ->where('relips.relit_id', '=', $relit->id)
            ->where('relips.parent_project_id', '=', $current_project->id)
            ->get();
            //if ($children_id_projects == null){
            //    dd(trans('main.projects_children_are_not_set') . '!');
            //}
            return $children_id_projects;
        }

    function get_array_relits(Template $template)
    {
        $array_relits = [];
        $child_relits = $template->child_relits;
        // 0 - текущий шаблон (нужно)
        $array_relits[0] = $template->name() . ' (' . trans('main.current_template') . ')';
        foreach ($child_relits as $relit) {
            $array_relits[$relit->id] = $relit->parent_template->name();
        }
        return $array_relits;
    }

    function get_template_name_from_relit_id($relit_id, $current_template_id)
            {
                $template_name = '';
                // Вычисление $template
                $template_id = null;
                if ($relit_id == 0){
                      $template = Template::findOrFail($current_template_id);
                      $template_name = $template->name() . ' (' . trans('main.current_template') . ')';
                    }
                else{
                    $relit = Relit::find($relit_id);
                    if ($relit){
                        $template = Template::findOrFail($relit->parent_template_id);
                        $template_name = $template->name();
                    }
                }
                return $template_name;
            }

}
