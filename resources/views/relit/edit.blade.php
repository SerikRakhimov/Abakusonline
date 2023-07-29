@extends('layouts.app')

@section('content')
    <?php
    $update = isset($relit);
    ?>
    <p>
        @include('layouts.template.show_name', ['template'=>$child_template])
    </p>
    <p>
        @include('layouts.form_edit_title', ['update'=>$update, 'table_name'=>trans('main.relit')])
    </p>
    <form action="{{$update ? route('relit.update',$relit):route('relit.store')}}" method="POST"
          enctype=multipart/form-data>
        @csrf

        @if ($update)
            @method('PUT')
        @endif
        <input type="hidden" name="child_template_id" value="{{$child_template->id}}">

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
                       value="{{ old('serial_number') ?? ($relit->serial_number ?? '0') }}">
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
            <div class="col-sm-3 text-right">
                <label for="parent_template_id" class="col-form-label">{{trans('main.parent')}}_{{trans('main.template')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-7">
                <select class="form-control"
                        name="parent_template_id"
                        id="parent_template_id"
                        class="@error('parent_template_id') is-invalid @enderror">
                    @foreach ($templates as $template)
                        <option value="{{$template->id}}"
                                @if ($update)
                                {{--                            "(int) 0" нужно--}}
                                @if ((old('parent_template_id') ?? ($relit->parent_template_id ?? (int) 0)) ==  $template->id)
                                selected
                            @endif
                            @endif
                        >{{$template->name()}}</option>
                    @endforeach
                </select>
                @error('parent_template_id')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row">
            @foreach (config('app.locales') as $key=>$value)
                <div class="col-sm-3 text-right">
                    <label for="parent_title_lang_{{$key}}" class="col-form-label">{{trans('main.parent')}}_{{trans('main.title')}}
                        ({{trans('main.' . $value)}})<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-sm-7">
                    <input type="text"
                           name="parent_title_lang_{{$key}}"
                           id="parent_title_lang_{{$key}}"
                           class="form-control @error('parent_title_lang_' . $key) is-invalid @enderror"
                           placeholder=""
                           value="{{ old('parent_title_lang_' . $key) ?? ($relit['parent_title_lang_' . $key] ?? '') }}">
                </div>
                @error('parent_title_lang_' . $key)
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            @endforeach
        </div>

        <div class="form-group row" id="parent_is_required_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="parent_is_required">{{trans('main.parent')}}_{{trans('main.is_required')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('parent_is_required') is-invalid @enderror"
                       type="checkbox"
                       name="parent_is_required"
                       placeholder=""
                       {{--                       "$relit->parent_is_required ?? false" - "false" значение по умолчанию--}}
                       @if ((old('parent_is_required') ?? ($relit->parent_is_required ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('parent_is_required')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="parent_is_use_current_project_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="parent_is_use_current_project">{{trans('main.parent')}}_{{trans('main.is_use_current_project')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('parent_is_use_current_project') is-invalid @enderror"
                       type="checkbox"
                       name="parent_is_use_current_project"
                       placeholder=""
                       {{--                       "$relit->parent_is_use_current_project ?? false" - "false" значение по умолчанию--}}
                       @if ((old('parent_is_use_current_project') ?? ($relit->parent_is_use_current_project ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('parent_is_use_current_project')
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
                        @include('layouts.relit.previous_url')
                    >
                        <i class="fas fa-arrow-left d-inline"></i>
                        {{trans('main.cancel')}}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
