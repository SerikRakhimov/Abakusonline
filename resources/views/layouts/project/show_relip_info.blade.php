@if($calc_relip_info['proj_name'] != '')
    <small
        title="{{trans('main.project')}} ({{mb_strtolower(trans('main.name'))}})">{{$calc_relip_info['proj_name']}}</small>
@endif
@if($calc_relip_info['relit_title'] != '')
    <h6 title="{{trans('main.relit')}} ({{mb_strtolower(trans('main.title'))}})">{{$calc_relip_info['relit_title']}}</h6>
@endif
