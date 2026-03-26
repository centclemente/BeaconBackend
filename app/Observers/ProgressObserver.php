<?php

namespace App\Observers;

use App\Models\Progress;
use Illuminate\Support\Facades\Auth;

class ProgressObserver
{
    /**
     * Handle the Progress "updating" event.
     * Set updated_by before the record is updated
     *
     * @param  \App\Models\Progress  $progress
     * @return void
     */
    public function updating(Progress $progress)
    {
        // Only set updated_by if the record is being modified (not on initial creation)
        if ($progress->exists) {
            $progress->updated_by = Auth::id();
        }
    }
}
