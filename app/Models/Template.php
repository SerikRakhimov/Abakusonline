<?php

namespace App\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = ['name_lang_0', 'name_lang_1', 'name_lang_2', 'name_lang_3'];

    function projects()
    {
        return $this->hasMany(Project::class, 'template_id');
    }

    function roles()
    {
        return $this->hasMany(Role::class, 'template_id');
    }

    function levels()
    {
        return $this->hasMany(Level::class, 'template_id');
    }

    function bases()
    {
        return $this->hasMany(Base::class, 'template_id');
    }

    function sets()
    {
        return $this->hasMany(Set::class, 'template_id');
    }

    function child_relits()
    {
        // Сортировка 'orderBy('serial_number')' нужна
        return $this->hasMany(Relit::class, 'child_template_id')->orderBy('serial_number');
    }

    function isTest(): bool
    {
        return $this->is_test == true;
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

    function name_id()
    {
        return $this->name() . " (Id = " . strval($this->id) . ")";
    }

    function name_is_test()
    {
        $result = $this->name();
        if ($this->isTest()){
            $result = $result . " (". mb_strtolower(trans('main.is_test')) . ")";
        }
        return $result;
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

}
