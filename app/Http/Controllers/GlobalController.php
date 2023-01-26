<?php

namespace App\Http\Controllers;

use App\Models\Relip;
use App\Models\Relit;
use App\Models\Set;
use App\Models\Template;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Base;
use App\Models\Link;
use App\Models\Item;
use App\Models\Main;
use App\Models\Text;
use App\Models\Project;
use App\Models\Role;
use App\Models\Roba;
use App\Models\Roli;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use phpDocumentor\Reflection\Types\Boolean;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class GlobalController extends Controller
{
    public $display = 'tape';
    private $displays = ['tape', 'table'];

    static function start_artisan()
    {
        Artisan::call('migrate');
        //Artisan::call('migrate', ['--path' => 'vendor/systeminc/laravel-admin/src/database/migrations']);
        //Artisan::call('migrate:refresh', ['--path' => 'database/migrations']);
        // для настройки папки storage
        Artisan::call('storage:link');
    }

    static function glo_user()
    {
        return Auth::user();
    }

    static function glo_user_id()
    {
        return Auth::user()->id;
    }

//  Похожие строки в в GlobalController.php и в Item.php
    static function deleted_user_date()
    {
        return self::glo_user()->name() . ", " . date(trans('main.format_date')) . ", " . self::glo_user()->email;
    }

    static function deleted_user_date_time()
    {
        return self::glo_user()->name() . ", " . date(trans('main.format_date_time')) . ", " . self::glo_user()->email;
    }

    static function num_is_boolean($value)
    {
        return $value == true ? 1 : 0;
    }

    static function name_is_boolean($value)
    {
        return $value == true ? html_entity_decode('	&#9745;')
            : ($value == false ? html_entity_decode('&#10065;') : trans('main.empty'));
    }

    static function base_right(Base $base, Role $role, $relit_id, bool $is_no_sndb_pd_rule = false)
    {
        //$relit_parent_template = self::get_parent_template_from_relit_id($relit_id, $role->template_id)['template'];
        $relit_parent_template = self::get_parent_template_from_relit_id($relit_id, $base->template_id)['template'];
        $is_all_base_calcname_enable = $role->is_all_base_calcname_enable;
        $is_list_base_sort_creation_date_desc = $role->is_list_base_sort_creation_date_desc;
        $is_bsin_base_enable = $role->is_bsin_base_enable;
        $is_exclude_related_records = $role->is_exclude_related_records;
        // true/false - значение по умолчанию (см.ниже)
        // Такие же значения должны быть в формах roles\edit.php, robas\edit.php
        $is_show_head_attr_enable = false;
        $is_view_prev_next = true;
        $is_skip_count_records_equal_1_base_index = false;
        $is_skip_count_records_equal_1_item_body_index = false;
        $is_list_base_create = $role->is_list_base_create;
        $is_list_base_read = $role->is_list_base_read;
        $is_list_base_update = $role->is_list_base_update;
        $is_list_base_delete = $role->is_list_base_delete;
        $is_list_base_used_delete = $role->is_list_base_used_delete;
        $is_list_base_byuser = $role->is_list_base_byuser;
        $is_list_base_user_id = false;
        $is_heading = false;
        $is_edit_base_read = $role->is_edit_base_read;
        $is_edit_base_update = $role->is_edit_base_update;
        $is_list_base_enable = $role->is_list_base_enable;
        $is_list_link_enable = $role->is_list_link_enable;
        $is_body_link_enable = $role->is_body_link_enable;
        $is_show_base_enable = $role->is_show_base_enable;
        $is_show_link_enable = $role->is_show_link_enable;
        $is_edit_link_read = $role->is_edit_link_read;
        $is_edit_link_update = $role->is_edit_link_update;
        $is_hier_base_enable = $role->is_hier_base_enable;
        $is_hier_link_enable = $role->is_hier_link_enable;
        $is_show_hist_attr_enable = false;
        $is_edit_hist_attr_enable = false;
        $is_list_hist_attr_enable = false;
        $is_list_hist_records_enable = true;
        $is_brow_hist_attr_enable = false;
        $is_brow_hist_records_enable = true;
        $is_edit_email_base_create = $role->is_edit_email_base_create;
        $is_edit_email_question_base_create = $role->is_edit_email_question_base_create;
        $is_edit_email_base_update = $role->is_edit_email_base_update;
        $is_edit_email_question_base_update = $role->is_edit_email_question_base_update;
        $is_show_email_base_delete = $role->is_show_email_base_delete;
        $is_show_email_question_base_delete = $role->is_show_email_question_base_delete;
        // Блок проверки по Role
        // "$is_list_base_calc = true" нужно
        $is_list_base_calc = true;
        if (!$is_no_sndb_pd_rule) {
            if ($role->is_list_base_sndbt == false) {
                if ($base->type_is_number() || $base->type_is_string() ||
                    $base->type_is_date() || $base->type_is_boolean() || $base->type_is_text()) {
                    $is_list_base_calc = false;
                }
            }
            if ($role->is_list_base_id == false) {
                if ($base->type_is_image == true || $base->type_is_document == true) {
                    $is_list_base_calc = false;
                }
            }
            if ($role->is_list_base_calculated == false) {
                if ($base->is_calculated_lst == true) {
                    $is_list_base_calc = false;
                }
            }
            if ($role->is_list_base_setup == false) {
                if ($base->is_setup_lst == true) {
                    $is_list_base_calc = false;
                }
            }

            // Показывать Основы только своего текущего шаблона ($role->template_id)
            // или связанных шаблонов ($relit_parent_template->id);
            // Если в связанном шаблоне есть свои связанные шаблоны,
            // то Основы таких (связанных шаблонов у связанных шаблонов) шаблонов не показываются
            // Также 'Связанные шаблоны у связанных шаблонов' не должны присутствовать в robas
            // Используется отрицание '!'
            if (!(($base->template_id == $role->template_id) || ($base->template_id == $relit_parent_template->id))) {
                $is_list_base_calc = false;
            }

//          Не 'Показывать Основы взаимосвязанных проектов'
            if ($role->is_list_base_relits == false) {
                if ($base->template_id != $role->template_id) {
                    $is_list_base_calc = false;
                }
            }

//          'Показывать Основы взаимосвязанных проектов'
            if ($role->is_list_base_relits == true) {
                if ($base->template_id != $role->template_id) {
                    // 'Показывать только для чтения Основы взаимосвязанных проектов'
                    if ($role->is_read_base_relits == true) {
                        $is_list_base_read = true;
                        $is_list_base_create = false;
                        $is_list_base_update = false;
                        $is_list_base_delete = false;
                    }
                    // 'Показывать Основы - настройки взаимосвязанных проектов' = false
                    if ($role->is_list_base_relits_setup == false) {
                        if ($base->is_setup_lst == true) {
                            $is_list_base_calc = false;
                        }
                    }
                }
            }
        }

//        if ($is_list_base_read == true) {
//            $is_list_base_create = false;
//            $is_list_base_update = false;
//            $is_list_base_delete = false;
//        }

        //      По умолчанию фильтровать по id пользователя в списке
        if (($is_list_base_user_id == false) && ($base->is_default_list_base_user_id == true)) {
            $is_list_base_user_id = true;
        }

//      По умолчанию фильтровать по пользователю - автору записи в списке
        if (($is_list_base_byuser == false) && ($base->is_default_list_base_byuser == true)) {
            $is_list_base_byuser = true;
        }

        //      По умолчанию основа-заголовок
        if ($base->is_default_heading == true) {
            $is_heading = true;
        }

        // Для вычисляемых base
        // ИЛИ $base с другого шаблона(не равен $role->template_id)
//        if (($base->is_calculated_lst == true) || ($base->template_id != $role->template_id)) {
//        if (($base->is_calculated_lst == true) || ($base->template_id != $template->id)) {
        if ($base->is_calculated_lst == true) {
            $is_list_base_create = false;
//          $is_list_base_read = true;
            $is_list_base_read = $is_list_base_calc;
            $is_list_base_update = false;
            $is_list_base_delete = false;
        }

        // "$is_enable &&" нужно
        $is_list_base_calc = $is_list_base_calc && ($is_list_base_create || $is_list_base_read || $is_list_base_update || $is_list_base_delete);

        // Блок проверки по robas, используя переменные $role, $relit_id и $base
        //$roba = Roba::where('role_id', $role->id)->where('base_id', $base->id)->first();
        $roba = Roba::where('role_id', $role->id)->where('relit_id', $relit_id)->where('base_id', $base->id)->first();
        if ($roba != null) {
            $is_roba_all_base_calcname_enable = $roba->is_all_base_calcname_enable;
            $is_roba_list_base_sort_creation_date_desc = $roba->is_list_base_sort_creation_date_desc;
            $is_roba_mnmn_base_enable = $roba->is_bsin_base_enable;
            $is_roba_exclude_related_records = $roba->is_exclude_related_records;
            $is_roba_show_head_attr_enable = $roba->is_show_head_attr_enable;
            $is_roba_view_prev_next = $roba->is_view_prev_next;
            $is_roba_skip_count_records_equal_1_base_index = $roba->is_skip_count_records_equal_1_base_index;
            $is_roba_skip_count_records_equal_1_item_body_index = $roba->is_skip_count_records_equal_1_item_body_index;
            $is_roba_list_base_create = $roba->is_list_base_create;
            $is_roba_list_base_read = $roba->is_list_base_read;
            $is_roba_list_base_update = $roba->is_list_base_update;
            $is_roba_list_base_delete = $roba->is_list_base_delete;
            $is_roba_list_base_used_delete = $roba->is_list_base_used_delete;
            $is_roba_list_base_byuser = $roba->is_list_base_byuser;
            $is_roba_list_base_user_id = $roba->is_list_base_user_id;
            $is_roba_edit_base_read = $roba->is_edit_base_read;
            $is_roba_edit_base_update = $roba->is_edit_base_update;
            $is_roba_list_base_enable = $roba->is_list_base_enable;
            $is_roba_list_link_enable = $roba->is_list_link_enable;
            $is_roba_body_link_enable = $roba->is_body_link_enable;
            $is_roba_show_base_enable = $roba->is_show_base_enable;
            $is_roba_show_link_enable = $roba->is_show_link_enable;
            $is_roba_edit_link_read = $roba->is_edit_link_read;
            $is_roba_edit_link_update = $roba->is_edit_link_update;
            $is_roba_hier_base_enable = $roba->is_hier_base_enable;
            $is_roba_hier_link_enable = $roba->is_hier_link_enable;
            $is_roba_show_hist_attr_enable = $roba->is_show_hist_attr_enable;
            $is_roba_edit_hist_attr_enable = $roba->is_edit_hist_attr_enable;
            $is_roba_list_hist_attr_enable = $roba->is_list_hist_attr_enable;
            $is_roba_list_hist_records_enable = $roba->is_list_hist_records_enable;
            $is_roba_brow_hist_attr_enable = $roba->is_brow_hist_attr_enable;
            $is_roba_brow_hist_records_enable = $roba->is_brow_hist_records_enable;
            $is_roba_edit_email_base_create = $roba->is_edit_email_base_create;
            $is_roba_edit_email_question_base_create = $roba->is_edit_email_question_base_create;
            $is_roba_edit_email_base_update = $roba->is_edit_email_base_update;
            $is_roba_edit_email_question_base_update = $roba->is_edit_email_question_base_update;
            $is_roba_show_email_base_delete = $roba->is_show_email_base_delete;
            $is_roba_show_email_question_base_delete = $roba->is_show_email_question_base_delete;

//            if ($is_roba_list_base_read == true) {
//                $is_roba_list_base_create = false;
//                $is_roba_list_base_update = false;
//                $is_roba_list_base_delete = false;
//            }

            // Для вычисляемых base
            // ИЛИ $base с другого шаблона(не равен $role->template_id)
//            if (($base->is_calculated_lst == true) || ($base->template_id != $role->template_id)) {
            if ($base->is_calculated_lst == true) {
                $is_roba_list_base_create = false;
//              Не нужно '$is_roba_list_base_read = true;'
//              $is_roba_list_base_read = true;
                $is_roba_list_base_update = false;
                $is_roba_list_base_delete = false;
            }

            $is_roba_list_base_calc = $is_roba_list_base_create || $is_roba_list_base_read || $is_roba_list_base_update || $is_roba_list_base_delete;
//          $is_roba_edit_base_enable = $is_roba_edit_base_read || $is_roba_edit_base_update;
//          $is_roba_edit_link_enable = $is_roba_edit_link_read || $is_roba_edit_link_update;

            $is_list_base_calc = $is_roba_list_base_calc;
            $is_all_base_calcname_enable = $is_roba_all_base_calcname_enable;
            $is_list_base_sort_creation_date_desc = $is_roba_list_base_sort_creation_date_desc;
            $is_bsin_base_enable = $is_roba_mnmn_base_enable;
            $is_exclude_related_records = $is_roba_exclude_related_records;
            $is_show_head_attr_enable = $is_roba_show_head_attr_enable;
            $is_view_prev_next = $is_roba_view_prev_next;
            $is_skip_count_records_equal_1_base_index = $is_roba_skip_count_records_equal_1_base_index;
            $is_skip_count_records_equal_1_item_body_index = $is_roba_skip_count_records_equal_1_item_body_index;
            $is_list_base_create = $is_roba_list_base_create;
            $is_list_base_read = $is_roba_list_base_read;
            $is_list_base_update = $is_roba_list_base_update;
            $is_list_base_delete = $is_roba_list_base_delete;
            $is_list_base_used_delete = $is_roba_list_base_used_delete;
            $is_list_base_byuser = $is_roba_list_base_byuser;
            $is_list_base_user_id = $is_roba_list_base_user_id;
//          $is_edit_base_enable = $is_roba_edit_base_enable;
            $is_edit_base_read = $is_roba_edit_base_read;
            $is_edit_base_update = $is_roba_edit_base_update;
            $is_list_base_enable = $is_roba_list_base_enable;

            $is_list_link_enable = $is_roba_list_link_enable;

            $is_body_link_enable = $is_roba_body_link_enable;
//          $is_edit_link_enable = $is_roba_edit_link_enable;
            $is_show_base_enable = $is_roba_show_base_enable;
            $is_show_link_enable = $is_roba_show_link_enable;
            $is_edit_link_read = $is_roba_edit_link_read;
            $is_edit_link_update = $is_roba_edit_link_update;
            $is_hier_base_enable = $is_roba_hier_base_enable;
            $is_hier_link_enable = $is_roba_hier_link_enable;
            $is_show_hist_attr_enable = $is_roba_show_hist_attr_enable;
            $is_edit_hist_attr_enable = $is_roba_edit_hist_attr_enable;
            $is_list_hist_attr_enable = $is_roba_list_hist_attr_enable;
            $is_list_hist_records_enable = $is_roba_list_hist_records_enable;
            $is_brow_hist_attr_enable = $is_roba_brow_hist_attr_enable;
            $is_brow_hist_records_enable = $is_roba_brow_hist_records_enable;
            $is_edit_email_base_create = $is_roba_edit_email_base_create;
            $is_edit_email_question_base_create = $is_roba_edit_email_question_base_create;
            $is_edit_email_base_update = $is_roba_edit_email_base_update;
            $is_edit_email_question_base_update = $is_roba_edit_email_question_base_update;
            $is_show_email_base_delete = $is_roba_show_email_base_delete;
            $is_show_email_question_base_delete = $is_roba_show_email_question_base_delete;
        }
        // Не нужно
        //if ($is_all_base_calcname_enable == true) {
        //$is_list_link_enable = false;
        //}
        $is_edit_base_enable = $is_edit_base_read || $is_edit_base_update;
        $is_edit_link_enable = $is_edit_link_read || $is_edit_link_update;

        return ['base_id' => $base->id,
            'base_name' => $base->name(),
            'is_list_base_calc' => $is_list_base_calc,
            'is_all_base_calcname_enable' => $is_all_base_calcname_enable,
            'is_list_base_sort_creation_date_desc' => $is_list_base_sort_creation_date_desc,
            'is_bsin_base_enable' => $is_bsin_base_enable,
            'is_bsmn_base_enable' => $is_list_base_calc && $is_bsin_base_enable,
            'is_exclude_related_records' => $is_exclude_related_records,
            'is_show_head_attr_enable' => $is_show_head_attr_enable,
            'is_view_prev_next' => $is_view_prev_next,
            'is_skip_count_records_equal_1_base_index' => $is_skip_count_records_equal_1_base_index,
            'is_skip_count_records_equal_1_item_body_index' => $is_skip_count_records_equal_1_item_body_index,
            'is_list_base_create' => $is_list_base_create,
            'is_list_base_read' => $is_list_base_read,
            'is_list_base_update' => $is_list_base_update,
            'is_list_base_delete' => $is_list_base_delete,
            'is_list_base_used_delete' => $is_list_base_used_delete,
            'is_list_base_byuser' => $is_list_base_byuser,
            'is_list_base_user_id' => $is_list_base_user_id,
            'is_heading' => $is_heading,
            'is_edit_base_enable' => $is_edit_base_enable,
            'is_edit_base_read' => $is_edit_base_read,
            'is_edit_base_update' => $is_edit_base_update,
            'is_list_base_enable' => $is_list_base_enable,
            'is_list_link_enable' => $is_list_link_enable,
            'is_body_link_enable' => $is_body_link_enable,
            'is_show_base_enable' => $is_show_base_enable,
            'is_show_link_enable' => $is_show_link_enable,
            'is_edit_link_enable' => $is_edit_link_enable,
            'is_edit_link_read' => $is_edit_link_read,
            'is_edit_link_update' => $is_edit_link_update,
            'is_hier_base_enable' => $is_hier_base_enable,
            'is_hier_link_enable' => $is_hier_link_enable,
            'is_show_hist_attr_enable' => $is_show_hist_attr_enable,
            'is_edit_hist_attr_enable' => $is_edit_hist_attr_enable,
            'is_list_hist_attr_enable' => $is_list_hist_attr_enable,
            'is_list_hist_records_enable' => $is_list_hist_records_enable,
            'is_brow_hist_attr_enable' => $is_brow_hist_attr_enable,
            'is_brow_hist_records_enable' => $is_brow_hist_records_enable,
            'is_edit_email_base_create' => $is_edit_email_base_create,
            'is_edit_email_question_base_create' => $is_edit_email_question_base_create,
            'is_edit_email_base_update' => $is_edit_email_base_update,
            'is_edit_email_question_base_update' => $is_edit_email_question_base_update,
            'is_show_email_base_delete' => $is_show_email_base_delete,
            'is_show_email_question_base_delete' => $is_show_email_question_base_delete,
        ];
    }

    static function base_link_right(Link $link, Role $role, $parent_relit_id, bool $child_base = false, $relit_dop_id = null)
    {
        $base = null;
        $relit_id = null;
        $base_ret_id = null;
        if ($child_base == true) {
            $base = $link->child_base;
            // 0 - текущий проект, по умолчанию
            //$relit_id = 0;
            $relit_id = $relit_dop_id;
            $base_ret_id = $parent_relit_id;
        } else {
            $base = $link->parent_base;
            $relit_id = $parent_relit_id;
            $base_ret_id = $relit_dop_id;
        }

//        $base = null;
//        if ($child_base == true) {
//            $base = $link->child_base;
//        } else {
//            $base = $link->parent_base;
//        }
//
//        $relit_id = $parent_relit_id;
//        $base_ret_id = $relit_dop_id;

        $base_right = self::base_right($base, $role, $relit_id);

        $is_list_base_calc = $base_right['is_list_base_calc'];
        $is_all_base_calcname_enable = $base_right['is_all_base_calcname_enable'];
        $is_list_base_sort_creation_date_desc = $base_right['is_list_base_sort_creation_date_desc'];
        $is_bsin_base_enable = $base_right['is_bsin_base_enable'];
        $is_exclude_related_records = $base_right['is_exclude_related_records'];
        $is_show_head_attr_enable = $base_right['is_show_head_attr_enable'];
        $is_view_prev_next = $base_right['is_view_prev_next'];
        $is_skip_count_records_equal_1_base_index = $base_right['is_skip_count_records_equal_1_base_index'];
        $is_skip_count_records_equal_1_item_body_index = $base_right['is_skip_count_records_equal_1_item_body_index'];
        $is_list_base_create = $base_right['is_list_base_create'];
        $is_list_base_read = $base_right['is_list_base_read'];
        $is_list_base_update = $base_right['is_list_base_update'];
        $is_list_base_delete = $base_right['is_list_base_delete'];
        $is_list_base_used_delete = $base_right['is_list_base_used_delete'];
        $is_list_base_byuser = $base_right['is_list_base_byuser'];
        $is_list_base_user_id = $base_right['is_list_base_user_id'];
        $is_heading = $base_right['is_heading'];
        $is_edit_base_enable = $base_right['is_edit_base_enable'];
        $is_edit_base_read = $base_right['is_edit_base_read'];
        $is_edit_base_update = $base_right['is_edit_base_update'];
        $is_list_base_enable = $base_right['is_list_base_enable'];
        $is_list_link_enable = $base_right['is_list_link_enable'];
        $is_body_link_enable = $base_right['is_body_link_enable'];
        $is_show_base_enable = $base_right['is_show_base_enable'];
        $is_show_link_enable = $base_right['is_show_link_enable'];
        $is_edit_link_read = $base_right['is_edit_link_read'];
        $is_edit_link_update = $base_right['is_edit_link_update'];
        $is_hier_base_enable = $base_right['is_hier_base_enable'];
        $is_hier_link_enable = $base_right['is_hier_link_enable'];
        $is_show_hist_attr_enable = $base_right['is_show_hist_attr_enable'];
        $is_edit_hist_attr_enable = $base_right['is_edit_hist_attr_enable'];
        $is_list_hist_attr_enable = $base_right['is_list_hist_attr_enable'];
        $is_list_hist_records_enable = $base_right['is_list_hist_records_enable'];
        $is_brow_hist_attr_enable = $base_right['is_brow_hist_attr_enable'];
        $is_brow_hist_records_enable = $base_right['is_brow_hist_records_enable'];
        $is_edit_email_base_create = $base_right['is_edit_email_base_create'];
        $is_edit_email_question_base_create = $base_right['is_edit_email_question_base_create'];
        $is_edit_email_base_update = $base_right['is_edit_email_base_update'];
        $is_edit_email_question_base_update = $base_right['is_edit_email_question_base_update'];
        $is_show_email_base_delete = $base_right['is_show_email_base_delete'];
        $is_show_email_question_base_delete = $base_right['is_show_email_question_base_delete'];
        // 'true' - значение по умолчанию
        $is_parent_full_sort_asc = true;
        $is_parent_page_sort_asc = true;
        //  Проверка Показывать Связь с признаком "Ссылка на основу"
        if ($role->is_list_link_baselink == false && $link->parent_is_base_link == true) {
            $is_list_link_enable = false;
            $is_body_link_enable = false;
            $is_show_link_enable = false;
            $is_edit_link_read = false;
            $is_hier_base_enable = false;
            $is_hier_link_enable = false;
        }
        //  Проверка скрывать поле или нет
        if ($link->parent_is_hidden_field == true) {
            $is_list_link_enable = false;
            // Не нужно '$is_body_link_enable = false;' - чтобы при прросмотре Пространство связь отображалась
            //$is_body_link_enable = false;
            $is_show_link_enable = false;
            $is_edit_link_read = false;
            //$is_edit_link_update = false;
            $is_hier_base_enable = false;
            $is_hier_link_enable = false;
            // При корректировке в форме ставится пометка hidden
            //$is_edit_link_update = false;
        }
        // Блок проверки по rolis, используя переменные $role, $relit_id и $link
        $roli = Roli::where('role_id', $role->id)->where('relit_id', $relit_id)->where('link_id', $link->id)->first();
        $is_roli_list_link_enable = false;
        $is_roli_body_link_enable = false;
        if ($roli != null) {
            //$is_roli_list_link_enable = $roli->is_list_link_enable;
            $is_list_link_enable = $roli->is_list_link_enable;
            $is_roli_body_link_enable = $roli->is_body_link_enable;
            $is_body_link_enable = $is_roli_body_link_enable;
            $is_show_link_enable = $roli->is_show_link_enable;
            $is_edit_link_read = $roli->is_edit_link_read;
            $is_edit_link_update = $roli->is_edit_link_update;
            $is_hier_base_enable = $roli->is_hier_base_enable;
            $is_hier_link_enable = $roli->is_hier_link_enable;
            $is_parent_full_sort_asc = $roli->is_parent_full_sort_asc;
            $is_parent_page_sort_asc = $roli->is_parent_page_sort_asc;
        }
        $is_edit_link_enable = $is_edit_link_read || $is_edit_link_update;

        return ['link_id' => $link->id,
            'is_list_base_calc' => $is_list_base_calc,
            'is_all_base_calcname_enable' => $is_all_base_calcname_enable,
            'is_list_base_sort_creation_date_desc' => $is_list_base_sort_creation_date_desc,
            'is_bsin_base_enable' => $is_bsin_base_enable,
            'is_bsmn_base_enable' => $is_list_base_calc && $is_bsin_base_enable,
            'is_exclude_related_records' => $is_exclude_related_records,
            'is_show_head_attr_enable' => $is_show_head_attr_enable,
            'is_view_prev_next' => $is_view_prev_next,
            'is_skip_count_records_equal_1_base_index' => $is_skip_count_records_equal_1_base_index,
            'is_skip_count_records_equal_1_item_body_index' => $is_skip_count_records_equal_1_item_body_index,
            'is_list_base_create' => $is_list_base_create,
            'is_list_base_read' => $is_list_base_read,
            'is_list_base_update' => $is_list_base_update,
            'is_list_base_delete' => $is_list_base_delete,
            'is_list_base_used_delete' => $is_list_base_used_delete,
            'is_list_base_byuser' => $is_list_base_byuser,
            'is_list_base_user_id' => $is_list_base_user_id,
            'is_heading' => $is_heading,
            'is_edit_base_enable' => $is_edit_base_enable,
            'is_edit_base_read' => $is_edit_base_read,
            'is_edit_base_update' => $is_edit_base_update,
            'is_list_base_enable' => $is_list_base_enable,
            'is_list_link_enable' => $is_list_link_enable,
            'is_body_link_enable' => $is_body_link_enable,
            'is_show_base_enable' => $is_show_base_enable,
            'is_show_link_enable' => $is_show_link_enable,
            'is_edit_link_enable' => $is_edit_link_enable,
            'is_edit_link_read' => $is_edit_link_read,
            'is_edit_link_update' => $is_edit_link_update,
            'is_hier_base_enable' => $is_hier_base_enable,
            'is_hier_link_enable' => $is_hier_link_enable,
            'is_show_hist_attr_enable' => $is_show_hist_attr_enable,
            'is_edit_hist_attr_enable' => $is_edit_hist_attr_enable,
            'is_list_hist_attr_enable' => $is_list_hist_attr_enable,
            'is_list_hist_records_enable' => $is_list_hist_records_enable,
            'is_brow_hist_attr_enable' => $is_brow_hist_attr_enable,
            'is_brow_hist_records_enable' => $is_brow_hist_records_enable,
            'is_edit_email_base_create' => $is_edit_email_base_create,
            'is_edit_email_question_base_create' => $is_edit_email_question_base_create,
            'is_edit_email_base_update' => $is_edit_email_base_update,
            'is_edit_email_question_base_update' => $is_edit_email_question_base_update,
            'is_show_email_base_delete' => $is_show_email_base_delete,
            'is_show_email_question_base_delete' => $is_show_email_question_base_delete,
//          'is_roli_list_link_enable' => $is_roli_list_link_enable,
//          'is_roli_body_link_enable' => $is_roli_body_link_enable,
            'is_parent_full_sort_asc' => $is_parent_full_sort_asc,
            'is_parent_page_sort_asc' => $is_parent_page_sort_asc,
        ];
    }

    // Не удалять
//
//        $items = $items->whereHas('child_mains', function ($query) {
//            $query->where('parent_item_id', 358);
//        });
//
//        $items = $items->whereHas('child_mains', function ($query) {
//            $query->whereHas('link', function ($query) {
//                $query->where('parent_is_unique', true);});
//        });

//        $items = $items->whereHas('child_mains', function ($query) {
//            $query->where('link_id', 11)->where('parent_item_id', 152);
//        });
//        $items = $items->whereHas('child_mains', function ($query) {
//            $query->where('link_id', 11)->whereHas('parent_item', function ($query) {
//                $query->where(strval('name_lang_0'), '<=',500);});
//        });

//        $items = $items->whereHas('child_mains', function ($query) {
//            $query->where('link_id', 11)->whereHas('parent_item', function ($query) {
//                $query->where(strval('name_lang_0'), '<=',500);});
//        })->whereHas('child_mains', function ($query) {
//      is_list_base_create      $query->where('link_id', 3)->whereHas('parent_item', function ($query) {
//                $query->whereDate('name_lang_0', '>','2020-02-09');});
//        });

    static function items_right(Base $base, Project $project, Role $role, $relit_id,
                                     $mains_item_id = null, $mains_link_id = null, $parent_proj = null, $view_ret_id = null, $current_item_id = null)
    {
        $base_right = null;
        $links = null;
        $items = null;
        $view_count = 0;
        $prev_item = null;
        $next_item = null;
        $current_index = null;
        $prev_index = null;
        $next_index = null;
        $seek_item = null;

        // Выборка из mains
//      "if ($mains_item_id && $mains_link_id && $parent_proj && $view_ret_id)" так некорректно
        if ($mains_item_id && $mains_link_id && $parent_proj) {
//            Не использовать "if ($view_ret_id != null)"
            $mains_link = Link::find($mains_link_id);
            if ($mains_link) {
//              $base_right = self::base_right($base, $role, $view_ret_id);
                $base_right = self::base_right($mains_link->child_base, $role, $view_ret_id);
                //        $mains = Main::all()->where('parent_item_id', $item->id)->where('link_id', $current_link->id)->sortBy(function ($main) {
                //            return $main->link->child_base->name() . $main->child_item->name();
                //        });
//            $items_ids = Main::select(DB::Raw('mains.child_item_id as id'))
//                ->join('items', 'mains.child_item_id', '=', 'items.id')
//                ->where('mains.parent_item_id', '=', $mains_item_id)
//                ->where('mains.link_id', '=', $mains_link_id)
//                ->get();
//            $coll_mains = collect();
//            foreach ($items_ids as $value) {
//                $coll_mains[$value['id']] = $value['id'];
//            }
//            $ids = $coll_mains->keys()->toArray();
//            $items = Item::whereIn('id', $ids);

//                $items_ids = Main::select(DB::Raw('mains.child_item_id as id'))
//                    ->join('items', 'mains.child_item_id', '=', 'items.id')
//                    ->where('mains.parent_item_id', '=', $mains_item_id)
//                    ->where('mains.link_id', '=', $mains_link_id);
//              Похожие запросы в ItemController::next_all_links_mains_calc() и GlobalController::items_right()
                $items_ids = Main::select(DB::Raw('mains.child_item_id as id'))
                    ->join('links', 'mains.link_id', '=', 'links.id')
                    ->join('items', 'mains.child_item_id', '=', 'items.id')
                    ->where('mains.parent_item_id', '=', $mains_item_id)
                    ->where('mains.link_id', '=', $mains_link_id)
                    ->where('links.parent_is_base_link', '=', false);

                if ($view_ret_id == 0) {
                    // Использовать '$parent_proj->id'
//                $items_ids = $items_ids
//                    ->where('items.project_id', '=', $parent_proj->id);
                    // Использовать '$project->id'
                    $items_ids = $items_ids
                        ->where('items.project_id', '=', $project->id);
                } else {
                    $items_ids = $items_ids
                        ->join('relips', 'items.project_id', '=', 'relips.parent_project_id')
                        ->where('relips.relit_id', '=', $view_ret_id)
                        ->where('relips.child_project_id', '=', $parent_proj->id);
                }
                $items = Item::joinSub($items_ids, 'items_ids', function ($join) {
                    $join->on('items.id', '=', 'items_ids.id');
                });
                if ($base_right['is_list_hist_records_enable'] == false) {
                    $items = $items->where('items.is_history', false);
                }
            }
            //dd(Link::find($mains_link_id));
            //dd($items->get());
            // Выборка из items
        } else {
            $base_right = self::base_right($base, $role, $relit_id);
            // Обязательно фильтр на два запроса:
            // where('base_id', $base->id)->where('project_id', $project->id)
            $items = Item::where('base_id', $base->id)->where('project_id', $project->id);
            if ($base_right['is_list_hist_records_enable'] == false) {
                $items = $items->where('items.is_history', false);
            }
        }
        // Такая же проверка и в GlobalController (function items_right()),
        // в ItemController (function next_all_links_mains_calc(), browser(), get_items_for_link(), get_items_ext_edit_for_link())
        if (($base_right['is_list_base_user_id'] == true) | ($base_right['is_list_base_byuser'] == true)) {
            if (Auth::check()) {
                if ($base_right['is_list_base_user_id'] == true) {
                    // $items = $items->where('id', GlobalController::glo_user_id());
//                  $items = $items->where('created_user_id', GlobalController::glo_user_id());
//                    $items = $items->whereHas('child_mains', function ($query) use () {
//                        $query->where('link_id', $usersetup_name_link_id);
//                    });
                    $items = self::get_items_user_id($items);
                }
                if ($base_right['is_list_base_byuser'] == true) {
                    $items = $items->where('created_user_id', GlobalController::glo_user_id());
                }
            } else {
                $items = null;
                //$collection = null;
            }
        }
        // Похожие строки items_right() и its_page()
        // -----------------------------------------
        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
        }
        if ($items != null) {
//            if ($current_item_id != null) {
//                $items = $items->where('id', $current_item_id);
//            }
            // Сортировать по дате создания записи в порядке убывания
            if ($base_right['is_list_base_sort_creation_date_desc'] == true) {
                //$items = $items->orderByDesc('created_user_id');
                $items = $items->latest();
            } elseif ($base->is_code_needed == true) {
                // Сортировка по коду
                if ($base->is_code_number == true) {
                    // Сортировка по коду числовому
                    $items = $items->selectRaw("*, code*1  AS code_value")->orderBy('code_value');
                } else {
                    $items = $items->orderBy('code');
                }
                // Если невычисляемое наименование
            } elseif ($base->is_calcname_lst == false) {
                // Сортировка по наименованию, не нужна
                // Если в вычисляемом наименовании есть число, то нули или пробелы спереди должны добавлятся для правильной сортировки
                $items = $items->orderBy($name);
                // Эта проверка нужна "if (count($items->get()) > 1)", иначе ошибка SQL
                // Если одна запись - нет смысла сортировать
            } elseif (count($items->get()) > 1) {
                //if (count($items->get()) > 0) {
                // Такая же проверка и в GlobalController (function items_right()),
                // в ItemController (function next_all_links_mains_calc(), browser(), get_items_for_link(), get_items_ext_edit_for_link())
//            if ($base_right['is_list_base_byuser'] == true) {
//                if (Auth::check()) {
//                    $items = $items->where('created_user_id', GlobalController::glo_user_id());
//                } else {
//                    $items = null;
//                    $collection = null;
//                }
//            }
//            if ($items != null) {
                // Сортировка по mains
                // иначе Сортировка по наименованию
                //if (!GlobalController::is_base_calcname_check($base, $base_right)) {
                // Не попадают в список $mains изображения/документы,
                // а также связанные поля (они в Mains не хранятся)
//            $mains = Main::select(DB::Raw('mains.child_item_id as item_id'))
//                ->join('links as ln', 'mains.link_id', '=', 'ln.id')
//                ->join('items as ct', 'mains.child_item_id', '=', 'ct.id')
//                ->join('bases as bs', 'ct.base_id', '=', 'bs.id')
//                ->where('ct.base_id', '=', $base->id)
//                ->where('ct.project_id', '=', $project->id)
//                ->where('bs.type_is_image', false)
//                ->where('bs.type_is_document', false)
//                ->orderBy('ln.parent_base_number')
//                ->orderBy('ct.' . $name)
//                ->distinct();
                // Не попадают в список $links изображения/документы
                // и с признаком "Ссылка на Основу"
                //->where('links.parent_is_base_link', false)
                // '->orderBy('links.parent_base_number')' обязательно нужно, в таком порядке и на экран записи выходят
                // '->get()' нужно
                $links = Link::select(DB::Raw('links.*'))
                    ->join('bases as pb', 'links.parent_base_id', '=', 'pb.id')
                    ->where('links.child_base_id', '=', $base->id)
                    ->where('links.parent_is_base_link', false)
                    ->where('pb.type_is_image', false)
                    ->where('pb.type_is_document', false)
                    ->orderBy('links.parent_base_number')
                    ->get();

                // '$items = $items->get();' нужно
                $items = $items->get();
                $str = "";
                // В $collection сохраняется в key - $item->id
                $collection = collect();
                foreach ($items as $item) {
                    $str = "";
                    foreach ($links as $link) {
                        $base_link_right = self::base_link_right($link, $role, $relit_id);
                        // Если 'Показывать Связь в списке' = true
                        if ($base_link_right['is_list_link_enable'] == true) {
                            $item_find = GlobalController::view_info($item->id, $link->id);
                            if ($item_find) {
                                // Формирование вычисляемой строки для сортировки
                                // Для строковых данных для сортировки берутся первые 50 символов
                                if ($item_find->base->type_is_list()
                                    || $item_find->base->type_is_string()
                                    || $item_find->base->type_is_text()) {
                                    $str = $str . str_pad(trim($item_find[$name]), 50);
                                } // '$base_link_right['is_parent_full_sort_asc']' используется
                                elseif ($item_find->base->type_is_date() && $base_link_right['is_parent_full_sort_asc'] == false) {
                                    $str = $str . trim($item_find->dt_desc());
                                } else {
                                    // Числовые значения хранятся в items с нулями спереди для правильной сортировки
                                    $str = $str . trim($item_find[$name]);
                                }
                            } else {
                                $str = $str . str_pad(' ', 50);
                            }
                            $str = $str . "|";
                        }
                    }
                    // В $collection сохраняется в key - $item->id
                    $collection[$item->id] = $str;
                }
//            Сортировка коллекции по значению
                $collection = $collection->sort();
//            Не удалять
//            $mains = Main::select(DB::Raw('mains.child_item_id as item_id'))
//                ->join('links as ln', 'mains.link_id', '=', 'ln.id')
//                ->join('items as ct', 'mains.child_item_id', '=', 'ct.id')
//                ->join('items as pt', 'mains.parent_item_id', '=', 'pt.id')
//                ->join('bases as bs', 'pt.base_id', '=', 'bs.id')
//                ->where('ct.base_id', '=', $base->id)
//                ->where('ct.project_id', '=', $project->id)
//                ->where('bs.type_is_image', false)
//                ->where('bs.type_is_document', false)
//                ->orderBy('pt.base_id')
//                ->orderBy('pt.name_lang_0')
//                ->distinct();
////
//            $items = Item::joinSub($mains, 'mains', function ($join) {
//                $join->on('items.id', '=', 'mains.item_id');
//            });
                $ids = $collection->keys()->toArray();
                $items = Item::whereIn('id', $ids)
                    ->orderBy(\DB::raw("FIELD(id, " . implode(',', $ids) . ")"));

                //}
            }
            //}
            //}
        }
//        $itget = null;
//        if ($items != null) {
//            // Одинаковые строка/строки в этой функции
//            $itget = $items->get();
//        } else {
//            $itget = null;
//        }
//
//        if ($itget) {
        if ($items) {
            // Поиск предыдущей/следующей записи относительно переданного $current_item_id
            if ($current_item_id != null) {
                $current_item = Item::find($current_item_id);
                if ($current_item) {
                    $itget = $items->get();
                    $current_index = $itget->search($current_item);
                    // Использовать '!==' для правильного сравнения
                    if ($current_index !== false) {
                        $prev_index = $current_index - 1;
                        if ($prev_index >= 0) {
                            $seek_item = $itget->get($prev_index);
                            if ($seek_item) {
                                $prev_item = $seek_item;
                            }
                        }
                        $next_index = $current_index + 1;
                        if ($next_index >= 0) {
                            $seek_item = $itget->get($next_index);
                            if ($seek_item) {
                                $next_item = $seek_item;
                            }
                        }
                    }
                    // Одинаковые строка/строки в этой функции
                    // Нужно 'items.id', иначе - сообщение об ошибке
                    $items = $items->where('items.id', $current_item_id);
                    //$itget = $items->get();
                }
            }
//            $view_count = count($itget);
//            // Такая же проверка в GlobalController::items_right() и start.php
//            if ($base_right['is_list_base_create'] == true) {
//                //$view_count = $view_count . self::base_max_count_for_start($base);
//                $view_count = $view_count;
//            }
//        } else {
//            $view_count = mb_strtolower(trans('main.no_access'));
        }
//        return ['items' => $items, 'itget' => $itget, 'view_count' => '(' . $view_count . ')',
//            'prev_item' => $prev_item, 'next_item' => $next_item];
        return ['links' => $links,
            'items' => $items,
            'prev_item' => $prev_item, 'next_item' => $next_item];
    }

    // Похожие строки в ItemController::next_all_links_mains_calc(), GlobalController::get_items_user_id(), GlobalController::base_user_id_maxcount_validate()
    function get_items_user_id($items)
    {
        // Устанавливает фильтр для Пользователи по текущему пользователю
        // Сравнение на равенство по наименованию/логину
        // Подразумевается, что наименование/логин имеет уникальное значение по base Пользователи
        $usersetup_project_id = env('USERSETUP_PROJECT_ID');
        $usersetup_base_id = env('USERSETUP_BASE_ID');
        $usersetup_name_link_id = env('USERSETUP_NAME_LINK_ID');
        $username = GlobalController::glo_user()->name();
        //if (Auth::check()) {
        if ($usersetup_project_id != '' && $usersetup_base_id != '' && $usersetup_name_link_id != '') {
            $items = $items->where('project_id', $usersetup_project_id)
                ->where('base_id', $usersetup_base_id)
                ->whereHas('child_mains', function ($query) use ($usersetup_name_link_id, $username) {
                    $query->where('link_id', $usersetup_name_link_id)
                        ->whereHas('parent_item', function ($query) use ($username) {
                            $query->where('name_lang_0', '=', $username);
                        });
                });
        }
        //}
        return $items;
    }

    static function item_index_calc_data_1(Project $project, Item $item, Role $role, $relit_id, $link = null, $tree_array, $is_next_all_mains_calc)
    {
        // Шаблон проекта
        $template_project = Template::select(DB::Raw('templates.id'))
            ->where('id', '=', $project->template_id);
        // Шаблоны relits
        $par_tems = Relit::select(DB::Raw('relits.child_template_id as id'))
            ->where('parent_template_id', '=', $project->template_id);
        $templates_relits = Template::select(DB::Raw('templates.id'))
            ->joinSub($par_tems, 'par_tems', function ($join) {
                $join->on('templates.id', '=', 'par_tems.id');
            });
        // Объединить шаблоны в одну выборку
        // Использовать union() т.к. эта команда возвращает уникальные записи
        // unionall() - возвращает все записи
        // distinct() - не обязательная команда в данном случае
        $templates_project = $template_project->union($templates_relits)->distinct();

        // Расчет $links
        $links = Link::select(DB::Raw('links.*'))
            ->where('links.parent_base_id', '=', $item->base_id);
        // Фильтр links.child_base.template_id = templates_project.id
        $links = $links->join('bases', 'links.child_base_id', '=', 'bases.id')
            ->joinSub($templates_project, 'templates_project', function ($join) {
                $join->on('bases.template_id', '=', 'templates_project.id');
            });

        // Расчет $mains
        $mains = Main::select(DB::Raw('mains.*'))
            ->where('mains.parent_item_id', '=', $item->id);
        // Фильтр mains.link_id = links.id
        $mains = $mains->joinSub($links, 'links', function ($join) {
            $join->on('mains.link_id', '=', 'links.id');
        });
    }

    // Предыдущая версия функции
    static function item_index_calc_data_2(Project $project, Item $item)
    {
        // Шаблон проекта
        $template_project = Template::select(DB::Raw('templates.id'))
            ->where('id', '=', $project->template_id);
        // Шаблоны relits
        $par_tems = Relit::select(DB::Raw('relits.child_template_id as id'))
            ->where('parent_template_id', '=', $project->template_id);
        $templates_relits = Template::select(DB::Raw('templates.id'))
            ->joinSub($par_tems, 'par_tems', function ($join) {
                $join->on('templates.id', '=', 'par_tems.id');
            });
        // Объединить шаблоны в одну выборку
        // Использовать union() т.к. эта команда возвращает уникальные записи
        // unionall() - возвращает все записи
        // distinct() - не обязательная команда в данном случае
        $templates_project = $template_project->union($templates_relits)->distinct();

        // Расчет $links
        $links = Link::select(DB::Raw('links.*'))
            ->where('links.parent_base_id', '=', $item->base_id);
        // Фильтр links.child_base.template_id = templates_project.id
        $links = $links->join('bases', 'links.child_base_id', '=', 'bases.id')
            ->joinSub($templates_project, 'templates_project', function ($join) {
                $join->on('bases.template_id', '=', 'templates_project.id');
            });

        // Расчет $mains
        $mains = Main::select(DB::Raw('mains.*'))
            ->where('mains.parent_item_id', '=', $item->id);
        // Фильтр mains.link_id = links.id
        $mains = $mains->joinSub($links, 'links', function ($join) {
            $join->on('mains.link_id', '=', 'links.id');
        });
    }

    static function its_page(Role $role, $relit_id, $links, $items_paginate)
    {
        // Похожие строки items_right() и its_page
        // ---------------------------------------
        // '$its_page = $items_paginate;' использовать
        $its_page = $items_paginate;
        if ($links) {
            if (count($items_paginate) > 0) {
                // Список $items на странице
                $array_items = $items_paginate->items();
                $name = "";  // нужно, не удалять
                $index = array_search(App::getLocale(), config('app.locales'));
                if ($index !== false) {   // '!==' использовать, '!=' не использовать
                    $name = 'name_lang_' . $index;
                }
                $collection = collect();
                $str = "";
                foreach ($array_items as $value) {
                    $str = "";
                    foreach ($links as $link) {
                        $base_link_right = self::base_link_right($link, $role, $relit_id);
                        // Если 'Показывать Связь в списке' = true
                        if ($base_link_right['is_list_link_enable'] == true) {
                            $item_find = GlobalController::view_info($value['id'], $link->id);
                            if ($item_find) {
                                // Формирование вычисляемой строки для сортировки
                                // Для строковых данных для сортировки берутся первые 50 символов
                                if ($item_find->base->type_is_list()
                                    || $item_find->base->type_is_string()
                                    || $item_find->base->type_is_text()) {
                                    $str = $str . str_pad(trim($item_find[$name]), 50);
                                } // '$base_link_right['is_parent_page_sort_asc']' используется
                                elseif ($item_find->base->type_is_date() && $base_link_right['is_parent_page_sort_asc'] == false) {
                                    $str = $str . trim($item_find->dt_desc());
                                } else {
                                    $str = $str . trim($item_find[$name]);
                                }
                            } else {
                                $str = $str . str_pad(' ', 50);
                            }
                            $str = $str . "|";
                        }
                    }
                    // В $collection сохраняется в key - $item->id
                    $collection[$value['id']] = $str;
                }

//            Сортировка коллекции по значению
                $collection = $collection->sort();

                $ids = $collection->keys()->toArray();
                $its_page = Item::whereIn('id', $ids)
                    ->orderBy(\DB::raw("FIELD(id, " . implode(',', $ids) . ")"));
//                $its_page = Item::whereIn('id', $ids);
//                $its_page = $its_page->latest();
//                //$its_page = $its_page->oldest();
                // 'get()' нужно
                $its_page = $its_page->get();
            }

        }
        return $its_page;
    }

    static function get_array_parent_related(Base $base)
    {
        $array_start = false;
        $array_result = false;

        $collection_start = collect();
        $collection_result = collect();

        // Связанные связи/поля выбираются
        $links = Link::select(DB::Raw('*'))
            ->where('child_base_id', '=', $base->id)
            ->where('parent_is_parent_related', true)
            ->orderBy('parent_base_number')->get();
        if ($links) {
            foreach ($links as $link) {
                // В $collection_result в key сохраняется $link->parent_parent_related_start_link_id
                $collection_start[$link->parent_parent_related_start_link_id] = true;
                // В $collection сохраняется в key - $link->parent_parent_related_start_link_id
                $collection_result[] = ['link_id' => $link->id,
                    'parent_parent_related_start_link_id' => $link->parent_parent_related_start_link_id,
                    'parent_parent_related_result_link_id' => $link->parent_parent_related_result_link_id];
            }

            $array_start = $collection_start->keys()->toArray();
            $array_result = $collection_result->toArray();
        }

        return ['array_start' => $array_start, 'array_result' => $array_result];

    }

    static function empty_html()
    {
        return trans('main.empty');
    }

    static function image_is_missing_html()
    {
        //        Изображение отсутствует
        return trans('main.image_is_missing');
    }

