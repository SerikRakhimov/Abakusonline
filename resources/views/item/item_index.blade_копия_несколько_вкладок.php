@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Item;
    use App\Models\Link;
    use App\Models\Main;
    $links = $item->base->child_links;
    ?>
    <p>
    <div class="container-fluid">
        <div class="row">
            <div class="col text-left align-top">
                <h3>
                    <a href="{{route('item.base_index', $item->base_id)}}" title="{{$item->base->names()}}">
                        {{$item->base->name()}}:
                    </a>
                    {{$item->name()}}
                </h3>
            </div>
            <div class="col-1 text-left">
                <a href="{{route('item.ext_create', ['base'=>$item->base_id,'heading'=>intval(true)])}}"
                   title="{{trans('main.add')}}">
                    <img src="{{Storage::url('add_record.png')}}" width="15" height="15" alt="{{trans('main.add')}}">
                </a>
            </div>
        </div>
    </div>
    </p>
    {{--    @if(count($links) !=0)--}}
    <table class="table table-sm table-borderless">
        <thead>
        <tr>
            @foreach($links as $link)
                <th>
                    <a href="{{route('item.base_index',$link->parent_base_id)}}"
                       title="{{$link->parent_base->names()}}">
                        {{$link->parent_label()}}:
                    </a>
                </th>
            @endforeach
            <th class="text-center"></th>
            <th class="text-center"></th>
            <th class="text-center"></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            @foreach($links as $link)
                <td>
                    <?php
                    $main_first = Main::all()->where('child_item_id', $item->id)->where('link_id', $link->id)->first();
                    ?>
                    @if($main_first != null)
                        <a href="{{route('item.item_index', $main_first->parent_item)}}">
                            @endif
                            {{$main_first != null ? $main_first->parent_item->name() : ""}}
                            @if($main_first != null)
                        </a>
                    @endif
                </td>
            @endforeach
            <td class="text-center">
                <a href="{{route('item.ext_show', $item)}}" title="{{trans('main.view')}}">
                    <img src="{{Storage::url('view_record.png')}}" width="15" height="15"
                         alt="{{trans('main.view')}}">
                </a>
            </td>
            <td class="text-center">
                <a href="{{route('item.ext_edit', $item)}}" title="{{trans('main.edit')}}">
                    <img src="{{Storage::url('edit_record.png')}}" width="15" height="15"
                         alt="{{trans('main.edit')}}">
                </a>
            </td>
            <td class="text-center">
                <a href="{{route('item.ext_delete_question', ['item' => $item, 'heading'=> true])}}"
                   title="{{trans('main.delete')}}">
                    <img src="{{Storage::url('delete_record.png')}}" width="15" height="15"
                         alt="{{trans('main.delete')}}">
                </a>
            </td>
        </tr>
        </tbody>
    </table>
    {{--    @endif--}}
    {{--    <hr align="center" width="100%" size="2" color="#ff0000"/>--}}
    {{--    &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195;--}}



    <hr>
    <?php
    $links = $item->base->parent_links;
    ?>
    <div class="tab-content">
        @foreach($links as $key=>$link)
            <div class="tab-pane fade show
                @if(isset($par_link))
            @if ($link->id == $par_link)
                active
@endif
            @else
            @if ($key == 0)
                active
