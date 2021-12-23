@extends('layouts.app')

@section('content')
    <?php
    use App\Http\Controllers\GlobalController;
    use App\Http\Controllers\ProjectController;
    $is_template = isset($template);
    $is_user = isset($user);
    $project_show = "";
    if ($is_template == true) {
        $project_show = "project.show_template";
    }
    if ($is_user == true) {
        $project_show = "project.show_user";
    }
    ?>
    <p>
        @if($is_template)
            @include('layouts.template.show_name',['template'=>$template])
        @endif
        @if($is_user)
            @include('layouts.user.show_name',['user'=>$user])
        @endif
    </p>
    <div class="container-fluid">
        <div class="row">
            <div class="col-5 text-center">
                <h3>{{trans('main.projects')}}</h3>
            </div>
            <div class="col-2">
            </div>
            <div class="col-5 text-right">
                <button type="button" class="btn btn-dreamer" title="{{trans('main.add')}}"
                        onclick="document.location=
                        @if($is_template)
                            '{{route('project.create_template', ['template'=>$template])}}'
                            ">
                    @endif
                    @if($is_user)
                        '{{route('project.create_user', ['user'=>$user])}}'
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
            <th class="text-left">{{trans('main.account')}}</th>
            @if(!$is_template)
                <th class="text-left">{{trans('main.template')}}</th>
            @endif
            @if(!$is_user)
                <th class="text-left">{{trans('main.author')}}</th>
            @endif
            <th class="text-left">{{trans('main.name')}}</th>
            <th class="text-left">{{trans('main.is_test')}}</th>
            <th class="text-left">{{trans('main.is_closed')}}</th>
            <th class="text-center">Id</th>
            <th class="text-center">{{trans('main.accesses')}}</th>
            <th class="text-center">{{trans('main.subscription_requests')}}</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = $projects->firstItem() - 1;
        ?>
        @foreach($projects as $project)
            <?php
            $i++;
            ?>
            <tr>
                <td class="text-center">
                    <a href="{{route($project_show, $project)}}" title="{{trans('main.show')}}">
                        {{$i}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route($project_show, $project)}}" title="{{trans('main.show')}}">
                        {{$project->account}}
                    </a>
                </td>
                @if(!$is_template)
                    <td class="text-left">
                        <a href="{{route($project_show, $project)}}" title="{{trans('main.show')}}">
                            {{$project->template->name()}}
                        </a>
                    </td>
                @endif
                @if(!$is_user)
                    <td class="text-left">
                        <a href="{{route($project_show, $project)}}" title="{{trans('main.show')}}">
                            {{$project->user->name}}
                        </a>
                    </td>
                @endif
                <td class="text-left">
                    <a href="{{route($project_show, $project)}}" title="{{trans('main.show')}}">
                        {{$project->name()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route($project_show, $project)}}" title="{{trans('main.show')}}">
                        {{GlobalController::name_is_boolean($project->is_test)}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route($project_show, $project)}}" title="{{trans('main.show')}}">
                        {{GlobalController::name_is_boolean($project->is_closed)}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route($project_show, $project)}}" title="{{trans('main.show')}}">
                        {{$project->id}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('access.index_project', $project)}}" title="{{trans('main.accesses')}}">
                        <i class="fas fa-universal-access"></i>
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('access.index_project', $project)}}" title="{{trans('main.accesses')}}">
                        {{ProjectController::subs_req_count($project)}}
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$projects->links()}}
@endsection

