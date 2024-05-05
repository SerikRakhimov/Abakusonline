{{--Алгоритмы одинаковые в types.img.height.blade.php и GlobalController::types_img_height()--}}
@if($size == "avatar")
    "30px"
@elseif($size == "small")
    "50px"
@elseif($size == "shundred")
    "100px"
@elseif($size == "smed")
    "125px"
@elseif($size == "mem")
    "200px"
@elseif($size == "medium")
    "250px"
@elseif($size == "big")
    "450px"
@endif

