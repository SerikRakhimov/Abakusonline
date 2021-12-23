@extends('layouts.app')

@section('content')
    <p>
    <div class="container-fluid">
        <div class="row">
            <div class="col text-left align-top">
                <h3>{{$base->names()}}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col text-left align-top">
                <h3>{{trans('main.links')}}</h3>
            </div>
            <div class="col-1 text-left">
                <a href="{{route('link.create', $base)}}" title="{{trans('main.add')}}">
                    <img src="{{Storage::url('add_record.png')}}" width="15" height="15" alt="{{trans('main.add')}}">
                </a>
            </div>
        </div>
    </div>
    </p>
    <table class="table table-sm table-bordered table-hover">
        <caption>{{trans('main.select_record_for_work')}}</caption>
        <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="text-left">{{trans('main.child')}}_{{trans('main.base')}}</th>
            <th class="text-left">{{trans('main.child_label')}}</th>
            <th class="text-left">{{trans('main.child_labels')}}</th>
            <th class="text-left">{{trans('main.parent')}}_{{trans('main.base')}}</th>
            <th class="text-left">{{trans('main.serial_number')}}</th>
            <th class="text-left">{{trans('main.level')}}_0</th>
            <th class="text-left">{{trans('main.level')}}_1</th>
            <th class="text-left">{{trans('main.level')}}_2</th>
            <th class="text-left">{{trans('main.level')}}_3</th>
            <th class="text-left">{{trans('main.parent_label')}}</th>
            <th class="text-left">{{trans('main.parent_is_enter_refer')}}</th>
            <th class="text-left">{{trans('main.parent_is_calcname')}}</th>
            <th class="text-left">{{trans('main.parent_is_left_calcname')}}</th>
            <th class="text-left">{{trans('main.parent_is_small_calcname')}}</th>
            <th class="text-left">{{trans('main.parent_calcname_prefix')}}</th>
            <th class="text-center">Id</th>
            <th class="text-center"></th>
            <th class="text-center"></th>
            <th class="text-center">{{trans('main.rolis')}}</th>
        </tr>
        </thead>
        <tbody>
        <?php
        //$i = $links->firstItem() - 1;
        $i = 0;
        ?>
        @foreach($links as $link)
            <?php
            $i++;
            ?>
            <tr>
                <td class="text-center">{{$i}}</td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->child_base->name()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->child_label()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->child_labels()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_base->info()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_base_number}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_level(0)}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_level(1)}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_level(2)}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_level(3)}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_label()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_enter_refer}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_calcname}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_left_calcname}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_small_calcname}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_calcname_prefix()}}
                    </a>
                </td>
                <td class="text-center">
                    {{$link->id}}
                </td>
                <td class="text-center">
                    <a href="{{route('link.edit',[$link, $base])}}" title="{{trans('main.edit')}}">
                        <img src="{{Storage::url('edit_record.png')}}" width="15" height="15"
                             alt="{{trans('main.edit')}}">
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('link.delete_question',$link)}}" title="{{trans('main.delete')}}">
                        <img src="{{Storage::url('delete_record.png')}}" width="15" height="15"
                             alt="{{trans('main.delete')}}">
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('roli.index_link', $link)}}" title="{{trans('main.rolis')}}">
                        <i class="fas fa-paperclip"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{--    {{$links->links()}}--}}
@endsection

