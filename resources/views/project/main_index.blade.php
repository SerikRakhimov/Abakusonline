@extends('layouts.app')

@section('content')
    <?php
    use App\Http\Controllers\GlobalController;
    use App\Http\Controllers\ProjectController;
    Use App\Models\Role;
    //$i = $projects->firstItem() - 1;
    $i = 0;
    $num_cols = GlobalController::get_number_of_columns_projects();
    ?>
    <p>
    <h3 class="display-5 text-center">{{$title}}</h3>
    </p>
    <br>
    <div class="card-deck">
        @foreach($projects as $project)
            <?php
            //$i++;
            //            $role = null;
            $message = "";
            //            if ($all_projects == true) {
            //                $role = Role::where('template_id', $project->template_id)->where('is_external', true)->first();
            //                if (!$role) {
            //                    $message = trans('main.role_default_for_external_not_found');
            //                }
            //            }
            //            if ($my_projects == true) {
            //                $role = Role::where('template_id', $project->template_id)->where('is_author', true)->first();
            //                if (!$role) {
            //                    $message = trans('main.role_author_not_found');
            //                }
            //            }
            $get_items_setup = $project->get_items_setup();
            $get_project_logo_item = $get_items_setup['logo_item'];
            $roles = ProjectController::get_roles($project, $all_projects, $subs_projects, $my_projects, $mysubs_projects);
            $get_user_author_avatar_item = $project->user->get_user_avatar_item();
            ?>
            @if(count($roles) == 0)
                @continue
            @endif
            {{--        <div class="elements shadow">--}}
            {{--            <img class="elements-img-top" src="{{Storage::url('background.png')}}" alt="Card image">--}}
            {{--            <p class="elements-header">{{$project->template->name()}}</p>--}}
            {{--            <div class="elements-body">--}}
            {{--                <h4 class="elements-title">{{$project->name()}}</h4>--}}
            {{--                <p class="elements-title text-label">Id = {{$project->id}}</p>--}}
            {{--                --}}{{--                <p class="elements-text">{{$project->desc()}}</p>--}}
            {{--                <p class="elements-text"><?php echo nl2br($project->dc_ext()); ?></p>--}}
            {{--                @if($role)--}}
            {{--                    --}}{{--                ($my_projects ? 1 : 0)--}}
            {{--                    <button type="button" class="btn btn-dreamer" title="{{trans('main.run')}}"--}}
            {{--                            onclick="document.location='{{route('base.template_index', ['project'=>$project, 'role'=>$role])}}'">--}}
            {{--                        <i class="fas fa-play d-inline"></i>--}}
            {{--                        {{trans('main.run')}}--}}
            {{--                    </button>--}}
            {{--                @else--}}
            {{--                    <p class="elements-text text-danger">{{$message}}</p>--}}
            {{--                @endif--}}
            {{--            </div>--}}
            {{--            <div class="elements-footer">--}}
            {{--                <small class="text-muted">{{$project->created_at}}</small>--}}
            {{--            </div>--}}
            {{--        </div>--}}
            {{--            @if($role)--}}
            <div class="card shadow m-2">
                {{--                <img class="elements-img-top" src="{{Storage::url('background.png')}}" alt="Card image">--}}
                <div class="card-header">
                    <div class="row">
                        {{--                        <div class="col-sm-6 text-left text-title">--}}
                        <div class="col-sm-8 text-left text-label">
                            <small>{{$project->account}}</small>
                        </div>
                        <div class="col-sm-4 text-right">
                            @include('layouts.project.show_icons',['project'=>$project])
                        </div>
                    </div>
                </div>
                <div class="card-block bg-light">
                    <p class="card-text ml-3"><small class="text-muted">{{$project->template->name_is_test()}}</small>
                    </p>
                </div>
                {{-- <div class="elements-body p-0">--}}
                {{--                <div class="elements-body bg-light d-flex flex-wrap align-items-center">--}}
                <div class="card-body bg-light flex-wrap align-items-center">
                    @if($get_project_logo_item)
                        {{--                            <div class="elements-block text-center">--}}
                        <div class="card-block text-center">
                            {{--                        @include('view.img',['item'=>$get_project_logo_item, 'size'=>"medium", 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>true, 'card_img_top'=>true, 'title'=>'empty'])--}}
                            @include('view.img',['item'=>$get_project_logo_item, 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>true, 'card_img_top'=>true, 'title'=>'empty'])
                        </div>
                    @endif
                    <p>
                    <h5 class="mb-2 pb-2">{{$project->name()}}</h5>
                    {{--                <p class="elements-text">{{$project->desc()}}</p>--}}
                    <span class="card-text"><?php echo nl2br($project->dc_ext()); ?></span>
                    </p>
                    <br>
                    <form action="{{route('project.start_check')}}" method=GET" enctype=multipart/form-data>
                        @csrf
                        <input type="hidden" name="project_id" value="{{$project->id}}">
                        <input type="hidden" name="is_cancel_all_projects"
                               value="{{GlobalController::num_is_boolean($all_projects)}}">
                        <input type="hidden" name="is_cancel_subs_projects"
                               value="{{GlobalController::num_is_boolean($subs_projects)}}">
                        <input type="hidden" name="is_cancel_my_projects"
                               value="{{GlobalController::num_is_boolean($my_projects)}}">
                        <input type="hidden" name="is_cancel_mysubs_projects"
                               value="{{GlobalController::num_is_boolean($mysubs_projects)}}">
                        {{--                        <div class="form-group row justify-content-center">--}}
                        <div class="row">
                            {{--                                <div class="col-2 text-right">--}}
                            {{--                                    <label for="role_id" class="col-form-label">{{trans('main.role')}}</label>--}}
                            {{--                                </div>--}}
                            {{--                                <div class="col-6 text-center pl-1">--}}
                            <div class="col-6 text-center pr-0">
                                <select class="form-control d-inline"
                                        name="role_id" title="{{trans('main.role')}}">
                                    @foreach ($roles as $key=>$value)
                                        <option value="{{$key}}"
                                            {{--                                                    @if ($update)--}}
                                            {{--                                                    --}}{{--            "(int) 0" нужно--}}
                                            {{--                                                    @if ((old('role_id') ?? ($key ?? (int) 0)) ==  $base->type())--}}
                                            {{--                                                    selected--}}
                                            {{--                                                @endif--}}
                                            {{--                                                @endif--}}
                                        >{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{--                                <div class="col-6 text-left p-0">--}}
                            <div class="col-6 text-center pl-0">
                                <button type="submit" class="btn btn-dreamer" title="
                                @if($subs_projects == true)
                                {{trans('main.subscribe')}}
                                @else
                                {{trans('main.run')}}
                                @endif
                                    ">
                                    @if($subs_projects == true)
                                        <i class="fas fa-book-open d-inline"></i>
                                        {{trans('main.subscribe')}}
                                    @else
                                        <i class="fas fa-play d-inline"></i>
                                        {{trans('main.run')}}
                                    @endif
                                </button>
                                {{--                            </div>--}}
                            </div>
                        </div>
                    </form>
                    {{--                        Не удалять--}}
                    @if ($all_projects == true && $project->is_closed == false)
                        <p class="card-text mt-3">
                            <small
                                class="text-muted">
                                {{$project->link_info()}}
                            </small>
                        </p>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-4 text-left text-title">
                            <small class="text-muted">Id: {{$project->id}}</small>
                        </div>
                        <div class="col-sm-8 text-right">
                            <small class="text-muted">
                                {{trans('main.author')}}: {{$project->user->name()}}
                                @if($get_user_author_avatar_item)
                                    @include('view.img',['item'=>$get_user_author_avatar_item, 'size'=>"avatar", 'filenametrue'=>false, 'link'=>false, 'img_fluid'=>false, 'card_img_top'=>false, 'title'=>'empty'])
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            {{--            @else--}}
            {{--                <p class="elements-text text-danger">{{$message}}</p>--}}
            {{--            @endif--}}
            <br>
            <?php
            $i++;
            ?>
            @if($i % $num_cols == 0)
    </div>
    <div class="card-deck">
        @endif
        @endforeach
        {{-- Если строка из $num_cols элементов не завершилась до $num_cols столбцов--}}
        {{-- (т.е. $i не делится без остатка на $num_cols)--}}
        @if($i % $num_cols != 0)
            <?php
            // Подсчитываем количество оставшихся колонок
            $n = $num_cols - ($i % $num_cols);
            ?>
            {{-- В цикле $n раз вставляем вставляем пустые колонки--}}
            @for($k = 0; $k < $n; $k++)
                {{-- Вставляем пустую карточку--}}
                <div class="card m-2 bg-transparent">
                </div>
            @endfor
        @endif
    </div>

    {{--    <div class="elements">--}}
    {{--        <h3 class="elements-header">Featured</h3>--}}
    {{--        <div class="elements-block">--}}
    {{--            <h4 class="elements-title">Special title treatment</h4>--}}
    {{--            <p class="elements-text">With supporting text below as a natural lead-in to additional content.</p>--}}
    {{--            <a href="#" class="btn btn-primary">Go somewhere</a>--}}
    {{--        </div>--}}
    {{--        <div class="elements-footer">--}}
    {{--            <small class="text-muted">{{$project->created_at}}</small>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    {{--        <div class="elements bg-primary">--}}
    {{--            <div class="elements-body text-center">--}}
    {{--                <p class="elements-text">Some text inside the first elements</p>--}}
    {{--                <p class="elements-text">Some more text to increase the height</p>--}}
    {{--                <p class="elements-text">Some more text to increase the height</p>--}}
    {{--                <p class="elements-text">Some more text to increase the height</p>--}}
    {{--            </div>--}}
    {{--        </div>--}}

    {{$projects->links()}}

    {{--    <div class="elements shadow w-100 mt-2">--}}
    {{--        <div class="elements-block">--}}
    {{--            <p class="elements-text ml-3"><small class="text-muted">{{Auth::user()->name()}}</small>--}}
    {{--            </p>--}}
    {{--        </div>--}}
    {{--        <div class="elements-body">--}}
    {{--            <span class="elements-text">Принеси воды...</span></p>--}}
    {{--        </div>--}}
    {{--    </div>--}}

    {{--    <div class="elements shadow w-100 mt-2">--}}
    {{--        <div class="elements-block">--}}
    {{--            <p class="elements-text ml-3"><small class="text-muted">{{Auth::user()->name()}}</small>--}}
    {{--            </p>--}}
    {{--        </div>--}}
    {{--        <div class="elements-body">--}}
    {{--            <span class="elements-text">http://abakusonline/project/start/30 - ссылка на проект (ее можно отправить пользователям или разместить в вашем аккаунте любой социальной сети для привлечения клиентов)</span></p>--}}
    {{--        </div>--}}
    {{--    </div>--}}

    {{--    <div class="elements shadow w-100 mt-2">--}}
    {{--        <div class="elements-block">--}}
    {{--            <p class="elements-text ml-3"><small class="text-muted">{{Auth::user()->name()}}</small>--}}
    {{--            </p>--}}
    {{--        </div>--}}
    {{--        <div class="elements-body">--}}
    {{--            <span class="elements-text">Принеси молоко...</span></p>--}}
    {{--        </div>--}}
    {{--    </div>--}}

    {{--    <div class="elements mt-4 text-label">--}}
    {{--        <p class="elements-header text-label">header</p>--}}
    {{--        <div class="row align-items-center">--}}
    {{--            <div class="col-md-3">--}}
    {{--                <img class="img-fluid" src="{{Storage::url('MyPhoto.jpeg')}}" alt="Card image">--}}
    {{--            </div>--}}
    {{--            <div class="col-md-8">--}}
    {{--                <h4 class="elements-title">ttttt</h4>--}}
    {{--                <h2 class="elements-title mt-2">Yummi Foods</h2>--}}
    {{--                <p>ghghghghhh hhhhhhhhhh ghghghghghhh eeeeeeeeer bbbbbxbxbxbxbxbxbx eeererrerrr hhhhhhffgfggf</p>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--        <div class="elements-footer text-label">--}}
    {{--            <small class="text-muted">Footer</small>--}}
    {{--        </div>--}}
    {{--    </div>--}}


    {{--    <div class="elements-columns">--}}
    {{--        <div class="elements">--}}
    {{--            <img class="elements-img-top img-fluid" src="{{Storage::url('MyPhoto.jpeg')}}" alt="Card image cap">--}}
    {{--            <div class="elements-block">--}}
    {{--                <h4 class="elements-title">Card title that wraps to a new line</h4>--}}
    {{--                <p class="elements-text">This is a longer elements with supporting text below as a natural lead-in to additional--}}
    {{--                    content. This content is a little bit longer.</p>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--        <div class="elements p-3">--}}
    {{--            <blockquote class="elements-block elements-blockquote">--}}
    {{--                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>--}}
    {{--                <footer>--}}
    {{--                    <small class="text-muted">--}}
    {{--                        Someone famous in <cite title="Source Title">Source Title</cite>--}}
    {{--                    </small>--}}
    {{--                </footer>--}}
    {{--            </blockquote>--}}
    {{--        </div>--}}
    {{--        <div class="elements">--}}
    {{--            <img class="elements-img-top img-fluid" src="{{Storage::url('MyPhoto.jpeg')}}" alt="Card image cap">--}}
    {{--            <div class="elements-block">--}}
    {{--                <h4 class="elements-title">Card title</h4>--}}
    {{--                <p class="elements-text">This elements has supporting text below as a natural lead-in to additional content.</p>--}}
    {{--                <p class="elements-text"><small class="text-muted">Last updated 3 mins ago</small></p>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--        <div class="elements elements-inverse elements-primary p-3 text-center">--}}
    {{--            <blockquote class="elements-blockquote">--}}
    {{--                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat.</p>--}}
    {{--                <footer>--}}
    {{--                    <small>--}}
    {{--                        Someone famous in <cite title="Source Title">Source Title</cite>--}}
    {{--                    </small>--}}
    {{--                </footer>--}}
    {{--            </blockquote>--}}
    {{--        </div>--}}
    {{--        <div class="elements text-center">--}}
    {{--            <div class="elements-block">--}}
    {{--                <h4 class="elements-title">Card title</h4>--}}
    {{--                <p class="elements-text">This elements has supporting text below as a natural lead-in to additional content.</p>--}}
    {{--                <p class="elements-text"><small class="text-muted">Last updated 3 mins ago</small></p>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--        <div class="elements">--}}
    {{--            <img class="elements-img img-fluid" src="{{Storage::url('MyPhoto.jpeg')}}" alt="Card image">--}}
    {{--        </div>--}}
    {{--        <div class="elements p-3 text-right">--}}
    {{--            <blockquote class="elements-blockquote">--}}
    {{--                <p>11111111111111111111111Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat--}}
    {{--                    a ante.</p>--}}
    {{--                <footer>--}}
    {{--                    <small class="text-muted">--}}
    {{--                        Someone famous in <cite title="Source Title">Source Title</cite>--}}
    {{--                    </small>--}}
    {{--                </footer>--}}
    {{--            </blockquote>--}}
    {{--        </div>--}}
    {{--        <div class="elements">--}}
    {{--            <div class="elements-block">--}}
    {{--                <h4 class="elements-title">Card title</h4>--}}
    {{--                <p class="elements-text">This is a wider elements with supporting text below as a natural lead-in to additional--}}
    {{--                    content. This elements has even longer content than the first to show that equal height action.</p>--}}
    {{--                <p class="elements-text"><small class="text-muted">Last updated 3 mins ago</small></p>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}

@endsection

