<?php

namespace App\Observers;

use App\Models\Ytd;
use Illuminate\Support\Facades\DB;

class YtdObserver
{
    /**
     * Handle the Ytd "created" event.
     *
     * @param  \App\Models\Ytd  $ytd
     * @return void
     */
    public function created(Ytd $ytd)
    {
        //
    }

    /**
     * Handle the Ytd "updated" event.
     *
     * @param  \App\Models\Ytd  $ytd
     * @return void
     */
    public function updated(Ytd $ytd)
    {
        //
    }

    /**
     * Handle the Ytd "deleted" event.
     *
     * @param  \App\Models\Ytd  $ytd
     * @return void
     */
    public function deleted(Ytd $ytd)
    {
        //
    }

    /**
     * Handle the Ytd "restored" event.
     *
     * @param  \App\Models\Ytd  $ytd
     * @return void
     */
    public function restored(Ytd $ytd)
    {
        //
    }

    /**
     * Handle the Ytd "force deleted" event.
     *
     * @param  \App\Models\Ytd  $ytd
     * @return void
     */
    public function forceDeleted(Ytd $ytd)
    {
        //
    }
}
