<?php
use App\Models\Template;
$template = Template::findOrFail($base->template_id);
?>@include('layouts.template.show_name',['template'=>$template])
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h4><a href="{{route('base.index', $template)}}" title="{{trans('main.base')}}" class="text-warning">{{trans('main.base')}}</a><span class="text-warning">:</span>
            <a href="{{route('base.show', $base)}}" title="{{$base->name()}}">{{$base->name()}}</a>
            </h4>
        </div>
    </div>
</div>

