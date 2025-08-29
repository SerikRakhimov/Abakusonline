<?php
use App\Http\Controllers\GlobalController;
$link_image = $item->base->get_link_primary_image();
$item_find = null;
if ($link_image) {
    $item_find = GlobalController::view_info($item->id, $link_image->id, $role, $relit_id, true);
}
$circ_para = false;
if(isset($circle)){
    $circ_para = $circle;
}
?>
<span class="text-nowrap">
{{--    "@if($link_image)" использовать--}}
@if($link_image)
{{--    @include('view.img',['item'=>$item_find, 'size'=>"avatar", 'filenametrue'=>false, 'border'=>true, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>trans('main.author')])--}}
        @include('view.img',['item'=>$item_find, 'size'=>$size, 'circle'=>$circ_para, 'noimg_def'=>true, 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>trans('main.author')])
    @endif
{{$item->name()}}
{{--            <img src="/storage/79/3/97VFXyS9tdkC5JlXr3DTLrqIyGWgaLH6ObmBrgtS.png"--}}
{{--                 style="object-fit:cover;"--}}
{{--class="rounded-circle"--}}
{{--                 class="circle shadow-4-strong"--}}
{{--                 class="img-circle"--}}
{{--                 height="250px" width="250px"--}}
{{--                 alt="" title="Автор"--}}
{{--            >--}}
{{--        <img src="/storage/79/3/97VFXyS9tdkC5JlXr3DTLrqIyGWgaLH6ObmBrgtS.png"--}}
{{--             style="object-fit:cover;"--}}
{{--             class="rounded-circle img-thumbnail img-fluid shadow"--}}
{{--             class="rounded-circle shadow"--}}
{{--            height="150px"--}}
{{--             width="150px"--}}
{{--             alt="" title="Автор"--}}
{{--        >--}}
</span>

