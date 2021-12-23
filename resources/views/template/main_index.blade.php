@extends('layouts.app')

@section('content')
    <?php
    Use App\Models\Role;
    ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <h3>{{trans('main.templates')}}</h3>
            </div>
        </div>
    </div>
    </p>
    <?php
    $i = $templates->firstItem() - 1;
    ?>
    <div class="card-columns">
        @foreach($templates as $template)
            <?php
            $i++;
            ?>
            <div class="card shadow">
                {{--            <img class="card-img-top" src="{{Storage::url('background.png')}}" alt="Card image">--}}
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6 text-left text-title">
                            <small class="text-muted">Id = {{$template->id}}</small>
                        </div>
                        <div class="col-sm-6 text-right">
                            @include('layouts.template.show_icons',['template'=>$template])
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="card-title mb-3">{{$template->name()}}</h4>
                    <p class="card-text text-label">
                        <?php
                        echo nl2br($template->desc());
                        ?>
                    </p>
                    {{--                ($my_projects ? 1 : 0)--}}
                    <button type="button" class="btn btn-dreamer" title="{{trans('main.create_project')}}"
                            onclick="document.location='{{route('project.create_template_user', ['template'=>$template])}}'">
                        <i class="fas fa-plus d-inline"></i>
                        {{trans('main.create_project')}}
                    </button>
                </div>
                <div class="card-footer">
                    <small class="text-muted">{{trans('main.projects')}}: {{$template->projects_count}}</small>
                </div>
            </div>
            <br>

        @endforeach
    </div>

    {{--    <div class="card">--}}
    {{--        <h3 class="card-header">Featured</h3>--}}
    {{--        <div class="card-block">--}}
    {{--            <h4 class="card-title">Special title treatment</h4>--}}
    {{--            <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>--}}
    {{--            <a href="#" class="btn btn-primary">Go somewhere</a>--}}
    {{--        </div>--}}
    {{--        <div class="card-footer">--}}
    {{--            <small class="text-muted">{{$project->created_at}}</small>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--        <div class="card bg-primary">--}}
    {{--            <div class="card-body text-center">--}}
    {{--                <p class="card-text">Some text inside the first card</p>--}}
    {{--                <p class="card-text">Some more text to increase the height</p>--}}
    {{--                <p class="card-text">Some more text to increase the height</p>--}}
    {{--                <p class="card-text">Some more text to increase the height</p>--}}
    {{--            </div>--}}
    {{--        </div>--}}

    {{$templates->links()}}

@endsection

