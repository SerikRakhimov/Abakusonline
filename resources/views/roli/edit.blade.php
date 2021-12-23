@extends('layouts.app')

@section('content')
    <?php
    $update = isset($roli);
    $is_role = isset($role);
    $is_link = isset($link);
    ?>
    <p>
        @if($is_role)
            @include('layouts.role.show_name',['role'=>$role])
        @endif
        @if($is_link)
            @include('layouts.link.show_name',['link'=>$link])
        @endif
    </p>
    <p>
        @include('layouts.form_edit_title', ['update'=>$update, 'table_name'=>trans('main.roli')])
    </p>
    <form action="{{$update ? route('roli.update', $roli):route('roli.store')}}" method="POST"
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
                                    @if ((old('role_id') ?? ($roli->role_id ?? (int) 0)) ==  $role->id)
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

        @if($is_link)
            <input type="hidden" name="link_id" value="{{$link->id}}">
        @else
            <div class="form-group row">
                <div class="col-sm-3 text-right">
                    <label for="link_id" class="col-form-label">{{trans('main.link')}}<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-sm-7">
                    <select class="form-control"
                            name="link_id"
                            id="link_id"
                            class="@error('link_id') is-invalid @enderror">
                        @foreach ($links as $link)
                            <option value="{{$link->id}}"
                                    @if ($update)
                                    @if ((old('link_id') ?? ($roli->link_id ?? (int) 0)) ==  $link->id)
                                    selected
                                @endif
                                @endif
                            >{{$link->name()}}</option>
                        @endforeach
                    </select>
                    @error('link_id')
                    <div class="text-danger">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-sm-2">
                </div>
            </div>
        @endif

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
                       {{--                       "$roli->is_list_link_enable ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_list_link_enable') ?? ($roli->is_list_link_enable ?? true)) ==  true)
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
                       {{--                       "$roli->is_show_link_enable ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_show_link_enable') ?? ($roli->is_show_link_enable ?? true)) ==  true)
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
                       @if ((old('is_edit_link_read') ?? ($roli->is_edit_link_read ?? false)) ==  true)
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
                       id="is_edit_link_update"
                       placeholder=""
{{--                       "$roli->is_edit_link_update ?? true" - "true" значение по умолчанию--}}
                       @if ((old('is_edit_link_update') ?? ($roli->is_edit_link_update ?? true)) ==  true)
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
                       {{--                       "$roli->is_hier_link_enable ?? false" - "false" значение по умолчанию--}}
                       @if ((old('is_hier_link_enable') ?? ($roli->is_hier_link_enable ?? false)) ==  true)
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
                        @include('layouts.roli.previous_url')
                    >
                        <i class="fas fa-arrow-left d-inline"></i>
                        {{trans('main.cancel')}}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
