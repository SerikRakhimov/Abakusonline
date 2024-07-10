@extends('layouts.app')

@section('content')

    <?php
    use App\Models\Link;
    use App\Models\Level;
    use App\Http\Controllers\GlobalController;
    use App\Http\Controllers\ItemController;
    use App\Http\Controllers\MainController;
    use Illuminate\Support\Facades\Storage;
    $base = $item->base;
    //$base_right = GlobalController::base_right($base, $role, $relit_id);
    $relip_project = GlobalController::calc_relip_project($relit_id, $project);
    //$is_delete = ItemController::is_delete($item, $role, $heading, $base_index_page, $relit_id, $parent_ret_id);
    $is_delete = ItemController::is_delete($item, $role, $relit_id);
    // Показывать emoji - да/нет
    $emoji_enable = true;
    $link_image = null;
    // Не использовать
    //$saveurl_show_edit = null;
    //$saveurl_show_del = null;
    // Шифровка
    if ($type_form == 'show') {
        // Предыдущая страница при просмотре записи
        //$saveurl_show_edit = GlobalController::set_url_save(Request::server('HTTP_REFERER'));
        $saveurl_show_edit = $saveurl_show;
        $saveurl_show_del = $saveurl_show_edit;
    }
    ?>
    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])
    <h4 class="display-5">
        @if ($type_form == 'show')
            {{trans('main.viewing_record')}}
        @elseif($type_form == 'delete_question')
            @if($is_delete['is_list_base_used_delete'] == true)
                {{trans('main.delete_record_question_links')}}?
            @else
                {{trans('main.delete_record_question')}}?
            @endif
        @endif
        <span class="text-label">-</span>
        <span class="text-title">
                @if($base_right['is_bsmn_base_enable'] == true)
                <a href="{{route('item.base_index',['base'=>$item->base_id, 'project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])}}"
                   title="{{$item->base->names($base_right)}}">
                                {{$item->base->name($emoji_enable)}}
                            </a>
            @else
                {{$item->base->name($emoji_enable)}}
            @endif
            </span>
        {{--        'Показывать признак "В истории" при просмотре записи'--}}
        @if($base_right['is_show_hist_attr_enable'] == true)
            @if($item->is_history())
                ,
            @endif
            @include('layouts.item.show_history',['item'=>$item])
        @endif
        @if($is_en_limit_minutes['is_view_en_minutes'] == true)
            <span class="badge badge-pill badge-related"
                  title="{{trans('main.title_en_min')}}">{{GlobalController::remaining_en_minutes($item)}}</span>
        @endif
        @if($is_lt_limit_minutes['is_view_lt_minutes'] == true)
            <span class="badge badge-pill badge"
                  title="{{trans('main.title_lt_min')}}">{{GlobalController::remaining_lt_minutes($item)}}</span>
        @endif
    </h4>
    <br>
    <?php
    if ($base_right['is_hier_base_enable'] == true) {
        $result = ItemController::form_parent_deta_hier($item->id, $project, $role, $relit_id, false);
        echo $result;
    }
    ?>
    {{--                    Вывод основы--}}
    @if($base_right['is_show_base_enable'] == true)
    {{--        <p>--}}
    {{--        @foreach (config('app.locales') as $key=>$value)--}}
    {{--            {{trans('main.name')}} ({{trans('main.' . $value)}}): <span class="text-related">{{$item['name_lang_' . $key]}}</span><br>--}}
    {{--        @endforeach--}}
    @if($base->type_is_image)
    {{--                            <li>--}}
    {{-- @include('view.img',['item'=>$item, 'size'=>"medium", 'border'=>true, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])--}}
    @include('view.img',['item'=>$item, 'size'=>"medium", 'border'=>false, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])
    {{--                <a href="{{Storage::url($item->filename())}}">--}}
    {{--                    <img src="{{Storage::url($item->filename())}}" height="250"--}}
    {{--                         alt="" title="{{$item->title_img()}}">--}}
    {{--                </a>--}}
    </li>--}}
    <hr class="hr_ext_show">
    @elseif($base->type_is_document)
        {{--                            <li>--}}
        {{--                                <b>--}}
        @include('view.doc',['item'=>$item,'usercode'=>GlobalController::usercode_calc()])
        {{--                <a href="{{Storage::url($item->filename())}}" target="_blank">--}}
        {{--                    Открыть документ--}}
        {{--                </a>--}}
        {{--                                </b>--}}
        {{--                            </li>--}}
        <hr class="hr_ext_show">
    @else
        {{--                Если тип-вычисляемое наименование и Показывать Основу с вычисляемым наименованием--}}
        {{--                или если тип-не вычисляемое наименование--}}
        {{--            похожая проверка в base_index.blade.php--}}
        {{-- @if(GlobalController::is_base_calcname_check($base, $base_right))--}}
        @if(GlobalController::is_base_calcname_check($base))
            {{--                                            $numcat = true - вывод числовых полей с разрядом тысячи/миллионы/миллиарды--}}
            {{--                                <li>--}}
            <div class="text-label">
                {{--                    <big>{{$base->name()}}:</big>--}}
                {{--                            <span class="text-related">--}}
                {{--                                        <b>--}}
                @if($base->type_is_text())
                    <big><big>
                            <?php
                            echo GlobalController::it_txnm_n2b($item, $emoji_enable);
                            ?>
                        </big></big>
                @else
                    <?php
                    $item_image = GlobalController::item_image($item);
                    $link_image = $item_image['link'];
                    ?>
                    @if($item_image['item'])
                        {{-- @include('view.img',['item'=>$item_image['item'], 'size'=>"medium", 'width'=>"30%", 'border'=>true, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>true, 'card_img_top'=>false, 'title'=>$link_image->parent_label()])--}}
                        @include('view.img',['item'=>$item_image['item'], 'size'=>"medium", 'border'=>false, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>true, 'card_img_top'=>false, 'title'=>$link_image->parent_label()])
                        <br><br>
                    @endif
                    <big><big>
                            <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
                                       'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id])}}"
                               title="">
                                {{--                                    {{$item->name(false, true)}}--}}
                                <?php
                                // echo $item->nmbr(false, true, false, $emoji_enable, false, null, false, true, $relit_id, $role);
                                // Исключить $view_link при расчете вычисляемого наименования
                                // 'set_un_all_par_link_null()' используется, при приведения к типу Link
                                // Чтобы в функцию передалось как Link, а не как число $link->id (так передается (почему, не понятно) из list\elements\info.php)
                                //echo $item->nmbr(true, true, false, $emoji_enable, false, GlobalController::set_un_all_par_null($view_link), true, true, $relit_id, $role);
                                echo $item->nmbr(true, true, false, $emoji_enable, false, GlobalController::set_un_all_par_link_null($view_link), true, true, $relit_id, $role);
                                ?>
                            </a>
                        </big></big>
                    <br><br>
                @endif
                {{--                </span>--}}
                {{--                                        </b>--}}
            </div>
            <hr class="hr_ext_show">
            {{--                                </li>--}}
        @endif
    @endif
    {{--            <br>--}}
    {{--        </p>--}}
    @endif
    {{--    <ul class="list-group list-group-flush">--}}
    {{--        <li class="list-group-item pb-0 pl-0">--}}
    {{--    </li>--}}
    {{--                </ul>--}}
    @if(1==2)
        <small class="text-label"><small>
                Id
            </small></small>
        <div class="text-project">
            {{--                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role, 'relit_id'=>$relit_id,--}}
            {{--                        'usercode' =>GlobalController::usercode_calc()])}}"--}}
            {{--                   title="">--}}
            {{GlobalController::id_and_emoji($item->id, $emoji_enable)}}
            {{--                </a>--}}
        </div>
        @if($base->is_code_needed == true)
            <hr class="hr_ext_show">
            <small class="text-label"><small>
                    {{trans('main.code')}}
                </small></small>
            <div class="text-project">
                {{--                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,--}}
                {{--                                'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id])}}"--}}
                {{--                           title="">--}}
                {{$item->code}}
                {{--                        </a>--}}
            </div>
        @endif
        {{--        <p class="text-label">--}}
        @if($base_right['is_list_base_sort_creation_date_desc'] == true)
            <hr class="hr_ext_show">
            <small class="text-label"><small>
                    {{trans('main.date')}}
                </small></small>
            <div class="text-project">
                {{GlobalController::date_and_emoji($item->created_date(), $emoji_enable)}}
            </div>
        @endif
        {{--    @foreach($array_plan as $key=>$value)--}}
        {{--        <?php--}}
        {{--        $result = ItemController::get_items_for_link(Link::find($key));--}}
        {{--        $items = $result['result_parent_base_items'];--}}
        {{--        $item_work = Item::find($value);--}}
        {{--        ?>--}}
        {{--        --}}{{--    проверка нужна; для правильного вывода '$item_work->name()'--}}
        {{--        @if($item_work)--}}
        {{--            --}}{{--            <p>{{$result['result_parent_label']}} ({{$result['result_parent_base_name']}}):--}}
        {{--            <p>{{$result['result_parent_label']}}:--}}
        {{--                <span class="text-related">{{$item_work->name()}}</span></p>--}}
        {{--        @endif--}}
        {{--    @endforeach--}}

        {{--        Вывод связей, закоментарено @if(1==2)--}}
        @foreach($array_calc as $key=>$value)
            <?php
            $link = Link::find($key);
            // Нужны все параметры GlobalController::view_info($item->id, $link->id, $role, $relit_id, false)
            $item_find = GlobalController::view_info($item->id, $key, $role, $relit_id, false);
            ?>
            {{--    Основное изображение второй раз не выводится--}}
            @if($link_image)
                @if($link->id == $link_image->id)
                    @continue
                @endif
            @endif
            @if($link && $item_find)
                <?php
                //            $base_link_right = null;
                //            if ($heading == 1 || $base_index_page > 0) {
                // Вычисляет $relit_id
                //            $calc_link_relit_id = GlobalController::calc_link_relit_id($link, $role, $relit_id);
                //            $base_link_right = GlobalController::base_link_right($link, $role, $calc_link_relit_id);
                $base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
                $result_par_link = \App\Http\Controllers\GlobalController::link_par_link($link, $par_link);
                //            } else {
                //                $base_link_right = GlobalController::base_link_right($link, $role, $link->parent_relit_id);
                //            }
                // Проверка $item_find
                // $item_find = GlobalController::items_check_right($item_find, $role, $relit_id, true);
                // "$link->parent_relit_id" нужно передавать
                $item_find = GlobalController::items_check_right($item_find, $role, $link->parent_relit_id, true);
                ?>
                @if($base_link_right['is_show_link_enable'] == true)
                    <hr class="hr_ext_show">
                    <small class="text-title">
                        <small>
                            {{--                        @if($base_link_right['is_bsmn_base_enable'] == true && $base_link_right['is_list_base_calc'] == true)--}}
                            @if($base_link_right['is_bsmn_base_enable'] == true)
                                <a href="{{route('item.base_index',['base'=>$link->parent_base_id, 'project'=>$project, 'role'=>$role, 'relit_id'=>$link->parent_relit_id])}}"
                                   title="{{$link->parent_base->names($base_link_right)}}">
                                    @include('layouts.item.ext_show.parent_label', ['link'=>$link, 'par_link'=>$par_link])
                                </a>
                            @else
                                @include('layouts.item.ext_show.parent_label', ['link'=>$link, 'par_link'=>$par_link])
                            @endif
                        </small></small>
                    @if($item_find)
                        <div class="text-project">
                            @if($link->parent_base->type_is_text())
                                {{--                            <span class="text-related">--}}
                                {{--                                <b>--}}
                                <?php
                                echo GlobalController::it_txnm_n2b($item_find, $emoji_enable);
                                ?>
                                {{--                        </span>--}}
                                {{--                                </b>--}}
                            @elseif($link->parent_base->type_is_image())
                                {{-- @include('view.img',['item'=>$item_find, 'size'=>"medium", 'border'=>true, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])--}}
                                @include('view.img',['item'=>$item_find, 'size'=>"medium", 'border'=>false, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])
                                {{--                            <a href="{{Storage::url($item_find->filename())}}">--}}
                                {{--                                <img src="{{Storage::url($item_find->filename())}}" height="250"--}}
                                {{--                                     alt="" title="{{$item_find->title_img()}}">--}}
                                {{--                            </a>--}}
                            @elseif($link->parent_base->type_is_document())
                                {{--                            <b>--}}
                                @include('view.doc',['item'=>$item_find, 'usercode'=>GlobalController::usercode_calc()])
                                {{--                            <a href="{{Storage::url($item_find->filename())}}" target="_blank">--}}
                                {{--                                Открыть документ--}}
                                {{--                            </a>--}}
                                {{--                            </b>--}}
                            @else
                                {{--                                            $numcat = true - вывод числовых полей с разрядом тысячи/миллионы/миллиарды--}}
                                {{--                            <span class="text-related">--}}
                                {{--                            <b>--}}
                                {{--  Используется 'is_list_base_calc' в ext_show.php и ItemController::item_index()  --}}
                                {{--                        @if($base_link_right['is_list_base_calc'] == true && $base_link_right['is_bsmn_base_enable'] == true)--}}
                                {{--                    Если $par_link == $link, то не показывать ссылку--}}
                                @if($result_par_link ==false & $base_link_right['is_list_base_calc'] == true)
                                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role,
                                                                'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$link->parent_relit_id,
                                                                'called_from_button'=>0,
                                                                'view_link'=>GlobalController::const_null()])}}"
                                       title="">
                                        {{$item_find->name(false, true, true, $emoji_enable)}}
                                    </a>
                                @else
                                    {{$item_find->name(false, true, true, $emoji_enable)}}
                                @endif
                                {{--                            </b>--}}
                                {{--                            </span>--}}
                            @endif
                        </div>
                    @else
                        <div class="text-danger">
                            {{GlobalController::access_restricted()}}
                        </div>
                    @endif
                    {{--                    <br>--}}
                @endif
            @endif
        @endforeach
    @endif
    <?php
    $percent_first = 1;
    $percent_second = 30;
    ?>
    <table class="table table-sm table-hover">
        {{--        <thead>--}}
        {{--        <th>Показатель</th>--}}
        {{--        <th>Значение</th>--}}
        {{--        </thead>--}}
        <tbody>
        <tr>
            <td style="width: {{$percent_first}}%">
                {{--                {{GlobalController::const_id_emoji()}}--}}
            </td>
            <td style="width: {{$percent_second}}%">
                <div class="text-label">
                    Id
                    {{--                    {{GlobalController::const_id_emoji()}}--}}
                </div>
            </td>
            <td>
                {{--                <b>--}}
                <div class="text-project">
                    {{--                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role, 'relit_id'=>$relit_id,--}}
                    {{--                        'usercode' =>GlobalController::usercode_calc()])}}"--}}
                    {{--                   title="">--}}
                    {{--                    {{GlobalController::id_and_emoji($item->id, $emoji_enable)}}--}}
                    {{$item->id}}
                    {{--                </a>--}}
                </div>
                {{--                </b>--}}
            </td>
        </tr>
        @if($base->is_code_needed == true)
            <tr>
                <td style="width: {{$percent_first}}%">
                    {{--               {{GlobalController::const_input_numbers()}}--}}
                </td>
                <td style="width: {{$percent_second}}%">
                    <div class="text-label">
                        {{trans('main.code')}}
                    </div>
                </td>
                <td>
                    <b>
                        <div class="text-project">
                            {{--                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,--}}
                            {{--                                'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id])}}"--}}
                            {{--                           title="">--}}
                            {{$item->code}}
                            {{--                        </a>--}}
                        </div>
                    </b>
                </td>
            </tr>
        @endif
        @if($base_right['is_list_base_sort_creation_date_desc'] == true)
            <tr>
                <td style="width: {{$percent_first}}%">
                    {{--                    {{GlobalController::const_date_emoji()}}--}}
                </td>
                <td style="width: {{$percent_second}}%">
                    <div class="text-label">
                        {{trans('main.date')}}
                    </div>
                </td>
                <td>
                    <b>
                        <div class="text-project">
                            {{--                        {{GlobalController::date_and_emoji($item->created_date(), $emoji_enable)}}--}}
                            {{$item->created_date()}}
                        </div>
                    </b>
                </td>
            </tr>
        @endif
        {{--        Вывод связей--}}
        @foreach($array_calc as $key=>$value)
            <?php
            //                dd($array_calc);
            $link = Link::find($key);
            // Нужны все параметры GlobalController::view_info($item->id, $link->id, $role, $relit_id, false)
            $item_find = GlobalController::view_info($item->id, $key, $role, $relit_id, false);
            ?>
            {{--    Основное изображение второй раз не выводится--}}
            @if($link_image)
                @if($link->id == $link_image->id)
                    @continue
                @endif
            @endif
            @if($link && $item_find)
                <?php
                //            $base_link_right = null;
                //            if ($heading == 1 || $base_index_page > 0) {
                // Вычисляет $relit_id
                //            $calc_link_relit_id = GlobalController::calc_link_relit_id($link, $role, $relit_id);
                //            $base_link_right = GlobalController::base_link_right($link, $role, $calc_link_relit_id);
                $base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
                $result_par_link = \App\Http\Controllers\GlobalController::link_par_link($link, $par_link);
                //            } else {
                //                $base_link_right = GlobalController::base_link_right($link, $role, $link->parent_relit_id);
                //            }
                // Проверка $item_find
                // $item_find = GlobalController::items_check_right($item_find, $role, $relit_id, true);
                // "$link->parent_relit_id" нужно передавать
                $item_find = GlobalController::items_check_right($item_find, $role, $link->parent_relit_id, true);
                ?>
                @if($base_link_right['is_show_link_enable'] == true)
                    <tr>
                        <td style="width: {{$percent_first}}%">
                            {{$link->parent_base->em_str()}}
                        </td>
                        <td style="width: {{$percent_second}}%">
                            <div class="text-label">
                                {{--                        @if($base_link_right['is_bsmn_base_enable'] == true && $base_link_right['is_list_base_calc'] == true)--}}
                                @if($base_link_right['is_bsmn_base_enable'] == true)
                                    <a href="{{route('item.base_index',['base'=>$link->parent_base_id, 'project'=>$project, 'role'=>$role, 'relit_id'=>$link->parent_relit_id])}}"
                                       title="{{$link->parent_base->names($base_link_right)}}">
                                        @include('layouts.item.ext_show.parent_label', ['link'=>$link, 'par_link'=>$par_link])
                                    </a>
                                @else
                                    @include('layouts.item.ext_show.parent_label', ['link'=>$link, 'par_link'=>$par_link])
                                @endif
                            </div>
                        </td>
                        <td>
                            <b>
                                @if($item_find)
                                    <div class="text-project">
                                        @if($link->parent_base->type_is_text())
                                            {{--                            <span class="text-related">--}}
                                            {{--                                <b>--}}
                                            <?php
                                            echo GlobalController::it_txnm_n2b($item_find, $emoji_enable);
                                            ?>
                                            {{--                        </span>--}}
                                            {{--                                </b>--}}
                                        @elseif($link->parent_base->type_is_image())
                                            {{-- @include('view.img',['item'=>$item_find, 'size'=>"medium", 'border'=>true, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])--}}
                                            @include('view.img',['item'=>$item_find,
                                            'size'=>($link->parent_is_parent_related == true) ? "shundred" : "medium",
                                            'border'=>false, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])
                                            {{--                            <a href="{{Storage::url($item_find->filename())}}">--}}
                                            {{--                                <img src="{{Storage::url($item_find->filename())}}" height="250"--}}
                                            {{--                                     alt="" title="{{$item_find->title_img()}}">--}}
                                            {{--                            </a>--}}
                                        @elseif($link->parent_base->type_is_document())
                                            {{--                            <b>--}}
                                            @include('view.doc',['item'=>$item_find, 'usercode'=>GlobalController::usercode_calc()])
                                            {{--                            <a href="{{Storage::url($item_find->filename())}}" target="_blank">--}}
                                            {{--                                Открыть документ--}}
                                            {{--                            </a>--}}
                                            {{--                            </b>--}}
                                        @else
                                            {{--                                            $numcat = true - вывод числовых полей с разрядом тысячи/миллионы/миллиарды--}}
                                            {{--                            <span class="text-related">--}}
                                            {{--                            <b>--}}
                                            {{--  Используется 'is_list_base_calc' в ext_show.php и ItemController::item_index()  --}}
                                            {{--                        @if($base_link_right['is_list_base_calc'] == true && $base_link_right['is_bsmn_base_enable'] == true)--}}
                                            {{--                    Если $par_link == $link, то не показывать ссылку--}}
                                            @if($result_par_link ==false & $base_link_right['is_list_base_calc'] == true)
                                                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role,
                                                                'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$link->parent_relit_id,
                                                                'called_from_button'=>0,
                                                                'view_link'=>GlobalController::const_null()])}}"
                                                   title="">
                                                    {{-- {{$item_find->name(false, true, true, false, false)}}--}}
                                                    {{$item_find->name(false, true, true, false, true)}}
                                                </a>
                                            @else
                                                {{-- {{$item_find->name(false, true, true, false, false)}}--}}
                                                {{$item_find->name(false, true, true, false, true)}}
                                            @endif
                                            {{--                                            <small><span--}}
                                            {{--                                                    class="text-label">{{$item_find->base->par_label_unit_meas()}}</span></small>--}}
                                            {{--                            </b>--}}
                                            {{--                            </span>--}}
                                        @endif
                                    </div>
                                @else
                                    <div class="text-danger">
                                        {{GlobalController::access_restricted()}}
                                    </div>
                                @endif
                            </b>
                        </td>
                    </tr>
                @endif
            @endif
        @endforeach
        </tbody>
    </table>
    {{--                    </p>--}}

    @if($base_right['is_hier_base_enable'] == true)
        <hr>
        <?php
        //        $result = ItemController::form_parent_coll_hier($item->id, $role, $relit_id);
        //        echo $result;
        //$result = ItemController::form_child_deta_hier($item, $project, $role, $relit_par_id, $parent_ret_par_id);
        $result = ItemController::form_child_deta_hier($item, $project, $role, $relit_id);
        echo $result;
        ?>
    @endif
    @if($role->is_author())
        <hr>
        <i>
            <?php
            $created_user_date_time = $item->created_user_date_time();
            $updated_user_date_time = $item->updated_user_date_time();
            ?>
            <div class="text-label">{{trans('main.created_user_date_time')}}:
                <span class="text-related">{{$created_user_date_time}}</span><br>
                @if($created_user_date_time != $updated_user_date_time)
                    {{trans('main.updated_user_date_time')}}:
                    <span class="text-related">{{$updated_user_date_time}}</span></div>
            @endif
        </i>
    @endif
    <br>
    @if ($type_form == 'show')
        <p>
            {{--            @if($base_right['is_list_base_create'] == true)--}}
            {{--            <button type="button" class="btn btn-dreamer"--}}
            {{--                    --}}{{--                        Выводится $message_mc--}}
            {{--                    title="{{trans('main.add')}}"--}}
            {{--                    onclick="document.location='{{route('item.ext_create', ['base'=>$item->base,--}}
            {{--                                        'project'=>$project, 'role'=>$role,--}}
            {{--                                        'usercode' =>GlobalController::usercode_calc(),--}}
            {{--                             'relit_id' => GlobalController::set_relit_id($relit_par_id),--}}
            {{--                             'string_all_codes_current' => $string_all_codes_current,--}}
            {{--                             'string_link_ids_current' => $string_link_ids_current,--}}
            {{--                             'string_item_ids_current' => $string_item_ids_current,--}}
            {{--                             'heading'=>intval(false),--}}
            {{--                             'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,--}}
            {{--                             'view_link'=>$view_link,--}}
            {{--                             'parent_ret_id' => GlobalController::set_relit_id($parent_ret_par_id),--}}
            {{--                             'par_link'=>$par_link, 'parent_item'=>$parent_item])}}'">--}}
            {{--                <i class="fas fa-edit"></i>--}}
            {{--                {{trans('main.add')}}--}}
            {{--            </button>--}}
            {{--            @endif--}}

            {{--        'Корректировать признак "В истории" при просмотре записи'--}}
            @if($base_right['is_edit_hist_attr_enable'] == true)
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                        onclick='document.location="{{route('item.change_history', ['item'=>$item])}}"'
                        title="{{$item->button_title()}}">
                    <i class="fas fa-history"></i>
                    {{$item->button_title()}}
                </button>
    @endif
    @if($is_en_limit_minutes['is_entry_minutes'] == true & $is_checking_history['result_entry_history'] == true & $is_checking_empty['result_entry_empty'] == true)
        @if($item->is_history() == false)
            {{--Похожая проверка в ItemController::ext_edit() и ext_show.php--}}
            @if($base_right['is_list_base_update'] == true)
                <?php
                $level_array = $item->base->level_array();
                ?>
                {{-- Используется "'relit_id'=>$relit_par_id, 'parent_ret_id' => $parent_ret_par_id"--}}
                {{--                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"--}}
                {{--                        onclick='document.location="{{route('item.ext_edit',--}}
                {{--            ['item'=>$item,'project'=>$project, 'role'=>$role,--}}
                {{--            'usercode' =>GlobalController::usercode_calc(),--}}
                {{--            'relit_id'=>GlobalController::set_relit_id($relit_par_id),--}}
                {{--            'string_link_ids_current' => $string_link_ids_current,--}}
                {{--            'string_item_ids_current' => $string_item_ids_current,--}}
                {{--            'string_all_codes_current' => $string_all_codes_current,--}}
                {{--            'heading' => $heading,--}}
                {{--            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,--}}
                {{--            'parent_ret_id' => GlobalController::set_relit_id($parent_ret_par_id),--}}
                {{--            'view_link' => $view_link,--}}
                {{--            'par_link' => $par_link,--}}
                {{--            'parent_item' => $parent_item])}}"'--}}
                {{--                        title="{{trans('main.edit')}}">--}}
                {{--                'string_link_ids_current' => $string_link_ids_current,--}}
                {{--                'string_item_ids_current' => $string_item_ids_current,--}}
                {{--                'string_all_codes_current' => $string_all_codes_current,--}}

                {{-- При просмотре кода формы в браузере возможно неправильно показывать 'saveurl_edit'=>$saveurl_show_edit как textnull,
                 это, возможно, связано что вычисляется Request::server('HTTP_REFERER')
                          $saveurl_show_edit = GlobalController::set_url_save(Request::server('HTTP_REFERER'));
                          На самом деле, все передается как надо в route('item.ext_edit')
                          --}}
                @if($level_array['result'] == true)
                    <div class="dropdown d-inline">
                        <button type="button" class="btn btn-dreamer dropdown-toggle"
                                data-toggle="dropdown"
                                title="{{trans('main.edit')}}">
                            <i class="fas fa-edit  d-inline"></i>
                            {{trans('main.edit')}}
                        </button>
                        <div class="dropdown-menu">
                            {{-- Корректировать все поля формы--}}
                            <a class="dropdown-item" href="{{route('item.ext_edit', ['item'=>$item,'project'=>$project, 'role'=>$role,
            'usercode' =>GlobalController::usercode_calc(),
            'relit_id'=>$relit_id,
            'string_current' => $string_current,
            'heading' => $heading,
            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
            'parent_ret_id' => $parent_ret_id,
            'view_link' => $view_link,
            'saveurl_edit'=>$saveurl_show_edit,
            'level_id' =>GlobalController::const_null(),
            'par_link' => $par_link,
            'parent_item' => $parent_item])}}"
                               title="{{trans('main.edit') . ' '.GlobalController::alf_text()}}">
                                {{--                                                    'string_all_codes_current' => $string_all_codes_current,--}}
                                {{--                                                    'string_link_ids_current' => $string_link_ids_current,--}}
                                {{--                                                    'string_item_ids_current' => $string_item_ids_current,--}}
                                {{trans('main.edit')}} {{GlobalController::alf_text()}}
                            </a>
                            {{-- Цикл по массиву--}}
                            @foreach($level_array['l_arr'] as $level_id)
                                <?php
                                $level = Level::find($level_id);
                                $level_name = '(' . mb_strtolower($level->name()) . ')';
                                ?>
                                @if($level)
                                    <a class="dropdown-item" href="{{route('item.ext_edit', ['item'=>$item,'project'=>$project, 'role'=>$role,
            'usercode' =>GlobalController::usercode_calc(),
            'relit_id'=>$relit_id,
            'string_current' => $string_current,
            'heading' => $heading,
            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
            'parent_ret_id' => $parent_ret_id,
            'view_link' => $view_link,
            'saveurl_edit'=>$saveurl_show_edit,
            'level_id' => $level_id,
            'par_link' => $par_link,
            'parent_item' => $parent_item
            ])}}"
                                       title="{{trans('main.edit')}} {{$level_name}}">
                                        {{--                                                    'string_all_codes_current' => $string_all_codes_current,--}}
                                        {{--                                                    'string_link_ids_current' => $string_link_ids_current,--}}
                                        {{--                                                    'string_item_ids_current' => $string_item_ids_current,--}}
                                        {{trans('main.edit')}} {{$level_name}}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                            onclick='document.location="{{route('item.ext_edit',
            ['item'=>$item,'project'=>$project, 'role'=>$role,
            'usercode' =>GlobalController::usercode_calc(),
            'relit_id'=>$relit_id,
            'string_current' => $string_current,
            'heading' => $heading,
            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
            'parent_ret_id' => $parent_ret_id,
            'view_link' => $view_link,
            'saveurl_edit'=>$saveurl_show_edit,
            'level_id' =>GlobalController::const_null(),
            'par_link' => $par_link,
            'parent_item' => $parent_item])}}"'
                            title="{{trans('main.edit')}}">
                        <i class="fas fa-edit"></i>
                        {{trans('main.edit')}}
                    </button>
                @endif
            @endif
        @endif
    @endif
    {{--            В ItemController::is_delete() есть необходимые проверки--}}
    {{--            @if($is_en_limit_minutes['is_entry_minutes'] == true & $is_checking_history['result_entry_history'] == true& $is_checking_empty['result_entry_empty'] == true)--}}
    @if($item->is_history() == false)
        {{--            В ItemController::is_delete() есть необходимые проверки на права по удалению записи--}}
        @if($is_delete['result'] == true)
            {{-- Используется "'relit_id'=>$relit_par_id, 'parent_ret_id' => $parent_ret_par_id"--}}
            {{--                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"--}}
            {{--                        onclick='document.location="{{route('item.ext_delete_question',--}}
            {{--            ['item'=>$item,'project'=>$project, 'role'=>$role,--}}
            {{--            'usercode' =>GlobalController::usercode_calc(),--}}
            {{--            'relit_id'=>GlobalController::set_relit_id($relit_id),--}}
            {{--            'string_link_ids_current' => $string_link_ids_current, 'string_item_ids_current' => $string_item_ids_current, 'string_all_codes_current' => $string_all_codes_current,--}}
            {{--            'heading' => $heading,--}}
            {{--            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,--}}
            {{--            'parent_ret_id' => GlobalController::set_relit_id($parent_ret_id),--}}
            {{--            'view_link' => $view_link,--}}
            {{--            'par_link' => $par_link,--}}
            {{--            'parent_item' => $parent_item])}}"'--}}
            {{--                        title="{{trans('main.delete')}}">--}}
            {{--                'string_link_ids_current' => $string_link_ids_current, 'string_item_ids_current' => $string_item_ids_current, 'string_all_codes_current' => $string_all_codes_current,--}}
            <button type="button" class="btn btn-dreamer mb-1 mb-sm-0  d-inline"
                    onclick='document.location="{{route('item.ext_delete_question',
            ['item'=>$item,'project'=>$project, 'role'=>$role,
            'usercode' =>GlobalController::usercode_calc(),
            'relit_id'=>$relit_id,
            'string_current' => $string_current,
            'heading' => $heading,
            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
            'parent_ret_id' => $parent_ret_id,
            'view_link' => $view_link,
            'saveurl_del'=>$saveurl_show_del,
            'par_link' => $par_link,
            'parent_item' => $parent_item])}}"'
                    title="{{trans('main.delete')}}">
                <i class="fas fa-trash"></i>
                {{trans('main.delete')}}
            </button>
        @endif
    @endif
    {{--            @endif--}}
    {{--                            С base_index.blade.php--}}
    {{--                                            Не удалять: нужно для просмотра Пространства--}}
    {{--                                                                                        проверка, если link - вычисляемое поле--}}
    {{--                                                @if ($link->parent_is_parent_related == true || $link->parent_is_numcalc == true)--}}
    {{--                                                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc()])}}">--}}
    {{--                                                        @else--}}
    {{--                                                            <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'par_link'=>$link])}}">--}}
    {{--                                                                @endif--}}
    {{--            Не удалять--}}
    @if(1==2)
        <button type="button" class="btn btn-dreamer mb-1 mb-sm-0 d-inline"
                onclick='document.location="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
                         'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>GlobalController::set_relit_id($relit_id)])}}"'
                title="{{trans('main.space')}}">
            <i class="fas fa-atlas"></i>
            {{trans('main.space')}}
        </button>
    @endif
    {{--                                            С base_index.blade.php--}}
    {{--                                                            Не удалять: нужно для просмотра Пространства--}}
    {{--                                                                                                        проверка, если link - вычисляемое поле--}}
    {{--                                                                            @if ($link->parent_is_parent_related == true || $link->parent_is_numcalc == true)--}}
    {{--                                                                                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc()])}}">--}}
    {{--                                                                                    @else--}}
    {{--                                                                                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'par_link'=>$link])}}">--}}
    {{--                                                                                            @endif--}}
    {{--                            Не удалять--}}
    {{--                                        <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"--}}
    {{--                                                onclick='document.location="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc()])}}"'--}}
    {{--                                                title="{{trans('main.space')}}">--}}
    {{--                                            <i class="fas fa-atlas"></i>--}}
    {{--                                            {{trans('main.space')}}--}}
    {{--                                        </button>--}}
    {{--            Похожие строки вверху/внизу--}}
    {{--            <button type="button" class="btn btn-dreamer"--}}
    {{--                    onclick='document.location="{{route('item.ext_return',['item'=>$item,'project'=>$project, 'role'=>$role,--}}
    {{--            'usercode' =>GlobalController::usercode_calc(),--}}
    {{--            'string_current' => $string_current,--}}
    {{--            'heading' => $heading,--}}
    {{--            'relit_id'=>$relit_id,--}}
    {{--            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,--}}
    {{--            'parent_ret_id' => $parent_ret_id,--}}
    {{--            'view_link' => $view_link,--}}
    {{--            'par_link' => $par_link, 'parent_item' => $parent_item])}}"'--}}
    {{--                    title="{{trans('main.return')}}" @include('layouts.item.base_index.previous_url')>--}}
    {{--                <i class="fas fa-arrow-left"></i>--}}
    {{--                {{trans('main.return')}}--}}
    {{--            </button>--}}

    {{--            <button type="button" class="btn btn-dreamer"--}}
    {{--                    onclick='document.location="{{route('item.ext_return',['item'=>$item,'project'=>$project, 'role'=>$role,--}}
    {{--            'usercode' =>GlobalController::usercode_calc(),--}}
    {{--            'string_current' => $string_current,--}}
    {{--            'heading' => $heading,--}}
    {{--            'relit_id'=>$relit_id,--}}
    {{--            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,--}}
    {{--            'parent_ret_id' => GlobalController::set_rev_relit_id($parent_ret_id),--}}
    {{--            'view_link' => $view_link,--}}
    {{--            'saveurl_ret' => $saveurl_show_ret,--}}
    {{--            'par_link' => $par_link, 'parent_item' => $parent_item])}}"'--}}
    {{--                    title="{{trans('main.return')}}"--}}
    {{--            >--}}
    {{--                <i class="fas fa-arrow-left"></i>--}}
    {{--                {{trans('main.return')}}--}}
    {{--            </button>--}}

    <button type="button" class="btn btn-dreamer d-inline" title="{{trans('main.cancel')}}"
            {{-- @include('layouts.item.base_index.previous_url')--}}
            onclick="document.location='{{GlobalController::set_un_url_save($saveurl_show)}}'"
    >
        <i class="fas fa-arrow-left d-inline"></i>
        {{trans('main.cancel')}}
    </button>
    {{--            <button type="button" class="btn btn-dreamer"--}}
    {{--                    title="{{trans('main.return')}}" onclick="javascript:history.back();">--}}
    {{--                <i class="fas fa-arrow-left"></i>--}}
    {{--                {{trans('main.return')}}--}}
    {{--            </button>--}}
    </p>
    @elseif($type_form == 'delete_question')
        {{-- Используется "'relit_id'=>$parent_ret_id, 'parent_ret_id' => $relit_id"--}}
        {{--        <form action="{{route('item.ext_delete',['item'=>$item,'project'=>$project, 'role'=>$role,--}}
        {{--            'usercode' =>GlobalController::usercode_calc(),--}}
        {{--            'string_link_ids_current' => $string_link_ids_current,--}}
        {{--            'string_item_ids_current' => $string_item_ids_current,--}}
        {{--            'string_all_codes_current' => $string_all_codes_current,--}}
        {{--            'heading' => $heading,--}}
        {{--            'relit_id'=>GlobalController::set_relit_id($relit_par_id),--}}
        {{--            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,--}}
        {{--            'parent_ret_id' => GlobalController::set_relit_id($parent_ret_par_id),--}}
        {{--            'view_link' => $view_link,--}}
        {{--            'par_link' => $par_link, 'parent_item' => $parent_item])}}"--}}
        {{--              method="POST"--}}
        {{--              id='delete-form'>--}}
        {{--        'string_link_ids_current' => $string_link_ids_current,--}}
        {{--        'string_item_ids_current' => $string_item_ids_current,--}}
        {{--        'string_all_codes_current' => $string_all_codes_current,--}}


        {{-- Переменная $saveurl_question_del передается в ItemController::ext_delete_question() при вызове  с 'type_form' => 'delete_question'--}}
        {{--        'saveurl_del' =>$saveurl_question_del,--}}
        <form action="{{route('item.ext_delete',['item'=>$item,'project'=>$project, 'role'=>$role,
            'usercode' =>GlobalController::usercode_calc(),
            'string_current' => $string_current,
            'heading' => $heading,
            'relit_id'=>$relit_id,
            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
            'parent_ret_id' => $parent_ret_id,
            'view_link' => $view_link,
            'saveurl_del' =>$saveurl_show,
            'par_link' => $par_link, 'parent_item' => $parent_item])}}"
              method="POST"
              id='delete-form'>
            @csrf
            @method('DELETE')
            <p>
                @if($item->is_history() == false)
                    <button type="submit" class="btn btn-danger d-inline" title="{{trans('main.delete')}}">
                        <i class="fas fa-trash"></i>
                        {{trans('main.delete')}}
                    </button>
                @endif
                {{--            Похожие строки вверху/внизу--}}
                {{--                --}}{{-- Переменная $saveurl_question_ret присваивается выше при вызове формы 'type_form' => 'delete_question'--}}
                {{--                <button type="button" class="btn btn-dreamer"--}}
                {{--                        onclick='document.location="{{route('item.ext_return',['item'=>$item,'project'=>$project, 'role'=>$role,--}}
                {{--            'usercode' =>GlobalController::usercode_calc(),--}}
                {{--            'string_current' => $string_current,--}}
                {{--            'heading' => $heading,--}}
                {{--            'relit_id'=>$relit_id,--}}
                {{--            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,--}}
                {{--            'parent_ret_id' => GlobalController::set_rev_relit_id($parent_ret_id),--}}
                {{--            'view_link' => $view_link,--}}
                {{--            'saveurl_ret' => $saveurl_question_ret,--}}
                {{--            'par_link' => $par_link, 'parent_item' => $parent_item])}}"'--}}
                {{--                        title="{{trans('main.return')}}"--}}
                {{--                >--}}
                {{--                    <i class="fas fa-arrow-left"></i>--}}
                {{--                    {{trans('main.return')}}--}}
                {{--                </button>--}}
                <button type="button" class="btn btn-dreamer d-inline" title="{{trans('main.cancel')}}"
                        {{-- @include('layouts.item.base_index.previous_url')--}}
                        onclick="document.location='{{GlobalController::set_un_url_save($saveurl_show)}}'"
                >
                    <i class="fas fa-arrow-left d-inline"></i>
                    {{trans('main.cancel')}}
                </button>
                {{--                <button type="button" class="btn btn-dreamer"--}}
                {{--                        title="{{trans('main.return')}}"--}}
                {{--                    @include('layouts.item.base_index.previous_url')>--}}
                {{--                    <i class="fas fa-arrow-left"></i>--}}
                {{--                    {{trans('main.return')}}--}}
                {{--                </button>--}}
            </p>
        </form>
    @endif
    {{--    <div class="pt-1 pl-2">--}}
    {{--        111111111111111--}}
    {{--        222222222222222--}}
    {{--        333333333333333--}}
    {{--    </div>--}}
    {{--    <div class="pt-1 pl-1">--}}
    {{--        fffffffffffff111111111111111--}}
    {{--        222222222222222--}}
    {{--        333333333333333--}}
    {{--    </div>--}}
    {{--    <div class="pt-1 pl-1">--}}
    {{--        hhhhhhhhhhhhhhhhhh111111111111111--}}
    {{--        222222222222222--}}
    {{--        333333333333333--}}
    {{--    </div>--}}
@endsection
