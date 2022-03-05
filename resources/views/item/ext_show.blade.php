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
    $base_right = GlobalController::base_right($base, $role);
    ?>
    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role])
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
        $result = ItemController::form_parent_deta_hier($item->id, $project, $role, false);
        echo $result;
    }
    ?>
    <ul>
        <p class="text-label">Id: <span class="text-related">
                <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
                        'usercode' =>GlobalController::usercode_calc()])}}"
                   title="">
                {{$item->id}}
                </a>
            </span></p>
        @if($base_right['is_show_base_enable'] == true)
            {{--        <p>--}}
            @if($base->is_code_needed == true)
                <p class="text-label">{{trans('main.code')}}: <span class="text-related">
                        <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
                                'usercode' =>GlobalController::usercode_calc()])}}"
                           title="">
                        {{$item->code}}
                                                            </a>
                    </span></p>
            @endif
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
                        {{--                    Открыть документ--}}
                        {{--                </a>--}}
                    </b></li>
            @else
                {{--                Если тип-вычисляемое поле и Показывать Основу с вычисляемым наименованием--}}
                {{--                или если тип-не вычисляемое наименование--}}
                {{--            похожая проверка в base_index.blade.php--}}
                @if(GlobalController::is_base_calcname_check($base, $base_right))
                    {{--                                            $numcat = true - вывод числовых полей с разрядом тысячи/миллионы/миллиарды--}}
                    <li>
                        <p class="text-label">{{trans('main.name')}}:
                            {{--                            <span class="text-related">--}}<b>
                                @if($base->type_is_text())
                                    <?php
                                    echo GlobalController::it_txnm_n2b($item);
                                    ?>
                                @else
                                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
                                       'usercode' =>GlobalController::usercode_calc()])}}"
                                       title="">
                                        {{$item->name(false, true)}}
                                    </a>
                                @endif
                                {{--                </span>--}}</b>
                        </p>
                    </li>
                @endif
            @endif
            {{--            <br>--}}
            {{--        </p>--}}
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

        <p class="text-label">
        @foreach($array_calc as $key=>$value)
            <?php
            $link = Link::find($key);
            $item_find = GlobalController::view_info($item->id, $key);
            ?>
            @if($link && $item_find)
                <?php
                $base_link_right = GlobalController::base_link_right($link, $role);
                ?>
                @if($base_link_right['is_show_link_enable'] == true)
                    <li>
                        @if($base_link_right['is_list_base_calc'] == true)
                            <a href="{{route('item.base_index',['base'=>$link->parent_base_id, 'project'=>$project, 'role'=>$role])}}"
                               title="{{$link->parent_base->names()}}">
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
                                {{--                                Открыть документ--}}
                                {{--                            </a>--}}
                            </b>
                        @else
                            {{--                                            $numcat = true - вывод числовых полей с разрядом тысячи/миллионы/миллиарды--}}
                            {{--                            <span class="text-related">--}}
                            <b>
                                {{--  Используется 'is_list_base_calc' в ext_show.php и ItemController::item_index()  --}}
                                @if($base_link_right['is_list_base_calc'] == true)
                                    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_find, 'role'=>$role,
                                        'usercode' =>GlobalController::usercode_calc(),'par_link'=>$link])}}"
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
    </ul>
    <hr>
    <?php
    if ($base_right['is_hier_base_enable'] == true) {
//        $result = ItemController::form_parent_coll_hier($item->id, $role);
//        echo $result;
        $result = ItemController::form_child_deta_hier($item, $project, $role);
        echo $result;
    }
    ?>
    <i>
        <p class="text-label">{{trans('main.created_user_date_time')}}:
            <span class="text-related">{{$item->created_user_date_time()}}</span><br>
            {{trans('main.updated_user_date_time')}}:
            <span class="text-related">{{$item->updated_user_date_time()}}</span></p>
    </i>
    @if ($type_form == 'show')
        <p>
            {{--Похожая проверка в ItemController::ext_edit() и ext_show.php--}}
            @if($base_right['is_list_base_update'] == true)
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                        onclick='document.location="{{route('item.ext_edit',
            ['item'=>$item,'project'=>$project, 'role'=>$role,
            'usercode' =>GlobalController::usercode_calc(),
            'heading' => $heading,
            'body_page' => $body_page, 'body_count' => $body_count, 'body_perpage' => $body_perpage,
            'par_link' => $par_link, 'parent_item' => $parent_item])}}"'
                        title="{{trans('main.edit')}}">
                    <i class="fas fa-edit"></i>
                    {{trans('main.edit')}}
                </button>
            @endif
            {{--            В ItemController::is_delete() есть необходимые проверки на права по удалению записи--}}
            @if(ItemController::is_delete($item, $role) == true)
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                        onclick='document.location="{{route('item.ext_delete_question',
            ['item'=>$item,'project'=>$project, 'role'=>$role,
            'usercode' =>GlobalController::usercode_calc(),
            'heading' => $heading,
            'body_page' => $body_page, 'body_count' => $body_count, 'body_perpage' => $body_perpage,
            'par_link' => $par_link, 'parent_item' => $parent_item])}}"'
                        title="{{trans('main.delete')}}">
                    <i class="fas fa-trash"></i>
                    {{trans('main.delete')}}
                </button>
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
            @if(1==1)
                <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                        onclick='document.location="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
                         'usercode' =>GlobalController::usercode_calc(),
                         'par_link' => null])}}"'
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
            <button type="button" class="btn btn-dreamer mb-1 mb-sm-0"
                    title="{{trans('main.return')}}" @include('layouts.item.base_index.previous_url')>
                <i class="fas fa-arrow-left"></i>
                {{trans('main.return')}}
            </button>
        </p>
    @elseif($type_form == 'delete_question')
        <form action="{{route('item.ext_delete',['item'=>$item,'project'=>$project, 'role'=>$role,
            'usercode' =>GlobalController::usercode_calc(),
            'heading' => $heading,
            'body_page' => $body_page, 'body_count' => $body_count, 'body_perpage' => $body_perpage,
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
