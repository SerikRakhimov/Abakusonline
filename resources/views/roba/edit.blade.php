@extends('layouts.app')

@section('content')
    <?php
    $update = isset($roba);
    $is_role = isset($role);
    $is_base = isset($base);
    ?>
    <p>
        @if($is_role)
            @include('layouts.role.show_name',['role'=>$role])
        @endif
        @if($is_base)
            @include('layouts.base.show_name',['base'=>$base])
        @endif
    </p>
    <p>
        @include('layouts.form_edit_title', ['update'=>$update, 'table_name'=>trans('main.roba')])
    </p>
    <form action="{{$update ? route('roba.update', $roba):route('roba.store')}}" method="POST"
          enctype=multipart/form-data name="form">
        @csrf

        @if ($update)
            @method('PUT')
        @endif

        @if($is_role)
            <input type="hidden" name="role_id" value="{{$role->id}}">
        @else
            <div class="form-group row">
                <div class="col-sm-3 text-right">
                    <label for="role_id" class="col-form-label">{{trans('main.role')}}<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-sm-7">
                    <select class="form-control"
                            name="role_id"
                            id="role_id"
                            class="@error('role_id') is-invalid @enderror">
                        @foreach ($roles as $role)
                            <option value="{{$role->id}}"
                                    @if ($update)
                                    @if ((old('role_id') ?? ($roba->role_id ?? (int) 0)) ==  $role->id)
                                    selected
                                @endif
                                @endif
                            >{{$role->name()}}</option>
                        @endforeach
                    </select>
                    @error('role_id')
                    <div class="text-danger">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-sm-2">
                </div>
            </div>
        @endif

        @if($is_base)
            @if($update)
                <input type="hidden" name="relit_id" value="{{$roba->relit_id}}">
            @else
                {{-- Значение по умолчанию - 'value="0"'--}}
                <input type="hidden" name="relit_id" value="0">
            @endif
            <input type="hidden" name="base_id" value="{{$base->id}}">
        @else
            <div class="form-group row">
                <div class="col-sm-3 text-right">
                    <label for="relit_id">{{trans('main.template')}}<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-sm-7">
                    <select class="form-control"
                            name="relit_id"
                            id="relit_id"
                            class="form-control @error('relit_id') is-invalid @enderror">
                        @foreach ($array_relits as $key=>$value)
                            <option value="{{$key}}"
                                    {{--            "(int) 0" нужно--}}
                                    @if ((old('relit_id') ?? ($roba->relit_id ?? (int) 0)) ==  $key)
                                    selected
                                @endif
                            >{{$value}}</option>
                        @endforeach
                    </select>
                    @error('relit_id')
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
                    <label for="base_id" class="col-form-label">{{trans('main.base')}}<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-sm-7">
                    <select class="form-control"
                            name="base_id"
                            id="base_id"
                            class="@error('base_id') is-invalid @enderror">
                        @foreach ($bases as $base_work)
                            <option value="{{$base_work->id}}"
                                    @if ($update)
                                    @if ((old('base_id') ?? ($roba->base_id ?? (int) 0)) ==  $base_work->id)
                                    selected
                                @endif
                                @endif
                            >{{$base_work->name()}}</option>
                        @endforeach
                    </select>
                    @error('base_id')
                    <div class="text-danger">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-sm-2">
                </div>
            </div>
        @endif

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
                       {{--                       "$roba->is_all_base_calcname_enable ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_all_base_calcname_enable') ?? ($roba->is_all_base_calcname_enable ?? false)) ==  true)
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
                       {{--                       "$roba->is_list_base_sort_creation_date_desc ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_list_base_sort_creation_date_desc') ?? ($roba->is_list_base_sort_creation_date_desc ?? false)) ==  true)
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

        <div class="form-group row" id="is_bsin_base_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_bsin_base_enable">{{trans('main.is_bsin_base_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_bsin_base_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_bsin_base_enable"
                       placeholder=""
                       @if ((old('is_bsin_base_enable') ?? ($roba->is_bsin_base_enable ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_bsin_base_enable')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_exclude_related_records_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_exclude_related_records">{{trans('main.is_exclude_related_records')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_exclude_related_records') is-invalid @enderror"
                       type="checkbox"
                       name="is_exclude_related_records"
                       placeholder=""
                       @if ((old('is_exclude_related_records') ?? ($roba->is_exclude_related_records ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_exclude_related_records')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_show_head_attr_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_show_head_attr_enable">{{trans('main.is_show_head_attr_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_show_head_attr_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_show_head_attr_enable"
                       placeholder=""
                       @if ((old('is_show_head_attr_enable') ?? ($roba->is_show_head_attr_enable ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_show_head_attr_enable')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_view_prev_next_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_view_prev_next">{{trans('main.is_view_prev_next')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_view_prev_next') is-invalid @enderror"
                       type="checkbox"
                       name="is_view_prev_next"
                       placeholder=""
                       @if ((old('is_view_prev_next') ?? ($roba->is_view_prev_next ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_view_prev_next')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_skip_count_records_equal_1_base_index_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_skip_count_records_equal_1_base_index">{{trans('main.is_skip_count_records_equal_1_base_index')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_skip_count_records_equal_1_base_index') is-invalid @enderror"
                       type="checkbox"
                       name="is_skip_count_records_equal_1_base_index"
                       placeholder=""
                       @if ((old('is_skip_count_records_equal_1_base_index') ?? ($roba->is_skip_count_records_equal_1_base_index ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_skip_count_records_equal_1_base_index')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_skip_count_records_equal_1_item_body_index_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_skip_count_records_equal_1_item_body_index">{{trans('main.is_skip_count_records_equal_1_item_body_index')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_skip_count_records_equal_1_item_body_index') is-invalid @enderror"
                       type="checkbox"
                       name="is_skip_count_records_equal_1_item_body_index"
                       placeholder=""
                       @if ((old('is_skip_count_records_equal_1_item_body_index') ?? ($roba->is_skip_count_records_equal_1_item_body_index ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_skip_count_records_equal_1_item_body_index')
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
                       {{--                       "$roba->is_list_base_create ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_list_base_create') ?? ($roba->is_list_base_create ?? true)) ==  true)
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
                       @if ((old('is_list_base_read') ?? ($roba->is_list_base_read ?? false)) ==  true)
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
                       {{--                       "$roba->is_list_base_update ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_list_base_update') ?? ($roba->is_list_base_update ?? true)) ==  true)
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
                       {{--                       "$roba->is_list_base_delete ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_list_base_delete') ?? ($roba->is_list_base_delete ?? true)) ==  true)
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
                       {{--                       "$roba->is_list_base_used_delete ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_list_base_used_delete') ?? ($roba->is_list_base_used_delete ?? false)) ==  true)
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

        <div class="form-group row" id="is_list_base_user_id_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_base_user_id">{{trans('main.is_list_base_user_id')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_base_user_id') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_base_user_id"
                       placeholder=""
                       @if ((old('is_list_base_user_id') ?? ($roba->is_list_base_user_id ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_base_user_id')
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
                       @if ((old('is_list_base_byuser') ?? ($roba->is_list_base_byuser ?? false)) ==  true)
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
                       @if ((old('is_edit_base_read') ?? ($roba->is_edit_base_read ?? false)) ==  true)
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
                       {{--                       "$roba->is_edit_base_update ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_edit_base_update') ?? ($roba->is_edit_base_update ?? true)) ==  true)
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
                       @if ((old('is_list_base_enable') ?? ($roba->is_list_base_enable ?? true)) ==  true)
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
                       {{--                       "$roba->is_list_link_enable ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_list_link_enable') ?? ($roba->is_list_link_enable ?? true)) ==  true)
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

        <div class="form-group row" id="is_body_link_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_body_link_enable">{{trans('main.is_body_link_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_body_link_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_body_link_enable"
                       placeholder=""
                       {{--                       "$roba->is_body_link_enable ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_body_link_enable') ?? ($roba->is_body_link_enable ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_body_link_enable')
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
                       {{--                       "$roba->is_show_base_enable ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_show_base_enable') ?? ($roba->is_show_base_enable ?? true)) ==  true)
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
                       {{--                       "$roba->is_show_link_enable ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_show_link_enable') ?? ($roba->is_show_link_enable ?? true)) ==  true)
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
                       @if ((old('is_edit_link_read') ?? ($roba->is_edit_link_read ?? false)) ==  true)
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
                       {{--                       "$roba->is_edit_link_update ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_edit_link_update') ?? ($roba->is_edit_link_update ?? true)) ==  true)
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
                       {{--                       "$roba->is_hier_base_enable ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_hier_base_enable') ?? ($roba->is_hier_base_enable ?? false)) ==  true)
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

        <div class="form-group row" id="is_tst_lst_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_tst_lst">{{trans('main.is_tst_lst')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_tst_lst') is-invalid @enderror"
                       type="checkbox"
                       name="is_tst_lst"
                       placeholder=""
                       {{--                       "$roba->is_tst_lst ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_tst_lst') ?? ($roba->is_tst_lst ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_tst_lst')
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
                       {{--                       "$roba->is_hier_link_enable ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_hier_link_enable') ?? ($roba->is_hier_link_enable ?? false)) ==  true)
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

        <div class="form-group row" id="is_show_hist_attr_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_show_hist_attr_enable">{{trans('main.is_show_hist_attr_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_show_hist_attr_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_show_hist_attr_enable"
                       placeholder=""
                       {{--                       "$roba->is_show_hist_attr_enable ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_show_hist_attr_enable') ?? ($roba->is_show_hist_attr_enable ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_show_hist_attr_enable')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_edit_hist_attr_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_edit_hist_attr_enable">{{trans('main.is_edit_hist_attr_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_edit_hist_attr_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_edit_hist_attr_enable"
                       placeholder=""
                       {{--                       "$roba->is_edit_hist_attr_enable ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_edit_hist_attr_enable') ?? ($roba->is_edit_hist_attr_enable ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_edit_hist_attr_enable')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_hist_attr_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_hist_attr_enable">{{trans('main.is_list_hist_attr_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_hist_attr_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_hist_attr_enable"
                       placeholder=""
                       {{--                       "$roba->is_list_hist_attr_enable ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_list_hist_attr_enable') ?? ($roba->is_list_hist_attr_enable ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_hist_attr_enable')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_list_hist_records_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_list_hist_records_enable">{{trans('main.is_list_hist_records_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_list_hist_records_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_list_hist_records_enable"
                       placeholder=""
                       {{--                       "$roba->is_list_hist_records_enable ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_list_hist_records_enable') ?? ($roba->is_list_hist_records_enable ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_list_hist_records_enable')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_brow_hist_attr_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_brow_hist_attr_enable">{{trans('main.is_brow_hist_attr_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_brow_hist_attr_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_brow_hist_attr_enable"
                       placeholder=""
                       {{--                       "$roba->is_brow_hist_attr_enable ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_brow_hist_attr_enable') ?? ($roba->is_brow_hist_attr_enable ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_brow_hist_attr_enable')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_brow_hist_records_enable_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_brow_hist_records_enable">{{trans('main.is_brow_hist_records_enable')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_brow_hist_records_enable') is-invalid @enderror"
                       type="checkbox"
                       name="is_brow_hist_records_enable"
                       placeholder=""
                       {{--                       "$roba->is_brow_hist_records_enable ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_brow_hist_records_enable') ?? ($roba->is_brow_hist_records_enable ?? true)) ==  true)
                       checked
                    @endif
                >
                @error('is_brow_hist_records_enable')
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
                       {{--                       "$roba->is_edit_email_base_create ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_edit_email_base_create') ?? ($roba->is_edit_email_base_create ?? false)) ==  true)
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
                       {{--                       "$roba->is_edit_email_question_base_create ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_edit_email_question_base_create') ?? ($roba->is_edit_email_question_base_create ?? false)) ==  true)
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
                       {{--                       "$roba->is_edit_email_base_update ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_edit_email_base_update') ?? ($roba->is_edit_email_base_update ?? false)) ==  true)
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
                       {{--                       "$roba->is_edit_email_question_base_update ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_edit_email_question_base_update') ?? ($roba->is_edit_email_question_base_update ?? false)) ==  true)
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
                       {{--                       "$roba->is_show_email_base_delete ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_show_email_base_delete') ?? ($roba->is_show_email_base_delete ?? false)) ==  true)
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
                       {{--                       "$roba->is_show_email_question_base_delete ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_show_email_question_base_delete') ?? ($roba->is_show_email_question_base_delete ?? false)) ==  true)
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
                        <i class="fas fa-save"></i>
                        {{trans('main.add')}}
                        @else
                            title="{{trans('main.save')}}">
                            <i class="fas fa-save"></i>
                            {{trans('main.save')}}
                        @endif
                    </button>
                </div>
                <div class="col-2">
                </div>
                <div class="col-5 text-left">
                    <button type="button" class="btn btn-dreamer" title="{{trans('main.cancel')}}"
                        @include('layouts.roba.previous_url')
                    >
                        <i class="fas fa-arrow-left"></i>
                        {{trans('main.cancel')}}
                    </button>
                </div>
            </div>
        </div>
    </form>
    <script>
        var relit_id = document.getElementById('relit_id');
        var base_id = document.getElementById('base_id');

        // Изменение relit_id
        function relit_id_changeOption(box) {
            axios.get('/global/get_bases_from_relit_id/'
                + relit_id.options[relit_id.selectedIndex].value
                + '/{{$template->id}}'
            ).then(function (res) {
                // если запуск функции не при загрузке страницы
                if (res.data['bases_options'] == "") {
                   base_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + relit_id.options[relit_id.selectedIndex].text + '"!</option>';
                } else {
                    base_id.innerHTML = res.data['bases_options'];
                }
                // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                @if ($update)  // при корректировке записи
                for (let i = 0; i < base_id.length; i++) {
                    // если элемент списка = текущему значению из базы данных
                    if (base_id[i].value == {{$roba->base_id}}) {
                        // установить selected на true
                        base_id[i].selected = true;
                    }
                }
                @endif
            });
        }

        relit_id.addEventListener("change", relit_id_changeOption);

        window.onload = function () {
            relit_id_changeOption(true);
        };

    </script>
@endsection
