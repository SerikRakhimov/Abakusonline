<?php
use \App\Http\Controllers\GlobalController;
$is_relit_id = isset($relit_id);
if ($is_relit_id) {
    $relip_project = GlobalController::calc_relip_project($relit_id, $project);
}
$get_items_setup = $project->get_items_setup();
$get_project_logo_item = $get_items_setup['logo_item'];
$get_user_author_avatar_item = $project->user->get_user_avatar_item();
?>
<p>
<div class="container-fluid">
    <p class="text-center">
        <a href="{{route('project.start', ['project' => $project->id, 'role' => $role])}}"
           title="{{trans('main.mainmenu')}}">
            @if($get_project_logo_item)
                @include('view.img',['item'=>$get_project_logo_item, 'size'=>"avatar", 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>false, 'title'=>'empty'])
                &nbsp;
            @endif
            <mark class="text-project">{{$project->name()}}</mark>
            &nbsp;@include('layouts.project.show_icons',['project'=>$project])
            <br>
            @if($is_relit_id)
                @if($relit_id != 0 && $role->is_view_info_relits == true)
                    <small><small><small>{{trans('main.project')}}: </small></small></small>
                    <small><small>{{$relip_project->name()}}</small></small>
                @endif
            @endif
        </a>
    </p>
    <div class="row">
        <div class="col-6 text-left">
            <a href="{{route('project.start', ['project' => $project->id, 'role' => $role])}}"
               title="{{trans('main.author')}}">
                {{-- <mark class="text-project">@guest{{trans('main.guest')}}@endguest @auth{{Auth::user()->name()}}@endauth</mark>--}}
                <small><small>{{mb_strtolower(trans('main.author'))}}: </small></small>
                <mark class="text-project"><small>{{$project->user->name()}}</small></mark>
                @if($get_user_author_avatar_item)
                    @include('view.img',['item'=>$get_user_author_avatar_item, 'size'=>"avatar", 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>false, 'title'=>trans('main.author')])
                @endif
            </a>
        </div>
        <div class="col-6 text-right">
            <a href="{{route('project.start', ['project' => $project->id, 'role' => $role])}}"
               title="{{trans('main.role')}}">
                <small><small>{{mb_strtolower(trans('main.role'))}}: </small></small>
                <mark class="text-project"><small>{{$role->name()}}</small></mark>
            </a>
        </div>
    </div>
    <blockquote class="text-title pt-1 pl-5 pr-5"><?php echo nl2br($role->desc()); ?></blockquote>
</div>
</p>
<hr>
