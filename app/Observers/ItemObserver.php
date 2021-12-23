<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\Main;
use App\Models\Text;
use Illuminate\Support\Facades\Storage;

class ItemObserver
{
    /**
     * Handle the item "created" event.
     *
     * @param \App\Item $item
     * @return void
     */
    //public function created(Item $item)
    public function created($item)
    {
        //
    }

    /**
     * Handle the item "updated" event.
     *
     * @param \App\Item $item
     * @return void
     */
//    public function updated($item)
//    {
//        //
//    }

//    public function updated($item)
//    {
//        //
//        if ($item->base->is_code_needed == false) {
//            $item->code = uniqid($item->base_id . '_1111___', true);
//        }
//    }
//
//    public function updating($item)
//    {
//        //
//        if ($item->base->is_code_needed == false) {
//            $item->code = uniqid($item->base_id . '_2222___', true);
//        }
//    }

    /**
     * Handle the item "deleted" event.
     *
     * @param \App\Item $item
     * @return void
     */
    public function deleting($item)
    {
        // Если тип - текст, удаление записей в связанной таблице
        if ($item->base->type_is_text()) {
            $texts = Text::where('item_id', $item->id)->get();
            if ($texts) {
                foreach ($texts as $text) {
                    $text->delete();
                }
            }
        } // Если тип - изображение или документ, предварительное удаление файлов с диска
        elseif ($item->base->type_is_image() || $item->base->type_is_document()) {
            Storage::delete($item->filename());
        }

        $mains = Main::where('child_item_id', $item->id)->get();
        foreach ($mains as $main) {
            // Эта проверка нужна, если неправильно заполнены Присваивания (sets)
            //if (isset($main->parent_item->base_id)){
            // Если тип - текст, изображение или документ
            if ($main->parent_item->base->type_is_text() || $main->parent_item->base->type_is_image() || $main->parent_item->base->type_is_document()) {
                //Storage::delete($main->parent_item->filename());
                //нужно удалять, "$text->delete()"(для текста) и "Storage::delete()"(изображения и документы) выполнится, т.к. это рекурсивный вызов этой же функции "public function deleting($item)"
                // item- тексты, изображения и документы создаются на каждую связь(Например спр-к товаров) - поэтому их нужно удалять
                $main->parent_item->delete();
            }
            //}
        }
    }

    /**
     * Handle the item "restored" event.
     *
     * @param \App\Item $item
     * @return void
     */
    public function restored($item)
    {
        //
    }

    /**
     * Handle the item "force deleted" event.
     *
     * @param \App\Item $item
     * @return void
     */
    public function forceDeleted($item)
    {
        //
    }
}
