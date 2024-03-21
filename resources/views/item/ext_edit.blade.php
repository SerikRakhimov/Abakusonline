@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Link;
    use App\Models\Item;
    use App\Models\Set;
    use \App\Http\Controllers\GlobalController;
    use \App\Http\Controllers\MainController;
    use \App\Http\Controllers\BaseController;
    use \App\Http\Controllers\ItemController;
    use \App\Http\Controllers\LinkController;
    use \App\Http\Controllers\StepController;

    // Нужно для функции date('Y-m-d')
    // установка часового пояса нужно для сохранения времени
    date_default_timezone_set('Asia/Almaty');

    $update = isset($item);
    $saveurl_add = null;
    // При добавлении записи
    if (!$update) {
        // Шифровка
        $saveurl_add = GlobalController::set_url_save(Request::server('HTTP_REFERER'));
    }
    $base_right = GlobalController::base_right($base, $role, $relit_id);
    $relip_project = GlobalController::calc_relip_project($relit_id, $project);
    // У $base есть ли считаемые поля (да/нет)
    // Похожие по смыслу проверки "@if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true && $link->parent_is_nc_viewonly==false)" в этом файле восемь раз
    // '->exists()' не использовать, дает сообщение об ошибке
    $allcalc_button = $base->child_links->where('parent_is_numcalc', true)
        ->where('parent_is_nc_screencalc', true)
        ->where('parent_is_nc_viewonly', false)
        ->first();
    $emoji_enable = true;
    ?>
    {{--    <script>--}}
    {{--        function browse(link_id, project_id, role_id, item_id) {--}}
    {{--            // Нужно, используется в browser.blade.php--}}
    {{--            window.item_id = document.getElementById("'" + link_id + "'");--}}
    {{--            window.item_code = document.getElementById('code' + link_id);--}}
    {{--            window.item_name = document.getElementById('name' + link_id);--}}
    {{--            open('{{route('item.browser', '')}}' + '/' + link_id + '/' + base_id + '/' + project_id + '/' + role_id + '/' + {{$relit_id}} + '/' + item_id + '/1/1', 'browse', 'width=850, height=800');--}}
    {{--        };--}}
    {{--    </script>--}}

    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])
    <h4 class="display-5 text-center">
        @if (!$update)
            {{trans('main.new_record')}}
        @else
            {{trans('main.edit_record')}}
        @endif
        <span class="text-label">-</span> <span class="text-title">{{$base->info()}}</span>
        @if($is_view_minutes == true)
            <span class="badge badge-pill badge-related" title="{{trans('main.title_min')}}">
                    @if (!$update)
                    {{GlobalController::base_minutes($base)}}
                @else
                    {{GlobalController::remaining_minutes($item)}}
                @endif
                </span>
        @endif
    </h4>
    <br>
    {{--    https://qastack.ru/programming/1191113/how-to-ensure-a-select-form-field-is-submitted-when-it-is-disabled--}}
    {{--    <form--}}
    {{--        action="{{$update ?--}}
    {{--        route('item.ext_update', ['item'=>$item, 'project' => $project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),--}}
    {{--        'relit_id' => GlobalController::set_relit_id($relit_id),--}}
    {{--    'string_link_ids_current' => $string_link_ids_current,--}}
    {{--    'string_item_ids_current' => $string_item_ids_current,--}}
    {{--    'string_all_codes_current'=> $string_all_codes_current,--}}
    {{--         'heading'=>$heading,--}}
    {{--         'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page, 'body_all_page'=>$body_all_page,--}}
    {{--         'parent_ret_id' => GlobalController::set_relit_id($parent_ret_id),--}}
    {{--         'view_link'=>GlobalController::set_par_null($view_link),--}}
    {{--         'par_link' =>$par_link,--}}
    {{--         'parent_item' => $parent_item]):--}}
    {{--        route('item.ext_store', ['base'=>$base, 'project' => $project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),--}}
    {{--         'relit_id' => GlobalController::set_relit_id($relit_id),--}}
    {{--    'string_link_ids_current' => $string_link_ids_current,--}}
    {{--    'string_item_ids_current' => $string_item_ids_current,--}}
    {{--    'string_all_codes_current'=> $string_all_codes_current,--}}
    {{--            'heading'=>$heading,--}}
    {{--            'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,--}}
    {{--            'parent_ret_id' => GlobalController::set_relit_id($parent_ret_id),--}}
    {{--            'view_link'=>GlobalController::set_par_null($view_link),--}}
    {{--            'par_link' =>$par_link,--}}
    {{--            'parent_item' => $parent_item])}}"--}}
    {{--        method="POST"--}}
    {{--        enctype=multipart/form-data--}}
    {{--        @if($par_link)--}}
    {{--        onsubmit=on_submit()--}}
    {{--        --}}{{--        @else--}}
    {{--        --}}{{--        onsubmit="playSound('sound');"--}}
    {{--        @endif--}}
    {{--        name="form">--}}
    {{--    'string_link_ids_current' => $string_link_ids_current,--}}
    {{--    'string_item_ids_current' => $string_item_ids_current,--}}
    {{--    'string_all_codes_current'=> $string_all_codes_current,--}}
    <form
        action="{{$update ?
        route('item.ext_update', ['item'=>$item, 'project' => $project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
        'relit_id' => $relit_id,
        'string_current' => $string_current,
        'heading'=>$heading,
        'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page, 'body_all_page'=>$body_all_page,
        'parent_ret_id' => GlobalController::set_par_null($parent_ret_id),
        'view_link'=>GlobalController::set_par_null($view_link),
        'saveurl_edit' => $saveurl_edit,
        'par_link' =>$par_link,
        'parent_item' => $parent_item
        ]):
        route('item.ext_store', ['base'=>$base, 'project' => $project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
        'relit_id' => $relit_id,
        'string_current' => $string_current,
        'heading'=>$heading,
        'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
        'parent_ret_id' => GlobalController::set_par_null($parent_ret_id),
        'view_link'=>GlobalController::set_par_null($view_link),
        'saveurl_add' => $saveurl_add,
        'par_link' =>$par_link,
        'parent_item' => $parent_item])}}"
        method="POST"
        enctype=multipart/form-data
        {{--        @if($par_link)--}}
        onsubmit=on_submit()
        {{--        @else--}}
        {{--        onsubmit="playSound('sound');"--}}
        {{--        @endif--}}
        name="form">
        @csrf

        @if ($update)
            @method('PUT')
        @endif

        {{--        <input type="hidden" name="base_id" value="{{$base->id}}">--}}
        @if ($update)
            <div class="form-group row">
                <div class="col-sm-3 text-right">
                    <label>{{GlobalController::id_and_brackets_emoji('Id', $emoji_enable)}}
                    </label>
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
                                class="text-danger">{{GlobalController::label_is_required($base)}}</span></label>
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
                                class="text-danger">{{GlobalController::label_is_required($base)}}</span></label>
                    </div>
                    <div class="col-sm-3">
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
                    <div class="col-sm-6">
                    </div>
                </div>
                {{--                            если тип корректировки поля - дата--}}
            @elseif($base->type_is_date())
                <div class="form-group row">
                    <div class="col-sm-3 text-right">
                        <label for="name_lang_0" class="col-form-label">{{$base->name()}}
                            <span
                                class="text-danger">{{GlobalController::label_is_required($base)}}</span></label>
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
                                <label for="name_lang_{{$key}}" class="col-form-label">{{$base->prnm(true)}}
                                    @if($base->is_one_value_lst_str_txt == false)
                                        ({{trans('main.' . $value)}})
                                    @endif
                                    <span
                                        class="text-danger">{{GlobalController::label_is_required($base)}}</span></label>
                            </div>
                            <div class="col-sm-7">
                                <textarea
                                    name="name_lang_{{$key}}"
                                    id="name_lang_{{$key}}"
                                    rows="5"
                                    class="form-control @error('name_lang_' . $key) is-invalid @enderror"
                                    placeholder=""
                                    maxlength="10000">
                                       {{ old('name_lang_' . $key) ?? ($item->text['name_lang_' . $key] ?? '') }}
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
                @include('edit.img_base',['update'=>$update, 'base'=>$base,'item'=>$item ?? null,
                         'title'=>$base->name(), 'name'=>"name_lang_0",'id'=>"name_lang_0", 'size'=>"smed"])
                {{--                            если тип корректировки поля - документ--}}
            @elseif($base->type_is_document())
                @include('edit.doc_base',['update'=>$update, 'base'=>$base,'item'=>$item ?? null,
                         'usercode'=>GlobalController::usercode_calc(), 'title'=>$base->name(), 'name'=>"name_lang_0",'id'=>"name_lang_0"])
                {{--                            если тип корректировки поля - строка или список--}}
            @else
                @if($base->is_calcname_lst == false)
                    <div class="form-group row">
                        @foreach (config('app.locales') as $key=>$value)
                            @if(($base->is_one_value_lst_str_txt == true && $key == 0) || ($base->is_one_value_lst_str_txt == false))
                                <div class="col-sm-3 text-right">
                                    <label for="name_lang_{{$key}}" class="col-form-label">{{$base->prnm(true)}}
                                        @if($base->is_one_value_lst_str_txt == false)
                                            ({{trans('main.' . $value)}})
                                        @endif
                                        <span
                                            class="text-danger">{{GlobalController::label_is_required($base, $base_right)}}</span></label>
                                </div>
                                <div class="col-sm-7">
                                    {{--                                    https://getbootstrap.com/docs/4.0/components/forms/--}}
                                    {{--                                    <div class="input-group">--}}
                                    {{--                                        <div class="input-group-prepend">--}}
                                    {{--                                            <div class="input-group-text">🌞</div>--}}
                                    {{--                                        </div>--}}
                                    {{--                                        <input type="text"--}}
                                    {{--                                               name="name_lang_{{$key}}"--}}
                                    {{--                                               id="name_lang_{{$key}}"--}}
                                    {{--                                               class="form-control @error('name_lang_' . $key) is-invalid @enderror"--}}
                                    {{--                                               placeholder=""--}}
                                    {{--                                               value="{{ old('name_lang_' . $key) ?? ($item['name_lang_' . $key] ?? '') }}"--}}
                                    {{--                                               maxlength="255">--}}
                                    {{--                                    </div>--}}
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
            // Вычисляет $relit_id
            //            $calc_link_relit_id = GlobalController::calc_link_relit_id($link, $role, $relit_id);
            //            $base_link_right = GlobalController::base_link_right($link, $role, $calc_link_relit_id);
            $base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
            ?>
            @if($base_link_right['is_edit_link_enable'] == false)
                @continue
            @endif
            <?php
            // При добавлении записи
            if (!$update) {
                // Передается без параметров
                $view_enable = GlobalController::view_enable($link->id);
                // При корректировке записи
            } else {
                $view_enable = GlobalController::view_enable($link->id, $item->id);
            }
            ?>
            @if($view_enable == false)
                @continue
            @endif
            <?php
            // Вывести с эмодзи
            $result_parent_label = $link->parent_label(true);
            $link_parent_base = $link->parent_base;
            //                Загружаются данные для списков выбора
            //$result = ItemController::get_items_ext_edit_for_link($link, $project, $role, $relit_id);
            //                $result = ItemController::get_items_for_link($link, $project, $role, $relit_id);
            //                $items = $result['result_parent_base_items'];
            $items_default = true;
            //  Проверка на фильтруемые поля
            $link_selection_table = ItemController::get_link_refer_main($link_parent_base, $link);
            // Проверка на ввод в виде справочника
            if ($link->parent_base->is_code_needed == true && $link->parent_is_enter_refer == true) {
                if ($link_selection_table) {
                    $items_default = false;
                }
            }
            if ($link->parent_base->type_is_list()) {
                // если это фильтрируемое поле - то, тогда загружать весь список не нужно
                // Первый вариант
                //$link_exists = Link::where('parent_is_child_related', true)->where('parent_child_related_start_link_id', $link->id)->exists();
                $link_exists = Link::where('child_base_id', $link->child_base_id)
                    ->where('parent_is_child_related', true)
                    ->where('parent_child_related_start_link_id', $link->id)
                    ->exists();
                if ($link_exists) {
                    $items_default = false;
                }
            }
            // Пустой список
            $its_no_get = Item::where('id', null);
            if ($items_default == true && $link->parent_base->type_is_list()) {
                //$result = ItemController::get_items_main($link_parent_base, $project, $role, $link->parent_relit_id, $link);
                //$result = ItemController::get_items_main($link_parent_base, $project, $role, $relit_id,
                //   $base_link_right['is_list_hist_records_enable'], $link);
                // Так правильно "$base_link_right['is_brow_hist_records_enable']", а не "$base_link_right['is_list_hist_records_enable']"
                $result = ItemController::get_items_main($link_parent_base, $project, $role, $relit_id,
                    $base_link_right['is_brow_hist_records_enable'], $link);
                $its_no_get = $result['items_no_get'];
            }
            $code_find = null;
            if ($value != null) {
                $item_find = Item::findOrFail($value);
                $code_find = $item_find->code;
            }
            ?>
            {{--     @if($link_start_child)--}}
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
                            @include('layouts.item.ext_edit.parent_label',
                                ['result_parent_label'=>$result_parent_label, 'key'=>$key, 'par_link'=>$par_link])
                        </label>
                    </div>
                    <div class="col-sm-7">
                        {{-- Такая проверка на '$link->parent_base->type_is_image()/!$link->parent_base->type_is_image()' в трех местах в этом файле--}}
                        @if($link->parent_base->type_is_image())
                            <span class=""
                                  name="calc{{$key}}"
                                  id="link{{$key}}"></span>
                            {{--                                                        <a href="{{Storage::url($item_find->filename())}}">--}}
                            {{--                                                            <img src="{{Storage::url($item_find->filename())}}" height="50"--}}
                            {{--                                                                 alt="" title="{{$item_find->filename()}}">--}}
                            {{--                                                        </a>--}}
                            {{--                                                    @elseif($link->parent_base->type_is_document())--}}
                            {{--                                                        <a href="{{Storage::url($item_find->filename())}}" target="_blank">--}}
                            {{--                                                            Открыть документ--}}
                            {{--                                                        </a>--}}
                        @else
                            <span class="form-label" name="calc{{$key}}">
                            <span class="text-related"
                                  id="link{{$key}}"></span>
                                <small>
                            <span class="text-label"
                                  id="unit{{$key}}"></span></small>
                            <span hidden
                                  id='{{$key}}'></span></span>
                        @endif
                    </div>
                    <div class="col-sm-2">
                    </div>
                </div>
                {{--                            проверка для вывода полей вычисляемой таблицы--}}
            @elseif($link->parent_is_output_calculated_table_field == true)
                <?php
                // Проверка, если ли только type_is_list()
                $sets_edit_group = ItemController::get_sets_list_group($base, $link);
                // Проверка нужна
                ?>
                @if($sets_edit_group == true)
                    <div class="form-group row"
                    >
                        <div class="col-sm-3 text-right">
                            <label for="calc{{$key}}" class="form-label">
                                @include('layouts.item.ext_edit.parent_label',
                                    ['result_parent_label'=>$result_parent_label, 'key'=>$key, 'par_link'=>$par_link])
                            </label>
                            {{--                            Выводить скрытое поле - id найденного значения, для 'Выводить поле вычисляемой таблицы'--}}
                            <span hidden
                                  id='{{$key}}'></span>
                        </div>
                        <div class="col-sm-7">
                            <span class="form-label" name="calc{{$key}}">
                                    <span class="text-related"
                                          id="link{{$key}}"></span>
                            <small><span class="text-label"
                                         id="unit{{$key}}"></span></small></span>
                        </div>
                        <div class="col-sm-2">
                        </div>
                    </div>
                @endif
            @else
                @if($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true)
                    <div class="form-group row">
                        <div class="col-sm-3 text-right">
                            <label for="{{$key}}" class="col-form-label">
                                @include('layouts.item.ext_edit.parent_label',
                                ['result_parent_label'=>$result_parent_label, 'key'=>$key, 'par_link'=>$par_link])
                                ({{mb_strtolower(trans('main.code'))}})
                                <span
                                    class="text-danger">{{GlobalController::label_is_required($link->parent_base)}}</span></label>
                        </div>

                        <div class="col-sm-2">
                            <input name="{{$key}}" id="{{$key}}" type="hidden" value="{{old($key) ?? $value ?? "0"}}">
                            {{-- <input name="{{$key}}" id="{{$key}}" value="{{old($key) ?? $value ?? "0"}}">--}}
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
                                   {{--                                   @if (($key == $par_link->id) & ($base_link_right['is_edit_parlink_enable'] == false))--}}
                                   {{--                                   disabled--}}
                                   {{--                                @endif--}}
                                   @if ($key == $par_link->id)
                                   {{-- '(!$update)' - только при добавлении записи--}}
                                   {{-- "((!$update) | ($base_link_right['is_edit_parlink_enable'] == false)" - используется в связке--}}
                                   @if ((!$update) | ($base_link_right['is_edit_parlink_enable'] == false))
                                   disabled
                                @endif
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
                            {{--                            onclick="browse('{{$link->id}}', '{{$link->parent_base_id}}', '{{$project->id}}', '{{$role->id}}', '{{$relit_id}}',child_base_start_id{{$link->id}}.options[child_base_start_id{{$link->id}}.selectedIndex].value)"--}}
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
                                    @else
                                    @if($par_link )
                                    {{--                                    @if (($key == $par_link->id) & ($base_link_right['is_edit_parlink_enable'] == false))--}}
                                    {{--                                    disabled--}}
                                    {{--                                @endif--}}
                                    @if ($key == $par_link->id)
                                    {{-- '(!$update)' - только при добавлении записи--}}
                                    {{-- "((!$update) | ($base_link_right['is_edit_parlink_enable'] == false)" - используется в связке--}}
                                    @if ((!$update) | ($base_link_right['is_edit_parlink_enable'] == false))
                                    disabled
                                @endif
                                @endif
                                @endif
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
                            <label for="{{$key}}" class="col-form-label">
                                @include('layouts.item.ext_edit.parent_label',
                                ['result_parent_label'=>$result_parent_label, 'key'=>$key, 'par_link'=>$par_link])
                                <span
                                    class="text-danger">{{GlobalController::label_is_required($link->parent_base)}}</span></label>
                        </div>
                        <div class="col-sm-3">
                            <input type="number"
                                   name="{{$key}}"
                                   id="link{{$key}}"
                                   class="form-control @error($key) is-invalid @enderror"
                                   placeholder=""
                                   {{--                                   value="{{(old($key)) ?? (($value != null) ? GlobalController::restore_number_from_item($link->parent_base, Item::find($value)->name()) :--}}
                                   {{--                                    (($link->parent_num_bool_default_value!="")? $link->parent_num_bool_default_value:'0')--}}
                                   {{--                                    )}}"--}}
                                   {{--                                    GlobalController::restore_number_from_item() - на вход число с нулями спереди--}}
                                   {{--                                    На выходе это же число в виде строки--}}
                                   {{--                                    Нужно для правильного отображения чисел--}}
                                   {{--                            "$parent_item->project_id" не использовать, правильно "$project"--}}
                                   value="{{(old($key)) ?? (($value != null) ? GlobalController::restore_number_from_item($link->parent_base, Item::find($value)->name()) :
                                                                (($link->parent_is_seqnum==true)? ItemController::calculate_new_seqnum($project, $link, $parent_item, $par_link):
                                                                (($link->parent_num_bool_default_value!="")? $link->parent_num_bool_default_value: '0'))
                                                                )}}"
                                   step="{{$link->parent_base->digits_num_format()}}"

                                   @if($base_link_right['is_edit_link_read'] == true)
                                   disabled
                                   @else
                                   @if($par_link || $link->parent_is_nc_viewonly==true)
                                   @if($par_link )
                                   {{--                                   @if (($key == $par_link->id) & ($base_link_right['is_edit_parlink_enable'] == false))--}}
                                   {{--                                   disabled--}}
                                   {{--                                   @endif--}}
                                   @if ($key == $par_link->id)
                                   {{-- '(!$update)' - только при добавлении записи--}}
                                   {{-- "((!$update) | ($base_link_right['is_edit_parlink_enable'] == false)" - используется в связке--}}
                                   @if ((!$update) | ($base_link_right['is_edit_parlink_enable'] == false))
                                   disabled
                                   @endif
                                   @endif
                                   @elseif($link->parent_is_nc_viewonly==true)
                                   {{-- Похожая строка ниже--}}
                                   readonly
                                @endif
                                {{--                                    @else--}}
                                {{--                                   тут использовать readonly (при disabled (здесь) - это поле не обновляется)--}}
                                {{--                                   также при disabled работают строки (ниже):--}}
                                {{--                                   parent_base_id_work = document.getElementById('link{{$key}}').disabled = true;--}}
                                {{--                                   parent_base_id_work = document.getElementById('link{{$key}}').disabled = false;--}}
                                {{--                                   readonly--}}
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
                        <div class="col-sm-1">
                            {{$link->parent_base->unit_meas_desc()}}
                        </div>
                        {{-- Похожие проверка внизу--}}
                        {{-- @if($base_link_right['is_edit_link_read'] == false)--}}
                        {{-- @if($link->parent_is_numcalc == true)--}}
                        {{-- 'is_edit_link_read' - 'Чтение Связи в форме'--}}
                        @if($base_link_right['is_edit_link_read'] == false)
                            {{--                            @if($link->parent_is_numcalc == true)--}}
                            {{-- Похожие по смыслу проверки "@if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true && $link->parent_is_nc_viewonly==false)" в этом файле восемь раз --}}
                            @if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true && $link->parent_is_nc_viewonly==false)
                                <div class="col-sm-1">
                                    {{--                                    Не удалять--}}
                                    {{--                                    <input type="button" value="..." title="{{trans('main.calculate')}}"--}}
                                    {{--                                           name="button_nc{{$key}}"--}}
                                    {{--                                           id="button_nc{{$key}}"--}}
                                    {{--                                    >--}}
                                    <button type="button" title="{{trans('main.calculate')}} {{$result_parent_label}}"
                                            name="button_nc{{$key}}"
                                            id="button_nc{{$key}}"
                                            class="text-label">
                                        <i class="fas fa-calculator d-inline"></i>
                                    </button>
                                </div>
                                <div class="col-sm-4">
                                <span class="form-label text-danger"
                                      name="name{{$key}}"
                                      id="name{{$key}}"></span>
                                </div>
                            @else
                                <div class="col-sm-5">
                                </div>
                            @endif
                        @endif
                    </div>

                    {{--                                если тип корректировки поля - дата--}}
                @elseif($link->parent_base->type_is_date())
                    <div class="form-group row">
                        <div class="col-sm-3 text-right">
                            <label for="{{$key}}" class="col-form-label">
                                @include('layouts.item.ext_edit.parent_label',
                                ['result_parent_label'=>$result_parent_label, 'key'=>$key, 'par_link'=>$par_link])
                                <span
                                    class="text-danger">{{GlobalController::label_is_required($link->parent_base)}}</span>
                            </label>
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
                                   @if($par_link )
                                   {{--                                   @if (($key == $par_link->id) & ($base_link_right['is_edit_parlink_enable'] == false))--}}
                                   {{--                                   disabled--}}
                                   {{--                                @endif--}}
                                   @if ($key == $par_link->id)
                                   {{-- '(!$update)' - только при добавлении записи--}}
                                   {{-- "((!$update) | ($base_link_right['is_edit_parlink_enable'] == false)" - используется в связке--}}
                                   @if ((!$update) | ($base_link_right['is_edit_parlink_enable'] == false))
                                   disabled
                                @endif
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
                            <label class="form-label" for="{{$key}}">
                                @include('layouts.item.ext_edit.parent_label',
                                ['result_parent_label'=>$result_parent_label, 'key'=>$key, 'par_link'=>$par_link])
                                <span
                                    class="text-danger">{{GlobalController::label_is_required($link->parent_base)}}</span>
                            </label>
                        </div>
                        <div class="col-sm-2">
                            <input class="@error($key) is-invalid @enderror"
                                   type="checkbox"
                                   name="{{$key}}"
                                   id="link{{$key}}"
                                   placeholder=""
                                   @if ((boolean)(old($key) ?? (($value != null) ? Item::find($value)->name_lang_0 :
                                       (($link->parent_num_bool_default_value!="")? $link->parent_num_bool_default_value:'0'))
                                           ) == true)
                                   checked
                                   @endif
                                   {{--                            @if($base_link_right['is_edit_link_read'] == true)--}}
                                   {{--                                disabled--}}
                                   {{--                            @else--}}
                                   {{--                                @if($par_link)--}}
                                   {{--                                    @if (($key == $par_link->id) & ($base_link_right['is_edit_parlink_enable'] == false))--}}
                                   {{--                                        disabled--}}
                                   {{--                                    @endif--}}
                                   {{--                                @endif--}}
                                   {{--                            @endif--}}
                                   @if($base_link_right['is_edit_link_read'] == true)
                                   disabled
                                   @else
                                   @if($par_link || $link->parent_is_nc_viewonly==true)
                                   @if($par_link )
                                   {{--                                   @if (($key == $par_link->id) & ($base_link_right['is_edit_parlink_enable'] == false))--}}
                                   {{--                                   disabled--}}
                                   {{--                                   @endif--}}
                                   @if ($key == $par_link->id)
                                   {{-- '(!$update)' - только при добавлении записи--}}
                                   {{-- "((!$update) | ($base_link_right['is_edit_parlink_enable'] == false)" - используется в связке--}}
                                   @if ((!$update) | ($base_link_right['is_edit_parlink_enable'] == false))
                                   disabled
                                   @endif
                                   @endif
                                   @elseif($link->parent_is_nc_viewonly==true)
                                   {{-- Похожая строка ниже--}}
                                   onclick="return false;"
                                @endif
                                {{--                                   @else--}}
                                {{--                                   тут использовать readonly (при disabled (здесь) - это поле не обновляется)--}}
                                {{--                                   также при disabled работают строки (ниже):--}}
                                {{--                                   parent_base_id_work = document.getElementById('link{{$key}}').disabled = true;--}}
                                {{--                                   parent_base_id_work = document.getElementById('link{{$key}}').disabled = false;--}}
                                {{-- https://www.codegrepper.com/code-examples/whatever/checkbox+readonly--}}
                                {{--                                   onclick="return false;"--}}
                                @endif
                                @endif
                            >
                            @error($key)
                            <div class="invalid-feedback">
                                {{$message}}
                            </div>
                            @enderror
                        </div>
                        {{-- 'is_edit_link_read' - 'Чтение Связи в форме'--}}
                        @if($base_link_right['is_edit_link_read'] == false)
                            {{-- Похожие по смыслу проверки "@if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true && $link->parent_is_nc_viewonly==false)" в этом файле восемь раз --}}
                            @if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true && $link->parent_is_nc_viewonly==false)
                                <div class="col-sm-1">
                                    <button type="button" title="{{trans('main.calculate')}} {{$result_parent_label}}"
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
                    {{--                                если тип корректировки поля - строка--}}
                @elseif($link->parent_base->type_is_string())
                    <fieldset id="link{{$key}}_fs"
                              @if($base_link_right['is_edit_link_read'] == true)
                              disabled
                              @else
                              @if($par_link )
                              {{--                              @if (($key == $par_link->id) & ($base_link_right['is_edit_parlink_enable'] == false))--}}
                              {{--                              disabled--}}
                              {{--                        @endif--}}
                              @if ($key == $par_link->id)
                              {{-- '(!$update)' - только при добавлении записи--}}
                              {{-- "((!$update) | ($base_link_right['is_edit_parlink_enable'] == false)" - используется в связке--}}
                              @if ((!$update) | ($base_link_right['is_edit_parlink_enable'] == false))
                              disabled
                        @endif
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
                                //$input_name = $key . '_' . $lang_key;  // такой вариант работает
                                $input_name = ($lang_key == 0) ? $key : $key . '_' . $lang_key;  // такой вариант работает
                                ?>
                                @if(($link->parent_base->is_one_value_lst_str_txt == true && $lang_key == 0)
                                    || ($link->parent_base->is_one_value_lst_str_txt == false))
                                    <div class="col-sm-3 text-right">
                                        <label for="{{$input_name}}"
                                               class="col-form-label">
                                            @include('layouts.item.ext_edit.parent_label',
                                ['result_parent_label'=>$result_parent_label, 'key'=>$key, 'par_link'=>$par_link])
                                            @if($link->parent_base->is_one_value_lst_str_txt == false)
                                                ({{trans('main.' . $lang_value)}})
                                            @endif
                                            <span
                                                class="text-danger">{{GlobalController::label_is_required($link->parent_base)}}</span></label>
                                    </div>
                                    <div class="col-sm-7">
                                        <?php
                                        $fix_name = '';
                                        // Есть права на корректировку
                                        if ($base_link_right['is_edit_link_update'] == true) {
                                            if (Auth::check()) {
                                                if ($link->parent_is_user_login_str == true) {
                                                    // Если добавление записи
                                                    if (!$update) {
                                                        $fix_name = Auth::user()->name();
                                                        // Если корректировка записи
                                                    } else {
                                                        $fix_name = Item::find($value)['name_lang_' . $lang_key];
                                                    }
                                                } elseif ($link->parent_is_user_email_str == true) {
                                                    // Если добавление записи
                                                    if (!$update) {
                                                        $fix_name = Auth::user()->email();
                                                        // Если корректировка записи
                                                    } else {
                                                        $fix_name = Item::find($value)['name_lang_' . $lang_key];
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                        <input type="text"
                                               name="{{$input_name}}"
                                               id="link{{$input_name}}"
                                               class="form-control @error($input_name) is-invalid @enderror"
                                               placeholder=""
                                               @if($fix_name == '')
                                               value="{{(old($input_name)) ?? (($value != null) ? Item::find($value)['name_lang_'.$lang_key] : '')}}"
                                               @else
                                               value="{{$fix_name}}"
                                               @endif
                                               maxlength="255"
                                               @if($fix_name != '')
                                               readonly
                                            @endif
                                        >
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
                                    {{--Нужно @if($lang_key == 0)--}}
                                    @if($lang_key == 0)
                                        {{-- Похожие по смыслу проверки "@if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true && $link->parent_is_nc_viewonly==false)" в этом файле восемь раз --}}
                                        @if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true && $link->parent_is_nc_viewonly==false)
                                            <div class="col-sm-1">
                                                <button type="button"
                                                        title="{{trans('main.calculate')}} {{$result_parent_label}}"
                                                        name="button_nc{{$key}}"
                                                        id="button_nc{{$key}}"
                                                        class="text-label">
                                                    <i class="fas fa-calculator d-inline"></i>
                                                </button>
                                            </div>
                                            <div class="col-sm-1">
                                    <span class="form-label text-danger"
                                          name="name{{$key}}"
                                          id="name{{$key}}"></span>
                                            </div>
                                        @else
                                            <div class="col-sm-2">
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </fieldset>

                    {{--                                если тип корректировки поля - текст--}}
                @elseif($link->parent_base->type_is_text())
                    <fieldset id="link{{$key}}_fs"
                              @if($base_link_right['is_edit_link_read'] == true)
                              disabled
                              @else
                              @if($par_link )
                              {{--                              @if (($key == $par_link->id) & ($base_link_right['is_edit_parlink_enable'] == false))--}}
                              {{--                              disabled--}}
                              {{--                        @endif--}}
                              @if ($key == $par_link->id)
                              {{-- '(!$update)' - только при добавлении записи--}}
                              {{-- "((!$update) | ($base_link_right['is_edit_parlink_enable'] == false)" - используется в связке--}}
                              @if ((!$update) | ($base_link_right['is_edit_parlink_enable'] == false))
                              disabled
                        @endif
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
                                // $input_name = $key . '_' . $lang_key;  // такой вариант работает
                                $input_name = ($lang_key == 0) ? $key : $key . '_' . $lang_key;  // такой вариант работает
                                ?>
                                @if(($link->parent_base->is_one_value_lst_str_txt == true && $lang_key == 0)
                                    || ($link->parent_base->is_one_value_lst_str_txt == false))
                                    <div class="col-sm-3 text-right">
                                        <label for="{{$input_name}}"
                                               class="col-form-label">
                                            @include('layouts.item.ext_edit.parent_label',
                                ['result_parent_label'=>$result_parent_label, 'key'=>$key, 'par_link'=>$par_link])
                                            @if($link->parent_base->is_one_value_lst_str_txt == false)
                                                ({{trans('main.' . $lang_value)}})
                                            @endif
                                            <span
                                                class="text-danger">{{GlobalController::label_is_required($link->parent_base)}}</span></label>
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
                    {{--                    @include('edit.img_link',['update'=>$update, 'base'=>$link->parent_base,'value'=>$value, 'title'=>$result_parent_label, 'name'=>$key,'id'=>"link".$key, 'size'=>"small"])--}}
                    @include('edit.img_link',['update'=>$update, 'base'=>$link->parent_base,'value'=>$value, 'base_link_right'=>$base_link_right,'title'=>$result_parent_label, 'name'=>$key,'id'=>"link".$key, 'size'=>"smed"])

                    {{--                            если тип корректировки поля - документ--}}
                @elseif($link->parent_base->type_is_document())
                    {{--                        @include('edit.doc_link',['update'=>$update, 'base'=>$link->parent_base,'value'=>$value, 'title'=>$result_parent_label, 'name'=>$key,'id'=>"link".$key])--}}
                    @include('edit.doc_link',['update'=>$update, 'base'=>$link->parent_base,'value'=>$value,
                            'base_link_right'=>$base_link_right,'usercode'=>GlobalController::usercode_calc(),
                             'title'=>$result_parent_label, 'name'=>$key,'id'=>"link".$key])
                    {{--                         Такая же проверка ItemController::get_items_ext_edit_for_link(),--}}
                    {{--                         в ext_edit.php--}}
                @elseif($link->parent_base->type_is_list())
                    <?php
                    $hidden_list = false;
                    $ing_filter = false;
                    // "if ($par_link && ($value != null))" - неправильно, т.к. при if ($par_link) выполняются два действия
                    if ($par_link) {
                        // 1-ое действие
                        // Оставить в списке только одно значение ">where('id', $value)" при $par_link == true,
                        // $value равно parent_item->id,
                        // это поле($par_link) в форме с признаком disabled, как правило
                        if ($value != null) {
                            if ($key == $par_link->id) {
                                $ing_filter = true;
                                $its_no_get = $its_no_get->where('id', $value);
                            }
                        }
                        // 2-ое действие
                        // При параллельной связи $par_link
                        // другие паралельные связи не доступны при добавлении/корректировке записи
                        // при способе ввода Пространство (если передано $par_link)
                        if ($par_link->parent_is_parallel == true && $link->parent_is_parallel == true) {
                            $hidden_list = true;
                        }
                    }
                    $item_tree = null;
                    if ($link->parent_is_tree_value == true) {
                        $item_tree = ItemController::get_tree_item($role, $link, $string_current);
                    }
                    $its_list = $its_no_get->get();
                    ?>
                    <div class="form-group row">
                        <div class="col-sm-3 text-right">
                            <label for="{{$key}}" class="col-form-label"
                                   {{--                               Проверка 'if ($par_link)' проверена ранее, при присваивании $hidden_list --}}
                                   @if($hidden_list)
                                   @if($key != $par_link->id)
                                   hidden
                                @endif
                                @endif
                            >
                                @include('layouts.item.ext_edit.parent_label',
                                ['result_parent_label'=>$result_parent_label, 'key'=>$key, 'par_link'=>$par_link])
                                <span
                                    class="text-danger">{{GlobalController::label_is_required($link->parent_base)}}{{$value !=null ? "" : "~"}}</span></label>
                        </div>
                        <div class="col-sm-7">
                            <select class="form-control"
                                    name="{{$key}}"
                                    id="link{{$key}}"
                                    class="form-control @error($key) is-invalid @enderror"
                                    {{--                                    @if($base_link_right['is_edit_link_read'] == true)--}}
                                    {{--                                    disabled--}}
                                    {{--                                    @else--}}
                                    {{--                                    @if($par_link)--}}
                                    {{--                                    @if (($key == $par_link->id) & ($base_link_right['is_edit_parlink_enable'] == false))--}}
                                    {{--                                    disabled--}}
                                    {{--                                    @else--}}
                                    {{--                                    @if($hidden_list)--}}
                                    {{--                                    hidden--}}
                                    {{--                                    @endif--}}
                                    {{--                                    @endif--}}
                                    {{--                                    @endif--}}
                                    {{--                                    @endif--}}
                                    @if($base_link_right['is_edit_link_read'] == true)
                                    disabled
                                    @else
                                    @if($par_link || $link->parent_is_nc_viewonly==true)
                                    @if($par_link)
                                    {{--                                    @if (($key == $par_link->id) & ($base_link_right['is_edit_parlink_enable'] == false))--}}
                                    {{--                                    disabled--}}
                                    {{--                                    @endif--}}
                                    @if ($key == $par_link->id)
                                    {{-- '(!$update)' - только при добавлении записи--}}
                                    {{-- "((!$update) | ($base_link_right['is_edit_parlink_enable'] == false)" - используется в связке--}}
                                    @if ((!$update) | ($base_link_right['is_edit_parlink_enable'] == false))
                                    disabled
                                    @endif
                                    @endif
                                    @elseif($link->parent_is_nc_viewonly==true)
                                    {{--                                Учет "disabled" при "$link->parent_is_nc_viewonly==true", см ItemController::get_array_calc()--}}
                                    disabled
                                    @else
                                    @if($hidden_list)
                                    hidden
                                    @else
                                    {{--                                   тут использовать readonly (при disabled (здесь) - это поле не обновляется)--}}
                                    {{--                                   также при disabled работают строки (ниже):--}}
                                    {{--                                   parent_base_id_work = document.getElementById('link{{$key}}').disabled = true;--}}
                                    {{--                                   parent_base_id_work = document.getElementById('link{{$key}}').disabled = false;--}}
                                    disabled
                                @endif
                                @endif
                                @endif
                                @endif
                            >
                                @if($item_tree)
                                    <option value="{{$item_tree->id}}"
                                    >{{$item_tree->name()}}
                                    </option>
                                @else
                                    @if ((count($its_list) == 0))
                                        @if($link->parent_base->is_view_empty_lst)
                                            {{--                                            @if(!$link->parent_base->is_required_lst_num_str_txt_img_doc)--}}
                                            {{--                                            @if($base_link_right['is_base_required'] == false)--}}
                                            <option value='0'>{{GlobalController::option_empty()}}</option>
                                        @else
                                            <option value='0'>{{trans('main.no_information_on')}}
                                                "{{$result_parent_label}}"!
                                            </option>
                                        @endif
                                    @else
                                        {{-- Чтобы не выводить лишний раз ненужное --}}
                                        @if($ing_filter == false)
                                            @if($link->parent_base->is_view_empty_lst)
                                                {{--                                            @if(!$link->parent_base->is_required_lst_num_str_txt_img_doc)--}}
                                                {{--                                            @if($base_link_right['is_base_required'] == false)--}}
                                                <option value='0'>{{GlobalController::option_empty()}}</option>
                                            @endif
                                        @endif
                                        @foreach ($its_list as $item_work)
                                            <option value="{{$item_work->id}}"
                                                    @if (((old($key)) ?? (($value != null) ? $value : 0)) == $item_work->id)
                                                    selected
                                                @endif
                                            >
                                                <?php
                                                //                                                echo $item_work->name();
                                                //                                                ?>
                                                {{$item_work->name()}}
                                                @include('layouts.item.show_history',['item'=>$item_work])
                                            </option>
                                        @endforeach
                                    @endif
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
                        {{--                        <div class="col-sm-2">--}}
                        {{--                        </div>--}}
                        {{-- Похожие проверка вверху--}}
                        {{-- 'is_edit_link_read' - 'Чтение Связи в форме'--}}
                        @if($base_link_right['is_edit_link_read'] == false)
                            {{--                            @if($link->parent_is_numcalc == true)--}}
                            {{-- Похожие по смыслу проверки "@if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true && $link->parent_is_nc_viewonly==false)" в этом файле восемь раз --}}
                            @if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true && $link->parent_is_nc_viewonly==false)
                                <div class="col-sm-1">
                                    {{--                                    Не удалять--}}
                                    {{--                                    <input type="button" value="..." title="{{trans('main.calculate')}}"--}}
                                    {{--                                           name="button_nc{{$key}}"--}}
                                    {{--                                           id="button_nc{{$key}}"--}}
                                    {{--                                    >--}}
                                    <button type="button" title="{{trans('main.calculate')}} {{$result_parent_label}}"
                                            name="button_nc{{$key}}"
                                            id="button_nc{{$key}}"
                                            class="text-label">
                                        <i class="fas fa-calculator d-inline"></i>
                                    </button>
                                </div>
                                <div class="col-sm-1">
                                <span class="form-label text-danger"
                                      name="name{{$key}}"
                                      id="name{{$key}}"></span>
                                </div>
                            @else
                                <div class="col-sm-2">
                                </div>
                            @endif
                        @endif
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
                @if ($allcalc_button)
                    <button type="button" class="btn btn-dreamer" title="{{trans('main.calculate_all')}}"
                            onclick="javascript:on_numcalc_noviewonly();"
                    >
                        <i class="fas fa-calculator d-inline"></i>
                        {{trans('main.calculate_all')}}
                    </button>
                @endif
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
    {{--    Не удалять--}}
    {{--        https://stackoverflow.com/questions/16852484/use-fieldset-legend-with-bootstrap--}}
    {{--    <fieldset class="border p-2">--}}
    {{--        <legend class="w-auto">Your Legend</legend>--}}
    {{--        <input type="checkbox"> создание пунктуальности (никогда не--}}
    {{--        будете никуда опаздывать);<br>--}}
    {{--        <input type="checkbox"> излечение от пунктуальности (никогда--}}
    {{--        никуда не будете торопиться);<br>--}}
    {{--        <input type="checkbox"> изменение восприятия времени и часов.--}}
    {{--        <p><input type="submit"></p>--}}
    {{--    </fieldset>--}}
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
    $functs_numcalc_noviewonly = array();
    $functs_numcalc_viewonly = array();
    $functs_change = array();

    // В этом массиве хранятся функции, которые выводят наименования вычисляемых полей
    // ($link->parent_is_parent_related == true)
    // в зависимости от поля, где вводится код какого-то справочника
    $functs_parent_refer = array();

    ?>
    {{--<script>--}}
    {{--    window.onload = function () {--}}
    {{--        // массив функций нужен, что при window.onload запустить обработчики всех полей--}}
    {{--                        @foreach($functions as $value)--}}
    {{--                            {{$value}}(true);--}}
    {{--                        @endforeach--}}

    {{--            // Не нужно вызывать функцию on_calc(),--}}
    {{--            // это связано с разрешенной корректировкой вычисляемых полей ($link->parent_is_nc_viewonly)--}}
    {{--            // on_numcalc_noviewonly();--}}
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
        // Находим $relip_link_project
        $relip_link_project = GlobalController::calc_link_project($link, $relip_project);
        // Вычисляет $relit_id
        //$calc_link_relit_id = GlobalController::calc_link_relit_id($link, $role, $relit_id);
        //$base_link_right = GlobalController::base_link_right($link, $role, $calc_link_relit_id);
        $base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
        ?>
        @if($base_link_right['is_edit_link_enable'] == false)
            <?php
            // Удаление из массива $array_disabled,
            // Нужно, чтобы не было ошибок в команде JavaScript "document.getElementById('link{{$key}}').disabled = false;"
            if (array_key_exists($key, $array_disabled)) {
                unset($array_disabled[$key]);
            }
            ?>
            @continue
        @endif
        <?php
        $prefix = '1_';
        ?>
        {{--        Вводить как справочник--}}
        @if($link->parent_base->is_code_needed == true && $link->parent_is_enter_refer == true)
            @if($link->parent_base->is_code_number == true && $link->parent_base->is_limit_sign_code == true && $link->parent_base->is_code_zeros == true  && $link->parent_base->significance_code > 0)
                <script>
                    var code_{{$prefix}}{{$link->id}} = document.getElementById('code{{$link->id}}');
                    <?php
                    $functions[] = "code_change_" . $prefix . $link->id;
                    ?>
                    function code_change_{{$prefix}}{{$link->id}}() {
                        numStr = code_{{$prefix}}{{$link->id}}.value;
                        numDigits = {{$link->parent_base->significance_code}};
                        code_{{$prefix}}{{$link->id}}.value = numDigits >= numStr.length ? Array.apply(null, {length: numDigits - numStr.length + 1}).join("0") + numStr : numStr.substring(0, numDigits);
                        {{-- http://javascript.ru/forum/events/76761-programmno-vyzvat-sobytie-change.html#post503465--}}
                        {{-- вызываем состояние "элемент изменился", в связи с этим запустятся функции - обработчики "change"--}}
                        {{--code_{{$prefix}}{{$link->id}}.dispatchEvent(new Event('input'));--}}
                        {{--alert('code_change_ code_{{$prefix}}{{$link->id}}.value = ' + code_{{$prefix}}{{$link->id}}.value);--}}

                    }

                    code_{{$prefix}}{{$link->id}}.addEventListener("change", code_change_{{$prefix}}{{$link->id}});
                    <?php
                    $functs_change['code' . $link->id] = 1;
                    ?>
                </script>
            @endif
        @endif

        <?php
        // похожие строки ниже
        $prefix = '';
        $link_parent_base = $link->parent_base;
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
                // не удалять
                //$link_id_selection_calc = LinkController::get_link_id_selection_calc($link);
            }
            // эта проверка не нужна
            //if (!array_key_exists($key, $array_disabled)) {
            //          Проверка на фильтрируемые поля ('parent_is_child_related')
            if ($link->parent_is_child_related == true) {
                $lres = LinkController::get_link_ids_from_calc_link($link);
                $const_link_id_start = $lres['const_link_id_start'];
                $const_link_start = $lres['const_link_start'];
                $link_start_child = Link::find($link->parent_child_related_start_link_id);
                $link_result_child = Link::find($link->parent_child_related_result_link_id);
                $link_selection_table = ItemController::get_link_refer_main($link_parent_base, $link);
                $prefix = '3_';
            }
            //}
            //          Проверка на вычисляемые поля ('Автоматически заполнять из родительского поля ввода')
            if ($link->parent_is_parent_related == true) {
                $lres = LinkController::get_link_ids_from_calc_link($link);
                $const_link_id_start = $lres['const_link_id_start'];
                $const_link_start = $lres['const_link_start'];
                $link_parent = Link::find($link->parent_parent_related_start_link_id);
                $prefix = '4_';
            }
            // Выводить поле вычисляемой таблицы
            if ($link->parent_is_output_calculated_table_field == true) {
                // Проверка, если ли только type_is_list()
                $sets_edit_group = ItemController::get_sets_list_group($base, $link);
                // Проверка нужна
                //if ($sets_group) {
                if ($sets_edit_group == true) {
                    $link_calculated_table = true;
                    $sets_group = ItemController::get_sets_group($base, $link);
                }
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
                {{--                @if(($link_start_child->parent_is_base_link == true) || ($link_start_child->parent_base->is_code_needed==true && $link_start_child->parent_is_enter_refer==true))--}}
                @if($link_start_child->parent_base->is_code_needed==true && $link_start_child->parent_is_enter_refer==true)
                var child_base_id{{$prefix}}{{$link->id}} = document.getElementById('{{$link_start_child->id}}');
                @else
                var child_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$link_start_child->id}}');
                @endif
                {{--var child_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$link_start_child->id}}');--}}

                {{--                @if(($link->parent_is_base_link == true) || ($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true))--}}
                @if($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true)
                var parent_base_id{{$prefix}}{{$link->id}} = document.getElementById('{{$link->id}}');
                @else
                var parent_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');

                @endif

                {{--var parent_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');--}}
                <?php
                // Эта проверка if() нужна, для правильного отображения значений
                //                'parent_is_numcalc' => 'Вычислять значение для числового поля, логического поля, списка',
                //                'parent_is_nc_screencalc' => 'Экранное вычисление',
                //                'parent_is_nc_viewonly' => 'Расчитанное значение только показывать',
                if (!($link->parent_is_numcalc == true & $link->parent_is_nc_screencalc == true & $link->parent_is_nc_viewonly == true)) {
                    $functions[] = "link_id_changeOption_" . $prefix . $link->id;
                }
                $link_get = null;
                // 1.0 В списке выбора использовать поле вычисляемой таблицы
                if ($link_selection_table) {
                    $link_get = $link->id;
                } // Обычное разделение на фильтруемые поля
                else {
                    $link_get = $link_start_child;
                }
                ?>
                {{-- async - await нужно, https://tproger.ru/translations/understanding-async-await-in-javascript/--}}
                async function link_id_changeOption_{{$prefix}}{{$link->id}}() {
                    {{--                    @if(($link->parent_is_base_link == true) || ($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true))--}}
                        {{--                    if (parent_base_id{{$prefix}}{{$link->id}}.value == 0) {--}}
                        {{--                        @else--}}
                        {{--                        if (parent_base_id{{$prefix}}{{$link->id}}.options[parent_base_id{{$prefix}}{{$link->id}}.selectedIndex].value == 0) {--}}
                        {{--                            @endif--}}
                        {{--                                --}}{{--                                @if(!$link->child_base->is_required_lst_num_str_txt_img_doc)--}}
                        {{--                                @if($base_link_right['is_base_required'] == false)--}}
                        {{--                                child_base_id{{$prefix}}{{$link->id}}.innerHTML = "<option value='0'>{{GlobalController::option_empty()}}</option>";--}}
                        {{--                            @else--}}
                        {{--                                child_base_id{{$prefix}}{{$link->id}}.innerHTML = "<option value='0'>{{trans('main.no_information') . '!'}}</option>";--}}
                        {{--                            @endif--}}
                        {{--                        } else {--}}
                        {{-- Здесь правильные проверки на условия--}}
                        @if(($link_start_child->parent_is_base_link == true) || ($link_start_child->parent_base->is_code_needed==true && $link_start_child->parent_is_enter_refer==true))
                        @else
                        await axios.get('/item/get_items_main_options/'
                        + '{{$link_start_child->parent_base_id}}' + '/' + {{$project->id}} + '/' + {{$role->id}} + '/' + {{$relit_id}} + '/' + {{$link_get->id}}
                            @if(($link->parent_is_base_link == true) || ($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true))
                        + '/' + parent_base_id{{$prefix}}{{$link->id}}.value
                        @else
                        + '/' + parent_base_id{{$prefix}}{{$link->id}}.options[parent_base_id{{$prefix}}{{$link->id}}.selectedIndex].value
                        @endif
                        {{--                    @if($par_link & $parent_item) - так не использовать (дает ошибку) --}}
                        @if($par_link && $parent_item)
                        + '/' + {{$par_link->id}} + '/' + {{$parent_item->id}}
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
                    {{-- }{{--
                    {{-- http://javascript.ru/forum/events/76761-programmno-vyzvat-sobytie-change.html#post503465{{--
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
                <?php
                $functs_change['link' . $link->id] = 1;
                ?>
                @endif

            </script>
        @endif

        <?php
        $prefix = '7_';
        ?>
        {{--        Вводить как справочник--}}
        @if($link_enter_refer)
            {{-- Проверка на фильтрируемые поля--}}
            @if($link_refer_main)
                <script>
                    var code_{{$prefix}}{{$link->id}} = document.getElementById('code{{$link->id}}');
                    var name_{{$prefix}}{{$link->id}} = document.getElementById('name{{$link->id}}');
                    var key_{{$prefix}}{{$link->id}} = document.getElementById('{{$link->id}}');

                    var child_base_id{{$prefix}}{{$link->id}} = document.getElementById('buttonbrow{{$link->id}}');

                    {{--                    @if(($link_refer_main->parent_is_base_link == true) || ($link_refer_main->parent_base->is_code_needed==true && $link_refer_main->parent_is_enter_refer==true))--}}
                    @if($link_refer_main->parent_base->is_code_needed==true && $link_refer_main->parent_is_enter_refer==true)
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
                        {{-- Здесь правильные проверки на условия--}}
                            @if(($link_refer_main->parent_is_base_link == true) || ($link_refer_main->parent_base->is_code_needed==true && $link_refer_main->parent_is_enter_refer==true))
                        if (parent_base_id{{$prefix}}{{$link->id}}.value == 0) {
                            @else
                            if (parent_base_id{{$prefix}}{{$link->id}}.options[parent_base_id{{$prefix}}{{$link->id}}.selectedIndex].value == 0) {
                                @endif
                                    window.item_id.value = 0;
                                window.item_code.value = "";
                                window.item_name.innerHTML = "";
                                {{-- Нужно, не удалять--}}
                                alert("{{trans('main.select_a_field_to_filter') . '!'}}");
                            } else {
                                open('{{route('item.browser', '')}}' + '/' + {{$link->id}} + '/' + {{$project->id}} + '/' + {{$role->id}} + '/' + {{$relit_id}}
                                        @if(($link_refer_main->parent_is_base_link == true) || ($link_refer_main->parent_base->is_code_needed==true && $link_refer_main->parent_is_enter_refer==true))
                                    + '/' + parent_base_id{{$prefix}}{{$link->id}}.value
                                    @else
                                    + '/' + parent_base_id{{$prefix}}{{$link->id}}.options[parent_base_id{{$prefix}}{{$link->id}}.selectedIndex].value
                                    @endif
                                    + '/code/code', 'browse', 'width=850, height=800');
                            }
                            {{-- Вызываем событие - обновление кода--}}
                            document.getElementById('code{{$link->id}}').dispatchEvent(new Event('change'));
                        }

                        {{-- Событие на кнопку "..."--}}
                        child_base_id{{$prefix}}{{$link->id}}.addEventListener("click", link_id_changeOption_{{$prefix}}{{$link->id}});

                        <?php
                        $functions[] = "code_input_" . $prefix . $link->id;
                        //$functs_parent_refer[] = "code_input_" . $prefix . $link->id;
                        ?>
                        {{-- async - await нужно, https://tproger.ru/translations/understanding-async-await-in-javascript/--}}
                        async function code_input_{{$prefix}}{{$link->id}}() {
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
                                        + '{{$link->parent_base_id}}' + '/' + {{$project->id}} + '/' + {{$role->id}} + '/' + {{$relit_id}} + '/' + {{$link->id}}
                                            @if(($link_refer_main->parent_is_base_link == true) || ($link_refer_main->parent_base->is_code_needed==true && $link_refer_main->parent_is_enter_refer==true))
                                        + '/' + parent_base_id{{$prefix}}{{$link->id}}.value
                                        @else
                                        + '/' + parent_base_id{{$prefix}}{{$link->id}}.options[parent_base_id{{$prefix}}{{$link->id}}.selectedIndex].value
                                        @endif
                                    ).then(function (res) {
                                                name_{{$prefix}}{{$link->id}}.innerHTML = res.data['item_name'];
                                                key_{{$prefix}}{{$link->id}}.value = res.data['item_id'];
                                                @if($link->parent_is_nc_parameter == true)
                                                on_numcalc_viewonly();
                                                @endif
                                            }
                                        );

                                    {{--Команда "on_parent_refer();" нужна, для вызова функция обновления данных с зависимых таблиц--}}
                                    {{--Функция code_input_{{$prefix}}{{$link->id}}(first) выполняется не сразу--}}
                                    {{--Не использовать проверку if (first == false) --}}
                                    {{--if (first == false) --}}

                                    {{--*** on_parent_refer(); ***--}}

                                    {{--}--}}
                                    {{--on_numcalc_viewonly(); --}}

                                    {{-- ? --}}
                                    {{--link_id_changeOption_{{$prefix_prev}}{{$link->id}}();--}}

                                    {{-- http://javascript.ru/forum/events/76761-programmno-vyzvat-sobytie-change.html#post503465--}}
                                    {{-- вызываем состояние "элемент изменился", в связи с этим запустятся функции - обработчики "change"--}}
                                }
                            }
                            code_{{$prefix}}{{$link->id}}.addEventListener("change", code_input_{{$prefix}}{{$link->id}});
                    <?php
                    $functs_change['code' . $link->id] = 1;
                    ?>

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
                        open('{{route('item.browser', '')}}' + '/' + {{$link->id}} + '/' + {{$project->id}} + '/' + {{$role->id}} + '/' + {{$relit_id}}
                            , 'browse', 'width=850, height=800');
                    }

                    {{--Событие на кнопку "..."--}}
                    child_base_id{{$prefix}}{{$link->id}}.addEventListener("click", link_id_changeOption_{{$prefix}}{{$link->id}});
                    <?php
                    $functions[] = "code_input_" . $prefix . $link->id;
                    //$functs_parent_refer[] = "code_input_" . $prefix . $link->id;
                    ?>
                    // Если у элемента code несколько функций-обработчиков,
                    // то команды ниже "async function code_input" выполняются последними
                    {{--async - await нужно, https://tproger.ru/translations/understanding-async-await-in-javascript/--}}
                    {{--async function code_input_{{$prefix}}{{$link->id}}() {--}}
                    {{--    await axios.get('/item/item_from_base_code/'--}}
                    {{--        + '{{$link->parent_base_id}}'--}}
                    {{--        + '/' + '{{$relip_link_project->id}}'--}}
                    {{--        + '/' + code_{{$prefix}}{{$link->id}}.value--}}
                    {{--    ).then(function (res) {--}}
                    {{--            --}}{{--code_{{$prefix}}{{$link->id}}.innerHTML = res.data['item_code'];--}}
                    {{--                name_{{$prefix}}{{$link->id}}.innerHTML = res.data['item_name'];--}}
                    {{--            key_{{$prefix}}{{$link->id}}.value = res.data['item_id'];--}}
                    {{--        }--}}
                    {{--    );--}}
                    function code_input_{{$prefix}}{{$link->id}}() {
                        axios.get('/item/item_from_base_code/'
                            + '{{$link->parent_base_id}}'
                            + '/' + '{{$relip_link_project->id}}'
                            + '/' + code_{{$prefix}}{{$link->id}}.value
                        ).then(function (res) {
                                {{--code_{{$prefix}}{{$link->id}}.innerHTML = res.data['item_code'];--}}
                                    name_{{$prefix}}{{$link->id}}.innerHTML = res.data['item_name'];
                                key_{{$prefix}}{{$link->id}}.value = res.data['item_id'];
                                {{-- on_parent_refer();--}}
                                @if($link->parent_is_nc_parameter == true)
                                on_numcalc_viewonly();
                                @endif
                            }
                        );

                        {{--Команда "on_parent_refer();" нужна, для вызова функция обновления данных с зависимых таблиц--}}
                        {{--Функция code_input_{{$prefix}}{{$link->id}}(first) выполняется не сразу--}}
                        {{--Не использовать проверку if (first == false) --}}
                        {{--if (first == false) --}}

                        {{-- Нужно использовать, см.примечание к on_parent_refer() ниже --}}
                        {{-- Нужно, например, для правильного отображения данных при добавлении/корректировке записей в шаблоне "Интернет - магазин" --}}
                        {{-- on_parent_refer(); --}}

                        {{--}--}}
                        {{--on_numcalc_viewonly(); --}}

                        {{-- ? --}}
                        {{--link_id_changeOption_{{$prefix_prev}}{{$link->id}}();--}}

                        {{-- http://javascript.ru/forum/events/76761-programmno-vyzvat-sobytie-change.html#post503465--}}
                        {{-- вызываем состояние "элемент изменился", в связи с этим запустятся функции - обработчики "change"--}}
                        {{--document.getElementById('code{{$link->id}}').dispatchEvent(new Event('change'));--}}
                    }

                    {{--                    code_{{$prefix}}{{$link->id}}.addEventListener("input", code_input_{{$prefix}}{{$link->id}});--}}
                    code_{{$prefix}}{{$link->id}}.addEventListener("change", code_input_{{$prefix}}{{$link->id}});
                    <?php
                    $functs_change['code' . $link->id] = 1;
                    ?>

                </script>
            @endif
        @endif

        {{--Проверка на вычисляемые поля ('Автоматически заполнять из родительского поля ввода')--}}
        @if($link_parent)
            <script>
                @if($const_link_start->parent_base->is_code_needed==true && $const_link_start->parent_is_enter_refer == true)
                var child_base_id{{$prefix}}{{$link->id}} = document.getElementById('{{$const_link_id_start}}');
                var child_code_id{{$prefix}}{{$link->id}} = document.getElementById('code{{$const_link_id_start}}');
                var parent_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');
                {{--                var parent_related_id{{$prefix}}{{$link->id}} = document.getElementById('related_id{{$link->id}}');--}}
                {{-- Такая проверка на '$link->parent_base->type_is_image()/!$link->parent_base->type_is_image()' в трех местах в этом файле--}}
                @if(!$link->parent_base->type_is_image())
                var parent_related_id{{$prefix}}{{$link->id}} = document.getElementById('{{$link->id}}');
                var parent_unit_id{{$prefix}}{{$link->id}} = document.getElementById('unit{{$link->id}}');
                @endif
                <?php
                // Нужно использовать, см.примечание к on_parent_refer() ниже
                // $functs_parent_refer[] = "link_id_change_" . $prefix . $link->id;
                // $functions[] = "link_id_change_" . $prefix . $link->id;
                ?>
                function link_id_change_{{$prefix}}{{$link->id}}() {
                    {{--alert('{{$link->id}}->>>' + child_base_id{{$prefix}}{{$link->id}}.value + 'child_code_id{{$link->id}}->>>' + child_code_id{{$prefix}}{{$link->id}}.value);--}}
                        {{--                    if (child_base_id{{$prefix}}{{$link->id}}.value == 0) {--}}
                        {{--Использовать "if (child_code_id{{$prefix}}{{$link->id}}.value == 0)"--}}
                    if (child_code_id{{$prefix}}{{$link->id}}.value == 0) {
                        parent_base_id{{$prefix}}{{$link->id}}.innerHTML = "{{trans('main.no_information') . '!'}}";
                        {{-- Такая проверка на '$link->parent_base->type_is_image()/!$link->parent_base->type_is_image()' в трех местах в этом файле--}}
                            @if(!$link->parent_base->type_is_image())
                            parent_related_id{{$prefix}}{{$link->id}}.innerHTML = "0";
                        parent_unit_id{{$prefix}}{{$link->id}}.innerHTML = "";
                        @endif
                        {{--Не использовать проверку if (first == false) {--}}
                        {{--if (first == false) {--}}
                        @if($link->parent_is_nc_parameter == true)
                        on_numcalc_viewonly();
                        <?php
                        // Отключено
                        //echo StepController::steps_javascript_code($link, 'link_id_changeOption');
                        ?>
                        @endif
                        {{--}--}}
                    } else {
                        axios.get('/item/get_parent_item_from_calc_child_code/'
                            + '{{$link_parent->parent_base_id}}'
                            + '/' + '{{$project->id}}'
                            + '/' + child_code_id{{$prefix}}{{$link->id}}.value
                            + '/{{$link->id}}'
                            + '/0'
                        ).then(function (res) {
                                parent_base_id{{$prefix}}{{$link->id}}.innerHTML = res.data['result_item_name'];
                                {{-- Такая проверка на '$link->parent_base->type_is_image()/!$link->parent_base->type_is_image()' в трех местах в этом файле--}}
                                    @if(!$link->parent_base->type_is_image())
                                    {{-- "related_id" используется несколько раз по тексту --}}
                                    parent_related_id{{$prefix}}{{$link->id}}.innerHTML = res.data['result_item_id'];
                                parent_unit_id{{$prefix}}{{$link->id}}.innerHTML = res.data['result_unit_name'];
                                {{--                                alert('{{$link->id}}-> ' + child_code_id{{$prefix}}{{$link->id}}.value + ' '+ child_base_id{{$prefix}}{{$link->id}}.value + ' 223-> ' + res.data['result_item_id'] + '-' + parent_related_id{{$prefix}}{{$link->id}}.innerHTML + ' ' + res.data['result_item_name'] + '-' + parent_base_id{{$prefix}}{{$link->id}}.innerHTML);--}}
                                @else
                                {{--                                alert('{{$link->id}}-> ' + child_code_id{{$prefix}}{{$link->id}}.value + ' ' + child_base_id{{$prefix}}{{$link->id}}.value + ' 223-> ' + res.data['result_item_name'] + '-' + parent_base_id{{$prefix}}{{$link->id}}.innerHTML);--}}
                                @endif
                                {{-- Нужно использовать, см.примечание к on_parent_refer() ниже --}}
                                {{-- Нужно, например, для правильного отображения данных при добавлении/корректировке записей в шаблоне "Интернет - магазин" --}}
                                {{-- on_parent_refer();--}}
                                {{-- 11111 --}}
                                {{--Не использовать проверку if (first == false) {--}}
                                {{--if (first == false) {--}}
                                @if($link->parent_is_nc_parameter == true)
                                on_numcalc_viewonly();
                                <?php
                                // Отключено
                                //echo StepController::steps_javascript_code($link, 'link_id_changeOption');
                                ?>
                                @endif
                                {{--}--}}
                                {{--                                @if(!$update & $link->parent_is_nc_parameter == true)--}}
                                {{--    arr = res.data;--}}
                                {{--for (key in arr) {--}}
                                {{--    // console.log(`${key} = ${arr[key]}`);--}}
                                {{--    alert('link_id = {{$link->id}} key = ' + key + ' value = ' + arr[key]);--}}
                                {{--}--}}
                            }
                        );
                        {{--При просмотре фото может неправильно работать при просмотре фото по связанному полю - проэтому закомментарено --}}
                        {{--вызываем состояние "элемент изменился", в связи с этим запустятся функции - обработчики "change" --}}
                        {{--child_code_id{{$prefix}}{{$link->id}}.dispatchEvent(new Event('input')); --}}
                    }
                    {{--Так не работает--}}
                    {{--on_numcalc_viewonly();--}}
                }

                {{-- 222 --}}
                {{--Эта команда не нужна/нужна --}}
                child_code_id{{$prefix}}{{$link->id}}.addEventListener("change", link_id_change_{{$prefix}}{{$link->id}});
                @elseif($const_link_start->parent_base->type_is_list())
                <?php
                // Проверка на вычисляемые поля ('Автоматически заполнять из родительского поля ввода')
                $link_related_start = Link::find($link->parent_parent_related_start_link_id);
                $link_related_calculated = false;
                if ($link_related_start) {
                    // Проверка на 'Выводить поле вычисляемой таблицы'
                    $link_related_calculated = $link_related_start->parent_is_output_calculated_table_field;
                }
                ?>
                @if($link_related_calculated)
                var child_base_id{{$prefix}}{{$link->id}} = document.getElementById('{{$const_link_id_start}}');
                @else
                var child_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$const_link_id_start}}');
                @endif
                var parent_base_id{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');
                var parent_unit_id{{$prefix}}{{$link->id}} = document.getElementById('unit{{$link->id}}');
                {{-- "related_id" используется несколько раз по тексту --}}
                {{--                var parent_related_id{{$prefix}}{{$link->id}} = document.getElementById('related_id{{$link->id}}');--}}
                var parent_related_id{{$prefix}}{{$link->id}} = document.getElementById('{{$link->id}}');

                <?php
                // Не нужно использовать
                // $functions[] = "link_id_changeOption_" . $prefix . $link->id;
                // $functs_parent_refer[] = "link_id_changeOption_" . $prefix . $link->id;
                ?>
                function link_id_changeOption_{{$prefix}}{{$link->id}}() {
                    @if($link_related_calculated)
                    if (child_base_id{{$prefix}}{{$link->id}}.innerHTML == 0) {
                        @else
                        if (child_base_id{{$prefix}}{{$link->id}}.options[child_base_id{{$prefix}}{{$link->id}}.selectedIndex].value == 0) {
                            @endif
                                parent_base_id{{$prefix}}{{$link->id}}.innerHTML = "{{trans('main.no_information') . '!'}}";
                            parent_related_id{{$prefix}}{{$link->id}}.innerHTML = "0";
                            {{--                                @if(!$update & $link->parent_is_nc_parameter == true)--}}
                            {{--Не использовать проверку if (first == false) {--}}
                            {{--if (first == false) {--}}
                            @if($link->parent_is_nc_parameter == true)
                            on_numcalc_viewonly();
                            <?php
                            // Отключено
                            //echo StepController::steps_javascript_code($link, 'link_id_changeOption');
                            ?>
                            @endif
                            {{--}--}}
                        } else {
                            axios.get('/item/get_parent_item_from_calc_child_item/'
                                @if($link_related_calculated)
                                + child_base_id{{$prefix}}{{$link->id}}.innerHTML
                                @else
                                + child_base_id{{$prefix}}{{$link->id}}.options[child_base_id{{$prefix}}{{$link->id}}.selectedIndex].value
                                @endif
                                + '/{{$link->id}}'
                                + '/0'
                            ).then(function (res) {
                                    parent_base_id{{$prefix}}{{$link->id}}.innerHTML = res.data['result_item_name'];
                                    parent_unit_id{{$prefix}}{{$link->id}}.innerHTML = res.data['result_unit_name'];
                                    {{-- "related_id" используется несколько раз по тексту --}}
                                        parent_related_id{{$prefix}}{{$link->id}}.innerHTML = res.data['result_item_id'];
                                    {{--                                @if(!$update & $link->parent_is_nc_parameter == true)--}}
                                    {{--Не использовать проверку if (first == false) {--}}
                                    {{--if (first == false)--}}
                                    @if($link->parent_is_nc_parameter == true)
                                    on_numcalc_viewonly();
                                    <?php
                                    // Отключено
                                    //echo StepController::steps_javascript_code($link, 'link_id_changeOption');
                                    ?>
                                    @endif
                                    {{--}--}}
                                }
                            );
                        }
                        {{--Так не работает--}}
                        {{--on_numcalc_viewonly();--}}

                        {{--Не использовать, работает неправильно--}}
                        {{--@if($link->parent_is_nc_parameter == true)--}}
                        {{--<?php--}}
                        {{--echo StepController::steps_javascript_code($link, 'link_id_changeOption');--}}
                        {{--?>--}}
                        {{--@endif--}}
                    }
                    {{--'@if(!$link_related_calculated)' используется--}}
                    {{--                    @if(!$link_related_calculated)--}}
                    // Нужно
                    child_base_id{{$prefix}}{{$link->id}}.addEventListener("change", link_id_changeOption_{{$prefix}}{{$link->id}});

                // Нужно
                <?php
                $functs_change['link' . $const_link_id_start] = 1;
                //$functs_change[$const_link_id_start] = 1;
                ?>

                {{--                @endif--}}

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

                var output_calc_id{{$prefix}}{{$link->id}} = document.getElementById('{{$link->id}}');
                var output_calc_inner{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');
                var parent_unit_id{{$prefix}}{{$link->id}} = document.getElementById('unit{{$link->id}}');

                <?php
                // Нужно
                $functs_parent_refer[] = "link_id_changeOption_" . $prefix . $link->id;
                //$functions[] = "link_id_changeOption_" . $prefix . $link->id;
                ?>
                function link_id_changeOption_{{$prefix}}{{$link->id}}() {
                    {{--if (child_base_id{{$prefix}}{{$link->id}}.options[child_base_id{{$prefix}}{{$link->id}}.selectedIndex].value == 0) {--}}
                    {{--    parent_base_id{{$prefix}}{{$link->id}}.innerHTML = "{{trans('main.no_information') . '!'}}";--}}
                    {{--} else {--}}
                    axios.get('/item/get_parent_item_from_output_calculated_table?'
                        + 'project_id={{$project->id}}'
                        + '&base_id={{$base->id}}'
                        + '&link_id={{$link->id}}'
                        {{-- Такой вариант рабочий, правильный, когда '&items_id_group[]=' в каждой итерации цикла используется --}}
                        @foreach($sets_group as $to_key => $to_value)
                        {{-- Если $to_value->link_from->Ссылка на основу = true --}}
                        {{-- Выше по тексту тоже используется "parent_is_base_link"--}}
                        @if(($to_value->link_from->parent_is_base_link == true) || ($to_value->link_from->parent_base->is_code_needed==true && $to_value->link_from->parent_is_enter_refer==true))
                        {{-- 'Ссылка на основу (кроме вычисляемых основ - Ребенок_Основа)' --}}
                        @if($to_value->link_from->parent_is_base_link == true)
                        + '&code_group[]=0'
                        + '&items_id_group[]=' + child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}}.value
                        {{-- Передается код --}}
                        @else
                        + '&code_group[]=' + code_child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}}.value
                        + '&items_id_group[]=0'
                        @endif
                        {{-- Передается $item->id --}}
                        @else
                        + '&code_group[]=0'
                        + '&items_id_group[]=' + child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}}.options[child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}}.selectedIndex].value
                        @endif
                        @endforeach
                        ).then(function (res) {
                                output_calc_id{{$prefix}}{{$link->id}}.innerHTML = res.data['id'];
                                parent_unit_id{{$prefix}}{{$link->id}}.innerHTML = res.data['unitname'];
                                output_calc_inner{{$prefix}}{{$link->id}}.innerHTML = res.data['inner'];
                                {{-- Не нужно, т.к. это блок для вывода вычисляемых полей, а не для корректировки--}}
                                {{--                                <?php--}}
                                {{--                                    $links_related_start = GlobalController::links_related_start($base, $link)--}}
                                {{--                                    ?>--}}
                                {{--                                    --}}{{-- https://ru.stackoverflow.com/questions/240856/%D0%9A%D0%B0%D0%BA-%D0%BC%D0%BE%D0%B6%D0%BD%D0%BE-%D0%BF%D1%80%D0%BE%D0%B2%D0%B5%D1%80%D0%B8%D1%82%D1%8C-%D1%81%D1%83%D1%89%D0%B5%D1%81%D1%82%D0%B2%D1%83%D0%B5%D1%82-%D0%BB%D0%B8-%D1%84%D1%83%D0%BD%D0%BA%D1%86%D0%B8%D1%8F-%D0%B2-js--}}
                                {{--                                    @foreach($links_related_start as $rel_st_value)--}}
                                {{--                                if (typeof (link_id_changeOption_7_{{$rel_st_value->id}}) === "function") {--}}
                                {{--                                    link_id_changeOption_7_{{$rel_st_value->id}}();--}}
                                {{--                                }--}}
                                {{--                                @endforeach--}}
                                @if($link->parent_is_nc_parameter == true)
                                on_numcalc_viewonly();
                                @endif
                            }
                        );
                }

                @foreach($sets_group as $to_key => $to_value)
                @if($to_value->link_from->parent_base->is_code_needed==true && $to_value->link_from->parent_is_enter_refer==true)
                code_child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}}.addEventListener("change", link_id_changeOption_{{$prefix}}{{$link->id}});
                <?php
                $functs_change['code' . $to_value->link_from_id] = 1;
                ?>
                @else
                child_base_id{{$prefix}}{{$link->id}}_{{$to_value->id}}.addEventListener("change", link_id_changeOption_{{$prefix}}{{$link->id}});
                <?php
                $functs_change['link' . $to_value->link_from_id] = 1;
                ?>
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
                {{--Не удалять комментарий (для информации):--}}
                {{--См. условие '@if($link->parent_is_parent_related == true & $link->parent_base->type_is_list())'--}}
                {{--в ext_edit.php и StepController::steps_javascript_code()--}}
                {{--            @if($link->parent_is_parent_related == true & $link->parent_base->type_is_list())--}}

                {{--                @if($link->parent_is_parent_related == true & ($link->parent_base->type_is_list() | $link->parent_base->type_is_string() | $link->parent_base->type_is_number() | $link->parent_base->type_is_boolean()))--}}
                {{-- "related_id" используется несколько раз по тексту --}}
                {{--var nc_parameter_{{$prefix}}{{$link->id}} = document.getElementById('related_id{{$link->id}}');--}}
                var nc_param_id_{{$prefix}}{{$link->id}} = document.getElementById('{{$link->id}}');
                {{--                @endif--}}

                {{--                @else--}}
                var nc_parameter_{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');
                @if ($link->parent_base->type_is_string() & $link->parent_base->is_one_value_lst_str_txt == false)
                <?php
                $i = 0;
                ?>
                @foreach (config('app.locales') as $lang_key => $lang_value)
                {{-- Начиная со второго(индекс==1) элемента массива языков сохранять--}}
                @if ($i > 0)
                var nc_parameter_{{$prefix}}{{$link->id}}_{{$lang_key}} = document.getElementById('link{{$link->id}}_{{$lang_key}}');
                @endif
                <?php
                $i = $i + 1;
                ?>
                @endforeach
                @endif
                {{--                @endif--}}
            </script>
        @endif

    @endforeach
    <script>
        @foreach($array_calc as $key=>$value)
        <?php
        $link = Link::find($key);
        // Вычисляет $relit_id
        //$calc_link_relit_id = GlobalController::calc_link_relit_id($link, $role, $relit_id);
        //$base_link_right = GlobalController::base_link_right($link, $role, $calc_link_relit_id);
        $base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
        // Префикс "5_" д.б. одинаков в StepController::steps_javascript_code() и в item\ext_edit.php
        $prefix = '5_';
        ?>
        {{-- Похожая проверка вверху--}}
        {{-- Кроме ($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true)--}}
        @if(!($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true))
        {{-- 'is_edit_link_read' - 'Чтение Связи в форме', 'is_edit_link_update' - 'Корректировка Связи в форме' --}}
        @if($base_link_right['is_edit_link_read'] == false & $base_link_right['is_edit_link_update'] == true)
        @if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true)
        {{--    Не срабатывает--}}
        {{--var numcalc_{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');--}}

        {{-- Похожие по смыслу проверки "@if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true && $link->parent_is_nc_viewonly==false)" в этом файле восемь раз --}}
        {{-- Проверка '@if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true)' выше--}}
        {{--        @if($link->parent_is_numcalc==true && $link->parent_is_nc_viewonly==false)--}}
        @if($link->parent_is_nc_viewonly == false)
        var button_nc_{{$prefix}}{{$link->id}} = document.getElementById('button_nc{{$link->id}}');
        var name_{{$prefix}}{{$link->id}} = document.getElementById('name{{$link->id}}');
        @endif

        function button_nc_click_{{$prefix}}{{$link->id}}() {
            var x, y, result, error_message;
            x = 0;
            y = 0;
            z = 0;
            v = document.getElementById('link{{$link->id}}');

            @if ($link->parent_base->type_is_string() & $link->parent_base->is_one_value_lst_str_txt == false)
            <?php
            $i = 0;
            ?>
            @foreach (config('app.locales') as $lang_key => $lang_value)
            {{-- Начиная со второго(индекс==1) элемента массива языков сохранять--}}
            @if ($i > 0)
            var x_{{$lang_key}} = 0;
            var y_{{$lang_key}} = 0;
            var z_{{$lang_key}} = 0;
            v_{{$lang_key}} = document.getElementById('link{{$link->id}}_{{$lang_key}}');
            @endif
            <?php
                $i = $i + 1;
                ?>
                @endforeach
                @endif

                error_message = "";
            error_nodata = "Нет данных";
            error_div0 = "Деление на 0";

            <?php
                echo StepController::steps_javascript_code($link, 'button_nc');
                ?>

                {{-- {{StepController::steps_javascript_code($link, 'button_nc')}};--}}
                @if($link->parent_base->type_is_number())
                {{--numcalc_{{$prefix}}{{$link->id}}.value = x;--}}
                v.value = x;

            @elseif ($link->parent_base->type_is_string())

                v.value = x;

            @if ($link->parent_base->is_one_value_lst_str_txt == false)
            <?php
                $i = 0;
                ?>
                @foreach (config('app.locales') as $lang_key => $lang_value)
                {{--Начиная со второго(индекс==1) элемента массива языков сохранять--}}
                @if ($i > 0)
                v_{{$lang_key}}.value = x_{{$lang_key}};
            @endif
            <?php
                $i = $i + 1;
                ?>
                @endforeach
                @endif

                @elseif ($link->parent_base->type_is_boolean())
                {{--v.checked = (x != 0);--}}
                v.checked = (x == 1);
            @elseif ($link->parent_base->type_is_list())
            if (isNaN(x)) {
                x = 0;
            }
            {{--for (let i = 0; i < numcalc_{{$prefix}}{{$link->id}}.length; i++) {--}}
                {{--    if (numcalc_{{$prefix}}{{$link->id}}[i].value == x) {--}}
                {{--        // установить selected на true--}}
                {{--        numcalc_{{$prefix}}{{$link->id}}[i].selected = true;--}}
                {{--    }--}}
                {{--}--}}
                {{--numcalc_{{$prefix}}{{$link->id}}.value = x;--}}

                v.value = x;

            @endif
                {{-- Проверка '@if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true)' выше--}}
                {{-- Похожие по смыслу проверки "@if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true && $link->parent_is_nc_viewonly==false)" в этом файле восемь раз --}}
                @if($link->parent_is_nc_viewonly == false)
                name_{{$prefix}}{{$link->id}}.innerHTML = error_message;
            @endif
            {{-- Нужно для обновления информации--}}
            {{--numcalc_{{$prefix}}{{$link->id}}.dispatchEvent(new Event('change'));--}}

            {{-- v.dispatchEvent(new Event('change'));--}}
            v.dispatchEvent(new Event('change'));

        }

        {{-- Проверка '@if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true)' выше--}}
        {{-- Похожие по смыслу проверки "@if($link->parent_is_numcalc==true && $link->parent_is_nc_screencalc==true && $link->parent_is_nc_viewonly==false)" в этом файле восемь раз --}}
        @if($link->parent_is_nc_viewonly == false)
        button_nc_{{$prefix}}{{$link->id}}.addEventListener("click", button_nc_click_{{$prefix}}{{$link->id}});
        @endif

        <?php
        if ($link->parent_is_nc_viewonly == true) {
            $functs_numcalc_viewonly[] = "button_nc_click_" . $prefix . $link->id;
        } else {
            $functs_numcalc_noviewonly[] = "button_nc_click_" . $prefix . $link->id;
        }
        ?>

        @endif
        @endif
        @endif

        @endforeach

        {{--                @if($link->parent_base->is_code_needed==true && $link->parent_is_enter_refer==true)--}}
        @if($base->is_code_number == true  && $base->is_limit_sign_code == true
            && $base->is_code_zeros == true  && $base->significance_code > 0)
        var code_el = document.getElementById('code');

        <?php
            $functions[] = "code_change";
            ?>
            numBaseDigits = {{$base->significance_code}};

        function code_change() {
            numStr = code_el.value;
            code_el.value = numBaseDigits >= numStr.length ? Array.apply(null, {length: numBaseDigits - numStr.length + 1}).join("0") + numStr : numStr.substring(0, numBaseDigits);
        }

        code_el.addEventListener("change", code_change);
        <?php
        $functs_change['code'] = 1;
        ?>
        @endif

        {{-- var child_base_id_work = 0;--}}
        {{-- var parent_base_id_work= 0;--}}

        function on_numcalc_viewonly() {
            @foreach($functs_numcalc_viewonly as $value)
                {{$value}}();
            @endforeach
        }

        function on_numcalc_noviewonly() {
            @foreach($functs_numcalc_noviewonly as $value)
                {{$value}}();
            @endforeach
        }

        // Нужно для случая, когда меняется значение в вводимом коде, без этого не обновляются parent-поля и поля из вычисляемых таблиц
        function on_parent_refer() {
            @foreach($functs_parent_refer as $value)
                {{$value}}();
            @endforeach
        }

        {{-- Два похожих блока команд в функциях on_submit() и window.onload по обработке строковых полей--}}
        function on_submit() {
            @foreach($array_disabled as $key=>$value)

            <?php
            $link = Link::find($key);
            ?>

            @if($link)
            {{--Две похожие команды в этой функции--}}
            document.getElementById('link{{$key}}').disabled = false;

            @if($link->parent_base->type_is_string())
            @if ($link->parent_base->is_one_value_lst_str_txt == false)
            <?php
            $i = 0;
            ?>
            @foreach (config('app.locales') as $lang_key => $lang_value)
            {{--Начиная со второго(индекс==1) элемента массива языков сохранять--}}
            @if ($i > 0)
            {{--Две похожие команды в этой функции--}}
            document.getElementById('link{{$link->id}}_{{$lang_key}}').disabled = false;
            @endif
            <?php
            $i = $i + 1;
            ?>
            @endforeach
            @endif
            @endif
            @endif
            @endforeach
        }

        function my_rnd(a, b, c) {
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
        {{-- Настройка автоматического перерасчета при выполнении условий--}}
        {{--        @if($link->parent_is_nc_parameter == true && $link->parent_is_numcalc == false--}}
        {{--                && $link->parent_is_nc_viewonly == false && $link->parent_is_parent_related == false--}}
        {{--                && !$link->parent_base->type_is_list())--}}
        @if($link->parent_is_nc_parameter == true && $link->parent_is_numcalc == false
            && $link->parent_is_nc_viewonly == false && $link->parent_is_parent_related == false)

        var numrecalc_{{$prefix}}{{$link->id}} = document.getElementById('link{{$link->id}}');
        numrecalc_{{$prefix}}{{$link->id}}.addEventListener("change", on_numcalc_viewonly);

        @if ($link->parent_base->type_is_string() & $link->parent_base->is_one_value_lst_str_txt == false)
        <?php
        $i = 0;
        ?>
        @foreach (config('app.locales') as $lang_key => $lang_value)
        {{-- Начиная со второго(индекс==1) элемента массива языков сохранять--}}
        @if ($i > 0)

        var numrecalc_{{$prefix}}{{$link->id}}_{{$lang_key}} = document.getElementById('link{{$link->id}}_{{$lang_key}}');
        numrecalc_{{$prefix}}{{$link->id}}_{{$lang_key}}.addEventListener("change", on_numcalc_viewonly);

        @endif
        <?php
        $i = $i + 1;
        ?>
        @endforeach
        @endif
        {{--<?php--}}
        {{--Не нужно, вместо этого запускается on_numcalc_viewonly() в window.onload--}}
        {{--$functs_change['link' . $link->id] = 1;--}}
        {{--?>--}}
        @endif

        @endforeach
    </script>
    <script>
        {{-- Два похожих блока команд в функциях on_submit() и window.onload по обработке строковых полей--}}
            window.onload = function () {

            {{-- Этот блок перед вызовом on_parent_refer()--}}

                ds = true;
            @foreach($array_disabled as $key=>$value)
            <?php
                $link = Link::find($key);
                ?>

                @if($link)
                ds = true;
            @if($par_link)
                {{-- Проверки на ((!$update) | ($base_link_right['is_edit_parlink_enable'] == false)) проводятся по тексту выше, здесь не нужные--}}
                {{-- @if (($key == $par_link->id) & ($base_link_right['is_edit_parlink_enable'] == false))--}}
                @if($key == $par_link->id)
                ds = false;
            @endif
                @endif
            if (ds == true) {
                {{--Две похожие команды в этой функции--}}
                document.getElementById('link{{$key}}').disabled = true;

                @if($link->parent_base->type_is_string())
                @if ($link->parent_base->is_one_value_lst_str_txt == false)
                <?php
                $i = 0;
                ?>
                @foreach (config('app.locales') as $lang_key => $lang_value)
                {{--Начиная со второго(индекс==1) элемента массива языков сохранять--}}
                @if ($i > 0)
                {{--Две похожие команды в этой функции--}}
                document.getElementById('link{{$link->id}}_{{$lang_key}}').disabled = true;
                @endif
                <?php
                $i = $i + 1;
                ?>
                @endforeach
                @endif
                @endif
                @endif
            }
            @endforeach
            {{-- Здесь не использовать--}}
            {{-- on_parent_refer();--}}

            {{-- массив функций нужен, что при window.onload запустить обработчики всех полей--}}
            {{-- @foreach($functions as $value)--}}
            {{--    {{$value}}();--}}
            {{-- @endforeach--}}

            on_numcalc_viewonly();

            {{-- Этот блок нужно выполнить два раза,--}}
            {{-- т.к. все взаимосвязано и порядок вызова этих функций разный:--}}
            {{-- функции про зависимые поля, вычисляемые поля, вывод полей из вычисляемых основ--}}
            {{-- Нужно, например, для правильного отображения данных при добавлении/корректировке записей в шаблоне "Интернет - магазин"--}}
            {{-- Блок 1 --}}
            @foreach($functs_change as $key=>$value)
            document.getElementById('{{$key}}').dispatchEvent(new Event('change'));
            @endforeach

            {{-- Нужно--}}
            {{-- on_numcalc_viewonly();--}}

            {{--            --}}{{-- Блок 2 --}}
            {{--            @foreach($functs_change as $key=>$value)--}}
            {{--            document.getElementById('{{$key}}').dispatchEvent(new Event('change'));--}}
            {{--            @endforeach--}}

            {{-- Использовать после цикла по массиву функций:--}}
            {{-- Сначала должны посчитаться значения parent_is_child_related=true ('Автоматически фильтровать поля ввода'),--}}
            {{-- затем вывод значений из справочников, в т.ч. из уже отфильтрованных--}}
            {{-- Нужно--}}
            {{-- on_parent_refer();--}}

            {{-- Не удалять--}}
            {{-- Не нужно вызывать функцию on_numcalc_noviewonly(),--}}
            {{-- это связано с разрешенной корректировкой вычисляемых полей ($link->parent_is_nc_viewonly=true)--}}
            {{--            @if(!$update)--}}
            {{--            on_numcalc_noviewonly();--}}
            {{--            @else--}}
            {{--            on_numcalc_viewonly();--}}
            {{--            @endif--}}

        };

        {{--https://ru.stackoverflow.com/questions/1114823/%D0%9A%D0%B0%D0%BA-%D1%81%D0%B4%D0%B5%D0%BB%D0%B0%D1%82%D1%8C-%D1%82%D0%B0%D0%BA-%D1%87%D1%82%D0%BE%D0%B1%D1%8B-%D0%BF%D1%80%D0%B8-%D0%BD%D0%B0%D0%B6%D0%B0%D1%82%D0%B8%D0%B8-%D0%BD%D0%B0-%D0%BA%D0%BD%D0%BE%D0%BF%D0%BA%D1%83-%D0%BF%D1%80%D0%BE%D0%B8%D0%B3%D1%80%D1%8B%D0%B2%D0%B0%D0%BB%D1%81%D1%8F-%D0%B7%D0%B2%D1%83%D0%BA--}}
        {{--https://odino.org/emit-a-beeping-sound-with-javascript/--}}
        {{--https://question-it.com/questions/1025607/vosproizvesti-zvukovoj-signal-pri-nazhatii-knopki--}}
        {{--function playSound(sound) { --}}
        {{--    var song = document.getElementById(sound); --}}
        {{--    song.volume = 1; --}}
        {{--    if (song.paused) { --}}
        {{--        song.play(); --}}
        {{--    } else { --}}
        {{--        song.pause(); --}}
        {{--    } --}}
        {{--} --}}
    </script>
@endsection
