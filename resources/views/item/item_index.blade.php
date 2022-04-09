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
    $body_page = 0;
    $body_count = 0;
    $body_perpage = 0;
    if ($body_items) {
        $body_page = $body_items->currentPage();
        $body_count = $body_items->count();
        $body_perpage = $body_items->perPage();
    }
    ?>
    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])
    {{--    <h3 class="display-5">--}}
    {{--        {{trans('main.space')}}--}}
    {{--        <span class="text-label">-</span> <span class="text-title">{{$item->base->info()}}</span>--}}
    {{--    </h3>--}}
    @foreach($tree_array as $value)
        <h6>{{$value['title_name']}}:
            <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$value['item_id'], 'role'=>$role,
                                        'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id, 'par_link'=>$value['link_id'],
                                        'string_link_ids_tree'=>$value['string_prev_link_ids'],
                                        'string_item_ids_tree'=>$value['string_prev_item_ids']])}}"
               title="">
                {{$value['item_name']}}
            </a>
        </h6>
    @endforeach
    <hr>
    <p>
    <div class="container-fluid">
        <div class="row">
            <div class="col-10 text-left">
                <h3>
                    @if ($base_right['is_list_base_calc'] == true)
                        <a href="{{route('item.base_index', ['base'=>$item->base,
                            'project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])}}"
                           title="{{$item->base->names()}}">
                            @endif
                            @if($current_link)
                                {{$current_link->parent_label()}}:
                            @else
                                {{$item->base->name()}}:
                            @endif
                            @if ($base_right['is_list_base_calc'] == true)
                        </a>
                    @endif
                    @if(GlobalController::is_base_calcname_check($item->base, $base_right))
                        {{--                        @if ($base_right['is_list_base_calc'] == true)--}}
                        {{--                            <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id])}}"--}}
                        {{--                               title="{{$item->cdnm()}}">--}}
                        {{--                                {{$item->cdnm()}}--}}
                        {{--                            </a>--}}
                        {{--                        @else--}}

                        {{$item->cdnm()}}

                        {{--                        @endif--}}
                    @endif
                </h3>
            </div>
            <div class="col-2 text-right">
                {{--                @if ($base_right['is_list_base_create'] == true)--}}
                {{--                    <button type="button" class="btn btn-dreamer" title="{{trans('main.add')}}"--}}
                {{--                            onclick="document.location='{{route('item.ext_create', ['base'=>$item->base,--}}
                {{--                             'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),--}}
                {{--                             'relit_id' =>$relit_id,--}}
                {{--                             'heading'=>intval(true), 'body_page'=>$body_page, 'body_count'=>$body_count,'body_perpage'=>$body_perpage,--}}
                {{--                             'par_link'=>$current_link, 'parent_item'=>null])}}'">--}}
                {{--                        <i class="fas fa-plus d-inline"></i>&nbsp;{{trans('main.add')}}--}}
                {{--                    </button>--}}
                {{--                @endif--}}
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
                'heading'=>intval(true), 'body_page'=>$body_page, 'body_count'=>$body_count,'body_perpage'=>$body_perpage,
                'par_link'=>$i_par_link, 'parent_item'=>$i_parent_item, 'is_table_body'=>false,
                    'base_index'=>false, 'item_heading_base'=>true, 'item_body_base'=>false,
                    'string_link_ids_next'=>'', 'string_item_ids_next'=>''])
    @endif
    {{--    <hr align="center" width="100%" size="2" color="#ff0000"/>--}}
    {{--        &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195;--}}
    {{--        <hr>--}}
    {{--        <div class="text-center">&#8595;</div>--}}
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
                                'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id, 'par_link'=>$current_link])}}"
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
                                'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id, 'par_link'=>$current_link])}}"
                       title="{{$next_item->cdnm()}}">></a>
                </li>
            @endif
        </ul>
    @endif
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

        <?php
        $message_bs_mc = GlobalController::base_maxcount_message($current_link->child_base);
        $message_bs_byuser_mc = GlobalController::base_byuser_maxcount_message($current_link->child_base);
        $message_ln_mc = GlobalController::link_maxcount_message($current_link);
        $message_it_mc = GlobalController::link_item_maxcount_message($current_link);
        $message_mc = ($message_bs_mc == "" ? "" : ', ' . PHP_EOL . $message_bs_mc)
            . ($message_bs_byuser_mc == "" ? "" : ', ' . PHP_EOL . $message_bs_byuser_mc)
            . ($message_ln_mc == "" ? "" : ', ' . PHP_EOL . $message_ln_mc)
            . ($message_it_mc == "" ? "" : ', ' . PHP_EOL . $message_it_mc);
        $message_link = GlobalController::link_maxcount_validate($project, $current_link, true);
        $message_item = GlobalController::link_item_maxcount_validate($project, $item, $current_link, true);

        //      $next_links_plan = $item->base->parent_links->where('id', '!=', $current_link->id);
        // исключить вычисляемые поля
        // Не удалять
        //        $next_links_plan = $item->base->parent_links->where('parent_is_parent_related', false)->where('id', '!=', $current_link->id);
        //
        //        $next_links_fact = DB::table('mains')
        //            ->select('link_id')
        //            ->where('parent_item_id', $item->id)
        //            ->where('link_id', '!=', $current_link->id)
        //            ->distinct()
        //            ->get()
        //            ->groupBy('link_id');

        // $next_links_plan = $item->base->parent_links->where('parent_is_parent_related', false);
        // Не удалять
        //                $next_links_fact = DB::table('mains')
        //                    ->select('link_id')
        //                    ->where('parent_item_id', $item->id)
        //                    ->distinct()
        //                    ->get()
        //                    ->groupBy('link_id');

        //                $array = objectToarray($next_links_fact);

        ?>
        @if($base_body_right['is_list_base_calc'] == true)
            @if ((count($body_items) > 0) || ($base_body_right['is_list_base_create'] == true))
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
                                   title="{{$current_link->child_base->names() . $message_mc}}">
                                    {{$current_link->child_labels()}}:
                                </a>
                            </h3>
                        </div>
                        <div class="col-2 text-right">
                            @if ($base_body_right['is_list_base_create'] == true)
                                {{--            Не удалять: используются $message_link и $message_item --}}
                                @if($message_link == "" && $message_item == "")
                                    <button type="button" class="btn btn-dreamer"
                                            {{--                        Выводится $message_mc--}}
                                            title="{{trans('main.add') . $message_mc}}"
                                            onclick="document.location='{{route('item.ext_create', ['base'=>$current_link->child_base_id,
                                        'project'=>$project, 'role'=>$role,
                                         'usercode' =>GlobalController::usercode_calc(),
                             'relit_id' =>$relit_id,
                             'heading'=>intval(false), 'body_page'=>$body_page, 'body_count'=>$body_count,'body_perpage'=>$body_perpage,
                             'par_link'=>$current_link, 'parent_item'=>$item])}}'">
                                        <i class="fas fa-plus d-inline"></i>&nbsp;{{trans('main.add')}}
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
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
                </p>
                @if (count($body_items) > 0)
                    @include('list.table',['base'=>$current_link->child_base, 'project'=>$project, 'links_info'=>$child_body_links_info, 'items'=>$body_items,
                'base_right'=>$base_body_right, 'relit_id'=>$relit_id,
                'heading'=>intval(false), 'body_page'=>$body_page, 'body_count'=>$body_count,'body_perpage'=>$body_perpage,
                'par_link'=>$current_link, 'parent_item'=>$item, 'is_table_body'=>false,
                    'base_index'=>false, 'item_heading_base'=>false, 'item_body_base'=>true,
                    'string_link_ids_next'=>$string_link_ids_next, 'string_item_ids_next'=>$string_item_ids_next])
                    {{$body_items->links()}}
                    {{--            {{$body_items->currentPage()}}--}}
                    {{--            {{$body_count = $body_items->count()}}--}}
                    {{--            {{$body_perpage = $body_items->perPage()}}--}}
                @endif
            @endif
            @if (count($next_links_plan) > 1)
                <hr>
                <form action="{{route('item.store_link_change')}}" method="POST" enctype=multipart/form-data>
                    <div class="form-row">
                        @csrf
                        <input type="hidden" name="project_id" value="{{$project->id}}">
                        <input type="hidden" name="item_id" value="{{$item->id}}">
                        <input type="hidden" name="role_id" value="{{$role->id}}">
                        <input type="hidden" name="relit_id" value="{{$relit_id}}">
                        <div class="d-flex justify-content-end align-items-center mt-0">
                            <div class="col-auto">
                                {{--                            <label for="link_id">{{trans('main.another_attitude')}} = </label>--}}
                                <label for="link_id">{{trans('main.link')}} = </label>
                            </div>
                            <div class="">
                                <select class="form-control"
                                        name="link_id"
                                        id="link_id"
                                        class="form-control @error('link_id') is-invalid @enderror">
                                    @foreach($next_links_plan as $key=>$value)
                                        <option value="{{$value->id}}"
                                                {{--                                                                                    @if(!isset($array["\x00*\x00items"][$value->id]))--}}
                                                {{--                                                                                    disabled--}}
                                                {{--                                                                                @endif--}}
                                                @if($value->id == $current_link->id)
                                                selected
                                            @endif
                                        >
                                            {{--                                                                                {{$value->parent_label()}} {{$main->child_item->name()}} ({{mb_strtolower(trans('main.on'))}} {{$value->child_labels()}})--}}
                                            {{--                                        {{$value->child_labels()}} ({{$value->parent_label()}})--}}
                                            {{$value->child_labels()}}
                                            @if($value->id == $current_link->id)
                                                &#10003;
                                            @endif
                                            @if(isset($array["\x00*\x00items"][$value->id]))
                                                *
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('link_id')
                                <div class="text-danger">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                            <div class="col-2 ml-auto">
                                <button type="submit" class="btn btn-dreamer"
                                >{{trans('main.select')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        @endif
@endsection
