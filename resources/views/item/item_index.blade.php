@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Item;
    use App\Models\Link;
    use App\Models\Main;
    use \App\Http\Controllers\GlobalController;
    use \App\Http\Controllers\ItemController;
    // Не удалять
    //        function objectToarray($data)
    //        {
    //            $array = (array)$data;
    //            return $array;
    //        }
    $relip_project = GlobalController::calc_relip_project($relit_id, $project);
    $base_index_page = 0;
    $body_link_page = 0;
    $body_all_page = 0;
    if ($body_items) {
        $body_link_page = $body_items->currentPage();
    }
    if ($next_all_mains) {
        $body_all_page = $next_all_mains->currentPage();
    }
    ?>
    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])
    {{--    <h3 class="display-5">--}}
    {{--        {{trans('main.space')}}--}}
    {{--        <span class="text-label">-</span> <span class="text-title">{{$item->base->info()}}</span>--}}
    {{--    </h3>--}}
    @foreach($tree_array as $value)
        <h6>
            {{GlobalController::calc_title_name($value['title_name'])}}:
            <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$value['item_id'], 'role'=>$role,
                                        'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,
                                        'par_link'=>$value['all_code'] ==GlobalController::const_alltrue() ? GlobalController::par_link_const_textnull():$value['link_id'],
                                        'string_link_ids_tree'=>$value['string_prev_link_ids'],
                                        'string_item_ids_tree'=>$value['string_prev_item_ids'],
                                        'string_all_codes_tree'=>$value['string_prev_all_codes']
                                        ])}}"
               title="{{$value['info_name']}}">
                                {{$value['item_name']}}
{{--                {{$value['info_name']}}--}}
            </a>
        </h6>
    @endforeach
    <hr>
    <p>
    <div class="container-fluid">
        <div class="row">
            <div class="col-10 text-left">
                <h5>
                    <?php
                    if ($current_link) {
                        $title = $current_link->parent_label();
                    } else {
                        $title = $item->base->name();
                    }
                    ?>
                    {{--                    Выводить вычисляемое наименование--}}
                    @if(GlobalController::is_base_calcname_check($item->base, $base_right))
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-8 text-left">
                                    @if ($base_right['is_list_base_calc'] == true)
                                        <a href="{{route('item.base_index', ['base'=>$item->base,
                            'project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])}}"
                                           title="{{$item->base->names()}}">
                                            @endif
                                            {{$title}}:
                                            @if ($base_right['is_list_base_calc'] == true)
                                        </a>
                                    @endif
                                    @if ($base_right['is_list_base_calc'] == true)
                                        <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role,
                                            'usercode' =>GlobalController::usercode_calc(),
                                            'relit_id'=>$relit_id
                                            ])}}"
                                           title="{{$item->cdnm()}}">
                                            {{$item->cdnm()}}
                                        </a>
                                    @else
                                        {{$item->name()}}
                                    @endif
                                </div>
                                <div class="col-4 text-left">
                                    @if($item->base->is_code_needed == true)
                                        {{trans('main.code')}}: <strong>{{$item->code}}</strong>
                                    @endif
                                </div>
                            </div>
                        </div>
                </h5>
                @else
                    <h5>
                        @if ($base_right['is_list_base_calc'] == true)
                            <a href="{{route('item.base_index', ['base'=>$item->base,
                            'project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])}}"
                               title="{{$item->base->names()}}">
                                @endif
                                {{$title}}:
                                @if($item->base->is_code_needed == true)
                                    {{trans('main.code')}}: <strong>{{$item->code}}</strong>
                                @endif
                                @if ($base_right['is_list_base_calc'] == true)
                            </a>
                        @endif
                    </h5>
                    {{--                    Нужно '@foreach($child_mains_link_is_calcname as $calcname_mains)'--}}
                    @foreach($child_mains_link_is_calcname as $calcname_mains)
                        @foreach($calcname_mains as $calcname_main)
                            <h6>
                                {{GlobalController::calc_title_name($calcname_main->link->parent_label())}}:
                                <strong>{{$calcname_main->parent_item->name()}}</strong>
                                @if($calcname_main->parent_item->base->is_code_needed == true)
                                    {{trans('main.code')}}: <strong>{{$calcname_main->parent_item->code}}</strong>
                                @endif
                            </h6>
                        @endforeach
                    @endforeach
                @endif
            </div>
            <div class="col-2 text-right">
                @if ($base_right['is_list_base_create'] == true)
                    <button type="button" class="btn btn-dreamer" title="{{trans('main.add')}}"
                            onclick="document.location='{{route('item.ext_create', ['base'=>$item->base,
                                             'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
                                             'relit_id' =>$relit_id,
                                             'heading'=>intval(true),
                                             'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                                             'par_link'=>$current_link, 'parent_item'=>null])}}'">
                        <i class="fas fa-plus d-inline"></i>&nbsp;{{trans('main.add')}}
                    </button>
                @endif
            </div>
            {{--            <div class="col-1 text-center">--}}
            {{--                <a href="{{route('item.ext_show', ['item'=>$item, 'role'=>$role])}}"--}}
            {{--                   title="{{trans('main.view')}}{{$item->base->is_code_needed?" (".trans('main.code')." = ".$item->code.")":""}}">--}}
            {{--                    <img src="{{Storage::url('view_record.png')}}" width="15" height="15"--}}
            {{--                         alt="{{trans('main.view')}}">--}}
            {{--                </a>--}}
            {{--            </div>--}}
            {{--            <div class="col-1 text-center">--}}
            {{--                <a href="{{route('item.ext_edit', ['item'=>$item, 'role'=>$role])}}" title="{{trans('main.edit')}}">--}}
            {{--                    <img src="{{Storage::url('edit_record.png')}}" width="15" height="15"--}}
            {{--                         alt="{{trans('main.edit')}}">--}}
            {{--                </a>--}}
            {{--            </div>--}}
            {{--            <div class="col-1 text-center">--}}
            {{--                <a href="{{route('item.ext_delete_question', ['item' => $item, 'role'=>$role, 'heading'=> true])}}"--}}
            {{--                   title="{{trans('main.delete')}}">--}}
            {{--                    <img src="{{Storage::url('delete_record.png')}}" width="15" height="15"--}}
            {{--                         alt="{{trans('main.delete')}}">--}}
            {{--                </a>--}}
            {{--            </div>--}}
        </div>
    </div>
    </p>
    @if(count($child_links) != 0)
        <?php
        //          Присваивания нужны
        $i_par_link = null;
        $i_parent_item = null;
        if ($current_link) {
            $i_par_link = $current_link;
            $i_parent_item = $item;
        }
        ?>
        @include('list.table',['base'=>$item->base, 'project'=>$project, 'links_info'=>$child_links_info, 'items'=>$items,
                'base_right'=>$base_right, 'relit_id'=>$relit_id,
                'heading'=>intval(true),
                 'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                'par_link'=>$i_par_link, 'parent_item'=>$i_parent_item, 'is_table_body'=>false,
                    'base_index'=>false, 'item_heading_base'=>true, 'item_body_base'=>false,
                    'string_link_ids_next'=>'', 'string_item_ids_next'=>'', 'string_all_codes_next'=>''])
    @endif
    {{--    <hr align="center" width="100%" size="2" color="#ff0000"/>--}}
    {{--        &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195;--}}
    {{--        <hr>--}}
    {{--        <div class="text-center">&#8595;</div>--}}
    <div class="container-fluid">
        <div class="row">
            <div class="col-10 text-left">
                @if(($prev_item) ||($next_item))
                    <ul class="pagination">
                        {{--        <li class="page-item"><a class="page-link"--}}
                        {{--                                 @if($prev_item)--}}
                        {{--                                 href="{{route('item.item_index', ['project'=>$project, 'item'=>$prev_item, 'role'=>$role,--}}
                        {{--                                'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id, 'par_link'=>$current_link])}}"--}}
                        {{--                                 title="{{$prev_item->cdnm()}}"--}}
                        {{--                                 @else--}}
                        {{--                                 style="cursor:default" href="#" title="{{trans('main.none')}}"--}}
                        {{--                @endif--}}
                        {{--            ><</a></li>--}}
                        @if($prev_item)
                            <li class="page-item">
                                <a class="page-link" href="{{route('item.item_index', ['project'=>$project, 'item'=>$prev_item, 'role'=>$role,
                                'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id, 'par_link'=>GlobalController::par_link_textnull($current_link),
                                        'string_link_ids_tree'=>$string_link_ids_current,
                                         'string_item_ids_tree'=>$string_item_ids_current,
                                         'string_all_codes_tree'=>$string_all_codes_current
                                         ])}}"
                                   title="{{$prev_item->cdnm()}}"><</a>
                            </li>
                        @endif
                        {{--        <li class="page-item"><a class="page-link"--}}
                        {{--                                 @if($next_item)--}}
                        {{--                                 href="{{route('item.item_index', ['project'=>$project, 'item'=>$next_item, 'role'=>$role,--}}
                        {{--                                'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id, 'par_link'=>$current_link])}}"--}}
                        {{--                                 title="{{$next_item->cdnm()}}"--}}
                        {{--                                 @else--}}
                        {{--                                 style="cursor:default" href="#" title="{{trans('main.none')}}"--}}
                        {{--                @endif--}}
                        {{--            >></a></li>--}}
                        @if($next_item)
                            <li class="page-item">
                                <a class="page-link" href="{{route('item.item_index', ['project'=>$project, 'item'=>$next_item, 'role'=>$role,
                                'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id, 'par_link'=>GlobalController::par_link_textnull($current_link),
                                        'string_link_ids_tree'=>$string_link_ids_current,
                                        'string_item_ids_tree'=>$string_item_ids_current,
                                        'string_all_codes_tree'=>$string_all_codes_current
                                        ])}}"
                                   title="{{$next_item->cdnm()}}">></a>
                            </li>
                        @endif
                    </ul>
                @endif
            </div>
            <div class="col-2 text-right">
                @if(count($next_all_links)>0)
                    <div class="row">
                        <div class="col-12 text-right">
                            <div class="dropdown">
                                <button type="button" class="btn btn-dreamer dropdown-toggle" data-toggle="dropdown">
                                    {{trans('main.link')}}
                                </button>
                                <div class="dropdown-menu">
                                    {{-- Если во всех $links не выводятся вычисляемые наименования, то выводится вариант 'all'--}}
                                    @if($next_all_is_enable)
                                        <a class="dropdown-item" href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
                                  'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,'par_link'=>GlobalController::par_link_const_textnull(),
                                  'string_link_ids_tree'=>$string_link_ids_current,
                                  'string_item_ids_tree'=>$string_item_ids_current,
                                  'string_all_codes_tree'=>$string_all_codes_current
                                  ])}}"
                                           title="{{$item->name()}}">
                                            {{GlobalController::option_all()}}
                                            @if($current_link == null)
                                                {{--                                        Этот символ используется в двух местах--}}
                                                &#10003;
                                            @endif
                                        </a>
                                    @endif
                                    @foreach($next_all_links as $key=>$value)
                                        <a class="dropdown-item" href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
                                          'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,'par_link'=>$value->id,
                                          'string_link_ids_tree'=>$string_link_ids_current,
                                           'string_item_ids_tree'=>$string_item_ids_current,
                                           'string_all_codes_tree'=>$string_all_codes_current
                                           ])}}"
                                           title="{{$item->name()}}">
                                            {{$value->child_labels()}}
                                            @if($current_link)
                                                @if($value->id == $current_link->id)
                                                    {{--                                        Этот символ используется в двух местах--}}
                                                    &#10003;
                                                @endif
                                            @endif
                                            @if(isset($array["\x00*\x00items"][$value->id]))
                                                *
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    {{--Похожие команды в ItemController::calc_tree_array() и item_index.php--}}
    @if($current_link)
        {{--        <hr>--}}
        {{--        <br>--}}
        {{--        <div class="text-center">&#8595;</div>--}}
        <!--                --><?php
        //                $mains = Main::all()->where('parent_item_id', $item->id)->where('link_id', $current_link->id)->sortBy(function ($main) {
        //                    return $main->link->child_base->name() . $main->child_item->name();
        //                });
        //                ?>
        {{--        Не удалять--}}
        {{--        <p>--}}
        {{--        <div class="container-fluid">--}}
        {{--            <div class="row">--}}
        {{--                <div class="col text-left">--}}
        {{--                    <h3>--}}
        {{--                    </h3>--}}
        {{--                    <h3>--}}
        {{--                        <a href="{{route('item.base_index', ['base'=>$current_link->child_base,--}}
        {{--                            'project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])}}"--}}
        {{--                           title="{{$current_link->child_base->names()}}">--}}
        {{--                            {{$current_link->child_labels()}}--}}
        {{--                        </a>--}}
        {{--                        ({{$current_link->parent_label()}} = {{$item->name()}}):--}}
        {{--                    </h3>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}
        {{--        </p>--}}

        @if($base_body_right['is_list_base_calc'] == true)
            <?php
            //                $message_bs_mc = GlobalController::base_maxcount_message($current_link->child_base);
            //                $message_bs_byuser_mc = GlobalController::base_byuser_maxcount_message($current_link->child_base);
            //                $message_ln_mc = GlobalController::link_maxcount_message($current_link);
            //                $message_it_mc = GlobalController::link_item_maxcount_message($current_link);
            //                $message_mc = ($message_bs_mc == "" ? "" : ', ' . PHP_EOL . $message_bs_mc)
            //                    . ($message_bs_byuser_mc == "" ? "" : ', ' . PHP_EOL . $message_bs_byuser_mc)
            //                    . ($message_ln_mc == "" ? "" : ', ' . PHP_EOL . $message_ln_mc)
            //                    . ($message_it_mc == "" ? "" : ', ' . PHP_EOL . $message_it_mc);
            //                $message_link = GlobalController::link_maxcount_validate($project, $current_link, true);
            //                $message_item = GlobalController::link_item_maxcount_validate($project, $item, $current_link, true);

            //      $next_all_links = $item->base->parent_links->where('id', '!=', $current_link->id);
            // исключить вычисляемые поля
            // Не удалять
            //        $next_all_links = $item->base->parent_links->where('parent_is_parent_related', false)->where('id', '!=', $current_link->id);
            //
            //        $next_all_links_fact = DB::table('mains')
            //            ->select('link_id')
            //            ->where('parent_item_id', $item->id)
            //            ->where('link_id', '!=', $current_link->id)
            //            ->distinct()
            //            ->get()
            //            ->groupBy('link_id');

            // $next_all_links = $item->base->parent_links->where('parent_is_parent_related', false);
            // Не удалять
            //                $next_all_links_fact = DB::table('mains')
            //                    ->select('link_id')
            //                    ->where('parent_item_id', $item->id)
            //                    ->distinct()
            //                    ->get()
            //                    ->groupBy('link_id');

            //                $array = objectToarray($next_all_links_fact);

            ?>
            <hr>
            <p>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-10 text-left">
                        <h3>
                            {{--                        @if($current_link)--}}
                            {{--                        {{$current_link->child_labels()}}:{{$current_link->child_base->name()}}--}}
                            {{--                        @else--}}
                            {{--                            {{$item->base->name()}}:--}}
                            {{--                        @endif--}}
                            <a href="{{route('item.base_index', ['base'=>$current_link->child_base,
                            'project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])}}"
                               title="{{$current_link->child_base->names() . $message_mc_info}}">
                                {{$current_link->child_labels()}}:
                            </a>
                        </h3>
                    </div>
                    <div class="col-2 text-right">
                        {{--                        @if ((count($body_items) > 0) || ($base_body_right['is_list_base_create'] == true))--}}
                        @if ($base_body_right['is_list_base_create'] == true)
                            {{--            Не удалять: используются $message_link и $message_item --}}
                            {{--                                @if($message_link == "" && $message_item == "")--}}
                            @if($message_mc_link_item == "")
                                <button type="button" class="btn btn-dreamer"
                                        {{--                        Выводится $message_mc--}}
                                        title="{{trans('main.add') . $message_mc_info}}"
                                        onclick="document.location='{{route('item.ext_create', ['base'=>$current_link->child_base_id,
                                        'project'=>$project, 'role'=>$role,
                                        'usercode' =>GlobalController::usercode_calc(),
                             'relit_id' =>$relit_id,
                             'heading'=>intval(false),
                             'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                             'par_link'=>$current_link, 'parent_item'=>$item])}}'">
                                    <i class="fas fa-plus d-inline"></i>&nbsp;{{trans('main.add')}}
                                </button>
                            @endif
                        @endif
                        {{--                        @endif--}}
                    </div>
                </div>
            </div>
            </p>
        @endif
        {{--        <div class="container-fluid">--}}
        {{--            <div class="row">--}}
        {{--                <div class="col text-right">--}}
        {{--                    <a href="{{route('item.ext_create', ['base'=>$current_link->child_base_id,--}}
        {{--                            'project'=>$project, 'role'=>$role, 'heading'=>intval(false), 'par_link'=>$current_link->id, 'parent_item'=>$item->id])}}"--}}
        {{--                       title="{{trans('main.add')}}">--}}
        {{--                        <img src="{{Storage::url('add_record.png')}}" width="15" height="15"--}}
        {{--                             alt="{{trans('main.add')}}">--}}
        {{--                    </a>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}
        @if (count($body_items) > 0)
            @include('list.table',['base'=>$current_link->child_base, 'project'=>$project, 'links_info'=>$child_body_links_info, 'items'=>$body_items,
        'base_right'=>$base_body_right, 'relit_id'=>$relit_id,
        'heading'=>intval(false),
        'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
        'par_link'=>$current_link, 'parent_item'=>$item, 'is_table_body'=>false,
            'base_index'=>false, 'item_heading_base'=>false, 'item_body_base'=>true,
            'string_link_ids_next'=>$string_link_ids_next,
            'string_item_ids_next'=>$string_item_ids_next,
            'string_all_codes_next'=>$string_all_codes_next])
            {{$body_items->links()}}
        @endif
        {{--            Вывод всех записей, с разным link--}}
        {{--Похожие команды в ItemController::calc_tree_array() и item_index.php--}}
    @else
        @if(count($next_all_links)>0)
            <hr>
            <p>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-10 text-left">
                        <h3>
                            {{--                        @if($current_link)--}}
                            {{--                        {{$current_link->child_labels()}}:{{$current_link->child_base->name()}}--}}
                            {{--                        @else--}}
                            {{--                            {{$item->base->name()}}:--}}
                            {{--                        @endif--}}
                            {{trans('main.all')}}:
                        </h3>
                    </div>
                    <div class="col-2 text-right">
                        {{-- Вся кнопка 'Добавить' доступна (для связей)--}}
                        @if($next_all_is_all_create == true)
                            <div class="dropdown">
                                <button type="button" class="btn btn-dreamer dropdown-toggle"
                                        data-toggle="dropdown">
                                    {{trans('main.add')}}
                                </button>
                                <div class="dropdown-menu">
                                    @foreach($next_all_links as $key=>$value)
                                        @if($next_all_is_create[$value->id] == true)
                                            @if($message_mc_link_array_item[$value->id] == "")
                                                <a class="dropdown-item" href="{{route('item.ext_create', ['base'=>$value->child_base_id,
                                                                                                'project'=>$project, 'role'=>$role,
                                                                                                 'usercode' =>GlobalController::usercode_calc(),
                                                                                     'relit_id' =>$relit_id,
                                                                                     'heading'=>intval(false),
                                                                                     'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                                                                                     'par_link'=>$value, 'parent_item'=>$item])}}"
                                                   title="{{trans('main.add') . $message_mc_array_info[$value->id]}}">
                                                    {{$value->child_labels()}}
                                                    {{--                                                <i class="fas fa-plus d-inline"></i>--}}
                                                    @if(isset($array["\x00*\x00items"][$value->id]))
                                                        *
                                                    @endif
                                                </a>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @if(count($next_all_mains) > 0)
                @include('list.all',['project'=>$project,
            'relit_id'=>$relit_id,
            'next_all_mains'=>$next_all_mains,
            'next_all_is_code_enable'=>$next_all_is_code_enable,
            'next_all_is_calcname' => $next_all_is_calcname,
            'next_all_is_enable' => $next_all_is_enable,
            'heading'=>intval(false),
            'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
            'parent_item'=>$item, 'is_table_body'=>false,
                'base_index'=>false, 'item_heading_base'=>false, 'item_body_base'=>true,
                'string_link_ids_next'=>$string_link_ids_next, 'string_item_ids_next'=>$string_item_ids_next,
            'string_link_ids_array_next' => $string_link_ids_array_next,
            'string_item_ids_array_next' => $string_item_ids_array_next,
            'string_all_codes_array_next' => $string_all_codes_array_next,
            'message_mc_array_info' => $message_mc_array_info, 'message_mc_link_array_item' => $message_mc_link_array_item
            ])
                {{$next_all_mains->links()}}
            @endif
        @endif
    @endif
@endsection
