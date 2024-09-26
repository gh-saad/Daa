<?php

namespace Modules\Account\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vender extends Model
{
    use HasFactory;
    protected $table = 'vendors';

    protected $fillable = [
        'id',
        'vendor_id',
        'user_id',
        'name',
        'email',
        'contact',
        'tax_number',
        'billing_name',
        'billing_country',
        'billing_state',
        'billing_city',
        'billing_phone',
        'billing_zip',
        'billing_address',
        'shipping_name',
        'shipping_country',
        'shipping_state',
        'shipping_city',
        'shipping_phone',
        'shipping_zip',
        'shipping_address',
        'lang',
        'balance',
        'currency',
        'workspace',
        'created_by',
        'remember_token'
    ];

    protected static function newFactory()
    {
        return \Modules\Account\Database\factories\VenderFactory::new();
    }
    public static function vendorNumberFormat($number)
    {
        $data = !empty(company_setting('vendor_prefix')) ? company_setting('vendor_prefix') : '#VEND0000';

        return $data . sprintf("%05d", $number);
    }
    public function vendorTotalBillSum($vendorId)
    {
        $bills = Bill::where('vendor_id', $vendorId)->get();
        $total = 0;
        foreach ($bills as $bill) {
            $total += $bill->getTotal();
        }
        return $total;
    }
    public function vendorTotalBill($vendorId)
    {
        $bills = Bill::where('vendor_id', $vendorId)->count();

        return $bills;
    }
    public function vendorOverdue($vendorId)
    {
        $dueBill = Bill::where('vendor_id', $vendorId)->whereNotIn(
            'status',
            [
                '0',
                '4',
            ]
        )->where('due_date', '<', date('Y-m-d'))->get();
        $due     = 0;
        foreach ($dueBill as $bill) {
            $due += $bill->getDue();
        }

        return $due;
    }
    public function vendorBill($vendorId)
    {
        $bills = Bill::where('vendor_id', $vendorId)->orderBy('bill_date', 'desc')->get();
        return $bills;
    }
    public function vendorPayment($vendorId)
    {
        // create an empty collection variable
        $vendorPayments = collect([]);

        // get all payments
        $payments = Payment::where('vendor_id', $vendorId)->orderBy('date', 'desc')->get();
        // append to collection
        $vendorPayments = $vendorPayments->merge($payments);

        // find bills with this vendor
        $bills = Bill::where('vendor_id', $vendorId)->get();
        // loop through bills and find payments for each bill
        foreach ($bills as $bill) {
            $billPayments = BillPayment::where('bill_id', $bill->id)->orderBy('date', 'desc')->get();
            // append to collection
            $vendorPayments = $vendorPayments->merge($billPayments);
        }

        // find purchases with this vendor
        $purchases = Purchase::where('vender_id', $vendorId)->get();
        // loop through purchases and find payments for each purchase
        foreach ($purchases as $purchase) {
            $purchasePayments = PurchasePayment::where('purchase_id', $purchase->id)->orderBy('date', 'desc')->get();
            // append to collection
            $vendorPayments = $vendorPayments->merge($purchasePayments);
        }

        return $vendorPayments;
    }
    public function user()
    {
        return  $this->hasOne(User::class,'id','user_id');
    }
}
