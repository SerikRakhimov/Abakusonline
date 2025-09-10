<?php
use \App\Http\Controllers\GlobalController;
?>
<div class="card shadow m-2">
    <div class="card-header text-center text-title">
        <div style="float:left;width:50%;" class="text-left">
            <small>
                <a href="{{route('item.ext_show', ['item'=>$item, 'project'=>$project, 'role'=>$role, 'usercode' =>GlobalController::usercode_calc(), 'relit_id'=>$relit_id,
                                                            'heading' => $heading,'base_index_page'=>$base_index_page, 'body_link_page'=>$body_link_page,'body_all_page'=>$body_all_page,
                                                            'parent_ret_id' => GlobalController::set_relit_id(null),
                                                            'view_link' => $view_link,
                                                            'saveurl_show' =>$saveurl_show,
                                                            'par_link'=>null, 'parent_item'=>null,
                                                            'string_current' => $string_current,
                                                            ])}}"
                   title="{{trans('main.viewing_record')}}">
                                                            <span class="badge-pill badge-related">
                                                                {{$i}}
                                                            </span>
                </a>
            </small>
        </div>
        {{-- Нужно 'class="text-right"'--}}
        <div style="float:right;width:50%;" class="text-right">
            <small class="text-title">
                {{$item->created_at->Format(trans('main.format_date'))}}
            </small>
        </div>
    </div>
    {{--                    https://sky.pro/wiki/html/odnorodnaya-vysota-kartochek-v-bootstrap-reshenie-bez-css/--}}
    <div class="card-body bg-light p-2 d-flex flex-wrap align-items-center">
        <div class="text-center">
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
               class="card-link" title="{{$item->name()}}">
                @if($item_find)
                    @include('view.img',['item'=>$item_find, 'noimg_def'=>false, 'filenametrue'=>false,'filenametrue'=>false, 'link'=>false, 'img_fluid'=>true, 'card_img_top'=>true, 'title'=>$item->name()])
                @endif
            </a>
        </div>
    </div>
    <div class="card-footer">
        <div class="card-text text-center p-2">
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
               class="card-link" title="{{$item->name()}}">
                <?php
                // echo $item->nmbr();
                // Исключить $view_link при расчете вычисляемого наименования
                // echo $item->nmbr(true, false, false, false, false, GlobalController::set_un_all_par_null($view_link), true, true, $relit_id, $role);
                echo $item->nmbr(true, false, false, false, false, GlobalController::set_un_all_par_link_null($view_link), true, true, $relit_id, $role);
                ?>
                @include('layouts.item.name_with_image',['item'=>$item, 'size'=>"avatar", "circle"=>false])                        </big></big>
                <small><i>{{GlobalController::calc_title_name($label_name,true,false)}}---</i></small>
            </a>
        </div>
    </div>
</div>

