<?php
use App\Models\Item;
?>
@if($base->type_is_image())
    <div class="form-group row">
        <div class="col-sm-3 text-right">
            {{--                            Выберите файл - изображение, размером не более 500 Кб--}}
            <label for="{{$name}}">{{$title}}<span
                    class="text-danger">*</span>
                @if($update)
                    @if ($value != null)
                        <?php
                        $item_image = Item::find($value);
                        ?>
                        @if ($item_image != null)
                            ({{mb_strtolower(trans('main.now'))}}:<a href="{{Storage::url($item_image->filename())}}">
                                <img src="{{Storage::url($item_image->filename())}}"
                                     height=@include('types.img.height',['size'=>$size])
                                         alt="" title="{{$item_image->title_img()}}">
                            </a>
                            @if($base->is_required_lst_num_str_txt_img_doc == false)
                                <label for="{{$name}}_img_doc_delete">{{trans('main.delete_image')}}
                                </label>
                                <input type="checkbox"
                                       name="{{$name}}_img_doc_delete"
                                       placeholder=""
                                       title="{{trans('main.delete_image')}}"
                                >
                            @endif
    )
                        @endif
                    @endif
                @endif
            </label>
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
        </div>
        <div class="col-sm-5-left">
            <label>{{trans('main.explanation_img')}}
                ({{mb_strtolower(trans('main.maximum'))}} {{$base->maxfilesize_title_img_doc}})</label>
        </div>
    </div>
@endif

