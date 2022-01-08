@extends('layouts.app')

@section('content')

    <?php
    use App\Http\Controllers\GlobalController;
    use App\Http\Controllers\BaseController;
    use Illuminate\Support\Facades\Request;
    use App\Models\User;
    ?>
    <p>
        @include('layouts.template.show_name', ['template'=>$template])
        @include('layouts.form_show_title', ['type_form'=>$type_form, 'table_name'=>trans('main.relit')])
    </p>

    <p>Id: <b>{{$relit->id}}</b></p>
    <p>{{trans('main.serial_number')}}: <b>{{$relit->serial_number}}</b></p>
    <p>{{trans('main.parent')}}_{{trans('main.template')}}: <b>{{$relit->parent_template->name()}} (Id = {{$relit->parent_template_id}})</b>

    @if ($type_form == 'show')
        <p>
            <button type="button" class="btn btn-dreamer"
                    onclick="document.location='{{route('relit.edit',$relit)}}'" title="{{trans('main.edit')}}">
                            <i class="fas fa-edit"></i>
                {{trans('main.edit')}}
            </button>
            <button type="button" class="btn btn-dreamer"
                    onclick="document.location='{{route('relit.delete_question',$relit)}}'"
                    title="{{trans('main.delete')}}">
                            <i class="fas fa-trash"></i>
                {{trans('main.delete')}}
            </button>
        </p>
        <p>
            {{--            Не удалять--}}
{{--            <button type="button" class="btn btn-dreamer" title="{{trans('main.accesses')}}"--}}
{{--                    onclick="document.location='{{route('access.index_relit', $relit)}}'"--}}
{{--            >--}}
{{--                <i class="fas fa-universal-access"></i>--}}
{{--                {{trans('main.accesses')}}--}}
{{--            </button>--}}

{{--            <button type="button" class="btn btn-dreamer" title="{{trans('main.robas')}}"--}}
{{--                    onclick="document.location='{{route('roba.index_relit', $relit)}}'"--}}
{{--            >--}}
{{--                <i class="fas fa-ring"></i>--}}
{{--                {{trans('main.robas')}}--}}
{{--            </button>--}}

            <button type="button" class="btn btn-dreamer"
                    title="{{trans('main.cancel')}}" @include('layouts.relit.previous_url')>
                            <i class="fas fa-arrow-left"></i>
                {{trans('main.cancel')}}
            </button>
        </p>
    @elseif($type_form == 'delete_question')
        <form action="{{route('relit.delete', $relit)}}" method="POST" id='delete-form'>
            @csrf
            @method('DELETE')
            <p>
                <button type="submit" class="btn btn-danger" title="{{trans('main.delete')}}">
                                    <i class="fas fa-trash"></i>
                    {{trans('main.delete')}}
                </button>
                <button type="button" class="btn btn-dreamer"
                        title="{{trans('main.cancel')}}" @include('layouts.relit.previous_url')>
                                    <i class="fas fa-arrow-left"></i>
                    {{trans('main.cancel')}}
                </button>
            </p>
        </form>
    @endif

@endsection
