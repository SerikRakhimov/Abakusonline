@if (Request::session()->has('links_previous_url'))
    onclick="document.location='{{session('links_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
