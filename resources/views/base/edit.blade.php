@extends('layouts.app')

@section('content')
    <?php
    $update = isset($base);
    ?>

    <p>
        @include('layouts.template.show_name', ['template'=>$template])
    </p>
    <p>
        @include('layouts.form_edit_title', ['update'=>$update, 'table_name'=>trans('main.base')])
    </p>

    <form action="{{$update ? route('base.update',$base):route('base.store')}}" method="POST"
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
                       value="{{ old('serial_number') ?? ($base->serial_number ?? '0') }}">
                @error('serial_number')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-7">
            </div>
        </div>

        {{--    в единственном числе--}}
        <div class="form-group row">
            @foreach (config('app.locales') as $key=>$value)
                <div class="col-sm-3 text-right">
                    <label for="name_lang_{{$key}}" class="col-form-label">{{trans('main.name')}}
                        ({{trans('main.' . $value)}})<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-sm-7">
                    <input type="text"
                           name="name_lang_{{$key}}"
                           id="name_lang_{{$key}}"
                           class="form-control @error('name_lang_' . $key) is-invalid @enderror"
                           placeholder=""
                           value="{{ old('name_lang_' . $key) ?? ($base['name_lang_' . $key] ?? '') }}">
                </div>
                @error('name_lang_' . $key)
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            @endforeach
        </div>

        {{--    во множественном числе--}}
        <div class="form-group row">
            @foreach (config('app.locales') as $key=>$value)
                <div class="col-sm-3 text-right">
                    <label for="names_lang_{{$key}}" class="col-form-label">{{trans('main.names')}}
                        ({{trans('main.' . $value)}})<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-sm-7">
                    <input type="text"
                           name="names_lang_{{$key}}"
                           id="names_lang_{{$key}}"
                           class="form-control @error('names_lang_' . $key) is-invalid @enderror"
                           placeholder=""
                           value="{{ old('names_lang_' . $key) ?? ($base['names_lang_' . $key] ?? '') }}">
                    @error('names_lang_' . $key)
                    <div class="text-danger">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-sm-2">
                </div>
            @endforeach
        </div>

        <div class="form-group row">
            <div class="col-sm-3 text-right">
                <label for="vartype" class="col-form-label">{{trans('main.type')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-7">
                <select class="form-control"
                        name="vartype"
                        id="vartype"
                        class="@error('vartype') is-invalid @enderror">
                    @foreach ($types as $key=>$value)
                        <option value="{{$key}}"
                                @if ($update)
                                {{--            "(int) 0" нужно--}}
                                @if ((old('vartype') ?? ($key ?? (int) 0)) ==  $base->type())
                                selected
                            @endif
                            @endif
                        >{{$value}}</option>
                    @endforeach
                </select>
                @error('vartype')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_calculated_lst_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_calculated_lst">{{trans('main.is_calculated_lst')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_calculated_lst') is-invalid @enderror"
                       type="checkbox"
                       name="is_calculated_lst"
                       id="linkis_calculated_lst"
                       placeholder=""
                       @if ((old('is_calculated_lst') ?? ($base->is_calculated_lst ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_calculated_lst')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_setup_lst_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_setup_lst">{{trans('main.is_setup_lst')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_setup_lst') is-invalid @enderror"
                       type="checkbox"
                       name="is_setup_lst"
                       id="linkis_setup_lst"
                       placeholder=""
                       @if ((old('is_setup_lst') ?? ($base->is_setup_lst ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_setup_lst')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_required_lst_num_str_txt_img_doc_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_required_lst_num_str_txt_img_doc">{{trans('main.is_required_lst_num_str_txt_img_doc')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_required_lst_num_str_txt_img_doc') is-invalid @enderror"
                       type="checkbox"
                       name="is_required_lst_num_str_txt_img_doc"
                       id="linkis_required_lst_num_str_txt_img_doc"
                       placeholder=""
                       @if ((old('is_required_lst_num_str_txt_img_doc') ?? ($base->is_required_lst_num_str_txt_img_doc ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_required_lst_num_str_txt_img_doc')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="maxcount_lst_form_group">
            <div class="col-sm-3 text-right">
                <label for="maxcount_lst">{{trans('main.maxcount_lst')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-2">
                <input type="number"
                       name="maxcount_lst"
                       id="maxcount_lst"
                       class="form-control @error('maxcount_lst') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('maxcount_lst') ?? ($base['maxcount_lst'] ?? '0') }}">
                @error('maxcount_lst')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-7">
            </div>
        </div>

        <div class="form-group row" id="length_txt_form_group">
            <div class="col-sm-3 text-right">
                <label for="length_txt">{{trans('main.length_txt')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-2">
                <input type="number"
                       name="length_txt"
                       id="length_txt"
                       class="form-control @error('length_txt') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('length_txt') ?? ($base['length_txt'] ?? '0') }}"
                       min="0"
                       max="10000">
                @error('length_txt')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-7">
            </div>
        </div>

        <div class="form-group row" id="digits_num_form_group">
            <div class="col-sm-3 text-right">
                <label for="digits_num">{{trans('main.digits_num')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-2">
                <input type="number" min="0" max="9"
                       name="digits_num"
                       id="digits_num"
                       class="form-control @error('digits_num') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('digits_num') ?? ($base['digits_num'] ?? '0') }}">
                @error('digits_num')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-7">
            </div>
        </div>

        <div class="form-group row" id="is_to_moderate_image_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_to_moderate_image">{{trans('main.is_to_moderate_image')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_to_moderate_image') is-invalid @enderror"
                       type="checkbox"
                       name="is_to_moderate_image"
                       id="linkis_to_moderate_image"
                       placeholder=""
                       @if ((old('is_to_moderate_image') ?? ($base->is_to_moderate_image ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_to_moderate_image')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="maxfilesize_img_doc_form_group">
            <div class="col-sm-3 text-right">
                <label for="maxfilesize_img_doc">{{trans('main.maxfilesize_img_doc')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-2">
                <input type="number"
                       name="maxfilesize_img_doc"
                       id="maxfilesize_img_doc"
                       class="form-control @error('maxfilesize_img_doc') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('maxfilesize_img_doc') ?? ($base['maxfilesize_img_doc'] ?? '0') }}">
                @error('maxfilesize_img_doc')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-7">
            </div>
        </div>

        <div class="form-group row" id="maxfilesize_title_img_doc_form_group">
            <div class="col-sm-3 text-right">
                <label for="maxfilesize_title_img_doc">{{trans('main.maxfilesize_title_img_doc')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-2">
                <input type="text"
                       name="maxfilesize_title_img_doc"
                       id="maxfilesize_title_img_doc"
                       class="form-control @error('maxfilesize_title_img_doc') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('maxfilesize_title_img_doc') ?? ($base['maxfilesize_title_img_doc'] ?? '') }}">
                @error('maxfilesize_title_img_doc')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-7">
            </div>
        </div>

        <div class="form-group row" id="is_code_needed_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_code_needed">{{trans('main.is_code_needed')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_code_needed') is-invalid @enderror"
                       type="checkbox"
                       name="is_code_needed"
                       id="linkis_code_needed"
                       placeholder=""
                       @if ((old('is_code_needed') ?? ($base->is_code_needed ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_code_needed')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_code_number_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_code_number">{{trans('main.is_code_number')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_code_number') is-invalid @enderror"
                       type="checkbox"
                       name="is_code_number"
                       id="linkis_code_number"
                       placeholder=""
                       @if ((old('is_code_number') ?? ($base->is_code_number ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_code_number')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_limit_sign_code_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_limit_sign_code">{{trans('main.is_limit_sign_code')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_limit_sign_code') is-invalid @enderror"
                       type="checkbox"
                       name="is_limit_sign_code"
                       id="linkis_limit_sign_code"
                       placeholder=""
                       @if ((old('is_limit_sign_code') ?? ($base->is_limit_sign_code ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_limit_sign_code')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="significance_code_form_group">
            <div class="col-sm-3 text-right">
                <label for="significance_code">{{trans('main.significance_code')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-2">
                <input type="number" min="0" max="15"
                       name="significance_code"
                       id="significance_code"
                       class="form-control @error('significance_code') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('significance_code') ?? ($base['significance_code'] ?? '0') }}">
                @error('significance_code')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-7">
            </div>
        </div>

        <div class="form-group row" id="is_code_zeros_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_code_zeros">{{trans('main.is_code_zeros')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_code_zeros') is-invalid @enderror"
                       type="checkbox"
                       name="is_code_zeros"
                       id="linkis_code_zeros"
                       placeholder=""
                       @if ((old('is_code_zeros') ?? ($base->is_code_zeros ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_code_zeros')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_suggest_code_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_suggest_code">{{trans('main.is_suggest_code')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_suggest_code') is-invalid @enderror"
                       type="checkbox"
                       name="is_suggest_code"
                       id="linkis_suggest_code"
                       placeholder=""
                       @if ((old('is_suggest_code') ?? ($base->is_suggest_code ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_suggest_code')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_suggest_max_code_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_suggest_max_code">{{trans('main.is_suggest_max_code')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_suggest_max_code') is-invalid @enderror"
                       type="checkbox"
                       name="is_suggest_max_code"
                       id="linkis_suggest_max_code"
                       placeholder=""
                       @if ((old('is_suggest_max_code') ?? ($base->is_suggest_max_code ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_suggest_max_code')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_recalc_code_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_recalc_code">{{trans('main.is_recalc_code')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_recalc_code') is-invalid @enderror"
                       type="checkbox"
                       name="is_recalc_code"
                       id="linkis_recalc_code"
                       placeholder=""
                       @if ((old('is_recalc_code') ?? ($base->is_recalc_code ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_recalc_code')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_one_value_lst_str_txt_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_one_value_lst_str_txt">{{trans('main.is_one_value_lst_str_txt')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_one_value_lst_str_txt') is-invalid @enderror"
                       type="checkbox"
                       name="is_one_value_lst_str_txt"
                       id="linkis_one_value_lst_str_txt"
                       placeholder=""
                       @if ((old('is_one_value_lst_str_txt') ?? ($base->is_one_value_lst_str_txt ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_one_value_lst_str_txt')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_calcname_lst_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_calcname_lst">{{trans('main.is_calcname_lst')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_calcname_lst') is-invalid @enderror"
                       type="checkbox"
                       name="is_calcname_lst"
                       id="linkis_calcname_lst"
                       placeholder=""
                       @if ((old('is_calcname_lst') ?? ($base->is_calcname_lst ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_calcname_lst')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="sepa_calcname_form_group">
            <div class="col-sm-3 text-right">
                <label for="sepa_calcname">{{trans('main.sepa_calcname')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-2">
                <input type="text"
                       name="sepa_calcname"
                       id="sepa_calcname"
                       class="form-control @error('sepa_calcname') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('sepa_calcname') ?? ($base['sepa_calcname'] ?? ',') }}">
                @error('sepa_calcname')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-7">
            </div>
        </div>

        <div class="form-group row" id="is_same_small_calcname_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_same_small_calcname">{{trans('main.is_same_small_calcname')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_same_small_calcname') is-invalid @enderror"
                       type="checkbox"
                       name="is_same_small_calcname"
                       id="linkis_same_small_calcname"
                       placeholder=""
                       @if ((old('is_same_small_calcname') ?? ($base->is_same_small_calcname ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_same_small_calcname')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="sepa_same_left_calcname_form_group">
            <div class="col-sm-3 text-right">
                <label for="sepa_same_left_calcname">{{trans('main.sepa_same_left_calcname')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-2">
                <input type="text"
                       name="sepa_same_left_calcname"
                       id="sepa_same_left_calcname"
                       class="form-control @error('sepa_same_left_calcname') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('sepa_same_left_calcname') ?? ($base['sepa_same_left_calcname'] ?? '') }}">
                @error('sepa_same_left_calcname')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-7">
            </div>
        </div>

        <div class="form-group row" id="sepa_same_right_calcname_form_group">
            <div class="col-sm-3 text-right">
                <label for="sepa_same_right_calcname">{{trans('main.sepa_same_right_calcname')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-2">
                <input type="text"
                       name="sepa_same_right_calcname"
                       id="sepa_same_right_calcname"
                       class="form-control @error('sepa_same_right_calcname') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('sepa_same_right_calcname') ?? ($base['sepa_same_right_calcname'] ?? '') }}">
                @error('sepa_same_right_calcname')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-7">
            </div>
        </div>

        <br>

        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-5 text-right">
                    <button type="submit" class="btn btn-dreamer"
                            @if (!$update)
                            title="{{trans('main.add')}}">
                        {{--                    <i class="fas fa-save"></i>--}}
                        {{trans('main.add')}}
                        @else
                            title="{{trans('main.save')}}">
                            {{--                        <i class="fas fa-save"></i>--}}
                            {{trans('main.save')}}
                        @endif
                    </button>
                </div>
                <div class="col-2">
                </div>
                <div class="col-5 text-left">
                    <button type="button" class="btn btn-dreamer" title="{{trans('main.cancel')}}"
                        @include('layouts.base.previous_url')
                    >
                        {{--                    <i class="fas fa-arrow-left"></i>--}}
                        {{trans('main.cancel')}}
                    </button>
                </div>
            </div>
        </div>

    </form>
    <script>
        //var vartype = form.vartype;  // так не работает
        var vartype = document.getElementById('vartype');
        var maxcount = document.getElementById('maxcount_lst_form_group');
        var len_txt = document.getElementById('length_txt_form_group');
        var is_calc = document.getElementById('is_calculated_lst_form_group');
        var is_setup = document.getElementById('is_setup_lst_form_group');
        var digits_num = document.getElementById('digits_num_form_group');
        var is_code_needed = document.getElementById('is_code_needed_form_group');
        var is_code_number = document.getElementById('is_code_number_form_group');
        var is_limit_sign_code = document.getElementById('is_limit_sign_code_form_group');
        var significance_code = document.getElementById('significance_code_form_group');
        var is_code_zeros = document.getElementById('is_code_zeros_form_group');
        var is_suggest_code = document.getElementById('is_suggest_code_form_group');
        var is_suggest_max_code = document.getElementById('is_suggest_max_code_form_group');
        var is_recalc_code = document.getElementById('is_recalc_code_form_group');
        var is_required_lst_num_str_txt_img_doc = document.getElementById('is_required_lst_num_str_txt_img_doc_form_group');
        var is_tomoderate_img = document.getElementById('is_to_moderate_image_form_group');
        var maxfilesize_img_doc = document.getElementById('maxfilesize_img_doc_form_group');
        var maxfilesize_title_img_doc = document.getElementById('maxfilesize_title_img_doc_form_group');
        var is_onevalue_str = document.getElementById('is_one_value_lst_str_txt_form_group');
        var is_calcname_lst = document.getElementById('is_calcname_lst_form_group');
        var sepa_calcname = document.getElementById('sepa_calcname_form_group');
        var is_same_small_calcname = document.getElementById('is_same_small_calcname_form_group');
        var sepa_same_left_calcname = document.getElementById('sepa_same_left_calcname_form_group');
        var sepa_same_right_calcname = document.getElementById('sepa_same_right_calcname_form_group');
        var val_digits_num = "";
        var val_required_num_str = "";
        var val_onevalue_str = "";
        var vartype_value = null;

        function vartype_changeOption(first) {
            // если запуск функции не при загрузке страницы
            if (first != true) {
                // сохранить текущие значения
                vartype_value = vartype.options[vartype.selectedIndex].value;
            }

            // val_digits_num = "none";
            // val_required_num_str = "none";
            // val_onevalue_str = "none";
            //
            // switch (vartype.options[vartype.selectedIndex].value) {
            //     // Число
            //     case "1":
            //         val_digits_num = "block";
            //         val_required_num_str = "block";
            //         break;
            //     // Строка
            //     case "2":
            //         val_required_num_str = "block";
            //         val_onevalue_str = "block";
            //         break;
            // }
            val_maxcount = "hidden";
            val_len_txt = "hidden";
            val_calc = "hidden";
            val_setup = "hidden";
            val_code_needed = "hidden";
            val_limit_sign_code = "hidden";
            val_code_number = "hidden";
            val_significance_code = "hidden";
            val_code_zeros = "hidden";
            val_suggest_code = "hidden";
            val_suggest_max_code = "hidden";
            val_recalc_code = "hidden";
            val_digits_num = "hidden";
            val_required_num_str = "hidden";
            val_tomoderate_img = "hidden";
            val_maxfilesize_img_doc = "hidden";
            val_maxfilesize_title_img_doc = "hidden";
            val_onevalue_str = "hidden";
            val_calcname_lst = "hidden";
            val_sepa_calcname = "hidden";
            val_same_small_calcname = "hidden";
            val_sepa_same_left_calcname = "hidden";
            val_sepa_same_right_calcname = "hidden";

            switch (vartype.options[vartype.selectedIndex].value) {
                // Список
                case "0":
                    val_maxcount = "visible";
                    val_calc = "visible";
                    val_setup = "visible";
                    val_code_needed = "visible";
                    val_limit_sign_code = "visible";
                    val_code_number = "visible";
                    val_significance_code = "visible";
                    val_code_zeros = "visible";
                    val_suggest_code = "visible";
                    val_suggest_max_code = "visible";
                    val_recalc_code = "visible";
                    val_required_num_str = "visible";
                    val_onevalue_str = "visible";
                    val_calcname_lst = "visible";
                    val_sepa_calcname = "visible";
                    val_same_small_calcname = "visible";
                    val_sepa_same_left_calcname = "visible";
                    val_sepa_same_right_calcname = "visible";
                    break;
                // Число
                case "1":
                    val_digits_num = "visible";
                    val_required_num_str = "visible";
                    break;
                // Строка
                case "2":
                    val_required_num_str = "visible";
                    val_onevalue_str = "visible";
                    break;
                // Текст
                case "5":
                    val_required_num_str = "visible";
                    val_len_txt = "visible";
                    val_onevalue_str = "visible";
                    break;
                // Изображение
                case "6":
                    val_required_num_str = "visible";
                    val_tomoderate_img = "visible";
                    val_maxfilesize_img_doc = "visible";
                    val_maxfilesize_title_img_doc = "visible";
                    break;
                // Документ
                case "7":
                    val_required_num_str = "visible";
                    val_maxfilesize_img_doc = "visible";
                    val_maxfilesize_title_img_doc = "visible";
                    break;
            }
            // digits_num.style.display = val_digits_num;
            // is_required_lst_num_str_txt_img_doc.style.display = val_required_num_str;
            // is_onevalue_str.style.display = val_onevalue_str;
            maxcount.style.visibility = val_maxcount;
            len_txt.style.visibility = val_len_txt;
            is_calc.style.visibility = val_calc;
            is_setup.style.visibility = val_setup;
            is_code_needed.style.visibility = val_code_needed;
            is_limit_sign_code.style.visibility = val_limit_sign_code;
            is_code_number.style.visibility = val_code_number;
            significance_code.style.visibility = val_significance_code;
            is_code_zeros.style.visibility = val_code_zeros;
            is_suggest_code.style.visibility = val_suggest_code;
            is_suggest_max_code.style.visibility = val_suggest_max_code;
            is_recalc_code.style.visibility = val_recalc_code;
            digits_num.style.visibility = val_digits_num;
            is_required_lst_num_str_txt_img_doc.style.visibility = val_required_num_str;
            is_tomoderate_img.style.visibility = val_tomoderate_img;
            maxfilesize_img_doc.style.visibility = val_maxfilesize_img_doc;
            maxfilesize_title_img_doc.style.visibility = val_maxfilesize_title_img_doc;
            is_onevalue_str.style.visibility = val_onevalue_str;
            is_calcname_lst.style.visibility = val_calcname_lst;
            sepa_calcname.style.visibility = val_sepa_calcname;
            is_same_small_calcname.style.visibility = val_same_small_calcname;
            sepa_same_left_calcname.style.visibility = val_sepa_same_left_calcname;
            sepa_same_right_calcname.style.visibility = val_sepa_same_right_calcname;
            //     // только если запуск функции при загрузке страницы
            //     if (first == true) {
            //         alert('if = ' + vartype_value);
            //         // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
            //         // при корректировке записи
            //         // child
            //         for (let i = 0; i < vartype.length; i++) {
            //             // если элемент списка = текущему значению из базы данных
            //             if (vartype[i].value ==  vartype_value) {
            //                 // установить selected на true
            //                 vartype[i].selected = true;
            //             }
            //         }
            //     } else {
            //         // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
            //         // child
            //         alert('else = ' + vartype_value);
            //         for (let i = 0; i < vartype.length; i++) {
            //             // если элемент списка = предыдущему(текущему) значению из базы данных
            //             if (vartype[i].value == vartype_value) {
            //                 // установить selected на true
            //                 vartype[i].selected = true;
            //             }
            //         }
            //     }
        }

        vartype.addEventListener("change", vartype_changeOption);

        window.onload = function () {
            vartype_changeOption(true);
        };

    </script>
@endsection
