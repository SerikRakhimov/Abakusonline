@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Base;
    use App\Models\Relit;
    use App\Models\Relip;
    use App\Models\Project;
    use App\Models\Item;
    use App\Models\Link;
    use App\Models\Main;
    use App\Models\Level;
    use \App\Http\Controllers\GlobalController;
    use \App\Http\Controllers\StepController;
    // Не удалять
    //        function objectToarray($data)
    //        {
    //            $array = (array)$data;
    //            return $array;
    //        }
    $relip_project = GlobalController::calc_relip_project($relit_id, $project);
    $relip_body_project = GlobalController::calc_relip_project($view_ret_id, $project);
    //    $relip_name_project = '';
    //    $relip_body_name_project = '';
    //    if ($relip_project) {
    //        if ($relit_id != 0) {
    //            $relip_name_project = $relip_project->name();
    //        }
    //    }
    //    if ($relip_body_project) {
    //        if ($relip_body_project->id != $project->id) {
    //            $relip_body_name_project = $relip_body_project->name();
    //        }
    //    }

    $calc_relip_info = GlobalController::calc_relip_info($project, $role, $relip_project, $relit_id);
    $calc_body_relip_info = GlobalController::calc_relip_info($project, $role, $relip_body_project, $view_ret_id);

    // Нужно
    $view_link = GlobalController::set_un_par_null($view_link);
    // $view_link передаются как число($link->id) или текст(GlobalController::par_link_const_textnull() или GlobalController::par_link_const_text_base_null())
    // $v_link либо приведен к типу link либо null
    $v_link = null;
    if ($view_link) {
        $v_link = Link::find($view_link);
    }
    // 'if (isset($parent_ret_id))' нужно
    if (isset($parent_ret_id)) {
        $parent_ret_id = GlobalController::set_un_par_null($parent_ret_id);
    }

    //    $heading = 0;
    //    $relit_par_id = null;
    //    $parent_ret_par_id = null;
    //    if ($heading == 1) {
    //        $relit_par_id = $relit_id;
    //        $parent_ret_par_id = $view_ret_id;
    //    } else {
    //        $relit_par_id = $view_ret_id;
    //        $parent_ret_par_id = $relit_id;
    //    }
    $tile_view = $item->base->tile_view($role, $relit_id, $base_right);
    $relit_heading_id = GlobalController::set_relit_id($relit_id);
    $view_ret_heading_id = GlobalController::set_relit_id($view_ret_id);
    $relit_body_id = GlobalController::set_relit_id($view_ret_id);
    $view_ret_body_id = GlobalController::set_relit_id($relit_id);
    // Нужно
    $emoji_enable = true;
    // Нужно
    $link_image = null;
    // Шифровка
    // Текущий адрес отображаемой страницы
    $saveurl_add = GlobalController::set_url_save(GlobalController::current_path());
    $saveurl_show = $saveurl_add;
    $level_array = $item->base->level_array();
    $ct = count($tree_array);
    ?>
    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])
    {{--    <h3 class="display-5">--}}
    {{--        {{trans('main.space')}}--}}
    {{--        <span class="text-label">-</span> <span class="text-title">{{$item->base->info()}}</span>--}}
    {{--    </h3>--}}
    {{-- Вывод дерева пройденных ссылок --}}
    <div class="container-fluid">
        @foreach($tree_array as $value)
            <div class="row">
                <div class="col-12 text-left">
                    {{--                    <h6>--}}
                    @if($value['is_bsmn_base_enable'] == true)
                        <a href="{{route('item.base_index', ['base'=>$value['base_id'],
                            'project'=>$project, 'role'=>$role, 'relit_id'=>$value['relit_id']])}}"
                           title="{{$value['base_names']}}">
                            @endif
                            {{--                        {{GlobalController::calc_title_name(GlobalController::name_and_end_emoji($value['label_name'], Base::find($value['base_id'])), true, true)}}--}}
                            {{GlobalController::calc_title_name($value['label_name'], true, true)}}
                            @if($value['is_bsmn_base_enable'] == true)
                        </a>
                    @endif
                    {{--                    Нужно "'called_from_button' => 1"--}}
                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$value['item_id'], 'role'=>$role,
                                        'usercode' => GlobalController::usercode_calc(), 'relit_id'=>$value['relit_id'],
                                        'called_from_button' => 1,
                                        'view_link'=>$value['all_code'] == GlobalController::const_alltrue() ? GlobalController::par_link_const_textnull():$value['link_id'],
                                                'view_ret_id'=>$value['vwret_id'],
                                                'string_current'=>$value['string_previous'],
                                                'prev_base_index_page'=>0,
                                                'prev_body_link_page'=>0,
                                                'prev_body_all_page'=>0]
                                        )}}"
                       title="{{$value['item_name'] . ' ' . $value['info_name']}}">
                        {{--                'string_link_ids_current'=>$value['string_prev_link_ids'],--}}
                        {{--                'string_item_ids_current'=>$value['string_prev_item_ids'],--}}
                        {{--                'string_relit_ids_current'=>$value['string_prev_relit_ids'],--}}
                        {{--                'string_all_codes_current'=>$value['string_prev_all_codes']--}}
                        {{--                {{$value['item_name']}}--}}
                        {{--                {{$value['info_name']}}--}}
                        <mark class="text-project">
                            {{--                        {{$value['item_name']}}--}}
                            {{GlobalController::name_and_emoji($value['item_name'], Base::find($value['base_id']))}}
                        </mark>
                        {{--                <span class="badge badge-related">{{$value['info_name']}}</span>--}}
                        <small><small><small>{{$value['info_name']}}</small></small></small>
                    </a>
                    {{--                    </h6>--}}
                </div>
            </div>
        @endforeach
    </div>
    @if($ct>0)
        <hr>
    @endif
    <div class="container-fluid">
        {{-- III.Вывод информации в виде таблицы--}}
        {{--        <div class="row">--}}
        <?php
        //            if ($view_link) {
        //                // true - с эмодзи
        //                $title = $view_link->parent_label($emoji_enable);
        //            } else {
        //                $title = $item->base->name($emoji_enable);
        //            }
        // Нужно так использовать '&&'
        // $v_link используется
        //        if (($v_link) && ($item->id == $it_local->id)) {
        //            // true - с эмодзи
        //            // $v_link используется
        //            $title = $v_link->parent_label($emoji_enable);
        //        } else {
        //            $title = $it_local->base->name($emoji_enable);
        //        }
        $title = $it_local->base->name();
        if ($ct > 0) {
            $title = $tree_array[$ct - 1]['label_work'];
        }
        $title = mb_strtolower($title);
        ?>
        {{--                    Выводить вычисляемое наименование--}}
        {{-- Одинаковые проверки должны быть в ItemController::item_index() и в item_index.php--}}
        {{-- здесь равно true--}}
        {{-- @if(GlobalController::is_base_calcname_check($it_local->base, $base_right))--}}
        @if(GlobalController::is_base_calcname_check($it_local->base))
            {{--                        @if(1==1)--}}
            <div class="col-12 text-center">
                {{--                    <big>--}}
                {{--                    <h6>--}}
                @if($base_right['is_bsmn_base_enable'] == true)
                    <a href="{{route('item.base_index', ['base'=>$it_local->base,
                        'project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])}}"
                       title="{{$it_local->base->names($base_right) . $message_bs_info}}">
                        @endif
                        {{$title}}:
                        <br>
                        @if ($base_right['is_bsmn_base_enable'] == true)
                    </a>
                @endif
                {{--                        <big/>--}}
                <div style="font-size:large">
                    {{-- Одинаковые строки рядом (route('item.ext_show'))--}}
                    @if ($base_right['is_list_base_calc'] == true)
                        {{--              Использовать "'heading' => intval(true)", проверяется в окончании функции ItemController:ext_delete()--}}
                        <a href="{{route('item.ext_show', ['item'=>$it_local, 'project'=>$project, 'role'=>$role,
                                        'usercode' =>GlobalController::usercode_calc(),
                                        'relit_id'=>$relit_id,
                                        'string_current' => $string_current,
        'heading' => intval(true),
        'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
        'view_link'=> GlobalController::set_par_null($view_link),
        'saveurl_show' =>$saveurl_show,
        'par_link'=>$tree_array_last_link_id, 'parent_item'=>$tree_array_last_item_id,
        'parent_ret_id' => $view_ret_id])}}"
                           title="{{trans('main.viewing_record')}}:{{$it_local->name(false, false, false, false)}}"
                        >
                            {{-- Первый параметр nmbr(): $fullname = true/false - вывод полной строки (более 255 символов)--}}
                            @if($base_right['is_list_base_read'] == true)
                                {{-- @include('layouts.item.empty_name', ['name'=>$it_local->nmbr(true, false, false, false)])--}}
                                @include('layouts.item.empty_name', ['name'=>$it_local->nmbr(true, false, false, false, false, null, false, true, $relit_id, $role)])
                            @else
                                @include('layouts.item.empty_name', ['name'=>$it_local->name(false, false, false, false, false, null, false, true, $relit_id, $role)])
                            @endif
                        </a>
                    @else
                        <?php
                        //                                    emoji не показывать
                        echo $it_local->nmbr(false, false, false, false, false, null, false, true, $relit_id, $role);
                        ?>
                    @endif
                </div>
                <br>
                @if($it_local->base->is_code_needed == true)
                    {{trans('main.code')}}: <strong>{{$it_local->code}}</strong>
                @endif
                {{--                                <div class="col-4 text-left">--}}
                {{--                                    @if($it_local->base->is_code_needed == true)--}}
                {{--                                        {{trans('main.code')}}: <strong>{{$it_local->code}}</strong>--}}
                {{--                                    @endif--}}
                {{--                                </div>--}}
                {{--                    </h6>--}}
                @include('layouts.project.show_relip_info',['calc_relip_info'=>$calc_relip_info])
            </div>
        @else
            <div class="col-12 text-left">
                <big><big>
                        {{--                                <h6>--}}
                        @if($base_right['is_bsmn_base_enable'] == true)
                            <a href="{{route('item.base_index', ['base'=>$it_local->base,
                        'project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])}}"
                               title="{{$it_local->base->names($base_right) . $message_bs_info}}">
                                @endif
                                {{$title}}:
                                @if ($base_right['is_bsmn_base_enable'] == true)
                            </a>
                        @endif
                    </big></big>
                <br>
                {{--                                </h6>--}}
                {{-- Одинаковые строки рядом (route('item.ext_show'))--}}
                @if ($base_right['is_list_base_calc'] == true)
                    {{--              Использовать "'heading' => intval(true)", проверяется в окончании функции ItemController:ext_delete()--}}
                    <a href="{{route('item.ext_show', ['item'=>$it_local, 'project'=>$project, 'role'=>$role,
                                        'usercode' =>GlobalController::usercode_calc(),
                                        'relit_id'=>$relit_id,
        'string_current' => $string_current,
        'heading' => intval(true),
        'base_index_page' => $base_index_page, 'body_link_page' => $body_link_page, 'body_all_page' => $body_all_page,
        'view_link'=> GlobalController::set_par_null($view_link),
        'saveurl_show' =>$saveurl_show,
        'par_link'=>$tree_array_last_link_id, 'parent_item'=>$tree_array_last_item_id,
        'parent_ret_id' => $relit_id])}}"
                       title="{{trans('main.viewing_record')}}: {{$it_local->cdnm()}}">
                        {{--                            'string_link_ids_current' => $string_link_ids_current,--}}
                        {{--                            'string_item_ids_current' => $string_item_ids_current,--}}
                        {{--                            'string_relit_ids_current' => $string_relit_ids_current,--}}
                        {{--                            'string_all_codes_current'=>$string_all_codes_current,--}}
                        @endif
                        @if($it_local->base->is_code_needed == true)
                            {{trans('main.code')}}: <strong>{{$it_local->code}}</strong>
                            <br>
                        @endif
                        {{--                    Нужно '@foreach($child_mains_link_is_calcname as $calcname_mains)'--}}
                        @foreach($child_mains_link_is_calcname as $calcname_mains)
                            {{-- Если нет записей, вывести trans('main.viewing_record'), чтобы ссылка вызова 'item.ext_show' работала--}}
                            @if(count($calcname_mains) == 0)
                                {{--                                                <h6>--}}
                                {{trans('main.viewing_record')}}
                                {{--                                                </h6>--}}
                            @else
                                @foreach($calcname_mains as $calcname_main)
                                    {{--                                                    <h6>--}}
                                    {{--                                        <big>--}}
                                    {{GlobalController::calc_title_name($calcname_main->link->parent_label(true),true,true)}}
                                    {{-- Выводить с 'Единица измерения (для числовых и строковых полей)'--}}
                                    <strong>{{$calcname_main->parent_item->name(false,false,false,false,true)}}</strong>
                                    {{--                                        </big>--}}
                                    <br>
                                    @if($calcname_main->parent_item->base->is_code_needed == true)
                                        {{trans('main.code')}}:
                                        <strong>{{$calcname_main->parent_item->code}}</strong>
                                        <br>
                                    @endif
                                    {{--                                                    </h6>--}}
                                @endforeach
                            @endif
                        @endforeach
                        @if ($base_right['is_list_base_calc'] == true)
                    </a>
                @endif
            </div>
        @endif
        {{--        </div>--}}
        <?php
        $nolink_id = null;
        // Проверка должна быть одинакова "$base_right['is_list_base_read'] == true" ItemController::item_index() и Base::tile_view()
        if ($base_right['is_list_base_read'] == true) {
            $item_image = GlobalController::item_image($item);
            $link_image = $item_image['link'];
        }
        ?>
        {{--        Показывать основное изображение при "$base_right['is_list_base_read'] == true"--}}
        @if(isset($item_image['item']))
            @if($item_image['item'])
                {{--            В table.php идет проверка на $link_image (вычисляется вначале table.php командой "$link_image = $tile_view['link'];")--}}
                <center>
                    {{--                @include('view.img',['item'=>$item_image['item'], 'size'=>"medium", 'width'=>"30%", 'border'=>true, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>true, 'card_img_top'=>false, 'title'=>$link_image->parent_label()])--}}
                    @include('view.img',['item'=>$item_image['item'], 'width'=>"25%", 'border'=>true, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>true, 'card_img_top'=>false, 'title'=>$link_image->parent_label()])
                    <?php
                    $nolink_id = $link_image->id;
                    ?>
                </center>
            @endif
        @endif
        {{--    @if((count($child_links) != 0) && ($base_right['is_show_head_attr_enable'] == true))--}}
        @if(count($child_links) != 0)
            <br>
            {{--        Выводится одна запись в шапке(все родительские links - столбы)--}}
            {{--        Используется "'heading'=>intval(true)"--}}
            {{--        Используется "'items'=>$items->get()"; два раза, т.к. в заголовке выводится только одна строка, ее на страницы не надо разбивать/сортировать--}}
            {{--        Параметры 'relit_id' и 'view_ret_id' передаются в зависимости от значения $heading--}}
            {{--            @include('list.table',['base'=>$item->base, 'project'=>$project, 'role'=>$role,--}}
            {{--                    'links_info'=>$child_links_info,--}}
            {{--                    'items'=>$items->get(),--}}
            {{--                    'its_page'=>$items->get(),--}}
            {{--                    'base_right'=>$base_right, 'relit_id'=>$relit_heading_id,--}}
            {{--                    'heading'=>intval(true),--}}
            {{--                    'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,--}}
            {{--                    'view_link'=>$view_link,--}}
            {{--                    'view_ret_id'=>$view_ret_heading_id,--}}
            {{--                     'parent_item'=>$item, 'is_table_body'=>false,--}}
            {{--                        'base_index'=>false, 'item_heading_base'=>true, 'item_body_base'=>false,--}}
            {{--                        'string_link_ids_current' => $string_link_ids_current,--}}
            {{--                        'string_item_ids_current' => $string_item_ids_current,--}}
            {{--                        'string_relit_ids_current' => $string_relit_ids_current,--}}
            {{--                        'string_all_codes_current' => $string_all_codes_current,--}}
            {{--                        'string_current' => $string_current,--}}
            {{--                        'string_link_ids_next'=>GlobalController::set_str_const_null(''),--}}
            {{--                        'string_item_ids_next'=>GlobalController::set_str_const_null(''),--}}
            {{--                        'string_relit_ids_next'=>GlobalController::set_str_const_null(''),--}}
            {{--                        'string_all_codes_next'=>GlobalController::set_str_const_null(''),--}}
            {{--                        'string_next' => $string_next,--}}
            {{--                        'saveurl_show' =>$saveurl_show,--}}
            {{--                        'nolink_id' =>$nolink_id--}}
            {{--            ])--}}
            @include('list.table',['base'=>$it_local->base, 'project'=>$project, 'role'=>$role,
        'links_info'=>$child_links_info,
        'items'=>$items->get(),
        'its_page'=>$items->get(),
        'base_right'=>$base_right, 'relit_id'=>$relit_heading_id,
        'heading'=>intval(true),
        'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
        'view_link'=>$view_link,
        'view_ret_id'=>$view_ret_heading_id,
         'parent_item'=>$it_local, 'is_table_body'=>false,
            'base_index'=>false, 'item_heading_base'=>true, 'item_body_base'=>false,
            'string_link_ids_current' => $string_link_ids_current,
            'string_item_ids_current' => $string_item_ids_current,
            'string_relit_ids_current' => $string_relit_ids_current,
            'string_all_codes_current' => $string_all_codes_current,
            'string_current' => $string_current,
            'string_link_ids_next'=>GlobalController::set_str_const_null(''),
            'string_item_ids_next'=>GlobalController::set_str_const_null(''),
            'string_relit_ids_next'=>GlobalController::set_str_const_null(''),
            'string_all_codes_next'=>GlobalController::set_str_const_null(''),
            'string_next' => $string_next,
            'saveurl_show' =>$saveurl_show,
            'nolink_id' =>$nolink_id
])
        @endif
        {{--        @endif--}}
        {{-- @if((($prev_item) ||($next_item)) & ($base_right['is_list_base_create'] == true))--}}
        {{-- "@if((($prev_item) | ($next_item)) | ($base_right['is_list_base_create'] == true))" - так не использовать--}}
        @if((($prev_item) || ($next_item)) || ($base_right['is_list_base_create'] == true))
            <br>
            <div class="row">
                <div class="col-6 text-center">
                    @if(($prev_item) ||($next_item))
                        <ul class="pagination">
                            {{--        <li class="page-item"><a class="page-link"--}}
                            {{--                                 @if($prev_item)--}}
                            {{--                                 href="{{route('item.item_index', ['project'=>$project, 'item'=>$prev_item, 'role'=>$role,--}}
                            {{--                                'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id, 'par_link'=>$view_link])}}"--}}
                            {{--                                 title="{{$prev_item->cdnm()}}"--}}
                            {{--                                 @else--}}
                            {{--                                 style="cursor:default" href="#" title="{{trans('main.none')}}"--}}
                            {{--                @endif--}}
                            {{--            ><</a></li>--}}
                            @if($prev_item)
                                <li class="page-item">
                                    <a class="page-link" href="{{route('item.item_index', ['project'=>$project, 'item'=>$prev_item, 'role'=>$role,
                            'usercode' =>GlobalController::usercode_calc(),
                                    'relit_id'=>$relit_id,
                                    'called_from_button'=>1,
                                    'view_link'=>GlobalController::set_par_null($view_link),
                                    'string_current'=>$string_current,
                                    'prev_base_index_page'=>$base_index_page,
                                    'prev_body_link_page'=>$body_link_page,
                                    'prev_body_all_page'=>$body_all_page,
                                    'view_ret_id' => $view_ret_id
                                     ])}}"
                                       title="{{$prev_item->cdnm()}}"><</a>
                                    {{--                                'string_link_ids_current'=>$string_link_ids_current,--}}
                                    {{--                                'string_item_ids_current'=>$string_item_ids_current,--}}
                                    {{--                                'string_all_codes_current'=>$string_all_codes_current,--}}
                                </li>
                            @endif
                            {{--        <li class="page-item"><a class="page-link"--}}
                            {{--                                 @if($next_item)--}}
                            {{--                                 href="{{route('item.item_index', ['project'=>$project, 'item'=>$next_item, 'role'=>$role,--}}
                            {{--                                'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id, 'par_link'=>$view_link])}}"--}}
                            {{--                                 title="{{$next_item->cdnm()}}"--}}
                            {{--                                 @else--}}
                            {{--                                 style="cursor:default" href="#" title="{{trans('main.none')}}"--}}
                            {{--                @endif--}}
                            {{--            >></a></li>--}}
                            @if($next_item)
                                <li class="page-item">
                                    <a class="page-link" href="{{route('item.item_index', ['project'=>$project, 'item'=>$next_item, 'role'=>$role,
                            'usercode' =>GlobalController::usercode_calc(),
                                    'relit_id'=>$relit_id,
                                    'called_from_button'=>1,
                                    'view_link'=>GlobalController::set_par_null($view_link),
                                    'string_current'=>$string_current,
                                    'prev_base_index_page'=>$base_index_page,
                                    'prev_body_link_page'=>$body_link_page,
                                    'prev_body_all_page'=>$body_all_page,
                                    'view_ret_id' => $view_ret_id
                                    ])}}"
                                       title="{{$next_item->cdnm()}}">></a>
                                    {{--                                'string_link_ids_current'=>$string_link_ids_current,--}}
                                    {{--                                'string_item_ids_current'=>$string_item_ids_current,--}}
                                    {{--                                'string_all_codes_current'=>$string_all_codes_current,--}}
                                </li>
                            @endif
                        </ul>
                    @endif
                </div>
                <div class="col-6 text-right">
                @if ($base_right['is_list_base_create'] == true)
                    @if($message_bs_validate == "")
                        <!--                        --><?php
                            //                        $heading = 1;
                            //                        $relit_id_par = null;
                            //                        $parent_ret_id_par = null;
                            //                        if ($heading == 1) {
                            //                            $relit_id_par = $relit_id;
                            //                            $parent_ret_id_par = $view_ret_id;
                            //                        } else {
                            //                            $relit_id_par = $view_ret_id;
                            //                            $parent_ret_id_par = $relit_id;
                            //                        }
                            //                        ?>
                            @if($level_array['result'] == true)
                                <div class="dropdown d-inline">
                                    <button type="button" class="btn btn-dreamer btn-sm dropdown-toggle"
                                            data-toggle="dropdown"
                                            title="{{trans('main.add')}}">
                                        <i class="fas fa-plus  d-inline"></i>
                                        {{trans('main.add')}}
                                    </button>
                                    <div class="dropdown-menu">
                                        {{-- Корректировать все поля формы--}}
                                        <a class="dropdown-item" href="{{route('item.ext_create', ['base'=>$item->base,
                                         'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
                                         'relit_id' => $relit_heading_id,
                                         'string_current'=>$string_current,
                                         'heading'=>intval(true),
                                         'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                                         'view_link'=>GlobalController::set_par_null($view_link),
                                         'parent_ret_id' => $view_ret_heading_id,
                             'view_link'=>GlobalController::const_null(),
                             'saveurl_add' =>$saveurl_add,
                             'level_id'=>GlobalController::const_null(),
                             'par_link'=>$tree_array_last_link_id, 'parent_item'=>$tree_array_last_item_id
                                         ])}}"
                                           title="{{trans('main.add') . ' '.GlobalController::alf_text()}}">
                                            {{--                                                    'string_all_codes_current' => $string_all_codes_current,--}}
                                            {{--                                                    'string_link_ids_current' => $string_link_ids_current,--}}
                                            {{--                                                    'string_item_ids_current' => $string_item_ids_current,--}}
                                            {{trans('main.add')}} {{GlobalController::alf_text()}}
                                        </a>
                                        {{-- Цикл по массиву--}}
                                        @foreach($level_array['l_arr'] as $level_id)
                                            <?php
                                            $level = Level::find($level_id);
                                            $level_name = '(' . mb_strtolower($level->name()) . ')';
                                            ?>
                                            @if($level)
                                                <a class="dropdown-item" href="{{route('item.ext_create', ['base'=>$item->base,
                                         'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
                                         'relit_id' => $relit_heading_id,
                                         'string_current'=>$string_current,
                                         'heading'=>intval(true),
                                         'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                                         'view_link'=>GlobalController::set_par_null($view_link),
                                         'parent_ret_id' => $view_ret_heading_id,
                             'view_link'=>GlobalController::const_null(),
                             'saveurl_add' =>$saveurl_add,
                             'level_id'=>GlobalController::const_null(),
                             'par_link'=>$tree_array_last_link_id, 'parent_item'=>$tree_array_last_item_id,
                             'level_id' => $level_id
                             ])}}"
                                                   title="{{trans('main.add')}} {{$level_name}}">
                                                    {{--                                                    'string_all_codes_current' => $string_all_codes_current,--}}
                                                    {{--                                                    'string_link_ids_current' => $string_link_ids_current,--}}
                                                    {{--                                                    'string_item_ids_current' => $string_item_ids_current,--}}
                                                    {{trans('main.add')}} {{$level_name}}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <button type="button" class="btn btn-dreamer btn-sm"
                                        title="{{trans('main.add') . " '". $item->base->name() . "' " . $message_bs_info}}"
                                        onclick="document.location='{{route('item.ext_create', ['base'=>$item->base,
                                         'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
                                         'relit_id' => $relit_heading_id,
                                         'string_current'=>$string_current,
                                         'heading'=>intval(true),
                                         'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                                         'view_link'=>GlobalController::set_par_null($view_link),
                                         'parent_ret_id' => $view_ret_heading_id,
                             'view_link'=>GlobalController::const_null(),
                             'saveurl_add' =>$saveurl_add,
                             'level_id'=>GlobalController::const_null(),
                             'par_link'=>$tree_array_last_link_id, 'parent_item'=>$tree_array_last_item_id
                                         ])}}'">
                                    {{--                                             'string_link_ids_current'=>$string_link_ids_current,--}}
                                    {{--                                             'string_item_ids_current'=>$string_item_ids_current,--}}
                                    {{--                                             'string_all_codes_current'=>$string_all_codes_current,--}}
                                    <i class="fas fa-plus d-inline"></i>&nbsp;{{trans('main.add')}}
                                </button>
                            @endif
                        @endif
                    @endif
                </div>
                {{--            <div class="col-1 text-center">--}}
                {{--                <a href="{{route('item.ext_show', ['item'=>$item, 'role'=>$role])}}"--}}
                {{--                   title="{{trans('main.view')}}{{$item->base->is_code_needed?" (".trans('main.code')." = ".$item->code.")":""}}">--}}
                {{--                    <img src="{{Storage::url('view_record.png')}}" width="15" height="15"--}}
                {{--                         alt="{{trans('main.view')}}">--}}
                {{--                </a>--}}
                {{--            </div>--}}
                {{--            <div class="col-1 text-center">--}}
                {{--                <a href="{{route('item.ext_edit', ['item'=>$item, 'role'=>$role])}}" title="{{trans('main.edit')}}">--}}
                {{--                    <img src="{{Storage::url('edit_record.png')}}" width="15" height="15"--}}
                {{--                         alt="{{trans('main.edit')}}">--}}
                {{--                </a>--}}
                {{--            </div>--}}
                {{--            <div class="col-1 text-center">--}}
                {{--                <a href="{{route('item.ext_delete_question', ['item' => $item, 'role'=>$role, 'heading'=> true])}}"--}}
                {{--                   title="{{trans('main.delete')}}">--}}
                {{--                    <img src="{{Storage::url('delete_record.png')}}" width="15" height="15"--}}
                {{--                         alt="{{trans('main.delete')}}">--}}
                {{--                </a>--}}
                {{--            </div>--}}
            </div>
        @endif
        <details>
            <summary>{{trans('main.select')}}</summary>
            {{-- Связи--}}
            {{-- Взаимосвязанные шаблоны--}}
            {{-- "count($array_relips) > 1" - т.е. есть взаимосвязанные шаблоны--}}
            @if(count($array_relips) > 1)
                <div class="row">
                    <div class="col-12 text-center mt-1">
                        @foreach($array_relips as $relit_key_id=>$array_relip_id)
                            <?php
                            $relit = null;
                            if ($relit_key_id == 0) {
                                $relit = null;
                            } else {
                                $relit = Relit::findOrFail($relit_key_id);
                            }
                            // Находим родительский проект
                            $relip_select_body_project = Project::findOrFail($array_relip_id);
                            // $v_link используется
                            if ($v_link) {
                                // $v_link используется
                                $view_value_link = $v_link->id;
                            } else {
                                $view_value_link = GlobalController::const_null();
                            }
                            ?>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Relips">
                                <button type="button" class="btn btn-icon"
                                        {{--                                'called_from_button'=>1 - вызов из кнопки--}}
                                        onclick='document.location="{{route('item.item_index', ['project'=>$project, 'item'=>$it_local, 'role'=>$role,
                                      'usercode' =>GlobalController::usercode_calc(),
                                      'relit_id'=>$relit_id,
                                      'called_from_button'=>1,
                                      'view_link'=>$view_value_link,
                                      'view_ret_id'=>$relit_key_id,
                                      'string_current'=>$string_current,
                                      'prev_base_index_page'=>$base_index_page,
                                      'prev_body_link_page'=>$body_link_page,
                                      'prev_body_all_page'=>$body_all_page
                                                                  ])}}"'
                                        title="{{$relip_select_body_project->name() . ' ('.mb_strtolower(trans('main.relip')).')'}}">
                                    <i>
                                        {{$relip_select_body_project->name()}}
                                        @if($relit)
                                            - <span
                                                class="text-project">{{mb_strtolower($relit->title())}}</span>
                                        @endif
                                    </i>
                                    {{--                                    - {{$relit_key_id}}- {{$relip_select_body_project->id}}--}}
                                    @if(isset($view_ret_id))
                                        @if($relit_key_id == $view_ret_id)
                                            <span class="text-danger font-weight-bold">
                                            {{-- Этот символ используется в нескольких местах--}}
                                            &#10003;
                                            </span>
                                        @endif
                                    @endif
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            {{-- Нужно '@if(count($next_all_links)>0)'--}}
            @if(count($next_all_links)>0)
                {{-- Для команды '@if(!($view_link && count($next_all_links) == 1))', чтобы исключить вариант count($next_all_links) == 0--}}
                {{--                Не высвечивать кнопку "Связи", если одна связь и $next_all_is_enable=false--}}
                {{--                    @if(($next_all_is_enable) || (count($next_all_links)>1))--}}
                {{--                Не высвечивать кнопку "Связи", если одна связь и $view_link!=false--}}
                {{-- Похожая проверка по смыслу 'count($next_all_links) == 1' в ItemController::item_index() и item_index.php--}}
                @if(!($view_link && count($next_all_links) == 1))
                    <div class="row">
                        <div class="col-12 text-center">
                            {{--                        <div class="dropdown">--}}
                            {{--                            <button type="button" class="btn btn-dreamer dropdown-toggle" data-toggle="dropdown"--}}
                            {{--                                    title="{{trans('main.link')}}">--}}
                            {{--                                <i class="fas fa-link d-inline"></i>--}}
                            {{--                                {{trans('main.link')}}--}}
                            {{--                            </button>--}}


                            {{--                            <div class="dropdown-menu">--}}
                            {{--                                --}}{{-- Если во всех $links не выводятся вычисляемые наименования, то выводится вариант 'all'--}}
                            {{--                                @if($next_all_is_enable)--}}
                            {{--                                    <a class="dropdown-item" href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,--}}
                            {{--                                  'usercode' =>GlobalController::usercode_calc(),--}}
                            {{--                                  'relit_id'=>$relit_id,--}}
                            {{--                                  'view_link'=>GlobalController::par_link_const_textnull(),--}}
                            {{--                                  'view_ret_id'=>$view_ret_id,--}}
                            {{--                                  'string_current'=>$string_current,--}}
                            {{--                                  'prev_base_index_page'=>$base_index_page,--}}
                            {{--                                  'prev_body_link_page'=>$body_link_page,--}}
                            {{--                                  'prev_body_all_page'=>$body_all_page--}}
                            {{--                                  ])}}"--}}
                            {{--                                       title="{{$item->name()}}">--}}
                            {{--                                        --}}{{--                                        'string_link_ids_current'=>$string_link_ids_current,--}}
                            {{--                                        --}}{{--                                        'string_item_ids_current'=>$string_item_ids_current,--}}
                            {{--                                        --}}{{--                                        'string_all_codes_current'=>$string_all_codes_current,--}}
                            {{--                                        {{GlobalController::option_all()}}--}}
                            {{--                                        @if($view_link == null)--}}
                            {{--                                            --}}{{-- Этот символ используется в нескольких местах--}}
                            {{--                                            &#10003;--}}
                            {{--                                        @endif--}}
                            {{--                                    </a>--}}
                            {{--                                @endif--}}
                            {{--                                @foreach($next_all_links as $key=>$value)--}}
                            {{--                                    <a class="dropdown-item" href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,--}}
                            {{--                                          'usercode' =>GlobalController::usercode_calc(),--}}
                            {{--                                          'relit_id'=>$relit_id,--}}
                            {{--                                          'view_link'=>$value->id,--}}
                            {{--                                          'view_ret_id'=>$view_ret_id,--}}
                            {{--                                          'string_current'=>$string_current,--}}
                            {{--                                          'prev_base_index_page'=>$base_index_page,--}}
                            {{--                                          'prev_body_link_page'=>$body_link_page,--}}
                            {{--                                          'prev_body_all_page'=>$body_all_page--}}
                            {{--                                          ])}}"--}}
                            {{--                                       title="{{$value->child_labels()}}">--}}
                            {{--                                        --}}{{--                                        'string_link_ids_current'=>$string_link_ids_current,--}}
                            {{--                                        --}}{{--                                        'string_item_ids_current'=>$string_item_ids_current,--}}
                            {{--                                        --}}{{--                                        'string_all_codes_current'=>$string_all_codes_current,--}}
                            {{--                                        {{$value->child_labels()}}--}}
                            {{--                                        @if(isset($view_link))--}}
                            {{--                                            @if($value->id == $view_link->id)--}}
                            {{--                                                --}}{{-- Этот символ используется в нескольких местах--}}
                            {{--                                                &#10003;--}}
                            {{--                                            @endif--}}
                            {{--                                        @endif--}}
                            {{--                                        @if(isset($array["\x00*\x00items"][$value->id]))--}}
                            {{--                                            *--}}
                            {{--                                        @endif--}}
                            {{--                                    </a>--}}
                            {{--                                @endforeach--}}
                            {{--                            </div>--}}
                            {{--                        </div>--}}
                            {{--                            Вывод "Все связи"--}}
                            @if($next_all_is_enable)
                                <div class="btn-group btn-group-sm" role="group" aria-label="Link">
                                    <button type="button" class="btn btn-icon"
                                            {{--                                'called_from_button'=>1 - вызов из кнопки--}}
                                            onclick='document.location="{{route('item.item_index', ['project'=>$project, 'item'=>$it_local, 'role'=>$role,
                                                                      'usercode' =>GlobalController::usercode_calc(),
                                                                      'relit_id'=>$relit_id,
                                                                      'called_from_button'=>1,
                                                                      'view_link'=>GlobalController::par_link_const_textnull(),
                                                                      'view_ret_id'=>$view_ret_id,
                                                                      'string_current'=>$string_current,
                                                                      'prev_base_index_page'=>$base_index_page,
                                                                      'prev_body_link_page'=>$body_link_page,
                                                                      'prev_body_all_page'=>$body_all_page
                                                                  ])}}"'
                                            title="{{GlobalController::option_all_links()}}">
                                <span class="text-label">
                                {{GlobalController::option_all_links()}}
                                </span>
                                        @if($view_link == null)
                                            <span class="text-danger font-weight-bold">
                                            {{-- Этот символ используется в нескольких местах--}}
                                            &#10003;
                                            </span>
                                        @endif
                                    </button>
                                </div>
                            @endif
                            @foreach($next_all_links as $key=>$value)
                                <?php
                                // $view_ret_id нужно передавать в параметрах
                                //$base_link_right = GlobalController::base_right($value->child_base, $role, $view_ret_id);
                                // Не нужно (т.е. высвечивается связь, $relit_id вычисляется отдельно и после(вывода на экран кнопок связей))
                                //$child_labels = $value->child_labels($base_link_right);
                                $child_labels = $value->child_labels($emoji_enable,null,true);
                                ?>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Links">
                                    <button type="button" class="btn btn-icon"
                                            {{--                                'called_from_button'=>1 - вызов из кнопки--}}
                                            onclick='document.location="{{route('item.item_index', ['project'=>$project, 'item'=>$it_local, 'role'=>$role,
                                                                  'usercode' =>GlobalController::usercode_calc(),
                                                                  'relit_id'=>$relit_id,
                                                                  'called_from_button'=>1,
                                                                  'view_link'=>$value->id,
                                                                  'view_ret_id'=>$view_ret_id,
                                                                  'string_current'=>$string_current,
                                                                  'prev_base_index_page'=>$base_index_page,
                                                                  'prev_body_link_page'=>$body_link_page,
                                                                  'prev_body_all_page'=>$body_all_page
                                                                  ])}}"'
                                            title="{{$child_labels . ' ('.mb_strtolower(trans('main.link')).')'}}">
                                <span class="text-label"
{{--                                          @if(isset($view_link))--}}
                                    {{--                                          @if($value->id == $view_link->id)--}}
                                    {{--                                          style="text-decoration: underline;"--}}
                                    {{--                                                                                  @endif--}}
                                    {{--                                        @endif--}}
                                >
                                {{$child_labels}}
                                </span>
                                        {{-- $v_link используется--}}
                                        @if(isset($v_link))
                                            {{-- $v_link используется--}}
                                            @if($value->id == $v_link->id)
                                                <span class="text-danger font-weight-bold">
                                            {{-- Этот символ используется в нескольких местах--}}
                                            &#10003;
                                                </span>
                                            @endif
                                        @endif
                                        @if(isset($array["\x00*\x00items"][$value->id]))
                                            *
                                        @endif
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
            {{--            <hr>--}}
        </details>
    </div>
    {{--    <hr align="center" width="100%" size="2" color="#ff0000"/>--}}
    {{--        &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195; &#8595;	&#8195;--}}
    {{--        <hr>--}}
    {{--        <div class="text-center">&#8595;</div>--}}
    {{--    <hr>--}}
    {{--Похожие команды в ItemController::calc_tree_array() и item_index.php--}}
    @if($view_link)
        <?php
        // $v_link используется
        $level_array = $v_link->child_base->level_array();
        ?>
        {{--        <hr>--}}
        {{--        <br>--}}
        {{--        <div class="text-center">&#8595;</div>--}}
        <!--                --><?php
        //                $mains = Main::all()->where('parent_item_id', $item->id)->where('link_id', $view_link->id)->sortBy(function ($main) {
        //                    return $main->link->child_base->name() . $main->child_item->name();
        //                });
        //                ?>
        {{--        Не удалять--}}
        {{--        <p>--}}
        {{--        <div class="container-fluid">--}}
        {{--            <div class="row">--}}
        {{--                <div class="col text-left">--}}
        {{--                    <h3>--}}
        {{--                    </h3>--}}
        {{--                    <h3>--}}
        {{--                        <a href="{{route('item.base_index', ['base'=>$view_link->child_base,--}}
        {{--                            'project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])}}"--}}
        {{--                           title="{{$view_link->child_base->names()}}">--}}
        {{--                            {{$view_link->child_labels()}}--}}
        {{--                        </a>--}}
        {{--                        ({{$view_link->parent_label()}} = {{$item->name()}}):--}}
        {{--                    </h3>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}
        {{--        </p>--}}
        @if($base_body_right['is_list_base_calc'] == true)
            <?php
            //                $message_bs_mc = GlobalController::base_maxcount_message($view_link->child_base);
            //                $message_bs_byuser_mc = GlobalController::base_byuser_maxcount_message($view_link->child_base);
            //                $message_ln_mc = GlobalController::link_maxcount_message($view_link);
            //                $message_it_mc = GlobalController::link_item_maxcount_message($view_link);
            //                $message_mc = ($message_bs_mc == "" ? "" : ', ' . PHP_EOL . $message_bs_mc)
            //                    . ($message_bs_byuser_mc == "" ? "" : ', ' . PHP_EOL . $message_bs_byuser_mc)
            //                    . ($message_ln_mc == "" ? "" : ', ' . PHP_EOL . $message_ln_mc)
            //                    . ($message_it_mc == "" ? "" : ', ' . PHP_EOL . $message_it_mc);
            //                $message_link = GlobalController::link_maxcount_validate($project, $view_link, true);
            //                $message_item = GlobalController::link_item_maxcount_validate($project, $item, $view_link, true);

            //      $next_all_links = $item->base->parent_links->where('id', '!=', $view_link->id);
            // Исключить вычисляемые поля
            // Не удалять
            //        $next_all_links = $item->base->parent_links->where('parent_is_parent_related', false)->where('id', '!=', $view_link->id);
            //
            //        $next_all_links_fact = DB::table('mains')
            //            ->select('link_id')
            //            ->where('parent_item_id', $item->id)
            //            ->where('link_id', '!=', $view_link->id)
            //            ->distinct()
            //            ->get()
            //            ->groupBy('link_id');

            // $next_all_links = $item->base->parent_links->where('parent_is_parent_related', false);
            // Не удалять
            //                $next_all_links_fact = DB::table('mains')
            //                    ->select('link_id')
            //                    ->where('parent_item_id', $item->id)
            //                    ->distinct()
            //                    ->get()
            //                    ->groupBy('link_id');

            //                $array = objectToarray($next_all_links_fact);

            ?>
            <p>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-8 text-left">
                        <h6>
                            {{--                        @if($view_link)--}}
                            {{--                        {{$view_link->child_labels()}}:{{$view_link->child_base->name()}}--}}
                            {{--                        @else--}}
                            {{--                            {{$item->base->name()}}:--}}
                            {{--                        @endif--}}
                            {{-- $v_link используется--}}
                            @if($base_body_right['is_bsmn_base_enable'] == true)
                                <a href="{{route('item.base_index', ['base'=>$v_link->child_base,
                        'project'=>$project, 'role'=>$role, 'relit_id'=>$view_ret_id])}}"
                                   title="{{$v_link->child_base->names($base_body_right) . $message_ln_info}}">
                                    @endif
                                    {{-- $v_link используется--}}
                                    {{$v_link->child_labels($emoji_enable, $base_body_right, true)}}2222222222
                                    @if($base_body_right['is_bsmn_base_enable'] == true)
                                </a>
                            @endif
                        </h6>
                        @include('layouts.project.show_relip_info',['calc_relip_info'=>$calc_body_relip_info])
                    </div>
                    <div class="col-4 text-right">
                    {{--                        @if ((count($body_items) > 0) || ($base_body_right['is_list_base_create'] == true))--}}
                    {{--                Такая же проверка на 'is_list_base_create' == true && 'is_edit_link_update' == true в item_index.php и ItemController.php--}}
                    {{--                @if ($base_body_right['is_list_base_create'] == true)--}}
                    {{--                    @if ($base_body_right['is_list_base_create'] == true && $base_body_right['is_edit_link_update'] == true)--}}
                    {{-- $v_link используется--}}
                    @if ($next_all_is_create[$v_link->id] == true)
                        @if($message_ln_validate == "")
                            <!--                                --><?php
                                //                                $heading = 0;
                                //                                $relit_par_id = null;
                                //                                $parent_ret_par_id = null;
                                //                                if ($heading == 1) {
                                //                                    $relit_par_id = $relit_id;
                                //                                    $parent_ret_par_id = $view_ret_id;
                                //                                } else {
                                //                                    $relit_par_id = $view_ret_id;
                                //                                    $parent_ret_par_id = $relit_id;
                                //                                }
                                //                                ?>
                                @if($level_array['result'] == true)
                                    <div class="dropdown d-inline">
                                        <button type="button" class="btn btn-dreamer btn-sm dropdown-toggle"
                                                data-toggle="dropdown"
                                                title="{{trans('main.add')}}">
                                            <i class="fas fa-plus  d-inline"></i>
                                            {{trans('main.add')}}
                                        </button>
                                        <div class="dropdown-menu">
                                            {{-- Корректировать все поля формы--}}
                                            {{-- $v_link используется--}}
                                            <a class="dropdown-item" href="{{route('item.ext_create', ['base'=>$v_link->child_base_id,
                                    'project'=>$project, 'role'=>$role,
                                    'usercode' =>GlobalController::usercode_calc(),
                         'relit_id' => $relit_body_id,
                         'string_current' => $string_current,
                         'heading'=>intval(false),
                         'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                         'view_link'=>$view_link,
                         'parent_ret_id' => $view_ret_body_id,
                             'view_link'=>$view_link,
                             'saveurl_add' =>$saveurl_add,
                             'level_id' =>GlobalController::const_null(),
                             'par_link'=>$view_link, 'parent_item'=>$item
                         ])}}"
                                               title="{{trans('main.add') . ' '.GlobalController::alf_text()}}">
                                                {{--                                                    'string_all_codes_current' => $string_all_codes_current,--}}
                                                {{--                                                    'string_link_ids_current' => $string_link_ids_current,--}}
                                                {{--                                                    'string_item_ids_current' => $string_item_ids_current,--}}
                                                {{trans('main.add')}} {{GlobalController::alf_text()}}
                                            </a>
                                            {{-- Цикл по массиву--}}
                                            @foreach($level_array['l_arr'] as $level_id)
                                                <?php
                                                $level = Level::find($level_id);
                                                $level_name = '(' . mb_strtolower($level->name()) . ')';
                                                ?>
                                                @if($level)
                                                    {{-- $v_link используется--}}
                                                    <a class="dropdown-item" href="{{route('item.ext_create', ['base'=>$v_link->child_base_id,
                                    'project'=>$project, 'role'=>$role,
                                    'usercode' =>GlobalController::usercode_calc(),
                         'relit_id' => $relit_body_id,
                         'string_current' => $string_current,
                         'heading'=>intval(false),
                         'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                         'view_link'=>$view_link,
                         'parent_ret_id' => $view_ret_body_id,
                             'view_link'=>$view_link,
                             'saveurl_add' =>$saveurl_add,
                             'level_id' =>GlobalController::const_null(),
                             'par_link'=>$view_link, 'parent_item'=>$item,
                             'level_id' => $level_id
                             ])}}"
                                                       title="{{trans('main.add')}} {{$level_name}}">
                                                        {{--                                                    'string_all_codes_current' => $string_all_codes_current,--}}
                                                        {{--                                                    'string_link_ids_current' => $string_link_ids_current,--}}
                                                        {{--                                                    'string_item_ids_current' => $string_item_ids_current,--}}
                                                        {{trans('main.add')}} {{$level_name}}
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    {{-- Выводится $message_mc--}}
                                    {{-- $v_link используется--}}
                                    {{--                             'view_link'=>$view_link,--}}
                                    <button type="button" class="btn btn-dreamer btn-sm"
                                            title="{{trans('main.add'). " '" . $v_link->child_base->name() . "'" . $message_ln_info}}"
                                            onclick="document.location='{{route('item.ext_create', ['base'=>$v_link->child_base_id,
                                    'project'=>$project, 'role'=>$role,
                                    'usercode' =>GlobalController::usercode_calc(),
                         'relit_id' => $relit_body_id,
                         'string_current' => $string_current,
                         'heading'=>intval(false),
                         'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                         'view_link'=>$view_link,
                         'parent_ret_id' => $view_ret_body_id,
                             'saveurl_add' =>$saveurl_add,
                             'level_id' =>GlobalController::const_null(),
                             'par_link'=>$view_link, 'parent_item'=>$item
                         ])}}'">
                                        {{-- 'view_link'=>GlobalController::const_null(),--}}
                                        {{--                                    'string_all_codes_current' => $string_all_codes_current,--}}
                                        {{--                                    'string_link_ids_current' => $string_link_ids_current,--}}
                                        {{--                                    'string_item_ids_current' => $string_item_ids_current,--}}
                                        <i class="fas fa-plus d-inline"></i>&nbsp;{{trans('main.add')}}
                                    </button>
                                @endif
                            @endif
                        @endif
                        {{--                        @endif--}}
                    </div>
                </div>
            </div>
            </p>
        @endif
        {{-- Проверка нужна--}}
        @if($body_items)
            @if (count($body_items) > 0)
                {{--        Выводится список записей по одной связи $view_link--}}
                {{--        Используется "'heading'=>intval(false)"--}}
                {{--        'view_link' передается затем (в list\table.php) в 'item.ext_show' как 'par_link'--}}
                {{--        Параметры 'relit_id' и 'view_ret_id' передаются в зависимости от значения $heading--}}
                {{--        "'its_page'=>$its_body_page->get()" "->get()" нужно--}}
                {{-- $v_link используется--}}
                @include('list.table',['base'=>$v_link->child_base, 'project'=>$project, 'role'=>$role,
            'links_info'=>$child_body_links_info,
            'items'=>$body_items,
            'its_page'=>$its_body_page,
            'base_right'=>$base_body_right,
            'relit_id'=>$relit_body_id,
            'heading'=>intval(false),
            'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
            'view_link'=>$view_link,
            'view_ret_id'=>$view_ret_body_id, 'parent_item'=>$item, 'is_table_body'=>false,
                'base_index'=>false, 'item_heading_base'=>false, 'item_body_base'=>true,
                'string_link_ids_current' => $string_link_ids_current,
                'string_item_ids_current' => $string_item_ids_current,
                'string_relit_ids_current' => $string_relit_ids_current,
                'string_all_codes_current' => $string_all_codes_current,
                'string_current' => $string_current,
                'string_link_ids_next'=>$string_link_ids_next,
                'string_item_ids_next'=>$string_item_ids_next,
                'string_relit_ids_next'=>$string_relit_ids_next,
                'string_all_codes_next'=>$string_all_codes_next,
                'string_next' => $string_next,
                'saveurl_show' =>$saveurl_show,
                'nolink_id' =>null
                ])
                {{$body_items->links()}}
                <?php
                $steps_recycle_code = StepController::steps_recycle_code($v_link, 'stored_procedures');
                ?>
                @foreach($steps_recycle_code as $step_rec)
                    <?php
                    $link3 = Link::find($step_rec->third);
                    $link4 = Link::find($step_rec->fourth);
                    $title_text = $item->name();
                    // "$link3 && $link4" - так использовать проверку
                    if ($link3 && $link4) {
                        $title_text = $link4->parent_base->names() . " -> " . $link3->parent_base->names();
                    }
                    ?>
                    <button type="button" class="btn btn-dreamer btn-sm"
                            {{--                        title="{{trans('main.recalculation') . " " . $item->name() . "->" . $v_link->child_base->name()}}"--}}
                            title="{{$title_text}}"
                            onclick="document.location='{{route('global.recycle_link', [
                                    'item'=>$item,
                                    'link_id1'=>$step_rec->first,
                                    'link_id2'=>$step_rec->second,
                                    'link_id3'=>$step_rec->third,
                                    'link_id4'=>$step_rec->fourth,
                                    'project'=>$project, 'role'=>$role,
                                    'usercode' =>GlobalController::usercode_calc(),

                         ])}}'">
                        <i class="fas fa-recycle d-inline"></i>&nbsp
                        {{--                    {{trans('main.recalculation') . " " . $item->name() . "->" . $v_link->child_base->name()}}--}}
                        {{$title_text}}
                    </button>
                @endforeach
            @endif
        @endif
        {{--            Вывод всех записей, с разным link--}}
        {{--Похожие команды в ItemController::calc_tree_array() и item_index.php--}}
    @else
        @if(count($next_all_links)>0)
            <div class="container-fluid">
                <div class="row">
                    <div class="col-10 text-left">
                        @if($next_all_is_sortdate)
                            <h3>
                                {{--                        @if($view_link)--}}
                                {{--                        {{$view_link->child_labels()}}:{{$view_link->child_base->name()}}--}}
                                {{--                        @else--}}
                                {{--                            {{$item->base->name()}}:--}}
                                {{--                        @endif--}}
                                {{trans('main.all_links')}}:
                            </h3>
                        @endif
                        @include('layouts.project.show_relip_info',['calc_relip_info'=>$calc_body_relip_info])
                    </div>
                    <div class="col-2 text-right">
                        {{-- Вся кнопка 'Добавить' доступна (для связей)--}}
                        @if($next_all_is_all_create == true)
                            <div class="dropdown">
                                <button type="button" class="btn btn-dreamer dropdown-toggle btn-sm"
                                        data-toggle="dropdown"
                                        title="{{trans('main.add')}}">
                                    <i class="fas fa-plus d-inline"></i>
                                    {{trans('main.add')}}
                                </button>
                                <div class="dropdown-menu">
                                @foreach($next_all_links as $key=>$value)
                                    <?php
                                    $level_array = $value->child_base->level_array();
                                    ?>
                                    @if($next_all_is_create[$value->id] == true)
                                        @if($message_ln_link_array_item[$value->id] == "")
                                            <!--                                                --><?php
                                                //                                                $heading = 0;
                                                //                                                $relit_id_par = null;
                                                //                                                $parent_ret_id_par = null;
                                                //                                                if ($heading == 1) {
                                                //                                                    $relit_id_par = $relit_id;
                                                //                                                    $parent_ret_id_par = $view_ret_id;
                                                //                                                } else {
                                                //                                                    $relit_id_par = $view_ret_id;
                                                //                                                    $parent_ret_id_par = $relit_id;
                                                //                                                }
                                                //                                                ?>
                                                @if($level_array['result'] == true)
                                                    <a class="dropdown-item" href="{{route('item.ext_create', ['base'=>$value->child_base_id,
                                                                                            'project'=>$project, 'role'=>$role,
                                                                                             'usercode' =>GlobalController::usercode_calc(),
                                                                                 'relit_id' => $relit_body_id,
                                                                                 'string_current' => $string_current,
                                                                                 'heading'=>intval(false),
                                                                                 'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                                                                                 'view_link'=>$value,
                                                                                 'parent_ret_id' => $view_ret_body_id,
                                                                                 'saveurl_add' =>$saveurl_add,
                                                                                 'par_link'=>$value, 'parent_item'=>$item,
                                                                                 'level_id' =>GlobalController::const_null()
                                                                                 ])}}"
                                                       title="{{trans('main.add') . $message_ln_array_info[$value->id]}} {{GlobalController::alf_text()}}">
                                                        {{--                                                    'string_all_codes_current' => $string_all_codes_current,--}}
                                                        {{--                                                    'string_link_ids_current' => $string_link_ids_current,--}}
                                                        {{--                                                    'string_item_ids_current' => $string_item_ids_current,--}}
                                                        {{$value->child_label()}} {{GlobalController::alf_text()}}
                                                        @if(isset($array["\x00*\x00items"][$value->id]))
                                                            *
                                                        @endif
                                                    </a>
                                                    {{-- Цикл по массиву--}}
                                                    @foreach($level_array['l_arr'] as $level_id)
                                                        <?php
                                                        $level = Level::find($level_id);
                                                        $level_name = '(' . mb_strtolower($level->name()) . ')';
                                                        ?>
                                                        @if($level)
                                                            <a class="dropdown-item" href="{{route('item.ext_create', ['base'=>$value->child_base_id,
                                                                                            'project'=>$project, 'role'=>$role,
                                                                                             'usercode' =>GlobalController::usercode_calc(),
                                                                                 'relit_id' => $relit_body_id,
                                                                                 'string_current' => $string_current,
                                                                                 'heading'=>intval(false),
                                                                                 'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                                                                                 'view_link'=>$value,
                                                                                 'parent_ret_id' => $view_ret_body_id,
                                                                                 'saveurl_add' =>$saveurl_add,
                                                                                 'par_link'=>$value, 'parent_item'=>$item,
                                                                                 'level_id' =>$level_id
                                                                                 ])}}"
                                                               title="{{trans('main.add') . $message_ln_array_info[$value->id]}} {{$level_name}}">
                                                                {{--                                                    'string_all_codes_current' => $string_all_codes_current,--}}
                                                                {{--                                                    'string_link_ids_current' => $string_link_ids_current,--}}
                                                                {{--                                                    'string_item_ids_current' => $string_item_ids_current,--}}
                                                                {{$value->child_label()}} {{$level_name}}
                                                                @if(isset($array["\x00*\x00items"][$value->id]))
                                                                    *
                                                                @endif
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    {{-- Разницы нет, что присваивать 'view_link', т.к. используется переменная $saveurl_add в ItemController::ext_store()--}}
                                                    {{-- 'view_link'=>$value,--}}
                                                    {{-- 'view_link'=>GlobalController::const_null(),--}}
                                                    <a class="dropdown-item" href="{{route('item.ext_create', ['base'=>$value->child_base_id,
                                                                                            'project'=>$project, 'role'=>$role,
                                                                                             'usercode' =>GlobalController::usercode_calc(),
                                                                                 'relit_id' => $relit_body_id,
                                                                                 'string_current' => $string_current,
                                                                                 'heading'=>intval(false),
                                                                                 'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                                                                                 'view_link'=>$value,
                                                                                 'parent_ret_id' => $view_ret_body_id,
                                                                                 'saveurl_add' =>$saveurl_add,
                                                                                 'par_link'=>$value, 'parent_item'=>$item,
                                                                                 'level_id' =>GlobalController::const_null()
                                                                                 ])}}"
                                                       title="{{trans('main.add') . $message_ln_array_info[$value->id]}}">
                                                        {{--                                                    'string_all_codes_current' => $string_all_codes_current,--}}
                                                        {{--                                                    'string_link_ids_current' => $string_link_ids_current,--}}
                                                        {{--                                                    'string_item_ids_current' => $string_item_ids_current,--}}
                                                        {{$value->child_label()}}
                                                        @if(isset($array["\x00*\x00items"][$value->id]))
                                                            *
                                                        @endif
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @if($next_all_mains)
                @if(count($next_all_mains) > 0)
                    {{--        Выводится список записей по всем связям--}}
                    {{--        Используется "'heading'=>intval(false)"--}}
                    {{--        'view_link' передавать не нужно, затем (в list\all.php) в 'item.ext_show' как 'par_link' передается $main->link--}}
                    {{--        Параметры 'relit_id' и 'view_ret_id' передаются в зависимости от значения $heading--}}
                    {{--                'message_ln_array_info' => $message_ln_array_info, 'message_ln_link_array_item' => $message_ln_link_array_item--}}
                    @include('list.all',['project'=>$project, 'role'=>$role,
                'relit_id'=>$relit_body_id,
                'view_ret_id'=>$view_ret_body_id,
                'next_all_mains'=>$next_all_mains,
                'next_all_is_code_enable'=>$next_all_is_code_enable,
                'next_all_is_calcname' => $next_all_is_calcname,
                'next_all_is_enable' => $next_all_is_enable,
                'next_all_is_tileview' => $next_all_is_tileview,
                'next_all_is_viewcards' => $next_all_is_viewcards,
                'next_all_is_sortdate' => $next_all_is_sortdate,
                'heading'=>intval(false),
                'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                'parent_item'=>$item, 'is_table_body'=>false,
                'base_index'=>false, 'item_heading_base'=>false, 'item_body_base'=>true,
                'string_link_ids_current' => $string_link_ids_current,
                'string_item_ids_current' => $string_item_ids_current,
                'string_relit_ids_current' => $string_relit_ids_current,
                'string_all_codes_current' => $string_all_codes_current,
                'string_current' => $string_current,
                'string_link_ids_next'=>$string_link_ids_next,
                'string_item_ids_next'=>$string_item_ids_next,
                'string_relit_ids_next'=>$string_relit_ids_next,
                'string_all_codes_next'=>$string_all_codes_next,
                'string_next' => $string_next,
                'string_link_ids_array_next' => $string_link_ids_array_next,
                'string_item_ids_array_next' => $string_item_ids_array_next,
                'string_relit_ids_array_next' => $string_relit_ids_array_next,
                'string_all_codes_array_next' => $string_all_codes_array_next,
                'string_array_next' => $string_array_next,
                'saveurl_show' =>$saveurl_show
                ])
                    {{$next_all_mains->links()}}
                @endif
            @endif
        @endif
    @endif
    <blockquote class="text-title pt-2 pl-0 pr-0"><?php echo nl2br($project->dc_int()); ?></blockquote>
@endsection
