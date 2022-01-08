<?php
use App\Models\Template;
$template = Template::findOrFail($relit->template_id);
?>@include('layouts.template.show_name',['template'=>$template])
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h4><a href="{{route('relit.index', $template)}}" title="{{trans('main.relit')}}" class="text-warning">{{trans('main.relit')}}</a><span class="text-warning">:</span>
            <a href="{{route('relit.show', $relit)}}" title="{{$relit->name()}}">{{$relit->name()}}</a>
            </h4>
        </div>
    </div>
</div>

