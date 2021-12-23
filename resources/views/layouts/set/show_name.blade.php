<?php
use App\Models\Template;
$template = Template::findOrFail($set->template_id);
?>@include('layouts.template.show_name',['template'=>$template])
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h4><a href="{{route('set.index', $template)}}" title="{{trans('main.set')}}" class="text-warning">{{trans('main.set')}}</a><span class="text-warning">:</span>
            <a href="{{route('set.show', $set)}}" title="{{$set->name()}}">{{$set->name()}}</a>
            </h4>
        </div>
    </div>
</div>

