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
                            {{$base->names($base_right)}}
                        </a>
                    @else
                        {{$base->names($base_right)}}
                    @endif
                </h3>
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
                            title="{{trans('main.add') . ', ' . $message_bs_info}}"
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
                @endif
                @if (Auth::user()->isAdmin())
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
    $i = $items->firstItem() - 1;
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
    @if($tile_view['result'] == true)
        <div class="card-columns">
{{--        $its_page используется--}}
            @foreach($its_page as $item)
                <?php
                $i = $i + 1;
                $item_find = GlobalController::view_info($item->id, $link_image->id);
                ?>
                {{--                <div class="card text-center">--}}
                {{--                    <div class="card card-inverse text-center" style="background-color: rgba(222,255,162,0.23); border-color: #3548ee;">--}}
                <div class="card shadow">
                    @if($base->is_code_needed == true)
                        <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,
                                    'heading' => $heading,'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                                    'par_link'=>null, 'parent_item'=>null,
                                    'string_current' => $string_current,
                                    ])}}" title="{{$item->name()}}">
{{--                            'string_all_codes_current' => $string_all_codes_current,--}}
{{--                            'string_link_ids_current' => $string_link_ids_current,--}}
{{--                            'string_item_ids_current' => $string_item_ids_current,--}}
                            <p class="card-header text-center text-label">{{trans('main.code')}}: {{$item->code}}</p>
                        </a>
                    @endif
                    <div class="card-body">
                        @if($item_find)
                            {{--                            <div class="card-block text-center">--}}
                            <div class="text-center">
                                {{-- https://askdev.ru/q/kak-vyzvat-funkciyu-javascript-iz-tega-href-v-html-276225/--}}
                                <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,
                                    'heading' => $heading,'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                                    'par_link'=>null, 'parent_item'=>null,
                                    'string_current' => $string_current,
                                    ])}}"
                                   title="{{$item->name()}}">
                                    {{--                                'string_all_codes_current' => $string_all_codes_current,--}}
                                    {{--                                'string_link_ids_current' => $string_link_ids_current,--}}
                                    {{--                                'string_item_ids_current' => $string_item_ids_current,--}}
                                    @include('view.img',['item'=>$item_find, 'size'=>"medium", 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>true, 'title'=>$item->name()])
                                </a>
                            </div>
                        @endif
                        {{--                    <div class="card-footer">--}}
                        <h5 class="card-title text-center"><a
                                href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,
                                    'heading' => $heading,'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                                    'par_link'=>null, 'parent_item'=>null,
                             'string_current' => $string_current,
                                    ])}}"
                                title="{{$item->name()}}">
{{--                                'string_all_codes_current' => $string_all_codes_current,--}}
{{--                                'string_link_ids_current' => $string_link_ids_current,--}}
{{--                                'string_item_ids_current' => $string_item_ids_current,--}}
                                {{--                            Где $item->name() выходит в cards выводить "<?php echo GlobalController::to_html();?>"--}}
                                <?php echo $item->nmbr(false);?>
                            </a></h5>
                        {{--                    </div>--}}
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">
                            {{$item->created_at->Format(trans('main.format_date'))}}
                        </small>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col text-center text-label">
                {{trans('main.select_record_for_work')}}
            </div>
        </div>
    @else
        <!--        --><?php
        //        $link_id_array = $links_info['link_id_array'];
        //        $matrix = $links_info['matrix'];
        //        $rows = $links_info['rows'];
        //        $cols = $links_info['cols'];
        //        ?>
        {{--        <table class="table table-sm table-bordered table-hover">--}}
        {{--            <caption>{{trans('main.select_record_for_work')}}</caption>--}}
        {{--            <thead>--}}
        {{--            <tr>--}}
        {{--                <th rowspan="{{$rows + 1}}" class="text-center align-top">#</th>--}}
        {{--                @if($base_right['is_list_base_enable'] == true)--}}
        {{--                    @if($base->is_code_needed == true)--}}
        {{--                        <th class="text-center align-top" rowspan="{{$rows + 1}}">{{trans('main.code')}}</th>--}}
        {{--                    @endif--}}
        {{--                    --}}{{--                Если тип-вычисляемое наименование и Показывать Основу с вычисляемым наименованием--}}
        {{--                    --}}{{--                или если тип-не вычисляемое наименование--}}
        {{--                    --}}{{--            похожая проверка в ext_show.blade.php--}}
        {{--                    @if(GlobalController::is_base_calcname_check($base, $base_right))--}}
        {{--                        <th rowspan="{{$rows + 1}}" @include('layouts.class_from_base',['base'=>$base, 'align_top'=>true])>--}}
        {{--                            {{trans('main.name')}}</th>--}}
        {{--            @endif--}}
        {{--            @endif--}}
        {{--            @if($rows > 0)--}}
        {{--                @for($x = ($rows-1); $x >= 0; $x--)--}}
        {{--                    @if($x != ($rows-1))--}}
        {{--                        <tr>--}}
        {{--                            @endif--}}
        {{--                            @for($y=0; $y<$cols;$y++)--}}
        {{--                                @if($matrix[$x][$y]["view_field"] != null)--}}
        {{--                                    <th rowspan="{{$matrix[$x][$y]["rowspan"]}}"--}}
        {{--                                        colspan="{{$matrix[$x][$y]["colspan"]}}"--}}
        {{--                                        class="text-center align-top">--}}
        {{--                                        @if($matrix[$x][$y]["fin_link"] == true)--}}
        {{--                                            <?php--}}
        {{--                                            $link = Link::findOrFail($matrix[$x][$y]["link_id"]);--}}
        {{--                                            ?>--}}
        {{--                                            <a href="{{route('item.base_index',['base'=>$link->parent_base_id, 'project'=>$project, 'role'=>$role])}}"--}}
        {{--                                               title="{{$link->parent_base->names()}}">--}}
        {{--                                                {{$matrix[$x][$y]["view_name"]}}--}}
        {{--                                            </a>--}}
        {{--                                        @else--}}
        {{--                                            {{$matrix[$x][$y]["view_name"]}}--}}
        {{--                                        @endif--}}
        {{--                                    </th>--}}
        {{--                                    --}}{{--                    {{$x}} {{$y}}  rowspan = {{$matrix[$x][$y]["rowspan"]}} colspan = {{$matrix[$x][$y]["colspan"]}} view_level_id = {{$matrix[$x][$y]["view_level_id"]}} view_level_name = {{$matrix[$x][$y]["view_level_name"]}}--}}
        {{--                                    --}}{{--                    <br>--}}
        {{--                                @endif--}}
        {{--                            @endfor--}}
        {{--                        </tr>--}}
        {{--                        @endfor--}}
        {{--                        </tr>--}}
        {{--                    @endif--}}
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
        {{--                            @if(1==2)--}}
        {{--                                @foreach($link_id_array as $value)--}}
        {{--                                    <?php--}}
        {{--                                    $link = Link::findOrFail($value);--}}
        {{--                                    ?>--}}
        {{--                                    --}}{{--                    <th--}}
        {{--                                    --}}{{--                        @include('layouts.class_from_base',['base'=>$link->parent_base])--}}
        {{--                                    --}}{{--                    >--}}
        {{--                                    <th class="text-center align-top">--}}
        {{--                                        <a href="{{route('item.base_index',['base'=>$link->parent_base_id, 'project'=>$project, 'role'=>$role])}}"--}}
        {{--                                           title="{{$link->parent_base->names()}}">--}}
        {{--                                            {{$link->parent_label()}}--}}
        {{--                                        </a>--}}
        {{--                                    </th>--}}
        {{--                                @endforeach--}}
        {{--                                --}}{{--            <th class="text-center">{{trans('main.user')}}</th>--}}
        {{--                                --}}{{--            <th class="text-center">{{trans('main.user')}}</th>--}}
        {{--                        </tr>--}}
        {{--                    @endif--}}
        {{--            </thead>--}}
        {{--            <tbody>--}}
        {{--            @foreach($items as $item)--}}
        {{--                <?php--}}
        {{--                $i++;--}}
        {{--                ?>--}}
        {{--                <tr>--}}
        {{--                    <td class="text-center">--}}
        {{--                        --}}{{--                    Не удалять--}}
        {{--                        --}}{{--                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role])}}">--}}
        {{--                        <a href="{{route('item.ext_show', ['item'=>$item, 'role'=>$role])}}">--}}
        {{--                            {{$i}}--}}
        {{--                        </a>--}}
        {{--                    </td>--}}
        {{--                    @if($base_right['is_list_base_enable'] == true)--}}
        {{--                        @if($base->is_code_needed == true)--}}
        {{--                            <td class="text-center">--}}
        {{--                                <a href="{{route('item.ext_show', ['item'=>$item, 'role'=>$role])}}">--}}
        {{--                                    {{$item->code}}--}}
        {{--                                </a>--}}
        {{--                            </td>--}}
        {{--                        @endif--}}
        {{--                        --}}{{--                Если тип-вычисляемое наименование и Показывать Основу с вычисляемым наименованием--}}
        {{--                        --}}{{--                или если тип-не вычисляемое наименование--}}
        {{--                        --}}{{--            похожая проверка в ext_show.blade.php--}}
        {{--                        @if(GlobalController::is_base_calcname_check($base, $base_right))--}}
        {{--                            <td @include('layouts.class_from_base',['base'=>$base])>--}}
        {{--                                @if($base->type_is_image)--}}
        {{--                                    @include('view.img',['item'=>$item, 'size'=>"small", 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'title'=>""])--}}
        {{--                                @elseif($base->type_is_document)--}}
        {{--                                    @include('view.doc',['item'=>$item, 'usercode'=>GlobalController::usercode_calc()])--}}
        {{--                                @else--}}
        {{--                                    <a href="{{route('item.ext_show', ['item'=>$item, 'role'=>$role])}}">--}}
        {{--                                        --}}{{--                            Где $item->name() выходит в cards выводить "<?php echo GlobalController::to_html();?>"--}}
        {{--                                        {{$item->name()}}--}}
        {{--                                    </a>--}}
        {{--                                @endif--}}
        {{--                            </td>--}}
        {{--                        @endif--}}
        {{--                    @endif--}}
        {{--                    --}}{{--                <td class="text-center">&#8594;</td>--}}
        {{--                    @foreach($link_id_array as $value)--}}
        {{--                        <?php--}}
        {{--                        $link = Link::findOrFail($value);--}}
        {{--                        ?>--}}
        {{--                        <td--}}
        {{--                            @include('layouts.class_from_base',['base'=>$link->parent_base])--}}
        {{--                        >--}}
        {{--                            <?php--}}
        {{--                            $item_find = GlobalController::view_info($item->id, $link->id);--}}
        {{--                            ?>--}}
        {{--                            @if($item_find)--}}
        {{--                                @if($link->parent_base->type_is_image())--}}
        {{--                                    @include('view.img',['item'=>$item_find, 'size'=>"small", 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'title'=>""])--}}
        {{--                                @elseif($link->parent_base->type_is_document())--}}
        {{--                                    @include('view.doc',['item'=>$item_find, 'usercode'=>GlobalController::usercode_calc()])--}}
        {{--                                @else--}}
        {{--                                    --}}{{--                                Не удалять: просмотр Пространство--}}
        {{--                                    --}}{{--                                                                            проверка, если link - вычисляемое поле--}}
        {{--                                    --}}{{--                                    @if ($link->parent_is_parent_related == true || $link->parent_is_numcalc == true)--}}
        {{--                                    --}}{{--                                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role])}}">--}}
        {{--                                    --}}{{--                                            @else--}}
        {{--                                    --}}{{--                                                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), par_link'=>$link])}}">--}}
        {{--                                    --}}{{--                                                    @endif--}}
        {{--                                    --}}{{--                                             Так использовать: 'item'=>$item--}}
        {{--                                    <a href="{{route('item.ext_show', ['item'=>$item, 'role'=>$role])}}">--}}
        {{--                                        --}}{{--                            Где $item->name() выходит в cards выводить "<?php echo GlobalController::to_html();?>"--}}
        {{--                                        {{$item_find->name(false,false,false)}}--}}
        {{--                                    </a>--}}
        {{--                                @endif--}}
        {{--                            @else--}}
        {{--                                <div class="text-danger">--}}
        {{--                                    {{GlobalController::empty_html()}}--}}
        {{--                                </div>--}}
        {{--                            @endif--}}
        {{--                        </td>--}}
        {{--                    @endforeach--}}
        {{--                    --}}{{--                    Не удалять--}}
        {{--                    --}}{{--                <td>{{$item->created_user_date()}}--}}
        {{--                    --}}{{--                </td>--}}
        {{--                    --}}{{--                <td>{{$item->updated_user_date()}}--}}
        {{--                    --}}{{--                </td>--}}
        {{--                    --}}{{--                <td class="text-left">--}}
        {{--                    --}}{{--                    <?php--}}
        {{--                    --}}{{--                    $link = Link::where('child_base_id', $item->base_id)->exists();--}}
        {{--                    --}}{{--                    $main = Main::where('child_item_id', $item->id)->exists();--}}
        {{--                    --}}{{--                    ?>--}}
        {{--                    --}}{{--                    @if ($link != null)--}}
        {{--                    --}}{{--                        @if ($main != null)--}}
        {{--                    --}}{{--                            {{trans('main.full')}}--}}
        {{--                    --}}{{--                        @endif--}}
        {{--                    --}}{{--                    @else--}}
        {{--                    --}}{{--                        <span class="text-danger font-weight-bold">{{trans('main.empty')}}</span>--}}
        {{--                    --}}{{--                    @endif--}}
        {{--                    --}}{{--                </td>--}}
        {{--                    --}}{{--                <td class="text-left">--}}
        {{--                    --}}{{--                    <?php--}}
        {{--                    --}}{{--                    //                  $link = Link::where('parent_base_id', $item->base_id)->first();--}}
        {{--                    --}}{{--                    //                  $main = Main::where('parent_item_id', $item->id)->first();--}}
        {{--                    --}}{{--                    //                  $link = Link::all()->contains('parent_base_id', $item->base_id);--}}
        {{--                    --}}{{--                    //                  $main = Main::all()->contains('parent_item_id', $item->id);--}}
        {{--                    --}}{{--                    $link = Link::where('parent_base_id', $item->base_id)->exists();--}}
        {{--                    --}}{{--                    $main = Main::where('parent_item_id', $item->id)->exists();--}}
        {{--                    --}}{{--                    ?>--}}
        {{--                    --}}{{--                    @if ($link != null)--}}
        {{--                    --}}{{--                        @if ($main != null)--}}
        {{--                    --}}{{--                            {{trans('main.used')}}--}}
        {{--                    --}}{{--                        @else--}}
        {{--                    --}}{{--                            {{trans('main.not_used')}}--}}
        {{--                    --}}{{--                        @endif--}}
        {{--                    --}}{{--                    @endif--}}
        {{--                    --}}{{--                    /--}}
        {{--                    --}}{{--                    @if  (count($item->parent_mains) == 0)--}}
        {{--                    --}}{{--                        <b>{{trans('main.not_used')}}</b>--}}
        {{--                    --}}{{--                    @else--}}
        {{--                    --}}{{--                        {{trans('main.used')}}--}}
        {{--                    --}}{{--                    @endif--}}
        {{--                    --}}{{--                </td>--}}
        {{--                    --}}{{--                Не удалять: другой способ просмотра--}}
        {{--                    --}}{{--                <td class="text-center">--}}
        {{--                    --}}{{--                    <a href="{{route('main.index_item',$item)}}" title="{{trans('main.information')}}">--}}
        {{--                    --}}{{--                        <img src="{{Storage::url('info_record.png')}}" width="15" height="15"--}}
        {{--                    --}}{{--                             alt="{{trans('main.info')}}">--}}
        {{--                    --}}{{--                    </a>--}}
        {{--                    --}}{{--                </td>--}}
        {{--                </tr>--}}
        {{--            @endforeach--}}
        {{--            </tbody>--}}
        {{--        </table>--}}
    @endif
    {{$items->links()}}
    {{--    <blockquote class="text-title pt-1 pl-5 pr-5"><?php echo nl2br($project->dc_ext()); ?></blockquote>--}}
    <blockquote class="text-title pt-1 pl-5 pr-5"><?php echo nl2br($project->dc_int()); ?></blockquote>
@endsection


