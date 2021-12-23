@extends('layouts.app')

@section('content')
    <p>
    <div class="container-fluid">
        <div class="row">
            <div class="col text-left align-top">
                <h3>{{$item->base->name()}} - {{$item->name()}}</h3>
            </div>
        </div>
    </div>
    </p>
    @if (count($child_mains) > 0)
        <table class="table table-sm table-bordered table-hover">
{{--            <caption>{{trans('main.select_record_for_work')}}</caption>--}}
{{--            <thead>--}}
{{--            <tr>--}}
{{--                <th class="text-center">#</th>--}}
{{--                <th class="text-left">{{trans('main.parent')}}_{{trans('main.base')}}</th>--}}
{{--                <th class="text-left">{{trans('main.parent_label')}}</th>--}}
{{--                <th class="text-left">{{trans('main.parent')}}_{{trans('main.item')}}</th>--}}
{{--            </tr>--}}
{{--            </thead>--}}
            <tbody>
            <?php
            $i = 0;
            ?>
            @foreach($child_mains as $main)
                <?php
                $i++;
                ?>
                <tr>
                    <td class="text-center">{{$i}}</td>
{{--                    <td class="text-left">--}}
{{--                        <a href="{{route('main.index_item',$main->parent_item)}}">--}}
{{--                            {{$main->link->parent_base->name()}}--}}
{{--                        </a>--}}
{{--                    </td>--}}
                    <td class="text-left">
                        <a href="{{route('main.index_item',$main->parent_item)}}">
                            {{$main->link->parent_label()}}
                        </a>
                    </td>
                    <td class="text-left">
                        <a href="{{route('main.index_item',$main->parent_item)}}">
                            {{$main->parent_item->name()}}
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
    @if (count($parent_mains) > 0)
        &#8595;&#8595;&#8595;&#8595;&#8595;&#8595;&#8595;&#8595;&#8595;
        <table class="table table-sm table-bordered table-hover">
{{--            <caption>{{trans('main.select_record_for_work')}}</caption>--}}
{{--            <thead>--}}
{{--            <tr>--}}
{{--                <th class="text-center">#</th>--}}
{{--                <th class="text-left">{{trans('main.child')}}_{{trans('main.base')}}</th>--}}
{{--                <th class="text-left">{{trans('main.child_label')}}</th>--}}
{{--                <th class="text-left">{{trans('main.child')}}_{{trans('main.item')}}</th>--}}
{{--            </tr>--}}
{{--            </thead>--}}
            <tbody>
            <?php
            $i = 0;
            ?>
            @foreach($parent_mains as $main)
                <?php
                $i++;
                ?>
                <tr>
                    <td class="text-center">{{$i}}</td>
{{--                    <td class="text-left">--}}
{{--                        <a href="{{route('main.index_item',$main->child_item)}}">--}}
{{--                            {{$main->link->child_base->name()}}--}}
{{--                        </a>--}}
{{--                    </td>--}}
                    <td class="text-left">
                        <a href="{{route('main.index_item',$main->child_item)}}">
                            {{$main->link->child_label()}}
                        </a>
                    </td>
                    <td class="text-left">
                        <a href="{{route('main.index_item',$main->child_item)}}">
                            {{$main->child_item->name()}}
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection

