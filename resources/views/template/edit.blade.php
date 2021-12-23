@extends('layouts.app')

@section('content')
    <?php
    $update = isset($template);
    ?>
    <h3 class="display-5 text-center">
        @if (!$update)
            {{trans('main.new_record')}}
        @else
            {{trans('main.edit_record')}}
        @endif
        <span class="text-info">-</span> <span class="text-success">{{trans('main.template')}}</span>
    </h3>
    <br>

    <form action="{{$update ? route('template.update',$template):route('template.store')}}" method="POST"
          enctype=multipart/form-data>
        @csrf

        @if ($update)
            @method('PUT')
        @endif

        <div class="form-group row">
            @foreach (config('app.locales') as $key=>$value)
                <div class="col-3 text-right">
                    <label for="name_lang_{{$key}}" class="col-form-label">{{trans('main.name')}}
                        ({{trans('main.' . $value)}})<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-7">
                    <input type="text"
                           name="name_lang_{{$key}}"
                           id="name_lang_{{$key}}"
                           class="form-control @error('name_lang_' . $key) is-invalid @enderror"
                           placeholder=""
                           value="{{ old('name_lang_' . $key) ?? ($template['name_lang_' . $key] ?? '') }}">
                </div>
                @error('name_lang_' . $key)
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            @endforeach
        </div>

        <div class="form-group row" id="is_test_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_test">{{trans('main.is_test')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_test') is-invalid @enderror"
                       type="checkbox"
                       name="is_test"
                       id="linkis_test"
                       placeholder=""
                       {{--                       'false' - значение по умолчанию --}}
                       @if ((old('is_test') ?? ($template->is_test ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_test')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_closed_default_value_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_closed_default_value">{{trans('main.is_closed_default_value')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_closed_default_value') is-invalid @enderror"
                       type="checkbox"
                       name="is_closed_default_value"
                       id="linkis_closed_default_value"
                       placeholder=""
                       @if ((old('is_closed_default_value') ?? ($template->is_closed_default_value ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_closed_default_value')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_closed_default_value_fixed_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_closed_default_value_fixed">{{trans('main.is_closed_default_value_fixed')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_closed_default_value_fixed') is-invalid @enderror"
                       type="checkbox"
                       name="is_closed_default_value_fixed"
                       id="linkis_closed_default_value_fixed"
                       placeholder=""
                       @if ((old('is_closed_default_value_fixed') ?? ($template->is_closed_default_value_fixed ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_closed_default_value_fixed')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row">
            @foreach (config('app.locales') as $key=>$value)
                <div class="col-3 text-right">
                    <label for="desc_lang_{{$key}}" class="col-form-label">{{trans('main.desc')}}
                        ({{trans('main.' . $value)}})<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-7">
                    <textarea
                           name="desc_lang_{{$key}}"
                           id="desc_lang_{{$key}}"
                           rows="5"
                           class="form-control @error('desc_lang_' . $key) is-invalid @enderror"
                           placeholder="">
                        {{ old('desc_lang_' . $key) ?? ($template['desc_lang_' . $key] ?? '') }}
                        </textarea>
                  @error('desc_lang_' . $key)
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
                </div>
                <div class="col-2">
                </div>
            @endforeach
        </div>

        <br>
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-5 text-right">
                    <button type="submit" class="btn btn-dreamer"
                            @if (!$update)
{{--                            d-inline нужно, чтобы на маленьких экранах иконка и текст кнопки были на одной линии--}}
{{--                            этот вариант убирает иконку на мобильных телефонах: title="{{trans('main.add')}}"><i class="fas fa-save d-none d-sm-inline"></i>&nbsp;{{trans('main.add')}}--}}
                        title="{{trans('main.add')}}"><i class="fas fa-save d-inline"></i>&nbsp;{{trans('main.add')}}
                        @else
                            title="{{trans('main.save')}}"><i class="fas fa-save d-inline"></i>&nbsp;{{trans('main.save')}}
                        @endif
                    </button>
                </div>
                <div class="col-2">
                </div>
                <div class="col-5 text-left">
                    <button type="button" class="btn btn-dreamer" title="{{trans('main.cancel')}}"
                        @include('layouts.template.previous_url')
                    ><i class="fas fa-arrow-left d-inline"></i>&nbsp;{{trans('main.cancel')}}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
