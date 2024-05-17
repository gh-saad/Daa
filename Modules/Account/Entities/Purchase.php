<?php

namespace Modules\Account\Entities;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'vender_id',
        'warehouse_id',
        'purchase_date',
        'purchase_number',
        'discount_apply',
        'category_id',
        'workspace',
        'created_by',
    ];
    
    public static $statues = [
        'Draft',
        'Sent',
        'Unpaid',
        'Partialy Paid',
        'Paid',
    ];

    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\PurchaseFactory::new();
    }

    public function vender()
    {
        return $this->hasOne(\Modules\Account\Entities\Vender::class, 'user_id', 'vender_id');
    }
}
