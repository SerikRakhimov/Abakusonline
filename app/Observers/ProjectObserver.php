<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\Project;
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
            if ($item->base->type_is_image() || $item->base->type_is_document()) {
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
