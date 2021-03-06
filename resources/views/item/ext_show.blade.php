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
    ?>
    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])
    <h3 class="display-5">
        @if ($type_form == 'show')
            {{trans('main.viewing_record')}}
        @elseif($type_form == 'delete_question')
            {{trans('main.delete_record_question')}}?
        @endif
        <span class="text-label">-</span> <span class="text-title">{{$item->base->info()}}</span>
    </h3>
    <br>
    <hr>
    <?php
    if ($base_right['is_hier_base_enable'] == true) {
        $result = ItemController::form_parent_deta_hier($item->id, $project, $role, $relit_id, false);
        echo $result;
    }
    ?>
    <ul>
        <p class="text-label">Id: <span class="text-related">
{{--                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role, 'relit_id'=>$relit_id,--}}
                {{--                        'usercode' =>GlobalController::usercode_calc()])}}"--}}
                {{--                   title="">--}}
                {{$item->id}}
                {{--                </a>--}}
            </span>
        </p>
        @if($base->is_code_needed == true)
            <p class="text-label">{{trans('main.code')}}: <span class="text-related">
{{--                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,--}}
                    {{--                                'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id])}}"--}}
                    {{--                           title="">--}}
                    {{$item->code}}
                    {{--                        </a>--}}
                    </span></p>
        @endif
        <p class="text-label">
        @if($base_right['is_list_base_sort_creation_date_desc'] == true)
            <li>
                {{trans('main.date')}}: <b>
                    {{$item->created_date()}}
                </b>
            </li>
        @endif
        {{--    @foreach($array_plan as $key=>$value)--}}
        {{--        <?php--}}
        {{--        $result = ItemController::get_items_for_link(Link::find($key));--}}
        {{--        $items = $result['result_parent_base_items'];--}}
        {{--        $item_work = Item::find($value);--}}
        {{--        ?>--}}
        {{--        --}}{{--    ???????????????? ??????????; ?????? ?????????????????????? ???????????? '$item_work->name()'--}}
        {{--        @if($item_work)--}}
        {{--            --}}{{--            <p>{{$result['result_parent_label']}} ({{$result['result_parent_base_name']}}):--}}
        {{--            <p>{{$result['result_parent_label']}}:--}}
        {{--                <span class="text-related">{{$item_work->name()}}</span></p>--}}
        {{--        @endif--}}
        {{--    @endforeach--}}

        {{--        ?????????? ????????????--}}
        @foreach($array_calc as $key=>$value)
            <?php
            $link = Link::find($key);
            $item_find = GlobalController::view_info($item->id, $key);
            ?>
            @if($link && $item_find)
                <?php
                    $base_link_right = null;
                    if ($heading == 1 || $base_index_page > 0) {
                        $base_link_right = GlobalController::base_link_right($link, $role, $relit_id);
                    } else {
                        //$base_link_right = GlobalController::base_link_right($link, $role, $parent_ret_id);
                        $base_link_right = GlobalController::base_link_right($link, $role, $link->parent_relit_id);
                    }
                ?>
                @if($base_link_right['is_show_link_enable'] == true)
                    <li>
                        @if($base_link_right['is_bsmn_base_enable'] == true)
                            <a href="{{route('item.base_index',['base'=>$link->parent_base_id, 'project'=>$project, 'role'=>$role, 'relit_id'=>$link->parent_relit_id])}}"
                               title="{{$link->parent_base->names($base_link_right)}}">
                                @include('layouts.item.ext_show.parent_label', ['link'=>$link, 'par_link'=>$par_link])
                            </a>
                        @else
                            @include('layouts.item.ext_show.parent_label', ['link'=>$link, 'par_link'=>$par_link])
                        @endif
                        @if($link->parent_base->type_is_text())
                            {{--                            <span class="text-related">--}}<b>
                                <?php
                                echo GlobalController::it_txnm_n2b($item_find);
                                ?>
                                {{--                        </span>--}}</b>
                        @elseif($link->parent_base->type_is_image())
                            <br>
                            @include('view.img',['item'=>$item_find, 'size'=>"medium", 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'title'=>""])
                            {{--                            <a href="{{Storage::url($item_find->filename())}}">--}}
                            {{--                                <img src="{{Storage::url($item_find->filename())}}" height="250"--}}
                            {{--                                     alt="" title="{{$item_find->title_img()}}">--}}
                            {{--                            </a>--}}
                        @elseif($link->parent_base->type_is_document())
                            <b>
                                @include('view.doc',['item'=>$item_find, 'usercode'=>GlobalController::usercode_calc()])
                                {{--                            <a href="{{Storage::url($item_find->filename())}}" target="_blank">--}}
                                {{--                                ?????????????? ????????????????--}}
                                {{--                            </a>--}}
                            </b>
                        @else
                            {{--                                            $numcat = true - ?????????? ???????????????? ?????????? ?? ???????????????? ????????????/????????????????/??????????????????--}}
                            {{--                            <span class="text-related">--}}
                            <b>
                                {{--  ???????????????????????? 'is_list_base_calc' ?? ext_show.php ?? ItemController::item_index()  --}}
                                @if($base_link_right['is_list_base_calc'] == true && $base_link_right['is_bsmn_base_enable'] == true)
                                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role,
                                        'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$link->parent_relit_id, 'view_link'=>null])}}"
                                       title="">
                                        {{$item_find->name(false, true, true)}}
                                    </a>
                                @else
                                    {{$item_find->name(false, true, true)}}
                                @endif
                            </b>
                            {{--                            </span>--}}
                        @endif
                    </li>
                    {{--                    <br>--}}
                    @endif
                    @endif
                    @endforeach
                    </p>
                    {{--                    ?????????? ????????????--}}
                    @if($base_right['is_show_base_enable'] == true)
                        {{--        <p>--}}
                        {{--        @foreach (config('app.locales') as $key=>$value)--}}
                        {{--            {{trans('main.name')}} ({{trans('main.' . $value)}}): <span class="text-related">{{$item['name_lang_' . $key]}}</span><br>--}}
                        {{--        @endforeach--}}
                        @if($base->type_is_image)
                            <li>
                                @include('view.img',['item'=>$item, 'size'=>"medium", 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'title'=>""])
                                {{--                <a href="{{Storage::url($item->filename())}}">--}}
                                {{--                    <img src="{{Storage::url($item->filename())}}" height="250"--}}
                                {{--                         alt="" title="{{$item->title_img()}}">--}}
                                {{--                </a>--}}
                            </li>
                        @elseif($base->type_is_document)
                            <li><b>
                                    @include('view.doc',['item'=>$item,'usercode'=>GlobalController::usercode_calc()])
                                    {{--                <a href="{{Storage::url($item->filename())}}" target="_blank">--}}
                                    {{--                    ?????????????? ????????????????--}}
                                    {{--                </a>--}}
                                </b></li>
                        @else
                            {{--                ???????? ??????-?????????????????????? ???????????????????????? ?? ???????????????????? ???????????? ?? ?????????????????????? ??????????????????????????--}}
                            {{--                ?????? ???????? ??????-???? ?????????????????????? ????????????????????????--}}
                            {{--            ?????????????? ???????????????? ?? base_index.blade.php--}}
                            @if(GlobalController::is_base_calcname_check($base, $base_right))
                                {{--                                            $numcat = true - ?????????? ???????????????? ?????????? ?? ???????????????? ????????????/????????????????/??????????????????--}}
                                <li>
                                    <p class="text-label">
                                        <big>{{$base->name()}}:</big>
                                        {{--                            <span class="text-related">--}}<b>
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
                                                            {{$item->name(false, true)}}
                                                        </a>
                                                    </big></big>
                                            @endif
                                            {{--                </span>--}}</b>
                                    </p>
                                </li>
                            @endif
                        @endif
                        {{--            <br>--}}
                        {{--        </p>--}}
                    @endif
    </ul>
    <hr>
    <?php
//    if ($base_right['is_hier_base_enable'] == true) {
////        $result = ItemController::form_parent_coll_hier($item->id, $role, $relit_id);
////        echo $result;
//        $result = ItemController::form_child_deta_hier($item, $project, $role, $relit_id);
//        echo $result;
//    }
//    $relit_par_id = null;
//    $parent_ret_par_id = null;
//    if (($heading == 1) || ($base_index_page > 0)) {
//        $relit_par_id = $relit_id;
//        $parent_ret_par_id = $parent_ret_id;
//    } else {
//        $relit_par_id = $parent_ret_id;
//        $parent_ret_par_id = $relit_id;
//    }
    if ($base_right['is_hier_base_enable'] == true) {
//        $result = ItemController::form_parent_coll_hier($item->id, $role, $relit_id);
//        echo $result;
        //$result = ItemController::form_child_deta_hier($item, $project, $role, $relit_par_id, $parent_ret_par_id);
        $result = ItemController::form_child_deta_hier($item, $project, $role, $relit_id, $parent_ret_id);
        echo $result;
    }
    ?>
    <i>
        <?php
        $created_user_date_time = $item->created_user_date_time();
        $updated_user_date_time = $item->updated_user_date_time();
        ?>
        <p class="text-label">{{trans('main.created_user_date_time')}}:
            <span class="text-related">{{$created_user_date_time}}</span><br>
            @if($created_user_date_time != $updated_user_date_time)
                {{trans('main.updated_user_date_time')}}:
                <span class="text-related">{{$updated_user_date_time}}</span></p>
        @endif
    </i>
    @if ($type_form == 'show')
        <p>
            {{--            @if($base_right['is_list_base_create'] == true)--}}
            {{--            <button type="button" class="btn btn-dreamer"--}}
            {{--                    --}}{{--                        ?????????????????? $message_mc--}}
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
            {{--?????????????? ???????????????? ?? ItemController::ext_edit() ?? ext_show.php--}}
            @if($base_right['is_list_base_update'] == true)
                {{-- ???????????????????????? "'relit_id'=>$relit_par_id, 'parent_ret_id' => $parent_ret_par_id"--}}
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
            {{--            ?? ItemController::is_delete() ???????? ?????????????????????? ???????????????? ???? ?????????? ???? ???????????????? ????????????--}}
            @if(ItemController::is_delete($item, $role, $heading, $base_index_page, $relit_id, $parent_ret_id) == true)
                {{-- ???????????????????????? "'relit_id'=>$relit_par_id, 'parent_ret_id' => $parent_ret_par_id"--}}
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
            {{--                            ?? base_index.blade.php--}}
            {{--                                            ???? ??????????????: ?????????? ?????? ?????????????????? ????????????????????????--}}
            {{--                                                                                        ????????????????, ???????? link - ?????????????????????? ????????--}}
            {{--                                                @if ($link->parent_is_parent_related == true || $link->parent_is_numcalc == true)--}}
            {{--                                                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc()])}}">--}}
            {{--                                                        @else--}}
            {{--                                                            <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'par_link'=>$link])}}">--}}
            {{--                                                                @endif--}}
            {{--            ???? ??????????????--}}
            @if(1==2)
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                        onclick='document.location="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
                         'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>GlobalController::set_relit_id($relit_id)])}}"'
                        title="{{trans('main.space')}}">
                    <i class="fas fa-atlas"></i>
                    {{trans('main.space')}}
                </button>
            @endif

            {{--                                            ?? base_index.blade.php--}}
            {{--                                                            ???? ??????????????: ?????????? ?????? ?????????????????? ????????????????????????--}}
            {{--                                                                                                        ????????????????, ???????? link - ?????????????????????? ????????--}}
            {{--                                                                            @if ($link->parent_is_parent_related == true || $link->parent_is_numcalc == true)--}}
            {{--                                                                                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc()])}}">--}}
            {{--                                                                                    @else--}}
            {{--                                                                                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'par_link'=>$link])}}">--}}
            {{--                                                                                            @endif--}}
            {{--                            ???? ??????????????--}}
            {{--                                        <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"--}}
            {{--                                                onclick='document.location="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc()])}}"'--}}
            {{--                                                title="{{trans('main.space')}}">--}}
            {{--                                            <i class="fas fa-atlas"></i>--}}
            {{--                                            {{trans('main.space')}}--}}
            {{--                                        </button>--}}
            <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                    title="{{trans('main.return')}}" @include('layouts.item.base_index.previous_url')>
                <i class="fas fa-arrow-left"></i>
                {{trans('main.return')}}
            </button>
        </p>
    @elseif($type_form == 'delete_question')
        {{-- ???????????????????????? "'relit_id'=>$parent_ret_id, 'parent_ret_id' => $relit_id"--}}
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
                <button type="submit" class="btn btn-danger" title="{{trans('main.delete')}}">
                    <i class="fas fa-trash"></i>
                    {{trans('main.delete')}}
                </button>
                <button type="button" class="btn btn-dreamer"
                        title="{{trans('main.return')}}" @include('layouts.item.base_index.previous_url')>
                    <i class="fas fa-arrow-left"></i>
                    {{trans('main.return')}}
                </button>
            </p>
        </form>
    @endif
@endsection
