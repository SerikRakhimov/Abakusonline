@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Link;
    use \App\Http\Controllers\GlobalController;
    $update = isset($link);
    ?>
    <h3 class="display-5">
        @if (!$update)
            {{trans('main.new_record')}}
        @else
            {{trans('main.edit_record')}}
        @endif
        <span class="text-info">-</span> <span class="text-success">{{trans('main.link')}}</span>
    </h3>
    <br>

    <form action="{{$update ? route('link.update',$link):route('link.store')}}" method="POST"
          enctype=multipart/form-data
          onsubmit="document.getElementById('parent_base_id').disabled = false;"
          name="form">
        @csrf

        @if ($update)
            @method('PUT')
        @endif

        <div class="form-group">
            <label for="child_base_id">{{trans('main.child')}}_{{trans('main.base')}}<span class="text-danger">*</span></label>
            <select class="form-control"
                    name="child_base_id"
                    id="child_base_id"
                    class="form-control @error('child_base_id') is-invalid @enderror">
                @foreach ($bases as $base)
                    <option value="{{$base->id}}"
                            {{--            "(int) 0" нужно--}}
                            @if ((old('child_base_id') ?? ($link->child_base_id ?? (int) 0)) ==  $base->id)
                            selected
                        @endif
                    >{{$base->info()}}</option>
                @endforeach
            </select>
            @error('child_base_id')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>

        @foreach (config('app.locales') as $key=>$value)
            <div class="form-group">
                <label for="child_label_lang_{{$key}}">{{trans('main.child_label')}} ({{trans('main.' . $value)}})<span
                        class="text-danger">*</span></label>
                <input type="text"
                       name="child_label_lang_{{$key}}"
                       id="child_label_lang_{{$key}}"
                       class="form-control @error('child_label_lang_{{$key}}') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('child_label_lang_' . $key) ?? ($link['child_label_lang_' . $key] ?? '') }}">
                @error('child_label_lang_{{$key}}')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
        @endforeach

        @foreach (config('app.locales') as $key=>$value)
            <div class="form-group">
                <label for="child_labels_lang_{{$key}}">{{trans('main.child_labels')}} ({{trans('main.' . $value)}})
                    <span class="text-danger">*</span></label>
                <input type="text"
                       name="child_labels_lang_{{$key}}"
                       id="child_labels_lang_{{$key}}"
                       class="form-control @error('child_labels_lang_{{$key}}') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('child_labels_lang_' . $key) ?? ($link['child_labels_lang_' . $key] ?? '') }}">
                @error('child_labels_lang_{{$key}}')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
        @endforeach

        <div class="form-group">
            <label for="parent_base_number">{{trans('main.parent')}}_{{trans('main.serial_number')}}<span
                    class="text-danger">*</span></label>
            <input type="number"
                   name="parent_base_number"
                   id="parent_base_number"
                   class="form-control @error('parent_base_number') is-invalid @enderror"
                   placeholder=""
                   value="{{ old('parent_base_number') ?? ($link['parent_base_number'] ?? '0') }}">
            @error('parent_base_number')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="parent_base_id">{{trans('main.parent')}}_{{trans('main.base')}}<span
                    class="text-danger">*</span></label>
            <select class="form-control"
                    name="parent_base_id"
                    id="parent_base_id"
                    class="form-control @error('parent_base_id') is-invalid @enderror">
                @foreach ($bases as $base)
                    <option value="{{$base->id}}"
                            {{--            "(int) 0" нужно--}}
                            @if ((old('parent_base_id') ?? ($link->parent_base_id ?? (int) 0)) ==  $base->id)
                            selected
                        @endif
                    >{{$base->info()}}</option>
                @endforeach
            </select>
            @error('parent_base_id')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="parent_num_bool_default_value">{{trans('main.parent')}}
                _{{trans('main.parent_num_bool_default_value')}}<span
                    class="text-danger">*</span></label>
            <input type="text"
                   name="parent_num_bool_default_value"
                   id="parent_num_bool_default_value"
                   class="form-control @error('parent_num_bool_default_value') is-invalid @enderror"
                   placeholder=""
                   value="{{ old('parent_num_bool_default_value') ?? ($link['parent_num_bool_default_value'] ?? '') }}">
            @error('parent_num_bool_default_value')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="parent_level_id_0">{{trans('main.parent')}}_{{trans('main.level')}}_0<span
                    class="text-danger">*</span></label>
            <select class="form-control"
                    name="parent_level_id_0"
                    id="parent_level_id_0"
                    class="form-control @error('parent_level_id_0') is-invalid @enderror">
                <option value="0">{{GlobalController::option_empty()}}</option>
                @foreach ($levels as $level)
                    <option value="{{$level->id}}"
                            {{--            "(int) 0" нужно--}}
                            @if ((old('parent_level_id_0') ?? ($link->parent_level_id_0 ?? (int) 0)) ==  $level->id)
                            selected
                        @endif
                    >{{$level->name()}}</option>
                @endforeach
            </select>
            @error('parent_level_id_0')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="parent_level_id_1">{{trans('main.parent')}}_{{trans('main.level')}}_1<span
                    class="text-danger">*</span></label>
            <select class="form-control"
                    name="parent_level_id_1"
                    id="parent_level_id_1"
                    class="form-control @error('parent_level_id_1') is-invalid @enderror">
                <option value="0">{{GlobalController::option_empty()}}</option>
                @foreach ($levels as $level)
                    <option value="{{$level->id}}"
                            {{--            "(int) 0" нужно--}}
                            @if ((old('parent_level_id_1') ?? ($link->parent_level_id_1 ?? (int) 0)) ==  $level->id)
                            selected
                        @endif
                    >{{$level->name()}}</option>
                @endforeach
            </select>
            @error('parent_level_id_1')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="parent_level_id_2">{{trans('main.parent')}}_{{trans('main.level')}}_2<span
                    class="text-danger">*</span></label>
            <select class="form-control"
                    name="parent_level_id_2"
                    id="parent_level_id_2"
                    class="form-control @error('parent_level_id_2') is-invalid @enderror">
                <option value="0">{{GlobalController::option_empty()}}</option>
                @foreach ($levels as $level)
                    <option value="{{$level->id}}"
                            {{--            "(int) 0" нужно--}}
                            @if ((old('parent_level_id_2') ?? ($link->parent_level_id_2 ?? (int) 0)) ==  $level->id)
                            selected
                        @endif
                    >{{$level->name()}}</option>
                @endforeach
            </select>
            @error('parent_level_id_2')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="parent_level_id_3">{{trans('main.parent')}}_{{trans('main.level')}}_3<span
                    class="text-danger">*</span></label>
            <select class="form-control"
                    name="parent_level_id_3"
                    id="parent_level_id_3"
                    class="form-control @error('parent_level_id_3') is-invalid @enderror">
                <option value="0">{{GlobalController::option_empty()}}</option>
                @foreach ($levels as $level)
                    <option value="{{$level->id}}"
                            {{--            "(int) 0" нужно--}}
                            @if ((old('parent_level_id_3') ?? ($link->parent_level_id_3 ?? (int) 0)) ==  $level->id)
                            selected
                        @endif
                    >{{$level->name()}}</option>
                @endforeach
            </select>
            @error('parent_level_id_3')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>

        @foreach (config('app.locales') as $key=>$value)
            <div class="form-group">
                <label for="parent_label_lang_{{$key}}">{{trans('main.parent_label')}} ({{trans('main.' . $value)}}
                    )<span class="text-danger">*</span></label>
                <input type="text"
                       name="parent_label_lang_{{$key}}"
                       id="parent_label_lang_{{$key}}"
                       class="form-control @error('parent_label_lang_{{$key}}') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('parent_label_lang_' . $key) ?? ($link['parent_label_lang_' . $key] ?? '') }}">
                @error('parent_label_lang_{{$key}}')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
        @endforeach

        @foreach (config('app.locales') as $key=>$value)
            <div class="form-group">
                <label for="parent_calcname_prefix_lang_{{$key}}">{{trans('main.parent_calcname_prefix')}}
                    ({{trans('main.' . $value)}}
                    )<span class="text-danger">*</span></label>
                <input type="text"
                       name="parent_calcname_prefix_lang_{{$key}}"
                       id="parent_calcname_prefix_lang_{{$key}}"
                       class="form-control @error('parent_calcname_prefix_lang_{{$key}}') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('parent_calcname_prefix_lang_' . $key) ?? ($link['parent_calcname_prefix_lang_' . $key] ?? '') }}">
                @error('parent_calcname_prefix_lang_{{$key}}')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
        @endforeach

        <div class="form-group" id="parent_is_base_link_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_base_link"
                       id="parent_is_base_link"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_base_link') ?? ($link->parent_is_base_link ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label"
                       for="parent_is_base_link">{{trans('main.parent_is_base_link')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_is_enter_refer_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_enter_refer"
                       id="parent_is_enter_refer"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_enter_refer') ?? ($link->parent_is_enter_refer ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label"
                       for="parent_is_enter_refer">{{trans('main.parent_is_enter_refer')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_is_numcalc_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_numcalc"
                       id="parent_is_numcalc"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_numcalc') ?? ($link->parent_is_numcalc ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label" for="parent_is_numcalc">{{trans('main.parent_is_numcalc')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_is_nc_screencalc_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_nc_screencalc"
                       id="parent_is_nc_screencalc"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_nc_screencalc') ?? ($link->parent_is_nc_screencalc ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label"
                       for="parent_is_nc_screencalc">{{trans('main.parent_is_nc_screencalc')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_is_nc_viewonly_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_nc_viewonly"
                       id="parent_is_nc_viewonly"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_nc_viewonly') ?? ($link->parent_is_nc_viewonly ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label"
                       for="parent_is_nc_viewonly">{{trans('main.parent_is_nc_viewonly')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_is_nc_parameter_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_nc_parameter"
                       id="parent_is_nc_parameter"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_nc_parameter') ?? ($link->parent_is_nc_parameter ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label"
                       for="parent_is_nc_parameter">{{trans('main.parent_is_nc_parameter')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_is_calcname_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_calcname"
                       id="parent_is_calcname"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_calcname') ?? ($link->parent_is_calcname ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label" for="parent_is_calcname">{{trans('main.parent_is_calcname')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_is_left_calcname_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_left_calcname"
                       id="parent_is_left_calcname"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_left_calcname') ?? ($link->parent_is_left_calcname ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label"
                       for="parent_is_left_calcname">{{trans('main.parent_is_left_calcname')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_is_small_calcname_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_small_calcname"
                       id="parent_is_small_calcname"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_small_calcname') ?? ($link->parent_is_small_calcname ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label"
                       for="parent_is_small_calcname">{{trans('main.parent_is_small_calcname')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_is_hidden_field_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_hidden_field"
                       id="parent_is_hidden_field"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_hidden_field') ?? ($link->parent_is_hidden_field ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label"
                       for="parent_is_hidden_field">{{trans('main.parent_is_hidden_field')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_is_primary_image_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_primary_image"
                       id="parent_is_primary_image"
                       {{--            "false" - значение по умолчанию --}}
                       @if ((old('parent_is_primary_image') ?? ($link->parent_is_primary_image ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label"
                       for="parent_is_primary_image">{{trans('main.parent_is_primary_image')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_is_delete_child_base_record_with_zero_value_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_delete_child_base_record_with_zero_value"
                       id="parent_is_delete_child_base_record_with_zero_value"
                       {{--            "false" - значение по умолчанию --}}
                       @if ((old('parent_is_delete_child_base_record_with_zero_value') ?? ($link->parent_is_delete_child_base_record_with_zero_value ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label"
                       for="parent_is_delete_child_base_record_with_zero_value">{{trans('main.parent_is_delete_child_base_record_with_zero_value')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_is_setup_project_logo_img_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_setup_project_logo_img"
                       id="parent_is_setup_project_logo_img"
                       {{--            "false" - значение по умолчанию --}}
                       @if ((old('parent_is_setup_project_logo_img') ?? ($link->parent_is_setup_project_logo_img ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label"
                       for="parent_is_setup_project_logo_img">{{trans('main.parent_is_setup_project_logo_img')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_is_setup_project_external_description_txt_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_setup_project_external_description_txt"
                       id="parent_is_setup_project_external_description_txt"
                       {{--            "false" - значение по умолчанию --}}
                       @if ((old('parent_is_setup_project_external_description_txt') ?? ($link->parent_is_setup_project_external_description_txt ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label"
                       for="parent_is_setup_project_external_description_txt">{{trans('main.parent_is_setup_project_external_description_txt')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_is_setup_project_internal_description_txt_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_setup_project_internal_description_txt"
                       id="parent_is_setup_project_internal_description_txt"
                       {{--            "false" - значение по умолчанию --}}
                       @if ((old('parent_is_setup_project_internal_description_txt') ?? ($link->parent_is_setup_project_internal_description_txt ?? false)) ==  true)
                       checked
                    @endif
                >
                <label class="form-check-label"
                       for="parent_is_setup_project_internal_description_txt">{{trans('main.parent_is_setup_project_internal_description_txt')}}</label>
            </div>
        </div>
        {{--        1.0 В списке выбора использовать поле вычисляемой таблицы--}}
        <div class="form-group" id="parent_is_in_the_selection_list_use_the_calculated_table_field_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"
                       name="parent_is_in_the_selection_list_use_the_calculated_table_field"
                       id="parent_is_in_the_selection_list_use_the_calculated_table_field"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_in_the_selection_list_use_the_calculated_table_field') ?? ($link->parent_is_in_the_selection_list_use_the_calculated_table_field ?? false)) ==  true)
                       checked
                       @endif
                       onclick="parent_selection_calculated_table_show_or_hide(this)">
                <label class="form-check-label"
                       for="parent_is_in_the_selection_list_use_the_calculated_table_field">{{trans('main.parent_is_in_the_selection_list_use_the_calculated_table_field')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_selection_calculated_table_set_id_form_group">
            <label
                for="parent_selection_calculated_table_set_id">{{trans('main.parent_selection_calculated_table_set_id')}}
                <span
                    class="text-danger">*</span></label>
            <select class="form-control"
                    name="parent_selection_calculated_table_set_id"
                    id="parent_selection_calculated_table_set_id"
                    class="form-control @error('parent_selection_calculated_table_set_id') is-invalid @enderror">
                <option value="0">0</option>
            </select>
            @error('parent_selection_calculated_table_set_id')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>
        {{--        1.1 В списке выбора использовать дополнительное связанное поле вычисляемой таблицы--}}
        <div class="form-group" id="parent_is_use_selection_calculated_table_link_id_0_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"
                       name="parent_is_use_selection_calculated_table_link_id_0"
                       id="parent_is_use_selection_calculated_table_link_id_0"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_use_selection_calculated_table_link_id_0') ?? ($link->parent_is_use_selection_calculated_table_link_id_0 ?? false)) ==  true)
                       checked
                       @endif
                       onclick="parent_selection_calculated_table_link_id_0_show_or_hide(this)">
                <label class="form-check-label"
                       for="parent_is_use_selection_calculated_table_link_id_0">{{trans('main.parent_is_use_selection_calculated_table_link_id_0')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_selection_calculated_table_link_id_0_form_group">
            <label
                for="parent_selection_calculated_table_link_id_0">{{trans('main.parent_selection_calculated_table_link_id_0')}}
                <span
                    class="text-danger">*</span></label>
            <select class="form-control"
                    name="parent_selection_calculated_table_link_id_0"
                    id="parent_selection_calculated_table_link_id_0"
                    class="form-control @error('parent_selection_calculated_table_link_id_0') is-invalid @enderror"
                    onchange="parent_selection_calculated_table_link_id_0_changeOption(false)">
                <option value="0">0</option>
            </select>
            @error('parent_selection_calculated_table_link_id_0')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>
        {{--        1.2 В списке выбора использовать два дополнительных связанных поля вычисляемой таблицы--}}
        <div class="form-group" id="parent_is_use_selection_calculated_table_link_id_1_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"
                       name="parent_is_use_selection_calculated_table_link_id_1"
                       id="parent_is_use_selection_calculated_table_link_id_1"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_use_selection_calculated_table_link_id_1') ?? ($link->parent_is_use_selection_calculated_table_link_id_1 ?? false)) ==  true)
                       checked
                       @endif
                       onclick="parent_selection_calculated_table_link_id_1_show_or_hide(this)">
                <label class="form-check-label"
                       for="parent_is_use_selection_calculated_table_link_id_1">{{trans('main.parent_is_use_selection_calculated_table_link_id_1')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_selection_calculated_table_link_id_1_form_group">
            <label
                for="parent_selection_calculated_table_link_id_1">{{trans('main.parent_selection_calculated_table_link_id_1')}}
                <span
                    class="text-danger">*</span></label>
            <select class="form-control"
                    name="parent_selection_calculated_table_link_id_1"
                    id="parent_selection_calculated_table_link_id_1"
                    class="form-control @error('parent_selection_calculated_table_link_id_1') is-invalid @enderror"
                    onchange="parent_selection_calculated_table_link_id_1_changeOption(false)">
                <option value="0">0</option>
            </select>
            @error('parent_selection_calculated_table_link_id_1')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>
        {{--       Выводить связанное поле--}}
        <div class="form-group" id="parent_is_parent_related_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_parent_related"
                       id="parent_is_parent_related"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_parent_related') ?? ($link->parent_is_parent_related ?? false)) ==  true)
                       checked
                       @endif
                       onclick="parent_parent_show_or_hide(this)">
                <label class="form-check-label"
                       for="parent_is_parent_related">{{trans('main.parent_is_parent_related')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_parent_related_start_link_id_form_group">
            <label for="parent_parent_related_start_link_id">{{trans('main.parent_parent_related_start_link_id')}}<span
                    class="text-danger">*</span></label>
            <select class="form-control"
                    name="parent_parent_related_start_link_id"
                    id="parent_parent_related_start_link_id"
                    class="form-control @error('parent_parent_related_start_link_id') is-invalid @enderror">
                <option value="0">0</option>
            </select>
            @error('parent_parent_related_start_link_id')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>

        <div class="form-group" id="parent_parent_related_result_link_id_form_group">
            <label for="parent_parent_related_result_link_id">{{trans('main.parent_parent_related_result_link_id')}}
                <span
                    class="text-danger">*</span></label>
            <select class="form-control"
                    name="parent_parent_related_result_link_id"
                    id="parent_parent_related_result_link_id"
                    class="form-control @error('parent_parent_related_result_link_id') is-invalid @enderror">
                <option value="0">0</option>
            </select>
            @error('parent_parent_related_result_link_id')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>
        {{--        Выводить поле вычисляемой таблицы--}}
        <div class="form-group" id="parent_is_output_calculated_table_field_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_output_calculated_table_field"
                       id="parent_is_output_calculated_table_field"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_output_calculated_table_field') ?? ($link->parent_is_output_calculated_table_field ?? false)) ==  true)
                       checked
                       @endif
                       onclick="parent_calculated_table_show_or_hide(this)">
                <label class="form-check-label"
                       for="parent_is_output_calculated_table_field">{{trans('main.parent_is_output_calculated_table_field')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_output_calculated_table_set_id_form_group">
            <label for="parent_output_calculated_table_set_id">{{trans('main.parent_output_calculated_table_set_id')}}
                <span
                    class="text-danger">*</span></label>
            <select class="form-control"
                    name="parent_output_calculated_table_set_id"
                    id="parent_output_calculated_table_set_id"
                    class="form-control @error('parent_output_calculated_table_set_id') is-invalid @enderror">
                <option value="0">0</option>
            </select>
            @error('parent_output_calculated_table_set_id')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>
        {{--        Фильтровать поля--}}
        <div class="form-group" id="parent_is_child_related_form_group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="parent_is_child_related"
                       id="parent_is_child_related"
                       {{--            "(int) 0" нужно--}}
                       @if ((old('parent_is_child_related') ?? ($link->parent_is_child_related ?? false)) ==  true)
                       checked
                       @endif
                       onclick="parent_child_show_or_hide(this)">
                <label class="form-check-label"
                       for="parent_is_child_related">{{trans('main.parent_is_child_related')}}</label>
            </div>
        </div>

        <div class="form-group" id="parent_child_related_result_link_id_form_group">
            <label for="parent_child_related_result_link_id">{{trans('main.parent_child_related_result_link_id')}}
                <span
                    class="text-danger">*</span></label>
            <select class="form-control"
                    name="parent_child_related_result_link_id"
                    id="parent_child_related_result_link_id"
                    class="form-control @error('parent_child_related_result_link_id') is-invalid @enderror">
                <option value="0">0</option>
            </select>
            @error('parent_child_related_result_link_id')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>

        <div class="form-group" id="parent_child_related_start_link_id_form_group">
            <label for="parent_child_related_start_link_id">{{trans('main.parent_child_related_start_link_id')}}<span
                    class="text-danger">*</span></label>
            <select class="form-control"
                    name="parent_child_related_start_link_id"
                    id="parent_child_related_start_link_id"
                    class="form-control @error('parent_child_related_start_link_id') is-invalid @enderror">
                <option value="0">0</option>
            </select>
            @error('parent_child_related_start_link_id')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            @if (!$update)
                {{trans('main.add')}}
            @else
                {{trans('main.save')}}
            @endif
        </button>
        <a class="btn btn-success" href="{{ route('link.base_index',
 ['base' => $base, 'links' => Link::where('child_base_id', $base->id)->orderBy('parent_base_number')->get()]) }}">
            {{trans('main.cancel')}}</a>
    </form>

    <script>
        var child_base_id = form.child_base_id;
        var parent_base_id = form.parent_base_id;

        // 1.0 В списке выбора использовать поле вычисляемой таблицы
        var parent_is_in_the_selection_list_use_the_calculated_table_field = form.parent_is_in_the_selection_list_use_the_calculated_table_field;
        var parent_is_in_the_selection_list_use_the_calculated_table_field_form_group = document.getElementById('parent_is_in_the_selection_list_use_the_calculated_table_field_form_group');
        var parent_selection_calculated_table_set_id_form_group = document.getElementById('parent_selection_calculated_table_set_id_form_group');
        var parent_selection_calculated_table_set_id = form.parent_selection_calculated_table_set_id;

        // 1.1 В списке выбора использовать дополнительное связанное поле вычисляемой таблицы
        var parent_is_use_selection_calculated_table_link_id_0 = form.parent_is_use_selection_calculated_table_link_id_0;
        var parent_is_use_selection_calculated_table_link_id_0_form_group = document.getElementById('parent_is_use_selection_calculated_table_link_id_0_form_group');
        var parent_selection_calculated_table_link_id_0_form_group = document.getElementById('parent_selection_calculated_table_link_id_0_form_group');
        var parent_selection_calculated_table_link_id_0 = form.parent_selection_calculated_table_link_id_0;

        // 1.2 В списке выбора использовать два дополнительных связанных поля вычисляемой таблицы
        var parent_is_use_selection_calculated_table_link_id_1 = form.parent_is_use_selection_calculated_table_link_id_1;
        var parent_is_use_selection_calculated_table_link_id_1_form_group = document.getElementById('parent_is_use_selection_calculated_table_link_id_1_form_group');
        var parent_selection_calculated_table_link_id_1_form_group = document.getElementById('parent_selection_calculated_table_link_id_1_form_group');
        var parent_selection_calculated_table_link_id_1 = form.parent_selection_calculated_table_link_id_1;

        // Выводить связанное поле
        var parent_is_parent_related = form.parent_is_parent_related;
        var parent_is_parent_related_form_group = document.getElementById('parent_is_parent_related_form_group');
        var parent_parent_related_start_link_id_form_group = document.getElementById('parent_parent_related_start_link_id_form_group');
        var parent_parent_related_start_link_id = form.parent_parent_related_start_link_id;
        var parent_parent_related_result_link_id_form_group = document.getElementById('parent_parent_related_result_link_id_form_group');
        var parent_parent_related_result_link_id = form.parent_parent_related_result_link_id;

        // Выводить поле вычисляемой таблицы
        var parent_is_output_calculated_table_field = form.parent_is_output_calculated_table_field;
        var parent_is_output_calculated_table_field_form_group = document.getElementById('parent_is_output_calculated_table_field_form_group');
        var parent_output_calculated_table_set_id_form_group = document.getElementById('parent_output_calculated_table_set_id_form_group');
        var parent_output_calculated_table_set_id = form.parent_output_calculated_table_set_id;

        // Фильтровать поля
        var parent_is_child_related = form.parent_is_child_related;
        var parent_is_child_related_form_group = document.getElementById('parent_is_child_related_form_group');
        var parent_child_related_start_link_id_form_group = document.getElementById('parent_child_related_start_link_id_form_group');
        var parent_child_related_start_link_id = form.parent_child_related_start_link_id;
        var parent_child_related_result_link_id_form_group = document.getElementById('parent_child_related_result_link_id_form_group');
        var parent_child_related_result_link_id = form.parent_child_related_result_link_id;

        // Изменение child_base_id
        function child_base_id_changeOption(first) {
            // В списке выбора использовать поле вычисляемой таблицы
            axios.get('/link/get_parent_selection_calculated_table_set_id/'
                + child_base_id.options[child_base_id.selectedIndex].value
            ).then(function (res) {
                // если запуск функции не при загрузке страницы
                if (first != true) {
                    // сохранить текущие значения
                    var parent_selection_calculated_table_set_id_value =
                        parent_selection_calculated_table_set_id.options[parent_selection_calculated_table_set_id.selectedIndex].value;
                }

                if (res.data['result_parent_selection_calculated_table_set_id_options'] == "") {
                    parent_is_in_the_selection_list_use_the_calculated_table_field.disabled = true;
                    parent_is_in_the_selection_list_use_the_calculated_table_field_form_group.style.display = "none";
                    parent_selection_calculated_table_set_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + child_base_id.options[child_base_id.selectedIndex].text + '"!</option>';
                } else {
                    parent_is_in_the_selection_list_use_the_calculated_table_field.disabled = false;
                    parent_is_in_the_selection_list_use_the_calculated_table_field_form_group.style.display = "block";
                    parent_selection_calculated_table_set_id.innerHTML = res.data['result_parent_selection_calculated_table_set_id_options'];
                }
                // только если запуск функции при загрузке страницы
                if (first == true) {
                    // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                    @if ($update)  // при корректировке записи
                    // child
                    for (let i = 0; i < parent_selection_calculated_table_set_id.length; i++) {
                        // если элемент списка = текущему значению из базы данных
                        if (parent_selection_calculated_table_set_id[i].value == {{$link->parent_selection_calculated_table_set_id}}) {
                            // установить selected на true
                            parent_selection_calculated_table_set_id[i].selected = true;
                        }
                    }
                    @endif
                } else {
                    // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                    // child
                    for (let i = 0; i < parent_selection_calculated_table_set_id.length; i++) {
                        // если элемент списка = предыдущему(текущему) значению из базы данных
                        if (parent_selection_calculated_table_set_id[i].value == parent_selection_calculated_table_set_id_value) {
                            // установить selected на true
                            parent_selection_calculated_table_set_id[i].selected = true;
                        }
                    }
                }
                parent_selection_calculated_table_show_or_hide(parent_is_in_the_selection_list_use_the_calculated_table_field);
                parent_selection_calculated_table_set_id_changeOption(first);
                parent_selection_calculated_table_link_id_0_show_or_hide(parent_is_use_selection_calculated_table_link_id_0);
                parent_selection_calculated_table_link_id_1_show_or_hide(parent_is_use_selection_calculated_table_link_id_1);
            });
            // Выводить связанное поле
            axios.get('/link/get_parent_parent_related_start_link_id/'
                + child_base_id.options[child_base_id.selectedIndex].value
                + '{{$update ? '/'.$link->id:''}}'
                {{--                @if($update)--}}
                {{--                    +'/{{$link->id}}'--}}
                {{--                @endif--}}
            ).then(function (res) {
                // если запуск функции не при загрузке страницы
                if (first != true) {
                    // сохранить текущие значения
                    var parent_parent_related_start_link_id_value =
                        parent_parent_related_start_link_id.options[parent_parent_related_start_link_id.selectedIndex].value;
                }
                if (res.data['result_parent_parent_related_start_link_id_options'] == "") {
                    parent_is_parent_related.disabled = true;
                    parent_is_parent_related_form_group.style.display = "none";
                    parent_parent_related_start_link_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + child_base_id.options[child_base_id.selectedIndex].text + '"!</option>';
                } else {
                    parent_is_parent_related.disabled = false;
                    parent_is_parent_related_form_group.style.display = "block";
                    parent_parent_related_start_link_id.innerHTML = res.data['result_parent_parent_related_start_link_id_options'];
                }
                // только если запуск функции при загрузке страницы
                if (first == true) {
                    // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                    @if ($update)  // при корректировке записи
                    // child
                    for (let i = 0; i < parent_parent_related_start_link_id.length; i++) {
                        // если элемент списка = текущему значению из базы данных
                        if (parent_parent_related_start_link_id[i].value == {{$link->parent_parent_related_start_link_id}}) {
                            // установить selected на true
                            parent_parent_related_start_link_id[i].selected = true;
                        }
                    }
                    @endif
                } else {
                    // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                    // child
                    for (let i = 0; i < parent_parent_related_start_link_id.length; i++) {
                        // если элемент списка = предыдущему(текущему) значению из базы данных
                        if (parent_parent_related_start_link_id[i].value == parent_parent_related_start_link_id_value) {
                            // установить selected на true
                            parent_parent_related_start_link_id[i].selected = true;
                        }
                    }
                }
                parent_parent_show_or_hide(parent_is_parent_related);
                parent_parent_related_start_link_id_changeOption(first);
            });
            // Выводить поле вычисляемой таблицы
            axios.get('/link/get_parent_output_calculated_table_set_id/'
                + child_base_id.options[child_base_id.selectedIndex].value
            ).then(function (res) {
                //alert(child_base_id.options[child_base_id.selectedIndex].value);
                // если запуск функции не при загрузке страницы
                if (first != true) {
                    // сохранить текущие значения
                    var parent_output_calculated_table_set_id_value =
                        parent_output_calculated_table_set_id.options[parent_output_calculated_table_set_id.selectedIndex].value;
                }

                if (res.data['result_parent_output_calculated_table_set_id_options'] == "") {
                    parent_is_output_calculated_table_field.disabled = true;
                    parent_is_output_calculated_table_field_form_group.style.display = "none";
                    parent_output_calculated_table_set_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + child_base_id.options[child_base_id.selectedIndex].text + '"!</option>';
                } else {
                    parent_is_output_calculated_table_field.disabled = false;
                    parent_is_output_calculated_table_field_form_group.style.display = "block";
                    parent_output_calculated_table_set_id.innerHTML = res.data['result_parent_output_calculated_table_set_id_options'];
                }
                // только если запуск функции при загрузке страницы
                if (first == true) {
                    // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                    @if ($update)  // при корректировке записи
                    // child
                    for (let i = 0; i < parent_output_calculated_table_set_id.length; i++) {
                        // если элемент списка = текущему значению из базы данных
                        if (parent_output_calculated_table_set_id[i].value == {{$link->parent_output_calculated_table_set_id}}) {
                            // установить selected на true
                            parent_output_calculated_table_set_id[i].selected = true;
                        }
                    }
                    @endif
                } else {
                    // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                    // child
                    for (let i = 0; i < parent_output_calculated_table_set_id.length; i++) {
                        // если элемент списка = предыдущему(текущему) значению из базы данных
                        if (parent_output_calculated_table_set_id[i].value == parent_output_calculated_table_set_id_value) {
                            // установить selected на true
                            parent_output_calculated_table_set_id[i].selected = true;
                        }
                    }
                }
                parent_calculated_table_show_or_hide(parent_is_output_calculated_table_field);
                parent_output_calculated_table_set_id_changeOption(first);
            });
            // Фильтровать поля
            axios.get('/link/get_parent_child_related_start_link_id/'
                + child_base_id.options[child_base_id.selectedIndex].value
                + '{{$update ? '/'.$link->id:''}}'
                {{--                @if($update)--}}
                {{--                    +'/{{$link->id}}'--}}
                {{--                @endif--}}
            ).then(function (res) {
                // если запуск функции не при загрузке страницы
                if (first != true) {
                    // сохранить текущие значения
                    var parent_child_parent_related_start_link_id_value =
                        parent_child_related_start_link_id.options[parent_child_related_start_link_id.selectedIndex].value;
                }

                if (res.data['result_parent_child_related_start_link_id_options'] == "") {
                    parent_is_child_related.disabled = true;
                    parent_is_child_related_form_group.style.display = "none";
                    parent_child_related_start_link_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + child_base_id.options[child_base_id.selectedIndex].text + '"!</option>';
                } else {
                    parent_is_child_related.disabled = false;
                    parent_is_child_related_form_group.style.display = "block";
                    parent_child_related_start_link_id.innerHTML = res.data['result_parent_child_related_start_link_id_options'];
                }
                // только если запуск функции при загрузке страницы
                if (first == true) {
                    // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                    @if ($update)  // при корректировке записи
                    // child
                    for (let i = 0; i < parent_child_related_start_link_id.length; i++) {
                        // если элемент списка = текущему значению из базы данных
                        if (parent_child_related_start_link_id[i].value == {{$link->parent_child_related_start_link_id}}) {
                            // установить selected на true
                            parent_child_related_start_link_id[i].selected = true;
                        }
                    }
                    @endif
                } else {
                    // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                    // child
                    for (let i = 0; i < parent_child_related_start_link_id.length; i++) {
                        // если элемент списка = предыдущему(текущему) значению из базы данных
                        if (parent_child_related_start_link_id[i].value == parent_child_related_start_link_id_value) {
                            // установить selected на true
                            parent_child_related_start_link_id[i].selected = true;
                        }
                    }
                }
                parent_child_show_or_hide(parent_is_child_related);
                parent_child_related_start_link_id_changeOption(first);
            });
        }

        // 1.0 В списке выбора использовать поле вычисляемой таблицы
        function parent_selection_calculated_table_show_or_hide(box) {
            var vis = "";
            var logval = false;
            if (box.checked) {
                vis = "block";
                logval = true;
            } else {
                vis = "none";
                logval = false;
            }
            parent_base_id.disabled = logval;
            parent_selection_calculated_table_set_id_form_group.style.display = vis;
            parent_selection_calculated_table_set_id.disabled = !logval;  // "!logval" используется

            parent_is_use_selection_calculated_table_link_id_0_form_group.style.display = vis;
            parent_is_use_selection_calculated_table_link_id_0.disabled = !logval;  // "!logval" используется
            parent_selection_calculated_table_link_id_0_form_group.style.display = vis;
            parent_selection_calculated_table_link_id_0.disabled = !logval;  // "!logval" используется

            parent_is_use_selection_calculated_table_link_id_1_form_group.style.display = vis;
            parent_is_use_selection_calculated_table_link_id_1.disabled = !logval;  // "!logval" используется
            parent_selection_calculated_table_link_id_1_form_group.style.display = vis;
            parent_selection_calculated_table_link_id_1.disabled = !logval;  // "!logval" используется

            if (vis == "block") {
                // "parent_selection_calculated_table_set_id_changeOption(false)" нужно
                parent_selection_calculated_table_set_id_changeOption(false);
            }
        }

        function parent_selection_calculated_table_set_id_changeOption(first) {
            axios.get('/link/get_parent_base_id_from_set_id/'
                + parent_selection_calculated_table_set_id.options[parent_selection_calculated_table_set_id.selectedIndex].value
            ).then(function (res) {
                // если запуск функции не при загрузке страницы
                if (first != true) {
                    // сохранить текущие значения
                    var parent_base_id_value =
                        parent_base_id.options[parent_base_id.selectedIndex].value;
                }
                if (res.data['parent_base_id'] == "") {
                    {{--parent_base_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + parent_output_calculated_table_set_id.options[parent_output_calculated_table_set_id.selectedIndex].text + '"!</option>';--}}
                } else {
                    //parent_base_id.innerHTML = '<option value = "' + res.data['parent_base_id'] + '">' + res.data['parent_base_name'] + '</option>';
                    for (let i = 0; i < parent_base_id.length; i++) {
                        // если элемент списка = предыдущему(текущему) значению из базы данных
                        if (parent_base_id[i].value == res.data['parent_base_id']) {
                            // установить selected на true
                            parent_base_id[i].selected = true;
                        }
                    }

                }
                // только если запуск функции при загрузке страницы
                if (first == true) {
                    // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                    @if ($update)  // при корректировке записи
                    // child
                    for (let i = 0; i < parent_base_id.length; i++) {
                        // если элемент списка = текущему значению из базы данных
                        if (parent_base_id[i].value == {{$link->parent_base_id}}) {
                            // установить selected на true
                            parent_base_id[i].selected = true;
                        }
                    }
                    @endif
                } else {
                    // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                    // child
                    // for (let i = 0; i < parent_base_id.length; i++) {
                    //     // если элемент списка = предыдущему(текущему) значению из базы данных
                    //     if (parent_base_id[i].value == parent_base_id_value) {
                    //         // установить selected на true
                    //         parent_base_id[i].selected = true;
                    //     }
                    // }
                }
            });
        }

        // 1.1 В списке выбора использовать дополнительное связанное поле вычисляемой таблицы
        function parent_selection_calculated_table_link_id_0_show_or_hide(box) {
            var vis = "";
            var logval = false;
            if (box.checked) {
                vis = "block";
                logval = true;
            } else {
                vis = "none";
                logval = false;
            }
             parent_base_id.disabled = logval;
            //parent_base_id.disabled = true;
            parent_selection_calculated_table_link_id_0_form_group.style.display = vis;
            parent_selection_calculated_table_link_id_0.disabled = !logval;  // "!logval" используется

            parent_is_use_selection_calculated_table_link_id_1_form_group.style.display = vis;
            parent_is_use_selection_calculated_table_link_id_1.disabled = !logval;  // "!logval" используется
            parent_selection_calculated_table_link_id_1_form_group.style.display = vis;
            parent_selection_calculated_table_link_id_1.disabled = !logval;  // "!logval" используется
            if (vis == "block") {
                if(parent_selection_calculated_table_set_id.options[parent_selection_calculated_table_set_id.selectedIndex].value != 0) {
                    axios.get('/link/get_links_from_set_id_link_from_parent_base/'
                        + parent_selection_calculated_table_set_id.options[parent_selection_calculated_table_set_id.selectedIndex].value
                    ).then(function (res) {
                        // если запуск функции не при загрузке страницы
                        if (res.data['links_options'] == "") {
                            parent_selection_calculated_table_link_id_0.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + parent_selection_calculated_table_set_id.options[parent_selection_calculated_table_set_id.selectedIndex].text + '"!</option>';
                        } else {
                            parent_selection_calculated_table_link_id_0.innerHTML = res.data['links_options'];
                        }
                        // только если запуск функции при загрузке страницы
                        // if (first == true) {
                        // if (true == true) {
                            // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                            @if ($update)  // при корректировке записи
                            // child
                            for (let i = 0; i < parent_selection_calculated_table_link_id_0.length; i++) {
                                // если элемент списка = текущему значению из базы данных
                                if (parent_selection_calculated_table_link_id_0[i].value == {{$link->parent_selection_calculated_table_link_id_0}}) {
                                    // установить selected на true
                                    parent_selection_calculated_table_link_id_0[i].selected = true;
                                }
                            }
                            @endif
                        // } else {
                        //     // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                        //     // child
                        //     // for (let i = 0; i < parent_selection_calculated_table_set_id.length; i++) {
                        //     //     // если элемент списка = предыдущему(текущему) значению из базы данных
                        //     //     if (parent_selection_calculated_table_set_id[i].value == parent_selection_calculated_table_set_id_value) {
                        //     //         // установить selected на true
                        //     //         parent_selection_calculated_table_set_id[i].selected = true;
                        //     //     }
                        //     // }
                        // }
                        parent_selection_calculated_table_link_id_1_show_or_hide(parent_is_use_selection_calculated_table_link_id_1);
                    });
                }
            }
        }

        function parent_selection_calculated_table_link_id_0_changeOption(first) {
            axios.get('/link/get_parent_base_id_from_link_id/'
                + parent_selection_calculated_table_link_id_0.options[parent_selection_calculated_table_link_id_0.selectedIndex].value
            ).then(function (res) {
                // если запуск функции не при загрузке страницы
                if (first != true) {
                    // сохранить текущие значения
                    var parent_base_id_value =
                        parent_base_id.options[parent_base_id.selectedIndex].value;
                }
                if (res.data['parent_base_id'] == "") {
                    {{--parent_base_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + parent_output_calculated_table_set_id.options[parent_output_calculated_table_set_id.selectedIndex].text + '"!</option>';--}}
                } else {
                    //parent_base_id.innerHTML = '<option value = "' + res.data['parent_base_id'] + '">' + res.data['parent_base_name'] + '</option>';
                    for (let i = 0; i < parent_base_id.length; i++) {
                        // если элемент списка = предыдущему(текущему) значению из базы данных
                        if (parent_base_id[i].value == res.data['parent_base_id']) {
                            // установить selected на true
                            parent_base_id[i].selected = true;
                        }
                    }

                }
                // только если запуск функции при загрузке страницы
                if (first == true) {
                    // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                    @if ($update)  // при корректировке записи
                    // child
                    for (let i = 0; i < parent_base_id.length; i++) {
                        // если элемент списка = текущему значению из базы данных
                        if (parent_base_id[i].value == {{$link->parent_base_id}}) {
                            // установить selected на true
                            parent_base_id[i].selected = true;
                        }
                    }
                    @endif
                } else {
                    // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                    // child
                    // for (let i = 0; i < parent_base_id.length; i++) {
                    //     // если элемент списка = предыдущему(текущему) значению из базы данных
                    //     if (parent_base_id[i].value == parent_base_id_value) {
                    //         // установить selected на true
                    //         parent_base_id[i].selected = true;
                    //     }
                    // }
                }
            });
        }

        // 1.2 В списке выбора использовать два дополнительных связанных поля вычисляемой таблицы
        function parent_selection_calculated_table_link_id_1_show_or_hide_change() {
            parent_selection_calculated_table_link_id_1_show_or_hide(parent_is_use_selection_calculated_table_link_id_1);
         }

        function parent_selection_calculated_table_link_id_1_show_or_hide(box) {
            var vis = "";
            var logval = false;
            if (box.checked) {
                vis = "block";
                logval = true;
            } else {
                vis = "none";
                logval = false;
            }
            parent_base_id.disabled = logval;
            //parent_base_id.disabled = true;
            parent_selection_calculated_table_link_id_1_form_group.style.display = vis;
            parent_selection_calculated_table_link_id_1.disabled = !logval;  // "!logval" используется
            if (vis == "block") {
                if(parent_selection_calculated_table_link_id_0.options[parent_selection_calculated_table_link_id_0.selectedIndex].value != 0) {
                    axios.get('/link/get_links_from_link_id_parent_base/'
                        + parent_selection_calculated_table_link_id_0.options[parent_selection_calculated_table_link_id_0.selectedIndex].value
                    ).then(function (res) {
                        // если запуск функции не при загрузке страницы
                        if (res.data['links_options'] == "") {
                            parent_selection_calculated_table_link_id_1.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + parent_selection_calculated_table_link_id_0.options[parent_selection_calculated_table_link_id_0.selectedIndex].text + '"!</option>';
                        } else {
                            parent_selection_calculated_table_link_id_1.innerHTML = res.data['links_options'];
                        }
                        // только если запуск функции при загрузке страницы
                        // if (first == true) {
                        // if (true == true) {
                            // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                            @if ($update)  // при корректировке записи
                            // child
                            for (let i = 0; i < parent_selection_calculated_table_link_id_1.length; i++) {
                                // если элемент списка = текущему значению из базы данных
                                if (parent_selection_calculated_table_link_id_1[i].value == {{$link->parent_selection_calculated_table_link_id_1}}) {
                                    // установить selected на true
                                    parent_selection_calculated_table_link_id_1[i].selected = true;
                                }
                            }
                            @endif
                        // } else {
                        //     // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                        //     // child
                        //     // for (let i = 0; i < parent_selection_calculated_table_set_id.length; i++) {
                        //     //     // если элемент списка = предыдущему(текущему) значению из базы данных
                        //     //     if (parent_selection_calculated_table_set_id[i].value == parent_selection_calculated_table_set_id_value) {
                        //     //         // установить selected на true
                        //     //         parent_selection_calculated_table_set_id[i].selected = true;
                        //     //     }
                        //     // }
                        // }
                        // "parent_selection_calculated_table_link_id_1_changeOption(false)" нужно
                        parent_selection_calculated_table_link_id_1_changeOption(false);
                    });
                }
            }
        }

        function parent_selection_calculated_table_link_id_1_changeOption(first) {
            axios.get('/link/get_parent_base_id_from_link_id/'
                + parent_selection_calculated_table_link_id_1.options[parent_selection_calculated_table_link_id_1.selectedIndex].value
            ).then(function (res) {
                // если запуск функции не при загрузке страницы
                if (first != true) {
                    // сохранить текущие значения
                    var parent_base_id_value =
                        parent_base_id.options[parent_base_id.selectedIndex].value;
                }
                if (res.data['parent_base_id'] == "") {
                    {{--parent_base_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + parent_output_calculated_table_set_id.options[parent_output_calculated_table_set_id.selectedIndex].text + '"!</option>';--}}
                } else {
                    //parent_base_id.innerHTML = '<option value = "' + res.data['parent_base_id'] + '">' + res.data['parent_base_name'] + '</option>';
                    for (let i = 0; i < parent_base_id.length; i++) {
                        // если элемент списка = предыдущему(текущему) значению из базы данных
                        if (parent_base_id[i].value == res.data['parent_base_id']) {
                            // установить selected на true
                            parent_base_id[i].selected = true;
                        }
                    }

                }
                // только если запуск функции при загрузке страницы
                if (first == true) {
                    // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                    @if ($update)  // при корректировке записи
                    // child
                    for (let i = 0; i < parent_base_id.length; i++) {
                        // если элемент списка = текущему значению из базы данных
                        if (parent_base_id[i].value == {{$link->parent_base_id}}) {
                            // установить selected на true
                            parent_base_id[i].selected = true;
                        }
                    }
                    @endif
                } else {
                    // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                    // child
                    // for (let i = 0; i < parent_base_id.length; i++) {
                    //     // если элемент списка = предыдущему(текущему) значению из базы данных
                    //     if (parent_base_id[i].value == parent_base_id_value) {
                    //         // установить selected на true
                    //         parent_base_id[i].selected = true;
                    //     }
                    // }
                }
            });
        }

        // Выводить связанное поле
        function parent_parent_show_or_hide(box) {
            var vis = "";
            var logval = false;
            if (box.checked) {
                vis = "block";
                logval = true;
            } else {
                vis = "none";
                logval = false;
            }
            parent_base_id.disabled = logval;
            parent_parent_related_start_link_id_form_group.style.display = vis;
            parent_parent_related_start_link_id.disabled = !logval;  // "!logval" используется
            parent_parent_related_result_link_id_form_group.style.display = vis;
            parent_parent_related_result_link_id.disabled = !logval;  // "!logval" используется
        }

        function parent_parent_related_start_link_id_changeOption(first) {
            if (parent_parent_related_start_link_id.options[parent_parent_related_start_link_id.selectedIndex].value != 0) {
                axios.get('/link/get_tree_from_link_id/'
                    + parent_parent_related_start_link_id.options[parent_parent_related_start_link_id.selectedIndex].value
                ).then(function (res) {
                    // если запуск функции не при загрузке страницы
                    if (first != true) {
                        // сохранить текущие значения
                        var parent_parent_related_result_link_id_value =
                            parent_parent_related_result_link_id.options[parent_parent_related_result_link_id.selectedIndex].value;
                    }

                    if (res.data['result_parent_parent_related_result_link_id_options'] == "") {
                        parent_parent_related_result_link_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + parent_parent_related_start_link_id.options[parent_parent_related_start_link_id.selectedIndex].text + '"!</option>';
                        //arent_item_id.innerHTML = "<option>Выберите героя</option>";
                        //return;
                    } else {
                        // заполнение select
                        parent_parent_related_result_link_id.innerHTML = res.data['result_parent_parent_related_result_link_id_options'];
                    }
                    // только если запуск функции при загрузке страницы
                    if (first == true) {
                        // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                        @if ($update)  // при корректировке записи
                        // child
                        for (let i = 0; i < parent_parent_related_result_link_id.length; i++) {
                            // если элемент списка = текущему значению из базы данных
                            if (parent_parent_related_result_link_id[i].value == {{$link->parent_parent_related_result_link_id}}) {
                                // установить selected на true
                                parent_parent_related_result_link_id[i].selected = true;
                            }
                        }
                        @endif
                    } else {
                        // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                        // child
                        for (let i = 0; i < parent_parent_related_result_link_id.length; i++) {
                            // если элемент списка = предыдущему(текущему) значению из базы данных
                            if (parent_parent_related_result_link_id[i].value == parent_parent_related_result_link_id_value) {
                                // установить selected на true
                                parent_parent_related_result_link_id[i].selected = true;
                            }
                        }
                    }
                    parent_parent_related_result_link_id_changeOption(first);
                });
            }
        }

        function parent_parent_related_result_link_id_changeOption(first) {
            axios.get('/link/get_parent_base_id_from_link_id/'
                + parent_parent_related_result_link_id.options[parent_parent_related_result_link_id.selectedIndex].value
            ).then(function (res) {
                // если запуск функции не при загрузке страницы
                if (first != true) {
                    // сохранить текущие значения
                    var parent_base_id_value =
                        parent_base_id.options[parent_base_id.selectedIndex].value;
                }

                if (res.data['parent_base_id'] == "") {
                    parent_base_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + parent_parent_related_result_link_id.options[parent_parent_related_result_link_id.selectedIndex].text + '"!</option>';
                } else {
                    //parent_base_id.innerHTML = '<option value = "' + res.data['parent_base_id'] + '">' + res.data['parent_base_name'] + '</option>';
                    for (let i = 0; i < parent_base_id.length; i++) {
                        // если элемент списка = предыдущему(текущему) значению из базы данных
                        if (parent_base_id[i].value == res.data['parent_base_id']) {
                            // установить selected на true
                            parent_base_id[i].selected = true;
                        }
                    }

                }
                // только если запуск функции при загрузке страницы
                if (first == true) {
                    // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                    @if ($update)  // при корректировке записи
                    // child
                    for (let i = 0; i < parent_base_id.length; i++) {
                        // если элемент списка = текущему значению из базы данных
                        if (parent_base_id[i].value == {{$link->parent_base_id}}) {
                            // установить selected на true
                            parent_base_id[i].selected = true;
                        }
                    }
                    @endif
                } else {
                    // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                    // child
                    // for (let i = 0; i < parent_base_id.length; i++) {
                    //     // если элемент списка = предыдущему(текущему) значению из базы данных
                    //     if (parent_base_id[i].value == parent_base_id_value) {
                    //         // установить selected на true
                    //         parent_base_id[i].selected = true;
                    //     }
                    // }
                }
            });
        }

        // Выводить поле вычисляемой таблицы
        function parent_calculated_table_show_or_hide(box) {
            var vis = "";
            var logval = false;
            if (box.checked) {
                vis = "block";
                logval = true;
            } else {
                vis = "none";
                logval = false;
            }
            parent_base_id.disabled = logval;
            parent_output_calculated_table_set_id_form_group.style.display = vis;
            parent_output_calculated_table_set_id.disabled = !logval;  // "!logval" используется
            if (vis == "block") {
                // "parent_output_calculated_table_set_id_changeOption(false)" нужно
                parent_output_calculated_table_set_id_changeOption(false);
            }
        }

        function parent_output_calculated_table_set_id_changeOption(first) {
            axios.get('/link/get_parent_base_id_from_set_id/'
                + parent_output_calculated_table_set_id.options[parent_output_calculated_table_set_id.selectedIndex].value
            ).then(function (res) {
                // если запуск функции не при загрузке страницы
                if (first != true) {
                    // сохранить текущие значения
                    var parent_base_id_value =
                        parent_base_id.options[parent_base_id.selectedIndex].value;
                }
                if (res.data['parent_base_id'] == "") {
                    {{--parent_base_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + parent_output_calculated_table_set_id.options[parent_output_calculated_table_set_id.selectedIndex].text + '"!</option>';--}}
                } else {
                    //parent_base_id.innerHTML = '<option value = "' + res.data['parent_base_id'] + '">' + res.data['parent_base_name'] + '</option>';
                    for (let i = 0; i < parent_base_id.length; i++) {
                        // если элемент списка = предыдущему(текущему) значению из базы данных
                        if (parent_base_id[i].value == res.data['parent_base_id']) {
                            // установить selected на true
                            parent_base_id[i].selected = true;
                        }
                    }

                }
                // только если запуск функции при загрузке страницы
                if (first == true) {
                    // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                    @if ($update)  // при корректировке записи
                    // child
                    for (let i = 0; i < parent_base_id.length; i++) {
                        // если элемент списка = текущему значению из базы данных
                        if (parent_base_id[i].value == {{$link->parent_base_id}}) {
                            // установить selected на true
                            parent_base_id[i].selected = true;
                        }
                    }
                    @endif
                } else {
                    // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                    // child
                    // for (let i = 0; i < parent_base_id.length; i++) {
                    //     // если элемент списка = предыдущему(текущему) значению из базы данных
                    //     if (parent_base_id[i].value == parent_base_id_value) {
                    //         // установить selected на true
                    //         parent_base_id[i].selected = true;
                    //     }
                    // }
                }
            });
        }

        // Фильтрация полей
        function parent_child_show_or_hide(box) {
            var vis = "";
            var logval = false;
            if (box.checked) {
                vis = "block";
                logval = true;
            } else {
                vis = "none";
                logval = false;
            }
            parent_base_id.disabled = logval;
            parent_child_related_start_link_id_form_group.style.display = vis;
            parent_child_related_start_link_id.disabled = !logval;  // "!logval" используется
            parent_child_related_result_link_id_form_group.style.display = vis;
            parent_child_related_result_link_id.disabled = !logval;  // "!logval" используется
            if (vis == "block") {
                parent_child_related_start_link_id_changeOption(true);
            }

        }

        function parent_child_related_start_link_id_changeOption(first) {
            if (parent_child_related_start_link_id.options[parent_child_related_start_link_id.selectedIndex].value != 0) {
                axios.get('/link/get_tree_from_link_id/'
                    + parent_child_related_start_link_id.options[parent_child_related_start_link_id.selectedIndex].value
                ).then(function (res) {
                    // если запуск функции не при загрузке страницы
                    if (first != true) {
                        // сохранить текущие значения
                        var parent_child_related_result_link_id_value =
                            parent_child_related_result_link_id.options[parent_child_related_result_link_id.selectedIndex].value;
                    }

                    if (res.data['result_parent_parent_related_result_link_id_options'] == "") {
                        parent_child_related_result_link_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + parent_child_related_start_link_id.options[parent_child_related_start_link_id.selectedIndex].text + '"!</option>';
                        //arent_item_id.innerHTML = "<option>Выберите героя</option>";
                        //return;
                    } else {
                        // заполнение select
                        parent_child_related_result_link_id.innerHTML = res.data['result_parent_parent_related_result_link_id_options'];
                    }
                    // только если запуск функции при загрузке страницы
                    if (first == true) {
                        // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                        @if ($update)  // при корректировке записи
                        // child
                        for (let i = 0; i < parent_child_related_result_link_id.length; i++) {
                            // если элемент списка = текущему значению из базы данных
                            if (parent_child_related_result_link_id[i].value == {{$link->parent_child_related_result_link_id}}) {
                                // установить selected на true
                                parent_child_related_result_link_id[i].selected = true;
                            }
                        }
                        @endif
                    } else {
                        // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                        // child
                        for (let i = 0; i < parent_child_related_result_link_id.length; i++) {
                            // если элемент списка = предыдущему(текущему) значению из базы данных
                            if (parent_child_related_result_link_id[i].value == parent_child_related_result_link_id_value) {
                                // установить selected на true
                                parent_child_related_result_link_id[i].selected = true;
                            }
                        }
                    }
                    parent_child_related_result_link_id_changeOption(first);
                });
            }
        }

        function parent_child_related_result_link_id_changeOption(first) {
            axios.get('/link/get_parent_base_id_from_link_id/'
                + parent_child_related_result_link_id.options[parent_child_related_result_link_id.selectedIndex].value
            ).then(function (res) {
                // если запуск функции не при загрузке страницы
                if (first != true) {
                    // сохранить текущие значения
                    var parent_base_id_value =
                        parent_base_id.options[parent_base_id.selectedIndex].value;
                }

                if (res.data['parent_base_id'] == "") {
                    parent_base_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + parent_child_related_result_link_id.options[parent_child_related_result_link_id.selectedIndex].text + '"!</option>';
                } else {
                    //parent_base_id.innerHTML = '<option value = "' + res.data['parent_base_id'] + '">' + res.data['parent_base_name'] + '</option>';
                    for (let i = 0; i < parent_base_id.length; i++) {
                        // если элемент списка = предыдущему(текущему) значению из базы данных
                        if (parent_base_id[i].value == res.data['parent_base_id']) {
                            // установить selected на true
                            parent_base_id[i].selected = true;
                        }
                    }

                }
                // только если запуск функции при загрузке страницы
                if (first == true) {
                    // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                    @if ($update)  // при корректировке записи
                    // child
                    for (let i = 0; i < parent_base_id.length; i++) {
                        // если элемент списка = текущему значению из базы данных
                        if (parent_base_id[i].value == {{$link->parent_base_id}}) {
                            // установить selected на true
                            parent_base_id[i].selected = true;
                        }
                    }
                    @endif
                } else {
                    // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                    // child
                    // for (let i = 0; i < parent_base_id.length; i++) {
                    //     // если элемент списка = предыдущему(текущему) значению из базы данных
                    //     if (parent_base_id[i].value == parent_base_id_value) {
                    //         // установить selected на true
                    //         parent_base_id[i].selected = true;
                    //     }
                    // }
                }
            });
        }

        child_base_id.addEventListener("change", child_base_id_changeOption);
        parent_selection_calculated_table_set_id.addEventListener("change", parent_selection_calculated_table_set_id_changeOption);
        //parent_selection_calculated_table_link_id_0.addEventListener("change", parent_selection_calculated_table_link_id_1_show_or_hide(parent_is_use_selection_calculated_table_link_id_1));
        parent_selection_calculated_table_link_id_0.addEventListener("change", parent_selection_calculated_table_link_id_1_show_or_hide_change);
        // parent_is_use_selection_calculated_table_link_id_1.addEventListener("change", parent_selection_calculated_table_link_id_1_changeOption);
        parent_parent_related_start_link_id.addEventListener("change", parent_parent_related_start_link_id_changeOption);
        parent_parent_related_result_link_id.addEventListener("change", parent_parent_related_result_link_id_changeOption);
        parent_output_calculated_table_set_id.addEventListener("change", parent_output_calculated_table_set_id_changeOption);
        parent_child_related_start_link_id.addEventListener("change", parent_child_related_start_link_id_changeOption);
        parent_child_related_result_link_id.addEventListener("change", parent_child_related_result_link_id_changeOption);

        window.onload = function () {
            child_base_id_changeOption(true);
            parent_selection_calculated_table_show_or_hide(parent_is_in_the_selection_list_use_the_calculated_table_field);
            parent_selection_calculated_table_link_id_0_show_or_hide(parent_is_use_selection_calculated_table_link_id_0);
            parent_selection_calculated_table_link_id_1_show_or_hide(parent_is_use_selection_calculated_table_link_id_1);
            parent_parent_show_or_hide(parent_is_parent_related);
            parent_parent_related_start_link_id_changeOption(true);
            parent_parent_related_result_link_id_changeOption(true);
            parent_calculated_table_show_or_hide(parent_is_output_calculated_table_field);
            parent_output_calculated_table_set_id_changeOption(true);
            parent_child_show_or_hide(parent_is_child_related);
            parent_child_related_start_link_id_changeOption(true);
            parent_child_related_result_link_id_changeOption(true);
        };

    </script>
@endsection
