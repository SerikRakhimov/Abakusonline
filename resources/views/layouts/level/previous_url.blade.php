@if (Request::session()->has('levels_previous_url'))
    onclick="document.location='{{session('levels_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
