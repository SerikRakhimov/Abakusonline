<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Link;
use App\Models\Main;
use App\Models\Project;
use App\Models\Template;
use App\Models\Set;
use App\Models\Relit;
use App\Models\Relip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class MainController extends Controller
{
    protected function rules()
    {
        return [
            'link_id' => 'exists:links,id',
            'child_item_id' => 'exists:items,id',
            'parent_item_id' => 'exists:items,id',
        ];

    }

    function index()
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $mains = Main::orderBy('link_id')->orderBy('child_item_id')->orderBy('parent_item_id');
        return view('main/index', ['mains' => $mains->paginate(60)]);

    }

    function index_item(Item $item)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

//        $child_mains = Main::all()->where('child_item_id', $item->id)->sortBy(function ($main) {
//            return $main->link->parent_base->name() . $main->parent_item->name();
//        });

        $child_mains = Main::where('child_item_id', $item->id)->sortBy(function ($main) {
            return $main->link->parent_base->name() . $main->parent_item->name();
        })->get();


        $parent_mains = Main::where('parent_item_id', $item->id)->sortBy(function ($main) {
            return $main->link->child_base->name() . $main->child_item->name();
        })->get();

        return view('main/index_item',
            ['item' => $item, 'child_mains' => $child_mains, 'parent_mains' => $parent_mains]);

    }

    function index_full(Item $item, Link $link)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

//        $item = $main->parent_item;
//        $link_head = $main->link;
        $link_head = $link;
        // исключим $link_head->id, он будет выводится в "заголовке"/"шапке" страницы
        $links = $link_head->child_base->child_links->where('id', '!=', $link_head->id);

        $mains = Main::all()->where('parent_item_id', $item->id)->where('link_id', $link_head->id)->sortBy(function ($main) {
            return $main->link->child_base->name() . $main->child_item->name();
        });

        return view('main/index_full',
            ['item' => $item, 'link_head' => $link_head, 'links' => $links, 'mains' => $mains]);
    }

    function store_full(Request $request)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $item = $request['item'];
        $link = $request['link'];

        return redirect()->route('main.index_full', ['item' => $item, 'link' => $link]);
    }

    function show(Main $main)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        return view('main/show', ['type_form' => 'show', 'main' => $main]);
    }

    function create()
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        // исключая вычисляемые поля
        return view('main/edit', ['links' => Link::all()->where('parent_is_parent_related', false)]);
    }

    function store(Request $request)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $request->validate($this->rules());

        // установка часового пояса нужно для сохранения времени
        date_default_timezone_set('Asia/Almaty');

        $main = new Main($request->except('_token', '_method'));

        $main->link_id = $request->link_id;
        $main->child_item_id = $request->child_item_id;
        $main->parent_item_id = $request->parent_item_id;

        $message_child_base_id = '';
        if (!($main->link->child_base_id == $main->child_item->base_id)) {
            $message_child_base_id = trans('main.base') . ' != ' . $main->link->child_base->name_lang_1;
        }

        $message_parent_base_id = '';
        if (!($main->link->parent_base_id == $main->parent_item->base_id)) {
            $message_parent_base_id = trans('main.base') . ' != ' . $main->link->parent_base->name_lang_1;
        }

        if (($message_child_base_id != '') || ($message_parent_base_id != '')) {
            // повторный вызов формы
            return redirect()->back()
                ->withInput()
                ->withErrors(['message_child_base_id' => $message_child_base_id, 'message_parent_base_id' => $message_parent_base_id]);
        }

        $main->save();

        return redirect()->route('main.index');
    }

    function update(Request $request, Main $main)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        // Если данные изменились - выполнить проверку
        if (!(($main->link_id == $request->link_id)
            and ($main->child_item_id == $request->child_item_id)
            and ($main->parent_item_id == $request->parent_item_id))) {
            $request->validate($this->rules());
        }

        $data = $request->except('_token', '_method');

        $main->fill($data);

        $main->link_id = $request->link_id;
        $main->child_item_id = $request->child_item_id;
        $main->parent_item_id = $request->parent_item_id;

        $message_child_base_id = '';
        if (!($main->link->child_base_id == $main->child_item->base_id)) {
            $message_child_base_id = trans('main.base') . ' != ' . $main->link->child_base->name_lang_1;
        }

        $message_parent_base_id = '';
        if (!($main->link->parent_base_id == $main->parent_item->base_id)) {
            $message_parent_base_id = trans('main.base') . ' != ' . $main->link->parent_base->name_lang_1;
        }

        if (($message_child_base_id != '') || ($message_parent_base_id != '')) {
            // повторный вызов формы
            return redirect()->back()
                ->withInput()
                ->withErrors(['message_child_base_id' => $message_child_base_id, 'message_parent_base_id' => $message_parent_base_id]);
        }

        $main->save();

        return redirect()->route('main.index');
    }

    function edit(Main $main)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        // исключая вычисляемые поля
        return view('main/edit', ['main' => $main, 'links' => Link::all()->where('parent_is_parent_related', false)]);
    }

    function delete_question(Main $main)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        return view('main/show', ['type_form' => 'delete_question', 'main' => $main]);
    }

    function delete(Main $main)
    {
        if (!
        Auth::user()->isAdmin()) {
            return redirect()->route('project.all_index');
        }

        $main->delete();
        return redirect()->route('main.index');
    }

}
