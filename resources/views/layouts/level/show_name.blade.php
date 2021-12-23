<?php
use App\Models\Template;
$template = Template::findOrFail($level->template_id);
?>@include('layouts.template.show_name',['template'=>$template])
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h4><a href="{{route('level.index', $template)}}" title="{{trans('main.level')}}" class="text-warning">{{trans('main.level')}}</a><span class="text-warning">:</span>
            <a href="{{route('level.show', $level)}}" title="{{$level->name()}}">{{$level->name()}}</a>
            </h4>
        </div>
    </div>
</div>

