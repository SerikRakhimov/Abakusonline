<p>
<div class="container-fluid">
        <p class="text-center">
            <a href="{{route('project.start', ['project' => $project->id, 'role' => $role])}}" title="{{trans('main.bases')}}">
                <mark class="text-project">{{$project->name()}}&nbsp;&nbsp;&nbsp;@include('layouts.project.show_icons',['project'=>$project])</mark>
            </a>
        </p>
    <div class="row">
        <div class="col-6 text-left">
            <a href="{{route('project.start', ['project' => $project->id, 'role' => $role])}}" title="{{trans('main.bases')}}">
                <mark class="text-project">@guest{{trans('main.guest')}}@endguest @auth{{Auth::user()->name()}}@endauth</mark>
            </a>
        </div>
        <div class="col-6 text-right">
            <a href="{{route('project.start', ['project' => $project->id, 'role' => $role])}}" title="{{trans('main.bases')}}">
                <mark class="text-project">{{$role->name()}}</mark>
            </a>
        </div>
    </div>
    <blockquote class="text-title pt-1 pl-5 pr-5"><?php echo nl2br($role->desc()); ?></blockquote>
</div>
</p>
