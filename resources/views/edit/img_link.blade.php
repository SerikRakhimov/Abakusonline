<?php
//Много похожих строк в этом файле;
//Похожие алгоритмы:
//view/doc.php, edit/doc_base.php, edit/doc_link.php,
//view/img.php, edit/img_base.php, edit/img_link.php.
use App\Models\Item;
use \App\Http\Controllers\GlobalController;

$item_image = null;
if ($update) {
    if ($value != null) {
        $item_image = Item::find($value);
    }
}
?>
{{--Если НЕ(добавление и read), то вообще не отображать ничего--}}
@if(!(!$update && $base_link_right['is_edit_link_read'] == true))
    @if($base->type_is_image())
        <div class="form-group row">
            {{--            Если read--}}
            @if($base_link_right['is_edit_link_read'] == true)
                <div class="col-sm-3 text-right">
                    <label for="{{$name}}">{{$title}}<span
                            class="text-danger">{{GlobalController::label_is_required($base)}}</span>
                    </label>
                </div>
                <div class="col-sm-4">
                    @if ($item_image != null)
                        {{-- В режиме корректировки формы не нужно--}}
                        {{-- <a href="{{Storage::url($item_image->filename())}}">--}}
                        <img src="{{Storage::url($item_image->filename())}}"
                             height=@include('types.img.height',['size'=>$size])
                                 alt="" title="{{$item_image->title_img()}}">
                        {{--                        </a>--}}
                    @endif
                </div>
                <div class="col-sm-5-left">
                </div>
            @endif
            {{--            Если права - update/edit--}}
            @if($base_link_right['is_edit_link_update'] == true)
                <div class="col-sm-3 text-right">
                    {{--                            Выберите файл - изображение, размером не более 500 Кб--}}
                    <label for="{{$name}}">{{$title}}<span
                            class="text-danger">{{GlobalController::label_is_required($base)}}</span>
                        @if ($item_image != null)
                            {{-- "(" нужно --}}
                            ({{mb_strtolower(trans('main.now'))}}:
                            {{-- В режиме корректировки формы не нужно--}}
                            {{-- <a href="{{Storage::url($item_image->filename())}}">--}}
                            <img src="{{Storage::url($item_image->filename())}}"
                                 height=@include('types.img.height',['size'=>$size])
                                     alt="" title="{{$item_image->title_img()}}">
                            {{--                            </a>--}}
                            @if($base->is_required_lst_num_str_txt_img_doc == false)
                                <label for="{{$name}}_img_doc_delete">{{trans('main.delete_image')}}
                                </label>
                                <input type="checkbox"
                                       name="{{$name}}_img_doc_delete"
                                       placeholder=""
                                       title="{{trans('main.delete_image')}}"
                                >
                            @endif
                            {{-- ")" нужно --}}
                            )
                        @endif
                    </label>
                </div>
                <div class="col-sm-6">
                    <input type="file"
                           name="{{$name}}" id="{{$id}}"
                           class="@error("$name") is-invalid @enderror"
                           accept="image/*">
                    @error($name)
                    <div class="text-danger">
                        {{$message}}
                    </div>
                    @enderror
                    {{--                    <label for="img{{$name}}">{{trans('main.selected_image')}}:--}}
                    {{--                    </label>--}}
                    <img src=""
{{--                     id="img{{$name}}" name="img{{$name}}"--}}
                         id="img{{$name}}"
                         height=@include('types.img.height',['size'=>$size])
                             alt="" title="{{trans('main.selected_image')}}">
                </div>
                <div class="col-sm-3-left">
                    {{--                    <label>{{trans('main.explanation_img')}}--}}
                    {{--                        ({{mb_strtolower(trans('main.maximum'))}} {{$base->maxfilesize_title_img_doc}})--}}
                    {{--                    </label>--}}
                </div>
                <script>
                    img{{$name}} = document.getElementById("img{{$name}}");
                    {{--Используется 'document.getElementById("link{{$name}}"'--}}
                        file{{$name}} = document.getElementById("link{{$name}}");
                    file{{$name}}.addEventListener('change', function () {
                        img{{$name}}.src = URL.createObjectURL(file{{$name}}.files[0]);
                        {{--img{{$name}}.style.display = "inline";--}}
                        {{--img{{$name}}.style.display = "block";--}}
                    });
                </script>
            @endif
        </div>
    @endif
@endif

