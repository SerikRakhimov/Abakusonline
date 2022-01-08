<?php

namespace App\Models;

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

}
