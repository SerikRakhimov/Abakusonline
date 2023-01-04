<?php
use App\Models\Link;
use \App\Http\Controllers\GlobalController;
$link_id_array = $links_info['link_id_array'];
$link_base_right_array = $links_info['link_base_right_array'];
$matrix = $links_info['matrix'];
$rows = $links_info['rows'];
$cols = $links_info['cols'];
$i = 0;
if ($item_heading_base == true) {
    $i = 0;
} else {
    $i = $items->firstItem() - 1;
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
$tile_view = $base->tile_view($base_right);
$link_image = $tile_view['link'];
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
@if(($base_index==true || $item_body_base==true) & $tile_view['result'] == true)
    <div class="card-deck">
        {{--        $its_page используется--}}
        @foreach($its_page as $item)
            <?php
            //$i = $i + 1;
            $item_find = GlobalController::view_info($item->id, $link_image->id);
            ?>
            {{--                <div class="card text-center">--}}
            {{--                    <div class="card card-inverse text-center" style="background-color: rgba(222,255,162,0.23); border-color: #3548ee;">--}}
            <div class="card shadow m-2">
{{--                <p class="card-header text-center text-label">{{$item->base->name()}}: {{$item->id}}--}}
                <small class="card-header text-center text-title">{{$item->base->name()}}: {{$item->id}}
                    @if($base->is_code_needed == true)
                        {{--                        <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,--}}
                        {{--                                    'heading' => $heading,'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,--}}
                        {{--                                    'par_link'=>null, 'parent_item'=>null,--}}
                        {{--                                    'string_current' => $string_current,--}}
                        {{--                                    ])}}" title="{{$item->name()}}">--}}
                        {{--                            'string_all_codes_current' => $string_all_codes_current,--}}
                        {{--                            'string_link_ids_current' => $string_link_ids_current,--}}
                        {{--                            'string_item_ids_current' => $string_item_ids_current,--}}
                        &nbsp;({{trans('main.code')}}: {{$item->code}})
                        {{--                        </a>--}}
                    @endif
                </small>
                {{--                    <div class="card-body d-flex align-items-center">--}}
                <div class="card-body p-0">
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
                                @include('view.img',['item'=>$item_find, 'size'=>"medium", 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>true, 'card_img_top'=>true, 'title'=>$item->name()])
                            </a>
                        </div>
                    @endif
                    {{--                        <h6 class="card-title text-center pt-2">--}}
                    <div class="card-title text-center pt-2">
                        <h6>
                            {{--                            <a--}}
                            {{--                                href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,--}}
                            {{--                                    'heading' => $heading,'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,--}}
                            {{--                                    'par_link'=>null, 'parent_item'=>null,--}}
                            {{--                             'string_current' => $string_current,--}}
                            {{--                                    ])}}"--}}
                            {{--                                title="{{$item->name()}}">--}}
                            {{--                                --}}{{--                                'string_all_codes_current' => $string_all_codes_current,--}}
                            {{--                                --}}{{--                                'string_link_ids_current' => $string_link_ids_current,--}}
                            {{--                                --}}{{--                                'string_item_ids_current' => $string_item_ids_current,--}}
                            {{--                                --}}{{--                            Где $item->name() выходит в cards выводить "<?php echo GlobalController::to_html();?>"--}}
                            {{--                                <?php echo $item->nmbr(false);?>--}}
                            {{--                            </a>--}}
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
                                <?php echo $item->nmbr(false);?>
                            </a>
                        </h6>
                    </div>
                </div>
                {{--                    </div>--}}
{{--                <div class="card-footer">--}}
{{--                    <small class="text-title">--}}
{{--                        {{$item->created_at->Format(trans('main.format_date'))}}--}}
{{--                    </small>--}}
{{--                </div>--}}
                <div class="card-footer">
                    <div class="row">
                        {{--                        <div class="col-sm-6 text-left text-title">--}}
                        <div class="col-sm-4 text-left text-label">
                            <small class="text-title">
                                {{$i+1}}
                            </small>
                        </div>
                        <div class="col-sm-8 text-right">
                            <small class="text-title">
                                {{$item->created_at->Format(trans('main.format_date'))}}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $i++;
            ?>
            @if($i % 3 == 0)
    </div>
    <div class="card-deck">
        @endif
        @endforeach
        {{-- Если строка из 3-х элементов не завершилась до 3-х столбцов--}}
        {{-- (т.е. $i не делится без остатка на 3)--}}
        @if($i % 3 != 0)
            <?php
            // Подсчитываем количество оставшихся колонок
            $n = 3 - ($i % 3);
            ?>
            {{-- В цикле $n раз вставляем вставляем пустые колонки--}}
            @for($k = 0; $k < $n; $k++)
                {{-- Вставляем пустую карточку--}}
                {{--                        <div class="card border-0 m-1">--}}
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
    <table class="table table-sm table-hover
        @if($heading)
        table-borderless
        @endif
        ">
        @if(!$heading)
            <caption>{{trans('main.select_record_for_work')}}</caption>
        @endif
        <thead>
        <tr>
            {{--        Похожие проверки ниже по тексту--}}
            @if(!$heading)
                <th rowspan="{{$rows + 1}}" style="width: 5%" class="text-center align-top">#</th>
            @endif
            {{--        'Показывать признак "В истории" при просмотре списков'--}}
            @if($base_right['is_list_hist_attr_enable'] == true)
                <th rowspan="{{$rows + 1}}" style="width: 5%" class="text-center align-top"
                    title="{{trans('main.history')}}">{{trans('main.small_history')}}</th>
            @endif
            {{--        <th rowspan="{{$rows + 1}}" class="text-center align-top">Id</th>--}}
            {{--        Вывод в $base_index или в $item_body_base--}}
            @if($base_index || $item_body_base)
                @if($base->is_code_needed == true)
                    <th class="text-center align-top" rowspan="{{$rows + 1}}" style="width: 5%"
                        class="text-center align-top">{{trans('main.code')}}</th>
                @endif
                @if($base_right['is_list_base_sort_creation_date_desc'] == true)
                    <th class="text-center align-top" rowspan="{{$rows + 1}}">{{trans('main.date')}}</th>
                @endif
                @if($base_right['is_list_base_enable'] == true)
                    {{--                Если тип-вычисляемое наименование и Показывать Основу с вычисляемым наименованием--}}
                    {{--                или если тип-не вычисляемое наименование--}}
                    {{--            похожая проверка в ext_show.blade.php--}}
                    @if(GlobalController::is_base_calcname_check($base, $base_right))
                        <th rowspan="{{$rows + 1}}" @include('layouts.class_from_base',['base'=>$base, 'align_top'=>true])>
                            {{--                        @if($view_link)--}}
                            {{--                            {{$view_link->child_label()}}--}}
                            {{--                        @else--}}
                            {{--                            {{$base->name()}}--}}
                            {{--                        @endif--}}
                            @if($view_link)
                                {{$view_link->child_label()}}
                            @else
                                {{$base->name()}}
                            @endif
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
                            @if($matrix[$x][$y]["view_field"] != null)
                                <th rowspan="{{$matrix[$x][$y]["rowspan"]}}"
                                    colspan="{{$matrix[$x][$y]["colspan"]}}"
                                    @if($x == 0)
                                    @include('layouts.class_from_base',['base'=>$link->parent_base, 'align_top'=>true])
                                    @else
                                    class="text-center align-top"
                                    @endif
                                >
                                    @if($heading)
                                        <small>
                                            @endif
                                            @if($item_heading_base && $matrix[$x][$y]["fin_link"] == true)
                                                <a href="{{route('item.base_index',['base'=>$link->parent_base_id, 'project'=>$project, 'role'=>$role, 'relit_id' => $relit_id])}}"
                                                   title="{{$link->parent_base->names()}}">
                                                    {{GlobalController::calc_title_name($matrix[$x][$y]["view_name"], $heading, $heading)}}
                                                </a>
                                            @else
                                                {{GlobalController::calc_title_name($matrix[$x][$y]["view_name"], $heading, $heading)}}
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
                        {{--    "'par_link' => GlobalController::set_par_view_link_null($view_link)" неправильно--}}
                        {{--    "'par_link' => $view_link" правильно--}}
                        <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role,
    'usercode' =>GlobalController::usercode_calc(),
    'relit_id'=>$relit_id,
    'heading'=>$heading, 'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
    'view_link' => GlobalController::set_par_view_link_null($view_link),
    'par_link' => $view_link,
    'parent_item'=>$parent_item,
    'parent_ret_id'=>$view_ret_id,
    'string_current' => $string_current,
    ])}}"
                           title="{{trans('main.viewing_record')}}">
                            {{--                        'string_link_ids_current' => $string_link_ids_current,--}}
                            {{--                        'string_item_ids_current' => $string_item_ids_current,--}}
                            {{--                        'string_all_codes_current'=> $string_all_codes_current--}}
                            <span class="badge badge-related">{{$i}}</span>
                        </a>
                    </td>
                @endif
                {{--        'Показывать признак "В истории" при просмотре списков'--}}
                @if($base_right['is_list_hist_attr_enable'] == true)
                    <td class="text-center">
                        @include('layouts.item.show_history',['item'=>$item])
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
                                {{$item->created_date()}}
                                {{--                                    @if($base_index || $item_body_base)--}}
                            </a>
                            {{--                            @endif--}}
                        </td>
                    @endif
                    @if($base_right['is_list_base_enable'] == true)
                        {{--                Если тип-вычисляемое наименование и Показывать Основу с вычисляемым наименованием--}}
                        {{--                или если тип-не вычисляемое наименование--}}
                        {{--                похожая проверка в list\table.php, ItemController::item_index() и ext_show.php--}}
                        @if(GlobalController::is_base_calcname_check($base, $base_right))
                            <td @include('layouts.class_from_base',['base'=>$base])>
                                @if($base->type_is_image)
                                    @include('view.img',['item'=>$item, 'size'=>"small", 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])
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
                                           title="{{$item->name()}}">
                                            {{--                                        'string_link_ids_current'=>$string_link_ids_next,--}}
                                            {{--                                        'string_item_ids_current'=>$string_item_ids_next,--}}
                                            {{--                                        'string_all_codes_current'=>$string_all_codes_next,--}}
                                            @endif
                                            @include('layouts.item.empty_name', ['name'=>$item->name()])
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
                    <td
                        @include('layouts.class_from_base',['base'=>$link->parent_base])
                    >
                        <?php
                        $item_find = GlobalController::view_info($item->id, $link->id);
                        ?>
                        @if($item_find)
                            @if($link->parent_base->type_is_image())
                                @include('view.img',['item'=>$item_find, 'size'=>"small", 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])
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
                                // Открывать item_index.php - проверка
                                if ($item_heading_base) {
// В таблице-заголовке ($heading=true) ссылки будут, если '$base_link_right['is_list_base_calc'] == true'
// В таблице-заголовке ($heading=true) ссылки будут, если '$base_link_right['is_bsmn_base_enable'] == true'
                                    if ($base_link_right['is_bsmn_base_enable'] == true) {
                                        //$item_index_view = true;
                                    }
                                } else {
// В таблице-теле ($heading=false) все ссылки будут
                                    $item_index_view = true;
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
                                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$i_item, 'role'=>$role,
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
                                       title="">
                                        {{--                                    'string_link_ids_current'=>$string_link_ids_next,--}}
                                        {{--                                    'string_item_ids_current'=>$string_item_ids_next,--}}
                                        {{--                                    'string_all_codes_current'=>$string_all_codes_next,--}}
                                        @endif
                                        {{--                                    @endif--}}
                                        @if($heading)
                                            <small>
                                                <mark class="text-project">
                                                    @endif
                                                    @include('layouts.item.empty_name', ['name'=>$item_find->name(false,false,false)])
                                                    @if($heading)
                                                </mark>
                                            </small>
                                        @endif
                                        {{--                                    @if ($ext_show_view || $item_index_view)--}}
                                        @if ($item_index_view)
                                    </a>
                                @endif
                            @endif
                        @else
                            {{--                        <div class="text-danger">--}}
                            {{--                            {{GlobalController::empty_html()}}--}}
                            {{--                        </div>--}}
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
