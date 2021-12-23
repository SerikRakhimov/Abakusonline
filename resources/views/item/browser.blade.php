<!DOCTYPE html>
<html lang="en">
<?php
use \App\Http\Controllers\GlobalController;
use \App\Http\Controllers\MainController;
$item_id = 0;
if ($item){
    $item_id = $item->id;
}
?>
<head>
    <meta charset="UTF-8">
    @include('layouts.style_header')
    <title>{{$base->names()}}</title>
</head>
<body>
<p>
<h3 class="display-5 text-center">{{$base->names()}}</h3>
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
                </div>
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
    $tile_view = $base->tile_view($base_right);
    $link_image = $tile_view['link'];
    $i = 0;
    ?>
    @if($tile_view['result'] == true)
        <h1>true</h1>
        @else
        <h1>false</h1>
        @endif
    @if($tile_view['result'] == true)
        <div class="row">
            <div class="col text-center text-label">
                {{trans('main.sort_by')}}:
            </div>
            <div class="col text-center {{$order_by == 'code'?'font-italic' : ''}}">
                <a href="{{route('item.browser',['link_id'=>$link->id, 'base_id'=>$base->id, 'project_id'=>$project->id, 'role_id'=>$role->id, 'item_id'=>$item_id, 'order_by'=>'code', 'filter_by'=>$filter_by, 'search'=>$search])}}"
                   title="{{trans('main.sort_by_code')}}">{{trans('main.code')}}
                </a>
            </div>
            <div class="col text-center {{$order_by != 'code'?'font-italic' : ''}}">
                <a href="{{route('item.browser',['link_id'=>$link->id, 'base_id'=>$base->id, 'project_id'=>$project->id, 'role_id'=>$role->id, 'item_id'=>$item_id, 'order_by'=>'name', 'filter_by'=>$filter_by, 'search'=>$search])}}"
                   title="{{trans('main.sort_by_name')}}">{{trans('main.name')}}</a>
            </div>
        </div>
        <br>
        {{--            Таблица из 3-х колонок--}}
        <div class="card-columns">
            @foreach($items as $it)
                <?php
                $i = $i + 1;
                $item_find = MainController::view_info($it->id, $link_image->id);
                ?>
                {{--            @if(($i-1) % 3 == 0)--}}
                {{--                --}}{{--                Открывает /row--}}
                {{--                <div class="row">--}}
                {{--                    @endif--}}
                {{--                    <div class="col-4">--}}
                <div class="card shadow">
                    <a href="#"
                       onclick="javascript:SelectFile('{{$it->id}}', '{{$it->code}}', '{{$it->name()}}')"
                       title="{{$it->name()}}">
                        <p class="card-header text-center text-label">{{trans('main.code')}}: {{$it->code}}</p>
                    </a>
                    <div class="card-body">
                        @if($item_find)
                            {{--                        <div class="card-block text-center">--}}
                            <div class="text-center">
                                {{--                                https://askdev.ru/q/kak-vyzvat-funkciyu-javascript-iz-tega-href-v-html-276225/--}}
                                <a href="#"
                                   onclick="SelectFile('{{$it->id}}', '{{$it->code}}', '{{$it->name()}}')"
                                   title="{{$it->name()}}">
                                    @include('view.img',['item'=>$item_find, 'size'=>"medium", 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>true, 'title'=>$it->name()])
                                    {{--                            @else--}}
                                    {{--                                <div class="text-danger">--}}
                                    {{--                                    {{GlobalController::empty_html()}}</div>--}}
                                </a>
                            </div>
                        @endif
                        <h5 class="card-title text-center"><a href="#"
                                                              onclick="SelectFile('{{$it->id}}', '{{$it->code}}', '{{$it->name()}}')"
                                                              title="{{$it->name()}}">
                                {{--                            Где $item->name() выходит в cards выводить "<?php echo GlobalController::to_html();?>"--}}
                                <?php echo $it->nmbr();?>
                            </a></h5>
                    </div>
                    {{--                    <div class="card-footer">--}}
                    {{--                        <small class="text-muted">--}}
                    {{--                            {{$item->created_at->Format(trans('main.format_date'))}}--}}
                    {{--                        </small>--}}
                    {{--                    </div>--}}
                </div>
                {{--                    </div>--}}

                {{--                    --}}{{--                $i делится без остатка на 3--}}
                {{--                    @if($i % 3 == 0)--}}
                {{--                        --}}{{--                Закрывает /row--}}
                {{--                </div><br>--}}
                {{--            @endif--}}

            @endforeach
        </div>
        {{--        --}}{{--                Если строка из 3-х элементов не завершилась до 3-х столбцов--}}
        {{--        --}}{{--                $i не делится без остатка на 3--}}
        {{--        @if($i % 3 != 0)--}}
        {{--            <?php--}}
        {{--            //                Подсчитываем количество оставшихся колонок--}}
        {{--            $n = 3 - ($i % 3);--}}
        {{--            ?>--}}
        {{--            --}}{{--            В цикле $n раз вставляем вставляем пустые колонки--}}
        {{--            @for($k = 0; $k < $n; $k++)--}}
        {{--                <div class="col-4">--}}
        {{--                </div>--}}
        {{--                @endfor--}}
        {{--                --}}{{--                Закрывает /row--}}
        {{--                </div><br>--}}
        {{--                @endif--}}
        <div class="row">
            <div class="col text-center text-label">
                {{trans('main.select_record_for_work')}}
            </div>
        </div>
    @else
        <table class="table table-sm table-bordered table-hover">
            <caption>{{trans('main.select_record_for_work')}}</caption>
            <thead>
            <th class="text-center {{$order_by == 'code'?'font-italic' : ''}}">
                {{--                <a href="{{route('item.browser',['link_id'=>$link->id, 'project_id'=>$project->id, 'role_id'=>$role->id, 'item_id'=>$item->id, 'sort_by_code'=>1, 'save_by_code'=>$save_by_code==true?"1":"0", 'search'=>$search])}}"--}}
                {{--                   title="{{trans('main.sort_by_code')}}">--}}
                <a href="{{route('item.browser',['link_id'=>$link->id, 'project_id'=>$project->id, 'role_id'=>$role->id, 'item_id'=>$item_id, 'order_by'=>'code', 'filter_by'=>$filter_by, 'search'=>$search])}}"
                   title="{{trans('main.sort_by_code')}}">
                    {{trans('main.code')}}
                </a></th>
            <th class="text-center {{$order_by != 'code'?'font-italic' : ''}}">
                <a href="{{route('item.browser',['link_id'=>$link->id, 'project_id'=>$project->id, 'role_id'=>$role->id,'item_id'=>$item_id, 'order_by'=>'name', 'filter_by'=>$filter_by, 'search'=>$search])}}"
                   title="{{trans('main.sort_by_name')}}">{{trans('main.name')}}</a></th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $it)
                <tr>
                    <td class="text-center" style="cursor:pointer"
                        onclick="SelectFile('{{$it->id}}', '{{$it->code}}', '{{$it->name()}}')">{{$it->code}}</td>
                    <td class="text-left" style="cursor:pointer"
                        onclick="SelectFile('{{$it->id}}', '{{$it->code}}', '{{$it->name()}}')">{{$it->name()}}
                    </td>
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
        var path = '{{route('item.browser', '')}}' + '/' + {{$link->id}}+'/' + {{$project->id}} +'/' + {{$role->id}} +'/' + {{$item_id}}
                +'/' + '{{$order_by}}' + '/' + '{{$order_by}}' + '/' + document.getElementById('search').value;
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
