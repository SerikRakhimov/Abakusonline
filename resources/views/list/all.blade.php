<?php
use App\Models\Link;
use \App\Http\Controllers\GlobalController;
use \App\Http\Controllers\ItemController;
// firstItem() - номер записи при постраничном выводе
// $i - переменная - порядковый номер отображаемых карточек
// $p - переменная - порядковый номер с учетом вывода пустых карточек
$i = $next_all_mains->firstItem() - 1;
$p = $i;
$num_cols = GlobalController::get_number_of_columns_info();
?>
@if($next_all_is_tileview == true)
    {{-- I.Вывод карт--}}
    @if($next_all_is_viewcards == true)
        {{--            Сортировка по links--}}
        @if(!$next_all_is_sortdate)
            <?php
            $link_id_current = 0;
            $main_first = $next_all_mains->first();
            $link_first = null;
            if ($main_first) {
                $link_first = $main_first->link;
                $link_id_current = $link_first->id;
            }
            ?>
            @if($link_first)
                {{-- Одинаковые строки (два раза по тексту в этом файле)--}}
                <br>
                <h4>{{$link_first->child_labels()}}:</h4>
            @endif
        @endif

        <div class="card-deck">
            {{--        $its_page используется--}}
            @foreach($next_all_mains as $main)
                <?php
                $i++;
                // '$p++' - правильно, '$p = $i' - неправильно
                $p++;
                $link = $main->link;
                $base = $link->child_base;
                $base_right = GlobalController::base_right($base, $role, $relit_id);
                $string_calc_next = ItemController::string_zip_current_next(
                    $string_link_ids_array_next[$main->link_id],
                    $string_item_ids_array_next[$main->link_id],
                    $string_relit_ids_array_next[$main->link_id],
                    $string_vwret_ids_array_next[$main->link_id],
                    $string_all_codes_array_next[$main->link_id]);
                $item = $main->child_item;
                $link_image = $item->base->get_link_primary_image();
                $item_find = null;
                if ($link_image) {
                    $item_find = GlobalController::view_info($item->id, $link_image->id, $role, $relit_id, true);
                }
                ?>
                {{--            Сортировка по links--}}
                @if(!$next_all_is_sortdate)
                    @if($link_id_current != $link->id)
                        {{--                    @if($i != $next_all_mains->firstItem())--}}
                        <?php
                        // Подсчитываем количество оставшихся колонок
                        $n = $num_cols - ($p % $num_cols) + 1;
                        ?>
                        {{--                        --}}{{--                                                 В цикле $n раз вставляем вставляем пустые колонки--}}
                        @for($k = 0; $k < $n; $k++)
                            {{--                                                                                                     Вставляем пустую карточку--}}
                            {{--                                                <div class="elements border-0 m-1">--}}
                            <div class="card m-2 bg-transparent">
                            </div>
                        @endfor
        </div>
        {{-- Одинаковые строки (два раза по тексту в этом файле)--}}
        <br>
        <h4>{{$link->child_labels()}}:</h4>
        <div class="card-deck">
            <?php
            $link_id_current = $link->id;
            $p = $p + $n;
            ?>
            @endif
            @endif
            {{--                @if($item_find)--}}
            {{-- Вывод карты--}}
            @include('list.elements.card',
['project'=>$project, 'item'=>$item, 'item_find'=>$item_find, 'role'=>$role,
'i'=>$i,
'label_name'=>GlobalController::br_work($main->link->child_label(true)),
'relit_id'=>$relit_id,
'called_from_button'=>0,
'view_link'=>$link,
'saveurl_show' =>$saveurl_show,
'view_ret_id'=>$view_ret_id,
'string_current'=>$string_calc_next,
'prev_base_index_page'=>$base_index_page,
'prev_body_link_page'=>$body_link_page,
'prev_body_all_page'=>$body_all_page])
            {{--                @endif--}}

            @if(($p % $num_cols == 0))
        </div>
        <div class="card-deck">
            @endif
            @endforeach
            {{-- Если строка из $num_cols элементов не завершилась до $num_cols столбцов--}}
            {{-- (т.е. $p не делится без остатка на $num_cols)--}}
            @if($p % $num_cols != 0)
                <?php
                // Подсчитываем количество оставшихся колонок
                $n = $num_cols - ($p % $num_cols);
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
        <?php
        $link_id_current = 0;
        ?>
        @foreach($next_all_mains as $main)
            <?php
            $i++;
            $link = $main->link;
            $base = $link->child_base;
            $base_right = GlobalController::base_right($base, $role, $relit_id);
            $string_calc_next = ItemController::string_zip_current_next(
                $string_link_ids_array_next[$main->link_id],
                $string_item_ids_array_next[$main->link_id],
                $string_relit_ids_array_next[$main->link_id],
                $string_vwret_ids_array_next[$main->link_id],
                $string_all_codes_array_next[$main->link_id]);
            $item = $main->child_item;
            $link_image = $item->base->get_link_primary_image();
            $item_find = null;
            if ($link_image) {
                $item_find = GlobalController::view_info($item->id, $link_image->id, $role, $relit_id, true);
            }
            ?>
            @if(!$next_all_is_sortdate)
                @if($link_id_current != $link->id)
                    <br>
                    <h4 class="pl-1">{{$link->child_labels()}}:</h4>
                    <?php
                    $link_id_current = $link->id;
                    ?>
                @endif
            @endif
            {{--            @if($item_find)--}}
            {{-- Вывод изображения--}}
            @include('list.elements.info',
['project'=>$project, 'item'=>$item, 'item_find'=>$item_find, 'role'=>$role,
'i'=>$i,
'label_name'=>$next_all_is_sortdate ? GlobalController::br_work($main->link->child_label(true)) : "",
'relit_id'=>$relit_id,
'called_from_button'=>0,
'view_link'=>$link,
'saveurl_show' =>$saveurl_show,
'view_ret_id'=>$view_ret_id,
'string_current'=>$string_calc_next,
'prev_base_index_page'=>$base_index_page,
'prev_body_link_page'=>$body_link_page,
'prev_body_all_page'=>$body_all_page])
            {{--            @endif--}}
        @endforeach
    @endif
