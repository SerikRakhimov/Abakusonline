@extends('layouts.app')

@section('content')
    <?php
    $update = isset($user);
    $edit_change_password = false;
    if (isset($change_password)) {
        $edit_change_password = $change_password;
    }

    ?>
    <h3 class="display-5 text-center">
        @if (!$update)
            {{trans('main.new_record')}}
        @else
            {{trans('main.edit_record')}}
        @endif
        <span class="text-info">-</span> <span class="text-success">{{trans('main.user')}}</span>
    </h3>
    <br>

    <form action="{{$update ? route('user.update',$user):route('user.store')}}" method="POST"
          enctype=multipart/form-data>
        @csrf

        @if ($update)
            @method('PUT')
        @endif

        <div class="form-group row">
            <div class="col-3 text-right">
                <label for="name" class="col-form-label">{{trans('main.name')}}
                    <span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-7">
                <input type="text"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('name') ?? ($user->name ?? '') }}"
                       @if(($update && $edit_change_password))
                       readonly
                    @endif
                >
            </div>
            @error('name')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>
        <div class="form-group row">
            <div class="col-3 text-right">
                <label for="email" class="col-form-label">{{trans('main.e-mail')}}
                    <span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-7">
                <input type="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('email') ?? ($user->email ?? '') }}"
                       @if(($update && $edit_change_password))
                       readonly
                    @endif
                >
            </div>
            @error('email')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>

        @if(($update && !$edit_change_password))
            <input type="hidden" name="password" value="{{$user->password}}">
        @else
            <div class="form-group row">
                <div class="col-3 text-right">
                    <label for="password" class="col-form-label">{{trans('main.password')}}
                        <span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-7">
                    <input type="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder=""
                           value="{{ old('password') ?? '' }}">
                </div>
                @error('password')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="form-group row">
                <div class="col-3 text-right">
                    <label for="confirm_password" class="col-form-label">{{trans('main.confirm_password')}}
                        <span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-7">
                    <input type="password"
                           name="confirm_password"
                           class="form-control @error('confirm_password') is-invalid @enderror"
                           placeholder=""
                           value="{{ old('confirm_password') ?? '' }}">
{{--                    value="{{ old('confirm_password') ?? ($user->confirm_password ?? '') }}">--}}
                </div>
                @error('confirm_password')
                <div class="text-danger">
                    {{$message}}
                </div>
                @enderror
            </div>
        @endif
        <br>
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-5 text-right">
                    <button type="submit" class="btn btn-dreamer"
                            @if (!$update)
                            title="{{trans('main.add')}}">
                        {{--                    <i class="fas fa-save"></i>--}}
                        {{trans('main.add')}}
                        @else
                            title="{{trans('main.save')}}">
                            {{--                        <i class="fas fa-save"></i>--}}
                            {{trans('main.save')}}
                        @endif
                    </button>
                </div>
                <div class="col-2">
                </div>
                <div class="col-5 text-left">
                    <button type="button" class="btn btn-dreamer" title="{{trans('main.cancel')}}"
                        @include('layouts.user.previous_url')
                    >
                        {{--                    <i class="fas fa-arrow-left"></i>--}}
                        {{trans('main.cancel')}}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
