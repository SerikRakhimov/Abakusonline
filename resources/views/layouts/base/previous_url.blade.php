@if (Request::session()->has('bases_previous_url'))
    onclick="document.location='{{session('bases_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
