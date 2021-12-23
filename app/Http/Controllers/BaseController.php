<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\Base;
use App\Models\Link;
use App\Models\Template;
use App\Models\Project;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BaseController extends Controller
{

    protected function rules()
    {
        // sun
//        return [
//            'name_lang_0' => ['required', 'max:255', 'unique_with: bases, name_lang_0'],
//            'names_lang_0' => ['required', 'max:255', 'unique_with: bases, names_lang_0'],
//        ];
        return [
            'name_lang_0' => ['required', 'max:255'],
            'names_lang_0' => ['required', 'max:255'],
        ];
    }

    protected function maxfilesize_rules()
    {
        return [
//            'maxfilesize_img_doc' => ['required', 'gte:50000', 'lte:1048576'],
            'maxfilesize_img_doc' => ['required', 'gte:50000', 'lte:10000000'],
            'maxfilesize_title_img_doc' => ['required', 'max:25'],
        ];
    }

    function index(Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }
        // Порядок сортировки; обычные bases, вычисляемые bases, настройки - bases, серийный номер
        $bases = Base::where('template_id', $template->id)->orderBy('is_setup_lst')->orderBy('is_calculated_lst')
            ->orderBy('serial_number');
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            switch ($index) {
                case 0:
                    //$bases = Base::all()->sortBy('names_lang_0');
                    $bases = $bases->orderBy('names_lang_0');
                    break;
                case 1:
                    //$bases = Base::all()->sortBy(function($row){return $row->names_lang_1 . $row->names_lang_0;});
                    $bases = $bases->orderBy('names_lang_1')->orderBy('names_lang_0');
                    break;
                case 2:
                    $bases = $bases->orderBy('names_lang_2')->orderBy('names_lang_0');
                    break;
                case 3:
                    $bases = $bases->orderBy('names_lang_3')->orderBy('names_lang_0');
                    break;
            }
        }
        session(['bases_previous_url' => request()->url()]);
        return view('base/index', ['template' => $template, 'bases' => $bases->paginate(60)]);
    }

    function show(Base $base)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        return view('base/show', ['type_form' => 'show', 'base' => $base]);
    }

    function create(Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        return view('base/edit', ['template' => $template, 'types' => Base::get_types()]);
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

        $base = new Base($request->except('_token', '_method'));
        $base->template_id = $request->template_id;

        $base->name_lang_0 = $request->name_lang_0;
        $base->name_lang_1 = isset($request->name_lang_1) ? $request->name_lang_1 : "";
        $base->name_lang_2 = isset($request->name_lang_2) ? $request->name_lang_2 : "";
        $base->name_lang_3 = isset($request->name_lang_3) ? $request->name_lang_3 : "";
        $base->names_lang_0 = $request->names_lang_0;
        $base->names_lang_1 = isset($request->names_lang_1) ? $request->names_lang_1 : "";
        $base->names_lang_2 = isset($request->names_lang_2) ? $request->names_lang_2 : "";
        $base->names_lang_3 = isset($request->names_lang_3) ? $request->names_lang_3 : "";

        // у этой команды два предназначения:
        // 1) заменить "on" на "1" при отмеченном checkbox
        // 2) создать новое значение "0" при выключенном checkbox
        // в базе данных информация хранится как "0" или "1"
        $base->is_code_needed = isset($request->is_code_needed) ? "1" : "0";
        $base->is_code_number = isset($request->is_code_number) ? "1" : "0";
        $base->is_limit_sign_code = isset($request->is_limit_sign_code) ? "1" : "0";
        $base->significance_code = $request->significance_code;
        $base->is_code_zeros = isset($request->is_code_zeros) ? "1" : "0";
        $base->is_suggest_code = isset($request->is_suggest_code) ? "1" : "0";
        $base->is_suggest_max_code = isset($request->is_suggest_max_code) ? "1" : "0";
        $base->is_recalc_code = isset($request->is_recalc_code) ? "1" : "0";
        $base->is_required_lst_num_str_txt_img_doc = isset($request->is_required_lst_num_str_txt_img_doc) ? "1" : "0";
        $base->is_to_moderate_image = isset($request->is_to_moderate_image) ? "1" : "0";
        $base->is_one_value_lst_str_txt = isset($request->is_one_value_lst_str_txt) ? "1" : "0";
        $base->is_calcname_lst = isset($request->is_calcname_lst) ? "1" : "0";
        $base->is_same_small_calcname = isset($request->is_same_small_calcname) ? "1" : "0";

        $base->digits_num = isset($request->digits_num) ? $request->digits_num : "0";
        $base->sepa_calcname = isset($request->sepa_calcname) ? $request->sepa_calcname : "";
        $base->sepa_same_left_calcname = isset($request->sepa_same_left_calcname) ? $request->sepa_same_left_calcname : "";
        $base->sepa_same_right_calcname = isset($request->sepa_same_left_calcname) ? $request->sepa_same_right_calcname : "";

        $base->maxfilesize_img_doc = $request->maxfilesize_img_doc;
        $base->maxfilesize_title_img_doc = isset($request->maxfilesize_title_img_doc) ? $request->maxfilesize_title_img_doc : "";

        $base->maxcount_lst = $request->maxcount_lst >= 0 ? $request->maxcount_lst : 0;
        $base->is_calculated_lst = isset($request->is_calculated_lst) ? "1" : "0";
        $base->is_setup_lst = isset($request->is_setup_lst) ? "1" : "0";
        $base->length_txt = $request->length_txt >= 0 ? $request->length_txt : 0;

        $base->serial_number = $request->serial_number;

        // Похожие строки в BaseController.php (functions: store(), edit())
        // и в Base.php (functions: get_types(), type(), type_name())
        // и в Base/edit.blade.php
        switch ($request->vartype) {
            // Список
            case 0:
                $base->type_is_list = true;
                $base->type_is_number = false;
                $base->type_is_string = false;
                $base->type_is_date = false;
                $base->type_is_boolean = false;
                $base->type_is_text = false;
                $base->type_is_image = false;
                $base->type_is_document = false;
                $base->digits_num = 0;
                $base->is_to_moderate_image = "0";
                $base->maxfilesize_img_doc = 0;
                $base->maxfilesize_title_img_doc = "";
                $base->length_txt =0;
                break;
            // Число
            case 1:
                $base->type_is_list = false;
                $base->type_is_number = true;
                $base->type_is_string = false;
                $base->type_is_date = false;
                $base->type_is_boolean = false;
                $base->type_is_text = false;
                $base->type_is_image = false;
                $base->type_is_document = false;
                $base->is_code_needed = "0";
                $base->is_one_value_lst_str_txt = "0";
                $base->is_calcname_lst = "0";
                $base->sepa_calcname = "";
                $base->is_same_small_calcname = "0";
                $base->sepa_same_left_calcname = "";
                $base->sepa_same_right_calcname = "";
                $base->is_to_moderate_image = "0";
                $base->maxfilesize_img_doc = 0;
                $base->maxfilesize_title_img_doc = "";
                $base->maxcount_lst = 0;
                $base->is_calculated_lst = "0";
                $base->is_setup_lst = "0";
                $base->length_txt =0;
                break;
            // Строка
            case 2:
                $base->type_is_list = false;
                $base->type_is_number = false;
                $base->type_is_string = true;
                $base->type_is_date = false;
                $base->type_is_boolean = false;
                $base->type_is_text = false;
                $base->type_is_image = false;
                $base->type_is_document = false;
                $base->is_code_needed = "0";
                $base->digits_num = 0;
                $base->is_calcname_lst = "0";
                $base->sepa_calcname = "";
                $base->is_same_small_calcname = "0";
                $base->sepa_same_left_calcname = "";
                $base->sepa_same_right_calcname = "";
                $base->is_to_moderate_image = "0";
                $base->maxfilesize_img_doc = 0;
                $base->maxfilesize_title_img_doc = "";
                $base->maxcount_lst = 0;
                $base->is_calculated_lst = "0";
                $base->is_setup_lst = "0";
                $base->length_txt =0;
                break;
            // Дата
            case 3:
                $base->type_is_list = false;
                $base->type_is_number = false;
                $base->type_is_string = false;
                $base->type_is_date = true;
                $base->type_is_boolean = false;
                $base->type_is_text = false;
                $base->type_is_image = false;
                $base->type_is_document = false;
                $base->is_code_needed = "0";
                $base->digits_num = 0;
                $base->is_required_lst_num_str_txt_img_doc = "0";
                $base->is_to_moderate_image = "0";
                $base->is_one_value_lst_str_txt = "0";
                $base->is_calcname_lst = "0";
                $base->sepa_calcname = "";
                $base->is_same_small_calcname = "0";
                $base->sepa_same_left_calcname = "";
                $base->sepa_same_right_calcname = "";
                $base->maxfilesize_img_doc = 0;
                $base->maxfilesize_title_img_doc = "";
                $base->maxcount_lst = 0;
                $base->is_calculated_lst = "0";
                $base->is_setup_lst = "0";
                $base->length_txt =0;
                break;
            // Логический
            case 4:
                $base->type_is_list = false;
                $base->type_is_number = false;
                $base->type_is_string = false;
                $base->type_is_date = false;
                $base->type_is_boolean = true;
                $base->type_is_text = false;
                $base->type_is_image = false;
                $base->type_is_document = false;
                $base->is_code_needed = "0";
                $base->digits_num = 0;
                $base->is_required_lst_num_str_txt_img_doc = "0";
                $base->is_to_moderate_image = "0";
                $base->is_one_value_lst_str_txt = "0";
                $base->is_calcname_lst = "0";
                $base->sepa_calcname = "";
                $base->is_same_small_calcname = "0";
                $base->sepa_same_left_calcname = "";
                $base->sepa_same_right_calcname = "";
                $base->maxfilesize_img_doc = 0;
                $base->maxfilesize_title_img_doc = "";
                $base->maxcount_lst = 0;
                $base->is_calculated_lst = "0";
                $base->is_setup_lst = "0";
                $base->length_txt =0;
                break;
            // Текст
            case 5:
                $base->type_is_list = false;
                $base->type_is_number = false;
                $base->type_is_string = false;
                $base->type_is_date = false;
                $base->type_is_boolean = false;
                $base->type_is_text = true;
                $base->type_is_image = false;
                $base->type_is_document = false;
                $base->is_code_needed = "0";
                $base->digits_num = 0;
                $base->is_calcname_lst = "0";
                $base->sepa_calcname = "";
                $base->is_same_small_calcname = "0";
                $base->sepa_same_left_calcname = "";
                $base->sepa_same_right_calcname = "";
                $base->is_to_moderate_image = "0";
                $base->maxfilesize_img_doc = 0;
                $base->maxfilesize_title_img_doc = "";
                $base->maxcount_lst = 0;
                $base->is_calculated_lst = "0";
                $base->is_setup_lst = "0";
                break;
            // Изображение
            case 6:
                $request->validate($this->maxfilesize_rules());
                $base->type_is_list = false;
                $base->type_is_number = false;
                $base->type_is_string = false;
                $base->type_is_date = false;
                $base->type_is_boolean = false;
                $base->type_is_text = false;
                $base->type_is_image = true;
                $base->type_is_document = false;
                $base->is_code_needed = "0";
                $base->digits_num = 0;
                $base->is_one_value_lst_str_txt = "0";
                $base->is_calcname_lst = "0";
                $base->sepa_calcname = "";
                $base->is_same_small_calcname = "0";
                $base->sepa_same_left_calcname = "";
                $base->sepa_same_right_calcname = "";
                $base->maxcount_lst = 0;
                $base->is_calculated_lst = "0";
                $base->is_setup_lst = "0";
                $base->length_txt =0;
                break;
            // Документ
            case 7:
                $request->validate($this->maxfilesize_rules());
                $base->type_is_list = false;
                $base->type_is_number = false;
                $base->type_is_string = false;
                $base->type_is_date = false;
                $base->type_is_boolean = false;
                $base->type_is_text = false;
                $base->type_is_image = false;
                $base->type_is_document = true;
                $base->is_code_needed = "0";
                $base->digits_num = 0;
                $base->is_one_value_lst_str_txt = "0";
                $base->is_calcname_lst = "0";
                $base->sepa_calcname = "";
                $base->is_same_small_calcname = "0";
                $base->sepa_same_left_calcname = "";
                $base->sepa_same_right_calcname = "";
                $base->is_to_moderate_image = "0";
                $base->maxcount_lst = 0;
                $base->is_calculated_lst = "0";
                $base->is_setup_lst = "0";
                $base->length_txt =0;
                break;
        }
        if ($base->is_code_needed == "0") {
            $base->is_code_number = "0";
        }
        if ($base->is_code_number == "0") {
            $base->is_limit_sign_code = "0";
            $base->is_suggest_code = "0";
            $base->is_recalc_code = "0";
        };
        // В принципе - необязательно, а так - нужно
        if ($base->is_suggest_code == "0") {
            $base->is_suggest_max_code = "0";
        };
        // Нужно, если пользователь введет 0 в поле $base->significance_code при $base->is_limit_sign_code = true
        if ($base->significance_code == 0) {
            $base->is_limit_sign_code = "0";
        }
        // Ограничить количество вводимых цифр
        // Нужно
        if ($base->is_limit_sign_code == "0") {
            $base->significance_code = 0;
            $base->is_code_zeros = 0;
        };

        // Нужно
        if ($base->is_calculated_lst == "1") {
            $base->is_setup_lst = "0";
        };
        if ($base->is_setup_lst == "1") {
            $base->is_calculated_lst = "0";
            $base->is_calcname_lst = "1";
            $base->maxcount_lst = 1;
        };

        $base->save();

        if ($request->session()->has('bases_previous_url')) {
            return redirect(session('bases_previous_url'));
        } else {
            return redirect()->back();
        }

    }

    function update(Request $request, Base $base)
    {

        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        //if (!(($base->name_lang_0 == $request->name_lang_0) && ($base->name_lang_0 == $request->name_lang_0))) {
        $request->validate($this->rules());
        //}

        $data = $request->except('_token', '_method');

        $base->fill($data);

        $base->name_lang_0 = $request->name_lang_0;
        $base->name_lang_1 = isset($request->name_lang_1) ? $request->name_lang_1 : "";
        $base->name_lang_2 = isset($request->name_lang_2) ? $request->name_lang_2 : "";
        $base->name_lang_3 = isset($request->name_lang_3) ? $request->name_lang_3 : "";
        $base->names_lang_0 = $request->names_lang_0;
        $base->names_lang_1 = isset($request->names_lang_1) ? $request->names_lang_1 : "";
        $base->names_lang_2 = isset($request->names_lang_2) ? $request->names_lang_2 : "";
        $base->names_lang_3 = isset($request->names_lang_3) ? $request->names_lang_3 : "";

        $base->digits_num = $request->digits_num;

        // у этой команды два предназначения:
        // 1) заменить "on" на "1" при отмеченном checkbox
        // 2) создать новое значение "0" при выключенном checkbox
        // в базе данных информация хранится как "0" или "1"
        $base->is_code_needed = isset($request->is_code_needed) ? "1" : "0";
        $base->is_code_number = isset($request->is_code_number) ? "1" : "0";
        $base->is_limit_sign_code = isset($request->is_limit_sign_code) ? "1" : "0";
        $base->significance_code = $request->significance_code;
        $base->is_code_zeros = isset($request->is_code_zeros) ? "1" : "0";
        $base->is_suggest_code = isset($request->is_suggest_code) ? "1" : "0";
        $base->is_suggest_max_code = isset($request->is_suggest_max_code) ? "1" : "0";
        $base->is_recalc_code = isset($request->is_recalc_code) ? "1" : "0";
        $base->is_required_lst_num_str_txt_img_doc = isset($request->is_required_lst_num_str_txt_img_doc) ? "1" : "0";
        $base->is_to_moderate_image = isset($request->is_to_moderate_image) ? "1" : "0";
        $base->is_one_value_lst_str_txt = isset($request->is_one_value_lst_str_txt) ? "1" : "0";
        $base->is_calcname_lst = isset($request->is_calcname_lst) ? "1" : "0";
        $base->is_same_small_calcname = isset($request->is_same_small_calcname) ? "1" : "0";

        $base->digits_num = isset($request->digits_num) ? $request->digits_num : "0";
        $base->sepa_calcname = isset($request->sepa_calcname) ? $request->sepa_calcname : "";
        $base->sepa_same_left_calcname = isset($request->sepa_same_left_calcname) ? $request->sepa_same_left_calcname : "";
        $base->sepa_same_right_calcname = isset($request->sepa_same_left_calcname) ? $request->sepa_same_right_calcname : "";

        $base->maxfilesize_img_doc = $request->maxfilesize_img_doc;
        $base->maxfilesize_title_img_doc = isset($request->maxfilesize_title_img_doc) ? $request->maxfilesize_title_img_doc : "";

        $base->maxcount_lst = $request->maxcount_lst >= 0 ? $request->maxcount_lst : 0;
        $base->is_calculated_lst = isset($request->is_calculated_lst) ? "1" : "0";
        $base->is_setup_lst = isset($request->is_setup_lst) ? "1" : "0";
        $base->length_txt = $request->length_txt >= 0 ? $request->length_txt : 0;

        $base->serial_number = $request->serial_number;

        // Похожие строки в BaseController.php (functions: store(), edit())
        // и в Base.php (functions: get_types(), type(), type_name())
        // и в Base/edit.blade.php
        switch ($request->vartype) {
            // Список
            case 0:
                $base->type_is_list = true;
                $base->type_is_number = false;
                $base->type_is_string = false;
                $base->type_is_date = false;
                $base->type_is_boolean = false;
                $base->type_is_text = false;
                $base->type_is_image = false;
                $base->type_is_document = false;
                $base->digits_num = 0;
                $base->is_to_moderate_image = "0";
                $base->maxfilesize_img_doc = 0;
                $base->maxfilesize_title_img_doc = "";
                $base->length_txt =0;
                break;
            // Число
            case 1:
                $base->type_is_list = false;
                $base->type_is_number = true;
                $base->type_is_string = false;
                $base->type_is_date = false;
                $base->type_is_boolean = false;
                $base->type_is_text = false;
                $base->type_is_image = false;
                $base->type_is_document = false;
                $base->is_code_needed = "0";
                $base->is_one_value_lst_str_txt = "0";
                $base->is_calcname_lst = "0";
                $base->sepa_calcname = "";
                $base->is_same_small_calcname = "0";
                $base->sepa_same_left_calcname = "";
                $base->sepa_same_right_calcname = "";
                $base->is_to_moderate_image = "0";
                $base->maxfilesize_img_doc = 0;
                $base->maxfilesize_title_img_doc = "";
                $base->maxcount_lst = 0;
                $base->is_calculated_lst = "0";
                $base->is_setup_lst = "0";
                $base->length_txt =0;
                break;
            // Строка
            case 2:
                $base->type_is_list = false;
                $base->type_is_number = false;
                $base->type_is_string = true;
                $base->type_is_date = false;
                $base->type_is_boolean = false;
                $base->type_is_text = false;
                $base->type_is_image = false;
                $base->type_is_document = false;
                $base->is_code_needed = "0";
                $base->digits_num = 0;
                $base->is_calcname_lst = "0";
                $base->sepa_calcname = "";
                $base->is_same_small_calcname = "0";
                $base->sepa_same_left_calcname = "";
                $base->sepa_same_right_calcname = "";
                $base->is_to_moderate_image = "0";
                $base->maxfilesize_img_doc = 0;
                $base->maxfilesize_title_img_doc = "";
                $base->maxcount_lst = 0;
                $base->is_calculated_lst = "0";
                $base->is_setup_lst = "0";
                $base->length_txt =0;
                break;
            // Дата
            case 3:
                $base->type_is_list = false;
                $base->type_is_number = false;
                $base->type_is_string = false;
                $base->type_is_date = true;
                $base->type_is_boolean = false;
                $base->type_is_text = false;
                $base->type_is_image = false;
                $base->type_is_document = false;
                $base->is_code_needed = "0";
                $base->digits_num = 0;
                $base->is_required_lst_num_str_txt_img_doc = "0";
                $base->is_to_moderate_image = "0";
                $base->is_one_value_lst_str_txt = "0";
                $base->is_calcname_lst = "0";
                $base->sepa_calcname = "";
                $base->is_same_small_calcname = "0";
                $base->sepa_same_left_calcname = "";
                $base->sepa_same_right_calcname = "";
                $base->maxfilesize_img_doc = 0;
                $base->maxfilesize_title_img_doc = "";
                $base->maxcount_lst = 0;
                $base->is_calculated_lst = "0";
                $base->is_setup_lst = "0";
                $base->length_txt =0;
                break;
            // Логический
            case 4:
                $base->type_is_list = false;
                $base->type_is_number = false;
                $base->type_is_string = false;
                $base->type_is_date = false;
                $base->type_is_boolean = true;
                $base->type_is_text = false;
                $base->type_is_image = false;
                $base->type_is_document = false;
                $base->is_code_needed = "0";
                $base->digits_num = 0;
                $base->is_required_lst_num_str_txt_img_doc = "0";
                $base->is_to_moderate_image = "0";
                $base->is_one_value_lst_str_txt = "0";
                $base->is_calcname_lst = "0";
                $base->sepa_calcname = "";
                $base->is_same_small_calcname = "0";
                $base->sepa_same_left_calcname = "";
                $base->sepa_same_right_calcname = "";
                $base->maxfilesize_img_doc = 0;
                $base->maxfilesize_title_img_doc = "";
                $base->maxcount_lst = 0;
                $base->is_calculated_lst = "0";
                $base->is_setup_lst = "0";
                $base->length_txt =0;
                break;
            // Текст
            case 5:
                $base->type_is_list = false;
                $base->type_is_number = false;
                $base->type_is_string = false;
                $base->type_is_date = false;
                $base->type_is_boolean = false;
                $base->type_is_text = true;
                $base->type_is_image = false;
                $base->type_is_document = false;
                $base->is_code_needed = "0";
                $base->digits_num = 0;
                $base->is_calcname_lst = "0";
                $base->sepa_calcname = "";
                $base->is_same_small_calcname = "0";
                $base->sepa_same_left_calcname = "";
                $base->sepa_same_right_calcname = "";
                $base->is_to_moderate_image = "0";
                $base->maxfilesize_img_doc = 0;
                $base->maxfilesize_title_img_doc = "";
                $base->maxcount_lst = 0;
                $base->is_calculated_lst = "0";
                $base->is_setup_lst = "0";
                break;
            // Изображение
            case 6:
                $request->validate($this->maxfilesize_rules());
                $base->type_is_list = false;
                $base->type_is_number = false;
                $base->type_is_string = false;
                $base->type_is_date = false;
                $base->type_is_boolean = false;
                $base->type_is_text = false;
                $base->type_is_image = true;
                $base->type_is_document = false;
                $base->is_code_needed = "0";
                $base->digits_num = 0;
                $base->is_one_value_lst_str_txt = "0";
                $base->is_calcname_lst = "0";
                $base->sepa_calcname = "";
                $base->is_same_small_calcname = "0";
                $base->sepa_same_left_calcname = "";
                $base->sepa_same_right_calcname = "";
                $base->maxcount_lst = 0;
                $base->is_calculated_lst = "0";
                $base->is_setup_lst = "0";
                $base->length_txt =0;
                break;
            // Документ
            case 7:
                $request->validate($this->maxfilesize_rules());
                $base->type_is_list = false;
                $base->type_is_number = false;
                $base->type_is_string = false;
                $base->type_is_date = false;
                $base->type_is_boolean = false;
                $base->type_is_text = false;
                $base->type_is_image = false;
                $base->type_is_document = true;
                $base->is_code_needed = "0";
                $base->digits_num = 0;
                $base->is_one_value_lst_str_txt = "0";
                $base->is_calcname_lst = "0";
                $base->sepa_calcname = "";
                $base->is_same_small_calcname = "0";
                $base->sepa_same_left_calcname = "";
                $base->sepa_same_right_calcname = "";
                $base->is_to_moderate_image = "0";
                $base->maxcount_lst = 0;
                $base->is_calculated_lst = "0";
                $base->is_setup_lst = "0";
                $base->length_txt =0;
                break;
        }

        if ($base->is_code_needed == "0") {
            $base->is_code_number = "0";
        }
        if ($base->is_code_number == "0") {
            $base->is_limit_sign_code = "0";
            $base->is_suggest_code = "0";
            $base->is_recalc_code = "0";
        };
        // Предлагать расчитать код при добавлении записи
        // В принципе - необязательно, а так - нужно
        if ($base->is_suggest_code == "0") {
            $base->is_suggest_max_code = "0";
        };
        // Нужно, если пользователь введет 0 в поле $base->significance_code при $base->is_limit_sign_code = true
        if ($base->significance_code == 0) {
            $base->is_limit_sign_code = "0";
        }
        // Ограничить количество вводимых цифр
        // Нужно
        if ($base->is_limit_sign_code == "0") {
            $base->significance_code = 0;
            $base->is_code_zeros = 0;
        };
        // Нужно
        if ($base->is_calculated_lst == "1") {
            $base->is_setup_lst = "0";
        };
        if ($base->is_setup_lst == "1") {
            $base->is_calculated_lst = "0";
            $base->is_calcname_lst = "1";
            $base->maxcount_lst = 1;
        };

        $base->save();

        $template = Template::findOrFail($base->template_id);
        return redirect()->route('base.index', ['template' => $template]);
    }

    function edit(Base $base)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($base->template_id);
        return view('base/edit', ['template' => $template, 'base' => $base, 'types' => Base::get_types()]);
    }

    function delete_question(Base $base)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        return view('base/show', ['type_form' => 'delete_question', 'base' => $base]);
    }

    function delete(Base $base)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        // сначала эта команда
        $template = Template::findOrFail($base->template_id);
        // потом эта команда
        $base->delete();

        return redirect()->route('base.index', ['template' => $template]);
    }

