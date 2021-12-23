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
        <span class="text-info">-</span> <span class="text-success">{{$base->info()}} ({{trans('main.item')}})</span>
    </h3>
    <br>
{{--    https://qastack.ru/programming/1191113/how-to-ensure-a-select-form-field-is-submitted-when-it-is-disabled--}}
    <form action="{{$update ? route('item.update', $item):route('item.store', $base)}}" method="POST"
          enctype=multipart/form-data onsubmit="document.getElementById('{{$par_link->id}}').disabled = false;">
        @csrf

        @if ($update)
            @method('PUT')
        @endif

        @foreach (config('app.locales') as $key=>$value)
            <div class="form-group">
                <label for="name_lang_{{$key}}">{{trans('main.name')}} ({{trans('main.' . $value)}})<span
                        class="text-danger">*</span></label>
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
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Library</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data</li>
            </ol>
        </nav>
        @foreach($array_plan as $key=>$value)
            <?php
            $result = ItemController::get_items_for_link(Link::find($key));
            $items = $result['result_parent_base_items'];
            ?>
            <div class="form-group">
                <label for="{{$key}}">{{$result['result_parent_label']}} ({{$result['result_parent_base_name']}})
                    <span class="text-danger">*{{$value !=null ? "" : "~"}}</span></label>
                <select class="form-control"
                        name="{{$key}}"
                        id="{{$key}}"
                        class="form-control @error('{{$key}}') is-invalid @enderror"
                        @if ($key == $par_link->id)
                        disabled
                    @endif
                >
                    @if (count($items) == 0)
                        <option value='0'>{{trans('main.no_information_on')}} "{{$result['result_parent_base_name']}}"!</option>
                    @else
                        @foreach ($items as $item1)
                            <option value="{{$item1->id}}"
                                    @if ($value == $item1->id)
                                    selected
                                @endif
                            >{{$item1->info()}}</option>
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

        <button type="submit" class="btn btn-primary">
            @if (!$update)
                {{trans('main.add')}}
            @else
                {{trans('main.save')}}
            @endif
        </button>
        <a class="btn btn-success" href="{{session('links')}}">{{trans('main.cancel')}}</a>
    </form>

@endsection
