<?php
$is_align_top = isset($align_top);
?>
{{--                                если тип корректировки поля - число--}}
@if($base->type_is_number())
{{--    class="text-right"--}}
    @if($is_align_top)
        class="text-right align-top"
    @else
        class="text-right"
    @endif
    {{--                                если тип корректировки поля - дата--}}
    {{--                                если тип корректировки поля - логический--}}
@elseif($base->type_is_date() || $base->type_is_boolean() || $base->type_is_image() || $base->type_is_document())
    {{--    Использовать именно так, как в строках ниже--}}
    @if($is_align_top)
        class="text-center align-top"
    @else
        class="text-center"
    @endif
@else
    {{--    Использовать именно так, как в строках ниже--}}
    @if($is_align_top)
        class="text-left align-top"
    @else
        class="text-left"
    @endif
@endif
