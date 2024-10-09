<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Account\Entities\BankAccount;
use Modules\Account\Entities\Transaction;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
            if(Auth::user()->can('report transaction manage'))
            {
            //     $filter['account']  = __('All');
            //     $filter['category'] = __('All');

            //     $account = BankAccount::where('workspace', '=', getActiveWorkSpace())->get()->pluck('holder_name', 'id');
            //     $account->prepend(__('Stripe / Paypal'), 'strip-paypal');

            //     $accounts = Transaction::select('bank_accounts.id', 'bank_accounts.holder_name', 'bank_accounts.bank_name')
            //                         ->leftjoin('bank_accounts', 'transactions.account', '=', 'bank_accounts.id')
            //                         ->groupBy('transactions.account')->selectRaw('sum(amount) as total');
            //    if(module_is_active('ProductService'))
            //    {
            //        $category = \Modules\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->whereIn(
            //            'type', [
            //                    1,
            //                    2,
            //                ]
            //        )->get()->pluck('name', 'name');
            //        $category->prepend('Invoice', 'Invoice');
            //        $category->prepend('Bill', 'Bill');
            //    }

            //     $category = ["Invoice"=>"Invoice","Bill"=>"Bill"];

            //     $transactions = Transaction::orderBy('id', 'desc');

            //     if(!empty($request->start_month) && !empty($request->end_month))
            //     {
            //         $start = strtotime($request->start_month);
            //         $end   = strtotime($request->end_month);
            //     }
            //     else
            //     {
            //         $start = strtotime(date('Y-m'));
            //         $end   = strtotime(date('Y-m', strtotime("-5 month")));
            //     }

            //     if($start > $end) {
            //         $tmp = $start;
            //         $start = $end;
            //         $end = $tmp;
            //     }
                
            //     $currentdate = $start;
            //     while($currentdate <= $end)
            //     {

            //         $data['month'] = date('m', $currentdate);
            //         $data['year']  = date('Y', $currentdate);

            //         $transactions->Orwhere(
            //             function ($query) use ($data){
            //                 $query->whereMonth('date', $data['month'])->whereYear('date', $data['year']);
            //                 $query->where('transactions.workspace', '=' ,getActiveWorkSpace());
            //             }
            //         );

            //         $accounts->Orwhere(
            //             function ($query) use ($data){
            //                 $query->whereMonth('date', $data['month'])->whereYear('date', $data['year']);
            //                 $query->where('transactions.workspace', '=' ,getActiveWorkSpace());

            //             }
            //         );

            //         $currentdate = strtotime('+1 month', $currentdate);
            //     }
            //     $filter['startDateRange'] = date('M-Y', $start);
            //     $filter['endDateRange']   = date('M-Y', $end);

            //     if(!empty($request->account))
            //     {
            //         $transactions->where('account', $request->account);

            //         if($request->account == 'strip-paypal')
            //         {
            //             $accounts->where('account', 0);
            //             $filter['account'] = __('Stripe / Paypal');
            //         }
            //         else
            //         {
            //             $accounts->where('account', $request->account);
            //             $bankAccount       = BankAccount::find($request->account);
            //             $filter['account'] = !empty($bankAccount) ? $bankAccount->holder_name . ' - ' . $bankAccount->bank_name : '';
            //             if($bankAccount->holder_name == 'Cash')
            //             {
            //                 $filter['account'] = 'Cash';
            //             }
            //         }

            //     }
            //     if(!empty($request->category))
            //     {
            //         $transactions->where('category', $request->category);
            //         $accounts->where('category', $request->category);

            //         $filter['category'] = $request->category;
            //     }

            //     $transactions->where('workspace', '=', getActiveWorkSpace());
            //     $accounts->where('transactions.workspace', '=', getActiveWorkSpace());
            //     $transactions = $transactions->get();
            //     $accounts     = $accounts->get();
                // return view('account::transaction.index', compact('transactions', 'account', 'category', 'filter', 'accounts'));

                // transactions query (no get() yet to keep the query builder)
                $transactions = Transaction::where('workspace', '=', getActiveWorkSpace());

                // chart of accounts
                $account = \Modules\Account\Entities\ChartOfAccount::where('workspace', '=', getActiveWorkSpace())->get()->mapWithKeys(function($item) {
                    return [$item->id => $item->name . ' (' . $item->types->name . ')'];
                });

                // filters
                $filter['account']  = __('All');

                // Handle date filtering
                if (!empty($request->start_month) && !empty($request->end_month)) {
                    $start = strtotime($request->start_month);
                    $end   = strtotime($request->end_month);
                } else {
                    $start = strtotime(date('Y-m'));
                    $end   = strtotime(date('Y-m', strtotime("-5 month")));
                }

                if ($start > $end) {
                    $tmp = $start;
                    $start = $end;
                    $end = $tmp;
                }

                // Apply date filter as a grouped condition
                $transactions->where(function ($query) use ($start, $end) {
                    $currentdate = $start;
                    while ($currentdate <= $end) {
                        $data['month'] = date('m', $currentdate);
                        $data['year']  = date('Y', $currentdate);

                        // Add the month/year filter within the same query group
                        $query->orWhere(function ($query) use ($data) {
                            $query->whereMonth('date', $data['month'])
                                ->whereYear('date', $data['year']);
                        });

                        $currentdate = strtotime('+1 month', $currentdate);
                    }
                });

                // Apply account filter if set
                if (!empty($request->account)) {
                    $transactions->where('account', $request->account);
                    $filter['account'] = $account[$request->account];
                }

                // Finally, get the filtered transactions
                $transactions = $transactions->get();

                $filter['startDateRange'] = date('M-Y', $start);
                $filter['endDateRange']   = date('M-Y', $end);

                return view('account::transaction.index', compact('account', 'transactions', 'filter'));

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
        return view('account::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        return view('account::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
