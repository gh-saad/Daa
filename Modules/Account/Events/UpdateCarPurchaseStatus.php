<?php

namespace Modules\Account\Events;

use Illuminate\Queue\SerializesModels;
use Modules\ProductService\Entities\ProductService;

class UpdateCarPurchaseStatus
{
    use SerializesModels;

    public $vehicle;
    public $buyer;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ProductService $vehicle, $buyer)
    {
        $this->vehicle = $vehicle;
        $this->buyer = $buyer;
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
