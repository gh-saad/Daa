<?php

namespace Modules\Hrm\Events;

use Illuminate\Queue\SerializesModels;

class DestroyTaxRelief
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $taxrelief;
    public function __construct($taxrelief)
    {
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
