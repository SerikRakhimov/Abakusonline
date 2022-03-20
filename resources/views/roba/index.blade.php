@extends('layouts.app')

@section('content')
    <?php
    use App\Http\Controllers\GlobalController;
    use App\Models\User;
    $is_role = isset($role);
    $is_base = isset($base);
    $roba_show = "";
    if ($is_role == true) {
        $roba_show = "roba.show_role";
    }
    if ($is_base == true) {
        $roba_show = "roba.show_base";
    }
    ?>
    <p>
    @if($is_role)
        @include('layouts.role.show_name',['role'=>$role])
    @endif
    @if($is_base)
        @include('layouts.base.show_name',['base'=>$base])
    @endif
    <div class="container-fluid">
        <div class="row">
            <div class="col-5 text-center">
                <h3>{{trans('main.robas')}}</h3>
            </div>
            <div class="col-2">
            </div>
            <div class="col-5 text-right">
                <button type="button" class="btn btn-dreamer" title="{{trans('main.add')}}"
                        onclick="document.location=
                        @if($is_role)
                            '{{route('roba.create_role', ['role'=>$role])}}'
                            ">
                    @endif
                    @if($is_base)
                        '{{route('roba.create_base', ['base'=>$base])}}'
                        ">
                    @endif <i class="fas fa-plus d-inline"></i>
                    {{trans('main.add')}}
                </button>
            </div>
        </div>
    </div>
    </p>
    <table class="table table-sm table-bordered table-hover">
        <caption>{{trans('main.select_record_for_work')}}</caption>
        <thead>
        <tr>
            <th class="text-center">#</th>
            @if(!$is_role)
                <th class="text-left">{{trans('main.role')}}</th>
            @endif
            @if(!$is_base)
                <th class="text-left">{{trans('main.template')}}</th>
                <th class="text-left">{{trans('main.base')}}</th>
            @endif
        </tr>
        </thead>
        <tbody>
        <?php
        $i = $robas->firstItem() - 1;
        ?>
        @foreach($robas as $roba)
            <?php
            $i++;
            ?>
            <tr>
                <td class="text-center">
                    <a href="{{route($roba_show, $roba)}}" title="{{trans('main.show')}}">
                        {{$i}}
                    </a></td>
                @if(!$is_role)
                    <td class="text-left">
                        <a href="{{route($roba_show, $roba)}}" title="{{trans('main.show')}}">
                            {{$roba->role->name()}}
                        </a>
                    </td>
                @endif
                @if(!$is_base)
                    <td class="text-left">
                        <a href="{{route($roba_show, $roba)}}" title="{{trans('main.show')}}">
                            {{GlobalController::get_template_name_from_relit_id($roba->relit_id, $role->template_id)}}
                        </a>
                    </td>
                    <td class="text-left">
                        <a href="{{route($roba_show, $roba)}}" title="{{trans('main.show')}}">
                            {{$roba->base->name()}}
                        </a>
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$robas->links()}}
@endsection

