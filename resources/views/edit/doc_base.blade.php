@if($base->type_is_document())
    <div class="form-group row">
        <div class="col-sm-3 text-right">
            {{--Выберите файл - документ (.xls, .xlsx, .pdf, .doc, .docx, .rtf, .txt)--}}
            <label for="{{$name}}">{{$title}} (.xls, .xlsx, .pdf, .doc, .docx, .rtf, .txt)<span
                    class="text-danger">*</span>
                @if($update)
                    @if($item->img_doc_exist())
                        ({{mb_strtolower(trans('main.now'))}}:<a href="{{Storage::url($item->filename())}}"
                                                                 target="_blank">
                            {{trans('main.open_document')}}
                        </a>)
                    @endif
                @endif
            </label>
        </div>
        <div class="col-sm-4">
            <input type="file"
                   name="{{$name}}" id="{{$id}}"
                   class="@error("$name") is-invalid @enderror"
                   accept=".xls, .xlsx, .pdf, .doc, .docx, .rtf, .txt">
            @error($name)
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>
        <div class="col-sm-7-left">
            <label>{{trans('main.explanation_doc')}}
                ({{mb_strtolower(trans('main.maximum'))}} {{$base->maxfilesize_title_img_doc}})</label>
        </div>
    </div>
@endif
