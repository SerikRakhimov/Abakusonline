@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Item;
    use App\Models\Link;
    use App\Models\Main;
    use \App\Http\Controllers\GlobalController;
    use \App\Http\Controllers\ItemController;
    $child_links = $item->base->child_links->sortBy('parent_base_number');
    $child_links_info = ItemController::links_info($item->base, $role);
    $child_link_id_array = $child_links_info['link_id_array'];
    $project = $item->project;
    $base_right = GlobalController::base_right($item->base, $role);
    $items_right = GlobalController::items_right($item->base, $project, $role, null, null, $item->id);
    $items = $items_right['itget'];
    function objectToarray($data)
    {
        $array = (array)$data;
        return $array;
    }
    ?>
    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role])
    <?php
    $current_link = null;  // нужно
    if (@$par_link) {
        $current_link = $par_link;
        $current_link = Link::find($current_link->id);  // проверка существования в базе данных
    }
    if (!$current_link) {
        // Находим заполненный подчиненный link
        $parent_links = $item->base->parent_links;
        if (count($parent_links) > 0) {
            $next_links_fact1 = DB::table('mains')
                ->select('link_id')
                ->where('parent_item_id', $item->id)
                ->distinct()
                ->get()
                ->groupBy('link_id');
            // Если найдены - берем первый
            if (count($next_links_fact1) > 0) {
                $current_link = Link::find($next_links_fact1->first()[0]->link_id);
                // Если не найдены - берем первый пустой (без данных)
            } else {
                $current_link = $parent_links[0];
            }
        };
    }
    ?>
    <p>
    <div class="container-fluid">
        <div class="row">
            <div class="col text-left">
                <h3>
                    @if ($base_right['is_list_base_calc'] == true)
                        <a href="{{route('item.base_index', ['base'=>$item->base,
                            'project'=>$project, 'role'=>$role])}}" title="{{$item->base->names()}}">
                            @endif
                            @if($current_link)
                                {{$current_link->parent_label()}}:
                            @else
                                {{$item->base->name()}}:
                            @endif
                            @if ($base_right['is_list_base_calc'] == true)
                        </a>
                    @endif
                    <a href="{{route('item.ext_show', ['item'=>$item, 'role'=>$role])}}"
                       title="{{$item->cdnm()}}">
                        {{$item->cdnm()}}
                    </a>
                </h3>
            </div>
            <div class="col-1 text-center">
                <a href="{{route('item.ext_create', ['base'=>$item->base,
                            'project'=>$project, 'role'=>$role, 'heading'=>intval(true)])}}"
                   title="{{trans('main.add')}}">
                    <img src="{{Storage::url('add_record.png')}}" width="15" height="15"
                         alt="{{trans('main.add')}}">
                </a>
            </div>
            <div class="col-1 text-center">
                <a href="{{route('item.ext_show', ['item'=>$item, 'role'=>$role])}}"
                   title="{{trans('main.view')}}{{$item->base->is_code_needed?" (".trans('main.code')." = ".$item->code.")":""}}">
                    <img src="{{Storage::url('view_record.png')}}" width="15" height="15"
                         alt="{{trans('main.view')}}">
                </a>
            </div>
            <div class="col-1 text-center">
                <a href="{{route('item.ext_edit', ['item'=>$item, 'role'=>$role])}}" title="{{trans('main.edit')}}">
                    <img src="{{Storage::url('edit_record.png')}}" width="15" height="15"
                         alt="{{trans('main.edit')}}">
                </a>
            </div>
            <div class="col-1 text-center">
                <a href="{{route('item.ext_delete_question', ['item' => $item, 'role'=>$role, 'heading'=> true])}}"
                   title="{{trans('main.delete')}}">
                    <img src="{{Storage::url('delete_record.png')}}" width="15" height="15"
                         alt="{{trans('main.delete')}}">
                </a>
            </div>
        </div>
    </div>
    </p>
    @if(count($child_links) !=0)
        {{--        <table class="table table-sm table-borderless">--}}
        <table class="table table-sm table-bordered">
            <thead>
            <tr>
                @foreach($child_links as $link)
                    <th>
                        <a href="{{route('item.base_index',['base'=>$link->parent_base,
                            'project'=>$project, 'role'=>$role])}}"
                           title="{{$link->parent_base->names()}}">
                            {{$link->parent_label()}}
                        </a>
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            <tr>
                @foreach($child_links as $link)
                    <?php
                    $item_find = GlobalController::view_info($item->id, $link->id);
                    ?>
                    <td>
                        @if($item_find)
                            {{--проверка на вычисляемые поля--}}
                            @if($link->parent_is_parent_related == false)
                                <a href="{{route('item.item_index', ['item'=>$item_find, 'role'=>$role, 'par_link'=>$link])}}">
                                    @else
                                        <a href="{{route('item.item_index', ['item'=>$item_find, 'role'=>$role])}}">
                                            @endif
                                            {{$item_find->cdnm()}}
                                        </a>
                            @endif
                    </td>
                @endforeach
            </tr>
            {{--            <tr align="center">--}}
            {{--                @foreach($child_links as $link)--}}
            {{--                    <td>--}}
            {{--                        &#8195; &#8195; &#8195; &#8595;--}}
            {{--                        &#8595;--}}
            {{--                    </td>--}}
            {{--                @endforeach--}}
            {{--            </tr>--}}
            </tbody>
        </table>
        @include('list.table',['base'=>$item->base, 'links_info'=>$child_links_info, 'items'=>$items,
    'base_right'=>$base_right, 'item_view'=>false, 'par_link'=>null, 'parent_item'=>null])

        {{--    @endif--}}
        {{--    <hr align="center" width="100%" size="2" color="#ff0000"/>--}}
        {{--        &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195;--}}
        {{--        <hr>--}}
        {{--        <div class="text-center">&#8595;</div>--}}
    @endif
    @if($current_link)
        {{--        <hr>--}}
        {{--        <br>--}}
        {{--        <div class="text-center">&#8595;</div>--}}
        <?php
        $mains = Main::all()->where('parent_item_id', $item->id)->where('link_id', $current_link->id)->sortBy(function ($main) {
            return $main->link->child_base->name() . $main->child_item->name();
        });
        ?>
        {{--        Не удалять--}}
        {{--        <p>--}}
        {{--        <div class="container-fluid">--}}
        {{--            <div class="row">--}}
        {{--                <div class="col text-left">--}}
        {{--                    <h3>--}}
        {{--                    </h3>--}}
        {{--                    <h3>--}}
        {{--                        <a href="{{route('item.base_index', ['base'=>$current_link->child_base,--}}
        {{--                            'project'=>$project, 'role'=>$role])}}"--}}
        {{--                           title="{{$current_link->child_base->names()}}">--}}
        {{--                            {{$current_link->child_labels()}}--}}
        {{--                        </a>--}}
        {{--                        ({{$current_link->parent_label()}} = {{$item->name()}}):--}}
        {{--                    </h3>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}
        {{--        </p>--}}

        <hr>
        <?php

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

        $next_links_plan = $item->base->parent_links->where('parent_is_parent_related', false);

        $next_links_fact = DB::table('mains')
            ->select('link_id')
            ->where('parent_item_id', $item->id)
            ->distinct()
            ->get()
            ->groupBy('link_id');

        $array = objectToarray($next_links_fact);
        ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col text-left align-top">
                    <h3>
                        @if($current_link)
                            {{$current_link->child_labels()}}:
                        @else
                            {{$item->base->name()}}:
                        @endif
                    </h3>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col text-right">
                    <a href="{{route('item.ext_create', ['base'=>$current_link->child_base_id,
                            'project'=>$project, 'role'=>$role, 'heading'=>intval(false), 'par_link'=>$current_link->id, 'parent_item'=>$item->id])}}"
                       title="{{trans('main.add')}}">
                        <img src="{{Storage::url('add_record.png')}}" width="15" height="15"
                             alt="{{trans('main.add')}}">
                    </a>
                </div>
            </div>
        </div>
        @if (count($mains) > 0)
            <?php
            //                $child_body_links = $current_link->child_base->child_links->where('id', '!=', $current_link->id)->sortBy('parent_base_number');
            $child_body_links_info = ItemController::links_info($current_link->child_base, $role, $current_link);
            $child_body_link_id_array = $child_body_links_info['link_id_array'];
            ?>
            <table class="table table-sm table-bordered table-hover">
                <caption>{{trans('main.select_record_for_work')}}</caption>
                <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-left">{{$current_link->child_label()}}</th>
                    {{--                    <th class="text-center"></th>--}}
                    @foreach($child_body_link_id_array as $link1_id)
                        <?php
                        $link1 = Link::find($link1_id);
                        ?>
                        @if($link1)
                            <th>
                                <a href="{{route('item.base_index', ['base'=>$link1->parent_base,
                            'project'=>$project, 'role'=>$role])}}"
                                   title="{{$link1->parent_base->names()}}">
                                    {{$link1->parent_label()}}
                                </a>
                            </th>
                        @endif
                    @endforeach
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                    <th class="text-center"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 0;
                ?>
                @foreach($mains as $main)
                    <?php
                    $item1_id = $main->child_item_id;
                    $item1 = Item::find($item1_id);
                    $i++;
                    ?>
                    <tr>
                        <td class="text-center">{{$i}}</td>
                        <td class="text-left">
                            <a href="{{route('item.item_index', ['item'=>$item1, 'role'=>$role])}}">
                                {{$item1->cdnm()}}
                            </a>
                        </td>
                        {{--                        <td class="text-center">&#8594;</td>--}}
                        @foreach($child_body_link_id_array as $link1_id)
                            <?php
                            $link1 = Link::find($link1_id);
                            ?>
                            @if($link1)
                                <td>
                                    <?php
                                    $item_find = GlobalController::view_info($item1->id, $link1->id);
                                    ?>
                                    @if($item_find)
                                        {{--проверка на вычисляемые поля--}}
                                        @if($link1->parent_is_parent_related == false)
                                            <a href="{{route('item.item_index', ['item'=>$item_find, 'role'=>$role, 'par_link'=>$link1])}}">
                                                @else
                                                    <a href="{{route('item.item_index', ['item'=>$item_find, 'role'=>$role])}}">
                                                        @endif
                                                        {{$item_find->name()}}
                                                    </a>
                                        @endif
                                </td>
                            @endif
                        @endforeach
                        <td class="text-center">
                            <a href="{{route('item.ext_show', ['item'=>$item1, 'role'=>$role])}}"
                               title="{{trans('main.view')}}">
                                <img src="{{Storage::url('view_record.png')}}" width="15" height="15"
                                     alt="{{trans('main.view')}}">
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="{{route('item.ext_edit',['item'=>$item1, 'role'=>$role, 'heading'=>intval(false), 'par_link'=>$current_link, 'parent_item'=>$item])}}"
                               title="{{trans('main.edit')}}">
                                <img src="{{Storage::url('edit_record.png')}}" width="15" height="15"
                                     alt="{{trans('main.edit')}}">
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="{{route('item.ext_delete_question', ['item'=>$item1, 'role'=>$role])}}"
                               title="{{trans('main.delete')}}">
                                <img src="{{Storage::url('delete_record.png')}}" width="15" height="15"
                                     alt="{{trans('main.delete')}}">
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <?php
            $base_right = GlobalController::base_right($current_link->child_base, $role);
            $items_right = GlobalController::items_right($item->base, $project, $role, $item->id, $current_link->id);
            $items = $items_right['itget'];
            //dd($current_link->child_base);
            ?>
            @include('list.table',['base'=>$current_link->child_base, 'links_info'=>$child_body_links_info, 'items'=>$items,
        'base_right'=>$base_right, 'item_view'=>true, 'par_link'=>$current_link, 'parent_item'=>$item])
        @endif
        <hr>
        @if (count($next_links_plan) > 1)
            <form action="{{route('item.store_link_change')}}" method="POST" enctype=multipart/form-data>
                <div class="form-row">
                    @csrf
                    <input type="hidden" name="item_id" value="{{$item->id}}">
                    <input type="hidden" name="role_id" value="{{$role->id}}">
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
