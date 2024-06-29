@if($name == '')
    {{-- <span class="text-danger">--}}
    {{-- {{mb_strtolower(trans('main.empty'))}}--}}
    {{mb_strtolower(trans('main.view'))}}
    {{-- </span>--}}
@else
    {{-- {{$name}}--}}
    <?php
    echo $name;
    ?>
@endif
