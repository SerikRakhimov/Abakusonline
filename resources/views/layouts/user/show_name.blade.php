<?php
$get_user_author_avatar_item = $user->get_user_avatar_item();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 text-center">
            <h4>
                @if (Auth::user()->isAdmin())
                <a href="{{route('user.index')}}" title="{{trans('main.user')}}"
                   class="text-warning">{{trans('main.user')}}:</a>
                @else
                    {{trans('main.user')}}:
                @endif
                    <a href="{{route('user.show', $user)}}" title="{{$user->name()}}">{{$user->name()}}
                        @if($get_user_author_avatar_item)
                            @include('view.img',['item'=>$get_user_author_avatar_item, 'size'=>"avatar", 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>Auth::user()->name])
                        @endif
                    </a>
            </h4>
        </div>
    </div>
</div>

