<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\Base;
use App\Models\Link;
use App\Models\Role;
use App\Models\Set;
use App\Models\Template;
use App\Rules\IsUniqueSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetController extends Controller
{
    protected function rules(Request $request)
    {
        return [
            'serial_number' => ['required', new IsUniqueSet($request)],
            'link_from_id' => ['required', new IsUniqueSet($request)],
            'link_to_id' => ['required', new IsUniqueSet($request)],
        ];
    }

    function index(Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $sets = Set::where('template_id', $template->id);
//        $name = "";  // нужно, не удалять
//        $index = array_search(App::getLocale(), config('app.locales'));
//        if ($index !== false) {   // '!==' использовать, '!=' не использовать
//            $name = 'name_lang_' . $index;
//            $sets = $sets->orderBy($name);
//        }

        // Сортировка такая одинаковая:
        // ItemController::get_item_from_parent_output_calculated_table()
        // и SetController::index()
        // влияет на обработку сортировки
        $sets = Set::select(DB::Raw('sets.*'))
            ->join('links as lf', 'sets.link_from_id', '=', 'lf.id')
            ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
            ->where('sets.template_id', $template->id)
            ->orderBy('sets.serial_number')
            ->orderBy('sets.line_number')
            ->orderBy('lf.child_base_id')
            ->orderBy('lt.child_base_id')
            ->orderBy('lf.parent_base_number')
            ->orderBy('lt.parent_base_number');

        session(['sets_previous_url' => request()->url()]);
        return view('set/index', ['template' => $template, 'sets' => $sets->paginate(60)]);
    }

    function show(Set $set)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($set->template_id);
        return view('set/show', ['type_form' => 'show', 'template' => $template, 'set' => $set]);
    }


    function create(Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $links = $this->select_links_template($template);
        return view('set/edit', ['template' => $template, 'links' => $links,
            'forwhats' => Set::get_forwhats(), 'updactions' => Set::get_updactions()]);
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

        $set = new Set($request->except('_token', '_method'));

        $this->set($request, $set);
        //https://laravel.demiart.ru/laravel-sessions/
        if ($request->session()->has('sets_previous_url')) {
            return redirect(session('sets_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function update(Request $request, Set $set)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        if (!(($set->link_from_id == $request->link_from_id) && ($set->link_to_id == $request->link_to_id))) {
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

        $set->fill($data);

        $this->set($request, $set);

        if ($request->session()->has('sets_previous_url')) {
            return redirect(session('sets_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function check(Request $request, &$array_mess)
    {
        //base_id  д.б. список
        // Не равен "Сортировка"
        if ($request->forwhat != 1) {
            // Одинаковые значения недопустимы
            if ($request->link_from_id == $request->link_to_id) {
                $message = trans('main.the_same_values_are_not_valid')
                    . ' ("' . trans('main.link_from') . '" ' . mb_strtolower(trans('main.and')) .
                    ' "' . trans('main.link_to') . '")!';;
                $array_mess['link_from_id'] = $message;
                $array_mess['link_to_id'] = $message;
                return;
            }
        }

        $link_from = Link::find($request->link_from_id);
        $link_to = Link::find($request->link_to_id);

        //Детские Основы должны быть с признаком "Вычисляемое наименование"
        if ($link_from) {
            if ($link_to) {
                if (($link_from->child_base->is_calcname_lst == false) || ($link_to->child_base->is_calcname_lst == false)) {
                    $message = trans('main.childrens_bases_must_be_with_the_characteristic_calculated_name')
                        . ' ("' . $link_from->child_base->name() . '" ' . mb_strtolower(trans('main.and')) .
                        ' "' . $link_to->child_base->name() . '")!';;
                    $array_mess['link_from_id'] = $message;
                    $array_mess['link_to_id'] = $message;
                    return;
                }
            }
        }
        // Не равен "Сортировка"
        if ($request->forwhat != 1) {
            // Детские основы не должны быть одинаковыми
            if ($link_from) {
                if ($link_to) {
                    if ($link_from->child_base_id == $link_to->child_base_id) {
                        $message = trans('main.child_bases_should_not_be_the_same')
                            . ' ("' . $link_from->child_base->name() . '" ' . mb_strtolower(trans('main.and')) .
                            ' "' . $link_to->child_base->name() . '")!';;
                        $array_mess['link_from_id'] = $message;
                        $array_mess['link_to_id'] = $message;
                        return;
                    }
                }
            }
        }
        // Родительские основы должны быть одинаковыми
        // Кроме Расчет Средний(), Расчет Количество(), Расчет Сумма()
        if (!(($request->forwhat == 3) && ($request->updaction >= 7) && ($request->updaction <= 9))) {
            if ($link_from) {
                if ($link_to) {
                    // Проверка "Ссылка на Основу" = false
                    // Для расчета количества нужна эта проверка
                    if ($link_from->parent_is_base_link == false) {
                        if ($link_from->parent_base_id != $link_to->parent_base_id) {
                            $message = trans('main.parent_bases_must_be_the_same')
                                . ' ("' . $link_from->parent_base->name() . '" ' . mb_strtolower(trans('main.and')) .
                                ' "' . $link_to->parent_base->name() . '")!';;
                            $array_mess['link_from_id'] = $message;
                            $array_mess['link_to_id'] = $message;
                            return;
                        }
                    }
                }
            }
        }
        if (1 == 2) {
            // Детская Основа должна быть с признаком "Вычисляемое"
            if ($link_from) {
                if ($link_to) {
                    if ($link_to->child_base->is_calculated_lst == false) {
                        $message = trans('main.childrens_base_must_be_with_the_characteristic_is_calculated')
                            . ' ("' . $link_to->child_base->name() . '")!';;
                        $array_mess['link_from_id'] = "";
                        $array_mess['link_to_id'] = $message;
                        return;
                    }
                }
            }
        }
        //Родительские основы должны быть Число
        // Обновление
        if ($request->forwhat == 3) {
            // Добавить, Отнять, Расчет Средний(), Расчет Сумма()
            if ((($request->updaction >= 0) && ($request->updaction <= 3)) || ($request->updaction == 7) || ($request->updaction == 9)) {
                if (($link_from->parent_base->type_is_number() == false) || ($link_to->parent_base->type_is_number() == false)) {
                    $message = trans('main.parent_bases_must_be_number')
                        . ' ("' . $link_from->parent_base->name() . '" ' . mb_strtolower(trans('main.and')) .
                        ' "' . $link_to->parent_base->name() . '")!';;
                    $array_mess['link_from_id'] = $message;
                    $array_mess['link_to_id'] = $message;
                    return;
                }
            }
        }
    }

    function set(Request $request, Set &$set)
    {
        $set->template_id = $request->template_id;
        $set->serial_number = $request->serial_number;
        $set->line_number = $request->line_number;
        $set->link_from_id = $request->link_from_id;
        $set->link_to_id = $request->link_to_id;
        // Если $set->is_savesetsenabled = false,
        // то стандартные обновления ItemController:save_sets() (и подобные функции) не проводятся
        $set->is_savesets_enabled = isset($request->is_savesets_enabled) ? true : false;

        $set->is_group = false;
        $set->is_update = false;
        $set->is_calcsort = false;
        $set->is_onlylink = false;
        $set->is_upd_pluscount = false;
        $set->is_upd_minuscount = false;
        $set->is_upd_plussum = false;
        $set->is_upd_minussum = false;
        $set->is_upd_replace = false;
        $set->is_upd_cl_gr_first = false;
        $set->is_upd_cl_gr_last = false;
        $set->is_upd_cl_fn_avg = false;
        $set->is_upd_cl_fn_count = false;
        $set->is_upd_cl_fn_sum = false;
        $set->is_upd_delete_record_with_zero_value = false;

        // Похожие строки в SetController.php (functions: store(), edit(), check())
        // и в Set.php (functions: get_types(), type(), type_name())
        // и в Set/edit.blade.php
        switch ($request->forwhat) {
            // Группировка
            case 0:
                $set->is_group = true;
                $set->is_calcsort = false;
                $set->is_onlylink = false;
                $set->is_update = false;
                $set->is_upd_pluscount = false;
                $set->is_upd_minuscount = false;
                $set->is_upd_plussum = false;
                $set->is_upd_minussum = false;
                $set->is_upd_replace = false;
                $set->is_upd_cl_gr_first = false;
                $set->is_upd_cl_gr_last = false;
                $set->is_upd_cl_fn_avg = false;
                $set->is_upd_cl_fn_count = false;
                $set->is_upd_cl_fn_sum = false;
                $set->is_upd_delete_record_with_zero_value = false;
                break;
            // Поля сортировки (для первый(), последний())
            case 1:
                $set->is_group = false;
                $set->is_calcsort = true;
                $set->is_onlylink = false;
                $set->is_update = false;
                $set->is_upd_pluscount = false;
                $set->is_upd_minuscount = false;
                $set->is_upd_plussum = false;
                $set->is_upd_minussum = false;
                $set->is_upd_replace = false;
                $set->is_upd_cl_gr_first = false;
                $set->is_upd_cl_gr_last = false;
                $set->is_upd_cl_fn_avg = false;
                $set->is_upd_cl_fn_count = false;
                $set->is_upd_cl_fn_sum = false;
                $set->is_upd_delete_record_with_zero_value = false;
                break;
            // Только связь (для вывода поля из вычисляемой Основы)
            case 2:
                $set->is_group = false;
                $set->is_calcsort = false;
                $set->is_onlylink = true;
                $set->is_update = false;
                $set->is_upd_pluscount = false;
                $set->is_upd_minuscount = false;
                $set->is_upd_plussum = false;
                $set->is_upd_minussum = false;
                $set->is_upd_replace = false;
                $set->is_upd_cl_gr_first = false;
                $set->is_upd_cl_gr_last = false;
                $set->is_upd_cl_fn_avg = false;
                $set->is_upd_cl_fn_count = false;
                $set->is_upd_cl_fn_sum = false;
                $set->is_upd_delete_record_with_zero_value = false;
                $set->is_savesets_enabled = false;
                break;
            // Обновление
            case 3:
                $set->is_group = false;
                $set->is_calcsort = false;
                $set->is_onlylink = false;
                $set->is_update = true;
                $set->is_upd_cl_fn_avg = false;
                $set->is_upd_cl_fn_count = false;
                $set->is_upd_cl_fn_sum = false;
                $set->is_upd_delete_record_with_zero_value = isset($request->is_upd_delete_record_with_zero_value) ? true : false;
                switch ($request->updaction) {
                    // Прибавить Количество
                    case 0:
                        $set->is_savesets_enabled = true;
                        $set->is_upd_pluscount = true;
                        break;
                    // Отнять Количество
                    case 1:
                        $set->is_savesets_enabled = true;
                        $set->is_upd_minuscount = true;
                        break;
                    // Прибавить Сумму
                    case 2:
                        $set->is_savesets_enabled = true;
                        $set->is_upd_plussum = true;
                        break;
                    // Отнять Сумму
                    case 3:
                        $set->is_savesets_enabled = true;
                        $set->is_upd_minussum = true;
                        break;
                    // Заменить
                    case 4:
                        $set->is_savesets_enabled = true;
                        $set->is_upd_replace = true;
                        break;
                    // Расчет Первый()
                    case 5:
                        $set->is_savesets_enabled = isset($request->is_savesets_enabled) ? true : false;
                        $set->is_upd_cl_gr_first = true;
                        break;
                    // Расчет Последний()
                    case 6:
                        $set->is_savesets_enabled = isset($request->is_savesets_enabled) ? true : false;
                        $set->is_upd_cl_gr_last = true;
                        break;
                    // Расчет Средний()
                    case 7:
                        $set->is_savesets_enabled = false;
                        $set->is_upd_cl_fn_avg = true;
                        break;
                    // Расчет Количество()
                    case 8:
                        $set->is_savesets_enabled = false;
                        $set->is_upd_cl_fn_count = true;
                        break;
                    // Расчет Сумма()
                    case 9:
                        $set->is_savesets_enabled = false;
                        $set->is_upd_cl_fn_sum = true;
                        break;
                }
                break;
        }
        $set->save();
    }

    function edit(Set $set)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($set->template_id);
        $links = $this->select_links_template($template);
        return view('set/edit', ['template' => $template, 'set' => $set, 'links' => $links,
            'forwhats' => Set::get_forwhats(), 'updactions' => Set::get_updactions()]);
    }

    function delete_question(Set $set)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($set->template_id);
        return view('set/show', ['type_form' => 'delete_question', 'template' => $template, 'set' => $set]);
    }

    function delete(Request $request, Set $set)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $set->delete();

        if ($request->session()->has('sets_previous_url')) {
            return redirect(session('sets_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function select_links_template(Template $template)
    {
// Проверка для вычисляемых полей
//        ->where('links.parent_is_parent_related', false)
        return Link::select(DB::Raw('links.*'))
            ->join('bases', 'links.child_base_id', '=', 'bases.id')
            ->where('bases.template_id', $template->id)
            ->orderBy('links.child_base_id')
            ->orderBy('links.parent_base_number')
            ->get();

    }
}
