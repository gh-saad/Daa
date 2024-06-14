<?php

namespace Modules\Hrm\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalaryModificationTemplate extends Model
{
    use HasFactory;

    protected $table = 'salary_modification_template';

    protected $fillable = [
        'name',
        'allowance',
        'commission',
        'loan',
        'saturation_deduction',
        'tax_deduction',
        'tax_relief',
        'other_payment',
        'overtime',
        'workspace',
        'created_by'
    ];

    protected $casts = [
        'allowance' => 'array',
        'commission' => 'array',
        'loan' => 'array',
        'saturation_deduction' => 'array',
        'tax_deduction' => 'array',
        'tax_relief' => 'array',
        'other_payment' => 'array',
        'overtime' => 'array'
    ];

    protected static function newFactory()
    {
        return \Modules\Hrm\Database\factories\SalaryModificationTemplateFactory::new();
    }
}
