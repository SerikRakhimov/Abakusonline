@extends('layouts.app')

@section('content')
    <?php
    $update = isset($project);
    $is_template = isset($template);
    $is_user = isset($user);
    $closed_default_value = false;
    if ($is_template)
        $closed_default_value = $template->is_closed_default_value;
    else
        {$closed_default_value = false;}
    ?>
    <p>
        @if($is_template)
            @include('layouts.template.show_name',['template'=>$template])
        @endif
        @if($is_user)
            @include('layouts.user.show_name',['user'=>$user])
        @endif
    </p>
    <p>
        @include('layouts.form_edit_title', ['update'=>$update, 'table_name'=>trans('main.project')])
    </p>
    <form action="{{$update ? route('project.update',$project):route('project.store')}}" method="POST"
          enctype=multipart/form-data>
        @csrf

        @if ($update)
            @method('PUT')
        @endif
        <div class="form-group row">
            <div class="col-3 text-right">
                <label for="account" class="col-form-label">{{trans('main.account')}}
                    <span
                        class="text-danger">*</span></label>
            </div>
            <div class="col-7">
                <input type="text"
                       name="account"
                       class="form-control @error('account') is-invalid @enderror"
                       placeholder=""
                       value="{{ old('account') ?? ($project->account ?? '') }}"
                >
            </div>
            @error('account')
            <div class="text-danger">
                {{$message}}
            </div>
            @enderror
        </div>
        @if($is_template)
            <input type="hidden" name="template_id" value="{{$template->id}}">
        @else
            <div class="form-group row">
                <div class="col-sm-3 text-right">
                    <label for="template_id" class="col-form-label">{{trans('main.template')}}<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-sm-7">
                    <select class="form-control"
                            name="template_id"
                            id="template_id"
                            class="@error('template_id') is-invalid @enderror">
                        @foreach ($templates as $template)
                            <option value="{{$template->id}}"
                            @if ($update)
                                "(int) 0" нужно
                                {{--                                @if ((old('template_id') ?? ($key ?? (int) 0)) ==  $project->template_id)--}}
                                @if ((old('template_id') ?? ($project->template_id ?? (int) 0)) ==  $template->id)
                                    selected
                                @endif
                            @endif
                            >{{$template->name()}}</option>
                        @endforeach
                    </select>
                    @error('template_id')
                    <div class="text-danger">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-sm-2">
                </div>
            </div>
        @endif

        <div class="form-group row">
            @foreach (config('app.locales') as $key=>$value)
                <div class="col-3 text-right">
                    <label for="name_lang_{{$key}}" class="col-form-label">{{trans('main.name')}}
                        ({{trans('main.' . $value)}})<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-7">
                    <input type="text"
                           name="name_lang_{{$key}}"
                           id="name_lang_{{$key}}"
                           class="form-control @error('name_lang_' . $key) is-invalid @enderror"
                           placeholder=""
                           value="{{ old('name_lang_' . $key) ?? ($project['name_lang_' . $key] ?? '') }}">
                    @error('name_lang_' . $key)
                    <div class="text-danger">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-sm-2">
                </div>
            @endforeach
        </div>

        <div class="form-group row" id="is_test_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_test">{{trans('main.is_test')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_test') is-invalid @enderror"
                       type="checkbox"
                       name="is_test"
                       id="linkis_test"
                       placeholder=""
                       {{--                       'false' - значение по умолчанию --}}
                       @if ((old('is_test') ?? ($project->is_test ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_test')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_closed_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_closed">{{trans('main.is_closed')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_closed') is-invalid @enderror"
                       type="checkbox"
                       name="is_closed"
                       id="linkis_closed"
                       placeholder=""
                       {{--                       '$closed_default_value' - значение по умолчанию --}}
                       @if ((old('is_closed') ?? ($project->is_closed ?? $closed_default_value)) ==  true)
                       checked
                    @endif
                >
                @error('is_closed')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        @if($is_user)
            <input type="hidden" name="user_id" value="{{$user->id}}">
        @else
            <div class="form-group row">
                <div class="col-sm-3 text-right">
                    <label for="user_id" class="col-form-label">{{trans('main.author')}}<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-sm-7">
                    <select class="form-control"
                            name="user_id"
                            id="user_id"
                            class="@error('user_id') is-invalid @enderror">
                        @foreach ($users as $user)
                            <option value="{{$user->id}}"
                            @if ($update)
                                "(int) 0" нужно
                                {{--                                @if ((old('user_id') ?? ($key ?? (int) 0)) ==  $project->user_id)--}}
                                @if ((old('user_id') ?? ($project->user_id ?? (int) 0)) ==  $user->id)
                                    selected
                                @endif
                            @endif
                            >{{$user->name}}, {{$user->email}}</option>
                        @endforeach
                    </select>
                    @error('user_id')
                    <div class="text-danger">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-sm-2">
                </div>
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
                            @if($is_template && $is_user)
                            onclick="document.location='{{route('template.main_index')}}'"
                    @else
                        @include('layouts.project.previous_url')
                        @endif
                    >
                        {{--                    <i class="fas fa-arrow-left"></i>--}}
                        {{trans('main.cancel')}}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
