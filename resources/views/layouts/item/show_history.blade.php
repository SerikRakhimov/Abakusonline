@if($item->is_history())
{{--    <span class="text-title">--}}
{{--        <i class="fas fa-history" title="{{trans('main.is_history')}}"></i>--}}
{{--    </span>--}}
{{--<a href="" title="{{trans('main.is_history')}}">🅗</a>--}}
{{--🅗𝓱Ⓗ🅷𝘏𝘩𝙃✓✔--}}
{{--<span title="{{trans('main.is_history')}}">✔</span>--}}
<span class="badge badge-pill badge-related" title="{{trans('main.is_history')}}">{{mb_strtolower(trans('main.is_history'))}}</span>
@endif
