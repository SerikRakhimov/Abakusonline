<?php

namespace App\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name_lang_0', 'name_lang_1', 'name_lang_2', 'name_lang_3'];

    function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
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
//        }
        return $result;
    }

    function accesses()
    {
        return $this->hasMany(Access::class, 'role_id');
    }

    function desc()
    {
        $result = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $result = $this['desc_lang_' . $index];
        }
//        if ($result == "") {
//            $result = $this->desc_lang_0;
//        }
        return $result;
    }

    function is_author()
    {
        return $this->is_author == true;
    }

    function is_default_for_external()
    {
        return $this->is_default_for_external == true;
    }

}
