@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Item;
    use App\Models\Link;
    use App\Models\Main;
    use \App\Http\Controllers\GlobalController;
    use \App\Http\Controllers\ItemController;
    //    function objectToarray($data)
    //    {
    //        $array = (array)$data;
    //        return $array;
    //    }
    ?>
    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role])
    <p>
    <div class="container-fluid">
        <div class="row">
            <div class="col-10 text-left">
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
                    @if ($base_right['is_list_base_calc'] == true)
                        <a href="{{route('item.ext_show', ['item'=>$item, 'role'=>$role])}}"
                           title="{{$item->cdnm()}}">
                            {{$item->cdnm()}}
                        </a>
                    @else
                        {{$item->cdnm()}}
                    @endif
                </h3>
            </div>
            <div class="col-2 text-right">
                {{--                <a href="{{route('item.ext_create', ['base'=>$item->base,--}}
                {{--                            'project'=>$project, 'role'=>$role, 'heading'=>intval(true)])}}"--}}
                {{--                   title="{{trans('main.add')}}">--}}
                {{--                    <img src="{{Storage::url('add_record.png')}}" width="15" height="15"--}}
                {{--                         alt="{{trans('main.add')}}">--}}
                {{--                </a>--}}
                <button type="button" class="btn btn-dreamer" title="{{trans('main.add')}}"
                        onclick="document.location='{{route('item.ext_create', ['base'=>$item->base,
                            'project'=>$project, 'role'=>$role, 'heading'=>intval(true),
                             'par_link'=>$current_link, 'parent_item'=>null])}}'">
                    <i class="fas fa-plus d-inline"></i>&nbsp;{{trans('main.add')}}
                </button>
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
    @if(count($child_links) !=0)
        @if($current_link)
            @include('list.table',['base'=>$item->base, 'links_info'=>$child_links_info, 'items'=>$items,
                'base_right'=>$base_right, 'item_view'=>false, 'par_link'=>$current_link, 'parent_item'=>$item])
        @else
            @include('list.table',['base'=>$item->base, 'links_info'=>$child_links_info, 'items'=>$items,
                'base_right'=>$base_right, 'item_view'=>false, 'par_link'=>null, 'parent_item'=>null])
        @endif
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

        // $next_links_plan = $item->base->parent_links->where('parent_is_parent_related', false);

        //        $next_links_fact = DB::table('mains')
        //            ->select('link_id')
        //            ->where('parent_item_id', $item->id)
        //            ->distinct()
        //            ->get()
        //            ->groupBy('link_id');
        //
        //        $array = objectToarray($next_links_fact);

        ?>
        <p>
        <div class="container-fluid">
            <div class="row">
                <div class="col-10 text-left">
                    <h3>
                        @if($current_link)
                            {{$current_link->child_labels()}}:
                        @else
                            {{$item->base->name()}}:
                        @endif
                    </h3>
                </div>
                <div class="col-2 text-right">
                    <button type="button" class="btn btn-dreamer" title="{{trans('main.add')}}"
                            onclick="document.location='{{route('item.ext_create', ['base'=>$current_link->child_base_id,
                                        'project'=>$project, 'role'=>$role, 'heading'=>intval(false), 'par_link'=>$current_link, 'parent_item'=>$item])}}'">
                        <i class="fas fa-plus d-inline"></i>&nbsp;{{trans('main.add')}}
                    </button>
                </div>
            </div>
        </div>
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
            @include('list.table',['base'=>$current_link->child_base, 'links_info'=>$child_body_links_info, 'items'=>$body_items,
        'base_right'=>$base_body_right, 'item_view'=>true, 'par_link'=>$current_link, 'parent_item'=>$item])
            {{$body_items->links()}}
            {{$body_items->currentPage()}}
            {{$body_items->count()}}
            {{$body_items->perPage()}}
        @endif
        <hr>
        @if (count($next_links_plan) > 1)
            <form action="{{route('item.store_link_change')}}" method="POST" enctype=multipart/form-data>
                <div class="form-row">
                    @csrf
                    <input type="hidden" name="project_id" value="{{$project->id}}">
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
