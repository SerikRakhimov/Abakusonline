@if (Request::session()->has('robas_previous_url'))
    onclick="document.location='{{session('robas_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
