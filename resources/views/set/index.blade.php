@extends('layouts.app')

@section('content')
    <?php
    use App\Http\Controllers\GlobalController;
    ?>
    <p>
    @include('layouts.template.show_name',['template'=>$template])
    <div class="container-fluid">
        <div class="row">
            <div class="col-5 text-center">
                <h3>{{trans('main.sets')}}</h3>
            </div>
            <div class="col-2">
            </div>
            <div class="col-5 text-right">
                <button type="button" class="btn btn-dreamer" title="{{trans('main.add')}}"
                        onclick="document.location='{{route('set.create', ['template'=>$template])}}'">
                    <i class="fas fa-plus d-inline"></i>
                    {{trans('main.add')}}
                </button>
            </div>
        </div>
    </div>
    </p>
    <table class="table table-sm table-bordered table-hover">
        <caption>{{trans('main.select_record_for_work')}}</caption>
        <thead>
        <tr>
            <th class="text-center align-top">#</th>
            <th class="text-center align-top">{{trans('main.serial_number')}}</th>
            <th class="text-center align-top">{{trans('main.line_number')}}</th>
            <th class="text-left align-top">{{trans('main.link_from')}}</th>
            <th class="text-left align-top">{{trans('main.link_to')}}</th>
            <th class="text-center align-top">{{trans('main.is_savesets_enabled')}}</th>
            <th class="text-center align-top">{{trans('main.forwhat')}}</th>
            <th class="text-center align-top">{{trans('main.updaction')}}</th>
            <th class="text-center align-top">{{trans('main.is_upd_delete_record_with_zero_value')}}</th>
            <th class="text-center align-top">Id</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = $sets->firstItem() - 1;
        ?>
        @foreach($sets as $set)
            <?php
            $i++;
            ?>
            <tr>
                <td class="text-center">
                    <a href="{{route('set.show',$set)}}" title="{{trans('main.show')}}">
                        {{$i}}
                    </a></td>
                <td class="text-center">
                    <a href="{{route('set.show',$set)}}" title="{{trans('main.show')}}">
                        {{$set->serial_number}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('set.show',$set)}}" title="{{trans('main.show')}}">
                        {{$set->line_number}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('set.show',$set)}}" title="{{trans('main.show')}}">
                        {{$set->link_from->name()}}
                    </a>
                </td>
                <td class="text-left">
                    <a href="{{route('set.show',$set)}}" title="{{trans('main.show')}}">
                        {{$set->link_to->name()}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('set.show',$set)}}" title="{{trans('main.show')}}">
                        {{GlobalController::name_is_boolean($set->is_savesets_enabled)}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('set.show',$set)}}" title="{{trans('main.show')}}">
                        {{$set->forwhat_name()}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('set.show',$set)}}" title="{{trans('main.show')}}">
                        {{$set->updaction_name()}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('set.show',$set)}}" title="{{trans('main.show')}}">
                        {{$set->updaction_delete_record_with_zero_value()}}
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{route('set.show',$set)}}" title="{{trans('main.show')}}">
                        {{$set->id}}
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$sets->links()}}
@endsection
