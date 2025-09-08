<?php

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
$hex_string_md5 = substr($random_string, 0, 6); // Получаем 10-значную шестнадцатеричную строку
// Пример использования dechex()
$random_decimal1 = mt_rand(0, 16777216); // Генерируем случайное десятичное число
$random_decimal2 = mt_rand(0, 16777216); // Генерируем случайное десятичное число
$hex_string_md1 = dechex($random_decimal1); // Преобразуем его в шестнадцатеричную строку
$hex_string_md2 = dechex($random_decimal2); // Преобразуем его в шестнадцатеричную строку
//$my_color = "#581303";
//$my_color = "#". mt_rand(1, 999999)
$my_color1 = "#". $hex_string_md1;
$my_color1 = "#FFFFF0";
$my_color2 = "#". $hex_string_md2;
$my_color2 = "#". $hex_string_md5;
?>
<span class="text-nowrap">
{{--    "@if($link_image)" использовать--}}
    {{--    @if($link_image)--}}
    {{--        --}}{{--    @include('view.img',['item'=>$item_find, 'size'=>"avatar", 'filenametrue'=>false, 'border'=>true, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>trans('main.author')])--}}
    {{--        @include('view.img',['item'=>$item_find, 'size'=>$size, 'circle'=>$circ_para, 'noimg_def'=>true, 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>trans('main.author')])--}}
    {{--    @endif--}}
    {{--    {{$item->name()}}--}}
{{--    <div class="row">--}}
{{--    <div class="col-6 text-center">--}}
        @if($item_find)
            @if($link_image)
                {{--    @include('view.img',['item'=>$item_find, 'size'=>"avatar", 'filenametrue'=>false, 'border'=>true, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>trans('main.author')])--}}
                @include('view.img',['item'=>$item_find, 'size'=>$size, 'circle'=>$circ_para, 'noimg_def'=>true, 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>trans('main.author')])
        @else
                NoImage
        @endif
                {{$item->name()}}
        @else
{{--            <span class="rounded-circle d-flex align-items-center justify-content-center font-weight-bold" style="width: 30px; height: 30px; color: {{$my_color1}}; background-color: {{$my_color2}};" >--}}
{{--  https://ru.stackoverflow.com/questions/197952/%D0%9F%D0%B5%D1%80%D0%B2%D1%8B%D0%B9-%D1%81%D0%B8%D0%BC%D0%B2%D0%BE%D0%BB-%D1%81%D1%82%D1%80%D0%BE%D0%BA%D0%B8#comment197968_197959--}}
{{--                {{mb_substr($item->name(), 0, 1)}}--}}
{{--            </span>--}}
    <span class="d-flex flex-nowrap">
        <span class="rounded-circle d-flex align-items-center justify-content-center font-weight-bold" style="width: 30px; height: 30px; color: {{$my_color1}}; background-color: {{$my_color2}};" >
{{--  https://ru.stackoverflow.com/questions/197952/%D0%9F%D0%B5%D1%80%D0%B2%D1%8B%D0%B9-%D1%81%D0%B8%D0%BC%D0%B2%D0%BE%D0%BB-%D1%81%D1%82%D1%80%D0%BE%D0%BA%D0%B8#comment197968_197959--}}
{{--            {{mb_strtoupper(mb_substr($item->name(), 0, 1))}}--}}
            </span>
                <span class="d-flex align-items-center" >
{{--  https://ru.stackoverflow.com/questions/197952/%D0%9F%D0%B5%D1%80%D0%B2%D1%8B%D0%B9-%D1%81%D0%B8%D0%BC%D0%B2%D0%BE%D0%BB-%D1%81%D1%82%D1%80%D0%BE%D0%BA%D0%B8#comment197968_197959--}}
                     &nbsp;{{$item->name()}}
            </span>
        </span>
        @endif
{{--    </div>--}}
{{--        <div class="col-6 left">--}}
{{--            {{$item->name()}}--}}
{{--    </div>--}}
        </div>

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

