<?php
use App\Models\Template;
$template = Template::findOrFail($relip->template_id);
?>@include('layouts.template.show_name',['template'=>$template])
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h4><a href="{{route('relip.index', $template)}}" title="{{trans('main.relip')}}" class="text-warning">{{trans('main.relip')}}</a><span class="text-warning">:</span>
            <a href="{{route('relip.show', $relip)}}" title="{{$relip->name()}}">{{$relip->name()}}</a>
            </h4>
        </div>
    </div>
</div>

