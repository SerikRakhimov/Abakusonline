@extends('layouts.app')

@section('content')
    <?php
    $update = isset($role);
    ?>
    <p>
        @include('layouts.template.show_name', ['template'=>$template])
    </p>
    <p>
        @include('layouts.form_edit_title', ['update'=>$update, 'table_name'=>trans('main.role')])
    </p>
    <form action="{{$update ? route('role.update',$role):route('role.store')}}" method="POST"
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
                       value="{{ old('serial_number') ?? ($role->serial_number ?? '0') }}">
                @error('serial_number')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-7">
            </div>
        </div>

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
                           value="{{ old('name_lang_' . $key) ?? ($role['name_lang_' . $key] ?? '') }}">
                </div>
                @error('name_lang_' . $key)
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            @endforeach
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
                        {{ old('desc_lang_' . $key) ?? ($role['desc_lang_' . $key] ?? '') }}
                        </textarea>
                </div>
                @error('desc_lang_' . $key)
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            @endforeach
        </div>

        <div class="form-group row" id="is_author_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_author">{{trans('main.is_author')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_author') is-invalid @enderror"
                       type="checkbox"
                       name="is_author"
                       placeholder=""
                       @if ((old('is_author') ?? ($role->is_author ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_author')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_default_for_external_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_default_for_external">{{trans('main.is_default_for_external')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_default_for_external') is-invalid @enderror"
                       type="checkbox"
                       name="is_default_for_external"
                       placeholder=""
                       @if ((old('is_default_for_external') ?? ($role->is_default_for_external ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_default_for_external')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_base_sndbt_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_base_sndbt">{{trans('main.is_list_base_sndbt')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_base_sndbt') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_base_sndbt"
                       placeholder=""
                       @if ((old('is_list_base_sndbt') ?? ($role->is_list_base_sndbt ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_base_sndbt')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_base_id_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_base_id">{{trans('main.is_list_base_id')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_base_id') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_base_id"
                       placeholder=""
                       @if ((old('is_list_base_id') ?? ($role->is_list_base_id ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_base_id')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_base_calculated_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_base_calculated">{{trans('main.is_list_base_calculated')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_base_calculated') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_base_calculated"
                       placeholder=""
{{--                       true - значение по умолчанию--}}
                       @if ((old('is_list_base_calculated') ?? ($role->is_list_base_calculated ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_base_calculated')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_base_setup_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_base_setup">{{trans('main.is_list_base_setup')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_base_setup') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_base_setup"
                       placeholder=""
                       @if ((old('is_list_base_setup') ?? ($role->is_list_base_setup ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_base_setup')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_all_base_calcname_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_all_base_calcname_enable">{{trans('main.is_all_base_calcname_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_all_base_calcname_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_all_base_calcname_enable"
                       placeholder=""
                       {{--                       "$role->is_all_base_calcname_enable ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_all_base_calcname_enable') ?? ($role->is_all_base_calcname_enable ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_all_base_calcname_enable')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_base_sort_creation_date_desc_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_base_sort_creation_date_desc">{{trans('main.is_list_base_sort_creation_date_desc')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_base_sort_creation_date_desc') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_base_sort_creation_date_desc"
                       placeholder=""
                       {{--                       "$role->is_list_base_sort_creation_date_desc ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_list_base_sort_creation_date_desc') ?? ($role->is_list_base_sort_creation_date_desc ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_base_sort_creation_date_desc')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_base_create_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_base_create">{{trans('main.is_list_base_create')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_base_create') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_base_create"
                       placeholder=""
                       {{--                       "$role->is_list_base_create ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_list_base_create') ?? ($role->is_list_base_create ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_base_create')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_base_read_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_base_read">{{trans('main.is_list_base_read')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_base_read') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_base_read"
                       placeholder=""
                       @if ((old('is_list_base_read') ?? ($role->is_list_base_read ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_base_read')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>


        <div class="form-group row" id="is_list_base_update_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_base_update">{{trans('main.is_list_base_update')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_base_update') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_base_update"
                       placeholder=""
                       {{--                       "$role->is_list_base_update ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_list_base_update') ?? ($role->is_list_base_update ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_base_update')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_base_delete_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_base_delete">{{trans('main.is_list_base_delete')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_base_delete') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_base_delete"
                       placeholder=""
                       {{--                       "$role->is_list_base_delete ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_list_base_delete') ?? ($role->is_list_base_delete ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_base_delete')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_base_used_delete_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_base_used_delete">{{trans('main.is_list_base_used_delete')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_base_used_delete') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_base_used_delete"
                       placeholder=""
                       {{--                       "$role->is_list_base_used_delete ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_list_base_used_delete') ?? ($role->is_list_base_used_delete ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_base_used_delete')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_base_byuser_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_base_byuser">{{trans('main.is_list_base_byuser')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_base_byuser') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_base_byuser"
                       placeholder=""
                       @if ((old('is_list_base_byuser') ?? ($role->is_list_base_byuser ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_base_byuser')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_link_baselink_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_link_baselink">{{trans('main.is_list_link_baselink')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_link_baselink') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_link_baselink"
                       placeholder=""
                       @if ((old('is_list_link_baselink') ?? ($role->is_list_link_baselink ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_link_baselink')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_edit_base_read_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_edit_base_read">{{trans('main.is_edit_base_read')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_edit_base_read') is-invalid @enderror"
                       type="checkbox"
                       name="is_edit_base_read"
                       placeholder=""
                       @if ((old('is_edit_base_read') ?? ($role->is_edit_base_read ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_edit_base_read')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_edit_base_update_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_edit_base_update">{{trans('main.is_edit_base_update')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_edit_base_update') is-invalid @enderror"
                       type="checkbox"
                       name="is_edit_base_update"
                       placeholder=""
                       {{--                       "$role->is_edit_base_update ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_edit_base_update') ?? ($role->is_edit_base_update ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_edit_base_update')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_base_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_base_enable">{{trans('main.is_list_base_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_base_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_base_enable"
                       placeholder=""
                       {{--                       "$roba->is_list_base_enable ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_list_base_enable') ?? ($role->is_list_base_enable ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_base_enable')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_link_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_link_enable">{{trans('main.is_list_link_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_link_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_link_enable"
                       placeholder=""
                       {{--                       "$role->is_list_link_enable ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_list_link_enable') ?? ($role->is_list_link_enable ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_link_enable')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_show_base_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_show_base_enable">{{trans('main.is_show_base_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_show_base_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_show_base_enable"
                       placeholder=""
                       {{--                       "$role->is_show_base_enable ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_show_base_enable') ?? ($role->is_show_base_enable ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_show_base_enable')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_show_link_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_show_link_enable">{{trans('main.is_show_link_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_show_link_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_show_link_enable"
                       placeholder=""
                       {{--                       "$role->is_show_link_enable ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_show_link_enable') ?? ($role->is_show_link_enable ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_show_link_enable')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_edit_link_read_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_edit_link_read">{{trans('main.is_edit_link_read')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_edit_link_read') is-invalid @enderror"
                       type="checkbox"
                       name="is_edit_link_read"
                       placeholder=""
                       @if ((old('is_edit_link_read') ?? ($role->is_edit_link_read ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_edit_link_read')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_edit_link_update_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_edit_link_update">{{trans('main.is_edit_link_update')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_edit_link_update') is-invalid @enderror"
                       type="checkbox"
                       name="is_edit_link_update"
                       placeholder=""
                       {{--                       "$role->is_edit_link_update ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_edit_link_update') ?? ($role->is_edit_link_update ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_edit_link_update')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_hier_base_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_hier_base_enable">{{trans('main.is_hier_base_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_hier_base_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_hier_base_enable"
                       placeholder=""
                       {{--                       "$role->is_hier_base_enable ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_hier_base_enable') ?? ($role->is_hier_base_enable ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_hier_base_enable')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_hier_link_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_hier_link_enable">{{trans('main.is_hier_link_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_hier_link_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_hier_link_enable"
                       placeholder=""
                       {{--                       "$role->is_hier_link_enable ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_hier_link_enable') ?? ($role->is_hier_link_enable ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_hier_link_enable')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_edit_email_base_create_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_edit_email_base_create">{{trans('main.is_edit_email_base_create')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_edit_email_base_create') is-invalid @enderror"
                       type="checkbox"
                       name="is_edit_email_base_create"
                       placeholder=""
                       {{--                       "$role->is_edit_email_base_create ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_edit_email_base_create') ?? ($role->is_edit_email_base_create ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_edit_email_base_create')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_edit_email_question_base_create_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_edit_email_question_base_create">{{trans('main.is_edit_email_question_base_create')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_edit_email_question_base_create') is-invalid @enderror"
                       type="checkbox"
                       name="is_edit_email_question_base_create"
                       placeholder=""
                       {{--                       "$role->is_edit_email_question_base_create ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_edit_email_question_base_create') ?? ($role->is_edit_email_question_base_create ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_edit_email_question_base_create')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_edit_email_base_update_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_edit_email_base_update">{{trans('main.is_edit_email_base_update')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_edit_email_base_update') is-invalid @enderror"
                       type="checkbox"
                       name="is_edit_email_base_update"
                       placeholder=""
                       {{--                       "$role->is_edit_email_base_update ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_edit_email_base_update') ?? ($role->is_edit_email_base_update ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_edit_email_base_update')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_edit_email_question_base_update_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_edit_email_question_base_update">{{trans('main.is_edit_email_question_base_update')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_edit_email_question_base_update') is-invalid @enderror"
                       type="checkbox"
                       name="is_edit_email_question_base_update"
                       placeholder=""
                       {{--                       "$role->is_edit_email_question_base_update ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_edit_email_question_base_update') ?? ($role->is_edit_email_question_base_update ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_edit_email_question_base_update')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_show_email_base_delete_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_show_email_base_delete">{{trans('main.is_show_email_base_delete')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_show_email_base_delete') is-invalid @enderror"
                       type="checkbox"
                       name="is_show_email_base_delete"
                       placeholder=""
                       {{--                       "$role->is_show_email_base_delete ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_show_email_base_delete') ?? ($role->is_show_email_base_delete ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_show_email_base_delete')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_show_email_question_base_delete_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_show_email_question_base_delete">{{trans('main.is_show_email_question_base_delete')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_show_email_question_base_delete') is-invalid @enderror"
                       type="checkbox"
                       name="is_show_email_question_base_delete"
                       placeholder=""
                       {{--                       "$role->is_show_email_question_base_delete ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_show_email_question_base_delete') ?? ($role->is_show_email_question_base_delete ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_show_email_question_base_delete')
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
                        @include('layouts.role.previous_url')
                    >
                        <i class="fas fa-arrow-left d-inline"></i>
                        {{trans('main.cancel')}}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
