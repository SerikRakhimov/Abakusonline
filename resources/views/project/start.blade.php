@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Base;
    use App\Models\Item;
    use App\Models\Project;
    use App\Models\Relit;
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
    $i = 0;
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
            <div class="col-1">
            </div>
            <div class="col-6 text-left">
                <h3>{{trans('main.mainmenu')}}</h3>
            </div>
            <div class="col-5 text-right">
            </div>
        </div>
    </div>
    @foreach($array_relips as $relit_id=>$array_relip)
        {{--        <hr>--}}
        <?php
        $relit = null;
        if ($relit_id == 0) {
            $relit = null;
        } else {
            $relit = Relit::findOrFail($relit_id);
        }
        // Находим родительский проект
        $relip_project = Project::findOrFail($array_relip['project_id']);
        $base_ids = $array_relip['base_ids'];
        ?>
        @if($role->is_view_info_relits == true)
            @if($relit_id != 0)
                <div class="row ml-5">
                    <div class="col-12 text-left">
                        {{--                    <small><small>{{trans('main.project')}}: </small></small>--}}
                        <small>{{$relip_project->name()}}</small>
                        <h6>{{$relit->title()}}</h6>
                    </div>
                </div>
            @endif
        @endif
        @foreach($base_ids as $base_id)
            <?php
            $base = Base::findOrFail($base_id);
            ?>
            <?php
            $i++;
            $message = GlobalController::base_maxcount_message($base);
            if ($message != '') {
                // Такая же проверка в GlobalController::items_right() и start.php
                $message = ' (' . $message . ')';
            }
            $base_right = GlobalController::base_right($base, $role, $relit_id);
//          Использовать так "$base->names($base_right, true)", "true" - вызов из base_index.php
            $base_names = $base->names($base_right, true, true);
            ?>
            <div class="row mt-3">
                <div style="width:100%;padding-left: 100px">
                    {{--                    <p style="float:left;width:10%;">--}}
                    <h5 style="float:left;width:5%;">
                        <a
                            href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role, 'relit_id' => $relit_id])}}"
                            title="{{$base_names}}">
                            {{$i}}
                        </a></h5>
{{--                    Нужно--}}
                    <div style="float:left;width:5%;">
                    </div>
                    {{--                    </p>--}}
                    {{--                    <p style="float:left;width:90%;">--}}
                    <h5 style="float:left;width:90%;">
                        <a
                            href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role, 'relit_id' => $relit_id])}}"
                            title="{{$base_names . $message}}">
                            {{$base_names}}
                            {{--                            @auth--}}
                            {{--                                <span--}}
                            {{--                                    class="text-muted text-related">--}}
                            {{--                                    {{GlobalController::items_right($base, $project, $role)['view_count']}}--}}
                            {{--                                </span>--}}
                        </a>
                        <?php
                        // Вывести иконки для вычисляемых основ и настроек
                        $menu_type_name = $base->menu_type_name();
                        ?>
                        <a
                            href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role, 'relit_id' => $relit_id])}}"
                            title="{{$menu_type_name['text']}}">
                                <span class="badge badge-related"><?php
                                    echo $menu_type_name['icon'];
                                    ?></span>
                            {{--                            @endauth--}}
                        </a>
                    </h5>
                    {{--                    </p>--}}
                </div>
            </div>
        @endforeach
    @endforeach

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
    {{--                                href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role, 'relit_id' => 0])}}"--}}
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
                        $base_right = GlobalController::base_right($base, $role, 0);
                        ?>
                        @if($base_right['is_list_base_calc'] == true)
                            <?php
                            $i++;
                            $base_names = $base->names($base_right);
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center listgroup">
                                <h5 class="card-title text-center">
                                    <a
                                        href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role, 'relit_id' => 0])}}"
                                        title="{{$base_names}}">
                                        {{$base_names}}
                                    </a>
                                </h5>
                                {{--                                <span--}}
                                {{--                                    class="badge badge-related badge-pill">{{GlobalController::items_right($base, $project, $role, 0)['view_count']}}</span>--}}
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
        {{$bases->links()}}
    @endif
    @if($project->dc_ext() != "")
        <hr>
        <blockquote class="text-title pt-1 pl-0 pr-0"><?php echo nl2br($project->dc_ext()); ?></blockquote>
        {{--    <blockquote class="text-title pt-1 pl-5 pr-5"><?php echo nl2br($project->dc_int()); ?></blockquote>--}}
    @endif
    {{--    Вывод сведений о подписке--}}
    @if(Auth::check())
        <hr>
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
@endsection

