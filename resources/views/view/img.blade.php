<?php
// Алгоритмы одинаковые в view.img.blade.php и GlobalController::view_img();
//Много похожих строк в этом файле;
//Похожие алгоритмы:
//view/doc.php, edit/doc_base.php, edit/doc_link.php,
//view/img.php, edit/img_base.php, edit/img_link.php.
use \App\Http\Controllers\GlobalController;

// Нужно "", ниже идет сравнение на ""
$url_filename = "";
$is_moderation_info = false;
if ($item) {
    if ($item->base->type_is_image()) {
        if ($item->img_doc_exist()) {
            if ($filenametrue == true) {
                // '$moderation = true' -  возвращать имя файла, независимо прошло/не прошло модерацию
                $url_filename = $item->filename(true);
            } else {
                $url_filename = $item->filename();
            }
            $is_moderation_info = $item->is_moderation_info();
        }
    }
} else {
    if (isset($noimg_def)) {
        if ($noimg_def == true) {
            $url_filename = "noimage.png";
//            if (isset($project)) {
//                if ($project) {
//                    $get_project_logo_item = $project->get_items_setup()['logo_item'];
//                    if ($get_project_logo_item) {
//                        $url_filename = $get_project_logo_item->filename();
//                    }
//                }
//            }
        }
    }
}

