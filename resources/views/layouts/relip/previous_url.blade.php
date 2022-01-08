@if (Request::session()->has('relips_previous_url'))
    onclick="document.location='{{session('relips_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
