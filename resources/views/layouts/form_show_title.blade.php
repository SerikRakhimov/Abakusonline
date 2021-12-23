<h3 class="display-5">
    @if ($type_form == 'show')
        {{trans('main.viewing_record')}}
    @elseif($type_form == 'delete_question')
        {{trans('main.delete_record_question')}}?
    @endif
    <span class="text-label">-</span> <span class="text-title">{{$table_name}}</span>
</h3>

