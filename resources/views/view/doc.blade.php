<?php
// Алгоритмы одинаковые в view.doc.blade.php и GlobalController::view_doc()
use \App\Http\Controllers\GlobalController;
{{--        Предыдущий вариант--}}
{{--        <a href="{{Storage::url($item->filename())}}" target="_blank"--}}
{{--           title="{{$item->title_img()}}">--}}
{{--            {{trans('main.open_document')}}--}}
{{--        </a>--}}
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

