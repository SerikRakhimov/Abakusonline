<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roli extends Model
{
    protected $fillable = ['role_id', 'link_id'];

    function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    function link()
    {
        return $this->belongsTo(Link::class, 'link_id');
    }
}
