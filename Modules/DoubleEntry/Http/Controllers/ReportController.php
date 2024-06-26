<?php

namespace Modules\DoubleEntry\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceProduct;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Account\Entities\AccountUtility;
use Modules\Account\Entities\Bill;
use Modules\Account\Entities\ChartOfAccount;
use Modules\Account\Entities\ChartOfAccountParent;
use Modules\Account\Entities\ChartOfAccountSubType;
use Modules\Account\Entities\ChartOfAccountType;
use Modules\Account\Entities\Transaction;
use Modules\DoubleEntry\Entities\JournalItem;
use Modules\Account\Entities\TransactionLines;

class ReportController extends Controller
{
    public function yearMonth()
    {
        $month[] = __('January');
        $month[] = __('February');
        $month[] = __('March');
        $month[] = __('April');
        $month[] = __('May');
        $month[] = __('June');
        $month[] = __('July');
        $month[] = __('August');
        $month[] = __('September');
        $month[] = __('October');
        $month[] = __('November');
        $month[] = __('December');
        return $month;
    }

    public function yearList()
    {
        $starting_year = date('Y', strtotime('-5 year'));
        $ending_year = date('Y');
        foreach (range($ending_year, $starting_year) as $year) {
            $years[$year] = $year;
        }
        return $years;
    }

