@extends('layouts.app')

@section('content')

    <?php
    use App\Http\Controllers\BaseController;
    use Illuminate\Support\Facades\Request;
    ?>
    <p>
        @include('layouts.template.show_name', ['template'=>$template])
        @include('layouts.form_show_title', ['type_form'=>$type_form, 'table_name'=>trans('main.task')])
    </p>

    <p>Id: <b>{{$task->id}}</b></p>

    @foreach (config('app.locales') as $key=>$value)
        <p>{{trans('main.name')}} ({{trans('main.' . $value)}}): <b>{{$task['name_lang_' . $key]}}</b></p>
    @endforeach

    @if ($type_form == 'show')
        <p>
            <button type="button" class="btn btn-dreamer"
                    onclick="document.location='{{route('task.edit',$task)}}'" title="{{trans('main.edit')}}">
                {{--            <i class="fas fa-edit"></i>--}}
                {{trans('main.edit')}}
            </button>
            <button type="button" class="btn btn-dreamer"
                    onclick="document.location='{{route('task.delete_question',$task)}}'"
                    title="{{trans('main.delete')}}">
                {{--            <i class="fas fa-trash"></i>--}}
                {{trans('main.delete')}}
            </button>
        </p>
        <p>
            <button type="button" class="btn btn-dreamer" title="{{trans('main.modules')}}"
                    onclick="document.location='{{route('module.index', $task)}}'">
                {{--            <i class="fas fa-tasks"></i>--}}
                {{trans('main.modules')}}
            </button>

            <button type="button" class="btn btn-dreamer"
                    title="{{trans('main.cancel')}}" @include('layouts.task.previous_url')>
                {{--            <i class="fas fa-arrow-left"></i>--}}
                {{trans('main.cancel')}}
            </button>
        </p>
    @elseif($type_form == 'delete_question')
        <form action="{{route('task.delete', $task)}}" method="POST" id='delete-form'>
            @csrf
            @method('DELETE')
            <p>
                <button type="submit" class="btn btn-danger" title="{{trans('main.delete')}}">
                    {{--                <i class="fas fa-trash"></i>--}}
                    {{trans('main.delete')}}
                </button>
                <button type="button" class="btn btn-dreamer"
                        title="{{trans('main.cancel')}}" @include('layouts.task.previous_url')>
                    {{--                <i class="fas fa-arrow-left"></i>--}}
                    {{trans('main.cancel')}}
                </button>
            </p>
        </form>
    @endif

@endsection
