@if (Request::session()->has('accesses_previous_url'))
    onclick="document.location='{{session('accesses_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