    public function ledgerReport(Request $request, $account = '')
    {

        if(Auth::user()->can('report ledger'))
        {

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start = $request->start_date;
                $end = $request->end_date;
            } else {
                $start = date('Y-m-01');
                $end = date('Y-m-t');
            }
            if (!empty($request->account)) {
                $chart_accounts = ChartOfAccount::where('id', $request->account)->where('created_by', creatorId())->get();
                $accounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name')
                    // ->where('parent', '=', 0)
                    ->where('workspace', getActiveWorkSpace())
                    ->where('created_by', creatorId())->get()
                    ->toarray();

            } else {
                $chart_accounts = ChartOfAccount::where('created_by', creatorId())->get();
                $accounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name')
                    // ->where('parent', '=', 0)
                    ->where('workspace', getActiveWorkSpace())
                    ->where('created_by', creatorId())->get()
                    ->toarray();
            }

            // $subAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_account_parents.account');
            // $subAccounts->leftjoin('chart_of_account_parents', 'chart_of_accounts.parent', 'chart_of_account_parents.id');
            // $subAccounts->where('chart_of_accounts.parent', '!=', 0);
            // $subAccounts->where('chart_of_accounts.created_by', creatorId());
            // $subAccounts->where('chart_of_accounts.workspace', getActiveWorkSpace());
            // $subAccounts = $subAccounts->get()->toArray();
            $balance = 0;
            $debit = 0;
            $credit = 0;
            $filter['balance'] = $balance;
            $filter['credit'] = $credit;
            $filter['debit'] = $debit;
            $filter['startDateRange'] = $start;
            $filter['endDateRange'] = $end;
            return view('doubleentry::report.ledger', compact('filter', 'accounts', 'chart_accounts'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function balanceSheet(Request $request, $view = '', $collapseview = 'expand')
    {
        if (Auth::user()->can('report balance sheet')) {
            $start = $request->start_date ?? date('Y-01-01');
            $end = $request->end_date ?? date('Y-m-d', strtotime('+1 day'));
    
            $types = ChartOfAccountType::where('workspace', getActiveWorkSpace())
                ->whereIn('name', ['Assets', 'Liabilities', 'Equity'])->get();
    
            $totalAccounts = [];
            foreach ($types as $type) {
    
                $subTypes = ChartOfAccountSubType::where('type', $type->id)->get();
                foreach ($subTypes as $subType) {
    
                    $accounts = ChartOfAccount::where('sub_type', $subType->id)
                        ->where('type', $type->id)->where('workspace', getActiveWorkSpace())->get();
                    foreach ($accounts as $account) {
                        
                        $transactions = Transaction::join(
                            "chart_of_accounts",
                            "transactions.account",
                            "=",
                            "chart_of_accounts.id"
                        )
                        ->select(
                            "transactions.*", 
                            "chart_of_accounts.type as chart_type"
                        )
                        ->where("transactions.account", $account->id)
                        ->whereBetween("transactions.date", [$start, $end])
                        ->get();
        
                        $balance = $transactions->sum(function ($transaction) {
                            return $transaction->type == 'Credit' ? $transaction->amount : -$transaction->amount;
                        });
    
                        if ($balance != 0) {
                            $totalAccounts[$type->name][$subType->name][$account->id] = [
                                'account_id' => $account->id,
                                'account_name' => $account->name,
                                'account_code' => $account->code,
                                'total_amount' => $balance,
                            ];
                        }
                    }
                }
            }
    
            $filter['startDateRange'] = $start;
            $filter['endDateRange'] = $end;
            if ($request->view == 'horizontal' || $view == 'horizontal') {
                return view('doubleentry::report.balance_sheet_horizontal', compact('filter', 'totalAccounts', 'collapseview', 'types'));
            } elseif ($view == '' || $view == 'vertical') {
                return view('doubleentry::report.balance_sheet', compact('filter', 'totalAccounts', 'collapseview', 'types'));
            } else {
                return redirect()->back();
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }    

    // 
    public function profitLoss(Request $request, $view = '', $collapseView = 'expand')
    {
        if (!Auth::user()->can('report profit loss')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
        } else {
            $start = date('Y-01-01');
            $end = date('Y-m-t');
        };
        $total_credit = 0;
        $total_accounts = [];
        $types = ChartOfAccountType::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
        foreach ($types as $type) {
            $subTypes = ChartOfAccountSubType::where('type', $type->id)->get();
            foreach ($subTypes as $subType) {
                $accounts = ChartOfAccount::where('sub_type', $subType->id)
                    ->where('type', $type->id)->where('workspace', getActiveWorkSpace())->get();
                foreach ($accounts as $account) {
                    $chartDatas = \Modules\Account\Entities\AccountUtility::getAccountData(
                        $account->id,
                        $start,
                        $end,
                    );
          
                    foreach ($chartDatas as $key => $payments) {
                        foreach ($payments as $payment) {
                            $total = $payment->amount;

                            if ($key == 'invoice' || $key == 'invoicepayment' || $key == 'revenue' || $key == 'bill') {
                                $total_credit += $total;
                            } 
                        }
                        if ($total_credit != 0 ) {
                            $total_accounts[$type->name][$subType->name][$account->id] = [
                                'id' => $account->id,
                                'name' => $account->name,
                                'code' => $account->code,
                                'amount' => $total_credit,
                                'credit' => $total_credit,
                            ];
                        }
                    }
                    // Reset total_credit and total_debit for the next account
                    $total_credit = 0;
                }
            }
        }
        $balance = 0;
        $debit = 0;
        $credit = 0;
        $filter['balance'] = $balance;
        $filter['credit'] = $credit;
        $filter['debit'] = $debit;
        $filter['startDateRange'] = $start;
        $filter['endDateRange'] = $end;
        return view('doubleentry::report.profit_loss', compact('filter', 'total_accounts', 'collapseView'));
    }

    public function trialBalance(Request $request, $view = "expand")
    {
        if (Auth::user()->can('report trial balance')) {

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start = $request->start_date;
                $end = $request->end_date;
            } else {
                $start = date('Y-01-01');
                $end = date('Y-m-t');
            }
            $total_accounts = [];
            $data = [
                "invoice" => 0,
                "invoicepayment" => 0,
                "revenue" => 0,
                "bill" => 0,
                "billdata" => 0,
                "billpayment" => 0,
                "payment" => 0,
                "journalItem" => 0
            ];
            $total_debit = 0;
            $total_credit = 0;
            $types = ChartOfAccountType::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            foreach ($types as $type) {
                $subTypes = ChartOfAccountSubType::where('type', $type->id)->get();
                foreach ($subTypes as $subType) {
                    $accounts = ChartOfAccount::where('sub_type', $subType->id)
                        ->where('type', $type->id)->where('workspace', getActiveWorkSpace())->get();
                    foreach ($accounts as $account) {
                        $chartDatas = \Modules\Account\Entities\AccountUtility::getAccountData(
                            $account->id,
                            $start,
                            $end,
                        );
                        // "invoice", credit
                        // "invoicepayment", credit
                        // "revenue", credit
                        // "bill", credit

                        // "billdata", debit
                        // "billpayment", debit
                        // "payment", debit
                        // "journalItem,
                        foreach ($chartDatas as $key => $payments) {
                            foreach ($payments as $payment) {
                                $total = $payment->amount;

                                if ($key == 'billdata' || $key == 'billpayment' || $key == 'payment') {
                                    $total_debit += $total;
                                } else {
                                    $total_credit += $total;
                                }
                            }
                            if ($total_credit != 0 || $total_debit != 0) {
                                $total_accounts[$type->name][$subType->name][$account->id] = [
                                    'id' => $account->id,
                                    'name' => $account->name,
                                    'code' => $account->code,
                                    'amount' => $total_credit - $total_debit,
                                    'credit' => $total_credit,
                                    'debit' => $total_debit,
                                ];
                            }
                        }
                        // Reset total_credit and total_debit for the next account
                        $total_credit = 0;
                        $total_debit = 0;
                    }
                }
            }

            $balance = 0;
            $debit = 0;
            $credit = 0;
            $filter['balance'] = $balance;
            $filter['credit'] = $credit;
            $filter['debit'] = $debit;
            $filter['startDateRange'] = $start;
            $filter['endDateRange'] = $end;

            return view('doubleentry::report.trial_balance', compact('filter', 'accounts', 'view', 'total_accounts'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }



    public function salesReport(Request $request)
    {
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
        } else {
            $start = date('Y-01-01');
            $end = date('Y-m-d', strtotime('+1 day'));
        }

        $invoiceItems = InvoiceProduct::select('product_services.name', \DB::raw('sum(invoice_products.quantity) as quantity'), \DB::raw('sum(invoice_products.price) as price'), \DB::raw('sum(invoice_products.price)/sum(invoice_products.quantity) as avg_price'));
        $invoiceItems->leftjoin('product_services', 'product_services.id', 'invoice_products.product_id');
        $invoiceItems->where('product_services.created_by', creatorId());
        $invoiceItems->where('product_services.workspace_id', getActiveWorkSpace());
        $invoiceItems->where('invoice_products.created_at', '>=', $start);
        $invoiceItems->where('invoice_products.created_at', '<=', $end);
        $invoiceItems->groupBy('invoice_products.product_id');
        $invoiceItems = $invoiceItems->get()->toArray();

        $invoiceCustomers = Invoice::select('customers.name',
            \DB::raw('count(invoices.customer_id) as invoice_count'),
            \DB::raw('sum(invoice_products.price) as price'),
            \DB::raw('sum(invoice_products.price * (taxes.rate / 100 )) as total_tax')
        );

        $invoiceCustomers->leftJoin('customers', 'customers.id', 'invoices.customer_id');
        $invoiceCustomers->leftJoin('invoice_products', 'invoice_products.invoice_id', 'invoices.id');
        $invoiceCustomers->leftJoin('taxes', \DB::raw('FIND_IN_SET(taxes.id, invoice_products.tax)'), '>', \DB::raw('0'));
        $invoiceCustomers->where('invoices.created_by', creatorId());
        $invoiceCustomers->where('invoices.workspace', getActiveWorkSpace());
        $invoiceCustomers->where('invoices.created_at', '>=', $start);
        $invoiceCustomers->where('invoices.created_at', '<=', $end);
        $invoiceCustomers->groupBy('invoices.customer_id');
        $invoiceCustomers = $invoiceCustomers->get()->toArray();

        $filter['startDateRange'] = $start;
        $filter['endDateRange'] = $end;

        return view('doubleentry::report.sales_report', compact('filter', 'invoiceItems', 'invoiceCustomers'));
    }

    public function salesReportPrint(Request $request)
    {
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
        } else {
            $start = date('Y-01-01');
            $end = date('Y-m-d', strtotime('+1 day'));
        }

        $invoiceItems = InvoiceProduct::select('product_services.name', \DB::raw('sum(invoice_products.quantity) as quantity'), \DB::raw('sum(invoice_products.price) as price'), \DB::raw('sum(invoice_products.price)/sum(invoice_products.quantity) as avg_price'));
        $invoiceItems->leftjoin('product_services', 'product_services.id', 'invoice_products.product_id');
        $invoiceItems->where('product_services.created_by', creatorId());
        $invoiceItems->where('product_services.workspace_id', getActiveWorkSpace());
        $invoiceItems->where('invoice_products.created_at', '>=', $start);
        $invoiceItems->where('invoice_products.created_at', '<=', $end);
        $invoiceItems->groupBy('invoice_products.product_id');
        $invoiceItems = $invoiceItems->get()->toArray();

        $invoiceCustomers = Invoice::select('customers.name',
            \DB::raw('count(invoices.customer_id) as invoice_count'),
            \DB::raw('sum(invoice_products.price) as price'),
            \DB::raw('sum(invoice_products.price * (taxes.rate / 100 )) as total_tax')
        );

        $invoiceCustomers->leftJoin('customers', 'customers.id', 'invoices.customer_id');
        $invoiceCustomers->leftJoin('invoice_products', 'invoice_products.invoice_id', 'invoices.id');
        $invoiceCustomers->leftJoin('taxes', \DB::raw('FIND_IN_SET(taxes.id, invoice_products.tax)'), '>', \DB::raw('0'));
        $invoiceCustomers->where('invoices.created_by', creatorId());
        $invoiceCustomers->where('invoices.workspace', getActiveWorkSpace());
        $invoiceCustomers->where('invoices.created_at', '>=', $start);
        $invoiceCustomers->where('invoices.created_at', '<=', $end);
        $invoiceCustomers->groupBy('invoices.customer_id');
        $invoiceCustomers = $invoiceCustomers->get()->toArray();

        $filter['startDateRange'] = $start;
        $filter['endDateRange'] = $end;

        $reportName = $request->report;

        return view('doubleentry::report.sales_report_receipt', compact('filter', 'invoiceItems', 'invoiceCustomers', 'reportName'));
    }

    public function ReceivablesReport(Request $request)
    {

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
        } else {
            $start = date('Y-01-01');
            $end = date('Y-m-d', strtotime('+1 day'));
        }

        $receivableCustomers = Invoice::select('customers.name')
            ->selectRaw('sum((invoice_products.price * invoice_products.quantity) - invoice_products.discount) as price')
            ->selectRaw('sum((invoice_payments.amount)) as pay_price')
            ->selectRaw('(SELECT SUM((price * quantity - discount) * (taxes.rate / 100)) FROM invoice_products
             LEFT JOIN taxes ON FIND_IN_SET(taxes.id, invoice_products.tax) > 0
             WHERE invoice_products.invoice_id = invoices.id) as total_tax')
            ->selectRaw('(SELECT SUM(credit_notes.amount) FROM credit_notes
             WHERE credit_notes.invoice = invoices.id) as credit_price')
            ->leftJoin('customers', 'customers.id', 'invoices.customer_id')
            ->leftJoin('invoice_payments', 'invoice_payments.invoice_id', 'invoices.id')
            ->leftJoin('invoice_products', 'invoice_products.invoice_id', 'invoices.id')
            ->where('invoices.created_by', creatorId())
            ->where('invoices.workspace', getActiveWorkSpace())
            ->where('invoices.issue_date', '>=', $start)
            ->where('invoices.issue_date', '<=', $end)
            ->groupBy('invoices.invoice_id')
            ->get()
            ->toArray();


        $receivableSummariesInvoice = Invoice::select('customers.name')
            ->selectRaw('(invoices.invoice_id) as invoice')
            ->selectRaw('sum((invoice_products.price * invoice_products.quantity) - invoice_products.discount) as price')
            ->selectRaw('sum((invoice_payments.amount)) as pay_price')
            ->selectRaw('(SELECT SUM((price * quantity - discount) * (taxes.rate / 100)) FROM invoice_products
             LEFT JOIN taxes ON FIND_IN_SET(taxes.id, invoice_products.tax) > 0
             WHERE invoice_products.invoice_id = invoices.id) as total_tax')
            ->selectRaw('invoices.issue_date as issue_date')
            ->selectRaw('invoices.status as status')
            ->leftJoin('customers', 'customers.id', 'invoices.customer_id')
            ->leftJoin('invoice_payments', 'invoice_payments.invoice_id', 'invoices.id')
            ->leftJoin('invoice_products', 'invoice_products.invoice_id', 'invoices.id')
            ->where('invoices.created_by', creatorId())
            ->where('invoices.workspace', getActiveWorkSpace())
            ->where('invoices.issue_date', '>=', $start)
            ->where('invoices.issue_date', '<=', $end)
            ->groupBy('invoices.invoice_id')
            ->get()
            ->toArray();

        $receivableSummariesCredit = \Modules\Account\Entities\CreditNote::select('customers.name')
            ->selectRaw('null as invoice')
            ->selectRaw('(credit_notes.amount) as price')
            ->selectRaw('0 as pay_price')
            ->selectRaw('0 as total_tax')
            ->selectRaw('credit_notes.date as issue_date')
            ->selectRaw('5 as status')
            ->leftJoin('customers', 'customers.id', 'credit_notes.customer')
            ->leftJoin('invoice_products', 'invoice_products.invoice_id', 'credit_notes.invoice')
            ->leftJoin('invoices', 'invoices.id', 'credit_notes.invoice')
            ->where('invoices.created_by', creatorId())
            ->where('invoices.workspace', getActiveWorkSpace())
            ->where('credit_notes.date', '>=', $start)
            ->where('credit_notes.date', '<=', $end)
            ->groupBy('credit_notes.id')
            ->get()
            ->toArray();

        $receivableSummaries = (array_merge($receivableSummariesCredit, $receivableSummariesInvoice));

        $receivableDetailsInvoice = Invoice::select('customers.name')
            ->selectRaw('(invoices.invoice_id) as invoice')
            ->selectRaw('sum(invoice_products.price) as price')
            ->selectRaw('(invoice_products.quantity) as quantity')
            ->selectRaw('(product_services.name) as product_name')
            ->selectRaw('invoices.issue_date as issue_date')
            ->selectRaw('invoices.status as status')
            ->leftJoin('customers', 'customers.id', 'invoices.customer_id')
            ->leftJoin('invoice_products', 'invoice_products.invoice_id', 'invoices.id')
            ->leftJoin('product_services', 'product_services.id', 'invoice_products.product_id')
            ->where('invoices.created_by', creatorId())
            ->where('invoices.workspace', getActiveWorkSpace())
            ->where('invoices.issue_date', '>=', $start)
            ->where('invoices.issue_date', '<=', $end)
            ->groupBy('invoices.invoice_id', 'product_services.name')
            ->get()
            ->toArray();

        $receivableDetailsCredit = \Modules\Account\Entities\CreditNote::select('customers.name')
            ->selectRaw('null as invoice')
            ->selectRaw('(credit_notes.id) as invoices')
            ->selectRaw('(credit_notes.amount) as price')
            ->selectRaw('(product_services.name) as product_name')
            ->selectRaw('credit_notes.date as issue_date')
            ->selectRaw('5 as status')
            ->leftJoin('customers', 'customers.id', 'credit_notes.customer')
            ->leftJoin('invoice_products', 'invoice_products.invoice_id', 'credit_notes.invoice')
            ->leftJoin('product_services', 'product_services.id', 'invoice_products.product_id')
            ->leftJoin('invoices', 'invoices.id', 'credit_notes.invoice')
            ->where('invoices.created_by', creatorId())
            ->where('invoices.workspace', getActiveWorkSpace())
            ->where('credit_notes.date', '>=', $start)
            ->where('credit_notes.date', '<=', $end)
            ->groupBy('credit_notes.id', 'product_services.name')
            ->get()
            ->toArray();

        $mergedArray = [];
        foreach ($receivableDetailsCredit as $item) {
            $invoices = $item["invoices"];

            if (!isset($mergedArray[$invoices])) {
                $mergedArray[$invoices] = [
                    "name" => $item["name"],
                    "invoice" => $item["invoice"],
                    "invoices" => $invoices,
                    "price" => $item["price"],
                    "quantity" => 0,
                    "product_name" => "",
                    "issue_date" => "",
                    "status" => 0,
                ];
            }

            if (!strstr($mergedArray[$invoices]["product_name"], $item["product_name"])) {
                if ($mergedArray[$invoices]["product_name"] !== "") {
                    $mergedArray[$invoices]["product_name"] .= ", ";
                }
                $mergedArray[$invoices]["product_name"] .= $item["product_name"];
            }

            $mergedArray[$invoices]["issue_date"] = $item["issue_date"];
            $mergedArray[$invoices]["status"] = $item["status"];
        }

        $receivableDetailsCredits = array_values($mergedArray);

        $receivableDetails = (array_merge($receivableDetailsInvoice, $receivableDetailsCredits));

        $agingSummary = Invoice::select('customers.name', 'invoices.due_date as due_date', 'invoices.status as status', 'invoices.invoice_id as invoice_id')
            ->selectRaw('sum((invoice_products.price * invoice_products.quantity) - invoice_products.discount) as price')
            ->selectRaw('sum((invoice_payments.amount)) as pay_price')
            ->selectRaw('(SELECT SUM((price * quantity - discount) * (taxes.rate / 100)) FROM invoice_products
             LEFT JOIN taxes ON FIND_IN_SET(taxes.id, invoice_products.tax) > 0
             WHERE invoice_products.invoice_id = invoices.id) as total_tax')
            ->selectRaw('(SELECT SUM(credit_notes.amount) FROM credit_notes
             WHERE credit_notes.invoice = invoices.id) as credit_price')
            ->leftJoin('customers', 'customers.id', 'invoices.customer_id')
            ->leftJoin('invoice_payments', 'invoice_payments.invoice_id', 'invoices.id')
            ->leftJoin('invoice_products', 'invoice_products.invoice_id', 'invoices.id')
            ->where('invoices.created_by', creatorId())
            ->where('invoices.workspace', getActiveWorkSpace())
            ->where('invoices.issue_date', '>=', $start)
            ->where('invoices.issue_date', '<=', $end)
            ->groupBy('invoices.invoice_id')
            ->get()
            ->toArray();

        $agingSummaries = [];

        $today = date("Y-m-d");
        foreach ($agingSummary as $item) {
            $name = $item["name"];
            $price = floatval(($item["price"] + $item['total_tax']) - ($item['pay_price'] + $item['credit_price']));
            $dueDate = $item["due_date"];

            if (!isset($agingSummaries[$name])) {
                $agingSummaries[$name] = [
                    'current' => 0.0,
                    "1_15_days" => 0.0,
                    "16_30_days" => 0.0,
                    "31_45_days" => 0.0,
                    "greater_than_45_days" => 0.0,
                    "total_due" => 0.0,
                ];
            }

            $daysDifference = date_diff(date_create($dueDate), date_create($today));
            $daysDifference = $daysDifference->format("%R%a");

            if ($daysDifference <= 0) {
                $agingSummaries[$name]["current"] += $price;
            } elseif ($daysDifference >= 1 && $daysDifference <= 15) {
                $agingSummaries[$name]["1_15_days"] += $price;
            } elseif ($daysDifference >= 16 && $daysDifference <= 30) {
                $agingSummaries[$name]["16_30_days"] += $price;
            } elseif ($daysDifference >= 31 && $daysDifference <= 45) {
                $agingSummaries[$name]["31_45_days"] += $price;
            } elseif ($daysDifference > 45) {
                $agingSummaries[$name]["greater_than_45_days"] += $price;
            }

            $agingSummaries[$name]["total_due"] += $price;
        }

        $currents = [];
        $days1to15 = [];
        $days16to30 = [];
        $days31to45 = [];
        $moreThan45 = [];

        foreach ($agingSummary as $item) {
            $dueDate = $item["due_date"];
            $price = floatval($item["price"]);
            $total_tax = floatval($item["total_tax"]);
            $credit_price = floatval($item["credit_price"]);
            $payPrice = $item["pay_price"] ? floatval($item["pay_price"]) : 0;

            $daysDifference = date_diff(date_create($dueDate), date_create($today));
            $daysDifference = $daysDifference->format("%R%a");
            $balanceDue = ($price + $total_tax) - ($payPrice + $credit_price);
            $totalPrice = $price + $total_tax;
            if ($daysDifference <= 0) {
                $item["total_price"] = $totalPrice;
                $item["balance_due"] = $balanceDue;
                $currents[] = $item;
            } elseif ($daysDifference >= 1 && $daysDifference <= 15) {
                $item["total_price"] = $totalPrice;
                $item["balance_due"] = $balanceDue;
                $item['age'] = intval(str_replace(array('+', '-'), '', $daysDifference));
                $days1to15[] = $item;
            } elseif ($daysDifference >= 16 && $daysDifference <= 30) {
                $item["total_price"] = $totalPrice;
                $item["balance_due"] = $balanceDue;
                $item['age'] = intval(str_replace(array('+', '-'), '', $daysDifference));
                $days16to30[] = $item;
            } elseif ($daysDifference >= 31 && $daysDifference <= 45) {
                $item["total_price"] = $totalPrice;
                $item["balance_due"] = $balanceDue;
                $item['age'] = intval(str_replace(array('+', '-'), '', $daysDifference));
                $days31to45[] = $item;
            } else {
                $item["total_price"] = $totalPrice;
                $item["balance_due"] = $balanceDue;
                $item['age'] = intval(str_replace(array('+', '-'), '', $daysDifference));
                $moreThan45[] = $item;
            }
        }

        $filter['startDateRange'] = $start;
        $filter['endDateRange'] = $end;

        return view('doubleentry::report.receivable_report', compact('filter', 'receivableCustomers', 'receivableSummaries', 'receivableDetails', 'agingSummaries', 'currents', 'days1to15', 'days16to30', 'days31to45', 'moreThan45'));
    }

    public function ReceivablesPrint(Request $request)
    {

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
        } else {
            $start = date('Y-01-01');
            $end = date('Y-m-d', strtotime('+1 day'));
        }
        $receivableCustomers = Invoice::select('customers.name')
            ->selectRaw('sum((invoice_products.price * invoice_products.quantity) - invoice_products.discount) as price')
            ->selectRaw('sum((invoice_payments.amount)) as pay_price')
            ->selectRaw('(SELECT SUM((price * quantity - discount) * (taxes.rate / 100)) FROM invoice_products
             LEFT JOIN taxes ON FIND_IN_SET(taxes.id, invoice_products.tax) > 0
             WHERE invoice_products.invoice_id = invoices.id) as total_tax')
            ->selectRaw('(SELECT SUM(credit_notes.amount) FROM credit_notes
             WHERE credit_notes.invoice = invoices.id) as credit_price')
            ->leftJoin('customers', 'customers.id', 'invoices.customer_id')
            ->leftJoin('invoice_payments', 'invoice_payments.invoice_id', 'invoices.id')
            ->leftJoin('invoice_products', 'invoice_products.invoice_id', 'invoices.id')
            ->where('invoices.created_by', creatorId())
            ->where('invoices.workspace', getActiveWorkSpace())
            ->where('invoices.issue_date', '>=', $start)
            ->where('invoices.issue_date', '<=', $end)
            ->groupBy('invoices.invoice_id')
            ->get()
            ->toArray();


        $receivableSummariesInvoice = Invoice::select('customers.name')
            ->selectRaw('(invoices.invoice_id) as invoice')
            ->selectRaw('sum((invoice_products.price * invoice_products.quantity) - invoice_products.discount) as price')
            ->selectRaw('sum((invoice_payments.amount)) as pay_price')
            ->selectRaw('(SELECT SUM((price * quantity - discount) * (taxes.rate / 100)) FROM invoice_products
             LEFT JOIN taxes ON FIND_IN_SET(taxes.id, invoice_products.tax) > 0
             WHERE invoice_products.invoice_id = invoices.id) as total_tax')
            ->selectRaw('invoices.issue_date as issue_date')
            ->selectRaw('invoices.status as status')
            ->leftJoin('customers', 'customers.id', 'invoices.customer_id')
            ->leftJoin('invoice_payments', 'invoice_payments.invoice_id', 'invoices.id')
            ->leftJoin('invoice_products', 'invoice_products.invoice_id', 'invoices.id')
            ->where('invoices.created_by', creatorId())
            ->where('invoices.workspace', getActiveWorkSpace())
            ->where('invoices.issue_date', '>=', $start)
            ->where('invoices.issue_date', '<=', $end)
            ->groupBy('invoices.invoice_id')
            ->get()
            ->toArray();

        $receivableSummariesCredit = \Modules\Account\Entities\CreditNote::select('customers.name')
            ->selectRaw('null as invoice')
            ->selectRaw('(credit_notes.amount) as price')
            ->selectRaw('0 as pay_price')
            ->selectRaw('0 as total_tax')
            ->selectRaw('credit_notes.date as issue_date')
            ->selectRaw('5 as status')
            ->leftJoin('customers', 'customers.id', 'credit_notes.customer')
            ->leftJoin('invoice_products', 'invoice_products.invoice_id', 'credit_notes.invoice')
            ->leftJoin('invoices', 'invoices.id', 'credit_notes.invoice')
            ->where('invoices.created_by', creatorId())
            ->where('invoices.workspace', getActiveWorkSpace())
            ->where('credit_notes.date', '>=', $start)
            ->where('credit_notes.date', '<=', $end)
            ->groupBy('credit_notes.id')
            ->get()
            ->toArray();


        $receivableSummaries = (array_merge($receivableSummariesCredit, $receivableSummariesInvoice));

        $receivableDetailsInvoice = Invoice::select('customers.name')
            ->selectRaw('(invoices.invoice_id) as invoice')
            ->selectRaw('sum(invoice_products.price) as price')
            ->selectRaw('(invoice_products.quantity) as quantity')
            ->selectRaw('(product_services.name) as product_name')
            ->selectRaw('invoices.issue_date as issue_date')
            ->selectRaw('invoices.status as status')
            ->leftJoin('customers', 'customers.id', 'invoices.customer_id')
            ->leftJoin('invoice_products', 'invoice_products.invoice_id', 'invoices.id')
            ->leftJoin('product_services', 'product_services.id', 'invoice_products.product_id')
            ->where('invoices.created_by', creatorId())
            ->where('invoices.workspace', getActiveWorkSpace())
            ->where('invoices.issue_date', '>=', $start)
            ->where('invoices.issue_date', '<=', $end)
            ->groupBy('invoices.invoice_id', 'product_services.name')
            ->get()
            ->toArray();

        $receivableDetailsCredit = \Modules\Account\Entities\CreditNote::select('customers.name')
            ->selectRaw('null as invoice')
            ->selectRaw('(credit_notes.id) as invoices')
            ->selectRaw('(credit_notes.amount) as price')
            ->selectRaw('(product_services.name) as product_name')
            ->selectRaw('credit_notes.date as issue_date')
            ->selectRaw('5 as status')
            ->leftJoin('customers', 'customers.id', 'credit_notes.customer')
            ->leftJoin('invoice_products', 'invoice_products.invoice_id', 'credit_notes.invoice')
            ->leftJoin('product_services', 'product_services.id', 'invoice_products.product_id')
            ->leftJoin('invoices', 'invoices.id', 'credit_notes.invoice')
            ->where('invoices.created_by', creatorId())
            ->where('invoices.workspace', getActiveWorkSpace())
            ->where('credit_notes.date', '>=', $start)
            ->where('credit_notes.date', '<=', $end)
            ->groupBy('credit_notes.id', 'product_services.name')
            ->get()
            ->toArray();

        $mergedArray = [];
        foreach ($receivableDetailsCredit as $item) {
            $invoices = $item["invoices"];

            if (!isset($mergedArray[$invoices])) {
                $mergedArray[$invoices] = [
                    "name" => $item["name"],
                    "invoice" => $item["invoice"],
                    "invoices" => $invoices,
                    "price" => $item["price"],
                    "quantity" => 0,
                    "product_name" => "",
                    "issue_date" => "",
                    "status" => 0,
                ];
            }

            if (!strstr($mergedArray[$invoices]["product_name"], $item["product_name"])) {
                if ($mergedArray[$invoices]["product_name"] !== "") {
                    $mergedArray[$invoices]["product_name"] .= ", ";
                }
                $mergedArray[$invoices]["product_name"] .= $item["product_name"];
            }

            $mergedArray[$invoices]["issue_date"] = $item["issue_date"];
            $mergedArray[$invoices]["status"] = $item["status"];
        }

        $receivableDetailsCredits = array_values($mergedArray);

        $receivableDetails = (array_merge($receivableDetailsInvoice, $receivableDetailsCredits));

        $agingSummary = Invoice::select('customers.name', 'invoices.due_date as due_date', 'invoices.status as status', 'invoices.invoice_id as invoice_id')
            ->selectRaw('sum((invoice_products.price * invoice_products.quantity) - invoice_products.discount) as price')
            ->selectRaw('sum((invoice_payments.amount)) as pay_price')
            ->selectRaw('(SELECT SUM((price * quantity - discount) * (taxes.rate / 100)) FROM invoice_products
             LEFT JOIN taxes ON FIND_IN_SET(taxes.id, invoice_products.tax) > 0
             WHERE invoice_products.invoice_id = invoices.id) as total_tax')
            ->selectRaw('(SELECT SUM(credit_notes.amount) FROM credit_notes
             WHERE credit_notes.invoice = invoices.id) as credit_price')
            ->leftJoin('customers', 'customers.id', 'invoices.customer_id')
            ->leftJoin('invoice_payments', 'invoice_payments.invoice_id', 'invoices.id')
            ->leftJoin('invoice_products', 'invoice_products.invoice_id', 'invoices.id')
            ->where('invoices.created_by', creatorId())
            ->where('invoices.workspace', getActiveWorkSpace())
            ->where('invoices.issue_date', '>=', $start)
            ->where('invoices.issue_date', '<=', $end)
            ->groupBy('invoices.invoice_id')
            ->get()
            ->toArray();

        $agingSummaries = [];

        $today = date("Y-m-d");
        foreach ($agingSummary as $item) {
            $name = $item["name"];
            $price = floatval(($item["price"] + $item['total_tax']) - ($item['pay_price'] + $item['credit_price']));
            $dueDate = $item["due_date"];

            if (!isset($agingSummaries[$name])) {
                $agingSummaries[$name] = [
                    'current' => 0.0,
                    "1_15_days" => 0.0,
                    "16_30_days" => 0.0,
                    "31_45_days" => 0.0,
                    "greater_than_45_days" => 0.0,
                    "total_due" => 0.0,
                ];
            }

            $daysDifference = date_diff(date_create($dueDate), date_create($today));
            $daysDifference = $daysDifference->format("%R%a");

            if ($daysDifference <= 0) {
                $agingSummaries[$name]["current"] += $price;
            } elseif ($daysDifference >= 1 && $daysDifference <= 15) {
                $agingSummaries[$name]["1_15_days"] += $price;
            } elseif ($daysDifference >= 16 && $daysDifference <= 30) {
                $agingSummaries[$name]["16_30_days"] += $price;
            } elseif ($daysDifference >= 31 && $daysDifference <= 45) {
                $agingSummaries[$name]["31_45_days"] += $price;
            } elseif ($daysDifference > 45) {
                $agingSummaries[$name]["greater_than_45_days"] += $price;
            }

            $agingSummaries[$name]["total_due"] += $price;
        }

        $currents = [];
        $days1to15 = [];
        $days16to30 = [];
        $days31to45 = [];
        $moreThan45 = [];

        foreach ($agingSummary as $item) {
            $dueDate = $item["due_date"];
            $price = floatval($item["price"]);
            $total_tax = floatval($item["total_tax"]);
            $credit_price = floatval($item["credit_price"]);
            $payPrice = $item["pay_price"] ? floatval($item["pay_price"]) : 0;

            $daysDifference = date_diff(date_create($dueDate), date_create($today));
            $daysDifference = $daysDifference->format("%R%a");
            $balanceDue = ($price + $total_tax) - ($payPrice + $credit_price);
            $totalPrice = $price + $total_tax;
            if ($daysDifference <= 0) {
                $item["total_price"] = $totalPrice;
                $item["balance_due"] = $balanceDue;
                $currents[] = $item;
            } elseif ($daysDifference >= 1 && $daysDifference <= 15) {
                $item["total_price"] = $totalPrice;
                $item["balance_due"] = $balanceDue;
                $item['age'] = intval(str_replace(array('+', '-'), '', $daysDifference));
                $days1to15[] = $item;
            } elseif ($daysDifference >= 16 && $daysDifference <= 30) {
                $item["total_price"] = $totalPrice;
                $item["balance_due"] = $balanceDue;
                $item['age'] = intval(str_replace(array('+', '-'), '', $daysDifference));
                $days16to30[] = $item;
            } elseif ($daysDifference >= 31 && $daysDifference <= 45) {
                $item["total_price"] = $totalPrice;
                $item["balance_due"] = $balanceDue;
                $item['age'] = intval(str_replace(array('+', '-'), '', $daysDifference));
                $days31to45[] = $item;
            } else {
                $item["total_price"] = $totalPrice;
                $item["balance_due"] = $balanceDue;
                $item['age'] = intval(str_replace(array('+', '-'), '', $daysDifference));
                $moreThan45[] = $item;
            }
        }

        $filter['startDateRange'] = $start;
        $filter['endDateRange'] = $end;
        $reportName = $request->report;


        return view('doubleentry::report.receivable_report_receipt', compact('filter', 'receivableCustomers', 'receivableSummaries', 'moreThan45', 'days31to45', 'days16to30', 'days1to15', 'currents',
            'reportName', 'receivableDetails', 'agingSummaries'));
    }

    public function PayablesReport(Request $request)
    {
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
        } else {
            $start = date('Y-01-01');
            $end = date('Y-m-d', strtotime('+1 day'));
        }

        $payableVendors = Bill::select('vendors.name')
            ->selectRaw('sum((bill_products.price * bill_products.quantity) - bill_products.discount) as price')
            ->selectRaw('sum((bill_payments.amount)) as pay_price')
            ->selectRaw('(SELECT SUM((price * quantity - discount) * (taxes.rate / 100)) FROM bill_products
         LEFT JOIN taxes ON FIND_IN_SET(taxes.id, bill_products.tax) > 0
         WHERE bill_products.bill_id = bills.id) as total_tax')
            ->selectRaw('(SELECT SUM(debit_notes.amount) FROM debit_notes
         WHERE debit_notes.bill = bills.id) as debit_price')
            ->leftJoin('vendors', 'vendors.id', 'bills.vendor_id')
            ->leftJoin('bill_payments', 'bill_payments.bill_id', 'bills.id')
            ->leftJoin('bill_products', 'bill_products.bill_id', 'bills.id')
            ->where('bills.created_by', creatorId())
            ->where('bills.workspace', getActiveWorkSpace())
//            ->whereNotIn('bills.user_type', ['employee', 'customer'])
            ->where('bills.bill_date', '>=', $start)
            ->where('bills.bill_date', '<=', $end)
            ->groupBy('bills.bill_id')
            ->get()
            ->toArray();

        $payableSummariesBill = Bill::select('vendors.name')
            ->selectRaw('(bills.bill_id) as bill')
//            ->selectRaw('(bills.type) as type')
            ->selectRaw('sum((bill_products.price * bill_products.quantity) - bill_products.discount) as price')
            ->selectRaw('sum((bill_payments.amount)) as pay_price')
            ->selectRaw('(SELECT SUM((price * quantity - discount) * (taxes.rate / 100)) FROM bill_products
         LEFT JOIN taxes ON FIND_IN_SET(taxes.id, bill_products.tax) > 0
         WHERE bill_products.bill_id = bills.id) as total_tax')
            ->selectRaw('bills.bill_date as bill_date')
            ->selectRaw('bills.status as status')
            ->leftJoin('vendors', 'vendors.id', 'bills.vendor_id')
            ->leftJoin('bill_payments', 'bill_payments.bill_id', 'bills.id')
            ->leftJoin('bill_products', 'bill_products.bill_id', 'bills.id')
            ->where('bills.created_by', creatorId())
            ->where('bills.workspace', getActiveWorkSpace())

            //            ->whereNotIn('bills.user_type', ['employee', 'customer'])
            ->where('bills.bill_date', '>=', $start)
            ->where('bills.bill_date', '<=', $end)
            ->groupBy('bills.id')
            ->get()
            ->toArray();

        $payableSummariesDebit = \Modules\Account\Entities\DebitNote::select('vendors.name')
            ->selectRaw('null as bill')
            ->selectRaw('debit_notes.amount as price')
            ->selectRaw('0 as pay_price')
            ->selectRaw('0 as total_tax')
            ->selectRaw('debit_notes.date as bill_date')
            ->selectRaw('5 as status')
            ->leftJoin('vendors', 'vendors.id', 'debit_notes.vendor')
            ->leftJoin('bill_products', 'bill_products.bill_id', 'debit_notes.bill')
            ->leftJoin('bills', 'bills.id', 'debit_notes.bill')
            ->where('bills.created_by', creatorId())
            ->where('bills.workspace', getActiveWorkSpace())
            ->where('debit_notes.date', '>=', $start)
            ->where('debit_notes.date', '<=', $end)
            ->groupBy('debit_notes.id')
            ->get()
            ->toArray();

        $payableSummaries = (array_merge($payableSummariesDebit, $payableSummariesBill));

        $payableDetailsBill = Bill::select('vendors.name')
            ->selectRaw('(bills.bill_id) as bill')
//            ->selectRaw('(bills.type) as type')
            ->selectRaw('sum(bill_products.price) as price')
            ->selectRaw('(bill_products.quantity) as quantity')
            ->selectRaw('(product_services.name) as product_name')
            ->selectRaw('bills.bill_date as bill_date')
            ->selectRaw('bills.status as status')
            ->leftJoin('vendors', 'vendors.id', 'bills.vendor_id')
            ->leftJoin('bill_products', 'bill_products.bill_id', 'bills.id')
            ->leftJoin('product_services', 'product_services.id', 'bill_products.product_id')
            ->where('bills.created_by', creatorId())
            ->where('bills.workspace', getActiveWorkSpace())
            //            ->whereNotIn('bills.user_type', ['employee', 'customer'])
            ->where('bills.bill_date', '>=', $start)
            ->where('bills.bill_date', '<=', $end)
            ->groupBy('bills.bill_id', 'product_services.name')
            ->get()
            ->toArray();

        $payableDetailsDebit = \Modules\Account\Entities\DebitNote::select('vendors.name')
            ->selectRaw('null as bill')
            ->selectRaw('(debit_notes.id) as bills')
            ->selectRaw('(debit_notes.amount) as price')
            ->selectRaw('(product_services.name) as product_name')
            ->selectRaw('debit_notes.date as bill_date')
            ->selectRaw('5 as status')
            ->leftJoin('vendors', 'vendors.id', 'debit_notes.vendor')
            ->leftJoin('bill_products', 'bill_products.bill_id', 'debit_notes.bill')
            ->leftJoin('product_services', 'product_services.id', 'bill_products.product_id')
            ->leftJoin('bills', 'bills.id', 'debit_notes.bill')
            ->where('bills.created_by', creatorId())
            ->where('bills.workspace', getActiveWorkSpace())
            ->where('debit_notes.date', '>=', $start)
            ->where('debit_notes.date', '<=', $end)
            ->groupBy('debit_notes.id', 'product_services.name')
            ->get()
            ->toArray();

        $mergedArray = [];
        foreach ($payableDetailsDebit as $item) {
            $invoices = $item["bills"];

            if (!isset($mergedArray[$invoices])) {
                $mergedArray[$invoices] = [
                    "name" => $item["name"],
                    "bill" => $item["bill"],
                    "bills" => $invoices,
                    "price" => $item["price"],
                    "quantity" => 0,
                    "product_name" => "",
                    "bill_date" => "",
                    "status" => 0,
                ];
            }

            if (!strstr($mergedArray[$invoices]["product_name"], $item["product_name"])) {
                if ($mergedArray[$invoices]["product_name"] !== "") {
                    $mergedArray[$invoices]["product_name"] .= ", ";
                }
                $mergedArray[$invoices]["product_name"] .= $item["product_name"];
            }

            $mergedArray[$invoices]["bill_date"] = $item["bill_date"];
            $mergedArray[$invoices]["status"] = $item["status"];
        }

        $payableDetailsDebits = array_values($mergedArray);

        $payableDetails = (array_merge($payableDetailsBill, $payableDetailsDebits));

        $filter['startDateRange'] = $start;
        $filter['endDateRange'] = $end;

        return view('doubleentry::report.payable_report', compact('filter', 'payableVendors', 'payableSummaries', 'payableDetails'));
    }

    public function PayablesPrint(Request $request)
    {

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
        } else {
            $start = date('Y-01-01');
            $end = date('Y-m-d', strtotime('+1 day'));
        }

        $payableVendors = Bill::select('vendors.name')
            ->selectRaw('sum((bill_products.price * bill_products.quantity) - bill_products.discount) as price')
            ->selectRaw('sum((bill_payments.amount)) as pay_price')
            ->selectRaw('(SELECT SUM((price * quantity - discount) * (taxes.rate / 100)) FROM bill_products
         LEFT JOIN taxes ON FIND_IN_SET(taxes.id, bill_products.tax) > 0
         WHERE bill_products.bill_id = bills.id) as total_tax')
            ->selectRaw('(SELECT SUM(debit_notes.amount) FROM debit_notes
         WHERE debit_notes.bill = bills.id) as debit_price')
            ->leftJoin('vendors', 'vendors.id', 'bills.vendor_id')
            ->leftJoin('bill_payments', 'bill_payments.bill_id', 'bills.id')
            ->leftJoin('bill_products', 'bill_products.bill_id', 'bills.id')
            ->where('bills.created_by', creatorId())
            ->where('bills.workspace', getActiveWorkSpace())
            //            ->whereNotIn('bills.user_type', ['employee', 'customer'])
            ->where('bills.bill_date', '>=', $start)
            ->where('bills.bill_date', '<=', $end)
            ->groupBy('bills.bill_id')
            ->get()
            ->toArray();


        $payableSummariesBill = Bill::select('vendors.name')
            ->selectRaw('(bills.bill_id) as bill')
//            ->selectRaw('(bills.type) as type')
            ->selectRaw('sum((bill_products.price * bill_products.quantity) - bill_products.discount) as price')
            ->selectRaw('sum((bill_payments.amount)) as pay_price')
            ->selectRaw('(SELECT SUM((price * quantity - discount) * (taxes.rate / 100)) FROM bill_products
         LEFT JOIN taxes ON FIND_IN_SET(taxes.id, bill_products.tax) > 0
         WHERE bill_products.bill_id = bills.id) as total_tax')
            ->selectRaw('bills.bill_date as bill_date')
            ->selectRaw('bills.status as status')
            ->leftJoin('vendors', 'vendors.id', 'bills.vendor_id')
            ->leftJoin('bill_payments', 'bill_payments.bill_id', 'bills.id')
            ->leftJoin('bill_products', 'bill_products.bill_id', 'bills.id')
            ->where('bills.created_by', creatorId())
            ->where('bills.workspace', getActiveWorkSpace())

            //            ->whereNotIn('bills.user_type', ['employee', 'customer'])
            ->where('bills.bill_date', '>=', $start)
            ->where('bills.bill_date', '<=', $end)
            ->groupBy('bills.id')
            ->get()
            ->toArray();

        $payableSummariesDebit = \Modules\Account\Entities\DebitNote::select('vendors.name')
            ->selectRaw('null as bill')
            ->selectRaw('debit_notes.amount as price')
            ->selectRaw('0 as pay_price')
            ->selectRaw('0 as total_tax')
            ->selectRaw('debit_notes.date as bill_date')
            ->selectRaw('5 as status')
            ->leftJoin('vendors', 'vendors.id', 'debit_notes.vendor')
            ->leftJoin('bill_products', 'bill_products.bill_id', 'debit_notes.bill')
            ->leftJoin('bills', 'bills.id', 'debit_notes.bill')
            ->where('bills.created_by', creatorId())
            ->where('bills.workspace', getActiveWorkSpace())
            ->where('debit_notes.date', '>=', $start)
            ->where('debit_notes.date', '<=', $end)
            ->groupBy('debit_notes.id')
            ->get()
            ->toArray();

        $payableSummaries = (array_merge($payableSummariesDebit, $payableSummariesBill));

        $payableDetailsBill = Bill::select('vendors.name')
            ->selectRaw('(bills.bill_id) as bill')
//            ->selectRaw('(bills.type) as type')
            ->selectRaw('sum(bill_products.price) as price')
            ->selectRaw('(bill_products.quantity) as quantity')
            ->selectRaw('(product_services.name) as product_name')
            ->selectRaw('bills.bill_date as bill_date')
            ->selectRaw('bills.status as status')
            ->leftJoin('vendors', 'vendors.id', 'bills.vendor_id')
            ->leftJoin('bill_products', 'bill_products.bill_id', 'bills.id')
            ->leftJoin('product_services', 'product_services.id', 'bill_products.product_id')
            ->where('bills.created_by', creatorId())
            ->where('bills.workspace', getActiveWorkSpace())

            //            ->whereNotIn('bills.user_type', ['employee', 'customer'])
            ->where('bills.bill_date', '>=', $start)
            ->where('bills.bill_date', '<=', $end)
            ->groupBy('bills.bill_id', 'product_services.name')
            ->get()
            ->toArray();


        $payableDetailsDebit = \Modules\Account\Entities\DebitNote::select('vendors.name')
            ->selectRaw('null as bill')
            ->selectRaw('(debit_notes.id) as bills')
            ->selectRaw('(debit_notes.amount) as price')
            ->selectRaw('(product_services.name) as product_name')
            ->selectRaw('debit_notes.date as bill_date')
            ->selectRaw('5 as status')
            ->leftJoin('vendors', 'vendors.id', 'debit_notes.vendor')
            ->leftJoin('bill_products', 'bill_products.bill_id', 'debit_notes.bill')
            ->leftJoin('product_services', 'product_services.id', 'bill_products.product_id')
            ->leftJoin('bills', 'bills.id', 'debit_notes.bill')
            ->where('bills.created_by', creatorId())
            ->where('bills.workspace', getActiveWorkSpace())
            ->where('debit_notes.date', '>=', $start)
            ->where('debit_notes.date', '<=', $end)
            ->groupBy('debit_notes.id', 'product_services.name')
            ->get()
            ->toArray();

        $mergedArray = [];
        foreach ($payableDetailsDebit as $item) {
            $invoices = $item["bills"];

            if (!isset($mergedArray[$invoices])) {
                $mergedArray[$invoices] = [
                    "name" => $item["name"],
                    "bill" => $item["bill"],
                    "bills" => $invoices,
                    "price" => $item["price"],
                    "quantity" => 0,
                    "product_name" => "",
                    "bill_date" => "",
                    "status" => 0,
                ];
            }

            if (!strstr($mergedArray[$invoices]["product_name"], $item["product_name"])) {
                if ($mergedArray[$invoices]["product_name"] !== "") {
                    $mergedArray[$invoices]["product_name"] .= ", ";
                }
                $mergedArray[$invoices]["product_name"] .= $item["product_name"];
            }

            $mergedArray[$invoices]["bill_date"] = $item["bill_date"];
            $mergedArray[$invoices]["status"] = $item["status"];
        }

        $payableDetailsDebits = array_values($mergedArray);

        $payableDetails = (array_merge($payableDetailsBill, $payableDetailsDebits));

        $filter['startDateRange'] = $start;
        $filter['endDateRange'] = $end;
        $reportName = $request->report;

        return view('doubleentry::report.payable_report_receipt', compact('filter', 'reportName', 'payableVendors', 'payableSummaries', 'payableDetails'));

    }


}
