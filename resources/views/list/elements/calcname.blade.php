<?php
use \App\Http\Controllers\GlobalController;
?>
<span class="d-flex flex-nowrap">
{{--            <article style="text-indent: 40px; text-align: justify">--}}
{{--<article style="text-align: justify; font-size: large; margin-bottom: 5px">--}}
{{--<article style="text-align: justify; font-size: large; margin-bottom: 5px">--}}
{{--<span style="text-align: justify; font-size: large; margin-bottom: 5px">--}}
<a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,
                                                                        'heading' => $heading,'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                                                                        'parent_ret_id' => GlobalController::set_relit_id(null),
                                                                        'view_link' => GlobalController::set_par_null($view_link),
                                                                        'saveurl_show' =>$saveurl_show,
                                                                        'par_link'=>null, 'parent_item'=>null,
                                                                        'string_current' => $string_current,
                                                                        ])}}"
   title="{{trans('main.viewing_record')}}{{GlobalController::calc_title_name($label_name,true,false)}}">
        <span style="font-size:small" class="badge badge-related">
            {{$i}}
        </span>
</a>&nbsp;&nbsp;
{{--    Условие '@if($label_name!="")' и тег '<br>' нужны. Не удалять!--}}
@if($label_name!="")
    <small>
        <i>
            <span class="text-muted"
                  title="{{GlobalController::calc_title_name($label_name,true,false)}}">{{GlobalController::calc_title_name($label_name,true,false)}}
            </span>
        </i>
    </small>
    <br>
@endif
<a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(),
        'relit_id'=>$relit_id,
        'called_from_button'=>0,
        'view_link'=>GlobalController::set_par_null($view_link),
        'view_ret_id'=>$view_ret_id,
        'string_current'=>$string_next,
        'prev_base_index_page'=>$base_index_page,
        'prev_body_link_page'=>$body_link_page,
        'prev_body_all_page'=>$body_all_page,
        ])}}"
   class="card-link"
   title="{{$item->name()}}">
{{--        <span style="font-size:medium">--}}
<!--        --><?php
            //            // echo $item->nmbr(true, true, false);
            //            // Исключить $view_link при расчете вычисляемого наименования
            //            // echo $item->nmbr(true, true, false, false, false, GlobalController::set_un_all_par_null($view_link), false, true, $relit_id, $role);
            //            echo $item->nmbr(true, true, false, false, false, GlobalController::set_un_all_par_link_null($view_link), false, true, $relit_id, $role);
            //            ?>
            @include('layouts.item.name_with_image',['item'=>$item, 'size'=>"avatar", "circle"=>true])
{{--            </span>--}}
    {{-- 'Показывать признак "В истории" при просмотре списков'--}}
    @if($base_right['is_list_hist_attr_enable'] == true)
        @include('layouts.item.show_history',['item'=>$item])
    @endif
    {{-- 'Показывать дату создания'--}}
    @if($base_right['is_list_base_sort_creation_date_desc'] == true)
        <small><span class="text-label">({{$item->created_date()}})</span></small>
    @endif
</a>
</span>
{{--    </article>--}}
{{--    </span>--}}
{{--<br>--}}
{{--<hr class="hr_ext_show">--}}
<hr>
