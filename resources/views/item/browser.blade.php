<!DOCTYPE html>
<html lang="en">
<?php
use \App\Http\Controllers\GlobalController;
use \App\Http\Controllers\ItemController;
$item_id = 0;
if ($item) {
    $item_id = $item->id;
}
$num_cols = GlobalController::get_number_of_columns_brow();
?>
<head>
    <meta charset="UTF-8">
    @include('layouts.style_header')
    <title>{{$base->names($base_right)}}</title>
</head>
<body>
<p>
<h3 class="display-5 text-center">{{$base->names($base_right)}}</h3>
<p>
<form class="navbar-form navbar-right" role="search">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <form class="">
                <div class="row  align-items-center">
                    <div class="col-auto">
                        <i class="fas fa-search h4 text-body"></i>
                    </div>
                    <div class="col">
                        <input class="form-control form-control form-control-borderless" name="search" id="search"
                               type="search"
                               placeholder="{{$order_by == 'code'? trans('main.search_by_code'):trans('main.search_by_name')}} @if($search !="")({{mb_strtolower(trans('main.empty_to_cancel'))}})@endif
                                   ">
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-dreamer" type="button" onclick="search_click()">
                            {{trans('main.search')}}</button>
                    </div>
                    @if ($base_right['is_list_base_create'] == true)
                        <div class="col-auto">
                            <?php
                            $message_bs_calc = ItemController::message_bs_calc($project, $base);
                            $message_bs_info = $message_bs_calc['message_bs_info'];
                            $message_bs_validate = $message_bs_calc['message_bs_validate'];
                            ?>
                            <button type="button" class="btn btn-dreamer btn-sm"
                                    title="{{trans('main.add') . " '". $base->name() . "' " . $message_bs_info}}"
                                    onclick="document.location='{{route('item.ext_create', ['base'=>$base,
                                             'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(),
                                             'relit_id' => $relit_id
                                             ])}}'">
                                {{--                                             'string_link_ids_current'=>$string_link_ids_current,--}}
                                {{--                                             'string_item_ids_current'=>$string_item_ids_current,--}}
                                {{--                                             'string_all_codes_current'=>$string_all_codes_current,--}}
                                <i class="fas fa-plus d-inline"></i>&nbsp;{{trans('main.add')}}
                            </button>
                        </div>
                @endif
            </form>
        </div>
    </div>
</form>
<br>
<div class="row justify-content-center">
    @if($search !="")
        {{$filter_by == 'code'? trans('main.filter_by_code'):trans('main.filter_by_name')}} "
        <mark>*{{$search}}*</mark>":
        @if(count($items) == 0)
            {{mb_strtolower(trans('main.no_data'))}}!
        @endif
    @endif
</div>

