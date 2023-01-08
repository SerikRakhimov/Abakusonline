@if($name == '')
    <span class="text-danger">{{mb_strtolower(trans('main.empty'))}}</span>
@else
<!--    --><?php
//        echo $name;
//        ?>
    {{$name}}
@endif
