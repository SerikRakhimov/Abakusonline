@if (Request::session()->has('sets_previous_url'))
    onclick="document.location='{{session('sets_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
