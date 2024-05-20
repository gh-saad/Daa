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
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'vender_id');
    }
    public function tax()
    {   if(module_is_active('ProductService')){

            return $this->hasOne(\Modules\ProductService\Entities\Tax::class, 'id', 'tax_id');
        }
    }

    public static function purchaseNumberFormat($number,$company_id = null,$workspace = null)
    {
        if(!empty($company_id) && empty($workspace))
        {
            $data = !empty(company_setting('purchase_prefix',$company_id)) ? company_setting('purchase_prefix',$company_id) : '#POS000';
        }
        elseif(!empty($company_id) && !empty($workspace))
        {
            $data = !empty(company_setting('purchase_prefix',$company_id,$workspace)) ? company_setting('purchase_prefix',$company_id,$workspace) : '#POS000';
        }
        else
        {
            $data = !empty(company_setting('purchase_prefix')) ? company_setting('purchase_prefix') : '#POS000';
        }

        return $data. sprintf("%05d", $number);
    }

}
