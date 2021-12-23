@extends('layouts.app')

@section('content')
    <p>
    @include('layouts.task.show_name',['template'=>$template, 'task'=>$task])
    <div class="container-fluid">
        <div class="row">
            <div class="col-5 text-center">
                <h3>{{trans('main.modules')}}</h3>
            </div>
            <div class="col-2">
            </div>
            <div class="col-5 text-right">
                <button type="button" class="btn btn-dreamer" title="{{trans('main.add')}}"
                        onclick="document.location='{{route('module.create', ['task'=>$task])}}'">
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
        $i = $modules->firstItem() - 1;
        ?>
        @foreach($modules as $module)
            <?php
            $i++;
            ?>
            <tr>
                {{--                <th scope="row">{{$i}}</th>--}}
                <td class="text-center">
                    <a href="{{route('module.show',$module)}}" title="{{trans('main.show')}}">
                        {{$i}}
                    </a></td>
                <td class="text-left">
                    <a href="{{route('module.show',$module)}}" title="{{trans('main.show')}}">
                        {{$module->name()}}
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{$modules->links()}}
@endsection

