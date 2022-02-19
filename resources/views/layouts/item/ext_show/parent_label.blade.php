<?php
//Похожие алгоритмы ext_show\parent_label.php и ext_edit\parent_label.php
$result = false;
if ($par_link != null) {
    if ($par_link->id == $link->id) {
        $result = true;
    } else {
        $result = false;
    }
} else {
    $result = false;
}
?>
@if($result)
    <u>{{$link->parent_label()}}</u>
@else
    {{$link->parent_label()}}
@endif
:
