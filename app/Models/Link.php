<?php

namespace App\Models;

use App\Http\Controllers\GlobalController;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = ['child_base_id', 'parent_base_id', 'parent_label_lang_0', 'parent_label_lang_1', 'parent_label_lang_2', 'parent_label_lang_3', 'parent_is_parent_related'];

    function child_base()
    {
        return $this->belongsTo(Base::class, 'child_base_id');
    }

    function parent_base()
    {
        return $this->belongsTo(Base::class, 'parent_base_id');
    }

    function child_label()
    {
        $result = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $result = $this['child_label_lang_' . $index];
        }
        if ($result == "") {
            $result = $this->child_label_lang_0;
        }
        return $result;
    }

    function child_labels()
    {
        $result = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $result = $this['child_labels_lang_' . $index];
        }
//        if ($result == "") {
//            $result = $this->child_labels_lang_0;
//        }
        return $result;
    }

    function parent_level($index)
    {
        $result = "";  // нужно, не удалять
        if (0 <= $index && $index <= 3) {
            $value = $this['parent_level_id_' . $index];
            if ($value == 0){
                $result = GlobalController::option_empty();
            }
            else{
                $level = Level::find($value);
                if ($level){
                    $result = $level->name();
                }
                else{
                    $result = $value;
                }
            }
        }
        return $result;
    }

    function parent_label()
    {
        $result = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $result = $this['parent_label_lang_' . $index];
        }
//        if ($result == "") {
//            $result = $this->parent_label_lang_0;
//        }
        return $result;
    }

    function parent_calcname_prefix()
    {
        $result = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $result = $this['parent_calcname_prefix_lang_' . $index];
        }
        if ($result == "") {
            $result = $this->parent_calcname_prefix_lang_0;
        }
        return $result;
    }

    function info()
    {
        return $this->child_base->name() . " - " . $this->parent_label() . ": " . $this->parent_base->name();
    }

    function info_full()
    {
        return trans('main.link') . " (" . $this->id . ")" . " _ " . $this->child_base->name() . " - " . $this->parent_label() . ": " . $this->parent_base->name();
    }


    function name()
    {
        return $this->info();
    }

}
