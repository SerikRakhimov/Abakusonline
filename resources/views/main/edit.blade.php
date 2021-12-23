@extends('layouts.app')

@section('content')
<?php
$update = isset($main);
?>
<h3 class="display-5">
    @if (!$update)
        {{trans('main.new_record')}}
    @else
        {{trans('main.edit_record')}}
    @endif
    <span class="text-info">-</span> <span class="text-success">{{trans('main.main')}}</span>
</h3>
<br>
<form action ="{{$update ? route('main.update',$main):route('main.store')}}" method="POST" enctype=multipart/form-data name = "form">
    @csrf

    @if ($update)
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="link_id">{{trans('main.link')}}<span class="text-danger">*</span></label>
        <select class="form-control"
                name="link_id"
                id="link_id"
                class="form-control @error('link_id') is-invalid @enderror">
            @foreach ($links as $link)
                <option value="{{$link->id}}"
                        {{--            "(int) 0" нужно--}}
                        @if ((old('link_id') ?? ($main->link_id ?? (int) 0)) ==  $link->id)
                        selected
                    @endif
                >{{$link->info()}}</option>
            @endforeach
        </select>
        @error('link_id')
        <div class="text-danger">
            {{$message}}
        </div>
        @enderror
    </div>

    <div class="form-group">
        <label for="child_item_id"><span id="child_item_id_label"></span><span class="text-danger">*</span></label>
        <select class="form-control"
                name="child_item_id"
                id="child_item_id"
                class="form-control @error('child_item_id') is-invalid @enderror">
        </select>
        @error('child_item_id')
        <div class="text-danger">
            {{$message}}
        </div>
        @enderror
        <div class="text-danger">
            {{-- session('errors') передается командой в контроллере "return redirect()->back()->withInput()->withErrors(...)"--}}
            {{session('errors')!=null ? session('errors')->first('message_child_base_id'): ''}}
        </div>
    </div>

    <div class="form-group">
        <label for="parent_item_id"><span id="parent_item_id_label"></span><span class="text-danger">*</span></label>
        <select class="form-control"
                name="parent_item_id"
                id="parent_item_id"
                class="form-control @error('parent_item_id') is-invalid @enderror">
        </select>
        @error('parent_item_id')
        <div class="text-danger">
            {{$message}}
        </div>
        @enderror
        <div class="text-danger">
            {{-- session('errors') передается командой в контроллере "return redirect()->back()->withInput()->withErrors(...)"--}}
            {{session('errors')!=null ? session('errors')->first('message_parent_base_id'): ''}}
        </div>
    </div>

    <button type = "submit" class="btn btn-primary">
        @if (!$update)
            {{trans('main.add')}}
        @else
            {{trans('main.save')}}
        @endif
    </button>
    <a class="btn btn-success" href="{{ route('main.index') }}">{{trans('main.cancel')}}</a>
    </form>

<script>
    var link_id = form.link_id;
    var child_item_id = form.child_item_id;
    var child_item_id_label = document.getElementById("child_item_id_label");
    var parent_item_id = form.parent_item_id;
    var parent_item_id_label = document.getElementById("parent_item_id_label");
    var child_item_id_value = null;
    var parent_item_id_value = null;

    function link_id_changeOption(first){
        axios.get('/item/get_items_for_link/' + link_id.options[link_id.selectedIndex].value).then(function (res){
            // если запуск функции не при загрузке страницы
            if (first != true) {
                // сохранить текущие значения
                var child_item_id_value = child_item_id.options[child_item_id.selectedIndex].value;
                var parent_item_id_value = parent_item_id.options[parent_item_id.selectedIndex].value;
            }

            // заполнение label
            child_item_id_label.textContent = res.data['result_child_base_name'];
            parent_item_id_label.textContent = res.data['result_parent_label']
                + " (" + res.data['result_parent_base_name'] +")";

            if(res.data['result_child_base_items_options'] == ""){
                child_item_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + res.data['result_child_base_name'] + '"!</option>';
                //arent_item_id.innerHTML = "<option>Выберите героя</option>";
                //return;
            }
            else{
                // заполнение select
                child_item_id.innerHTML = res.data['result_child_base_items_options'];
            }

            if(res.data['result_parent_base_items_options'] == ""){
                parent_item_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + res.data['result_parent_base_name'] + '"!</option>';
                //return;
            }else{
                // заполнение select
                parent_item_id.innerHTML = res.data['result_parent_base_items_options'];
            }

            // только если запуск функции при загрузке страницы
            if (first == true) {
                // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                @if ($update)  // при корректировке записи
                    // child
                    for (let i = 0; i < child_item_id.length; i++) {
                        // если элемент списка = текущему значению из базы данных
                        if (child_item_id[i].value == {{$main->child_item_id}}) {
                            // установить selected на true
                            child_item_id[i].selected = true;
                        }
                    }
                    // parent
                    for (let i = 0; i < parent_item_id.length; i++) {
                        // если элемент списка = текущему значению из базы данных
                        if (parent_item_id[i].value == {{$main->parent_item_id}}) {
                            // установить selected на true
                            parent_item_id[i].selected = true;
                        }
                    }
                @endif
            }
            else{
                // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                // child
                for (let i = 0; i < child_item_id.length; i++) {
                    // если элемент списка = предыдущему(текущему) значению из базы данных
                    if (child_item_id[i].value == child_item_id_value) {
                        // установить selected на true
                        child_item_id[i].selected = true;
                    }
                }
                // parent
                for (let i = 0; i < parent_item_id.length; i++) {
                    // если элемент списка = предыдущему(текущему) значению из базы данных
                    if (parent_item_id[i].value == parent_item_id_value) {
                        // установить selected на true
                        parent_item_id[i].selected = true;
                    }
                }
            }
        });
    }

    link_id.addEventListener("change", link_id_changeOption);

    window.onload = function() {
        link_id_changeOption(true);
    };

</script>
@endsection
