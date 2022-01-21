<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\Project;
use App\Models\Text;
use Illuminate\Support\Facades\Storage;

class ProjectObserver
{
    /**
     * Handle the project "created" event.
     *
     * @param \App\Project $project
     * @return void
     */
    public function created($project)
    {
        //
    }

    /**
     * Handle the project "updated" event.
     *
     * @param \App\Project $project
     * @return void
     */
    public function updated($project)
    {
        //
    }

    /**
     * Handle the project "deleted" event.
     *
     * @param \App\Project $project
     * @return void
     */
    public function deleting($project)
    {
        // предварительное удаление файлов с диска
        // эти записи items потом удалятся автоматически, т.к. связаны с projects
        $items = Item::where('project_id', $project->id)->get();
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
     * Handle the project "restored" event.
     *
     * @param \App\Project $project
     * @return void
     */
    public function restored($project)
    {
        //
    }

    /**
     * Handle the project "force deleted" event.
     *
     * @param \App\Project $project
     * @return void
     */
    public function forceDeleted($project)
    {
        //
    }
}
