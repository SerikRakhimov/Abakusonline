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
            {{--            @if ($project->is_calculated_base_exist() == true)--}}
            {{--                <div class="col-12 text-right">--}}
            {{--                    <a href="{{route('project.calculate_bases_start', ['project'=>$project, 'role'=>$role])}}"--}}
            {{--                       title="{{trans('main.calculate_bases')}}">--}}
            {{--                        {{trans('main.calculate_bases')}}--}}
            {{--                    </a>--}}
            {{--                </div>--}}
            {{--            @endif--}}
            @if ((new ProjectController)->is_exist_calculate_bases($project) == true)
                <div class="col-12 text-right">
                    <a href="{{route('project.calculate_bases_start', ['project'=>$project, 'role'=>$role])}}"
                       title="{{trans('main.calculate_bases')}}">
                        {{trans('main.calculate_bases')}}
                    </a>
                </div>
            @endif
        @endif
    @endauth
    {{--        ----------------------------------}}
    {{--        Первый вариант, не удалять--}}
{{--    <h3 class="ml-5">{{trans('main.mainmenu')}}</h3>--}}
{{--    <table class="table">--}}
{{--        @foreach($array_relips as $relit_id=>$array_relip)--}}
{{--            --}}{{--        <hr>--}}
{{--            <?php--}}
{{--            $relit = null;--}}
{{--            if ($relit_id == 0) {--}}
{{--                $relit = null;--}}
{{--            } else {--}}
{{--                $relit = Relit::findOrFail($relit_id);--}}
{{--            }--}}




