<?php
// Алгоритмы одинаковые в view.img.blade.php и GlobalController::view_img();
//Много похожих строк в этом файле;
//Похожие алгоритмы:
//view/doc.php, edit/doc_base.php, edit/doc_link.php,
//view/img.php, edit/img_base.php, edit/img_link.php.
use \App\Http\Controllers\GlobalController;

$url_filename = "noimage.png";
$is_moderation_info = false;
if ($item) {
    if ($item->base->type_is_image()) {
        if ($item->img_doc_exist()) {
            if ($filenametrue == true) {
                $url_filename = $item->filename(true);
            } else {
                $url_filename = $item->filename();
            }
            $is_moderation_info = $item->is_moderation_info();
        }
    }
}
?>
@if($link == true)
    <a href="{{Storage::url($url_filename)}}">
        @endif
        <img src="{{Storage::url($url_filename)}}"
{{--             style=""--}}
             style="object-fit:contain;border: solid #bfc7f6;"
             @if($card_img_top)
             {{--                                    class="card-img-top" style="object-fit:contain"--}}
             class="card-img-top"
             @endif
             @if($size == 'avatar')
             class="circle"
             {{--                                    @elseif( == 'medium')--}}
             {{--                                    class="rectangle"--}}
             @endif
             @if($img_fluid == true)
             class="img-fluid"
             @endif
                 width="300px"
             height=
             @include('types.img.height',['size'=>$size])
                 alt="" title=
             @if($title == "")
                 "{{$item->title_img()}}"
        @elseif($title == "empty")
            ""
        @else
            "{{$title}}"
        @endif
        >
        @if($link == true)
    </a>
@endif
        @if($is_moderation_info == true)
            <div class="text-danger">
                {{$item->title_img()}}</div>
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
