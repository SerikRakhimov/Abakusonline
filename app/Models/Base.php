<?php

namespace App\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    protected $fillable = ['name_lang_0', 'name_lang_1', 'name_lang_2', 'name_lang_3'];

    function items()
    {
        return $this->hasMany(Item::class, 'base_id');
    }

    function child_links()
    {
        return $this->hasMany(Link::class, 'child_base_id');
    }

    function parent_links()
    {
        return $this->hasMany(Link::class, 'parent_base_id');
    }

    // Используется "name"
    function name()
    {
        $result = "";  // нужно, не удалять
        //$index = array_search(App::getLocale(), session('glo_menu_save'));
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $result = $this['name_lang_' . $index];
        }
//        if ($result == "") {
//            $result = $this->name_lang_0;
//        }
        return $result;
    }

    // Используется "names"
    function names()
    {
        $result = "";  // нужно, не удалять
//      $index = array_search(App::getLocale(), config('app.locales'));
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $result = $this['names_lang_' . $index];
        }
//        if ($result == "") {
//            $result = $this->names_lang_0;
//        }
        return $result;
    }

    // Похожие строки в BaseController.php (function store() и edit())
    // и base/edit.blade.php
    // и ModerationController (function index())
    static function get_types()
    {
        return array(
            "0" => trans('main.list'),
            "1" => trans('main.number'),
            "2" => trans('main.string'),
            "3" => trans('main.date'),
            "4" => trans('main.boolean'),
            "5" => trans('main.text'),
            "6" => trans('main.image'),
            "7" => trans('main.document')
        );
    }

    function type()
    {
        // нужно
        $result = -1;
        if ($this->type_is_list == true) {
            $result = 0;
        } else if ($this->type_is_number == true) {
            $result = 1;
        } else if ($this->type_is_string == true) {
            $result = 2;
        } else if ($this->type_is_date == true) {
            $result = 3;
        } else if ($this->type_is_boolean == true) {
            $result = 4;
        } else if ($this->type_is_text == true) {
            $result = 5;
        } else if ($this->type_is_image == true) {
            $result = 6;
        } else if ($this->type_is_document == true) {
            $result = 7;
        }
        return $result;
    }

    function type_name()
    {
        $result = "";
        switch ($this->type()) {
            case 0:
                $result = trans('main.list');
                break;
            case 1:
                $result = trans('main.number');
                break;
            case 2:
                $result = trans('main.string');
                break;
            case 3:
                $result = trans('main.date');
                break;
            case 4:
                $result = trans('main.boolean');
                break;
            case 5:
                $result = trans('main.text');
                break;
            case 6:
                $result = trans('main.image');
                break;
            case 7:
                $result = trans('main.document');
                break;
        }
        return $result;
    }

    function type_is_list()
    {
        return $this->type_is_list == true;
    }

    function type_is_number()
    {
        return $this->type_is_number == true;
    }

    function type_is_string()
    {
        return $this->type_is_string == true;
    }

    function type_is_date()
    {
        return $this->type_is_date == true;
    }

    function type_is_boolean()
    {
        return $this->type_is_boolean == true;
    }

    function type_is_text()
    {
        return $this->type_is_text == true;
    }

    function type_is_image()
    {
        return $this->type_is_image == true;
    }

    function type_is_document()
    {
        return $this->type_is_document == true;
    }

    function is_calculated()
    {
        return $this->is_calculated_lst == true;
    }

    function info()
    {
        return $this->name();
    }

    function info_full()
    {
        return trans('main.base') . " (" . $this->id . ")" . " _ " . $this->name();
    }

    function digits_num_format()
    {
        $result = "";
        if ($this->digits_num == 0) {
            $result = "0";
        } else {
            $result = "0.";
            for ($i = 0; $i < ($this->digits_num - 1); $i++) {
                $result = $result . "0";
            }
            $result = $result . "1";
        }
        return $result;
    }

    function number_format()
    {
        $sg = 0;
        if ($this->significance_code > 0) {
            $sg = $this->significance_code;
        } else {
            // Const "15" используется и в ext_edit.blade.php
            $sg = 15;
        }
        return str_repeat('9', $sg);
    }

//    // Возвращает истину, если есть основное изображение
//    function is_primary_image_link()
//    {
//        $links = $this->child_links();
//        $link = $links->where('parent_is_primary_image', true)->first();
//        return $link;
//    }

    // Возвращает истину, если вид отображения информации - плитка, и если есть основное изображение в links
    function tile_view($base_right)
    {
        $result = false;
        $link = null;
        // Только чтение данных(без создания, корректировки и удаления)
        if ($base_right['is_list_base_read'] == true) {
            $links = $this->child_links();
            $link = $links->where('parent_is_primary_image', true)->first();
            if ($link) {
                if ($link->parent_base->type_is_image()) {
                    $result = true;
                }
            }
        }
        return ['result' => $result, 'link' => $link];
    }

    // Возвращает $link  с признаком 'parent_is_setup_project_logo_img'
    function get_link_project_logo()
    {
        //$link = $this->child_links()->where('parent_is_setup_project_logo_img', true)->first();
        $links = $this->child_links();
        $link = $links->where('parent_is_setup_project_logo_img', true)->first();
        if ($link) {
            if (!($link->parent_base->type_is_image())) {
                $link = null;
            }
        }
        return $link;
    }


    // Возвращает $link  с признаками 'parent_is_setup_project_external_description_txt' и 'parent_is_setup_project_internal_description_txt',
    // признаки передаются как параметры функции
    function get_link_project_description($name)
    {
        $link = $this->child_links()->where($name, true)->first();
        if ($link) {
            if (!($link->parent_base->type_is_text())) {
                $link = null;
            }
        }
        return $link;
    }

//    function link_primary_image()
//    {
//        $link = null;
//        $links = $this->child_links();
//        $link = $links->where('parent_is_primary_image', true)->first();
//        if ($link) {
//            if ($link->parent_base->type_is_image()==false) {
//                $link = null;
//            }
//        }
//        return $link;
//    }

    function menu_type_name()
    {
        $text = "";
        $icon = "";
        if ($this->is_calculated_lst == true) {
            $text = $text . trans('main.is_calculated_lst');
            $icon = $icon . '<i class="fas fa-binoculars"></i>';
        }
        if ($this->is_setup_lst == true) {
            $text = $text . trans('main.is_setup_lst');
            $icon = $icon . '<i class="fas fa-tags"></i>';
        }
        return ['text' => $text, 'icon' => $icon];
    }

}
