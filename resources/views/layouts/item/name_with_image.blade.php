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
$random_string = md5($item->id);
$hex_string = substr($random_string, 0, 6); // Получаем 10-значную шестнадцатеричную строку
// Пример использования dechex()
// 16 в шестой степени равно 16 777 216
//$random_decimal1 = mt_rand(0, 16777216); // Генерируем случайное десятичное число
//$hex_string_md1 = dechex($random_decimal1); // Преобразуем его в шестнадцатеричную строку
// Цвет текста: "#FFFFFF" - белый цвет
$my_color = "#FFFFFF";
// Цвет фона
$my_bg_color = "#" . $hex_string;
$height = GlobalController::types_img_height($size, false);
$it_nm = GlobalController::itnm_left($item->name(),25);
?>
<span class="text-nowrap">
    @if($item_find)
        @if($link_image)
            {{--    @include('view.img',['item'=>$item_find, 'size'=>"avatar", 'filenametrue'=>false, 'border'=>true, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>trans('main.author')])--}}
            @include('view.img',['item'=>$item_find, 'size'=>$size, 'circle'=>$circ_para, 'noimg_def'=>true, 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>trans('main.author')])
        @endif
        {{$it_nm}}
    @else
        <span class="d-flex flex-nowrap">
        @if($link_image)
                <span class="rounded-circle d-flex align-items-center justify-content-center font-weight-bold"
                      style="width: {{$height}}px; height: {{$height}}px; color: {{$my_color}}; background-color: {{$my_bg_color}};">
                                {{mb_strtoupper(mb_substr($item->name(), 0, 1))}}
            </span>
            @endif
                <span class="d-flex align-items-center">
                     &nbsp;{{$it_nm}}
            </span>
        </span>
    @endif
</span>

