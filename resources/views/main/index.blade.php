@extends('layouts.app')

@section('content')
    <p>
    <div class="container-fluid">
        <div class="row">
            <div class="col text-left align-top">
                <h3>{{trans('main.mains')}}</h3>
            </div>
            <div class="col-1 text-left">
                <a href="{{route('main.create')}}" title = "{{trans('main.add')}}">
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
            <th class="text-left">{{trans('main.child_label')}}</th>
            <th class="text-left">{{trans('main.child')}}_{{trans('main.base')}}</th>
            <th class="text-left">{{trans('main.child')}}_{{trans('main.item')}}</th>
            <th class="text-left">{{trans('main.parent_label')}}</th>
            <th class="text-left">{{trans('main.parent')}}_{{trans('main.base')}}</th>
            <th class="text-left">{{trans('main.parent')}}_{{trans('main.item')}}</th>
            <th class="text-center">Id</th>
            <th class="text-center"></th>
            <th class="text-center"></th>
            <th class="text-center"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = $mains->firstItem() - 1;
        ?>
        @foreach($mains as $main)
            <?php
            $i++;
            ?>
            <tr>
                <td class="text-center">{{$i}}</td>
                <td class="text-left">
                    <a href="{{route('main.show',$main)}}">
                        {{$main->link->child_label()}}:
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('main.show',$main)}}">
                        {{$main->link->child_base->name()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('main.show',$main)}}">
                        {{$main->child_item->name()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('main.show',$main)}}">
                        {{$main->link->parent_label()}}:
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('main.show',$main)}}">
                        {{$main->link->parent_base->name()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('main.show',$main)}}">
                        {{$main->parent_item->name()}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('main.show',$main)}}">
                        {{$main->id}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('main.index_full',['item'=>$main->parent_item_id,'link'=>$main->link_id])}}" title = "{{trans('main.full')}}">
                        <img src="{{Storage::url('full_record.png')}}" width="15" height="15" alt = "{{trans('main.full')}}">
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('main.edit',$main)}}" title = "{{trans('main.edit')}}">
                        <img src="{{Storage::url('edit_record.png')}}" width="15" height="15" alt = "{{trans('main.edit')}}">
                    </a>
                </td>
                <td  class="text-center">
                    <a href="{{route('main.delete_question',$main)}}" title = "{{trans('main.delete')}}">
                        <img src="{{Storage::url('delete_record.png')}}" width="15" height="15" alt = "{{trans('main.delete')}}">
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$mains->links()}}
@endsection