//  Если тип-вычисляемое наименование(Вычисляемое наименование) и Показывать Основу с вычисляемым наименованием
//  или если тип-не вычисляемое наименование(Вычисляемое наименование)
//  В $base_right могут передаваться и $base_right и $base_link_right
    static function is_base_calcname_check($base, $base_right)
    {
//        $var = ($base->is_calcname_lst == true && $base_right['is_all_base_calcname_enable'] == true)
//            || ($base->is_calcname_lst == false);
//        echo "is_calcname_lst = " . $base->is_calcname_lst;
//        echo ", is_all_base_calcname_enable = " . $base_right['is_all_base_calcname_enable'];
//        echo ", result = " . $var;
//        return ($base->is_calcname_lst == true && $base_right['is_all_base_calcname_enable'] == true)
//            || ($base->is_calcname_lst == false);
//        return ($base->is_calcname_lst == true && $base->is_calcnm_correct_lst == true && $base_right['is_all_base_calcname_enable'] == true)
//            || ($base->is_calcname_lst == false);
        return ($base->is_calcname_lst == true && $base->is_calcnm_correct_lst == true)
            || ($base->is_calcname_lst == false);
    }

    //  Если тип-вычисляемое наименование(Вычисляемое наименование) и Вычисляемое наименование корректно(заполняется)
