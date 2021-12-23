<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Models\Base;
use App\Models\Item;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ModerationController extends Controller
{

    function index()
    {
        if (!
        Auth::user()->isModerator()) {
            return redirect()->route('project.all_index');
        }
        $items = Item::select(DB::Raw('items.*'))
            ->join('bases', 'items.base_id', '=', 'bases.id')
            ->where('bases.type_is_image', true)
            ->where('bases.is_to_moderate_image', true)
            ->orderBy('items.name_lang_1', 'desc')
            ->orderBy('items.created_at', 'desc');

        session(['moderations_previous_url' => request()->url()]);
        return view('moderation/index', ['items' => $items->paginate(60)]);
    }

    function show(Item $item)
    {
        if (!
        Auth::user()->isModerator()) {
            return redirect()->route('project.all_index');
        }
        return view('moderation/show', ['type_form' => 'show', 'item' => $item]);
    }

    function update(Request $request, Item $item)
    {
        if (!
        Auth::user()->isModerator()) {
            return redirect()->route('project.all_index');
        }

        $data = $request->except('_token', '_method');
        date_default_timezone_set('Asia/Almaty');
        $item->fill($data);

        $this->set($request, $item);

        if ($request->session()->has('moderations_previous_url')) {
            return redirect(session('moderations_previous_url'));
        } else {
            return redirect()->back();
        }
    }

    function set(Request $request, Item &$item)
    {
        if ($request->hasFile('name_lang_0')) {
            Storage::delete($item->filename(true));
        }
        $path = "";
        if ($request->hasFile('name_lang_0')) {
            $path = $request->name_lang_0->store('public/' . $item->project_id . '/' . $item->base->id);
            $item->name_lang_0 = $path;
        }
        $item->name_lang_1 = $request->name_lang_1;
        $item->name_lang_2 = isset($request->name_lang_2) ? $request->name_lang_2 : "";
        // Кроме 2 - не прошло модерацию
        if ($item->name_lang_1 != 2) {
            $item->name_lang_2 = "";
        }
        $item->name_lang_3 = "";

        $item->save();
    }

    function edit(Item $item)
    {
        if (!
        Auth::user()->isModerator()) {
            return redirect()->route('project.all_index');
        }
        return view('moderation/edit', ['item' => $item, 'statuses' => Item::get_img_statuses()]);
    }

    function delete_question(Item $item)
    {
        if (!
        Auth::user()->isModerator()) {
            return redirect()->route('project.all_index');
        }
        return view('moderation/show', ['type_form' => 'delete_question', 'item' => $item]);
    }

    function delete(Request $request, Item $item)
    {
        if (!
        Auth::user()->isModerator()) {
            return redirect()->route('project.all_index');
        };
        $item->delete();

        if ($request->session()->has('moderations_previous_url')) {
            return redirect(session('moderations_previous_url'));
        } else {
            return redirect()->back();
        }
    }

}
