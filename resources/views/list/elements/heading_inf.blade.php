<?php
use \App\Http\Controllers\GlobalController;
// Показывать emoji - да/нет
$emoji_enable = true;
?>
{{--            <article style="text-indent: 40px; text-align: justify">--}}
<span style="text-align: justify; font-size: large; margin-bottom: 5px">
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
    @if($item_find->base->type_is_text())
        {{--                            <span class="text-related">--}}
        {{--                                <b>--}}
        <?php
        echo GlobalController::it_txnm_n2b($item_find, $emoji_enable);
        ?>
        {{--                        </span>--}}
        {{--                                </b>--}}
    @elseif($item_find->base->type_is_image())
        @include('view.img',['item'=>$item_find, 'size'=>"small", 'border'=>false, 'filenametrue'=>false, 'link'=>true, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>""])
    @elseif($link->parent_base->type_is_document())
        @include('view.doc',['item'=>$item_find, 'usercode'=>GlobalController::usercode_calc()])
    @else
    <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(),
        'relit_id'=>$relit_id,
        'called_from_button'=>0,
        'view_link'=>$view_link,
        'view_ret_id'=>$view_ret_id,
        'string_current'=>$string_next,
        'prev_base_index_page'=>$base_index_page,
        'prev_body_link_page'=>$body_link_page,
        'prev_body_all_page'=>$body_all_page,
        ])}}"
       class="card-link"
       title="{{$item_find->name()}}">
        <?php
        // echo $item_find->nmbr(true, true, false);
        // Исключить $view_link при расчете вычисляемого наименования
        // echo $item_find->nmbr(true, true, false, false, true, GlobalController::set_un_all_par_null($view_link), false, true, $relit_id, $role);
        echo $item_find->nmbr(true, true, false, false, true, GlobalController::set_un_all_par_link_null($view_link), false, true, $relit_id, $role);
        ?>
    </a>
    @endif
{{--    </article>--}}
    </span>
<br>
<hr class="hr_ext_show">
