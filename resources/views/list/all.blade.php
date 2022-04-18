<?php
use App\Models\Link;
use \App\Http\Controllers\GlobalController;
$i = 0;
?>
{{--<table class="table table-sm table-bordered table-hover">--}}
<table class="table table-sm table-borderless table-hover">
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
        ?>
        <tr>
            <td class="text-center">
                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$main->child_item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,'par_link'=>GlobalController::par_link_const_textnull(),
        'string_link_ids_tree'=>$string_link_ids_array_next[$main->link_id],
        'string_item_ids_tree'=>$string_item_ids_array_next[$main->link_id]])}}"
                   title="{{$item->name()}}">
                .{{$i}}.
                </a>
            </td>
            {{--            <td class="text-left">--}}
            {{--                {{$main->link->child_labels()}}--}}
            {{--            </td>--}}
            <td class="text-left">
                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$main->child_item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,'par_link'=>GlobalController::par_link_const_textnull(),
        'string_link_ids_tree'=>$string_link_ids_array_next[$main->link_id],
        'string_item_ids_tree'=>$string_item_ids_array_next[$main->link_id]])}}"
                   title="{{$item->name()}}">
                {{GlobalController::calc_title_name($main->link->child_label())}}
                </a>
            </td>
            <td class="text-left">
                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$main->child_item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,'par_link'=>GlobalController::par_link_const_textnull(),
        'string_link_ids_tree'=>$string_link_ids_array_next[$main->link_id],
        'string_item_ids_tree'=>$string_item_ids_array_next[$main->link_id]])}}"
                   title="{{$item->name()}}">
                    {{$main->child_item->name()}}
                </a>
            </td>
            @if($next_all_is_code_enable == true)
                <td class="text-left">
                    @if($main->link->child_base->is_code_needed == true)
                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$main->child_item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,'par_link'=>GlobalController::par_link_const_textnull(),
        'string_link_ids_tree'=>$string_link_ids_array_next[$main->link_id],
        'string_item_ids_tree'=>$string_item_ids_array_next[$main->link_id]])}}"
                           title="{{$item->name()}}">
                        {{$main->child_item->code}}
                        </a>
                    @endif
                </td>
            @endif
        </tr>
    @endforeach

    @if(1==2)
        @foreach($items as $item)
            <?php
            $i++;
            ?>
            <tr>
                {{--        Похожие проверки выше по тексту--}}
                @if($base_index || $item_body_base)
                    <td class="text-center">
                        {{--                    Не удалять--}}
                        {{--                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc()])}}">--}}
                        <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,
    'heading'=>$heading, 'body_page'=>$body_page, 'body_count'=>$body_count,'body_perpage'=>$body_perpage,
    'par_link'=>$par_link, 'parent_item'=>$parent_item])}}">
                            -{{$i}}-
                        </a>
                    </td>
                @endif
                @if($base_index || $item_body_base)
                    @if($base->is_code_needed == true)
                        <td class="text-center">
                            @if($base_index || $item_body_base)
                                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
       'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,'par_link'=>$par_link])}}"
                                   title="{{$item->name()}}">
                                    @endif
                                    {{$item->code}}
                                    @if($base_index || $item_body_base)
                                </a>
                            @endif
                        </td>
                    @endif
                    @if($base_right['is_list_base_enable'] == true)
                        {{--                Если тип-вычисляемое наименование и Показывать Основу с вычисляемым наименованием--}}
                        {{--                или если тип-не вычисляемое наименование--}}
                        {{--            похожая проверка в ext_show.blade.php--}}
                        @if(GlobalController::is_base_calcname_check($base, $base_right))
                            <td @include('layouts.class_from_base',['base'=>$base])>
                                @if($base->type_is_image)
                                    @include('view.img',['item'=>$item, 'size'=>"small", 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'title'=>""])
                                @elseif($base->type_is_document)
                                    @include('view.doc',['item'=>$item, 'usercode'=>GlobalController::usercode_calc()])
                                @else
                                    {{--                                <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),--}}
                                    {{--                                    'heading'=>$heading, 'body_page'=>$body_page, 'body_count'=>$body_count,'body_perpage'=>$body_perpage,--}}
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
                                    {{--                                    'heading'=>$heading, 'body_page'=>$body_page, 'body_count'=>$body_count,'body_perpage'=>$body_perpage,--}}
                                    {{--                                    'par_link'=>$par_link, 'parent_item'=>$parent_item])}}">--}}
                                    {{--                                        {{$item->name()}}--}}
                                    {{--                                    </a>--}}
                                    {{--                                @else--}}
                                    @if ($item_index_view)
                                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
       'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,'par_link'=>$par_link,
        'string_link_ids_tree'=>$string_link_ids_next, 'string_item_ids_tree'=>$string_item_ids_next])}}"
                                           title="{{$item->name()}}">
                                            @endif
                                            {{$item->name()}}
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
                                @include('view.img',['item'=>$item_find, 'size'=>"small", 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'title'=>""])
                            @elseif($link->parent_base->type_is_document())
                                @include('view.doc',['item'=>$item_find, 'usercode'=>GlobalController::usercode_calc()])
                            @else
                                <?php
                                // Похожие строки ниже/выше (метка 111); разница $base_right/$base_link_right
                                // Открывать ext_show.php
                                $ext_show_view = $is_table_body;
                                // Открывать item_index.php
                                $item_index_view = false;
                                // Открывать item_index.php - проверка
                                if ($item_heading_base) {
// В таблице-заголовке ($heading=true) ссылки будут, если '$base_link_right['is_list_base_calc'] == true'
                                    if ($base_link_right['is_list_base_calc'] == true) {
                                        $item_index_view = true;
                                    }
                                } else {
// В таблице-теле ($heading=false) все ссылки будут
                                    $item_index_view = true;
                                }
                                ?>
                                @if ($item_index_view)
                                    {{--                                        Вызывается item_index.php--}}
                                    <?php
                                    $i_item = null;
                                    $i_par_link = null;
                                    if ($item_heading_base) {
                                        $i_item = $item_find;
                                        $i_par_link = $link;
                                    } else {
                                        $i_item = $item;
                                        $i_par_link = $par_link;
                                    }
                                    ?>
                                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$i_item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id, 'par_link'=>$i_par_link,
        'string_link_ids_tree'=>$string_link_ids_next, 'string_item_ids_tree'=>$string_item_ids_next])}}"
                                       title="">
                                        @endif
                                        {{--                                    @endif--}}
                                        {{$item_find->name(false,false,false)}}
                                        {{--                                    @if ($ext_show_view || $item_index_view)--}}
                                        @if ($item_index_view)
                                    </a>
                                @endif
                            @endif
                        @else
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