?>
@if($url_filename !="")
    @if($link == true)
        <a href="{{Storage::url($url_filename)}}">
            @endif
            <img src="{{Storage::url($url_filename)}}"
                 @if(isset($var_percent))
                 @if($item)
                 id="img{{$item->id}}"
                 @endif
                 @endif
                 style="object-fit:cover;
                 {{--                 style="object-fit:scale-down;--}}
                 {{--                 style="object-fit:contain;--}}
                 @if(isset($border))
                 @if($border==true)
                     border: solid #bfc7f6;
                 @endif
                 @endif
                     "
                 @if($card_img_top)
                 {{--                 class="card-img-top" style="object-fit:contain"--}}
                 class="card-img-top"
                 @endif
                 @if(isset($size))
                 @if($size == 'avatar')
                 class="circle"
                 {{--                                    @elseif( == 'medium')--}}
                 {{--                                    class="rectangle"--}}
                 @endif
                 @endif
                 @if($img_fluid == true)
                 class="img-fluid"
                 @endif
                 @if(!isset($var_percent))
                 {{--                                   Обязательно так нужно(устанавливать значения ширину и высоту):--}}
                 {{--                 width="{{$var_percent}}%"--}}
                 {{--                 height="{{$var_percent}}%"--}}
                 {{--                 @if(isset($width))--}}
                 {{--                 width={{$width}}--}}
                 {{--                 @endif--}}
                 {{--                 @if(!isset($width) & isset($size))--}}
                 {{--                     height=@include('types.img.height',['size'=>$size])--}}
                 {{--                 @endif--}}
                 @if(isset($width))
                 width={{$width}}
                 @else
                 @if(isset($size))
                     height=@include('types.img.height',['size'=>$size])
                 @endif
                 @endif
                 @endif
                     alt="" title=
                 @if($title == "")
                 @if($item)
                     "{{$item->title_img()}}"
            @endif
            @elseif($title == "empty")
                ""
            @else
                "{{$title}}"
            @endif
            >
            @if(isset($var_percent))
                @if($item)
                    <script>
                        document.getElementById("img{{$item->id}}").title = 'var4: ';
                        {{-- https://www.manhunter.ru/webmaster/905_kak_na_javascript_uznat_realniy_razmer_izobrazheniya.html--}}
                        {{--get_dimensions(el) описана в layouts\app.php--}}
                        var el = document.getElementById("img{{$item->id}}");
                        {{-- Изображение уже загружено или взято из кэша браузера--}}
                        if (el.complete) {
                            var tmp = get_dimensions(el);
                            {{-- el.title = 'complete: ' + [tmp.real_width, tmp.real_height, tmp.client_width, tmp.client_height];--}}
                                {{-- *** Блок одинаковых строк (см. ниже по тексту) *** --}}
                            if (tmp) {
                                document.getElementById("img{{$item->id}}").title = 'var6: ';
                                {{-- Вертикальная(книжная) картинка и горизонтальный(альбомный) экран --}}
                                if ((tmp.real_height > tmp.real_width) & (window.innerHeight < window.innerWidth)) {
                                    document.getElementById("img{{$item->id}}").height = Math.ceil(window.innerHeight * {{$var_percent}} / 100);
                                } else {
                                    {{--Одинаковый процент 0.75 layouts\app.php и view\img.php--}}
                                    document.getElementById("img{{$item->id}}").width = Math.ceil(window.innerWidth * {{$var_percent}} / 100 * 0.75);
                                }
                            }
                            {{-- *** --}}
                        }
                            {{-- Ожидаем загрузки изображения--}}
                        else {
                            el.onload = function (event) {
                                event = event || window.event;
                                var el = event.target || event.srcElement;
                                var tmp = get_dimensions(el);
                                {{-- el.title = 'onload: ' + [tmp.real_width, tmp.real_height, tmp.client_width, tmp.client_height];--}}
                                    {{-- *** Блок одинаковых строк (см. выше по тексту)  *** --}}
                                if (tmp) {
                                    document.getElementById("img{{$item->id}}").title = 'var6: ';
                                    {{-- Вертикальная(книжная) картинка и горизонтальный(альбомный) экран --}}
                                    if ((tmp.real_height > tmp.real_width) & (window.innerHeight < window.innerWidth)) {
                                        document.getElementById("img{{$item->id}}").height = Math.ceil(window.innerHeight * {{$var_percent}} / 100);
                                    } else {
                                        {{--Одинаковый процент 0.75 layouts\app.php и view\img.php--}}
                                        document.getElementById("img{{$item->id}}").width = Math.ceil(window.innerWidth * {{$var_percent}} / 100 * 0.75);
                                    }
                                }
                                {{-- *** --}}
                            }
                        }
                    </script>
                @endif
            @endif
            @if($link == true)
        </a>
    @endif
    @if($is_moderation_info == true)
        <div class="text-danger">
            {{$item->title_img()}}</div>
    @endif
@endif
{{--        @if($item->base->type_is_image())--}}
{{--            @if($item->img_doc_exist())--}}
{{--                @if($filenametrue == true)--}}
{{--                    @if($link == true)--}}
{{--                        <a href="{{Storage::url($item->filename(true))}}">--}}
{{--                            @endif--}}
{{--                            <img src="{{Storage::url($item->filename(true))}}"--}}
{{--                            @else--}}
{{--                                @if($link == true)--}}
{{--                                    <a href="{{Storage::url($item->filename())}}">--}}
{{--                                        @endif--}}
{{--                                        <img--}}
{{--                                            @if($card_img_top)--}}
{{--                                            --}}{{--                                    class="card-img-top" style="object-fit:contain"--}}
{{--                                            class="card-img-top" style="object-fit:cover"--}}
{{--                                            @endif--}}
{{--                                            @if($size == 'avatar')--}}
{{--                                            class="circle"--}}
{{--                                            --}}{{--                                    @elseif( == 'medium')--}}
{{--                                            --}}{{--                                    class="rectangle"--}}
{{--                                            @endif--}}
{{--                                            @if($img_fluid == true)--}}
{{--                                            class="img-fluid"--}}
{{--                                            @endif--}}
{{--                                            src="{{Storage::url($item->filename())}}"--}}
{{--                                            @endif--}}
{{--                                            height=--}}
{{--                                            @include('types.img.height',['size'=>$size])--}}
{{--                                                alt="" title=--}}
{{--                                            @if($title == "")--}}
{{--                                                "{{$item->title_img()}}"--}}
{{--                                        @elseif($title == "empty")--}}
{{--                                            ""--}}
{{--                                        @else--}}
{{--                                            "{{$title}}"--}}
{{--                                        @endif--}}
{{--                                        >--}}
{{--                                        @if($link == true)--}}
{{--                                    </a>--}}
{{--                                @endif--}}
{{--                                @if($item->is_moderation_info() == true)--}}
{{--                                    <div class="text-danger">--}}
{{--                                        {{$item->title_img()}}</div>--}}
{{--                                @endif--}}
{{--                                @else--}}
{{--                                    <div class="text-danger">--}}
{{--                                        {{GlobalController::image_is_missing_html()}}</div>--}}
{{--@endif--}}
{{--@endif--}}
