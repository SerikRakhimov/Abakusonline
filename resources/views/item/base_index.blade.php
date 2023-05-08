@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Base;
    use App\Models\Item;
    use App\Models\Link;
    use App\Models\Main;
    use \App\Http\Controllers\GlobalController;
    use \App\Http\Controllers\ItemController;
    use \App\Http\Controllers\MainController;
    $relip_project = GlobalController::calc_relip_project($relit_id, $project);
    $relip_name_project = '';
    if ($relip_project) {
//      if ($relip_project->id != $project->id) {
        if ($relit_id != 0) {
//          $relip_name_project = trans('main.project') . ': ' . $relip_project->name();
            $relip_name_project = $relip_project->name();
        }
    }

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

    //    Config::set('app.display', 'table');
    ?>
    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-left align-top">
                <h3>
                    @if($message_bs_info != '')
                        <a href="#" title="{{$message_bs_info}}">
                            {{-- Использовать так "$base->names($base_right, true)", "true" - вызов из base_index.php--}}
                            {{$base->names($base_right, true)}}
                        </a>
                    @else
                        {{-- Использовать так "$base->names($base_right, true)", "true" - вызов из base_index.php--}}
                        {{$base->names($base_right, true)}}
                    @endif
                </h3>
                @if($role->is_view_info_relits == true)
                    <small><small>{{$relip_name_project}}</small></small>
                @endif
            </div>
        </div>
        {{--        Похожая проверка в ItemController::ext_create() и base_index.php--}}
        @if($base_right['is_list_base_create'] == true)
            <div class="col-12 text-right">
                {{--            Не удалять: используется $message_bs_validate --}}
                @if($message_bs_validate == "")
                    {{-- Используется "'parent_ret_id' => 0"--}}
                    <button type="button" class="btn btn-dreamer"
                            {{--                        Выводится $message_mc--}}
                            title="{{trans('main.add') . $message_bs_info}}"
                            onclick="document.location='{{route('item.ext_create',
                            ['base'=>$base, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
                             'relit_id' =>$relit_id_par,
                             'string_current' => $string_current,
                             'heading' =>intval(false),
                             'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                             'parent_ret_id' => $parent_ret_id_par])}}'">
                        {{--                        'string_link_ids_current' => $string_link_ids_current,--}}
                        {{--                        'string_item_ids_current' => $string_item_ids_current,--}}
                        {{--                        'string_all_codes_current' => $string_all_codes_current,--}}
                        <i class="fas fa-plus d-inline"></i>&nbsp;{{trans('main.add')}}
                    </button>
                @endif
            </div>
        @endif
        {{--        Не удалять--}}
        @if(1==1)
            @auth
                @if ($role->is_author())
                    @if ($base->is_calcname_lst == true)
                        <div class="col-12 text-right">
                            <a href="{{route('item.calculate_names', ['base'=>$base, 'project'=>$relip_project])}}"
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
                @endif
            @endauth
        @endif
    </div>
    </p>
    <?php
    $tile_view = $base->tile_view($base_right);
    $link_image = $tile_view['link'];
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
    {{--    <div class="card border-info">--}}
    {{--        <!-- Шапка (bg-primary - цвет фона, text-white - цвет текста) -->--}}
    {{--        <div class="card-header bg-primary text-white">--}}
    {{--            Название панели--}}
    {{--        </div>--}}
    {{--        <!-- Текстовый контент -->--}}
    {{--        <div class="card-body">--}}
    {{--            <h4 class="card-title">Заголовок</h4>--}}
    {{--            <p class="card-text">...</p>--}}
    {{--            <a href="#" class="btn btn-primary">Ссылка</a>--}}
    {{--        </div>--}}
    {{--    </div><!-- Конец карточки -->--}}

    {{--    <!-- Карточка с текстовым контентом и списком -->--}}
    {{--    <div class="card">--}}
    {{--        <!-- Текстовый контент -->--}}
    {{--        <div class="card-body">--}}
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
    {{--    <div class="card">--}}
    {{--        <!-- Шапка (header) карточки -->--}}
    {{--        <div class="card-header">--}}
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
    {{--    <div class="card">--}}
    {{--        <!-- Шапка с навигацией -->--}}
    {{--        <div class="card-header">--}}
    {{--            <ul class="nav nav-tabs card-header-tabs">--}}
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
    {{--        <div class="card-body tab-content">--}}
    {{--            <div class="tab-pane fade show active" id="item1">--}}
    {{--                <a href="#" class="card-link">Ссылка №1</a>--}}
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
                'base_index'=>true, 'item_heading_base'=>false, 'item_body_base'=>false
                ])
    {{--    @endif--}}
    {{$items->links()}}
    {{--    <blockquote class="text-title pt-1 pl-5 pr-5"><?php echo nl2br($project->dc_ext()); ?></blockquote>--}}
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
    {{--    <div class="card" style="width: 18rem;">--}}
    {{--    <div class="card shadow" style="width: 100%;">--}}
    {{--        <div class="card-body">--}}
    {{--            <h5 class="card-title">Название карточки</h5>--}}
    {{--            <h6 class="card-subtitle mb-2 text-muted">Подзаголовок карты</h6>--}}
    {{--            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the--}}
    {{--                card's--}}
    {{--                content.--}}
    {{--                лллллллллллллллллллл--}}
    {{--                ддддддддддддддддддд--}}
    {{--                жжжжжжжжжжжжжжжжжжжжжжжжжжжжжжж--}}
    {{--            </p>--}}
    {{--            <a href="#" class="card-link">Ссылка карты</a>--}}
    {{--            <a href="#" class="card-link">Другая ссылка</a>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <br>--}}
    {{--    <div class="card shadow" style="width: 100%;">--}}
    {{--        <div class="card-body">--}}
    {{--            <h5 class="card-title">Название карточки</h5>--}}
    {{--            <h6 class="card-subtitle mb-2 text-muted">Подзаголовок карты</h6>--}}
    {{--            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the--}}
    {{--                card's--}}
    {{--                content.--}}
    {{--                лллллллллллллллллллл--}}
    {{--                ддддддддддддддддддд--}}
    {{--                жжжжжжжжжжжжжжжжжжжжжжжжжжжжжжж--}}
    {{--            </p>--}}
    {{--            <a href="#" class="card-link">Ссылка карты</a>--}}
    {{--            <a href="#" class="card-link">Другая ссылка</a>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <br>--}}
    {{--    <div class="card shadow" style="width: 100%;">--}}
    {{--        <div class="card-body">--}}
    {{--            <h5 class="card-title">Название карточки</h5>--}}
    {{--            <h6 class="card-subtitle mb-2 text-muted">Подзаголовок карты</h6>--}}
    {{--            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the--}}
    {{--                card's--}}
    {{--                content.--}}
    {{--                лллллллллллллллллллл--}}
    {{--                ддддддддддддддддддд--}}
    {{--                жжжжжжжжжжжжжжжжжжжжжжжжжжжжжжж--}}
    {{--            </p>--}}
    {{--            <a href="#" class="card-link">Ссылка карты</a>--}}
    {{--            <a href="#" class="card-link">Другая ссылка</a>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="card shadow ">--}}
    {{--        <div class="card" style="width: 100%;">--}}
    {{--            <div class="card-body mt-0">--}}
    {{--                <h5 class="card-title">Название карточки</h5>--}}
    {{--                <h6 class="card-subtitle mb-2 text-muted">Подзаголовок карты</h6>--}}
    {{--                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the--}}
    {{--                    card's--}}
    {{--                    content.</p>--}}
    {{--                <a href="#" class="card-link">Ссылка карты</a>--}}
    {{--                <a href="#" class="card-link">Другая ссылка</a>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--    <div class="card shadow">--}}
    {{--        <div class="card" style="width: 18rem;">--}}
    {{--            <div class="card-body">--}}
    {{--                <h5 class="card-title">Название карточки</h5>--}}
    {{--                <h6 class="card-subtitle mb-2 text-muted">Подзаголовок карты</h6>--}}
    {{--                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the--}}
    {{--                    card's--}}
    {{--                    content.</p>--}}
    {{--                <a href="#" class="card-link">Ссылка карты</a>--}}
    {{--                <a href="#" class="card-link">Другая ссылка</a>--}}
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
@endsection


