@extends('layouts.app')

@section('content')
    <?php
    use App\Http\Controllers\GlobalController;
    ?>
    <p>
    <div class="container-fluid">
        <div class="row">
            <div class="col-5 text-center">
                <h3>{{trans('main.configuring_templates')}}</h3>
            </div>
            <div class="col-2">
            </div>
            <div class="col-5 text-right">
                @if (Auth::user()->isAdmin())
                <button type="button" class="btn btn-dreamer" title="{{trans('main.add')}}"
                        {{--                        "d-inline" нужно, чтобы иконка и текст были на одной линии--}}
                        onclick="document.location='{{route('template.create')}}'"><i class="fas fa-plus d-inline"></i>&nbsp;{{trans('main.add')}}
                </button>
                @endif
            </div>
        </div>
    </div>
    </p>
    <table class="table table-sm table-bordered table-hover">
        <caption>{{trans('main.select_record_for_work')}}</caption>
        <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="text-left">{{trans('main.name')}}</th>
            <th class="text-left">{{trans('main.is_test')}}</th>
            <th class="text-left">{{trans('main.is_closed_default_value')}}</th>
            <th class="text-left">{{trans('main.is_closed_default_value_fixed')}}</th>
            <th class="text-center">{{trans('main.projects')}}</th>
            <th class="text-center">{{trans('main.roles')}}</th>
            <th class="text-center">{{trans('main.levels')}}</th>
            <th class="text-center">{{trans('main.bases')}}</th>
            <th class="text-center">{{trans('main.sets')}}</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = $templates->firstItem() - 1;
        ?>
        @foreach($templates as $template)
            <?php
            $i++;
            ?>
            <tr>
                {{--                <th scope="row">{{$i}}</th>--}}
                <td class="text-center">
                    <a href="{{route('template.show',$template)}}" title="{{trans('main.show')}}">
                        {{$i}}
                    </a></td>
                <td class="text-left">
                    <a href="{{route('template.show',$template)}}" title="{{trans('main.show')}}">
                        {{$template->name()}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('template.show',$template)}}" title="{{trans('main.show')}}">
                        {{GlobalController::name_is_boolean($template->is_test)}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('template.show',$template)}}" title="{{trans('main.show')}}">
                        {{GlobalController::name_is_boolean($template->is_closed_default_value)}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('template.show',$template)}}" title="{{trans('main.show')}}">
                        {{GlobalController::name_is_boolean($template->is_closed_default_value_fixed)}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('project.index_template', $template)}}" title="{{trans('main.projects')}}">
                        <i class="fas fa-cube"></i> ({{$template->projects_count}})
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('role.index', $template)}}" title="{{trans('main.roles')}}">
                        <i class="fas fa-user-circle"></i> ({{$template->roles_count}})
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('level.index', $template)}}" title="{{trans('main.levels')}}">
{{--                        <i class="fas fa-layer-group"></i> ({{$template->levels_counts}})--}}
                        <i class="fas fa-layer-group"></i> ({{count($template->levels)}})
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('base.index', $template)}}" title="{{trans('main.bases')}}">
                        <i class="fas fa-atom"></i> ({{$template->bases_count}})
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('set.index', $template)}}" title="{{trans('main.sets')}}">
                        <i class="fas fa-equals"></i> ({{$template->sets_count}})
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$templates->links()}}
@endsection

