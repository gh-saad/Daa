<?php

namespace Modules\Hrm\Events;

use Illuminate\Queue\SerializesModels;

class CreateTaxRelief
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

     public $request;
     public $taxrelief;

    public function __construct($request, $taxrelief)
    {
        $this->request = $request;
        $this->taxrelief = $taxrelief;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
