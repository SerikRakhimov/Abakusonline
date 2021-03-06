<?php

namespace App\Observers;

use App\Models\Base;
use App\Models\Item;
use App\Models\Text;
use Illuminate\Support\Facades\Storage;

class BaseObserver
{
    /**
     * Handle the base "created" event.
     *
     * @param  \App\Base  $base
     * @return void
     */
    public function created($base)
    {
        //
    }

    /**
     * Handle the base "updated" event.
     *
     * @param  \App\Base  $base
     * @return void
     */
    public function updated($base)
    {
        //
    }

    /**
     * Handle the base "deleted" event.
     *
     * @param  \App\Base  $base
     * @return void
     */
    public function deleting($base)
    {
        // предварительное удаление файлов с диска
        // эти записи items потом удалятся автоматически, т.к. связаны с bases
        $items = Item::where('base_id', $base->id)->get();
        foreach ($items as $item) {
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
        }
    }

    /**
     * Handle the base "restored" event.
     *
     * @param  \App\Base  $base
     * @return void
     */
    public function restored($base)
    {
        //
    }

    /**
     * Handle the base "force deleted" event.
     *
     * @param  \App\Base  $base
     * @return void
     */
    public function forceDeleted($base)
    {
        //
    }
}
