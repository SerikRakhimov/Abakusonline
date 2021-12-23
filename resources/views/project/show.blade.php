@extends('layouts.app')

@section('content')

    <?php
    use App\Http\Controllers\BaseController;
    use App\Http\Controllers\GlobalController;
    use Illuminate\Support\Facades\Request;
    use App\Models\User;
    $is_template = isset($template);
    $is_user = isset($user);
    $project_edit = "";
    if($is_template == true){
        $project_edit = "project.edit_template";
    }
    if($is_user == true){
        $project_edit = "project.edit_user";
    }
    ?>
    <p>
        @if($is_template)
            @include('layouts.template.show_name',['template'=>$template])
        @endif
        @if($is_user)
            @include('layouts.user.show_name',['user'=>$user])
        @endif
        @include('layouts.form_show_title', ['type_form'=>$type_form, 'table_name'=>trans('main.project')])
    </p>

    <p>Id: <b>{{$project->id}}</b></p>
    <p>{{trans('main.account')}}: <b>{{$project->account}}</b></p>

    @if(!$is_template)
        <p>{{trans('main.template')}}: <b>{{$project->template->name()}}</b></p>
    @endif

    @foreach (config('app.locales') as $key=>$value)
        <p>{{trans('main.name')}} ({{trans('main.' . $value)}}): <b>{{$project['name_lang_' . $key]}}</b></p>
    @endforeach

    <p>{{trans('main.is_test')}}: <b>{{GlobalController::name_is_boolean($project->is_test)}}</b></p>
    <p>{{trans('main.is_closed')}}: <b>{{GlobalController::name_is_boolean($project->is_closed)}}</b></p>

    @if(!$is_user)
        <p>{{trans('main.author')}}: <b>{{$project->user->name()}}</b></p>
    @endif

    @if ($type_form == 'show')
        <p>
            <button type="button" class="btn btn-dreamer"
                    onclick="document.location='{{route($project_edit,$project)}}'" title="{{trans('main.edit')}}">
                            <i class="fas fa-edit"></i>
                {{trans('main.edit')}}
            </button>
            <button type="button" class="btn btn-dreamer"
                    onclick="document.location='{{route('project.delete_question',$project)}}'"
                    title="{{trans('main.delete')}}">
                            <i class="fas fa-trash"></i>
                {{trans('main.delete')}}
            </button>
        </p>
        <p>
            {{--            Не удалять--}}
{{--            <button type="button" class="btn btn-dreamer" title="{{trans('main.accesses')}}"--}}
{{--                    onclick="document.location='{{route('access.index_project', $project)}}'">--}}
{{--                            <i class="fas fa-universal-access"></i>--}}
{{--                {{trans('main.accesses')}}--}}
{{--            </button>--}}
            <button type="button" class="btn btn-dreamer"
                    title="{{trans('main.cancel')}}" @include('layouts.project.previous_url')>
                            <i class="fas fa-arrow-left"></i>
                {{trans('main.cancel')}}
            </button>
        </p>
    @elseif($type_form == 'delete_question')
        <form action="{{route('project.delete', $project)}}" method="POST" id='delete-form'>
            @csrf
            @method('DELETE')
            <p>
                <button type="submit" class="btn btn-danger" title="{{trans('main.delete')}}">
                                    <i class="fas fa-trash"></i>
                    {{trans('main.delete')}}
                </button>
                <button type="button" class="btn btn-dreamer"
                        title="{{trans('main.cancel')}}" @include('layouts.project.previous_url')>
                                    <i class="fas fa-arrow-left"></i>
                    {{trans('main.cancel')}}
                </button>
            </p>
        </form>
    @endif

@endsection
