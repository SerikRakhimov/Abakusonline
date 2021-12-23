<?php
// Алгоритмы одинаковые в view.doc.blade.php и GlobalController::view_doc()
use \App\Http\Controllers\GlobalController;
?>
@if($item->base->type_is_document())
    @if($item->img_doc_exist())
        <a href="{{Storage::url($item->filename())}}" target="_blank"
           alt="" title="{{$item->title_img()}}">
            {{trans('main.open_document')}}
        </a>
    @else
        <div class="text-danger">
            {{GlobalController::empty_html()}}</div>
    @endif
@endif

