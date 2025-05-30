<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function isAdmin(): bool
    {
        return $this->is_admin == true;
    }

    function isModerator(): bool
    {
        return $this->is_moderator == true;
    }

    function isTester(): bool
    {
        return $this->is_tester == true;
    }

    function count()
    {
        return $this->count;
    }

    function name()
    {
        return $this->name;
    }

    function email()
    {
        return $this->email;
    }

    function get_user_item()
    {
        $result_item = null;
        $usersetup_project_id = env('USERSETUP_PROJECT_ID');
        $usersetup_base_id = env('USERSETUP_BASE_ID');
        $usersetup_avatar_link_id = env('USERSETUP_AVATAR_LINK_ID');
        //if (Auth::check()) {
        if ($usersetup_project_id != '' && $usersetup_base_id != '' && $usersetup_avatar_link_id != '') {
            $project = Project::find($usersetup_project_id);
            $base = Base::find($usersetup_base_id);
            $link = Link::find($usersetup_avatar_link_id);
            if ($project && $base && $link) {
                $username = $this->name();
                $result_item = Item::select('items.*')
                    ->where('items.project_id', '=', $project->id)
                    ->where('items.base_id', '=', $base->id)
                    ->where('items.name_lang_0', '=', $username)
                    ->first();
            }
        }
        //}
        return $result_item;
    }

//    function get_user_avatar_item()
//    {
//        $result_item = null;
//        $usersetup_project_id = env('USERSETUP_PROJECT_ID');
//        $usersetup_base_id = env('USERSETUP_BASE_ID');
//        $usersetup_avatar_link_id = env('USERSETUP_AVATAR_LINK_ID');
//        //if (Auth::check()) {
//        if ($usersetup_project_id != '' && $usersetup_base_id != '' && $usersetup_avatar_link_id != '') {
//            $project = Project::find($usersetup_project_id);
//            $base = Base::find($usersetup_base_id);
//            $link = Link::find($usersetup_avatar_link_id);
//            if ($project && $base && $link) {
//                $username = $this->name();
//
//                $main = Main::select('mains.*')
//                    ->join('items', 'mains.child_item_id', '=', 'items.id')
//                    ->where('mains.link_id', '=', $link->id)
//                    ->where('items.project_id', '=', $project->id)
//                    ->where('items.base_id', '=', $base->id)
//                    ->where('items.name_lang_0', '=', $username);
//
//                $main_array = $main->get();
//                if (count($main_array) > 0) {
//                    $item = Item::find($main_array[0]->parent_item_id);
//                    if ($item) {
//                        $result_item = $item;
//                    }
//                }
//
//            }
//        }
//        //}
//        return $result_item;

    function get_user_avatar_item()
    {
        $result_item = null;
        $user_item = $this->get_user_item();
        if ($user_item) {
            $usersetup_project_id = env('USERSETUP_PROJECT_ID');
            $usersetup_base_id = env('USERSETUP_BASE_ID');
            $usersetup_avatar_link_id = env('USERSETUP_AVATAR_LINK_ID');
            //if (Auth::check()) {
            if ($usersetup_project_id != '' && $usersetup_base_id != '' && $usersetup_avatar_link_id != '') {
                $project = Project::find($usersetup_project_id);
                $base = Base::find($usersetup_base_id);
                $link = Link::find($usersetup_avatar_link_id);
                if ($project && $base && $link) {

                    $main = Main::select('mains.*')
                        ->where('mains.child_item_id', '=', $user_item->id)
                        ->where('mains.link_id', '=', $link->id);

                    $main_array = $main->get();
                    if (count($main_array) > 0) {
                        $item = Item::find($main_array[0]->parent_item_id);
                        if ($item) {
                            $result_item = $item;
                        }
                    }

                }
            }
            //}
        }
        return $result_item;
    }

    function get_user_itnm()
    {
        $result = "";
        $item = $this->get_user_item();
        if ($item) {
            $result = $item->name();
        }
        return $result;
    }

}
