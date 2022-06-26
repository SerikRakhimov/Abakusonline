<?php
use App\Models\Link;
use \App\Http\Controllers\GlobalController;
use \App\Http\Controllers\ItemController;
$i = $next_all_mains->firstItem() - 1;
?>
<table class="table table-sm table-bordered table-hover">
{{--<table class="table table-sm table-borderless table-hover">--}}
    <caption>{{trans('main.select_record_for_work')}}</caption>
    <thead>
    <tr>
        <th class="text-center align-top">#</th>
        {{--        <th class="text-left align-top">{{trans('main.link')}}</th>--}}
        <th class="text-left align-top">{{trans('main.base')}}</th>
        <th class="text-left align-top">{{trans('main.name')}}</th>
        @if($next_all_is_code_enable == true)
            <th class="text-left align-top">{{trans('main.code')}}</th>
    @endif
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
        $base = $main->link->child_base;
        $base_right = GlobalController::base_right($base, $role, $relit_id);
        $string_calc_next = ItemController::string_zip_current_next(
            $string_link_ids_array_next[$main->link_id],
            $string_item_ids_array_next[$main->link_id],
            $string_relit_ids_array_next[$main->link_id],
            $string_all_codes_array_next[$main->link_id]);
        ?>
            <tr>
            <td class="text-center">
                <a href="{{route('item.ext_show', ['item'=>$main->child_item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
    'relit_id'=>$relit_id,
    'heading'=>$heading, 'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
            'view_link' => GlobalController::set_par_view_link_null($view_link),
            'par_link'=>$main->link, 'parent_item'=>$item,
        'parent_ret_id' => $view_ret_id,
        'string_current' => $string_calc_next,
    ])}}"
                    title = "{{trans('main.viewing_record')}}">
{{--                    'string_link_ids_current'=>$string_link_ids_current,--}}
{{--                    'string_item_ids_current'=>$string_item_ids_current,--}}
{{--                    'string_all_codes_current'=>$string_all_codes_current--}}
                <span class="badge badge-related">{{$i}}</span>
                </a>
            </td>
            {{--            <td class="text-left">--}}
            {{--                {{$main->link->child_labels()}}--}}
            {{--            </td>--}}
            <td class="text-left">
                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$main->child_item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(),
        'relit_id'=>$relit_id,
        'view_link'=>GlobalController::par_link_const_textnull(),
        'view_ret_id'=>$view_ret_id,
        'string_current'=>$string_calc_next,
        'prev_base_index_page'=>$base_index_page,
        'prev_body_link_page'=>$body_link_page,
        'prev_body_all_page'=>$body_all_page
        ])}}"
                   title="{{GlobalController::calc_title_name($main->link->child_label())}}">
{{--                    'string_link_ids_current'=>$string_link_ids_array_next[$main->link_id],--}}
{{--                    'string_item_ids_current'=>$string_item_ids_array_next[$main->link_id],--}}
{{--                    'string_all_codes_current'=>$string_all_codes_array_next[$main->link_id],--}}
                    <small>{{GlobalController::calc_title_name($main->link->child_label())}}</small>
                </a>
            </td>
            <td class="text-left">
                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$main->child_item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(),
        'relit_id'=>$relit_id,
        'view_link'=>GlobalController::par_link_const_textnull(),
        'view_ret_id'=>$view_ret_id,
        'string_current'=>$string_calc_next,
        'prev_base_index_page'=>$base_index_page,
        'prev_body_link_page'=>$body_link_page,
        'prev_body_all_page'=>$body_all_page
        ])}}"
                   title="{{GlobalController::calc_title_name($main->link->child_label())}}">
{{--                    'string_link_ids_current'=>$string_link_ids_array_next[$main->link_id],--}}
{{--                    'string_item_ids_current'=>$string_item_ids_array_next[$main->link_id],--}}
{{--                    'string_all_codes_current'=>$string_all_codes_array_next[$main->link_id],--}}
                    {{--                    Выводить вычисляемое наименование--}}
                    @if($next_all_is_calcname[$main->link_id])
                        @include('layouts.item.empty_name', ['name'=>$main->child_item->name()])
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
