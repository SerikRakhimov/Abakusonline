<?php

namespace App\Http\Controllers;

use App\Http\Controllers\GlobalController;
use App\Http\Controllers\MainController;
use App\Rules\IsUniqueRoba;
use Exception;
use Illuminate\Support\Facades\App;
use App\Models\User;
use App\Models\Base;
use App\Models\Item;
use App\Models\Link;
use App\Models\Main;
use App\Models\Set;
use App\Models\Project;
use App\Models\Role;
use App\Models\Text;
use App\Models\Level;
use App\Models\Relit;
use App\Models\Relip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Rules\IsUniqueItem;
use function PHPUnit\Framework\isNull;

class ItemController extends Controller
{
//    protected function rules(Request $request, $project_id, $base_id)
//    {
////    https://qna.habr.com/q/342501
////    use Illuminate\Validation\Rule;
////
////        public function rules()
////    {
////        $rules = [
////            'name_eng'=>'required|string',
////            'field1' => [
////                'required',
////                Rule::unique('table_name')->where(function ($query) {
////                    $query->where('field2', $this->get('field2'));
////                })
////            ],
////        ];
////
////        return $rules;
////    }
////        return [
////            'base_id' => 'exists:bases,id|unique_with: items, base_id, name_lang_0',
////            'name_lang_0' => ['required', 'max:255', 'unique_with: items, base_id, name_lang_0']
////        ];
//        // exists:table,column
//        // поле должно существовать в заданной таблице базе данных.
//        // 1000 - размер картинки и файла
//        //'name_lang_0' => ['max:1000'] не использовать, т.к. при загрузке изображений и документов мешает
//        return [
//                        'code' => ['required', new IsUniqueItem($request, $project_id, $base_id)],
//        ];
//    }

//    protected function name_lang_boolean_rules()
//    {
//        return [
//            'base_id' => 'exists:bases,id|unique_with: items, base_id, name_lang_0',
//            'name_lang_0' => ['unique_with: items, base_id, name_lang_0']
//        ];
//
//    }

    protected function code_rules(Request $request, $project_id, $base_id)
    {
//        return [
//            'name_lang_0' => ['required', 'max:255']
//        ];
        return [
            'code' => ['required', new IsUniqueItem($request, $project_id, $base_id)],
        ];
    }


//    protected function img_rules($input_img, $maxfilesize)
//    {
////        return [
////            $input_img => ['max:' . $maxfilesize]
////        ];
//        return [
//            '16' => ['max:25']
//        ];
//    }

    protected function name_lang_rules()
    {
        return [
            // 255 - макс.размер строковых полей name_lang_x в items
            'name_lang_0' => ['max:255']
        ];
    }

    // Две переменные $sort_by_code и $save_by_code нужны,
    // для случая: когда установлен фильтр (неважно по коду или по наименованию):
    // можно нажимать на заголовки "Код"/"Наименование" - количество записей на экране то же оставаться должно,
    // меняется только сортировка
    // Использовать знак вопроса "/{base_id?}" (web.php)
    //              равенство null "$base_id = null" (ItemController.php),
    // иначе ошибка в function search_click() - open('{{route('item.browser', '')}}' ...
    //function browser($link_id, $project_id = null, $role_id = null, $item_id = null, bool $sort_by_code = true, bool $save_by_code = true, $search = "")
//    function browser($link_id, $project_id = null, $role_id = null, $item_id = null, $order_by = null, $filter_by = null, $search = "")
    function browser($link_id, $project_id = null, $role_id = null, $relit_id = null, $item_id = null, $order_by = null, $filter_by = null, $search = "")
    {
        $link = Link::findOrFail($link_id);
        $base_id = $link->parent_base_id;
        $base = Base::findOrFail($base_id);
        $project = Project::findOrFail($project_id);
        $role = Role::findOrFail($role_id);
        $item = null;
        if ($item_id) {
            if ($item_id != 0) {
                $item = Item::findOrFail($item_id);
            }
        }
        $base_right = GlobalController::base_right($base, $role, $relit_id);
        $name = BaseController::field_name();
        // Только в функции browser() используется 'Показывать записи с признаком "В истории" при просмотре списков выбора',
        // в других функциях выборки таблиц данных подразумевается 'Показывать записи с признаком "В истории" при просмотре списков'
        $items = self::get_items_main($base, $project, $role, $relit_id, $base_right['is_brow_hist_records_enable'], $link, $item_id, false)['items_no_get'];
        if ($order_by == null) {
            $order_by = "name";
        }
        if ($order_by == "") {
            $order_by = "name";
        }
        if ($filter_by == null) {
            $filter_by = "name";
        }
        if ($filter_by == "") {
            $filter_by = "name";
        }
        if ($items != null) {
            if ($order_by) {
                if ($order_by == 'name') {
                    $items = $items->orderBy($name);
                } elseif ($order_by == 'code') {
                    if ($base->is_code_needed == true) {
                        // Сортировка по коду
                        if ($base->is_code_number == true) {
                            // Сортировка по коду числовому
                            $items = $items->selectRaw("code*1  AS code_value")->orderBy('code_value');
                        } else {
                            $items = $items->orderBy('code');
                        }
                    }
                }
            }
            if ($filter_by && $search != "") {
                if ($filter_by != "") {
                    if ($filter_by == 'name') {
                        $items = $items->where('items.' . $name, 'LIKE', '%' . $search . '%');
                    } else {
                        $items = $items->where('items.' . $filter_by, 'LIKE', '%' . $search . '%');
                    }
                }
            }
//            if ($sort_by_code == true) {
//                if ($base->is_code_number == true) {
//                    // Сортировка по коду числовому
////                $items = Item::selectRaw("*, code*1  AS code_value")
////                    ->where('base_id', $base_id)->where('project_id', $project->id)->orderBy('code_value');
////                $items = $items->selectRaw("*, code*1  AS code_value")
////                    ->where('base_id', $base_id)->where('project_id', $project->id)->orderBy('code_value');
//                    //$items = $items->where('base_id', $base_id)->where('project_id', $project->id)->orderBy('code');
//                    $items = $items->orderBy('code');
//                } else {
//                    // Сортировка по коду строковому
//                    //$items = Item::where('base_id', $base_id)->where('project_id', $project->id)->orderByRaw(strval('code'));
//                    //$items = $items->where('base_id', $base_id)->where('project_id', $project->id)->orderBy('code');
//                    $items = $items->orderBy('code');
//                }
//            } else {
//                // Сортировка по наименованию
////            $items = Item::where('base_id', $base_id)->where('project_id', $project->id)->orderByRaw(strval($name));
// //               $items = $items->where('base_id', $base_id)->where('project_id', $project->id)->orderBy($name);
//                $items = $items->orderBy($name);
//            }
//        }
//        if ($items != null) {
            // Такая же проверка и в GlobalController (function items_right(), items_check_right()),
            // в ItemController (function next_all_links_mains_calc(), browser(), get_items_for_link(), get_items_ext_edit_for_link())
            //            if ($base_right['is_list_base_byuser'] == true) {
//                $items = $items->where('created_user_id', GlobalController::glo_user_id());
//            }
//            if ($search != "") {
//                if ($save_by_code == true) {
//                    $items = $items->where('code', 'LIKE', '%' . $search . '%');
//                } else {
//                    $items = $items->where($name, 'LIKE', '%' . $search . '%');
//                }
//            }
        }
//        $ids = $collection->keys()->toArray();
//
//        $items = Item::whereIn('id', $ids)
//            ->orderBy(\DB::raw("FIELD(id, " . implode(',', $ids) . ")"));
        if ($items) {
//            return view('item/browser', ['link' => $link, 'base' => $base, 'project' => $project, 'role' => $role, 'item' => $item, 'base_right' => $base_right, 'sort_by_code' => $sort_by_code, 'save_by_code' => $save_by_code,
//                'items' => $items->paginate(60), 'search' => $search]);
//            return view('item/browser', ['link' => $link, 'base' => $base, 'project' => $project, 'role' => $role, 'item' => $item, 'base_right' => $base_right, 'order_by' => $order_by, 'filter_by' => $filter_by,
//                'items' => $items->paginate(60), 'search' => $search]);
            return view('item/browser', ['link' => $link, 'base' => $base, 'project' => $project, 'role' => $role, 'relit_id' => $relit_id, 'item' => $item, 'base_right' => $base_right,
                'order_by' => $order_by, 'filter_by' => $filter_by,
                'items' => $items->paginate(60), 'search' => $search]);
        } else {
            return view('message', ['message' => trans('main.no_data')]);
        }
    }

//    function index()
//    {
//        $items = null;
//        $index = array_search(App::getLocale(), config('app.locales'));
//        if ($index !== false) {   // '!==' использовать, '!=' не использовать
//            switch ($index) {
//                case 0:
//                    //$items = Item::all()->sortBy('name_lang_0');
//                    $items = Item::where('project_id', GlobalController::glo_project_id())->orderBy('base_id')->orderBy('name_lang_0');
//                    break;
//                case 1:
//                    //$items = Item::all()->sortBy(function($row){return $row->name_lang_1 . $row->name_lang_0;});
//                    $items = Item::where('project_id', GlobalController::glo_project_id())->orderBy('base_id')->orderBy('name_lang_1')->orderBy('name_lang_0');
//                    break;
//                case 2:
//                    $items = Item::where('project_id', GlobalController::glo_project_id())->orderBy('base_id')->orderBy('name_lang_2')->orderBy('name_lang_0');
//                    break;
//                case 3:
//                    $items = Item::where('project_id', GlobalController::glo_project_id())->orderBy('base_id')->orderBy('name_lang_3')->orderBy('name_lang_0');
//                    break;
//            }
//        }
//        session(['links' => ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/' . request()->path()]);
//        return view('item/index', ['items' => $items->paginate(60)]);
//    }
//  $base_index_page_current = текущая страница, $body_link_page_current = 0, $body_all_page_current = 0
    function base_index(Base $base, Project $project, Role $role, $relit_id)
    {
        if (GlobalController::check_project_user($project, $role) == false) {
            return view('message', ['message' => trans('main.info_user_changed')]);
        }

        $links_info = ItemController::links_info($base, $role, $relit_id);
        if ($links_info['error_message'] != "") {
            return view('message', ['message' => $links_info['error_message']]);
        }
        $relip_project = GlobalController::calc_relip_project($relit_id, $project);

        $base_right = GlobalController::base_right($base, $role, $relit_id);

////      Похожая проверка в ItemController::base_index() и project/start.php
////      Используется 'is_list_base_calc' в ext_show.php и ItemController::item_index()
//        if ($base_right['is_list_base_calc'] == false) {
//            return view('message', ['message' => trans('main.no_access')]);
//        }
        // Похожая проверка в GlobalController::get_project_bases(), ItemController::base_index() и project/start.php
        if ($base_right['is_bsmn_base_enable'] == false) {
            //return view('message', ['message' => trans('main.no_access')]);
            // Вызов главного меню
            return redirect()->route('project.start', ['project' => $project, 'role' => $role]);
        }

        // Используется $relip_project
        // Вызывается без параметров '($mains_item_id, $mains_link_id, $parent_proj и $current_item_id)', чтобы проверка сработала '$base_right['is_twt_enable'] == true'
        $items_right = GlobalController::items_right($base, $relip_project, $role, $relit_id);
        $items = $items_right['items'];
        if ($items) {
            $is_table_body = true;
            $items = $items->paginate(60, ['*'], 'base_index_page');
            $its_page = GlobalController::its_page($role, $relit_id, $items_right['links'], $items);
            $base_index_page_current = $items->currentPage();
            $body_link_page_current = 0;
            $body_all_page_current = 0;

//            // Похожая проверка в GlobalController::get_project_bases(), ItemController::base_index() и project/start.php
//            // Две проверки использовать
//            if ($base_right['is_list_base_calc'] == false || $base_right['is_bsin_base_enable'] == false) {
//                return view('message', ['message' => trans('main.no_access')]);
//            }
            // Нужно '$redirect_item_index = false;'
            $redirect_item_index = false;
            if ($base_right['is_skip_count_records_equal_1_base_index'] == true) {
                if (count($items) == 1) {
                    $item_redirect = $items->first();
                    if ($item_redirect) {
                        $redirect_item_index = true;
                        return redirect()->route('item.item_index', ['project' => $project, 'item' => $item_redirect, 'role' => $role,
                            'usercode' => GlobalController::usercode_calc(),
                            'relit_id' => $relit_id
                        ]);
                    }
                }
            }
            if ($redirect_item_index == false) {
                session(['base_index_previous_url' => ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/' . request()->path()]);
                // Нужно 'GlobalController::const_null()' 'null/null/null/', иначе в строке с параметрами будет '///' (дает ошибку)
                return view('item/base_index', ['base_right' => $base_right, 'base' => $base, 'project' => $project, 'role' => $role, 'relit_id' => $relit_id,
                    'string_link_ids_current' => GlobalController::const_null(),
                    'string_item_ids_current' => GlobalController::const_null(),
                    'string_relit_ids_current' => GlobalController::const_null(),
                    'string_vwret_ids_current' => GlobalController::const_null(),
                    'string_all_codes_current' => GlobalController::const_null(),
                    'string_current' => self::string_zip_current_next(GlobalController::const_null(),
                        GlobalController::const_null(),
                        GlobalController::const_null(),
                        GlobalController::const_null(),
                        GlobalController::const_null()),
                    'items' => $items,
                    'its_page' => $its_page,
                    'links_info' => $links_info,
                    'is_table_body' => $is_table_body,
                    'base_index_page' => $base_index_page_current,
                    'body_link_page' => $body_link_page_current,
                    'body_all_page' => $body_all_page_current
                ]);
            }
        } else {
            // Нужная команда (при доступе без входа/регистрации) , не удалять
            return view('message', ['message' => trans('main.no_access_for_unregistered_users')]);
        }

    }

    // $usercode нужно, чтобы проверять на текущего пользователя, если у $item проект внешний,
    // и чтобы невозможно было скопировать адресную строку с item_index с параметрами
    //  и вставить в адресную строку другого пользователя платформы www.abakusonline.com
    // - должно работать только на текущем проекте
    // $view_link передается в функцию item_index(),
    // может иметь значения null, GlobalController::par_link_const_textnull(), GlobalController::par_link_const_text_base_null() - вызов из base_index.php)
    // $current_link расчитывается в item_index(), затем $view_link присваивается $current_link и передается в index_item.php
    // $par_link используется (index_item.php, list\table.php, list\all.php, ext_show.php, ext_edit.php)
    // для вызова 'item.ext_show', 'item.ext_show', 'item.ext_create', get:'item.ext_edit', 'item.ext_store', put:'item.ext_edit', 'item.ext_delete', 'item.ext_delete_question'
    // $prev_base_index_page, $prev_body_link_page, $prev_body_all_page - "предыдущие"/"текущие" номера страниц при пагинации
    //      используются как параметры при вызове 'item.ext_show' в конце функции item_index();
    //  $base_index_page_current = 0, $body_link_page_current = текущая страница, $body_all_page_current = текущая страница,
    //      используются как параметры при вызове 'item_index.php' в конце функции item_index();
    //  'base_index_page', 'body_link_page', 'body_all_page' - названия переменных пагинации,
    //      используются/вызываются при вызове 'route('item.item_index)' в конце функций ext_store(), ext_update(), ext_delete();
    // $view_ret_id - это $relit_id для просмотра нескольких взаимосвязанных шаблонов/проектов(если они есть) в body-списке

//    function item_index(Project $project, Item $item, Role $role, $usercode, $relit_id, $view_link = null,
//                                $string_link_ids_current = '', $string_item_ids_current = '', $string_all_codes_current = '',
//                                $prev_base_index_page = 0, $prev_body_link_page = 0, $prev_body_all_page = 0, $view_ret_id = null)
    function item_index(Project $project, Item $item, Role $role, $usercode, $relit_id,
                                $called_from_button = 0,
                                $view_link = null,
                                $string_current = '',
                                $prev_base_index_page = 0, $prev_body_link_page = 0, $prev_body_all_page = 0, $view_ret_id = null)
    {
        // В форме item_index.php выводятся данные:
        // 1) по одному $current_link и $view_ret_id
        // 2) все записи по $view_ret_id.
        // Используются основные правила:
        // 1) $link->parent_base->project->template_id = $item->base->template_id
        // 2) $relip->child_project_id = $project->id
        //    (соответственно $relip->child_project->template_id = $project->template_id)
        //    проекты $relip->parent_project_id находятся из существующих взаимосвязанных проектов $project.

        if (GlobalController::check_project_item_user($project, $item, $role, $usercode) == false) {
            return view('message', ['message' => trans('main.no_access') . ' (check_project_item_user)']);
        }

        // Нужно
        if (GlobalController::find_base_from_relit_id($item->base_id, $relit_id, $project->template_id) == false) {
            return view('message', ['message' => trans('main.no_access') . ' Base = ' . $item->base->name() . ', relit_id = ' . $relit_id]);
        }

        $base_right = GlobalController::base_right($item->base, $role, $relit_id);
//      Похожая проверка в ItemController::base_index() и project/start.php
//      Используется 'is_list_base_calc' в ext_show.php и ItemController::item_index()
        if ($base_right['is_list_base_calc'] == false) {
            return view('message', ['message' => trans('main.no_access') . ' ($base_right["is_list_base_calc"])']);
        }

        // Сохранить значения для вызова ext_show.php
        $save_relit_id = $relit_id;
        $save_view_ret_id = $view_ret_id;
        $save_view_link = $view_link;
        $save_string_current = $string_current;

        $string_unzip_current_next = self::string_unzip_current_next($string_current);
        $string_link_ids_current = $string_unzip_current_next['string_link_ids'];
        $string_item_ids_current = $string_unzip_current_next['string_item_ids'];
        $string_relit_ids_current = $string_unzip_current_next['string_relit_ids'];
        $string_vwret_ids_current = $string_unzip_current_next['string_vwret_ids'];
        $string_all_codes_current = $string_unzip_current_next['string_all_codes'];

        // Пустой массив
        $tree_array = array();
        if (($string_link_ids_current != "")
            && ($string_item_ids_current != "")
            && ($string_relit_ids_current != "")
            && ($string_vwret_ids_current != "")
            && ($string_all_codes_current != "")
            && ($string_link_ids_current != GlobalController::const_null())
            && ($string_item_ids_current != GlobalController::const_null())
            && ($string_relit_ids_current != GlobalController::const_null())
            && ($string_vwret_ids_current != GlobalController::const_null())
            && ($string_all_codes_current != GlobalController::const_null())) {
            $tree_array = self::calc_tree_array($role,
                $string_link_ids_current,
                $string_item_ids_current,
                $string_relit_ids_current,
                $string_vwret_ids_current,
                $string_all_codes_current);
        }

        // Если есть признак where('parent_is_tree_top', true)
// Меняется только $item при вызове из base_index.php    ($view_link == null)
// Было:  https://www.abakusonline.com/item/item_index/42/9220/34/18/0/text_base_null/null;null;null;null/1/0/0/0
// Стало: https://www.abakusonline.com/item/item_index/42/9822/34/18/0/text_base_null/null;null;null;null/1/0/0/0
// $item_tree_top становится верхним в иерархии на экране, Связь автоматические подбирает $view_link и $view_ret_id
        $item_change = false;
        $link_tree_top = $item->base->child_links->where('parent_is_tree_top', true)->first();
        if ($link_tree_top) {
            $item_tree_top = GlobalController::view_info($item->id, $link_tree_top->id);
            if ($item_tree_top) {
                //$relit_tree_top_id = $link_tree_top->parent_relit_id;
                $relit_tree_top_id = GlobalController::get_parent_relit_from_template_id($project->template_id, $item_tree_top->base->template_id);
                if ($relit_tree_top_id != -1) {
                    $base_tree_top_right = GlobalController::base_right($item_tree_top->base, $role, $relit_tree_top_id);
                    if ($base_tree_top_right['is_view_prev_next'] == false) {
                        // Поиск $item_tree_top->id в массиве $tree_array
                        $found_tree_top = false;
                        foreach ($tree_array as $value) {
                            if ($value['item_id'] == $item_tree_top->id) {
                                $found_tree_top = true;
                                break;
                            }
                        }
                        // Если не найдено
                        if ($found_tree_top == false) {
                            // Нужно
                            $item = $item_tree_top;
                            $base_right = $base_tree_top_right;
                            $relit_id = $relit_tree_top_id;
                            //$view_link = $link_tree_top->id;
                            // Эти переменные должны посчитаться далее по алгоритмам
                            $view_link = null;
                            $view_ret_id = null;
                            $item_change = true;
                        }
                    }
                }
            }
        }

        if (1 == 2) {
            // Используется 'is_list_base_calc' в ext_show.php и ItemController::item_index()
            // Нужно, после вызова calc_tree_array()
            if ($item_change == false) {
                if (empty($tree_array)) {
                    if ($base_right['is_bsin_base_enable'] == false) {
                        return view('message', ['message' => trans('main.no_access') . ' ($base_right["is_bsin_base_enable"])']);
                    }
                }
            }
        }
        // Нужно
        if ($view_link == null || $view_link == GlobalController::par_link_const_textnull() || $view_link == GlobalController::par_link_const_text_base_null()) {
            // Нужно '$view_link = null;'
            $view_link = null;
        } else {
            $view_link = Link::find($view_link);
        }

        // Нужно
        $view_ret_id = GlobalController::set_relit_id($view_ret_id);

        // Нужно
        $string_link_ids_next = $string_link_ids_current;
        $string_item_ids_next = $string_item_ids_current;
        $string_relit_ids_next = $string_relit_ids_current;
        $string_vwret_ids_next = $string_vwret_ids_current;
        $string_all_codes_next = $string_all_codes_current;

        $relip_project = GlobalController::calc_relip_project($relit_id, $project);

        $child_links = $item->base->child_links->sortBy('parent_base_number');
        $child_mains_link_is_calcname = self::mains_link_is_calcname($item, $role, $relit_id, $tree_array);

        $para_child_mains_link_is_calcname = null;
        // Одинаковые проверки должны быть в ItemController::item_index() и в item_index.php
        // здесь равно false
        // Исключить link_id из $child_mains_link_is_calcname в итоговом результате функции links_info()
        // if (GlobalController::is_base_calcnm_correct_check($item->base, $base_right) == false) {
        if (GlobalController::is_base_calcname_check($item->base, $base_right) == false) {
            $para_child_mains_link_is_calcname = $child_mains_link_is_calcname;
        }
        // Нужно передать в функцию links_info() $item
        $child_links_info = self::links_info($item->base, $role, $relit_id,
            $item, null, true, $tree_array, $para_child_mains_link_is_calcname);

        // Похожие строки в ItemController::item_index() и ItemController::ext_update()
        // Используется последний элемент массива $tree_array
        $tree_array_last_link_id = null;
        $tree_array_last_item_id = null;
        $tree_array_last_string_previous = self::string_null();
        $count_tree_array = count($tree_array);
        // "$count_tree_array > 0 & !$item_change" используется
        if ($count_tree_array > 0 & !$item_change) {
            // ' - 1' т.к. нумерация массива $tree_array с нуля начинается
            $tree_array_last_link_id = $tree_array[$count_tree_array - 1]['link_id'];
            $tree_array_last_item_id = $tree_array[$count_tree_array - 1]['item_id'];
            $tree_array_last_string_previous = $tree_array[$count_tree_array - 1]['string_previous'];
        }

        // "Шапка" документа
        // Используется фильтр на равенство одному $item->id (для вывода таблицы из одной строки)
        $items_right = GlobalController::items_right($item->base, $item->project, $role, $relit_id, null, null, null, null, $item->id);
//        if (empty($tree_array)) {
//            $items_right = GlobalController::items_right($item->base, $item->project, $role, $relit_id, null, null, null, null, $item->id);
//        } else {
//            //$items_right = GlobalController::items_right($item->base, $item->project, $role, $relit_id, $tree_array_last_item_id, $tree_array_last_link_id, $project, $view_ret_id, $item->id);
//            // $relit_id нужно передавать, предпоследний параметр, нужно так, чтобы правильно данные выбирались
//            $items_right = GlobalController::items_right($item->base, $item->project, $role, $relit_id, $tree_array_last_item_id, $tree_array_last_link_id, $project, $relit_id, $item->id);
//        }
        if (count($items_right['items']->get()) != 1) {
            return view('message', ['message' => trans('main.no_access') . ' (count($items_right[items]->get()) != 1)']);
        }

//        // 'itget' нужно
//        $items = $items_right['itget'];
        $items = $items_right['items'];
        // Все нужно
        $prev_item = null;
        $next_item = null;
        if ($base_right['is_view_prev_next'] == true) {
            $prev_item = $items_right['prev_item'];
            $next_item = $items_right['next_item'];
        }

        // Используется $project, $view_ret_id, false
        $next_all_links_mains_calc = self::next_all_links_mains_calc($project, $item, $role, $relit_id, $view_link, $view_ret_id, $tree_array, $base_right, $called_from_button);
        $next_all_links = $next_all_links_mains_calc['next_all_links'];
        $next_all_mains = $next_all_links_mains_calc['next_all_mains'];
        $next_all_is_create = $next_all_links_mains_calc['next_all_is_create'];
        $next_all_is_all_create = $next_all_links_mains_calc['next_all_is_all_create'];
        $next_all_is_calcname = $next_all_links_mains_calc['next_all_is_calcname'];
        $next_all_is_code_enable = $next_all_links_mains_calc['next_all_is_code_enable'];
        $next_all_is_enable = $next_all_links_mains_calc['next_all_is_enable'];
        $string_link_ids_array_next = $next_all_links_mains_calc['string_link_ids_array_next'];
        $string_item_ids_array_next = $next_all_links_mains_calc['string_item_ids_array_next'];
        $string_relit_ids_array_next = $next_all_links_mains_calc['string_relit_ids_array_next'];
        $string_vwret_ids_array_next = $next_all_links_mains_calc['string_vwret_ids_array_next'];
        $string_all_codes_array_next = $next_all_links_mains_calc['string_all_codes_array_next'];
        $string_array_next = $next_all_links_mains_calc['string_array_next'];
        $message_ln_array_info = $next_all_links_mains_calc['message_ln_array_info'];
        $message_ln_link_array_item = $next_all_links_mains_calc['message_ln_link_array_item'];
        $array_relips = $next_all_links_mains_calc['array_relips'];
        $current_link = $next_all_links_mains_calc['current_link'];
        $view_ret_id = $next_all_links_mains_calc['current_vw_ret_id'];
        // Нужно
        $view_ret_id = GlobalController::set_relit_id($view_ret_id);
        $message_ln_info = '';
        $message_ln_validate = '';

        $base_index_page_current = 0;
        $body_link_page_current = 0;
        $body_all_page_current = 0;

        $child_body_links_info = null;
        $base_body_right = null;
        $body_items = null;
        $its_body_page = null;
        if ($current_link) {
//            $base_body_right = GlobalController::base_link_right($current_link, $role, $view_ret_id, true, $view_ret_id);
            // 'true' нужно в параметрах
            $base_body_right = GlobalController::base_link_right($current_link, $role, $view_ret_id, true);
            // Исключить переданный $nolink - $current_link
//          $child_body_links_info = self::links_info($current_link->child_base, $role, $view_ret_id, null, $current_link);
            $child_body_links_info = self::links_info($current_link->child_base, $role, $view_ret_id, null, $current_link, false, $tree_array);
            if (count($child_body_links_info['link_id_array']) == 0) {
//                Если тип-вычисляемое наименование и Показывать Основу с вычисляемым наименованием
//                           или если тип-не вычисляемое наименование
//                           похожая проверка в list\table.php, ItemController::item_index() и ext_show.php
                // = false
                if (GlobalController::is_base_calcname_check($current_link->child_base, $base_body_right) == false) {
                    // Не исключать переданный $nolink - null
                    // В таблице 'item_body_base' должно быть как минимум два столбца: номер строки с вызовом 'item.show'
                    // и вычисляемое наименование, код, связи для вызова 'item.item_index'.
                    // Проверка выше для этого нужна, чтобы как минимум один столбец был для вызова 'item.item_index'.
                    $child_body_links_info = self::links_info($current_link->child_base, $role, $view_ret_id, null, null);
                }
            }
            $relip_body_project = GlobalController::calc_relip_project($view_ret_id, $project);
            // Используется $relip_body_project, $view_ret_id
            $items_body_right = GlobalController::items_right($current_link->child_base, $relip_body_project, $role, $relit_id, $item->id, $current_link->id, $project, $view_ret_id);
            $body_items = $items_body_right['items']->paginate(60, ['*'], 'body_link_page');
            $its_body_page = GlobalController::its_page($role, $relit_id, $items_body_right['links'], $body_items);
            // Нужно
            //$next_all_mains = null;

            // $item, $current_link присоединяются к списку $tree_array
            // Нужно '$current_link' передавать

            $string_current_next_ids = self::calc_string_current_next_ids($tree_array, $item, $current_link, $relit_id, $view_ret_id, GlobalController::const_allfalse());
            $string_link_ids_current = $string_current_next_ids['string_current_link_ids'];
            $string_item_ids_current = $string_current_next_ids['string_current_item_ids'];
            $string_relit_ids_current = $string_current_next_ids['string_current_relit_ids'];
            $string_vwret_ids_current = $string_current_next_ids['string_current_vwret_ids'];
            $string_all_codes_current = $string_current_next_ids['string_current_all_codes'];
            $string_link_ids_next = $string_current_next_ids['string_next_link_ids'];
            $string_item_ids_next = $string_current_next_ids['string_next_item_ids'];
            $string_relit_ids_next = $string_current_next_ids['string_next_relit_ids'];
            $string_vwret_ids_next = $string_current_next_ids['string_next_vwret_ids'];
            $string_all_codes_next = $string_current_next_ids['string_next_all_codes'];
            $message_ln_calc = self::message_ln_calc($project, $item, $current_link);
            $message_ln_info = $message_ln_calc['message_ln_info'];
            $message_ln_validate = $message_ln_calc['message_ln_validate'];
        } else {
            if ($next_all_mains) {
                $next_all_mains = $next_all_mains->paginate(60, ['*'], 'body_all_page');
            }
        }

        // Команды ниже нужны
        $string_link_ids_current = GlobalController::set_str_const_null($string_link_ids_current);
        $string_item_ids_current = GlobalController::set_str_const_null($string_item_ids_current);
        $string_relit_ids_current = GlobalController::set_str_const_null($string_relit_ids_current);
        $string_vwret_ids_current = GlobalController::set_str_const_null($string_vwret_ids_current);
        $string_all_codes_current = GlobalController::set_str_const_null($string_all_codes_current);
        $string_link_ids_next = GlobalController::set_str_const_null($string_link_ids_next);
        $string_item_ids_next = GlobalController::set_str_const_null($string_item_ids_next);
        $string_relit_ids_next = GlobalController::set_str_const_null($string_relit_ids_next);
        $string_vwret_ids_next = GlobalController::set_str_const_null($string_vwret_ids_next);
        $string_all_codes_next = GlobalController::set_str_const_null($string_all_codes_next);
        // Проверки ниже нужны
        // При вызове item_index.php должно быть либо так '$body_items!=null и $next_all_mains=null',
        // либо так '$body_items=null и $next_all_mains!=null'
        // Это регулируется в функции ItemController::item_index()
        if ($body_items) {
            $body_link_page_current = $body_items->currentPage();
        }
        if ($next_all_mains) {
            $body_all_page_current = $next_all_mains->currentPage();
        }

        // Нужно
        $view_link = $current_link;
        $string_current = self::string_zip_current_next($string_link_ids_current, $string_item_ids_current, $string_relit_ids_current, $string_vwret_ids_current, $string_all_codes_current);
        $string_next = self::string_zip_current_next($string_link_ids_next, $string_item_ids_next, $string_relit_ids_next, $string_vwret_ids_next, $string_all_codes_next);

        // Не удалять
        // Перенаправление на "route('item.ext_show'" при "count($next_all_links) == 0"
        if (count($next_all_links) == 0) {
//            "'par_link' => GlobalController::set_par_view_link_null($tree_array_last_link_id)" неправильно
//            "'par_link' => $tree_array_last_link_id" правильно
            return redirect()->route('item.ext_show', [
                'item' => $item,
                'project' => $project,
                'role' => $role,
                'usercode' => GlobalController::usercode_calc(),
                'relit_id' => GlobalController::set_relit_id($save_relit_id),
                'string_current' => $tree_array_last_string_previous,
                'heading' => intval(false),
                'base_index_page' => $prev_base_index_page,
                'body_link_page' => $prev_body_link_page,
                'body_all_page' => $prev_body_all_page,
                'view_link' => GlobalController::set_par_view_link_null($tree_array_last_link_id),
                'parent_item' => $tree_array_last_item_id,
                'par_link' => $tree_array_last_link_id,
                'parent_ret_id' => GlobalController::set_relit_id($save_view_ret_id)
            ]);

        } else {
            //     session(['links' => ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/' . request()->path()]);
            // Редирект страницы
            // Если одна запись в списке - тогда идти дальше, пропустить
            // Нужно '$redirect_item_index = false;'
            $redirect_item_index = false;
            if ($view_link) {
                if ($base_body_right['is_skip_count_records_equal_1_item_body_index'] == true) {
                    if (count($body_items) == 1) {
                        $item_redirect = $body_items->first();
                        if ($item_redirect) {
                            $redirect_item_index = true;
                            // "'view_link' => $view_link" не использовать
                            return redirect()->route('item.item_index', ['project' => $project, 'item' => $item_redirect, 'role' => $role,
                                'usercode' => GlobalController::usercode_calc(),
                                'relit_id' => $view_ret_id,
                                'called_from_button' => 0,
                                'view_link' => GlobalController::par_link_const_textnull(),
                                'string_current' => $string_next,
                                'prev_base_index_page' => $base_index_page_current,
                                'prev_body_link_page' => $body_link_page_current,
                                'prev_body_all_page' => $body_all_page_current,
                                'view_ret_id' => $view_ret_id]);
                        }
                    }
                }
            }
            if ($redirect_item_index == false) {
                $message_bs_calc = ItemController::message_bs_calc($relip_project, $item->base);
                $message_bs_info = $message_bs_calc['message_bs_info'];
                $message_bs_validate = $message_bs_calc['message_bs_validate'];
                return view('item/item_index', ['project' => $project, 'item' => $item, 'role' => $role,
                    'relit_id' => $relit_id,
                    'view_link' => GlobalController::set_par_view_link_null($view_link),
                    'view_ret_id' => $view_ret_id,
                    'array_relips' => $array_relips,
                    'base_right' => $base_right,
                    'items' => $items,
                    'prev_item' => $prev_item,
                    'next_item' => $next_item,
                    'child_links' => $child_links,
                    'child_links_info' => $child_links_info,
                    'child_mains_link_is_calcname' => $child_mains_link_is_calcname,
                    'child_body_links_info' => $child_body_links_info,
                    'body_items' => $body_items,
                    'its_body_page' => $its_body_page,
                    'base_body_right' => $base_body_right,
                    'tree_array' => $tree_array,
                    'tree_array_last_link_id' => $tree_array_last_link_id,
                    'tree_array_last_item_id' => $tree_array_last_item_id,
                    'string_link_ids_current' => $string_link_ids_current,
                    'string_item_ids_current' => $string_item_ids_current,
                    'string_relit_ids_current' => $string_relit_ids_current,
                    'string_vwret_ids_current' => $string_vwret_ids_current,
                    'string_all_codes_current' => $string_all_codes_current,
                    'string_current' => $string_current,
                    'string_link_ids_next' => $string_link_ids_next,
                    'string_item_ids_next' => $string_item_ids_next,
                    'string_relit_ids_next' => $string_relit_ids_next,
                    'string_vwret_ids_next' => $string_vwret_ids_next,
                    'string_all_codes_next' => $string_all_codes_next,
                    'string_next' => $string_next,
                    'next_all_links' => $next_all_links,
                    'next_all_mains' => $next_all_mains,
                    'next_all_is_create' => $next_all_is_create,
                    'next_all_is_all_create' => $next_all_is_all_create,
                    'next_all_is_calcname' => $next_all_is_calcname,
                    'next_all_is_code_enable' => $next_all_is_code_enable,
                    'next_all_is_enable' => $next_all_is_enable,
                    'message_bs_info' => $message_bs_info,
                    'message_bs_validate' => $message_bs_validate,
                    'message_ln_info' => $message_ln_info,
                    'message_ln_validate' => $message_ln_validate,
                    'string_link_ids_array_next' => $string_link_ids_array_next,
                    'string_item_ids_array_next' => $string_item_ids_array_next,
                    'string_relit_ids_array_next' => $string_relit_ids_array_next,
                    'string_vwret_ids_array_next' => $string_vwret_ids_array_next,
                    'string_all_codes_array_next' => $string_all_codes_array_next,
                    'string_array_next' => $string_array_next,
                    'message_ln_array_info' => $message_ln_array_info,
                    'message_ln_link_array_item' => $message_ln_link_array_item,
                    'base_index_page' => $base_index_page_current,
                    'body_link_page' => $body_link_page_current,
                    'body_all_page' => $body_all_page_current
                ]);
            }
        }
    }

    function string_null()
    {
        return GlobalController::const_null()
            . ';' . GlobalController::const_null()
            . ';' . GlobalController::const_null()
            . ';' . GlobalController::const_null()
            . ';' . GlobalController::const_null();

    }

    function calc_tree_array(Role $role,
                                  $string_link_ids_current,
                                  $string_item_ids_current,
                                  $string_relit_ids_current,
                                  $string_vwret_ids_current,
                                  $string_all_codes_current)
    {
        $result = array();
        if (($string_link_ids_current != "")
            && ($string_item_ids_current != "")
            && ($string_relit_ids_current != "")
            && ($string_vwret_ids_current != "")
            && ($string_all_codes_current != "")
            && ($string_link_ids_current != GlobalController::const_null())
            && ($string_item_ids_current != GlobalController::const_null())
            && ($string_relit_ids_current != GlobalController::const_null())
            && ($string_vwret_ids_current != GlobalController::const_null())
            && ($string_all_codes_current != GlobalController::const_null())) {

            $array_link_ids = explode(",", $string_link_ids_current);
            $array_item_ids = explode(",", $string_item_ids_current);
            $array_relit_ids = explode(",", $string_relit_ids_current);
            $array_vwret_ids = explode(",", $string_vwret_ids_current);
            $array_all_codes = explode(",", $string_all_codes_current);

            // Количества переданных $links и $items должны совпадать
            if (count($array_link_ids) == count($array_item_ids)) {
                if (count($array_link_ids) > 0) {
                    // Сначала проверка, затем заполнение массива $result
                    $error = false;
                    foreach ($array_link_ids as $link_id) {
                        $link = link::find($link_id);
                        if (!$link) {
                            $error = true;
                            break;
                        }
                    }
                    if (!$error) {
                        foreach ($array_item_ids as $item_id) {
                            $item = Item::find($item_id);
                            if (!$item) {
                                $error = true;
                                break;
                            }
                        }
                        if (!$error) {
                            foreach ($array_relit_ids as $relit_id) {
                                // Кроме текущего шаблона
                                if ($relit_id != 0) {
                                    $relit = Relit::find($relit_id);
                                    if (!$relit) {
                                        $error = true;
                                        break;
                                    }
                                }
                            }
                            if (!$error) {
                                // Цикл по $array_vwret_ids
                                foreach ($array_vwret_ids as $relit_id) {
                                    // Кроме текущего шаблона
                                    if ($relit_id != 0) {
                                        $relit = Relit::find($relit_id);
                                        if (!$relit) {
                                            $error = true;
                                            break;
                                        }
                                    }
                                }
                                if (!$error) {
                                    $i = 0;
                                    $str = '';
                                    foreach ($array_link_ids as $link_id) {
                                        $result[$i]['string_prev_link_ids'] = $str;
                                        $str = $str . ($i == 0 ? '' : ',') . $link_id;
                                        $result[$i]['link_id'] = $link_id;
                                        $result[$i]['string_link_ids'] = $str;
                                        $link = link::find($link_id);
                                        if ($link) {
                                            $result[$i]['title_name'] = $link->parent_label();
                                        } else {
                                            $result[$i]['title_name'] = "";
                                        }
                                        $i = $i + 1;
                                    }
                                    // Заполнение массива по $relit_id должно быть перед заполнением массива по $item_id
                                    $i = 0;
                                    $str = '';
                                    foreach ($array_relit_ids as $relit_id) {
                                        $result[$i]['string_prev_relit_ids'] = $str;
                                        $str = $str . ($i == 0 ? '' : ',') . $relit_id;
                                        $result[$i]['relit_id'] = $relit_id;
                                        $result[$i]['string_relit_ids'] = $str;
                                        $i = $i + 1;
                                    }
                                    $i = 0;
                                    $str = '';
                                    // Цикл по $array_vwret_ids
                                    foreach ($array_vwret_ids as $relit_id) {
                                        $result[$i]['string_prev_vwret_ids'] = $str;
                                        $str = $str . ($i == 0 ? '' : ',') . $relit_id;
                                        $result[$i]['vwret_id'] = $relit_id;
                                        $result[$i]['string_vwret_ids'] = $str;
                                        $i = $i + 1;
                                    }
                                    $i = 0;
                                    $str = '';
                                    foreach ($array_item_ids as $item_id) {
                                        $result[$i]['string_prev_item_ids'] = $str;
                                        $str = $str . ($i == 0 ? '' : ',') . $item_id;
                                        $result[$i]['item_id'] = $item_id;
                                        $result[$i]['string_item_ids'] = $str;
                                        // Проверка на правильность поиска $item_id выше
                                        $item = Item::findOrFail($item_id);
                                        $base_right = GlobalController::base_right($item->base, $role, $result[$i]['relit_id']);
                                        // Эти массивы используются в item_index.php при выводе $tree_array
                                        $result[$i]['base_id'] = $item->base_id;
                                        $result[$i]['base_names'] = $item->base->names($base_right);
                                        //$result[$i]['title_name'] = $item->base->name();
                                        $result[$i]['item_name'] = $item->name();
                                        // Для вызова 'item.base_index' нужно
                                        $result[$i]['is_bsmn_base_enable'] = $base_right['is_bsmn_base_enable'];
                                        $i = $i + 1;
                                    }
                                    $i = 0;
                                    $str = '';
                                    $info = '';
                                    foreach ($array_all_codes as $all_code) {
                                        $result[$i]['string_prev_all_codes'] = $str;
                                        $str = $str . ($i == 0 ? '' : ',') . $all_code;
                                        $result[$i]['all_code'] = $all_code;
                                        $result[$i]['string_all_codes'] = $str;
                                        $info = '';
                                        // Похожие команды в ItemController::calc_tree_array() и item_index.php                                //
                                        if ($all_code == GlobalController::const_alltrue()) {
                                            $info = trans('main.all_links');
                                        } else {
                                            // Проверка на правильность поиска $link_id выше
                                            $link = Link::findOrFail($result[$i]['link_id']);
                                            $info = $link->child_labels();
                                        }
                                        //$result[$i]['info_name'] = $result[$i]['item_name'] . ' (' . mb_strtolower($info) . ')';
                                        $result[$i]['info_name'] = '(' . mb_strtolower($info) . ')';
                                        $i = $i + 1;
                                    }
                                    for ($i = 0; $i < count($result); $i++) {
                                        $result[$i]['string_current'] = self::string_zip_current_next($result[$i]['string_link_ids'],
                                            $result[$i]['string_item_ids'],
                                            $result[$i]['string_relit_ids'],
                                            $result[$i]['string_vwret_ids'],
                                            $result[$i]['string_all_codes']);
                                        $result[$i]['string_previous'] = self::string_zip_current_next($result[$i]['string_prev_link_ids'],
                                            $result[$i]['string_prev_item_ids'],
                                            $result[$i]['string_prev_relit_ids'],
                                            $result[$i]['string_prev_vwret_ids'],
                                            $result[$i]['string_prev_all_codes']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    // Поиск в массиве tree_array $link->parent_base_id = $item->base_id
    function get_tree_item($role, $link, $string_current)
    {
        $result = null;
        $string_unzip_current_next = self::string_unzip_current_next($string_current);
        $string_link_ids_current = $string_unzip_current_next['string_link_ids'];
        $string_item_ids_current = $string_unzip_current_next['string_item_ids'];
        $string_relit_ids_current = $string_unzip_current_next['string_relit_ids'];
        $string_vwret_ids_current = $string_unzip_current_next['string_vwret_ids'];
        $string_all_codes_current = $string_unzip_current_next['string_all_codes'];

        $tree_array = self::calc_tree_array($role,
            $string_link_ids_current,
            $string_item_ids_current,
            $string_relit_ids_current,
            $string_vwret_ids_current,
            $string_all_codes_current);

        foreach ($tree_array as $value) {
            $item = Item::find($value['item_id']);
            if ($item) {
                if ($link->parent_base_id == $item->base_id) {
                    $result = $item;
                    break;
                }
            }
        }
        return $result;
    }

    function string_zip_current_next($string_link_ids, $string_item_ids, $string_relit_ids, $string_vwret_ids, $string_all_codes)
    {
        $result = self::string_null();
        if (($string_link_ids != "")
            && ($string_item_ids != "")
            && ($string_relit_ids != "")
            && ($string_vwret_ids != "")
            && ($string_all_codes != "")) {
            $result = $string_link_ids . ';' . $string_item_ids . ';' . $string_relit_ids . ';' . $string_vwret_ids . ';' . $string_all_codes;
        }
        return $result;
    }

    function string_unzip_current_next($string_current_next)
    {
        $string_link_ids = '';
        $string_item_ids = '';
        $string_relit_ids = '';
        $string_vwret_ids = '';
        $string_all_codes = '';
        $array_items = explode(";", $string_current_next);
        // const 5 используется
        if (count($array_items) == 5) {
            $string_link_ids = $array_items[0];
            $string_item_ids = $array_items[1];
            $string_relit_ids = $array_items[2];
            $string_vwret_ids = $array_items[3];
            $string_all_codes = $array_items[4];
        }
        return ['string_link_ids' => $string_link_ids,
            'string_item_ids' => $string_item_ids,
            'string_relit_ids' => $string_relit_ids,
            'string_vwret_ids' => $string_vwret_ids,
            'string_all_codes' => $string_all_codes];
    }

    function calc_string_current_next_ids($tree_array, Item $item, Link $link, $relit_id, $view_ret_id, $all_code)
    {
//        $string_current_link_ids = '';
//        $string_current_item_ids = '';
//        $string_current_all_codes = '';
        // Нужно использовать 'GlobalController::const_null()'
        $string_current_link_ids = GlobalController::const_null();
        $string_current_item_ids = GlobalController::const_null();
        $string_current_relit_ids = GlobalController::const_null();
        $string_current_vwret_ids = GlobalController::const_null();
        $string_current_all_codes = GlobalController::const_null();
        //$string_current = self::string_zip_current_next($string_current_link_ids, $string_current_item_ids, $string_current_relit_ids, $string_current_all_codes);

        $string_next_link_ids = $link->id;
        $string_next_item_ids = $item->id;
        $string_next_relit_ids = $relit_id;
        $string_next_vwret_ids = $view_ret_id;
        $string_next_all_codes = $all_code;
        //$string_next = zip_string_next_next($string_next_link_ids, $string_next_item_ids, $string_next_relit_ids, $string_next_all_codes);

        $count = count($tree_array);
        if ($count > 0) {
            // '- 1' т.к. нумерация элементов массива начинается с 0
            $string_current_link_ids = $tree_array[$count - 1]['string_link_ids'];
            $string_current_item_ids = $tree_array[$count - 1]['string_item_ids'];
            $string_current_relit_ids = $tree_array[$count - 1]['string_relit_ids'];
            $string_current_vwret_ids = $tree_array[$count - 1]['string_vwret_ids'];
            $string_current_all_codes = $tree_array[$count - 1]['string_all_codes'];
            //$string_current = self::string_zip_current_next($string_current_link_ids, $string_current_item_ids, $string_current_relit_ids, $string_current_all_codes);

            $string_next_link_ids = $tree_array[$count - 1]['string_link_ids'] . ',' . $string_next_link_ids;
            $string_next_item_ids = $tree_array[$count - 1]['string_item_ids'] . ',' . $string_next_item_ids;
            $string_next_relit_ids = $tree_array[$count - 1]['string_relit_ids'] . ',' . $string_next_relit_ids;
            $string_next_vwret_ids = $tree_array[$count - 1]['string_vwret_ids'] . ',' . $string_next_vwret_ids;
            $string_next_all_codes = $tree_array[$count - 1]['string_all_codes'] . ',' . $string_next_all_codes;
            //$string_next = zip_string_next_next($string_next_link_ids, $string_next_item_ids, $string_next_relit_ids, $string_next_all_codes);

        }
        return ['string_current_link_ids' => $string_current_link_ids,
            'string_current_item_ids' => $string_current_item_ids,
            'string_current_relit_ids' => $string_current_relit_ids,
            'string_current_vwret_ids' => $string_current_vwret_ids,
            'string_current_all_codes' => $string_current_all_codes,
            'string_next_link_ids' => $string_next_link_ids,
            'string_next_item_ids' => $string_next_item_ids,
            'string_next_relit_ids' => $string_next_relit_ids,
            'string_next_vwret_ids' => $string_next_vwret_ids,
            'string_next_all_codes' => $string_next_all_codes
        ];
//        return ['string_current' => $string_current,
//            'string_next' => $string_next
//        ];
    }

    function item_link_parent_mains_exists(Item $item, Link $link)
    {
        // Проверка "Существуют ли записи по связи"
        return $item->parent_mains()->where('mains.link_id', $link->id)->exists();;
    }

    function next_all_links_mains_calc(Project $project, Item $item, Role $role, $relit_id, $view_link, $view_ret_id, $tree_array, $base_right, $called_from_button)
    {
        // Блок расчета данных по $item($item->base, $item->template)
        // Список доступных связей
        $base = $item->base;
        $links = $base->parent_links
            ->where('parent_is_parent_related', false)
            ->where('parent_is_base_link', false)
            ->where('parent_is_output_calculated_table_field', false)
            ->sortBy('child_base_number');
        $next_all_links = array();
        $next_all_links_ids = array();
        $next_all_links_user_ids = array();
        $next_all_links_byuser_ids = array();
        $next_all_is_calcname = array();
        $next_all_is_create = array();
        $array_relips = array();
        $next_all_rts_links = array();
        $next_all_rts_links_ids = array();
        $next_all_rts_links_byuser_ids = array();
        $next_all_rts_links_user_ids = array();
        $next_all_rts_is_calcname = array();
        $next_all_rts_is_create = array();

        foreach ($links as $link) {
            //$array_link_relips = [];
            // Выводить вычисляемое наименование
            // Использовать 'is_base_calcnm_correct_check()' (а не is_base_calcname_check())
            // Использовать '$link->child_base'
            $is_calcname = GlobalController::is_base_calcnm_correct_check($link->child_base);
//            // Текущий проект
//            $array_link_relips[0] = $project->id;
//            // relips текущего проекта $parent->id
//            // '->get()' нужно
//            // Проекты $relip->parent_project_id находятся из существующих взаимосвязанных проектов $project
//            $par_prs_ids = Relip::select(DB::Raw('relips.relit_id as relit_id, relips.parent_project_id as project_id'))
//                ->join('relits', 'relips.relit_id', '=', 'relits.id')
//                ->where('child_project_id', '=', $project->id)
//                ->orderBy('relits.serial_number')
//                ->get();
//            // Заполнение массива $array_link_relips, $key = $relit_id, $value = project_id
//            foreach ($par_prs_ids as $value) {
//                $array_link_relips[$value->relit_id] = $value->project_id;
//            }
            $array_link_relips = self::calc_array_link_relips($project);
            // Если '$link->parent_relit_id = 0' значит link..parent_project = $item->project_id
            // и link..child_project должен быть равен $item->project_id
            // Оставляем в массиве $array_link_relips только строки с проектом $item->project_id
            if ($link->parent_relit_id == 0) {
                foreach ($array_link_relips as $key => $value) {
                    if ($item->project_id != $value) {
                        unset($array_link_relips[$key]);
                    }
                }
            } else {
                // relips проекта $item->parent_id
//                $item_prs_ids_data = Relip::select(DB::Raw('relips.relit_id as relit_id, relips.parent_project_id as project_id'))
//                    ->where('child_project_id', '=', $project->id)
//                    ->where('relit_id', '=', $link->parent_relit_id)
//                    ->where('parent_project_id', '=', $item->project_id)
//                    ->first();
//                $item_prs_ids_found = $item_prs_ids_data;
//                if (!$item_prs_ids_found) {
//                    foreach ($array_link_relips as $key => $value) {
//                        //if ($item_prs_ids_data->project_id != $value) {
//                        if ($item->project_id != $value) {
//                            unset($array_link_relips[$key]);
//                        }
//                    }
//                }
                foreach ($array_link_relips as $key => $value) {
                    //if ($link->child_base->template_id == $project->template_id && $link->parent_relit_id != 0 && $link->parent_relit_id != $key) {
                    //unset($array_link_relips[$key]);
                    //} else {
                    $item_prs_ids_data = Relip::select(DB::Raw('relips.child_project_id as project_id'))
                        ->where('child_project_id', '=', $value)
                        ->where('relit_id', '=', $link->parent_relit_id)
                        ->where('parent_project_id', '=', $item->project_id)
                        ->exists();
//                    if (!$item_prs_ids_data) {
//                        if ($item->project_id != $value) {
//                            unset($array_link_relips[$key]);
//                        }
//                    }
                    if ($item_prs_ids_data) {
//                        if ($link->parent_relit_id != 0) {
//                            if ($link->parent_relit_id != $key) {
//                                unset($array_link_relips[$key]);
//                            }
//                        }
                    } else {
                        if ($item->project_id != $value) {
                            unset($array_link_relips[$key]);
                        }
                    }
                    //}
                }
            }
            // Цикл по relips текущего проекта, вкл. relit_id = 0
            foreach ($array_link_relips as $key => $value) {
                $find_proj = Project::find($value);
                if ($find_proj) {
                    // Проверка на равенство шаблонов
                    if ($link->child_base->template_id != $find_proj->template_id) {
                        unset($array_link_relips[$key]);
                    } else {
//                            // Проверка на $link->parent_relit_id и равенство проектов
//                            if (($link->parent_relit_id == 0) && ($item->project_id != $value)) {
//                                //if ($link_parent_relit_project_id != $find_proj->id) {
//                                unset($array_link_relips[$key]);
//                                // 'continue' нужно, иначе, например, в $next_all_rts_links попадают удаленные элементы массива $array_link_relips
//                                //continue;
//                            } else {
//                        $base_link_right = GlobalController::base_link_right($link, $role, $relit_id, true, $key);
                        // "GlobalController::base_link_right($link, $role,$key,true)" - true обязательно нужно
                        $base_link_right = GlobalController::base_link_right($link, $role, $key, true);

                        // Проверка 'Доступность ввода данных на основе проверки истории (links)'
                        // Используется "GlobalController::is_checking_add_history()"
                        $is_checking_add_history = GlobalController::is_checking_add_history($role, $relit_id, $view_link, $item);

                        // Проверка 'Доступность ввода данных на основе проверки заполненности данных (links)'
                        // Используется "GlobalController::is_checking_add_empty()"
                        $is_checking_add_empty = GlobalController::is_checking_add_empty($role, $relit_id, $view_link, $item);

                        // Использовать две этих проверки
                        if (!(($base_link_right['is_body_link_enable'] == true) && ($base_link_right['is_list_base_calc'] == true))) {
                            unset($array_link_relips[$key]);
                        } else {
                            // 'is_edit_link_update' - 'Корректировка Связи в форме'
                            $next_create = $base_link_right['is_list_base_create'] == true
                                && $base_link_right['is_edit_link_update'] == true
                                && $is_checking_add_history['result_entry_history'] == true
                                && $is_checking_add_empty['result_entry_empty'] == true;
                            if (!(self::item_link_parent_mains_exists($item, $link) || $next_create)) {
                                unset($array_link_relips[$key]);
                            } else {
                                // Такая же проверка и в GlobalController (function items_right(), items_check_right()),
                                // в ItemController (function next_all_links_mains_calc(), browser(), get_items_for_link(), get_items_ext_edit_for_link())
                                if (($base_link_right['is_list_base_user_id'] == true) | ($base_link_right['is_list_base_byuser'] == true)) {
                                    if (Auth::check()) {
                                        // Два блока одинаковых команд
                                        // Нужно '$next_all_rts_links[$key][] = $link;'
                                        // Второй элемент массива - порядковый номер, начинается с 0
                                        $next_all_rts_links[$key][] = $link;
                                        // Эта команда "$next_all_rts_links_ids[$key][] = $link->id;" не нужна
                                        // $next_all_rts_links_ids[$key][] = $link->id;
                                        if ($base_link_right['is_list_base_user_id'] == true) {
                                            $next_all_rts_links_user_ids[$key][] = $link->id;
                                        }
                                        if ($base_link_right['is_list_base_byuser'] == true) {
                                            $next_all_rts_links_byuser_ids[$key][] = $link->id;
                                        }
                                        $next_all_rts_is_calcname[$key][$link->id] = $is_calcname;
//                      Такая же проверка на 'is_list_base_create' == true && 'is_edit_link_update' == true в item_index.php и ItemController.php
                                        //$next_all_is_create[$link->id] = $base_right['is_list_base_create'];
                                        $next_all_rts_is_create[$key][$link->id] = $next_create;
                                    } else {
                                        // Данные не добавляются
                                        unset($array_link_relips[$key]);
                                    }
                                } else {
                                    // Два блока одинаковых команд
                                    // Нужно '$next_all_rts_links[$key][] = $link;'
                                    // Второй элемент массива - порядковый номер, начинается с 0
                                    $next_all_rts_links[$key][] = $link;
                                    $next_all_rts_links_ids[$key][] = $link->id;
                                    $next_all_rts_is_calcname[$key][$link->id] = $is_calcname;
                                    $next_all_rts_is_create[$key][$link->id] = $next_create;
                                }
                            }
                            //}
                        }
                    }
                }
            }
            if (count($array_link_relips) > 0) {
                foreach ($array_link_relips as $key => $value) {
                    $array_relips[$key] = $value;
                }
            }
        }

        // Блок проверки и вычисления $current_vw_ret_id
        // Нужно
        $current_vw_ret_id = null;
        if (count($array_relips) > 0) {
            // Использовать '!is_null($view_ret_id)' чтобы правильно срабатывал алгоритм при $view_ret_id = 0
            if (!is_null($view_ret_id)) {
                // Проверка, есть ли $view_link в $next_all_links
                foreach ($array_relips as $key => $array_relip) {
                    if ($key == $view_ret_id) {
                        $current_vw_ret_id = $view_ret_id;
                        break;
                    }
                }
            }
// Использовать 'is_null($current_vw_ret_id)' чтобы правильно срабатывал алгоритм при $current_vw_ret_id = 0
            if (is_null($current_vw_ret_id)) {
                // Если не найдены, то берем первый ключ из массива $array_relips
                $current_vw_ret_id = array_key_first($array_relips);
            }

//      'if ($called_from_button == 0)' используется
            if ($called_from_button == 0) {
                foreach ($array_relips as $key => $value) {
                    if ($relit_id == $key) {
                        $current_vw_ret_id = $key;
                        break;
                    }
                }
            }
        }

//        if (!is_null($current_vw_ret_id)) {
//            if (count($next_all_rts_links) > 0) {
//                if (in_array($next_all_rts_links[$current_vw_ret_id], $next_all_rts_links)) {
//                    $next_all_links = $next_all_rts_links[$current_vw_ret_id];
//                }
//            }
//            if (count($next_all_rts_links_ids) > 0) {
//                if (in_array($next_all_rts_links_ids[$current_vw_ret_id], $next_all_rts_links_ids)) {
//                    $next_all_links_ids = $next_all_rts_links_ids[$current_vw_ret_id];
//                }
//            }
//            if (count($next_all_rts_links_byuser_ids) > 0) {
//                if (in_array($next_all_rts_links_byuser_ids[$current_vw_ret_id], $next_all_rts_links_byuser_ids)) {
//                    $next_all_links_byuser_ids = $next_all_rts_links_byuser_ids[$current_vw_ret_id];
//                }
//            }
//            if (count($next_all_rts_links_user_ids) > 0) {
//                if (in_array($next_all_rts_links_user_ids[$current_vw_ret_id], $next_all_rts_links_user_ids)) {
//                    $next_all_links_user_ids = $next_all_rts_links_user_ids[$current_vw_ret_id];
//                }
//            }
//            if (count($next_all_rts_is_calcname) > 0) {
//                if (in_array($next_all_rts_is_calcname[$current_vw_ret_id], $next_all_rts_is_calcname)) {
//                    $next_all_is_calcname = $next_all_rts_is_calcname[$current_vw_ret_id];
//                }
//            }
//            if (count($next_all_rts_is_create) > 0) {
//                if (in_array($next_all_rts_is_create[$current_vw_ret_id], $next_all_rts_is_create)) {
//                    $next_all_is_create = $next_all_rts_is_create[$current_vw_ret_id];
//                }
//            }
//        }

        if (!is_null($current_vw_ret_id)) {
            if (count($next_all_rts_links) > 0) {
                if (isset($next_all_rts_links[$current_vw_ret_id])) {
                    $next_all_links = $next_all_rts_links[$current_vw_ret_id];
                }
            }
            if (count($next_all_rts_links_ids) > 0) {
                if (isset($next_all_rts_links_ids[$current_vw_ret_id])) {
                    $next_all_links_ids = $next_all_rts_links_ids[$current_vw_ret_id];
                }
            }
            if (count($next_all_rts_links_byuser_ids) > 0) {
                if (isset($next_all_rts_links_byuser_ids[$current_vw_ret_id])) {
                    $next_all_links_byuser_ids = $next_all_rts_links_byuser_ids[$current_vw_ret_id];
                }
            }
            if (count($next_all_rts_links_user_ids) > 0) {
                if (isset($next_all_rts_links_user_ids[$current_vw_ret_id])) {
                    $next_all_links_user_ids = $next_all_rts_links_user_ids[$current_vw_ret_id];
                }
            }
            if (count($next_all_rts_is_calcname) > 0) {
                if (isset($next_all_rts_is_calcname[$current_vw_ret_id])) {
                    $next_all_is_calcname = $next_all_rts_is_calcname[$current_vw_ret_id];
                }
            }
            if (count($next_all_rts_is_create) > 0) {
                if (isset($next_all_rts_is_create[$current_vw_ret_id])) {
                    $next_all_is_create = $next_all_rts_is_create[$current_vw_ret_id];
                }
            }
        }

// Первая связь (по сортировке)
        $next_all_first_link = null;
        if (count($next_all_links) > 0) {
            $next_all_first_link = $next_all_links[0];
        }

// Первая связь с данными (по сортировке)
        $next_all_full_link = null;
        foreach ($next_all_links as $link) {
            if (self::item_link_parent_mains_exists($item, $link) == true) {
                $next_all_full_link = $link;
                break;
            }
        }
// $next_all_is_enable равен истина, если во всех links выводить вычисляемое наименование
// Есть все записи $next_all_is_calcname = true, то $next_all_is_enable = true
// (в кнопке 'Связь' вариант 'все' доступен)
// Нужно '$next_all_is_enable = true;'
        $next_all_is_enable = true;
// $next_all_is_calcname - массив 'Выводить вычисляемое наименование'
        foreach ($next_all_is_calcname as $value) {
            if ($value == false) {
                $next_all_is_enable = false;
                break;
            }
        }
// Проверки link_maxcount, item_maxcount
        $message_ln_array_info = array();
        $message_ln_link_array_item = array();
        foreach ($next_all_links as $link) {
            $message_ln_calc = self::message_ln_calc($project, $item, $link);
            $message_ln_array_info[$link->id] = $message_ln_calc['message_ln_info'];
            $message_ln_link_array_item[$link->id] = $message_ln_calc['message_ln_validate'];
        }

// Есть хотя бы одна запись $next_all_is_create = true, то $next_all_is_all_create = true
// (т.е. вся кнопка 'Добавить' доступна (для всех связей))
// Нужно '$next_all_is_all_create = false;'
        $next_all_is_all_create = false;
        foreach ($next_all_is_create as $key => $value) {
            if ($value == true && $message_ln_link_array_item[$key] == "") {
                $next_all_is_all_create = true;
                break;
            }
        }
// Нужно
        $current_link = null;
// Блок проверки и вычисления $current_link
        if (count($next_all_links) > 0) {
            if ($view_link) {
                // Проверка, есть ли $view_link в $next_all_links
                foreach ($next_all_links as $next_all_link) {
                    if ($next_all_link->id == $view_link->id) {
                        $current_link = $view_link;
                        break;
                    }
                }
            }
            if (is_null($current_link)) {
                if ($view_link) {
                    if ($view_link == GlobalController::par_link_const_text_base_null()) {
                        if ($base_right['is_heading']) {
                            // Если не найдены, то берем первый $link по списку
                            $current_link = $next_all_first_link;
                        }
                    }
                }
            }
            if (is_null($current_link)) {
                // Если во всех $links не выводятся вычисляемые наименования или количество связей = 1,
                // То берем первый $link по списку.
                // Похожая проверка по смыслу 'count($next_all_links) == 1' в ItemController::item_index() и item_index.php
                if ($next_all_is_enable == false || count($next_all_links) == 1) {
                    $current_link = $next_all_first_link;
                }
            }
        }

//      'if ($called_from_button == 0)' используется
        if ($called_from_button == 0) {
            if ($next_all_full_link) {
                if ($current_link) {
                    // Если нет данных по связи $current_link
                    if (self::item_link_parent_mains_exists($item, $current_link) == false) {
                        $current_link = $next_all_full_link;
                    }
                    // Если выводятся 'Все связи'
                } else {
                    $current_link = $next_all_full_link;
                }
            }
        }

// Есть ли хотя бы в одной связи код,
// Нужно для вывода столбца "Код" (list\all.php)
        $next_all_is_code_enable = false;
        foreach ($next_all_links as $link) {
            if ($link->child_base->is_code_needed == true) {
                $next_all_is_code_enable = true;
                break;
            }
        }

// Ссылки link_next, item_next
        $string_array_next = array();
        $string_link_ids_array_next = array();
        $string_item_ids_array_next = array();
        $string_relit_ids_array_next = array();
        $string_vwret_ids_array_next = array();
        $string_all_codes_array_next = array();
        foreach ($next_all_links as $link) {
            // $item, GlobalController::par_link_const_textnull() присоединяются к списку $tree_array
            // В all.php par_link = GlobalController::par_link_const_textnull() при формировании ссылки на item_index()
            $string_current_next_ids = self::calc_string_current_next_ids($tree_array, $item, $link, $relit_id, $current_vw_ret_id, GlobalController::const_alltrue());
            $string_link_ids_array_next[$link->id] = $string_current_next_ids['string_next_link_ids'];
            $string_item_ids_array_next[$link->id] = $string_current_next_ids['string_next_item_ids'];
            $string_relit_ids_array_next[$link->id] = $string_current_next_ids['string_next_relit_ids'];
            $string_vwret_ids_array_next[$link->id] = $string_current_next_ids['string_next_vwret_ids'];
            $string_all_codes_array_next[$link->id] = $string_current_next_ids['string_next_all_codes'];
            $string_array_next[$link->id] = self::string_zip_current_next($string_link_ids_array_next[$link->id],
                $string_item_ids_array_next[$link->id],
                $string_relit_ids_array_next[$link->id],
                $string_vwret_ids_array_next[$link->id],
                $string_all_codes_array_next[$link->id]);
        }


// После блока расчета данных результаты в переменных
// $array_relips = Relips по текущему $item
// Берутся значения по $current_vw_ret_id
// $next_all_links = все связи;
// $next_all_links_ids = связи без фильтра по пользователю($link->id);
// $next_all_links_user_ids = связи c фильтром по пользователю user_id($link->id);
//// $next_all_links_byuser_ids = связи c фильтром по пользователю($link->id);
// $next_all_is_calcname = $links с признаком "Выводить наименование";
// $next_all_is_create = $links с признаком "Есть кнопка 'Добавить' в теле документа";
        $item_name_lang = GlobalController::calc_item_name_lang();
// Нужно
        $next_all_mains = null;
// Запрос $next_all_mains выбирает все записи по $array_relips[$current_vw_ret_id]
// 'is_null($current_link)'
// Запускать запрос - расчет
        // Расчет "все" записи, когда is_null($current_link)
        // Похожие строки в ItemController::next_all_links_mains_calc(), GlobalController::get_items_user_id(), GlobalController::base_user_id_maxcount_validate()
        if (is_null($current_link) && !is_null($current_vw_ret_id) && count($next_all_links) > 0) {
            $usersetup_project_id = env('USERSETUP_PROJECT_ID');
            $usersetup_base_id = env('USERSETUP_BASE_ID');
            $usersetup_name_link_id = env('USERSETUP_NAME_LINK_ID');
            $username = GlobalController::glo_user()->name();
            if ($usersetup_project_id != '' && $usersetup_base_id != '' && $usersetup_name_link_id != '') {
                // Все записи, со всеми links, по факту
                // Условия одинаковые 'where('parent_is_base_link', false)'
                // Такая же проверка и в GlobalController (function items_right(), items_check_right()),
                // в ItemController (function next_all_links_mains_calc(), browser(), get_items_for_link(), get_items_ext_edit_for_link())
                //              Похожие запросы в ItemController::next_all_links_mains_calc() и GlobalController::items_right()
                // Фильтры GlobalController::items_right():
                // "if ($base_right['is_tst_enable'] == true)"
                // "if ($base_right['is_cus_enable'] == true)"
                // здесь не настроены.
                $next_all_mains = Main::select('mains.*')
                    ->join('links', 'mains.link_id', '=', 'links.id')
                    ->join('items', 'mains.child_item_id', '=', 'items.id')
                    ->where(function ($query) use (
                        $next_all_links_ids, $next_all_links_user_ids, $next_all_links_byuser_ids,
                        $usersetup_project_id, $usersetup_base_id, $usersetup_name_link_id, $username
                    ) {
                        $query->whereIn('links.id', $next_all_links_ids)
                            ->orWhere(function ($query) use ($next_all_links_user_ids, $usersetup_project_id, $usersetup_base_id, $usersetup_name_link_id, $username) {
                                $query->whereIn('links.id', $next_all_links_user_ids)
                                    ->whereHas('child_item', function ($query) use ($usersetup_project_id, $usersetup_base_id, $usersetup_name_link_id, $username) {
                                        $query->where('project_id', $usersetup_project_id)
                                            ->where('base_id', $usersetup_base_id)
                                            ->whereHas('child_mains', function ($query) use ($usersetup_name_link_id, $username) {
                                                $query->where('link_id', $usersetup_name_link_id)
                                                    ->whereHas('parent_item', function ($query) use ($username) {
                                                        $query->where('name_lang_0', '=', $username);
                                                    });
                                            });
                                    });
                            })
                            ->orWhere(function ($query) use ($next_all_links_byuser_ids) {
                                $query->whereIn('links.id', $next_all_links_byuser_ids)
                                    ->where('items.created_user_id', GlobalController::glo_user_id());
                            });
                    })
                    ->where('items.project_id', '=', $array_relips[$current_vw_ret_id])
                    ->where('links.parent_is_base_link', '=', false)
                    ->where('parent_item_id', $item->id)
                    ->orderBy('links.child_base_number')
                    ->orderBy('links.child_base_id')
                    ->orderBy('items.' . $item_name_lang);

                // Запрос по одному $current_link и $array_relips[$current_vw_ret_id], иначе все записи по $array_relips[$current_vw_ret_id]
//            if (!is_null($current_link)) {
//                $next_all_mains = $next_all_mains
//                    ->where('links.id', '=', $current_link->id);
//            }

//                if ($current_vw_ret_id == 0) {
//                    $next_all_mains = $next_all_mains
//                        ->where('items.project_id', '=', $project->id);
//                } else {
//                    $next_all_mains = $next_all_mains
//                        ->join('relips', 'items.project_id', '=', 'relips.parent_project_id')
//                        ->where('relips.relit_id', '=', $current_vw_ret_id)
//                        ->where('relips.child_project_id', '=', $project->id);
//                }
            }
        }

        return ['next_all_links' => $next_all_links,
            'array_relips' => $array_relips,
            'current_link' => $current_link,
            'current_vw_ret_id' => $current_vw_ret_id,
            'next_all_mains' => $next_all_mains,
            'next_all_is_create' => $next_all_is_create,
            'next_all_is_all_create' => $next_all_is_all_create,
            'next_all_is_calcname' => $next_all_is_calcname,
            'next_all_full_link' => $next_all_full_link,
            'next_all_is_code_enable' => $next_all_is_code_enable,
            'next_all_is_enable' => $next_all_is_enable,
            'string_link_ids_array_next' => $string_link_ids_array_next,
            'string_item_ids_array_next' => $string_item_ids_array_next,
            'string_relit_ids_array_next' => $string_relit_ids_array_next,
            'string_vwret_ids_array_next' => $string_vwret_ids_array_next,
            'string_all_codes_array_next' => $string_all_codes_array_next,
            'string_array_next' => $string_array_next,
            'message_ln_array_info' => $message_ln_array_info, 'message_ln_link_array_item' => $message_ln_link_array_item];
    }

    // Заполнение массива $relit_id и соответствующими проектами
    function calc_array_link_relips(Project $project)
    {
        $array_link_relips = [];
        // Текущий проект
        $array_link_relips[0] = $project->id;
        // relips текущего проекта $parent->id
        // '->get()' нужно
        // Проекты $relip->parent_project_id находятся из существующих взаимосвязанных проектов $project
        $par_prs_ids = Relip::select(DB::Raw('relips.relit_id as relit_id, relips.parent_project_id as project_id'))
            ->join('relits', 'relips.relit_id', '=', 'relits.id')
            ->where('child_project_id', '=', $project->id)
            ->orderBy('relits.serial_number')
            ->get();
        // Заполнение массива $array_link_relips, $key = $relit_id, $value = project_id
        foreach ($par_prs_ids as $value) {
            $array_link_relips[$value->relit_id] = $value->project_id;
        }
        return $array_link_relips;
    }

    function message_bs_calc(Project $project, Base $base)
    {
        $message_bs_mc = GlobalController::base_maxcount_message($base);
        $message_bs_user_id_mc = GlobalController::base_user_id_maxcount_message($base);
        $message_bs_byuser_mc = GlobalController::base_byuser_maxcount_message($base);
        $message_bs_info = ($message_bs_mc == "" ? "" : ', ' . PHP_EOL . $message_bs_mc)
            . ($message_bs_user_id_mc == "" ? "" : ', ' . PHP_EOL . $message_bs_user_id_mc)
            . ($message_bs_byuser_mc == "" ? "" : ', ' . PHP_EOL . $message_bs_byuser_mc);
        $message_base = GlobalController::base_maxcount_validate($project, $base, true);
        $message_user_id_base = GlobalController::base_user_id_maxcount_validate($project, $base, true);
        $message_byuser_base = GlobalController::base_byuser_maxcount_validate($project, $base, true);
        $message_bs_validate = $message_base . $message_user_id_base . $message_byuser_base;
        return ['message_bs_info' => $message_bs_info, 'message_bs_validate' => $message_bs_validate];
    }

    function message_ln_calc(Project $project, Item $item, Link $current_link)
    {
        $message_bs_mc = GlobalController::base_maxcount_message($current_link->child_base);
        $message_bs_user_id_mc = GlobalController::base_user_id_maxcount_message($current_link->child_base);
        $message_bs_byuser_mc = GlobalController::base_byuser_maxcount_message($current_link->child_base);
        $message_ln_mc = GlobalController::link_maxcount_message($current_link);
        $message_it_mc = GlobalController::link_item_maxcount_message($current_link, $item);
        $message_ln_info = ($message_bs_mc == "" ? "" : ', ' . PHP_EOL . $message_bs_mc)
            . ($message_bs_user_id_mc == "" ? "" : ', ' . PHP_EOL . $message_bs_user_id_mc)
            . ($message_bs_byuser_mc == "" ? "" : ', ' . PHP_EOL . $message_bs_byuser_mc)
            . ($message_ln_mc == "" ? "" : ', ' . PHP_EOL . $message_ln_mc)
            . ($message_it_mc == "" ? "" : ', ' . PHP_EOL . $message_it_mc);
        $message_base = GlobalController::base_maxcount_validate($project, $current_link->child_base, true);
        $message_byuser_base = GlobalController::base_byuser_maxcount_validate($project, $current_link->child_base, true);
        $message_user_id_base = GlobalController::base_user_id_maxcount_validate($project, $current_link->child_base, true);
        $message_link = GlobalController::link_maxcount_validate($project, $current_link, true);
        // added = 'true' используется
        $message_item = GlobalController::link_item_maxcount_validate($project, $item, $current_link, true);
        $message_ln_validate = $message_base . $message_user_id_base . $message_byuser_base . $message_link . $message_item;
        return ['message_ln_info' => $message_ln_info, 'message_ln_validate' => $message_ln_validate];
    }

    function store_link_change(Request $request)
    {
        $project = Project::find($request['project_id']);
        $item = Item::find($request['item_id']);
        $role = Role::find($request['role_id']);
        $relit_id = $request['relit_id'];
        $link = Link::find($request['link_id']);
        $string_link_ids_current = $request['string_link_ids_current'];
        $string_item_ids_current = $request['string_item_ids_current'];
        $string_all_codes_current = $request['string_all_codes_current'];
        return redirect()->route('item.item_index', ['project' => $project, 'item' => $item, 'role' => $role, 'relit_id' => $relit_id,
            'usercode' => GlobalController::usercode_calc(), 'relit_id' => $relit_id, 'called_from_button' => 0, 'par_link' => $link,
            'string_link_ids_current' => $string_link_ids_current, 'string_item_ids_current' => $string_item_ids_current, 'string_all_codes_current' => $string_all_codes_current]);
    }

    private
    function get_child_links(Base $base)
    {
        // "sortBy('parent_base_number')" обязательно использовать
        return $base->child_links->sortBy('parent_base_number');
    }

    private
    function get_array_calc(Base $base, Item $item = null, $create = false, Link $par_link = null, Item $parent_item = null)  // 'Item $item=null' нужно
    {
        // по настройке links
        $plan_child_links = self::get_child_links($base);
        if (!$create) {
            // по факту в таблице mains
            // Не использовать команду "$fact_child_mains = $item->child_mains;"
            // - неправильно вытаскивает данные, когда находится внутри транзакции при корректировке записи
            //$fact_child_mains = $item->child_mains;
            //$fact_child_mains = Main::all()->where('child_item_id', $item->id);
            $fact_child_mains = Main::where('child_item_id', '=', $item->id)->get();
        }
        $array_plan = array();
        foreach ($plan_child_links as $key => $link) {
            // добавление или корректировка массива по ключу $link_id
            // заносится null, т.к. это план (настройка от таблицы links)
            $array_plan[$link->id] = null;
        }

        // если main->link_id одинаковый для записей, то берется одно значение(последнее по списку)
        $array_fact = array();
        $array_disabled = array();
        $array_refer = array();
        if ($par_link && $parent_item) {
            if (array_key_exists($par_link->id, $array_plan)) {
                $array_disabled[$par_link->id] = $parent_item->id;
            }
            // вычисление зависимых значений по фильтрируемым полям
            // чтобы были с признаком disabled поля при создании записи
            self::par_link_calc_in_array_disabled($plan_child_links, $parent_item, $array_disabled, $par_link);
        }

        // При создании записи
        if ($create) {
            // если переданы $par_link и $parent_item
            foreach ($array_disabled as $key => $value) {
                if (array_key_exists($key, $array_plan)) {
                    $array_fact[$key] = $array_disabled[$key];
                }
            }
        } else {
            foreach ($fact_child_mains as $key => $main) {
                // добавление или корректировка массива по ключу $link_id
                // заносится $main->parent_item_id (используется в форме ext_edit)
                $array_fact[$main->link_id] = $main->parent_item_id;
            }
        }

// объединяем два массива, главный $array_plan
// он содержит количество записей, как настроено в links
// индекс массива = links->id
// значение массива = null (при создании нового item или если в mains нет записи с таким links->id)
// или mains->parent_item_id (по существующим записям в mains)
        foreach ($array_plan as $key => $value) {
            if (array_key_exists($key, $array_fact)) {
                $array_plan[$key] = $array_fact[$key];
//                $link = Link::findOrFail($key);
//                if($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true){
//                    // В "$array_fact[$key]" хранится item_id
//                    $item = Item::findOrFail($array_fact[$key]);
//                    $array_refer[$key]=$item->code;
//                }

            }
        }
//        $array_fill = array();
//        foreach ($array_plan as $key => $value) {
//            if (array_key_exists($key, $array_fact)) {
//                if ($array_plan[$key] != null) {
//                    $array_fill[$key] = $array_plan[$key];
//                }
//            }
//        }
        // array_fill() - список полей со значениями (не равны null)
        // array_disabled() - список полей, которые будут недоступны для ввода
        // array_refer() - список значений $item->code

        // Учет "disabled" при "$link->parent_is_nc_viewonly==true", см ItemController::get_array_calc()
        foreach ($array_plan as $key => $value) {
            $link = Link::find($key);
            if ($link) {
                if ($link->parent_is_nc_viewonly == true) {
                    $array_disabled[$key] = 0;
                }
            }
        }

//      return ['array_calc' => $array_plan, 'array_fill' => $array_fill, 'array_disabled' => $array_disabled, 'array_refer' => $array_refer];
        return ['array_calc' => $array_plan, 'array_disabled' => $array_disabled, 'array_refer' => $array_refer];
    }

    private
    function get_array_calc_create(Base $base, Link $par_link = null, Item $parent_item = null)
    {
        return $this->get_array_calc($base, null, true, $par_link, $parent_item);
    }

    private
    function get_array_calc_edit(Item $item, Link $par_link = null, Item $parent_item = null)
    {
        return self::get_array_calc($item->base, $item, false, $par_link, $parent_item);
    }

// Рекурсивная функция
// Вычисление зависимых значений по фильтрируемым полям
// Например, есть фильтр на форме Поставщик - Номер заказа,
// parlink, например, поле Номер заказа, тогда поле Поставщик тоже будет с признаком disabled в форме
    private
    function par_link_calc_in_array_disabled($plan_child_links, $parent_item, &$array_disabled, Link $p_link)
    {
        foreach ($plan_child_links as $key => $link) {
            if ($link->parent_is_child_related == true) {
                if ($link->parent_child_related_start_link_id == $p_link->id) {
                    $link_result = Link::find($link->parent_child_related_result_link_id);
                    if ($link_result) {
                        $item = self::get_parent_item_from_child_item($parent_item, $link_result)['result_item'];
                        if ($item) {
                            $array_disabled[$link->id] = $item->id;
                            // рекурсивный вызов этой же функции, $link передается в функцию
                            self::par_link_calc_in_array_disabled($plan_child_links, $parent_item, $array_disabled, $link);
                        }
                    }
                }
            }
        }
    }

    function show(Item $item)
    {
        return view('item/show', ['type_form' => 'show', 'item' => $item]);
    }

    static function base_relit_right(Base $base, Role $role, $heading, $base_index_page, $relit_id, $parent_ret_id)
    {
        $result = null;
        $parent_ret_id = GlobalController::set_relit_id($parent_ret_id);
        //if ($heading == 1 || $base_index_page > 0) {
        $result = GlobalController::base_right($base, $role, $relit_id);
        //} else {
        //    $result = GlobalController::base_right($base, $role, $parent_ret_id);
        //}
        return $result;
    }

//    function ext_show(Item $item, Project $project, Role $role, $usercode,
//                           $relit_id,
//                           $string_link_ids_current = '', $string_item_ids_current = '', $string_all_codes_current = '',
//                           $heading = 0,
//                           $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
//                           $parent_ret_id = null,
//                           $view_link = null,
//                      Link $par_link = null, Item $parent_item = null)

    function ext_show(Item $item, Project $project, Role $role, $usercode,
                           $relit_id,
                           $string_current = '',
                           $heading = 0,
                           $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
                           $parent_ret_id = null,
                           $view_link = null,
                      Link $par_link = null, Item $parent_item = null)
    {

        if (GlobalController::check_project_item_user($project, $item, $role, $usercode) == false) {
            return view('message', ['message' => trans('main.no_access')]);
        }

//        if (GlobalController::check_project_user($project, $role) == false) {
//            return view('message', ['message' => trans('main.info_user_changed')]);
//        }

        //$base_right = self::base_relit_right($item->base, $role, $heading, $base_index_page, $relit_id, $parent_ret_id);
        $base_right = GlobalController::base_right($item->base, $role, $relit_id);
        if ($base_right['is_list_base_calc'] == false) {
            return view('message', ['message' => trans('main.no_access')]);
        }

        // Проверка $item
        // Использовать так "GlobalController::items_check_right($item, $role, $relit_id, true)"
        $item_ch = GlobalController::items_check_right($item, $role, $relit_id, true);
        if (!$item_ch) {
            return view('message', ['message' => trans('main.access_restricted')]);
        }

        $is_limit_minutes = GlobalController::is_limit_minutes($base_right, $item);
        $is_checking_history = GlobalController::is_checking_history($item, $role, $relit_id);
        $is_checking_empty = GlobalController::is_checking_empty($item, $role, $relit_id);

        $string_unzip_current_next = self::string_unzip_current_next($string_current);
        $string_link_ids_current = $string_unzip_current_next['string_link_ids'];
        $string_item_ids_current = $string_unzip_current_next['string_item_ids'];
        $string_relit_ids_current = $string_unzip_current_next['string_relit_ids'];
        $string_vwret_ids_current = $string_unzip_current_next['string_vwret_ids'];
        $string_all_codes_current = $string_unzip_current_next['string_all_codes'];

        // Команды ниже нужны
        if ($string_link_ids_current == '') {
            $string_link_ids_current = GlobalController::const_null();
        }
        if ($string_item_ids_current == '') {
            $string_item_ids_current = GlobalController::const_null();
        }
        if ($string_relit_ids_current == '') {
            $string_relit_ids_current = GlobalController::const_null();
        }
        if ($string_vwret_ids_current == '') {
            $string_vwret_ids_current = GlobalController::const_null();
        }
        if ($string_all_codes_current == '') {
            $string_all_codes_current = GlobalController::const_null();
        }

        return view('item/ext_show', ['type_form' => 'show', 'item' => $item,
            'role' => $role,
            'project' => $project,
            'relit_id' => $relit_id,
            'base_right' => $base_right,
            'array_calc' => $this->get_array_calc_edit($item)['array_calc'],
            'is_limit_minutes' => $is_limit_minutes,
            'is_checking_history' => $is_checking_history,
            'is_checking_empty' => $is_checking_empty,
            'string_link_ids_current' => $string_link_ids_current,
            'string_item_ids_current' => $string_item_ids_current,
            'string_relit_ids_current' => $string_relit_ids_current,
            'string_vwret_ids_current' => $string_vwret_ids_current,
            'string_all_codes_current' => $string_all_codes_current,
            'string_current' => self::string_zip_current_next(
                $string_link_ids_current,
                $string_item_ids_current,
                $string_relit_ids_current,
                $string_vwret_ids_current,
                $string_all_codes_current),
            'heading' => $heading,
            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
            'view_link' => GlobalController::set_par_view_link_null($view_link),
            'par_link' => $par_link, 'parent_item' => $parent_item,
            'parent_ret_id' => $parent_ret_id
        ]);
    }

//    function ext_create(Base $base, Project $project, Role $role, $usercode, $relit_id,
//                             $string_link_ids_current = '', $string_item_ids_current = '', $string_all_codes_current = '',
//                             $heading = 0, $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
//                             $parent_ret_id = null,
//                             $view_link = null,
//                        Link $par_link = null, Item $parent_item = null)
    function ext_create(Base $base, Project $project, Role $role, $usercode, $relit_id,
                             $string_current = '',
                             $heading = 0, $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
                             $parent_ret_id = null,
                             $view_link = null,
                        Link $par_link = null, Item $parent_item = null)
        // '$heading = 0' использовать; аналог '$heading = false', в этом случае так /item/ext_create/{base}//
    {
        if (GlobalController::check_project_item_user($project, null, $role, $usercode) == false) {
            return view('message', ['message' => trans('main.no_access')]);
        }

//        if (GlobalController::check_project_user($project, $role) == false) {
//            return view('message', ['message' => trans('main.info_user_changed')]);
//        }

//      $base_right = self::base_relit_right($base, $role, $heading, $base_index_page, $relit_id, $parent_ret_id);
        $base_right = GlobalController::base_right($base, $role, $relit_id);

//      Похожая проверка в ItemController::ext_create() и base_index.php
        if ($base_right['is_list_base_create'] == false) {
            return view('message', ['message' => trans('main.no_access')]);
        }

        // Проверка: выводить минуты при 'Ограничение в минутах для корректировки/удаления данных'
        // Используется "GlobalController::is_limit_add_record_minutes()"
        $is_view_minutes = GlobalController::is_limit_add_record_minutes($base_right);

        // Проверка 'Доступность ввода данных на основе проверки истории (links)'
        // Используется "GlobalController::is_checking_add_history()"
        $is_checking_add_history = GlobalController::is_checking_add_history($role, $relit_id, $par_link, $parent_item);
        if ($is_checking_add_history['result_entry_history'] == false) {
            return view('message', ['message' => $is_checking_add_history['result_message_history']]);
        }

        // Проверка 'Доступность ввода данных на основе проверки заполненности данных (links)'
        // Используется "GlobalController::is_checking_add_empty()"
        $is_checking_add_empty = GlobalController::is_checking_add_empty($role, $relit_id, $par_link, $parent_item);
        if ($is_checking_add_empty['result_entry_empty'] == false) {
            return view('message', ['message' => $is_checking_add_empty['result_message_empty']]);
        }

        $string_unzip_current_next = self::string_unzip_current_next($string_current);
        $string_link_ids_current = $string_unzip_current_next['string_link_ids'];
        $string_item_ids_current = $string_unzip_current_next['string_item_ids'];
        $string_relit_ids_current = $string_unzip_current_next['string_relit_ids'];
        $string_vwret_ids_current = $string_unzip_current_next['string_vwret_ids'];
        $string_all_codes_current = $string_unzip_current_next['string_all_codes'];

        $relip_project = GlobalController::calc_relip_project($relit_id, $project);
        $arrays = $this->get_array_calc_create($base, $par_link, $parent_item);
        $array_calc = $arrays['array_calc'];
        $array_disabled = $arrays['array_disabled'];
        $code_new = $this->calculate_new_code($base, $relip_project);
        // Похожая строка внизу
        // создать уникальный идентификато
        $code_uniqid = uniqid($base->id . '_', true);
        //$view_link = GlobalController::set_view_link_null($view_link);

        //$array_parent_related = GlobalController::get_array_parent_related($base);
//        'string_all_codes_current' => $string_all_codes_current,
//            'string_link_ids_current' => $string_link_ids_current,
//            'string_item_ids_current' => $string_item_ids_current,
        return view('item/ext_edit', ['base' => $base,
            'code_new' => $code_new, 'code_uniqid' => $code_uniqid,
            'heading' => $heading,
            'project' => $project,
            'role' => $role,
            'relit_id' => $relit_id,
            'string_current' => self::string_zip_current_next(
                $string_link_ids_current,
                $string_item_ids_current,
                $string_relit_ids_current,
                $string_vwret_ids_current,
                $string_all_codes_current),
            'array_calc' => $array_calc,
            'array_disabled' => $array_disabled,
            'is_view_minutes' => $is_view_minutes,
            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
            'parent_ret_id' => $parent_ret_id,
            'view_link' => GlobalController::set_par_view_link_null($view_link),
            'par_link' => $par_link,
            'parent_item' => $parent_item]);
    }

    function create()
    {
        return view('item/edit', ['bases' => Base::all()]);
    }

    static function extstore_ext(Request $request, Base $base, Project $project, Role $role, $usercode,
                                         $relit_id)
    {
        self::ext_store($request, $base, $project, $role, $usercode, $relit_id);
    }
//    function ext_store(Request $request, Base $base, Project $project, Role $role, $usercode,
//                               $relit_id,
//                               $string_link_ids_current = '', $string_item_ids_current = '', $string_all_codes_current = '',
//                               $heading = 0, $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
//                               $parent_ret_id = null,
//                               $view_link = null,
//                       Link    $par_link = null, Item $parent_item = null)

    function ext_store(Request $request, Base $base, Project $project, Role $role, $usercode,
                               $relit_id,
                               $string_current = '',
                               $heading = 0, $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
                               $parent_ret_id = null,
                               $view_link = null,
                       Link    $par_link = null, Item $parent_item = null)
    {

        if (GlobalController::check_project_item_user($project, null, $role, $usercode) == false) {
            return view('message', ['message' => trans('main.no_access')]);
        }

        //        if (GlobalController::check_project_user($project, $role) == false) {
//            return view('message', ['message' => trans('main.info_user_changed')]);
//        }
        $relip_project = GlobalController::calc_relip_project($relit_id, $project);

        //https://webformyself.com/kak-v-php-poluchit-znachenie-checkbox/
        //        if($base->type_is_boolean()){
//            $request->validate($this->name_lang_boolean_rules());
//        }else{
        $request->validate($this->code_rules($request, $relip_project->id, $base->id));
//        }

        // Проверка на $base->maxcount_lst
        // Проверка осуществляется только при добавлении записи в начале функции и при сохранении записи
        $message = GlobalController::base_maxcount_validate($relip_project, $base, true);
        if ($message != '') {
//            $array_mess['name_lang_0'] = $message;
//            // повторный вызов формы
//            return redirect()->back()
//                ->withInput()
//                ->withErrors($array_mess);
            return view('message', ['message' => $message]);
        }

        // Проверка на $base->maxcount_user_id_lst
        // Проверка осуществляется только при добавлении записи
        $message = GlobalController::base_user_id_maxcount_validate($relip_project, $base, true);
        if ($message != '') {
            return view('message', ['message' => $message]);
        }

        // Проверка на $base->maxcount_byuser_lst
        // Проверка осуществляется только при добавлении записи
        $message = GlobalController::base_byuser_maxcount_validate($relip_project, $base, true);
        if ($message != '') {
            return view('message', ['message' => $message]);
        }

        // Проверка на максимальное количество записей
        // Это есть проверка в начале функции и во время сохранения записи $item
        if ($par_link && $parent_item) {
            // Проверка на $par_link->link_maxcount
            $message = GlobalController::link_maxcount_validate($relip_project, $par_link, true);
            if ($message != '') {
                return view('message', ['message' => $message]);
            }
            // Проверка на $par_link->child_maxcount
            // added = 'true' используется
            $message = GlobalController::link_item_maxcount_validate($relip_project, $parent_item, $par_link, true);
            if ($message != '') {
                return view('message', ['message' => $message]);
            }
        }

        // Проверка полей с типом "текст" на длину текста
        if ($base->type_is_text() && $base->length_txt > 0) {
            $errors = false;
            foreach (config('app.locales') as $lang_key => $lang_value) {
                if (strlen($request['name_lang_' . $lang_key]) > $base->length_txt) {
                    $array_mess['name_lang_' . $lang_key] = trans('main.length_txt_rule') . ' ' . $base->length_txt . '!';
                    $errors = true;
                }
            }
            if ($errors) {
                // повторный вызов формы
                return redirect()->back()
                    ->withInput()
                    ->withErrors($array_mess);
            }
        }
        $base_right = GlobalController::base_right($base, $role, $relit_id);
        // Проверка на обязательность ввода наименования
        //if ($base->is_required_lst_num_str_txt_img_doc == true && $base->is_calcname_lst == false) {
        //          'Обязательно к заполнению (для списков, при условии $base->is_required_lst_num_str_txt_img_doc = false
        if ($base_right['is_base_required'] == true && $base->is_calcname_lst == false) {
            // Тип - список, строка или текст
            if ($base->type_is_list() || $base->type_is_string() || $base->type_is_text()) {
                $name_lang_array = array();
                // значения null в ""
                $name_lang_array[0] = isset($request->name_lang_0) ? $request->name_lang_0 : "";
                $name_lang_array[1] = isset($request->name_lang_1) ? $request->name_lang_1 : "";
                $name_lang_array[2] = isset($request->name_lang_2) ? $request->name_lang_2 : "";
                $name_lang_array[3] = isset($request->name_lang_3) ? $request->name_lang_3 : "";
                $errors = false;
                $i = 0;
                foreach (config('app.locales') as $lang_key => $lang_value) {
                    if (($base->is_one_value_lst_str_txt == true && $lang_key == 0) || ($base->is_one_value_lst_str_txt == false)) {
                        // Точное сравнение "$name_lang_array[$i] === ''" используется
                        if ($name_lang_array[$i] === '') {
                            $array_mess['name_lang_' . $i] = trans('main.is_required_lst_num_str_txt_img_doc') . '!';
                            $errors = true;
                        }
                        $i = $i + 1;
                    }
                }
                if ($errors) {
                    // повторный вызов формы
                    return redirect()->back()
                        ->withInput()
                        ->withErrors($array_mess);
                }
                // Тип - число
            } elseif ($base->type_is_number()) {
                // значения null в "0"
                $name_lang_0_val = isset($request->name_lang_0) ? $request->name_lang_0 : "0";
                $errors = false;
                // "$value === '0'" использовать для точного сравнения (например, при $link->parent_base->type_is_string())
                if ($name_lang_0_val === '0') {
                    $array_mess['name_lang_0'] = trans('main.is_required_lst_num_str_txt_img_doc') . '!';
                    $errors = true;
                } else {
                    $floatvalue = floatval($name_lang_0_val);
                    if ($floatvalue == 0) {
                        $array_mess['name_lang_0'] = trans('main.is_required_lst_num_str_txt_img_doc') . '!';
                        $errors = true;
                    }
                }
                if ($errors) {
                    // повторный вызов формы
                    return redirect()->back()
                        ->withInput()
                        ->withErrors($array_mess);
                }
                // Тип - изображение
            } elseif ($base->type_is_image()) {
                $errors = false;
                if (!$request->hasFile('name_lang_0')) {
                    $array_mess['name_lang_0'] = trans('main.is_required_lst_num_str_txt_img_doc') . '!';
                    $errors = true;
                }
                if ($errors) {
                    // повторный вызов формы
                    return redirect()->back()
                        ->withInput()
                        ->withErrors($array_mess);
                }
                // Тип - документ
            } elseif ($base->type_is_document()) {
                $errors = false;
                if (!$request->hasFile('name_lang_0')) {
                    $array_mess['name_lang_0'] = trans('main.is_required_lst_num_str_txt_img_doc') . '!';
                    $errors = true;
                }
                if ($errors) {
                    // повторный вызов формы
                    return redirect()->back()
                        ->withInput()
                        ->withErrors($array_mess);
                }
            }
        }
        // Проверка полей с типом "текст" на наличие запрещенных тегов HTML
        // Правило: только в текстовых полях можно применять разрешенную HTML-теги
        if ($base->type_is_text()) {
            $errors = false;
            foreach (config('app.locales') as $lang_key => $lang_value) {
                $text_html_check = GlobalController::text_html_check($request['name_lang_' . $lang_key]);
                if ($text_html_check['result'] == true) {
                    $array_mess['name_lang_' . $lang_key] = $text_html_check['message'] . '!';
                    $errors = true;
                }
            }
            if ($errors) {
                // повторный вызов формы
                return redirect()->back()
                    ->withInput()
                    ->withErrors($array_mess);
            }
        }

        if ($base->type_is_image() || $base->type_is_document()) {
            if ($request->hasFile('name_lang_0')) {
                $fs = $request->file('name_lang_0')->getSize();
                $mx = $base->maxfilesize_img_doc;
                if ($fs > $mx) {
                    $errors = false;
                    if ($request->file('name_lang_0')->isValid()) {
                        $array_mess['name_lang_0'] = self::filesize_message($fs, $mx);
                        $errors = true;
                    }
                    if ($errors) {
                        // повторный вызов формы
                        return redirect()->back()
                            ->withInput()
                            ->withErrors($array_mess);
                    }
                }
            }
        }

        $string_unzip_current_next = self::string_unzip_current_next($string_current);
        $string_link_ids_current = $string_unzip_current_next['string_link_ids'];
        $string_item_ids_current = $string_unzip_current_next['string_item_ids'];
        $string_relit_ids_current = $string_unzip_current_next['string_relit_ids'];
        $string_vwret_ids_current = $string_unzip_current_next['string_vwret_ids'];
        $string_all_codes_current = $string_unzip_current_next['string_all_codes'];

        // установка часового пояса нужно для сохранения времени
        date_default_timezone_set('Asia/Almaty');

        $item = new Item($request->except('_token', '_method'));
        $item->base_id = $base->id;
        //$project = Project::findOrFail($request->project_id);
        //$role = Role::findOrFail($request->role_id);
        // Похожая проверка в ext_edit.blade.php
//        if ($base->is_code_needed == true && $base->is_code_number == true && $base->is_limit_sign_code == true
//            && $base->is_code_zeros == true && $base->is_code_zeros > 0) {
//            // Дополнить код слева нулями
//            $item->code = str_pad($item->code, $base->significance_code, '0', STR_PAD_LEFT);
//        }

        // нужно по порядку: сначала этот блок
        // значения null в ""
        // у строк могут быть пустые значения, поэтому нужно так: '$item->name_lang_0 = isset($request->name_lang_0) ? $request->name_lang_0 : ""'
        $item->name_lang_0 = isset($request->name_lang_0) ? $request->name_lang_0 : "";
        $item->name_lang_1 = isset($request->name_lang_1) ? $request->name_lang_1 : "";
        $item->name_lang_2 = isset($request->name_lang_2) ? $request->name_lang_2 : "";
        $item->name_lang_3 = isset($request->name_lang_3) ? $request->name_lang_3 : "";
        $item->project_id = $relip_project->id;

        // далее этот блок
        // похожие формулы ниже (в этой же процедуре)

        // тип - логический
        if ($base->type_is_boolean()) {
            $item->name_lang_0 = isset($request->name_lang_0) ? "1" : "0";

            // тип - число
        } elseif ($base->type_is_number()) {
            $item->name_lang_0 = GlobalController::save_number_to_item($base, $request->name_lang_0);

        } // тип - текст
        elseif ($base->type_is_text()) {
            $item->name_lang_0 = GlobalController::itnm_left($request->name_lang_0);
            $item->name_lang_1 = GlobalController::itnm_left($request->name_lang_1);
            $item->name_lang_2 = GlobalController::itnm_left($request->name_lang_2);
            $item->name_lang_3 = GlobalController::itnm_left($request->name_lang_3);
        }

        // затем этот блок (используется "$base")
        if ($base->type_is_number() || $base->type_is_date() || $base->type_is_boolean()) {
            // присваивание полям наименование строкового значение числа/даты
//            foreach (config('app.locales') as $key => $value) {
//                if ($key > 0) {
//                    $item['name_lang_' . $key] = $item->name_lang_0;
//                }
//            }
            $item->name_lang_1 = $item->name_lang_0;
            $item->name_lang_2 = $item->name_lang_0;
            $item->name_lang_3 = $item->name_lang_0;
        }

        $this::save_img_doc($request, $item);

        $excepts = array('_token', 'code', '_method', 'name_lang_0', 'name_lang_1', 'name_lang_2', 'name_lang_3');
        $string_langs = $this->get_child_links($base);
        // Формируется массив $code_names - названия полей кодов
        // Формируется массив $string_names - названия полей наименование
        $code_names = array();
        $string_names = array();
        $i = 0;

        foreach ($string_langs as $key => $link) {
            if ($link->parent_base->type_is_string() || $link->parent_base->type_is_text()) {
                $i = 0;
                foreach (config('app.locales') as $lang_key => $lang_value) {
                    // начиная со второго(индекс==1) элемента массива языков сохранять
                    if ($i > 0) {
                        // для первого (нулевого) языка $input_name = $key ($link->id)
                        // для последующих языков $input_name = $key . '_' . $lang_key($link->id . '_' . $lang_key);
                        // это же правило используется в ext_edit.blade.php
                        //$string_names[] = $link->id . ($lang_key == 0) ? '' : '_' . $lang_key;  // так не работает, дает '' в результате
                        $string_names[] = ($lang_key == 0) ? $link->id : $link->id . '_' . $lang_key;  // такой вариант работает
                    }
                    $i = $i + 1;
                }
            }
            if ($link->parent_is_enter_refer == true) {
                $code_names[] = 'code' . $link->id;
            }
        }

        // загрузить в $inputs все поля ввода, кроме $excepts, $string_names, $string_codes, array_merge() - функция суммирования двух и более массивов
        $inputs = $request->except(array_merge($excepts, $string_names, $code_names));

        $it_texts = null;
        if ($item->base->type_is_text()) {
            $only = array('name_lang_0', 'name_lang_1', 'name_lang_2', 'name_lang_3');
            $it_texts = $request->only($only);

            foreach ($it_texts as $it_key => $it_text) {
                $it_texts[$it_key] = isset($it_texts[$it_key]) ? $it_texts[$it_key] : "";
            }
        }

        // Проверка существования кода объекта
        foreach ($inputs as $key => $value) {
            $link = Link::findOrFail($key);
            if ($link->parent_base->is_code_needed == true && $link->parent_is_enter_refer == true) {
                if ($value != 0) {
                    $item_needed = Item::find($value);
                    if (!$item_needed) {
                        $array_mess['code' . $key] = trans('main.code_not_found') . "!";
                        // повторный вызов формы
                        return redirect()->back()
                            ->withInput()
                            ->withErrors($array_mess);
                    }
                }
            }
        }

        foreach ($inputs as $key => $value) {
            $link = Link::findOrFail($key);
            if ($link->parent_base->type_is_image() || $link->parent_base->type_is_document()) {
                if ($request->hasFile($link->id)) {
                    $fs = $request->file($link->id)->getSize();
                    $mx = $link->parent_base->maxfilesize_img_doc;
                    if ($fs > $mx) {
                        $errors = false;
                        if ($request->file($link->id)->isValid()) {
                            $array_mess[$link->id] = self::filesize_message($fs, $mx);
                            $errors = true;
                        }

                        if ($errors) {
                            // повторный вызов формы
                            return redirect()->back()
                                ->withInput()
                                ->withErrors($array_mess);
                        }
                    }
                }
            }
        }

// обработка для логических полей
// если при вводе формы пометка checkbox не установлена, в $request записи про элемент checkbox вообще нет
// если при вводе формы пометка checkbox установлена, в $request есть запись со значеним "on"
// см. https://webformyself.com/kak-v-php-poluchit-znachenie-checkbox/
//        foreach ($string_langs as $link) {
//            // Проверка нужна
//            $base_link_right = GlobalController::base_link_right($link, $role);
//            if ($base_link_right['is_edit_link_enable'] == false) {
//                continue;
//            }
//            // похожая формула выше (в этой же процедуре)
//            if ($link->parent_base->type_is_boolean()) {
//                // у этой команды два предназначения:
//                // 1) заменить "on" на "1" при отмеченном checkbox
//                // 2) создать новый ([$link->id]-й) элемент массива со значением "0" при выключенном checkbox
//                // в базе данных информация хранится как "0" или "1"
//                $inputs[$link->id] = isset($inputs[$link->id]) ? "1" : "0";
//            }
//        }

        foreach ($string_langs as $link) {
            if ($link->parent_base->type_is_boolean()) {
                // Проверка нужна
//                $base_link_right = GlobalController::base_link_right($link, $role);
//                if ($base_link_right['is_edit_link_update'] == false) {
//                    continue;
//                }
                // похожая формула выше (в этой же процедуре)
                // у этой команды два предназначения:
                // 1) заменить "on" на "1" при отмеченном checkbox
                // 2) создать новый ([$link->id]-й) элемент массива со значением "0" при выключенном checkbox
                // в базе данных информация хранится как "0" или "1"
                $inputs[$link->id] = isset($inputs[$link->id]) ? "1" : "0";
            }
        }

        $array_mess = array();
        foreach ($string_langs as $link) {
            if ($link->parent_is_parent_related == false) {
                // Тип - изображение
                if ($link->parent_base->type_is_image() || $link->parent_base->type_is_document()) {
                    // Проверка на обязательность ввода
                    if ($link->parent_base->is_required_lst_num_str_txt_img_doc == true) {
                        $errors = false;
                        if (!$request->hasFile($link->id)) {
                            $array_mess[$link->id] = trans('main.is_required_lst_num_str_txt_img_doc') . '!';
                            $errors = true;
                        }
                        if ($errors) {
                            // повторный вызов формы
                            return redirect()->back()
                                ->withInput()
                                ->withErrors($array_mess);
                        }
                    }
                }
            }
        }

        foreach ($inputs as $key => $value) {
            $inputs[$key] = ($value != null) ? $value : "";
        }

        // Только при добавлении записи
        foreach ($inputs as $key => $value) {
            $link = Link::findOrFail($key);
            // Так не использовать
//          if ($link->parent_is_seqnum == 1 & floatval($inputs[$key]) == 0) {
            if ($link->parent_is_seqnum == 1) {
                if (floatval($inputs[$key]) == 0) {
                    $pr_item = null;
                    if ($link->parent_seqnum_link_id != 0) {
                        $lnk = Link::find($link->parent_seqnum_link_id);
                        if ($lnk) {
                            if ($lnk->parent_base->type_is_list()) {
                                if (isset($inputs[$link->parent_seqnum_link_id])) {
                                    $pr_item = Item::find($inputs[$link->parent_seqnum_link_id]);
                                    // Нужно проверять "if ($pr_item)",
                                    // т.к. вызов calculate_new_seqnum($project, $link, null, null) - расчет кода для всей основы(таблицы)
                                    if ($pr_item) {
                                        $inputs[$key] = $this->calculate_new_seqnum($project, $link, $pr_item, $lnk);
                                    }
                                }
                            }
                        }
                    } else {
                        $pr_item = null;
                        $inputs[$key] = $this->calculate_new_seqnum($project, $link);
                    }
                }
            }
        }
        $strings_inputs = $request->only($string_names);

        foreach ($strings_inputs as $key => $value) {
            $strings_inputs[$key] = ($value != null) ? $value : "";
        }

        foreach ($inputs as $key => $value) {
            $link = Link::findOrFail($key);
            if ($link->parent_base->type_is_image() || $link->parent_base->type_is_document()) {
                $path = "";
                if ($request->hasFile($key)) {
                    if ($link->parent_base->type_is_image()) {
                        $path = GlobalController::image_store($request, $key, $item->project_id, $link->parent_base_id);
                    } else {
                        $path = $request[$key]->store('public/' . $item->project_id . '/' . $link->parent_base_id);
                    }
                }
                $inputs[$key] = $path;
            } elseif ($link->parent_base->type_is_number()) {
                $inputs[$key] = GlobalController::save_number_to_item($link->parent_base, $value);
            }
        }
        $keys = array_keys($inputs);
        $values = array_values($inputs);

// Проверка полей с типом "текст" на длину текста
        $errors = false;
        foreach ($inputs as $key => $value) {
            $link = Link::findOrFail($key);
            $work_base = $link->parent_base;
            if ($work_base->type_is_text() && $work_base->length_txt > 0) {
                $errors = false;
                $name_lang_value = null;
                $name_lang_key = null;
                $i = 0;
                foreach (config('app.locales') as $lang_key => $lang_value) {
                    if (($work_base->is_one_value_lst_str_txt == true && $lang_key == 0) || ($work_base->is_one_value_lst_str_txt == false)) {
                        if ($i == 0) {
                            $name_lang_key = $key;
                            $name_lang_value = $value;
                        }
                        // начиная со второго(индекс==1) элемента массива языков учитывать
                        if ($i > 0) {
                            $name_lang_key = $key . '_' . $lang_key;
                            $name_lang_value = $strings_inputs[$name_lang_key];
                        }
                        if (strlen($name_lang_value) > $work_base->length_txt) {
                            $array_mess[$name_lang_key] = trans('main.length_txt_rule') . ' ' . $work_base->length_txt . '!';
                            $errors = true;
                        }
                        $i = $i + 1;
                    }
                }
                if ($errors) {
                    // повторный вызов формы
                    return redirect()->back()
                        ->withInput()
                        ->withErrors($array_mess);
                }
            }
        }

        $errors = false;
        foreach ($inputs as $key => $value) {
            $link = Link::findOrFail($key);
            $base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
            $work_base = $link->parent_base;
            // при типе "логический" проверять на обязательность заполнения не нужно
            $control_required = false;
            // При ссылке '$link->parent_is_base_link == false' не проверять на обязательность заполнения
            if ($link->parent_is_base_link == false) {
                // Тип - список
                if ($work_base->type_is_list()) {
                    // так не использовать
                    // Проверка на обязательность ввода
                    //if ($work_base->is_required_lst_num_str_txt_img_doc == true) {
                    //          'Обязательно к заполнению (для списков, при условии $base->is_required_lst_num_str_txt_img_doc = false
                    if ($base_link_right['is_base_required'] == true) {
                        $control_required = true;
                    }
                } // Тип - число
                elseif ($work_base->type_is_number()) {
                    // Проверка на обязательность ввода
                    if ($work_base->is_required_lst_num_str_txt_img_doc == true) {
                        $control_required = true;
                    }
                } // Тип - строка или текст
                elseif ($work_base->type_is_string() || $work_base->type_is_text()) {
                    // Проверка на обязательность ввода
                    if ($work_base->is_required_lst_num_str_txt_img_doc == true) {
                        $control_required = true;
                    }
                } // Тип - дата
                elseif ($work_base->type_is_date()) {
                    $control_required = true;
                }
            }
            // при типе корректировки поля "строка", "логический" проверять на обязательность заполнения не нужно
            if ($control_required == true) {
                // Тип - строка или текст
                if ($work_base->type_is_string() || $work_base->type_is_text()) {
                    // поиск в таблице items значение с таким же названием и base_id
                    $name_lang_value = null;
                    $name_lang_key = null;
                    $i = 0;
                    foreach (config('app.locales') as $lang_key => $lang_value) {
                        if (($work_base->is_one_value_lst_str_txt == true && $lang_key == 0) || ($work_base->is_one_value_lst_str_txt == false)) {
                            if ($i == 0) {
                                $name_lang_key = $key;
                                $name_lang_value = $value;
                            }
                            // начиная со второго(индекс==1) элемента массива языков учитывать
                            if ($i > 0) {
                                $name_lang_key = $key . '_' . $lang_key;
                                $name_lang_value = $strings_inputs[$name_lang_key];
                            }
                            // "<option value = '0'>" присваивается при заполнении 'edit.blade' если нет данных (объектов по заданному base)            if ($value == 0)
                            // "$value === '0'" использовать для точного сравнения (например, при $link->parent_base->type_is_string())
                            // Преобразование null в '' было ранее произведено
                            if ($name_lang_value == "") {
//                              $array_mess[$name_lang_key] = trans('main.no_data_on') . ' "' . $link->parent_base->name() . '"!';
                                $array_mess[$name_lang_key] = trans('main.no_data_on') . ' "' . $link->parent_label() . '"!';
                                $errors = true;
                            }
                            $i = $i + 1;
                        }
                    }
                } else {
                    // "<option value = '0'>" присваивается при заполнении 'edit.blade' если нет данных (объектов по заданному base)            if ($value == 0)
                    // "$value === '0'" использовать для точного сравнения (например, при $link->parent_base->type_is_string())
                    if ($value == null) {
                        $array_mess[$key] = trans('main.no_data_on') . ' "' . $link->parent_label() . '"!';
                        $errors = true;
                    } elseif ($value === '0') {
                        $array_mess[$key] = trans('main.no_data_on') . ' "' . $link->parent_label() . '"!';
                        $errors = true;
                    } else {
                        $floatvalue = floatval($value);
                        if ($floatvalue == 0) {
                            $array_mess[$key] = trans('main.no_data_on') . ' "' . $link->parent_label() . '"!';
                            $errors = true;
                        }
                    }
                }
            }
            // Проверка полей с типом "текст" на наличие запрещенных тегов HTML
            // Правило: только в текстовых полях можно применять разрешенную HTML-теги
            if ($work_base->type_is_text()) {
                // поиск в таблице items значение с таким же названием и base_id
                $name_lang_value = null;
                $name_lang_key = null;
                $i = 0;
                foreach (config('app.locales') as $lang_key => $lang_value) {
                    if ($i == 0) {
                        $name_lang_key = $key;
                        $name_lang_value = $value;
                    }
                    if ($link->parent_base->is_one_value_lst_str_txt == false) {
                        // начиная со второго(индекс==1) элемента массива языков учитывать
                        if ($i > 0) {
                            $name_lang_key = $key . '_' . $lang_key;
                            $name_lang_value = $strings_inputs[$name_lang_key];
                        }
                    }
                    $text_html_check = GlobalController::text_html_check($name_lang_value);
                    if ($text_html_check['result'] == true) {
                        $array_mess[$name_lang_key] = $text_html_check['message'] . '!';
                        $errors = true;
                    }
                    $i = $i + 1;
                }
            }
        }

        // Проверка на уникальность базовых типов Дата, Число, Строка, Логический
        $message = self::verify_item_unique($item);
        if ($message != '') {
            $array_mess['name_lang_0'] = $message;
            $errors = true;
        }

        if ($errors) {
            // повторный вызов формы
            return redirect()->back()
                ->withInput()
                ->withErrors($array_mess);
        }

// Одно значение у всех языков
        if ($base->is_one_value_lst_str_txt == true) {
            $item->name_lang_1 = $item->name_lang_0;
            $item->name_lang_2 = $item->name_lang_0;
            $item->name_lang_3 = $item->name_lang_0;
        }

// при создании записи "$item->created_user_id" заполняется
        $item->created_user_id = Auth::user()->id;
        $item->updated_user_id = Auth::user()->id;

        try {
            // начало транзакции
            DB::transaction(function ($r) use ($relip_project, $item, $role, $relit_id, $it_texts, $keys, $values, $strings_inputs) {
                // При добавлении записи
                // Эта команда "$item->save();" нужна, чтобы при сохранении записи стало известно значение $item->id.
                // оно нужно в функции save_main() (для команды "$main->child_item_id = $item->id;");
                $item->save();

                // Присвоение $item->id при $link->parent_is_base_link и при добавлении записи
                foreach ($keys as $index => $value) {
                    $link = Link::findOrFail($value);
                    // Проверка Показывать Связь с признаком "Ссылка на основу"
                    if ($link->parent_is_base_link == true) {
                        $values[$index] = $item->id;
                    }
                }

                // тип - текст
                if ($it_texts) {
                    if ($item->base->type_is_text()) {
                        //$text = $item->text();
                        $text = Text::where('item_id', $item->id)->first();
                        if (!$text) {
                            $text = new Text();
                            $text->item_id = $item->id;
                        }
                        $text->name_lang_0 = $it_texts['name_lang_0'];
                        // Одно значение у всех языков для тип - текст
                        if ($item->base->is_one_value_lst_str_txt == true) {
                            $text->name_lang_1 = $text->name_lang_0;
                            $text->name_lang_2 = $text->name_lang_0;
                            $text->name_lang_3 = $text->name_lang_0;
                        } else {
                            $text->name_lang_1 = "";
                            $text->name_lang_2 = "";
                            $text->name_lang_3 = "";
                            foreach ($it_texts as $it_key => $it_text) {
                                $text[$it_key] = $it_texts[$it_key];
                            }
                        }
                        $text->save();
                    }
                }

//              после ввода данных в форме массив состоит:
//              индекс массива = link_id (для занесения в links->id)
//              значение массива = item_id (для занесения в mains->parent_item_id)
                $i_max = count($keys);

//                // Предыдущий вариант
//                $mains = Main::where('child_item_id', $item->id)->get();
//                $i = 0;
//                foreach ($mains as $main) {
//                    if ($i < $i_max) {
//                        $this->save_main($main, $item, $keys, $values, $i, $strings_inputs);
//                        $i = $i + 1;
//                    } else {
//                        $main->delete();
//                    }
//                }
//                for ($i; $i < $i_max; $i++) {
//                    $main = new Main();
//                    $this->save_main($main, $item, $keys, $values, $i, $strings_inputs);
//                }

                // Проверку можно убрать, т.к. $item создается
                // Новый вариант
                // Сначала проверка, потом присвоение
                // Проверка на $main->link_id, если такой не найден - то удаляется
                $mains = Main::where('child_item_id', $item->id)->get();
                foreach ($mains as $main) {
                    $delete_main = false;
                    $link = Link::where('id', $main->link_id)->first();
                    if ($link) {
                        if ($link->child_base_id != $item->base_id) {
                            $delete_main = true;
                            // Нужно
                        } elseif ($link->parent_is_parent_related == true) {
                            $delete_main = true;
                            // Нужно
                        } elseif ($link->parent_is_output_calculated_table_field == true) {
                            $delete_main = true;
                        }
                    } else {
                        $delete_main = true;
                    }
                    if ($delete_main) {
                        $main->delete();
                    }
                }

                $valits = $values;
                // Присвоение данных для $this->save_sets()
                // "$i = 0" использовать, т.к. индексы в массивах начинаются с 0
                $i = 0;

                foreach ($keys as $key) {
                    $link = Link::findOrFail($key);

                    $main = Main::where('child_item_id', $item->id)->where('link_id', $key)->first();
                    if ($main == null) {
                        $main = new Main();
                        // при создании записи "$item->created_user_id" заполняется
                        $main->created_user_id = Auth::user()->id;
                    } else {
                        // удалить файл-предыдущее значение при корректировке
                        if ($main->parent_item->base->type_is_image() || $main->parent_item->base->type_is_document()) {
                            if ($values[$i] != "") {
                                Storage::delete($main->parent_item->filename());
                            }
                        }
                    }
                    $message = '';
                    $this->save_main($main, $item, $keys, $values, $valits, $i, $strings_inputs, $message);
                    if ($message != '') {
                        throw new Exception($message);
                    }
                    // После выполнения массив $valits заполнен ссылками $item->id

                    // Проверка на максимальное количество записей
                    // Это есть проверка в начале функции и во время сохранения записи $item
                    // Проверка на $par_link->link_maxcount
                    // 'added = false' используется, нужно
                    $message = GlobalController::link_maxcount_validate($relip_project, $link, false);
                    if ($message != '') {
                        throw new Exception($message);
                    }
                    // Проверка на $link->child_maxcount
                    // added = 'true' используется
//                    $message_info = GlobalController::link_item_maxcount_validate($relip_project, $main->parent_item, $link, true);
//                    if ($message_info != '') {
//                        $item_maxcount = Item::findOrFail($valits[$i]);
//                        // added = 'true' используется
//                        $message_result = GlobalController::link_item_maxcount_validate($relip_project, $item_maxcount, $link, true);
//                        if ($message_result != '') {
//                            throw new Exception($message_result);
//                        }
//                    }
                    // Проверка на $link->child_maxcount
                    $item_maxcount = Item::find($valits[$i]);
                    if ($item_maxcount) {
                        // 'added = false' используется, нужно
                        $message = GlobalController::link_item_maxcount_validate($relip_project, $item_maxcount, $link, false);
                        if ($message != '') {
                            throw new Exception($message);
                        }
                    }

                    // "$i = $i + 1;" использовать здесь, т.к. индексы в массивах начинаются с 0
                    $i = $i + 1;
                }
                // Проверка на $base->maxcount_lst, После цикла по $keys
                // Проверка осуществляется только при добавлении записи в начале функции и при сохранении записи
                // 'added = false' используется, нужно
                $message = GlobalController::base_maxcount_validate($relip_project, $item->base, false);
                if ($message == '') {
                    self::func_del_items_maxcnt($relip_project, $item);
                } else {
                    throw new Exception($message);
                }

                // Проверка на уникальность значений $item->child_mains;
                // Похожие строки при добавлении (функция ext_store()) и сохранении (функция ext_update()) записи
                $get_child_links = $this->get_child_links($item->base);
                // Нужно 'where('id', '!=', $item->id)'
                $items_unique_select = Item::where('id', '!=', $item->id)
                    ->where('project_id', '=', $relip_project->id);
                // Нужно '$items_unique_exist = false;'
                $items_unique_exist = false;
                // Нужно '$items_unique_bases = '';'
                $items_unique_bases = '';
                foreach ($get_child_links as $key => $link) {
                    $link_id = $link->id;
                    if ($link->parent_is_unique == true) {
                        $main = Main::where('child_item_id', $item->id)->where('link_id', $link_id)->first();
                        if ($main) {
                            $parent_item_id = $main->parent_item_id;
                            $items_unique_select = $items_unique_select->whereHas('child_mains', function ($query) use ($link_id, $parent_item_id) {
                                $query->where('link_id', $link_id)
                                    ->where('parent_item_id', $parent_item_id);
                            });
                            $items_unique_bases = $items_unique_bases . ($items_unique_exist == false ? '' : ', ') . $link->parent_base->name();;
                            // Нужно '$items_unique_exist = true;'
                            $items_unique_exist = true;
                        }
                    }
                }
                if ($items_unique_exist == true) {
                    $items_unique_select = $items_unique_select->get();
                    if (count($items_unique_select) != 0) {
                        throw new Exception(trans('main.value_uniqueness_violation') . ' (' . $items_unique_bases . ')!');
                    }
                }

                // Ипользовать GlobalController::is_checking_history()
                // Проверка 'Доступность ввода данных на основе проверки истории (links)'
                $is_checking_history = GlobalController::is_checking_history($item, $role, $relit_id);
                if ($is_checking_history['result_entry_history'] == false) {
                    throw new Exception($is_checking_history['result_message_history']);
                }

                // Проверка 'Доступность ввода данных на основе проверки заполненности данных (links)'
                $is_checking_empty = GlobalController::is_checking_empty($item, $role, $relit_id);
                if ($is_checking_empty['result_entry_empty'] == false) {
                    throw new Exception($is_checking_empty['result_message_empty']);
                }

                $rs = $this->calc_value_func($item);
                if ($rs != null) {
                    $item->name_lang_0 = $rs['calc_lang_0'];
                    $item->name_lang_1 = $rs['calc_lang_1'];
                    $item->name_lang_2 = $rs['calc_lang_2'];
                    $item->name_lang_3 = $rs['calc_lang_3'];
                }
                // В ext_store() вызывается один раз, т.к. запись создается
                // При reverse = false передаем null
                // параметр $urepl = true - с заменой
                //$this->save_sets($item, $keys, $values, $valits, false, true);

                $this->save_info_sets($item, false, true);

                $item->save();

            }, 3);  // Повторить три раза, прежде чем признать неудачу
            // окончание транзакции

        } catch (Exception $exc) {
            //return trans('transaction_not_completed') . ": " . $exc->getMessage();
            return view('message', ['message' => trans('main.transaction_not_completed') . ": " . $exc->getMessage()]);
        }

        if (env('MAIL_ENABLED') == 'yes') {
            $base_right = GlobalController::base_right($item->base, $role, $relit_id);
            if ($base_right['is_edit_email_base_create'] == true) {
                $email_to = $item->project->user->email;
                $appname = config('app.name', 'Abakus');
                try {
                    Mail::send(['html' => 'mail/item_create'], ['item' => $item],
                        function ($message) use ($email_to, $appname, $item) {
                            $message->to($email_to, '')->subject(trans('main.new_record') . ' - ' . $item->base->name());
                            $message->from(env('MAIL_FROM_ADDRESS', ''), $appname);
                        });
                } catch (Exception $exc) {
                    return trans('error_sending_email') . ": " . $exc->getMessage();
                }
            }
        }

        //  Похожий текст в функциях ext_store(), ext_update(), ext_delete(), ext_return();
        //  По алгоритму передается $base_index_page, $body_link_page, $body_all_page - сохраненные номера страниц;
        $parent_find_item = true;
        if ($parent_item) {
            // За время добавления(маловероятно, т.к. есть связи между основами)/корректировки/удаления
            // $parent_item может быть удален из базы данных.
            // Например, при установке признака 'Разрешить корректировку поля при связи parlink (при корректировке записи)' в rolis
            // и например, поле $parent_item логического типа
            $parent_find_item = Item::find($parent_item->id);
        }
        if (!$parent_find_item) {
            // Вызов главного меню
            return redirect()->route('project.start', ['project' => $project, 'role' => $role]);
        }
        $str_link = '';
        if ($base_index_page > 0) {
            // Использовать "project' => $project"
            // Используется "'relit_id'=> $relit_id"
            return redirect()->route('item.base_index', ['base' => $item->base, 'project' => $project, 'role' => $role,
                'relit_id' => $relit_id,
                'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page]);
        } else {
            // Если $heading = true - нажата Добавить из "heading", false - из "body" (только при добавлении записи)
            $str_link = '';
            if ($body_all_page > 0) {
                // Вызываем item_index.php с body - все
                $str_link = GlobalController::par_link_const_textnull();
            } else {
                // Вызываем item_index.php с body - связь $par_link
                $str_link = $view_link;
            }
            if (!$heading && $parent_item) {
                // Используется "'relit_id'=>$parent_ret_id, 'view_ret_id' => $relit_id'"
                return redirect()->route('item.item_index', ['project' => $project, 'item' => $parent_item, 'role' => $role,
                    'usercode' => GlobalController::usercode_calc(),
                    'relit_id' => $parent_ret_id,
                    'called_from_button' => 1,
                    'view_link' => $str_link,
                    // 'string_link_ids_current' => $string_link_ids_current, 'string_item_ids_current' => $string_item_ids_current, 'string_all_codes_current' => $string_all_codes_current,
                    'string_current' => $string_current,
                    'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
                    'prev_base_index_page' => $base_index_page,
                    'prev_body_link_page' => $body_link_page,
                    'prev_body_all_page' => $body_all_page,
                    'view_ret_id' => $relit_id]);
            } else {
                // Используется "'relit_id'=>$parent_ret_id, 'view_ret_id' => $relit_id'"
//                return redirect()->route('item.item_index', ['project' => $project, 'item' => $item, 'role' => $role,
//                    'usercode' => GlobalController::usercode_calc(),
//                    'relit_id' => $parent_ret_id,
//                    'view_link' => $str_link,
//                    'string_link_ids_current' => $string_link_ids_current, 'string_item_ids_current' => $string_item_ids_current, 'string_all_codes_current' => $string_all_codes_current,
//                    'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
//                    'prev_base_index_page' => $base_index_page,
//                    'prev_body_link_page' => $body_link_page,
//                    'prev_body_all_page' => $body_all_page,
//                    'view_ret_id' => $relit_id]);
                return redirect()->route('item.item_index', ['project' => $project, 'item' => $item, 'role' => $role,
                    'usercode' => GlobalController::usercode_calc(),
                    'relit_id' => $relit_id,
                    'called_from_button' => 1,
                    'view_link' => $str_link,
                    // 'string_link_ids_current' => $string_link_ids_current, 'string_item_ids_current' => $string_item_ids_current, 'string_all_codes_current' => $string_all_codes_current,
                    'string_current' => $string_current,
                    'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
                    'prev_base_index_page' => $base_index_page,
                    'prev_body_link_page' => $body_link_page,
                    'prev_body_all_page' => $body_all_page,
                    'view_ret_id' => $parent_ret_id
                ]);
            }
        }
    }

    // Автоматическое удаление записей из $items при достижении предела разрешенного количества записей
    function func_del_items_maxcnt(Project $project, Item $item)
    {
        $base = $item->base;
        $skip = $base->maxcount_lst;
        if (($item->base->is_del_maxcnt_lst == true) & ($skip > 0)) {
            // Выбрать все записи, кроме $skip первых, отсортировано по дате изменения
            //'$skip - 1' нужно, т.к. есть условие "where('id', '!=',$item->id)"
            $items = Item::where('project_id', '=', $project->id)
                ->where('id', '!=', $item->id)
                ->where('base_id', '=', $base->id)
                ->skip($skip - 1)->take(100000)
                ->orderBy('updated_at', 'desc')->get();
            foreach ($items as $value) {
                // Инициализация массива, нужно
                $array_items_ids = array();
                // Вычисляем массив вложенных $item_id для удаления
                self::calc_items_ids_for_delete($value, $array_items_ids, false);
                // Нужно
                self::func_delete($value);
                // Удаление подчиненных связанных записей
                self::run_items_ids_for_delete($array_items_ids);
            }
        }
    }

    // Вызывается из ext_store(), ext_update()
    // Проверка на уникальность базовых типов Дата, Число, Строка, Логический
    // Похожие строки есть в ItemController::save_main() и в ItemController::verify_item_unique()
    function verify_item_unique(Item $item)
    {
        $result = "";
        $base = $item->base;
        $result_dop = "";
        if ($base->type_is_number() | $base->type_is_string() | $base->type_is_date() | $base->type_is_boolean()) {
            $items = Item::where('project_id', '=', $item->project_id)
                ->where('base_id', '=', $base->id)
                ->where('name_lang_0', '=', $item->name_lang_0);
            if ($base->type_is_string() & $base->is_one_value_lst_str_txt == false) {
                $i = 0;
                $result_dop = '"' . $item->name_lang_0;
                foreach (config('app.locales') as $lang_key => $lang_value) {
                    // начиная со второго(индекс==1) элемента массива языков учитывать
                    if ($i > 0) {
                        $items = $items->where('name_lang_' . $lang_key, $item['name_lang_' . $lang_key]);
                        $result_dop = $result_dop . ", " . $item['name_lang_' . $lang_key];
                    }
                    $i = $i + 1;
                }
                $result_dop = $result_dop . '" - ';
            }
            $exists = $items->exists();
            if ($exists) {
                $result = $result_dop . trans('main.record_already_exists') . '!';
            }
        }
        return $result;
    }

// save_info_sets() выполняет все присваивания для $item с отниманием/прибавлением значений
// Расчитывает массивы $keys_reverse, $values_reverse, $valits_reverse, $reverse
// сначала по всем $itpv->base->child_links()->get(),
// потом по фактическому заполнению $itpv->child_mains()->get()
// Это сделано для того, чтобы правильно вычислялись first()/last() для изображений и документов
// Есть особенность ввода форм (ext_edit.php):
// если файл изображения/документа не выбран, или есть предыдущее значение файл изображения/документа,
// то тогда $request/$mains (при обработке формы и присваиваний) нет признака ввода файла изображения/документа,
// что давало неправильные результаты при расчете first()/last() для изображений и документов.
// Нужно выполнять присваивания по всем $links
// $reverse = true - отнимать, false - прибавлять
// $urepl = true используется при добавлении/корректировке записи, = false при удалении записи; проверяется при Заменить(->is_upd_replace = true)
// private
    function save_info_sets(Item $item, bool $reverse, bool $urepl)
    {
        $is_save_sets = self::is_save_sets($item);
        if (!$is_save_sets) {
            return;
        }
        $itpv = Item::findOrFail($item->id);

        $inputs_reverse = array();

        $links = $itpv->base->child_links()->get();
        foreach ($links as $key => $link) {
            //$inputs_reverse[$link->id] = -1;
            // см.комментарий выше
//            if ($link->parent_base->type_is_image() | $link->parent_base->type_is_document()) {
//                // "-1" - такое значение, чтобы не находилось Item::find() с таким значением
//                $inputs_reverse[$link->id] = -1;
//            } elseif ($link->parent_is_base_link == true) {
//                $inputs_reverse[$link->id] = $item->id;
//            }
//            if ($link->parent_is_base_link == true) {
//                $inputs_reverse[$link->id] = $item->id;
//            }
        }

        $mains = $itpv->child_mains()->get();
        //$inputs_reverse = array();
        foreach ($mains as $key => $main) {
            $inputs_reverse[$main->link_id] = $main->parent_item_id;
        }
//
//        $valits_previous = null;
//        if ($reverse == true) {
//            $item_previous = Item::where('base_id', $itpv->base_id)->where('base_id', $itpv->base_id)->first();
//            $mains = $itpv->child_mains()->get();
//            $inputs_previous = array();
//            foreach ($mains as $key => $main) {
//                $inputs_previous[$main->link_id] = $main->parent_item_id;
//            }
//            $valits_previous = array_values($inputs_previous);
//        }

        $invals = array();
        foreach ($inputs_reverse as $key => $value) {
//            $item_work = Item::findOrFail($value);
//            $var = $item_work->numval();
//            if ($var['result'] == true) {
//                $invals[$key] = $var['value'];
//            } else {
//                $invals[$key] = $inputs_reverse[$key];
//            }
            $item_work = Item::find($value);
            if ($item_work) {
                $var = $item_work->numval();
                if ($var['result'] == true) {
                    $invals[$key] = $var['value'];
                } else {
                    $invals[$key] = $inputs_reverse[$key];
                }
            } else {
                $invals[$key] = $inputs_reverse[$key];
            }
        }

        $keys_reverse = array_keys($inputs_reverse);
        $values_reverse = array_values($invals);
        $valits_reverse = array_values($inputs_reverse);

        $this->save_sets($itpv, $keys_reverse, $values_reverse, $valits_reverse, $reverse, $urepl);

    }

// Проверка на возможность выполнения присваиваний для переданного $item
    private
    function is_save_sets(Item $item)
    {
//        $set_main = Set::select(DB::Raw('sets.*, lt.child_base_id as to_child_base_id, lt.parent_base_id as to_parent_base_id'))
//            ->join('links as lf', 'sets.link_from_id', '=', 'lf.id')
//            ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
//            ->where('lf.child_base_id', '=', $item->base_id)
//            ->where('sets.is_savesets_enabled', '=', true)
//            ->orderBy('sets.serial_number')
//            ->orderBy('sets.link_from_id')
//            ->orderBy('sets.link_to_id')->get();
        $set_main = Set::select(DB::Raw('sets.*, lt.child_base_id as to_child_base_id, lt.parent_base_id as to_parent_base_id'))
            ->join('links as lf', 'sets.link_from_id', '=', 'lf.id')
            ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
            ->where('lf.child_base_id', '=', $item->base_id)
            ->where('sets.is_savesets_enabled', '=', true)
            ->get();
        $result = null;
        // Эта проверка нужна
        if ($set_main) {
            // Эта проверка нужна
            if (count($set_main) > 0) {
                $result = true;
            }
        }

        return $result;

    }

//    Эти функции похожи:
// save_sets()
// get_item_from_parent_output_calculated_table()
// get_sets_group()
// get_sets_list_group()
// get_parent_item_from_output_calculated_table()
// Обрабатывает присваивания
// $valits_previous - предыщения значения $valits при $reverse = true и обновлении данных = замена
    private
    function save_sets(Item $item, $keys, $values, $valits, bool $reverse, bool $urepl)
    {
//        $table1 = Set::select(DB::Raw('sets.*'))
//            ->join('links', 'sets.link_from_id', '=', 'links.id')
//            ->join('bases', 'links.child_base_id', '=', $item->base_id)
//            ->orderBy('sets.link_from_id')
//            ->orderBy('sets.link_to_id')->get();
        $kf = $reverse == true ? -1 : 1;
        $set_main = Set::select(DB::Raw('sets.*, lt.child_base_id as to_child_base_id, lt.parent_base_id as to_parent_base_id'))
            ->join('links as lf', 'sets.link_from_id', '=', 'lf.id')
            ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
            ->where('lf.child_base_id', '=', $item->base_id)
            ->where('sets.is_savesets_enabled', '=', true)
            ->where('sets.is_calcsort', '=', false)
            ->orderBy('sets.serial_number')
            ->orderBy('sets.line_number')
            ->orderBy('sets.link_from_id')
            ->orderBy('sets.link_to_id')->get();

        // Группировка $set_main по serial_number, индексы массива - serial_number
        $set_group_by_serial_number = $set_main->groupBy('serial_number')->
        sortBy('serial_number');

        //$table2 = Set::select(DB::Raw('$table1.*'))->get();
        // Цикл по записям, в каждой итерации цикла свой порядковый номер $sn_key
        foreach ($set_group_by_serial_number as $sn_key => $sn_value) {
            // Группировка $set_main по to_child_base_id, индексы массива - to_child_base_id
            //             + нужный фильтр "where('serial_number', '=', $sn_key)"
            $set_group_by_base_to = $set_main->where('serial_number', '=', $sn_key)->
            groupBy('to_child_base_id')->
            sortBy('to_child_base_id');
            // Цикл по записям, в каждой итерации цикла свой to_child_base_id в переменной $to_key
            foreach ($set_group_by_base_to as $to_key => $to_value) {
                // Выборка из $set_main
                // "where('serial_number', '=', $sn_key)" нужно
                $set_base_to = $set_main->where('serial_number', '=', $sn_key)->
                where('to_child_base_id', '=', $to_key)->
                sortBy('to_parent_base_id');

                // Группировка данных
                $set_is_group = $set_base_to->where('is_group', true);

                // Использовать именно так "Item::where('base_id', $to_key)->where('project_id', $item->project_id)"
                $items = Item::where('base_id', $to_key)->where('project_id', $item->project_id);

                $error = true;
                $found = false;
                $item_seek = null;

                // Поиск $item_seek в цикле
                // Цикл по группировке данных
                foreach ($set_is_group as $key => $value) {
                    $relip_project = GlobalController::calc_relip_project($value->relit_to_id, $item->project);

//                   проверка, если link - вычисляемое поле
                    //if ($link->parent_is_parent_related == true || $link->parent_is_numcalc == true)
                    if ($value->link_from->parent_is_parent_related == true) {

                    } else {
                        //$item_seek = GlobalController::view_info($item, $value['link_from_id']);
                        //Находится $nk - индекс/порядковый номер
                        $nk = -1;
                        foreach ($keys as $k => $v) {
                            if ($v == $value['link_from_id']) {
                                $nk = $k;
                                break;
                            }
                        }
                        if ($nk != -1) {
                            $set_to = $set_is_group->where('link_from_id', $value['link_from_id'])->first();
                            if ($set_to) {
                                $nt = $set_to->link_to_id;
                                //$nv = $values[$nk];
                                // Получить item->id
                                $nv = $valits[$nk];
//                                //$items = $items->whereHas('child_mains', function ($query) use ($nt, $nv) {
//                                //    $query->where('link_id', $nt)->where('parent_item_id', $nv);
//                                //});
//                                $items = $items->where('project_id', $relip_project->id)
//                                    ->whereHas('child_mains', function ($query) use ($nt, $nv) {
//                                        $query->where('link_id', $nt)->where('parent_item_id', $nv);
//                                    });
//                                // Поиск по item
//                                // похожие строки чуть ниже
//                                $item_seek = $items->first();
//                                $error = false;
//                                if (!$item_seek) {
//                                    $found = false;
//                                    break;
//                                } else {
//                                    $found = true;
//                                }
                                //$nv_find = Item::find($nv);
//                                if ($nv_find) {
                                //$items = $items->whereHas('child_mains', function ($query) use ($nt, $nv) {
                                //    $query->where('link_id', $nt)->where('parent_item_id', $nv);
                                //});
                                $items = $items->where('project_id', $relip_project->id)
                                    ->whereHas('child_mains', function ($query) use ($nt, $nv) {
                                        $query->where('link_id', $nt)->where('parent_item_id', $nv);
                                    });
                                // Поиск по item
                                // похожие строки чуть ниже
                                $item_seek = $items->first();
                                // Нужно "$error = false;"
                                $error = false;
                                if (!$item_seek) {
                                    $found = false;
                                    break;
                                } else {
                                    $found = true;
                                }
//                                } else {
//                                    // Нужно "$error = false;"
//                                    $error = false;
//                                    $found = false;
//                                    break;
//                                }
                            }
                        }
                    }
                }
                // Если нет группировки
                if (count($set_is_group) == 0) {
                    // похожие строки чуть выше
                    $item_seek = $items->first();
                    $error = false;
                    if (!$item_seek) {
                        $found = false;
                    } else {
                        $found = true;
                    }
                }
                if (!$error) {
                    $create_item_seek = false;

                    if (!$found) {
                        $create_item_seek = true;
                        // Эта проверка сделана, чтобы зря не создавать $item_seek
                        // Фильтры 111 - похожие строки ниже
                        $relip_project = null;
                        foreach ($set_base_to as $key => $value) {
                            $relip_project = GlobalController::calc_relip_project($value->relit_to_id, $item->project);
                            //Находится $nk - индекс/порядковый номер
                            $nk = -1;
                            foreach ($keys as $k => $v) {
                                if ($v == $value['link_from_id']) {
                                    $nk = $k;
                                    break;
                                }
                            }
                            if ($nk == -1) {
                                // $item_seek не создано
                                // Выход из цикла
                                $create_item_seek = false;
                                break;
                            }
                        }
                        if ($create_item_seek == true) {
                            // создать новую запись
                            $item_seek = new Item();
                            $item_seek->base_id = $to_key;
                            if ($relip_project) {
                                $item_seek->project_id = $relip_project->id;
                            } else {
                                // Нужно
                                dd(trans('main.parent_project_not_found' . '!'));
                            }
                            $item_seek->code = uniqid($item_seek->id . '_', true);
                            $item_seek->name_lang_0 = "";
                            $item_seek->name_lang_1 = "";
                            $item_seek->name_lang_2 = "";
                            $item_seek->name_lang_3 = "";
                            $item_seek->created_user_id = Auth::user()->id;
                            $item_seek->updated_user_id = Auth::user()->id;
//                          $item_seek->created_user_id = $relip_project->user_id;
//                          $item_seek->updated_user_id = $relip_project->user_id;

                            // Нужно, чтобы id было
                            $item_seek->save();
                        }
                    } else {
                        // "$create_item_seek = true;" нужно
                        $create_item_seek = true;
                        // true - с реверсом
                        $this->save_info_sets($item_seek, true, $urepl);
                    }
                    // Если нужно создавать $item
                    // Если $item_seek создано
                    if ($create_item_seek == true) {
                        //$items = $items->get();
                        $error = true;
                        $found = false;

                        // Фильтры 111 - похожие строки выше
                        foreach ($set_base_to as $key => $value) {
                            $relip_project = GlobalController::calc_relip_project($value->relit_to_id, $item->project);
                            //Находится $nk - индекс/порядковый номер
                            $nk = -1;
                            foreach ($keys as $k => $v) {
                                if ($v == $value['link_from_id']) {
                                    $nk = $k;
                                    break;
                                }
                            }
                            if ($nk != -1) {
                                $nt = $value->link_to_id;
                                $nlink = Link::find($nt);
                                if ($nlink) {
                                    //$nv = $values[$nk];
                                    $main = Main::where('link_id', $nt)->where('child_item_id', $item_seek->id)->first();
                                    $error = false;
                                    $vl = 0;
                                    if (!$main) {
                                        $main = new Main();
                                        // при создании записи "$item->created_user_id" заполняется
                                        $main->created_user_id = Auth::user()->id;

                                        $main->link_id = $nt;
                                        $main->child_item_id = $item_seek->id;
                                        $vl = 0;
                                    } else {
                                        $vl = $main->parent_item->numval()['value'];
                                    }
                                    $main->updated_user_id = Auth::user()->id;

                                    // "$seek_item = false" нужно
                                    // "$seek_value = 0" нужно
                                    $seek_item = false;
                                    $seek_value = 0;
                                    $delete_main = false;
                                    $ch = 0;
                                    if ($value->link_to->parent_base->type_is_number() && is_numeric($values[$nk])) {
                                        $ch = $values[$nk];
                                    } else {
                                        $ch = 0;
                                    }
                                    if ($value->is_group == true) {
                                        $main->parent_item_id = $valits[$nk];
                                    } elseif ($value->is_update == true) {
                                        if ($value->is_upd_plussum == true || $value->is_upd_pluscount == true) {
                                            // Учет Количества
                                            if ($value->is_upd_pluscount == true) {
                                                $ch = 1;
                                            }
                                            $seek_item = true;
                                            $seek_value = $vl + $kf * $ch;
//                                        // Удалить запись с нулевым значением при обновлении
//                                        if ($value->is_upd_delete_record_with_zero_value == true) {
//                                            if ($seek_value == 0) {
//                                                $valnull = true;
//                                            }
//                                        }
                                        } elseif ($value->is_upd_minussum == true || $value->is_upd_minuscount == true) {
                                            // Учет Количества
                                            if ($value->is_upd_minuscount == true) {
                                                $ch = 1;
                                            }
                                            $seek_item = true;
                                            $seek_value = $vl - $kf * $ch;
//                                        // Удалить запись с нулевым значением при обновлении
//                                        if ($value->is_upd_delete_record_with_zero_value == true) {
//                                            if ($seek_value == 0) {
//                                                $valnull = true;
//                                            }
//                                        }
                                        } elseif ($value->is_upd_replace == true) {
                                            if ($urepl == true) {
                                                //if ($reverse == false && $valits[$nk] != 0) {
                                                $main->parent_item_id = $valits[$nk];
//                                            // Удалить запись с нулевым значением при обновлении
//                                            if ($value->is_upd_delete_record_with_zero_value == true) {
//                                                $item_numval = Item::findOrFail($main->parent_item_id);
//                                                $numval = $item_numval->numval();
//                                                if ($numval["result"] == true) {
//                                                    if ($numval["value"] == 0) {
//                                                        $valnull = true;
//                                                    }
//                                                }
//                                            }
                                            } else {
                                                $delete_main = true;
                                                // Используем $valits_previous[$nk]
//                                            $main->parent_item_id = $valits_previous[$nk];
//                                            // Удалить запись с нулевым значением при обновлении
//                                            if ($value->is_upd_delete_record_with_zero_value == true) {
//                                                $valnull = true;
//                                            }
                                            }
                                        }
                                        // При $reverse == false
                                        // и при корректировке записи(если подкорректировано поле группировки)
                                        // и при удалении записи
                                        // работает некорректно
                                        // При $reverse == true работает корректно
                                        elseif ($value->is_upd_cl_gr_first == true || $value->is_upd_cl_gr_last == true) {
                                            $calc = "";

                                            if ($value->is_upd_cl_gr_first == true) {
                                                $calc = "first";
                                            } elseif ($value->is_upd_cl_gr_last == true) {
                                                $calc = "last";
                                            }
                                            // Расчет Первый(), Последний()
                                            //$item_calc = null;
                                            $item_calc = self::get_item_from_parent_output_calculated_firstlast_table($item, $value, $calc, $reverse);
                                            if ($item_calc) {
                                                $main->parent_item_id = $item_calc->id;
                                            } else {
                                                $delete_main = true;
                                            }
                                        }
                                    }
                                    if ($delete_main == true) {
                                        $main->delete();
                                    } else {
                                        //  Добавление числа в базу данных
                                        if ($seek_item == true) {
                                            $item_find = self::find_save_number($value->link_to->parent_base_id, $relip_project->id, $seek_value);
                                            $main->parent_item_id = $item_find->id;
                                        }
                                        $main->save();

                                    }
                                }
                            }
                        }

                        $rs = $this->calc_value_func($item_seek);

                        if ($rs != null) {
                            $item_seek->name_lang_0 = $rs['calc_lang_0'];
                            $item_seek->name_lang_1 = $rs['calc_lang_1'];
                            $item_seek->name_lang_2 = $rs['calc_lang_2'];
                            $item_seek->name_lang_3 = $rs['calc_lang_3'];
                        }

                        $item_seek->save();

                        // Не использовать: возможны ошибки, "лишние" операции
                        // Вызов обработки присваиваний вложенных
                        // false - без реверса
                        // "$this->save_info_sets()" выполнять перед проверкой на удаление
                        // $this->save_info_sets($item_seek, false);
                        $this->save_info_sets($item_seek, false, $urepl);

                        // Если links->"Удалить запись с нулевым значением при обновлении" == true и значение равно нулю,
                        // то удалить запись
                        $val_item_seek_delete = $this->val_item_seek_delete_func($item_seek, $urepl);
                        if ($val_item_seek_delete) {
                            $item_seek->delete();
                        } else {
                            // Похожие строки выше
                            // Если в цикле не создано mains в цикле
                            if (!$item_seek->child_mains()->exists()) {
                                $item_seek->delete();
                            }
                        }
                    }
                }
            }
        }
    }

    static function val_item_seek_delete_func(Item $item, $urepl)
    {
        $result = false;
//        $mains = Main::select(DB::Raw('mains.*'))
//            ->join('links', 'mains.link_id', '=', 'links.id')
//            ->join('bases', 'links.parent_base_id', '=', 'bases.id')
//            ->where('mains.child_item_id', $item->id)
//            ->where('links.parent_is_delete_child_base_record_with_zero_value', true)
//            ->where('bases.type_is_number', true)
//            ->get();
        $mains = Main::select(DB::Raw('mains.*'))
            ->join('links', 'mains.link_id', '=', 'links.id')
            ->join('bases', 'links.parent_base_id', '=', 'bases.id')
            ->where('mains.child_item_id', $item->id)
            ->where('links.parent_is_delete_child_base_record_with_zero_value', true)
            ->where(function ($query) {
                $query->where('bases.type_is_number', true)
                    ->orWhere('bases.type_is_boolean', true);
            })
            ->get();
        // Эта проверка нужна
        if ($mains) {
            // Эта проверка нужна
            if (count($mains) > 0) {
                $valtotal = true;
                // Цикл по записям
                // links.parent_is_delete_child_base_record_with_zero_value = true может быть несколько у одной записи child
                // проверка на равенство проверяется для всех записей одновременно в цикле
                foreach ($mains as $main) {
                    $item_numval = Item::findOrFail($main->parent_item_id);
                    $valnull = false;
                    if ($item_numval) {
                        $numval = $item_numval->numval();
                        if ($numval["result"] == true) {
                            if ($numval["value"] == 0) {
                                $valnull = true;
                            }
                        }
                    }
                    $valtotal = $valtotal && $valnull;
                    if ($valtotal == false) {
                        break;
                    }
                }
                if ($valtotal) {
                    $result = true;
                }
            }
        }

        // При удалении записи, например
        if ($urepl == false) {
            if ($result == false) {
                // "->get()" нужно
                // Поиск записей "where('links.parent_is_delete_child_base_record_with_zero_value', true)" без $main
                // Запись $main может быть ранее удалена при замене ($value->is_upd_replace == true) в функции save_sets()
                // Если такие записи есть, то считать итоговое значение = 0 и удалить запись ($result = true;)
                $links = Link::select(DB::Raw('links.*'))
                    ->join('bases', 'links.parent_base_id', '=', 'bases.id')
                    ->where('links.child_base_id', $item->base_id)
                    ->where('links.parent_is_delete_child_base_record_with_zero_value', true)
                    ->where(function ($query) {
                        $query->where('bases.type_is_number', true)
                            ->orWhere('bases.type_is_boolean', true);
                    })
                    ->get();
                foreach ($links as $link) {
                    $main = Main::where('link_id', $link->id)->where('child_item_id', $item->id)->first();
                    // Если не найдено
                    if (!$main) {
                        $result = true;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    static function find_save_number($base_id, $project_id, $seek_value)
    {
        $item_find = null;
        $base = Base::find($base_id);
        if ($base) {
            $item_find = Item::where('base_id', $base_id)->where('project_id', $project_id)
                ->where('name_lang_0', GlobalController::save_number_to_item($base, $seek_value))
                ->first();
            // если не найдено
            if (!$item_find) {
                // создание новой записи в items
                $item_find = new Item();
                $item_find->base_id = $base_id;
                // Похожие строки вверху
                $item_find->code = uniqid($item_find->base_id . '_', true);
                // присваивание полям наименование строкового значение числа
                foreach (config('app.locales') as $key => $value) {
                    if ($item_find->base->type_is_number()) {
                        $item_find['name_lang_' . $key] = GlobalController::save_number_to_item($item_find->base, $seek_value);
                    } else {
                        $item_find['name_lang_' . $key] = $seek_value;
                    }
                }
                $item_find->project_id = $project_id;
                // при создании записи "$item->created_user_id" заполняется
                $project = Project::find($project_id);
                if ($project) {
                    $item_find->created_user_id = $project->user_id;
                    $item_find->updated_user_id = $project->user_id;
                } else {
                    $item_find->created_user_id = Auth::user()->id;
                    $item_find->updated_user_id = Auth::user()->id;
                }
                $item_find->save();
            }
        }
        return $item_find;
    }

//    // Вызывается из save_sets()
//    // Вычисление first(), last()
    static function get_item_from_parent_output_calculated_firstlast_table(Item $item, Set $set, $calc, $reverse)
    {
        $result_item = null;
        //$set = Set::find($link->parent_output_calculated_table_set_id);
        if ($set) {
            // base_id вычисляемой таблицы
            $calc_table_base_id = $set->link_to->child_base_id;
            // Не нужно 'where('sets.is_savesets_enabled', '=', false)'
            $sets_group = Set::select(DB::Raw('sets.*'))
                ->join('links as lf', 'sets.link_from_id', '=', 'lf.id')
                ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
                ->where('is_group', true)
                ->where('lf.child_base_id', '=', $item->base_id)
                ->where('serial_number', '=', $set->serial_number)
                ->orderBy('sets.serial_number')
                ->orderBy('sets.link_from_id')
                ->orderBy('sets.link_to_id')
                ->get();

            $items = Item::where('base_id', $item->base_id)->where('project_id', $item->project_id);
            // При реверсе отключить $item->id при расчете first()/last()
            if ($reverse == true) {
                $items = $items->where('id', '!=', $item->id);
            }

            // Цикл по записям, в каждой итерации цикла свой to_child_base_id в переменной $to_key
            foreach ($sets_group as $to_key => $to_value) {
                $item_seek = GlobalController::get_parent_item_from_main($item->id, $to_value->link_from_id);
                if ($item_seek) {
                    $items = $items->whereHas('child_mains', function ($query) use ($to_value, $item_seek) {
                        $query->where('link_id', $to_value->link_from_id)->where('parent_item_id', $item_seek->id);
                    });
                }
            }
            $item_calc = null;
            if ($calc == "first") {
                //$item_calc = $items->first();
                $item_calc = self::output_calculated_table_firstlast($item->base, $set, $item->project, $items);
            } elseif ($calc == "last") {
                //$item_calc = $items->get()->last();
                $item_calc = self::output_calculated_table_firstlast($item->base, $set, $item->project, $items);
            }
            if ($item_calc) {
                $result_item = GlobalController::get_parent_item_from_main($item_calc->id, $set->link_from_id);
            }

        }
        return $result_item;
    }

// Функции get_sets_calcsort_dop() и get_sets_calcsort_firstlast() похожи
    static function get_sets_calcsort_firstlast(Base $base, Set $set)
    {
        //$set = Set::find($link->parent_output_calculated_table_set_id);

        // Не нужно 'where('sets.is_savesets_enabled', '=', false)'
        // Сортировка такая одинаковая:
        // ItemController::get_item_from_parent_output_calculated_table()
        // и SetController::index(),
        // влияет на обработку сортировки
        $sets_calcsort = Set::select(DB::Raw('sets.*'))
            ->join('links as lf', 'sets.link_from_id', '=', 'lf.id')
            ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
            ->where('is_calcsort', true)
            ->where('lf.child_base_id', '=', $base->id)
            ->where('serial_number', '=', $set->serial_number)
            ->orderBy('sets.serial_number')
            ->orderBy('sets.line_number')
            ->orderBy('lf.child_base_id')
            ->orderBy('lt.child_base_id')
            ->orderBy('lf.parent_base_number')
            ->orderBy('lt.parent_base_number')
            ->get();

        return $sets_calcsort;
    }

// Функции output_calculated_table_dop() и output_calculated_table_firstlast() похожи
    static function output_calculated_table_firstlast(Base $base, Set $set, Project $project, $items)
    {
        $result_item = null;
        $sets_calcsort = self::get_sets_calcsort_firstlast($base, $set);
        // Обработка сортировки
        // Эти проверки нужны
        // 'link_from_id' не используется при обработке сортировки
        // 'link_to_id' используется при обработке сортировки
        if (($set->is_upd_cl_gr_first == true || $set->is_upd_cl_gr_last == true)
            && ($sets_calcsort) && ($items->count() > 0)) {
            $name = "";  // нужно, не удалять
            $index = array_search(App::getLocale(), config('app.locales'));
            if ($index !== false) {   // '!==' использовать, '!=' не использовать
                $name = 'name_lang_' . $index;
            }
            $collection = collect();
            $items_calcsort = $items->orderBy($name)->get();
            $str = "";
            foreach ($items_calcsort as $item) {
                $str = "";
                foreach ($sets_calcsort as $set_value) {
                    // '$set_value->link_from_id' используется
                    $item_find = GlobalController::view_info($item->id, $set_value->link_from_id);
                    if ($item_find) {
                        // Формирование вычисляемой строки для сортировки
                        // Для строковых данных для сортировки берутся первые 50 символов
                        if ($item_find->base->type_is_list() || $item_find->base->type_is_string()) {
                            $str = $str . str_pad(trim($item_find[$name]), 50);
                        } else {
                            $str = $str . trim($item_find[$name]);
                        }

                    }
                }
                // В $collection сохраняется в key - $item->id
                $collection[$item->id] = $str;
            }
            //            Сортировка коллекции по значению
            $collection = $collection->sort();
            $ids = $collection->keys()->toArray();
            $items = Item::whereIn('id', $ids)
                ->orderBy(\DB::raw("FIELD(id, " . implode(',', $ids) . ")"));
        }

        $item_calc = null;
        // '$is_func = false;' нужно
        $is_func = false;
        $count = 0;
        $sum = 0;
        // Первый(), Последний()
        if ($set->is_upd_cl_gr_first == true || $set->is_upd_cl_gr_last == true) {
            // '$is_func = true;' используется
            $is_func = true;
            if ($set->is_upd_cl_gr_first == true) {
                $item_calc = $items->first();
            } elseif ($set->is_upd_cl_gr_last == true) {
                // Нужно '->get()'
                $item_calc = $items->get()->last();
            }
        }
        if ($item_calc) {
            // Если данные ($item_calc) уже найдены и посчитаны
            if ($is_func) {
                $result_item = $item_calc;
            } else {
                // '$set->link_from_id' используется
                $result_item = GlobalController::get_parent_item_from_main($item_calc->id, $set->link_from_id);
            }
        }
        return $result_item;
    }

// Удаление в вычисляемых основах записей с пустыми значениями (пустыми $mains)
    static function sets_null_delete($base_id, $project_id)
    {
        // Если вычисляемое наименование
        // и вычисляемая основа
        $base_main = Link::select('links.child_base_id')
            ->join('bases', 'bases.id', '=', 'links.child_base_id')
            ->where('links.parent_base_id', '=', $base_id)
            ->where('bases.is_calcname_lst', '=', true)
            ->where('bases.is_calculated_lst', '=', true)
            ->groupBy('links.child_base_id')
            ->get();

        foreach ($base_main as $link) {
            $items = Item::where('base_id', $link->child_base_id)->where('project_id', $project_id)->get();
            foreach ($items as $item) {
                if (!$item->child_mains()->exists()) {
                    if (!$item->parent_mains()->exists()) {
                        $item->delete();
                    }
                }
            }
        }
    }

    // Функции get_sets_group() и get_sets_list_group() похожи
    // "->where('bs.type_is_list', '=', true)" нужно, т.к. запрос функции идет с ext_edit.php
    static function get_sets_group(Base $base, Link $link, $type_no_is_list_enable = false)
    {
        $result = null;
        // "->where('bs.type_is_list', '=', true)" нужно, т.к. запрос функции идет с ext_edit.php
        // Эта проверка убрано с запроса,
        // нужно самостоятельно проверять тип поля в зависимости от
        // "поле используется для фильтрации в ext_edit.php" да или нет
        // a) Если поле просто выводится, то оно может любого типа (type_is_list(), type_is_number(), type_is_string(), type_is_date(), type_is_boolean())
        // b) Если поле используется для фильтрации в ext_edit.php, то оно может только типа type_is_list(),
        // т.к. при выборе значений из списка item->id элементов списка известны и передаются дальше в форме для фильтрации
        // а если вводятся числа/даты/строки/логические поля, их item->id определится только после сохранения всей формы,
        // и не может быть передан дальше в форме для фильтрации по причине неизвестности значения item->id
        //->where('sets.is_savesets_enabled', '=', true)
        $set = Set::find($link->parent_output_calculated_table_set_id);
        if ($set) {
//            $result = Set::select(DB::Raw('sets.*'))
//                ->join('links as lf', 'sets.link_from_id', '=', 'lf.id')
//                ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
//                ->join('bases as bs', 'lf.parent_base_id', '=', 'bs.id')
//                ->where('lf.child_base_id', '=', $base->id)
//                ->where('is_group', true)
//                ->where('bs.type_is_list', '=', true)
//                ->where('sets.serial_number', '=', $set->serial_number)
//                ->orderBy('sets.serial_number')
//                ->orderBy('sets.link_from_id')
//                ->orderBy('sets.link_to_id')->get();

            $result = Set::select(DB::Raw('sets.*'))
                ->join('links as lf', 'sets.link_from_id', '=', 'lf.id')
                ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
                ->join('bases as bs', 'lf.parent_base_id', '=', 'bs.id')
                ->where('lf.child_base_id', '=', $base->id)
                ->where('is_group', true)
                ->where('sets.serial_number', '=', $set->serial_number)
                ->orderBy('sets.serial_number')
                ->orderBy('sets.link_from_id')
                ->orderBy('sets.link_to_id')->get();
        }

        return $result;
    }

    // Функция "Если в присваиваниях группировки "только type_is_list()"
    static function get_sets_list_group(Base $base, Link $link)
    {
        $result = false;
        $set = Set::find($link->parent_output_calculated_table_set_id);
        if ($set) {
            $sets = Set::select(DB::Raw('sets.*'))
                ->join('links as lf', 'sets.link_from_id', '=', 'lf.id')
                ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
                ->join('bases as bs', 'lf.parent_base_id', '=', 'bs.id')
                ->where('lf.child_base_id', '=', $base->id)
                ->where('is_group', true)
                ->where('sets.serial_number', '=', $set->serial_number)
                ->where('bs.type_is_list', '=', false)
                ->get();

            $result = (count($sets) == 0);

        }

        return $result;
    }

// Функции get_sets_calcsort_dop() и get_sets_calcsort_firstlast() похожи
    static function get_sets_calcsort_dop(Base $base, Link $link)
    {
        $set = Set::find($link->parent_output_calculated_table_set_id);

        // Не нужно 'where('sets.is_savesets_enabled', '=', false)'
        // Сортировка такая одинаковая:
        // ItemController::get_item_from_parent_output_calculated_table()
        // и SetController::index(),
        // влияет на обработку сортировки
        $sets_calcsort = Set::select(DB::Raw('sets.*'))
            ->join('links as lf', 'sets.link_from_id', '=', 'lf.id')
            ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
            ->where('is_calcsort', true)
            ->where('lf.child_base_id', '=', $base->id)
            ->where('serial_number', '=', $set->serial_number)
            ->orderBy('sets.serial_number')
            ->orderBy('sets.line_number')
            ->orderBy('lf.child_base_id')
            ->orderBy('lt.child_base_id')
            ->orderBy('lf.parent_base_number')
            ->orderBy('lt.parent_base_number')
            ->get();

        return $sets_calcsort;
    }

// Функции get_item_from_parent_output_calculated_table() и get_parent_item_from_output_calculated_table() похожи,
// выполняют одинаковую функцию: Выводят/считают поле из таблицы
// Первая вызывается из из MainController.php - view_info(), вторая из ext_edit.php,
// Первая возвращает $item, вторая $item->name()

// Вызывается из MainController.php - view_info()
// Выводит поле вычисляемой таблицы
    static function get_item_from_parent_output_calculated_table(Item $item_main, Link $link)
    {
        $result_item = null;
        $set = Set::find($link->parent_output_calculated_table_set_id);
        $r_info = true;
        if ($set) {
            // base_id вычисляемой таблицы
            $calc_table_base_id = $set->link_to->child_base_id;
            $items = Item::where('base_id', $calc_table_base_id)->where('project_id', $item_main->project_id);
            $sets_group = self::get_sets_group($item_main->base, $link);

            if ($sets_group) {
                // Фильтрация/поиск
                // Цикл по записям, в каждой итерации цикла свой to_child_base_id в переменной $to_key
                foreach ($sets_group as $to_key => $to_value) {
                    $item_seek = GlobalController::get_parent_item_from_main($item_main->id, $to_value->link_from_id);
                    if ($item_seek) {
                        $items = $items->whereHas('child_mains', function ($query) use ($to_value, $item_seek) {
                            $query->where('link_id', $to_value->link_to_id)->where('parent_item_id', $item_seek->id);
                        });
                        // все присваивания по группе должны быть учтены, их может быть один и выше
                    } else {
                        $r_info = false;
                        break;
                    }
                }
            }
            if ($r_info) {
                $result_item = self::output_calculated_table_dop($item_main->base, $link, $set, $item_main->project, $items);
            }
        }

        return $result_item;
    }

// Вызывается из ext_edit.php
    static function get_parent_item_from_output_calculated_table(Request $request)
    {
        $params = $request->query();
        // '=0' использовать, в ext_edit.php проверка на равенство нулю
        $result_id = 0;
        $result_inner = trans('main.no_information') . '!';
//      $project = null;
//      if (array_key_exists('project_id', $params)) {
//          $project = Project::find($params['project_id']);
//      }
        $base = null;
        if (array_key_exists('base_id', $params)) {
            $base = Base::find($params['base_id']);
        }
        $link = null;
        if (array_key_exists('link_id', $params)) {
            $link = Link::find($params['link_id']);
        }
        $items_id_group = null;
        if (array_key_exists('items_id_group', $params)) {
            if (is_array($params['items_id_group'])) {
                $items_id_group = $params['items_id_group'];
            }
        }
        //  '&& $items_id_group' не нужно, т.к. группировки может не быть
        if ($base && $link) {
            //$result_item = null;
            // true использовать
            $result_item = true;
            $set = Set::find($link->parent_output_calculated_table_set_id);
            $sets_group = self::get_sets_group($base, $link);
            if ($sets_group) {
                // base_id вычисляемой таблицы
                $calc_table_base_id = $set->link_to->child_base_id;
                $item_seek0 = null;
                if (isset($items_id_group[0])) {
                    $item_seek0 = Item::find($items_id_group[0]);
                }
                if ($item_seek0) {
                    $items = Item::where('base_id', $calc_table_base_id)->where('project_id', $item_seek0->project_id);
//                  $items = Item::where('base_id', $calc_table_base_id)->where('project_id', $project->id);

                    $i = 0;
                    // Фильтрация/поиск
                    // Цикл по записям, в каждой итерации цикла свой to_child_base_id в переменной $to_key
                    foreach ($sets_group as $to_key => $to_value) {
                        $item_seek = null;
                        if (isset($items_id_group[$i])) {
                            $item_seek = Item::find($items_id_group[$i]);
                        }
                        // все присваивания по группе должны быть учтены, их может быть один и выше
                        if (!$item_seek) {
                            // Нужно, разницы нет null или false присвоить
                            //$result_item = null;
                            $result_item = false;
                            break;
                        }
                        $items = $items->whereHas('child_mains', function ($query) use ($to_value, $item_seek) {
                            $query->where('link_id', $to_value->link_to_id)->where('parent_item_id', $item_seek->id);
                        });
                        $i = $i + 1;
                    }
                    //if (!$result_item) {
                    if ($result_item) {
                        $result_item = self::output_calculated_table_dop($base, $link, $set, $item_seek0->project, $items);
//                  $result_item = self::output_calculated_table_dop($base, $link, $set, $project, $items);
                    }
                }
                // Похожие строки в self::get_parent_item_from_calc_child_item()
                // и в self::get_parent_item_from_output_calculated_table()
                if ($result_item) {
                    $result_id = $result_item->id;
                    //$result_inner = $result_item->name(false, true, true);
                    if ($result_item->base->type_is_image() || $result_item->base->type_is_document()) {
                        if ($result_item->base->type_is_image()) {
                            //$result_item_name = "<img src='" . Storage::url($result_item->filename()) . "' height='250' alt='' title='" . $result_item->title_img() . "'>";
                            $result_inner = GlobalController::view_img($result_item, "medium", false, false, false, $result_item->title_img());
                        } else {
                            $result_inner = GlobalController::view_doc($result_item, GlobalController::usercode_calc());
                        }
                    } elseif ($result_item->base->type_is_text()) {
                        $result_inner = GlobalController::it_txnm_n2b($result_item);
                    } else {
                        $result_inner = $result_item->name(false, true, true);
                    }
                }
            }
        }
        return ['id' => $result_id, 'inner' => $result_inner];
    }

// Функции output_calculated_table_dop() и output_calculated_table_firstlast() похожи
// Вызывается из get_item_from_parent_output_calculated_table() и get_parent_item_from_output_calculated_table()
    static function output_calculated_table_dop(Base $base, Link $link, Set $set, Project $project, $items)
    {
        $result_item = null;
        $sets_calcsort = self::get_sets_calcsort_dop($base, $link);

        // Обработка сортировки
        // Эти проверки нужны
        // 'link_from_id' не используется при обработке сортировки
        // 'link_to_id' используется при обработке сортировки
        if (($set->is_upd_cl_gr_first == true || $set->is_upd_cl_gr_last == true)
            && ($sets_calcsort) && ($items->count() > 0)) {
            $name = "";  // нужно, не удалять
            $index = array_search(App::getLocale(), config('app.locales'));
            if ($index !== false) {   // '!==' использовать, '!=' не использовать
                $name = 'name_lang_' . $index;
            }
            $collection = collect();
            $items_calcsort = $items->orderBy($name)->get();
            $str = "";
            foreach ($items_calcsort as $item) {
                $str = "";
                foreach ($sets_calcsort as $set_value) {
                    $item_find = GlobalController::view_info($item->id, $set_value->link_to_id);
                    if ($item_find) {
                        // Формирование вычисляемой строки для сортировки
                        // Для строковых данных для сортировки берутся первые 50 символов
                        if ($item_find->base->type_is_list() || $item_find->base->type_is_string()) {
                            $str = $str . str_pad(trim($item_find[$name]), 50);
                        } else {
                            $str = $str . trim($item_find[$name]);
                        }

                    }
                }
                // В $collection сохраняется в key - $item->id
                $collection[$item->id] = $str;
            }

            //            Сортировка коллекции по значению
            $collection = $collection->sort();
            $ids = $collection->keys()->toArray();

            $items = Item::whereIn('id', $ids)
                ->orderBy(\DB::raw("FIELD(id, " . implode(',', $ids) . ")"));
        }

        $item_calc = null;
        // '$is_func = false;' нужно
        $is_func = false;
        $count = 0;
        $sum = 0;
        // Первый(), Последний()
        if ($set->is_upd_cl_gr_first == true || $set->is_upd_cl_gr_last == true) {
            if ($set->is_upd_cl_gr_first == true) {
                $item_calc = $items->first();
            } elseif ($set->is_upd_cl_gr_last == true) {
                // Нужно '->get()'
                $item_calc = $items->get()->last();
            }

            // Расчет Средний(), Количество(), Сумма()
        } elseif ($set->is_upd_cl_fn_count == true || $set->is_upd_cl_fn_avg == true || $set->is_upd_cl_fn_sum == true) {
            $is_func = true;
            $items_list = $items->get();
            // "$seek_value = 0" нужно
            $seek_value = 0;

            // Расчет Количество()
            if ($set->is_upd_cl_fn_count == true) {
                foreach ($items_list as $item) {
                    $str = "";
                    // Находим в исходной таблице объект, по которому считается Количество()
                    $item_find = GlobalController::view_info($item->id, $set->link_to_id);
                    if ($item_find) {
                        $seek_value = $seek_value + 1;
                    }
                }
                $seek_item = $seek_value > 0;

                // Расчет Средний(), Сумма()
            } elseif ($set->is_upd_cl_fn_avg == true || $set->is_upd_cl_fn_sum == true) {
                $count = 0;
                $sum = 0;
                foreach ($items_list as $item) {
                    $str = "";
                    // Находим в исходной таблице объект, по которуму считается Средний(), Сумма()
                    $item_find = GlobalController::view_info($item->id, $set->link_to_id);
                    if ($item_find) {
                        $count = $count + 1;
                        $sum = $sum + $item_find->numval()['value'];
                    }
                }
                // Расчет Средний()
                if ($set->is_upd_cl_fn_avg == true) {
                    // Если деление на ноль
                    if ($count == 0) {
                        $seek_value = 0;
                    } else {
                        $seek_value = $sum / $count;
                    }
                    // Расчет Сумма()
                } elseif ($set->is_upd_cl_fn_sum == true) {
                    $seek_value = $sum;
                }
                $seek_item = $count > 0;
            }

            // Если есть данные для расчета
            if ($seek_item) {
                $item_calc = self::find_save_number($set->link_from->parent_base_id, $project->id, $seek_value);
            }

        } else {
            $count = $items->count();
            if ($count == 1) {
                $item_calc = $items->first();
            }
        }
        if ($item_calc) {
            // Если данные ($item_calc) уже найдены и посчитаны
            if ($is_func) {
                $result_item = $item_calc;
            } else {
                $result_item = GlobalController::get_parent_item_from_main($item_calc->id, $set->link_to_id);
            }
        }
        return $result_item;
    }

// Сохранение $main, $index - номер $link,
// Присваивание $valits[] значениями $item->id, изначально там значения и $item->id в зависимости от типа данных(Число, Строка, Список, Изображение, Документ и т.д.)
// Похожие строки есть в ItemController::save_main() и в ItemController::verify_item_unique()
    function save_main(Main $main, $item, $keys, $values, &$valits, $index, $strings_inputs, &$message)
    {
        $main->link_id = $keys[$index];
        $main->child_item_id = $item->id;

        // поиск должен быть удачным, иначе "$main->link_id = $keys[$index]" может дать ошибку
        $link = Link::findOrFail($keys[$index]);
        // Находим $relip_project
        $relip_project = GlobalController::calc_link_project($link, $item->project);
        // Проверка и вывод сообщения нужны
        // Похожие проверка и вывод сообщения GlobalController::calc_relip_project() и ItemController::save_main()
        if (!$relip_project) {
            $message = "'" . trans('main.check_project_properties_projects_parents_are_not_set') . '!' . "'";
            return;
        }
        // $relip_project->user_id нужен чтобы в проектах типа "Основные основы Abakusonline"
        // автоматически созданные основы (с простыми типами Дата, Логический, Число, Строка, Текст, Изображение, Документ)
        // сохранялись с пользователем - автором проекта, куда идет добавление
        $relip_project_id = $relip_project->id;
        $relip_project_user_id = $relip_project->user_id;

        // тип корректировки поля - список
        if ($link->parent_base->type_is_list()) {
            if ($values[$index] == 0) {
                // Нужно
                // Если запись main существует - то удалить ее
                if (isset($main->id)) {
                    $main->delete();
                }
                // Нужно
                return;
            }
            $main->parent_item_id = $values[$index];

        } // тип корректировки поля - изображение или документ
        elseif ($link->parent_base->type_is_image() || $link->parent_base->type_is_document()) {
            $item_find = Item::find($main->parent_item_id);
            if (!$item_find) {
                // создание новой записи в items
                $item_find = new Item();
            }
            $item_find->base_id = $link->parent_base_id;
            // Похожая строка вверху и внизу
            $item_find->code = uniqid($item_find->base_id . '_', true);
            //присваивание полям наименование строкового значение числа
//            $i = 0;
//            foreach (config('app.locales') as $lang_key => $lang_value) {
//                if ($i == 0) {
//                    $item_find['name_lang_' . $lang_key] = $values[$index];
//                } else {
//                    if ($link->parent_base->is_one_value_lst_str_txt == true) {
//                        // Одно значение для наименований у всех языков
//                        $item_find['name_lang_' . $lang_key] = $values[$index];
//                    } else {
//                        $item_find['name_lang_' . $lang_key] = $strings_inputs[$link->id . '_' . $lang_key];
//                    }
//                }
//                $i = $i + 1;
//            }
            $item_find->name_lang_0 = $values[$index];
            $item_find->name_lang_1 = "";
            if ($item_find->base->type_is_image() == true) {
                if ($item_find->base->is_to_moderate_image == true) {
                    // На модерации
                    $item_find->name_lang_1 = "3";
                    // Похожие строки ниже
                    if (env('MAIL_ENABLED') == 'yes') {
                        $appname = config('app.name', 'Abakus');
                        try {
                            Mail::send(['html' => 'mail/login_site'], ['remote_addr' => $_SERVER['REMOTE_ADDR'],
                                'http_user_agent' => $_SERVER['HTTP_USER_AGENT'], 'appname' => $appname],
                                function ($message) use ($appname) {
                                    $message->to(env('MAIL_TO_ADDRESS_MODERATION', 'moderation@rsb0807.kz'), '')->subject("Модерация '" . $appname . "'");
                                    $message->from(env('MAIL_FROM_ADDRESS', 'support@rsb0807.kz'), $appname);
                                });
                        } catch (Exception $exc) {
                            return trans('error_sending_email') . ": " . $exc->getMessage();
                        }
                    }
                } else {
                    // Без модерации
                    $item_find->name_lang_1 = "0";
                }
            }
            $item_find->name_lang_2 = "";
            $item_find->name_lang_3 = "";

            $item_find->project_id = $relip_project_id;
            // при создании записи "$item->created_user_id" заполняется
//          $item_find->created_user_id = Auth::user()->id;
//          $item_find->updated_user_id = Auth::user()->id;
            $item_find->created_user_id = $relip_project_user_id;
            $item_find->updated_user_id = $relip_project_user_id;
            $item_find->save();
            $main->parent_item_id = $item_find->id;
            // заменяем значение в массиве ссылкой на $item вместо значения
            $valits[$index] = $item_find->id;

        } // тип корректировки поля - строка
        elseif ($link->parent_base->type_is_string()) {
            if ($link->parent_base->is_required_lst_num_str_txt_img_doc == false) {
                $main_delete = $values[$index] == "";
                if ($link->parent_base->is_one_value_lst_str_txt == false) {
                    $i = 0;
                    foreach (config('app.locales') as $lang_key => $lang_value) {
                        // начиная со второго(индекс==1) элемента массива языков учитывать
                        if ($i > 0) {
                            $main_delete = $main_delete && ($strings_inputs[$link->id . '_' . $lang_key] == "");
                        }
                        $i = $i + 1;
                    }
                }
                if ($main_delete) {
                    // Нужно
                    // Если запись main существует - то удалить ее
                    if (isset($main->id)) {
                        $main->delete();
                    }
                    // Нужно
                    return;
                }
            }
            // Похожие строки в ItemController::save_main() и в UserController::save_to_project_users()
            // Поиск в таблице items значение с таким же названием и base_id
            // По одному ($link->parent_base->is_one_value_lst_str_txt == true)
            // Или всем языкам ($link->parent_base->is_one_value_lst_str_txt == false)
            $item_find = Item::where('base_id', $link->parent_base_id)->where('project_id', $relip_project_id)->where('name_lang_0', $values[$index]);
            if ($link->parent_base->is_one_value_lst_str_txt == false) {
                $i = 0;
                foreach (config('app.locales') as $lang_key => $lang_value) {
                    // начиная со второго(индекс==1) элемента массива языков учитывать
                    if ($i > 0) {
                        $item_find = $item_find->where('name_lang_' . $lang_key, $strings_inputs[$link->id . '_' . $lang_key]);
                    }
                    $i = $i + 1;
                }
            }

            $item_find = $item_find->first();

            // если не найдено
            if (!$item_find) {
                // создание новой записи в items
                $item_find = new Item();
                $item_find->base_id = $link->parent_base_id;
                // Похожая строка вверху и внизу
                $item_find->code = uniqid($item_find->base_id . '_', true);
                // присваивание полям наименование строкового значение числа
                $i = 0;
                foreach (config('app.locales') as $lang_key => $lang_value) {
                    if ($i == 0) {
                        $item_find['name_lang_' . $lang_key] = $values[$index];
                    } else {
                        if ($link->parent_base->is_one_value_lst_str_txt == true) {
                            // Одно значение для наименований у всех языков
                            $item_find['name_lang_' . $lang_key] = $values[$index];
                        } else {
                            $item_find['name_lang_' . $lang_key] = $strings_inputs[$link->id . '_' . $lang_key];
                        }
                    }
                    $i = $i + 1;
                }
                $item_find->project_id = $relip_project_id;
                // при создании записи "$item->created_user_id" заполняется
//              $item_find->created_user_id = Auth::user()->id;
//              $item_find->updated_user_id = Auth::user()->id;
                $item_find->created_user_id = $relip_project_user_id;
                $item_find->updated_user_id = $relip_project_user_id;

                $item_find->save();
            }
            $main->parent_item_id = $item_find->id;
            // заменяем значение в массиве ссылкой на $item вместо значения
            $valits[$index] = $item_find->id;
        } // тип корректировки поля - текст
        // Полные значения полей text хранятся в таблице texts,
        // краткие (ограниченные 255 - размером полей хранятся в $item->name_lang_0 - $item->name_lang_3)
        // связь между таблицами items и text - "один-к-одному", по полю $item->id = $text->item->id
        elseif ($link->parent_base->type_is_text()) {
            if ($link->parent_base->is_required_lst_num_str_txt_img_doc == false) {
                $main_delete = $values[$index] == "";
                if ($link->parent_base->is_one_value_lst_str_txt == false) {
                    $i = 0;
                    foreach (config('app.locales') as $lang_key => $lang_value) {
                        // начиная со второго(индекс==1) элемента массива языков учитывать
                        if ($i > 0) {
                            $main_delete = $main_delete && ($strings_inputs[$link->id . '_' . $lang_key] == "");
                        }
                        $i = $i + 1;
                    }
                }
                if ($main_delete) {
                    // Нужно
                    // Если запись main существует - то удалить ее
                    if (isset($main->id)) {
                        $main->delete();
                    }
                    // Нужно
                    return;
                }
            }
            $item_find = Item::find($main->parent_item_id);
            if (!$item_find) {
                // создание новой записи в items
                $item_find = new Item();
                // при создании записи "$item->created_user_id" заполняется
//              $item_find->created_user_id = Auth::user()->id;
                $item_find->created_user_id = $relip_project_user_id;
            }
            $item_find->base_id = $link->parent_base_id;
            // Похожая строка вверху и внизу
            $item_find->code = uniqid($item_find->base_id . '_', true);
            $item_find->project_id = $relip_project_id;
//          $item_find->updated_user_id = Auth::user()->id;
            $item_find->updated_user_id = $relip_project_user_id;

            // Нужно чтобы знать $item_find->id в команде "$text->item_id = $item_find->id;"
            $item_find->save();

            //$text = $item->text();
            $text = Text::where('item_id', $item_find->id)->first();
            if (!$text) {
                $text = new Text();
                $text->item_id = $item_find->id;
            }

            // присваивание полям наименование строкового значение числа
            $i = 0;
            foreach (config('app.locales') as $lang_key => $lang_value) {
                if ($i == 0) {
                    $item_find['name_lang_' . $lang_key] = GlobalController::itnm_left($values[$index]);
                    $text['name_lang_' . $lang_key] = $values[$index];
                } else {
                    if ($link->parent_base->is_one_value_lst_str_txt == true) {
                        // Одно значение для наименований у всех языков
                        $item_find['name_lang_' . $lang_key] = GlobalController::itnm_left($values[$index]);
                        $text['name_lang_' . $lang_key] = $values[$index];
                    } else {
                        $item_find['name_lang_' . $lang_key] = GlobalController::itnm_left($strings_inputs[$link->id . '_' . $lang_key]);
                        $text['name_lang_' . $lang_key] = $strings_inputs[$link->id . '_' . $lang_key];
                    }
                }
                $i = $i + 1;
            }
            // Нужно чтобы сохранить name_lang_0 - name_lang_3
            $item_find->save();

            $text->save();
            $main->parent_item_id = $item_find->id;
            // заменяем значение в массиве ссылкой на $item вместо значения
            $valits[$index] = $item_find->id;

            // тип корректировки поля - не строка и не список
        } else {

            // Проверка числовых полей
            // Если равно нулю и "$link->parent_base->is_required_lst_num_str_txt_img_doc == false",
            // удаляет запись $main, если она есть
            // и в итоге: вместо нуля отображается null/empty
//            if (($link->parent_base->type_is_number()) && ($link->parent_base->is_required_lst_num_str_txt_img_doc == false)) {
//                if ($values[$index] == 0) {
//                    // Нужно
//                    // Если запись main существует - то удалить ее
//                    if (isset($main->id)) {
//                        $main->delete();
//                    }
//                    // Нужно
//                    return;
//                }
//            }

            // поиск в таблице items значение с таким же названием и base_id
            $item_find = Item::where('base_id', $link->parent_base_id)->where('project_id', $relip_project_id)->where('name_lang_0', $values[$index])->first();

            // если не найдено
            if (!$item_find) {
                // создание новой записи в items
                $item_find = new Item();
                $item_find->base_id = $link->parent_base_id;
                // Похожие строки вверху
                $item_find->code = uniqid($item_find->base_id . '_', true);
                // присваивание полям наименование строкового значение числа
                foreach (config('app.locales') as $key => $value) {
                    $item_find['name_lang_' . $key] = $values[$index];
                }
                $item_find->project_id = $relip_project_id;
                // при создании записи "$item->created_user_id" заполняется
//              $item_find->created_user_id = Auth::user()->id;
//              $item_find->updated_user_id = Auth::user()->id;
                $item_find->created_user_id = $relip_project_user_id;
                $item_find->updated_user_id = $relip_project_user_id;
                $item_find->save();
            }
            $main->parent_item_id = $item_find->id;
            // заменяем значение в массиве ссылкой на $item вместо значения
            $valits[$index] = $item_find->id;
        }
        $main->updated_user_id = Auth::user()->id;
        $main->save();
    }

    function save_img_doc(Request $request, Item &$item)
    {
        $base = $item->base;
        if ($base->type_is_image() || $base->type_is_document()) {
            $path = "";
            if ($request->hasFile('name_lang_0')) {
                if ($base->type_is_image()) {
                    $path = GlobalController::image_store($request, 'name_lang_0', $item->project_id, $base->id);
                } else {
                    $path = $item->name_lang_0->store('public/' . $item->project_id . '/' . $base->id);
                }
                $item->name_lang_0 = $path;
                if ($base->type_is_image()) {
                    if ($item->base->is_to_moderate_image == true) {
                        // На модерации
                        $item->name_lang_1 = "3";

                        // Похожие строки выше
                        if (env('MAIL_ENABLED') == 'yes') {
                            $appname = config('app.name', 'Abakus');
                            try {
                                Mail::send(['html' => 'mail/login_site'], ['remote_addr' => $_SERVER['REMOTE_ADDR'],
                                    'http_user_agent' => $_SERVER['HTTP_USER_AGENT'], 'appname' => $appname],
                                    function ($message) use ($appname) {
                                        $message->to(env('MAIL_TO_ADDRESS_MODERATION', 'moderation@rsb0807.kz'), '')->subject("Модерация '" . $appname . "'");
                                        $message->from(env('MAIL_FROM_ADDRESS', 'support@rsb0807.kz'), $appname);
                                    });
                            } catch (Exception $exc) {
                                return trans('error_sending_email') . ": " . $exc->getMessage();
                            }
                        }
                    } else {
                        // Без модерации
                        $item->name_lang_1 = "0";
                    }
                } else {
                    // В $item->name_lang_1 хранится наименование документа
                    //$item->name_lang_1 = "";
                }
                $item->name_lang_2 = "";
                $item->name_lang_3 = "";
            } else {
                // Проверка существует ли переменная '$request->name_lang_0_img_doc_delete'
//                if (isset($request->name_lang_0_img_doc_delete)) {
//                    // $delete = true, если отметка поставлена, = false без отметки'
//                    $delete = isset($request->name_lang_0_img_doc_delete) ? true : false;
//                    if ($delete == true) {
//                        if ($item->img_doc_exist()) {
//                            // Удаление изображения или документа
//                            Storage::delete($item->filename());
//                            $item->name_lang_0 = "";
//                            // Без модерации
//                            $item->name_lang_1 = "";
//                            $item->name_lang_2 = "";
//                            $item->name_lang_3 = "";
//                            $item->save();
//                        }
//                    }
//                }
            }
        }
    }

    function store(Request $request)
    {
        //$request->validate($this->rules($request));

        // установка часового пояса нужно для сохранения времени
        date_default_timezone_set('Asia/Almaty');

        $item = new Item($request->except('_token', '_method'));

        $item->base_id = $request->base_id;
        $item->name_lang_0 = $request->name_lang_0;

        $item->name_lang_1 = isset($request->name_lang_1) ? $request->name_lang_1 : "";
        $item->name_lang_2 = isset($request->name_lang_2) ? $request->name_lang_2 : "";
        $item->name_lang_3 = isset($request->name_lang_3) ? $request->name_lang_3 : "";

        $item->save();

//        return redirect()->route('item.base_index', $item->base->id);
        return redirect(session('links'));
    }

    function update(Request $request, Item $item)
    {
        // Если данные изменились - выполнить проверку
//        if (!(($item->base_id == $request->base_id) and ($item->name_lang_0 == $request->name_lang_0))) {
//            $request->validate($this->rules($request));
//        }

        $data = $request->except('_token', '_method');

        $item->fill($data);

        $item->base_id = $request->base_id;
        $item->name_lang_0 = $request->name_lang_0;
        $item->name_lang_1 = isset($request->name_lang_1) ? $request->name_lang_1 : "";
        $item->name_lang_2 = isset($request->name_lang_2) ? $request->name_lang_2 : "";
        $item->name_lang_3 = isset($request->name_lang_3) ? $request->name_lang_3 : "";

        $item->save();

//        return redirect()->route('item.base_index', $item->base->id);
        return redirect(session('links'));
    }

//    function ext_update(Request $request, Item $item, Project $project, Role $role, $usercode,
//                                $relit_id,
//                                $string_link_ids_current = '', $string_item_ids_current = '', $string_all_codes_current = '',
//                                $heading = 0, $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
//                                $parent_ret_id = null,
//                                $view_link = null,
//                        Link    $par_link = null, Item $parent_item = null)

    function ext_update(Request $request, Item $item, Project $project, Role $role, $usercode,
                                $relit_id,
                                $string_current = '',
                                $heading = 0, $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
                                $parent_ret_id = null,
                                $view_link = null,
                        Link    $par_link = null, Item $parent_item = null)
    {
        // установка часового пояса нужно для сохранения времени
        date_default_timezone_set('Asia/Almaty');

        if ($item->is_history() == true) {
            return view('message', ['message' => trans('main.no_access')]);
        }

        if (GlobalController::check_project_item_user($project, $item, $role, $usercode) == false) {
            return view('message', ['message' => trans('main.no_access')]);
        }

        //    if (GlobalController::check_project_user($project, $role) == false) {
        //        return view('message', ['message' => trans('main.info_user_changed')]);
        //    }

        $relip_project = GlobalController::calc_relip_project($relit_id, $project);

        // При корректировке
        // Если данные изменились - выполнить проверку. оператор '??' нужны
        $data_change = false;
//      if (!($item->name_lang_0 ?? '' == $request->name_lang_0 ?? '')) {
        if ($item->base->type_is_boolean()) {
            $data_change = $item->name_lang_0 != isset($request->name_lang_0) ? "1" : "0";
        } else {
            $data_change = $item->name_lang_0 != $request->name_lang_0;
            if ($item->base->type_is_string() & $item->base->is_one_value_lst_str_txt == false) {
                $i = 0;
                foreach (config('app.locales') as $lang_key => $lang_value) {
                    // начиная со второго(индекс==1) элемента массива языков учитывать
                    if ($i > 0) {
                        // Используется | - ИЛИ
                        $data_change = $data_change | $item['name_lang_' . $lang_key] != $request['name_lang_' . $lang_key];
                    }
                    $i = $i + 1;
                }
            }
        }
        if ($data_change) {
            $request->validate($this->name_lang_rules($request));
        }

        if (!($item->code == $request->code)) {
            $request->validate($this->code_rules($request, $relip_project->id, $item->base_id));
        }
        // Проверка полей с типом "текст" на длину текста
        if ($item->base->type_is_text() && $item->base->length_txt > 0) {
            $errors = false;
            foreach (config('app.locales') as $lang_key => $lang_value) {
                if (strlen($request['name_lang_' . $lang_key]) > $item->base->length_txt) {
                    $array_mess['name_lang_' . $lang_key] = trans('main.length_txt_rule') . ' ' . $item->base->length_txt . '!';
                    $errors = true;
                }
            }
            if ($errors) {
                // повторный вызов формы
                return redirect()->back()
                    ->withInput()
                    ->withErrors($array_mess);
            }
        }

        $base_right = GlobalController::base_right($item->base, $role, $relit_id);

        $is_limit_minutes = GlobalController::is_limit_minutes($base_right, $item);
        if ($is_limit_minutes['is_entry_minutes'] == false) {
            return view('message', ['message' => trans('main.no_access') . ' (' . trans('main.title_min') . ')']);
        }

        // Проверка на обязательность ввода
        //if ($item->base->is_required_lst_num_str_txt_img_doc == true && $item->base->is_calcname_lst == false) {
        //          'Обязательно к заполнению (для списков, при условии $base->is_required_lst_num_str_txt_img_doc = false
        if ($base_right['is_base_required'] == true && $item->base->is_calcname_lst == false) {
            // Тип - список, строка или текст
            if ($item->base->type_is_list() || $item->base->type_is_string() || $item->base->type_is_text()) {
                $name_lang_array = array();
                // значения null в ""
                $name_lang_array[0] = isset($request->name_lang_0) ? $request->name_lang_0 : "";
                $name_lang_array[1] = isset($request->name_lang_1) ? $request->name_lang_1 : "";
                $name_lang_array[2] = isset($request->name_lang_2) ? $request->name_lang_2 : "";
                $name_lang_array[3] = isset($request->name_lang_3) ? $request->name_lang_3 : "";
                $errors = false;
                $i = 0;
                foreach (config('app.locales') as $lang_key => $lang_value) {
                    if (($item->base->is_one_value_lst_str_txt == true && $lang_key == 0) || ($item->base->is_one_value_lst_str_txt == false)) {
                        if ($name_lang_array[$i] === '') {
                            $array_mess['name_lang_' . $i] = trans('main.is_required_lst_num_str_txt_img_doc') . '!';
                            $errors = true;
                        }
                        $i = $i + 1;
                    }
                }
                if ($errors) {
                    // повторный вызов формы
                    return redirect()->back()
                        ->withInput()
                        ->withErrors($array_mess);
                }
                // Тип - число
            } elseif ($item->base->type_is_number()) {
                // значения null в "0"
                $name_lang_0_val = isset($request->name_lang_0) ? $request->name_lang_0 : "0";
                $errors = false;
                // "$value === '0'" использовать для точного сравнения (например, при $link->parent_base->type_is_string())
                if ($name_lang_0_val === '0') {
                    $array_mess['name_lang_0'] = trans('main.is_required_lst_num_str_txt_img_doc') . '!';
                    $errors = true;
                } else {
                    $floatvalue = floatval($name_lang_0_val);
                    if ($floatvalue == 0) {
                        $array_mess['name_lang_0'] = trans('main.is_required_lst_num_str_txt_img_doc') . '!';
                        $errors = true;
                    }
                }
                if ($errors) {
                    // повторный вызов формы
                    return redirect()->back()
                        ->withInput()
                        ->withErrors($array_mess);
                }
                // Тип - изображение
            } elseif
            ($item->base->type_is_image()) {
                $errors = false;
                if (!$item->img_doc_exist()) {
                    if (!$request->hasFile('name_lang_0')) {
                        $array_mess['name_lang_0'] = trans('main.is_required_lst_num_str_txt_img_doc') . '!';
                        $errors = true;
                    }
                }
                if ($errors) {
                    // повторный вызов формы
                    return redirect()->back()
                        ->withInput()
                        ->withErrors($array_mess);
                }
                // Тип - документ
            } elseif
            ($item->base->type_is_document()) {
                $errors = false;
                if (!$item->img_doc_exist()) {
                    if (!$request->hasFile('name_lang_0')) {
                        $array_mess['name_lang_0'] = trans('main.is_required_lst_num_str_txt_img_doc') . '!';
                        $errors = true;
                    }
                }
                if ($errors) {
                    // повторный вызов формы
                    return redirect()->back()
                        ->withInput()
                        ->withErrors($array_mess);
                }
            }
        }

        // Проверка полей с типом "текст" на наличие запрещенных тегов HTML
        // Правило: только в текстовых полях можно применять разрешенную HTML-теги
        if ($item->base->type_is_text()) {
            $errors = false;
            foreach (config('app.locales') as $lang_key => $lang_value) {
                $text_html_check = GlobalController::text_html_check($request['name_lang_' . $lang_key]);
                if ($text_html_check['result'] == true) {
                    $array_mess['name_lang_' . $lang_key] = $text_html_check['message'] . '!';
                    $errors = true;
                }
            }
            if ($errors) {
                // повторный вызов формы
                return redirect()->back()
                    ->withInput()
                    ->withErrors($array_mess);
            }
        }

        if ($item->base->type_is_image() || $item->base->type_is_document()) {
            if ($request->hasFile('name_lang_0')) {
                $fs = $request->file('name_lang_0')->getSize();
                $mx = $item->base->maxfilesize_img_doc;
                if ($fs > $mx) {
                    $errors = false;
                    if ($request->file('name_lang_0')->isValid()) {
                        $array_mess['name_lang_0'] = self::filesize_message($fs, $mx);
                        $errors = true;
                    }
                    if ($errors) {
                        // повторный вызов формы
                        return redirect()->back()
                            ->withInput()
                            ->withErrors($array_mess);
                    }
                }
            }
        }
        // Только для режима корректировки
        if ($item->base->type_is_image() || $item->base->type_is_document()) {
            if ($request->hasFile('name_lang_0')) {
                Storage::delete($item->filename());
            }
        }

        $string_unzip_current_next = self::string_unzip_current_next($string_current);
        $string_link_ids_current = $string_unzip_current_next['string_link_ids'];
        $string_item_ids_current = $string_unzip_current_next['string_item_ids'];
        $string_relit_ids_current = $string_unzip_current_next['string_relit_ids'];
        $string_vwret_ids_current = $string_unzip_current_next['string_vwret_ids'];
        $string_all_codes_current = $string_unzip_current_next['string_all_codes'];

        $data = $request->except('_token', '_method');
        $item->fill($data);
        //$item->project_id = $request->project_id;
        $item->updated_user_id = Auth::user()->id;
        //$role = Role::findOrFail($request->role_id);

        // Похожая проверка в ext_edit.blade.php
//        if ($item->base->is_code_needed == true && $item->base->is_code_number == true && $item->base->is_limit_sign_code == true
//            && $item->base->is_code_zeros == true && $item->base->is_code_zeros > 0) {
//            // Дополнить код слева нулями
//            $item->code = str_pad($item->code, $item->base->significance_code, '0', STR_PAD_LEFT);
//        }

        //$item->base_id = $item->base_id;

        // нужно по порядку: сначала этот блок
        // значения null в ""
        // у строк могут быть пустые значения, поэтому нужно так: '$item->name_lang_0 = isset($request->name_lang_0) ? $request->name_lang_0 : ""'
        // Проверка "if (!($item->base->type_is_image() || $item->base->type_is_document()))" нужна
        if (!($item->base->type_is_image() || $item->base->type_is_document())) {
            $item->name_lang_0 = isset($request->name_lang_0) ? $request->name_lang_0 : "";
            $item->name_lang_1 = isset($request->name_lang_1) ? $request->name_lang_1 : "";
            $item->name_lang_2 = isset($request->name_lang_2) ? $request->name_lang_2 : "";
            $item->name_lang_3 = isset($request->name_lang_3) ? $request->name_lang_3 : "";
        }

        // далее этот блок
        // похожие формула выше (в этой же процедуре)

        // тип - логический
        if ($item->base->type_is_boolean()) {
            $item->name_lang_0 = isset($request->name_lang_0) ? "1" : "0";

        } // тип - число
        elseif ($item->base->type_is_number()) {
            $item->name_lang_0 = GlobalController::save_number_to_item($item->base, $request->name_lang_0);

        } // тип - текст
        elseif ($item->base->type_is_text()) {
            $item->name_lang_0 = GlobalController::itnm_left($request->name_lang_0);
            $item->name_lang_1 = GlobalController::itnm_left($request->name_lang_1);
            $item->name_lang_2 = GlobalController::itnm_left($request->name_lang_2);
            $item->name_lang_3 = GlobalController::itnm_left($request->name_lang_3);
        }

        // затем этот блок (используется "$item->base")
        if ($item->base->type_is_number() || $item->base->type_is_date() || $item->base->type_is_boolean()) {
            // присваивание полям наименование строкового значение числа/даты
//            foreach (config('app.locales') as $key => $value) {
//                if ($key > 0) {
//                    $item['name_lang_' . $key] = $item->name_lang_0;
//                }
//            }
            $item->name_lang_1 = $item->name_lang_0;
            $item->name_lang_2 = $item->name_lang_0;
            $item->name_lang_3 = $item->name_lang_0;
        }

        $this::save_img_doc($request, $item);

        $excepts = array('_token', 'code', '_method', 'name_lang_0', 'name_lang_1', 'name_lang_2', 'name_lang_3');
        $string_langs = $this->get_child_links($item->base);

        // Формируется массив $code_names - названия полей кодов
        // Формируется массив $string_names - названия полей наименование
        $code_names = array();
        $string_names = array();
        $i = 0;
        foreach ($string_langs as $key => $link) {
            if ($link->parent_base->type_is_string() || $link->parent_base->type_is_text()) {
                $i = 0;
                foreach (config('app.locales') as $lang_key => $lang_value) {
                    // начиная со второго(индекс==1) элемента массива языков сохранять
                    if ($i > 0) {
                        // для первого (нулевого) языка $input_name = $key ($link->id)
                        // для последующих языков $input_name = $key . '_' . $lang_key($link->id . '_' . $lang_key);
                        // это же правило используется в ext_edit.blade.php
                        //$string_names[] = $link->id . ($lang_key == 0) ? '' : '_' . $lang_key;  // так не работает, дает '' в результате
                        $string_names[] = ($lang_key == 0) ? $link->id : $link->id . '_' . $lang_key;  // такой вариант работает
                    }
                    $i = $i + 1;
                }
            }
            if ($link->parent_is_enter_refer == true) {
                $code_names[] = 'code' . $link->id;
            }
        }
        // при корректировке base (например, список основы Изображение) не используется - не нужно: можно выполнить удаление изображения
        // Только при корректировке записи используется массив $del_names()
        // Формируется массив $del_names - названия полей "Удалить изображение"/"Удалить документ"
        // массив $del_links - список links для удаления
        $del_names = array();
        $del_links = array();
        foreach ($string_langs as $key => $link) {
            if ($link->parent_base->type_is_image() || $link->parent_base->type_is_document()) {
                $i = 0;
                // Проверка:
                // 1) Поле 'link->id' существует в $request
                // 2) Поле 'link->id' будет существовать в $request, если на форме выбран файл (изображение или документ)
                $is_img_doc = isset($request[$link->id]);
                // Две проверки:
                // 1) на наличие вводимого поля
                // 2) в введенном поле поставлена отметка
                $is_del = isset($request[$link->id . '_img_doc_delete']);
                if ($is_del) {
                    $del_names[] = $link->id . '_img_doc_delete';
                    if (!$is_img_doc) {
                        $del_links[] = $link->id;
                    }
                }
            }
        }
        // Только при корректировке записи используется массив $del_names()
        // загрузить в $inputs все поля ввода, кроме $excepts, $string_names, $string_codes, $del_names, array_merge() - функция суммирования двух и более массивов
        $inputs = $request->except(array_merge($excepts, $string_names, $code_names, $del_names));

        $it_texts = null;
        if ($item->base->type_is_text()) {
            $only = array('name_lang_0', 'name_lang_1', 'name_lang_2', 'name_lang_3');
            $it_texts = $request->only($only);
            foreach ($it_texts as $it_key => $it_text) {
                $it_texts[$it_key] = isset($it_texts[$it_key]) ? $it_texts[$it_key] : "";
            }
        }
        // Проверка существования кода объекта
        foreach ($inputs as $key => $value) {
            $link = Link::findOrFail($key);
            if ($link->parent_base->is_code_needed == true && $link->parent_is_enter_refer == true) {
                if ($value != 0) {
                    $item_needed = Item::find($value);
                    if (!$item_needed) {
                        $array_mess['code' . $key] = trans('main.code_not_found') . "!";
                        // повторный вызов формы
                        return redirect()->back()
                            ->withInput()
                            ->withErrors($array_mess);
                    }
                }
            }
        }
        foreach ($inputs as $key => $value) {
            $link = Link::findOrFail($key);
            if ($link->parent_base->type_is_image() || $link->parent_base->type_is_document()) {
                if ($request->hasFile($link->id)) {
                    $fs = $request->file($link->id)->getSize();
                    $mx = $link->parent_base->maxfilesize_img_doc;
                    if ($fs > $mx) {
                        $errors = false;
                        if ($request->file($link->id)->isValid()) {
                            $array_mess[$link->id] = self::filesize_message($fs, $mx);
                            $errors = true;
                        }
                        if ($errors) {
                            // повторный вызов формы
                            return redirect()->back()
                                ->withInput()
                                ->withErrors($array_mess);
                        }
                    }
                }
            }
        }

        // обработка для логических полей
        // если при вводе формы пометка checkbox не установлена, в $request записи про элемент checkbox вообще нет
        // если при вводе формы пометка checkbox установлена, в $request есть запись со значеним "on"
        // см. https://webformyself.com/kak-v-php-poluchit-znachenie-checkbox/
        foreach ($inputs as $key => $value) {
            $link = Link::findOrFail($key);
            if ($link->parent_base->type_is_image() || $link->parent_base->type_is_document()) {
                $path = "";
                if ($request->hasFile($key)) {
                    if ($link->parent_base->type_is_image()) {
                        $path = GlobalController::image_store($request, $key, $item->project_id, $link->parent_base_id);
                    } else {
                        $path = $request[$key]->store('public/' . $item->project_id . '/' . $link->parent_base_id);
                    }
                }
                $inputs[$key] = $path;
            } elseif ($link->parent_base->type_is_number()) {
                $inputs[$key] = GlobalController::save_number_to_item($link->parent_base, $value);
            }
        }

//        foreach ($string_langs as $link) {
//            // Проверка нужна
//            $base_link_right = GlobalController::base_link_right($link, $role);
//            if ($base_link_right['is_edit_link_enable'] == false) {
//                continue;
//            }
//            // похожая формула выше (в этой же процедуре)
//            if ($link->parent_base->type_is_boolean()) {
//                // у этой команды два предназначения:
//                // 1) заменить "on" на "1" при отмеченном checkbox
//                // 2) создать новый ([$link->id]-й) элемент массива со значением "0" при выключенном checkbox
//                // в базе данных информация хранится как "0" или "1"
//                $inputs[$link->id] = isset($inputs[$link->id]) ? "1" : "0";
//            }
//        }

        foreach ($string_langs as $link) {
            if ($link->parent_base->type_is_boolean()) {
                // Проверка нужна
//                $base_link_right = GlobalController::base_link_right($link, $role);
//                if ($base_link_right['is_edit_link_update'] == false) {
//                    continue;
//                }
                // похожая формула выше (в этой же процедуре)
                // у этой команды два предназначения:
                // 1) заменить "on" на "1" при отмеченном checkbox
                // 2) создать новый ([$link->id]-й) элемент массива со значением "0" при выключенном checkbox
                // в базе данных информация хранится как "0" или "1"
                $inputs[$link->id] = isset($inputs[$link->id]) ? "1" : "0";
            }
        }

        $array_mess = array();

        foreach ($string_langs as $link) {
            if ($link->parent_is_parent_related == false) {
                // Тип - изображение
                if ($link->parent_base->type_is_image() || $link->parent_base->type_is_document()) {
                    // Проверка на обязательность ввода
                    if ($link->parent_base->is_required_lst_num_str_txt_img_doc == true) {
                        $item_seek = GlobalController::get_parent_item_from_main($item->id, $link->id);
                        $check = false;
                        if ($item_seek) {
                            if (!$item_seek->img_doc_exist()) {
                                $check = true;
                            }
                        } else {
                            $check = true;
                        }

                        $errors = false;
                        if ($check && !$request->hasFile($link->id)) {
                            $array_mess[$link->id] = trans('main.is_required_lst_num_str_txt_img_doc') . '!';
                            $errors = true;
                        }
                        if ($errors) {
                            // повторный вызов формы
                            return redirect()->back()
                                ->withInput()
                                ->withErrors($array_mess);
                        }
                    }
                }
            }
        }

        foreach ($inputs as $key => $value) {
            $inputs[$key] = ($value != null) ? $value : "";
        }
        $strings_inputs = $request->only($string_names);
        foreach ($strings_inputs as $key => $value) {
            $strings_inputs[$key] = ($value != null) ? $value : "";
        }

        $keys = array_keys($inputs);
        $values = array_values($inputs);

        // Проверка полей с типом "текст" на длину текста
        $errors = false;
        foreach ($inputs as $key => $value) {
            $link = Link::findOrFail($key);
            $work_base = $link->parent_base;
            if ($work_base->type_is_text() && $work_base->length_txt > 0) {
                $errors = false;
                $name_lang_value = null;
                $name_lang_key = null;
                $i = 0;
                foreach (config('app.locales') as $lang_key => $lang_value) {
                    if (($work_base->is_one_value_lst_str_txt == true && $lang_key == 0) || ($work_base->is_one_value_lst_str_txt == false)) {
                        if ($i == 0) {
                            $name_lang_key = $key;
                            $name_lang_value = $value;
                        }
                        // начиная со второго(индекс==1) элемента массива языков учитывать
                        if ($i > 0) {
                            $name_lang_key = $key . '_' . $lang_key;
                            $name_lang_value = $strings_inputs[$name_lang_key];
                        }
                        if (strlen($name_lang_value) > $work_base->length_txt) {
                            $array_mess[$name_lang_key] = trans('main.length_txt_rule') . ' ' . $work_base->length_txt . '!';
                            $errors = true;
                        }
                        $i = $i + 1;
                    }
                }
                if ($errors) {
                    // повторный вызов формы
                    return redirect()->back()
                        ->withInput()
                        ->withErrors($array_mess);
                }
            }
        }

        $errors = false;
        foreach ($inputs as $key => $value) {
            $link = Link::findOrFail($key);
            $base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
            $work_base = $link->parent_base;
            // при типе "логический" проверять на обязательность заполнения не нужно
            $control_required = false;
            // При ссылке '$link->parent_is_base_link == false' не проверять на обязательность заполнения
            if ($link->parent_is_base_link == false) {
                // Тип - список
                if ($work_base->type_is_list()) {
                    // так не использовать
                    // Проверка на обязательность ввода
                    //if ($work_base->is_required_lst_num_str_txt_img_doc == true) {
                    //          'Обязательно к заполнению (для списков, при условии $base->is_required_lst_num_str_txt_img_doc = false
                    if ($base_link_right['is_base_required'] == true) {
                        $control_required = true;
                    }
                } // Тип - число
                elseif ($work_base->type_is_number()) {
                    // Проверка на обязательность ввода
                    if ($work_base->is_required_lst_num_str_txt_img_doc == true) {
                        $control_required = true;
                    }
                } // Тип - строка или текст
                elseif ($work_base->type_is_string() || $work_base->type_is_text()) {
                    // Проверка на обязательность ввода
                    if ($work_base->is_required_lst_num_str_txt_img_doc == true) {
                        $control_required = true;
                    }
                } // Тип - дата
                elseif ($work_base->type_is_date()) {
                    $control_required = true;
                }
            }
            // при типе корректировки поля "строка", "логический" проверять на обязательность заполнения не нужно
            if ($control_required == true) {
                // Тип - строка или текст
                if ($work_base->type_is_string() || $work_base->type_is_text()) {
                    // поиск в таблице items значение с таким же названием и base_id
                    $name_lang_value = null;
                    $name_lang_key = null;
                    $i = 0;
                    foreach (config('app.locales') as $lang_key => $lang_value) {
                        if (($work_base->is_one_value_lst_str_txt == true && $lang_key == 0) || ($work_base->is_one_value_lst_str_txt == false)) {
                            if ($i == 0) {
                                $name_lang_key = $key;
                                $name_lang_value = $value;
                            }
                            // начиная со второго(индекс==1) элемента массива языков учитывать
                            if ($i > 0) {
                                $name_lang_key = $key . '_' . $lang_key;
                                $name_lang_value = $strings_inputs[$name_lang_key];
                            }
                            // "<option value = '0'>" присваивается при заполнении 'edit.blade' если нет данных (объектов по заданному base)            if ($value == 0)
                            // "$value === '0'" использовать для точного сравнения (например, при $link->parent_base->type_is_string())
                            // Преобразование null в '' было ранее произведено
                            if ($name_lang_value == "") {
//                              $array_mess[$name_lang_key] = trans('main.no_data_on') . ' "' . $link->parent_base->name() . '"!';
                                $array_mess[$name_lang_key] = trans('main.no_data_on') . ' "' . $link->parent_label() . '"!';
                                $errors = true;
                            }
                            $i = $i + 1;
                        }
                    }
                } else {
                    // "<option value = '0'>" присваивается при заполнении 'edit.blade' если нет данных (объектов по заданному base)            if ($value == 0)
                    // "$value === '0'" использовать для точного сравнения (например, при $link->parent_base->type_is_string())
                    if ($value == null) {
                        $array_mess[$key] = trans('main.no_data_on') . ' "' . $link->parent_base->name() . '"!';
                        $errors = true;
                    } elseif ($value === '0') {
                        $array_mess[$key] = trans('main.no_data_on') . ' "' . $link->parent_base->name() . '"!';
                        $errors = true;
                    } else {
                        $floatvalue = floatval($value);
                        if ($floatvalue == 0) {
                            $array_mess[$key] = trans('main.no_data_on') . ' "' . $link->parent_base->name() . '"!';
                            $errors = true;
                        }
                    }
                }
            }
            // Проверка полей с типом "текст" на наличие запрещенных тегов HTML
            // Правило: только в текстовых полях можно применять разрешенную HTML-теги
            if ($work_base->type_is_text()) {
                // поиск в таблице items значение с таким же названием и base_id
                $name_lang_value = null;
                $name_lang_key = null;
                $i = 0;
                foreach (config('app.locales') as $lang_key => $lang_value) {
                    if ($i == 0) {
                        $name_lang_key = $key;
                        $name_lang_value = $value;
                    }
                    if ($link->parent_base->is_one_value_lst_str_txt == false) {
                        // начиная со второго(индекс==1) элемента массива языков учитывать
                        if ($i > 0) {
                            $name_lang_key = $key . '_' . $lang_key;
                            $name_lang_value = $strings_inputs[$name_lang_key];
                        }
                    }
                    $text_html_check = GlobalController::text_html_check($name_lang_value);
                    if ($text_html_check['result'] == true) {
                        $array_mess[$name_lang_key] = $text_html_check['message'] . '!';
                        $errors = true;
                    }
                    $i = $i + 1;
                }
            }
        }

        // При корректировке
        // Если данные изменились
        if ($data_change == true) {
            // Проверка на уникальность базовых типов Дата, Число, Строка, Логический
            $message = self::verify_item_unique($item);
            if ($message != '') {
                $array_mess['name_lang_0'] = $message;
                $errors = true;
            }
        }

        if ($errors) {
            // повторный вызов формы
            return redirect()->back()
                ->withInput()
                ->withErrors($array_mess);
        }

        // Одно значение у всех языков
        if ($item->base->is_one_value_lst_str_txt == true) {
            $item->name_lang_1 = $item->name_lang_0;
            $item->name_lang_2 = $item->name_lang_0;
            $item->name_lang_3 = $item->name_lang_0;
        }

        // сохранение предыдущих значений $array_plan
        // до начала выполнения транзакции
        $array_calc = $this->get_array_calc_edit($item)['array_calc'];

        $array_items_ids = array();
        // Вычисляем массив вложенных $item_id для удаления
        self::calc_items_ids_for_delete($item, $array_items_ids, false);

        try {
            // начало транзакции
            // $array_plan передается при корректировке
            // $del_links ипользуется при корректировке item (функция ext_update()), при добавлении не используется (функция ext_store())
            DB::transaction(function ($r) use ($relip_project, $item, $role, $relit_id, $it_texts, $keys, $values, $strings_inputs, $del_links) {

                //$item->save();

                // тип - текст
                if ($it_texts) {
                    if ($item->base->type_is_text()) {
                        //$text = $item->text();
                        $text = Text::where('item_id', $item->id)->first();
                        if (!$text) {
                            $text = new Text();
                            $text->item_id = $item->id;
                        }
                        $text->name_lang_0 = $it_texts['name_lang_0'];
                        // Одно значение у всех языков для тип - текст
                        if ($item->base->is_one_value_lst_str_txt == true) {
                            $text->name_lang_1 = $text->name_lang_0;
                            $text->name_lang_2 = $text->name_lang_0;
                            $text->name_lang_3 = $text->name_lang_0;
                        } else {
                            $text->name_lang_1 = "";
                            $text->name_lang_2 = "";
                            $text->name_lang_2 = "";
                            foreach ($it_texts as $it_key => $it_text) {
                                $text[$it_key] = $it_texts[$it_key];
                            }
                        }
                        $text->save();
                    }
                }
                // Удаление изображений/документов с проставленной отметкой об удалении (если в форме проставлена отметка об удалении изображения)
                foreach ($del_links as $key) {
                    $main = Main::where('child_item_id', $item->id)->where('link_id', $key)->first();
                    if ($main) {
                        $main->parent_item->delete();
                        $main->delete();
                    }
                }

                // Только для ext_update()
                // true - с реверсом
                // обязательно true - с заменой
                $this->save_info_sets($item, true, true);

                // после ввода данных в форме массив состоит:
                // индекс массива = link_id (для занесения в links->id)
                // значение массива = item_id (для занесения в mains->parent_item_id)
                $i_max = count($keys);

                // Новый вариант
                // Сначала проверка, потом присвоение
                // Проверка на $main->link_id, если такой не найден - то удаляется
                $mains = Main::where('child_item_id', $item->id)->get();
                foreach ($mains as $main) {
                    $delete_main = false;
                    $link = Link::where('id', $main->link_id)->first();
                    if ($link) {
                        if ($link->child_base_id != $item->base_id) {
                            $delete_main = true;
                            // Нужно
                        } elseif ($link->parent_is_parent_related == true) {
                            $delete_main = true;
                            // Нужно
                        } elseif ($link->parent_is_output_calculated_table_field == true) {
                            $delete_main = true;
                        }
                    } else {
                        $delete_main = true;
                    }
                    if ($delete_main) {
                        $main->delete();
                    }
                }

                // Присвоение данных
                // "$i = 0" использовать, т.к. индексы в массивах начинаются с 0
                $i = 0;
                $valits = $values;

                foreach ($keys as $key) {
                    $main = Main::where('child_item_id', $item->id)->where('link_id', $key)->first();
                    $link = Link::where('id', $key)->first();
                    if ($link) {
                        if ($main == null) {
                            $main = new Main();
                            // при создании записи "$item->created_user_id" заполняется
                            $main->created_user_id = Auth::user()->id;
                        } else {
                            // удалить файл-предыдущее значение при корректировке
                            if ($main->parent_item->base->type_is_image() || $main->parent_item->base->type_is_document()) {
                                if ($values[$i] != "") {
                                    Storage::delete($main->parent_item->filename());
                                    //$main->parent_item->delete();
                                }
                            }
                        }
                        $message = '';
                        $this->save_main($main, $item, $keys, $values, $valits, $i, $strings_inputs, $message);
                        if ($message != '') {
                            throw new Exception($message);
                        }
                        // После выполнения массив $valits заполнен ссылками $item->id

                        // Проверка на максимальное количество записей
                        // Это есть проверка во время сохранения записи $item
                        // Проверка на $par_link->link_maxcount
                        // 'added = false' используется, нужно
                        $message = GlobalController::link_maxcount_validate($relip_project, $link, false);
                        if ($message != '') {
                            throw new Exception($message);
                        }
                        // Проверка на $par_link->child_maxcount
                        // added = 'false' используется
//                        $message_info = GlobalController::link_item_maxcount_validate($relip_project, $item, $link, false);
//                        if ($message_info != '') {
//                            $item_maxcount = Item::findOrFail($valits[$i]);
//                            // added = 'false' используется
//                            $message_result = GlobalController::link_item_maxcount_validate($relip_project, $item_maxcount, $link, false);
//                            if ($message_result != '') {
//                                throw new Exception($message_result);
//                            }
//                        }
                        $item_maxcount = Item::find($valits[$i]);
                        if ($item_maxcount) {
                            // 'added = false' используется, нужно
                            $message = GlobalController::link_item_maxcount_validate($relip_project, $item_maxcount, $link, false);
                            if ($message != '') {
                                throw new Exception($message);
                            }
                        }

                        // "$i = $i + 1;" использовать здесь, т.к. индексы в массивах начинаются с 0
                        $i = $i + 1;
                    }
                }

                // Проверка на $base->maxcount_lst, После цикла по $keys
                // Проверка осуществляется только при корректировке записи при сохранении записи
                // 'added = false' используется, нужно
                $message = GlobalController::base_maxcount_validate($relip_project, $item->base, false);
                if ($message == '') {
                    self::func_del_items_maxcnt($relip_project, $item);
                } else {
                    throw new Exception($message);
                }

                // Проверка на $base->maxcount_user_id_lst
                // Проверка осуществляется только при корректировке записи при сохранении записи
                // 'added = false' используется, нужно
                $message = GlobalController::base_user_id_maxcount_validate($relip_project, $item->base, false);
                if ($message != '') {
                    throw new Exception($message);
                }
                // Проверка на $base->maxcount_byuser_lst
                // Проверка осуществляется только при корректировке записи при сохранении записи
                // 'added = false' используется, нужно
                $message = GlobalController::base_byuser_maxcount_validate($relip_project, $item->base, false);
                if ($message != '') {
                    return view('message', ['message' => $message]);
                }

                // Проверка на уникальность значений $item->child_mains;
                // Похожие строки при добавлении (функция ext_store()) и сохранении (функция ext_update()) записи
                $get_child_links = $this->get_child_links($item->base);
                // Нужно 'where('id', '!=', $item->id)'
                $items_unique_select = Item::where('id', '!=', $item->id)
                    ->where('project_id', '=', $relip_project->id);
                // Нужно '$items_unique_exist = false;'
                $items_unique_exist = false;
                // Нужно '$items_unique_bases = '';'
                $items_unique_bases = '';
                foreach ($get_child_links as $key => $link) {
                    $link_id = $link->id;
                    if ($link->parent_is_unique == true) {
                        $main = Main::where('child_item_id', $item->id)->where('link_id', $link_id)->first();
                        if ($main) {
                            $parent_item_id = $main->parent_item_id;
                            $items_unique_select = $items_unique_select->whereHas('child_mains', function ($query) use ($link_id, $parent_item_id) {
                                $query->where('link_id', $link_id)
                                    ->where('parent_item_id', $parent_item_id);
                            });
                            $items_unique_bases = $items_unique_bases . ($items_unique_exist == false ? '' : ', ') . $link->parent_base->name();;
                            // Нужно '$items_unique_exist = true;'
                            $items_unique_exist = true;
                        }
                    }
                }
                if ($items_unique_exist == true) {
                    $items_unique_select = $items_unique_select->get();
                    if (count($items_unique_select) != 0) {
                        throw new Exception(trans('main.value_uniqueness_violation') . ' (' . $items_unique_bases . ')!');
                    }
                }

                // Проверка 'Доступность ввода данных на основе проверки истории (links)'
                $is_checking_history = GlobalController::is_checking_history($item, $role, $relit_id);
                if ($is_checking_history['result_entry_history'] == false) {
                    throw new Exception($is_checking_history['result_message_history']);
                }

                // Проверка 'Доступность ввода данных на основе проверки заполненности данных (links)'
                $is_checking_empty = GlobalController::is_checking_empty($item, $role, $relit_id);
                if ($is_checking_empty['result_entry_empty'] == false) {
                    throw new Exception($is_checking_empty['result_message_empty']);
                }

                // Расчет вычисляемого наименования
                $rs = $this->calc_value_func($item);
                if ($rs != null) {
                    $item->name_lang_0 = $rs['calc_lang_0'];
                    $item->name_lang_1 = $rs['calc_lang_1'];
                    $item->name_lang_2 = $rs['calc_lang_2'];
                    $item->name_lang_3 = $rs['calc_lang_3'];
                }
                // ext_update()
                // При reverse = false передаем null
                // true - с заменой
                //$this->save_sets($item, $keys, $values, $valits, false, true);
                $this->save_info_sets($item, false, true);

                $item->save();

                // Нужно
                // Только для ext_update()
                // Перерасчет $items по переданным $item по всем проектам,
                // т.к. $item->names... могли поменяться
                // обязательно нужно после команды " $item->save();"
                $this->calc_item_names($item);

            }, 3);  // Повторить три раза, прежде чем признать неудачу
            // окончание транзакции

        } catch (Exception $exc) {
            //return trans('transaction_not_completed') . ": " . $exc->getMessage();
            return view('message', ['message' => trans('main.transaction_not_completed') . ": " . $exc->getMessage()]);
        }

        // удаление неиспользуемых данных
        $this->delete_items_old($array_calc);

        if (env('MAIL_ENABLED') == 'yes') {
            $base_right = GlobalController::base_right($item->base, $role, $relit_id);
            if ($base_right['is_edit_email_base_update'] == true) {
                $email_to = $item->created_user->email;
                $appname = config('app.name', 'Abakus');
                try {
                    Mail::send(['html' => 'mail/item_update'], ['item' => $item],
                        function ($message) use ($email_to, $appname, $item) {
                            $message->to($email_to, '')->subject(trans('main.edit_record') . ' - ' . $item->base->name());
                            $message->from(env('MAIL_FROM_ADDRESS', ''), $appname);
                        });
                } catch (Exception $exc) {
                    return trans('error_sending_email') . ": " . $exc->getMessage();
                }
            }
        }

//        if (env('MAIL_ENABLED') == 'yes'){
//            $appname = config('app.name', 'Abakus');
//            Mail::send(['html' => 'mail/login_site'], ['remote_addr' => $_SERVER['REMOTE_ADDR'],
//                'http_user_agent' => $_SERVER['HTTP_USER_AGENT'],'appname' => $appname],
//                function ($message) use ($appname) {
//                    $message->to('s_astana@mail.ru', '')->subject("Заказ одобрен '" . $appname . "'");
//                    $message->from(env('MAIL_FROM_ADDRESS', 'support@rsb0807.kz'), $appname);
//                });
//        }

        //  Похожий текст в функциях ext_store(), ext_update(), ext_delete(), ext_return();
        //  По алгоритму передается $base_index_page, $body_link_page, $body_all_page - сохраненные номера страниц;
        $parent_find_item = true;
        if ($parent_item) {
            // За время добавления(маловероятно, т.к. есть связи между основами)/корректировки/удаления
            // $parent_item может быть удален из базы данных.
            // Например, при установке признака 'Разрешить корректировку поля при связи parlink (при корректировке записи)' в rolis
            // и например, поле $parent_item логического типа
            $parent_find_item = Item::find($parent_item->id);
        }
        if (!$parent_find_item) {
            // Вызов главного меню
            return redirect()->route('project.start', ['project' => $project, 'role' => $role]);
        }
        $str_link = '';
        if ($base_index_page > 0) {
            // Использовать "project' => $project"
            // Используется "'relit_id'=> $relit_id"
            return redirect()->route('item.base_index', ['base' => $item->base, 'project' => $project, 'role' => $role,
                'relit_id' => $relit_id,
                'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page]);
        } else {
            // Если $heading = true - нажата Добавить из "heading", false - из "body" (только при добавлении записи)
            $str_link = '';
            if ($body_all_page > 0) {
                // Вызываем item_index.php с body - все
                $str_link = GlobalController::par_link_const_textnull();
            } else {
                // Вызываем item_index.php с body - связь $par_link
                $str_link = $view_link;
            }
            if (!$heading && $parent_item) {
                // Используется "'relit_id'=>$parent_ret_id, 'view_ret_id' => $relit_id'"
                return redirect()->route('item.item_index', ['project' => $project, 'item' => $parent_item, 'role' => $role,
                    'usercode' => GlobalController::usercode_calc(),
                    'relit_id' => $parent_ret_id,
                    'called_from_button' => 1,
                    'view_link' => $str_link,
                    // 'string_link_ids_current' => $string_link_ids_current, 'string_item_ids_current' => $string_item_ids_current, 'string_all_codes_current' => $string_all_codes_current,
                    'string_current' => $string_current,
                    'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
                    'prev_base_index_page' => $base_index_page,
                    'prev_body_link_page' => $body_link_page,
                    'prev_body_all_page' => $body_all_page,
                    'view_ret_id' => $relit_id]);
            } else {
                // Используется "'relit_id'=>$parent_ret_id, 'view_ret_id' => $relit_id'"
//                return redirect()->route('item.item_index', ['project' => $project, 'item' => $item, 'role' => $role,
//                    'usercode' => GlobalController::usercode_calc(),
//                    'relit_id' => $parent_ret_id,
//                    'view_link' => $str_link,
//                    'string_link_ids_current' => $string_link_ids_current, 'string_item_ids_current' => $string_item_ids_current, 'string_all_codes_current' => $string_all_codes_current,
//                    'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
//                    'prev_base_index_page' => $base_index_page,
//                    'prev_body_link_page' => $body_link_page,
//                    'prev_body_all_page' => $body_all_page,
//                    'view_ret_id' => $relit_id]);

                // Только для ext_update()
                // Этот блок нужен, если $base_link_right['is_edit_parlink_enable'] == true есть в $item->links
                // Например, корректировка "На модерации"
                $is_redirect = false;
                // Похожие строки в ItemController::item_index() и ItemController::ext_update()
                $string_unzip_current_next = self::string_unzip_current_next($string_current);
                $string_link_ids_current = $string_unzip_current_next['string_link_ids'];
                $string_item_ids_current = $string_unzip_current_next['string_item_ids'];
                $string_relit_ids_current = $string_unzip_current_next['string_relit_ids'];
                $string_vwret_ids_current = $string_unzip_current_next['string_vwret_ids'];
                $string_all_codes_current = $string_unzip_current_next['string_all_codes'];

                $tree_array = self::calc_tree_array($role,
                    $string_link_ids_current,
                    $string_item_ids_current,
                    $string_relit_ids_current,
                    $string_vwret_ids_current,
                    $string_all_codes_current);
                $count_tree_array = count($tree_array);
                if ($count_tree_array > 0) {
                    // Используется последний элемент массива $tree_array
                    // ' - 1' т.к. нумерация массива $tree_array с нуля начинается
                    $tree_array_last_link_id = $tree_array[$count_tree_array - 1]['link_id'];
                    $tree_array_last_item_id = $tree_array[$count_tree_array - 1]['item_id'];
                    $tree_array_last_relit_id = $tree_array[$count_tree_array - 1]['relit_id'];
                    $tree_array_last_string_previous = $tree_array[$count_tree_array - 1]['string_previous'];
                    // "Шапка" документа
                    // Используется фильтр на равенство одному $item->id (для вывода таблицы из одной строки)
                    // $relit_id нужно передавать, предпоследний параметр, нужно так, чтобы правильно данные выбирались
                    // Эта проверка "GlobalController::items_right() и count($items_right) == 0" нужна чтобы проверить нужно ли отображать запись $item (в "шапке")
                    // Например, корректируется "На модерации": ставится null, в $tree_array есть значение "На модерации"
                    // "count($items_right) == 0" - значит запись $item не должна отображаться
                    // "count($items_right) != 0" - если значение "На модерации" осталось как в $tree_array, значит запись отображается
                    $items_right = GlobalController::items_right($item->base, $item->project, $role, $relit_id, $tree_array_last_item_id, $tree_array_last_link_id, $project, $relit_id, $item->id)['items']->get();
                    // Использовать проверку 'count($items_right) == 0'
                    if (count($items_right) == 0) {
                        $is_redirect = true;
                        return redirect()->route('item.item_index', ['project' => $project, 'item' => $tree_array_last_item_id, 'role' => $role,
                            'usercode' => GlobalController::usercode_calc(),
                            'relit_id' => $tree_array_last_relit_id,
                            'called_from_button' => 1,
                            'view_link' => $tree_array_last_link_id,
                            'string_current' => $tree_array_last_string_previous,
                            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
                            'prev_base_index_page' => $base_index_page,
                            'prev_body_link_page' => $body_link_page,
                            'prev_body_all_page' => $body_all_page
                        ]);
                    }
                }
                if (!$is_redirect) {
                    return redirect()->route('item.item_index', ['project' => $project, 'item' => $item, 'role' => $role,
                        'usercode' => GlobalController::usercode_calc(),
                        'relit_id' => $relit_id,
                        'called_from_button' => 1,
                        'view_link' => $str_link,
                        // 'string_link_ids_current' => $string_link_ids_current, 'string_item_ids_current' => $string_item_ids_current, 'string_all_codes_current' => $string_all_codes_current,
                        'string_current' => $string_current,
                        'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
                        'prev_base_index_page' => $base_index_page,
                        'prev_body_link_page' => $body_link_page,
                        'prev_body_all_page' => $body_all_page,
                        'view_ret_id' => $parent_ret_id
                    ]);
                }
            }
        }
    }

    private
    function delete_items_old($array_calc)
    {
        foreach ($array_calc as $key => $value) {
            // использовать '$link = Link::find($key); if($link){}'
            // не использовать findOrFail($key), т.к. данная функция выполняется уже вне основной транзакции
            // и за время окончания выполнения основной транзакции
            // в базе данных (bases, links, items, mains) могут поменяться значения/записи
            //
            // проверка предыдущего значения (числа),
            // и если оно не используется (на него нет parent-ссылок в таблице main)
            // то тогда, это число (запись item) удаляется из таблицы items,
            // чтобы не засорять таблицу items неиспользуемой информацией
            //
            $link = Link::find($key);
            if ($link) {
                // тип корректировки поля - не список
                // '!' используется
                if (!$link->parent_base->type_is_list()) {
                    $item_old_id = $array_calc[$link->id];
                    // выполнять проверку на null,
                    // т.к. в функции get_array_plan() по умолчанию элементам массива $array_calc[] присваивается null
                    if ($item_old_id != null) {
                        $item_old = Item::find($item_old_id);
                        if ($item_old) {
                            if (count($item_old->parent_mains) == 0) {
                                // Проверку на присваивания - ?
                                $item_old->delete();
                            }
                        }
                    }
                }
            }
        }
    }

    function edit(Item $item)
    {
        return view('item/edit', ['item' => $item, 'bases' => Base::all()]);
    }

//    function ext_edit(Item $item, Project $project, Role $role, $usercode, $relit_id,
//                           $string_link_ids_current = '', $string_item_ids_current = '', $string_all_codes_current = '',
//                           $heading = 0,
//                           $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
//                           $parent_ret_id = null,
//                           $view_link = null,
//                      Link $par_link = null, Item $parent_item = null)
    function ext_edit(Item $item, Project $project, Role $role, $usercode, $relit_id,
                           $string_current = '',
                           $heading = 0,
                           $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
                           $parent_ret_id = null,
                           $view_link = null,
                      Link $par_link = null, Item $parent_item = null)
    {
        if ($item->is_history() == true) {
            return view('message', ['message' => trans('main.no_access')]);
        }

        if (GlobalController::check_project_item_user($project, $item, $role, $usercode) == false) {
            return view('message', ['message' => trans('main.no_access')]);
        }

//    if (GlobalController::check_project_user($project, $role) == false) {
//        return view('message', ['message' => trans('main.info_user_changed')]);
//    }

        // Проверка $item
        $item_ch = GlobalController::items_check_right($item, $role, $relit_id);
        if (!$item_ch) {
            return view('message', ['message' => trans('main.access_restricted')]);
        }

//      $base_right = self::base_relit_right($item->base, $role, $heading, $base_index_page, $relit_id, $parent_ret_id);
        $base_right = GlobalController::base_right($item->base, $role, $relit_id);

//      Похожая проверка в ItemController::ext_edit() и ext_show.php
        if ($base_right['is_list_base_update'] == false) {
            return view('message', ['message' => trans('main.no_access')]);
        }

        $is_limit_minutes = GlobalController::is_limit_minutes($base_right, $item);
        if ($is_limit_minutes['is_entry_minutes'] == false) {
            return view('message', ['message' => trans('main.no_access') . ' (' . trans('main.title_min') . ')']);
        }

        $is_checking_history = GlobalController::is_checking_history($item, $role, $relit_id);
        if ($is_checking_history['result_entry_history'] == false) {
            return view('message', ['message' => $is_checking_history['result_message_history']]);
        }

        $is_checking_empty = GlobalController::is_checking_empty($item, $role, $relit_id);
        if ($is_checking_empty['result_entry_empty'] == false) {
            return view('message', ['message' => $is_checking_empty['result_message_empty']]);
        }

        $string_unzip_current_next = self::string_unzip_current_next($string_current);
        $string_link_ids_current = $string_unzip_current_next['string_link_ids'];
        $string_item_ids_current = $string_unzip_current_next['string_item_ids'];
        $string_relit_ids_current = $string_unzip_current_next['string_relit_ids'];
        $string_vwret_ids_current = $string_unzip_current_next['string_vwret_ids'];
        $string_all_codes_current = $string_unzip_current_next['string_all_codes'];

        $arrays = $this->get_array_calc_edit($item, $par_link, $parent_item);
        $array_calc = $arrays['array_calc'];
        $array_disabled = $arrays['array_disabled'];
        if ($item->code == "" && $item->base->is_code_needed == false) {
            // Похожая строка есть и в ext_create
            $item->code = uniqid($item->base_id . '_', true);
        }

        return view('item/ext_edit', ['base' => $item->base, 'item' => $item,
            'project' => $project,
            'role' => $role,
            'relit_id' => $relit_id,
            'array_calc' => $array_calc,
            'array_disabled' => $array_disabled,
            'is_view_minutes' => $is_limit_minutes['is_view_minutes'],
//          'string_link_ids_current' => $string_link_ids_current, 'string_item_ids_current' => $string_item_ids_current, 'string_all_codes_current' => $string_all_codes_current,
            'string_current' => $string_current,
            'heading' => $heading,
            'base_index_page' => $base_index_page,
            'body_link_page' => $body_link_page,
            'body_all_page' => $body_all_page,
            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
            'parent_ret_id' => $parent_ret_id,
            'view_link' => $view_link,
            'par_link' => $par_link,
            'parent_item' => $parent_item]);
    }

//    function ext_delete_question(Item $item, Project $project, Role $role,
//                                      $usercode,
//                                      $relit_id,
//                                      $string_link_ids_current = '', $string_item_ids_current = '', $string_all_codes_current = '',
//                                      $heading = 0,
//                                      $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
//                                      $parent_ret_id = null,
//                                      $view_link = null,
//                                 Link $par_link, Item $parent_item = null)
    function ext_delete_question(Item $item, Project $project, Role $role,
                                      $usercode,
                                      $relit_id,
                                      $string_current = '',
                                      $heading = 0,
                                      $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
                                      $parent_ret_id = null,
                                      $view_link = null,
                                 Link $par_link, Item $parent_item = null)

    {
        if ($item->is_history() == true) {
            return view('message', ['message' => trans('main.no_access')]);
        }

        if (GlobalController::check_project_item_user($project, $item, $role, $usercode) == false) {
            return view('message', ['message' => trans('main.no_access')]);
        }

//        if (GlobalController::check_project_user($project, $role) == false) {
//            return view('message', ['message' => trans('main.info_user_changed')]);
//        }

        $string_unzip_current_next = self::string_unzip_current_next($string_current);
        $string_link_ids_current = $string_unzip_current_next['string_link_ids'];
        $string_item_ids_current = $string_unzip_current_next['string_item_ids'];
        $string_relit_ids_current = $string_unzip_current_next['string_relit_ids'];
        $string_vwret_ids_current = $string_unzip_current_next['string_vwret_ids'];
        $string_all_codes_current = $string_unzip_current_next['string_all_codes'];

//      $base_right = self::base_relit_right($item->base, $role, $heading, $base_index_page, $relit_id, $parent_ret_id);
        $base_right = GlobalController::base_right($item->base, $role, $relit_id);

        $is_limit_minutes = GlobalController::is_limit_minutes($base_right, $item);
        if ($is_limit_minutes['is_entry_minutes'] == false) {
            return view('message', ['message' => trans('main.no_access') . ' (' . trans('main.title_min') . ')']);
        }

        $is_checking_history = GlobalController::is_checking_history($item, $role, $relit_id);
        if ($is_checking_history['result_entry_history'] == false) {
            return view('message', ['message' => $is_checking_history['result_message_history']]);
        }

        $is_checking_empty = GlobalController::is_checking_empty($item, $role, $relit_id);
        if ($is_checking_empty['result_entry_empty'] == false) {
            return view('message', ['message' => $is_checking_empty['result_message_empty']]);
        }

        return view('item/ext_show', ['type_form' => 'delete_question', 'item' => $item, 'role' => $role,
            'project' => $project,
            'relit_id' => $relit_id,
            'base_right' => $base_right,
            'array_calc' => $this->get_array_calc_edit($item)['array_calc'],
            'is_limit_minutes' => $is_limit_minutes,
            'is_checking_history' => $is_checking_history,
            'is_checking_empty' => $is_checking_empty,
            // 'string_link_ids_current' => $string_link_ids_current, 'string_item_ids_current' => $string_item_ids_current, 'string_all_codes_current' => $string_all_codes_current,
            'string_current' => $string_current,
            'heading' => $heading,
            'base_index_page' => $base_index_page,
            'body_link_page' => $body_link_page,
            'body_all_page' => $body_all_page,
            'view_link' => $view_link,
            'par_link' => $par_link,
            'parent_item' => $parent_item,
            'parent_ret_id' => $parent_ret_id]);

    }

//    function ext_delete(Item $item, Project $project, Role $role,
//                             $usercode, $relit_id,
//                             $string_link_ids_current = '', $string_item_ids_current = '', $string_all_codes_current = '',
//                             $heading = 0, $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
//                             $parent_ret_id = null,
//                             $view_link = null,
//                        Link $par_link, Item $parent_item = null)

    function ext_delete(Item $item, Project $project, Role $role,
                             $usercode, $relit_id,
                             $string_current = '',
                             $heading = 0, $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
                             $parent_ret_id = null,
                             $view_link = null,
                        Link $par_link, Item $parent_item = null)
    {
        if ($item->is_history() == true) {
            return view('message', ['message' => trans('main.no_access')]);
        }

        if (GlobalController::check_project_item_user($project, $item, $role, $usercode) == false) {
            return view('message', ['message' => trans('main.no_access')]);
        }

//        if (GlobalController::check_project_user($project, $role) == false) {
//            return view('message', ['message' => trans('main.info_user_changed')]);
//        }

//        if ($item->base->type_is_image() || $item->base->type_is_document()) {
//            Storage::delete($item->filename());
//        }
//
//        $mains = Main::where('child_item_id', $item->id)->get();
//        foreach ($mains as $main) {
//            if ($main->parent_item->base->type_is_image() || $main->parent_item->base->type_is_document()) {
//                Storage::delete($main->parent_item->filename());
//                $main->parent_item->delete();
//            }
//        }
        $string_unzip_current_next = self::string_unzip_current_next($string_current);
        $string_link_ids_current = $string_unzip_current_next['string_link_ids'];
        $string_item_ids_current = $string_unzip_current_next['string_item_ids'];
        $string_relit_ids_current = $string_unzip_current_next['string_relit_ids'];
        $string_vwret_ids_current = $string_unzip_current_next['string_vwret_ids'];
        $string_all_codes_current = $string_unzip_current_next['string_all_codes'];

        $relip_project = GlobalController::calc_relip_project($relit_id, $project);
        $array_items_ids = array();
//      $is_delete = self::is_delete($item, $role, $heading, $base_index_page, $relit_id, $parent_ret_id);
        $is_delete = self::is_delete($item, $role, $relit_id);

        if ($is_delete['result'] == true) {

            $item_copy = $item;

            if ($is_delete['is_list_base_used_delete'] == true) {
                // Вычисляем массив вложенных $item_id для удаления
                self::calc_items_ids_for_delete($item, $array_items_ids, false);
            }

//            if ($this->is_save_sets($item)) {
//                try {
//                    // начало транзакции
//                    DB::transaction(function ($r) use ($item) {
//                        // true - с реверсом
//                        // false - без замены
//                        $this->save_info_sets($item, true, false);
//
//                        $base_id = $item->base_id;
//                        $project_id = $item->project_id;
//
//                        $item->delete();
//
//                        $this->sets_null_delete($base_id, $project_id);
//
//                    }, 3);  // Повторить три раза, прежде чем признать неудачу
//                    // окончание транзакции
//
//                } catch (Exception $exc) {
//                    return trans('transaction_not_completed') . ": " . $exc->getMessage();
//                }
//
//            } else {
//                $item->delete();
//
//            }

            if (($this->is_save_sets($item)) || (count($array_items_ids) > 0)) {
                try {

                    // начало транзакции
                    DB::transaction(function ($r) use ($item, $array_items_ids) {
                        // Нужно
                        self::func_delete($item);

                        // Удаление подчиненных связанных записей
                        self::run_items_ids_for_delete($array_items_ids);

                    }, 3);  // Повторить три раза, прежде чем признать неудачу
                    // окончание транзакции

                } catch (Exception $exc) {
                    return trans('transaction_not_completed') . ": " . $exc->getMessage();
                }

            } else {

                $item->delete();

            }

            $item = $item_copy;

            if (env('MAIL_ENABLED') == 'yes') {
                $base_right = GlobalController::base_right($item->base, $role, $relit_id);
                if ($base_right['is_show_email_base_delete'] == true) {
                    $email_to = $item->created_user->email;
                    $deleted_user_date_time = GlobalController::deleted_user_date_time();
                    $appname = config('app.name', 'Abakus');
                    try {
                        Mail::send(['html' => 'mail/item_delete'], ['item' => $item, 'deleted_user_date_time' => $deleted_user_date_time],
                            function ($message) use ($email_to, $appname, $item) {
                                $message->to($email_to, '')->subject(trans('main.delete_record') . ' - ' . $item->base->name());
                                $message->from(env('MAIL_FROM_ADDRESS', ''), $appname);
                            });
                    } catch (Exception $exc) {
                        return trans('error_sending_email') . ": " . $exc->getMessage();
                    }
                }
            }
        } else {
            if ($is_delete['is_limit_minutes']['is_entry_minutes'] == false) {
                return view('message', ['message' => trans('main.no_access') . ' (' . trans('main.title_min') . ')']);
            }
        }

        // Если запись удаляется при просмотре Пространство с "шапки", то перейти на base_index
        if ($heading == true) {
            // Используется "'relit_id'=>$parent_ret_id"
            return redirect()->route('item.base_index', ['base' => $item->base, 'project' => $project, 'role' => $role,
                'relit_id' => $relit_id]);
        } else {
            //  Похожий текст в функциях ext_store(), ext_update(), ext_delete(), ext_return();
            //  По алгоритму передается $base_index_page, $body_link_page, $body_all_page - сохраненные номера страниц;
            $parent_find_item = true;
            if ($parent_item) {
                // За время добавления(маловероятно, т.к. есть связи между основами)/корректировки/удаления
                // $parent_item может быть удален из базы данных.
                // Например, при установке признака 'Разрешить корректировку поля при связи parlink (при корректировке записи)' в rolis
                // и например, поле $parent_item логического типа
                $parent_find_item = Item::find($parent_item->id);
            }
            if (!$parent_find_item) {
                // Вызов главного меню
                return redirect()->route('project.start', ['project' => $project, 'role' => $role]);
            }
            $str_link = '';
            if ($base_index_page > 0) {
                // Только при удалении эти строки
                if ($base_index_page > 1) {
                    $base_index_page = $base_index_page - 1;
                }
                // Использовать "project' => $project"
                // Используется "'relit_id'=> $relit_id"
                return redirect()->route('item.base_index', ['base' => $item->base, 'project' => $project, 'role' => $role,
                    'relit_id' => $relit_id,
                    'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page]);
            } else {
                // Если $heading = true - нажата Добавить из "heading", false - из "body" (только при добавлении записи)
                $str_link = '';
                if ($body_all_page > 0) {
                    // Вызываем item_index.php с body - все
                    $str_link = GlobalController::par_link_const_textnull();
                    // Только при удалении эти строки
                    if ($body_all_page > 1) {
                        $body_all_page = $body_all_page - 1;
                    }
                } else {
                    // Вызываем item_index.php с body - связь $par_link
                    $str_link = $view_link;
                    // Только при удалении эти строки
                    if ($body_link_page > 1) {
                        $body_link_page = $body_link_page - 1;
                    }
                }
                // Используется "'relit_id'=>$parent_ret_id, 'view_ret_id' => $relit_id'"
                return redirect()->route('item.item_index', ['project' => $project, 'item' => $parent_item, 'role' => $role,
                    'usercode' => GlobalController::usercode_calc(),
                    'relit_id' => $parent_ret_id,
                    'called_from_button' => 1,
                    'view_link' => $str_link,
//                      'string_link_ids_current' => $string_link_ids_current, 'string_item_ids_current' => $string_item_ids_current, 'string_all_codes_current' => $string_all_codes_current,
                    'string_current' => $string_current,
                    'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
                    'prev_base_index_page' => $base_index_page,
                    'prev_body_link_page' => $body_link_page,
                    'prev_body_all_page' => $body_all_page,
                    'view_ret_id' => $relit_id]);
            }
//            if (Session::has('base_index_previous_url')) {
//                return redirect(session('base_index_previous_url'));
//            } else {
//                return redirect()->back();
//            }
        }
    }

    function func_delete(Item $item)
    {
        $is_save_sets = $this->is_save_sets($item);

        if ($is_save_sets == true) {
            // true - с реверсом
            // false - без замены
            $this->save_info_sets($item, true, false);

            $base_id = $item->base_id;
            $project_id = $item->project_id;

            $item->delete();

            $this->sets_null_delete($base_id, $project_id);

        } else {
            $item->delete();
        }

    }

//    static function is_delete(Item $item, Role $role, $heading, $base_index_page, $relit_id, $parent_ret_id)
    static function is_delete(Item $item, Role $role, $relit_id)
    {
        // Нужно присваивания
        $result = false;
        $is_list_base_used_delete = false;
        //$base_right = self::base_relit_right($item->base, $role, $heading, $base_index_page, $relit_id, $parent_ret_id);
        $base_right = GlobalController::base_right($item->base, $role, $relit_id);
        $is_limit_minutes = GlobalController::is_limit_minutes($base_right, $item);
        $is_checking_history = GlobalController::is_checking_history($item, $role, $relit_id);
        $is_checking_empty = GlobalController::is_checking_empty($item, $role, $relit_id);
        if ($is_limit_minutes['is_entry_minutes'] == true
            & $is_checking_history['result_entry_history'] == true
            & $is_checking_empty['result_entry_empty'] == true) {
            if ($base_right['is_list_base_delete'] == true) {
                if ($base_right['is_list_base_used_delete'] == true) {
                    $result = true;
                    $array_items_ids = array();
                    // Проверка на существование хотя бы одного элемента в массиве
                    self::calc_items_ids_for_delete($item, $array_items_ids, true);
                    // Используется "count($array_items_ids) > 0"
                    if (count($array_items_ids) > 0) {
                        $is_list_base_used_delete = true;
                    }
                } else {
                    // Отрицание "!" используется
                    $result = !self::main_exists($item);
                }
            }
        }
        return ['result' => $result, 'is_list_base_used_delete' => $is_list_base_used_delete, 'is_limit_minutes' => $is_limit_minutes];
    }

    // Рекурсивная функция
    // Вычисление вложенных items_ids для удаления взависимости от переданного $item
    private
    function calc_items_ids_for_delete(Item $item, &$array_items_ids, bool $exist)
    {
        // '->get()' нужно
        $mains = Main::where('parent_item_id', $item->id)->get();
        foreach ($mains as $main) {
            if (!in_array($main->child_item_id, $array_items_ids)) {
                // При "$exist & count($array_items_ids)>1" условие не срабатывает, и это правильно
                // При "!$exist" вычисляется весь список $items
                if (($exist & count($array_items_ids) == 0) | (!$exist)) {
                    // В массиве $array_items_ids сохраняются уникальные значения
                    $array_items_ids[] = $main->child_item_id;
                    // рекурсивный вызов этой же функции
                    self::calc_items_ids_for_delete($main->child_item, $array_items_ids, $exist);
                }
            }
        }
    }

    // Удаление $items для удаления
    private
    function run_items_ids_for_delete($array_items_ids)
    {
        foreach ($array_items_ids as $item_id) {
            $item_find = Item::find($item_id);
            if ($item_find) {
                self::func_delete($item_find);
            }
        }
    }

    function ext_return(Item $item, Project $project, Role $role,
                             $usercode, $relit_id,
                             $string_current = '',
                             $heading = 0, $base_index_page = 0, $body_link_page = 0, $body_all_page = 0,
                             $parent_ret_id = null,
                             $view_link = null,
                        Link $par_link, Item $parent_item = null)
    {
        // Если запись удаляется при просмотре Пространство с "шапки", то перейти на base_index
//        if ($heading == true) {
//            // Используется "'relit_id'=>$parent_ret_id"
//            return redirect()->route('item.base_index', ['base' => $item->base, 'project' => $project, 'role' => $role,
//                'relit_id' => $relit_id]);
//        } else {
        //  Похожий текст в функциях ext_store(), ext_update(), ext_delete(), ext_return();
        //  По алгоритму передается $base_index_page, $body_link_page, $body_all_page - сохраненные номера страниц;
        $parent_find_item = true;
        if ($parent_item) {
            // За время добавления(маловероятно, т.к. есть связи между основами)/корректировки/удаления
            // $parent_item может быть удален из базы данных.
            // Например, при установке признака 'Разрешить корректировку поля при связи parlink (при корректировке записи)' в rolis
            // и например, поле $parent_item логического типа
            $parent_find_item = Item::find($parent_item->id);
        }
        if (!$parent_find_item) {
            // Вызов главного меню
            return redirect()->route('project.start', ['project' => $project, 'role' => $role]);
        }
        $str_link = '';
        if ($base_index_page > 0) {
            // Только при удалении эти строки
//            if ($base_index_page > 1) {
//                $base_index_page = $base_index_page - 1;
//            }
            // Использовать "project' => $project"
            // Используется "'relit_id'=> $relit_id"
//                return $item->base_id . '-' . $project->id . '-' . $role->id . '-' . $relit_id . '-' . $base_index_page . '-' . $body_link_page . '-'
//                . $body_all_page;
            return redirect()->route('item.base_index', ['base' => $item->base, 'project' => $project, 'role' => $role,
                'relit_id' => $relit_id,
                'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page]);
        } else {
            // Если $heading = true - нажата Добавить из "heading", false - из "body" (только при добавлении записи)
            $str_link = '';
            if ($body_all_page > 0) {
                // Вызываем item_index.php с body - все
                $str_link = GlobalController::par_link_const_textnull();
                // Только при удалении эти строки
//                if ($body_all_page > 1) {
//                    $body_all_page = $body_all_page - 1;
//                }
            } else {
                // Вызываем item_index.php с body - связь $par_link
                $str_link = $view_link;
                // Только при удалении эти строки
//                if ($body_link_page > 1) {
//                    $body_link_page = $body_link_page - 1;
//                }
            }
            if (!$heading && $parent_item) {
                // Используется "'relit_id'=>$parent_ret_id, 'view_ret_id' => $relit_id'"
                return redirect()->route('item.item_index', ['project' => $project, 'item' => $parent_item, 'role' => $role,
                    'usercode' => GlobalController::usercode_calc(),
                    'relit_id' => $parent_ret_id,
                    'called_from_button' => 1,
                    'view_link' => $str_link,
//                      'string_link_ids_current' => $string_link_ids_current, 'string_item_ids_current' => $string_item_ids_current, 'string_all_codes_current' => $string_all_codes_current,
                    'string_current' => $string_current,
                    'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
                    'prev_base_index_page' => $base_index_page,
                    'prev_body_link_page' => $body_link_page,
                    'prev_body_all_page' => $body_all_page,
                    'view_ret_id' => $relit_id]);
            } else {
                return redirect()->route('item.item_index', ['project' => $project, 'item' => $item, 'role' => $role,
                    'usercode' => GlobalController::usercode_calc(),
                    'relit_id' => $relit_id,
                    'called_from_button' => 1,
                    'view_link' => $str_link,
                    // 'string_link_ids_current' => $string_link_ids_current, 'string_item_ids_current' => $string_item_ids_current, 'string_all_codes_current' => $string_all_codes_current,
                    'string_current' => $string_current,
                    'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
                    'prev_base_index_page' => $base_index_page,
                    'prev_body_link_page' => $body_link_page,
                    'prev_body_all_page' => $body_all_page,
                    'view_ret_id' => $parent_ret_id]);
            }
        }
        //}
    }

    static function main_exists(Item $item)
    {
//      $result =  Main::where('parent_item_id', $item->id)->exists();
        $mains = Main::where('parent_item_id', $item->id);
        $result = $mains->exists();
        // Проверка:  если есть одна запись Ссылка на саму Основу, тогда запись можно удалить
        if ($result) {
            $count = count($mains->get());
            if ($count == 1) {
                $main = $mains->first();
                $link = $main->link;
                if ($link->parent_is_base_link == true) {
                    $result = false;
                }
            } else {
                // Проверка: есть ли записи вычисляемых основ
                $mn = $mains->get();
                $cn = count($mn);
                $i = 0;
                foreach ($mn as $m) {
                    $link = $m->link;
                    if ($link->parent_is_base_link == true) {
                        $i = $i + 1;
                    } else {
                        if ($m->child_item->base->is_calculated_lst == true)
                            $i = $i + 1;
                    }
                }
                if ($cn == $i) {
                    $result = false;
                }
            }
        }
        return $result;
    }

// Функции get_items_for_link() и get_items_ext_edit_for_link()
// в целом похожи в части возвращаемых 'result_parent_label', 'result_parent_base_name', 'result_parent_base_items'
    static function get_items_for_link(Link $link, Project $project, Role $role, $relit_id)
    {
        $result_parent_label = '';
        $result_child_base_name = '';
        $result_parent_base_name = '';
        $result_child_base_items = [];
        $result_parent_base_items_no_get = [];
        $result_parent_base_items = [];
        $result_child_base_items_options = '';
        $result_parent_base_items_options = '';

        if ($link != null) {
            // наименование
            $result_parent_label = $link->parent_label();
            // наименование child_base и parent_base
            $result_child_base_name = $link->child_base->name();
            $result_parent_base_name = $link->parent_base->name();
//            // если это фильтрируемое поле - то, тогда загружать весь список не нужно
//            $link_exists = Link::where('parent_is_child_related', true)->where('parent_child_related_start_link_id', $link->id)->exists();
//            if ($link_exists == null) {
            $name = "";  // нужно, не удалять
            $index = array_search(App::getLocale(), config('app.locales'));
            if ($index !== false) {   // '!==' использовать, '!=' не использовать
                $name = 'name_lang_' . $index;
            }

            // список items по выбранному child_base_id
            $result_child_base_items = Item::select(['id', 'base_id', 'name_lang_0', 'name_lang_1', 'name_lang_2', 'name_lang_3'])
                ->where('base_id', $link->child_base_id)->where('project_id', $project->id)->orderBy($name)
                ->get();
            foreach ($result_child_base_items as $item) {
                //$item->name() - для быстрого получения $item->name()
                $result_child_base_items_options = $result_child_base_items_options . "<option value='" . $item->id . "'>" . $item->name() . "</option>";
            }

            // список items по выбранному parent_base_id
            $base_right = GlobalController::base_right($link->parent_base, $role, $relit_id);

            // если это фильтрируемое поле - то, тогда загружать весь список не нужно
            //$link_exists = Link::where('parent_is_child_related', true)->where('parent_child_related_start_link_id', $link->id)->exists();
            //if ($link_exists == false || $link_exists == null) {
            //if ($link_exists == true) {
            // 1.0 В списке выбора использовать поле вычисляемой таблицы
            if ($link->parent_is_in_the_selection_list_use_the_calculated_table_field == true) {
                $set = Set::findOrFail($link->parent_selection_calculated_table_set_id);
                $set_link = $set->link_to;
                // Получаем список из вычисляемой таблицы
                $result_parent_base_items = Item::select(DB::Raw('items.*'))
                    ->join('mains', 'items.id', '=', 'mains.parent_item_id')
                    ->where('mains.link_id', '=', $set_link->id);

                //->orderBy('items.' . $name);

                //                             ->where('items.project_id', $project->id)

//                    1.1 В списке выбора использовать дополнительное связанное поле вычисляемой таблицы
                if ($link->parent_is_use_selection_calculated_table_link_id_0 == true) {
                    $link_id = $link->parent_selection_calculated_table_link_id_0;
                    // Получаем данные из обычной таблицы(невычисляемой) + фильтр проверки наличия в вычисляемой таблице
                    // Список 'items.*' формируется из 'mains.parent_item_id'
                    // Связь с вычисляемой таблицей - 'joinSub($result_parent_base_items, 'items_start', function ($join) {
                    //                                $join->on('mains.child_item_id', '=', 'items_start.id')'
                    $result_parent_base_items = Item::select(DB::Raw('items.*'))
                        ->join('mains', 'items.id', '=', 'mains.parent_item_id')
                        ->joinSub($result_parent_base_items, 'items_start', function ($join) {
                            $join->on('mains.child_item_id', '=', 'items_start.id');
                        })
                        ->where('mains.link_id', '=', $link_id)
                        ->distinct()
                        ->orderBy('items.' . $name);

                    //                             ->where('items.project_id', $project->id)

//                        1.2 В списке выбора использовать два дополнительных связанных поля вычисляемой таблицы
                    if ($link->parent_is_use_selection_calculated_table_link_id_1 == true) {
                        $link_id = $link->parent_selection_calculated_table_link_id_1;
                        // Получаем данные из обычной таблицы(невычисляемой) + фильтр проверки наличия в вычисляемой таблице
                        // Список 'items.*' формируется из 'mains.parent_item_id'
                        // Связь с таблицей-результатом предыдущего запроса - 'joinSub($result_parent_base_items, 'items_start', function ($join) {
                        //                                $join->on('mains.child_item_id', '=', 'items_start.id')'
                        $result_parent_base_items = Item::select(DB::Raw('items.*'))
                            ->join('mains', 'items.id', '=', 'mains.parent_item_id')
                            ->joinSub($result_parent_base_items, 'items_start', function ($join) {
                                $join->on('mains.child_item_id', '=', 'items_start.id');
                            })
                            ->where('mains.link_id', '=', $link_id)
                            ->distinct()
                            ->orderBy('items.' . $name);

                        //                             ->where('items.project_id', $project->id)
                    }
                }
                // Загрузить список $items
            } else {
                $result_parent_base_items = Item::select(['id', 'code', 'base_id', 'name_lang_0', 'name_lang_1', 'name_lang_2', 'name_lang_3', 'created_user_id'])
                    ->where('base_id', $link->parent_base_id)
                    ->where('project_id', $project->id);
//                        ->orderBy($name);
            }
            // Такая же проверка и в GlobalController (function items_right(), items_check_right()),
            // в ItemController (function next_all_links_mains_calc(), browser(), get_items_for_link(), get_items_ext_edit_for_link())
            if ($base_right['is_list_base_user_id'] == true) {
                $result_parent_base_items = $result_parent_base_items->where('id', GlobalController::glo_user_id());
            }
            if ($base_right['is_list_base_byuser'] == true) {
                $result_parent_base_items = $result_parent_base_items->where('created_user_id', GlobalController::glo_user_id());
            }
            $result_parent_base_items_no_get = $result_parent_base_items;
            // '->get()' нужно
            $result_parent_base_items = $result_parent_base_items->get();
            foreach ($result_parent_base_items as $item) {
                $result_parent_base_items_options = $result_parent_base_items_options . "<option value='" . $item->id . "'>" . $item->name() . "</option>";
            }
            //} else {

            //}
        }
        return [
            'result_parent_label' => $result_parent_label,
            'result_child_base_name' => $result_child_base_name,
            'result_parent_base_name' => $result_parent_base_name,
            'result_child_base_items' => $result_child_base_items,
            'result_parent_base_items_no_get' => $result_parent_base_items_no_get,
            'result_parent_base_items' => $result_parent_base_items,
            'result_child_base_items_options' => $result_child_base_items_options,
            'result_parent_base_items_options' => $result_parent_base_items_options,
        ];
    }

// Функции get_items_for_link() и get_items_ext_edit_for_link()
// в целом похожи в части возвращаемых 'result_parent_label', 'result_parent_base_name', 'result_parent_base_items'
    static function get_items_ext_edit_for_link(Link $link, Project $project, Role $role, $relit_id)
    {
        // наименование
        $result_parent_label = $link->parent_label();
        $result_parent_base_name = $link->parent_base->name();
        $result_parent_base_items = [];
        // Такая же проверка ItemController::get_items_ext_edit_for_link(),
        // в ext_edit.php
        if ($link->parent_base->type_is_list()) {
            $name = "";  // нужно, не удалять
            $index = array_search(App::getLocale(), config('app.locales'));
            if ($index !== false) {   // '!==' использовать, '!=' не использовать
                $name = 'name_lang_' . $index;
            }
            // список items по выбранному parent_base_id
            $base_right = GlobalController::base_right($link->parent_base, $role, $relit_id);
            $result_parent_base_items = Item::select(['id', 'base_id', 'name_lang_0', 'name_lang_1', 'name_lang_2', 'name_lang_3', 'created_user_id'])->where('base_id', $link->parent_base_id)->where('project_id', $project->id)->orderBy($name);
            // Такая же проверка и в GlobalController (function items_right(), items_check_right()),
            // в ItemController (function next_all_links_mains_calc(), browser(), get_items_for_link(), get_items_ext_edit_for_link())
            if ($base_right['is_list_base_user_id'] == true) {
                $result_parent_base_items = $result_parent_base_items->where('id', GlobalController::glo_user_id());
            }
            if ($base_right['is_list_base_byuser'] == true) {
                $result_parent_base_items = $result_parent_base_items->where('created_user_id', GlobalController::glo_user_id());
            }
            $result_parent_base_items = $result_parent_base_items->get();
        }
        return [
            'result_parent_label' => $result_parent_label,
            'result_parent_base_name' => $result_parent_base_name,
            'result_parent_base_items' => $result_parent_base_items,
        ];
    }

//// Используется в ext_edit.php при фильтрации данных + данные из вычисляемых таблиц
//// $item_select - выбранное значение
//    static function get_selection_child_items_from_parent_item(Link $link, Item $item_select)
//    {
//        $result_items_no_get = null;
//        $result_items = null;
//        $result_items_name_options = null;
//        $result_parent_base_items = null;
//        $name = "";  // нужно, не удалять
//        $index = array_search(App::getLocale(), config('app.locales'));
//        if ($index !== false) {   // '!==' использовать, '!=' не использовать
//            $name = 'name_lang_' . $index;
//        }
//        // Похожие строки есть в LinkController store()/update() и в ItemController get_selection_child_items_from_parent_item()
//        // Проверка допустимого случая, если 'Фильтровать поля == true' и '1.0 В списке выбора использовать поле вычисляемой таблицы == true'
//        $link_start = Link::findOrFail($link->parent_child_related_start_link_id);
//        $link_result = Link::findOrFail($link->parent_child_related_result_link_id);
//        // 1.0 В списке выбора использовать поле вычисляемой таблицы
//        // 1.1 В списке выбора использовать дополнительное связанное поле вычисляемой таблицы
//        if ($link->parent_is_in_the_selection_list_use_the_calculated_table_field) {
//            $set = Set::findOrFail($link->parent_selection_calculated_table_set_id);
//            $set_link = $set->link_to;
//            // Получаем список из вычисляемой таблицы
//            $result_parent_base_items = Item::select(DB::Raw('items.*'))
//                ->join('mains', 'items.id', '=', 'mains.parent_item_id')
//                ->where('mains.link_id', '=', $set_link->id)
//                ->orderBy('items.' . $name);
//            $sel_error = true;
//            if ($link->parent_is_use_selection_calculated_table_link_id_0) {
//                $set = Set::findOrFail($link->parent_selection_calculated_table_set_id);
//                $link_sel_0 = Link::findOrFail($link->parent_selection_calculated_table_link_id_0);
//                // 1.1 В списке выбора использовать дополнительное связанное поле вычисляемой таблицы
//                if ($link->parent_is_use_selection_calculated_table_link_id_1 == false) {
//                    $sel_error = !(($set->link_to->parent_base_id == $link_start->parent_base_id) && ($link_sel_0->parent_base_id == $link_result->parent_base_id));
//
//                    if ($sel_error == false) {
//                        $link_id = $link->parent_selection_calculated_table_link_id_0;
//                        // '$result_child_base_items' присваивается
//                        // Получаем данные из обычной таблицы(невычисляемой) + фильтр проверки наличия в вычисляемой таблице
//                        // Список 'items.*' формируется из 'mains.child_item_id'
//                        // Фильтр используется '->where('mains.parent_item_id', '=', $item_select->id)'
//                        // Связь с вычисляемой таблицей - 'joinSub($result_parent_base_items, 'items_start', function ($join) {
//                        //                                $join->on('mains.child_item_id', '=', 'items_start.id')'
//                        //  Нужно '->join('mains', 'items.id', '=', 'mains.child_item_id')'
//                        $result_child_base_items = Item::select(DB::Raw('items.*'))
//                            ->join('mains', 'items.id', '=', 'mains.child_item_id')
//                            ->joinSub($result_parent_base_items, 'items_start', function ($join) {
//                                $join->on('mains.child_item_id', '=', 'items_start.id');
//                            })
//                            ->where('mains.link_id', '=', $link_id)
//                            ->where('mains.parent_item_id', '=', $item_select->id)
//                            ->distinct()
//                            ->orderBy('items.' . $name);;
//                    }
//
//                } //                Т.е. '$link->parent_is_use_selection_calculated_table_link_id_1 == true'
//                else {
//
//                    $link_sel_1 = Link::findOrFail($link->parent_selection_calculated_table_link_id_1);
//                    $sel_error = !(($link_sel_0->parent_base_id == $link_start->parent_base_id) && ($link_sel_1->parent_base_id == $link_result->parent_base_id));
//
//                    if ($sel_error == false) {
//                        $link_id = $link->parent_selection_calculated_table_link_id_0;
//                        // Получаем данные из обычной таблицы(невычисляемой) + фильтр проверки наличия в вычисляемой таблице
//                        // Список 'items.*' формируется из 'mains.parent_item_id'
//                        // Связь с вычисляемой таблицей - 'joinSub($result_parent_base_items, 'items_start', function ($join) {
//                        //                                $join->on('mains.child_item_id', '=', 'items_start.id')'
//                        $result_parent_base_items = Item::select(DB::Raw('items.*'))
//                            ->join('mains', 'items.id', '=', 'mains.parent_item_id')
//                            ->joinSub($result_parent_base_items, 'items_start', function ($join) {
//                                $join->on('mains.child_item_id', '=', 'items_start.id');
//                            })
//                            ->where('mains.link_id', '=', $link_id)
//                            ->distinct()
//                            ->orderBy('items.' . $name);
//
//                        //                             ->where('items.project_id', $project->id)
//
////                        1.2 В списке выбора использовать два дополнительных связанных поля вычисляемой таблицы
//                        $link_id = $link->parent_selection_calculated_table_link_id_1;
//                        // '$result_child_base_items' присваивается
//                        // Получаем данные из обычной таблицы(невычисляемой) + фильтр проверки наличия в вычисляемой таблице
//                        // Список 'items.*' формируется из 'mains.child_item_id'
//                        // Фильтр используется '->where('mains.parent_item_id', '=', $item_select->id)'
//                        // Связь с вычисляемой таблицей - 'joinSub($result_parent_base_items, 'items_start', function ($join) {
//                        //                                $join->on('mains.child_item_id', '=', 'items_start.id')'
//                        //  Нужно '->join('mains', 'items.id', '=', 'mains.child_item_id')'
//
//                        $result_child_base_items = Item::select(DB::Raw('items.*'))
//                            ->join('mains', 'items.id', '=', 'mains.child_item_id')
//                            ->joinSub($result_parent_base_items, 'items_start', function ($join) {
//                                $join->on('mains.child_item_id', '=', 'items_start.id');
//                            })
//                            ->where('mains.link_id', '=', $link_id)
//                            ->where('mains.parent_item_id', '=', $item_select->id)
//                            ->distinct()
//                            ->orderBy('items.' . $name);
//
//                    }
//                }
//            }
//        }
//
//        $result_items_no_get = $result_child_base_items;
//        // '->get()' нужно
//        $result_items = $result_child_base_items->get();
//
//        if ($result_items) {
//            $result_items_name_options = "";
//            foreach ($result_items as $item) {
//                $result_items_name_options = $result_items_name_options . "<option value='" . $item->id . "'>" . $item->name() . "</option>";
//            }
//        } else {
//            $result_items_name_options = "<option value='0'>" . trans('main.no_information') . "!</option>";
//        }
//
//        return ['result_items_no_get' => $result_items_no_get,
//            'result_items' => $result_items,
//            'result_items_name_options' => $result_items_name_options];
//    }

//// Используется в ext_edit.php при обычной фильтрации данных
//    static function get_child_items_from_parent_item(Base $base_start, Item $item_start, Link $link)
//    {
//        $link_result = Link::find($link->parent_child_related_result_link_id);
//        $result_items = null;
//        $result_items_name_options = null;
//        $cn = 0;
//        $error = false;
//        $link = null;
//        $mains = null;
//        $items_parent = null;
//        $items_child = null;
//        // список links - маршрутов до поиска нужного объекта
//        $links = BaseController::get_array_bases_tree_routes($base_start->id, $link_result->id, false);
//        if ($links) {
//            $items_parent = array();
//            // добавление элемента в конец массива
//            array_unshift($items_parent, $item_start->id);
//            $cn = 0;
//            $error = false;
//            foreach ($links as $link_value) {
//                $cn = $cn + 1;
//                $link = Link::find($link_value);
//                if (!$link) {
//                    $error = true;
//                    break;
//                }
//                // обнуление массива $items_child
//                $items_child = array();
//                foreach ($items_parent as $item_id) {
//                    // $item используется в цикле
//                    $mains = Main::select(['child_item_id'])
//                        ->where('parent_item_id', $item_id)->where('link_id', $link->id)->get();
//                    if (!$mains) {
//                        $error = true;
//                        break;
//                    }
//                    foreach ($mains as $main) {
//                        // добавление элемента в конец массива
//                        array_unshift($items_child, $main->child_item_id);
//                    }
//                }
//                $items_parent = $items_child;
//            }
//        }
//        if (!$error) {
//            // проверки "цикл прошел по всем элементам до конца";
//            if (count($links) == $cn) {
//                $result_items = $items_child;
//                if ($items_child) {
//                    $result_items_name_options = "";
//                    if (!$base_start->is_required_lst_num_str_txt_img_doc) {
//                        $result_items_name_options = "<option value='0'>" . GlobalController::option_empty() . "</option>";
//                    }
//                    $selected = false;
//                    foreach ($items_child as $item_id) {
//                        $item = Item::find($item_id);
//                        if ($item) {
////                            $result_items_name_options = $result_items_name_options . "<option value='" . $item_id . "'>" . $item->name() . "</option>";
//                            $result_items_name_options = $result_items_name_options . "<option value='" . $item_id;
//                            if ($selected) {
//                                $result_items_name_options = $result_items_name_options . " selected ";
//                            }
//                            $result_items_name_options = $result_items_name_options . "'>" . $item->name() . "</option>";
//                        }
//                    }
//                    //$result_items_name_options = $result_items_name_options . "<option value='0'>" . trans('main.no_information') . "!</option>";
//                } else {
//                    if (!$base_start->is_required_lst_num_str_txt_img_doc) {
//                        $result_items_name_options = "<option value='0'>" . GlobalController::option_empty() . "</option>";
//                    } else {
//                        $result_items_name_options = "<option value='0'>" . trans('main.no_information') . "!</option>";
//                    }
//                }
//            }
//        }
//        // }
//        return ['result_items' => $result_items,
//            'result_items_name_options' => $result_items_name_options];
//    }

    static function get_parent_item_from_child_item(Item $item_start, Link $link_result)
    {
        $result_item = null;
        $result_item_name = null;
        $result_item_name_options = null;
        $item = $item_start;
        $cn = 0;
        $error = false;
        $link = null;
        $link_work = null;
        $main = null;
        // список links - маршрутов до поиска нужного объекта
        $links = BaseController::get_array_bases_tree_routes($item_start->base_id, $link_result->id, true);
        if ($links) {
            $cn = 0;
            $error = false;
            foreach ($links as $value) {
                $cn = $cn + 1;
                $link = Link::find($value);
                if (!$link) {
                    $error = true;
                    break;
                }
                $link_work = $link;
                // первый элемент списка mains по выбранному child_base_id и link_id
                // $item используется в цикле
                $main = Main::select(['id', 'child_item_id', 'parent_item_id', 'link_id'])
                    ->where('child_item_id', $item->id)->where('link_id', $link->id)->get()->first();
                if (!$main) {
                    $error = true;
                    break;
                }
                $item = Item::find($main->parent_item_id);
                if (!$item) {
                    $error = true;
                    break;
                }
            }
            if (!$error) {
                // проверки "цикл прошел по всем элементам до конца" и конечный найденный $link_work == необходимому $link_result;
                if ((count($links) == $cn) && ($link_work == $link_result)) {
                    $result_item = $item;
                    $result_item_name = $item->name();
                    $result_item_name_options = "<option value='" . $item->id . "'>" . $item->name() . "</option>";
                }
            }
        }
        return ['result_item' => $result_item,
            'result_item_name' => $result_item_name,
            'result_item_name_options' => $result_item_name_options];
    }

// Функция get_parent_item_from_calc_child_item() ищет вычисляемое поля от первого невычисляемого
// в форме item/ext_edit.php
// Например: значение вычисляемого (через "Бабушка со стороны матери") "Прабабушка со стороны матери" находится от значение поля "Мать",
// т.е. не зависит от промежуточных значений ("Бабушка со стороны матери")
    static function get_parent_item_from_calc_child_item(Item $item_start, Link $link_result, $item_calc, Role $role = null, $relit_id = null)
    {
        $result_item = null;
        $result_item_id = null;
        $result_item_name = null;
        $result_item_name_options = null;
        // проверка, если link - вычисляемое поле
//        if($link_result->id == 339){
//            dd($link_result);
//        }
        if ($link_result->parent_is_parent_related == true) {

            // Не использовать - не работает при сложных связях: Например: Товар-ЕдиницаИзмерения-Цвет
            // ----------------------------------------
            // Вставка нового алгоритма
            // Вычисляем первоначальный $item;
//            $item = null;
//            if ($item_calc == true) {
//                // Поиск item-start (например: в заказе - поиск товара)
//                $item = GlobalController::get_parent_item_from_main($item_start->id, $link_result->parent_parent_related_start_link_id);
//            } else {
//                $item = $item_start;
//            }
//            if ($item) {
//                // Поиск item-result (например: в товаре - поиск наименования)
//                $item = GlobalController::get_parent_item_from_main($item->id, $link_result->parent_parent_related_result_link_id);
//                if ($item) {
//                    $result_item = $item;
//                    $result_item_id = $item->id;
//                    if ($item->base->type_is_image() || $item->base->type_is_document()) {
//                        //$result_item_name = "<a href='" . Storage::url($item->filename()) . "'><img src='" . Storage::url($item->filename()) . "' height='50' alt='' title='" . $item->filename() . "'></a>";
//                        if ($item->base->type_is_image()) {
//                            $result_item_name = "<img src='" . Storage::url($item->filename()) . "' height='250' alt='' title='" . $item->title_img() . "'>";
//                        } else {
//                            $result_item_name = "<a href='" . Storage::url($item->filename()) . "'><img src='" . Storage::url($item->filename()) . "' height='50' alt='' title='" . $item->filename() . "'></a>";
//                        }
//                    } elseif ($item->base->type_is_text()) {
//                        $result_item_name = GlobalController::it_txnm_n2b($item);
//                    } else {
//                        // $numcat = false - не выводить числовых поля с разрядом тысячи/миллионы/миллиарды
//                        $result_item_name = $item->name();
//                    }
//                    $result_item_name_options = "<option value='" . $item->id . "'>" . $item->name() . "</option>";
//                }
//            }
            // ------------------------------------------------------------

            // ------------------------------------------------------------
            // Не удалять - сложный алгоритм поиска, например, прабабушка мамы
//            if (1 == 2) {
            // возвращает маршрут $link_ids по вычисляемым полям до первого найденного постоянного link_id ($const_link_id_start)
            $rs = LinkController::get_link_ids_from_calc_link($link_result);
            $const_link_id_start = $rs['const_link_id_start'];
            $link_ids = $rs['link_ids'];
            // Вычисляем первоначальный $item;
            if ($item_calc == true) {
                $item = GlobalController::get_parent_item_from_main($item_start->id, $const_link_id_start);
                if ($item) {
                    if ($role) {
//                    Использовать так '$relit_id!=null'
                        if ($relit_id != null) {
                            // Проверка $item_find
                            $item = GlobalController::items_check_right($item, $role, $relit_id);
                        }
                    }
                }
            } else {
                $item = $item_start;
            }
            if ($item) {
                if ($const_link_id_start && $link_ids) {
                    $error = false;
                    // цикл по вычисляемым полям
                    foreach (@$link_ids as $link_id) {
                        $link_find = Link::find($link_id);
                        if (!$link_find) {
                            $error = true;
                            break;
                        }
                        $link_find = Link::find($link_find->parent_parent_related_result_link_id);
                        if (!$link_find) {
                            $error = true;
                            break;
                        }
                        // используется поле link->parent_parent_related_result_link_id
                        // находим новый $item (невычисляемый)
                        // $item меняется внутри цикла
                        $item = self::get_parent_item_from_child_item($item, $link_find)['result_item'];
                        if (!$item) {
                            $error = true;
                            break;
                        }
                    }
                    // Похожие строки в self::get_parent_item_from_calc_child_item()
                    // и в self::get_parent_item_from_output_calculated_table()
                    if (!$error && $item) {
                        $result_item = $item;
                        $result_item_id = $item->id;
                        if ($item->base->type_is_image() || $item->base->type_is_document()) {
                            //$result_item_name = "<a href='" . Storage::url($item->filename()) . "'><img src='" . Storage::url($item->filename()) . "' height='50' alt='' title='" . $item->filename() . "'></a>";
                            if ($item->base->type_is_image()) {
                                //$result_item_name = "<img src='" . Storage::url($item->filename()) . "' height='250' alt='' title='" . $item->title_img() . "'>";
                                $result_item_name = GlobalController::view_img($item, "medium", false, false, false, $item->title_img());
                            } else {
                                $result_item_name = GlobalController::view_doc($item, GlobalController::usercode_calc());
                            }
                        } elseif ($item->base->type_is_text()) {
                            $result_item_name = GlobalController::it_txnm_n2b($item);
                        } else {
                            // $numcat = false - не выводить числовых поля с разрядом тысячи/миллионы/миллиарды
                            $result_item_name = $item->name();
                        }
                        $result_item_name_options = "<option value='" . $item->id . "'>" . $item->name() . "</option>";
                    }
                }
            }
            //}
            // --------------------------------------------------------------
        }

        return ['result_item' => $result_item,
            'result_item_id' => $result_item_id,
            'result_item_name' => $result_item_name,
            'result_item_name_options' => $result_item_name_options];
    }

    static function form_parent_coll_hier($item_id, Project $project, Role $role, $relit_id)
    {
        $item = Item::find($item_id);
        $items = array();
        $result = self::form_parent_hier_coll_start($items, $item_id, $project, $relit_id, 0, $role);
        if ($result != "") {
            $kod = 0;
            $result = '<a data-toggle="collapse" href="#collapse' . $kod . '">' . trans('main.ancestors') . '</br>' .
                '' . '</a>' .
                '<span id="collapse' . $kod . '" class="collapse in">' . $result . '</span>' .
                '<hr>';
        }
        return $result . '';
    }

// $items нужно - чтобы не было бесконечного цикла
//static function form_parent_coll_hier_start($items, $item_id, $project, $level, $role)   - можно использовать так
//static function form_parent_coll_hier_start(&$items, $item_id, $project, $level, $role)  - и так - результаты разные
    static function form_parent_hier_coll_start(&$items, $item_id, Project $project, $relit_id, $level, Role $role)
    {
        $result = '';
        $level = $level + 1;
        $item = Item::findOrFail($item_id);
        $base = Base::findOrFail($item->base_id);
        $base_right = GlobalController::base_right($base, $role, $relit_id);
        if ($base_right['is_hier_base_enable'] == true) {
            $mains = Main::all()->where('child_item_id', $item_id)->sortBy(function ($row) {
                return $row->link->parent_base_number;
            });
            if (count($mains) == 0) {
                return '';
            }
            if (!(array_search($item_id, $items) === false)) {
                return '';
            }
            $items[count($items)] = $item_id;
            foreach ($mains as $main) {
                $str = '';
                $link = Link::findOrFail($main->link_id);
                // Вычисляет $relit_id
//                $calc_link_relit_id = GlobalController::calc_link_relit_id($link, $role, $relit_id);
//                $base_link_right = GlobalController::base_link_right($link, $role, $calc_link_relit_id);
                $base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
                if ($base_link_right['is_hier_link_enable'] == true) {
                    $str = self::form_parent_hier_coll_start($items, $main->parent_item_id, $level, $role);
                    $alink = '';
                    if ($base_link_right['is_list_base_calc'] == true) {
                        $alink = '<a href="' . route('item.ext_show', ['item' => $main->parent_item_id, 'project' => $project, 'role' => $role, 'usercode' => GlobalController::usercode_calc(), 'relit_id' => GlobalController::set_relit_id($relit_id)]) . '" title="' .
                            $main->parent_item->name() . '">...</a>';
                    }
                    $img_doc = '';
                    if ($link->parent_base->type_is_image()) {
                        $img_doc = GlobalController::view_img($main->parent_item, "small", false, true, false, "");
                    } elseif ($link->parent_base->type_is_document()) {
                        $img_doc = GlobalController::view_doc($main->parent_item, GlobalController::usercode_calc());
                    }
                    if ($str == '') {
                        $result = $result . '<li>';
                        if ($img_doc != '') {
                            $result = $result . $main->link->parent_label() . ': ' . '<b>' . $img_doc . '</b>';
                        } else {
                            $result = $result . $main->link->parent_label() . ': ' . '<b>' . $main->parent_item->name() . '</b>' . $alink;
                        }
                        $result = $result . '</li>';
                    } else {
                        $kod = $main->parent_item_id;
                        $result = $result . '<li><span id="collapse' . $kod . '" class="collapse in">' . $str . '</span>
                                <a data-toggle="collapse" href="#collapse' . $kod . '">' . $main->link->parent_label() . ': ' . '<b>';
                        if ($img_doc != '') {
                            $result = $result . $img_doc . '</b>';
                        } else {
                            $result = $result . $main->parent_item->name() . '</b>' . $alink;
                        }
                        $result = $result . '</a></li>';
                    }
                }
            }
            if ($result != '') {
                $result = '<ul type="circle">' . $result . '</ul>';
                if ($level > 1) {
                    $result = '<div class="card">' . $result . '</div>';
                }
            }
        }
        return $result;
    }

// $level_one = true, т.е. получить простые родительские поля один первый уровень
// $level_one = false, т.е. получить связанные(со вложенными значениями) родительские поля один первый уровень, на остальных уровнях показать простые и связанные поля
    static function form_parent_deta_hier($item_id, Project $project, Role $role, $relit_id, $level_one)
    {
        $item = Item::find($item_id);
        $items = array();
        $result = self::form_parent_hier_deta_start($items, $item_id, $project, $relit_id, 0, $role, $level_one);
        if ($result != '') {
            //$kod = 0 . $level_one;
            if ($level_one == false) {
//                $result = '<a data-toggle="collapse" href="#collapse' . $kod . '">' . trans('main.ancestors') . '</br>' .
//                    '' . '</a>' .
//                    '<span id="collapse' . $kod . '" class="collapse in">' . $result . '</span>' .
//                    '<hr>';
                $result = trans('main.ancestors') . ':<br>' . $result . '<hr>';
            }
        }
        return $result;
    }

// $items нужно - чтобы не было бесконечного цикла
//static function form_parent_hier_deta_start($items, $item_id, $project, $relit_id, $level, $role, $level_one)   - можно использовать так
//static function form_parent_hier_deta_start(&$items, $item_id, $project, $relit_id, $level, $role, $level_one)  - и так - результаты разные
    static function form_parent_hier_deta_start(&$items, $item_id, Project $project, $relit_id, $level, Role $role, $level_one)
    {
        $result = '';
        $level = $level + 1;
        $item = Item::findOrFail($item_id);
        $base = Base::findOrFail($item->base_id);
        $base_right = GlobalController::base_right($base, $role, $relit_id);
        if ($base_right['is_hier_base_enable'] == true) {
            $mains = Main::all()->where('child_item_id', $item_id)->sortBy(function ($row) {
                return $row->link->parent_base_number;
            });
            if (count($mains) == 0) {
                return '';
            }
            if (!(array_search($item_id, $items) === false)) {
                return '';
            }
            if ($level_one == true && ($level > 1)) {
                return '';
            }
            $items[count($items)] = $item_id;
            foreach ($mains as $main) {
                $str = '';
                $link = Link::findOrFail($main->link_id);
                // Вычисляет $relit_id
//                $calc_link_relit_id = GlobalController::calc_link_relit_id($link, $role, $relit_id);
//                $base_link_right = GlobalController::base_link_right($link, $role, $calc_link_relit_id);
                $base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
                if ($base_link_right['is_hier_link_enable'] == true) {
                    // Получить $str - вложенные родительские значения
                    //$str = self::form_parent_hier_deta_start($items, $main->parent_item_id, $project, $relit_id, $level, $role, $level_one);
                    $str = self::form_parent_hier_deta_start($items, $main->parent_item_id, $project,
                        $base_link_right['base_rel_id'], $level, $role, $level_one);
                    $alink = '';
                    if ($base_link_right['is_list_base_calc'] == true) {
//                        $alink = '<a href="' . route('item.ext_show', ['item' => $main->parent_item_id, 'project' => $project, 'role' => $role, 'usercode' => GlobalController::usercode_calc(), 'relit_id' => GlobalController::set_relit_id($relit_id)]) . '" title="' .
//                            $main->parent_item->name() . '">...</a>';
                        $alink = '<a href="' . route('item.ext_show', ['item' => $main->parent_item_id,
                                'project' => $project, 'role' => $role, 'usercode' => GlobalController::usercode_calc(),
                                'relit_id' => $base_link_right['base_rel_id']]) . '" title="' .
                            $main->parent_item->name() . '">...</a>';
                    }
                    $img_doc = '';
                    if ($link->parent_base->type_is_image()) {
                        $img_doc = GlobalController::view_img($main->parent_item, "small", false, true, false, "");
                    } elseif ($link->parent_base->type_is_document()) {
                        $img_doc = GlobalController::view_doc($main->parent_item, GlobalController::usercode_calc());
                    }

                    // $link_exists = false, поле $main->parent_item->base_id простое
                    // $link_exists = true, поле $main->parent_item->base_id связанное
                    // Например у Человека/Инструкции простые поля: Фамилия, Имя, Отчество, Дата рождения, Пол, Национальность, Наименование, Документ
                    // связанные поля: Родители, Папка
                    $link_exists = Link::where('child_base_id', $main->parent_item->base_id)->exists();

//                  if (!($level_one == true && ($link_exists))) {
                    if ($level_one == false || !$link_exists) {
                        if ($str == '') {
                            //if (!($level_one == false && $level == 1)) {
                            if ($level_one == true || $level > 1) {
                                $result = $result . '<li>';
                                if ($img_doc != '') {
                                    $result = $result . $main->link->parent_label() . ': ' . '<b>' . $img_doc . '</b>';
                                } else {
                                    $result = $result . $main->link->parent_label() . ': ' . '<b>' . $main->parent_item->name() . '</b>' . $alink;
                                }
                                $result = $result . '</li>';
                            }
                        } else {
                            $result = $result . '<li><details><summary>' . $main->link->parent_label() . ': ' . '<b>';
                            if ($img_doc != '') {
                                $result = $result . $img_doc . '</b>';
                            } else {
                                $result = $result . $main->parent_item->name() . '</b> ' . $alink;
                            }
                            $result = $result . '</summary>' . $str . '</details></li>';
                        }
                    }
                }
            }
            if ($result != '') {
                $result = '<ul type="circle">' . $result . "</ul>";
            }
        }
        return $result;
    }

    //static function form_child_deta_hier(Item $item, Project $project, Role $role, $relit_id, $view_ret_id)
    static function form_child_deta_hier(Item $item, Project $project, Role $role, $relit_id)
    {
        $items = array();
        $result = self::form_child_hier_deta_start($items, $item->id, $project, $relit_id, 0, $role);
        if ($result != '') {
            $result = trans('main.descendants') . ':<br>' . $result . '<hr>';
        }
        return $result;
    }

// $items нужно - чтобы не было бесконечного цикла
//static function form_child_hier_deta_start($items, $item_id, $project, $relit_id, $view_ret_id, $level, $role)   - можно использовать так
//static function form_child_hier_deta_start(&$items, $item_id, $project, $relit_id, $view_ret_id, $level, $role)  - и так - результаты разные
// '$items' и '$items_dop' использовать для того, чтобы записи, отображаемые на экране, были уникальными (см.ниже)
    static function form_child_hier_deta_start(&$items, $item_id, Project $project, $relit_id, $level, Role $role)
    {
        $result = '';
        $level = $level + 1;
        $item = Item::findOrFail($item_id);
        $base = Base::findOrFail($item->base_id);
        $base_right = GlobalController::base_right($base, $role, $relit_id);
        if ($base_right['is_hier_base_enable'] == true) {
            $array_link_relips = self::calc_array_link_relips($project);
//            $mains = Main::all()->where('parent_item_id', $item_id)->sortBy(function ($row) {
//                return $row->child_item->name();
//            });
//            $mains = Main::all()
//                ->where('parent_item_id', $item_id)
//                ->where('parent_item_id', $item_id)
//                ->whereHas('child_item', function ($query) use ($array_link_relips) {
//                    $query->whereIn('project_id', $array_link_relips);
//                })
//                ->sortBy(function ($row) {
//                    return $row->child_item()->name();
//                });
            $name = "";  // нужно, не удалять
            $index = array_search(App::getLocale(), config('app.locales'));
            if ($index !== false) {   // '!==' использовать, '!=' не использовать
                $name = 'name_lang_' . $index;
            }
            $mains = Main::
            join('items', 'mains.child_item_id', '=', 'items.id')
                ->where('parent_item_id', $item_id)
                ->whereIn('items.project_id', $array_link_relips)
                ->orderBy('items.' . $name)
                ->get();

            if (count($mains) == 0) {
                return '';
            }
            if (!(array_search($item_id, $items) === false)) {
                return '';
            }
            $items[count($items)] = $item_id;

            foreach ($mains as $main) {
                $str = '';
                $link = Link::findOrFail($main->link_id);
                // Вычисляет $relit_id
//                $calc_link_relit_id = GlobalController::calc_link_relit_id($link, $role, $relit_id);
//                $base_link_right = GlobalController::base_link_right($link, $role, $calc_link_relit_id, true, $calc_link_relit_id);
                $base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
                // Найти $relit_child_id
                $relit_child_id = array_search($main->child_item->project_id, $array_link_relips);

                // '!($relit_child_id===false)' нужно, см. https://www.php.net/manual/ru/function.array-search.php
                if (!($relit_child_id === false) & $base_link_right['is_hier_link_enable'] == true) {
                    // Получить $str - вложенные детские значения
                    //$str = self::form_child_hier_deta_start($items, $main->child_item_id, $project, $relit_id, $view_ret_id, $level, $role);
                    $str = self::form_child_hier_deta_start($items, $main->child_item_id, $project, $relit_child_id, $level, $role);
                    $alink = '';
                    if ($base_link_right['is_list_base_calc'] == true) {
                        $alink = '<a href="' . route('item.ext_show', ['item' => $main->child_item_id,
                                'project' => $project, 'role' => $role, 'usercode' => GlobalController::usercode_calc(),
                                'relit_id' => $relit_child_id]) . '" title="' .
                            $main->child_item->name() . '">...</a>';
                    }
                    $img_doc = '';
                    if ($link->child_base->type_is_image()) {
                        $img_doc = GlobalController::view_img($main->child_item, "small", false, true, false, "");
                    } elseif ($link->child_base->type_is_document()) {
                        $img_doc = GlobalController::view_doc($main->child_item, GlobalController::usercode_calc());
                    }
                    $items_dop = array();
                    // '$items' и '$items_dop' использовать для того, чтобы записи, отображаемые на экране, были уникальными
                    if ($str == '') {
                        // '$items' использовать
                        // 'level_one = true' используется
                        // получить простые родительские поля один первый уровень
//                        $str = self::form_parent_hier_deta_start($items, $main->child_item_id, $project,
//                            $relit_id, 0, $role, true);
                        $str = self::form_parent_hier_deta_start($items, $main->child_item_id, $project,
                            $relit_child_id, 0, $role, true);


                    } else {
                        // '$items_dop' использовать
                        // 'level_one = true' используется
                        // получить простые родительские поля один первый уровень
                        // '. $str' используется
                        $str = self::form_parent_hier_deta_start($items_dop, $main->child_item_id, $project, $relit_id, 0, $role, true) . $str;
                    }
                    if ($str == '') {
                        $result = $result . '<li>';
                        if ($img_doc != '') {
                            $result = $result . $main->link->child_label() . ': ' . '<b>' . $img_doc . '</b>';
                        } else {
                            $result = $result . $main->link->child_label() . ': ' . '<b>' . $main->child_item->name() . '</b>' . $alink;
                        }
                        $result = $result . '</li>';
                    } else {
                        $result = $result . '<li><details><summary>' . $main->link->child_label() . ': ' . '<b>';
                        if ($img_doc != '') {
                            $result = $result . $img_doc . '</b>';
                        } else {
                            $result = $result . $main->child_item->name() . '</b> ' . $alink;
                        }
                        $result = $result . '</summary>' . $str . '</details>' . '</li>';
                    }
                }
            }
            if ($result != '') {
                $result = '<ul type="circle">' . $result . "</ul>";
            }
        }
        return $result;
    }

// Функция calc_value_func() вычисляет наименования для записи $item
    function calc_value_func(Item $item, $level = 0, $first_run = true)
    {
        // Эта функция только для base с вычисляемым наименованием
        if ($item->base->is_calcname_lst == false) {
            return null;
        }
        $level = $level + 1;

        $array_calc = self::get_array_calc_edit($item)['array_calc'];
        $item_find = null;
        $item_result = null;
        $result_func = null;
        $calc_lang_0 = "";
        $calc_lang_1 = "";
        $calc_lang_2 = "";
        $calc_lang_3 = "";
        $is_required_second = false;
        // При первой итерации цикла равно "", в последующих итерациях равно " "
        $space = "";
        // по циклу значений mains
        foreach ($array_calc as $key => $value) {
            $next = false;
            $link = Link::find($key);
            // Эта строка "$item_result = null;" нужна
            $item_result = null;
            if ($link) {
                // если поле входит в состав вычисляемого составного поля / Для вычисляемого наименования
                if ($link->parent_is_calcname == true) {
                    // $first_run = false запускается только для однородных значений (например: ФизЛицо имеет поле Мать(ФизЛицо), Отец(ФизЛицо))
                    if (($first_run == true) ||
                        (($first_run == false)
                            && (($item->base->is_same_small_calcname == false)
                                || ($item->base->is_same_small_calcname == true) && ($link->parent_is_small_calcname == true)))) {
                        if ($value == null) {
                            // Проверка на вычисляемые поля / Автоматически заполнять из родительского поля ввода
                            if ($link->parent_is_parent_related == true) {
                                $const_link_id_start = LinkController::get_link_ids_from_calc_link($link)['const_link_id_start'];
                                $link_parent = Link::find($link->parent_parent_related_start_link_id);
                                if ($link_parent) {
                                    // Если существует такой индекс в массиве
                                    if (array_key_exists($const_link_id_start, $array_calc)) {
                                        $item_find = Item::find($array_calc[$const_link_id_start]);
                                        if ($item_find) {
                                            // Функция get_parent_item_from_calc_child_item() ищет вычисляемое поля от первого невычисляемого
                                            // Например: значение вычисляемого (через "Бабушка со стороны матери") "Прабабушка со стороны матери" находится от значение поля "Мать",
                                            // т.е. не зависит от промежуточных значений ("Бабушка со стороны матери")
                                            $result_func = self::get_parent_item_from_calc_child_item($item_find, $link, false);
                                            // Сохранить значение в массиве
                                            $array_calc[$link->id] = $result_func['result_item_id'];
                                            $item_result = $result_func['result_item'];
                                        }
                                    }
                                }
                            }
                        } else {
                            $item_result = Item::find($value);
                        }
                    }
                }
                if ($item_result) {
                    $dop_name_0 = "";
                    $dop_name_1 = "";
                    $dop_name_2 = "";
                    $dop_name_3 = "";
                    if ($item->base_id == $item_result->base_id) {

                        // Не удалять
//                        if ($level == 1) {

                        // всего два запуска этой функции (основной и этот), только для однородных значений (например: ФизЛицо имеет поле Мать(ФизЛицо), Отец(ФизЛицо))
                        $rs = $this->calc_value_func($item_result, $level, false);
                        $dop_name_0 = $rs['calc_lang_0'] == "" ? "" : $item->base->sepa_same_left_calcname . $rs['calc_lang_0'] . $item->base->sepa_same_right_calcname;
                        $dop_name_1 = $rs['calc_lang_1'] == "" ? "" : $item->base->sepa_same_left_calcname . $rs['calc_lang_1'] . $item->base->sepa_same_right_calcname;
                        $dop_name_2 = $rs['calc_lang_2'] == "" ? "" : $item->base->sepa_same_left_calcname . $rs['calc_lang_2'] . $item->base->sepa_same_right_calcname;
                        $dop_name_3 = $rs['calc_lang_3'] == "" ? "" : $item->base->sepa_same_left_calcname . $rs['calc_lang_3'] . $item->base->sepa_same_right_calcname;

//                        } else {
//                            continue;

                        //$res_names = $item_result->names();
//                            $dop_name_0 = $res_names[0];
//                            $dop_name_1 = $res_names[1];
//                            $dop_name_2 = $res_names[2];
//                            $dop_name_3 = $res_names[3];
//                            $dop_name_0 = "";
//                            $dop_name_1 = "";
//                            $dop_name_2 = "";
//                            $dop_name_3 = "";

                        //}

                    } else {
                        $res_names = $item_result->names();
                        $dop_name_0 = $res_names[0];
                        $dop_name_1 = $res_names[1];
                        $dop_name_2 = $res_names[2];
                        $dop_name_3 = $res_names[3];
                    }
                    $dop_name_0 = trim($dop_name_0);
                    $dop_name_1 = trim($dop_name_1);
                    $dop_name_2 = trim($dop_name_2);
                    $dop_name_3 = trim($dop_name_3);
                    if (!($dop_name_0 == "" && $dop_name_1 == "" && $dop_name_2 == "" && $dop_name_3 == "")) {
                        // $item->base->sepa_calcname - символ разделения для вычисляемых полей
                        // "\~" - символ перевода каретки (используется также в Item.php: name() nmbr())
                        // "\~" - символ перевода каретки (используется также в ItemController.php: calc_value_func(), GlobalController: itnm_left)
                        //$sc = trim($item->base->sepa_calcname) . "\~";
                        $sc = trim($item->base->sepa_calcname);
//                        $dop_sepa0 = $calc_lang_0 == "" ? "" : $sc . " ";
//                        $dop_sepa1 = $calc_lang_1 == "" ? "" : $sc . " ";
//                        $dop_sepa2 = $calc_lang_2 == "" ? "" : $sc . " ";
//                        $dop_sepa3 = $calc_lang_3 == "" ? "" : $sc . " ";
                        $dop_sepa0 = $calc_lang_0 == "" ? "" : $sc;
                        $dop_sepa1 = $calc_lang_1 == "" ? "" : $sc;
                        $dop_sepa2 = $calc_lang_2 == "" ? "" : $sc;
                        $dop_sepa3 = $calc_lang_3 == "" ? "" : $sc;

//Лучше без пробела ("Цена = 15000" на одной строке может быть "Цена =", на второй "15000"; а если "Цена=15000" всегда выходит на одной строке, т.к. это одно слово)
//                        $left_str0 = $link->parent_is_left_calcname_lang_0 == true ? $link->parent_calcname_prefix_lang_0 . " " : "";
//                        $left_str1 = $link->parent_is_left_calcname_lang_1 == true ? $link->parent_calcname_prefix_lang_1 . " " : "";
//                        $left_str2 = $link->parent_is_left_calcname_lang_2 == true ? $link->parent_calcname_prefix_lang_2 . " " : "";
//                        $left_str3 = $link->parent_is_left_calcname_lang_3 == true ? $link->parent_calcname_prefix_lang_3 . " " : "";
//                        $right_str0 = $link->parent_is_left_calcname_lang_0 == false ? " " . $link->parent_calcname_prefix_lang_0 : "";
//                        $right_str1 = $link->parent_is_left_calcname_lang_1 == false ? " " . $link->parent_calcname_prefix_lang_1 : "";
//                        $right_str2 = $link->parent_is_left_calcname_lang_2 == false ? " " . $link->parent_calcname_prefix_lang_2 : "";
//                        $right_str3 = $link->parent_is_left_calcname_lang_3 == false ? " " . $link->parent_calcname_prefix_lang_3 : "";

//                        $left_str0 = $link->parent_is_left_calcname_lang_0 == true ? $link->parent_calcname_prefix_lang_0 . " " : "";
//                        $left_str1 = $link->parent_is_left_calcname_lang_1 == true ? $link->parent_calcname_prefix_lang_1 . " " : "";
//                        $left_str2 = $link->parent_is_left_calcname_lang_2 == true ? $link->parent_calcname_prefix_lang_2 . " " : "";
//                        $left_str3 = $link->parent_is_left_calcname_lang_3 == true ? $link->parent_calcname_prefix_lang_3 . " " : "";
//                        $right_str0 = $link->parent_is_left_calcname_lang_0 == false ? " " . $link->parent_calcname_prefix_lang_0 : "";
//                        $right_str1 = $link->parent_is_left_calcname_lang_1 == false ? " " . $link->parent_calcname_prefix_lang_1 : "";
//                        $right_str2 = $link->parent_is_left_calcname_lang_2 == false ? " " . $link->parent_calcname_prefix_lang_2 : "";
//                        $right_str3 = $link->parent_is_left_calcname_lang_3 == false ? " " . $link->parent_calcname_prefix_lang_3 : "";
                        $left_str0 = $link->parent_is_left_calcname_lang_0 == true ? $link->parent_calcname_prefix_lang_0 : "";
                        $left_str1 = $link->parent_is_left_calcname_lang_1 == true ? $link->parent_calcname_prefix_lang_1 : "";
                        $left_str2 = $link->parent_is_left_calcname_lang_2 == true ? $link->parent_calcname_prefix_lang_2 : "";
                        $left_str3 = $link->parent_is_left_calcname_lang_3 == true ? $link->parent_calcname_prefix_lang_3 : "";
                        $right_str0 = $link->parent_is_left_calcname_lang_0 == false ? $link->parent_calcname_prefix_lang_0 : "";
                        $right_str1 = $link->parent_is_left_calcname_lang_1 == false ? $link->parent_calcname_prefix_lang_1 : "";
                        $right_str2 = $link->parent_is_left_calcname_lang_2 == false ? $link->parent_calcname_prefix_lang_2 : "";
                        $right_str3 = $link->parent_is_left_calcname_lang_3 == false ? $link->parent_calcname_prefix_lang_3 : "";

//                        $calc_lang_0 = $calc_lang_0 . ($dop_name_0 == "" ? "" : $dop_sepa0 . $left_str0) . $dop_name_0 . ($dop_name_0 == "" ? "" : $right_str0);
//                        $calc_lang_1 = $calc_lang_1 . ($dop_name_1 == "" ? "" : $dop_sepa1 . $left_str1) . $dop_name_1 . ($dop_name_1 == "" ? "" : $right_str1);
//                        $calc_lang_2 = $calc_lang_2 . ($dop_name_2 == "" ? "" : $dop_sepa2 . $left_str2) . $dop_name_2 . ($dop_name_2 == "" ? "" : $right_str2);
//                        $calc_lang_3 = $calc_lang_3 . ($dop_name_3 == "" ? "" : $dop_sepa3 . $left_str3) . $dop_name_3 . ($dop_name_3 == "" ? "" : $right_str3);

                        $calc_lang_0 = $calc_lang_0 . ($dop_name_0 == "" ? "" : $dop_sepa0 . $space . $left_str0) . $dop_name_0 . ($dop_name_0 == "" ? "" : $right_str0);
                        $calc_lang_1 = $calc_lang_1 . ($dop_name_1 == "" ? "" : $dop_sepa1 . $space . $left_str1) . $dop_name_1 . ($dop_name_1 == "" ? "" : $right_str1);
                        $calc_lang_2 = $calc_lang_2 . ($dop_name_2 == "" ? "" : $dop_sepa2 . $space . $left_str2) . $dop_name_2 . ($dop_name_2 == "" ? "" : $right_str2);
                        $calc_lang_3 = $calc_lang_3 . ($dop_name_3 == "" ? "" : $dop_sepa3 . $space . $left_str3) . $dop_name_3 . ($dop_name_3 == "" ? "" : $right_str3);
                        $space = " ";
                    }
                }
            }
        }
        //full
        $calc_full_lang_0 = $calc_lang_0;
        $calc_full_lang_1 = $calc_lang_1;
        $calc_full_lang_2 = $calc_lang_2;
        $calc_full_lang_3 = $calc_lang_3;

//        // меняем и возвращаем $item
//        // 1000 - макс.размер строковых полей name_lang_x в items
//        $calc_lang_0 = mb_substr($calc_lang_0, 0, 1000);
//        $calc_lang_1 = mb_substr($calc_lang_1, 0, 1000);
//        $calc_lang_2 = mb_substr($calc_lang_2, 0, 1000);
//        $calc_lang_3 = mb_substr($calc_lang_3, 0, 1000);
        // меняем и возвращаем $item
        // 255 - макс.размер строковых полей name_lang_x в items
        $calc_lang_0 = GlobalController::itnm_left($calc_lang_0);
        $calc_lang_1 = GlobalController::itnm_left($calc_lang_1);
        $calc_lang_2 = GlobalController::itnm_left($calc_lang_2);
        $calc_lang_3 = GlobalController::itnm_left($calc_lang_3);

        return ['calc_full_lang_0' => $calc_full_lang_0, 'calc_full_lang_1' => $calc_full_lang_1,
            'calc_full_lang_2' => $calc_full_lang_2, 'calc_full_lang_3' => $calc_full_lang_3,
            'calc_lang_0' => $calc_lang_0, 'calc_lang_1' => $calc_lang_1, 'calc_lang_2' => $calc_lang_2, 'calc_lang_3' => $calc_lang_3];
    }

    // Перерасчет $items по переданным $base, $project
    function calculate_names(Base $base, Project $project)
    {
        // "->get()" нужно
        $items = Item::where('base_id', $base->id)->where('project_id', $project->id)
            ->get();

        $rs = false;
        foreach ($items as $item) {
            $rs = $this->calc_value_func($item);
            $item->name_lang_0 = $rs['calc_lang_0'];
            $item->name_lang_1 = $rs['calc_lang_1'];
            $item->name_lang_2 = $rs['calc_lang_2'];
            $item->name_lang_3 = $rs['calc_lang_3'];
            $item->save();
        }
        return redirect()->back();
    }

    // Перерасчет $items по переданным $item по всем проектам
    function calc_item_names(Item $item)
    {
        //->join('items', 'mains.child_item_id', '=', 'items.id')
        //->where('items.base_id', '!=', $item->base_id)
        $items_ids = Main::select(DB::Raw('mains.child_item_id as id'))
            ->join('links', 'mains.link_id', '=', 'links.id')
            ->where('mains.parent_item_id', '=', $item->id)
            ->where('links.parent_is_calcname', '=', true);

        // "->get()" нужно
        $items = Item::joinSub($items_ids, 'items_ids', function ($join) {
            $join->on('items.id', '=', 'items_ids.id');
        })
            ->get();

        $rs = false;
        foreach ($items as $item) {
            $rs = $this->calc_value_func($item);
            $item->name_lang_0 = $rs['calc_lang_0'];
            $item->name_lang_1 = $rs['calc_lang_1'];
            $item->name_lang_2 = $rs['calc_lang_2'];
            $item->name_lang_3 = $rs['calc_lang_3'];
            $item->save();
            // Рекурсивный вызов для изменения вычисляемого наименования во вложенных записях, нужно
            self::calc_item_names($item);
        }
    }

    function calculate_new_code(Base $base, Project $project)
    {
        $result = 0;
        if ($project) {
            // Если предложить код при добавлении записи
            if ($base->is_suggest_code == true) {
                //Список, отсортированный по коду
//          $items = Item::where('base_id', $base->id)->orderBy('code')->get();
                $items = Item::all()->where('base_id', $base->id)->where('project_id', $project->id)
                    ->sortBy(function ($row) {
                        return $row->code;
                    })->toArray();
                if ($items == null) {
                    $result = 1;
                } else {
                    // Предложить код по максимальному значению, иначе - по первому свободному значению
                    if ($base->is_suggest_max_code == true) {
                        //$result = strval($items[count($items) - 1]->code) + 1;
                        $result = strval($items[array_key_last($items)]['code']) + 1;
                    } else {
                        $i = 0;
                        // Эта строка нужна
                        $result = count($items) + 1;
                        foreach ($items as $key => $item) {
                            $i = $i + 1;
                            if (strval($item['code']) != $i) {
                                $result = $i;
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    function calculate_new_seqnum(Project $project, Link $link, Item $parent_item = null, Link $par_ln = null)
    {
        $result = 0;
        // "      if ($parent_item & $par_ln)",
        // т.е. расчет значения проходит для body-таблиц формы item_index.php
        // при добавлении записи в base_table.php - значение 0, значение не вычисляется
        if ($link->parent_base->type_is_number == true
            & $link->parent_is_seqnum == true) {
            if ($parent_item) {
                if ($par_ln) {
                    if ($link->parent_seqnum_link_id != 0) {
                        if ($link->parent_seqnum_link_id == $par_ln->id) {
                            // "->get()" нужно
                            $mains = Main::select(['mains.*'])
                                ->where('mains.parent_item_id', $parent_item->id)
                                ->where('link_id', $link->parent_seqnum_link_id)
                                ->get();
                            // Блок похожих строк в этой функции
                            foreach ($mains as $main) {
                                $item_find = GlobalController::view_info($main->child_item_id, $link->id);
                                if ($item_find) {
                                    $numval = $item_find->numval();
                                    if ($numval['result'] == true) {
                                        // $numval['int_vl'] - целая часть числа
                                        $value = $numval['int_vl'];
                                        if ($value > $result) {
                                            $result = $value;
                                        }
                                    }
                                }
                            }
                            $result = $result + 1;
                        }
                    }
                }
            } else {
                if ($link->parent_seqnum_link_id == 0) {
                    // "->get()" нужно
                    $mains = Main::select(['mains.*'])
                        ->join('items', 'mains.child_item_id', '=', 'items.id')
                        ->where('items.project_id', $project->id)
                        ->where('link_id', $link->id)
                        ->get();
                    // Блок похожих строк в этой функции
                    foreach ($mains as $main) {
                        $item_find = Item::find($main->parent_item_id);
                        if ($item_find) {
                            $numval = $item_find->numval();
                            if ($numval['result'] == true) {
                                // $numval['int_vl'] - целая часть числа
                                $value = $numval['int_vl'];
                                if ($value > $result) {
                                    $result = $value;
                                }
                            }
                        }
                    }
                    $result = $result + 1;
                }
            }
        }
        return $result;
    }

// Перерасчет кодов
    function recalculation_codes(Base $base, Project $project)
    {
        // Сортировка по наименованиею "->orderBy('name_lang_0')"
        $items = Item::where('base_id', $base->id)->where('project_id', $project->id)->orderBy('name_lang_0')->get();
        // Чтобы не было ошибки уникальность кода "items:base_id, project_id, code" нарушена
        $i = 0;
        foreach ($items as $item) {
            $i = $i + 1;
            $item->code = -$i;
            $item->save();
        }
        // Непосредственно расчет и присвоение новых кодов
        $i = 0;
        foreach ($items as $item) {
            $i = $i + 1;
            $item->code = $i;
            $item->code_add_zeros();
            $item->save();
        }
        return redirect()->back();
    }

// Заполнение признака "Ссылка на основу"
    function verify_baselink(Base $base, Project $project)
    {
        $items = Item::where('base_id', $base->id)->where('project_id', $project->id)->get();
        // Т.е. 'child_base_id' = 'parent_base_id'
        // parent_is_base_link
        $links = Link::where('child_base_id', $base->id)->where('parent_base_id', $base->id)->get();
        $i = 0;
        foreach ($items as $item) {
            foreach ($links as $link) {
                $i = $i + 1;
                $main = Main::where('child_item_id', $item->id)->where('link_id', $link->id)->first();
                if (!$main) {
                    // Создание записи в mains "Ссылка на основу"
                    $main = new Main();
                    $main->link_id = $link->id;
                    $main->child_item_id = $item->id;
                    $main->created_user_id = Auth::user()->id;
                }
                $main->parent_item_id = $item->id;
                $main->updated_user_id = Auth::user()->id;
                $main->save();
            }
        }
        $result = trans('main.processed') . " " . $i . " " . mb_strtolower(trans('main.records')) . ".";
        return view('message', ['message' => $result]);
    }

// Проверка хранения чисел
    function verify_number_values()
    {
        // Выбрать только числовые $items
        // В базе данных должно храниться с нулем впереди для правильной сортировки
        $items = Item::whereHas('base', function ($query) {
            $query->where('type_is_number', true);
        })->get();
        foreach ($items as $item) {
            foreach (config('app.locales') as $key => $value) {
                $value = GlobalController::restore_number_from_item($item->base, $item['name_lang_0']);
                $item['name_lang_' . $key] = GlobalController::save_number_to_item($item->base, $value);
            }
            $item->save();
        }
        $result = trans('main.processed') . " " . count($items) . " " . mb_strtolower(trans('main.records')) . ".";
        return view('message', ['message' => $result]);
    }

// Проверка таблицы Тексты
    function verify_table_texts()
    {
        $result = trans('main.deleted') . ' text.ids: ';
        $i = 0;
        $texts = Text::get();
        foreach ($texts as $text) {
            $item = Item::find($text->item_id);
            $delete = false;
            // Если найдено
            if ($item) {
                if (!$item->base->type_is_text()) {
                    $delete = true;
                }
                // Если не найдено
            } else {
                $delete = true;
            }
            if ($delete) {
                $text->delete();
                if ($i > 0) {
                    $result = $result . ", ";
                }
                $result = $result . $text->id;
                $i = $i + 1;
            }
        }
        $result = $result . " - " . $i . " " . mb_strtolower(trans('main.recs_genitive_case')) . ".";
        return view('message', ['message' => $result]);
    }

    function item_from_base_code(Base $base, Project $project, $code)
    {
        $item_id = 0;
        $item_name = trans('main.no_information') . '!';
        $item = Item::where('project_id', $project->id)->where('base_id', $base->id)->where('code', $code)->get()->first();
        if ($item != null) {
            $item_id = $item->id;
            $item_name = $item->name();
        }
        return ['item_id' => $item_id, 'item_name' => $item_name];
    }

    function filesize_message($fs, $mx)
    {
        return trans('main.size_selected_file') . ' (' . $fs . ' ' . mb_strtolower(trans('main.byte')) . ') '
            . mb_strtolower(trans('main.must_less_equal')) . ' (' . $mx . ' ' . mb_strtolower(trans('main.byte')) . ') !';
    }

    static function links_info(Base $base, Role $role, $relit_id, Item $item = null, Link $nolink = null,
                                    $item_heading_base = false, $tree_array = [], $child_mains_link_is_calcname = null)
    {
        $base_right = GlobalController::base_right($base, $role, $relit_id);
        $link_id_array = array();
        $link_base_relit_id_array = array();
        $link_base_right_array = array();
        $matrix = array(array());
        // Нужно
        $links = null;
        // Если передано $item
        if ($item) {
//          В $links_values попадают фактические записи, не попадают связанные и вычисляемые связи
//          Выборка из mains
            $links_ids = Main::select(DB::Raw('mains.link_id'))
                ->where('child_item_id', '=', $item->id);
            // Нужно "                    ->where(function ($query) {
            //                        $query->where('parent_is_parent_related', '=', false)
            //                            ->Where('parent_is_output_calculated_table_field', '=', false);
            //                    });"
            $links_values = Link::joinSub($links_ids, 'links_ids', function ($join) {
                $join->on('links.id', '=', 'links_ids.link_id')
                    ->where(function ($query) {
                        $query->where('parent_is_parent_related', '=', false)
                            ->where('parent_is_output_calculated_table_field', '=', false);
                    });
            });
//          $links = $base->child_links->(where('parent_is_parent_related', '=', true)
//                        ->orWhere('parent_is_output_calculated_table_field', '=', true));
//            связанные и вычисляемые связи
            $links_reca_ids = Link::select(DB::Raw('links.id as link_id'))
                ->where('child_base_id', '=', $base->id)
                ->where(function ($query) {
                    $query->where('parent_is_parent_related', '=', true)
                        ->orWhere('parent_is_output_calculated_table_field', '=', true);
                });
            $links_reca = Link::joinSub($links_reca_ids, 'links_reca_ids', function ($join) {
                $join->on('links.id', '=', 'links_reca_ids.link_id');
            });

            // Объединение
            // '$links = $links_values->union($links_reca);' - так тоже работает
            // '->get()' нужно
            $links = $links_values->unionall($links_reca)->get();
            // Если $item не передано вычисление идет по $base
        } else {
            $links = $base->child_links;
        }
        //        Если тип-вычисляемое наименование и Показывать Основу с вычисляемым наименованием
        //        или если тип-не вычисляемое наименование
        // или показывать в заголовке item_index.php
//        if (GlobalController::is_base_calcname_check($base, $base_right) || $item_heading_base == true) {
//            // Исключить links с признаком 'Для вычисляемого наименования'
//            $links = $links->where('parent_is_calcname', '=', false);
//        }

        // Исключить links из переданного массива $child_mains_link_is_calcname
//        if ($child_mains_link_is_calcname) {
////            Нужно 'foreach($child_mains_link_is_calcname as $calcname_mains)'
//            foreach ($child_mains_link_is_calcname as $calcname_mains) {
//                foreach ($calcname_mains as $calcname_main) {
//                    $links = $links->where('id', '!=', $calcname_main->link_id);
//                }
//            }
//        }

        // Исключить links из переданного массива $tree_array
        if (count($tree_array) > 0) {
            foreach ($tree_array as $value) {
                $links = $links->where('id', '!=', $value['link_id']);
            }
        }

        // Не удалять
        // Исключить link->parent_base_id из переданного массива $tree_array
        if (count($tree_array) > 0) {
            foreach ($tree_array as $value) {
                $links = $links->where('parent_base_id', '!=', $value['base_id']);
            }
        }

        if ($nolink != null) {
            // При параллельной связи $nolink ($nolink->parent_is_parallel == true)
            // другие паралельные связи не доступны при отображении списка в Пространство-тело таблицы
            // (если передано $nolink)
            if ($nolink->parent_is_parallel == true) {
                // Исключить child_links с параллельными связями
                $links = $links->where('parent_is_parallel', '!=', true);
            } else {
                // Исключить переданный $nolink
                $links = $links->where('id', '!=', $nolink->id);
            }
        }

        // Проверка на "$base_link_right['is_list_link_enable']"
        foreach ($links as $link) {
            //$base_link_right = GlobalController::base_link_right($link, $role, $relit_id, true, $relit_id);
            // Вычисляет $relit_id
//            $calc_link_relit_id = GlobalController::calc_link_relit_id($link, $role, $relit_id);
//            $base_link_right = GlobalController::base_link_right($link, $role, $calc_link_relit_id);
            $base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
            if ($base_link_right['is_list_link_enable'] == true) {
                $base_right = GlobalController::base_right($link->child_base, $role, $relit_id);
                if (GlobalController::is_base_calcname_check($link->child_base, $base_right) == true) {
                    // Исключить links с признаком 'Для вычисляемого наименования'
                    if ($link->parent_is_calcname == true) {
                        $links = $links->where('id', '!=', $link->id);
                    }
                }
            } else {
                $links = $links->where('id', '!=', $link->id);
            }
        }
        // Исключить связанные записи по текущей связи (($link->parent_is_parent_related == true) && ($link->parent_parent_related_start_link_id == $nolink->id))
        // Выполняется последним, после блока
        // "            if ($base_link_right['is_list_link_enable'] == false) {
        //                $links = $links->where('id', '!=', $link->id);
        //            }"
        $link_related_array = array();
        if ($base_right['is_exclude_related_records'] == true) {
            if ($nolink != null) {
                // Нужно: если в $body
                if ($item_heading_base == false) {
                    foreach ($links as $link) {
                        if (($link->parent_is_parent_related == true) && ($link->parent_parent_related_start_link_id == $nolink->id)) {
                            $link_related_array[] = $link->id;
                        }
                    }
                    $links = $links->whereNotIn('id', $link_related_array);
                }
            }
        }

        $links = $links->sortBy('parent_base_number');

        $k = 0;
        foreach ($links as $link) {
            //$base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
            // Вычисляет $relit_id
            //$calc_link_relit_id = GlobalController::calc_link_relit_id($link, $role, $relit_id);
            //$base_link_right = GlobalController::base_link_right($link, $role, $link->parent_relit_id);
            // Права
            //$base_link_right = GlobalController::base_link_right($link, $role, $calc_link_relit_id);
            $base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
            //if ($base_link_right['is_list_link_enable'] == true) {
            //$is_list_base_calc = $base_link_right['is_list_base_calc'];
            $is_list_base_calc = $base_link_right['is_bsmn_base_enable'];
            $link_id_array[] = $link->id;
            $link_base_relit_id_array[$link->id] = $base_link_right['base_rel_id'];
            $link_base_right_array[$link->id] = $base_link_right;
            // 0-ая строка с link->id
            $matrix[0][$k] = ['parent_level_id' => null, 'link_id' => $link->id, 'work_field' => null,
                'work_link' => null, 'is_list_base_calc' => $is_list_base_calc, 'fin_link' => null, 'view_field' => null, 'view_name' => '', 'colspan' => 0, 'rowspan' => 0];
            // строки с уровнями
            $matrix[1][$k] = ['parent_level_id' => $link->parent_level_id_0, 'link_id' => $link->id, 'work_field' => null,
                'work_link' => null, 'is_list_base_calc' => $is_list_base_calc, 'fin_link' => null, 'view_field' => null, 'view_name' => '', 'colspan' => 0, 'rowspan' => 0];
            $matrix[2][$k] = ['parent_level_id' => $link->parent_level_id_1, 'link_id' => $link->id, 'work_field' => null,
                'work_link' => null, 'is_list_base_calc' => $is_list_base_calc, 'fin_link' => null, 'view_field' => null, 'view_name' => '', 'colspan' => 0, 'rowspan' => 0];
            $matrix[3][$k] = ['parent_level_id' => $link->parent_level_id_2, 'link_id' => $link->id, 'work_field' => null,
                'work_link' => null, 'is_list_base_calc' => $is_list_base_calc, 'fin_link' => null, 'view_field' => null, 'view_name' => '', 'colspan' => 0, 'rowspan' => 0];
            $matrix[4][$k] = ['parent_level_id' => $link->parent_level_id_3, 'link_id' => $link->id, 'work_field' => null,
                'work_link' => null, 'is_list_base_calc' => $is_list_base_calc, 'fin_link' => null, 'view_field' => null, 'view_name' => '', 'colspan' => 0, 'rowspan' => 0];
            $k = $k + 1;
            //}
        }

// 0-ая строка с link->id + 4 строки с уровнями
        $rows = 5;
        $cols = $k;

        $error_message = "";

// Заполнение $matrix[$i][$j]['work_field'] и $matrix[$i][$j]['work_link']
// 0-ая строка с link->id
        $i = 0;
        for ($j = 0; $j < $cols; $j++) {
            $matrix[$i][$j]['work_field'] = 'link' . $matrix[$i][$j]['link_id'];
            $matrix[$i][$j]['work_link'] = true;
        }
// Сколько строк полностью заполнено
// $rowmax - максимальная строка
        $rowmax = 0;  // "$rowmax = 0;" нужно, как минимум одна 0-ая строка есть
// "$i = 1" - начинать с 1-ой строки, т.к. 0-ая заполнена link->id
        for ($i = 1; $i < $rows; $i++) {
            $k = 0;
            for ($j = 0; $j < $cols; $j++) {
                if ($matrix[$i][$j]['parent_level_id'] != null) {
                    $matrix[$i][$j]['work_field'] = 'level' . $matrix[$i][$j]['parent_level_id'];
                    $matrix[$i][$j]['work_link'] = false;
                    $k = $k + 1;
                }
            }
            // Есть хотя бы одна заполненная ячейка в строке
            if ($k > 0) {
                for ($j = 0; $j < $cols; $j++) {
                    // Если в links->parent_level_id_x не заполнено, то тогда в эту ячейку выводится link->id (точнее link->parent_label())
                    if ($matrix[$i][$j]['parent_level_id'] == null) {
                        $matrix[$i][$j]['work_field'] = 'link' . $matrix[$i][$j]['link_id'];
                        $matrix[$i][$j]['work_link'] = true;
                    }
                }
                $rowmax = $i;
            }
        }

// "$i = 1" - начинать с 1-ой строки, т.к. 0-ая заполнена link->id
        for ($i = 1; $i < $rowmax; $i++) {
            for ($j = 0; $j < $cols; $j++) {
                if ($matrix[$i][$j]['work_field'] == null) {
                    $error_message = trans('main.levels_row_is_not_populated_in_settings')
                        . ' (' . mb_strtolower(trans('main.level')) . '_' . ($i - 1) . ')!';
                    break;
                }
            }
        }

// Если нет ошибки и есть строки для вывода
        if ($error_message == '') {
            // "$rows = $rowmax + 1;" нужно
            $rows = $rowmax + 1;

            // Цикл расчета 'colspan'
            for ($i = 0; $i < $rows; $i++) {
                $k = 0;
                for ($j = 0; $j < $cols; $j++) {
                    if ($matrix[$i][$j]['work_field'] != $matrix[$i][$k]['work_field']) {
                        $k = $j;
                    }
                    $matrix[$i][$k]['colspan'] = $matrix[$i][$k]['colspan'] + 1;
                }
            }

            // Цикл расчета 'rowspan'
            for ($j = 0; $j < $cols; $j++) {
                $k = $rowmax;
                for ($i = $rowmax; $i >= 0; $i--) {
                    // Проверка '$matrix[$i][$j]['colspan'] != $matrix[$k][$j]['colspan']' нужна,
                    // признак того, что выше этой ячейки подниматься не следует
                    // даже при равенстве '$matrix[$i][$j]['parent_level_id'] = $matrix[$k][$j]['parent_level_id']'
                    if ($matrix[$i][$j]['work_field'] != $matrix[$k][$j]['work_field']
                        || $matrix[$i][$j]['colspan'] != $matrix[$k][$j]['colspan']) {
                        $k = $i;
                    }
                    $matrix[$k][$j]['rowspan'] = $matrix[$k][$j]['rowspan'] + 1;
                }
            }

            // Цикл заполнения $matrix[$i][$j]['view_field'] и $matrix[$i][$j]['view_name']
            for ($i = 0; $i < $rows; $i++) {
                for ($j = 0; $j < $cols; $j++) {
                    if ($matrix[$i][$j]['colspan'] != 0 && $matrix[$i][$j]['rowspan'] != 0) {
                        $matrix[$i][$j]['view_field'] = $matrix[$i][$j]['work_field'];
                        if ($matrix[$i][$j]['work_link'] == true) {
                            $link_id = $matrix[$i][$j]['link_id'];
                            $link = Link::findOrFail($link_id);
                            $matrix[$i][$j]['view_name'] = $link->parent_label();
                            // '$matrix[$i][$j]['fin_link']' = true, если есть право показывать ссылку на таблицу с заданным base
                            // Проверка на '$matrix[$i][$j]['fin_link']' используется  в base_index.php
                            $matrix[$i][$j]['fin_link'] = $matrix[$i][$j]['is_list_base_calc'];
                        } else {
                            $level_id = $matrix[$i][$j]['parent_level_id'];
                            $level = Level::findOrFail($level_id);
                            $matrix[$i][$j]['view_name'] = $level->name();
                            // Присвоить '$matrix[$i][$j]['fin_link']' = false, т.к. $matrix[$i][$j]['work_link'] == false
                            // Проверка на '$matrix[$i][$j]['fin_link']' используется  в base_index.php
                            $matrix[$i][$j]['fin_link'] = false;
                        }
                    }
                }
            }

        } else {
            // Нужно, не удалять
            // Для '<th rowspan="{{$rows + 1}}">' в item/base_index.php при выводе в "шапке" столбцов №, кода и наименования
            $rows = 0;
            $cols = 0;
        }
        return ['link_id_array' => $link_id_array,
            'link_base_relit_id_array' => $link_base_relit_id_array, 'link_base_right_array' => $link_base_right_array,
            'matrix' => $matrix, 'rows' => $rows, 'cols' => $cols, 'error_message' => $error_message];
    }

// Список полей, для вывода вычисляемого наименования в заголовке item_index.php
// например 'содержимое документа состоит из склада, номенклатурного номера, цены'
    static function mains_link_is_calcname(Item $item, Role $role, $relit_id, $tree_array = [])
    {
        $base = $item->base;
        $base_right = GlobalController::base_right($base, $role, $relit_id);
        $mains = Main::where('child_item_id', '=', $item->id);
        //        Если не тип-вычисляемое наименование и Показывать Основу с вычисляемым наименованием
        //        или если тип-не вычисляемое наименование
        if (!GlobalController::is_base_calcname_check($base, $base_right)) {
            // Оставить links с признаком 'Для вычисляемого наименования'
            $mains = $mains->whereHas('link', function ($query) {
                $query->where('parent_is_calcname', true);
            });
        }
        // Нужно '$mains = $mains->get();', иначе - тело цикла ниже не прорабатывается ни разу
        //$mains = $mains->get();
        foreach ($mains as $main) {
            // Вычисляет $relit_id
//            $calc_link_relit_id = GlobalController::calc_link_relit_id($main->link, $role, $relit_id);
//            $base_link_right = GlobalController::base_link_right($main->link, $role, $calc_link_relit_id);
            $base_link_right = GlobalController::base_link_right($main->link, $role, $relit_id);
            if ($base_link_right['is_list_link_enable'] == false) {
                $mains = $mains->where('link_id', '!=', $main->link_id);
            }
        }
        // Исключить links из переданного массива $tree_array
        if (count($tree_array) > 0) {
            foreach ($tree_array as $value) {
                $mains = $mains->where('link_id', '!=', $value['link_id']);
            }
        }
        $mains = $mains->get()->sortBy('link.parent_base_number');

        return ['mains_link_is_calcname' => $mains];
    }

    static function get_link_refer_main(Base $base, Link $link_refer_start)
    {
        $link = Link::where('parent_is_child_related', true)
            ->where('child_base_id', $base->id)
            ->where('parent_child_related_start_link_id', $link_refer_start->id)
            ->first();
        return $link;
    }

// Выборка данных в виде списка
    static function get_items_main(Base $base, Project $project, Role $role, $relit_id, $enable_hist_records = true, Link $link = null, $item_id = null, $default_order_by = true)
    {
        // Фильтр данных
        $is_filter = false;
        // В списке использовать поля вычисляемой таблицы
        $is_calcuse = false;
        // Результат, no get()
        $items = null;
        $base_right = null;
        $items_filter = null;
        $result_parent_label = $link->parent_label();
        $result_parent_base_name = $link->parent_base->name();
        $relip_proj = GlobalController::calc_relip_project($relit_id, $project);
        if ($relip_proj) {
            if ($link) {
                $base_right = GlobalController::base_right($link->child_base, $role, $relit_id);
                // Если это фильтрируемое поле (в связка ЕдинИзмерения-Материал - поле Материал(parent_child_related_start_link_id) является фильтрируемым полем)
                // Первый вариант
                //$is_filter = Link::where('parent_is_child_related', true)->where('parent_child_related_start_link_id', $link->id)->exists();
                $is_filter = Link::where('child_base_id', $link->child_base_id)
                    ->where('parent_is_child_related', true)
                    ->where('parent_child_related_start_link_id', $link->id);
                // Этот блок не использовать
//                 //'$item_id == 0' т.е. передано null, выбранное в форме
//                if ($item_id == 0 & $base_right['is_tst_enable'] == true) {
//                    $is_filter = $is_filter->where('parent_is_tst_link', true);
//                }
                $is_filter = $is_filter->exists();

                // 1.0 В списке выбора использовать поле вычисляемой таблицы
                $is_calcuse = $link->parent_is_in_the_selection_list_use_the_calculated_table_field;
                //////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\/////////////////////////}
                // Права по base_id
                //$base_right = GlobalController::base_right($base, $role, $relit_id);
                $link_project = GlobalController::calc_link_project($link, $relip_proj, false);
                if ($link_project) {
                    $base_right = GlobalController::base_right($base, $role, $link->parent_relit_id);
                    if (($is_filter) || ($is_calcuse)) {
                        if ($is_filter) {
                            $items_filter = self::get_items_filter_main($base, $link, $base_right, $relip_proj, $item_id);
                            $items = $items_filter;
                        }
                        if ($is_calcuse) {
                            $items = self::get_items_calc_main($link);
                            if ($is_filter) {
                                // Объединение двух запросов $items_filter и $items(вычисляемые)
                                //$items = Item::select(DB::Raw('items.*'))
                                $items = Item::select(DB::Raw('items.*'))
                                    ->joinSub($items, 'items_start', function ($join) {
                                        $join->on('items.id', '=', 'items_start.id');
                                    })
                                    ->joinSub($items_filter, 'items_second', function ($join) {
                                        $join->on('items.id', '=', 'items_second.id');
                                    });
                            }
                        }
                    } else {
                        //$items = self::get_items_list_main($base, $project, $link);
                        // Используется $relip_proj
                        $items = self::get_items_list_main($base, $project, $relip_proj, $link);
                    }
                    // Такая же проверка и в GlobalController (function items_right(), items_check_right()),
                    // в ItemController (function next_all_links_mains_calc(), browser(), get_items_for_link(), get_items_ext_edit_for_link())
                    if ($base_right['is_list_base_user_id'] == true) {
                        $items = GlobalController::get_items_user_id($items);
                    }
                    if ($base_right['is_list_base_byuser'] == true) {
                        $items = $items->where('created_user_id', GlobalController::glo_user_id());
                    }
                    if ($enable_hist_records == false) {
                        $items = $items->where('is_history', false);
                    }

                    // Одинаковые строки GlobalController::items_right(), GlobalController::items_check_right() и ItemController::get_items_main()
                    // 'tst структура (main->parent_item = null, для base_index.php, item_index($link))'
                    // Важно: Для просмотра в base_index.php и item_index.php(если есть $link в исходных передаваемых параметрах)
                    // Использовать так:
                    if ($base_right['is_tst_enable'] == true) {
                        // Если выборка идет из таблицы mains, значит mains.parent_item_id есть и заполнено
                        $mains = Main::select(['mains.*'])->
                        join('items as it_ch', 'mains.child_item_id', '=', 'it_ch.id')
                            ->join('links', 'mains.link_id', '=', 'links.id')
                            ->where('it_ch.base_id', $base->id)
                            ->where('it_ch.project_id', $project->id)
                            ->where('links.parent_is_tst_link', true);

                        if ($base_right['is_list_hist_records_enable'] == false) {
                            $mains = $mains
                                ->join('items as it_pr', 'mains.parent_item_id', '=', 'it_pr.id')
                                ->where('it_pr.is_history', false);
                        }

                        // 'get()' нужно
                        $mains = $mains->get();

                        $arr_it = array();
                        foreach ($mains as $m) {
                            $arr_it[] = $m['child_item_id'];
                        }

                        $items = $items->whereNotIn('items.id', $arr_it);
                    }

                    if ($base_right['is_cus_enable'] == true) {
                        if (Auth::check()) {
                            $user_item = GlobalController::glo_user()->get_user_item();
                            if ($user_item) {
                                $mains = Main::select(['mains.*'])->
                                join('items as it_ch', 'mains.child_item_id', '=', 'it_ch.id')
                                    ->join('links', 'mains.link_id', '=', 'links.id')
                                    ->where('it_ch.base_id', $base->id)
                                    ->where('it_ch.project_id', $project->id)
                                    ->where('mains.parent_item_id', $user_item->id)
                                    ->where('links.parent_is_cus_link', true);

                                // 'get()' нужно
                                $mains = $mains->get();

                                $arr_it = array();
                                foreach ($mains as $m) {
                                    $arr_it[] = $m['child_item_id'];
                                }

                                $items = $items->whereIn('items.id', $arr_it);
                            }
                        } else {
                            $items = null;
                        }
                    }

                }
                // Сортировка не нужна, т.к. мешает сортировке по коду/наименованию в $this->browser()
                // По умолчанию, сортировка по наименованию
                // Нужно учесть для дат, сортировка не совсем корректная: 07.07.2023 08.07.2022 08.07.2023
                if ($default_order_by == true) {
                    $index = array_search(App::getLocale(), config('app.locales'));
                    if ($index !== false) {   // '!==' использовать, '!=' не использовать
                        $items = $items->orderBy('name_lang_' . $index);
                    }
                }
            }///////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\//////////////////////////////
        }
        return ['items_no_get' => $items,
            'result_parent_label' => $result_parent_label,
            'result_parent_base_name' => $result_parent_base_name];
    }

// Выборка данных в виде списка
    static function get_items_main_options(Base $base, Project $project, Role $role, $relit_id, Link $link = null, $item_id = null, $par_link_id = null, $parent_item_id = null)
    {
        $base_right = GlobalController::base_right($base, $role, $relit_id);
        $base_link_right = null;
        //$items_main = self::get_items_main($base, $project, $role, $relit_id, $base_right['is_list_hist_records_enable'], $link, $item);
        $items_main = self::get_items_main($base, $project, $role, $relit_id, $base_right['is_brow_hist_records_enable'], $link, $item_id);
        $items_no_get = $items_main['items_no_get'];

        $ing_filter = false;
        if ($items_no_get) {
            if ($link) {
                if ($par_link_id & $parent_item_id) {
                    $par_link = Link::find($par_link_id);
                    $parent_item = Item::find($parent_item_id);
//                  "if ($par_link & $parent_item)" - не использовать(дает ошибку)
                    if ($par_link && $parent_item) {
                        if ($link->id == $par_link->id) {
                            $ing_filter = true;
                            // 'items.id' использовать
                            $items_no_get = $items_no_get->where('items.id', $parent_item_id);
                        }
                    }
                }
            }
        }

        // '->get()' нужно
        $result_items = $items_no_get->get();

        $is_base_required = $base_right['is_base_required'];
        $base_link_right = null;
        if ($link) {
            $base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
            $is_base_required = $base_link_right['is_base_required'];
        }
        $result_items_name_options = "";
        if (count($result_items) > 0) {
//          Чтобы не выводить лишний раз ненужное
            if ($ing_filter == false) {
                if (!$base->is_required_lst_num_str_txt_img_doc) {
                    //          'Обязательно к заполнению (для списков, при условии $base->is_required_lst_num_str_txt_img_doc = false
                    //if (!$is_base_required) {
                    $result_items_name_options = "<option value='0'>" . GlobalController::option_empty() . "</option>";
                }
            }
            foreach ($result_items as $it) {
                $result_items_name_options = $result_items_name_options . "<option value='" . $it->id . "'>" . $it->name() . "</option>";
            }
        } else {
            if (!$base->is_required_lst_num_str_txt_img_doc) {
                //          'Обязательно к заполнению (для списков, при условии $base->is_required_lst_num_str_txt_img_doc = false
                //if (!$is_base_required) {
                $result_items_name_options = "<option value='0'>" . GlobalController::option_empty() . "</option>";
            } else {
                $result_items_name_options = "<option value='0'>" . trans('main.no_information') . "!</option>";
            }
        }
        return ['items_no_get' => $items_no_get,
            'result_parent_label' => $items_main['result_parent_label'],
            'result_parent_base_name' => $items_main['result_parent_base_name'],
            'result_items' => $result_items,
            'result_items_name_options' => $result_items_name_options];

    }

// Выборка данных в виде списка
    static function get_items_main_code($code, Base $base, Project $project, Role $role, $relit_id, Link $link = null, $item_id = null)
    {
        $base_right = GlobalController::base_right($base, $role, $relit_id);
        $items_main = self::get_items_main($base, $project, $role, $relit_id, $base_right['is_list_hist_records_enable'], $link, $item_id);
        $items_no_get = $items_main['items_no_get'];
        $item_id = 0;
        $item_name = trans('main.no_information') . '!';
        if ($items_no_get->get()) {
            $item = $items_no_get->where('items.code', $code)->first();
            if ($item != null) {
                $item_id = $item->id;
                $item_name = $item->name();
            }
        }

        return ['item_id' => $item_id, 'item_name' => $item_name];
    }

// Выборка данных без фильтра и вычисляемых
//static function get_items_list_main(Base $base, Project $project, Role $role)
    static function get_items_list_main(Base $base, Project $current_project, Project $relip_project, Link $link)
    {
        $project = null;
        //$items = null;
        // Пустой список items класса Item, как значение по умолчанию
        $items = Item::select(DB::Raw('items.*'))
            ->where('id', '_');

        // Если передано $link
        if ($link) {
            // Находим проект
            //$project = GlobalController::calc_link_project($link, $current_project);
            $project = GlobalController::calc_link_project($link, $relip_project);
        } else {
            $project = $relip_project;
        }
        if ($project) {
            // Результат, no get()
            $items = Item::select(DB::Raw('items.*'))
                ->where('items.base_id', $base->id)
                ->where('project_id', $project->id);
        }
        return $items;
    }

// Выборка данных c вычисляемыми
    static function get_items_calc_main(Link $link)
    {
        // Результат, no get()
        $items = null;

        $set = Set::findOrFail($link->parent_selection_calculated_table_set_id);
        $set_link = $set->link_to;
        // Получаем список из вычисляемой таблицы
        // "->distinct();" не нужен здесь
        $items = Item::select(DB::Raw('items.*'))
            ->join('mains', 'items.id', '=', 'mains.parent_item_id')
            ->where('mains.link_id', '=', $set_link->id);

        //->orderBy('items.' . $name);
        //                             ->where('items.project_id', $project->id)

//                    1.1 В списке выбора использовать дополнительное связанное поле вычисляемой таблицы
        if ($link->parent_is_use_selection_calculated_table_link_id_0 == true) {
            $link_id = $link->parent_selection_calculated_table_link_id_0;
            // Получаем данные из обычной таблицы(невычисляемой) + фильтр проверки наличия в вычисляемой таблице
            // Список 'items.*' формируется из 'mains.parent_item_id'
            // Связь с вычисляемой таблицей - 'joinSub($items, 'items_start', function ($join) {
            //                                $join->on('mains.child_item_id', '=', 'items_start.id')'
            $items = Item::select(DB::Raw('items.*'))
                ->join('mains', 'items.id', '=', 'mains.parent_item_id')
                ->joinSub($items, 'items_start', function ($join) {
                    $join->on('mains.child_item_id', '=', 'items_start.id');
                })
                ->where('mains.link_id', '=', $link_id)
                ->distinct();

            //->orderBy('items.' . $name);
            //                             ->where('items.project_id', $project->id)

//                        1.2 В списке выбора использовать два дополнительных связанных поля вычисляемой таблицы
            if ($link->parent_is_use_selection_calculated_table_link_id_1 == true) {
                $link_id = $link->parent_selection_calculated_table_link_id_1;
                // Получаем данные из обычной таблицы(невычисляемой) + фильтр проверки наличия в вычисляемой таблице
                // Список 'items.*' формируется из 'mains.parent_item_id'
                // Связь с таблицей-результатом предыдущего запроса - 'joinSub($items, 'items_start', function ($join) {
                //                                $join->on('mains.child_item_id', '=', 'items_start.id')'
                $items = Item::select(DB::Raw('items.*'))
                    ->join('mains', 'items.id', '=', 'mains.parent_item_id')
                    ->joinSub($items, 'items_start', function ($join) {
                        $join->on('mains.child_item_id', '=', 'items_start.id');
                    })
                    ->where('mains.link_id', '=', $link_id)
                    ->distinct();

                //->orderBy('items.' . $name);
                //                             ->where('items.project_id', $project->id)

            }
        }

        return $items;
    }

// Выборка данных с фильтром
    static function get_items_filter_main(Base $base, Link $link_filter, $base_right, Project $project, $item_id)
    {
        // Результат, no get()
        $items = null;
        // Находим $link_find - (из примера) ЕдиницуИзмерения, $link передано в функцию как Материал
        // Если это фильтрируемое поле (например: в связка ЕдинИзмерения-Материал - поле Материал(parent_child_related_start_link_id) является фильтрируемым полем)
        // Первый вариант
        //$link_find = Link::where('parent_is_child_related', true)->where('parent_child_related_start_link_id', $link_filter->id)->first();
        $link_find = Link::where('child_base_id', $link_filter->child_base_id)
            ->where('parent_is_child_related', true)
            ->where('parent_child_related_start_link_id', $link_filter->id);
        // Этот блок не использовать
//        // '$item_id == 0' т.е. передано null, выбранное в форме
//        if ($item_id == 0 & $base_right['is_tst_enable'] == true) {
//            $link_find = $link_find->where('parent_is_tst_link', true);
//        }
        $link_find = $link_find->first();

        if ($link_find) {
            $link_result = Link::find($link_find->parent_child_related_result_link_id);
            $result_items = null;
            $result_items_name_options = null;
            $cn = 0;
            $error = false;
            $link = null;
            $mains = null;
            $items_parent = null;
            $items_child = null;

            // список links - маршрутов до поиска нужного объекта
            $links = BaseController::get_array_bases_tree_routes($base->id, $link_result->id, false);
            if ($links) {
                $items_parent = array();
                // добавление элемента в конец массива
                array_unshift($items_parent, $item_id);
                $cn = 0;
                $error = false;
                foreach ($links as $link_value) {
                    $cn = $cn + 1;
                    $link = Link::find($link_value);
                    if (!$link) {
                        $error = true;
                        break;
                    }
                    // обнуление массива $items_child
                    $items_child = array();
                    foreach ($items_parent as $item_value) {
                        // $item_value нужно использовать в проверке
                        if ($item_value == 0 & $base_right['is_tst_enable'] == true
                            & $link->parent_is_tst_link == true) {
                            // для tst структуры, 'whereDoesntHave()' - не содержит
                            $items_n = Item::where('project_id', '=', $project->id)
                                ->where('base_id', '=', $base->id)
                                ->whereDoesntHave('child_mains', function ($query) use ($link) {
                                    $query->where('link_id', '=', $link->id);
                                })->get();

                            foreach ($items_n as $it) {
                                // добавление элемента в конец массива
                                array_unshift($items_child, $it->id);
                            }
                        } else {
                            $mains = Main::select(['child_item_id'])
                                ->where('parent_item_id', $item_value)->where('link_id', $link->id)->get();
                            if (!$mains) {
                                $error = true;
                                break;
                            }
                            foreach ($mains as $main) {
                                // добавление элемента в конец массива
                                array_unshift($items_child, $main->child_item_id);
                            }
                        }
                    }
                    $items_parent = $items_child;
                }
            }
            if (!$error) {
                // проверки "цикл прошел по всем элементам до конца";
                if (count($links) == $cn) {
                    $items = $items_child;

//                    $items = Item::whereIn('id', $items_child)
//                        ->orderBy(\DB::raw("FIELD(id, " . implode(',', $items_child) . ")"));
                    $items = Item::whereIn('id', $items_child);

//                    $items = Item::select(DB::Raw('items.*'))
//                        ->joinSub($items_child, 'items_start', function ($join) {
//                            $join->on('items.id', '=', 'items_start');
//                        });
                }
            }
        }
        return $items;
    }

    public
    function doc_download(Item $item, $usercode)
    {
        $user_id = GlobalController::usercode_uncalc($usercode);
        // Нужно
        $check = false;
        if ($item->base->type_is_document() == true) {
            // При авторизации
            if (Auth::check()) {
                if ($user_id == Auth::user()->id) {
                    $check = true;
                } else {
                    $check = false;
                }
                // Без авторизации
            } else {
                // Похожие строки в GlobalController::usercode_calc() и ItemController::doc_download()
                // 807 - выбранное случайное число
                if ($user_id == 807) {
                    $check = true;
                } else {
                    $check = false;
                }
            }
        } else {
            $check = false;
        }
        if ($check) {
            $file_path = $item->filename();
            return Storage::download($file_path);
        } else {
            return view('message', ['message' => trans('main.no_access')]);
        }
    }

    public
    function change_history(Item $item)
    {
        $item->change_history(true);
        //return back()->withInput();
        return back();
        //return view('message', ['message' => trans('main.is_history')]);
    }

}
