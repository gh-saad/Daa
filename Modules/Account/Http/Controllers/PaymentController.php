<?php

namespace Modules\Account\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Account\Entities\AccountUtility;
use Modules\Account\Entities\BankAccount;
use Modules\Account\Entities\BillPayment;
use Modules\Account\Entities\Payment;
use Modules\Account\Entities\Transaction;
use Modules\Account\Entities\Transfer;
use Modules\Account\Entities\Vender;
use App\Models\EmailTemplate;
use Modules\Account\Events\CreatePayment;
use Modules\Account\Events\DestroyPayment;
use Modules\Account\Events\UpdatePayment;
use App\Http\Controllers\ReceiptController;

// use Modules\ProductService\Entities\Category;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {

        if(Auth::user()->can('expense payment manage'))
        {
            $vendor = Vender::where('workspace', '=',getActiveWorkSpace())->get()->pluck('name', 'id');

            $account = BankAccount::where('workspace',getActiveWorkSpace())->get()->pluck('holder_name', 'id');

            $category=[];
            if(module_is_active('ProductService'))
            {
                $category = \Modules\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->where('type', 1)->get()->pluck('name', 'id');
            }

            $query = Payment::where('workspace', '=', getActiveWorkSpace());

            if(!empty($request->date))
            {
                $date_range = explode(',', $request->date);
                if(count($date_range) == 2)
                {
                    $query->whereBetween('date',$date_range);
                }
                else
                {
                    $query->where('date',$date_range[0]);
                }
            }
            if(!empty($request->vendor))
            {
                $query->where('id', '=', $request->vendor);
            }
            if(!empty($request->account))
            {
                $query->where('account_id', '=', $request->account);
            }

            if(!empty($request->category))
            {
                $query->where('category_id', '=', $request->category);
            }
            $payments = $query->get();

            return view('account::payment.index', compact('payments', 'account', 'category', 'vendor'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if(\Auth::user()->can('expense payment create'))
        {
            $vendors = Vender::where('workspace', '=',getActiveWorkSpace())->get()->pluck('name', 'id');
            $categories=[];
            if(module_is_active('ProductService'))
            {
                $categories = \Modules\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->where('type','2')->get()->pluck('name', 'id');
            }
            $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            $revenue_chart_accounts = \Modules\Account\Entities\ChartOfAccount::where('created_by', '=', creatorId())->where('type', '=', 6)->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            return view('account::payment.create', compact('vendors', 'categories', 'accounts', 'revenue_chart_accounts'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (Auth::user()->can('expense payment create')) {
    
            $validator = \Validator::make(
                $request->all(), [
                    'date' => 'required',
                    'amount' => 'required|min:0',
                    'chart_account_id' => 'required',
                    'account_id' => 'required',
                    'category_id' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
    
            if ($request->type == 'vendor_included'){
                $vendor = \Modules\Account\Entities\Vender::where('id', $request->vendor_id)->where('workspace',getActiveWorkSpace())->first();
            }

            $amount = currency_conversion($request->amount, $request->currency, company_setting("defult_currancy"));

            $payment = new Payment();
            $payment->date = $request->date;
            $payment->amount = $amount;
            $payment->currency = company_setting("defult_currancy");
            $payment->account_id = $request->account_id;
            $payment->chart_account_id = $request->chart_account_id;
            $payment->vendor_id = $request->type == 'vendor_included' ? $vendor->id : null;
            $payment->category_id = $request->category_id;
            $payment->payment_method = 0; // Assuming 0 is a valid payment method ID
            $payment->reference = !empty($request->reference)?$request->reference:'-';
            $payment->description = !empty($request->description)?$request->description:'-';

            // $category = \Modules\ProductService\Entities\Category::where('id', $request->category_id)->first();

            // if(!empty($request->add_receipt))
            // {

            //     // use the user provided receipt
            //     $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
            //     $uplaod = upload_file($request,'add_receipt',$fileName,'payment');
            //     if($uplaod['flag'] == 1)
            //     {
            //         $url = $uplaod['url'];
            //     }
            //     else{
            //         return redirect()->back()->with('error',$uplaod['msg']);
            //     }

            //     $payment->add_receipt = $url;

            // }else{

            //     // Generate receipt
            //     $receiptController = new ReceiptController();
            //     $accountDetails = \Modules\Account\Entities\BankAccount::where('id', $request->account_id)->first();
            //     $vendorDetails = \Modules\Account\Entities\Vender::where('id', $request->vendor_id)->first();

            //     $paymentDetails = [
            //         'Date' => $request->date,
            //         'Amount' => $request->amount,
            //         'Account Holder Name' => $accountDetails->holder_name,
            //         'Vendor Name' => $vendorDetails->name,
            //         'Bank Name' => $vendorDetails->bank_name,
            //         'Vendor Name' => $vendorDetails->name,
            //         'Vendor Billing Address' => $vendorDetails->billing_address,
            //         'Vendor Billing Phone' => $vendorDetails->billing_phone,
            //         'Vendor Tax Number' => $vendorDetails->tax_number,
            //         'Category Name' => $category->name,
            //         'Reference' => $request->reference,
            //         'Description' => $request->description,
            //     ];
        
            //     $filePath = $receiptController->generatePdf($paymentDetails);
        
            //     $payment->add_receipt = 'uploads/receipts/' . basename($filePath);
        
            // }
            
            if(!empty($request->add_receipt))
            {
                $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();

                $uplaod = upload_file($request,'add_receipt',$fileName,'revenue');
                if($uplaod['flag'] == 1)
                {
                    $url = $uplaod['url'];
                }
                else{
                    return redirect()->back()->with('error',$uplaod['msg']);
                }
                $revenue->add_receipt = $url;

            }

            $payment->workspace = getActiveWorkSpace();
            $payment->created_by = \Auth::user()->id;
            $payment->save();
    
            if (!empty($vendor)){
                AccountUtility::userBalance('vendor', $vendor->id, $payment->amount, 'credit');
            }

            // $payment->payment_id = $payment->id;
            // $payment->type = 'Payment';
            // $payment->category = $category->name;
            // $payment->user_id = $payment->vendor_id;
            // $payment->user_type = 'Vendor';
            // $payment->account = $request->account_id;
    
            // Transaction::addTransaction($payment);
    
            // $vendor = Vender::where('id', $request->vendor_id)->first();
            // $payment = new BillPayment();
            // $payment->name = !empty($vendor) ? $vendor['name'] : '';
            // $payment->method = '-';
            // $payment->date = company_date_formate($request->date);
            // $payment->amount = currency_format_with_sym($request->amount);
            // $payment->bill = '';
    
            // Transfer::bankAccountBalance($request->account_id, $request->amount, 'debit');
    
            // currency conversion
            $convertedAmount = currency_conversion($payment->amount, $payment->currency, company_setting("defult_currancy"));
            $bank_account = \Modules\Account\Entities\BankAccount::find($request->account_id);

            // adding Journal Entries
            // Expense Account = Debit = Provided amount
            // Cash/Bank Account = Credit = Provided amount
            
            // new journal entry
            $new_journal_entry = new \Modules\DoubleEntry\Entities\JournalEntry();
            $new_journal_entry->date = now();
            $new_journal_entry->reference = !empty($request->reference)?$request->reference:'-';
            $new_journal_entry->description = !empty($request->description)?$request->description:'Direct Expense';
            $new_journal_entry->journal_id = $this->journalNumber();
            $new_journal_entry->currency = company_setting("defult_currancy");
            $new_journal_entry->workspace = getActiveWorkSpace();
            $new_journal_entry->created_by = \Auth::user()->id;
            $new_journal_entry->save();

            // for expense account provided by the user in the request
            $first_journal_item = new \Modules\DoubleEntry\Entities\JournalItem();
            $first_journal_item->journal = $new_journal_entry->id;
            $first_journal_item->account = $request->chart_account_id; // Expense Account
            $first_journal_item->description = '-';
            $first_journal_item->debit = $convertedAmount;
            $first_journal_item->credit = 0.00;
            $first_journal_item->workspace = getActiveWorkSpace();
            $first_journal_item->created_by = \Auth::user()->id;
            $first_journal_item->save();

            $first_transaction = add_quick_transaction('Debit', $request->chart_account_id, $convertedAmount);

            // for cash/bank account provided by the user in the request
            $second_journal_item = new \Modules\DoubleEntry\Entities\JournalItem();
            $second_journal_item->journal = $new_journal_entry->id;
            $second_journal_item->account = $bank_account->chart_account_id; // Cash/Bank Account
            $second_journal_item->description = '-';
            $second_journal_item->debit = 0.00;
            $second_journal_item->credit = $convertedAmount;
            $second_journal_item->workspace = getActiveWorkSpace();
            $second_journal_item->created_by = \Auth::user()->id;
            $second_journal_item->save();

            $second_transaction = add_quick_transaction('Credit', $bank_account->chart_account_id, $convertedAmount);

            event(new CreatePayment($request, $payment));
    
            // if (!empty($vendor)) {
            //     AccountUtility::userBalance('vendor', $vendor->id, $request->amount, 'debit');
            //     if (!empty(company_setting('Bill Payment Create')) && company_setting('Bill Payment Create') == true) {
            //         $uArr = [
            //             'payment_name' => $payment->name,
            //             'payment_bill' => $payment->bill,
            //             'payment_amount' => $payment->amount,
            //             'payment_date' => $payment->date,
            //             'payment_method' => $payment->method
            //         ];
            //         try {
            //             $resp = EmailTemplate::sendEmailTemplate('Bill Payment Create', [$vendor->id => $vendor->email], $uArr);
            //         } catch (\Exception $e) {
            //             $resp['error'] = $e->getMessage();
            //         }
            //         return redirect()->route('payment.index')->with('success', __('Payment successfully created.') . ((isset($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
            //     }
            // }
    
            return redirect()->route('payment.index')->with('success', __('Payment successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return redirect()->back();
        return view('account::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Payment $payment)
    {
        if(Auth::user()->can('expense payment edit'))
        {
            $vendors = Vender::where('workspace', '=',getActiveWorkSpace())->get()->pluck('name', 'id');

            $categories=[];
            if(module_is_active('ProductService'))
            {
                $categories = \Modules\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            return view('account::payment.edit', compact('vendors', 'categories', 'accounts', 'payment'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, Payment $payment)
    {
        // task1: use chart of account id from the payment to find and delete old journal entries and transactions
        // task2: use the new provided chart of account id and details to create new journal entries and transations similar to in the create function

        // disabling until tasks are completed
        return redirect()->back()->with('error', 'cannot update or delete for now, please notify us where and when you encountered this error.');

        if(Auth::user()->can('expense payment edit'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                    'date' => 'required',
                                    'amount' => 'required|min:0',
                                    'account_id' => 'required',
                                    'vendor_id' => 'required',
                                    'category_id' => 'required',
                                    'reference' => 'required',
                                    'description' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $vendor = Vender::where('id', $request->vendor_id)->first();
            if(!empty($vendor))
            {
                AccountUtility::userBalance('vendor', $vendor->id, $payment->amount, 'credit');
            }
            Transfer::bankAccountBalance($payment->account_id, $payment->amount, 'credit');

            $payment->date           = $request->date;
            $payment->amount         = $request->amount;
            $payment->account_id     = $request->account_id;
            $payment->vendor_id      = $request->vendor_id;
            $payment->category_id    = $request->category_id;
            $payment->payment_method = 0;
            $payment->reference      = $request->reference;
            $payment->description    = $request->description;
            if(!empty($request->add_receipt))
            {
                if(!empty($payment->add_receipt))
                {
                    try
                    {
                        delete_file($payment->add_receipt);
                    }
                catch (Exception $e)
                    {

                    }
                }

                $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                $uplaod = upload_file($request,'add_receipt',$fileName,'payment');
                if($uplaod['flag'] == 1)
                {
                    $url = $uplaod['url'];
                }
                else{
                    return redirect()->back()->with('error',$uplaod['msg']);
                }

                $payment->add_receipt = $url;
            }
            $payment->save();

            $category            = \Modules\ProductService\Entities\Category::where('id', $request->category_id)->first();
            $payment->category   = $category->name;
            $payment->payment_id = $payment->id;
            $payment->type       = 'Payment';
            $payment->account    = $request->account_id;
            Transaction::editTransaction($payment);
            if(!empty($vendor))
            {
                AccountUtility::userBalance('vendor', $vendor->id, $request->amount, 'debit');
            }

            Transfer::bankAccountBalance($request->account_id, $request->amount, 'debit');
            event(new UpdatePayment($request,$payment));
            return redirect()->back()->with('success', __('Payment successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function journalNumber()
    {
        $latest = \Modules\DoubleEntry\Entities\JournalEntry::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->journal_id + 1;
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Payment $payment)
    {
        // task1: use chart of account id from the payment to find and delete old journal entries and transactions
        
        // disabling until tasks are completed
        return redirect()->back()->with('error', 'cannot update or delete for now, please notify us where and when you encountered this error.');
        
        if(Auth::user()->can('expense payment delete'))
        {
            if($payment->workspace == getActiveWorkSpace())
            {
                if(!empty($payment->add_receipt))
                {
                    try
                    {
                        delete_file($payment->add_receipt);
                    }
                    catch (Exception $e)
                    {

                    }
                }
                event(new DestroyPayment($payment));
                $payment->delete();
                $type = 'Payment';
                $user = 'Vendor';
                Transaction::destroyTransaction($payment->id, $type, $user);

                if($payment->vendor_id != 0)
                {
                    AccountUtility::userBalance('vendor', $payment->vendor_id, $payment->amount, 'credit');
                }
                Transfer::bankAccountBalance($payment->account_id, $payment->amount, 'credit');

                return redirect()->route('payment.index')->with('success', __('Payment successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
