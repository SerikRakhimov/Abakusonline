<?php
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
?>
@if($result)
    <i>{{$result_parent_label}}</i>
@else
    {{$result_parent_label}}
@endif


