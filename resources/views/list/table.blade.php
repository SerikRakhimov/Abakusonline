<?php
use App\Models\Link;
use \App\Http\Controllers\GlobalController;
$link_id_array = $links_info['link_id_array'];
//dd($links_info['link_base_right_array']);
$link_base_right_array = $links_info['link_base_right_array'];
//dd($link_base_right_array);
$matrix = $links_info['matrix'];
$rows = $links_info['rows'];
$cols = $links_info['cols'];
//$i = $items->firstItem() - 1;
$i = 0;
?>
<table class="table table-sm table-bordered table-hover">
    @if(!$heading)
        <caption>{{trans('main.select_record_for_work')}}</caption>
    @endif
    <thead>
    <tr>
        {{--        Похожие проверки ниже по тексту--}}
        @if(!$heading)
            <th rowspan="{{$rows + 1}}" class="text-center align-top">#</th>
        @endif
        <th rowspan="{{$rows + 1}}" class="text-center align-top">Id</th>
        @if(!$heading)
            @if($base_right['is_list_base_enable'] == true)
                @if($base->is_code_needed == true)
                    <th class="text-center align-top" rowspan="{{$rows + 1}}">{{trans('main.code')}}</th>
                @endif
                {{--                Если тип-вычисляемое поле и Показывать Основу с вычисляемым наименованием--}}
                {{--                или если тип-не вычисляемое наименование--}}
                {{--            похожая проверка в ext_show.blade.php--}}
                @if(GlobalController::is_base_calcname_check($base, $base_right))
                    <th rowspan="{{$rows + 1}}" @include('layouts.class_from_base',['base'=>$base, 'align_top'=>true])>
                        {{trans('main.name')}}</th>
    @endif
    @endif
    @endif
    @if($rows > 0)
        @for($x = ($rows-1); $x >= 0; $x--)
            @if($x != ($rows-1))
                <tr>
                    @endif
                    @for($y=0; $y<$cols;$y++)
                        @if($matrix[$x][$y]["view_field"] != null)
                            <th rowspan="{{$matrix[$x][$y]["rowspan"]}}"
                                colspan="{{$matrix[$x][$y]["colspan"]}}"
                                class="text-center align-top">
                                @if($matrix[$x][$y]["fin_link"] == true)
                                    <?php
                                    $link = Link::findOrFail($matrix[$x][$y]["link_id"]);
                                    ?>
                                    <a href="{{route('item.base_index',['base'=>$link->parent_base_id, 'project'=>$project, 'role'=>$role])}}"
                                       title="{{$link->parent_base->names()}}">
                                        {{$matrix[$x][$y]["view_name"]}}
                                    </a>
                                @else
                                    {{$matrix[$x][$y]["view_name"]}}
                                @endif
                            </th>
                            {{--                    {{$x}} {{$y}}  rowspan = {{$matrix[$x][$y]["rowspan"]}} colspan = {{$matrix[$x][$y]["colspan"]}} view_level_id = {{$matrix[$x][$y]["view_level_id"]}} view_level_name = {{$matrix[$x][$y]["view_level_name"]}}--}}
                            {{--                    <br>--}}
                        @endif
                    @endfor
                </tr>
                @endfor
                </tr>
            @endif
    </thead>
    <tbody>
    @foreach($items as $item)
        <?php
        $i++;
        ?>
        <tr>
            {{--        Похожие проверки выше по тексту--}}
            @if(!$heading)
                <td class="text-center">
                    {{--                    Не удалять--}}
                    {{--                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc()])}}">--}}
                    <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
                                    'heading'=>$heading, 'body_page'=>$body_page, 'body_count'=>$body_count,'body_perpage'=>$body_perpage,
                                    'par_link'=>$par_link, 'parent_item'=>$parent_item])}}">
                        {{$i}}
                    </a>
                </td>
            @endif
            <td class="text-center">
                {{--                    Не удалять--}}
                {{--                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc()])}}">--}}
                <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
                                    'heading'=>$heading, 'body_page'=>$body_page, 'body_count'=>$body_count,'body_perpage'=>$body_perpage,
                                    'par_link'=>$par_link, 'parent_item'=>$parent_item])}}">
                    {{$item->id}}
                </a>
            </td>
            @if(!$heading)
                @if($base_right['is_list_base_enable'] == true)
                    @if($base->is_code_needed == true)
                        <td class="text-center">
                            <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
                                    'heading'=>$heading, 'body_page'=>$body_page, 'body_count'=>$body_count,'body_perpage'=>$body_perpage,
                                    'par_link'=>$par_link, 'parent_item'=>$parent_item])}}">
                                {{$item->code}}
                            </a>
                        </td>
                    @endif
                    {{--                Если тип-вычисляемое поле и Показывать Основу с вычисляемым наименованием--}}
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
                                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
                                       'usercode' =>GlobalController::usercode_calc()])}}"
                                   title="{{$item->name()}}">
                                    {{$item->name()}}
                                </a>
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
                            {{--                                Не удалять: просмотр Пространство--}}
                            {{--                                                                            проверка, если link - вычисляемое поле--}}
                            {{--                                    @if ($link->parent_is_parent_related == true || $link->parent_is_numcalc == true)--}}
                            {{--                                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc()])}}">--}}
                            {{--                                            @else--}}
                            {{--                                                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'par_link'=>$link])}}">--}}
                            {{--                                                    @endif--}}
                            {{--                                             Так использовать: 'item'=>$item--}}
                            {{--                            <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),--}}
                            {{--                                'heading'=>$heading, 'body_page'=>$body_page, 'body_count'=>$body_count,'body_perpage'=>$body_perpage,--}}
                            {{--                                'par_link'=>$par_link, 'parent_item'=>$parent_item])}}">--}}
                            {{--                                --}}{{--                            Где $item->name() выходит в cards выводить "<?php echo GlobalController::to_html();?>"--}}
                            {{--                                {{$item_find->name(false,false,false)}}--}}
                            {{--                            </a>--}}
{{--                            @if ($base_link_right['is_list_base_calc'] == true)--}}
                                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
                                        'usercode' =>GlobalController::usercode_calc(),'par_link'=>$link])}}"
                                       title="">
{{--                                    @endif--}}
                                    {{$item_find->name(false,false,false)}}
{{--                                    @if ($base_link_right['is_list_base_calc'] == true)--}}
                                </a>
{{--                            @endif--}}
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
