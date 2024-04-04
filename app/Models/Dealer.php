<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dealer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'logo',
        'relational_manager',
        'website',
        'company_whatsapp',
        'GM_whatsapp',
        'marketing_director_no',
        'dealer_document',
        'passport_copy',
        'trade_license',
        'emirates_document',
        'tax_document',
        'security_cheque_copy',
        'po_box',
        'is_agreement_signed',
        'bank_name',
        'ac_name',
        'branch_name',
        'branch_address',
        'currency',
        'swift_code',
        'iban',
        'created_by',
        'status',
        'is_submitted',
        'reason'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the user that owns the dealer.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the manager associated with the dealer.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'relational_manager');
    }
}
