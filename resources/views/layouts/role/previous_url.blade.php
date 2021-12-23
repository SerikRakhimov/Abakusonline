@if (Request::session()->has('roles_previous_url'))
    onclick="document.location='{{session('roles_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
