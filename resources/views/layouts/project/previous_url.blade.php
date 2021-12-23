@if (Request::session()->has('projects_previous_url'))
    onclick="document.location='{{session('projects_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
