@if (Request::session()->has('users_previous_url'))
    onclick="document.location='{{session('users_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
