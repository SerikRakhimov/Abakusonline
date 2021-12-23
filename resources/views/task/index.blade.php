@extends('layouts.app')

@section('content')
    <p>
    @include('layouts.template.show_name',['template'=>$template])
    <div class="container-fluid">
        <div class="row">
            <div class="col-5 text-center">
                <h3>{{trans('main.tasks')}}</h3>
            </div>
            <div class="col-2">
            </div>
            <div class="col-5 text-right">
                <button type="button" class="btn btn-dreamer" title="{{trans('main.add')}}"
                        onclick="document.location='{{route('task.create', ['template'=>$template])}}'">
                    {{--                    <i class="fas fa-plus fa-fw d-none d-sm-block "></i>--}}
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
            <th class="text-center">#</th>
            <th class="text-left">{{trans('main.name')}}</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = $tasks->firstItem() - 1;
        ?>
        @foreach($tasks as $task)
            <?php
            $i++;
            ?>
            <tr>
                {{--                <th scope="row">{{$i}}</th>--}}
                <td class="text-center">
                    <a href="{{route('task.show',$task)}}" title="{{trans('main.show')}}">
                        {{$i}}
                    </a></td>
                <td class="text-left">
                    <a href="{{route('task.show',$task)}}" title="{{trans('main.show')}}">
                        {{$task->name()}}
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$tasks->links()}}
@endsection

