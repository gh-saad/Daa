<?php

namespace Modules\Hrm\Events;

use Illuminate\Queue\SerializesModels;

class DestroyTaxDeduction
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $taxdeduction;
    public function __construct($taxdeduction)
    {
        $this->taxdeduction = $taxdeduction;
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
