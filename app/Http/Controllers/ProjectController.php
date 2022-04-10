<?php

namespace App\Http\Controllers;

use App\Models\Access;
use App\Models\Base;
use App\Models\Item;
use App\Rules\IsLatinProject;
use App\Rules\IsLowerProject;
use App\Rules\IsOneWordProject;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Project;
use App\Models\Template;
use App\Models\Role;
use App\Models\Set;
use App\Models\Relit;
use App\Models\Relip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Types\Boolean;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    protected function account_rules()
    {
        return [
            'account' => ['required', 'string', 'max:255', 'unique:projects', new IsOneWordProject(), new IsLatinProject(), new IsLowerProject()],
        ];
    }

    protected function name_lang_0_rules()
    {
        return ['name_lang_0' => ['required', 'max:255'],
        ];
    }

    function all_index()
    {
        $projects = Project::where('is_closed', false)
            ->whereHas('template.roles', function ($query) {
                $query->where('is_default_for_external', true)
                    ->where('is_author', false);
            });

//        $projects = Project::whereHas('template.roles', function ($query) {
//                $query->where('is_default_for_external', true)
//                    ->where('is_author', false);
//            });

        if (Auth::check()) {
            // 'orwhereHas' правильно
            $projects = $projects->orwhereHas('accesses', function ($query) {
                $query->where('user_id', GlobalController::glo_user_id())
                    ->where('is_access_allowed', true);
            })->whereHas('template.roles', function ($query) {
                $query->where('is_author', false);
            });
        }

        $projects = $projects->orderBy('user_id')->orderBy('template_id')->orderBy('created_at');
        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $projects = $projects->orderBy($name);
        }
        //session(['projects_previous_url' => request()->url()]);
        return view('project/main_index', ['projects' => $projects->paginate(60),
            'all_projects' => true, 'subs_projects' => false, 'my_projects' => false, 'mysubs_projects' => false,
            'title' => trans('main.all_projects')]);
    }

    function subs_index()
    {
//        $projects = Project::where('is_closed', true)
//            ->whereHas('template.roles', function ($query) {
//                $query->where('is_author', false)->where('is_default_for_external', false);
//            })
//            ->orderBy('user_id')->orderBy('template_id')->orderBy('created_at');
        $projects = Project::where('is_closed', true)
            ->whereHas('template.roles', function ($query) {
                $query->where('is_author', false);
            })
            ->orderBy('user_id')->orderBy('template_id')->orderBy('created_at');

        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $projects = $projects->orderBy($name);
        }
        //session(['projects_previous_url' => request()->url()]);
        return view('project/main_index', ['projects' => $projects->paginate(60),
            'all_projects' => false, 'subs_projects' => true, 'my_projects' => false, 'mysubs_projects' => false,
            'title' => trans('main.subscribe')]);
    }

    function my_index()
    {
//        $projects = Project::where('user_id', GlobalController::glo_user_id())
//            ->whereHas('accesses', function ($query) {
//                $query->where('user_id', GlobalController::glo_user_id())
//                    ->where('is_access_allowed', true);
//            })
//            ->whereHas('template.roles', function ($query) {
//                $query->where('is_author', true)
//                    ->orwhere('is_default_for_external', true);
//            })->orderBy('user_id')->orderBy('template_id')->orderBy('created_at');

        // Проекты, у которых автор проекта = текущему пользователю
        $projects = Project::where('user_id', GlobalController::glo_user_id())
            ->orwhereHas('template.roles', function ($query) {
                $query->where('is_author', true);
            })->orderBy('user_id')->orderBy('template_id')->orderBy('created_at');

        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $projects = $projects->orderBy($name);
        }
        //session(['projects_previous_url' => request()->url()]);
        return view('project/main_index', ['projects' => $projects->paginate(60),
            'all_projects' => false, 'subs_projects' => false, 'my_projects' => true, 'mysubs_projects' => false,
            'title' => trans('main.my_projects')]);
    }

    function mysubs_index()
    {
//        $projects = Project::whereHas('accesses', function ($query) {
//            $query->where('user_id', GlobalController::glo_user_id())
//                ->where('is_access_allowed', true);
//        })->whereHas('template.roles', function ($query) {
//            $query->where('is_default_for_external', true)
//                ->where('is_author', false);
//        })
//            ->orderBy('user_id')->orderBy('template_id')->orderBy('created_at');
        $projects = Project::whereHas('accesses', function ($query) {
            $query->where('user_id', GlobalController::glo_user_id());
        })->whereHas('template.roles', function ($query) {
            $query->where('is_author', false);
        })
            ->orderBy('user_id')->orderBy('template_id')->orderBy('created_at');

        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $projects = $projects->orderBy($name);
        }
        //session(['projects_previous_url' => request()->url()]);
        return view('project/main_index', ['projects' => $projects->paginate(60),
            'all_projects' => false, 'subs_projects' => false, 'my_projects' => false, 'mysubs_projects' => true,
            'title' => trans('main.my_subscriptions')]);
    }

    static function get_roles(Project $project, bool $all_projects, bool $subs_projects, bool $my_projects, bool $mysubs_projects)
    {
        $result = array();
        if ($all_projects == true) {
            $roles = Role::where('template_id', $project->template->id)
                ->where('is_default_for_external', true)
                ->where('is_author', false)
                ->whereHas('template', function ($query) use ($project) {
                    $query->where('id', $project->template_id)
                        ->whereHas('projects', function ($query) use ($project) {
                            $query->where('id', $project->id)
                                ->where('is_closed', false);
                        });
                })
                ->orderBy('serial_number')->get();
            foreach ($roles as $role) {
                $result[$role->id] = $role->name();
            }
            if (Auth::check()) {
                $accesses = Access::where('project_id', $project->id)
                    ->where('user_id', GlobalController::glo_user_id())
                    ->whereHas('role', function ($query) {
                        $query->where('is_author', false)
                            ->orderBy('serial_number');
                    })
                    ->where('is_access_allowed', true)
                    ->get();
                foreach ($accesses as $access) {
                    $role = $access->role;
                    $result[$role->id] = $role->name();
                }
            }

        } elseif ($subs_projects == true) {

            $roles = Role::where('is_author', false)
                ->whereHas('template', function ($query) use ($project) {
                    $query->where('id', $project->template_id)
                        ->whereHas('projects', function ($query) use ($project) {
                            $query->where('id', $project->id)
                                ->where('is_closed', true);
                        });
                })
                ->whereDoesntHave('accesses', function ($query) use ($project) {
                    $query->where('user_id', GlobalController::glo_user_id())
                        ->where('project_id', $project->id);
                })->orderBy('serial_number')->get();

            foreach ($roles as $role) {
                $result[$role->id] = $role->name();
            }

        } elseif ($my_projects == true) {
//            $roles = Role::where('is_author', true)
//                ->whereHas('template', function ($query) use ($project) {
//                    $query->where('id', $project->template_id)
//                        ->whereHas('projects', function ($query) use ($project) {
//                            $query->where('id', $project->id)
//                                ->where('user_id', GlobalController::glo_user_id());
//                        });
//                })
//                ->orwhere('is_default_for_external', true)
//                ->whereHas('template', function ($query) use ($project) {
//                    $query->where('id', $project->template_id)
//                        ->whereHas('projects', function ($query) use ($project) {
//                            $query->where('id', $project->id)
//                                ->where('user_id', GlobalController::glo_user_id());
//                        });
//                })->get();
//
//            foreach ($roles as $role) {
//                $result[$role->id] = $role->name();
//            }

//            $accesses = Access::where('project_id', $project->id)
//                ->where('user_id', GlobalController::glo_user_id())
//                ->whereHas('role', function ($query) {
//                    $query->where('is_author', true)
//                        ->orwhere('is_default_for_external', true);
//                })
//                ->orderBy('role_id')->get();
//            foreach ($accesses as $access) {
//                $role = $access->role;
//                $result[$role->id] = $role->name();
//            }

            $roles = Role::where('is_author', true)
                ->whereHas('template', function ($query) use ($project) {
                    $query->where('id', $project->template_id)
                        ->whereHas('projects', function ($query) use ($project) {
                            $query->where('id', $project->id)
                                ->where('user_id', GlobalController::glo_user_id());
                        });
                })
                ->whereDoesntHave('accesses', function ($query) use ($project) {
                    $query->where('user_id', GlobalController::glo_user_id())
                        ->where('project_id', $project->id);
                })->orderBy('serial_number')->get();

            foreach ($roles as $role) {
                $result[$role->id] = $role->name();
            }
            // Все подписки и роли пользователя
            $accesses = Access::where('project_id', $project->id)
                ->where('user_id', GlobalController::glo_user_id())
                ->whereHas('role', function ($query) {
                    $query->where('is_author', true)
                        ->orderBy('serial_number');
                })
                ->get();
            foreach ($accesses as $access) {
                $role = $access->role;
                $result[$role->id] = $role->name();
            }
            // Все запросы на подписку и роли пользователя
            $accesses = Access::where('project_id', $project->id)
                ->where('user_id', GlobalController::glo_user_id())
                ->whereHas('role', function ($query) {
                    $query->where('is_author', true)
                        ->orderBy('serial_number');
                })
                ->where('is_subscription_request', true)
                ->where('is_access_allowed', false)
                ->get();
            foreach ($accesses as $access) {
                $role = $access->role;
                $result[$role->id] = $result[$role->id] . " (" . trans('main.subscription_request_sent') . ")";
            }

            // Все закрытые доступы и роли пользователя
            $accesses = Access::where('project_id', $project->id)
                ->where('user_id', GlobalController::glo_user_id())
                ->whereHas('role', function ($query) {
                    $query->where('is_author', true)
                        ->orderBy('serial_number');
                })
                ->where('is_subscription_request', false)
                ->where('is_access_allowed', false)
                ->get();
            foreach ($accesses as $access) {
                $role = $access->role;
                $result[$role->id] = $result[$role->id] . " (" . trans('main.access_denied') . ")";
            }

        } elseif ($mysubs_projects == true) {
            if (Auth::check()) {
                // Все подписки и роли пользователя
                $accesses = Access::where('project_id', $project->id)
                    ->where('user_id', GlobalController::glo_user_id())
                    ->whereHas('role', function ($query) {
                        $query->where('is_author', false)
                            ->orderBy('serial_number');
                    })->get();
                foreach ($accesses as $access) {
                    $role = $access->role;
                    $result[$role->id] = $role->name();
                }

                // Все запросы на подписку и роли пользователя
                $accesses = Access::where('project_id', $project->id)
                    ->where('user_id', GlobalController::glo_user_id())
                    ->whereHas('role', function ($query) {
                        $query->where('is_author', false)
                            ->orderBy('serial_number');
                    })
                    ->where('is_subscription_request', true)
                    ->where('is_access_allowed', false)
                    ->get();
                foreach ($accesses as $access) {
                    $role = $access->role;
                    $result[$role->id] = $result[$role->id] . " (" . trans('main.subscription_request_sent') . ")";
                }

                // Все закрытые доступы и роли пользователя
                $accesses = Access::where('project_id', $project->id)
                    ->where('user_id', GlobalController::glo_user_id())
                    ->whereHas('role', function ($query) {
                        $query->where('is_author', false)
                            ->orderBy('serial_number');
                    })
                    ->where('is_subscription_request', false)
                    ->where('is_access_allowed', false)
                    ->get();
                foreach ($accesses as $access) {
                    $role = $access->role;
                    $result[$role->id] = $result[$role->id] . " (" . trans('main.access_denied') . ")";
                }

                // Все недопустимые комбинации  и роли пользователя
                $accesses = Access::where('project_id', $project->id)
                    ->where('user_id', GlobalController::glo_user_id())
                    ->whereHas('role', function ($query) {
                        $query->where('is_author', false)
                            ->orderBy('serial_number');
                    })
                    ->where('is_subscription_request', true)
                    ->where('is_access_allowed', true)
                    ->get();
                foreach ($accesses as $access) {
                    $role = $access->role;
                    $result[$role->id] = $result[$role->id] . " (" . trans('main.invalid_parameter_combination') . ")";
                }

            }
        }
        return $result;
    }

    // для access/index.php и access/show.php
    static function subs_desc(Access $access)
    {
        $result = '';
        if ($access->is_subscription_request == false && $access->is_access_allowed == false) {
            // Доступ запрещен
            // " . '!'" нужно для удобства,
            // чтобы лучше видно было в списке "Доступ запрещен!" по сравнению с похожим по количеству букв "Доступ разрешен"
            $result = trans('main.access_denied') . '!';

        } elseif ($access->is_subscription_request == false && $access->is_access_allowed == true) {
            // Доступ разрешен
            $result = trans('main.is_access_allowed');

        } elseif ($access->is_subscription_request == true && $access->is_access_allowed == false) {
            // Запрос на подписку
            $result = trans('main.subscription_request');

        } elseif ($access->is_subscription_request == true && $access->is_access_allowed == true) {
            // Такая комбинация недопустима
            $result = trans('main.subscription_request');

        }
        return $result;

    }

    static function acc_check(Project $project, Role $role)
    {
        $is_open_default = false;
        $is_request = false;
        $is_subs = false;
        $is_delete = false;
        $is_ask = false;
        $is_access_allowed = false;
        if (Auth::check()) {
            $user = GlobalController::glo_user();
            // Проект открыт и роль = is_default_for_external
            $is_open_default = ($project->is_closed == false) && ($role->is_default_for_external == true);
            $access = Access::where('project_id', $project->id)
                ->where('role_id', $role->id)
                ->where('user_id', $user->id)->first();
            if (@$is_open_default) {
                if ($access) {
                    // Доступ разрешен
                    if ($access->is_subscription_request == false && $access->is_access_allowed == true) {
                        // Доступ к проекту разрешен
                        $is_access_allowed = true;
                        // Удаление подписки
                        $is_delete = true;
                    }
                } else {
                    // Доступ к проекту разрешен
                    $is_access_allowed = true;
                    // Подписка
                    $is_subs = true;
                }
            } else {
                // Запрос на подписку
                $is_request = true;
                if ($access) {
                    // Доступ разрешен
                    if ($access->is_subscription_request == false && $access->is_access_allowed == true) {
                        // Доступ к проекту разрешен
                        $is_access_allowed = true;
                        // Удаление подписки
                        $is_delete = true;
                        // Предварительный запрос Да/Нет
                        $is_ask = true;
                    }
                }
            }
            if ($is_delete == true) {
                // Подписка автора проекта с авторской ролью не удаляется
                if ($project->user_id == $access->user_id && $role->is_author == true) {
                    $is_delete = false;
                }
            }
        }
        return ['is_open_default' => $is_open_default, 'is_request' => $is_request, 'is_subs' => $is_subs, 'is_delete' => $is_delete,
            'is_ask' => $is_ask, 'is_access_allowed' => $is_access_allowed];
    }

    function subs_create_form(Request $request)
    {
        $project = Project::findOrFail($request->project_id);
        $role = Role::findOrFail($request->role_id);
        $is_cancel_all_projects = $request->is_cancel_all_projects ? true : false;
        $is_cancel_subs_projects = $request->is_cancel_subs_projects ? true : false;
        $is_cancel_my_projects = $request->is_cancel_my_projects ? true : false;
        $is_cancel_mysubs_projects = $request->is_cancel_mysubs_projects ? true : false;
        $is_request = $request->is_request;
        $is_subs = $request->is_subs;
        $is_delete = $request->is_delete;
        $additional_information = isset($request->additional_information) ? $request->additional_information : "";

        if ($is_subs == true) {
            // создать новую запись
            $access = new Access();
            $access->project_id = $project->id;
            $access->role_id = $role->id;
            $access->user_id = GlobalController::glo_user_id();
            // Если запрос на подписку
            if ($is_request) {
                // Запрос на подписку
                $access->is_subscription_request = true;
                $access->additional_information = $additional_information;
                $access->is_access_allowed = false;
            } else {
                // Подписка с разрешением доступа проходит автоматически
                $access->is_subscription_request = false;
                $access->additional_information = '';
                $access->is_access_allowed = true;
            }
            $access->save();

            $project = $access->project;
            // Автору проекта не посылать
            if ($project->user_id != $access->user_id) {
                // Если запрос на подписку - послать по почте автору проекта
                if ($is_request) {
                    if (env('MAIL_ENABLED') == 'yes') {
                        $email_to = $project->user->email;
                        $appname = config('app.name', 'Abakus');
                        try {
                            Mail::send(['html' => 'mail/access_create'], ['access' => $access],
                                function ($message) use ($email_to, $appname, $project) {
                                    $message->to($email_to, '')->subject($project->name() . ' - ' . trans('main.subscription_request_sent'));
                                    $message->from(env('MAIL_FROM_ADDRESS', ''), $appname);
                                });
                        } catch (Exception $exc) {
                            return trans('error_sending_email') . ": " . $exc->getMessage();
                        }
                    }
                }
            }
        }

        if ($is_delete == true) {
            // Находим подписку
            $access = Access::where('project_id', $project->id)
                ->where('user_id', GlobalController::glo_user_id())
                ->where('role_id', $role->id)
                ->first();

            // Если найдено, то удаляем запись
            if ($access) {
                $access->delete();

                // Автору проекта не посылать
                if ($project->user_id != $access->user_id) {
                    // Послать подписчику об изменении статуса подписки
                    if (env('MAIL_ENABLED') == 'yes') {
                        $email_to = $access->user->email;
                        $appname = config('app.name', 'Abakus');
                        try {
                            Mail::send(['html' => 'mail/access_update'], ['access' => $access],
                                function ($message) use ($email_to, $appname, $project) {
                                    $message->to($email_to, '')->subject($project->name() . ' - ' . trans('main.subscription_status_has_changed'));
                                    $message->from(env('MAIL_FROM_ADDRESS', ''), $appname);
                                });
                        } catch (Exception $exc) {
                            return trans('error_sending_email') . ": " . $exc->getMessage();
                        }
                    }
                }
            }
        }

        $acc_check = self::acc_check($project, $role);
        if ($acc_check['is_access_allowed'] == true) {
            // Запуск проекта
            return redirect()->route('project.start',
                ['project' => $project->id, 'role' => $role->id]);
        } else {
            if ($is_cancel_all_projects == true) {
                return redirect()->route('project.all_index');
            } elseif ($is_cancel_subs_projects == true) {
                return redirect()->route('project.subs_index');
            } elseif ($is_cancel_my_projects == true) {
                return redirect()->route('project.my_index');
            } elseif ($is_cancel_mysubs_projects == true) {
                return redirect()->route('project.mysubs_index');
            }
        }
    }

    function subs_create(bool $is_request, Project $project, Role $role)
    {
        return view('project/ask_subs_form', ['project' => $project, 'role' => $role,
            'is_subs' => true, 'is_delete' => false,
            'is_request' => $is_request,
            'is_cancel_all_projects' => false,
            'is_cancel_subs_projects' => false,
            'is_cancel_my_projects' => false,
            'is_cancel_mysubs_projects' => false
        ]);
    }

    function subs_delete(Project $project, Role $role)
    {
        return view('project/ask_subs_form', ['project' => $project, 'role' => $role,
            'is_subs' => false, 'is_delete' => true,
            'is_request' => false,
            'is_cancel_all_projects' => false,
            'is_cancel_subs_projects' => false,
            'is_cancel_my_projects' => false,
            'is_cancel_mysubs_projects' => false
        ]);
    }

    static function current_status(Project $project, Role $role)
    {
        $result = '';
        $user = GlobalController::glo_user();
        $access = Access::where('project_id', $project->id)
            ->where('role_id', $role->id)
            ->where('user_id', $user->id)->first();
        if ($access) {
            if ($access->is_subscription_request == false && $access->is_access_allowed == false) {
                // Вы подписаны, доступ запрещен
                $result = trans('main.you_are_subscribed') . ', ' . mb_strtolower(trans('main.access_denied'));

            } elseif ($access->is_subscription_request == false && $access->is_access_allowed == true) {
                // Вы подписаны, доступ разрешен
                $result = trans('main.you_are_subscribed') . ', ' . mb_strtolower(trans('main.is_access_allowed'));

            } elseif ($access->is_subscription_request == true && $access->is_access_allowed == false) {
                // Отправлен запрос на подписку
                $result = trans('main.subscription_request_sent');

            } elseif ($access->is_subscription_request == true && $access->is_access_allowed == true) {
                // Вы подписаны, такая комбинация недопустима
                $result = trans('main.you_are_subscribed') . ', ' . mb_strtolower(trans('main.invalid_parameter_combination'));
            }
        } else {
            // Вы не подписаны
            $result = trans('main.you_are_not_subscribed');
            // Проект открыт и роль = is_default_for_external
            $is_open_default = ($project->is_closed == false) && ($role->is_default_for_external == true);
            if ($is_open_default == true) {
                $result = $result . ', ' . mb_strtolower(trans('main.is_access_allowed'));
            }
        }

        return $result;

    }

    // Вызывается из main_index.php
    function start_check(Request $request)
    {
        $project = Project::findOrFail($request->project_id);
        $role = Role::findOrFail($request->role_id);
        $is_cancel_all_projects = $request->is_cancel_all_projects ? true : false;
        $is_cancel_subs_projects = $request->is_cancel_subs_projects ? true : false;
        $is_cancel_my_projects = $request->is_cancel_my_projects ? true : false;
        $is_cancel_mysubs_projects = $request->is_cancel_mysubs_projects ? true : false;
        $acc_check = self::acc_check($project, $role);
        $is_request = $acc_check['is_request'];

        // Проект открыт и роль = is_default_for_external
        $open_default = ($project->is_closed == false) && ($role->is_default_for_external == true);

        $access = null;

        if (Auth::check()) {
            $user = GlobalController::glo_user();
            $access = Access::where('project_id', $project->id)
                ->where('role_id', $role->id)
                ->where('user_id', $user->id)->first();
        } else {
            $access = null;
        }

        if ($access) {
            $is_delete = true;
            // Подписка автора проекта с авторской ролью не удаляется
            if ($project->user_id == $access->user_id && $role->is_author == true) {
                $is_delete = false;
            }
            if ($access->is_subscription_request == false && $access->is_access_allowed == false) {
                // Доступ запрещен, далее страница отмены подписки
                return view('project/ask_subs_form', ['project' => $project, 'role' => $role,
                    'is_subs' => false, 'is_delete' => $is_delete,
                    'is_request' => false,
                    'is_cancel_all_projects' => $is_cancel_all_projects,
                    'is_cancel_subs_projects' => $is_cancel_subs_projects,
                    'is_cancel_my_projects' => $is_cancel_my_projects,
                    'is_cancel_mysubs_projects' => $is_cancel_mysubs_projects
                ]);

            } elseif ($access->is_subscription_request == false && $access->is_access_allowed == true) {
                // Доступ разрешен, далее запуск проекта
                return redirect()->route('project.start',
                    ['project' => $project->id, 'role' => $role->id]);

            } elseif ($access->is_subscription_request == true && $access->is_access_allowed == false) {
                // Запрос на подписку, далее страница отмены запроса на подписку
                return view('project/ask_subs_form', ['project' => $project, 'role' => $role,
                    'is_subs' => false, 'is_delete' => $is_delete,
                    'is_request' => true,
                    'is_cancel_all_projects' => $is_cancel_all_projects,
                    'is_cancel_subs_projects' => $is_cancel_subs_projects,
                    'is_cancel_my_projects' => $is_cancel_my_projects,
                    'is_cancel_mysubs_projects' => $is_cancel_mysubs_projects,
                    'additional_information' => $access->additional_information
                ]);

            } elseif ($access->is_subscription_request == true && $access->is_access_allowed == true) {
                // Такая комбинация недопустима, далее страница отмены подписки
                return view('project/ask_subs_form', ['project' => $project, 'role' => $role,
                    'is_subs' => false, 'is_delete' => $is_delete,
                    'is_request' => false,
                    'is_cancel_all_projects' => $is_cancel_all_projects,
                    'is_cancel_subs_projects' => $is_cancel_subs_projects,
                    'is_cancel_my_projects' => $is_cancel_my_projects,
                    'is_cancel_mysubs_projects' => $is_cancel_mysubs_projects
                ]);
            }
        } else {
            if ($open_default) {
                // Запуск проекта
                return redirect()->route('project.start',
                    ['project' => $project->id, 'role' => $role->id]);
            } else {
                // Запуск формы подписки
                return view('project/ask_subs_form', ['project' => $project, 'role' => $role,
                    'is_subs' => true, 'is_delete' => false,
                    'is_request' => $is_request,
                    'is_cancel_all_projects' => $is_cancel_all_projects,
                    'is_cancel_subs_projects' => $is_cancel_subs_projects,
                    'is_cancel_my_projects' => $is_cancel_my_projects,
                    'is_cancel_mysubs_projects' => $is_cancel_mysubs_projects
                ]);
            }
        }

    }

    static function subs_req_count(Project $project)
    {
        $result = '';
        // Запросы на подписку по текущему проекту
        $count = Access::where('project_id', $project->id)
            ->where('is_subscription_request', true)
            ->where('is_access_allowed', false)
            ->count();
        if ($count > 0) {
            $result = $count;
        } else {
            $result = trans('main.no');
        }
        return $result;
    }

    function index_template(Template $template)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }
        //$projects = Project::where('template_id', $template->id)->orderBy('user_id');
        $projects = Project::where('template_id', $template->id)->orderBy('account');
        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $projects = $projects->orderBy($name);
        }
        session(['projects_previous_url' => request()->url()]);
        return view('project/index', ['template' => $template, 'projects' => $projects->paginate(60)]);
    }

    function index_user(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            if (GlobalController::glo_user_id() != $user->id) {
                return redirect()->route('project.all_index');
            }
        }

