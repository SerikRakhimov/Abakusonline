@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Link;
    use App\Models\Item;
    use App\Models\Set;
    use \App\Http\Controllers\GlobalController;
    use \App\Http\Controllers\BaseController;
    use \App\Http\Controllers\ItemController;
    use \App\Http\Controllers\LinkController;
    use \App\Http\Controllers\StepController;
    $update = isset($item);
    $base_right = GlobalController::base_right($base, $role);
    if ($update) {
        $project = $item->project;
    } else {
        // Итак должен передаваться в форму $project при добавлении записи
        //$project = $project;
    }
    ?>
    <script>
        function browse(link_id, project_id, role_id, item_id) {
            // Нужно, используется в browser.blade.php
            window.item_id = document.getElementById("'" + link_id + "'");
            window.item_code = document.getElementById('code' + link_id);
            window.item_name = document.getElementById('name' + link_id);
            open('{{route('item.browser', '')}}' + '/' + link_id + '/' + base_id + '/' + project_id + '/' + role_id + '/' + item_id + '/1/1', 'browse', 'width=800, height=800');
        };
    </script>

    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role])
    <h3 class="display-5 text-center">
        @if (!$update)
            {{trans('main.new_record')}}
        @else
            {{trans('main.edit_record')}}
        @endif
        <span class="text-label">-</span> <span class="text-title">{{$base->info()}}</span>
    </h3>
    <br>
    {{--    https://qastack.ru/programming/1191113/how-to-ensure-a-select-form-field-is-submitted-when-it-is-disabled--}}
    <form
        action="{{$update ? route('item.ext_update', ['item'=>$item, 'role'=>$role]):route('item.ext_store', ['base' => $base, 'project' => $project, 'role'=>$role, 'heading' => $heading])}}"
        method="POST"
        enctype=multipart/form-data
        @if($par_link)
        onsubmit=on_submit()
        {{--        @else--}}
        {{--        onsubmit="playSound('sound');"--}}
        @endif

        name="form">
        @csrf

        @if ($update)
            @method('PUT')
        @endif
        {{--        <input type="hidden" name="base_id" value="{{$base->id}}">--}}
        @if ($update)
            <div class="form-group row">
                <div class="col-sm-3 text-right">
                    <label>Id</label>
                </div>
                <div class="col-sm-9">
                    <label>{{$item->id}}</label>
                </div>
            </div>
        @endif
        @if($base_right['is_edit_base_enable'] == true)
            {{--        код--}}
            @if($base->is_code_needed == true)
                <div class="form-group row">
                    <div class="col-sm-3 text-right">
                        <label for="code" class="col-form-label">{{trans('main.code')}}
                            <span
                                class="text-danger">*</span></label>
                    </div>
                    <div class="col-sm-2">
                        <input type={{$base->is_code_number == true?"number":"text"}}
                            name="code"
                               id="code" ;
                               class="form-control @error('code') is-invalid @enderror"
                               placeholder=""
                               value="{{old('code') ?? ($item->code ?? ($base->is_code_number == true?($update ?"0":$code_new):""))}}"
                               {{$base->is_code_number == true?" step = 0":""}}
                               @if($base->is_code_number == true  && $base->is_limit_sign_code == true)
                               min="0" max="{{$base->number_format()}}"
                            @endif
                        >
                        @error('code')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-sm-7">
                    </div>
                </div>
            @else
                {{--                Похожая строка ниже--}}
                <input type="hidden" name="code" value="{{$update ? $item->code: $code_uniqid}}">
            @endif
            {{--        если тип корректировки поля - число--}}
            @if($base->type_is_number())
                <div class="form-group row">
                    <div class="col-sm-3 text-right">
                        <label for="name_lang_0" class="col-form-label">{{$base->name()}}
                            <span
                                class="text-danger">*</span></label>
                    </div>
                    <div class="col-sm-2">
                        <input type="number"
                               name="name_lang_0"
                               id="name_lang_0" ;
                               class="form-control @error('name_lang_0') is-invalid @enderror"
                               placeholder=""
                               {{--                               value="{{old('name_lang_0') ?? (GlobalController::restore_number_from_item($base,$item['name_lang_0']) ?? '') }}"--}}
                               {{--                               value="{{old('name_lang_0') ?? ($item['name_lang_0'] ?? '') }}"--}}
                               value="{{old('name_lang_0') ?? ($update?GlobalController::restore_number_from_item($base,$item['name_lang_0']):'0')}}"
                               step="{{$base->digits_num_format()}}">
                        @error('name_lang_0')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-sm-7">
                    </div>
                </div>
                {{--                            если тип корректировки поля - дата--}}
            @elseif($base->type_is_date())
                <div class="form-group row">
                    <div class="col-sm-3 text-right">
                        <label for="name_lang_0" class="col-form-label">{{$base->name()}}
                            <span
                                class="text-danger">*</span></label>
                    </div>
                    <div class="col-sm-2">
                        <input type="date"
                               name="name_lang_0"
                               id="name_lang_0" ;
                               class="form-control @error('name_lang_0') is-invalid @enderror"
                               placeholder=""
                               value="{{old('name_lang_0') ?? ($item['name_lang_0'] ?? date('Y-m-d')) }}">
                        @error('name_lang_0')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-sm-7">
                    </div>
                </div>
                {{--                            если тип корректировки поля - логический--}}
            @elseif($base->type_is_boolean())
                <div class="form-group row">
                    <div class="col-sm-3 text-right">
                        <label class="form-label" for="name_lang_0">{{$base->name()}}</label>
                    </div>
                    <div class="col-sm-7">
                        <input class="form-check-input @error('name_lang_0') is-invalid @enderror"
                               type="checkbox"
                               name="name_lang_0"
                               id="name_lang_0"
                               placeholder=""
                               @if ((old('name_lang_0') ?? ($item['name_lang_0'] ?? false)) ==  true)
                               checked
                            @endif
                        >
                        @error('name_lang_0')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-sm-2">
                    </div>
                </div>
                {{--                            если тип корректировки поля - текст--}}
            @elseif($base->type_is_text())
                <div class="form-group row">
                    @foreach (config('app.locales') as $key=>$value)
                        @if(($base->is_one_value_lst_str_txt == true && $key == 0) || ($base->is_one_value_lst_str_txt == false))
                            <div class="col-sm-3 text-right">
                                <label for="name_lang_{{$key}}" class="col-form-label">{{trans('main.name')}}
                                    @if($base->is_one_value_lst_str_txt == false)
                                        ({{trans('main.' . $value)}})
                                    @endif<span
                                        class="text-danger">*</span></label>
                            </div>
                            <div class="col-sm-7">
                                <textarea
                                    name="name_lang_{{$key}}"
                                    id="name_lang_{{$key}}"
                                    rows="5"
                                    class="form-control @error('name_lang_' . $key) is-invalid @enderror"
                                    placeholder=""
                                    maxlength="10000">
                                       {{ old('name_lang_' . $key) ?? ($item->text['name_lang_' . $key] ?? '') }}"
                                </textarea>
                                {{--                            <div class="invalid-feedback">--}}
                                {{--                                Не заполнена строка!--}}
                                {{--                            </div>--}}
                                @error('name_lang_' . $key)
                                <div class="text-danger">
                                    {{$message}}
                                </div>
                                @enderror
                                {{--                            <div class="text-danger">--}}
                                {{--                                session('errors') передается командой в контроллере "return--}}
                                {{--                                redirect()->back()->withInput()->withErrors(...)"--}}
                                {{--                                {{session('errors')!=null ? session('errors')->first('"name_lang_' . $key): ''}}--}}
                                {{--                            </div>--}}
                            </div>
                            <div class="col-sm-2">
                            </div>
                        @endif
                    @endforeach
                </div>
            @elseif($base->type_is_image())
                @include('edit.img_base',['update'=>$update, 'base'=>$base,'item'=>$item ?? null, 'title'=>$base->name(), 'name'=>"name_lang_0",'id'=>"name_lang_0", 'size'=>"small"])
                {{--                            если тип корректировки поля - документ--}}
            @elseif($base->type_is_document())
                @include('edit.doc_base',['update'=>$update, 'base'=>$base,'item'=>$item ?? null, 'title'=>$base->name(), 'name'=>"name_lang_0",'id'=>"name_lang_0"])
                {{--                            если тип корректировки поля - строка или список--}}
            @else
                @if($base->is_calcname_lst == false)
                    <div class="form-group row">
                        @foreach (config('app.locales') as $key=>$value)
                            @if(($base->is_one_value_lst_str_txt == true && $key == 0) || ($base->is_one_value_lst_str_txt == false))
                                <div class="col-sm-3 text-right">
                                    <label for="name_lang_{{$key}}" class="col-form-label">{{trans('main.name')}}
                                        @if($base->is_one_value_lst_str_txt == false)
                                            ({{trans('main.' . $value)}})
                                        @endif<span
                                            class="text-danger">*</span></label>
                                </div>
                                <div class="col-sm-7">
                                    <input type="text"
                                           name="name_lang_{{$key}}"
                                           id="name_lang_{{$key}}"
                                           class="form-control @error('name_lang_' . $key) is-invalid @enderror"
                                           placeholder=""
                                           value="{{ old('name_lang_' . $key) ?? ($item['name_lang_' . $key] ?? '') }}"
                                           maxlength="255">
                                    {{--                            <div class="invalid-feedback">--}}
                                    {{--                                Не заполнена строка!--}}
                                    {{--                            </div>--}}
                                    @error('name_lang_' . $key)
                                    <div class="text-danger">
                                        {{$message}}
                                    </div>
                                    @enderror
                                    {{--                            <div class="text-danger">--}}
                                    {{--                                session('errors') передается командой в контроллере "return--}}
                                    {{--                                redirect()->back()->withInput()->withErrors(...)"--}}
                                    {{--                                {{session('errors')!=null ? session('errors')->first('"name_lang_' . $key): ''}}--}}
                                    {{--                            </div>--}}
                                </div>
                                <div class="col-sm-2">
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            @endif
        @else
            {{--                Похожая строка выше--}}
            <input type="hidden" name="code" value="{{$update ? $item->code: $code_uniqid}}">
        @endif

        @foreach($array_calc as $key=>$value)
            <?php
            $link = Link::find($key);
            $base_link_right = GlobalController::base_link_right($link, $role);
            ?>
            @if($base_link_right['is_edit_link_enable'] == false)
                @continue
            @endif
            <?php
            $result_parent_label = $link->parent_label();
            $base_items = $link->parent_base;
            //                Загружаются данные для списков выбора
            //$result = ItemController::get_items_ext_edit_for_link($link, $project, $role);
            //                $result = ItemController::get_items_for_link($link, $project, $role);
            //                $items = $result['result_parent_base_items'];
            $items_default = true;
            //  Проверка на фильтруемые поля
            $link_selection_table = ItemController::get_link_refer_main($base_items, $link);
            // Проверка на ввод в виде справочника
            if ($link->parent_base->is_code_needed == true && $link->parent_is_enter_refer == true) {
                if ($link_selection_table) {
                    $items_default = false;
                }
            }
            $items = [];
            if ($items_default = true) {
                $result = ItemController::get_items_main($base_items, $project, $role, $link);
                $items = $result['items_no_get']->get();
            }
            $code_find = null;
            if ($value != null) {
                $item_find = Item::findOrFail($value);
                $code_find = $item_find->code;
            }
            ?>
            {{--            @if($link_start_child)--}}
            {{--                @if(($link->parent_is_base_link == true) || ($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true))--}}
            {{--                    <script>--}}
            {{--                        var child_base_start_id{{$link_start_child->id}} = document.getElementById('{{$link->id}}');--}}
            {{--                    </script>--}}
            {{--                @else--}}
            {{--                    <script>--}}
            {{--                        var child_base_start_id{{$link_start_child->id}} = document.getElementById('link{{$link->id}}');--}}
            {{--                    </script>--}}
            {{--                @endif--}}
            {{--            @endif--}}
            {{--                $link_start_child - {{$$link_start_child->id}}--}}
            {{-- Проверка Показывать Связь с признаком "Ссылка на основу"--}}
            {{-- Ниже по тексту тоже используется "parent_is_base_link"--}}
            @if($link->parent_is_base_link == true)
                <input type="hidden" name="{{$key}}" id="link{{$key}}"
                       @if ($update)
                       value="{{$item->id}}"
                       @else
                       value="0"
                    @endif
                >
                {{--                            проверка для вычисляемых полей--}}
            @elseif($link->parent_is_parent_related == true)
                <div class="form-group row"
                     {{--                     проверка скрывать поле или нет--}}
                     @if($link->parent_is_hidden_field == true)
                     hidden
                    @endif
                >
                    <div class="col-sm-3 text-right">
                        <label for="calc{{$key}}" class="form-label">
                            {{$result_parent_label}}
                        </label>
                    </div>
                    <div class="col-sm-7">
                        @if($link->parent_base->type_is_image())
                            <span class=""
                                  name="calc{{$key}}"
                                  id="link{{$key}}"></span>
                            {{--                            <a href="{{Storage::url($item_find->filename())}}">--}}
                            {{--                                <img src="{{Storage::url($item_find->filename())}}" height="50"--}}
                            {{--                                     alt="" title="{{$item_find->filename()}}">--}}
                            {{--                            </a>--}}
                            {{--                        @elseif($link->parent_base->type_is_document())--}}
                            {{--                            <a href="{{Storage::url($item_find->filename())}}" target="_blank">--}}
                            {{--                                Открыть документ--}}
                            {{--                            </a>--}}
                        @else
                            <span class="form-label text-related"
                                  name="calc{{$key}}"
                                  id="link{{$key}}"></span>
                        @endif
                    </div>
                    <div class="col-sm-2">
                    </div>
                </div>
                {{--                            проверка для вывода полей вычисляемой таблицы--}}
            @elseif($link->parent_is_output_calculated_table_field == true)
                <div class="form-group row"
                >
                    <div class="col-sm-3 text-right">
                        <label for="calc{{$key}}" class="form-label">
                            {{$result['result_parent_label']}}
                        </label>
                    </div>
                    <div class="col-sm-7">
                                    <span class="form-label text-related"
                                          name="calc{{$key}}"
                                          id="link{{$key}}"></span>
                    </div>
                    <div class="col-sm-2">
                    </div>
                </div>
            @else
                @if($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true)
                    <div class="form-group row">
                        <div class="col-sm-3 text-right">
                            <label for="{{$key}}" class="col-form-label">{{$result_parent_label}}
                                ({{mb_strtolower(trans('main.code'))}})
                                <span
                                    class="text-danger">*</span></label>
                        </div>

                        <div class="col-sm-2">
                            <input name="{{$key}}" id="{{$key}}" type="hidden" value="{{old($key) ?? $value ?? "0"}}">
                            <input type={{$link->parent_base->is_code_number == true?"number":"text"}}
                                name="code{{$key}}"
                                   id="code{{$key}}"
                                   class="form-control @error($key) is-invalid @enderror"
                                   placeholder=""
                                   {{--                                       value="{{old('code{{$key}}') ?? ($item->code ?? ($base->is_code_number == true?($update ?"0":$code_new):""))}}"--}}
                                   value="{{old('code'.$key) ?? $code_find??''}}"

                                   {{--                                   value="{{(old('code'.$key)) ?? (($value != null) ? Item::find($value)->code: '0')}}"--}}
                                   {{--                                       {{$link->parent_base->is_code_number == true?" step = 0":""}}--}}
                                   {{--                                       @if($link->parent_base->is_code_number == true  && $link->parent_base->is_limit_sign_code == true)--}}
                                   {{--                                       min="0" max="{{$link->parent_base->number_format()}}"--}}
                                   {{--                                       @endif--}}
                                   @if($base_link_right['is_edit_link_read'] == true)
                                   disabled
                                   @else
                                   @if($par_link)
                                   @if ($key == $par_link->id)
                                   disabled
                                @endif
                                @endif
                                @endif
                            >
                            @error("code" . $key)
                            {{--                            <div class="invalid-feedback">--}}
                            <div class="text-danger">
                                {{$message}}
                            </div>
                            @enderror
                        </div>
                        {{--                            <div class="text-danger">--}}
                        {{--                                //session('errors') передается командой в контроллере "return--}}
                        {{--                                //redirect()->back()->withInput()->withErrors(...)"--}}
                        {{--                                {{session('errors')!=null ? session('errors')->first($key): ''}}--}}
                        {{--                            </div>--}}
                        <div class="col-sm-1">
                            {{--                                    Не удалять--}}
                            {{--                            <input type="button" value="..." title="{{trans('main.select_from_refer')}}"--}}
                            {{--                                   onclick="browse('{{$link->parent_base_id}}','{{$project->id}}','{{$role->id}}','{{$key}}')"--}}
                            {{--                                   @if($base_link_right['is_edit_link_read'] == true)--}}
                            {{--                                   disabled--}}
                            {{--                                @endif--}}
                            {{--                            >--}}
                            {{--                                    onclick="browse('{{$link->id}}', '{{$link->parent_base_id}}', '{{$project->id}}', '{{$role->id}}', '{{$key}}')"--}}
                            {{--                            onclick="browse('{{$link->id}}', '{{$link->parent_base_id}}', '{{$project->id}}', '{{$role->id}}',child_base_start_id{{$link->id}}.options[child_base_start_id{{$link->id}}.selectedIndex].value)"--}}
                            <button type="button" title="{{trans('main.select_from_refer')}}" class="text-label"
                                    id="buttonbrow{{$link->id}}"
                                    name="buttonbrow{{$link->id}}"
                                    {{--                                    onclick="browse('{{$link->id}}', '{{$project->id}}', '{{$role->id}}',child_base_start_id{{$link->id}})"--}}
                                    {{--                                    @if(($link_start_child->parent_is_base_link == true) || ($link_start_child->parent_base->is_code_needed==true && $link_start_child->parent_is_enter_refer==true))--}}
                                    {{--                                        child_base_start_id{{$link->id}}--}}
                                    {{--                                    @else--}}
                                    {{--                                        child_base_start_id{{$link->id}}.options[child_base_start_id{{$link->id}}.selectedIndex].value--}}
                                    {{--                                    @endif--}}
                                    {{--                                          )"--}}

                                    @if($base_link_right['is_edit_link_read'] == true)
                                    disabled
                                @endif
                            >
                                {{--                                <i class="fas fa-mouse-pointer d-inline"></i>--}}
                                ...
                            </button>
                        </div>
                        <div class="col-sm-6">
                                <span class="form-label text-related"
                                      name="name{{$key}}"
                                      id="name{{$key}}"
                                      @if($link->parent_is_hidden_field == true)
                                      hidden
                                        @endif
                                    ></span>
                        </div>
                        {{--                                <span class="form-label text-success"--}}
                        {{--                                      name="calc{{$key}}"--}}
                        {{--                                      id="calc{{$key}}" >1111111111111111111111111111</span>--}}
                        {{--                        </div>--}}

                    </div>

                    {{--                                если тип корректировки поля - число--}}
                @elseif($link->parent_base->type_is_number())
                    <div class="form-group row">
                        <div class="col-sm-3 text-right">
                            <label for="{{$key}}" class="col-form-label">{{$result_parent_label}}
                                <span
                                    class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-2">
                            <input type="number"
                                   name="{{$key}}"
                                   id="link{{$key}}"
                                   class="form-control @error($key) is-invalid @enderror"
                                   placeholder=""
                                   value="{{(old($key)) ?? (($value != null) ? GlobalController::restore_number_from_item($link->parent_base, Item::find($value)->name()) :
                                    (($link->parent_num_bool_default_value!="")? $link->parent_num_bool_default_value:'0')
                                    )}}"
                                   step="{{$link->parent_base->digits_num_format()}}"

                                   @if($base_link_right['is_edit_link_read'] == true)
                                   disabled
                                   @else
                                   @if($par_link || $link->parent_is_nc_viewonly==true)
                                   @if($par_link)
                                   @if ($key == $par_link->id)
                                   disabled
                                   @endif
                                   @else
                                   {{--                                   тут использовать readonly (при disabled (здесь) - это поле не обновляется)--}}
                                   {{--                                   также при disabled работают строки (ниже):--}}
                                   {{--                                   parent_base_id_work = document.getElementById('link{{$key}}').disabled = true;--}}
                                   {{--                                   parent_base_id_work = document.getElementById('link{{$key}}').disabled = false;--}}
                                   readonly
                                @endif
                                @endif
                                @endif
                            >
                            @error($key)
                            <div class="invalid-feedback">
                                {{--                            <div class="text-danger">--}}
                                {{$message}}
                            </div>
                            @enderror
                            {{--                            <div class="text-danger">--}}
                            {{--                                //session('errors') передается командой в контроллере "return--}}
                            {{--                                //redirect()->back()->withInput()->withErrors(...)"--}}
                            {{--                                {{session('errors')!=null ? session('errors')->first($key): ''}}--}}
                            {{--                            </div>--}}
                        </div>
                        {{-- Похожая проверка внизу--}}
                        {{-- @if($base_link_right['is_edit_link_read'] == false)--}}
                        {{-- @if($link->parent_is_numcalc == true)--}}
                        @if($base_link_right['is_edit_link_read'] == false)
                            {{--                            @if($link->parent_is_numcalc == true)--}}
                            @if($link->parent_is_numcalc==true)
                                <div class="col-sm-1">
                                    {{--                                    Не удалять--}}
                                    {{--                                    <input type="button" value="..." title="{{trans('main.calculate')}}"--}}
                                    {{--                                           name="button_nc{{$key}}"--}}
                                    {{--                                           id="button_nc{{$key}}"--}}
                                    {{--                                    >--}}
                                    <button type="button" title="{{trans('main.calculate')}}"
                                            name="button_nc{{$key}}"
                                            id="button_nc{{$key}}"
                                            class="text-label">
                                        <i class="fas fa-calculator d-inline"></i>
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                <span class="form-label text-danger"
                                      name="name{{$key}}"
                                      id="name{{$key}}"></span>
                                </div>
                            @else
                                <div class="col-sm-7">
                                </div>
                            @endif
                        @endif
                    </div>

                    {{--                                если тип корректировки поля - дата--}}
                @elseif($link->parent_base->type_is_date())
                    <div class="form-group row">
                        <div class="col-sm-3 text-right">
                            <label for="{{$key}}" class="col-form-label">{{$result_parent_label}}
                                <span
                                    class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-2">
                            <input type="date"
                                   name="{{$key}}"
                                   id="link{{$key}}"
                                   class="form-control @error($key) is-invalid @enderror"
                                   placeholder=""
                                   value="{{(old($key)) ?? (($value != null) ? Item::find($value)->name_lang_0 : date('Y-m-d'))}}"
                                   @if($base_link_right['is_edit_link_read'] == true)
                                   disabled
                                   @else
                                   @if($par_link)
                                   @if ($key == $par_link->id)
                                   disabled
                                @endif
                                @endif
                                @endif
                            >
                            @error($key)
                            <div class="text-danger">
                                {{$message}}
                            </div>
                            @enderror
                            {{--                        <div class="text-danger">--}}
                            {{--                            session('errors') передается командой в контроллере "return--}}
                            {{--                            redirect()->back()->withInput()->withErrors(...)"--}}
                            {{--                            {{session('errors')!=null ? session('errors')->first($key): ''}}--}}
                            {{--                        </div>--}}
                        </div>
                        <div class="col-sm-7">
                        </div>
                    </div>

                    {{--                                если тип корректировки поля - логический--}}
                @elseif($link->parent_base->type_is_boolean())
                    {{--                        https://mdbootstrap.com/docs/jquery/forms/basic/--}}
                    {{--(($link->parent_num_bool_default_value!="")? $link->parent_num_bool_default_value:'0'))--}}
                    <div class="form-group row">
                        <div class="col-sm-3 text-right">
                            <label class="form-label" for="{{$key}}">{{$result_parent_label}}</label>
                        </div>
                        <div class="col-sm-7">
                            <input class="@error($key) is-invalid @enderror"
                                   type="checkbox"
                                   name="{{$key}}"
                                   id="link{{$key}}"
                                   placeholder=""
                            @if ((boolean)(old($key) ?? (($value != null) ? Item::find($value)->name_lang_0 :
                                (($link->parent_num_bool_default_value!="")? $link->parent_num_bool_default_value:'0'))
                                    )) == true)
                            checked
                            @endif
                            @if($base_link_right['is_edit_link_read'] == true)
                                disabled
                            @else
                                @if($par_link)
                                    @if ($key == $par_link->id)
                                        disabled
                                    @endif
                                @endif
                            @endif
                            >
                            @error($key)
                            <div class="invalid-feedback">
                                {{$message}}
                            </div>
                            @enderror
                        </div>
                        <div class="col-sm-2">
                        </div>
                    </div>

                    {{--                                если тип корректировки поля - строка--}}
                @elseif($link->parent_base->type_is_string())
                    <fieldset id="link{{$key}}"
                              @if($base_link_right['is_edit_link_read'] == true)
                              disabled
                              @else
                              @if($par_link)
                              @if ($key == $par_link->id)
                              disabled
                        @endif
                        @endif
                        @endif
                    >
                        <div class="form-group row">
                            @foreach (config('app.locales') as $lang_key=>$lang_value)
                                <?php
                                // для первого (нулевого) языка $input_name = $key
                                // для последующих языков $input_name = $key . '_' . $lang_key;
                                // это же правило используется в ItemController.php
                                // $input_name = $key . ($lang_key == 0) ? '' : '_' . $lang_key;  // так не работает, дает '' в результате
                                $input_name = ($lang_key == 0) ? $key : $key . '_' . $lang_key;  // такой вариант работает
                                ?>
                                @if(($link->parent_base->is_one_value_lst_str_txt == true && $lang_key == 0)
                                    || ($link->parent_base->is_one_value_lst_str_txt == false))
                                    <div class="col-sm-3 text-right">
                                        <label for="{{$input_name}}"
                                               class="col-form-label">{{$result_parent_label}}
                                            @if($link->parent_base->is_one_value_lst_str_txt == false)
                                                ({{trans('main.' . $lang_value)}})
                                            @endif
                                            <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text"
                                               name="{{$input_name}}"
                                               id="link{{$input_name}}"
                                               class="form-control @error($input_name) is-invalid @enderror"
                                               placeholder=""
                                               value="{{(old($input_name)) ?? (($value != null) ? Item::find($value)['name_lang_'.$lang_key] : '')}}"
                                               maxlength="255">
                                        @error($input_name)
                                        <div class="invalid-feedback">
                                            {{--                                    <div class="text-danger">--}}
                                            {{$message}}
                                        </div>
                                        @enderror
                                        {{--                                                            <div class="text-danger">--}}
                                        {{--                                                                 session('errors') передается командой в контроллере "return redirect()->back()->withInput()->withErrors(...)"--}}
                                        {{--                                                                {{session('errors')!=null ? session('errors')->first($input_name): ''}}--}}
                                        {{--                                                            </div>--}}

                                    </div>
                                    <div class="col-sm-2">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </fieldset>

                    {{--                                если тип корректировки поля - текст--}}
                @elseif($link->parent_base->type_is_text())
                    <fieldset id="link{{$key}}"
                              @if($base_link_right['is_edit_link_read'] == true)
                              disabled
                              @else
                              @if($par_link)
                              @if ($key == $par_link->id)
                              disabled
                        @endif
                        @endif
                        @endif
                    >
                        <div class="form-group row">
                            @foreach (config('app.locales') as $lang_key=>$lang_value)
                                <?php
                                // для первого (нулевого) языка $input_name = $key
                                // для последующих языков $input_name = $key . '_' . $lang_key;
                                // это же правило используется в ItemController.php
                                // $input_name = $key . ($lang_key == 0) ? '' : '_' . $lang_key;  // так не работает, дает '' в результате
                                $input_name = ($lang_key == 0) ? $key : $key . '_' . $lang_key;  // такой вариант работает
                                ?>
                                @if(($link->parent_base->is_one_value_lst_str_txt == true && $lang_key == 0)
                                    || ($link->parent_base->is_one_value_lst_str_txt == false))
                                    <div class="col-sm-3 text-right">
                                        <label for="{{$input_name}}"
                                               class="col-form-label">{{$result_parent_label}}
                                            @if($link->parent_base->is_one_value_lst_str_txt == false)
                                                ({{trans('main.' . $lang_value)}})
                                            @endif
                                            <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-7">
                                            <textarea type="text"
                                                      name="{{$input_name}}"
                                                      id="link{{$input_name}}"
                                                      rows="5"
                                                      class="form-control @error($input_name) is-invalid @enderror"
                                                      placeholder=""
                                                      maxlength="10000">
                                                   {{(old($input_name)) ?? (($value != null) ? Item::find($value)->text['name_lang_'.$lang_key] : '')}}
                                            </textarea>
                                        @error($input_name)
                                        <div class="invalid-feedback">
                                            {{--                                    <div class="text-danger">--}}
                                            {{$message}}
                                        </div>
                                        @enderror
                                        {{--                                                            <div class="text-danger">--}}
                                        {{--                                                                 session('errors') передается командой в контроллере "return redirect()->back()->withInput()->withErrors(...)"--}}
                                        {{--                                                                {{session('errors')!=null ? session('errors')->first($input_name): ''}}--}}
                                        {{--                                                            </div>--}}

                                    </div>
                                    <div class="col-sm-2">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </fieldset>

                    {{--                            если тип корректировки поля - изображение--}}
                @elseif($link->parent_base->type_is_image())
                    {{--                        @include('edit.img_link',['update'=>$update, 'base'=>$link->parent_base,'result'=>$result,'value'=>$value, 'name'=>$key,'id'=>"link".$key, 'size'=>"small"])--}}
                    @include('edit.img_link',['update'=>$update, 'base'=>$link->parent_base,'value'=>$value, 'title'=>$result_parent_label, 'name'=>$key,'id'=>"link".$key, 'size'=>"small"])

                    {{--                            если тип корректировки поля - документ--}}
                @elseif($link->parent_base->type_is_document())
                    {{--                        @include('edit.doc_link',['update'=>$update, 'base'=>$link->parent_base,'result'=>$result,'value'=>$value, 'name'=>$key,'id'=>"link".$key])--}}
                    @include('edit.doc_link',['update'=>$update, 'base'=>$link->parent_base,'value'=>$value, 'title'=>$result_parent_label, 'name'=>$key,'id'=>"link".$key])

                    {{--                         Такая же проверка ItemController::get_items_ext_edit_for_link(),--}}
                    {{--                         в ext_edit.php--}}
                @elseif($link->parent_base->type_is_list())
                    <div class="form-group row">
                        <div class="col-sm-3 text-right">
                            <label for="{{$key}}" class="col-form-label">{{$result_parent_label}}
                                <span class="text-danger">*{{$value !=null ? "" : "~"}}</span></label>
                        </div>
                        <div class="col-sm-7">
                            <select class="form-control"
                                    name="{{$key}}"
                                    id="link{{$key}}"
                                    class="form-control @error($key) is-invalid @enderror"
                                    @if($base_link_right['is_edit_link_read'] == true)
                                    disabled
                                    @else
                                    @if($par_link)
                                    @if ($key == $par_link->id)
                                    disabled
                                @endif
                                @endif
                                @endif
                            >
                                @if ((count($items) == 0)))
                                <option value='0'>{{trans('main.no_information_on')}}
                                    "{{$result_parent_label}}"!
                                </option>
                                @else
                                    @if(!$link->parent_base->is_required_lst_num_str_txt_img_doc)
                                        <option value="0">{{GlobalController::option_empty()}}</option>
                                    @endif
                                    @foreach ($items as $item_work)
                                        <option value="{{$item_work->id}}"
                                                @if (((old($key)) ?? (($value != null) ? $value : 0)) == $item_work->id)
                                                selected
                                            @endif
                                        >{{$item_work->name()}}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error($key)
                            <div class="text-danger">
                                {{$message}}
                            </div>
                            @enderror
                            {{--                                                                                        <div class="text-danger">--}}
                            {{--                                                                                             session('errors') передается командой в контроллере "return redirect()->back()->withInput()->withErrors(...)"--}}
                            {{--                                                                                            {{session('errors')!=null ? session('errors')->first($key): ''}}--}}
                            {{--                                                                                        </div>--}}
                        </div>
                        <div class="col-sm-2">
                        </div>
                    </div>
                @endif
            @endif
        @endforeach

        <br>
        <div class="row text-center">
            <div class="col-sm-5 text-right">
                <button type="submit" class="btn btn-dreamer"
                        @if (!$update)
                        title="{{trans('main.add')}}"><i class="fas fa-save d-inline"></i> {{trans('main.add')}}
                    @else
                        title="{{trans('main.save')}}"><i class="fas fa-save d-inline"></i> {{trans('main.save')}}
                    @endif
                </button>
            </div>
            <div class="col-sm-2">
            </div>
            <div class="col-sm-5 text-left">
                <button type="button" class="btn btn-dreamer" title="{{trans('main.cancel')}}"
                    @include('layouts.item.base_index.previous_url')
                >
                    <i class="fas fa-arrow-left d-inline"></i>
                    {{trans('main.cancel')}}
                </button>
            </div>
        </div>
        {{--    <audio id="sound"><source src="https://ozarnik.ru/uploads/files/2019-02/1549784984_dj-ozarnik-primite-zakaz.mp3" type="audio/mp3"></audio>--}}
    </form>
    {{--    <?php--}}
    {{--    $array_start = $array_parent_related['array_start'];--}}
    {{--    $array_result = $array_parent_related['array_result'];--}}
    {{--    ?>--}}
    {{--    <script>--}}
    {{--            @foreach($array_start as $value)--}}
    {{--        var parent_related_start_{{$value}} = document.getElementById('link{{$value}}');--}}
    {{--            @endforeach--}}
    {{--            @foreach($array_result as $value)--}}
    {{--        var parent_related_result_{{$value['link_id']}} = document.getElementById('link{{$value['link_id']}}');--}}
    {{--        @endforeach--}}
    {{--    </script>--}}
    <?php
    $functions = array();
    $functs_numcalc = array();
    // В этом массиве хранятся функции, которые выводят наименования вычисляемых полей
    // ($link->parent_is_parent_related == true)
    // в зависимости от поля, где вводится код какого-то справочника
    $functs_parent_refer = array();
    ?>
    {{--<script>--}}
    {{--    window.onload = function () {--}}
    {{--        // массив функций нужен, что при window.onload запустить обработчики всех полей--}}
    {{--        alert('onload');--}}
    {{--                        @foreach($functions as $value)--}}
    {{--                            {{$value}}(true);--}}
    {{--                        @endforeach--}}

    {{--            on_parent_refer();--}}

    {{--            // Не нужно вызывать функцию on_calc(),--}}
    {{--            // это связано с разрешенной корректировкой вычисляемых полей ($link->parent_is_nc_viewonly)--}}
    {{--            // on_numcalc();--}}
    {{--            @foreach($array_disabled as $key=>$value)--}}
    {{--            parent_base_id_work = document.getElementById('link{{$key}}').disabled = true;--}}
    {{--            document.getElementById('link{{$key}}').disabled = true;--}}
    {{--            @endforeach--}}
    {{--        --}}
    {{--    }--}}
    {{--</script>--}}

    @foreach($array_calc as $key=>$value)
        <?php
        $link = Link::find($key);
        $base_link_right = GlobalController::base_link_right($link, $role);
        ?>
        @if($base_link_right['is_edit_link_enable'] == false)
            @continue
        @endif

        <?php
        $prefix = '1_';
        ?>
        {{--        Вводить как справочник--}}
        @if($link->parent_base->is_code_needed == true && $link->parent_is_enter_refer == true)
            <script>
                var code_{{$prefix}}{{$link->id}} = document.getElementById('code{{$link->id}}');

                @if($link->parent_base->is_code_number == true && $link->parent_base->is_limit_sign_code == true && $link->parent_base->is_code_zeros == true  && $link->parent_base->significance_code > 0)
                <?php
                $functions[count($functions)] = "code_change_" . $prefix . $link->id;
                ?>
                function code_change_{{$prefix}}{{$link->id}}(first) {
                    numStr = code_{{$prefix}}{{$link->id}}.value;
                    numDigits = {{$link->parent_base->significance_code}};
                    code_{{$prefix}}{{$link->id}}.value = numDigits >= numStr.length ? Array.apply(null, {length: numDigits - numStr.length + 1}).join("0") + numStr : numStr.substring(0, numDigits);
                    {{-- http://javascript.ru/forum/events/76761-programmno-vyzvat-sobytie-change.html#post503465--}}
                    {{-- вызываем состояние "элемент изменился", в связи с этим запустятся функции - обработчики "change"--}}
                    {{--code_{{$prefix}}{{$link->id}}.dispatchEvent(new Event('input'));--}}
                    {{--alert('code_change_ code_{{$prefix}}{{$link->id}}.value = ' + code_{{$prefix}}{{$link->id}}.value);--}}

                }

                code_{{$prefix}}{{$link->id}}.addEventListener("change", code_change_{{$prefix}}{{$link->id}});

                @endif

            </script>
        @endif

        <?php
        // похожие строки ниже
        $prefix = '';
        $base_items = $link->parent_base;
        $link_enter_refer = null;
        $link_refer_start = null;
        $link_refer_main = null;
        $link_selection_table = null;
        $link_id_selection_calc = null;
        $const_link_id_start = null;
        $const_link_start = null;
        $link_start_child = null;
        $link_result_child = null;
        $link_parent = null;
        $lres = null;
        $set = null;
        $sets_group = null;
        $sets_calcsort = null;
        $link_calculated_table = null;
        //$link = Link::find($key);
        if ($link) {
            // Проверка на ввод в виде справочника
            if ($link->parent_base->is_code_needed == true && $link->parent_is_enter_refer == true) {
                $link_enter_refer = true;
                //          Проверка на фильтруемые поля
                $link_refer_main = ItemController::get_link_refer_main($base, $link);
                $prefix = '2_';
            }

            //            1.0 В списке выбора использовать поле вычисляемой таблицы
            if ($link->parent_is_in_the_selection_list_use_the_calculated_table_field == true) {
                $link_selection_table = true;
                $link_id_selection_calc = LinkController::get_link_id_selection_calc($link);
            }
            // эта проверка не нужна
            //if (!array_key_exists($key, $array_disabled)) {
            //          Проверка на фильтруемые поля
            if ($link->parent_is_child_related == true) {
                $lres = LinkController::get_link_ids_from_calc_link($link);
                $const_link_id_start = $lres['const_link_id_start'];
                $const_link_start = $lres['const_link_start'];
                $link_start_child = Link::find($link->parent_child_related_start_link_id);
                $link_result_child = Link::find($link->parent_child_related_result_link_id);
                $link_selection_table = ItemController::get_link_refer_main($base_items, $link);
                $prefix = '3_';
            }
            //}
            //          Проверка на вычисляемые поля
            if ($link->parent_is_parent_related == true) {
                $lres = LinkController::get_link_ids_from_calc_link($link);
                $const_link_id_start = $lres['const_link_id_start'];
                $const_link_start = $lres['const_link_start'];
                $link_parent = Link::find($link->parent_parent_related_start_link_id);
                $prefix = '4_';
            }
            // Выводить поле вычисляемой таблицы
            if ($link->parent_is_output_calculated_table_field == true) {
                $sets_group = ItemController::get_sets_group($base, $link);
                $link_calculated_table = true;
                $prefix = '5_';
            }
        }
        ?>

        <?php
        $prefix = '6_';
        // Используется ниже
        $prefix_prev = $prefix;
        ?>
        {{--        Проверка на фильтруемые поля--}}
        @if($link_start_child && $link_result_child)
            {{--            @if($link_enter_refer)--}}
            {{--                <script>--}}
            {{--                </script>--}}
            <script>
                    @if(($link_start_child->parent_is_base_link == true) || ($link_start_child->parent_base->is_code_needed==true && $link_start_child->parent_is_enter_refer==true))
                var child_base_id{{$prefix}}{{$link->id}} = document.getElementById('{{$link_start_child->id}}');
                    @else
                var child_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$link_start_child->id}}');
                    @endif
                    {{--var child_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$link_start_child->id}}');--}}

                    @if(($link->parent_is_base_link == true) || ($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true))
                var parent_base_id{{$prefix}}{{$link->id}} = document.getElementById('{{$link->id}}');
                    @else
                var parent_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');
                @endif

                {{--var parent_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');--}}
                <?php
                $functions[count($functions)] = "link_id_changeOption_" . $prefix . $link->id;
                $link_get = null;
                // 1.0 В списке выбора использовать поле вычисляемой таблицы
                if ($link_selection_table) {
                    $link_get = $link->id;
                } // Обычное разделение на фильтруемые поля
                else {
                    $link_get = $link_start_child;
                }
                ?>
                // async - await нужно, https://tproger.ru/translations/understanding-async-await-in-javascript/
                async function link_id_changeOption_{{$prefix}}{{$link->id}}(first) {
                    @if(($link->parent_is_base_link == true) || ($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true))
                    if (parent_base_id{{$prefix}}{{$link->id}}.value == 0) {
                        @else
                        if (parent_base_id{{$prefix}}{{$link->id}}.options[parent_base_id{{$prefix}}{{$link->id}}.selectedIndex].value == 0) {
                            @endif
                                child_base_id{{$prefix}}{{$link->id}}.innerHTML = "<option value='0'>{{trans('main.no_information') . '!'}}</option>";
                        } else {
                            @if(($link_start_child->parent_is_base_link == true) || ($link_start_child->parent_base->is_code_needed==true && $link_start_child->parent_is_enter_refer==true))
                                @else
                                await axios.get('/item/get_items_main_options/'
                                + '{{$link_start_child->parent_base_id}}' + '/' + {{$project->id}} +'/' + {{$role->id}} +'/' + {{$link_get->id}}
                                    @if(($link->parent_is_base_link == true) || ($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true))
                                    +'/' + parent_base_id{{$prefix}}{{$link->id}}.value
                                @else
                                + '/' + parent_base_id{{$prefix}}{{$link->id}}.options[parent_base_id{{$prefix}}{{$link->id}}.selectedIndex].value
                                @endif
                               ).then(function (res) {
                                        child_base_id{{$prefix}}{{$link->id}}.innerHTML = res.data['result_items_name_options'];
                                        for (let i = 0; i < child_base_id{{$prefix}}{{$link->id}}.length; i++) {
                                            if (child_base_id{{$prefix}}{{$link->id}}[i].value ==
                                                {{old($link_start_child->id) ?? (($array_calc[$link_start_child->id] != null) ? $array_calc[$link_start_child->id] : 0)}}) {
                                                // установить selected на true
                                                child_base_id{{$prefix}}{{$link->id}}[i].selected = true;
                                            }
                                        }
                                    }
                                );
                            @endif
                        }
                        {{-- http://javascript.ru/forum/events/76761-programmno-vyzvat-sobytie-change.html#post503465--}}
                        {{-- Вызываем состояние "элемент изменился", в связи с этим запустятся функции - обработчики "change"--}}
                        @if($link_start_child->parent_base->is_code_needed==true && $link_start_child->parent_is_enter_refer==true)
                        document.getElementById('code{{$link_start_child->id}}').dispatchEvent(new Event('change'));
                        @else
                        document.getElementById('link{{$link_start_child->id}}').dispatchEvent(new Event('change'));
                        @endif
                    }
                    {{-- Событие на изменение значения--}}
                    @if($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true)
                    {{--Не нужно--}}
                    {{--document.getElementById('code{{$link->id}}').addEventListener("change", link_id_changeOption_{{$prefix}}{{$link->id}});--}}
                    @else
                    document.getElementById('link{{$link->id}}').addEventListener("change", link_id_changeOption_{{$prefix}}{{$link->id}});
                @endif

            </script>
        @endif

        <?php
        $prefix = '7_';
        ?>
        {{--        Вводить как справочник--}}
        @if($link_enter_refer)
            {{-- Проверка на фильтруемые поля--}}
            @if($link_refer_main)
                <script>
                    var code_{{$prefix}}{{$link->id}} = document.getElementById('code{{$link->id}}');
                    var name_{{$prefix}}{{$link->id}} = document.getElementById('name{{$link->id}}');
                    var key_{{$prefix}}{{$link->id}} = document.getElementById('{{$link->id}}');

                    var child_base_id{{$prefix}}{{$link->id}} = document.getElementById('buttonbrow{{$link->id}}');

                        @if(($link_refer_main->parent_is_base_link == true) || ($link_refer_main->parent_base->is_code_needed==true && $link_refer_main->parent_is_enter_refer==true))
                    var parent_base_id{{$prefix}}{{$link->id}} = document.getElementById('{{$link_refer_main->id}}');
                        @else
                    var parent_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$link_refer_main->id}}');

                    @endif

                    {{-- async - await нужно, https://tproger.ru/translations/understanding-async-await-in-javascript/--}}
                    function link_id_changeOption_{{$prefix}}{{$link->id}}() {
                        {{-- Нужно, используется в browser.blade.php--}}
                            window.item_id = document.getElementById('{{$link->id}}');
                        window.item_code = document.getElementById('code{{$link->id}}');
                        window.item_name = document.getElementById('name{{$link->id}}');

                        @if(($link_refer_main->parent_is_base_link == true) || ($link_refer_main->parent_base->is_code_needed==true && $link_refer_main->parent_is_enter_refer==true))
                        if (parent_base_id{{$prefix}}{{$link->id}}.value == 0) {
                            @else
                            if (parent_base_id{{$prefix}}{{$link->id}}.options[parent_base_id{{$prefix}}{{$link->id}}.selectedIndex].value == 0) {
                                @endif
                                    window.item_id.value = 0;
                                window.item_code.value = "";
                                window.item_name.innerHTML = "";
                                alert("{{trans('main.select_a_field_to_filter') . '!'}}");
                            } else {
                                open('{{route('item.browser', '')}}' + '/' + {{$link->id}} +'/' + {{$project->id}} +'/' + {{$role->id}}
                                        @if(($link_refer_main->parent_is_base_link == true) || ($link_refer_main->parent_base->is_code_needed==true && $link_refer_main->parent_is_enter_refer==true))
                                        +'/' + parent_base_id{{$prefix}}{{$link->id}}.value
                                    @else
                                    + '/' + parent_base_id{{$prefix}}{{$link->id}}.options[parent_base_id{{$prefix}}{{$link->id}}.selectedIndex].value
                                    @endif
                                    + '/code/code', 'browse', 'width=800, height=800');
                            }
                            {{-- Вызываем событие - обновление кода--}}
                            document.getElementById('code{{$link->id}}').dispatchEvent(new Event('change'));
                        }

                        {{-- Событие на кнопку "..."--}}
                        child_base_id{{$prefix}}{{$link->id}}.addEventListener("click", link_id_changeOption_{{$prefix}}{{$link->id}});


                        <?php
                        $functions[count($functions)] = "code_input_" . $prefix . $link->id;
                        //$functs_parent_refer[count($functs_parent_refer)] = "code_input_" . $prefix . $link->id;
                        ?>
                        {{-- async - await нужно, https://tproger.ru/translations/understanding-async-await-in-javascript/--}}
                        async function code_input_{{$prefix}}{{$link->id}}(first) {
                            @if(($link_refer_main->parent_is_base_link == true) || ($link_refer_main->parent_base->is_code_needed==true && $link_refer_main->parent_is_enter_refer==true))
                            if (parent_base_id{{$prefix}}{{$link->id}}.value == 0) {
                                @else
                                if (parent_base_id{{$prefix}}{{$link->id}}.options[parent_base_id{{$prefix}}{{$link->id}}.selectedIndex].value == 0) {
                                    @endif
                                        name_{{$prefix}}{{$link->id}}.innerHTML = "{{trans('main.no_information') . '!'}}";
                                    key_{{$prefix}}{{$link->id}}.value = 0;
                                } else {
                                    await axios.get('/item/get_items_main_code/'
                                        + code_{{$prefix}}{{$link->id}}.value + '/'
                                        + '{{$link->parent_base_id}}' + '/' + {{$project->id}} +'/' + {{$role->id}} +'/' + {{$link->id}}
                                            @if(($link_refer_main->parent_is_base_link == true) || ($link_refer_main->parent_base->is_code_needed==true && $link_refer_main->parent_is_enter_refer==true))
                                            +'/' + parent_base_id{{$prefix}}{{$link->id}}.value
                                        @else
                                        + '/' + parent_base_id{{$prefix}}{{$link->id}}.options[parent_base_id{{$prefix}}{{$link->id}}.selectedIndex].value
                                        @endif
                                    ).then(function (res) {
                                                name_{{$prefix}}{{$link->id}}.innerHTML = res.data['item_name'];
                                                key_{{$prefix}}{{$link->id}}.value = res.data['item_id'];
                                            }
                                        );

                                    {{--Команда "on_parent_refer();" нужна, для вызова функция обновления данных с зависимых таблиц--}}
                                    {{--Функция code_input_{{$prefix}}{{$link->id}}(first) выполняется не сразу--}}
                                    on_parent_refer();

                                    link_id_changeOption_{{$prefix_prev}}{{$link->id}}();

                                    {{-- http://javascript.ru/forum/events/76761-programmno-vyzvat-sobytie-change.html#post503465--}}
                                    {{-- вызываем состояние "элемент изменился", в связи с этим запустятся функции - обработчики "change"--}}
                                }
                            }
                            code_{{$prefix}}{{$link->id}}.addEventListener("change", code_input_{{$prefix}}{{$link->id}});
                    {{--code_{{$prefix}}{{$link->id}}.addEventListener("change", link_id_changeOption_6_{{$link->id}});--}}

                </script>
            @else
                <script>
                    var code_{{$prefix}}{{$link->id}} = document.getElementById('code{{$link->id}}');
                    var name_{{$prefix}}{{$link->id}} = document.getElementById('name{{$link->id}}');
                    var key_{{$prefix}}{{$link->id}} = document.getElementById('{{$link->id}}');

                    var child_base_id{{$prefix}}{{$link->id}} = document.getElementById('buttonbrow{{$link->id}}');

                    {{--async - await нужно, https://tproger.ru/translations/understanding-async-await-in-javascript/--}}
                    function link_id_changeOption_{{$prefix}}{{$link->id}}() {
                        // Нужно, используется в browser.blade.php
                        window.item_id = document.getElementById('{{$link->id}}');
                        window.item_code = document.getElementById('code{{$link->id}}');
                        window.item_name = document.getElementById('name{{$link->id}}');
                        open('{{route('item.browser', '')}}' + '/' + {{$link->id}} +'/' + {{$project->id}} +'/' + {{$role->id}}
                            , 'browse', 'width=800, height=800');
                    }

                    {{--Событие на кнопку "..."--}}
                    child_base_id{{$prefix}}{{$link->id}}.addEventListener("click", link_id_changeOption_{{$prefix}}{{$link->id}});

                    <?php
                    $functions[count($functions)] = "code_input_" . $prefix . $link->id;
                    //$functs_parent_refer[count($functs_parent_refer)] = "code_input_" . $prefix . $link->id;
                    ?>

                    {{--async - await нужно, https://tproger.ru/translations/understanding-async-await-in-javascript/--}}
                    async function code_input_{{$prefix}}{{$link->id}}(first) {
                        await axios.get('/item/item_from_base_code/'
                            + '{{$link->parent_base_id}}'
                            + '/' + '{{$project->id}}'
                            + '/' + code_{{$prefix}}{{$link->id}}.value
                        ).then(function (res) {
                                code_{{$prefix}}{{$link->id}}.innerHTML = res.data['item_code'];
                                name_{{$prefix}}{{$link->id}}.innerHTML = res.data['item_name'];
                                key_{{$prefix}}{{$link->id}}.value = res.data['item_id'];
                            }
                        );
                        {{--Команда "on_parent_refer();" нужна, для вызова функция обновления данных с зависимых таблиц--}}
                        {{--Функция code_input_{{$prefix}}{{$link->id}}(first) выполняется не сразу--}}
                        on_parent_refer();

                        link_id_changeOption_{{$prefix_prev}}{{$link->id}}();

                        {{-- http://javascript.ru/forum/events/76761-programmno-vyzvat-sobytie-change.html#post503465--}}
                        {{-- вызываем состояние "элемент изменился", в связи с этим запустятся функции - обработчики "change"--}}
                        {{--document.getElementById('code{{$link->id}}').dispatchEvent(new Event('change'));--}}
                    }

                    {{--code_{{$prefix}}{{$link->id}}.addEventListener("input", code_input_{{$prefix}}{{$link->id}});--}}

                    code_{{$prefix}}{{$link->id}}.addEventListener("change", code_input_{{$prefix}}{{$link->id}});

                </script>
            @endif
        @endif


        {{--        Проверка на вычисляемые поля--}}
        @if($link_parent)
            <script>
                    @if($const_link_start->parent_base->is_code_needed==true && $const_link_start->parent_is_enter_refer==true)

                var child_base_id{{$prefix}}{{$link->id}} = document.getElementById('{{$const_link_id_start}}');
                var child_code_id{{$prefix}}{{$link->id}} = document.getElementById('code{{$const_link_id_start}}');
                var parent_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');

                <?php
                $functs_parent_refer[count($functs_parent_refer)] = "link_id_change_" . $prefix . $link->id;
                //           $functions[count($functions)] = "link_id_change_" . $prefix . $link->id;
                ?>
                function link_id_change_{{$prefix}}{{$link->id}}(first = false) {
                    //alert('{{$link->id}} - {{$link_parent->id}} - {{$const_link_id_start}} - {{$const_link_start->parent_base->is_code_needed}} - {{$const_link_start->parent_is_enter_refer}}');
                    //alert('child_base_id{{$prefix}}{{$link->id}}.value = ' + child_base_id{{$prefix}}{{$link->id}}.value);
                    if (child_base_id{{$prefix}}{{$link->id}}.value == 0) {
                        parent_base_id{{$prefix}}{{$link->id}}.innerHTML = "{{trans('main.no_information') . '!'}}";
                        //alert('---->'+"{{trans('main.no_information') . '!'}}")
                    } else {
                        axios.get('/item/get_parent_item_from_calc_child_item/'
                            + child_base_id{{$prefix}}{{$link->id}}.value
                            + '/{{$link->id}}'
                            + '/0'
                        ).then(function (res) {
                                parent_base_id{{$prefix}}{{$link->id}}.innerHTML = res.data['result_item_name'];
                                //alert('---->'+res.data['result_item_name'])
                                @if($link->parent_is_nc_parameter == true)
                                on_numcalc();
                                @endif
                                {{--    arr = res.data;--}}
                                {{--for (key in arr) {--}}
                                {{--    // console.log(`${key} = ${arr[key]}`);--}}
                                {{--    alert('link_id = {{$link->id}} key = ' + key + ' value = ' + arr[key]);--}}
                                {{--}--}}
                            }
                        );
                        // При просмотре фото может неправильно работать при просмотре фото по связанному полю - проэтому закомментарено
                        // вызываем состояние "элемент изменился", в связи с этим запустятся функции - обработчики "change"
                        // child_code_id{{$prefix}}{{$link->id}}.dispatchEvent(new Event('input'));
                    }
                }

                // Эта команда не нужна
                //child_code_id{{$prefix}}{{$link->id}}.addEventListener("change", link_id_change_{{$prefix}}{{$link->id}});

                    @elseif($const_link_start->parent_base->type_is_list())
                var child_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$const_link_id_start}}');
                var parent_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');

                <?php
                $functions[count($functions)] = "link_id_changeOption_" . $prefix . $link->id;
                ?>
                function link_id_changeOption_{{$prefix}}{{$link->id}}(first = false) {
//                    alert(child_base_id{{$prefix}}{{$link->id}}.options[child_base_id{{$prefix}}{{$link->id}}.selectedIndex].value);
                    if (child_base_id{{$prefix}}{{$link->id}}.options[child_base_id{{$prefix}}{{$link->id}}.selectedIndex].value == 0) {
                        parent_base_id{{$prefix}}{{$link->id}}.innerHTML = "{{trans('main.no_information') . '!'}}";
                        @if($link->parent_is_nc_parameter == true)
                        on_numcalc();
                        @endif
                    } else {
                        axios.get('/item/get_parent_item_from_calc_child_item/'
                            + child_base_id{{$prefix}}{{$link->id}}.options[child_base_id{{$prefix}}{{$link->id}}.selectedIndex].value
                            + '/{{$link->id}}'
                            + '/0'
                        ).then(function (res) {
                                parent_base_id{{$prefix}}{{$link->id}}.innerHTML = res.data['result_item_name'];
                                @if($link->parent_is_nc_parameter == true)
                                on_numcalc();
                                @endif

                            }
                        );
                    }
                }

                child_base_id{{$prefix}}{{$link->id}}.addEventListener("change", link_id_changeOption_{{$prefix}}{{$link->id}});

                @endif
            </script>
        @endif

        {{--        Выводится одно поле из вычисляемой таблицы--}}
        @if($link_calculated_table)
            <script>
                    @foreach($sets_group as $to_key => $to_value)
                var code_needed_child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}} = {{$to_value->link_from->parent_base->is_code_needed}};

                    @if($to_value->link_from->parent_base->is_code_needed==true && $to_value->link_from->parent_is_enter_refer==true)
                var child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}} = document.getElementById('{{$to_value->link_from_id}}');
                var code_child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}} = document.getElementById('code{{$to_value->link_from_id}}');
                    @else
                var child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}} = document.getElementById('link{{$to_value->link_from_id}}');
                    @endif
                    @endforeach

                var parent_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');

                <?php
                $functs_parent_refer[count($functs_parent_refer)] = "link_id_changeOption_" . $prefix . $link->id;
                $functions[count($functions)] = "link_id_changeOption_" . $prefix . $link->id;
                ?>
                function link_id_changeOption_{{$prefix}}{{$link->id}}(first = false) {
                    {{--if (child_base_id{{$prefix}}{{$link->id}}.options[child_base_id{{$prefix}}{{$link->id}}.selectedIndex].value == 0) {--}}
                    {{--    parent_base_id{{$prefix}}{{$link->id}}.innerHTML = "{{trans('main.no_information') . '!'}}";--}}
                    {{--} else {--}}
                    axios.get('/item/get_parent_item_from_output_calculated_table?'
                        + 'base_id={{$base->id}}'
                        + '&link_id={{$link->id}}'
                        @foreach($sets_group as $to_key => $to_value)
                        {{-- Если $to_value->link_from->Ссылка на основу = true --}}
                        {{-- Выше по тексту тоже используется "parent_is_base_link"--}}
                        @if(($to_value->link_from->parent_is_base_link == true) || ($to_value->link_from->parent_base->is_code_needed==true && $to_value->link_from->parent_is_enter_refer==true))
                        {{--+ '/' + child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}}.value--}}
                        + '&items_id_group[]=' + child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}}.value
                        @else
                        {{--+ '/' + child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}}.options[child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}}.selectedIndex].value--}}
                        + '&items_id_group[]=' + child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}}.options[child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}}.selectedIndex].value
                        @endif
                        @endforeach
                        ).then(function (res) {
                                parent_base_id{{$prefix}}{{$link->id}}.innerHTML = res.data;
                            }
                        );
                    // }
                }

                @foreach($sets_group as $to_key => $to_value)
                @if($to_value->link_from->parent_base->is_code_needed==true && $to_value->link_from->parent_is_enter_refer==true)
                code_child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}}.addEventListener("change", link_id_changeOption_{{$prefix}}{{$link->id}});
                @else
                child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}}.addEventListener("change", link_id_changeOption_{{$prefix}}{{$link->id}});
                @endif
                @endforeach

            </script>
        @endif

        <?php
        $prefix = '4_';
        ?>
        {{--        Расчитывать значение числового поля--}}
        @if($link->parent_is_nc_parameter==true)
            <script>
                var nc_parameter_{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');
            </script>
        @endif

    @endforeach

    <script>

            @foreach($array_calc as $key=>$value)
            <?php
            $link = Link::find($key);
            $base_link_right = GlobalController::base_link_right($link, $role);
            $prefix = '5_';
            ?>

            {{-- Похожая проверка вверху--}}
            {{-- @if($base_link_right['is_edit_link_read'] == false)--}}
            {{-- @if($link->parent_is_numcalc == true)--}}
            @if($base_link_right['is_edit_link_read'] == false)
            @if($link->parent_is_numcalc == true)
            @if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true)
        var button_nc_{{$prefix}}{{$link->id}} = document.getElementById('button_nc{{$link->id}}');
        var numcalc_{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');
        var name_{{$prefix}}{{$link->id}} = document.getElementById('name{{$link->id}}');

        <?php
        $functs_numcalc[count($functs_numcalc)] = "button_nc_click_" . $prefix . $link->id;
        ?>

        function button_nc_click_{{$prefix}}{{$link->id}}() {
            var x, y, result, error_message;
            x = 0;
            y = 0;
            z = 0;
            error_message = "";
            error_nodata = "Нет данных";
            error_div0 = "Деление на 0";
            {{StepController::steps_javascript_code($link)}}

                numcalc_{{$prefix}}{{$link->id}}.value = x;
            name_{{$prefix}}{{$link->id}}.innerHTML = error_message;
        }

        button_nc_{{$prefix}}{{$link->id}}.addEventListener("click", button_nc_click_{{$prefix}}{{$link->id}});
            {{--    button_nc_{{$prefix}}{{$link->id}}.addEventListener("click", on_numcalc);--}}
            @endif
            @endif
            @endif

            @endforeach
            {{--                @if($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true)--}}
            @if($base->is_code_number == true  && $base->is_limit_sign_code == true
                && $base->is_code_zeros == true  && $base->significance_code > 0)
        var code_el = document.getElementById('code');

        <?php
            $functions[count($functions)] = "code_change";
            ?>
            numBaseDigits = {{$base->significance_code}};

        function code_change(first) {
            numStr = code_el.value;

            code_el.value = numBaseDigits >= numStr.length ? Array.apply(null, {length: numBaseDigits - numStr.length + 1}).join("0") + numStr : numStr.substring(0, numBaseDigits);

        }

        code_el.addEventListener("change", code_change);

            @endif

        var child_base_id_work = 0;
        var parent_base_id_work = 0;

        function on_numcalc() {
            @foreach($functs_numcalc as $value)
                {{$value}}();

            @endforeach
        }

        function on_parent_refer() {
            @foreach($functs_parent_refer as $value)
                {{$value}}();
            @endforeach
        }

        function on_submit() {

            @foreach($array_disabled as $key=>$value)
            {{--parent_base_id_work = document.getElementById('link{{$key}}').disabled = false;--}}
            document.getElementById('link{{$key}}').disabled = false;
            @endforeach

        }

        function round(a, b, c) {
            r = 0;
            p = Math.pow(10, b);
            switch (c) {
                case 0:
                    r = Math.round(a * p) / p;
                    break;
                case -1:
                    r = Math.floor(a * p) / p;
                    break;
                case 1:
                    r = Math.ceil(a * p) / p;
                    break;
            }
            return r;
        }

            @foreach($array_calc as $key=>$value)
            <?php
            $link = Link::find($key);
            $prefix = '6_';
            ?>

            @if($link->parent_is_nc_parameter == true && $link->parent_is_numcalc == false
                    && $link->parent_is_nc_viewonly == false && $link->parent_is_parent_related == false)
            {{--            @if($link->parent_is_nc_parameter == true && $link->parent_is_nc_viewonly == false)--}}
            {{--            @if($link->parent_is_nc_parameter == true)--}}

        var numrecalc_{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');

        numrecalc_{{$prefix}}{{$link->id}}.addEventListener("change", on_numcalc);


        @endif

        @endforeach
    </script>
    <script>
        window.onload = function () {
            // массив функций нужен, что при window.onload запустить обработчики всех полей
            @foreach($functions as $value)
                {{$value}}(true);
            @endforeach
            // on_parent_refer();

            // Не нужно вызывать функцию on_numcalc(),
            // это связано с разрешенной корректировкой вычисляемых полей ($link->parent_is_nc_viewonly)
            //on_numcalc();
            @foreach($array_disabled as $key=>$value)
                parent_base_id_work = document.getElementById('link{{$key}}').disabled = true;
            document.getElementById('link{{$key}}').disabled = true;
            @endforeach
        };

        // https://ru.stackoverflow.com/questions/1114823/%D0%9A%D0%B0%D0%BA-%D1%81%D0%B4%D0%B5%D0%BB%D0%B0%D1%82%D1%8C-%D1%82%D0%B0%D0%BA-%D1%87%D1%82%D0%BE%D0%B1%D1%8B-%D0%BF%D1%80%D0%B8-%D0%BD%D0%B0%D0%B6%D0%B0%D1%82%D0%B8%D0%B8-%D0%BD%D0%B0-%D0%BA%D0%BD%D0%BE%D0%BF%D0%BA%D1%83-%D0%BF%D1%80%D0%BE%D0%B8%D0%B3%D1%80%D1%8B%D0%B2%D0%B0%D0%BB%D1%81%D1%8F-%D0%B7%D0%B2%D1%83%D0%BA
        // https://odino.org/emit-a-beeping-sound-with-javascript/
        // https://question-it.com/questions/1025607/vosproizvesti-zvukovoj-signal-pri-nazhatii-knopki
        // function playSound(sound) {
        //     var song = document.getElementById(sound);
        //     song.volume = 1;
        //     if (song.paused) {
        //         song.play();
        //     } else {
        //         song.pause();
        //     }
        // }
    </script>

@endsection
