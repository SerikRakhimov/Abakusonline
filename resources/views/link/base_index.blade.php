@extends('layouts.app')

@section('content')
<?php
    use \App\Http\Controllers\GlobalController;
    ?>
    <p>
    <div class="container-fluid">
        <div class="row">
            <div class="col text-left align-top">
                <h3>
                    {{-- {{$base->names($base_right)}}--}}
                    {{$base->names()}}
                </h3>
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
            <th class="text-left">{{trans('main.link_maxcount')}}</th>
            <th class="text-left">{{trans('main.child')}}_{{trans('main.serial_number')}}</th>
            <th class="text-left">{{trans('main.child')}}_{{trans('main.base')}}</th>
            <th class="text-left">{{trans('main.child_label')}}</th>
            <th class="text-left">{{trans('main.child_labels')}}</th>
            <th class="text-left">{{trans('main.child')}}_{{trans('main.child_maxcount')}}</th>
            <th class="text-left">{{trans('main.parent')}}_{{trans('main.serial_number')}}</th>
            <th class="text-left">{{trans('main.parent')}}_{{trans('main.template')}}</th>
            <th class="text-left">{{trans('main.parent')}}_{{trans('main.base')}}</th>
            <th class="text-left">{{trans('main.parent_is_seqnum')}}</th>
            <th class="text-left">{{trans('main.parent_seqnum_link_id')}}</th>
            <th class="text-left">{{trans('main.level')}}_0</th>
            <th class="text-left">{{trans('main.level')}}_1</th>
            <th class="text-left">{{trans('main.level')}}_2</th>
            <th class="text-left">{{trans('main.level')}}_3</th>
            <th class="text-left">{{trans('main.parent_label')}}</th>
            <th class="text-left">{{trans('main.parent_is_base_link')}}</th>
            <th class="text-left">{{trans('main.parent_is_unique')}}</th>
            <th class="text-left">{{trans('main.parent_is_sorting')}}</th>
            <th class="text-left">{{trans('main.parent_is_parallel')}}</th>
            <th class="text-left">{{trans('main.parent_is_enter_refer')}}</th>
            <th class="text-left">{{trans('main.parent_is_nc_parameter')}}</th>
            <th class="text-left">{{trans('main.parent_is_numcalc')}}</th>
            <th class="text-left">{{trans('main.parent_is_nc_screencalc')}}</th>
            <th class="text-left">{{trans('main.parent_is_nc_viewonly')}}</th>
            <th class="text-left">{{trans('main.parent_is_twt_link')}}</th>
            <th class="text-left">{{trans('main.parent_is_tst_link')}}</th>
            <th class="text-left">{{trans('main.parent_is_cus_link')}}</th>
            <th class="text-left">{{trans('main.parent_is_tree_value')}}</th>
            <th class="text-left">{{trans('main.parent_is_tree_top')}}</th>
            <th class="text-left">{{trans('main.parent_is_calcname')}}</th>
            <th class="text-left">{{trans('main.parent_is_left_calcname')}}</th>
            <th class="text-left">{{trans('main.parent_is_small_calcname')}}</th>
            <th class="text-left">{{trans('main.parent_is_checking_history')}}</th>
            <th class="text-left">{{trans('main.parent_is_checking_empty')}}</th>
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
                        {{$link->link_maxcount}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->child_base_number}}
                    </a>
                </td>
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
                        {{$link->child_maxcount}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_base_number}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{GlobalController::get_parent_template_from_relit_id($link->parent_relit_id, $link->child_base->template_id)['template_name']}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_base->info()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_seqnum}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_seqnum_link_id}}
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
                        {{$link->parent_is_base_link}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_unique}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_sorting}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_parallel}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_enter_refer}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_nc_parameter}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_numcalc}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_nc_screencalc}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_nc_viewonly}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_twt_link}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_tst_link}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_cus_link}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_tree_value}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_tree_top}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_calcname}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_left_calcname()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_small_calcname}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_checking_history}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('link.show',$link)}}">
                        {{$link->parent_is_checking_empty}}
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

