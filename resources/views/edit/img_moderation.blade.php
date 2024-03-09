    <div class="form-group row">
        <div class="col-sm-3 text-right">
            {{--                            Выберите файл - изображение, размером не более 500 Кб--}}
            <label for="{{$name}}">{{$item->base->name()}}<span
                    class="text-danger">*</span></label>
        </div>
        <div class="col-sm-4">
            @if($item->img_doc_exist())
                ({{mb_strtolower(trans('main.now'))}}:<a href="{{Storage::url($item->filename(true))}}">
                    <img src="{{Storage::url($item->filename(true))}}"
                         height=@include('types.img.height',['size'=>$size])
                             alt="" title="{{$item->title_img()}}">
                </a>)
                @endif
                </label>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-3 text-right">
            {{--                            Выберите файл - изображение, размером не более 500 Кб--}}
        </div>
        <div class="col-sm-4">
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
                 id="img{{$name}}"
                 height=@include('types.img.height',['size'=>"medium"])
                     alt="" title="{{trans('main.selected_image')}}">
        </div>
        <div class="col-sm-5-left">
{{--            <label>{{trans('main.explanation_img')}}--}}
{{--                ({{mb_strtolower(trans('main.maximum'))}} {{$item->base->maxfilesize_title_img_doc}})--}}
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




