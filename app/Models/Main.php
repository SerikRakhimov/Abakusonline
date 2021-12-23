<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Main extends Model
{
    protected $fillable = ['link_id', 'child_item_id', 'parent_item_id', 'updated_user_id'];

    function link() {
        return $this->belongsTo(Link::class, 'link_id');
    }

    function child_item() {
        return $this->belongsTo(Item::class, 'child_item_id');
    }

    function parent_item() {
        return $this->belongsTo(Item::class, 'parent_item_id');
    }

    function updated_user()
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }


}
