<?php
//Похожие алгоритмы ext_show\parent_label.php и ext_edit\parent_label.php
//$result = false;
//if ($par_link != null) {
//    if ($par_link->id == $link->id) {
//        $result = true;
//    } else {
//        $result = false;
//    }
//} else {
//    $result = false;
//}
$result = \App\Http\Controllers\GlobalController::link_par_link($link, $par_link)
?>
@if($result)
{{--<u>{{$link->parent_label()}}</u>--}}
    <u>{{$link->parent_label(true)}}</u>
@else
{{--{{$link->parent_label()}}--}}
    {{$link->parent_label(true)}}
@endif
