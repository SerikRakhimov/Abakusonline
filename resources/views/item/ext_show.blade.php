@extends('layouts.app')

@section('content')

    <?php
    use App\Models\Link;
    use App\Models\Item;
    use App\Http\Controllers\GlobalController;
    use App\Http\Controllers\ItemController;
    use App\Http\Controllers\MainController;
    use Illuminate\Support\Facades\Storage;
    $base = $item->base;
    //$base_right = GlobalController::base_right($base, $role, $relit_id);
    $relip_project = GlobalController::calc_relip_project($relit_id, $project);
    $is_delete = ItemController::is_delete($item, $role, $heading, $base_index_page, $relit_id, $parent_ret_id);
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
        <span class="text-label">-</span> <span class="text-title">{{$item->base->info()}}</span>
            {{--        'Показывать признак "В истории" при просмотре записи'--}}
            {{--        @if($base_right['is_show_hist_attr_enable'] == true)--}}
            @include('layouts.item.show_history',['item'=>$item])
            {{--        @endif--}}
    </h4>
    <br>
    <?php
    if ($base_right['is_hier_base_enable'] == true) {
        $result = ItemController::form_parent_deta_hier($item->id, $project, $role, $relit_id, false);
        echo $result;
    }
    ?>
    <?php
    echo $item->nmbr(false, true);
    echo "---------------------------";
    echo $item->nmbr(true, true);
    ?>
    {{--                    Вывод основы--}}
    @if($base_right['is_show_base_enable'] == true)
        {{--        <p>--}}
        {{--        @foreach (config('app.locales') as $key=>$value)--}}
        {{--            {{trans('main.name')}} ({{trans('main.' . $value)}}): <span class="text-related">{{$item['name_lang_' . $key]}}</span><br>--}}
        {{--        @endforeach--}}
        @if($base->type_is_image)
            {{--                            <li>--}}
            @include('view.img',['item'=>$item, 'size'=>"medium", 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])
            {{--                <a href="{{Storage::url($item->filename())}}">--}}
            {{--                    <img src="{{Storage::url($item->filename())}}" height="250"--}}
            {{--                         alt="" title="{{$item->title_img()}}">--}}
            {{--                </a>--}}
            {{--                            </li>--}}
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
            @if(GlobalController::is_base_calcname_check($base, $base_right))
                {{--                                            $numcat = true - вывод числовых полей с разрядом тысячи/миллионы/миллиарды--}}
                {{--                                <li>--}}
                <div class="text-label">
                    {{--                    <big>{{$base->name()}}:</big>--}}
                    {{--                            <span class="text-related">--}}
                    {{--                                        <b>--}}
                    @if($base->type_is_text())
                        <big><big>
                                <?php
                                echo GlobalController::it_txnm_n2b($item);
                                ?>
                            </big></big>
                    @else
                        <big><big>
                                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
                                       'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id])}}"
                                   title="">
                                    {{--                                    {{$item->name(false, true)}}--}}
                                    <?php
                                    echo $item->nmbr(false, true);
                                    ?>
                                </a>
                            </big></big>
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
    <small class="text-label"><small>
            Id
        </small></small>
    <div class="text-project">
        {{--                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role, 'relit_id'=>$relit_id,--}}
        {{--                        'usercode' =>GlobalController::usercode_calc()])}}"--}}
        {{--                   title="">--}}
        {{$item->id}}
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
            {{$item->created_date()}}
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

    {{--        Вывод связей--}}
    @foreach($array_calc as $key=>$value)
        <?php
        $link = Link::find($key);
        $item_find = GlobalController::view_info($item->id, $key);
        ?>
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
            ?>
            @if($base_link_right['is_show_link_enable'] == true)
                <hr class="hr_ext_show">
                <small class="text-title">
                    <small>
                        {{--                        @if($base_link_right['is_bsmn_base_enable'] == true)--}}
                        @if($base_link_right['is_bsmn_base_enable'] == true && $base_link_right['is_list_base_calc'] == true)
                            <a href="{{route('item.base_index',['base'=>$link->parent_base_id, 'project'=>$project, 'role'=>$role, 'relit_id'=>$link->parent_relit_id])}}"
                               title="{{$link->parent_base->names($base_link_right)}}">
                                @include('layouts.item.ext_show.parent_label', ['link'=>$link, 'par_link'=>$par_link])
                            </a>
                        @else
                            @include('layouts.item.ext_show.parent_label', ['link'=>$link, 'par_link'=>$par_link])
                        @endif
                    </small></small>
                <div class="text-project">
                    @if($link->parent_base->type_is_text())
                        {{--                            <span class="text-related">--}}
                        {{--                                <b>--}}
                        <?php
                        echo GlobalController::it_txnm_n2b($item_find);
                        ?>
                        {{--                        </span>--}}
                        {{--                                </b>--}}
                    @elseif($link->parent_base->type_is_image())
                        @include('view.img',['item'=>$item_find, 'size'=>"medium", 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])
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
                                                                'view_link'=>\App\Http\Controllers\GlobalController::const_null()])}}"
                               title="">
                                {{$item_find->name(false, true, true, true)}}
                            </a>
                        @else
                            {{$item_find->name(false, true, true, true)}}
                        @endif
                        {{--                            </b>--}}
                        {{--                            </span>--}}
                    @endif
                </div>
                {{--                    <br>--}}
            @endif
        @endif
    @endforeach
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
            @if($item->is_history() == false)
                {{--Похожая проверка в ItemController::ext_edit() и ext_show.php--}}
                @if($base_right['is_list_base_update'] == true)
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
            'par_link' => $par_link,
            'parent_item' => $parent_item])}}"'
                            title="{{trans('main.edit')}}">
                        <i class="fas fa-edit"></i>
                        {{trans('main.edit')}}
                    </button>
                @endif
            @endif
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
                    <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                            onclick='document.location="{{route('item.ext_delete_question',
            ['item'=>$item,'project'=>$project, 'role'=>$role,
            'usercode' =>GlobalController::usercode_calc(),
            'relit_id'=>$relit_id,
            'string_current' => $string_current,
            'heading' => $heading,
            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
            'parent_ret_id' => $parent_ret_id,
            'view_link' => $view_link,
            'par_link' => $par_link,
            'parent_item' => $parent_item])}}"'
                            title="{{trans('main.delete')}}">
                        <i class="fas fa-trash"></i>
                        {{trans('main.delete')}}
                    </button>
                @endif
            @endif
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
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
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
            {{--            <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"--}}
            {{--                    title="{{trans('main.return')}}" @include('layouts.item.base_index.previous_url')>--}}
            {{--                <i class="fas fa-arrow-left"></i>--}}
            {{--                {{trans('main.return')}}--}}
            {{--            </button>--}}
            {{--            @include('layouts.item.base_index.previous_url')--}}
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
            <button type="button" class="btn btn-dreamer"
                    onclick='document.location="{{route('item.ext_return',['item'=>$item,'project'=>$project, 'role'=>$role,
            'usercode' =>GlobalController::usercode_calc(),
            'string_current' => $string_current,
            'heading' => $heading,
            'relit_id'=>$relit_id,
            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
            'parent_ret_id' => GlobalController::set_rev_relit_id($parent_ret_id),
            'view_link' => $view_link,
            'par_link' => $par_link, 'parent_item' => $parent_item])}}"'
                    title="{{trans('main.return')}}"
            >
                <i class="fas fa-arrow-left"></i>
                {{trans('main.return')}}
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
        <form action="{{route('item.ext_delete',['item'=>$item,'project'=>$project, 'role'=>$role,
            'usercode' =>GlobalController::usercode_calc(),
            'string_current' => $string_current,
            'heading' => $heading,
            'relit_id'=>$relit_id,
            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
            'parent_ret_id' => $parent_ret_id,
            'view_link' => $view_link,
            'par_link' => $par_link, 'parent_item' => $parent_item])}}"
              method="POST"
              id='delete-form'>
            @csrf
            @method('DELETE')
            <p>
                @if($item->is_history() == false)
                    <button type="submit" class="btn btn-danger" title="{{trans('main.delete')}}">
                        <i class="fas fa-trash"></i>
                        {{trans('main.delete')}}
                    </button>
                @endif
                {{--                <button type="button" class="btn btn-dreamer"--}}
                {{--                        title="{{trans('main.return')}}" @include('layouts.item.base_index.previous_url')>--}}
                {{--                    <i class="fas fa-arrow-left"></i>--}}
                {{--                    {{trans('main.return')}}--}}
                {{--                </button>--}}
                {{--            Похожие строки вверху/внизу--}}
                <button type="button" class="btn btn-dreamer"
                        onclick='document.location="{{route('item.ext_return',['item'=>$item,'project'=>$project, 'role'=>$role,
            'usercode' =>GlobalController::usercode_calc(),
            'string_current' => $string_current,
            'heading' => $heading,
            'relit_id'=>$relit_id,
            'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
            'parent_ret_id' => GlobalController::set_rev_relit_id($parent_ret_id),
            'view_link' => $view_link,
            'par_link' => $par_link, 'parent_item' => $parent_item])}}"'
                        title="{{trans('main.return')}}"
                >
                    <i class="fas fa-arrow-left"></i>
                    {{trans('main.return')}}
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