//  или если тип-не вычисляемое наименование(Вычисляемое наименование)
// Используется в функции ItemController::next_all_links_mains_calc()
    static function is_base_calcnm_correct_check($base)
    {
        return ($base->is_calcname_lst == true && $base->is_calcnm_correct_lst == true)
            || ($base->is_calcname_lst == false);
    }

    static function check_project_user(Project $project, Role $role)
    {
        $result = false;
        if ($project->template_id == $role->template_id) {
            if ($role->is_default_for_external == true) {
                $result = true;
            } else {
                if ($role->is_author == true) {
                    if (Auth::check()) {
//                        $result = $project->user_id == GlobalController::glo_user_id();
                        // Проверка, если доступ у этого пользователя
                        $access = Access::where('project_id', $project->id)
                            ->where('user_id', GlobalController::glo_user_id())
                            ->where('role_id', $role->id)
                            ->where('is_access_allowed', true)->first();
                        if ($access) {
                            $result = true;
                        } else {
                            $result = false;
                        }
                    } else {
                        $result = false;
                    }
                    // Обычная роль (не $role->is_default_for_external и не $role->is_author)
                } else {
                    if (Auth::check()) {
                        // Проверка, если доступ у этого пользователя
                        $access = Access::where('project_id', $project->id)
                            ->where('user_id', GlobalController::glo_user_id())
                            ->where('role_id', $role->id)
                            ->where('is_access_allowed', true)->first();
                        if ($access) {
                            $result = true;
                        } else {
                            $result = false;
                        }
                    } else {
                        $result = false;
                    }
                }
            }
        } else {
            $result = false;
        }
        return $result;
    }

    static function check_project_item_user(Project $project, Item $item = null, Role $role, $usercode)
    {
        $result = false;

//        // Если проекты равны
//        if ($project->id == $item->project_id) {
//            // Стандартная проверка
//            $result = self::check_project_user($project, $role);
//        } // Если проекты разные
//        else {
//            // Проверка на равенство кодов пользователя: переданного в функцию и текущего
//            $user_id = GlobalController::usercode_uncalc($usercode);
//            $result = ($user_id == Auth::user()->id);
//            if ($result) {
//                // Проверка на равенство шаблонов
//                $result = ($project->template_id == $role->template_id);
//                if ($result) {
//                    // Проверка на наличие проекта в Relips
//                    $result = self::is_found_parent_project($project, $item->project);
//                }
//            }
//        }

        // Проверка на равенство кодов пользователя: переданного в функцию и текущего
        $user_id = GlobalController::usercode_uncalc($usercode);
        if (Auth::check()) {
            $result = ($user_id == Auth::user()->id);
        } else {
            $result = true;
        }
        if ($result) {
            // Проверка на равенство шаблонов
            $result = ($project->template_id == $role->template_id);
            if ($result) {
                // Передача параметра $item=null в функциях ItemController: ext_create() и ext_store()
                //if ($item != null)
                if ($item) {
                    // Если проекты равны
                    if ($project->id == $item->project_id) {
                        // Стандартная проверка
                        $result = self::check_project_user($project, $role);
                        // Если проекты разные
                    } else {
                        // Проверка на наличие проекта в Relips
                        $result = self::is_found_parent_project($project, $item->project);
                    }
                }
            }
        }
        return $result;
    }

