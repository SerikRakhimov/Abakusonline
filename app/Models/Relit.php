<?php

namespace App\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relit extends Model
{
    use HasFactory;
    protected $fillable = ['serial_number', 'child_template_id', 'parent_template_id'];

    function child_template()
    {
        return $this->belongsTo(Template::class, 'child_template_id');
    }

    function parent_template()
    {
        return $this->belongsTo(Template::class, 'parent_template_id');
    }

    function title()
    {
        $result = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $result = $this['parent_title_lang_' . $index];
        }
        return $result;
    }

    function name()
    {
        $result = $this->serial_number . '. ' . $this->parent_template->name()
                . ' (Id =' . $this->id . ', ' . $this->title() . ')';
        return $result;
    }

}
