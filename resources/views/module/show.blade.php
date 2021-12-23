@extends('layouts.app')

@section('content')

    <?php
    use App\Http\Controllers\BaseController;
    use Illuminate\Support\Facades\Request;
    ?>
    <p>
        @include('layouts.task.show_name', ['task'=>$task])
        @include('layouts.form_show_title', ['type_form'=>$type_form, 'table_name'=>trans('main.module')])
    </p>

    <p>Id: <b>{{$module->id}}</b></p>

    @foreach (config('app.locales') as $key=>$value)
        <p>{{trans('main.name')}} ({{trans('main.' . $value)}}): <b>{{$module['name_lang_' . $key]}}</b></p>
    @endforeach

    @if ($type_form == 'show')
        <p>
            <button type="button" class="btn btn-dreamer"
                    onclick="document.location='{{route('module.edit',$module)}}'" title="{{trans('main.edit')}}">
                {{--            <i class="fas fa-edit"></i>--}}
                {{trans('main.edit')}}
            </button>
            <button type="button" class="btn btn-dreamer"
                    onclick="document.location='{{route('module.delete_question',$module)}}'"
                    title="{{trans('main.delete')}}">
                {{--            <i class="fas fa-trash"></i>--}}
                {{trans('main.delete')}}
            </button>
        </p>
        <p>
            <button type="button" class="btn btn-dreamer" title="{{trans('main.bases')}}"
                    onclick="document.location='{{route('base.index', $module)}}'">
                {{--            <i class="fas fa-modules"></i>--}}
                {{trans('main.bases')}}
            </button>

            <button type="button" class="btn btn-dreamer"
                    title="{{trans('main.cancel')}}" @include('layouts.module.previous_url')>
                {{--            <i class="fas fa-arrow-left"></i>--}}
                {{trans('main.cancel')}}
            </button>
        </p>
    @elseif($type_form == 'delete_question')
        <form action="{{route('module.delete', $module)}}" method="POST" id='delete-form'>
            @csrf
            @method('DELETE')
            <p>
                <button type="submit" class="btn btn-danger" title="{{trans('main.delete')}}">
                    {{--                <i class="fas fa-trash"></i>--}}
                    {{trans('main.delete')}}
                </button>
                <button type="button" class="btn btn-dreamer"
                        title="{{trans('main.cancel')}}" @include('layouts.module.previous_url')>
                    {{--                <i class="fas fa-arrow-left"></i>--}}
                    {{trans('main.cancel')}}
                </button>
            </p>
        </form>
    @endif

@endsection
