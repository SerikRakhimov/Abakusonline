@extends('layouts.app')

@section('content')

    <?php
    use App\Http\Controllers\ItemController;
    ?>

    <h3 class="display-5">
        @if ($type_form == 'show')
            {{trans('main.viewing_record')}}
        @elseif($type_form == 'delete_question')
            {{trans('main.delete_record_question')}}?
        @endif
        <span class="text-info">-</span> <span class="text-success">{{trans('main.item')}}</span>
    </h3>
    <br>

    <p>Id: <b>{{$item->id}}</b></p>
    <p>{{trans('main.base')}}: <b>{{$item->base->info()}}</b>
    <div><b>{{$item->base->info_full()}}</b></div></p>

    @foreach (config('app.locales') as $key=>$value)
        <p>{{trans('main.name')}} ({{trans('main.' . $value)}}): <b>{{$item['name_lang_' . $key]}}</b></p>
    @endforeach

    <p>{{trans('main.date_created')}}: <b>{{$item->created_at}}</b></p>
    <p>{{trans('main.date_updated')}}: <b>{{$item->updated_at}}</b></p>

    <!--    --><?php
    //    $result = ItemController::form_tree($item->id);
    //    echo $result;
    //    ?>

    @if ($type_form == 'show')
        <div class="mb-3 btn-group btn-group-sm">
            <a class="btn btn-primary" href="{{session('links')}}">{{trans('main.return')}}</a>
        </div>
    @elseif($type_form == 'delete_question')
        <form action="{{route('item.item_delete', ['parent_item'=>$parent_item, 'item'=>$item])}}" method="POST"
              id='delete-form'>
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-primary">{{trans('main.delete')}}</button>
            <a class="btn btn-success" href="{{session('links')}}">{{trans('main.cancel')}}</a>
        </form>
    @endif

@endsection