//      Первоначальный вариант: выводить проекты, где автор равен текущему пользователю
//      $projects = Project::where('user_id', $user->id)->orderBy('account');
//
        // Проекты, у которых в accesses есть записи для текущего пользователя
        // с ролью Автор
        $projects = GlobalController::get_author_roles_projects();

        $name = "";  // нужно, не удалять
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            $name = 'name_lang_' . $index;
            $projects = $projects->orderBy($name);
        }
        session(['projects_previous_url' => request()->url()]);
        return view('project/index', ['user' => $user, 'projects' => $projects->paginate(60)]);
    }

    function show_template(Project $project)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($project->template_id);
        $child_relits_info = $this->child_relits_info($template, $project);
        return view('project/show', ['type_form' => 'show', 'template' => $template, 'project' => $project,
            'array_calc' => $child_relits_info['array_calc']]);
    }

    function show_user(Project $project)
    {
        $user = User::findOrFail($project->user_id);
//        Первоначальная проверка
//        if (!
//        Auth::user()->isAdmin()) {
//            if (GlobalController::glo_user_id() != $user->id) == false) {
//                return redirect()->route('project.all_index');
//            }
//        }
        if (!
        Auth::user()->isAdmin()) {
            if (!GlobalController::is_author_roles_project($project->id)) {
                return redirect()->route('project.all_index');
            }
        }

        $child_relits_info = $this->child_relits_info($project->template, $project);
        return view('project/show', ['type_form' => 'show', 'user' => $user, 'project' => $project,
            'array_calc' => $child_relits_info['array_calc']]);
    }

    function start(Project $project, Role $role = null)
    {
        // Если $role не передана, $role = null - идет поиск роли 'where('is_default_for_external', true)'
        if (!$role) {
            $role = Role::where('template_id', $project->template_id)->where('is_default_for_external', true)->first();
            if (!$role) {
                return view('message', ['message' => trans('main.role_default_for_external_not_found')]);
            }
        }

        if (GlobalController::check_project_user($project, $role) == false) {
            return view('message', ['message' => trans('main.info_user_changed')]);
        }

        if (Auth::check()) {
            $acc_check = self::acc_check($project, $role);
            if ($acc_check['is_access_allowed'] == false) {
                return view('message', ['message' => trans('main.project_access_denied') . '!']);
            }
        }
        $template = $project->template;
        // Порядок сортировки; обычные bases, вычисляемые bases, настройки - bases, серийный номер
        $bases = Base::where('template_id', $template->id)->orderBy('is_setup_lst')->orderBy('is_calculated_lst')
            ->orderBy('serial_number');
        $index = array_search(App::getLocale(), config('app.locales'));
        if ($index !== false) {   // '!==' использовать, '!=' не использовать
            switch ($index) {
                case 0:
                    //$bases = Base::all()->sortBy('name_lang_0');
                    $bases = $bases->orderBy('name_lang_0');
                    break;
                case 1:
                    //$bases = Base::all()->sortBy(function($row){return $row->name_lang_1 . $row->name_lang_0;});
                    $bases = $bases->orderBy('name_lang_1')->orderBy('name_lang_0');
                    break;
                case 2:
                    $bases = $bases->orderBy('name_lang_2')->orderBy('name_lang_0');
                    break;
                case 3:
                    $bases = $bases->orderBy('name_lang_3')->orderBy('name_lang_0');
                    break;
            }
        }
        $array_relips = GlobalController::get_project_bases($project, $role);

        session(['projects_previous_url' => request()->url()]);

        return view('project/start', ['array_relips' => $array_relips, 'project' => $project, 'role' => $role, 'bases' => $bases->paginate(60)]);

    }

    function create_template(Template $template)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $exists = Template::whereHas('roles', function ($query) {
            $query->where('is_author', true);
        })->where('id', $template->id)->exists();
        if ($exists) {
            $users = User::orderBy('name')->get();
            $child_relits_info = $this->child_relits_info($template);
            if ($child_relits_info['error_message'] != '') {
                return view('message', ['message' => $child_relits_info['error_message']]);
            } else {
                return view('project/edit', ['template' => $template, 'users' => $users, 'child_relits_info' => $child_relits_info]);
            }
        } else {
            return view('message', ['message' => trans('main.role_author_not_found')]);
        }
    }

    function create_user(User $user)
    {
        if (GlobalController::glo_user_id() != $user->id) {
            return redirect()->route('project.all_index');
        }

        $templates = Template::whereHas('roles', function ($query) {
            $query->where('is_author', true);
        })->get();
        if ($templates) {
            return view('project/edit', ['user' => $user, 'templates' => $templates]);
        } else {
            return view('message', ['message' => trans('main.role_author_not_found')]);
        }

    }

    function create_template_user(Template $template)
    {
        $user = GlobalController::glo_user();

        $exists = Template::whereHas('roles', function ($query) {
            $query->where('is_author', true);
        })->where('id', $template->id)->exists();
        if ($exists) {
            $child_relits_info = $this->child_relits_info($template);
            if ($child_relits_info['error_message'] != '') {
                return view('message', ['message' => $child_relits_info['error_message']]);
            } else {
                return view('project/edit', ['template' => $template, 'user' => $user, 'child_relits_info' => $child_relits_info]);
            }
        } else {
            return view('message', ['message' => trans('main.role_author_not_found')]);
        }
    }

    function store(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        if (GlobalController::glo_user_id() != $user->id) {
            return redirect()->route('project.all_index');
        }
        $request->validate($this->account_rules());
        $request->validate($this->name_lang_0_rules());

        $array_mess = [];
        $this->check($request, $array_mess);

        if (count($array_mess) > 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors($array_mess);
        }

        // установка часового пояса нужно для сохранения времени
        date_default_timezone_set('Asia/Almaty');

        $project = new Project($request->except('_token', '_method'));
        //$project->template_id = $request->template_id;

        $this->set($request, $project);

        // Создать запись в основе/таблице accesses
        $role = Role::where('template_id', $project->template_id)->where('is_author', true)->first();
        if ($role) {
            $access = new Access();
            $access->project_id = $project->id;
            $access->user_id = $project->user_id;
            $access->role_id = $role->id;
            // Запрос на подписку = false
            $access->is_subscription_request = false;
            // Доступ разрешен = true
            $access->is_access_allowed = true;
            $access->additional_information = '';
            $access->save();
        }

        //https://laravel.demiart.ru/laravel-sessions/
        if ($request->session()->has('projects_previous_url')) {
            return redirect(session('projects_previous_url'));
        } else {
            //return redirect()->back();
            return redirect()->route('project.my_index');
        }

    }

    function update(Request $request, Project $project)
    {
//        Первоначальный вариант
//        if (!Auth::user()->isAdmin()) {
//            $user = User::findOrFail($project->user_id);
//            if (GlobalController::glo_user_id() != $user->id) {
//                return redirect()->route('project.all_index');
//            }
//        }

        if (!
        Auth::user()->isAdmin()) {
            if (!GlobalController::is_author_roles_project($project->id)) {
                return redirect()->route('project.all_index');
            }
        }

        if ($project->account != $request->account) {
            $request->validate($this->account_rules());
        }
        if ($project->name_lang_0 != $request->name_lang_0) {
            $request->validate($this->name_lang_0_rules());
        }

        $array_mess = [];
        $this->check($request, $array_mess);

        if (count($array_mess) > 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors($array_mess);
        }

        $data = $request->except('_token', '_method');

        $project->fill($data);

        $this->set($request, $project);

        if ($request->session()->has('projects_previous_url')) {
            return redirect(session('projects_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function check(Request $request, &$array_mess)
    {
        $template = Template::findOrFail($request->template_id);
        // Без этой команды "$is_closed = isset($request->is_closed) ? true : false;"
        // эта строка неправильно сравнивает "if ($request->is_closed != $template->is_closed_default_value)"
        $is_closed = isset($request->is_closed) ? true : false;
        if ($template->is_closed_default_value_fixed == true) {
            if ($is_closed != $template->is_closed_default_value) {
                if ($template->is_closed_default_value == true) {
                    $array_mess['is_closed'] = trans('main.is_closed_true_rule') . '!';
                } else {
                    $array_mess['is_closed'] = trans('main.is_closed_false_rule') . '!';
                }
            }
        }

        foreach (config('app.locales') as $lang_key => $lang_value) {
            $text_html_check = GlobalController::text_html_check($request['dc_ext_lang_' . $lang_key]);
            if ($text_html_check['result'] == true) {
                $array_mess['dc_ext_lang_' . $lang_key] = $text_html_check['message'] . '!';
            }

            $text_html_check = GlobalController::text_html_check($request['dc_int_lang_' . $lang_key]);
            if ($text_html_check['result'] == true) {
                $array_mess['dc_int_lang_' . $lang_key] = $text_html_check['message'] . '!';
            }
        }
    }

    function set(Request $request, Project &$project)
    {
        try {
            // начало транзакции
            DB::transaction(function () use ($request, $project) {
                $project->template_id = $request->template_id;
                $project->user_id = $request->user_id;
                $project->account = $request->account;

                $project->name_lang_0 = $request->name_lang_0;
                $project->name_lang_1 = isset($request->name_lang_1) ? $request->name_lang_1 : "";
                $project->name_lang_2 = isset($request->name_lang_2) ? $request->name_lang_2 : "";
                $project->name_lang_3 = isset($request->name_lang_3) ? $request->name_lang_3 : "";

                $project->is_test = isset($request->is_test) ? true : false;
                $project->is_closed = isset($request->is_closed) ? true : false;

                $project->save();

                $array_relips = [];

                $child_relits = $project->template->child_relits;
                foreach ($child_relits as $key => $relit) {
                    // добавление или корректировка массива по ключу $relit_id
                    // заносится null, т.к. это план (настройка от таблицы relits)
                    $array_relips[$relit->id] = $request[$relit->id];
                }
                // Сначала проверка, потом присвоение
                // Проверка на $relip->relit_id, если такой не найден - то удаляется
                $relips = Relip::where('child_project_id', $project->id)->get();
                foreach ($relips as $relip) {
                    $delete_main = false;
                    $relit = Relit::where('id', $relip->relit_id)->first();
                    if ($relit) {
                        if ($relit->child_template_id != $project->template_id) {
                            $delete_main = true;
                        }
                    } else {
                        $delete_main = true;
                    }
                    if ($delete_main) {
                        $relip->delete();
                    }
                }

                foreach ($child_relits as $relit) {
                    $relip = Relip::where('child_project_id', $project->id)->where('relit_id', $relit->id)->first();
                    // Если в списке проектов выбрано null
                    // При условии (relit->parent_is_required == false)
                    // или просто нет проектов (например шаблон проекта ссылается на свой же шаблон и первый раз создается проект такого шаблона)
                    if ($request[$relit->id] == 0) {
                        if ($relip) {
                            $relip->delete();
                        }
                    } else {
                        if ($relip == null) {
                            $relip = new Relip();
                            $relip->relit_id = $relit->id;
                            $relip->child_project_id = $project->id;
                        }
                        // Заполняется введенный проект
                        $relip->parent_project_id = $request[$relit->id];
                        $relip->save();
                    }
                }
            }, 3);  // Повторить три раза, прежде чем признать неудачу
            // окончание транзакции

        } catch (Exception $exc) {
            return trans('transaction_not_completed') . ": " . $exc->getMessage();
        }
    }

    function edit_template(Project $project)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $template = Template::findOrFail($project->template_id);
        $users = User::orderBy('name')->get();
        $child_relits_info = $this->child_relits_info($template, $project);
        if ($child_relits_info['error_message'] != '') {
            return view('message', ['message' => $child_relits_info['error_message']]);
        } else {
            return view('project/edit',
                ['template' => $template, 'project' => $project, 'users' => $users, 'child_relits_info' => $child_relits_info]);
        }
    }

    function edit_user(Project $project)
    {
        $user = User::findOrFail($project->user_id);

//        Первоначальный вариант
//        if (!Auth::user()->isAdmin()) {
//            if (GlobalController::glo_user_id() != $user->id) {
//                return redirect()->route('project.all_index');
//            }
//        }

        if (!
        Auth::user()->isAdmin()) {
            if (!GlobalController::is_author_roles_project($project->id)) {
                return redirect()->route('project.all_index');
            }
        }

//        $templates = Template::get();
//        return view('project/edit', ['user' => $user, 'project' => $project, 'templates' => $templates]);
// Передаются $user, $project, $template
        if (isset($project)) {
            $template = $project->template;
            $child_relits_info = $this->child_relits_info($template, $project);
            return view('project/edit', ['user' => $user, 'project' => $project,
                'template' => $template, 'child_relits_info' => $child_relits_info]);
        }
    }

    function delete_question(Project $project)
    {
        $user = User::findOrFail($project->user_id);

//        Первоначальный вариант
//        if (!Auth::user()->isAdmin()) {
//            if (GlobalController::glo_user_id() != $user->id) {
//                return redirect()->route('project.all_index');
//            }
//        }

        if (!
        Auth::user()->isAdmin()) {
            if (!GlobalController::is_author_roles_project($project->id)) {
                return redirect()->route('project.all_index');
            }
        }

        $template = Template::findOrFail($project->template_id);
        $child_relits_info = $this->child_relits_info($template, $project);
        return view('project/show', ['type_form' => 'delete_question', 'template' => $template, 'project' => $project,
            'array_calc' => $child_relits_info['array_calc']]);
    }

    function delete(Request $request, Project $project)
    {
        $user = User::findOrFail($project->user_id);

//        Первоначальный вариант
//        if (!Auth::user()->isAdmin()) {
//            if (GlobalController::glo_user_id() != $user->id) {
//                return redirect()->route('project.all_index');
//            }
//        }

        if (!
        Auth::user()->isAdmin()) {
            if (!GlobalController::is_author_roles_project($project->id)) {
                return redirect()->route('project.all_index');
            }
        }

        $project->delete();

        if ($request->session()->has('projects_previous_url')) {
            return redirect(session('projects_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function calculate_bases_start(Project $project, Role $role)
    {
        if (!(($project->template_id == $role->template_id) && ($role->is_author()))) {
            return;
        }
        return view('project/calculate_bases_start', ['project' => $project, 'role' => $role]);
    }

    function calculate_bases(Project $project, Role $role)
    {
        if (!(($project->template_id == $role->template_id) && ($role->is_author()))) {
            return;
        }

        echo nl2br(trans('main.calculation') . ": " . PHP_EOL);

        try {
            // начало транзакции
            DB::transaction(function ($r) use ($project) {
                // Запрос для определения bases, которые нужно удалить
                // Нужно "->where('sets.is_savesets_enabled', '=', true)"
                $bases_to = Set::select(DB::Raw('links.child_base_id as base_id'))
                    ->join('links', 'sets.link_to_id', '=', 'links.id')
                    ->join('bases', 'links.child_base_id', '=', 'bases.id')
                    ->where('bases.template_id', $project->template_id)
                    ->where('sets.is_savesets_enabled', '=', true)
                    ->distinct()
                    ->orderBy('links.child_base_id')
                    ->get();

//                $bases_from = Set::select(DB::Raw('links.child_base_id as base_id'))
//                    ->join('links', 'sets.link_from_id', '=', 'links.id')
//                    ->join('bases', 'links.child_base_id', '=', 'bases.id')
//                    ->where('bases.template_id', $project->template_id)
//                    ->distinct()
//                    ->orderBy('links.child_base_id')
//                    ->get();

                // Нужно "->where('sets.is_savesets_enabled', '=', true)"
                // Это условие 'where('bf.is_calculated_lst', '=', false)->where('bt.is_calculated_lst', '=', true)' означает
                // исключить sets, когда link_from->child_base и link_to->child_base являются вычисляемыми (base->is_calculated_lst=true)
//                $bases_from = Set::select(DB::Raw('lf.child_base_id as base_id'))
//                    ->join('links as lf', 'sets.link_from_id', '=', 'lf.id')
//                    ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
//                    ->join('bases as bf', 'lf.child_base_id', '=', 'bf.id')
//                    ->join('bases as bt', 'lt.child_base_id', '=', 'bt.id')
//                    ->where('bf.template_id', $project->template_id)
//                    ->where('sets.is_savesets_enabled', '=', true)
//                    ->where('bf.is_calculated_lst', '=', false)
//                    ->where('bt.is_calculated_lst', '=', true)
//                    ->distinct()
//                    ->orderBy('lf.child_base_id')
//                    ->get();
//                $bases_from = Set::select(DB::Raw('lf.child_base_id as base_id'))
//                    ->join('links as lf', 'sets.link_from_id', '=', 'lf.id')
//                    ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
//                    ->join('bases as bf', 'lf.child_base_id', '=', 'bf.id')
//                    ->join('bases as bt', 'lt.child_base_id', '=', 'bt.id')
//                    ->where('bf.template_id', $project->template_id)
//                    ->where('sets.is_savesets_enabled', '=', true)
//                    ->where('bf.is_calculated_lst', '=', false)
//                    ->where('bt.is_calculated_lst', '=', true)
//                    ->where('sets.is_calcsort', '=', false)
//                    ->distinct()
//                    ->orderBy('lf.child_base_id')
//                    ->get();
                // Запросы $bases_from и $bases_relit_from похожи
                // Запрос по текущему проекту
                $bases_from = Set::select(DB::Raw('lf.child_base_id as base_id'))
                    ->join('links as lf', 'sets.link_from_id', '=', 'lf.id')
                    ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
                    ->join('bases as bf', 'lf.child_base_id', '=', 'bf.id')
                    ->join('bases as bt', 'lt.child_base_id', '=', 'bt.id')
                    ->where('bf.template_id', $project->template_id)
                    ->where('sets.is_savesets_enabled', '=', true)
                    ->where('bf.is_calculated_lst', '=', false)
                    ->where('bt.is_calculated_lst', '=', true)
                    ->where('sets.is_calcsort', '=', false)
                    ->distinct()
                    ->orderBy('lf.child_base_id')
                    ->get();

                // Запрос по проектам - Дети по отношению к текущему проекту/шаблону
                // '->orderBy('lf.child_base_id, relits.id')' - дает ошибку
                $bases_relit_from = Set::select(DB::Raw('lf.child_base_id as base_id, relits.id as relit_id'))
                    ->join('relits', 'sets.relit_to_id', '=', 'relits.id')
                    ->join('links as lf', 'sets.link_from_id', '=', 'lf.id')
                    ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
                    ->join('bases as bf', 'lf.child_base_id', '=', 'bf.id')
                    ->join('bases as bt', 'lt.child_base_id', '=', 'bt.id')
                    ->where('sets.is_savesets_enabled', '=', true)
                    ->where('bf.is_calculated_lst', '=', false)
                    ->where('bt.is_calculated_lst', '=', true)
                    ->where('sets.is_calcsort', '=', false)
                    ->distinct()
                    ->orderBy('lf.child_base_id')
                    ->get();

                $str_records = mb_strtolower(trans('main.records'));

                // Удаление записей
                foreach ($bases_to as $base_to_id) {
                    $base = Base::findOrFail($base_to_id['base_id']);
                    echo nl2br(trans('main.base') . ": " . $base->name() . " - ");
                    $items = Item::where('project_id', $project->id)->where('base_id', $base->id);
                    $count = $items->count();
                    $items->delete();
                    echo nl2br(trans('main.deleted') . " " . $count . " " . $str_records . PHP_EOL);
                }

                // Обработка записей текущего проекта
                foreach ($bases_from as $base_from_id) {
                    $base = Base::findOrFail($base_from_id['base_id']);
                    echo nl2br(trans('main.base') . ": " . $base->name() . " - ");
                    $items = Item::where('project_id', $project->id)->where('base_id', $base->id)->get();
                    $count = $items->count();
                    foreach ($items as $item) {
                        //Log::info($item->id . ' - ' . $item->name());
                        //echo nl2br(trans('main.processed') . " id = " . $item->id . " " . $item->name() . " ".$item->id. PHP_EOL);
                        // $reverse = true - отнимать, false - прибавлять
                        (new ItemController)->save_info_sets($item, false);
                    }
                    echo nl2br(trans('main.processed') . " " . $count . " " . $str_records . PHP_EOL);
                }

                // Обработка записей проектов - Дети
                foreach ($bases_relit_from as $value) {
                    $base = Base::findOrFail($value['base_id']);
                    echo nl2br(trans('main.base') . ": " . $base->name() . PHP_EOL);
                    $relit = Relit::findOrFail($value['relit_id']);
                    // Поиск $child_project
                    $child_id_projects = GlobalController::calc_relit_children_id_projects($relit, $project);
                    foreach ($child_id_projects as $project_id) {
                        $child_project = Project::findOrFail($project_id['project_id']);
                        echo nl2br('->' . trans('main.child') . '_' . trans('main.template') . ": " . $relit->child_template->name() . ", "
                            . trans('main.project') . ": " . $child_project->name()
                            . " - ");

                        // Используется $child_project
                        $items = Item::where('project_id', $child_project->id)->where('base_id', $base->id)->get();
                        $count = $items->count();
                        foreach ($items as $item) {
                            //Log::info($item->id . ' - ' . $item->name());
                            //echo nl2br(trans('main.processed') . " id = " . $item->id . " " . $item->name() . " ".$item->id. PHP_EOL);
                            // $reverse = true - отнимать, false - прибавлять
                            (new ItemController)->save_info_sets($item, false);
                        }
                        echo nl2br(trans('main.processed') . " " . $count . " " . $str_records . PHP_EOL);
                    }
                }

            }, 3);  // Повторить три раза, прежде чем признать неудачу
            // окончание транзакции

        } catch (Exception $exc) {
            return trans('transaction_not_completed') . ": " . $exc->getMessage();
        }

        echo '<p class="text-center">
            <a href=' . '"' . route('project.start', ['project' => $project->id, 'role' => $role]) . '" title="' . trans('main.bases') . '">' . $project->name()
            . '</a>
        </p>';

//        $set_main = Set::select(DB::Raw('sets.*, lt.child_base_id as to_child_base_id, lt.parent_base_id as to_parent_base_id'))
//            ->join('links as lt', 'sets.link_to_id', '=', 'lt.id')
//            ->where('lf.child_base_id', '=', $item->base_id)
//            ->orderBy('sets.serial_number')
//            ->orderBy('sets.link_from_id')
//            ->orderBy('sets.link_to_id')->get();

        //$items = Item::joinSub($sets, 'sets', function ($join) {
        //        $join->on('items.base_id', '=', 'sets.base_id');})->get();


//        $users = DB::table('items')
//            ->joinSub($bases, 'bases', function ($join) {
//                $join->on('items.id', 1);
//            })->get();

        //dd($items);

    }

    private
    function get_array_calc(Template $template, Project $project = null)
    {
        $plan_child_relits = $template->child_relits;
        $create = $project == null;
        if (!$create) {
            // по факту в таблице relips
            $fact_child_relips = Relip::where('child_project_id', $project->id)->get();
        }
        $array_plan = array();
        foreach ($plan_child_relits as $key => $relit) {
            // добавление или корректировка массива по ключу $relit_id
            // заносится null, т.к. это план (настройка от таблицы relits)
            $array_plan[$relit->id] = null;
        }

        // если relip->relit_id одинаковый для записей, то берется одно значение(последнее по списку)
        $array_fact = array();
        if (!$create) {
            foreach ($fact_child_relips as $key => $relip) {
                // добавление или корректировка массива по ключу $relit_id
                // заносится $relip->parent_project_id (используется в форме extended.edit)
                $array_fact[$relip->relit_id] = $relip->parent_project_id;
            }
        }
// объединяем два массива, главный $array_plan
// он содержит количество записей, как настроено в relits
// индекс массива = relits->id
// значение массива = null (при создании нового project или если в relips нет записи с таким relits->id)
        foreach ($array_plan as $key => $value) {
            if (array_key_exists($key, $array_fact)) {
                $array_plan[$key] = $array_fact[$key];
            }
        }
        return $array_plan;
    }

    function child_relits_info(Template $template, Project $project = null)
    {
        $is_child_relits = false;
        $error_message = '';
        $array_projects = [];
        $array_calc = $this->get_array_calc($template, $project);
        // '$child_relits =$template->child_relits();' так не использовать
        $child_relits = $template->child_relits;
        if (count($child_relits) > 0) {
            $is_child_relits = true;
        }
        if ($is_child_relits) {
            foreach ($child_relits as $relit) {
                $parent_template = $relit->parent_template;
                // Есть ли проекты с таким шаблоном
                $projects = Project::where('template_id', $parent_template->id)->orderBy('account')->get();
                if (count($projects) > 0) {
                    $array_projects[$parent_template->id] = $projects;
                } else {
                    if ($relit->parent_is_required == true) {
                        $error_message = '"' . $parent_template->name() . '" - ' . trans('main.no_projects_found_with_this_template') . '!';
                        break;
                    }
                }
            }
        }
        return ['is_child_relits' => $is_child_relits, 'error_message' => $error_message, 'child_relits' => $child_relits,
            'array_calc' => $array_calc, 'array_projects' => $array_projects];
    }

}
