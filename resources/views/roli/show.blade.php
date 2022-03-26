@extends('layouts.app')

@section('content')

    <?php
    use App\Http\Controllers\GlobalController;
    use App\Http\Controllers\LinkController;
    use Illuminate\Support\Facades\Request;
    use App\Models\User;
    $is_role = isset($role);
    $is_link = isset($link);
    $roli_edit = "";
    if ($is_role == true) {
        $roli_edit = "roli.edit_role";
    }
    if ($is_link == true) {
        $roli_edit = "roli.edit_link";
    }
    ?>
    <p>
        @if($is_role)
            @include('layouts.role.show_name',['role'=>$role])
        @endif
            @if($is_link)
                @include('layouts.link.show_name',['link'=>$link])
            @endif
        @include('layouts.form_show_title', ['type_form'=>$type_form, 'table_name'=>trans('main.roli')])
    </p>

    <p>Id: <b>{{$roli->id}}</b></p>

    @if(!$is_role)
        <p>{{trans('main.role')}}: <b>{{$roli->role->name()}}</b></p>
    @endif
    @if(!$is_link)
        <p>{{trans('main.template')}}: <b>{{GlobalController::get_parent_template_from_relit_id($roli->relit_id, $roli->role->template_id)['template_name']}}</b></p>
        <p>{{trans('main.link')}}: <b>{{$roli->link->name()}}</b></p>
    @endif
    <p>{{trans('main.is_list_link_enable')}}: <b>{{GlobalController::name_is_boolean($roli->is_list_link_enable)}}</b></p>
    <p>{{trans('main.is_show_link_enable')}}: <b>{{GlobalController::name_is_boolean($roli->is_show_link_enable)}}</b></p>
    <p>{{trans('main.is_edit_link_read')}}: <b>{{GlobalController::name_is_boolean($roli->is_edit_link_read)}}</b></p>
    <p>{{trans('main.is_edit_link_update')}}: <b>{{GlobalController::name_is_boolean($roli->is_edit_link_update)}}</b></p>
    <p>{{trans('main.is_hier_link_enable')}}: <b>{{GlobalController::name_is_boolean($roli->is_hier_link_enable)}}</b></p>
    @if ($type_form == 'show')
{{--        @if (Auth::user()->isAdmin() ||!(($is_user == true) && ($roli->role->is_default_for_external == false)))--}}
            <p>
                <button type="button" class="btn btn-dreamer"
                        onclick="document.location='{{route($roli_edit,$roli)}}'"
                        title="{{trans('main.edit')}}">
                    <i class="fas fa-edit"></i>
                    {{trans('main.edit')}}
                </button>
                <button type="button" class="btn btn-dreamer"
                        onclick="document.location='{{route('roli.delete_question',$roli)}}'"
                        title="{{trans('main.delete')}}">
                    <i class="fas fa-trash"></i>
                    {{trans('main.delete')}}
                </button>
            </p>
{{--        @endif--}}
        <p>
            <button type="button" class="btn btn-dreamer"
                    title="{{trans('main.cancel')}}" @include('layouts.roli.previous_url')>
                <i class="fas fa-arrow-left"></i>
                {{trans('main.cancel')}}
            </button>
        </p>
    @elseif($type_form == 'delete_question')
        <form action="{{route('roli.delete', $roli)}}" method="POST" id='delete-form'>
            @csrf
            @method('DELETE')
            <p>
                <button type="submit" class="btn btn-danger" title="{{trans('main.delete')}}">
                    <i class="fas fa-trash"></i>
                    {{trans('main.delete')}}
                </button>
                <button type="button" class="btn btn-dreamer"
                        title="{{trans('main.cancel')}}" @include('layouts.roli.previous_url')>
                    <i class="fas fa-arrow-left"></i>
                    {{trans('main.cancel')}}
                </button>
            </p>
        </form>
    @endif

@endsection
