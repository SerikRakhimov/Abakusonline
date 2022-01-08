@if (Request::session()->has('relits_previous_url'))
    onclick="document.location='{{session('relits_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
