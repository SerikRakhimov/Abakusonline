<?php
use App\Models\Base;
use App\Models\Link;
use \App\Http\Controllers\GlobalController;
$link_id_array = $links_info['link_id_array'];
$link_base_relit_id_array = $links_info['link_base_relit_id_array'];
$link_base_right_array = $links_info['link_base_right_array'];
$matrix = $links_info['matrix'];
$rows = $links_info['rows'];
$cols = $links_info['cols'];
$emoji_enable = true;
$i = 0;
if ($item_heading_base == true) {
    $i = 0;
} else {
    $i = $items->firstItem() - 1;
}
// $view_link передаются как число($link->id) или текст(GlobalController::par_link_const_textnull() или GlobalController::par_link_const_text_base_null())
// $v_link либо приведен к типу link либо null
$v_link = null;
if ($view_link) {
    $v_link = Link::find($view_link);
}
$i_par_link = null;
// Вызов list\table.php из base_index.php
if ($base_index == true) {
    $i_par_link = GlobalController::par_link_const_text_base_null();
    //$i_par_link = null;
} else {
//    $i_par_link = GlobalController::par_link_const_textnull();
    $i_par_link = $view_link;
}
$tile_view = $base->tile_view($role, $relit_id, $base_right);
// $link_image используется не только при выводе Карт, но и при выводе таблицы(см.ниже)
$link_image = $tile_view['link'];
$num_cols = GlobalController::get_number_of_columns_info();
$v_label = "";
// $v_link используется
if ($v_link) {
    // $v_link используется
    $v_label = $v_link->child_label();
} else {
    $v_label = $base->name();
}
?>
{{--<table class="table table-sm table-bordered table-hover">--}}
{{--<table class="table table-sm table-borderless table-hover">--}}
{{--<table class="table table-sm table-hover--}}
{{--@if($heading)--}}
{{--    table-borderless--}}
{{--@else--}}
{{--    table-bordered--}}
{{--@endif--}}
{{--    ">--}}
{{--Т.е. кроме вывода в заголовке @if($heading==false)--}}
@if(($base_index==true || $item_body_base==true) & $tile_view['result'] == true)
    {{-- I.Вывод карт--}}
    @if($base_right['is_view_cards'] == true)
        <div class="card-deck">
        {{--        $its_page используется--}}
        @foreach($its_page as $item)
            <?php
            $i = $i + 1;
            // Нужно
            $item_find = null;
            if ($link_image) {
                $item_find = GlobalController::view_info($item->id, $link_image->id, $role, $relit_id, true);
            }
            //                $s_title = $v_label . ", id =" . $item->id;
            //                if ($base->is_code_needed == true) {
            //                    $s_title = $s_title . ", " . trans('main.code') . " =" . $item->code;
            //                }
            ?>
            @include('list.elements.card',
           ['project'=>$project, 'item'=>$item, 'item_find'=>$item_find, 'role'=>$role,
           'i'=>$i,
           'label_name'=>"",
           'relit_id'=>$relit_id,
           'called_from_button'=>0,
           'view_link'=>$i_par_link,
           'view_ret_id'=>$view_ret_id,
           'string_current'=>$string_next,
           'prev_base_index_page'=>$base_index_page,
           'prev_body_link_page'=>$body_link_page,
           'prev_body_all_page'=>$body_all_page])
            <!--                --><?php
                //                $i++;
                //                ?>
                @if($i % $num_cols == 0)
        </div>
        <div class="card-deck">
            @endif
            @endforeach
            {{-- Если строка из $num_cols элементов не завершилась до $num_cols столбцов--}}
            {{-- (т.е. $i не делится без остатка на $num_cols)--}}
            @if($i % $num_cols != 0)
                <?php
                // Подсчитываем количество оставшихся колонок
                $n = $num_cols - ($i % $num_cols);
                ?>
                {{-- В цикле $n раз вставляем вставляем пустые колонки--}}
                @for($k = 0; $k < $n; $k++)
                    {{-- Вставляем пустую карточку--}}
                    {{--                        <div class="elements border-0 m-1">--}}
                    <div class="card m-2 bg-transparent">
                    </div>
                @endfor
            @endif
        </div>
        <div class="row">
            <div class="col text-center text-label">
                {{trans('main.select_record_for_work')}}
            </div>
        </div>
    @else
        {{-- II.Вывод фото на весь экран--}}
        @foreach($its_page as $item)
            <?php
            $item_find = null;
            if ($link_image) {
                $item_find = GlobalController::view_info($item->id, $link_image->id, $role, $relit_id, true);
            }
            $i++;
            ?>
            @include('list.elements.info',
['project'=>$project, 'item'=>$item, 'item_find'=>$item_find, 'role'=>$role,
'i'=>$i,
'label_name'=>"",
'relit_id'=>$relit_id,
'called_from_button'=>0,
'view_link'=>$i_par_link,
'view_ret_id'=>$view_ret_id,
'string_current'=>$string_next,
'prev_base_index_page'=>$base_index_page,
'prev_body_link_page'=>$body_link_page,
'prev_body_all_page'=>$body_all_page])
        @endforeach
    @endif
@else
    {{-- при $heading = true и $base_right['is_list_base_read'] = true--}}
    @if($heading == true & $base_right['is_list_base_read'] == true)
        {{-- III.Вывод информации на весь экран в $heading--}}
        {{-- 'Показывать признак "В истории" при просмотре списков'--}}
        @if($base_right['is_list_hist_attr_enable'] == true)
            @include('layouts.item.show_history',['item'=>$item])
        @endif
        {{-- 'Показывать дату создания'--}}
        @if($base_right['is_list_base_sort_creation_date_desc'] == true)
            {{-- Одинаковые строки с '<h6></h6>' два раза по тексту в этом файле--}}
            <h6 class="pt-2 pl-1">{{trans('main.date')}}:</h6>
            {{$item->created_date()}}
        @endif
        @foreach($link_id_array as $value)
            <?php
            $link = Link::findOrFail($value);
            $base_link_right = $link_base_right_array[$link->id];
            ?>
            {{-- Не выводить основное изображение повторно--}}
            @if($nolink_id)
                @if($link->id == $nolink_id)
                    @continue
                @endif
            @endif
            <?php
            // Нужны все параметры GlobalController::view_info($item->id, $link->id, $role, $relit_id, false)
            $item_find = GlobalController::view_info($item->id, $link->id, $role, $relit_id, false);
            ?>
            @if($item_find)
                <?php
                // Проверка $item_find
                $item_find = GlobalController::items_check_right($item_find, $role, $relit_id);
                ?>
                @if($item_find)
                    {{-- Одинаковые строки с '<h6></h6>' два раза по тексту в этом файле--}}
                    <h6 class="pt-2 pl-1">{{$link->parent_label()}}:</h6>
                    @include('list.elements.heading_inf',
    ['project'=>$project, 'item'=>$item, 'item_find'=>$item_find, 'role'=>$role,
    'i'=>null,
    'label_name'=>"",
    'relit_id'=>$relit_id,
    'called_from_button'=>0,
    'view_link'=>$i_par_link,
    'view_ret_id'=>$view_ret_id,
    'string_current'=>$string_next,
    'prev_base_index_page'=>$base_index_page,
    'prev_body_link_page'=>$body_link_page,
    'prev_body_all_page'=>$body_all_page])
                @endif
            @endif
        @endforeach
    @else
        {{-- IV.Вывод информации в виде таблицы--}}
        {{--    style="margin: 0 auto;"--}}
        <table class="table table-sm table-hover
        @if($heading)
            table-borderless
@endif
            w-auto">
            @if(!$heading)
                <caption>{{trans('main.select_record_for_work')}}</caption>
            @endif
            <thead class="bg-transparent">
            <tr>
                {{--        Похожие проверки ниже по тексту--}}
                @if(!$heading)
                    <th rowspan="{{$rows + 1 - 1}}" style="width: 5%" class="text-center align-top">#</th>
                @endif
                {{-- 'Показывать признак "В истории" при просмотре списков'--}}
                @if($base_right['is_list_hist_attr_enable'] == true)
                    <th rowspan="{{$rows + 1 - 1}}" style="width: 5%" class="text-center align-top"
                        title="{{trans('main.history')}}">{{trans('main.small_history')}}</th>
                @endif
                {{-- 'Показывать дату создания'--}}
                @if($base_right['is_list_base_sort_creation_date_desc'] == true)
                    <th rowspan="{{$rows + 1 - 1}}" class="text-center align-top" rowspan="{{$rows + 1 - 1}}">
                        {{trans('main.date')}}
                    </th>
                @endif
                {{--        <th rowspan="{{$rows + 1}}" class="text-center align-top">Id</th>--}}
                {{--        Вывод в $base_index или в $item_body_base--}}
                @if($base_index || $item_body_base)
                    @if($base->is_code_needed == true)
                        <th class="text-center align-top" rowspan="{{$rows + 1 - 1}}" style="width: 5%"
                            class="text-center align-top">{{trans('main.code')}}</th>
                    @endif
                    @if($base_right['is_list_base_enable'] == true)
                        {{--                Если тип-вычисляемое наименование и Показывать Основу с вычисляемым наименованием--}}
                        {{--                или если тип-не вычисляемое наименование--}}
                        {{--            похожая проверка в ext_show.blade.php--}}
                        {{--                        Так нужно GlobalController::is_bs_calcname_check($base)--}}
                        @if(GlobalController::is_bs_calcname_check($base))
                            <th rowspan="{{$rows + 1 - 1}}" @include('layouts.class_from_base',['base'=>$base, 'align_top'=>true])>
                                {{--                        {{trans('main.name')}}--}}
                                {{--                                @if($view_link)--}}
                                {{--                                    {{$view_link->child_label(false)}}--}}
                                {{--                                @else--}}
                                {{--                                    {{$base->name(false)}}--}}
                                {{--                                @endif--}}
                                {{$v_label}}
                                <br><small><span class="text-label">{{$base->par_label_unit_meas()}}</span></small>
                            </th>
            @endif
            @endif
            @endif
            @if($rows > 0)
                @for($x = ($rows-1); $x >= 0; $x--)
                    @if($x != ($rows-1))
                        <tr>
                            @endif
                            @for($y=0; $y<$cols;$y++)
                                <?php
                                $link = Link::findOrFail($matrix[$x][$y]["link_id"]);
                                ?>
                                {{--    Основное изображение второй раз не выводится--}}
                                @if($link_image)
                                    @if($link->id == $link_image->id)
                                        @continue
                                    @endif
                                @endif
                                @if($matrix[$x][$y]["view_field"] != null)
                                    <th rowspan="{{$matrix[$x][$y]["rowspan"]}}"
                                        colspan="{{$matrix[$x][$y]["colspan"]}}"
                                        @if($x == 0)
                                        @if($heading)
                                        @if($link->parent_base->type_is_text())
                                        class="text-left"
                                        @else
                                        class="text-center align-top"
                                @endif
                                @else  // no ($heading)
                                @include('layouts.class_from_base',['base'=>$link->parent_base, 'align_top'=>true])
                                @endif
                                @else  // no ($x == 0)
                                class="text-center align-top"
                                @endif
                                >
                                @if($heading)
                                    <small>
                                        @endif
                                        @if($item_heading_base && $matrix[$x][$y]["fin_link"] == true)
                                            {{--                                                <a href="{{route('item.base_index',['base'=>$link->parent_base_id, 'project'=>$project, 'role'=>$role, 'relit_id' => $relit_id])}}"--}}
                                            {{--                                                   title="{{$link->parent_base->names()}}">--}}
                                            {{--                                                    {{GlobalController::calc_title_name($matrix[$x][$y]["view_name"], $heading, $heading)}}--}}
                                            {{--                                                </a>--}}
                                            <a href="{{route('item.base_index',['base'=>$link->parent_base_id, 'project'=>$project, 'role'=>$role, 'relit_id' => $link->parent_relit_id])}}"
                                               title="{{$link->parent_base->names()}}">
                                                {{--                                            {{GlobalController::calc_title_name($matrix[$x][$y]["view_name"])}}--}}
                                                {{GlobalController::calc_title_name(GlobalController::name_and_end_emoji($matrix[$x][$y]["view_name"], $link->parent_base), false, $heading)}}
                                            </a>
                                        @else
                                            {{--                                        {{GlobalController::calc_title_name($matrix[$x][$y]["view_name"],false, $heading)}}--}}
                                            {{GlobalController::calc_title_name(GlobalController::name_and_end_emoji($matrix[$x][$y]["view_name"], $link->parent_base), false, $heading)}}
                                        @endif
                                        {{--                                @if($item_heading_base && $matrix[$x][$y]["work_link"] == true)--}}
                                        {{--                                    @if($matrix[$x][$y]["work_link"] == true)--}}
                                        {{-- При $heading=true выводить единицу измерения в ячейке таблицы <td>, в "шапке" таблицы не выводить--}}
                                        {{-- При $heading=false не выводить единицу измерения в ячейке таблицы <td>, в "шапке" таблицы выводить--}}
                                        {{-- В этом файле две похожие проверки--}}
                                        @if($matrix[$x][$y]["work_link"] == true & !$heading)
                                            <br><small><span
                                                    class="text-label">{{$link->parent_base->par_label_unit_meas()}}</span></small>
                                        @endif
                                        @if($heading)
                                    </small>
                                    @endif
                                    </th>
                                    {{--                    {{$x}} {{$y}}  rowspan = {{$matrix[$x][$y]["rowspan"]}} colspan = {{$matrix[$x][$y]["colspan"]}} view_level_id = {{$matrix[$x][$y]["view_level_id"]}} view_level_name = {{$matrix[$x][$y]["view_level_name"]}}--}}
                                    {{--                    <br>--}}
                                @endif
                            @endfor
                            {{--                </tr>--}}
                            @if($x != ($rows-1))
                        </tr>
                        @endif
                        @endfor
                        </tr>
                    @endif
            </thead>
            <tbody>
            @foreach($its_page as $item)
                <?php
                $i++;
                ?>
                <tr>
                    {{--        Похожие проверки выше по тексту--}}
                    @if($base_index || $item_body_base)
                        <td class="text-center">
                            {{--                    Не удалять--}}
                            {{--                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc()])}}">--}}
                            {{--    "'par_link' => GlobalController::set_par_null($view_link)" неправильно--}}
                            {{--    "'par_link' => $view_link" правильно--}}
                            <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role,
    'usercode' =>GlobalController::usercode_calc(),
    'relit_id'=>$relit_id,
    'heading'=>$heading,
    'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
    'view_link' => GlobalController::set_par_null($view_link),
    'saveurl_show' =>$saveurl_show,
    'par_link' => $view_link,
    'parent_item'=>$parent_item,
    'parent_ret_id'=>$view_ret_id,
    'string_current' => $string_current,
    ])}}"
                               title="{{trans('main.viewing_record') . ' (id = ' . $item->id . ')'}}">
                                {{--                        'string_link_ids_current' => $string_link_ids_current,--}}
                                {{--                        'string_item_ids_current' => $string_item_ids_current,--}}
                                {{--                        'string_all_codes_current'=> $string_all_codes_current--}}
                                <small class="badge-pill badge-related">{{$i}}</small>
                            </a>
                        </td>
                    @endif
                    {{-- 'Показывать признак "В истории" при просмотре списков'--}}
                    @if($base_right['is_list_hist_attr_enable'] == true)
                        <td class="text-center">
                            @include('layouts.item.show_history',['item'=>$item])
                        </td>
                    @endif
                    {{-- 'Показывать дату создания'--}}
                    @if($base_right['is_list_base_sort_creation_date_desc'] == true)
                        <td class="text-center">
                            {{--                            @if($base_index || $item_body_base)--}}
                            <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(),
        'relit_id'=>$relit_id,
        'called_from_button'=>0,
        'view_link'=>$i_par_link,
        'view_ret_id'=>$view_ret_id,
        'string_current'=>$string_next,
        'prev_base_index_page'=>$base_index_page,
        'prev_body_link_page'=>$body_link_page,
        'prev_body_all_page'=>$body_all_page,
        ])}}"
                               title="{{$item->name()}}">
                                {{--                            'string_link_ids_current'=>$string_link_ids_next,--}}
                                {{--                            'string_item_ids_current'=>$string_item_ids_next,--}}
                                {{--                            'string_all_codes_current'=>$string_all_codes_next,--}}
                                {{--                                    @endif--}}
                                {{--                                {{GlobalController::date_and_emoji($item->created_date(), $heading & $emoji_enable)}}--}}
                                {{--                                {{GlobalController::date_and_emoji($item->created_date(), false)}}--}}
                                {{$item->created_date()}}
                                {{--                                    @if($base_index || $item_body_base)--}}
                            </a>
                            {{--                            @endif--}}
                        </td>
                    @endif
                    {{--            <td class="text-center">--}}
                    {{--                    Не удалять--}}
                    {{--                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc()])}}">--}}
                    {{--                <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,--}}
                    {{--                                    'heading'=>$heading, 'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,--}}
                    {{--                                    'par_link'=>$par_link, 'parent_item'=>$parent_item])}}">--}}
                    {{--                @if($base_index || $item_body_base)--}}
                    {{--                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,--}}
                    {{--                                       'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,'par_link'=>$par_link])}}"--}}
                    {{--                       title="{{$item->name()}}">--}}
                    {{--                        @endif--}}
                    {{--                        {{$item->id}}--}}
                    {{--                        @if($base_index || $item_body_base)--}}
                    {{--                    </a>--}}
                    {{--                @endif--}}
                    {{--            </td>--}}
                    @if($base_index || $item_body_base)
                        @if($base->is_code_needed == true)
                            <td class="text-center">
                                {{--                        @if($base_index || $item_body_base)--}}
                                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(),
        'relit_id'=>$relit_id,
        'called_from_button'=>0,
        'view_link'=>$i_par_link,
        'view_ret_id'=>$view_ret_id,
        'string_current'=>$string_next,
        'prev_base_index_page'=>$base_index_page,
        'prev_body_link_page'=>$body_link_page,
        'prev_body_all_page'=>$body_all_page,
        ])}}"
                                   title="{{$item->name()}}">
                                    {{--                            'string_link_ids_current'=>$string_link_ids_next,--}}
                                    {{--                            'string_item_ids_current'=>$string_item_ids_next,--}}
                                    {{--                            'string_all_codes_current'=>$string_all_codes_next,--}}
                                    {{--                                @endif--}}
                                    {{$item->code}}
                                    {{--                                @if($base_index || $item_body_base)--}}
                                </a>
                                {{--                        @endif--}}
                            </td>
                        @endif
                        @if($base_right['is_list_base_enable'] == true)
                            {{--                Если тип-вычисляемое наименование и Показывать Основу с вычисляемым наименованием--}}
                            {{--                или если тип-не вычисляемое наименование--}}
                            {{--                похожая проверка в list\table.php, ItemController::item_index() и ext_show.php--}}
                            {{-- Вывод вычисляемого наименования--}}
                            {{--                        Так нужно GlobalController::is_bs_calcname_check($base)--}}
                            @if(GlobalController::is_bs_calcname_check($base))
                                <td @include('layouts.class_from_base',['base'=>$base])>
                                    @if($base->type_is_image)
                                        {{--                                @include('view.img',['item'=>$item, 'size'=>"small", 'border'=>true, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])--}}
                                        @include('view.img',['item'=>$item, 'size'=>"small", 'border'=>false, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])
                                    @elseif($base->type_is_document)
                                        @include('view.doc',['item'=>$item, 'usercode'=>GlobalController::usercode_calc()])
                                    @else
                                        {{--                                <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),--}}
                                        {{--                                    'heading'=>$heading, 'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,--}}
                                        {{--                                    'par_link'=>$par_link, 'parent_item'=>$parent_item])}}">--}}
                                        {{--                                    --}}{{--                                                                Где $item->name() выходит в cards выводить "<?php echo GlobalController::to_html();?>"--}}
                                        {{--                                    {{$item->name()}}--}}
                                        {{--                                </a>--}}
                                        <?php
                                        // Похожие строки ниже/выше (метка 111); разница $base_right/$base_link_right
                                        // Открывать ext_show.php
                                        $ext_show_view = $is_table_body;
                                        // Открывать item_index.php
                                        $item_index_view = false;
                                        //                                if (!$ext_show_view) {
                                        // Открывать item_index.php - проверка
                                        if ($item_heading_base) {
                                            // В таблице-заголовке ($heading=true) ссылки будут, если '$base_link_right['is_list_base_calc'] == true'
                                            if ($base_right['is_list_base_calc'] == true) {
                                                $item_index_view = true;
                                            }
                                        } else {
                                            // В таблице-теле ($heading=false) все ссылки будут
                                            $item_index_view = true;
                                        }
                                        //                                }
                                        ?>
                                        {{--                                @if($ext_show_view)--}}
                                        {{--                                    --}}{{--                                        Вызывается ext_show.php--}}
                                        {{--                                    <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,--}}
                                        {{--                                    'heading'=>$heading, 'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,--}}
                                        {{--                                    'par_link'=>$par_link, 'parent_item'=>$parent_item])}}">--}}
                                        {{--                                        {{$item->name()}}--}}
                                        {{--                                    </a>--}}
                                        {{--                                @else--}}
                                        @if ($item_index_view)
                                            {{--                                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,--}}
                                            {{--        'usercode' =>GlobalController::usercode_calc(),--}}
                                            {{--        'relit_id'=>$relit_id,--}}
                                            {{--        'called_from_button'=>0,--}}
                                            {{--        'view_link'=>$i_par_link,--}}
                                            {{--        'view_ret_id'=>$view_ret_id,--}}
                                            {{--        'string_current'=>$string_next,--}}
                                            {{--        'prev_base_index_page'=>$base_index_page,--}}
                                            {{--        'prev_body_link_page'=>$body_link_page,--}}
                                            {{--        'prev_body_all_page'=>$body_all_page--}}
                                            {{--        ])}}"--}}
                                            {{--                                           title="{{$item->name()}}">--}}
                                            <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(),
        'relit_id'=>$relit_id,
        'called_from_button'=>0,
        'view_link'=>$i_par_link,
        'view_ret_id'=>$view_ret_id,
        'string_current'=>$string_next,
        'prev_base_index_page'=>$base_index_page,
        'prev_body_link_page'=>$body_link_page,
        'prev_body_all_page'=>$body_all_page
        ])}}"
                                               {{--                                           title="{{$item->name()}}"--}}
                                               title=""
                                            >
                                                {{--                                        'string_link_ids_current'=>$string_link_ids_next,--}}
                                                {{--                                        'string_item_ids_current'=>$string_item_ids_next,--}}
                                                {{--                                        'string_all_codes_current'=>$string_all_codes_next,--}}
                                                @endif
                                                {{--                                            @include('layouts.item.empty_name', ['name'=>$item->name()])--}}
                                                {{--                                            @include('layouts.item.empty_name', ['name'=>$item->nmbr()])--}}
                                                {{--                                            "$item->name()" чтобы быстрее выводилось на экран--}}
                                                {{-- @include('layouts.item.empty_name', ['name'=>$item->name()])--}}
                                                {{-- nmbr(true): $fullname = true/false - вывод полной строки (более 255 символов), исключить $view_link при расчете вычисляемого наименования--}}
                                                @include('layouts.item.empty_name', ['name'=>$item->nmbr(true, false, false, false, false, GlobalController::set_un_all_par_link_null($i_par_link), false, true, $relit_id, $role)])
                                                @if ($item_index_view)
                                            </a>
                                        @endif
                                        {{--                                @endif--}}
                                    @endif
                                </td>
                            @endif
                        @endif
                    @endif
                    {{--                <td class="text-center">&#8594;</td>--}}
                    @foreach($link_id_array as $value)
                        <?php
                        $link = Link::findOrFail($value);
                        $base_link_right = $link_base_right_array[$link->id];
                        ?>
                        {{--    Основное изображение второй раз не выводится--}}
                        @if($link_image)
                            @if($link->id == $link_image->id)
                                @continue
                            @endif
                        @endif
                        <td
                            @if($heading)
                            @if($link->parent_base->type_is_text())
                            class="text-left"
                            @else
                            class="text-center"
                            @endif
                        @else
                            @include('layouts.class_from_base',['base'=>$link->parent_base])
                            @endif
                        >
                            <?php
                            // Нужны все параметры GlobalController::view_info($item->id, $link->id, $role, $relit_id, false)
                            $item_find = GlobalController::view_info($item->id, $link->id, $role, $relit_id, false);
                            ?>
                            @if($item_find)
                                <?php
                                // Проверка $item_find
                                // $item_find = GlobalController::items_check_right($item_find, $role, $relit_id);
                                // "$link->parent_relit_id" нужно передавать
                                $item_find = GlobalController::items_check_right($item_find, $role, $link->parent_relit_id);
                                ?>
                                @if($item_find)
                                    @if($link->parent_base->type_is_image())
                                        {{--                                @include('view.img',['item'=>$item_find, 'size'=>"small", 'border'=>true, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])--}}
                                        @include('view.img',['item'=>$item_find, 'size'=>"small", 'border'=>false, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])
                                    @elseif($link->parent_base->type_is_document())
                                        @include('view.doc',['item'=>$item_find, 'usercode'=>GlobalController::usercode_calc()])
                                    @else
                                        {{--                                Не удалять: просмотр Пространство--}}
                                        {{--                                                                            проверка, если link - вычисляемое поле--}}
                                        {{--                                    @if ($link->parent_is_parent_related == true || $link->parent_is_numcalc == true)--}}
                                        {{--                                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc()])}}">--}}
                                        {{--                                            @else--}}
                                        {{--                                                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'par_link'=>$link])}}">--}}
                                        {{--                                                    @endif--}}
                                        {{--                                             Так использовать: 'item'=>$item--}}
                                        {{--                            <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),--}}
                                        {{--                                'heading'=>$heading, 'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,--}}
                                        {{--                                'par_link'=>$par_link, 'parent_item'=>$parent_item])}}">--}}
                                        {{--                                --}}{{--                            Где $item->name() выходит в cards выводить "<?php echo GlobalController::to_html();?>"--}}
                                        {{--                                {{$item_find->name(false,false,false)}}--}}
                                        {{--                            </a>--}}
                                        <?php
                                        // Похожие строки ниже/выше (метка 111); разница $base_right/$base_link_right
                                        // Открывать ext_show.php
                                        $ext_show_view = $is_table_body;
                                        // Открывать item_index.php
                                        $item_index_view = false;
                                        //                                if (!$ext_show_view) {
                                        //                                    // Открывать item_index.php - проверка
                                        //                                    if ($heading) {
                                        //                                        // В таблице-заголовке ($heading=true) ссылки будут, если '$base_link_right['is_list_base_calc'] == true'
                                        //                                        if ($base_link_right['is_list_base_calc'] == true) {
                                        //                                            $item_index_view = true;
                                        //                                        }
                                        //                                    } else {
                                        //                                        // В таблице-теле ($heading=false) все ссылки будут
                                        //                                        $item_index_view = true;
                                        //                                    }
                                        //                                }
                                        // Значение по умолчанию
                                        $string_value = $string_next;
                                        $relit_value = $relit_id;
                                        // Открывать item_index.php - проверка
                                        if ($item_heading_base) {
// В таблице-заголовке ($heading=true) ссылки будут, если '$base_link_right['is_list_base_calc'] == true'
// В таблице-заголовке ($heading=true) ссылки будут, если '$base_link_right['is_bsmn_base_enable'] == true'
//                                   if ($base_link_right['is_bsmn_base_enable'] == true) {
                                            if ($base_link_right['is_list_base_calc'] == true) {
                                                $item_index_view = true;
                                                // Для "шапки" item_index.php
//                                      $string_value = $string_current;
                                                $string_value = GlobalController::const_null();;
                                                $relit_value = $link_base_relit_id_array[$link->id];
                                            }
                                        } else {
// В таблице-теле ($heading=false) все ссылки будут
                                            $item_index_view = true;
                                            $string_value = $string_next;
                                            $relit_value = $relit_id;
                                        }
                                        ?>
                                        {{--                                @if($ext_show_view)--}}
                                        {{--                                        Вызывается ext_show.php--}}
                                        {{--                                <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,--}}
                                        {{--                                        'heading'=>$heading, 'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,--}}
                                        {{--                                    'par_link'=>$par_link, 'parent_item'=>$parent_item])}}">--}}
                                        {{--                                    @else--}}
                                        @if ($item_index_view)
                                            {{--                                        Вызывается item_index.php--}}
                                            <?php
                                            $i_item = null;
                                            //                                $i_par_link = null;
                                            if ($item_heading_base) {
                                                $i_item = $item_find;//
                                            } else {
                                                $i_item = $item;//
                                            }
                                            ?>
                                            {{--                                                                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$i_item, 'role'=>$role,--}}
                                            {{--                                            'usercode' =>GlobalController::usercode_calc(),--}}
                                            {{--                                            'relit_id'=>$relit_id,--}}
                                            {{--                                            'called_from_button'=>0,--}}
                                            {{--                                            'view_link'=>$i_par_link,--}}
                                            {{--                                            'view_ret_id'=>$view_ret_id,--}}
                                            {{--                                            'string_current'=>$string_next,--}}
                                            {{--                                            'prev_base_index_page'=>$base_index_page,--}}
                                            {{--                                            'prev_body_link_page'=>$body_link_page,--}}
                                            {{--                                            'prev_body_all_page'=>$body_all_page--}}
                                            {{--                                            ])}}"--}}
                                            {{--                                                                           title="">--}}
                                            {{--                                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$i_item, 'role'=>$role,--}}
                                            {{--        'usercode' =>GlobalController::usercode_calc(),--}}
                                            {{--        'relit_id'=>$link->parent_relit_id,--}}
                                            {{--        'called_from_button'=>0,--}}
                                            {{--        'view_link'=>$i_par_link,--}}
                                            {{--        'view_ret_id'=>$view_ret_id,--}}
                                            {{--        'string_current'=>$string_value,--}}
                                            {{--        'prev_base_index_page'=>$base_index_page,--}}
                                            {{--        'prev_body_link_page'=>$body_link_page,--}}
                                            {{--        'prev_body_all_page'=>$body_all_page--}}
                                            {{--        ])}}"--}}
                                            {{--                                       title="">--}}
                                            {{--                                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$i_item, 'role'=>$role,--}}
                                            {{--                                                'usercode' =>GlobalController::usercode_calc(),--}}
                                            {{--                                                'relit_id'=>$relit_value,--}}
                                            {{--                                                'called_from_button'=>0,--}}
                                            {{--                                                'view_link'=>$i_par_link,--}}
                                            {{--                                                'view_ret_id'=>$view_ret_id,--}}
                                            {{--                                                'string_current'=>$string_value,--}}
                                            {{--                                                'prev_base_index_page'=>$base_index_page,--}}
                                            {{--                                                'prev_body_link_page'=>$body_link_page,--}}
                                            {{--                                                'prev_body_all_page'=>$body_all_page--}}
                                            {{--                                                ])}}"--}}
                                            {{--                                       title="">--}}
                                            <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$i_item, 'role'=>$role,
                                                'usercode' =>GlobalController::usercode_calc(),
                                                'relit_id'=>$relit_value,
                                                'called_from_button'=>0,
                                                'view_link'=>$i_par_link,
                                                'view_ret_id'=>$view_ret_id,
                                                'string_current'=>$string_value,
                                                'prev_base_index_page'=>$base_index_page,
                                                'prev_body_link_page'=>$body_link_page,
                                                'prev_body_all_page'=>$body_all_page
                                                ])}}"
                                               title="">
                                                {{--                                    'string_link_ids_current'=>$string_link_ids_next,--}}
                                                {{--                                    'string_item_ids_current'=>$string_item_ids_next,--}}
                                                {{--                                    'string_all_codes_current'=>$string_all_codes_next,--}}
                                                @endif
                                                {{--                                    @endif--}}
                                                {{--                                                @if($heading)--}}
                                                {{--                                            <small>--}}
                                                {{--                                                <mark class="text-project">--}}
                                                {{--                                            @include('layouts.item.empty_name', ['name'=>$item_find->name(true,false,false,$heading & $emoji_enable)])--}}
                                                {{--                                                @else--}}
                                                {{--                                            @include('layouts.item.empty_name', ['name'=>$item_find->name(false,false,false,$heading & $emoji_enable)])--}}
                                                {{--                                                @endif--}}
                                                {{--                                        @if($heading & $link->parent_base->type_is_text() & $base_link_right['is_list_base_read'] == true)--}}
                                                @if($link->parent_base->type_is_text() & $base_link_right['is_list_base_read'] == true)
                                                    {{--                                                @include('layouts.item.empty_name', ['name'=>GlobalController::it_txnm_n2b($item_find,$heading & $emoji_enable)])--}}
                                                    @include('layouts.item.empty_name', ['name'=>GlobalController::it_txnm_n2b($item_find, false)])
                                                @else
                                                    {{--                                                @include('layouts.item.empty_name', ['name'=>$item_find->name(false,false,false,$heading & $emoji_enable)])--}}
                                                    {{--                                                @include('layouts.item.empty_name', ['name'=>$item_find->name(false, false, false, false, false)])--}}
                                                    {{--                                                @include('layouts.item.empty_name', ['name'=>$item_find->name(false, false, true, false, false)])--}}
                                                    {{-- При $heading=true выводить единицу измерения в ячейке таблицы <td>, в "шапке" таблицы не выводить--}}
                                                    {{-- При $heading=false не выводить единицу измерения в ячейке таблицы <td>, в "шапке" таблицы выводить--}}
                                                    {{-- В этом файле две похожие проверки--}}
                                                    @include('layouts.item.empty_name', ['name'=>$item_find->name(false, false, true, false, $heading)])
                                                @endif
                                                @if($heading)
                                                    {{--                                                </mark>--}}
                                                    {{--                                            </small>--}}
                                                @endif
                                                {{--                                    @if ($ext_show_view || $item_index_view)--}}
                                                @if ($item_index_view)
                                            </a>
                                        @endif
                                    @endif
                                @else
                                    {{--                            Этот блок нужен, не удалять--}}
                                    <div class="text-danger">
                                        {{--                                    {{GlobalController::empty_html()}}--}}
                                        {{GlobalController::access_restricted()}}
                                    </div>
                                @endif
                            @else
                                {{--                            <div class="text-danger">--}}
                                {{--                                --}}{{--                                    {{GlobalController::empty_html()}}--}}
                                {{--                                {{GlobalController::value_not_found()}}--}}
                                {{--                            </div>--}}
                            @endif
                        </td>
                    @endforeach
                    {{--                    Не удалять--}}
                    {{--                <td>{{$item->created_user_date()}}--}}
                    {{--                </td>--}}
                    {{--                <td>{{$item->updated_user_date()}}--}}
                    {{--                </td>--}}
                    {{--                <td class="text-left">--}}
                    {{--                    <?php--}}
                    {{--                    $link = Link::where('child_base_id', $item->base_id)->exists();--}}
                    {{--                    $main = Main::where('child_item_id', $item->id)->exists();--}}
                    {{--                    ?>--}}
                    {{--                    @if ($link != null)--}}
                    {{--                        @if ($main != null)--}}
                    {{--                            {{trans('main.full')}}--}}
                    {{--                        @endif--}}
                    {{--                    @else--}}
                    {{--                        <span class="text-danger font-weight-bold">{{trans('main.empty')}}</span>--}}
                    {{--                    @endif--}}
                    {{--                </td>--}}
                    {{--                <td class="text-left">--}}
                    {{--                    <?php--}}
                    {{--                    //                  $link = Link::where('parent_base_id', $item->base_id)->first();--}}
                    {{--                    //                  $main = Main::where('parent_item_id', $item->id)->first();--}}
                    {{--                    //                  $link = Link::all()->contains('parent_base_id', $item->base_id);--}}
                    {{--                    //                  $main = Main::all()->contains('parent_item_id', $item->id);--}}
                    {{--                    $link = Link::where('parent_base_id', $item->base_id)->exists();--}}
                    {{--                    $main = Main::where('parent_item_id', $item->id)->exists();--}}
                    {{--                    ?>--}}
                    {{--                    @if ($link != null)--}}
                    {{--                        @if ($main != null)--}}
                    {{--                            {{trans('main.used')}}--}}
                    {{--                        @else--}}
                    {{--                            {{trans('main.not_used')}}--}}
                    {{--                        @endif--}}
                    {{--                    @endif--}}
                    {{--                    /--}}
                    {{--                    @if  (count($item->parent_mains) == 0)--}}
                    {{--                        <b>{{trans('main.not_used')}}</b>--}}
                    {{--                    @else--}}
                    {{--                        {{trans('main.used')}}--}}
                    {{--                    @endif--}}
                    {{--                </td>--}}
                    {{--                Не удалять: другой способ просмотра--}}
                    {{--                <td class="text-center">--}}
                    {{--                    <a href="{{route('main.index_item',$item)}}" title="{{trans('main.information')}}">--}}
                    {{--                        <img src="{{Storage::url('info_record.png')}}" width="15" height="15"--}}
                    {{--                             alt="{{trans('main.info')}}">--}}
                    {{--                    </a>--}}
                    {{--                </td>--}}
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endif
