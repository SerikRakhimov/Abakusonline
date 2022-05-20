{{--Алгоритмы одинаковые в types.img.height.blade.php и GlobalController::types_img_height()--}}
@if($size == "avatar")
    "35"
@elseif($size == "small")
    "50"
@elseif($size == "medium")
    "250"
@elseif($size == "big")
    "450"
@endif

