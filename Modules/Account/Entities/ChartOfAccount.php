<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'sub_type',
        'is_enabled',
        'description',
        'workspace',
        'created_by',
    ];



    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\ChartOfAccountFactory::new();
    }

    public function types()
    {
        return $this->hasOne('Modules\Account\Entities\ChartOfAccountType', 'id', 'type');
    }

    public function subType()
    {
        return $this->hasOne('Modules\Account\Entities\ChartOfAccountSubType', 'id', 'sub_type');
    }

    public function balance($acc_id, $from_date = null, $to_date = null)
    {
        // Eager load the account with its type to prevent N+1 queries
        $account = ChartOfAccount::with('types')->find($acc_id);
    
        if (!$account) {
            return 0; // Return 0 if account not found
        }
    
        // Define a reusable function to calculate the balance
        $calculateBalance = function ($isDebitIncrease) use ($acc_id, $from_date, $to_date) {
    
            // Initialize total values
            $total_debit = 0;
            $total_credit = 0;
    
            // Fetch all debits and convert to default currency
            $all_debit = Transaction::where('account', $acc_id)
                ->where('type', 'Debit')
                ->when($from_date, function ($query) use ($from_date) {
                    return $query->where('date', '>=', $from_date);
                })
                ->when($to_date, function ($query) use ($to_date) {
                    return $query->where('date', '<=', $to_date);
                })
                ->get();
    
            foreach ($all_debit as $debit) {
                $converted_debit_amount = currency_conversion($debit->amount, $debit->currency, company_setting('defult_currancy'));
                $total_debit += $converted_debit_amount;
            }
    
            // Fetch all credits and convert to default currency
            $all_credit = Transaction::where('account', $acc_id)
                ->where('type', 'Credit')
                ->when($from_date, function ($query) use ($from_date) {
                    return $query->where('date', '>=', $from_date);
                })
                ->when($to_date, function ($query) use ($to_date) {
                    return $query->where('date', '<=', $to_date);
                })
                ->get();
    
            foreach ($all_credit as $credit) {
                $converted_credit_amount = currency_conversion($credit->amount, $credit->currency, company_setting('defult_currancy'));
                $total_credit += $converted_credit_amount;
            }
    
            // Return balance based on account type
            return $isDebitIncrease ? $total_debit - $total_credit : $total_credit - $total_debit;
        };
    
        // Switch between account types and calculate balances accordingly
        switch ($account->types->name) {
            case 'Assets':
            case 'Expenses':
            case 'Costs of Goods Sold':
                // For these types, Debit increases the account, Credit decreases it
                return $calculateBalance(true);
    
            case 'Liabilities':
            case 'Equity':
            case 'Income':
                // For these types, Credit increases the account, Debit decreases it
                return $calculateBalance(false);
    
            default:
                return 0; // Return 0 if type is unrecognized
        }
    }    

    public function credit_and_debit_balance($acc_id)
    {
        // Eager load the account with its type to prevent N+1 queries
        $account = ChartOfAccount::with('types')->find($acc_id);
        
        if (!$account) {
            return [
                'total_debit' => 0,
                'total_credit' => 0
            ]; // Return 0 for both debit and credit if account not found
        }

        // Define a reusable function to calculate the debit and credit totals
        $calculateBalance = function () use ($acc_id) {
            
            // Initialize total values
            $total_debit = 0;
            $total_credit = 0;

            // Fetch all debits and convert to default currency
            $all_debit = Transaction::where('account', $acc_id)->where('type', 'Debit')->get();
            foreach ($all_debit as $debit) {
                $converted_debit_amount = currency_conversion($debit->amount, $debit->currency, company_setting('defult_currancy'));
                $total_debit += $converted_debit_amount;
            }

            // Fetch all credits and convert to default currency
            $all_credit = Transaction::where('account', $acc_id)->where('type', 'Credit')->get();
            foreach ($all_credit as $credit) {
                $converted_credit_amount = currency_conversion($credit->amount, $credit->currency, company_setting('defult_currancy'));
                $total_credit += $converted_credit_amount;
            }

            return [
                'total_debit' => $total_debit,
                'total_credit' => $total_credit
            ];
        };

        // Get the calculated debit and credit totals
        $balances = $calculateBalance();

        // Return both totals without further calculations based on account types
        return [
            'total_debit' => $balances['total_debit'],
            'total_credit' => $balances['total_credit']
        ];
    }
    
    public function getRetainedEarnings($from_date, $to_date)
    {
        // Retained Earnings = Beginning Retained Earnings + Net Income (Revenues - Expenses) - Dividends Paid

        // Get the retained earnings account ID
        $retainedEarningsAccount = ChartOfAccount::where('name', 'Retained income')->first();
        
        if (!$retainedEarningsAccount) {
            return 0; // Return 0 if the retained earnings account is not found
        }
    
        // Fetch opening retained earnings
        $openingRetainedEarnings = $this->balance($retainedEarningsAccount->id, null, $from_date); // currently will return zero
    
        // Fetch all chart of accounts related to expense and cost of goods sold
        $all_expense_accounts = ChartOfAccount::whereIn('type', [5, 6])->get();
        $net_expense = 0;
        foreach ($all_expense_accounts as $expense_account) {
            $net_expense += $this->balance($expense_account->id, $from_date, $to_date);
        }
    
        // Fetch all chart of accounts related to income
        $all_income_accounts = ChartOfAccount::where('type', 4)->get();
        $net_income = 0;
        foreach ($all_income_accounts as $income_account) {
            $net_income += $this->balance($income_account->id, $from_date, $to_date);
        }
    
        // Calculate net income for the period
        $net_profit_or_loss = $net_income - $net_expense;
    
        // Calculate the retained earnings including previous period earnings
        $retainedEarnings = $openingRetainedEarnings + $net_profit_or_loss;
    
        // Optionally subtract Owners Drawings if it represents dividends
        $ownersDrawings = ChartOfAccount::where('name', 'Owners Drawings')->first();
        if ($ownersDrawings) {
            $retainedEarnings -= $this->balance($ownersDrawings->id, $from_date, $to_date); // currently will return zero
        }
    
        return $retainedEarnings; // Return the calculated retained earnings
    }
    
}
