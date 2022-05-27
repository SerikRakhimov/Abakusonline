@extends('layouts.app')

@section('content')

    <?php
    use App\Http\Controllers\GlobalController;
    use App\Http\Controllers\BaseController;
    use Illuminate\Support\Facades\Request;
    use App\Models\User;
    ?>
    <p>
        @include('layouts.template.show_name', ['template'=>$template])
        @include('layouts.form_show_title', ['type_form'=>$type_form, 'table_name'=>trans('main.role')])
    </p>

    <p>Id: <b>{{$role->id}}</b></p>
    <p>{{trans('main.serial_number')}}: <b>{{$role->serial_number}}</b></p>

    @foreach (config('app.locales') as $key=>$value)
        <p>{{trans('main.name')}} ({{trans('main.' . $value)}}): <b>{{$role['name_lang_' . $key]}}</b></p>
    @endforeach

    @foreach (config('app.locales') as $key=>$value)
{{--        <p>{{trans('main.desc')}} ({{trans('main.' . $value)}}): <b>{{$role['desc_lang_' . $key]}}</b></p>--}}
        <p>{{trans('main.desc')}} ({{trans('main.' . $value)}}): <b><?php echo nl2br($role['desc_lang_' . $key]); ?></b></p>
    @endforeach

    <p>{{trans('main.is_author')}}: <b>{{GlobalController::name_is_boolean($role->is_author)}}</b></p>
    <p>{{trans('main.is_default_for_external')}}: <b>{{GlobalController::name_is_boolean($role->is_default_for_external)}}</b></p>
    <p>{{trans('main.is_list_base_relits')}}: <b>{{GlobalController::name_is_boolean($role->is_list_base_relits)}}</b></p>
    <p>{{trans('main.is_read_base_relits')}}: <b>{{GlobalController::name_is_boolean($role->is_read_base_relits)}}</b></p>
    <p>{{trans('main.is_view_info_relits')}}: <b>{{GlobalController::name_is_boolean($role->is_view_info_relits)}}</b></p>
    <p>{{trans('main.is_list_base_sndbt')}}: <b>{{GlobalController::name_is_boolean($role->is_list_base_sndbt)}}</b></p>
    <p>{{trans('main.is_list_base_id')}}: <b>{{GlobalController::name_is_boolean($role->is_list_base_id)}}</b></p>
    <p>{{trans('main.is_list_base_calculated')}}: <b>{{GlobalController::name_is_boolean($role->is_list_base_calculated)}}</b></p>
    <p>{{trans('main.is_list_base_setup')}}: <b>{{GlobalController::name_is_boolean($role->is_list_base_setup)}}</b></p>
    <p>{{trans('main.is_list_base_relits_setup')}}: <b>{{GlobalController::name_is_boolean($role->is_list_base_relits_setup)}}</b></p>
    <p>{{trans('main.is_all_base_calcname_enable')}}: <b>{{GlobalController::name_is_boolean($role->is_all_base_calcname_enable)}}</b></p>
    <p>{{trans('main.is_list_base_sort_creation_date_desc')}}: <b>{{GlobalController::name_is_boolean($role->is_list_base_sort_creation_date_desc)}}</b></p>
    <p>{{trans('main.is_mnmn_base_enable')}}: <b>{{GlobalController::name_is_boolean($role->is_mnmn_base_enable)}}</b></p>
    <p>{{trans('main.is_list_base_create')}}: <b>{{GlobalController::name_is_boolean($role->is_list_base_create)}}</b></p>
    <p>{{trans('main.is_list_base_read')}}: <b>{{GlobalController::name_is_boolean($role->is_list_base_read)}}</b></p>
    <p>{{trans('main.is_list_base_update')}}: <b>{{GlobalController::name_is_boolean($role->is_list_base_update)}}</b></p>
    <p>{{trans('main.is_list_base_delete')}}: <b>{{GlobalController::name_is_boolean($role->is_list_base_delete)}}</b></p>
    <p>{{trans('main.is_list_base_used_delete')}}: <b>{{GlobalController::name_is_boolean($role->is_list_base_used_delete)}}</b></p>
    <p>{{trans('main.is_list_base_byuser')}}: <b>{{GlobalController::name_is_boolean($role->is_list_base_byuser)}}</b></p>
    <p>{{trans('main.is_list_link_baselink')}}: <b>{{GlobalController::name_is_boolean($role->is_list_link_baselink)}}</b></p>
    <p>{{trans('main.is_edit_base_read')}}: <b>{{GlobalController::name_is_boolean($role->is_edit_base_read)}}</b></p>
    <p>{{trans('main.is_edit_base_update')}}: <b>{{GlobalController::name_is_boolean($role->is_edit_base_update)}}</b></p>
    <p>{{trans('main.is_list_base_enable')}}: <b>{{GlobalController::name_is_boolean($role->is_list_base_enable)}}</b></p>
    <p>{{trans('main.is_list_link_enable')}}: <b>{{GlobalController::name_is_boolean($role->is_list_link_enable)}}</b></p>
    <p>{{trans('main.is_body_link_enable')}}: <b>{{GlobalController::name_is_boolean($role->is_body_link_enable)}}</b></p>
    <p>{{trans('main.is_show_base_enable')}}: <b>{{GlobalController::name_is_boolean($role->is_show_base_enable)}}</b></p>
    <p>{{trans('main.is_show_link_enable')}}: <b>{{GlobalController::name_is_boolean($role->is_show_link_enable)}}</b></p>
    <p>{{trans('main.is_edit_link_read')}}: <b>{{GlobalController::name_is_boolean($role->is_edit_link_read)}}</b></p>
    <p>{{trans('main.is_edit_link_update')}}: <b>{{GlobalController::name_is_boolean($role->is_edit_link_update)}}</b></p>
    <p>{{trans('main.is_hier_base_enable')}}: <b>{{GlobalController::name_is_boolean($role->is_hier_base_enable)}}</b></p>
    <p>{{trans('main.is_hier_link_enable')}}: <b>{{GlobalController::name_is_boolean($role->is_hier_link_enable)}}</b></p>
    <p>{{trans('main.is_edit_email_base_create')}}: <b>{{GlobalController::name_is_boolean($role->is_edit_email_base_create)}}</b></p>
    <p>{{trans('main.is_edit_email_question_base_create')}}: <b>{{GlobalController::name_is_boolean($role->is_edit_email_question_base_create)}}</b></p>
    <p>{{trans('main.is_edit_email_base_update')}}: <b>{{GlobalController::name_is_boolean($role->is_edit_email_base_update)}}</b></p>
    <p>{{trans('main.is_edit_email_question_base_update')}}: <b>{{GlobalController::name_is_boolean($role->is_edit_email_question_base_update)}}</b></p>
    <p>{{trans('main.is_show_email_base_delete')}}: <b>{{GlobalController::name_is_boolean($role->is_show_email_base_delete)}}</b></p>
    <p>{{trans('main.is_show_email_question_base_delete')}}: <b>{{GlobalController::name_is_boolean($role->is_show_email_question_base_delete)}}</b></p>

    @if ($type_form == 'show')
        <p>
            <button type="button" class="btn btn-dreamer"
                    onclick="document.location='{{route('role.edit',$role)}}'" title="{{trans('main.edit')}}">
                            <i class="fas fa-edit"></i>
                {{trans('main.edit')}}
            </button>
            <button type="button" class="btn btn-dreamer"
                    onclick="document.location='{{route('role.delete_question',$role)}}'"
                    title="{{trans('main.delete')}}">
                            <i class="fas fa-trash"></i>
                {{trans('main.delete')}}
            </button>
        </p>
        <p>
            {{--            Не удалять--}}
{{--            <button type="button" class="btn btn-dreamer" title="{{trans('main.accesses')}}"--}}
{{--                    onclick="document.location='{{route('access.index_role', $role)}}'"--}}
{{--            >--}}
{{--                <i class="fas fa-universal-access"></i>--}}
{{--                {{trans('main.accesses')}}--}}
{{--            </button>--}}
            <button type="button" class="btn btn-dreamer" title="{{trans('main.robas')}}"
                    onclick="document.location='{{route('roba.index_role', $role)}}'"
            >
                <i class="fas fa-ring"></i>
                {{trans('main.robas')}}
            </button>
            <button type="button" class="btn btn-dreamer" title="{{trans('main.rolis')}}"
                    onclick="document.location='{{route('roli.index_role', $role)}}'"
            >
                <i class="fas fa-paperclip"></i>
                {{trans('main.rolis')}}
            </button>
            <button type="button" class="btn btn-dreamer"
                    title="{{trans('main.cancel')}}" @include('layouts.role.previous_url')>
                            <i class="fas fa-arrow-left"></i>
                {{trans('main.cancel')}}
            </button>
        </p>
    @elseif($type_form == 'delete_question')
        <form action="{{route('role.delete', $role)}}" method="POST" id='delete-form'>
            @csrf
            @method('DELETE')
            <p>
                <button type="submit" class="btn btn-danger" title="{{trans('main.delete')}}">
                                    <i class="fas fa-trash"></i>
                    {{trans('main.delete')}}
                </button>
                <button type="button" class="btn btn-dreamer"
                        title="{{trans('main.cancel')}}" @include('layouts.role.previous_url')>
                                    <i class="fas fa-arrow-left"></i>
                    {{trans('main.cancel')}}
                </button>
            </p>
        </form>
    @endif

@endsection