//    static function to_html($item)
//    {
//        $str = trim($item->base->sepa_calcname);
//        return str_replace($str, $str . '<br>', $item->name());
//    }

// На вход число в виде строки
// На выходе это же число с нулями спереди
// Нужно для правильной сортировки чисел
    static function save_number_to_item(Base $base, $str)
    {
        // Максимальное количество разрядов для числа
        $max_len = 17;
        $work_len = 0;
        $result = "";
        $str = trim($str);
        $first_char = "";
        $sminus = "-";
        // Первый символ равен "-"
        if (substr($str, 0, 1) == $sminus) {
            // Первый символ убирается
            $str = substr($str, 1);
            $work_len = $max_len - 1;
            $first_char = $sminus;
        } else {
            $work_len = $max_len;
            $first_char = "";
        }
        if ($base->type_is_number()) {
            $digits_num = $base->digits_num;

            // Число целое
            if ($digits_num == 0) {
                $int_value = intval($str);
                $result = $first_char . str_pad($int_value, $work_len, "0", STR_PAD_LEFT);

                // Число вещественное
            } else {
                $float_value = floatval($str);
                $float_value = sprintf("%1." . $digits_num . "f", floatval($float_value));
                $result = $first_char . str_pad($float_value, $work_len, "0", STR_PAD_LEFT);
            }
        }
        return $result;
    }

