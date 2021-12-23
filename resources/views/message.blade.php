@extends('layouts.app')

@section('content')
    {{--            Похожие строки layouts\app.blade.php и message.blade.php--}}
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <p>
        <h5 class="display-5 text-danger text-center">{{$message}}</h5>
        </p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endsection
