@extends('layouts.app')

@section('content')

    <?php
    use App\Http\Controllers\BaseController;
    use App\Http\Controllers\GlobalController;
    use Illuminate\Support\Facades\Request;
    ?>
    <p>
        @include('layouts.form_show_title', ['type_form'=>$type_form, 'table_name'=>trans('main.template')])
    </p>

    <p>Id: <b>{{$template->id}}</b></p>

    @foreach (config('app.locales') as $key=>$value)
        <p>{{trans('main.name')}} ({{trans('main.' . $value)}}): <b>{{$template['name_lang_' . $key]}}</b></p>
    @endforeach

    <p>{{trans('main.is_test')}}: <b>{{GlobalController::name_is_boolean($template->is_test)}}</b></p>
    <p>{{trans('main.is_closed_default_value')}}: <b>{{GlobalController::name_is_boolean($template->is_closed_default_value)}}</b></p>
    <p>{{trans('main.is_closed_default_value_fixed')}}: <b>{{GlobalController::name_is_boolean($template->is_closed_default_value_fixed)}}</b></p>

    @foreach (config('app.locales') as $key=>$value)
        {{--        <p>{{trans('main.desc')}} ({{trans('main.' . $value)}}): <b>{{$template['desc_lang_' . $key]}}</b></p>--}}
        <p>{{trans('main.desc')}} ({{trans('main.' . $value)}}):
        <div class="text-label"><?php echo nl2br($template['desc_lang_' . $key]); ?></div></p>
    @endforeach

    @if ($type_form == 'show')
        @if(Auth::user()->isAdmin() == true)
            <p>
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                        onclick="document.location='{{route('template.edit',$template)}}'"
                        title="{{trans('main.edit')}}">
                    <i class="fas fa-edit"></i>
                    {{trans('main.edit')}}
                </button>
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                        onclick="document.location='{{route('template.delete_question',$template)}}'"
                        title="{{trans('main.delete')}}">
                    <i class="fas fa-trash"></i>
                    {{trans('main.delete')}}
                </button>
            </p>
        @endif
        <p>
            @if(Auth::user()->isAdmin() == true)
                {{--            "mb-1 mb-sm-0" нужно чтобы на маленьких экранах кнопки не слипались (margin-botton - 1px) --}}
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0" title="{{trans('main.projects')}}"
                        onclick="document.location='{{route('project.index_template', $template)}}'">
                    <i class="fas fa-cube"></i>
                    {{trans('main.projects')}}
                </button>
            @else
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0" title="{{trans('main.projects')}}"
                        onclick="document.location='{{route('project.index_user', Auth::user())}}'">
                    <i class="fas fa-cube"></i>
                    {{trans('main.projects')}}
                </button>
            @endif
            <button type="button" class="btn btn-dreamer  mb-1 mb-sm-0" title="{{trans('main.roles')}}"
                    onclick="document.location='{{route('role.index', $template)}}'">
                <i class="fas fa-user-circle"></i>
                {{trans('main.roles')}}
            </button>
            <button type="button" class="btn btn-dreamer  mb-1 mb-sm-0" title="{{trans('main.levels')}}"
                    onclick="document.location='{{route('level.index', $template)}}'">
                <i class="fas fa-layer-group"></i>
                {{trans('main.levels')}}
            </button>
            <button type="button" class="btn btn-dreamer mb-1 mb-sm-0" title="{{trans('main.bases')}}"
                    onclick="document.location='{{route('base.index', $template)}}'">
                <i class="fas fa-atom"></i>
                {{trans('main.bases')}}
            </button>
            <button type="button" class="btn btn-dreamer  mb-1 mb-sm-0" title="{{trans('main.sets')}}"
                    onclick="document.location='{{route('set.index', $template)}}'">
                <i class="fas fa-equals"></i>
                {{trans('main.sets')}}
            </button>
        </p>
        <p>
            <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                    title="{{trans('main.cancel')}}" @include('layouts.template.previous_url')>
                <i class="fas fa-arrow-left"></i>
                {{trans('main.cancel')}}
            </button>
        </p>
    @elseif($type_form == 'delete_question')
        <form action="{{route('template.delete', $template)}}" method="POST" id='delete-form'>
            @csrf
            @method('DELETE')
            <p>
                <button type="submit" class="btn btn-danger" title="{{trans('main.delete')}}">
                    <i class="fas fa-trash"></i>
                    {{trans('main.delete')}}
                </button>
                <button type="button" class="btn btn-dreamer"
                        title="{{trans('main.cancel')}}" @include('layouts.template.previous_url')>
                    <i class="fas fa-arrow-left"></i>
                    {{trans('main.cancel')}}
                </button>
            </p>
        </form>
    @endif

@endsection
