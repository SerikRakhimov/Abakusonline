@include('layouts.task.show_name',['task'=>$task])
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h4><a href={{route('module.show', $module)}}title="{{$module->name()}}">{{$module->name()}}</a>
            </h4>
        </div>
    </div>
</div>

