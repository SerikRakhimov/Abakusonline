<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relip extends Model
{
    use HasFactory;
    protected $fillable = ['relit_id', 'child_project_id', 'parent_project_id'];

    function relit()
    {
        return $this->belongsTo(Relit::class, 'relit_id');
    }

    function child_project()
    {
        return $this->belongsTo(Project::class, 'child_project_id');
    }

    function parent_project()
    {
        return $this->belongsTo(Project::class, 'parent_project_id');
    }

}
