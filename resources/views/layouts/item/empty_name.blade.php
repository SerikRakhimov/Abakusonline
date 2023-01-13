@if($name == '')
    <span class="text-danger">{{mb_strtolower(trans('main.empty'))}}</span>
@else
{{--    {{$name}}--}}
<?php
echo $name;
?>
@endif
