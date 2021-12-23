<?php
use App\Http\Controllers\MainController;
//$link_image = $item->base->link_primary_image();
//$item_find = null;
//if ($link_image) {
//    $item_find = MainController::view_info($item->id, $link_image->id);
//} else {
//    $item_find = null;
//}
?>
<p>{{trans('main.project')}}: <b>{{$item->project->name()}}</b></p>
<hr>
<h3>{{$item->project->user->name()}}!</h3>
<h3 class="display-5 text-center">{{trans('main.new_record')}} - {{$item->base->name()}}</h3>
<p>Id: <b>{{$item->id}}</b></p>
@if($item->base->is_code_needed == true)
    <p>{{trans('main.code')}}: <b>{{$item->code}}</b></p>
@endif
{{--@if($item_find)--}}
{{--    @include('view.img',['item'=>$item_find, 'size'=>"medium", 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>true, 'title'=>$item->name()])--}}
{{--@endif--}}
<p>{{trans('main.name')}}:<br><b><?php echo $item->nmbr();?></b></p><br>
<p class="text-label">{{trans('main.created_user_date_time')}}:
    <b>{{$item->created_user_date_time()}}</b><br>
</p>
<br>
<hr>
<h5>www.abakusonline.com</h5>
