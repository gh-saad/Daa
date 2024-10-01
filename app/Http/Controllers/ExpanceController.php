<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Account\Entities\BankAccount;
use Modules\Account\Entities\BillPayment;
use Modules\Account\Entities\Payment;
use Modules\Account\Entities\PurchasePayment;
use Modules\Account\Entities\Transaction;

class ExpanceController extends Controller
{
    public function cash(){
        $cash_account = BankAccount::where(['holder_name' => 'cash', 'workspace' => getActiveWorkSpace()])->first();

        // bill_payments account_id currency date
        $billPayments = BillPayment::where('account_id', $cash_account->id)->get();
        // payments account_id currency date
        $payments = Payment::where('account_id', $cash_account->id)->get();
        // purchase_payments account_id currency date
        $purchasePayments = PurchasePayment::where('account_id', $cash_account->id)->get();

        return view('cash_expance',compact('billPayments', 'payments' ,'purchasePayments')); // [$billPayments, $payments, $purchasePayments,];
    }

    public function bank(){
        $equity_usd_account = BankAccount::where(['bank_name' => 'EQUITY BANK USD', 'workspace' => getActiveWorkSpace()])->first();
        $equity_kes_account = BankAccount::where(['bank_name' => 'EQUITY BANK KES', 'workspace' => getActiveWorkSpace()])->first();
        $prime_kes_account = BankAccount::where(['bank_name' => 'PRIME BANK -KES', 'workspace' => getActiveWorkSpace()])->first();
        $prime_usd_account = BankAccount::where(['bank_name' => 'PRIME BANK -USD', 'workspace' => getActiveWorkSpace()])->first();
        $filters = [];
        if ($equity_usd_account) {
            array_push($filters, $equity_usd_account->id);
        }
        if ($equity_kes_account) {
            array_push($filters, $equity_kes_account->id);
        }
        if ($prime_kes_account) {
            array_push($filters, $prime_kes_account->id);
        }
        if ($prime_usd_account) {
            array_push($filters, $prime_usd_account->id);
        }
        // bill_payments account_id currency date
        $billPayments = BillPayment::whereIn('account_id', $filters)->get();
        // payments account_id currency date
        $payments = Payment::whereIn('account_id', $filters)->get();
        // purchase_payments account_id currency date
        $purchasePayments = PurchasePayment::whereIn('account_id', $filters)->get();

        return view('bank_expance',compact('billPayments', 'payments' ,'purchasePayments')); // [$billPayments, $payments, $purchasePayments,];
    }

    // $equity_kes_account = BankAccount::where(['bank_name' => 'EQUITY BANK KES', 'workspace' => getActiveWorkSpace()])->first();
    // if($equity_kes_account){
    //     $openingEquity = Transaction::where('account', $equity_kes_account->id)->where('date', '<', $date)->sum('amount');
    // }
    // $prime_kes_account = BankAccount::where(['bank_name' => 'PRIME BANK -KES', 'workspace' => getActiveWorkSpace()])->first();
    // if($prime_kes_account){
    //     $openingPrime = Transaction::where('account', $prime_kes_account->id)->where('date', '<', $date)->sum('amount');
    // }
    // $prime_usd_account = BankAccount::where(['bank_name' => 'PRIME BANK -KES', 'workspace' => getActiveWorkSpace()])->first();
    // if($prime_kes_account){
    //     $openingPrimeUsd = Transaction::where('account', $prime_kes_account->id)->where('date', '<', $date)->sum('amount');
    // }
}
