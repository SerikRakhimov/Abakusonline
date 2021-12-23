@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Item;
    use App\Http\Controllers\GlobalController;
    use App\Http\Controllers\ProjectController;
    // https://ru.coredump.biz/questions/41704091/laravel-file-uploads-failing-when-file-size-is-larger-than-2mb
    //phpinfo(); - для поиска php.ini
    $acc_check = ProjectController::acc_check($project, $role);
    $is_request = $acc_check['is_request'];
    $is_ask = $acc_check['is_ask'];
    $is_subs = $acc_check['is_subs'];
    $is_delete = $acc_check['is_delete'];
    $is_num_request = $is_request ? 1 : 0;
    $is_num_ask = $is_ask ? 1 : 0;
    ?>
    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role])
    @auth
        @if ($role->is_author())
            @if ($project->is_calculated_base_exist() == true)
                <div class="col-12 text-right">
                    <a href="{{route('project.calculate_bases_start', ['project'=>$project, 'role'=>$role])}}"
                       title="{{trans('main.calculate_bases')}}">
                        {{trans('main.calculate_bases')}}
                    </a>
                </div>
            @endif
        @endif
    @endauth
    <div class="container-fluid">
        <div class="row">
            <div class="col-5 text-center">
{{--                <h3>{{trans('main.bases')}}</h3>--}}
                <h3>{{trans('main.mainmenu')}}</h3>
            </div>
            <div class="col-2">
            </div>
            <div class="col-5 text-right">
            </div>
        </div>
    </div>
    </p>
    <table class="table table-sm table-bordered table-hover">
        <caption>{{trans('main.select_record_for_work')}}</caption>
        <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="text-left">{{trans('main.names')}}</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = $bases->firstItem() - 1;
        ?>
        @foreach($bases as $base)
            <?php
            $base_right = GlobalController::base_right($base, $role);
            ?>
            @if($base_right['is_list_base_calc'] == true)
                <?php
                $i++;
                $message = GlobalController::base_maxcount_message($base);
                if ($message != '') {
                    // Такая же проверка в GlobalController::item_right() и start.php
                    if ($base_right['is_list_base_create'] == true) {
                        $message = ' (' . $message . ')';
                    } else {
                        $message = '';
                    }
                }
                ?>
                <tr>
                    {{--                                    <th scope="row">{{$i}}</th>--}}
                    <td class="text-center"><h5>
                            <a
                                href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role])}}"
                                title="{{$base->names()}}">
                                {{$i}}
                            </a></h5></td>
                    <td class="text-left">
                        <h5>
                            <a
                                href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role])}}"
                                title="{{$base->names() . $message}}">
                                {{$base->names()}}
                                {{--                            @auth--}}
{{--                                <span--}}
{{--                                    class="text-muted text-related">--}}
{{--                                    {{GlobalController::items_right($base, $project, $role)['view_count']}}--}}
{{--                                </span>--}}
                            </a>
                            <?php
                            $menu_type_name = $base->menu_type_name();
                            ?>
                            <a
                                href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role])}}"
                                title="{{$menu_type_name['text']}}">
                                <span class="badge badge-related"><?php
                                    echo $menu_type_name['icon'];
                                    ?></span>
                                {{--                            @endauth--}}
                            </a>
                        </h5>
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
    {{$bases->links()}}




    {{--    <h3 class="text-center">Справочники</h3><br>--}}

    {{--    <div class="card-deck">--}}
    {{--        <?php--}}
    {{--        $i = $bases->firstItem() - 1;--}}
    {{--        ?>--}}
    {{--        @foreach($bases as $base)--}}
    {{--            <?php--}}
    {{--            $base_right = GlobalController::base_right($base, $role);--}}
    {{--            ?>--}}
    {{--            @if($base_right['is_list_base_calc'] == true)--}}
    {{--                <?php--}}
    {{--                $i++;--}}
    {{--                ?>--}}
    {{--                <div class="card shadow">--}}
    {{--                    --}}{{--                                        <p class="card-header text-center">{{$i}}</p>--}}
    {{--                    <div class="card-body">--}}
    {{--                        <h5 class="card-title text-center">--}}
    {{--                            <a--}}
    {{--                                href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role])}}"--}}
    {{--                                title="{{$base->names()}}">--}}
    {{--                                {{$base->names()}}--}}

    {{--                                <small class="text-related">--}}
    {{--                                    {{GlobalController::items_right($base, $project, $role)['view_count']}}--}}
    {{--                                </small>--}}
    {{--                            </a>--}}
    {{--                        </h5>--}}
    {{--                    </div>--}}
    {{--                    --}}{{--                    <div class="card-footer text-center">--}}
    {{--                    --}}{{--                        <small class="text-muted">--}}
    {{--                    --}}{{--                            {{GlobalController::items_right($base, $project, $role)['view_count']}}--}}
    {{--                    --}}{{--                        </small>--}}
    {{--                    --}}{{--                    </div>--}}
    {{--                </div>--}}
    {{--            @endif--}}
    {{--        @endforeach--}}
    {{--        {{$bases->links()}}--}}

    {{--    </div>--}}

    @if(Auth::check())
        <small>
            {{trans('main.current_status')}}: <span
                class="text-title">{{ProjectController::current_status($project, $role)}}</span>
        </small>
        @if($is_subs == true)
            <button type="button" class="btn btn-sm btn-dreamer" title="{{trans('main.subscribe')}}"
                    onclick="document.location='{{route('project.subs_create',
                        ['is_request' => $is_num_request, 'project'=>$project, 'role'=>$role])}}'">
                <i class="fas fa-book-open d-inline"></i>&nbsp;{{trans('main.subscribe')}}
            </button>
        @endif
        @if($is_delete == true)
            <button type="button" class="btn btn-sm btn-dreamer" title="{{trans('main.delete_subscription')}}"
                    onclick="document.location='{{route('project.subs_delete',
                        [ 'project'=>$project, 'role'=>$role])}}'">
                <i class="fas fa-trash"></i>&nbsp;{{trans('main.delete_subscription')}}
            </button>
        @endif
    @endif

    @if(1==2)
        <?php
        $i = $bases->firstItem() - 1;
        ?>
        <div class="row">
            <div class="col-2">
            </div>
            <div class="col-8">
                <ul class="list-group">
                    @foreach($bases as $base)
                        <?php
                        $base_right = GlobalController::base_right($base, $role);
                        ?>
                        @if($base_right['is_list_base_calc'] == true)
                            <?php
                            $i++;
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center listgroup">
                                <h5 class="card-title text-center">
                                    <a
                                        href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role])}}"
                                        title="{{$base->names()}}">
                                        {{$base->names()}}
                                    </a>
                                </h5>
                                <span
                                    class="badge badge-related badge-pill">{{GlobalController::items_right($base, $project, $role)['view_count']}}</span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
        {{$bases->links()}}
    @endif

    <blockquote class="text-title pt-1 pl-5 pr-5"><?php echo nl2br($project->dc_ext()); ?></blockquote>
    {{--    <blockquote class="text-title pt-1 pl-5 pr-5"><?php echo nl2br($project->dc_int()); ?></blockquote>--}}
@endsection

