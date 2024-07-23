<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_account',
        'to_account',
        'amount',
        'date',
        'payment_method',
        'reference',
        'description',
        'workspace',
        'created_by',
    ];

    protected $table = 'bank_transfers';
    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\TransferFactory::new();
    }
    public function fromBankAccount()
    {
        return  $this->hasOne(BankAccount::class,'id','from_account')->first();
    }

    public function toBankAccount()
    {
        return $this->hasOne(BankAccount::class,'id','to_account')->first();
    }

    public function getRate($currency){
        // locate currency by code
        $currency = Currency::where('code', $currency)->first();
        // get and return rate
        return $currency->rate;
    }

    public static function bankAccountBalance($id, $amount, $currency = null, $type)
    {
        if ($currency === null) {
            $currency = company_setting('defult_currancy');
        }

        $provided_currency = Currency::where('code', $currency)->first();
        $provided_currency_rate = $provided_currency->rate;

        $default_currency = Currency::where('code', company_setting('defult_currancy'))->first();
        $default_currency_rate = $default_currency->rate;

        $new_amount = ($amount / $default_currency_rate) * $provided_currency_rate;
    
        $bankAccount = BankAccount::find($id);
        if ($bankAccount)
        {
            if ($type == 'credit')
            {
                $oldBalance = $bankAccount->opening_balance;
                $bankAccount->opening_balance = $oldBalance + $new_amount;
                $bankAccount->save();
            }
            elseif ($type == 'debit')
            {
                $oldBalance = $bankAccount->opening_balance;
                $bankAccount->opening_balance = $oldBalance - $new_amount;
                $bankAccount->save();
            }
        }
    }
    
}
