@extends('layouts.app')

@section('content')

    <?php
    use App\Http\Controllers\BaseController;
    use App\Http\Controllers\GlobalController;
    ?>

    <h3 class="display-5">
        @if ($type_form == 'show')
            {{trans('main.viewing_record')}}
        @elseif($type_form == 'delete_question')
            {{trans('main.delete_record_question')}}?
        @endif
        <span class="text-info">-</span> <span class="text-success">{{trans('main.base')}}</span>
    </h3>
    <br>

    <p>Id: <b>{{$base->id}}</b></p>
    <p>{{trans('main.serial_number')}}: <b>{{$base->serial_number}}</b></p>

    @foreach (config('app.locales') as $key=>$value)
        <p>{{trans('main.name')}} ({{trans('main.' . $value)}}): <b>{{$base['name_lang_' . $key]}}</b></p>
    @endforeach

    @foreach (config('app.locales') as $key=>$value)
        <p>{{trans('main.names')}} ({{trans('main.' . $value)}}): <b>{{$base['names_lang_' . $key]}}</b></p>
    @endforeach

    <p>{{trans('main.type')}}: <b>{{$base->type_name()}}</b></p>
    <p>{{trans('main.is_calculated_lst')}}: <b>{{GlobalController::name_is_boolean($base->is_calculated_lst)}}</b></p>
    <p>{{trans('main.is_setup_lst')}}: <b>{{GlobalController::name_is_boolean($base->is_setup_lst)}}</b></p>
    <p>{{trans('main.is_required_lst_num_str_txt_img_doc')}}: <b>{{GlobalController::name_is_boolean($base->is_required_lst_num_str_txt_img_doc)}}</b></p>
    <p>{{trans('main.maxcount_lst')}}: <b>{{$base->maxcount_lst}}</b></p>
    <p>{{trans('main.length_txt')}}: <b>{{$base->length_txt}}</b></p>
    <p>{{trans('main.is_code_needed')}}: <b>{{GlobalController::name_is_boolean($base->is_code_needed)}}</b></p>
    <p>{{trans('main.is_to_moderate_image')}}: <b>{{GlobalController::name_is_boolean($base->is_to_moderate_image)}}</b></p>
    <p>{{trans('main.is_code_number')}}: <b>{{GlobalController::name_is_boolean($base->is_code_number)}}</b></p>
    <p>{{trans('main.is_limit_sign_code')}}: <b>{{GlobalController::name_is_boolean($base->is_limit_sign_code)}}</b></p>
    <p>{{trans('main.significance_code')}}: <b>{{$base->significance_code}}</b></p>
    <p>{{trans('main.is_code_zeros')}}: <b>{{GlobalController::name_is_boolean($base->is_code_zeros)}}</b></p>
    <p>{{trans('main.is_suggest_code')}}: <b>{{GlobalController::name_is_boolean($base->is_suggest_code)}}</b></p>
    <p>{{trans('main.is_suggest_max_code')}}: <b>{{GlobalController::name_is_boolean($base->is_suggest_max_code)}}</b></p>
    <p>{{trans('main.is_recalc_code')}}: <b>{{GlobalController::name_is_boolean($base->is_recalc_code)}}</b></p>
    <p>{{trans('main.digits_num')}}: <b>{{$base->digits_num}}</b></p>
    <p>{{trans('main.is_one_value_lst_str_txt')}}: <b>{{GlobalController::name_is_boolean($base->is_one_value_lst_str_txt)}}</b></p>
    <p>{{trans('main.is_calcname_lst')}}: <b>{{GlobalController::name_is_boolean($base->is_calcname_lst)}}</b></p>
    <p>{{trans('main.sepa_calcname')}}: <b>{{$base->sepa_calcname}}</b></p>
    <p>{{trans('main.is_same_small_calcname')}}: <b>{{GlobalController::name_is_boolean($base->is_same_small_calcname)}}</b></p>
    <p>{{trans('main.sepa_same_left_calcname')}}: <b>{{$base->sepa_same_left_calcname}}</b></p>
    <p>{{trans('main.sepa_same_right_calcname')}}: <b>{{$base->name_sepa_same_right_calcname}}</b></p>
    <p>{{trans('main.maxfilesize_img_doc')}}: <b>{{$base->maxfilesize_img_doc}}</b></p>
    <p>{{trans('main.maxfilesize_title_img_doc')}}: <b>{{$base->maxfilesize_title_img_doc}}</b></p>
    <p>{{trans('main.date_created')}}: <b>{{$base->created_at}}</b></p>
    <p>{{trans('main.date_updated')}}: <b>{{$base->updated_at}}</b></p>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Library</a></li>
            <li class="breadcrumb-item active" aria-current="page">Data</li>
        </ol>
    </nav>

    <?php
    $result = BaseController::form_tree($base->id);
    echo $result;
    ?>

    <?php
    $result = BaseController::get_array_bases_tree_ul($base->id);
    echo $result;
    ?>

    @if ($type_form == 'show')
        <button type="button" class="btn btn-dreamer" title="{{trans('main.return')}}"
            @include('layouts.base.previous_url')
        >
            {{--                    <i class="fas fa-arrow-left"></i>--}}
            {{trans('main.return')}}
        </button>
    @elseif($type_form == 'delete_question')
        <form action="{{route('base.delete', $base)}}" method="POST" id='delete-form'>
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-primary">{{trans('main.delete')}}</button>
            <button type="button" class="btn btn-dreamer" title="{{trans('main.cancel')}}"
                @include('layouts.base.previous_url')
            >
                {{--                    <i class="fas fa-arrow-left"></i>--}}
                {{trans('main.cancel')}}
            </button>
        </form>
    @endif

@endsection
