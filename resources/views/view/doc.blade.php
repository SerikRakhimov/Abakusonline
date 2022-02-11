<?php
// Алгоритмы одинаковые в view.doc.blade.php и GlobalController::view_doc();
//Много похожих строк в этом файле;
//Похожие алгоритмы:
//view/doc.php, edit/doc_base.php, edit/doc_link.php,
//view/img.php, edit/img_base.php, edit/img_link.php.
use \App\Http\Controllers\GlobalController;
{{--        Предыдущий вариант--}}
{{--        <a href="{{Storage::url($item->filename())}}" target="_blank"--}}
{{--           title="{{$item->title_img()}}">--}}
{{--            {{trans('main.open_document')}}--}}
{{--        </a>--}}
{{--Ссылка на открытие документа в виде роута, а не в виде прямой ссылки на сайт.--}}
{{--Сделано, чтобы доступ был только у одного пользователя--}}
{{--Ссылка на открытие документа чтобы открывалась у одного пользователя--}}
{{--Функции usercode_calc() и usercode_uncalc() для того, чтобы скрыть user_id в параметрах роута--}}
?>
@if($item->base->type_is_document())
    @if($item->img_doc_exist())
        <a href="{{route('item.doc_download', ['item'=>$item, 'usercode'=>$usercode])}}"
           title="{{$item->title_img()}}">
            {{trans('main.open_document')}}
        </a>
    @else
        <div class="text-danger">
            {{GlobalController::empty_html()}}</div>
    @endif
@endif