@if(count($items) !=0)
    <?php
    $tile_view = $base->tile_view($role, $relit_id, $base_right);
    // $link_image вычисляется из tile_view() с учетом правил:
    // if (!($role->is_author() & $relit_id == 0)) {
    // if ($base_right['is_list_base_read'] == true) {
    $link_image = $tile_view['link'];
    $i = 0;
    ?>
    @if($tile_view['result'] == true & $base_right['is_view_cards'] == true)
        <div class="row">
            <div class="col text-center text-label">
                {{trans('main.sort_by')}}:
            </div>
            <div class="col text-center {{$order_by == 'code'?'font-italic' : ''}}">
                <a href="{{route('item.browser',['link_id'=>$link->id, 'base_id'=>$base->id, 'project_id'=>$project->id, 'role_id'=>$role->id, 'relit_id'=>$relit_id, 'item_id'=>$item_id, 'order_by'=>'code', 'filter_by'=>$filter_by, 'search'=>$search])}}"
                   title="{{trans('main.sort_by_code')}}">{{trans('main.code')}}
                </a>
            </div>
            <div class="col text-center {{$order_by != 'code'?'font-italic' : ''}}">
                <a href="{{route('item.browser',['link_id'=>$link->id, 'base_id'=>$base->id, 'project_id'=>$project->id, 'role_id'=>$role->id, 'relit_id'=>$relit_id, 'item_id'=>$item_id, 'order_by'=>'name', 'filter_by'=>$filter_by, 'search'=>$search])}}"
                   title="{{trans('main.sort_by_name')}}">{{trans('main.name')}}</a>
            </div>
        </div>
        <br>
        {{-- Таблица из $num_cols колонок--}}
        {{-- "m-2" нужно--}}
        <div class="card-deck m-2">
            @foreach($items as $it)
                <?php
                $it_name = $it->name();
                //$i = $i + 1;
                $item_find = GlobalController::view_info($it->id, $link_image->id);
                ?>
                {{--            @if(($i-1) % $num_cols == 0)--}}
                {{--                --}}{{--                Открывает /row--}}
                {{--                <div class="row">--}}
                {{--                    @endif--}}
                {{--                    <div class="col-4">--}}
                <div class="card shadow m-2">
                    <a href="#"
                       onclick="javascript:SelectFile('{{$it->id}}', '{{$it->code}}', '{{$it_name}}')"
                       title="{{$it_name}}">
{{--                        <p class="elements-header text-center text-label">{{trans('main.code')}}: {{$it->code}}</p>--}}
                        <p class="card-header text-center text-title">{{trans('main.code')}}: {{$it->code}}</p>
                    </a>
                    {{--                    <div class="elements-body p-0">--}}
                    <div class="card-body bg-light p-2 d-flex flex-wrap align-items-center">
                        @if($item_find)
                            {{--                        <div class="elements-block text-center">--}}
                            <div class="text-center">
                                {{--                                https://askdev.ru/q/kak-vyzvat-funkciyu-javascript-iz-tega-href-v-html-276225/--}}
                                <a href="#"
                                   onclick="SelectFile('{{$it->id}}', '{{$it->code}}', '{{$it_name}}')"
                                   title="{{$it_name}}">
                                    @include('view.img',['item'=>$item_find, 'size'=>"medium", 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>true, 'card_img_top'=>true, 'title'=>$it_name])
                                    {{--                            @else--}}
                                    {{--                                <div class="text-danger">--}}
                                    {{--                                    {{GlobalController::empty_html()}}</div>--}}
                                </a>
                            </div>
                        @endif
                        {{--                        <h5 class="elements-title text-center">--}}
                        <div class="card-footer">
                            <div class="card-text text-center p-2">
                                <a href="#"
                                   onclick="SelectFile('{{$it->id}}', '{{$it->code}}', '{{$it_name}}')"
                                   title="{{$it_name}}">
                                    {{--                            Где $item->name() выходит в cards выводить "<?php echo GlobalController::to_html();?>"--}}
                                    <?php echo $it->nmbr();?>
                                </a>
                                {{--                        </h5>--}}
                            </div>
                        </div>
                    </div>
                    {{--                    <div class="elements-footer">--}}
                    {{--                        <small class="text-muted">--}}
                    {{--                            {{$item->created_at->Format(trans('main.format_date'))}}--}}
                    {{--                        </small>--}}
                    {{--                    </div>--}}
                </div>
                {{--                    </div>--}}

                {{--                    --}}{{--                $i делится без остатка на 3--}}
                {{--                    @if($i % $num_cols == 0)--}}
                {{--                        --}}{{--                Закрывает /row--}}
                {{--                </div><br>--}}
                {{--            @endif--}}
                <?php
                $i++;
                ?>
                @if($i % $num_cols == 0)
        </div>
        {{-- "m-2" нужно--}}
        <div class="card-deck m-2">
            @endif
            @endforeach
            {{-- Если строка из $num_cols элементов не завершилась до $num_cols столбцов--}}
            {{-- (т.е. $i не делится без остатка на $num_cols)--}}
            @if($i % $num_cols != 0)
                <?php
                // Подсчитываем количество оставшихся колонок
                $n = $num_cols - ($i % $num_cols);
                ?>
                {{-- В цикле $n раз вставляем вставляем пустые колонки--}}
                @for($k = 0; $k < $n; $k++)
                    {{-- Вставляем пустую карточку--}}
                    <div class="card m-2 bg-transparent">
                    </div>
                @endfor
            @endif
        </div>
        <br>
        <div class="row">
            <div class="col text-center text-label">
                {{trans('main.select_record_for_work')}}
            </div>
        </div>
    @else
        <?php
        $link_image = $base->get_link_primary_image();
        ?>
        <table class="table table-sm table-hover">
            <caption>{{trans('main.select_record_for_work')}}</caption>
            <thead>
            {{--        'Показывать признак "В истории" при просмотре списков выбора'--}}
            @if($base_right['is_brow_hist_attr_enable'] == true)
                <th style="width: 5%" class="text-center"
                    title="{{trans('main.history')}}">{{trans('main.small_history')}}</th>
            @endif
            <th class="text-center {{$order_by == 'code'?'font-italic' : ''}}">
                {{--                <a href="{{route('item.browser',['link_id'=>$link->id, 'project_id'=>$project->id, 'role_id'=>$role->id, 'item_id'=>$item->id, 'sort_by_code'=>1, 'save_by_code'=>$save_by_code==true?"1":"0", 'search'=>$search])}}"--}}
                {{--                   title="{{trans('main.sort_by_code')}}">--}}
                <a href="{{route('item.browser',['link_id'=>$link->id, 'project_id'=>$project->id, 'role_id'=>$role->id, 'relit_id'=>$relit_id, 'item_id'=>$item_id, 'order_by'=>'code', 'filter_by'=>$filter_by, 'search'=>$search])}}"
                   title="{{trans('main.sort_by_code')}}">
                    {{trans('main.code')}}
                </a></th>
            <th class="text-center {{$order_by != 'code'?'font-italic' : ''}}">
                <a href="{{route('item.browser',['link_id'=>$link->id, 'project_id'=>$project->id, 'role_id'=>$role->id, 'relit_id'=>$relit_id, 'item_id'=>$item_id, 'order_by'=>'name', 'filter_by'=>$filter_by, 'search'=>$search])}}"
                   title="{{trans('main.sort_by_name')}}">{{trans('main.name')}}
                </a></th>
            @if($link_image)
                <th class="text-center" title="{{trans('main.image')}}">
                    {{--                    📷--}}
                    {{trans('main.image')}}
                </th>
                @endif
                </tr>
            </thead>
            <tbody>
            @foreach($items as $it)
                <?php
                $it_name = $it->name();
                ?>
                @if($link_image)
                    <?php
                    $item_find = GlobalController::view_info($it->id, $link_image->id);
                    ?>
                @endif
                <tr>
                    {{--        'Показывать признак "В истории" при просмотре списков выбора'--}}
                    @if($base_right['is_brow_hist_attr_enable'] == true)
                        <td class="text-center">
                            @include('layouts.item.show_history',['item'=>$it])
                        </td>
                    @endif
                    <td class="text-center" style="cursor:pointer"
                        onclick="SelectFile('{{$it->id}}', '{{$it->code}}', '{{$it_name}}')">{{$it->code}}</td>
                    <td class="text-left" style="cursor:pointer"
                        onclick="SelectFile('{{$it->id}}', '{{$it->code}}', '{{$it_name}}')">{{$it_name}}
                    </td>
                    @if($link_image)
                        <td class="text-center" style="cursor:pointer"
                            onclick="SelectFile('{{$it->id}}', '{{$it->code}}', '{{$it_name}}')">
                            @if($item_find)
                                @include('view.img',['item'=>$item_find, 'size'=>"small", 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>$it_name])
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
    {{$items->links()}}
@endif

<script>
    function search_click() {
        {{--param = '/{{$sort_by_code == true?"1":"0"}}';--}}
        {{-- " + param + param" правильно
        {{--open('{{route('item.browser', '')}}' + '/' + {{$link->id}}+'/' + {{$project->id}} +'/' + {{$role->id}}--}}
        {{--        +'/' + {{$item->id}} +param + param--}}
        {{--    + '/' + document.getElementById('search').value, '_self', 'width=800, height=800');--}}
        {{--open('{{route('item.browser', '')}}' + '/' + {{$link->id}}+'/' + {{$project->id}} +'/' + {{$role->id}}--}}
        {{--        +'/' + {{$item->id}} +'/' + '{{$order_by}}' +'/' + '{{$filter_by}}'--}}
        {{--    + '/' + document.getElementById('search').value, '_self', 'width=800, height=800');--}}
        {{--open('{{route('item.browser', '')}}' + '/' + '{{$link->id}}'+'/' + '{{$project->id}}' +'/' + '{{$role->id}}'--}}
        {{--        +'/' + '{{$item->id}}'--}}
        {{--    , '_self', 'width=800, height=800');--}}
        {{--var path = '{{route('item.browser', '')}}' + '/' + {{$link->id}}+'/' + {{$project->id}} +'/' + {{$role->id}} +'/' + {{$item->id}} +'/'--}}
        {{--    + '{{$order_by}}' +'/' + '{{$filter_by}}' + '/' + document.getElementById('search').value;--}}
        var path = '{{route('item.browser', '')}}' + '/' + {{$link->id}} + '/' + {{$project->id}} + '/' + {{$role->id}} + '/' + {{$relit_id}} + '/' + {{$item_id}}
            + '/' + '{{$order_by}}' + '/' + '{{$order_by}}' + '/' + document.getElementById('search').value;
        open(path, '_self', 'width=800, height=800');

    };

    function SelectFile(id, code, name) {
        opener.item_id.value = id;
        opener.item_code.value = code;
        opener.item_name.innerHTML = name;
        //opener.on_parent_refer();

        opener.item_code.dispatchEvent(new Event('change'));

        close();
    }
</script>

</body>
</html>
