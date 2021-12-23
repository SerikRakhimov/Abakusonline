@extends('layouts.app')

@section('content')
<?php
use App\Models\Link;
use \App\Http\Controllers\ItemController;
$update = isset($item);
?>
<h3 class="display-5">
    @if (!$update)
        {{trans('main.new_record')}}
    @else
        {{trans('main.edit_record')}}
    @endif
    <span class="text-info">-</span> <span class="text-success">{{$item->name()}} ({{$item->base->name()}})</span>
</h3>
<br>

<form action ="{{$update ? route('item.extended_update',$item):route('item.extended_update',$item)}}" method="POST" enctype=multipart/form-data>
    @csrf

    @if ($update)
        @method('PUT')
    @endif

    @foreach($array_plan as $key=>$value)
        <?php
            $result = ItemController::get_items_for_link( Link::find($key));
            $items = $result['result_parent_base_items'];
        ?>
        <div class="form-group">
            <label for="{{$key}}">{{$result['result_parent_label']}} ({{$result['result_parent_base_name']}})
                <span class="text-danger">*{{$value !=null ? "" : "~"}}</span></label>
            <select class="form-control"
                    name="{{$key}}"
                    id="{{$key}}"
                    class="form-control @error('{{$key}}') is-invalid @enderror">
                @if (count($items) == 0)
                    <option value = '0'>{{trans('main.no_information_on')}} "{{$result['result_parent_base_name']}}"!</option>
                @else
                @foreach ($items as $item)
                    <option value="{{$item->id}}"
                        @if ($value == $item->id)
                                    selected
                        @endif
                    >{{$item->info()}}</option>
                @endforeach
                @endif
            </select>
            @error('{{$key}}')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
            <div class="text-danger">
                {{-- session('errors') передается командой в контроллере "return redirect()->back()->withInput()->withErrors(...)"--}}
                {{session('errors')!=null ? session('errors')->first($key): ''}}
            </div>
        </div>

    @endforeach

    <button type = "submit" class="btn btn-primary">
        @if (!$update)
            {{trans('main.add')}}
        @else
            {{trans('main.save')}}
        @endif
    </button>
{{--    <a class="btn btn-success" href="{{ route('item.index') }}">{{trans('main.cancel')}}</a>--}}
    </form>

@endsection