// На вход число с нулями спереди
// На выходе это же число в виде строки
// Нужно для правильного отображения чисел
// $numcat = true/false - вывод числовых полей с разрядом тысячи/миллионы/миллиарды
// $rightnull = true/false - у вещественных чисел убрать правые нули после запятой
    static function restore_number_from_item(Base $base, $str, $numcat = false, $rightnull = true)
    {
        // Максимальное количество разрядов для числа
        $result = "";
        $str = trim($str);
        $first_char = "";
        $sminus = "-";
        // Первый символ равен "-"
        if (substr($str, 0, 1) == $sminus) {
            // Первый символ убирается
            $str = substr($str, 1);
            $first_char = $sminus;
        } else {
            $first_char = "";
        }
        if ($base->type_is_number()) {
            $digits_num = $base->digits_num;

            // Число целое
            if ($digits_num == 0) {
                $int_value = intval($str);
                // $numcat = true/false - вывод числовых полей с разрядом тысячи/миллионы/миллиарды
                if ($numcat) {
                    $result = $first_char . number_format($int_value, $digits_num, '.', ' ');
                } else {
                    $result = $first_char . strval($int_value);
                }

                // Число вещественное
            } else {
                $float_value = floatval($str);
                // $numcat = true/false - вывод числовых полей с разрядом тысячи/миллионы/миллиарды
                if ($numcat) {
                    $result = $first_char . number_format($float_value, $digits_num, '.', ' ');
                } else {
                    $result = $first_char . sprintf("%1." . $digits_num . "f", floatval($float_value));
                }
                if ($rightnull == true) {
                    $result = rtrim(rtrim($result, '0'), '.');
                }
            }
        }
        return $result;
    }

// Возвращает первые 255 символов переданной строки
    static function itnm_left($str)
    {
        // Убрать HTML-теги
        $str = strip_tags($str);
        // Нужно - убрать символы перевода строки (https://php.ru/forum/threads/udalenie-simvolov-perevoda-stroki.25065/)
        //$str = str_replace(array("\r\n", "\r", "\n"), '', $str);
        $str = str_replace(array("\r\n", "\r", "\n"), '\~', $str);
        //ограниченные 255 - размером полей хранятся в $item->name_lang_0 - $item->name_lang_3
        $maxlen = 255;
        $result = "";
        // похожи GlobalController::itnm_left() и Item.php ("...")
        if (mb_strlen($str) > $maxlen) {
            $result = mb_substr($str, 0, $maxlen - 3) . "...";
        } else {
            $result = mb_substr($str, 0, $maxlen);
        }
        return $result;
    }

    static function it_text_name(Item $item)
    {
        $result = "";
        //$text = $item->text();
        $text = Text::where('item_id', $item->id)->first();
        if ($text) {
            $result = $text->name();
        }
        return $result;
    }

    static function it_txnm_n2b(Item $item)
    {
        $result = nl2br(self::it_text_name($item));
        return $result;
    }

// Проверяет текст на запрещенные html-теги
    static function text_html_check($text)
    {
        // Пробелы нужны "< html" и т.д.
        $array = array(
//            "< html",
            "<html", "</html",
            "<head", "</head",
            "<body", "</body",
            "<script", "</script",
            "<applet", "</applet",
            "<form", "</form",
            "<input", "</input",
            "<button", "</button",
            "<audio", "</audio",
            "<img", "</img",
            "<video", "</video",
            "<a", "</a",
            "onblur",
            "onchange",
            "onclick",
            "ondblclick",
            "onfocus",
            "onkeydown",
            "onkeypress",
            "onkeyup",
            "onload",
            "onmousedown",
            "onmousemove",
            "onmouseout",
            "onmouseover",
            "onmouseup",
            "onreset",
            "onselect",
            "onsubmit",
            "onunload"
        );
        $result = false;
        $message = "";
        if ($text == "" || $text == null) {
            $result = false;
        } else {
            foreach ($array as $value) {
                // Для поиска используется без пробела, например "<html" stripos
                //     if (mb_strpos(mb_strtolower($text), str_replace(" ", "", $value)) === false) {
                // Поиск без учета регистра с помощью функции stripos
                // Нужно так проверять "=== false" (https://fb.ru/article/375154/funktsiya-strpos-v-php-opredelenie-pozitsii-podstroki)
                //if (mb_stripos($text, str_replace(" ", "", $value)) === false) {
                if (mb_stripos($text, $value) === false) {
                } else {
                    $result = true;
                    // В переменную message присваивается с пробелом, чтобы при выводе echo $message эти теги не срабатывали
                    $message = trans('main.text_must_not_contain') . " '" . $value . "'";
                    break;
                }
            }
        }
        return ['result' => $result, 'message' => $message];
    }

    static function option_empty()
    {
        return '- ' . mb_strtolower(trans('main.empty')) . ' -';
    }

    static function option_all_links()
    {
        return '- ' . mb_strtolower(trans('main.all_links')) . ' -';
    }

// Алгоритмы одинаковые в types.img.height.blade.php и GlobalController::types_img_height()
    static function types_img_height($size)
    {
        $result = '';
        if ($size == "small") {
            $result = '"50"';
        } elseif ($size == "medium") {
            $result = '"250"';
        } elseif ($size == "big") {
            $result = '"450"';
        }
        return $result;
    }

// Алгоритмы одинаковые в view.img.blade.php и GlobalController::view_img()
    static function view_img(Item $item, $size, $filenametrue, $link, $img_fluid, $title)
    {
        $result = '';
        if ($item->base->type_is_image()) {
            if ($item->img_doc_exist()) {
                if ($filenametrue == true) {
                    if ($link == true) {
                        $result = '<a href="' . Storage::url($item->filename(true)) . '">';
                    }
                    $result = $result . '<img src="' . Storage::url($item->filename(true)) . '"';
                } else {
                    if ($link == true)
                        $result = $result . '<a href="' . Storage::url($item->filename()) . '">';
                }
                $result = $result . '<img ';
                if ($img_fluid == true) {
                    $result = $result . 'class="img-fluid"';
                }
                $result = $result . 'src="' . Storage::url($item->filename()) . '"';
                $result = $result . 'height=' . GlobalController::types_img_height($size)
                    . 'alt="" title=';
                if ($title == "") {
                    $result = $result . '"' . $item->title_img() . '"';
                } elseif ($title == "empty") {
                    $result = $result . '""';
                } else {
                    $result = $result . '"' . $title . '"';
                }
                $result = $result . '>';
                if ($link == true) {
                    $result = $result . '</a>';
                }
                if ($item->is_moderation_info() == true) {
                    $result = $result . '<div class="text-danger">';
                    $result = $result . $item->title_img() . '</div>';
                }
            } else {
                $result = $result . '<div class="text-danger">';
                $result = $result . GlobalController::image_is_missing_html() . '</div>';
            }
        }
        return $result;
    }

// Алгоритмы одинаковые в view.doc.blade.php и GlobalController::view_doc()
    static function view_doc(Item $item, $usercode)
    {
        $result = '';
        if ($item->base->type_is_document()) {
            if ($item->img_doc_exist()) {
                //$result = '<a href = "' . Storage::url($item->filename()) . '" target = "_blank"  title = "' . $item->title_img() . '" >' . trans('main.open_document') . '</a>';
                $result = '<a href = "' . route('item.doc_download', ['item' => $item, 'usercode' => $usercode])
                    . '" target = "_blank"  title = "' . $item->title_img() . '" >'
                    . trans('main.open_document') . '</a>';
            } else {
                $result = '<div class="text-danger">' . GlobalController::empty_html() . '</div>';
            }
        }
        return $result;
    }

