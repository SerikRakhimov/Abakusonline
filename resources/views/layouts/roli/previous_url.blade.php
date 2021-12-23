@if (Request::session()->has('rolis_previous_url'))
    onclick="document.location='{{session('rolis_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
