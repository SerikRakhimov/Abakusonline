@extends('layouts.app')
@section('content')
    <?php
    use App\Models\Item;
    use App\Models\Main;
    use App\Http\Controllers\GlobalController;
    $i = 0;
    $s_get = $mzv->get();
    $arr_sv_title = array();
    $arr_sv_count = count($arr_sv_title);
    $arr_sv_work = array();
    $arr_in = array();
    ?>
    @include('layouts.project.show_project_role',['project'=>$project, 'role'=>$role, 'relit_id'=>$relit_id])
    <h3>{{$item->name()}}</h3>
    <details open>
        <?php
        $k = 0;
        $mks_get = $mks->get();
        $cn_ks = $mks->count();
        ?>
        <summary>Свойства ({{$cn_ks}})</summary>
        @foreach($mks_get as $value)
            <?php
            $item = GlobalController::get_parent_item_from_main($value->ks_id, $link_title_id);
            ?>
            @if($item)
                <?php
                $arr_sv_title[$k] = $item->id;
                $k++;
                ?>
                {{$k}}. <span class="badge-pill badge-related">{{$item->name()}}</span>
            @endif
            <br>
        @endforeach
        <?php
        $arr_sv_count = count($arr_sv_title);
        ?>
    </details>
    <br>
    <table class="table table-sm table-hover w-auto">
        <caption>{{trans('main.select_record_for_work')}}</caption>
        <thead class="bg-transparent">
        <tr>
            {{--        Похожие проверки ниже по тексту--}}
            <th style="width: 5%" class="text-center align-top">#</th>
            <th class="text-left align-top" title="">{{$base_zv->names()}}
            </th>
            <th class="text-center align-top" title="">Совпадений
            </th>
            <th class="text-center align-top" title="">
            </th>
            <th class="text-center align-top" title="">Совпали
            </th>
            <th class="text-center align-top" title="">Не найдены
            </th>
            <th class="text-center align-top" title="">Другие свойства ({{mb_strtolower($base_zv->name())}})
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($s_get as $s_value)
            <?php
            $i++;
            $item_zv = Item::find($s_value->zv_id);
            for ($j = 0; $j < $arr_sv_count; $j++) {
                $arr_sv_work[$j] = $arr_sv_title[$j];
            }
            ?>
            @if($item_zv)
                <tr>
                    <td class="text-center">
                        <small class="badge-pill badge-related">{{$i}}</small>
                    </td>
                    <td class="text-left">
                        <b>
                            <a href="{{route('item.item_index', ['project'=>$project, 'item'=>$item_zv, 'role'=>$role,
        'usercode' =>GlobalController::usercode_calc(),
        'relit_id'=>$relit_id
        ])}}"
                               title="{{$item_zv->name()}}">
                                {{$item_zv->name()}}
                            </a>
                        </b>
                    </td>
                    <td class="text-center">
                        {{$s_value->count}}/{{$cn_ks}}<br>
                        <progress max="{{$cn_ks}}" value="{{$s_value->count}}">
                        </progress>
                    </td>
                    <td class="text-center">
                        @if($s_value->count == $cn_ks)
                            🏆
                        @endif
                    </td>
                    <td class="text-left">
                        <?php
                        // Фильтр на заявку, получаем список совпавших свойств
                        $zs_in_get = $mzs_in->get()->where('zv_id', $item_zv->id);
                        $j = 0;
                        // Очистить массив $arr_in
                        array_splice($arr_in, 0, count($arr_in));
                        ?>
                        @foreach($zs_in_get as $d_value)
                            <?php
                            $item_sv = GlobalController::get_parent_item_from_main($d_value->zs_id, $link_body_id);
                            ?>
                            @if($item_sv)
                                <?php
                                $key = array_search($item_sv->id, $arr_in);
                                //                                    // 'if ($key !== false)' так правильно
                                //                                    // 'if ($key != false)' - не использовать, $key может быть равным 0
                                //                                    //                                    $is_zero = false;
                                if ($key === false) {
                                    $arr_in[$j] = $item_sv->id;
                                    $j++;
                                }
                                ?>
                            @endif
                        @endforeach
{{--                        <details open--}}
{{--                        >--}}
{{--                            <summary></summary>--}}
{{--                            <?php--}}
{{--                            $j = 0;--}}
{{--                            ?>--}}
{{--                            @foreach($zs_in_get as $d_value)--}}
{{--                                <?php--}}
{{--                                $item_sv = GlobalController::get_parent_item_from_main($d_value->zs_id, $link_body_id);--}}
{{--                                ?>--}}
{{--                                @if($item_sv)--}}
{{--                                    <?php--}}
{{--                                    $j++;--}}
{{--                                    $key = array_search($item_sv->id, $arr_sv_work);--}}
{{--                                    // 'if ($key !== false)' так правильно--}}
{{--                                    // 'if ($key != false)' - не использовать, $key может быть равным 0--}}
{{--                                    //                                    $is_zero = false;--}}
{{--                                    if ($key !== false) {--}}
{{--                                        //unset($arr_sv_work[$key]);--}}
{{--                                        $arr_sv_work[$key] = 0;--}}
{{--                                    }--}}
{{--                                    ?>--}}
{{--                                    {{$j}}. <span class="badge-pill badge-related">{{$item_sv->name()}}</span>--}}
{{--                                    <br>--}}
{{--                                @endif--}}
{{--                            @endforeach--}}
{{--                        </details>--}}
                        <details open>
                            <summary></summary>
                            <?php
                            $j = 0;
                            ?>
                            @for ($t = 0; $t < count($arr_in); $t++)
                                @if($arr_in[$t] != 0)
                                    <?php
                                    $item_sv = Item::find($arr_in[$t]);
                                    ?>
                                    @if($item_sv)
                                        <?php
                                        $j++;
                                        $key = array_search($item_sv->id, $arr_sv_work);
                                        // 'if ($key !== false)' так правильно
                                        // 'if ($key != false)' - не использовать, $key может быть равным 0
                                        //                                    $is_zero = false;
                                        if ($key !== false) {
                                            //unset($arr_sv_work[$key]);
                                            $arr_sv_work[$key] = 0;
                                        }
                                        ?>
                                        {{$j}}. <span class="badge-pill badge-related">{{$item_sv->name()}}</span>
                                        <br>
                                    @endif
                                @endif
                            @endfor
                        </details>
                    </td>
                    <td class="text-left">
                        <?php
                        $j = 0;
                        $arr_cn_work = 0;
                        for ($t = 0; $t < count($arr_sv_work); $t++) {
                            if ($arr_sv_work[$t] != 0) {
                                $arr_cn_work++;
                            }
                        }
                        ?>
                        @if($arr_cn_work>0)
                            <details>
                                <summary>{{$arr_cn_work}} записей</summary>
                                @for ($t = 0; $t < count($arr_sv_work); $t++)
                                    @if($arr_sv_work[$t] != 0)
                                        <?php
                                        $item_sv = Item::find($arr_sv_work[$t]);
                                        ?>
                                        @if($item_sv)
                                            <?php
                                            $j++;
                                            ?>
                                            {{$j}}. <s>{{$item_sv->name()}}</s><br>
                                        @endif
                                    @endif
                                @endfor
                            </details>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-left">
                        <?php
                        // Фильтр на заявку, получаем список совпавших свойств
                        $zs_notin_get = $mzs_notin->get()->where('zv_id', $item_zv->id);
                        $count_zs_notin_get = count($zs_notin_get);
                        $j = 0;
                        ?>
                        @if($count_zs_notin_get>0)
                            <details
                            >
                                <summary>{{$count_zs_notin_get}} записей</summary>
                                @foreach($zs_notin_get as $d_value)
                                    <?php
                                    $item_sv = GlobalController::get_parent_item_from_main($d_value->zs_id, $link_body_id);
                                    ?>
                                    @if($item_sv)
                                        <?php
                                        $j++;
                                        ?>
                                        {{$j}}. <span class="badge-pill badge-related">{{$item_sv->name()}}</span>
                                        <br>
                                    @endif
                                @endforeach
                            </details>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
@endsection
