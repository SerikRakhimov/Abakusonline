@extends('layouts.app')

@section('content')

    <?php
    use App\Http\Controllers\BaseController;
    use Illuminate\Support\Facades\Request;
    ?>
    <p>
        @include('layouts.form_show_title', ['type_form'=>$type_form, 'table_name'=>trans('main.user')])
    </p>

    <p>Id: <b>{{$user->id}}</b></p>

    <p>{{trans('main.name')}}: <b>{{$user->name}}</b></p>
    <p>{{trans('main.e-mail')}}: <b>{{$user->email}}</b></p>

    @if ($type_form == 'show')
        <p>
{{--            Не удалять--}}
            @if(1==2)
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                        onclick="document.location='{{route('user.edit', $user)}}'" title="{{trans('main.edit')}}">
                    <i class="fas fa-edit"></i>
                    {{trans('main.edit')}}
                </button>
            @endif
            <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                    onclick="document.location='{{route('user.change_password', $user)}}'"
                    title="{{trans('main.change_password')}}">
                <i class="fas fa-key"></i>
                {{trans('main.change_password')}}
            </button>
            @if($is_delete)
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                        onclick="document.location='{{route('user.delete_question',$user)}}'"
                        title="{{trans('main.delete')}}">
                    <i class="fas fa-trash"></i>
                    {{trans('main.delete')}}
                </button>
            @endif
        </p>
        <p>
            <button type="button" class="btn btn-dreamer mb-1 mb-sm-0" title="{{trans('main.projects')}}"
                    onclick="document.location='{{route('project.index_user', $user)}}'">
                <i class="fas fa-cube"></i>
                {{trans('main.projects')}}
            </button>
            <button type="button" class="btn btn-dreamer" title="{{trans('main.accesses')}}"
                    onclick="document.location='{{route('access.index_user', $user)}}'"
            >
                <i class="fas fa-universal-access"></i>
                {{trans('main.accesses')}}
            </button>
            <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                    title="{{trans('main.cancel')}}" @include('layouts.user.previous_url')>
                <i class="fas fa-arrow-left"></i>
                {{trans('main.cancel')}}
            </button>
        </p>
    @elseif($type_form == 'delete_question')
        <form action="{{route('user.delete', $user)}}" method="POST" id='delete-form'>
            @csrf
            @method('DELETE')
            <p>
                <button type="submit" class="btn btn-danger" title="{{trans('main.delete')}}">
                    <i class="fas fa-trash"></i>
                    {{trans('main.delete')}}
                </button>
                <button type="button" class="btn btn-dreamer"
                        title="{{trans('main.cancel')}}" @include('layouts.user.previous_url')>
                    <i class="fas fa-arrow-left"></i>
                    {{trans('main.cancel')}}
                </button>
            </p>
        </form>
    @endif

@endsection
