<h3 class="display-5 text-center">
    @if (!$update)
        {{trans('main.new_record')}}
    @else
        {{trans('main.edit_record')}}
    @endif
    <span class="text-info">-</span> <span class="text-success">{{$table_name}}</span>
</h3>

