@if($calc_relip_info['proj_name'] != '')
    <small
        title="{{trans('main.project')}} ({{mb_strtolower(trans('main.name'))}})">{{$calc_relip_info['proj_name']}}</small>
    <br>
@endif
@if($calc_relip_info['relit_title'] != '')
    {{--    <h5 title="{{trans('main.relit')}} ({{mb_strtolower(trans('main.title'))}})">{{$calc_relip_info['relit_title']}}</h5>--}}
    <span class="text-label"
          title="{{trans('main.relit')}} ({{mb_strtolower(trans('main.title'))}})">{{$calc_relip_info['relit_title']}}</span>
@endif
