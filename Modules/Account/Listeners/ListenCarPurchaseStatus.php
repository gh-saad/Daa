<?php

namespace Modules\Account\Listeners;

use Modules\Account\Events\UpdateCarPurchaseStatus;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ListenCarPurchaseStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param UpdateCarPurchaseStatus $event
     * @return void
     */
    public function handle(UpdateCarPurchaseStatus $event)
    {
        // update accounts table
    }
}
