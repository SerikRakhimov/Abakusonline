<?php
// https://ru.stackoverflow.com/questions/197952/%D0%9F%D0%B5%D1%80%D0%B2%D1%8B%D0%B9-%D1%81%D0%B8%D0%BC%D0%B2%D0%BE%D0%BB-%D1%81%D1%82%D1%80%D0%BE%D0%BA%D0%B8#comment197968_197959
use App\Http\Controllers\GlobalController;

$link_image = $item->base->get_link_primary_image();
$item_find = null;
if ($link_image) {
    $item_find = GlobalController::view_info($item->id, $link_image->id, $role, $relit_id, true);
}
$circ_para = false;
if (isset($circle)) {
    $circ_para = $circle;
}

$height = GlobalController::types_img_height($size, false);
$h_text = $height - 3;
$rnd_colors = GlobalController::rnd_colors($item->id);
// Цвет текста: "#FFFFFF" - белый цвет
$my_color = $rnd_colors['my_color'];
// Цвет фона
$my_bg_color = $rnd_colors['my_bg_color'];

//$it_nm = GlobalController::itnm_left($item->name(),25);
$it_nm = $item->name();
$circle_rounded = "";
if ($circle) {
    $circle_rounded = "rounded-circle";
}
?>
<span class="d-flex flex-row align-items-stretch">
    <span class="pt-0">
        {{--<span class="d-flex flex-nowrap align-items-center">--}}
        @if($item_find)
        @if($link_image)
        {{--        <span class="d-flex">--}}
        {{--    @include('view.img',['item'=>$item_find, 'size'=>"avatar", 'filenametrue'=>false, 'border'=>true, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>trans('main.author')])--}}
        @include('view.img',['item'=>$item_find, 'size'=>$size, 'circle'=>$circ_para, 'noimg_def'=>true, 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>trans('main.author')])
        {{--                    </span>--}}
        @endif
        {{--        {{$it_nm}}--}}
        @else
            {{--        <span class="d-flex flex-nowrap">--}}
            @if($link_image)
                {{--            <span class="rounded-circle d-flex align-items-center justify-content-center"--}}
                {{--            <span class="d-flex align-items-center justify-content-center"--}}
                <span class="{{$circle_rounded}} d-flex align-items-center justify-content-center font-weight-bold"
                      style="width: {{$h_text}}px; height: {{$h_text}}px; color: {{$my_color}}; background-color: {{$my_bg_color}}; float: left; margin: 3px;">
                                {{mb_strtoupper(mb_substr($it_nm, 0, 1))}}
                </span>
            @endif
            {{--    <span class="d-flex align-items-center">--}}
            {{--                     &nbsp;{{$it_nm}}--}}
            {{--                </span>--}}
            {{--            </span>--}}
        @endif
        {{--</span>--}}
    </span>
    <span class="p-1">
        @include('layouts.item.empty_name', ['name'=>$item->nmbr(true, false, false, false, false, null, false, true, $relit_id, $role)])
        {{--<img src="/storage/logotype.png" width="30" height="30"--}}
        {{--     style="float: left; margin: 3px">content2222</img>--}}
        {{--<span class=" d-flex align-items-center justify-content-center font-weight-bold "--}}
        {{--      style="width: 50px; height: 50px; color: #FFFFFF; background-color: #3f6900; float: left; margin: 3px">--}}
        {{--К--}}
        {{--</span>--}}
        {{--    <p>--}}
        {{--    FFFFFFFFFFFFFFFFFF--}}
        {{--    HHHHHHHHHHHHHHHHHHHHHHHH--}}
        {{--    YYYYYYYYYYYYYYYYYYYYYYY--}}
        {{--    JJJJJJJJJJJJJJJJJJJJJJJ--}}
        {{--    UUUUUUUUUUUUUUUUUUUUUUUU--}}
        {{--</p>--}}
    </span>
</span>
