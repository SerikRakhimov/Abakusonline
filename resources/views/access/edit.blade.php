@extends('layouts.app')

@section('content')
    <?php
    use App\Models\Role;
    $update = isset($access);
    $is_project = isset($project);
    $is_user = isset($user);
    $is_role = isset($role);
    ?>
    <p>
        @if($is_project)
            @include('layouts.project.show_name',['project'=>$project])
        @endif
        @if($is_user)
            @include('layouts.user.show_name',['user'=>$user])
        @endif
        @if($is_role)
            @include('layouts.role.show_name',['role'=>$role])
        @endif
    </p>
    <p>
        @include('layouts.form_edit_title', ['update'=>$update, 'table_name'=>trans('main.access')])
    </p>
    <form action="{{$update ? route('access.update', $access):route('access.store')}}" method="POST"
          enctype=multipart/form-data name="form">
        @csrf

        @if ($update)
            @method('PUT')
        @endif
        @if($is_project)
            <input type="hidden" name="project_id" value="{{$project->id}}">
        @else
            <div class="form-group row">
                <div class="col-sm-3 text-right">
                    <label for="project_id" class="col-form-label">{{trans('main.project')}}<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-sm-7">
                    <select class="form-control"
                            name="project_id"
                            id="project_id"
                            class="@error('project_id') is-invalid @enderror">
                        @foreach ($projects as $project)
                            <?php
                            //                            Только те проекты, у которых where('roles.is_default_for_external', true)
                            $find = Role::where('template_id', $project->template_id)->where('is_default_for_external', true)->exists();
                            ?>
                            @if($find)
                                <option value="{{$project->id}}"
                                        @if ($update)
                                        @if ((old('project_id') ?? ($access->project_id ?? (int) 0)) ==  $project->id)
                                        selected
                                    @endif
                                    @endif
                                >{{$project->name()}}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('project_id')
                    <div class="text-danger">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-sm-2">
                </div>
            </div>
        @endif

        @if($is_user)
            <input type="hidden" name="user_id" value="{{$user->id}}">
        @else
            <div class="form-group row">
                <div class="col-sm-3 text-right">
                    <label for="user_id" class="col-form-label">{{trans('main.user')}}<span
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
                                    @if ((old('user_id') ?? ($access->user_id ?? (int) 0)) ==  $user->id)
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
        @if($is_role)
            <input type="hidden" name="role_id" value="{{$role->id}}">
        @else
            <div class="form-group row">
                <div class="col-sm-3 text-right">
                    <label for="role_id" class="col-form-label">{{trans('main.role')}}<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-sm-7">
                    <select class="form-control"
                            name="role_id"
                            id="role_id"
                            class="@error('role_id') is-invalid @enderror">
                        @foreach ($roles as $role)
                            <option value="{{$role->id}}"
                                    @if ($update)
                                    @if ((old('role_id') ?? ($access->role_id ?? (int) 0)) ==  $role->id)
                                    selected
                                @endif
                                @endif
                            >{{$role->name()}}</option>
                        @endforeach
                    </select>
                    @error('role_id')
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
                <div class="col-3 text-right">
                    <label for="additional_information" class="col-form-label">
                        {{trans('main.additional_information')}}<span
                            class="text-danger">*</span></label>
                </div>
                <div class="col-7">
                    <input type="text"
                           name="additional_information"
                           id="additional_information"
                           class="form-control @error('additional_information') is-invalid @enderror"
                           placeholder=""
                           value="{{ old('additional_information') ?? ($access->additional_information ?? '') }}"
                           disabled>
                    @error('additional_information')
                    <div class="text-danger">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-sm-2">
                </div>
        </div>

        <div class="form-group row" id="is_subscription_request_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_subscription_request">{{trans('main.is_subscription_request')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_subscription_request') is-invalid @enderror"
                       type="checkbox"
                       name="is_subscription_request"
                       id="linkis_subscription_request"
                       placeholder=""
                       {{--                       'false' - значение по умолчанию --}}
                       @if ((old('is_subscription_request') ?? ($access->is_subscription_request ?? false)) ==  true)
                       checked
                    @endif
                disabled>
                @error('is_subscription_request')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div class="form-group row" id="is_access_allowed_form_group">
            <div class="col-sm-3 text-right">
                <label class="form-label"
                       for="is_access_allowed">{{trans('main.is_access_allowed')}}</label>
            </div>
            <div class="col-sm-7">
                <input class="@error('is_access_allowed') is-invalid @enderror"
                       type="checkbox"
                       name="is_access_allowed"
                       id="linkis_access_allowed"
                       placeholder=""
                       {{--                       'false' - значение по умолчанию --}}
                       @if ((old('is_access_allowed') ?? ($access->is_access_allowed ?? false)) ==  true)
                       checked
                    @endif
                >
                @error('is_access_allowed')
                <div class="invalid-feedback">
                    {{$message}}
                </div>
                @enderror
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <br>
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-5 text-right">
                    <button type="submit" class="btn btn-dreamer"
                            @if (!$update)
                            title="{{trans('main.add')}}">
                        <i class="fas fa-save d-inline"></i>
                        {{trans('main.add')}}
                        @else
                            title="{{trans('main.save')}}">
                            <i class="fas fa-save d-inline"></i>
                            {{trans('main.save')}}
                        @endif
                    </button>
                </div>
                <div class="col-2">
                </div>
                <div class="col-5 text-left">
                    <button type="button" class="btn btn-dreamer" title="{{trans('main.cancel')}}"
                        @include('layouts.access.previous_url')
                    >
                        <i class="fas fa-arrow-left d-inline"></i>
                        {{trans('main.cancel')}}
                    </button>
                </div>
            </div>
        </div>
    </form>

    @if ($is_user)
        <script>
            var project_id = form.project_id;
            var role_id = form.role_id;
            var role_id_value = null;

            function project_id_changeOption(first) {
                axios.get('/access/get_roles_options_from_project/' + project_id.options[project_id.selectedIndex].value).then(function (res) {
                    // если запуск функции не при загрузке страницы
                    if (first != true) {
                        // сохранить текущие значения
                        var role_id_value = role_id.options[role_id.selectedIndex].value;
                    }

                    if (res.data['result_roles_options'] == "") {
                        role_id.innerHTML = '<option value = "0">{{trans('main.no_information_on')}} "' + res.data['result_child_base_name'] + '"!</option>';
                    } else {
                        // заполнение select
                        role_id.innerHTML = res.data['result_roles_options'];
                    }

                    // только если запуск функции при загрузке страницы
                    if (first == true) {
                        // нужно чтобы при первом вызове формы корректировки записи значения полей соответствовали значениям из базы данных
                        @if ($update)  // при корректировке записи
                        for (let i = 0; i < role_id.length; i++) {
                            // если элемент списка = текущему значению из базы данных
                            if (role_id[i].value == {{$access->role_id}}) {
                                // установить selected на true
                                role_id[i].selected = true;
                            }
                        }
                        @endif
                    } else {
                        // нужно чтобы после обновления списка сохранить текущий выбор если соответствующий(child/parent) base не поменялся (при добавлении/корректировке записи)
                        for (let i = 0; i < role_id.length; i++) {
                            // если элемент списка = предыдущему(текущему) значению из базы данных
                            if (role_id[i].value == role_id_value) {
                                // установить selected на true
                                role_id[i].selected = true;
                            }
                        }
                    }
                });
            }

            project_id.addEventListener("change", project_id_changeOption);

            window.onload = function () {
                project_id_changeOption(true);
            };

        </script>
    @endif
@endsection
