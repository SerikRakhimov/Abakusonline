{{--<div class="container-fluid">--}}
{{--    <div class="row">--}}
{{--        <div class="col-12 text-center">--}}
{{--            <h4><a href=" {{route('roli.show_project', $roli)}}" title="{{$roli->id}}">{{$roli->id}}</a>--}}
{{--            </h4>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<?php
use App\Models\Role;
$role = Role::findOrFail($roli->role_id);
?>@include('layouts.role.show_name',['role'=>$role])
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h4><a href="{{route('roli.index_role', $role)}}" title="{{trans('main.roli')}}" class="text-warning">{{trans('main.roli')}}</a><span class="text-warning">:</span>
                <a href="{{route('roli.show', $roli)}}" title="{{$roli->name()}}">{{$roli->name()}}</a>
            </h4>
        </div>
    </div>
</div>