// Сообщение "максимальное количество записей" для start.php
    static function base_max_count_for_start(Base $base)
    {
        $result = '';
        if ($base->maxcount_lst > 0) {
            //if ($base->type_is_list() || $base->type_is_image() || $base->type_is_document()) {
            $result = '/' . $base->maxcount_lst;
            //}
        }
        return $result;
    }

// Сообщение "максимальное количество записей" в $base
    static function base_maxcount_message(Base $base)
    {
        $result = '';
        if ($base->maxcount_lst > 0) {
            //if ($base->type_is_list() || $base->type_is_image() || $base->type_is_document()) {
            $result = trans('main.base') . ' "' . $base->name() . '": '
                . trans('main.max_count_message_first') . ' ' . $base->maxcount_lst;
            if ($base->is_del_maxcnt_lst == true) {
                $result = $result . ', ' . PHP_EOL . trans('main.is_del_max_cnt_message');
            }
            //}
        }
        return $result;
    }

// Проверка на максимальное количество записей в $base
// $added - true, проверка при добавлении; - false, общая проверка
    static function base_maxcount_validate(Project $project, Base $base, bool $added)
    {
        $result = '';
        $error = false;
        $maxcount = $base->maxcount_lst;
        // Проверка нужна
        if ($base->is_del_maxcnt_lst == false) {
            if ($maxcount > 0) {
                //if ($base->type_is_list() || $base->type_is_image() || $base->type_is_document()) {
                $items_count = Item::where('project_id', $project->id)->where('base_id', $base->id)->count();
                if ($added == true) {
                    // '>=' используется
                    if ($items_count >= $maxcount) {
                        $error = true;
                    }
                } else {
                    // '>' используется
                    if ($items_count > $maxcount) {
                        $error = true;
                    }
                }
                if ($error == true) {
                    $result = trans('main.max_count_message_second') . $base->names()
                        . trans('main.max_count_message_third') . '. '
                        . self::base_maxcount_message($base) . '!';
                }
                //}
            }
        }
        return $result;
    }

    // Сообщение "максимальное количество записей" в $base по id пользователя для основы Пользователи
    // Похожие строки в ItemController::next_all_links_mains_calc(), GlobalController::get_items_user_id(), GlobalController::base_user_id_maxcount_validate()
    static function base_user_id_maxcount_message(Base $base)
    {
        $result = '';
        if ($base->maxcount_user_id_lst > 0) {
            //if ($base->type_is_list() || $base->type_is_image() || $base->type_is_document()) {
            $result = trans('main.base') . ': '
                . trans('main.max_count_message_first') . ' ' . $base->maxcount_user_id_lst
                . ' (' . mb_strtolower(trans('main.is_list_base_user_id')) . ')';
            //}
        }
        return $result;
    }

// Проверка на максимальное количество записей в $base по id пользователя для основы Пользователи
// $added - true, проверка при добавлении; - false, общая проверка
    static function base_user_id_maxcount_validate(Project $project, Base $base, bool $added)
    {
        $result = '';
        $error = false;
        $maxcount = $base->maxcount_user_id_lst;
        if ($maxcount > 0) {
            if (Auth::check()) {
                //if ($base->type_is_list() || $base->type_is_image() || $base->type_is_document()) {
                // Фильтр по пользователю, создавшему $item
                $items_count = 0;

                $usersetup_project_id = env('USERSETUP_PROJECT_ID');
                $usersetup_base_id = env('USERSETUP_BASE_ID');
                $usersetup_name_link_id = env('USERSETUP_NAME_LINK_ID');
                $username = GlobalController::glo_user()->name();

                if ($usersetup_project_id != '' && $usersetup_base_id != '' && $usersetup_name_link_id != '') {
                    $items_count = Item::where('project_id', $usersetup_project_id)
                        ->where('base_id', $usersetup_base_id)
                        ->whereHas('child_mains', function ($query) use ($usersetup_name_link_id, $username) {
                            $query->where('link_id', $usersetup_name_link_id)
                                ->whereHas('parent_item', function ($query) use ($username) {
                                    $query->where('name_lang_0', '=', $username);
                                });
                        })->count();
                }

                if ($added == true) {
                    if ($items_count >= $maxcount) {
                        $error = true;
                    }
                } else {
                    if ($items_count > $maxcount) {
                        $error = true;
                    }
                }
                if ($error == true) {
                    $result = trans('main.max_count_message_second') . $base->names()
                        . trans('main.max_count_message_third') . '. '
                        . self::base_user_id_maxcount_message($base) . '!';
                }
                //}
            } else {
                $result = trans('main.please_login_or_register') . '!';
            }
        }
        return $result;
    }

    // Сообщение "максимальное количество записей" в $base по пользователю-автору записи
    static function base_byuser_maxcount_message(Base $base)
    {
        $result = '';
        if ($base->maxcount_byuser_lst > 0) {
            if ($base->type_is_list() || $base->type_is_image() || $base->type_is_document()) {
                $result = trans('main.base') . ': '
                    . trans('main.max_count_message_first') . ' ' . $base->maxcount_byuser_lst
                    . ' (' . mb_strtolower(trans('main.is_list_base_byuser')) . ')';
            }
        }
        return $result;
    }

// Проверка на максимальное количество записей в $base по пользователю-автору записи
// $added - true, проверка при добавлении; - false, общая проверка
    static function base_byuser_maxcount_validate(Project $project, Base $base, bool $added)
    {
        $result = '';
        $error = false;
        $maxcount = $base->maxcount_byuser_lst;
        if ($maxcount > 0) {
            if (Auth::check()) {
//                if ($base->type_is_list() || $base->type_is_image() || $base->type_is_document()) {
                // Фильтр по пользователю, создавшему $item
                $items_count = Item::where('project_id', $project->id)
                    ->where('base_id', $base->id)
                    ->where('created_user_id', GlobalController::glo_user_id())
                    ->count();
                if ($added == true) {
                    if ($items_count >= $maxcount) {
                        $error = true;
                    }
                } else {
                    if ($items_count > $maxcount) {
                        $error = true;
                    }
                }
                if ($error == true) {
                    $result = trans('main.max_count_message_second') . $base->names()
                        . trans('main.max_count_message_third') . '. '
                        . self::base_byuser_maxcount_message($base) . '!';
                }
//                }
            } else {
                $result = trans('main.please_login_or_register') . '!';
            }
        }
        return $result;
    }

    // Сообщение "максимальное количество записей" в $link
    static function link_maxcount_message(Link $link)
    {
        $result = '';
        if ($link->link_maxcount > 0) {
            $result = trans('main.link') . ': '
                . trans('main.max_count_message_first') . ' ' . $link->link_maxcount;
        }
        return $result;
    }

// Проверка на максимальное количество записей в $link
// $added - true, проверка при добавлении; - false, общая проверка
    static function link_maxcount_validate(Project $project, Link $link, bool $added)
    {
        $result = '';
        $error = false;
        $maxcount = $link->link_maxcount;
        if ($maxcount > 0) {
            // Не использовать '->where('mains.parent_item_id', '=', $item->id)'
            $mains_count = Main::select(DB::Raw('mains.*'))
                ->join('links as ln', 'mains.link_id', '=', 'ln.id')
                ->join('items as ct', 'mains.child_item_id', '=', 'ct.id')
                ->where('ct.project_id', '=', $project->id)
                ->where('mains.link_id', '=', $link->id)
                ->count();
            if ($added == true) {
                if ($mains_count >= $maxcount) {
                    $error = true;
                }
            } else {
                if ($mains_count > $maxcount) {
                    $error = true;
                }
            }
            if ($error == true) {
                $result = trans('main.max_count_message_second') . $link->child_base->names()
                    . trans('main.max_count_message_third') . '. '
                    . self::link_maxcount_message($link) . '! (' . $link->info_full() . ')';
            }
        }
        return $result;
    }

    // Сообщение "максимальное количество записей" в $link - $item
    static function link_item_maxcount_message(Link $link, Item $item)
    {
        $result = '';
        if ($link->child_maxcount > 0) {
//            $result = trans('main.link') . ' - ' . trans('main.item') . ': '
//                . $item->base->name() . ' ' . $item->name()
//                . trans('main.max_count_message_first') . ' ' . $link->child_maxcount;
            $result = $item->base->name() . ' "' . $item->name() . '": '
                . trans('main.max_count_message_first') . ' ' . $link->child_maxcount;
        }
        return $result;
    }

