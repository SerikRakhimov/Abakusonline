@if (Request::session()->has('modules_previous_url'))
    onclick="document.location='{{session('modules_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