//    // Вариант 1
//    static function form_tree($id)
//    {
//        $base = Base::find($id);
//        $result = self::form_tree_start($id);
//        if ($result != '') {
//            $result = '<ul type="circle"><li>' . $base->name() . $result . '</li></ul>';
//
//        }
//        return $result;
//    }

    // эти функции похожи
    // вычисляемый link_id уникальный в этих функциях должен быть
    static function form_tree($base_id)
    {
        $base = Base::find($base_id);
        $list = array();
        $path_previous = $base->name();
        $result = self::form_tree_start($list, $base_id, $path_previous);
        if ($result != '') {
            $result = '<ul type="circle"><li>' . $base->name() . $result . '</li></ul>';

        }
        return $result;
    }

    static function form_tree_start(&$list, $id, $path_previous)
    {
        $result = '<ul type="circle">';
        // расчетные поля не включаются в список
        //$links = Link::all()->where('child_base_id', $id)->where('parent_is_parent_related', false)->sortBy('parent_base_number');
        $links = Link::all()->where('child_base_id', $id)->sortBy('parent_base_number');
        // эти строки нужны
        if (count($links) == 0) {
            return '';
        }
        if (!(array_search($id, $list) === false)) {
            return '';
        }
        ////////////////////
        $list[count($list)] = $id;
        foreach ($links as $link) {
            $rs = '';
            $pt = '';
            $str = '';
            $path = $path_previous . ' \\' . $link->parent_label();// . $pt;
            // чтобы не было бесконечного цикла
            if ($link->parent_base_id != $id) {
                $str = self::form_tree_start($list, $link->parent_base_id, $path);
            };
            $result = $result . '<li>' . $link->id . ' ' . $link->child_base_id . ' ' . $link->parent_base_id . ' ' . $path
                . $str . '</li>';
        }


        $result = $result . "</ul>";
        return $result;
    }

    static function get_array_bases_tree_ul($base_id)
    {
        $base = Base::find($base_id);
        $list = array();
        $result_index = 0;
        $result_keys = array();
        $result_values = array();
        $path_previous = $base->name();
        self::get_array_bases_tree_start($list, $result_index, $result_keys, $result_values, $base_id, $path_previous);
        $result = '<ul type="circle">';
        foreach ($result_values as $base) {
            $result = $result . '<li>' . $base . '</li>';
        }
        $result = $result . '</ul>';
        return $result;
    }

    static function get_array_bases_tree_options($base_id)
    {
        $base = Base::find($base_id);
        $list = array();
        $result_index = 0;
        $result_keys = array();
        $result_values = array();
        $path_previous = $base->name();
        self::get_array_bases_tree_start($list, $result_index, $result_keys, $result_values, $base_id, $path_previous);
        $result = '';


//        $result_keys[$result_index] = 186;
//        $result_values[$result_index] = "Товары по остаткам";
//        $result_index = $result_index + 1;


        foreach ($result_values as $key => $value) {
            $result = $result . '<option value="' . $result_keys[$key] . '">' . $value . '</option>';
        }
        return $result;
    }

    static function get_array_bases_tree_start(&$list, &$result_index, &$result_keys, &$result_values, $id, $path_previous)
    {
        $result = '<ul type="circle">';
        // расчетные поля не включаются в список
        $links = Link::all()->where('child_base_id', $id)->where('parent_is_parent_related', false)->sortBy('parent_base_number');
        // эти строки нужны
        if (count($links) == 0) {
            return;
        }
        if (!(array_search($id, $list) === false)) {
            return;
        }
        ////////////////////
        $list[count($list)] = $id;
        foreach ($links as $link) {
            $rs = '';
            $pt = '';
            $str = '';
            $path = $path_previous . ' \\' . $link->parent_label();
            $result_keys[$result_index] = $link->id;
            $result_values[$result_index] = $path;
            $result_index = $result_index + 1;
            // чтобы не было бесконечного цикла
            if ($link->parent_base_id != $id) {
                self::get_array_bases_tree_start($list, $result_index, $result_keys, $result_values, $link->parent_base_id, $path);
            };
        }
        return;
    }

    // возвращает "существуют ли переданный $par_link в уникальном маршруте $links из $link-id"
    static function get_par_link_in_array_bases_tree_routes($base_id, $link_id, $par_link)
    {
        $arr = self::get_array_bases_tree_routes($base_id, $link_id, false);
        $result = false;
        if ($arr != null) {
            $result = in_array($par_link, $arr);  // true или false
        }
        return $arr;  // true или false
    }

    // возвращает маршрут $links из link_id для доступа к объекту для переданного параметра (link_id уникальный д.б. в функции)
    static function get_array_bases_tree_routes($base_id, $link_id, $child_to_parent)  // "boolean $child_to_parent"
    {
        $list = array();
        $route_previous = '';
        $routes = array();
        $result = null;
        self::get_array_bases_tree_routes_start($list, $base_id, $routes, $route_previous);

        foreach ($routes as $route) {
            $arr = explode(" ", $route);
            // удаляет первый элемент массива, после команды explode() создается первый элемент массива нулевой
            array_shift($arr);
            // если последний элемент массива равен нужному link_id
            if ($arr[count($arr) - 1] == $link_id) {
                if (!$child_to_parent) {
                    //  возвращает массив с элементами в обратном порядке
                    $arr = array_reverse($arr);
                }
                $result = $arr;
                break;
            }
        }
        return $result;
    }

    static function get_array_bases_tree_routes_start(&$list, $id, &$routes, $route_previous)
    {
        // расчетные поля не включаются в список
        $links = Link::all()->where('child_base_id', $id)->where('parent_is_parent_related', false)->sortBy('parent_base_number');
        // эти строки нужны
        if (count($links) == 0) {
            return;
        }
        if (!(array_search($id, $list) === false)) {
            return;
        }
        ////////////////////
        $list[count($list)] = $id;
        foreach ($links as $link) {
            $route = $route_previous . ' ' . $link->id;
            $routes[count($routes)] = $route;
            // чтобы не было бесконечного цикла
            if ($link->parent_base_id != $id) {
                self::get_array_bases_tree_routes_start($list, $link->parent_base_id, $routes, $route);
            };
        }
        return;
    }
    ////////////////////////////////////////
    // Возвращает имя поля ноименование в зависимости от текущего языка
    static function field_name()
    {
        $result = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $result = 'name_lang_' . $index;
        }
        if ($result == "") {
            $result = 'name_lang_0';
        }
        return $result;
    }

    function getBasesAll()
    {
        $bases = $bases = Base::orderBy('name_lang_0')->get();

        print(json_encode($bases));


    }

}