// Проверка на максимальное количество записей в $link - $item
// $added - true, проверка при добавлении; - false, общая проверка
    static function link_item_maxcount_validate(Project $project, Item $item, Link $link, bool $added)
    {
        $result = '';
        $error = false;
        $maxcount = $link->child_maxcount;
        if ($maxcount > 0) {
            $mains_count = Main::select(DB::Raw('mains.*'))
                ->join('links as ln', 'mains.link_id', '=', 'ln.id')
                ->join('items as ct', 'mains.child_item_id', '=', 'ct.id')
                ->where('ct.project_id', '=', $project->id)
                ->where('mains.parent_item_id', '=', $item->id)
                ->where('mains.link_id', '=', $link->id)
                ->count();
            if ($added == true) {
                if ($mains_count >= $maxcount) {
                    $error = true;
                }
            } else {
                if ($mains_count > $maxcount) {
                    $error = true;
                }
            }
            if ($error == true) {
                $result = trans('main.max_count_message_second') . $link->child_base->names()
                    . trans('main.max_count_message_third') . '. '
                    . self::link_item_maxcount_message($link, $item) . '! (' . $link->info_full() . ')';
            }
        }
        return $result;
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

    // Вывод объекта по имени главного $item и $link
    static function view_info($child_item_id, $link_id)
    {
        // Нужно '$item = null;'
        $item = null;
        $item_find = Item::find($child_item_id);
        $link_find = Link::find($link_id);
        $view_enable = false;
        //
        if ($item_find && $link_find) {
            $view_enable = self::view_enable($link_id, $child_item_id);
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

    // Возможен вывод объекта по имени главного $item и $link
    static function view_enable($link_id, $child_item_id = null)
    {
        $link_find = Link::find($link_id);
        $result = false;
        //
        if ($link_find) {
            // Если установлено 'Доступно от значения поля Логический'
            if ($link_find->parent_is_enabled_boolean_value) {
                $link_bool = Link::find($link_find->parent_enabled_boolean_value_link_id);
                if ($link_bool) {
//                    Как правило, при добавлении записи $child_item_id == null
                    if ($child_item_id == null) {
                        $result = false;
                    } else {
                        // Находим $item_bool
                        $item_bool = self::get_parent_item_from_main($child_item_id, $link_bool->id);
                        if ($item_bool) {
                            // Если checked, то показывать поле
                            if ($item_bool->boolval()['value']) {
                                $result = true;
                            } else {
                                $result = false;
                            }
                        }
                    }
                }
            } else {
                $result = true;
            }
        }
        return $result;
    }

    static function calc_relip_project($relit_id, Project $current_project, bool $is_message = true)
    {
        $project = null;
        $mess = '';
        $parent_is_required = false;
        if ($relit_id == 0 || $relit_id == null) {
            // Возвращается текущий проект
            $project = $current_project;
        } else {
            // Поиск взаимосвязанного проекта
            $relit = Relit::find($relit_id);
            if ($relit) {
                $parent_is_required = $relit->parent_is_required;
                $relip = Relip::where('relit_id', $relit->id)->where('child_project_id', $current_project->id)->first();
                if ($relip) {
                    $project = $relip->parent_project;
                } else {
                    $mess = trans('main.relip') . ' ' . trans('main.code_not_found');
                }
            } else {
                $mess = trans('main.relit') . ' ' . trans('main.code_not_found');
            }
        }
        // Проверка и вывод сообщения нужны
        if ($parent_is_required && $project == null && $is_message == true) {
            dd('current_project: ' . $current_project->name_id()
                . ', template: ' . $current_project->template->name_id()
                . ', relit_id: ' . $relit_id . ', '
                . $mess . ', '
                . trans('main.check_project_properties_projects_parents_are_not_set') . '!');
        }
        return $project;
    }

    // Вычисляет $relit_id
    static function calc_link_relit_id(Link $link, Role $role, $relit_id)
    {
        $result_relit_id = 0;
        if ($link->parent_relit_id == 0) {
            // Возвращается текущий проект
            $result_relit_id = $relit_id;
        } else {
            if ($link->parent_base->template_id == $role->template_id) {
                $result_relit_id = $link->parent_relit_id;
            } else {
                // Используется первый нашедший проект
                $relit = Relit::select(DB::Raw('relits.id as relit_id'))
                    ->where('child_template_id', $role->template_id)
                    ->where('parent_template_id', $link->parent_base->template_id)
                    ->orderBy('serial_number')
                    ->first();
                if ($relit) {
                    $result_relit_id = $relit['relit_id'];
                }
            }
        }
        return $result_relit_id;
    }

    // Вывод проекта по $link и $current_project
    static function calc_link_project(Link $link, Project $current_project, bool $is_message = true)
    {
        // Поиск взаимосвязанного проекта
        $project = self::calc_relip_project($link->parent_relit_id, $current_project, $is_message);
        return $project;
    }

    // Проверка: существует ли связанный проект $relip_project в основном проекте $child_project
    static function is_found_parent_project(Project $child_project, Project $relip_project)
    {
        return Relip::where('child_project_id', $child_project->id)
            ->where('parent_project_id', $relip_project->id)->exists();
    }

    // Вывод проекта по $relit и $current_project
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
            $array_relits[$relit->id] = $relit->serial_number . '. ' . $relit->parent_template->name()
                . ' (Id =' . $relit->id . ', ' . $relit->title() . ')';
        }
        return $array_relits;
    }

    static function get_project_bases(Project $current_project, Role $role,
                                              $check_main_menu = true,
                                              $view_ret_isset_id = false, $view_ret_id = null,
                                      Base    $base = null,
                                      Link    $link = null, $parent_relit_id = null)
    {
        $array_relips = [];
        $child_relits = $current_project->template->child_relits;
        // Заполнение массива для текущего шаблона
        // Похожие строки ниже в этой функции
        // 0 - текущий шаблон (нужно)
        $current_id = 0;
        $bases_ids = self::get_bases_from_relit_id($current_id, $current_project->template_id);
        if ($bases_ids) {
            $array_relips[$current_id]['project_id'] = $current_project->id;
            $array_relips[$current_id]['base_ids'] = $bases_ids['bases_ids'];
        }
        // Заполнение массива для шаблонов $child_relits
        foreach ($child_relits as $relit) {
            // Поиск взаимосвязанного проекта
            $project = self::calc_relip_project($relit->id, $current_project);
            if ($project) {
                // Похожие строки выше в этой функции
                $bases_ids = self::get_bases_from_relit_id($relit->id, $current_project->template_id);
                if ($bases_ids) {
                    $array_relips[$relit->id]['project_id'] = $project->id;
                    $array_relips[$relit->id]['base_ids'] = $bases_ids['bases_ids'];
                }
            }
        }
        // Если передано $base
        $array_bases = [];
        if ($base) {
            // Нужно '$base_parent_links'
            $base_parent_links = $base->parent_links()->get();
            foreach ($base_parent_links as $link_value) {
                // В качестве индекса массива нужно использовать $link_value->child_base_id для уникальности значений
                $array_bases[$link_value->child_base_id] = $link_value->child_base_id;
            }
            if (count($array_bases) > 0) {
                foreach ($array_relips as $relit_id => $val_arr) {
                    $project_id = $val_arr['project_id'];
                    $bases_ids = $val_arr['base_ids'];
                    foreach ($bases_ids as $key => $value) {
                        // Удаляем элемент массива с $base, если $value не найдено в $array_bases
                        if (!in_array($value, $array_bases)) {
                            unset($array_relips[$relit_id]['base_ids'][$key]);
                        }
                    }
                }
            }
        }
        if (1 == 2) {
            if ($base) {
                foreach ($array_relips as $relit_id => $value) {
                    // Нужно: кроме текущего шаблона
                    if ($relit_id == 0) {
                        if ($base->template_id != $current_project->template_id) {
                            // Удаляем элемент массива с $relit_id, если "$base->template_id != $current_project->template_id"
                            //dd($base->name() . ' ' .  $base->template->name() . ' ' . $current_project->template->name());
                            //unset($array_relips[$relit_id]);
                        }
                    } else {
                        $relit = Relit::find($relit_id);
                        if ($relit) {
                            if ($base->template_id != $relit->parent_template_id) {
                                // Удаляем элемент массива с $relit_id, если "$base->template_id != $relit->parent_template_id"
                                unset($array_relips[$relit_id]);
                                // Не нужно "break"
                                // break;
                            }
                        }
                    }
                }
            }
        }
        // Если передано $link
        if ($link) {
            //foreach ($array_relips as $relit_id => $value) {
            // Нужно: кроме текущего шаблона
            //if ($relit_id != 0) {
            //$relit = Relit::find($relit_id);
            //if ($relit) {
            //if (($link->parent_base->template_id != $relit->parent_template_id)) {
            // Удаляем элемент массива с $relit_id, если "$relit_id == $link->parent_relit_id"
            //unset($array_relips[$relit_id]);
            // Не нужно "break"
            // break;
            //}
            //}
            //}
            //}
            if (1 == 2) {
                foreach ($array_relips as $relit_id => $value) {
                    // Нужно: кроме текущего шаблона
                    if ($relit_id == 0) {
                        if ($link->parent_base->template_id != $current_project->template_id) {
                            // Удаляем элемент массива с $relit_id, если "$base->template_id != $current_project->template_id"
                            //unset($array_relips[$relit_id]);
                        }
                    } else {
                        $relit = Relit::find($relit_id);
                        if ($relit) {
                            if ($link->parent_base->template_id != $relit->parent_template_id) {
                                // Удаляем элемент массива с $relit_id, если "$base->template_id != $relit->parent_template_id"
                                //unset($array_relips[$relit_id]);
                                // Не нужно "break"
                                // break;
                            }
                        }
                    }
                }
            }
            $base_right = null;
            foreach ($array_relips as $relit_id => $value) {
                $base_right = self::base_link_right($link, $role, $parent_relit_id, true, $relit_id);
                if ($base_right['is_body_link_enable'] == false) {
                    // Удаляем элемент массива с $relit_id, если "$relit_id == $link->parent_relit_id"
                    unset($array_relips[$relit_id]);
                    // Не нужно "break"
                    // break;
                }
            }

            foreach ($array_relips as $relit_id => $value) {
                // Текущий шаблон
                if ($relit_id == 0) {
                    // Похожие строки в этом цикле
                    if ($current_project->template_id != $link->child_base->template_id) {
                        // Удаляем элемент массива с $relit_id, если "$relit->parent_template_id != $link->child_base->template_id"
                        unset($array_relips[$relit_id]);
                        // Не нужно "break"
                        // break;
                    }
                } else {
                    $relit = Relit::find($relit_id);
                    if ($relit) {
                        // Похожие строки в этом цикле
                        if ($relit->parent_template_id != $link->child_base->template_id) {
                            // Удаляем элемент массива с $relit_id, если "$relit->parent_template_id != $link->child_base->template_id"
                            unset($array_relips[$relit_id]);
                            // Не нужно "break"
                            // break;
                        }
                    }
                }
            }
        }
        // Если передано $item_base
//        if ($item_base) {
//            foreach ($array_relips as $relit_id => $value) {
//                // Текущий шаблон
//                if ($relit_id == 0) {
//                    // Похожие строки в этом цикле
//                    if ($current_project->template_id != $item_base->template_id) {
//                        // Удаляем элемент массива с $relit_id, если "$relit->parent_template_id != $item_base->template_id"
//                        unset($array_relips[$relit_id]);
//                        // Не нужно "break"
//                        // break;
//                    }
//                } else {
//                    $relit = Relit::find($relit_id);
//                    if ($relit) {
//                        // Похожие строки в этом цикле
//                        if ($relit->parent_template_id != $item_base->template_id) {
//                            // Удаляем элемент массива с $relit_id, если "$relit->parent_template_id != $item_base->template_id"
//                            unset($array_relips[$relit_id]);
//                            // Не нужно "break"
//                            // break;
//                        }
//                    }
//                }
//            }
//        }

        foreach ($array_relips as $relit_id => $val_arr) {
            $project_id = $val_arr['project_id'];
            $bases_ids = $val_arr['base_ids'];
            foreach ($bases_ids as $key => $value) {
                $base = Base::find($value);
                if ($base) {
                    $base_right = self::base_right($base, $role, $relit_id);
                    // Удаляем элемент массива с $base, если '$base_right['is_list_base_calc'] == false || $base_right['is_bsin_base_enable'] == false'
                    // Похожая проверка в GlobalController::get_project_bases(), ItemController::base_index() и project/start.php
//                  if ($base_right['is_bsmn_base_enable'] == false) {
                    // Нужно использовать '$base_right['is_list_base_calc'] == false'
                    // При $check_main_menu = true - дополнительная проверка '$base_right['is_bsin_base_enable'] == false'
                    if (($base_right['is_list_base_calc'] == false) || (($check_main_menu == true) && ($base_right['is_bsin_base_enable'] == false))) {
                        unset($array_relips[$relit_id]['base_ids'][$key]);
                    }
                }
            }
        }

//      $relit_id нужно
        foreach ($array_relips as $relit_id => $value) {
            $project_id = $value['project_id'];
            $count = count($value['base_ids']);
            if ($count == 0) {
                // Удаляем элемент массива с $relit_id, если количество $bases в массиве равно 0
                unset($array_relips[$relit_id]);
            }
        }

        $first_found_ret_id = false;
        $first_ret_id = -1;
        $view_found_ret_id = false;
        // Если передано $view_ret_id
        if ($view_ret_isset_id == true) {
            // Если передано $link
            if ($link) {
//            foreach ($array_relips as $relit_id => $value) {
//                if ($view_ret_id == $relit_id) {
//                    $view_found_ret_id = true;
//                    break;
//                }
//            }

//            foreach ($array_relips as $relit_id => $val_arr) {
//                $bases_ids = $val_arr['base_ids'];
//                foreach ($bases_ids as $key => $base_id) {
//                    if (!$first_found_ret_id) {
//                        // Первый $relit_id
//                        if ($base_id == $link->child_base_id) {
//                            $first_found_ret_id = true;
//                            $view_ret_id = $relit_id;
//                            $view_found_ret_id = true;
//                            break;
//                        }
//                    }
//                }
//                if ($view_found_ret_id) {
//                    break;
//                }
//            }

                foreach ($array_relips as $relit_id => $val_arr) {
                    $bases_ids = $val_arr['base_ids'];
                    foreach ($bases_ids as $key => $base_id) {
                        if ($base_id == $link->child_base_id) {
                            // Проверка текущего $view_ret_id на равенство $relit_id (приоритетнее)
                            if ($view_ret_id == $relit_id) {
                                $view_ret_id = $relit_id;
                                $view_found_ret_id = true;
                                // Нужен break
                                break;
                            }
                            // Параллельно ищем первый $relit_id
                            if (!$first_found_ret_id) {
                                $first_found_ret_id = true;
                                $first_ret_id = $relit_id;
                                // Не нужен break
                                // break;
                            }
                        }
                    }
                    if ($view_found_ret_id) {
                        break;
                    }
                }
                if ($view_found_ret_id == false) {
                    // Если первый $relit_id найден
                    if ($first_found_ret_id) {
                        $view_found_ret_id = true;
                        $view_ret_id = $first_ret_id;
                    }
                }
                // Если $link не передано
            } else {
                foreach ($array_relips as $relit_id => $value) {
                    if ($view_ret_id == $relit_id) {
                        $view_found_ret_id = true;
                        break;
                    }
                }
                if ($view_found_ret_id == false) {
                    foreach ($array_relips as $relit_id => $value) {
                        // Присваиваем $relit_id первый по списку; $link = null, значит будут показаны все связи и берем первый возможный проект ($relit_id)
                        $view_ret_id = $relit_id;
                        $view_found_ret_id = true;
                        break;
                    }
                }
            }
            if ($view_found_ret_id == false) {
                // Нужно для сравнения "if ($view_ret_id != $view_ret_new_id)" в ItemController::item_index()
                $view_ret_id = -1;
            }
        }
        return ['array_relips' => $array_relips, 'view_found_ret_id' => $view_found_ret_id, 'view_ret_id' => $view_ret_id];

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

    // Похожие процедуры find_base_from_relit_id(), get_bases_from_relit_id() и get_links_from_relit_id()
    static function find_base_from_relit_id($base_id, $relit_id, $current_template_id)
    {
        $result = false;
        // Вычисление $template
        $template_id = null;
        if ($relit_id == 0) {
            $template_id = $current_template_id;
        } else {
            $relit = Relit::find($relit_id);
            if ($relit) {
                $template_id = $relit->parent_template_id;
            }
        }
        if ($template_id != null) {
            $bases = Base::where('template_id', $template_id)
                ->where('id', $base_id)
                ->get();
            $result = count($bases) > 0;
        }
        return $result;
    }

    // Похожие процедуры find_base_from_relit_id(), get_bases_from_relit_id() и get_links_from_relit_id()
    static function get_bases_from_relit_id($relit_id, $current_template_id)
    {
        $bases_ids = [];
        $bases_options = '';
        // Вычисление $template
        $template_id = null;
        if ($relit_id == 0) {
            $template_id = $current_template_id;
        } else {
            $relit = Relit::find($relit_id);
            if ($relit) {
                $template_id = $relit->parent_template_id;
            }
        }
        if ($template_id != null) {
            // Список bases по выбранному template_id
//            $bases = Base::all()
//                ->where('template_id', $template_id)
//                ->sortBy('serial_number');
            // Порядок сортировки; обычные bases, вычисляемые bases, настройки - bases, серийный номер
            $bases = Base::where('template_id', $template_id)
                ->orderBy('is_setup_lst')
                ->orderBy('is_calculated_lst')
                ->orderBy('serial_number')
                ->get();
            foreach ($bases as $base) {
                $bases_ids[] = $base->id;
                //$bases_options = $bases_options . "<option value='" . $base->id . "'>" . $base->name() . "</option>";
                $bases_options = $bases_options . "<option value='" . $base->id . "'>" . $base->desc_type() . "</option>";
            }
        }
        return [
            'bases_ids' => $bases_ids,
            'bases_options' => $bases_options
        ];
    }

    // Похожие процедуры find_base_from_relit_id(), get_bases_from_relit_id() и get_links_from_relit_id()
    static function get_links_from_relit_id($relit_id, $current_template_id)
    {
        $links_options = '';
        // Вычисление $template
        $template_id = null;
        if ($relit_id == 0) {
            $template_id = $current_template_id;
        } else {
            $relit = Relit::find($relit_id);
            if ($relit) {
                $template_id = $relit->parent_template_id;
            }
        }
        if ($template_id != null) {
            $template = Template::findOrFail($template_id);
            // список links по выбранному template_id
            $links = self::select_links_template($template);
            foreach ($links as $link) {
                $links_options = $links_options
                    . "<option value='" . $link->id . "'>" . $link->name() . "</option>";
            }
        }
        return [
            'links_options' => $links_options
        ];
    }

    static function get_parent_template_from_relit_id($relit_id, $current_template_id)
    {
        $template = null;
        $template_name = '';
        // Вычисление $template
        if ($relit_id == 0) {
            $template = Template::findOrFail($current_template_id);
            $template_name = $template->name() . ' (' . trans('main.current_template') . ')';
        } else {
            $relit = Relit::find($relit_id);
            if ($relit) {
                $template = $relit->parent_template;
                $template_name = $relit->serial_number . '. ' . $template->name()
                    . ' (Id =' . $relit->id . ')';
            }
        }
        return ['template' => $template, 'template_name' => $template_name];
    }

    static function get_child_relit_id_from_link_current_template(Link $link, $relit_id, $current_template_id, $item_template_id)
    {
        $found = false;
        $child_relit_id = 0;
        $relit = null;
        $child_template_id = $link->child_base->template_id;
        $parent_template_id = $link->parent_base->template_id;
        if (1 == 2) {
            //dd($current_template_id . ' ' .$child_template_id . ' ' . $item_template_id. ' ' . $relit->child_template_id. ' ' . $relit->parent_template_id.' '.$relit_id.' '.$link->parent_relit_id);
            //dd($current_template_id . ' ' .$child_template_id . ' ' . $item_template_id. ' ' . $relit_id.' '.$link->parent_relit_id);
            if ($relit_id == 0) {
                if (($parent_template_id == $current_template_id)) {
                    // Текущий шаблон
                    $found = true;
                }
            } else {
                $parent_relit_id = $link->parent_relit_id;
                $child_relit_id = 0;
                $relit = Relit::find($relit_id);
                if ($relit) {
                    if (($parent_template_id == $relit->parent_template_id)) {
                        // Взаимосвязанный шаблон
                        $found = true;
                    }
//                $template = $relit->parent_template;
//                $template_name = $relit->serial_number . '. ' . $template->name()
//                    . ' (Id =' . $relit->id . ')';
                }
            }
            if (($child_template_id == $current_template_id)) {
                // Текущий шаблон
                $child_relit_id = 0;
            } else {
                $relit = Relit::find($relit_id);
                if ($relit) {
                    $child_relit_id = $relit_id;
                }
            }
        }
        return ['found' => $found, 'child_relit_id' => $child_relit_id];
    }

    // Поиск $relit->id
    static function get_parent_relit_from_template_id($parent_project_template_id, $parent_template_id)
    {
        $child_template_id = $parent_project_template_id;
        // Проверка на '-1' используется в ItemController.php
        $result = -1;
        if ($child_template_id == $parent_template_id) {
            // Текущий проект
            $result = 0;
        } else {
            $relit = Relit::where('child_template_id', $child_template_id)
                ->where('parent_template_id', $parent_template_id)
                ->first();
            if ($relit) {
                $result = $relit->id;
            }
        }
        return $result;
    }

    // Сохранение и resize() изображения
    function image_store($request, $key, $project_id, $base_id)
    {
        // Сохраняем на диск графический файл "один к одному"
        //$path = $request[$key]->store('public/' . $item->project_id . '/' . $link->parent_base_id);
        $path = $request[$key]->store('public/' . $project_id . '/' . $base_id);
        // Пример заполнения $path = "public/24/55/axlkyj0ldw7ge0OaNU0hIZBHiqyMhB4oAeyS8HLs.jpg"
        // Заменяем начальные 'public/' на 'storage/'
        $path_storage = substr_replace($path, 'storage/', 0, 7);
        // Пример заполнения $path_storage = "storage/24/55/DqKxdrZbpielRYdng4XoVomkMRSi3DqvRqWjIkHM.jpg"
        $public_path = public_path($path_storage);
        // Команда resize()
        $img = Image::make($request[$key])->orientate()->resize(500, 500, function ($constraint) {
            $constraint->aspectRatio();
            // Предотвратить возможное увеличение размера
            $constraint->upsize();
        }
        );
//        $img = Image::make($request[$key])->orientate()->widen(300, function ($constraint) {
//            // Предотвратить возможное увеличение размера
//            $constraint->upsize();
//        }
//        );
//        $img = Image::make($request[$key])->orientate()->fit(300, 400, function ($constraint) {
//            $constraint->upsize();
//        });

        // Сохраняем(перезаписываем с тем же именем) графический файл после resize()
        $img->save($public_path);

        //$this->createThumbnail($path, 150, 150);
        return $path;
    }

    function get_author_roles_projects($project_id)
    {
        // Проекты, у которых в accesses есть записи для текущего пользователя
        // с ролью Автор
        $projects = null;
        $projects = Project::where('id', $project_id)
            ->whereHas('accesses', function ($query) use ($project_id) {
                $query->where('user_id', GlobalController::glo_user_id())
                    ->where('project_id', $project_id);
            })->whereHas('template.roles', function ($query) {
                $query->where('is_author', true);
            });
        return $projects;
    }

    function get_author_users_projects($user_id)
    {
        // Проекты, у которых в accesses есть записи для текущего пользователя
        // с ролью Автор
        $projects = null;
        $projects = Project::whereHas('accesses', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->whereHas('template.roles', function ($query) {
            $query->where('is_author', true);
        });
        return $projects;
    }

    function is_author_roles_project($project_id)
    {
        $projects = self::get_author_roles_projects($project_id);
        $project = $projects->first();
        $result = false;
        if ($project) {
            $result = true;
        }
        return $result;
    }

//    Функции function usercode_calc() и usercode_uncalc()- прямой и обратный расчеты
    static function usercode_calc()
    {
        $user_id = 0;
        // При авторизации
        if (Auth::check()) {
            $user_id = Auth::user()->id;
            // Без авторизации
        } else {
            // Похожие строки в GlobalController::usercode_calc() и ItemController::doc_download()
            // 807 - выбранное случайное число
            $user_id = 807;
        }
        $result = $user_id * 11 + 7;
        return $result;
    }

    static function usercode_uncalc($usercode)
    {
        $result = intval(($usercode - 7) / 11);
        return $result;
    }

    static function par_link_const_textnull()
    {
        return 'textnull';
    }

    static function par_link_const_text_base_null()
    {
        return 'text_base_null';
    }

    // Функция нужна, что в строке запуска не было без параметра
    // (http://abakusonline:8080/item/item_index/33/2789/21/18/0//195/2787) - неправильно
    // http://abakusonline:8080/item/item_index/33/2789/21/18/0/textnull/195/2787 - правильно
    static function par_link_textnull($par_link)
    {
        $result = null;
        if ($par_link) {
            $result = $par_link;
        } else {
            $result = self::par_link_const_textnull();
        }
        return $result;
    }

    static function const_alltrue()
    {
        return 'alltrue';
    }

    static function const_allfalse()
    {
        return 'allfalse';
    }

    static function const_null()
    {
        return 'null';
    }

    static function set_relit_id($relit_id)
    {
        $result = $relit_id;
        if ($result == null || $result == self::const_null()) {
            $result = 0;
        }
        return $result;
    }

    static function set_rev_relit_id($relit_id)
    {
        $result = $relit_id;
        if ($result == null) {
            $result = self::const_null();
        }
        return $result;
    }

    static function set_par_view_link_null($view_link)
    {
        $result = $view_link;
        if ($result == null) {
            $result = GlobalController::par_link_const_textnull();
        }
        return $result;
    }

    static function set_un_par_view_link_null($view_link)
    {
        $result = $view_link;
        if ($result == GlobalController::par_link_const_textnull()) {
            $result = null;
        }
        return $result;
    }

    static function set_str_const_null($str)
    {
        $result = $str;
        if ($str == '') {
            $result = GlobalController::const_null();
        }
        return $result;
    }

    static function calc_item_name_lang()
    {
        //$name = "";  // нужно, не удалять
        $name = "name_lang_0";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
        }
        return $name;
    }

    static function calc_title_name($name, $lower = true, $dvoetoch = false)
    {
        $result = $name;
        if ($lower) {
            // Начинать со строчной буквы
            $result = mb_strtolower($name);
        }
        if ($dvoetoch) {
            $result = $result . ":";
        }
        return $result;
    }

    static function my_info($base_right = null)
    {
        $result = '';
        if ($base_right) {
            if ($base_right['is_list_base_user_id'] == true) {
                // Наименование
                $result = ' (' . mb_strtolower(trans('main.current_user')) . ')';
            }
            if ($base_right['is_list_base_byuser'] == true) {
                // Наименование
                $result = ' (' . mb_strtolower(trans('main.my_records')) . ')';
            }
        }
        return $result;
    }

    static function link_par_link(Link $link, $par_link)
    {
        $result = false;
        if ($par_link != null) {
            if ($par_link->id == $link->id) {
                $result = true;
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }
        return $result;
    }

    static function name_and_emoji($name, Base $base)
    {
        $result = $name;
        $sem = $base->em_str();
        if (!$base->type_is_number()) {
            $result = $sem . $result;
        } else {
            $result = $result . $sem;
        }
        return $result;
    }

    static function name_and_first_emoji($name, Base $base)
    {
        $result = $name;
        $sem = $base->em_str();
//        if (!$base->type_is_number()) {
        $result = $sem . $result;
//        } else {
//        $result = $result . $sem;
//        }
        return $result;
    }

    static function name_and_end_emoji($name, Base $base)
    {
        $result = $name;
        $sem = $base->em_str();
//        if (!$base->type_is_number()) {
//        $result = $sem . $result;
//        } else {
        $result = $result . $sem;
//        }
        return $result;
    }

    static function name_and_brackets_emoji($name, Base $base)
    {
        $result = $name;
//        if ($base->type_is_number()) {
//            $result = $result . $base->em_br();
//        } else {
//            $result = $result . $base->em_str();
//        }
        if (!$base->type_is_list()) {
            //if ($base->type_is_number()) {
            $result = $result . $base->em_br();
            //} else {
            //    $result = $result . $base->em_str();
            //}
        }
        return $result;
    }

    static function label_is_required(Base $base)
    {
        $result = '';
        if ($base->is_required_lst_num_str_txt_img_doc == true) {
            $result = '*';
        }
        return $result;
    }

//    function get_display()
//    {
//        return config('app.display');
//        //return $this->display;
//    }
//
//    function current_display()
//    {
//        return $this->get_display();
//    }
//
//    function set_display($display)
//    {
//        // Проверка на правильность значения
//        //$index = array_search($display, config('app.displays'));
//        //if ($index !== false) {   // '!==' использовать, '!=' не использовать
//            // Сохранение значения
//            //Config::set('app.display', $display);
//        //}
//        $this->display = $display;
//        return redirect()->back();
//    }

}
