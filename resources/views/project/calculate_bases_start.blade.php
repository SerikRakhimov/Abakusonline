@extends('layouts.app')

@section('content')

    <?php
    use App\Http\Controllers\BaseController;
    use Illuminate\Support\Facades\Request;
    ?>
    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role])
    <h3>{{trans('main.calculate_bases')}}?</h3>
    <form action="{{route('project.calculate_bases', ['project'=>$project, 'role'=>$role])}}" method="POST">
        @csrf
        @method('HEAD')
        <p>
            <button type="submit" class="btn btn-dreamer" title="{{trans('main.calculation')}}">
                {{--                <i class="fas fa-trash"></i>--}}
                {{trans('main.calculation')}}
            </button>
            <button type="button" class="btn btn-dreamer"
                    title="{{trans('main.cancel')}}"
                    onclick="document.location='{{route('project.start', ['project' => $project->id, 'role' => $role])}}'">
                {{--                <i class="fas fa-arrow-left"></i>--}}
                {{trans('main.cancel')}}
            </button>
        </p>
    </form>

@endsection
