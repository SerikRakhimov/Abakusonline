<?php
use Illuminate\Support\Facades\Auth;
$is_admin = Auth::user()->isAdmin();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h4>
                @if($is_admin)
                    <a href="{{route('template.index')}}" title="{{trans('main.template')}}" class="text-warning">
                        @endif
                        {{trans('main.template')}}
                        @if($is_admin)
                    </a>
                @endif
                <span class="text-warning">:</span>
                @if($is_admin)
                    <a href={{route('template.show', $template)}} title="{{$template->name()}}">
                    @endif
                        {{$template->name()}}
                        @if($is_admin)
                    </a>
                @endif
            </h4>
        </div>
    </div>
</div>

