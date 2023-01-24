<?php
//Много похожих строк в этом файле;
//Похожие алгоритмы:
//view/doc.php, edit/doc_base.php, edit/doc_link.php,
//view/img.php, edit/img_base.php, edit/img_link.php.
use App\Models\Item;
$item_doc = null;
if ($update) {
    if ($value != null) {
        $item_doc = Item::find($value);
    }
}
?>
{{--Если НЕ(добавление и read), то вообще не отображать ничего--}}
@if(!(!$update && $base_link_right['is_edit_link_read'] == true))
    @if($base->type_is_document())
        <div class="form-group row">
            {{--            Если read--}}
            @if($base_link_right['is_edit_link_read'] == true)
                <div class="col-sm-3 text-right">
                    <label for="{{$name}}">{{$title}}<span
                            class="text-danger">{{GlobalController::label_is_required($base)}}</span>
                    </label>
                </div>
                <div class="col-sm-4">
                    @if ($item_doc != null)
                        <a href="{{route('item.doc_download', ['item'=>$item_doc, 'usercode'=>$usercode])}}"
                           target="_blank">
                            {{trans('main.open_document')}}
                        </a>
                    @endif
                </div>
                <div class="col-sm-5-left">
                </div>
            @endif
            {{--            Если update--}}
            @if($base_link_right['is_edit_link_update'] == true)
                <div class="col-sm-3 text-right">
                    {{--Выберите файл - документ (.xls, .xlsx, .pdf, .doc, .docx, .rtf, .txt)--}}
                    <label for="{{$name}}">{{$title}}<span
                            class="text-danger">{{GlobalController::label_is_required($base)}}</span>
                        @if ($item_doc != null)
                            ({{mb_strtolower(trans('main.now'))}}:<a
                                href="{{route('item.doc_download', ['item'=>$item_doc, 'usercode'=>$usercode])}}"
                                target="_blank">
                                {{trans('main.open_document')}}
                            </a>
                            @if($base->is_required_lst_num_str_txt_img_doc == false)
                                <label for="{{$name}}_img_doc_delete">{{trans('main.delete_document')}}
                                </label>
                                <input type="checkbox"
                                       name="{{$name}}_img_doc_delete"
                                       placeholder=""
                                       title="{{trans('main.delete_document')}}"
                                >
                            @endif
                                )
                        @endif
                    </label>
                </div>
                <div class="col-sm-4">
                    <input type="file"
                           accept=".xls, .xlsx, .pdf, .doc, .docx, .rtf, .txt"
                           {{--                   accept="application/excel, application/pdf, application/msword, application/rtf, text/plain"--}}
                           name="{{$name}}" id="{{$id}}"
                           class="@error("$name") is-invalid @enderror"
                    >
                    @error($name)
                    <div class="text-danger">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-sm-5-left">
                    <label>{{trans('main.explanation_doc')}}
                        ({{mb_strtolower(trans('main.maximum'))}} {{$base->maxfilesize_title_img_doc}})
                        (.xls, .xlsx, .pdf, .doc, .docx,.rtf, .txt)</label>
                </div>
            @endif
        </div>
    @endif
@endif

