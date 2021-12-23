<?php

namespace App\Models;

use App\Http\Controllers\GlobalController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Text extends Model
{
    protected $fillable = ['item_id', 'code', 'name_lang_0', 'name_lang_1', 'name_lang_2', 'name_lang_3'];

    function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
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


}
