@if (Request::session()->has('base_index_previous_url'))
    onclick="document.location='{{session('base_index_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
