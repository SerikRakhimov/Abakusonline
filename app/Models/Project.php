<?php

namespace App\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use \App\Http\Controllers\GlobalController;

class Project extends Model
{
    protected $fillable = ['name_lang_0', 'name_lang_1', 'name_lang_2', 'name_lang_3'];


    function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    function accesses()
    {
        return $this->hasMany(Access::class, 'project_id');
    }

    function name()
    {
        $result = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $result = $this['name_lang_' . $index];
        }
//        if ($result == "") {
//            $result = $this->name_lang_0;
//        };
//        $result = $result . ' (' . $this->user->name . ')';
//        $result = $result . ' (' . $this->account . ', Id = ' .$this->id . ')';
        //$result = $result . ' (Id = ' .$this->id . ')';
        return $result;
    }

    function name_id()
    {
        return $this->name() . " (Id = " . strval($this->id) . ")";
    }

    function is_calculated_base_exist()
    {
        $result = Base::where('template_id', $this->template_id)->where('is_calculated_lst', true)->exists();
        return $result;
    }

    // Возвращает настройки проекта в виде массива
    function get_items_setup()
    {
        $result = array();
        // Возвращает $base с признаком 'is_setup_lst'
        $base = $this->template->bases->where('is_setup_lst', true)->first();
        $item_logo = null;
        $item_ext_desc = null;
        $item_int_desc = null;
        if ($base) {
            $item = Item::where('project_id', $this->id)->where('base_id', $base->id)->first();
            if ($item) {
                $link = $base->get_link_project_logo();
                if ($link) {
                    $item_logo = GlobalController::view_info($item->id, $link->id);
                }

                $link = $base->get_link_project_description('parent_is_setup_project_external_description_txt');
                if ($link) {
                    $item_ext_desc = GlobalController::view_info($item->id, $link->id);
                }

                $link = $base->get_link_project_description('parent_is_setup_project_internal_description_txt');
                if ($link) {
                    $item_int_desc = GlobalController::view_info($item->id, $link->id);
                }

            }
        }
        $result['logo'] = $item_logo;
        $result['ext_desc'] = $item_ext_desc;
        $result['int_desc'] = $item_int_desc;
        return $result;
    }

    function dc_ext()
    {
        $result = "";  // нужно, не удалять
        $item = $this->get_items_setup()['ext_desc'];
        if ($item) {
            $index = array_search(App::getLocale(), config('app.locales'));
            if ($index !== false) {   // '!==' использовать, '!=' не использовать
                $result = $item->text['name_lang_' . $index];
            }
        }
        return $result;
    }

    function dc_int()
    {
        $result = "";  // нужно, не удалять
        $item = $this->get_items_setup()['int_desc'];
        if ($item) {
            $index = array_search(App::getLocale(), config('app.locales'));
            if ($index !== false) {   // '!==' использовать, '!=' не использовать
                $result = $item->text['name_lang_' . $index];
            }
        }
        return $result;
    }

    function link_info()
    {
        // "isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http'" отсюда "https://www.php.net/reserved.variables.server"
        return (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['SERVER_NAME']
            . '/project/start/' . $this->id . ' - ' . mb_strtolower(trans('main.project_link'));
    }

}
