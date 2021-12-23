@extends('layouts.app')

@section('content')
    <h3 class="display-5">
        @if ($type_form == 'show')
            {{trans('main.viewing_record')}}
        @elseif($type_form == 'delete_question')
            {{trans('main.delete_record_question')}}?
        @endif
        <span class="text-info">-</span> <span class="text-success">{{trans('main.main')}}</span>
    </h3>
    <br>

    <p>Id: <b>{{$main->id}}</b></p>
    <p>{{trans('main.link')}}: <b>{{$main->link->info()}}</b>
        <div><b>{{$main->link->info_full()}}</b></div></p>
    <p>{{trans('main.child')}}_{{trans('main.item')}}: <b>{{$main->child_item->name()}}</b></p>
        <div><b>{{$main->child_item->info_full()}}</b></div></p>
    <p>{{trans('main.parent')}}_{{trans('main.item')}}: <b>{{$main->parent_item->name()}}</b>
        <div><b>{{$main->parent_item->info_full()}}</b></div></p>
    <p>{{trans('main.parent_label')}}: <b>{{$main->link->parent_label()}}</b></p>
    <p>{{trans('main.date_created')}}: <b>{{$main->created_at}}</b></p>
    <p>{{trans('main.date_updated')}}: <b>{{$main->updated_at}}</b></p>

    @if ($type_form == 'show')
        <div class="mb-3 btn-group btn-group-sm">
            <a class="btn btn-primary" href="{{ route('main.index') }}">{{trans('main.return')}}</a>
        </div>
    @elseif($type_form == 'delete_question')
        <form action="{{route('main.delete', $main)}}" method="POST" id='delete-form'>
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-primary">{{trans('main.delete')}}</button>
            <a class="btn btn-success" href="{{ route('main.index') }}">{{trans('main.cancel')}}</a>
        </form>
    @endif

@endsection
