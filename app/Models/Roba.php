<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roba extends Model
{
    protected $fillable = ['role_id', 'base_id'];

    function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    function base()
    {
        return $this->belongsTo(Base::class, 'base_id');
    }

}
