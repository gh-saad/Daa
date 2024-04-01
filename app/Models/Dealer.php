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
        'registration_no',
        'company_name',
        'logo',
        'relational_manager',
        'website',
        'company_whatsapp',
        'GM_whatsapp',
        'marketing_director_no',
        'trade_license',
        'trno_expiry',
        'agency_license_number',
        'trno_issue_place',
        'po_box',
        'trn_certificate',
        'rera_certificate',
        'passport',
        'emirates_id',
        'rara_card',
        'brokage_agreement',
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

    protected $dates = ['trno_expiry', 'deleted_at'];

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
