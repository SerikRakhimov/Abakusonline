@if (Request::session()->has('moderations_previous_url'))
    onclick="document.location='{{session('moderations_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
