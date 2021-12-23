{{--<div class="container-fluid">--}}
{{--    <div class="row">--}}
{{--        <div class="col-12 text-center">--}}
{{--            <h4><a href=" {{route('link.show_project', $link)}}" title="{{$link->id}}">{{$link->id}}</a>--}}
{{--            </h4>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<?php
use App\Models\Base;
$base = Base::findOrFail($link->child_base_id);
?>
{{--@include('layouts.role.show_name',['role'=>$role])--}}
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h4><a href="{{route('link.base_index', $base)}}" title="{{trans('main.link')}}" class="text-warning">{{trans('main.link')}}</a><span class="text-warning">:</span>
                <a href="{{route('link.show', $link)}}" title="{{$link->name()}}">{{$link->name()}}</a>
            </h4>
        </div>
    </div>
</div>
