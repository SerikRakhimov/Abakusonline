@if (Request::session()->has('tasks_previous_url'))
    onclick="document.location='{{session('tasks_previous_url')}}'"
@else
    onclick="javascript:history.back();"
@endif
