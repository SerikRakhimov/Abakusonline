<?php
//Много похожих строк в этом файле;
//Похожие алгоритмы:
//view/doc.php, edit/doc_base.php, edit/doc_link.php,
//view/img.php, edit/img_base.php, edit/img_link.php.
use \App\Http\Controllers\GlobalController;

?>
@if($base->type_is_image())
    <div class="form-group row">
        <div class="col-sm-3 text-right">
            {{--                            Выберите файл - изображение, размером не более 500 Кб--}}
            <label for="{{$name}}">{{$title}}<span
                    class="text-danger">{{GlobalController::label_is_required($base)}}</span>
                @if($update)
                    @if($item->img_doc_exist())
                        ({{mb_strtolower(trans('main.now'))}}:
                        {{-- В режиме корректировки формы не нужно--}}
                        {{-- <a href="{{Storage::url($item->filename())}}">--}}
                        <img src="{{Storage::url($item->filename())}}"
                             height=@include('types.img.height',['size'=>$size])
                                 alt="" title="{{$item->title_img()}}">
                        {{-- </a>--}}
                        )
                    @endif
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
            <img src=""
                 id="img{{$name}}" name="img{{$name}}"
                 height=@include('types.img.height',['size'=>$size])
                     alt="" title="{{trans('main.selected_image')}}">
        </div>
        <div class="col-sm-3-left">
            {{--            <label>{{trans('main.explanation_img')}}--}}
            {{--                ({{mb_strtolower(trans('main.maximum'))}} {{$base->maxfilesize_title_img_doc}})--}}
            {{--            </label>--}}
        </div>
        <script>
            img{{$name}} = document.getElementById("img{{$name}}");
            {{--Используется 'document.getElementById("{{$name}}"'--}}
                file{{$name}} = document.getElementById("{{$name}}");
            file{{$name}}.addEventListener('change', function () {
                img{{$name}}.src = URL.createObjectURL(file{{$name}}.files[0]);
                {{--img{{$name}}.style.display = "inline";--}}
                {{--img{{$name}}.style.display = "block";--}}
            });
        </script>
    </div>
@endif

