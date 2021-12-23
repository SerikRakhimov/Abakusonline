@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Link;
    use \App\Http\Controllers\LinkController;
    ?>
    <h3 class="display-5">
        @if ($type_form == 'show')
            {{trans('main.viewing_record')}}
        @elseif($type_form == 'delete_question')
            {{trans('main.delete_record_question')}}?
        @endif
        <span class="text-info">-</span> <span class="text-success">{{trans('main.link')}}</span>
    </h3>
    <br>

    <p>Id: <b>{{$link->id}}</b></p>
    <p>{{trans('main.child')}}_{{trans('main.base')}}: <b>{{$link->child_base->name()}}</b>
    <div><b>{{$link->child_base->info_full()}}</b></div></p>

    @foreach (config('app.locales') as $key=>$value)
        <p>{{trans('main.child_label')}} ({{trans('main.' . $value)}}): <b>{{$link['child_label_lang_' . $key]}}</b></p>
    @endforeach

    @foreach (config('app.locales') as $key=>$value)
        <p>{{trans('main.child_labels')}} ({{trans('main.' . $value)}}): <b>{{$link['child_labels_lang_' . $key]}}</b>
        </p>
    @endforeach

    <p>{{trans('main.serial_number')}}: <b>{{$link->parent_base_number}}</b></p>

    <p>{{trans('main.parent')}}_{{trans('main.base')}}: <b>{{$link->parent_base->name()}}</b>
    <div><b>{{$link->parent_base->info_full()}}</b></div></p>

    @foreach (config('app.locales') as $key=>$value)
        <p>{{trans('main.parent_label')}} ({{trans('main.' . $value)}}): <b>{{$link['parent_label_lang_' . $key]}}</b>
        </p>
    @endforeach

    <p>{{trans('main.date_created')}}: <b>{{$link->created_at}}</b></p>
    <p>{{trans('main.date_updated')}}: <b>{{$link->updated_at}}</b></p>

    @if ($type_form == 'show')
        <div class="mb-3 btn-group btn-group-sm">
            <a class="btn btn-primary" href="{{ route('link.base_index',
 ['base' => $link->child_base, 'links' => Link::where('child_base_id', $link->child_base_id)->orderBy('parent_base_number')->get()]) }}">
                {{trans('main.return')}}</a>
        </div>
    @elseif($type_form == 'delete_question')
        <form action="{{route('link.delete', $link)}}" method="POST" id='delete-form'>
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-primary">{{trans('main.delete')}}</button>
            <a class="btn btn-success" href="{{ route('link.base_index',
 ['base' => $link->child_base, 'links' => Link::where('child_base_id', $link->child_base_id)->orderBy('parent_base_number')->get()]) }}">
                {{trans('main.cancel')}}</a>
        </form>
    @endif

@endsection
