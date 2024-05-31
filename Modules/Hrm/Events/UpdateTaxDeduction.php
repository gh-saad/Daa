<?php

namespace Modules\Hrm\Events;

use Illuminate\Queue\SerializesModels;

class UpdateTaxDeduction
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

     public $request;
     public $taxdeduction;
    public function __construct($request, $taxdeduction)
    {
        $this->request = $request;
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
