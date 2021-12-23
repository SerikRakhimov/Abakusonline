<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h4><a href="{{route('moderation.index')}}" title="{{trans('main.moderation')}}" class="text-warning">{{trans('main.moderation')}}</a><span class="text-warning">:</span>
                <a href={{route('moderation.show', $moderator)}}title="{{$moderator->name()}}">{{$moderator->name()}}</a>
            </h4>
        </div>
    </div>
</div>

