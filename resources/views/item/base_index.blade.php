@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Base;
    use App\Models\Item;
    use App\Models\Link;
    use App\Models\Main;
    use App\Models\Level;
    use \App\Http\Controllers\GlobalController;
    use \App\Http\Controllers\ItemController;
    use \App\Http\Controllers\MainController;
    $relip_project = GlobalController::calc_relip_project($relit_id, $project);
    //    $relip_name_project = '';
    //    if ($relip_project) {
    //        if ($relit_id != 0) {
    //            $relip_name_project = $relip_project->name();
    //        }
    //    }

    $calc_relip_info = GlobalController::calc_relip_info($project, $role, $relip_project, $relit_id);

    $message_bs_calc = ItemController::message_bs_calc($relip_project, $base);
    $message_bs_info = $message_bs_calc['message_bs_info'];
    $message_bs_validate = $message_bs_calc['message_bs_validate'];

    $heading = 0;
    $parent_ret_id = 0;
    $relit_id_par = null;
    $parent_ret_id_par = null;
    if ($heading == 0) {
        $relit_id_par = $relit_id;
        $parent_ret_id_par = $parent_ret_id;
    } else {
        $relit_id_par = $parent_ret_id;
        $parent_ret_id_par = $relit_id;
    }
    $emoji_enable = true;
    //    Config::set('app.display', 'table');
    // Шифровка
    //    $saveurl_add = GlobalController::set_url_save(Request::server('HTTP_REFERER'));
    $saveurl_add = GlobalController::set_url_save(GlobalController::current_path());
    $saveurl_show = $saveurl_add;
    $level_array = $base->level_array();
    ?>
    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-left align-top">
                <h3>
                    @if($message_bs_info != '')
                        <a href="#" title="{{$message_bs_info}}">
                            {{-- Использовать так "$base->names($base_right, true)", "true" - вызов из base_index.php--}}
                            {{$base->names($base_right, true, $emoji_enable, true)}}
                        </a>
                    @else
                        {{-- Использовать так "$base->names($base_right, true)", "true" - вызов из base_index.php--}}
                        {{$base->names($base_right, true, $emoji_enable, true)}}
                    @endif
                </h3>
                @include('layouts.project.show_relip_info',['calc_relip_info'=>$calc_relip_info])
            </div>
        </div>
        {{--        Похожая проверка в ItemController::ext_create() и base_index.php--}}
        @if($base_right['is_list_base_create'] == true)
            <div class="col-12 text-right">
                {{--            Не удалять: используется $message_bs_validate --}}
                @if($message_bs_validate == "")
                    @if($level_array['result'] == true)
                        <div class="dropdown d-inline">
                            <button type="button" class="btn btn-dreamer dropdown-toggle"
                                    data-toggle="dropdown"
                                    title="{{trans('main.add')}}">
                                <i class="fas fa-plus  d-inline"></i>
                                {{trans('main.add')}}
                            </button>
                            <div class="dropdown-menu">
                                {{-- Корректировать все поля формы--}}
                                <a class="dropdown-item" href="{{route('item.ext_create', ['base'=>$base, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
                             'relit_id' => $relit_id_par,
                             'string_current' => $string_current,
                             'heading' =>intval(false),
                             'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                             'parent_ret_id' => $parent_ret_id_par,
                             'view_link'=>GlobalController::const_null(),
                             'saveurl_add' =>$saveurl_add
                             ])}}"
                                   title="{{trans('main.add') . ' '.GlobalController::alf_text()}}">
                                    {{--                                                    'string_all_codes_current' => $string_all_codes_current,--}}
                                    {{--                                                    'string_link_ids_current' => $string_link_ids_current,--}}
                                    {{--                                                    'string_item_ids_current' => $string_item_ids_current,--}}
                                    {{trans('main.add')}} {{GlobalController::alf_text()}}
                                </a>
                                {{-- Цикл по массиву--}}
                                @foreach($level_array['l_arr'] as $level_id)
                                    <?php
                                    $level = Level::find($level_id);
                                    $level_name = '(' . mb_strtolower($level->name()) . ')';
                                    ?>
                                    @if($level)
                                        <a class="dropdown-item" href="{{route('item.ext_create', ['base'=>$base, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
                             'relit_id' => $relit_id_par,
                             'string_current' => $string_current,
                             'heading' =>intval(false),
                             'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                             'parent_ret_id' => $parent_ret_id_par,
                             'view_link'=>GlobalController::const_null(),
                             'saveurl_add' =>$saveurl_add,
                             'level_id' => $level_id
                             ])}}"
                                           title="{{trans('main.add')}} {{$level_name}}">
                                            {{--                                                    'string_all_codes_current' => $string_all_codes_current,--}}
                                            {{--                                                    'string_link_ids_current' => $string_link_ids_current,--}}
                                            {{--                                                    'string_item_ids_current' => $string_item_ids_current,--}}
                                            {{trans('main.add')}} {{$level_name}}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <button type="button" class="btn btn-dreamer"
                                {{--                        Выводится $message_mc--}}
                                title="{{trans('main.add') . $message_bs_info}}"
                                onclick="document.location='{{route('item.ext_create',
                            ['base'=>$base, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
                             'relit_id' => $relit_id_par,
                             'string_current' => $string_current,
                             'heading' =>intval(false),
                             'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                             'parent_ret_id' => $parent_ret_id_par,
                             'view_link'=>GlobalController::const_null(),
                             'saveurl_add' =>$saveurl_add])}}'">
                            {{--                        'string_link_ids_current' => $string_link_ids_current,--}}
                            {{--                        'string_item_ids_current' => $string_item_ids_current,--}}
                            {{--                        'string_all_codes_current' => $string_all_codes_current,--}}
                            <i class="fas fa-plus d-inline"></i>&nbsp;{{trans('main.add')}}
                        </button>
                    @endif
                @endif
            </div>
        @endif
        {{--        Не удалять--}}
        @if(1==1)
            @auth
                @if ($role->is_author())
                    @if ($base->is_calcname_lst == true)
                        <div class="col-12 text-right">
                            <a href="{{route('item.calculate_names', ['base'=>$base, 'project'=>$relip_project, 'relit_id'=>$relit_id_par, 'role'=>$role])}}"
                               title="{{trans('main.calculate_names')}}">
                                <img src="{{Storage::url('calculate_names.png')}}" width="15" height="15"
                                     alt="{{trans('main.calculate_names')}}">
                            </a>
                        </div>
                    @endif
                    @if ($base->is_recalc_code == true)
                        <div class="col-12 text-right">
                            <a href="{{route('item.recalculation_codes',['base'=>$base, 'project'=>$relip_project])}}"
                               title="{{trans('main.recalculation_codes')}}">
                                <img src="{{Storage::url('recalculation_codes.png')}}" width="15" height="15"
                                     alt="{{trans('main.recalculation_codes')}}">
                            </a>
                        </div>
                    @endif
                    <div class="col-12 text-right">
                        <a href="{{route('item.verify_baselink',['base'=>$base, 'project'=>$relip_project])}}"
                           title="{{trans('main.verify_baselink')}}">
                            <img src="{{Storage::url('recalculation_codes.png')}}" width="15" height="15"
                                 alt="{{trans('main.verify_baselink')}}">
                        </a>
                    </div>
                    {{--                @endif--}}
                    {{--                @if (Auth::user()->isAdmin())--}}
                    <div class="col-12 text-right">
                        <a href="{{route('item.verify_number_values')}}" title="{{trans('main.verify_number_values')}}">
                            {{trans('main.verify_number_values')}}
                        </a>
                    </div>
                    <div class="col-12 text-right">
                        <a href="{{route('item.verify_table_texts')}}" title="{{trans('main.verify_table_texts')}}">
                            {{trans('main.verify_table_texts')}}
                        </a>
                    </div>
{{--                        <div class="col-12 text-right">--}}
{{--                            <a href="{{route('project.calculate_all', ['base'=>$base, 'project'=>$project, 'relip_proj'=>$relip_project, 'relit_id'=>$relit_id_par, 'role'=>$role])}}"--}}
{{--                            title="{{trans('main.calculate_all')}}">--}}
{{--                                {{trans('main.calculate_all')}}--}}
{{--                            </a>--}}
{{--                        </div>--}}
                @endif
            @endauth
        @endif
    </div>
    </p>
    <?php
    //$i = $items->firstItem() - 1;
    $i = 0;
    ?>
    <!---->
    {{--            <p>Выберите любимого персонажа:</p>--}}
    {{--            <p><input list="character">--}}
    {{--                <datalist id="character">--}}
    {{--                    <option value="Чебурашка"></option>--}}
    {{--                    <option value="Крокодил Гена"></option>--}}
    {{--                    <option value="Шапокляк"></option>--}}
    {{--                </datalist>--}}
    {{--            </p>--}}

    {{--    <!-- Карточка (border-primary - цвет границ карточки) -->--}}
    {{--    <div class="elements border-info">--}}
    {{--        <!-- Шапка (bg-primary - цвет фона, text-white - цвет текста) -->--}}
    {{--        <div class="elements-header bg-primary text-white">--}}
    {{--            Название панели--}}
    {{--        </div>--}}
    {{--        <!-- Текстовый контент -->--}}
    {{--        <div class="elements-body">--}}
    {{--            <h4 class="elements-title">Заголовок</h4>--}}
    {{--            <p class="elements-text">...</p>--}}
    {{--            <a href="#" class="btn btn-primary">Ссылка</a>--}}
    {{--        </div>--}}
    {{--    </div><!-- Конец карточки -->--}}

    {{--    <!-- Карточка с текстовым контентом и списком -->--}}
    {{--    <div class="elements">--}}
    {{--        <!-- Текстовый контент -->--}}
    {{--        <div class="elements-body">--}}
    {{--            <!-- Текстовое содержимое карточки -->--}}
    {{--        </div>--}}
    {{--        <!-- Список List groups -->--}}
    {{--        <ul class="list-group list-group-flush">--}}
    {{--            <li class="list-group-item">1...</li>--}}
    {{--            <li class="list-group-item">2...</li>--}}
    {{--            <li class="list-group-item">3...</li>--}}
    {{--        </ul>--}}
    {{--    </div><!-- Конец карточки -->--}}

    {{--    <!-- Карточка с шапкой и списком -->--}}
    {{--    <div class="elements">--}}
    {{--        <!-- Шапка (header) карточки -->--}}
    {{--        <div class="elements-header">--}}
    {{--            Шапка карточки--}}
    {{--        </div>--}}
    {{--        <!-- Список List groups -->--}}
    {{--        <ul class="list-group list-group-flush">--}}
    {{--            <li class="list-group-item">1...</li>--}}
    {{--            <li class="list-group-item">2...</li>--}}
    {{--            <li class="list-group-item">3...</li>--}}
    {{--        </ul>--}}
    {{--    </div><!-- Конец карточки -->--}}
    {{--    <!-- Карточка с навигацией (в заголовке) -->--}}
    {{--    <div class="elements">--}}
    {{--        <!-- Шапка с навигацией -->--}}
    {{--        <div class="elements-header">--}}
    {{--            <ul class="nav nav-tabs elements-header-tabs">--}}
    {{--                <li class="nav-item">--}}
    {{--                    <a class="nav-link active" data-toggle="tab" href="#item1">Заказать товар</a>--}}
    {{--                </li>--}}
    {{--                <li class="nav-item">--}}
    {{--                    <a class="nav-link" data-toggle="tab" href="#item2">Справочники</a>--}}
    {{--                </li>--}}
    {{--                <li class="nav-item">--}}
    {{--                    <a class="nav-link disabled" data-toggle="tab" href="#item3">Item 3</a>--}}
    {{--                </li>--}}
    {{--                <li class="nav-item">--}}
    {{--                    <a class="nav-link" data-toggle="tab" href="#item3">Настройки</a>--}}
    {{--                </li>--}}
    {{--            </ul>--}}
    {{--        </div>--}}
    {{--        <!-- Текстовый контент -->--}}
    {{--        <div class="elements-body tab-content">--}}
    {{--            <div class="tab-pane fade show active" id="item1">--}}
    {{--                <a href="#" class="elements-link">Ссылка №1</a>--}}
    {{--            </div>--}}
    {{--            <div class="tab-pane fade" id="item2">--}}
    {{--                <ul class="list-group list-group-flush">--}}
    {{--                    <li class="list-group-item">1...</li>--}}
    {{--                    <li class="list-group-item">2...</li>--}}
    {{--                    <li class="list-group-item">3...</li>--}}
    {{--                </ul>--}}
    {{--            </div>--}}
    {{--            <div class="tab-pane fade" id="item3">--}}
    {{--                Некоторое содержимое для Item 3...--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div><!-- Конец карточки -->--}}
    {{--        Используется 'heading' => $heading'--}}
    {{-- "'view_ret_id'=>0", 0 - текущий проект--}}
    @include('list.table',['base'=>$base, 'links_info'=>$links_info,
                'items'=>$items,
                'its_page'=>$its_page,
                'base_right'=>$base_right, 'item_view'=>true,
                'role'=>$role,
                'relit_id'=>$relit_id,
                'string_all_codes_current' => $string_all_codes_current,
                'string_link_ids_current' => $string_link_ids_current,
                'string_item_ids_current' => $string_item_ids_current,
                'string_relit_ids_current' => $string_relit_ids_current,
                'string_current' => $string_current,
                'string_link_ids_next' => $string_link_ids_current,
                'string_item_ids_next' => $string_item_ids_current,
                'string_relit_ids_next' => $string_relit_ids_current,
                'string_all_codes_next' => $string_all_codes_current,
                'string_next' => $string_current,
                'heading' => $heading,
                'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                'view_link'=>null,
                'view_ret_id'=>0,
                'current_link'=>null, 'parent_item'=>null, 'is_table_body'=>$is_table_body,
                'base_index'=>true, 'item_heading_base'=>false, 'item_body_base'=>false,
                'saveurl_show' =>$saveurl_show,
                        'nolink_id' =>null
                ])
    {{--    @endif--}}
    {{$items->links()}}
    {{--    <blockquote class="text-title pt-2 pl-5 pr-5"><?php echo nl2br($project->dc_ext()); ?></blockquote>--}}
    <blockquote class="text-title pt-1 pl-0 pr-0"><?php echo nl2br($project->dc_int()); ?></blockquote>
    {{--    https://www.w3schools.com/css/css3_object-fit.asp--}}
    {{--    <div style="width:100%;height:400px;">--}}
    {{--        <p style="float:left;width:50%;height:100%;">--}}
    {{--            hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh--}}
    {{--        </p>--}}
    {{--        <p style="float:left;width:50%;height:100%;">--}}
    {{--            hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh;;;;;;;;;;;;;;;; kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk lllllllllllllll--}}
    {{--        </p>--}}
    {{--        </div>--}}
    {{--        <img src="paris.jpg" alt="Paris" style="float:left;width:50%;height:100%;object-fit:cover;">--}}
    {{--    <div class="elements" style="width: 18rem;">--}}
    {{--    <div class="elements shadow" style="width: 100%;">--}}
    {{--        <div class="elements-body">--}}
    {{--            <h5 class="elements-title">Название карточки</h5>--}}
    {{--            <h6 class="elements-subtitle mb-2 text-muted">Подзаголовок карты</h6>--}}
    {{--            <p class="elements-text">Some quick example text to build on the elements title and make up the bulk of the--}}
    {{--                elements's--}}
    {{--                content.--}}
    {{--                лллллллллллллллллллл--}}
    {{--                ддддддддддддддддддд--}}
    {{--                жжжжжжжжжжжжжжжжжжжжжжжжжжжжжжж--}}
    {{--            </p>--}}
    {{--            <a href="#" class="elements-link">Ссылка карты</a>--}}
    {{--            <a href="#" class="elements-link">Другая ссылка</a>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <br>--}}
    {{--    <div class="elements shadow" style="width: 100%;">--}}
    {{--        <div class="elements-body">--}}
    {{--            <h5 class="elements-title">Название карточки</h5>--}}
    {{--            <h6 class="elements-subtitle mb-2 text-muted">Подзаголовок карты</h6>--}}
    {{--            <p class="elements-text">Some quick example text to build on the elements title and make up the bulk of the--}}
    {{--                elements's--}}
    {{--                content.--}}
    {{--                лллллллллллллллллллл--}}
    {{--                ддддддддддддддддддд--}}
    {{--                жжжжжжжжжжжжжжжжжжжжжжжжжжжжжжж--}}
    {{--            </p>--}}
    {{--            <a href="#" class="elements-link">Ссылка карты</a>--}}
    {{--            <a href="#" class="elements-link">Другая ссылка</a>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <br>--}}
    {{--    <div class="elements shadow" style="width: 100%;">--}}
    {{--        <div class="elements-body">--}}
    {{--            <h5 class="elements-title">Название карточки</h5>--}}
    {{--            <h6 class="elements-subtitle mb-2 text-muted">Подзаголовок карты</h6>--}}
    {{--            <p class="elements-text">Some quick example text to build on the elements title and make up the bulk of the--}}
    {{--                elements's--}}
    {{--                content.--}}
    {{--                лллллллллллллллллллл--}}
    {{--                ддддддддддддддддддд--}}
    {{--                жжжжжжжжжжжжжжжжжжжжжжжжжжжжжжж--}}
    {{--            </p>--}}
    {{--            <a href="#" class="elements-link">Ссылка карты</a>--}}
    {{--            <a href="#" class="elements-link">Другая ссылка</a>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="elements shadow ">--}}
    {{--        <div class="elements" style="width: 100%;">--}}
    {{--            <div class="elements-body mt-0">--}}
    {{--                <h5 class="elements-title">Название карточки</h5>--}}
    {{--                <h6 class="elements-subtitle mb-2 text-muted">Подзаголовок карты</h6>--}}
    {{--                <p class="elements-text">Some quick example text to build on the elements title and make up the bulk of the--}}
    {{--                    elements's--}}
    {{--                    content.</p>--}}
    {{--                <a href="#" class="elements-link">Ссылка карты</a>--}}
    {{--                <a href="#" class="elements-link">Другая ссылка</a>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="elements shadow">--}}
    {{--        <div class="elements" style="width: 18rem;">--}}
    {{--            <div class="elements-body">--}}
    {{--                <h5 class="elements-title">Название карточки</h5>--}}
    {{--                <h6 class="elements-subtitle mb-2 text-muted">Подзаголовок карты</h6>--}}
    {{--                <p class="elements-text">Some quick example text to build on the elements title and make up the bulk of the--}}
    {{--                    elements's--}}
    {{--                    content.</p>--}}
    {{--                <a href="#" class="elements-link">Ссылка карты</a>--}}
    {{--                <a href="#" class="elements-link">Другая ссылка</a>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div style="width:100%;">--}}
    {{--        <p style="float:left;width:50%;height:100%;">--}}
    {{--            hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh--}}
    {{--        </p>--}}
    {{--        <p style="float:left;width:50%;height:100%;">--}}
    {{--            hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh;;;;;;;;;;;;;;;; kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk--}}
    {{--            lllllllllllllll--}}
    {{--        </p>--}}
    {{--        <img src="/storage/2/3/6Ew9weglokd9WxLjXZNSizbVc7uQoOZRGJz8OrG6.png" alt="Paris"--}}
    {{--             style="float:left;width:100%;height:150px;object-fit:contain;">--}}
    {{--    </div>--}}
    {{--    <div style="width:100%;">--}}
    {{--        <p style="float:left;width:50%;height:100%;">--}}
    {{--            22222222222222hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh--}}
    {{--        </p>--}}
    {{--        <p style="float:left;width:50%;height:100%;">--}}
    {{--            hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh;;;;;;;;;;;;;;;; kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk--}}
    {{--            lllllllllllllll--}}
    {{--        </p>--}}
    {{--        <img src="/storage/2/3/gDGQdvq2ytL5ONiwPY3vyA4CqPiayJOSWj1ax0Kp.jpg" alt="Paris"--}}
    {{--             style="float:left;width:100%;height:150px;object-fit:contain;">--}}
    {{--    </div>--}}
    {{--    <p style="width:100%;">--}}
    {{--        <div style="float:left;width:80%;">--}}
    {{--            hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh11111111111--}}
    {{--            111111111111111111111--}}
    {{--            111111111111111111111--}}
    {{--        </div>--}}
    {{--        <img src="/storage/2/3/6Ew9weglokd9WxLjXZNSizbVc7uQoOZRGJz8OrG6.png" alt="Paris"--}}
    {{--             style="float:left;width:20%;height:30%;object-fit:contain;"/>--}}
    {{--    </p>--}}
    {{--    <p style="width:100%;">--}}
    {{--        <div style="float:left;width:80%;">--}}
    {{--            hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh22222222222222222222222222--}}
    {{--            22222222222222222222--}}
    {{--            22222222222222222222--}}
    {{--        </div>--}}
    {{--        <img src="/storage/2/3/gDGQdvq2ytL5ONiwPY3vyA4CqPiayJOSWj1ax0Kp.jpg" alt="Paris"--}}
    {{--             style="float:left;width:20%;height:30%;object-fit:contain;"/>--}}
    {{--    </p>--}}
    {{--    <div class="row">--}}
    {{--        <div class="col-12">--}}
    {{--            <div class="text-title">--}}
    {{--                111111111111111111111--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="row">--}}
    {{--        <div class="col-9">--}}
    {{--            200--}}
    {{--        </div>--}}
    {{--        <div class="col-3">--}}
    {{--            <img src="/storage/2/3/gDGQdvq2ytL5ONiwPY3vyA4CqPiayJOSWj1ax0Kp.jpg"--}}
    {{--                 style="width:100px;float:right;object-fit:contain;">--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <hr>--}}
    {{--    <div class="row">--}}
    {{--        <div class="col-12">--}}
    {{--            111111111111111111111--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="row">--}}
    {{--        <div class="col-9">--}}
    {{--            200--}}
    {{--        </div>--}}
    {{--        <div class="col-3">--}}
    {{--            <img src="/storage/2/3/6Ew9weglokd9WxLjXZNSizbVc7uQoOZRGJz8OrG6.png"--}}
    {{--                 style="width:100px;float:right;object-fit:contain;">--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <hr>--}}
    {{--    <div class="row">--}}
    {{--        <div class="col-12">--}}
    {{--            111111111111111111111--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="row">--}}
    {{--        <div class="col-9">--}}
    {{--            200--}}
    {{--        </div>--}}
    {{--        <div class="col-3">--}}
    {{--            <img src="/storage/2/3/gDGQdvq2ytL5ONiwPY3vyA4CqPiayJOSWj1ax0Kp.jpg"--}}
    {{--                 style="width:100px;float:right;object-fit:contain;">--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <hr>--}}

    {{--    <figure>--}}
    {{--        <img src="http://abakusonline/storage/2/3/gDGQdvq2ytL5ONiwPY3vyA4CqPiayJOSWj1ax0Kp.jpg"--}}
    {{--             alt="The head and torso of a dinosaur skeleton;--}}
    {{--            it has a large head with long sharp teeth"--}}
    {{--             width="400"--}}
    {{--             height="341">--}}

    {{--        <figcaption>A T-Rex on display in the Manchester University Museum.A T-Rex on display in the Manchester University Museum.A T-Rex on display in the Manchester University Museum.--}}
    {{--        </figcaption>--}}
    {{--    </figure>--}}

    {{--    <style>--}}
    {{--        .rightpic {--}}
    {{--            float: left; /* Выравнивание по правому краю */--}}
    {{--            margin: 0px 15px 15px 0; /* Отступы вокруг фотографии */--}}
    {{--            /*border: 2px solid #ff0000;*/--}}
    {{--        }--}}
    {{--    </style>--}}
    {{--    <article style="text-indent: 40px; text-align: justify">--}}
    {{--        <img src="http://abakusonline/storage/2/3/gDGQdvq2ytL5ONiwPY3vyA4CqPiayJOSWj1ax0Kp.jpg" alt="Иллюстрация"--}}
    {{--             height="200" class="rightpic">--}}
    {{--        Гармония, в первом приближении, параллельно образует экзистенциальный--}}
    {{--        художественный талант, как и предсказывает теория о бесполезном знании.--}}
    {{--        Действие, в том числе, выстраивает понимающий архетип, однако само по--}}
    {{--        себе состояние игры всегда амбивалентно. Композиция, в представлении--}}
    {{--        Морено, диссонирует невротический объект, что-то подобное можно встретить--}}
    {{--        в работах Ауэрбаха и Тандлера. Бессознательное, конечно, диссонирует--}}
    {{--        экспериментальный символизм, таким образом осуществляется своего рода--}}
    {{--        связь с темнотой бессознательного. После того как тема сформулирована,--}}
    {{--        либидо параллельно.--}}
    {{--        Гармония, в первом приближении, параллельно образует экзистенциальный--}}
    {{--        художественный талант, как и предсказывает теория о бесполезном знании.--}}
    {{--        Действие, в том числе, выстраивает понимающий архетип, однако само по--}}
    {{--        себе состояние игры всегда амбивалентно. Композиция, в представлении--}}
    {{--        Морено, диссонирует невротический объект, что-то подобное можно встретить--}}
    {{--        в работах Ауэрбаха и Тандлера. Бессознательное, конечно, диссонирует--}}
    {{--        экспериментальный символизм, таким образом осуществляется своего рода--}}
    {{--        связь с темнотой бессознательного. После того как тема сформулирована,--}}
    {{--        либидо параллельно.--}}
    {{--    </article>--}}
    {{--    <hr>--}}
    {{--       <article style="text-indent: 40px; text-align: justify">--}}
    {{--        <img src="http://abakusonline/storage/2/3/gDGQdvq2ytL5ONiwPY3vyA4CqPiayJOSWj1ax0Kp.jpg" alt="Иллюстрация"--}}
    {{--             height="200" class="rightpic">--}}
    {{--        Иррациональное в творчестве начинает психоз, это обозначено Ли Россом как--}}
    {{--        фундаментальная ошибка атрибуции, которая прослеживается во многих экспериментах.--}}
    {{--        Индивидуальность аккумулирует комплекс, именно об этом комплексе движущих сил--}}
    {{--        писал З.Фрейд в теории сублимации. Иными словами, рефлексия использует элитарный--}}
    {{--        стресс, это же положение обосновывал Ж.Польти в книге "Тридцать шесть--}}
    {{--        драматических ситуаций". Как было показано выше, эриксоновский гипноз--}}
    {{--        иллюстрирует социометрический онтогенез, что лишний раз подтверждает правоту--}}
    {{--        З.Фрейда.--}}
    {{--    </article>--}}

    {{--    <br><br><br>--}}
    {{--    <table>--}}
    {{--        <tr>--}}
    {{--            <td valign="top"><img src="http://abakusonline/storage/2/3/gDGQdvq2ytL5ONiwPY3vyA4CqPiayJOSWj1ax0Kp.jpg"--}}
    {{--                                  alt="Иллюстрация"--}}
    {{--                                  height="200" class="rightpic"></td>--}}
    {{--            <td valign="top">--}}
    {{--                <article style="text-indent: 40px; text-align: justify">--}}
    {{--                    Гармония, в первом приближении, параллельно образует экзистенциальный--}}
    {{--                    художественный талант, как и предсказывает теория о бесполезном знании.--}}
    {{--                    Действие, в том числе, выстраивает понимающий архетип, однако само по--}}
    {{--                    себе состояние игры всегда амбивалентно. Композиция, в представлении--}}
    {{--                    Морено, диссонирует невротический объект, что-то подобное можно встретить--}}
    {{--                    в работах Ауэрбаха и Тандлера. Бессознательное, конечно, диссонирует--}}
    {{--                    экспериментальный символизм, таким образом осуществляется своего рода--}}
    {{--                    связь с темнотой бессознательного. После того как тема сформулирована,--}}
    {{--                    либидо параллельно.--}}
    {{--                    Гармония, в первом приближении, параллельно образует экзистенциальный--}}
    {{--                    художественный талант, как и предсказывает теория о бесполезном знании.--}}
    {{--                    Действие, в том числе, выстраивает понимающий архетип, однако само по--}}
    {{--                    себе состояние игры всегда амбивалентно. Композиция, в представлении--}}
    {{--                    Морено, диссонирует невротический объект, что-то подобное можно встретить--}}
    {{--                    в работах Ауэрбаха и Тандлера. Бессознательное, конечно, диссонирует--}}
    {{--                    экспериментальный символизм, таким образом осуществляется своего рода--}}
    {{--                    связь с темнотой бессознательного. После того как тема сформулирована,--}}
    {{--                    либидо параллельно.--}}
    {{--                    Гармония, в первом приближении, параллельно образует экзистенциальный--}}
    {{--                    художественный талант, как и предсказывает теория о бесполезном знании.--}}
    {{--                    Действие, в том числе, выстраивает понимающий архетип, однако само по--}}
    {{--                    себе состояние игры всегда амбивалентно. Композиция, в представлении--}}
    {{--                    Морено, диссонирует невротический объект, что-то подобное можно встретить--}}
    {{--                    в работах Ауэрбаха и Тандлера. Бессознательное, конечно, диссонирует--}}
    {{--                    экспериментальный символизм, таким образом осуществляется своего рода--}}
    {{--                    связь с темнотой бессознательного. После того как тема сформулирована,--}}
    {{--                    либидо параллельно.--}}
    {{--                </article>--}}
    {{--            </td>--}}
    {{--        </tr>--}}
    {{--        <tr>--}}
    {{--            <td><img src="http://abakusonline/storage/2/3/gDGQdvq2ytL5ONiwPY3vyA4CqPiayJOSWj1ax0Kp.jpg"--}}
    {{--                     alt="Иллюстрация"--}}
    {{--                     height="200" class="rightpic"></td>--}}
    {{--            <td>--}}
    {{--                <article style="text-indent: 40px; text-align: justify">--}}
    {{--                    Иррациональное в творчестве начинает психоз, это обозначено Ли Россом как--}}
    {{--                    фундаментальная ошибка атрибуции, которая прослеживается во многих экспериментах.--}}
    {{--                    Индивидуальность аккумулирует комплекс, именно об этом комплексе движущих сил--}}
    {{--                    писал З.Фрейд в теории сублимации. Иными словами, рефлексия использует элитарный--}}
    {{--                    стресс, это же положение обосновывал Ж.Польти в книге "Тридцать шесть--}}
    {{--                    драматических ситуаций". Как было показано выше, эриксоновский гипноз--}}
    {{--                    иллюстрирует социометрический онтогенез, что лишний раз подтверждает правоту--}}
    {{--                    З.Фрейда.--}}
    {{--                </article>--}}
    {{--            </td>--}}
    {{--        </tr>--}}
    {{--    </table>--}}

    {{--    <article style="text-indent: 40px; text-align: justify">--}}
    {{--        Гармония, в первом приближении, параллельно образует экзистенциальный--}}
    {{--        художественный талант, как и предсказывает теория о бесполезном знании.--}}
    {{--        Действие, в том числе, выстраивает понимающий архетип, однако само по--}}
    {{--        себе состояние игры всегда амбивалентно. Композиция, в представлении--}}
    {{--        Морено, диссонирует невротический объект, что-то подобное можно встретить--}}
    {{--        в работах Ауэрбаха и Тандлера. Бессознательное, конечно, диссонирует--}}
    {{--        экспериментальный символизм, таким образом осуществляется своего рода--}}
    {{--        связь с темнотой бессознательного. После того как тема сформулирована,--}}
    {{--        либидо параллельно.--}}
    {{--    </article>--}}
    {{--    <center>--}}
    {{--    <img src="http://abakusonline/storage/2/3/gDGQdvq2ytL5ONiwPY3vyA4CqPiayJOSWj1ax0Kp.jpg" alt="Иллюстрация"--}}
    {{--         width="30%">--}}
    {{--    </center>--}}
    {{--    <hr>--}}
    {{--    <article style="text-indent: 40px; text-align: justify">--}}
    {{--        Иррациональное в творчестве начинает психоз, это обозначено Ли Россом как--}}
    {{--        фундаментальная ошибка атрибуции, которая прослеживается во многих экспериментах.--}}
    {{--        Индивидуальность аккумулирует комплекс, именно об этом комплексе движущих сил--}}
    {{--        писал З.Фрейд в теории сублимации. Иными словами, рефлексия использует элитарный--}}
    {{--        стресс, это же положение обосновывал Ж.Польти в книге "Тридцать шесть--}}
    {{--        драматических ситуаций". Как было показано выше, эриксоновский гипноз--}}
    {{--        иллюстрирует социометрический онтогенез, что лишний раз подтверждает правоту--}}
    {{--        З.Фрейда.--}}
    {{--    </article>--}}
    {{--    <center>--}}
    {{--    <img src="http://abakusonline/storage/2/3/gDGQdvq2ytL5ONiwPY3vyA4CqPiayJOSWj1ax0Kp.jpg" alt="Иллюстрация"--}}
    {{--         width="30%">--}}
    {{--    </center>--}}

    {{--    https://itchief.ru/bootstrap/tables--}}
    {{--    <table class="table table-fixed">--}}
    {{--        <thead>--}}
    {{--        <tr>--}}
    {{--            <th class="col-xs-2">#</th><th class="col-xs-8">Наименование</th><th class="col-xs-2">Цена</th>--}}
    {{--        </tr>--}}
    {{--        </thead>--}}
    {{--        <tbody>--}}
    {{--        <tr>--}}
    {{--            <td class="col-xs-2">1</td><td class="col-xs-8">Хлеб ржаной</td><td class="col-xs-2">23,50 р.</td>--}}
    {{--        </tr>--}}
    {{--        <tr>--}}
    {{--            <td class="col-xs-2">2</td><td class="col-xs-8">Хлеб пшеничный</td><td class="col-xs-2">27,00 р.</td>--}}
    {{--        </tr>--}}
    {{--        <tr>--}}
    {{--            <td class="col-xs-2">3</td><td class="col-xs-8" ">Хлеб новый</td><td class="col-xs-2">33,20 р.</td>--}}
    {{--        </tr>--}}
    {{--        <tr>--}}
    {{--            <td class="col-xs-2">4</td><td class="col-xs-8">Хлеб подольский</td><td class="col-xs-2">27,50 р.</td>--}}
    {{--        </tr>--}}
    {{--        <tr>--}}
    {{--            <td class="col-xs-2">5</td><td class="col-xs-8">Хлеб серый</td><td class="col-xs-2">21,00 р.</td>--}}
    {{--        </tr>--}}
    {{--        </tbody>--}}
    {{--    </table>--}}
    {{--    <div class="jumbotron">--}}
    {{--        <h1 class="display-4">Hello, world!</h1>--}}
    {{--        <p class="lead">Это простой пример блока с компонентом в стиле jumbotron для привлечения дополнительного--}}
    {{--            внимания к содержанию или информации.</p>--}}
    {{--        <hr class="my-4">--}}
    {{--        <p>Используются служебные классы для типографики и расстояния содержимого в контейнере большего размера.</p>--}}
    {{--        <p class="lead">--}}
    {{--            <a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a>--}}
    {{--        </p>--}}
    {{--    </div>--}}
    {{--    <div class="jumbotron pt-3 pb-1 mb-3">--}}
    {{--            <p>Гаухар:</p>--}}
    {{--            <p class="lead">Привет!</p>--}}
    {{--    </div>--}}
    {{--    <div class="jumbotron pt-3 pb-1 mb-3">--}}
    {{--        <p>Серик:</p>--}}
    {{--        <p class="lead">Используются служебные классы для типографики и расстояния содержимого в контейнере большего размера.</p>--}}
    {{--    </div>--}}
    {{--    <div class="jumbotron pt-3 pb-1 mb-3">--}}
    {{--        <p>Серик:</p>--}}
    {{--        <p class="lead">Используются служебные классы для типографики и расстояния содержимого в контейнере большего размера.</p>--}}
    {{--    </div>--}}
    {{--    <span class="badge badge-pill badge-primary">Главный</span>--}}
    {{--    <span class="badge badge-pill badge-secondary">Вторичный</span>--}}
    {{--    <span class="badge badge-pill badge-success">Успех</span>--}}
    {{--    <span class="badge badge-pill badge-danger">Опасность</span>--}}
    {{--    <span class="badge badge-pill badge-warning">Предупреждение</span>--}}
    {{--    <span class="badge badge-pill badge-info">Инфо</span>--}}
    {{--    <span class="badge badge-pill badge-light">Светлый</span>--}}
    {{--    <span class="badge badge-pill badge-dark">Темный</span>--}}
    {{--    <div class="collapse">--}}
    {{--        <div class="elements elements-body">--}}
    {{--            Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.--}}
    {{--        </div>--}}
    {{--        <button>JJJ</button>--}}
    {{--    </div>--}}
    {{--    <table>--}}
    {{--        <tr><th  >Продукт</th><th  ></th><th  >Кол-во</th></tr>--}}
    {{--        <tr><td  >Творог протёртый</td><td  >-</td><td  >900 г</td></tr>--}}
    {{--        <tr><td  >Сливочное масло</td><td  >-</td><td  >180 г</td></tr>--}}
    {{--        <tr><td  >Сахар</td><td  >-</td><td  >150 г</td></tr>--}}
    {{--        <tr><td  >Ванильный сахар</td><td  >-</td><td  >4 ч. л.</td></tr>--}}
    {{--        <tr><td  >Изюм</td><td  >-</td><td  >80 г</td></tr>--}}
    {{--        <tr><td  >Апельсиновая цедра</td><td  >-</td><td  >1 ст. л.</td></tr>--}}
    {{--    </table>--}}
    {{--    <br>--}}
    {{--    <table cellpadding="1">--}}
    {{--        <tr><th  >Дата</th><th  >Количество</th><th  >Материал</th></tr>--}}
    {{--        <tr><td  >06.10.2023</td><td  >101</td></tr>--}}
    {{--        <tr><td  >06.10.2023</td><td  >101</td><td  >Сок</td></tr>--}}
    {{--        <tr><td  >06.10.2023</td><td  >102</td><td  >Сок</td></tr>--}}
    {{--        <tr><td  >07.10.2023</td><td  >0</td><td  >Помидоры</td></tr>--}}
    {{--        <tr><td  >07.10.2023</td><td  >10</td><td  >Ноутбук</td></tr>--}}
    {{--        <tr><td  >08.10.2023</td><td  >10</td></tr>--}}
    {{--    </table>--}}
@endsection