@endif
            @endif
                " id="description{{$key}}">

                <?php
                $mains = Main::all()->where('parent_item_id', $item->id)->where('link_id', $link->id)->sortBy(function ($main) {
                    return $main->link->child_base->name() . $main->child_item->name();
                });
                ?>
                <div class="row">
                    <div class="col-1 text-left">
                        <a href="{{route('item.ext_create', ['base'=>$link->child_base_id, 'heading'=>intval(false), 'par_link'=>$link->id, 'parent_item'=>$item->id])}}"
                           title="{{trans('main.add')}}">
                            <img src="{{Storage::url('add_record.png')}}" width="15" height="15"
                                 alt="{{trans('main.add')}}">
                        </a>
                    </div>
                </div>
                <p>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col text-left align-top">
                            <h3>
                            </h3>
                            <h3>
                                <a href="{{route('item.base_index',$link->child_base_id)}}"
                                   title="{{$link->child_base->names()}}">
                                    {{$link->child_labels()}}
                                </a>
                                ({{$link->parent_label()}} = {{$item->name()}}):
                            </h3>
                        </div>
                    </div>
                </div>
                </p>
                @if (count($mains) > 0)
                    <table class="table table-sm table-bordered table-hover">
                        <caption>{{trans('main.select_record_for_work')}}</caption>
                        <?php
                        //$links1 = $link->child_base->child_links->where('id', '!=', $link->id);
                        $links1 = $link->child_base->child_links;
                        ?>
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-left">{{$link->child_label()}}</th>
                            @foreach($links1 as $link1)
                                <th>
                                    <a href="{{route('item.base_index',$link1->parent_base_id)}}"
                                       title="{{$link1->parent_base->names()}}">
                                        {{$link1->parent_label()}}
                                    </a>
                                </th>
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
                                    <a href="{{route('item.item_index', $item1)}}">
                                        {{$item1->name()}}
                                    </a>
                                </td>
                                @foreach($links1 as $link1)
                                    <td>
                                        <?php
                                        $main_first = Main::all()->where('child_item_id', $item1->id)->where('link_id', $link1->id)->first();
                                        ?>
                                        @if($main_first != null)
                                            <a href="{{route('item.item_index', $main_first->parent_item)}}">
                                                @endif
                                                {{$main_first != null ? $main_first->parent_item->name() : ""}}
                                                @if($main_first != null)
                                            </a>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-center">
                                    <a href="{{route('item.ext_show', $item1)}}" title="{{trans('main.view')}}">
                                        <img src="{{Storage::url('view_record.png')}}" width="15" height="15"
                                             alt="{{trans('main.view')}}">
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="{{route('item.ext_edit',['item'=>$item1, 'heading'=>intval(false), 'par_link'=>$link, 'parent_item'=>$item])}}"
                                       title="{{trans('main.edit')}}">
                                        <img src="{{Storage::url('edit_record.png')}}" width="15" height="15"
                                             alt="{{trans('main.edit')}}">
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="{{route('item.ext_delete_question', $item1)}}"
                                       title="{{trans('main.delete')}}">
                                        <img src="{{Storage::url('delete_record.png')}}" width="15" height="15"
                                             alt="{{trans('main.delete')}}">
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach
    </div>
    <hr>
    {{--    <ul class="nav nav-pills">--}}
    {{--    <ul class="nav nav-tabs">--}}
    {{--    <ul class="nav nav-pills flex-column">--}}
    {{--        @foreach($links as $key=>$link)--}}
    {{--            <li class="nav-item">--}}
    {{--                <a class="nav-link--}}
    {{--                @if ($key == 0)--}}
    {{--                --}}{{--                    active--}}
    {{--                @endif--}}
    {{--                    hidden--}}
    {{--                    " data-toggle="tab" href="#description{{$key}}">--}}
    {{--                    --}}{{--                  {{$link->parent_label()}} ({{mb_strtolower(trans('main.on'))}} {{$link->child_labels()}})--}}
    {{--                    --}}{{--                    {{$link->child_labels()}} ({{$link->parent_label()}})--}}
    {{--                    {{$link->parent_label()}} -> <b>{{$link->child_labels()}}</b>--}}
    {{--                </a>--}}
    {{--            </li>--}}
    {{--        @endforeach--}}
    {{--    </ul>--}}
    <div class="width100">
        <ul class="nav nav-pills nav-stacked">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Изменить отношение
                    <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    @foreach($links as $key=>$link)
                        <li class="nav-item width100">
                            <a class="
                                " data-toggle="tab" href="#description{{$key}}">
                                {{--                  {{$link->parent_label()}} ({{mb_strtolower(trans('main.on'))}} {{$link->child_labels()}})--}}
                                {{--                    {{$link->child_labels()}} ({{$link->parent_label()}})--}}
                                {{$link->child_labels()}} ({{$link->parent_label()}})
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>
    </div>
@endsection
