<?php
use App\Models\Template;
$template = Template::findOrFail($role->template_id);
?>@include('layouts.template.show_name',['template'=>$template])
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h4><a href="{{route('role.index', $template)}}" title="{{trans('main.role')}}" class="text-warning">{{trans('main.role')}}</a><span class="text-warning">:</span>
            <a href="{{route('role.show', $role)}}" title="{{$role->name()}}">{{$role->name()}}</a>
            </h4>
        </div>
    </div>
</div>

