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

    <h3 class="ml-5 b4-menu-title">{{trans('main.mainmenu')}}</h3>

    <table class="table table-borderless b4-custom-table-menu">
        @foreach($array_relips as $relit_id => $array_relip)
            @php
                $relit = ($relit_id == 0) ? null : Relit::findOrFail($relit_id);
                $relip_project = Project::findOrFail($array_relip['project_id']);
                $base_ids = $array_relip['base_ids'];
                $calc_relip_info = GlobalController::calc_relip_info($project, $role, $relip_project, $relit_id);
            @endphp

            {{-- Заголовок секции меню --}}
            @if($calc_relip_info['proj_relit_total'] != '')
                <tr class="bg-light">
                    <td colspan="2" class="pl-5 py-2 menu-section-info font-weight-bold text-muted border-bottom">
                        @include('layouts.project.show_relip_info',['calc_relip_info'=>$calc_relip_info]):
                    </td>
                </tr>
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

                <tr class="menu-row position-relative">
                    {{-- Левая колонка: Номер без фона --}}
                    <td class="col-2 pl-4 text-center align-middle pr-0">
                    <span class="menu-badge d-inline-flex align-items-center justify-content-center">
                        {{$i}}
                    </span>
                    </td>

                    {{-- Правая колонка: Текст ссылки + Стрелочка --}}
                    <td class="col-10 text-left align-middle menu-text-cell">
                        <a href="{{route('item.base_index',['base'=>$base, 'project' => $project, 'role' => $role, 'relit_id' => $relit_id])}}"
                           title="{{$base_names . $message}}"
                           class="menu-link-block text-truncate d-flex align-items-center justify-content-between">

                            <span class="menu-item-text text-truncate">{{$base_names}}</span>
                            <span class="menu-arrow text-muted font-weight-bold ml-2">&rsaquo;</span>
                        </a>
                    </td>
                </tr>
            @endforeach
        @endforeach
    </table>

    <!-- Кастомные стили -->
    <style>
        .b4-menu-title {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
            font-weight: 700;
        }

        .b4-custom-table-menu {
            border-collapse: separate;
            border-spacing: 0 4px;
        }

        .menu-row {
            background-color: #ffffff;
            transition: background-color 0.15s ease-in-out;
            -webkit-tap-highlight-color: transparent;
        }

        .menu-row td {
            padding-top: 14px !important;
            padding-bottom: 14px !important;
            border-top: 1px solid #f1f3f5 !important;
            border-bottom: 1px solid #f1f3f5 !important;
        }

        .menu-link-block {
            color: #495057;
            text-decoration: none !important;
            width: 100%;
        }

        .menu-item-text {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", Roboto, sans-serif;
            font-size: 1.05rem;
            font-weight: 500;
        }

        /* --- НАСТРОЙКА ЦИФРЫ БЕЗ ФОНА --- */
        .menu-badge {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", Roboto, sans-serif;
            font-size: 1.1rem;      /* Немного увеличили размер, так как без фона цифра кажется меньше */
            font-weight: 600;       /* Сделали полужирной для лучшей видимости */
            color: #6c757d;         /* Исходный цвет цифры (серый) */
            background: none;       /* Полностью убираем фон */
            transition: color 0.15s ease-in-out;
            width: 28px;
            height: 28px;
        }

        .menu-arrow {
            font-size: 1.6rem;
            line-height: 1;
            transition: transform 0.15s ease-in-out, color 0.15s ease-in-out;
        }

        /* Эффекты при наведении (на ПК) */
        @media (hover: hover) {
            .menu-row:hover {
                background-color: #f8f9fa;
            }
            .menu-row:hover .menu-link-block {
                color: #007bff;     /* Цвет названия при наведении (синий) */
            }
            .menu-row:hover .menu-badge {
                color: #007bff;     /* Цвет ЦИФРЫ при наведении также становится синим */
            }
            .menu-row:hover .menu-arrow {
                color: #007bff !important;
                transform: translateX(3px);
            }
        }

        /* Состояние при тапе (на iPhone) */
        .menu-row:active {
            background-color: #f1f3f5;
        }
        .menu-row:active .menu-link-block {
            color: #0056b3;
        }
        .menu-row:active .menu-badge {
            color: #0056b3;         /* Цвет цифры при нажатии на экране смартфона */
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

