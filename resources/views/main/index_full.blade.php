@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Link;
    use App\Models\Main;

    function objectToarray($data)
    {
        $array = (array)$data;
        return $array;
    }

    ?>
    <h2><span class="text-primary">{{$link_head->parent_label()}}</span> <span class="text-secondary">-</span> <span
            class="text-danger">{{$item->name()}}</span></h2><br>
    <h5><span class="text-success">{{$link_head->child_labels()}}:</span></h5>
    <br>
    <table class="table table-sm table-bordered table-hover">
        <caption>{{trans('main.select_record_for_work')}}</caption>
        <thead>
        <tr>
            <th class="text-center">#</th>
            {{--            <th scope="col">{{$link_head->child_label_ru}} ({{$link_head->child_base->name_ru}})</th>--}}
            <th class="text-left"><span class="text-success">{{$link_head->child_label()}}</span></th>
            @foreach($links as $link)
                {{--                <th scope="col">{{$link->parent_label_ru}} ({{$link->parent_base->name_ru}})</th>--}}
                <th scope="col">{{$link->parent_label()}}</th>
            @endforeach
            <th class="text-center"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 0;
        ?>
        @foreach($mains as $main)
            <?php
            $i++;
            ?>
            <tr>
                <th scope="row">{{$i}}</th>
                <td>
                    <span class="text-success">{{$main->child_item->name()}}</span>
                </td>

                @foreach($links as $link)
                    <td>
                        <?php
                        $main_first = Main::all()->where('child_item_id', $main->child_item_id)->where('link_id', $link->id)->first();
                        ?>
                        @if($main_first != null)
                            <a href="{{route('main.index_full', ['item'=>$main_first->parent_item,'link'=>$link])}}">
                                @endif
                                {{$main_first != null ? $main_first->parent_item->name() : ""}}
                                @if($main_first != null)
                            </a>
                        @endif
                    </td>
                @endforeach

                <td>
                    <?php
                    $next_links_plan = $main->child_item->base->parent_links;

                    $next_links_fact = DB::table('mains')
                        ->select('link_id')
                        ->where('parent_item_id', $main->child_item_id)
                        ->distinct()
                        ->get()
                        ->groupBy('link_id');


                    $array = objectToarray($next_links_fact);
                    //                   $array2 = $next_links_fact->all();
                    //echo $array["\x00*\x00items"][14];
                    ?>
{{--                        @if (count($next_links_plan) == 0)--}}
                    @if (count($next_links_plan) == 0)
{{--                        Нет child-связей по "{{$main->child_item->name_ru}}"--}}
                    @else
                        <form action="{{route('main.store_full')}}" method="POST" enctype=multipart/form-data>
                            @csrf
                            <input type="hidden" name="item" value="{{$main->child_item->id}}">

                            <div class="form-group">
                                <select class="form-control"
                                        name="link"
                                        id="link"
                                        class="form-control @error('link') is-invalid @enderror">
                                    @foreach($next_links_plan as $key=>$value)
                                        <option value="{{$value->id}}"
                                                @if(!isset($array["\x00*\x00items"][$value->id]))
                                                disabled
                                            @endif
                                        >{{$value->parent_label()}} {{$main->child_item->name()}} ({{mb_strtolower(trans('main.on'))}} {{$value->child_labels()}})
                                        </option>
                                    @endforeach
                                </select>
                                @error('link')
                                <div class="text-danger">
                                    {{$message}}
                                </div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary"
                                    {{--                                    нет в базе фактически link_id--}}
                                    @if (count($array["\x00*\x00items"]) == 0)
                                    disabled
                                @endif
                            >Select
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection

