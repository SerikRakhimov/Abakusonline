@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{trans('main.information')}}</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    {{(trans('main.you_are_logged_in') . '!')}}
                        <br>
                        <?php
                        // Ссылка на проект Настройки пользователя
                        $usersetup_link = env('USERSETUP_LINK');
                        ?>
                        @if($usersetup_link !='')
                            {{(trans('main.specify_additional_settings'))}}:
                            <a href="{{$usersetup_link}}"
                               title="{{trans('main.personal_account_of_the_user')}}">
                                <big>{{trans('main.personal_account_of_the_user')}}</big>
                            </a>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
