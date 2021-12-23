@extends('layouts.app')

@section('content')
    <p>
    <div class="container-fluid">
        <div class="row">
            <div class="col text-left align-top">
                <h3>{{trans('main.links')}}</h3>
            </div>
            <div class="col-1 text-left">
                <a href="{{route('link.create')}}" title = "{{trans('main.add')}}">
                    <img src="{{Storage::url('add_record.png')}}" width="15" height="15" alt = "{{trans('main.add')}}">
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
            <th class="text-left">{{trans('main.parent')}}_{{trans('main.serial_number')}}</th>
            <th class="text-left">{{trans('main.parent')}}_{{trans('main.base')}}</th>
            <th class="text-left">{{trans('main.parent_label')}}</th>
            <th class="text-left">{{trans('main.parent_is_calcname')}}</th>
            <th class="text-left">{{trans('main.parent_calcname_prefix')}}</th>
            <th class="text-left">parent is parent related</th>
            <th class="text-left">parent parent related start link id</th>
            <th class="text-left">parent parent related result link id</th>
            <th class="text-left">parent is child related</th>
            <th class="text-left">parent child related start link id</th>
            <th class="text-left">parent child related result link id</th>
            <th class="text-left">{{trans('main.parent_nc_parameter')}}</th>
            <th class="text-center">Id</th>
            <th class="text-center"></th>
            <th class="text-center"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = $links->firstItem() - 1;
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
                        {{$link->parent_base_number}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_base->info()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_label()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_calcname}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_calcname_prefix()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_parent_related}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_parent_related_start_link_id}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_parent_related_result_link_id}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_child_related}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_child_related_start_link_id}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_child_related_result_link_id}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_nc_parameter}}
                    </a>
                </td>
                <td class="text-center">
                    {{$link->id}}
                </td>
                <td class="text-center">
                    <a href="{{route('link.edit',$link)}}" title = "{{trans('main.edit')}}">
                        <img src="{{Storage::url('edit_record.png')}}" width="15" height="15" alt = "{{trans('main.edit')}}">
                    </a>
                </td>
                <td  class="text-center">
                    <a href="{{route('link.delete_question',$link)}}" title = "{{trans('main.delete')}}">
                        <img src="{{Storage::url('delete_record.png')}}" width="15" height="15" alt = "{{trans('main.delete')}}">
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$links->links()}}
@endsection

