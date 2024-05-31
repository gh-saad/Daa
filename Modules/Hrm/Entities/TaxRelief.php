<?php

namespace Modules\Hrm\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaxRelief extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'title',
        'tax_relief_value_type',
        'tax_relief_value',
        'workspace',
        'created_by',
    ];
    
    public static $tax_relief_value_type = [
        'fixed'=>'Fixed',
        'percentage'=> 'Percentage',
    ];

    protected static function newFactory()
    {
        return \Modules\Hrm\Database\factories\TaxReliefFactory::new();
    }
}
