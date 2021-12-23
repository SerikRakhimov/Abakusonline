<?php

use App\Models\Template;
use Illuminate\Support\Facades\Auth;

$template = Template::findOrFail($project->template_id);
$is_admin = Auth::user()->isAdmin();
?>
@include('layouts.template.show_name',['template'=>$template])
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h4>
                @if($is_admin)
                    <a href="{{route('project.index_template', $template)}}" title="{{trans('main.project')}}"
                       class="text-warning">
                        @endif
                        {{trans('main.project')}}
                        @if($is_admin)
                    </a>
                @endif
                <span class="text-warning">:</span>
                @if($is_admin)
                    <a href="{{route('project.show_template', $project)}}" title="{{$project->name()}}">
                        @endif
                        {{$project->name()}}&nbsp;&nbsp;&nbsp;@include('layouts.project.show_icons',['project'=>$project])
                        @if($is_admin)
                    </a>
                @endif
            </h4>
        </div>
    </div>
</div>

