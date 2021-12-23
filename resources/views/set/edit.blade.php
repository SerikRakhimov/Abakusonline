@extends('layouts.app')

@section('content')
    <?php
    $update = isset($set);
    ?>
    <p>
        @include('layouts.template.show_name', ['template'=>$template])
    </p>
    <p>
        @include('layouts.form_edit_title', ['update'=>$update, 'table_name'=>trans('main.set')])
    </p>
    <form action="{{$update ? route('set.update',$set):route('set.store')}}" method="POST"
          enctype=multipart/form-data>
        @csrf

        @if ($update)
            @method('PUT')
        @endif
        <input type="hidden" name="template_id" value="{{$template->id}}">

        <div class="form-group row" id="serial_number_form_group">
            <div class="col-sm-3 text-right">
                <label for="serial_number">{{trans('main.serial_number')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-2">
                <input type="number"
                       name="serial_number"
                       id="serial_number"
                       class="form-control @error('serial_number') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('serial_number') ?? ($set['serial_number'] ?? '0') }}">
                @error('serial_number')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-7">
            </div>
        </div>

        <div class="form-group row" id="line_number_form_group">
            <div class="col-sm-3 text-right">
                <label for="line_number">{{trans('main.line_number')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-2">
                <input type="number"
                       name="line_number"
                       id="line_number"
                       class="form-control @error('line_number') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('line_number') ?? ($set['line_number'] ?? '0') }}">
                @error('line_number')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-7">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-3 text-right">
                <label for="link_from_id" class="col-form-label">{{trans('main.link_from')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-7">
                <select class="form-control"
                        name="link_from_id"
                        id="link_from_id"
                        class="@error('link_from_id') is-invalid @enderror">
                    @foreach ($links as $link)
                        <option value="{{$link->id}}"
                                @if ($update)
                                @if ((old('link_from_id') ?? ($set->link_from_id ?? (int) 0)) ==  $link->id)
                                selected
                            @endif
                            @endif
                        >{{$link->name()}}</option>
                    @endforeach
                </select>
                @error('link_from_id')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-3 text-right">
                <label for="link_to_id" class="col-form-label">{{trans('main.link_to')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-7">
                <select class="form-control"
                        name="link_to_id"
                        id="link_to_id"
                        class="@error('link_to_id') is-invalid @enderror">
                    @foreach ($links as $link)
                        <option value="{{$link->id}}"
                                @if ($update)
                                @if ((old('link_to_id') ?? ($set->link_to_id ?? (int) 0)) ==  $link->id)
                                selected
                            @endif
                            @endif
                        >{{$link->name()}}</option>
                    @endforeach
                </select>
                @error('link_to_id')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-3 text-right">
                <label for="forwhat" class="col-form-label">{{trans('main.forwhat')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-7">
                <select class="form-control"
                        name="forwhat"
                        id="forwhat"
                        class="@error('forwhat') is-invalid @enderror">
                    @foreach ($forwhats as $key=>$value)
                        <option value="{{$key}}"
                                @if ($update)
                                {{--            "(int) 0" нужно--}}
                                @if ((old('forwhat') ?? ($key ?? (int) 0)) ==  $set->forwhat())
                                selected
                            @endif
                            @endif
                        >{{$value}}</option>
                    @endforeach
                </select>
                @error('forwhat')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_savesets_enabled_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_savesets_enabled">{{trans('main.is_savesets_enabled')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_savesets_enabled') is-invalid @enderror"
                       type="checkbox"
                       name="is_savesets_enabled"
                       placeholder=""
                       @if ((old('is_savesets_enabled') ?? ($set->is_savesets_enabled ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_savesets_enabled')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="updaction_form_group">
            <div class="col-sm-3 text-right">
                <label for="updaction" class="col-form-label">{{trans('main.updaction')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-7">
                <select class="form-control"
                        name="updaction"
                        id="updaction"
                        class="@error('updaction') is-invalid @enderror">
                    @foreach ($updactions as $key=>$value)
                        <option value="{{$key}}"
                                @if ($update)
                                {{--            "(int) 0" нужно--}}
                                @if ((old('updaction') ?? ($key ?? (int) 0)) ==  $set->updaction())
                                selected
                            @endif
                            @endif
                        >{{$value}}</option>
                    @endforeach
                </select>
                @error('updaction')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_upd_delete_record_with_zero_value_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_upd_delete_record_with_zero_value">{{trans('main.is_upd_delete_record_with_zero_value')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_upd_delete_record_with_zero_value') is-invalid @enderror"
                       type="checkbox"
                       name="is_upd_delete_record_with_zero_value"
                       placeholder=""
                       @if ((old('is_upd_delete_record_with_zero_value') ?? ($set->is_upd_delete_record_with_zero_value ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_upd_delete_record_with_zero_value')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <br>
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-5 text-right">
                    <button type="submit" class="btn btn-dreamer"
                            @if (!$update)
                            title="{{trans('main.add')}}">
                                            <i class="fas fa-save d-inline"></i>
                        {{trans('main.add')}}
                        @else
                            title="{{trans('main.save')}}">
                                                    <i class="fas fa-save d-inline"></i>
                            {{trans('main.save')}}
                        @endif
                    </button>
                </div>
                <div class="col-2">
                </div>
                <div class="col-5 text-left">
                    <button type="button" class="btn btn-dreamer" title="{{trans('main.cancel')}}"
                        @include('layouts.set.previous_url')
                    >
                                            <i class="fas fa-arrow-left d-inline"></i>
                        {{trans('main.cancel')}}
                    </button>
                </div>
            </div>
        </div>
    </form>
    <script>
        var forwhat = document.getElementById('forwhat');
        var updaction_fg = document.getElementById('updaction_form_group');
        var upd_delwithzero_fg = document.getElementById('is_upd_delete_record_with_zero_value_form_group');
        var savesets_fg = document.getElementById('is_savesets_enabled_form_group');
        var forwhat_value = null;

        function forwhat_changeOption(first) {
            // если запуск функции не при загрузке страницы
            if (first != true) {
                // сохранить текущие значения
                forwhat_value = forwhat.options[forwhat.selectedIndex].value;
            }

            val_updaction = "hidden";
            val_savesets = "hidden";

            switch (forwhat.options[forwhat.selectedIndex].value) {
                // Группировка
                 case "0":
                     val_savesets = "visible";
                 break;
                // Поля сортировки (для первый(), последний())
                case "1":
                    val_savesets = "visible";
                    break;
                // Только связь (для вывода поля из вычисляемой Основы)
                //case "2":
                //    break;
                // Обновление
                case "3":
                    val_savesets = "visible";
                    val_updaction = "visible";
                    break;
            }
            updaction_fg.style.visibility = val_updaction;
            upd_delwithzero_fg.style.visibility = val_updaction;
            savesets_fg.style.visibility = val_savesets;

        }

        forwhat.addEventListener("change", forwhat_changeOption);

        window.onload = function () {
            forwhat_changeOption(true);
        };

    </script>
@endsection