{{--            // Находим родительский проект--}}
{{--            // Использовать Project::findOrFail(), а не Project::find()--}}
{{--            $relip_project = Project::findOrFail($array_relip['project_id']);--}}
{{--            $base_ids = $array_relip['base_ids'];--}}
{{--            $calc_relip_info = GlobalController::calc_relip_info($project, $role, $relip_project, $relit_id);--}}
{{--            ?>--}}
{{--            --}}{{--        @if($role->is_view_info_relits == true)--}}
{{--            --}}{{--            @if($relit_id != 0)--}}
{{--            --}}{{--                <div class="row ml-5">--}}
{{--            --}}{{--                    <div class="col-12 text-left">--}}
{{--            --}}{{--                        --}}{{----}}{{--                    <small><small>{{trans('main.project')}}: </small></small>--}}
{{--            --}}{{--                        <small>{{$relip_project->name()}}</small>--}}
{{--            --}}{{--                        <h6>{{$relit->title()}}</h6>--}}
{{--            --}}{{--                    </div>--}}
{{--            --}}{{--                </div>--}}
{{--            --}}{{--            @endif--}}
{{--            --}}{{--        @endif--}}
{{--            @if($calc_relip_info['proj_relit_total'] != '')--}}
{{--                --}}{{--                    <div class="row ml-5">--}}
{{--                --}}{{--                        <div class="col-12 text-left">--}}
{{--                --}}{{--                @include('layouts.project.show_relip_info',['calc_relip_info'=>$calc_relip_info])--}}
{{--                --}}{{--                        </div>--}}
{{--                --}}{{--                    </div>--}}
{{--                <tr>--}}
{{--                    <td colspan="2" class="pl-5">--}}
{{--                        --}}{{--                        <br>--}}
{{--                        @include('layouts.project.show_relip_info',['calc_relip_info'=>$calc_relip_info]):--}}
{{--                    </td>--}}
{{--                </tr>--}}
{{--            @endif--}}
{{--            --}}{{--        <table class="table">--}}
{{--            @foreach($base_ids as $base_id)--}}
{{--                <?php--}}
{{--                $base = Base::findOrFail($base_id);--}}
{{--                ?>--}}
{{--                <?php--}}
{{--                $i++;--}}
{{--                $message = GlobalController::base_maxcount_message($base);--}}
{{--                if ($message != '') {--}}
{{--                    // Такая же проверка в GlobalController::items_right() и start.php--}}
{{--                    $message = ' (' . $message . ')';--}}
{{--                }--}}
{{--                $base_right = GlobalController::base_right($base, $role, $relit_id);--}}
{{--                //          Использовать так "$base->names($base_right, true)", "true" - вызов из base_index.php--}}
{{--                $base_names = $base->names($base_right, true, true, true);--}}
{{--                ?>--}}
{{--                <tr>--}}
{{--                    --}}{{--                    <td class="col-3 text-right">--}}
{{--                    <td class="col-2 pl-4 text-center">--}}
{{--                        --}}{{--                        <h5>--}}
{{--                        <big>--}}
{{--                            <a href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role, 'relit_id' => $relit_id])}}"--}}
{{--                               title="{{$base_names}}">--}}
{{--                                {{$i}}--}}
{{--                            </a>--}}
{{--                        </big>--}}
{{--                        --}}{{--                        </h5>--}}
{{--                    </td>--}}
{{--                    --}}{{--                    <td class="col-9 text-left">--}}
{{--                    <td class="col-10 text-left">--}}
{{--                        --}}{{--                        <h5>--}}
{{--                        <big>--}}
{{--                            <a--}}
{{--                                href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role, 'relit_id' => $relit_id])}}"--}}
{{--                                title="{{$base_names . $message}}">--}}
{{--                                {{$base_names}}--}}
{{--                                --}}{{--                            @auth--}}
{{--                                --}}{{--                                <span--}}
{{--                                --}}{{--                                    class="text-muted text-related">--}}
{{--                                --}}{{--                                    {{GlobalController::items_right($base, $project, $role)['view_count']}}--}}
{{--                                --}}{{--                                </span>--}}
{{--                            </a>--}}
{{--                        </big>--}}
{{--                        --}}{{--                            <?php--}}
{{--                        --}}{{--                            // Вывести иконки для вычисляемых основ и настроек--}}
{{--                        --}}{{--                            $menu_type_name = $base->menu_type_name();--}}
{{--                        --}}{{--                            ?>--}}
{{--                        --}}{{--                            <a--}}
{{--                        --}}{{--                                href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role, 'relit_id' => $relit_id])}}"--}}
{{--                        --}}{{--                                title="{{$menu_type_name['text']}}">--}}
{{--                        --}}{{--                                <span class="badge badge-related"><?php--}}
{{--                        --}}{{--                                    echo $menu_type_name['icon'];--}}
{{--                        --}}{{--                                    ?></span>--}}
{{--                        --}}{{--                                --}}{{----}}{{--                            @endauth--}}
{{--                        --}}{{--                            </a>--}}
{{--                        --}}{{--                        </h5>--}}
{{--                    </td>--}}
{{--                </tr>--}}
{{--            @endforeach--}}
{{--            --}}{{--        </table>--}}
{{--        @endforeach--}}
{{--    </table>--}}
    {{--        ------------------------------------------------}}
    {{--        Второй вариант--}}
    <div class="container-fluid px-0">
        <h3 class="ml-5 b4-menu-title mb-4">{{trans('main.mainmenu')}}</h3>

        <div class="b4-custom-grid-menu">
            @foreach($array_relips as $relit_id => $array_relip)
                @php
                    $relit = ($relit_id == 0) ? null : Relit::findOrFail($relit_id);
                    $relip_project = Project::findOrFail($array_relip['project_id']);
                    $base_ids = $array_relip['base_ids'];
                    $calc_relip_info = GlobalController::calc_relip_info($project, $role, $relip_project, $relit_id);
                @endphp

                {{-- Заголовок секции меню с новыми точными отступами --}}
                @if($calc_relip_info['proj_relit_total'] != '')
                    <div class="row mx-0 header-row">
                        <!-- Заменили pl-5 на кастомный класс custom-header-pos, убрали py-2 -->
                        <div class="col-12 custom-header-pos font-weight-bold text-muted small text-uppercase tracking-wider">
                            @include('layouts.project.show_relip_info',['calc_relip_info'=>$calc_relip_info]):
                        </div>
                    </div>
                @endif

                {{-- Рендеринг пунктов меню --}}
                @foreach($base_ids as $base_id)
                    @php
                        $base = Base::findOrFail($base_id);
                        $i++;
                        $message = GlobalController::base_maxcount_message($base);
                        if ($message != '') {
                            $message = ' (' . $message . ')';
                        }
                        $base_right = GlobalController::base_right($base, $role, $relit_id);
                        $base_names = $base->names($base_right, true, true, true);
                    @endphp

                    <a href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role, 'relit_id' => $relit_id])}}"
                       title="{{$base_names . $message}}"
                       class="row mx-2 menu-row text-decoration-none align-items-center">

                        {{-- Левая колонка: Номер --}}
                        <div class="col-2 pl-4 pr-0 text-center text-nowrap d-flex justify-content-center">
                        <span class="menu-badge">
                            {{$i}}
                        </span>
                        </div>

                        {{-- Правая колонка: Текст пункта --}}
                        <div class="col-10 text-left d-flex align-items-center pr-4">
                            <span class="menu-item-text text-truncate">{{$base_names}}</span>
                        </div>
                    </a>
                @endforeach
            @endforeach
        </div>
    </div>

    <!-- Стили -->
    <style>
        .b4-menu-title {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .b4-custom-grid-menu {
            margin-bottom: 35px;
        }

        /* --- ТОЧНОЕ ПОЛОЖЕНИЕ ЗАГОЛОВКА СЕКЦИИ --- */
        .header-row {
            background-color: transparent !important;
        }

        .custom-header-pos {
            font-size: 0.75rem;
            letter-spacing: 0.5px;

            /* Сдвиг правее: 2.8rem идеально выравнивает текст по левой границе цифр ниже */
            padding-left: 3.5rem !important;

            /* Сдвиг ниже: уменьшаем расстояние до первого пункта меню */
            padding-top: 10px !important;
            padding-bottom: 2px !important;
        }

        /* СТИЛИЗАЦИЯ ИНТЕРФЕЙСА */
        .menu-row {
            background-color: transparent;
            padding-top: 12px;
            padding-bottom: 12px;
            margin-bottom: 6px;
            border-radius: 10px;
            -webkit-tap-highlight-color: transparent;
            cursor: pointer;
            display: flex !important;
            transition: background-color 0.2s ease-in-out;
        }

        /* ЦИФРА */
        .menu-badge {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", Roboto, sans-serif;
            font-size: 1.05rem;
            font-weight: 600;
            color: #8a929a;
            background: none;
            display: inline-block;
            text-align: center;
            width: 35px;
            transition: color 0.2s ease-in-out;
        }

        /* ТЕКСТ ПУНКТА */
        .menu-item-text {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", Roboto, sans-serif;
            font-size: 1.05rem;
            font-weight: 500;
            color: #343a40;
            text-align: left !important;
            display: block;
            transition: color 0.2s ease-in-out;
        }

        /* Эффекты при наведении */
        @media (hover: hover) {
            .menu-row:hover {
                background-color: rgba(0, 123, 255, 0.05) !important;
            }
            .menu-row:hover .menu-item-text,
            .menu-row:hover .menu-badge {
                color: #007bff;
            }
        }

        /* Тач на устройствах */
        .menu-row:make-active,
        .menu-row:active {
            background-color: rgba(0, 123, 255, 0.1) !important;
        }
        .menu-row:active .menu-item-text,
        .menu-row:active .menu-badge {
            color: #0056b3;
        }
    </style>








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
        <blockquote class="text-title pt-2 pl-5 pr-5"><?php echo nl2br($project->dc_ext()); ?></blockquote>
        {{--    <blockquote class="text-title pt-1 pl-5 pr-5"><?php echo nl2br($project->dc_int()); ?></blockquote>--}}
    @endif
    {{--    Вывод сведений о подписке--}}
    @if(Auth::check())
        <hr>
        <div class="pl-5">
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
                <button type="button" class="btn btn-sm btn-dreamer"
                        title="{{trans('main.delete_subscription')}}"
                        onclick="document.location='{{route('project.subs_delete',
                        [ 'project'=>$project, 'role'=>$role])}}'">
                    <i class="fas fa-trash"></i>&nbsp;{{trans('main.delete_subscription')}}
                </button>
            @endif
        </div>
    @endif
@endsection

