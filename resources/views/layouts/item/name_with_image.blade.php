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

//$it_nm = GlobalController::itnm_left($item->name(),25);
$it_nm = $item->name();
$circle_rounded = "";
if ($circle) {
    $circle_rounded = "rounded-circle";
}
?>
<span class="d-flex flex-row align-items-stretch">
    <span class="pt-0">
        {{-- Использовать "&&"--}}
        @if($link_image && $item_find)
            {{--        <span class="d-flex">--}}
            {{--    @include('view.img',['item'=>$item_find, 'size'=>"avatar", 'filenametrue'=>false, 'border'=>true, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>trans('main.author')])--}}
            @include('view.img',['item'=>$item_find, 'size'=>$size, 'circle'=>$circ_para, 'noimg_def'=>true, 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>trans('main.author')])
            {{--                    </span>--}}
        @else
            {{--        <span class="d-flex flex-nowrap">--}}
            {{-- Вывод буквы в кружочке--}}
            <?php
            $item_color = $item;
            // Выводить связанное поле
            if ($link_image->parent_is_parent_related == true) {
                $item_color = GlobalController::view_info($item->id, $link_image->parent_parent_related_start_link_id, $role, $relit_id, true);
            }
            // Проверка с командой внутри нужна
            if (!$item_color) {
                $item_color = $item;
            }
            $height = GlobalController::types_img_height($size, false);
            $h_text = $height;
            // $rnd_colors = GlobalController::rnd_colors($item->id);
            $rnd_colors = GlobalController::rnd_colors($item_color->id);
            // Цвет текста: "#FFFFFF" - белый цвет
            $my_color = $rnd_colors['my_color'];
            // Цвет фона
            $my_bg_color = $rnd_colors['my_bg_color'];
            ?>
            <span class="{{$circle_rounded}} d-flex align-items-center justify-content-center font-weight-bold"
                  style="width: {{$h_text}}px; height: {{$h_text}}px; color: {{$my_color}}; background-color: {{$my_bg_color}}; float: left; margin: 3px;">
                                {{mb_strtoupper(mb_substr($it_nm, 0, 1))}}
            </span>
        @endif
        {{$relit_id}}
    </span>
    <span class="p-1">
        {{-- Вывод наименования--}}
        @include('layouts.item.empty_name', ['name'=>$item->nmbr(true, false, false, false, false, null, false, true, $relit_id, $role)])
    </span>
</span>
