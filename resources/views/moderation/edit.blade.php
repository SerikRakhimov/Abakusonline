@extends('layouts.app')

@section('content')
    <?php
    $update = isset($item);
    ?>
    <h3 class="display-5 text-center">
        @if (!$update)
            {{trans('main.new_record')}}
        @else
            {{trans('main.edit_record')}}
        @endif
        <span class="text-info">-</span> <span class="text-success">{{trans('main.item')}}</span>
    </h3>
    <br>

    <form action="{{route('moderation.update',$item)}}" method="POST"
          enctype=multipart/form-data>
        @csrf

        @if ($update)
            @method('PUT')
        @endif
        <div class="form-group row">
            <div class="col-sm-3 text-right">
                <label>Id</label>
            </div>
            <div class="col-sm-9">
                <label>{{$item->id}}</label>
            </div>
        </div>
        @include('edit.img_moderation',['item'=>$item ?? null, 'name'=>"name_lang_0",'id'=>"name_lang_0", 'size'=>"big"])
        <div class="form-group row">
            <div class="col-sm-3 text-right">
                <label for="name_lang_1" class="col-form-label">{{trans('main.status')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-sm-7">
                <select class="form-control"
                        name="name_lang_1"
                        id="name_lang_1"
                        class="@error('name_lang_1') is-invalid @enderror">
                    @foreach ($statuses as $key=>$value)
                        <option value="{{$key}}"
                                @if ($update)
                                {{--            "(int) 0" нужно--}}
                                @if ((old('name_lang_1') ?? ($key ?? (int) 0)) ==  $item->name_lang_1)
                                selected
                            @endif
                            @endif
                        >{{$value}}</option>
                    @endforeach
                </select>
                @error('name_lang_1')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="explanation_form_group">
            <div class="col-3 text-right">
                <label for="name_lang_2" class="col-form-label">{{trans('main.explanation')}}<span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-7">
                <input type="text"
                       name="name_lang_2"
                       id="name_lang_2"
                       class="form-control @error('name_lang_2') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('name_lang_2') ?? ($item['name_lang_2'] ?? '') }}">
            </div>
            @error('name_lang_2')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>
        <div class="form-group row">
            <div class="col-sm-3 text-right">
                <label>{{trans('main.project')}}</label>
            </div>
            <div class="col-sm-9">
                <label>{{$item->project->name_id()}}</label>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3 text-right">
                <label>{{trans('main.template')}}</label>
            </div>
            <div class="col-sm-9">
                <label>{{$item->project->template->name_id()}}</label>
            </div>
        </div>
        <br>
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-5 text-right">
                    <button type="submit" class="btn btn-dreamer"
                            @if (!$update)
                            {{--                            d-inline нужно, чтобы на маленьких экранах иконка и текст кнопки были на одной линии--}}
                            {{--                            этот вариант убирает иконку на мобильных телефонах: title="{{trans('main.add')}}"><i class="fas fa-save d-none d-sm-inline"></i>&nbsp;{{trans('main.add')}}--}}
                            title="{{trans('main.add')}}"><i
                            class="fas fa-save d-inline"></i>&nbsp;{{trans('main.add')}}
                        @else
                            title="{{trans('main.save')}}"><i class="fas fa-save d-inline"></i>
                            &nbsp;{{trans('main.save')}}
                        @endif
                    </button>
                </div>
                <div class="col-2">
                </div>
                <div class="col-5 text-left">
                    <button type="button" class="btn btn-dreamer" title="{{trans('main.cancel')}}"
                        @include('layouts.moderation.previous_url')
                    ><i class="fas fa-arrow-left d-inline"></i>&nbsp;{{trans('main.cancel')}}
                    </button>
                </div>
            </div>
        </div>
    </form>
    <script>

        var varstatus = document.getElementById('name_lang_1');
        var explanation = document.getElementById('explanation_form_group');
        var varstatus_value = null;

        function varstatus_changeOption() {
            // сохранить текущие значения
            varstatus_value = varstatus.options[varstatus.selectedIndex].value;

            val_explanation = "hidden";
            // Не прошло модерацию
            if (varstatus.options[varstatus.selectedIndex].value == 2) {
                val_explanation = "visible";
            }

            explanation.style.visibility = val_explanation;

        }

        varstatus.addEventListener("change", varstatus_changeOption);

        window.onload = function () {
            varstatus_changeOption();
        };

    </script>
@endsection
