@extends('layouts.app')

@section('content')
    <?php
    use App\Http\Controllers\GlobalController;
    use App\Models\User;
    $is_role = isset($role);
    $is_link = isset($link);
    $roli_show = "";
    if ($is_role == true) {
        $roli_show = "roli.show_role";
    }
    if ($is_link == true) {
        $roli_show = "roli.show_link";
    }
    ?>
    <p>
    @if($is_role)
        @include('layouts.role.show_name',['role'=>$role])
    @endif
    @if($is_link)
        @include('layouts.link.show_name',['link'=>$link])
    @endif
    <div class="container-fluid">
        <div class="row">
            <div class="col-5 text-center">
                <h3>{{trans('main.rolis')}}</h3>
            </div>
            <div class="col-2">
            </div>
            <div class="col-5 text-right">
                 <button type="button" class="btn btn-dreamer" title="{{trans('main.add')}}"
                        onclick="document.location=
                        @if($is_role)
                            '{{route('roli.create_role', ['role'=>$role])}}'
                            ">
                    @endif
                    @if($is_link)
                        '{{route('roli.create_link', ['link'=>$link])}}'
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
            @if(!$is_link)
                <th class="text-left">{{trans('main.template')}}</th>
                <th class="text-left">{{trans('main.link')}}</th>
            @endif
        </tr>
        </thead>
        <tbody>
        <?php
        $i = $rolis->firstItem() - 1;
        ?>
        @foreach($rolis as $roli)
            <?php
            $i++;
            ?>
            <tr>
                <td class="text-center">
                    <a href="{{route($roli_show, $roli)}}" title="{{trans('main.show')}}">
                        {{$i}}
                    </a></td>
                @if(!$is_role)
                    <td class="text-left">
                        <a href="{{route($roli_show, $roli)}}" title="{{trans('main.show')}}">
                            {{$roli->role->name()}}
                        </a>
                    </td>
                @endif
                @if(!$is_link)
                    <td class="text-left">
                        <a href="{{route($roli_show, $roli)}}" title="{{trans('main.show')}}">
                            {{GlobalController::get_template_from_relit_id($roli->relit_id, $roli->role->template_id)['template_name']}}
                        </a>
                    </td>
                    <td class="text-left">
                        <a href="{{route($roli_show, $roli)}}" title="{{trans('main.show')}}">
                            {{$roli->link->name()}}
                        </a>
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$rolis->links()}}
@endsection

