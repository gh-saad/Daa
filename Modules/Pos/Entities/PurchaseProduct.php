<?php

namespace Modules\Pos\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_type',
        'product_id',
        'purchase_id',
        'quantity',
        'currency',
        'tax',
        'discount',
        'price',
        'description',
        'workspace',
    ];

    public function product()
    {
        if(module_is_active('ProductService'))
        {
            return $this->hasOne(\Modules\ProductService\Entities\ProductService::class, 'id', 'product_id')->first();
        }
    }
    protected static function newFactory()
    {
        return \Modules\Pos\Database\factories\PurchaseProductFactory::new();
    }

    // added by saad

    public function purchase() {
        return $this->hasOne(\Modules\Pos\Entities\Purchase::class, 'id', 'purchase_id')->first();
    }

    // get price after discount
    public function getPriceAfterDiscount(){
        $price = $this->price;
        $discount = $this->discount;
        $priceAfterDiscount = $price - $discount;
        return $priceAfterDiscount;
    }
}
