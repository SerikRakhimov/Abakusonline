@extends('layouts.app')

@section('content')

    <?php
    use App\Http\Controllers\GlobalController;
    use App\Http\Controllers\MainController;
    use App\Http\Controllers\BaseController;
    use Illuminate\Support\Facades\Request;
    use App\Models\User;
    ?>
    <p>
        @include('layouts.template.show_name', ['template'=>$template])
        @include('layouts.form_show_title', ['type_form'=>$type_form, 'table_name'=>trans('main.set')])
    </p>

    <p>Id: <b>{{$set->id}}</b></p>

    <p>{{trans('main.serial_number')}}: <b>{{$set->serial_number}}</b></p>
    <p>{{trans('main.line_number')}}: <b>{{$set->line_number}}</b></p>
    <p>{{trans('main.link_from')}}: <b>{{$set->link_from->id}} {{$set->link_from->name()}}</b></p>
    <p>{{trans('main.parent')}}_{{trans('main.template')}}: <b>{{GlobalController::get_template_name_from_relit_id($set->relit_to_id, $template->id)}}</b></p>
    <p>{{trans('main.link_to')}}: <b>{{$set->link_to->id}} {{$set->link_to->name()}}</b></p>
    <p>{{trans('main.is_savesets_enabled')}}: <b>{{GlobalController::name_is_boolean($set->is_savesets_enabled)}}</b></p>
    <p>{{trans('main.forwhat')}}: <b>{{$set->forwhat_name()}}</b></p>
    <p>{{trans('main.updaction')}}: <b>{{$set->updaction_name()}}</b></p>
    <p>{{trans('main.is_upd_delete_record_with_zero_value')}}: <b>{{$set->updaction_delete_record_with_zero_value()}}</b></p>

    @if ($type_form == 'show')
        <p>
            <button type="button" class="btn btn-dreamer"
                    onclick="document.location='{{route('set.edit',$set)}}'" title="{{trans('main.edit')}}">
                <i class="fas fa-edit"></i>
                {{trans('main.edit')}}
            </button>
            <button type="button" class="btn btn-dreamer"
                    onclick="document.location='{{route('set.delete_question',$set)}}'"
                    title="{{trans('main.delete')}}">
                <i class="fas fa-trash"></i>
                {{trans('main.delete')}}
            </button>
        </p>
        <p>
            <button type="button" class="btn btn-dreamer"
                    title="{{trans('main.cancel')}}" @include('layouts.set.previous_url')>
                <i class="fas fa-arrow-left"></i>
                {{trans('main.cancel')}}
            </button>
        </p>
    @elseif($type_form == 'delete_question')
        <form action="{{route('set.delete', $set)}}" method="POST" id='delete-form'>
            @csrf
            @method('DELETE')
            <p>
                <button type="submit" class="btn btn-danger" title="{{trans('main.delete')}}">
                    <i class="fas fa-trash"></i>
                    {{trans('main.delete')}}
                </button>
                <button type="button" class="btn btn-dreamer"
                        title="{{trans('main.cancel')}}" @include('layouts.set.previous_url')>
                    <i class="fas fa-arrow-left"></i>
                    {{trans('main.cancel')}}
                </button>
            </p>
        </form>
    @endif

@endsection
