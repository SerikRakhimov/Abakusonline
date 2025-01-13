@extends('layouts.app')

@section('content')
    <?php
    use App\Http\Controllers\GlobalController;
    use App\Http\Controllers\ProjectController;
    use App\Models\User;
    $is_project = isset($project);
    $is_user = isset($user);
    $is_role = isset($role);
    $access_show = "";
    if ($is_project == true) {
        $access_show = "access.show_project";
    }
    if ($is_user == true) {
        $access_show = "access.show_user";
    }
    if ($is_role == true) {
        $access_show = "access.show_role";
    }
    ?>
    <p>
    @if($is_project)
        @include('layouts.project.show_name',['project'=>$project])
    @endif
    @if($is_user)
        @include('layouts.user.show_name',['user'=>$user])
    @endif
    @if($is_role)
        @include('layouts.role.show_name',['role'=>$role])
    @endif
    <div class="container-fluid">
        <div class="row">
            <div class="col-5 text-center">
                <h3>{{trans('main.accesses')}}</h3>
            </div>
            <div class="col-2">
            </div>
            <div class="col-5 text-right">
                <button type="button" class="btn btn-dreamer" title="{{trans('main.add')}}"
                        onclick="document.location=
                        @if($is_project)
                            '{{route('access.create_project', ['project'=>$project])}}'
                            ">
                    @endif
                    @if($is_user)
                        '{{route('access.create_user', ['user'=>$user])}}'
                        ">
                    @endif
                    @if($is_role)
                        '{{route('access.create_role', ['role'=>$role])}}'
                        ">
                    @endif
                    <i class="fas fa-plus d-inline"></i>
                    {{trans('main.add')}}
                </button>
            </div>
        </div>
    </div>
    </p>
    <table class="table table-sm table-bordered table-hover">
        <caption>{{trans('main.select_record_for_work')}}</caption>
        <thead>
        <tr>
            <th class="text-center">#</th>
            @if(!$is_project)
                <th class="text-left">{{trans('main.project')}}</th>
            @endif
            @if(!$is_user)
                <th class="text-left">{{trans('main.user')}}</th>
            @endif
            @if(!$is_role)
                <th class="text-left">{{trans('main.role')}}</th>
            @endif
            <th class="text-center">{{trans('main.is_subscription_request')}}</th>
            <th class="text-center">{{trans('main.is_access_allowed')}}</th>
            <th class="text-center">{{trans('main.desc')}}</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = $accesses->firstItem() - 1;
        ?>
        @foreach($accesses as $access)
            <?php
            $i++;
            ?>
            <tr>
                <td class="text-center">
                    <a href="{{route($access_show, $access)}}" title="{{trans('main.show')}}">
                        {{$i}}
                    </a></td>
                @if(!$is_project)
                    <td class="text-left">
                        <a href="{{route($access_show, $access)}}" title="{{trans('main.show')}}">
                            {{$access->project->name()}}
                        </a>
                    </td>
                @endif
                @if(!$is_user)
                    <td class="text-left">
                        <a href="{{route($access_show, $access)}}" title="{{trans('main.show')}}">
                            {{$access->user->name}} @include('layouts.user.show_logotype',['user'=>$access->user])
                        </a>
                    </td>
                @endif
                @if(!$is_role)
                    <td class="text-left">
                        <a href="{{route($access_show, $access)}}" title="{{trans('main.show')}}">
                            {{$access->role->name()}}
                        </a>
                    </td>
                @endif
                <td class="text-center">
                    <a href="{{route($access_show, $access)}}" title="{{trans('main.show')}}">
                        {{GlobalController::name_is_boolean($access->is_subscription_request)}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route($access_show, $access)}}" title="{{trans('main.show')}}">
                        {{GlobalController::name_is_boolean($access->is_access_allowed)}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route($access_show, $access)}}" title="{{trans('main.show')}}">
                            <span
                                @if($access->is_subscription_request == true)
                                class="text-danger"
                                @endif
                            >{{ProjectController::subs_desc($access)}}</span>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$accesses->links()}}
@endsection