@else
    {{-- III.Вывод информации в виде таблицы--}}
    {{-- III.1 Сортировка по дате создания записи--}}
    @if($next_all_is_sortdate)
        {{--<table class="table table-sm table-bordered table-hover">--}}
        <table class="table table-sm table-hover w-auto">
            {{--<table class="table table-sm table-borderless table-hover">--}}
            <caption>{{trans('main.select_record_for_work')}}</caption>
            <thead>
            <tr>
                <th class="text-center align-top">#</th>
                <th class="text-left align-top"></th>
                <th class="text-left align-top">{{trans('main.name')}}</th>
                @if($next_all_is_code_enable == true)
                    <th class="text-left align-top">{{trans('main.code')}}</th>
            @endif
            {{-- <th class="text-left align-top">{{trans('main.base')}}</th>--}}
            @if($base_right['is_list_base_enable'] == true)
                {{--                Если тип-вычисляемое наименование и Показывать Основу с вычисляемым наименованием--}}
                {{--                или если тип-не вычисляемое наименование--}}
                {{--            похожая проверка в ext_show.blade.php--}}
                {{--            @if(GlobalController::is_base_calcname_check($base, $base_right))--}}
                {{--                <th rowspan="{{$rows + 1}}" @include('layouts.class_from_base',['base'=>$base, 'align_top'=>true])>--}}
                {{--                    @if($par_link)--}}
                {{--                        {{$par_link->child_label()}}--}}
                {{--                    @else--}}
                {{--                        {{$base->name()}}--}}
                {{--                    @endif--}}
                {{--                </th>--}}
            @endif
            </thead>
            <tbody>
            @foreach($next_all_mains as $main)
                <?php
                $i++;
                $link = $main->link;
                $base = $link->child_base;
                $base_right = GlobalController::base_right($base, $role, $relit_id);
                $string_calc_next = ItemController::string_zip_current_next(
                    $string_link_ids_array_next[$main->link_id],
                    $string_item_ids_array_next[$main->link_id],
                    $string_relit_ids_array_next[$main->link_id],
                    $string_vwret_ids_array_next[$main->link_id],
                    $string_all_codes_array_next[$main->link_id]);
                ?>
                <tr>
                    <td class="text-center">
                        <a href="{{route('item.ext_show', ['item'=>$main->child_item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
    'relit_id'=>$relit_id,
    'heading'=>$heading, 'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
            'view_link' => GlobalController::set_par_null($view_link),
            'saveurl_show' =>$saveurl_show,
            'par_link'=>$main->link, 'parent_item'=>$item,
        'parent_ret_id' => $view_ret_id,
        'string_current' => $string_current
    ])}}"
                           title="{{trans('main.viewing_record')}}">
                            {{--                    'string_link_ids_current'=>$string_link_ids_current,--}}
                            {{--                    'string_item_ids_current'=>$string_item_ids_current,--}}
                            {{--                    'string_all_codes_current'=>$string_all_codes_current--}}
                            <span class="badge badge-related">{{$i}}</span>
                            {{--                        <span class="badge badge-pale">{{$i}}</span>--}}
                        </a>
                    </td>
                    {{--            <td class="text-left">--}}
                    {{--                {{$main->link->child_labels()}}--}}
                    {{--            </td>--}}
                    <td class="text-left">
                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$main->child_item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(),
        'relit_id'=>$relit_id,
        'called_from_button'=>0,
        'view_link'=>GlobalController::par_link_const_textnull(),
        'view_ret_id'=>$view_ret_id,
        'string_current'=>$string_calc_next,
        'prev_base_index_page'=>$base_index_page,
        'prev_body_link_page'=>$body_link_page,
        'prev_body_all_page'=>$body_all_page
        ])}}"
                           title="{{GlobalController::calc_title_name($main->link->child_label(true),true,false)}}">
                            {{--                    'string_link_ids_current'=>$string_link_ids_array_next[$main->link_id],--}}
                            {{--                    'string_item_ids_current'=>$string_item_ids_array_next[$main->link_id],--}}
                            {{--                    'string_all_codes_current'=>$string_all_codes_array_next[$main->link_id],--}}
                            <small><i>{{GlobalController::calc_title_name($main->link->child_label(true),true,false)}}</i></small>
                        </a>
                    </td>
                    <td class="text-left">
                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$main->child_item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(),
        'relit_id'=>$relit_id,
        'called_from_button'=>0,
        'view_link'=>GlobalController::par_link_const_textnull(),
        'view_ret_id'=>$view_ret_id,
        'string_current'=>$string_calc_next,
        'prev_base_index_page'=>$base_index_page,
        'prev_body_link_page'=>$body_link_page,
        'prev_body_all_page'=>$body_all_page
        ])}}"
                           title="{{GlobalController::calc_title_name($main->link->child_label(true))}}">
                            {{--                    'string_link_ids_current'=>$string_link_ids_array_next[$main->link_id],--}}
                            {{--                    'string_item_ids_current'=>$string_item_ids_array_next[$main->link_id],--}}
                            {{--                    'string_all_codes_current'=>$string_all_codes_array_next[$main->link_id],--}}
                            {{--                    Выводить вычисляемое наименование--}}
                            @if($next_all_is_calcname[$main->link_id])
                                {{--                        @include('layouts.item.empty_name', ['name'=>$main->child_item->nmbr()])--}}
                                {{--                        "child_item->name()" чтобы быстрее выводилось на экран--}}
                                {{--                            @include('layouts.item.empty_name', ['name'=>$main->child_item->name()])--}}
                                {{-- nmbr(true): $fullname = true/false - вывод полной строки (более 255 символов), исключить $main->link при расчете вычисляемого наименования--}}
                                {{--                            @include('layouts.item.empty_name', ['name'=>$main->child_item->nmbr(true,false,false,false,false,$main->link)])--}}
                                @include('layouts.item.empty_name', ['name'=>$main->child_item->nmbr(true,false,false,false,false,$main->link,false, true, $relit_id, $role)])
                                <?php
                                $item = $main->child_item;
                                $link_image = $item->base->get_link_primary_image();
                                $item_find = null;
                                if ($link_image) {
                                    $item_find = GlobalController::view_info($item->id, $link_image->id, $role, $relit_id, true);
                                }
                                ?>
                                @if($item_find)
                                    {{-- Вывод только изображения, похожие строки list.elements.info.php и list.all.php--}}
                                    <center>
                                        @include('view.img',['item'=>$item_find, 'var_percent'=>"50", 'border'=>true, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>$item->name()])
                                    </center>

                                @endif
                            @else
                                <span class="text-danger">
                        {{mb_strtolower(trans('main.empty'))}}
                        </span>
                            @endif
                        </a>
                    </td>
                    @if($next_all_is_code_enable == true)
                        <td class="text-left">
                            @if($main->link->child_base->is_code_needed == true)
                                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$main->child_item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(),
        'relit_id'=>$relit_id,
        'called_from_button'=>0,
        'view_link'=>GlobalController::par_link_const_textnull(),
        'view_ret_id'=>$view_ret_id,
        'string_current'=>$string_calc_next,
        'prev_base_index_page'=>$base_index_page,
        'prev_body_link_page'=>$body_link_page,
        'prev_body_all_page'=>$body_all_page
        ])}}"
                                   title="{{$item->name()}}">
                                    {{--                            'string_link_ids_current'=>$string_link_ids_array_next[$main->link_id],--}}
                                    {{--                            'string_item_ids_current'=>$string_item_ids_array_next[$main->link_id],--}}
                                    {{--                            'string_all_codes_current'=>$string_all_codes_array_next[$main->link_id],--}}
                                    {{$main->child_item->code}}
                                </a>
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        {{-- III.Вывод информации в виде таблицы--}}
        {{-- III.2 Сортировка по links--}}
        <?php
        $link_id_current = 0;
        ?>
        @foreach($next_all_mains as $main)
            <?php
            $i++;
            $link = $main->link;
            $base = $link->child_base;
            $base_right = GlobalController::base_right($base, $role, $relit_id);
            $string_calc_next = ItemController::string_zip_current_next(
                $string_link_ids_array_next[$main->link_id],
                $string_item_ids_array_next[$main->link_id],
                $string_relit_ids_array_next[$main->link_id],
                $string_vwret_ids_array_next[$main->link_id],
                $string_all_codes_array_next[$main->link_id]);
            $item = $main->child_item;
            $link_image = $item->base->get_link_primary_image();
            $item_find = null;
            if ($link_image) {
                $item_find = GlobalController::view_info($item->id, $link_image->id, $role, $relit_id, true);
            }
            ?>
            {{-- Если другой $link->id--}}
            @if($link_id_current != $link->id)
                <br>
                <h4 class="pl-1">{{$link->child_labels()}}:</h4>
                <?php
                $link_id_current = $link->id;
                ?>
            @endif
            {{--            @if($item_find)--}}
            {{-- Вывод изображения--}}
            {{--            Использовать 'label_name'=>""--}}
            @include('list.elements.info',
        ['project'=>$project, 'item'=>$item, 'item_find'=>$item_find, 'role'=>$role,
        'i'=>$i,
        'label_name'=>"",
        'relit_id'=>$relit_id,
        'called_from_button'=>0,
        'view_link'=>$link,
        'view_ret_id'=>$view_ret_id,
        'string_current'=>$string_calc_next,
        'prev_base_index_page'=>$base_index_page,
        'prev_body_link_page'=>$body_link_page,
        'prev_body_all_page'=>$body_all_page])
            {{--            @endif--}}
        @endforeach
    @endif
@endif
