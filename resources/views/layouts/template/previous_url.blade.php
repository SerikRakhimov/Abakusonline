@if (Request::session()->has('templates_previous_url'))
    onclick="document.location='{{session('templates_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
