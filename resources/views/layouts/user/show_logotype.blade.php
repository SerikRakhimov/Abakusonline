<?php
$get_user_author_avatar_item = $user->get_user_avatar_item();
?>
@if($get_user_author_avatar_item)
    @include('view.img',['item'=>$get_user_author_avatar_item, 'size'=>"avatar", 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>Auth::user()->name])
@endif
