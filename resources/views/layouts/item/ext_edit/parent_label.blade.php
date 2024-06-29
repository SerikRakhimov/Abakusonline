<?php

use App\Models\Link;

//Похожие алгоритмы ext_show\parent_label.php и ext_edit\parent_label.php
$result = false;
if ($par_link != null) {
    if ($par_link->id == $key) {
        $result = true;
    } else {
        $result = false;
    }
} else {
    $result = false;
}
$level_link = Link::find($key);
?>
@if($result)
    <u>{{$result_parent_label}}</u>
@else
    {{$result_parent_label}}
@endif
@if($level_link)
    @if ($lvl)
        {{-- Эта проверка нужна--}}
        @if($level_link->parent_level_id_0 != 0)
            @if($level_link->parent_level_id_0 == $lvl->id)
                <small class="text-title">
                    {{$level_link->level_info()}}
                </small>
            @endif
        @endif
    @endif
@endif

