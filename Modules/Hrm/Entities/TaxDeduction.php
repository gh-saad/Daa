<?php

namespace Modules\Hrm\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaxDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'title',
        'salary_amount',
        'difference',
        'tax_deduction_value_type',
        'tax_deduction_value',
        'tax_deduction_calculated',
        'workspace',
        'created_by',
    ];
    
    public static $tax_deduction_value_type = [
        'fixed'=>'Fixed',
        'percentage'=> 'Percentage',
    ];

    protected static function newFactory()
    {
        return \Modules\Hrm\Database\factories\TaxDeductionFactory::new();
    }
}
