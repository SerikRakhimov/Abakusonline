@extends('layouts.app')

@section('content')

    <?php
    use App\Http\Controllers\BaseController;
    use Illuminate\Support\Facades\Request;
    ?>
    <p>
        @include('layouts.form_show_title', ['type_form'=>$type_form, 'table_name'=>trans('main.item')])
    </p>

    <p>Id: <b>{{$item->id}}</b></p>
    <p>{{$item->base->name()}}:
        @include('view.img',['item'=>$item, 'size'=>"medium", 'filenametrue'=>true, 'link'=>true, 'img_fluid'=>false, 'title'=>""])

{{--        <a href="{{Storage::url($item->filename(true))}}">--}}
{{--            <img src="{{Storage::url($item->filename(true))}}" height="250"--}}
{{--                 alt="" title="{{$item->title_img()}}"></a>--}}
    </p>
    <p>{{trans('main.status')}}: <b>{{$item->status_img()}}</b></p>
    <p>{{trans('main.project')}}: <b>{{$item->project->name_id()}}</b></p>
    <p>{{trans('main.template')}}: <b>{{$item->project->template->name_id()}}</b></p>
    <p>{{trans('main.created_user_date_time')}}: <b>{{$item->created_user_date_time()}}</b></p>
    <p>{{trans('main.updated_user_date_time')}}: <b>{{$item->updated_user_date_time()}}</b></p>

    @if ($type_form == 'show')
        <p>
            @if(Auth::user()->isModerator() == true)
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                        onclick="document.location='{{route('moderation.edit',$item)}}'"
                        title="{{trans('main.edit')}}">
                    <i class="fas fa-edit"></i>
                    {{trans('main.edit')}}
                </button>
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                        onclick="document.location='{{route('moderation.delete_question',$item)}}'"
                        title="{{trans('main.delete')}}">
                    <i class="fas fa-trash"></i>
                    {{trans('main.delete')}}
                </button>
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                        title="{{trans('main.cancel')}}" @include('layouts.moderation.previous_url')>
                    <i class="fas fa-arrow-left"></i>
                    {{trans('main.cancel')}}
                </button>
            @endif
        </p>
    @elseif($type_form == 'delete_question')
        <form action="{{route('moderation.delete', $item)}}" method="POST" id='delete-form'>
            @csrf
            @method('DELETE')
            <p>
                <button type="submit" class="btn btn-danger" title="{{trans('main.delete')}}">
                    <i class="fas fa-trash"></i>
                    {{trans('main.delete')}}
                </button>
                <button type="button" class="btn btn-dreamer"
                        title="{{trans('main.cancel')}}" @include('layouts.moderation.previous_url')>
                    <i class="fas fa-arrow-left"></i>
                    {{trans('main.cancel')}}
                </button>
            </p>
        </form>
    @endif

@endsection
