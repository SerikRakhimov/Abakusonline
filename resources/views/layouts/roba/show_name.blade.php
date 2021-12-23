{{--<div class="container-fluid">--}}
{{--    <div class="row">--}}
{{--        <div class="col-12 text-center">--}}
{{--            <h4><a href=" {{route('roba.show_project', $roba)}}" title="{{$roba->id}}">{{$roba->id}}</a>--}}
{{--            </h4>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<?php
use App\Models\Role;
$role = Role::findOrFail($roba->role_id);
?>@include('layouts.role.show_name',['role'=>$role])
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h4><a href="{{route('roba.index_role', $role)}}" title="{{trans('main.roba')}}" class="text-warning">{{trans('main.roba')}}</a><span class="text-warning">:</span>
                <a href="{{route('roba.show', $roba)}}" title="{{$roba->name()}}">{{$roba->name()}}</a>
            </h4>
        </div>
    </div>
</div>

