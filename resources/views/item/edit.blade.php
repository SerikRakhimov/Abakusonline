@extends('layouts.app')

@section('content')
<?php
$update = isset($item);
?>
<h3 class="display-5">
    @if (!$update)
        {{trans('main.new_record')}}
    @else
        {{trans('main.edit_record')}}
    @endif
    <span class="text-info">-</span> <span class="text-success">{{trans('main.item')}}</span>
</h3>
<br>

<form action ="{{$update ? route('item.update',$item):route('item.store')}}" method="POST" enctype=multipart/form-data>
    @csrf

    @if ($update)
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="base_id">{{trans('main.base')}}<span class="text-danger">*</span></label>
        <select class="form-control"
                name="base_id"
                id="base_id"
                class="form-control @error('base_id') is-invalid @enderror">
            @foreach ($bases as $base)
                <option value="{{$base->id}}"
                        {{--            "(int) 0" нужно--}}
                        @if ((old('base_id') ?? ($item->base_id ?? (int) 0)) ==  $base->id)
                        selected
                    @endif
                >{{$base->info()}}</option>
            @endforeach
        </select>
        @error('base_id')
        <div class="text-danger">
            {{$message}}
        </div>
        @enderror
    </div>

    @foreach (config('app.locales') as $key=>$value)
        <div class="form-group">
            <label for="name_lang_{{$key}}">{{trans('main.name')}} ({{trans('main.' . $value)}})<span class="text-danger">*</span></label>
            <input type="text"
                   name="name_lang_{{$key}}"
                   id="name_lang_{{$key}}"
                   class="form-control @error('name_lang_{{$key}}') is-invalid @enderror"
                   placeholder=""
                   value="{{ old('name_lang_' . $key) ?? ($item['name_lang_' . $key] ?? '') }}">
            @error('name_lang_{{$key}}')
            <div class="invalid-feedback">
                {{$message}}
            </div>
            @enderror
        </div>
    @endforeach

    <button type = "submit" class="btn btn-primary">
        @if (!$update)
            {{trans('main.add')}}
        @else
            {{trans('main.save')}}
        @endif
    </button>
    <a class="btn btn-success" href="{{session('links')}}">{{trans('main.cancel')}}</a>
    </form>

@endsection
