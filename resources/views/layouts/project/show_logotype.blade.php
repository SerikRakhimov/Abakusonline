<?php
$get_items_setup = $project->get_items_setup();
$get_project_logo_item = $get_items_setup['logo_item'];
?>
@if($get_project_logo_item)
@include('view.img',['item'=>$get_project_logo_item, 'size'=>"avatar", 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>'empty'])
@endif
