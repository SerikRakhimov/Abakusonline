@extends('layouts.app')

@section('content')
    <?php
    use App\Http\Controllers\GlobalController;
    Use App\Models\Role;
    //$i = $templates->firstItem() - 1;
    $i = 0;
    $num_cols = GlobalController::get_number_of_columns_projects();
    ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <h3>{{trans('main.templates')}}</h3>
            </div>
        </div>
    </div>
    <div class="card-deck">
        @foreach($templates as $template)
<!--            --><?php
//            $i++;
//            ?>
            <div class="card shadow m-2">
                {{--            <img class="card-img-top" src="{{Storage::url('background.png')}}" alt="Card image">--}}
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8 text-left text-title">
                            <small class="text-muted">{{$template->account}}</small>
                        </div>
                        <div class="col-sm-4 text-right">
                            @include('layouts.template.show_icons',['template'=>$template])
                        </div>
                    </div>
                </div>
{{--                <div class="card-body p-0">--}}
                    <div class="card-body">
                    <h4 class="card-title mb-3">{{$template->name()}}</h4>
                    <p class="card-text text-label">
                        <?php
                        echo nl2br($template->desc());
                        ?>
                    </p>
                    {{--                ($my_projects ? 1 : 0)--}}
                    <?php
                    // Используется '$is_create_project = true;'
                    $is_create_project = true;
                    if (Auth::check()) {
                        if ($template->is_create_admin == true) {
                            if (!Auth::user()->isAdmin()) {
                                $is_create_project = false;
                            }
                        }
                    } else {
                        $is_create_project = false;
                    }
                    ?>
                    @if($is_create_project)
                        <button type="button" class="btn btn-dreamer" title="{{trans('main.create_project')}}"
                                onclick="document.location='{{route('project.create_template_user', ['template'=>$template])}}'">
                            <i class="fas fa-plus d-inline"></i>
                            {{trans('main.create_project')}}
                        </button>
                    @else
                        <small class="text-muted">{{trans('main.for_project_create')}}</small>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-4 text-left text-title">
                            <small class="text-muted">Id: {{$template->id}}</small>
                        </div>
                        <div class="col-sm-8 text-right">
                            <small class="text-muted">{{trans('main.projects')}}: {{$template->projects_count}}</small>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <?php
            $i++;
            ?>
            @if($i % $num_cols == 0)
    </div>
    <div class="card-deck">
        @endif
        @endforeach
        {{-- Если строка из $num_cols элементов не завершилась до $num_cols столбцов--}}
        {{-- (т.е. $i не делится без остатка на $num_cols)--}}
        @if($i % $num_cols != 0)
            <?php
            // Подсчитываем количество оставшихся колонок
            $n = $num_cols - ($i % $num_cols);
            ?>
            {{-- В цикле $n раз вставляем вставляем пустые колонки--}}
            @for($k = 0; $k < $n; $k++)
                {{-- Вставляем пустую карточку--}}
                <div class="card m-2 bg-transparent">
                </div>
            @endfor
        @endif
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

