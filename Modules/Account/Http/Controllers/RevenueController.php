<?php

namespace Modules\Account\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Account\Entities\AccountUtility;
use Modules\Account\Entities\BankAccount;
use Modules\Account\Entities\Customer;
use Modules\Account\Entities\Revenue;
use Modules\Account\Entities\Transaction;
use Modules\Account\Entities\Transfer;
use Modules\Account\Events\CreateRevenue;
use Modules\Account\Events\DestroyRevenue;
use Modules\Account\Events\UpdateRevenue;

class RevenueController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
            if(Auth::user()->can('revenue manage'))
            {
                $customer = Customer::where('workspace', '=',getActiveWorkSpace())->get()->pluck('name', 'id');

                $account = BankAccount::where('workspace',getActiveWorkSpace())->get()->pluck('holder_name', 'id');
                if(module_is_active('ProductService'))
                {
                    $category = \Modules\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->where('type', 1)->get()->pluck('name', 'id');
                }
                else
                {
                    $category = [];
                }

                $query = Revenue::where('workspace', '=', getActiveWorkSpace());

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
                if(!empty($request->customer))
                {
                    $query->where('customer_id', '=', $request->customer);
                }

                if(!empty($request->account))
                {
                    $query->where('account_id', '=', $request->account);
                }
                if(!empty($request->category))
                {
                    $query->where('category_id', '=', $request->category);
                }

                if(!empty($request->payment))
                {
                    $query->where('payment_method', '=', $request->payment);
                }

                $revenues = $query->get();

                return view('account::revenue.index', compact('revenues', 'customer', 'account', 'category'));
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
        if(Auth::user()->can('revenue create'))
        {
            $customers = Customer::where('workspace', '=',getActiveWorkSpace())->get()->pluck('name', 'id');
            if(module_is_active('ProductService'))
            {
                $categories = \Modules\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->where('type', 1)->get()->pluck('name', 'id');
            }
            else
            {
                $categories = [];
            }
            $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            $revenue_chart_accounts = \Modules\Account\Entities\ChartOfAccount::where('created_by', '=', creatorId())->where('type', '=', 4)->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            return view('account::revenue.create', compact('customers', 'categories', 'accounts', 'revenue_chart_accounts'));
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
        if(Auth::user()->can('revenue create'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'date' => 'required',
                                   'amount' => 'required|numeric|gt:0',
                                   'chart_account_id' => 'required',
                                   'account_id' => 'required',
                                   'category_id' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if ($request->type == 'customer_included'){
                $customer = Customer::where('id',$request->customer_id)->where('workspace',getActiveWorkSpace())->first();
            }

            $amount = currency_conversion($request->amount, $request->currency, company_setting("defult_currancy"));

            $revenue                 = new Revenue();
            $revenue->date           = $request->date;
            $revenue->amount         = $amount;
            $revenue->currency       = company_setting("defult_currancy");
            $revenue->account_id     = $request->account_id;
            $revenue->chart_account_id = $request->chart_account_id;
            $revenue->customer_id    = $request->type == 'customer_included' ? $request->customer_id : null;
            $revenue->user_id        = $request->type == 'customer_included' ? $customer->user_id : null;
            $revenue->category_id    = $request->category_id;
            $revenue->payment_method = 0;
            $revenue->reference      = !empty($request->reference)?$request->reference:'-';
            $revenue->description    = !empty($request->description)?$request->description:'-';

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

            $revenue->created_by     = \Auth::user()->id;
            $revenue->workspace        = getActiveWorkSpace();
            $revenue->save();

            // if(module_is_active('ProductService'))
            // {
            //     $category            = \Modules\ProductService\Entities\Category::where('id', $request->category_id)->first();
            // }
            // else
            // {
            //     $category = [];
            // }

            if (!empty($customer)){
                AccountUtility::userBalance('customer', $customer->id, $revenue->amount, 'credit');
            }

            // $revenue->payment_id = $revenue->id;
            // $revenue->type       = 'Revenue';
            // $revenue->category   = !empty($category) ? $category->name : '';
            // $revenue->user_id    = $revenue->customer_id;
            // $revenue->user_type  = 'Customer';
            // $revenue->account    = $request->account_id;
            // Transaction::addTransaction($revenue);

            // $customer         = Customer::where('id', $request->customer_id)->first();
            // $payment          = new InvoicePayment();
            // $payment->name    = !empty($customer) ? $customer['name'] : '';
            // $payment->date    = company_date_formate($request->date);
            // $payment->amount  = currency_format_with_sym($request->amount);
            // $payment->invoice = '';
            // if(!empty($customer))
            // {
            //     AccountUtility::userBalance('customer', $customer->id, $revenue->amount, 'credit');
            // }

            // Transfer::bankAccountBalance($request->account_id, $revenue->amount, 'credit');

            // currency conversion
            $convertedAmount = currency_conversion($revenue->amount, $revenue->currency, company_setting("defult_currancy"));
            $bank_account = \Modules\Account\Entities\BankAccount::find($request->account_id);

            // adding Journal Entries
            // Revenue Account = Credit = Provided amount
            // Cash/Bank Account = Debit = Provided amount
            
            // new journal entry
            $new_journal_entry = new \Modules\DoubleEntry\Entities\JournalEntry();
            $new_journal_entry->date = now();
            $new_journal_entry->reference = !empty($request->reference)?$request->reference:'-';
            $new_journal_entry->description = !empty($request->description)?$request->description:'Direct Revenue';
            $new_journal_entry->journal_id = $this->journalNumber();
            $new_journal_entry->currency = company_setting("defult_currancy");
            $new_journal_entry->workspace = getActiveWorkSpace();
            $new_journal_entry->created_by = \Auth::user()->id;
            $new_journal_entry->save();

            // for revenue account provided by the user in the request
            $first_journal_item = new \Modules\DoubleEntry\Entities\JournalItem();
            $first_journal_item->journal = $new_journal_entry->id;
            $first_journal_item->account = $request->chart_account_id; // Revenue Account
            $first_journal_item->description = '-';
            $first_journal_item->debit = 0.00;
            $first_journal_item->credit = $convertedAmount;
            $first_journal_item->workspace = getActiveWorkSpace();
            $first_journal_item->created_by = \Auth::user()->id;
            $first_journal_item->save();

            $first_transaction = add_quick_transaction('Credit', $request->chart_account_id, $convertedAmount);

            // for cash/bank account provided by the user in the request
            $second_journal_item = new \Modules\DoubleEntry\Entities\JournalItem();
            $second_journal_item->journal = $new_journal_entry->id;
            $second_journal_item->account = $bank_account->chart_account_id; // Cash/Bank Account
            $second_journal_item->description = '-';
            $second_journal_item->debit = $convertedAmount;
            $second_journal_item->credit = 0.00;
            $second_journal_item->workspace = getActiveWorkSpace();
            $second_journal_item->created_by = \Auth::user()->id;
            $second_journal_item->save();

            $second_transaction = add_quick_transaction('Debit', $bank_account->chart_account_id, $convertedAmount);

            event(new CreateRevenue($request,$revenue));
            // if(!empty(company_setting('Revenue Payment Create')) && company_setting('Revenue Payment Create')  == true)
            // {
            //     $uArr = [
            //         'payment_name' => $payment->name,
            //         'payment_amount' => $payment->amount,
            //         'revenue_type' =>$revenue->type,
            //         'payment_date' => $payment->date,
            //     ];
            //     try
            //     {
            //         $resp = EmailTemplate::sendEmailTemplate('Revenue Payment Create', [$customer->id => $customer->email], $uArr);
            //     }
            //     catch(\Exception $e)
            //     {
            //         $resp['error'] = $e->getMessage();
            //         }
            //         return redirect()->route('revenue.index')->with('success', __('Revenue successfully created.') . ((isset($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
            // }

            return redirect()->route('revenue.index')->with('success', __('Revenue successfully created.'));

        }
        else
        {
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
    public function edit(Revenue $revenue)
    {
        if(Auth::user()->can('revenue edit'))
        {
            $customers = Customer::where('workspace', '=',getActiveWorkSpace())->get()->pluck('name', 'id');
            if(module_is_active('ProductService'))
            {
                $categories = \Modules\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->where('type', 1)->get()->pluck('name', 'id');
            }
            else
            {
                $categories = [];
            }
            $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            $revenue_chart_accounts = \Modules\Account\Entities\ChartOfAccount::where('created_by', '=', creatorId())->where('type', '=', 4)->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            return view('account::revenue.edit', compact('customers', 'categories', 'accounts', 'revenue', 'revenue_chart_accounts'));
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
    public function update(Request $request, Revenue $revenue)
    {
        // task1: revenue table requires chart_account_id to store user provided chart of account id
        // task2: use this chart of account id to find and delete old journal entries and transactions
        // task3: use the new provided chart of account id and details to create new journal entries and transations similar to in the create function

        // disabling until tasks are completed
        return redirect()->back()->with('error', 'cannot update or delete for now, please notify us where and when you encountered this error.');

        if(Auth::user()->can('revenue edit'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'date' => 'required',
                                    'amount' => 'required|numeric|gt:0',
                                    'account_id' => 'required',
                                    'category_id' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $amount = currency_conversion($request->amount, $request->currency, company_setting("defult_currancy"));

            $requires_new_journal_entries = false;
            if ($request->amount != $revenue->amount || $request->currency != ($revenue->currency ? $revenue->currency : 'KES') || $request->account_id != $revenue->account_id || $request->chart_account_id != null){
                $requires_new_journal_entries = true;
            }

            dd($request->all());

            if ($request->type == 'customer_included'){
                $customer = Customer::where('id',$request->customer_id)->where('workspace',getActiveWorkSpace())->first();
            }

            // Reverse old customer balance if customer included in the original entry
            if ($revenue->customer_id) {
                AccountUtility::userBalance('customer', $revenue->customer_id, $revenue->amount, 'debit');
            }
    
            // Update revenue details
            $revenue->date = $request->date;
            $revenue->amount = $amount;
            $revenue->currency = company_setting("defult_currancy");
            $revenue->account_id = $request->account_id;
            $revenue->customer_id = $request->type == 'customer_included' ? $request->customer_id : null;
            $revenue->customer_id = $request->type == 'customer_included' ? $customer->user_id : null;
            $revenue->category_id = $request->category_id;
            $revenue->reference = !empty($request->reference) ? $request->reference : '-';
            $revenue->description = !empty($request->description) ? $request->description : '-';

            if(!empty($request->add_receipt))
            {
                if(!empty($revenue->add_receipt))
                {
                    try
                    {
                          delete_file($revenue->add_receipt);
                    }
                    catch (Exception $e)
                    {

                    }
                }
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

            $revenue->save();
            
            // Update user balance if customer is included
            if($request->type == 'customer_included') {
                if (!empty($customer)) {
                    AccountUtility::userBalance('customer', $customer->id, $amount, 'credit');
                }
            }

            if(module_is_active('ProductService'))
            {
                $category            = \Modules\ProductService\Entities\Category::where('id', $request->category_id)->first();
            }
            else
            {
                $category = [];
            }
            $revenue->category   = !empty($category) ? $category->name : '';
            $revenue->payment_id = $revenue->id;
            $revenue->type       = 'Revenue';
            $revenue->account    = $request->account_id;
            
            // Transaction::editTransaction($revenue);

            // if(!empty($customer))
            // {
            //     AccountUtility::userBalance('customer', $customer->id, $request->amount, 'credit');
            // }

            // Transfer::bankAccountBalance($request->account_id, $request->amount, 'credit');

            event(new UpdateRevenue($request,$revenue));
            return redirect()->route('revenue.index')->with('success', __('Revenue successfully updated.'));
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
    public function destroy(Revenue $revenue)
    {
        // task1: revenue table requires chart_account_id to store user provided chart of account id
        // task2: use this chart of account id to find and delete old journal entries and transactions
        
        // disabling until tasks are completed
        return redirect()->back()->with('error', 'cannot update or delete for now, please notify us where and when you encountered this error.');
        
        if(Auth::user()->can('revenue delete'))
        {
            if($revenue->workspace == getActiveWorkSpace())
            {
                $type = 'Revenue';
                $user = 'Customer';
                Transaction::destroyTransaction($revenue->id, $type, $user);

                if($revenue->customer_id != 0)
                {
                    AccountUtility::userBalance('customer', $revenue->customer_id, $revenue->amount, 'debit');
                }

                Transfer::bankAccountBalance($revenue->account_id, $revenue->amount, 'debit');
                if(!empty($revenue->add_receipt))
                {
                    try
                    {
                        delete_file($revenue->add_receipt);
                    }
                    catch (Exception $e)
                    {

                    }
                }
                event(new DestroyRevenue($revenue));
                $revenue->delete();
                return redirect()->route('revenue.index')->with('success', __('Revenue successfully deleted.'));
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
