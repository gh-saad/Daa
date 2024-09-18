<?php

namespace App\Http\Controllers;

use App\Events\CreateInvoice;
use App\Events\CreatePaymentInvoice;
use App\Events\DestroyInvoice;
use App\Events\DuplicateInvoice;
use App\Events\PaymentDestroyInvoice;
use App\Events\PaymentReminderInvoice;
use App\Events\ResentInvoice;
use App\Events\SentInvoice;
use App\Events\UpdateInvoice;
use App\Models\BankTransferPayment;
use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\InvoiceAttechment;
use App\Models\InvoicePayment;
use App\Models\InvoiceProduct;
use App\Models\Proposal;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Modules\ProductService\Entities\ProductService;
use Rawilk\Settings\Support\Context;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        if(Auth::user()->can('invoice manage'))
        {
            $customer = User::where('workspace_id', '=',getActiveWorkSpace())->where('type','Client')->get()->pluck('name', 'id');

            $status = Invoice::$statues;

            $query = Invoice::where('workspace', '=', getActiveWorkSpace());
            if(!empty($request->customer))
            {

                $query->where('user_id', '=', $request->customer);
            }
            if(!empty($request->issue_date))
            {
                $date_range = explode('to', $request->issue_date);
                if(count($date_range) == 2)
                {
                    $query->whereBetween('issue_date',$date_range);
                }
                else
                {
                    $query->where('issue_date',$date_range[0]);
                }
            }
            if(!empty($request->status))
            {
                $query->where('status', $request->status);
            }
            $invoices = $query->get();
            return view('invoice.index', compact('invoices', 'customer', 'status'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function Grid(Request $request)
    {
        if(Auth::user()->can('invoice manage'))
        {
            $customer = User::where('workspace_id', '=',getActiveWorkSpace())->where('type','Client')->get()->pluck('name', 'id');

            $status = Invoice::$statues;

            $query = Invoice::where('workspace', '=', getActiveWorkSpace());
            if(!empty($request->customer))
            {

                $query->where('user_id', '=', $request->customer);
            }
            if(!empty($request->issue_date))
            {
                $date_range = explode('to', $request->issue_date);
                if(count($date_range) == 2)
                {
                    $query->whereBetween('issue_date',$date_range);
                }
                else
                {
                    $query->where('issue_date',$date_range[0]);
                }
            }
            if(!empty($request->status))
            {
                $query->where('status', $request->status);
            }
            $invoices = $query->get();
            return view('invoice.grid', compact('invoices', 'customer', 'status'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create($customerId)
    {
        if(module_is_active('ProductService'))
        {
            if(Auth::user()->can('invoice create'))
            {
                $invoice_number = Invoice::invoiceNumberFormat($this->invoiceNumber());
                
                $category = [];
                $projects = [];
                $taxs = [];
                $customers_array = [];
                $company_product_array = [];
                $company_service_array = [];
                if(module_is_active('Account'))
                {
                    if ($customerId > 0) {
                        $temp_cm = \Modules\Account\Entities\Customer::where('customer_id',$customerId)->first();
                        if($temp_cm)
                        {
                            $customerId = $temp_cm->user_id;
                        }
                        else
                        {
                            return redirect()->back()->with('error', __('Something went wrong please try again!'));
                        }
                    }
                    $category = \Modules\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->where('type', 1)->get()->pluck('name', 'id');
                    
                    // Fetch customers based on active workspace
                    $customers = \Modules\Account\Entities\Customer::where('workspace', '=', getActiveWorkSpace())->get();
                    
                    foreach ($customers as $customer) {
                        // combine both values
                        $customer_detail =  \Modules\Account\Entities\Customer::customerNumberFormat($customer->customer_id) . ' ' . $customer->name;
                    
                        // Add customer detail to customers_array with customer's ID as key
                        $customers_array[$customer->id] = $customer_detail;
                    }

                }

                if(module_is_active('ProductService'))
                {
                    $company_products =  \Modules\ProductService\Entities\ProductService::where('workspace_id',getActiveWorkSpace())->where('type', '=', 'product')->where('purchased_status', '=', 'Paid')->where('vehicle_status', '=', 'Yard')->get();
                    
                    foreach ($company_products as $ps) {
                        // combine both values
                        $ps_detail = $ps->sku . ' - ' . $ps->name;
                    
                        // Add vendor detail to company_product_array with ps's ID as key
                        $company_product_array[$ps->id] = $ps_detail;
                    }   
                    
                    $product_type =\Modules\ProductService\Entities\ProductService::$product_type;
                    
                    $company_services =  \Modules\ProductService\Entities\ProductService::where('workspace_id',getActiveWorkSpace())->where('type', '=', 'service')->get();
                    
                    foreach ($company_services as $ss) {
                        // combine both values
                        $ss_detail = $ss->sku . ' - ' . $ss->name;
                    
                        // Add vendor detail to company_service_array with ss's ID as key
                        $company_service_array[$ss->id] = $ss_detail;
                    }   
                    
                }

                if(module_is_active('Taskly'))
                {
                    if(module_is_active('ProductService'))
                    {
                        $taxs = \Modules\ProductService\Entities\Tax::where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
                    }
                    $projects = \Modules\Taskly\Entities\Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', Auth::user()->id)->where('workspace', getActiveWorkSpace())->projectonly()->get()->pluck('name', 'id');
                }
                if(module_is_active('CustomField')){
                    $customFields =  \Modules\CustomField\Entities\CustomField::where('workspace_id',getActiveWorkSpace())->where('module', '=', 'Base')->where('sub_module','Invoice')->get();
                }else{
                    $customFields = null;
                }
                return view('invoice.create', compact('customers', 'customers_array', 'company_product_array', 'company_service_array', 'product_type', 'invoice_number','projects','taxs','category','customerId','customFields'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->route('invoice.index')->with('error', __('Please Enable Product & Service Module'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('invoice create'))
        {
            // important check required
            foreach($request->items as $verify_item){
                if($verify_item == "null"){
                    return redirect()->back()->with('error', __('No product or service was selected for one of the items, kindly review your form and try again.'));
                }
            }
        
            $validator = \Validator::make(
                $request->all(), [
                                'customer_id' => 'required',
                                'issue_date' => 'required',
                                'due_date' => 'required',
                                'category_id' => 'required',
                                'items' => 'required',

                            ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if(!empty($request->customer_id)){
                $customer = \Modules\Account\Entities\Customer::find($request->customer_id);
            }

            $invoice                 = new Invoice();
            $invoice->invoice_id     = $this->invoiceNumber();
            $invoice->user_id        = !empty($customer) ? $customer->user_id : null;
            $invoice->customer_id    = $customer->id;
            $invoice->status         = 0;
            $invoice->invoice_module = 'account';
            $invoice->issue_date     = $request->issue_date;
            $invoice->due_date       = $request->due_date;
            $invoice->category_id    = $request->category_id;
            $invoice->workspace      = getActiveWorkSpace();
            $invoice->created_by     = Auth::user()->id;
            $invoice->save();

            Invoice::starting_number( $invoice->invoice_id + 1, 'invoice');
            if(module_is_active('CustomField'))
            {
                \Modules\CustomField\Entities\CustomField::saveData($invoice, $request->customField);
            }

            event(new CreateInvoice($request,$invoice));
            
            $products = $request->items;
            for($i = 0; $i < count($products); $i++)
            {
                $invoiceProduct                 = new InvoiceProduct();
                $invoiceProduct->invoice_id     = $invoice->id;
                $invoiceProduct->product_type   = $request->item_types[$i];
                $invoiceProduct->product_id     = $products[$i];
                $invoiceProduct->quantity       = 1;
                $invoiceProduct->currency       = company_setting("defult_currancy");
                $invoiceProduct->discount       = $request->item_discounts[$i];
                $invoiceProduct->price          = $request->item_prices[$i];
                $invoiceProduct->description    = $request->item_desc[$i];

                // Tax handling logic
                if (module_is_active('ProductService')) {
                    if (isset($request->item_taxes[$i]) && !empty($request->item_taxes[$i])) {
                        // Generate a random name for the tax entry
                        $random_tax_name = 'tax-' . Str::random(5);
                        
                        // Create a new tax entry with the generated name and provided rate
                        $tax_object_to_create = new \Modules\ProductService\Entities\Tax();
                        $tax_object_to_create->name = $random_tax_name;
                        $tax_object_to_create->rate = $request->item_taxes[$i];
                        $tax_object_to_create->created_by = \Auth::user()->id;
                        $tax_object_to_create->workspace_id = getActiveWorkSpace();
                        $tax_object_to_create->save();
            
                        // Get the ID of this newly added tax entry
                        $new_tax_id = $tax_object_to_create->id;
            
                        // Assign the tax ID to the invoice product tax entry
                        $invoiceProduct->tax = $new_tax_id;
                    } else {
                        // If no tax rate is provided, assign a default tax ID
                        $invoiceProduct->tax = 1;
                    }
                } else {
                    // If the ProductService module is not active, assign a default tax ID
                    $invoiceProduct->tax = 1;
                }
            
                $invoiceProduct->save();

                if(module_is_active('ProductService'))
                {
                    Invoice::total_quantity('minus',$invoiceProduct->quantity,$invoiceProduct->product_id);
                }

                if(module_is_active('Account'))
                {
                    //Product Stock Report
                    $type='invoice';
                    $type_id = $invoice->id;
                    \Modules\Account\Entities\StockReport::where('type','=','invoice')->where('type_id' ,'=', $invoice->id)->delete();
                    $description = $invoiceProduct->quantity.'  '.__(' quantity sold in invoice').' '. Invoice::invoiceNumberFormat($invoice->invoice_id);
                    \Modules\Account\Entities\AccountUtility::addProductStock( $products[$i], $invoiceProduct->quantity, $type, $description, $type_id);
                }

            }
            
            return redirect()->route('invoice.index', $invoice->id)->with('success', __('Invoice successfully created.'));

        }
    }

    public function show($e_id)
    {
        if(Auth::user()->can('invoice show'))
        {
            try {
                $id       = Crypt::decrypt($e_id);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Invoice Not Found.'));
            }
            $invoice = Invoice::find($id);
            if($invoice)
            {
                $bank_transfer_payments = BankTransferPayment::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->where('type','invoice')->where('request',$invoice->id)->get();
                if($invoice->workspace == getActiveWorkSpace())
                {
                    $invoicePayment = InvoicePayment::where('invoice_id', $invoice->id)->first();
                    $invoice_attachment = InvoiceAttechment::where('invoice_id', $invoice->id)->get();
                    if(module_is_active('Account'))
                    {
                        $customer = \Modules\Account\Entities\Customer::where('user_id',$invoice->user_id)->where('workspace',getActiveWorkSpace())->first();
                    }
                    else
                    {
                        $customer = $invoice->customer;
                    }
                    if(module_is_active('CustomField')){
                        $invoice->customField = \Modules\CustomField\Entities\CustomField::getData($invoice, 'Base','Invoice');
                        $customFields      = \Modules\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'Base')->where('sub_module','Invoice')->get();
                    }else{
                        $customFields = null;
                    }
                    $iteams   = $invoice->items;

                    return view('invoice.view', compact('invoice', 'customer', 'iteams', 'invoicePayment','customFields','bank_transfer_payments','invoice_attachment'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('This invoice is deleted.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($e_id)
    {
        if(module_is_active('ProductService'))
        {
            if(Auth::user()->can('invoice edit'))
            {
                try {
                    $id       = Crypt::decrypt($e_id);
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', __('Invoice Not Found.'));
                }
                $invoice = Invoice::find($id);

                $customers_array = [];
                $company_product_array = [];
                $company_products_selected_array = [];
                $company_service_array = [];
                $category = [];
                $projects = [];
                $taxs = [];

                $invoice_number = Invoice::invoiceNumberFormat($invoice->invoice_id);

                if(module_is_active('Account'))
                {
                    $category = \Modules\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->where('type', 1)->get()->pluck('name', 'id');
                    
                    // Fetch customers based on active workspace
                    $customers = \Modules\Account\Entities\Customer::where('workspace', '=', getActiveWorkSpace())->get();
                    
                    foreach ($customers as $customer) {
                        // combine both values
                        $customer_detail =  \Modules\Account\Entities\Customer::customerNumberFormat($customer->customer_id) . ' ' . $customer->name;
                    
                        // Add customer detail to customers_array with customer's ID as key
                        $customers_array[$customer->id] = $customer_detail;
                    }

                }
                
                if(module_is_active('ProductService'))
                {
                    $taxs = \Modules\ProductService\Entities\Tax::where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
                    
                    $company_products =  \Modules\ProductService\Entities\ProductService::where('workspace_id',getActiveWorkSpace())->where('type', '=', 'product')->where('purchased_status', '=', 'Paid')->where('vehicle_status', '=', 'Yard')->get();
                    
                    foreach ($company_products as $ps) {
                        // combine both values
                        $ps_detail = $ps->sku . ' - ' . $ps->name;
                    
                        // Add vendor detail to company_product_array with ps's ID as key
                        $company_product_array[$ps->id] = $ps_detail;
                    }   

                    $company_products_selected = InvoiceProduct::where('invoice_id', '=', $invoice->id)->get();
                    foreach ($company_products_selected as $prs) {
                        $prs_product = \Modules\ProductService\Entities\ProductService::find($prs->product_id);
                        $tax = \Modules\ProductService\Entities\Tax::find($prs->tax);
                        $company_products_selected_array[$prs->id] = [
                            'product_id' => $prs_product->id,
                            'type' => $prs->product_type,
                            'name' => $prs_product->sku . ' - ' . $prs_product->name,
                            'desc' => $prs->description,
                            'price' => currency_conversion($prs->price, $prs->currency, company_setting('defult_currancy')),
                            'discount' => currency_conversion($prs->discount, $prs->currency, company_setting('defult_currancy')),
                            'tax' => $tax->rate,
                        ];
                    }

                    $product_type =\Modules\ProductService\Entities\ProductService::$product_type;
                    
                    $company_services =  \Modules\ProductService\Entities\ProductService::where('workspace_id',getActiveWorkSpace())->where('type', '=', 'service')->get();
                    
                    foreach ($company_services as $ss) {
                        // combine both values
                        $ss_detail = $ss->sku . ' - ' . $ss->name;
                    
                        // Add vendor detail to company_service_array with ss's ID as key
                        $company_service_array[$ss->id] = $ss_detail;
                    }   
                
                }

                if(module_is_active('Taskly'))
                {
                    $projects = \Modules\Taskly\Entities\Project::where('workspace', getActiveWorkSpace())->projectonly()->get()->pluck('name', 'id');
                }

                if(module_is_active('CustomField')){
                    $invoice->customField = \Modules\CustomField\Entities\CustomField::getData($invoice, 'Base','Invoice');
                    $customFields             = \Modules\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'Base')->where('sub_module','Invoice')->get();
                }else{
                    $customFields = null;
                }

                return view('invoice.edit', compact('customers', 'customers_array', 'company_product_array', 'company_service_array', 'company_products_selected_array','projects','taxs', 'invoice', 'invoice_number', 'category','customFields'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return redirect()->route('invoice.index')->with('error', __('Please Enable Product & Service Module'));
        }
    }

    public function update(Request $request, Invoice $invoice)
    {
        if(Auth::user()->can('invoice edit'))
        {
            if($invoice->workspace == getActiveWorkSpace())
            {
                // important check required
                foreach($request->items as $verify_item){
                    if($verify_item == "null"){
                        return redirect()->back()->with('error', __('No product or service was selected for one of the items, kindly review your form and try again.'));
                    }
                }
            
                $validator = \Validator::make(
                    $request->all(), [
                                    'customer_id' => 'required',
                                    'issue_date' => 'required',
                                    'due_date' => 'required',
                                    'category_id' => 'required',
                                    'items' => 'required',
    
                                ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
    
                    return redirect()->back()->with('error', $messages->first());
                }
    
                if(!empty($request->customer_id)){
                    $customer                  = \Modules\Account\Entities\Customer::find($request->customer_id);
                    $invoice->customer_id      = !empty($customer) ?  $customer->id : null;
                    $invoice->user_id          = !empty($customer)? $customer->user_id : null;
                }
    
                $invoice->issue_date     = $request->issue_date;
                $invoice->due_date       = $request->due_date;
                $invoice->invoice_module = 'account';
                $invoice->category_id    = $request->category_id;
                $invoice->save();

                if(module_is_active('CustomField'))
                {
                    \Modules\CustomField\Entities\CustomField::saveData($invoice, $request->customField);
                }
                
                event(new UpdateInvoice($request ,$invoice));

                // delete all products added by this invoice
                InvoiceProduct::where('invoice_id', '=', $invoice->id)->delete();
                \Modules\Account\Entities\StockReport::where('type','=','invoice')->where('type_id' ,'=', $invoice->id)->delete();

                // add using provided data
                $products = $request->items;
                for($i = 0; $i < count($products); $i++)
                {
                    $invoiceProduct                 = new InvoiceProduct();
                    $invoiceProduct->invoice_id     = $invoice->id;
                    $invoiceProduct->product_type   = $request->item_types[$i];
                    $invoiceProduct->product_id     = $products[$i];
                    $invoiceProduct->quantity       = 1;
                    $invoiceProduct->currency       = company_setting("defult_currancy");
                    $invoiceProduct->discount       = $request->item_discounts[$i];
                    $invoiceProduct->price          = $request->item_prices[$i];
                    $invoiceProduct->description    = $request->item_desc[$i];
    
                    // Tax handling logic
                    if (module_is_active('ProductService')) {
                        if (isset($request->item_taxes[$i]) && !empty($request->item_taxes[$i])) {
                            // Generate a random name for the tax entry
                            $random_tax_name = 'tax-' . Str::random(5);
                            
                            // Create a new tax entry with the generated name and provided rate
                            $tax_object_to_create = new \Modules\ProductService\Entities\Tax();
                            $tax_object_to_create->name = $random_tax_name;
                            $tax_object_to_create->rate = $request->item_taxes[$i];
                            $tax_object_to_create->created_by = \Auth::user()->id;
                            $tax_object_to_create->workspace_id = getActiveWorkSpace();
                            $tax_object_to_create->save();
                
                            // Get the ID of this newly added tax entry
                            $new_tax_id = $tax_object_to_create->id;
                
                            // Assign the tax ID to the invoice product tax entry
                            $invoiceProduct->tax = $new_tax_id;
                        } else {
                            // If no tax rate is provided, assign a default tax ID
                            $invoiceProduct->tax = 1;
                        }
                    } else {
                        // If the ProductService module is not active, assign a default tax ID
                        $invoiceProduct->tax = 1;
                    }
                
                    $invoiceProduct->save();
    
                    if(module_is_active('ProductService'))
                    {
                        Invoice::total_quantity('minus',$invoiceProduct->quantity,$invoiceProduct->product_id);
                    }
    
                    if(module_is_active('Account'))
                    {
                        //Product Stock Report
                        $type='invoice';
                        $type_id = $invoice->id;
                        $description = $invoiceProduct->quantity.'  '.__(' quantity sold in invoice').' '. Invoice::invoiceNumberFormat($invoice->invoice_id);
                        \Modules\Account\Entities\AccountUtility::addProductStock( $products[$i], $invoiceProduct->quantity, $type, $description, $type_id);
                    }        

                }

                // **************** i dont know what all of this is
                // else if($request->invoice_type == "project")
                // {
                //     $validator = \Validator::make(
                //         $request->all(), [
                //                         'customer_id' => 'required',
                //                         'issue_date' => 'required',
                //                         'due_date' => 'required',
                //                         'project' => 'required',
                //                         'tax_project' => 'required',
                //                         'items' => 'required',

                //                     ]
                //     );
                //     if($validator->fails())
                //     {
                //         $messages = $validator->getMessageBag();

                //         return redirect()->back()->with('error', $messages->first());
                //     }

                //     if(module_is_active('Account'))
                //     {
                //         $customer = \Modules\Account\Entities\Customer::where('user_id', '=', $request->customer_id)->first();
                //         $invoice->customer_id    = !empty($customer) ?  $customer->id : null;
                //     }
                //     if($request->invoice_type != $invoice->invoice_module)
                //     {
                //         InvoiceProduct::where('invoice_id', '=', $invoice->id)->delete();
                //     }

                //     $status = Invoice::$statues;
                //     $invoice->invoice_id     = $invoice->invoice_id;
                //     $invoice->user_id        = $request->customer_id;
                //     $invoice->issue_date     = $request->issue_date;
                //     $invoice->due_date       = $request->due_date;
                //     $invoice->invoice_module = 'taskly';
                //     $invoice->category_id    = $request->project;
                //     $invoice->save();

                //     $products = $request->items;
                //     if(module_is_active('CustomField'))
                //     {
                //         \Modules\CustomField\Entities\CustomField::saveData($invoice, $request->customField);
                //     }

                //     $project_tax = implode(',',$request->tax_project);
                //     for($i = 0; $i < count($products); $i++)
                //     {
                //         $invoiceProduct = InvoiceProduct::find($products[$i]['id']);
                //         if($invoiceProduct == null)
                //         {
                //             $invoiceProduct             = new InvoiceProduct();
                //             $invoiceProduct->invoice_id = $invoice->id;
                //         }
                //         $invoiceProduct->product_id  = $products[$i]['item'];
                //         $invoiceProduct->quantity    = 1;
                //         $invoiceProduct->tax         = $project_tax;
                //         $invoiceProduct->discount    = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                //         $invoiceProduct->price       = $products[$i]['price'];
                //         $invoiceProduct->description = $products[$i]['description'];
                //         $invoiceProduct->save();
                //     }

                // }
                // **************** i dont know what all of this is

                return redirect()->route('invoice.index')->with('success', __('Invoice successfully updated.'));
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

    public function duplicate($invoice_id)
    {
        if(Auth::user()->can('invoice duplicate'))
        {
            $invoice                            = Invoice::where('id', $invoice_id)->first();
            $duplicateInvoice                   = new Invoice();
            $duplicateInvoice->invoice_id       = $this->invoiceNumber();
            $duplicateInvoice->customer_id      = $invoice['customer_id'];
            $duplicateInvoice->user_id          = $invoice['user_id'];
            $duplicateInvoice->issue_date       = date('Y-m-d');
            $duplicateInvoice->due_date         = $invoice['due_date'];
            $duplicateInvoice->send_date        = null;
            $duplicateInvoice->category_id      = $invoice['category_id'];
            $duplicateInvoice->status           = 0;
            $duplicateInvoice->shipping_display = $invoice['shipping_display'];
            $duplicateInvoice->invoice_module   = $invoice['invoice_module'];
            $duplicateInvoice->workspace        = $invoice['workspace'];
            $duplicateInvoice->created_by       = $invoice['created_by'];
            $duplicateInvoice->save();
            Invoice::starting_number( $duplicateInvoice->invoice_id + 1, 'invoice');

            if($duplicateInvoice)
            {
                $invoiceProduct = InvoiceProduct::where('invoice_id', $invoice_id)->get();
                foreach($invoiceProduct as $product)
                {
                    $duplicateProduct                 = new InvoiceProduct();
                    $duplicateProduct->invoice_id     = $duplicateInvoice->id;
                    $duplicateProduct->product_type   = $product->product_type;
                    $duplicateProduct->product_id     = $product->product_id;
                    $duplicateProduct->quantity       = $product->quantity;
                    $duplicateProduct->tax            = $product->tax;
                    $duplicateProduct->discount       = $product->discount;
                    $duplicateProduct->price          = $product->price;
                    $duplicateProduct->save();
                }
            }
            event(new DuplicateInvoice($duplicateInvoice));

            return redirect()->back()->with('success', __('Invoice duplicate successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function sent($id)
    {
        if(Auth::user()->can('invoice send'))
        {
            $invoice            = Invoice::where('id', $id)->first();
            $invoice->send_date = date('Y-m-d');
            $invoice->status    = 1;
            $invoice->save();
            
            event(new SentInvoice($invoice));
            if(module_is_active('Account'))
            {
                if(!empty($invoice->customer_id != 0))
                {
                    $customer = \Modules\Account\Entities\Customer::find($invoice->customer_id);
                    if(empty($customer)){
                        $customer = User::where('id', $invoice->user_id)->first();
                    }
                    \Modules\Account\Entities\AccountUtility::userBalance('customer', $customer->id, $invoice->getTotal(), 'credit');
                }
            } else {
                $customer = User::where('id', $invoice->user_id)->first();
            }
            $invoice->name = !empty($customer) ? $customer->name : '';
            $invoice->invoice = Invoice::invoiceNumberFormat($invoice->invoice_id);

            $invoiceId = Crypt::encrypt($invoice->id);
            $invoice->url = route('invoice.pdf', $invoiceId);

            // Get the items associated with the invoice
            $items = $invoice->items;

            foreach($items as $item){

                // update product status
                $product = \Modules\ProductService\Entities\ProductService::find($item->product_id);

                if($item->product_type == 'product'){
                    // if invoice was made on sale of a product to a customer

                    // update product status
                    $product->sold_status = 'Awaiting Payment';
                    $product->sold_to = $customer->name;
                    $product->save();
                
                    // adding Journal Entries
                    // Inventory = Credit = Net price after discount before tax
                    // Account Recievable = Debit = Net price after tax
                    // Sales Income = Credit = Net price after discount before tax
                    // VAT Pay / Refund = Credit = Tax amount
                    // Cost of Sales - Purchases = Debit =  Net price after discount before tax
                    
                    // values
                    $netPriceAfterDiscount = $item->price - $item->discount;
                    $tax = \Modules\ProductService\Entities\Tax::find($item->tax);
                    $taxAmount = ($netPriceAfterDiscount * $tax->rate) / 100;
                    $netPriceAfterTax = $netPriceAfterDiscount + $taxAmount;

                    // currency conversion
                    $convertedAmountBeforeTax = currency_conversion($netPriceAfterDiscount, $item->currency, company_setting("defult_currancy"));
                    $convertedTaxAmount = currency_conversion($taxAmount, $item->currency, company_setting("defult_currancy"));
                    $convertedAmountAfterTax = currency_conversion($netPriceAfterTax, $item->currency, company_setting("defult_currancy")); 

                    // new journal entry
                    $new_journal_entry = new \Modules\DoubleEntry\Entities\JournalEntry();
                    $new_journal_entry->date = now();
                    $new_journal_entry->reference = Invoice::invoiceNumberFormat($invoice->invoice_id);
                    $new_journal_entry->description = 'Invoice created for sale of product';
                    $new_journal_entry->journal_id = $this->journalNumber();
                    $new_journal_entry->currency = company_setting("defult_currancy");
                    $new_journal_entry->workspace = getActiveWorkSpace();
                    $new_journal_entry->created_by = \Auth::user()->id;
                    $new_journal_entry->save();

                    // for inventory
                    $first_journal_item = new \Modules\DoubleEntry\Entities\JournalItem();
                    $first_journal_item->journal = $new_journal_entry->id;
                    $first_journal_item->account = 5; // Inventory
                    $first_journal_item->description = '-';
                    $first_journal_item->debit = 0.00;
                    $first_journal_item->credit = $convertedAmountBeforeTax;
                    $first_journal_item->workspace = getActiveWorkSpace();
                    $first_journal_item->created_by = \Auth::user()->id;
                    $first_journal_item->save();

                    $first_transaction = add_quick_transaction('Credit', 5, $convertedAmountBeforeTax);

                    // for account recievable
                    $second_journal_item = new \Modules\DoubleEntry\Entities\JournalItem();
                    $second_journal_item->journal = $new_journal_entry->id;
                    $second_journal_item->account = 3; // Account Recievable
                    $second_journal_item->description = '-';
                    $second_journal_item->debit = $convertedAmountAfterTax;
                    $second_journal_item->credit = 0.00;
                    $second_journal_item->workspace = getActiveWorkSpace();
                    $second_journal_item->created_by = \Auth::user()->id;
                    $second_journal_item->save();

                    $second_transaction = add_quick_transaction('Debit', 3, $convertedAmountAfterTax);

                    // for sales income
                    $third_journal_item = new \Modules\DoubleEntry\Entities\JournalItem();
                    $third_journal_item->journal = $new_journal_entry->id;
                    $third_journal_item->account = 50; // Sales Income
                    $third_journal_item->description = '-';
                    $third_journal_item->debit = 0.00;
                    $third_journal_item->credit = $convertedAmountBeforeTax;
                    $third_journal_item->workspace = getActiveWorkSpace();
                    $third_journal_item->created_by = \Auth::user()->id;
                    $third_journal_item->save();

                    $third_transaction = add_quick_transaction('Credit', 50, $convertedAmountBeforeTax);

                    // for tax
                    $fourth_journal_item = new \Modules\DoubleEntry\Entities\JournalItem();
                    $fourth_journal_item->journal = $new_journal_entry->id;
                    $fourth_journal_item->account = 22; // VAT Pay / Refund
                    $fourth_journal_item->description = '-';
                    $fourth_journal_item->debit = 0.00;
                    $fourth_journal_item->credit = $convertedTaxAmount;
                    $fourth_journal_item->workspace = getActiveWorkSpace();
                    $fourth_journal_item->created_by = \Auth::user()->id;
                    $fourth_journal_item->save();

                    $fourth_transaction = add_quick_transaction('Credit', 22, $convertedTaxAmount);

                    // for cost of goods sold
                    $fifth_journal_item = new \Modules\DoubleEntry\Entities\JournalItem();
                    $fifth_journal_item->journal = $new_journal_entry->id;
                    $fifth_journal_item->account = 59; // Cost of Sales - Purchases
                    $fifth_journal_item->description = '-';
                    $fifth_journal_item->debit = $convertedAmountBeforeTax;
                    $fifth_journal_item->credit = 0.00;
                    $fifth_journal_item->workspace = getActiveWorkSpace();
                    $fifth_journal_item->created_by = \Auth::user()->id;
                    $fifth_journal_item->save();

                    $fifth_transaction = add_quick_transaction('Debit', 59, $convertedAmountBeforeTax);

                }else{
                    // if invoice was made for a service performed on a product for a customer

                    // no need to update status

                    // adding Journal Entries
                    // Account Recievable = Debit = Net price after tax
                    // Service Income = Credit = Net price after discount before tax
                    // VAT Pay / Refund = Credit = Tax amount

                    // values
                    $netPriceAfterDiscount = $item->price - $item->discount;
                    $tax = \Modules\ProductService\Entities\Tax::find($item->tax);
                    $taxAmount = ($netPriceAfterDiscount * $tax->rate) / 100;
                    $netPriceAfterTax = $netPriceAfterDiscount + $taxAmount;

                    // currency conversion
                    $convertedAmountBeforeTax = currency_conversion($netPriceAfterDiscount, $item->currency, company_setting("defult_currancy"));
                    $convertedTaxAmount = currency_conversion($taxAmount, $item->currency, company_setting("defult_currancy"));
                    $convertedAmountAfterTax = currency_conversion($netPriceAfterTax, $item->currency, company_setting("defult_currancy")); 

                    // new journal entry
                    $new_journal_entry = new \Modules\DoubleEntry\Entities\JournalEntry();
                    $new_journal_entry->date = now();
                    $new_journal_entry->reference = Invoice::invoiceNumberFormat($invoice->invoice_id);
                    $new_journal_entry->description = 'Invoice created for service performed on product';
                    $new_journal_entry->journal_id = $this->journalNumber();
                    $new_journal_entry->currency = company_setting("defult_currancy");
                    $new_journal_entry->workspace = getActiveWorkSpace();
                    $new_journal_entry->created_by = \Auth::user()->id;
                    $new_journal_entry->save();

                    // for account recievable
                    $first_journal_item = new \Modules\DoubleEntry\Entities\JournalItem();
                    $first_journal_item->journal = $new_journal_entry->id;
                    $first_journal_item->account = 3; // Account Recievable
                    $first_journal_item->description = '-';
                    $first_journal_item->debit = $convertedAmountAfterTax;
                    $first_journal_item->credit = 0.00;
                    $first_journal_item->workspace = getActiveWorkSpace();
                    $first_journal_item->created_by = \Auth::user()->id;
                    $first_journal_item->save();

                    $first_transaction = add_quick_transaction('Debit', 3, $convertedAmountAfterTax);

                    // for service income
                    $second_journal_item = new \Modules\DoubleEntry\Entities\JournalItem();
                    $second_journal_item->journal = $new_journal_entry->id;
                    $second_journal_item->account = 51; // Service Income
                    $second_journal_item->description = '-';
                    $second_journal_item->debit = 0.00;
                    $second_journal_item->credit = $convertedAmountBeforeTax;
                    $second_journal_item->workspace = getActiveWorkSpace();
                    $second_journal_item->created_by = \Auth::user()->id;
                    $second_journal_item->save();

                    $second_transaction = add_quick_transaction('Credit', 51, $convertedAmountBeforeTax);

                    // for tax
                    $third_journal_item = new \Modules\DoubleEntry\Entities\JournalItem();
                    $third_journal_item->journal = $new_journal_entry->id;
                    $third_journal_item->account = 22; // VAT Pay / Refund
                    $third_journal_item->description = '-';
                    $third_journal_item->debit = 0.00;
                    $third_journal_item->credit = $convertedTaxAmount;
                    $third_journal_item->workspace = getActiveWorkSpace();
                    $third_journal_item->created_by = \Auth::user()->id;
                    $third_journal_item->save();

                    $third_transaction = add_quick_transaction('Credit', 22, $convertedTaxAmount);

                }

            }

            // sending email
            if(!empty(company_setting('Customer Invoice Send')) && company_setting('Customer Invoice Send')  == true)
            {
                $uArr = [
                    'invoice_name' => $invoice->name,
                    'invoice_number' => $invoice->invoice,
                    'invoice_url' => $invoice->url,
                ];

                try
                {
                    $resp = EmailTemplate::sendEmailTemplate('Customer Invoice Send', [$customer->id => $customer->email],$uArr);
                }
                catch(\Exception $e)
                {
                    $resp['error'] = $e->getMessage();
                }

                return redirect()->back()->with('success', __('Invoice successfully sent.') . ((isset($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
            }
            return redirect()->back()->with('success', 'Invoice sent email notification is off.');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function resent($id)
    {
        if(Auth::user()->can('invoice send'))
        {
            $invoice = Invoice::where('id', $id)->first();
            if(module_is_active('Account'))
            {
                $customer         = \Modules\Account\Entities\Customer::where('user_id', $invoice->user_id)->first();
                if(empty($customer))
                {
                    $customer         = User::where('id', $invoice->user_id)->first();
                }
            }
            else
            {
                $customer         = User::where('id', $invoice->user_id)->first();
            }

            $invoice->name    = !empty($customer) ? $customer->name : '';
            $invoice->invoice = Invoice::invoiceNumberFormat($invoice->invoice_id);

            $invoiceId    = Crypt::encrypt($invoice->id);
            $invoice->url = route('invoice.pdf', $invoiceId);

             // first parameter invoice
             event(new ResentInvoice($invoice));

            if(!empty(company_setting('Customer Invoice Send')) && company_setting('Customer Invoice Send')  == true)
            {
                $uArr = [
                    'invoice_name' => $invoice->name,
                    'invoice_number' => $invoice->invoice,
                    'invoice_url' => $invoice->url,
                ];

                try
                {
                    $resp = EmailTemplate::sendEmailTemplate('Customer Invoice Send', [$customer->id => $customer->email],$uArr);
                }
                catch(\Exception $e)
                {
                    $resp['error'] = $e->getMessage();
                }
                return redirect()->back()->with('success', __('Invoice successfully sent.') . ((isset($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
            }
            return redirect()->back()->with('success', 'Invoice sent email notification is off.');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function paymentReminder($invoice_id)
    {
        $invoice            = Invoice::find($invoice_id);
        if($invoice)
        {
            if(module_is_active('Account'))
            {
                $customer         = \Modules\Account\Entities\Customer::where('user_id', $invoice->user_id)->first();
                if(empty($customer)){
                    $customer         = User::where('id', $invoice->user_id)->first();
                }
            }
            else
            {
                $customer         = User::where('id', $invoice->user_id)->first();
            }

            $invoice->dueAmount = currency_format_with_sym($invoice->getDue());
            $invoice->name      = $customer['name'];
            $invoice->date      = company_date_formate($invoice->send_date);
            $invoice->invoice   = Invoice::invoiceNumberFormat($invoice->invoice_id);

             // first parameter invoice
             event(new PaymentReminderInvoice($invoice));

            //Email notification
            if(!empty(company_setting('Payment Reminder')) && company_setting('Payment Reminder')  == true)
            {
                $uArr = [
                    'payment_name' => $invoice->name,
                    'invoice_number' => $invoice->invoice,
                    'payment_dueAmount'=> $invoice->dueAmount,
                    'payment_date'=> $invoice->date,
                ];

                try
                {
                    $resp = EmailTemplate::sendEmailTemplate('Payment Reminder', [$customer->id => $customer->email], $uArr);
                }
                catch(\Exception $e)
                {
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }
            }

            return redirect()->back()->with('success', __('Payment reminder successfully send.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        }
        else
        {
            return redirect()->back()->with('error', __('Invoice not found!'));

        }
    }

    public function invoice($invoice_id)
    {
        try {
            $invoiceId = Crypt::decrypt($invoice_id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Invoice Not Found.'));
        }
        $invoice   = Invoice::where('id', $invoiceId)->first();
        if(module_is_active('Account'))
        {
            $customer         = \Modules\Account\Entities\Customer::where('user_id', $invoice->user_id)->first();
        }
        else
        {
            $customer         = User::where('id', $invoice->user_id)->first();
        }
        $items         = [];
        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];
        foreach($invoice->items as $product)
        {
            $item              = new \stdClass();
            if($invoice->invoice_module == "taskly")
            {
                $item->name        = !empty($product->product())?$product->product()->title:'';
            }
            elseif($invoice->invoice_module == "account")
            {
                $item->name        = !empty($product->product()) ? $product->product()->name : '';
                $item->product_type   = !empty($product->product_type) ? $product->product_type : '';
            }
            $item->quantity    = $product->quantity;
            $item->tax         = $product->tax;
            $item->discount    = $product->discount;
            $item->price       = $product->price;
            $item->description = $product->description;
            $totalQuantity += $item->quantity;
            $totalRate     += $item->price;
            $totalDiscount += $item->discount;
            if(module_is_active('ProductService'))
            {
                $taxes = \Modules\ProductService\Entities\Tax::tax($product->tax);
                $itemTaxes = [];
                $tax_price = 0;
                if(!empty($item->tax))
                {
                    foreach($taxes as $tax)
                    {
                        $taxPrice      = Invoice::taxRate($tax->rate, $item->price, $item->quantity,$item->discount);
                        $tax_price  += $taxPrice;
                        $totalTaxPrice += $taxPrice;

                        $itemTax['name']  = $tax->name;
                        $itemTax['rate']  = $tax->rate . '%';
                        $itemTax['price'] = currency_format_with_sym($taxPrice,$invoice->created_by);
                        $itemTaxes[]      = $itemTax;

                        if(array_key_exists($tax->name, $taxesData))
                        {
                            $taxesData[$tax->name] = $taxesData[$tax->name] + $taxPrice;
                        }
                        else
                        {
                            $taxesData[$tax->name] = $taxPrice;
                        }
                    }
                    $item->itemTax = $itemTaxes;
                    $item->tax_price = $tax_price;
                }
                else
                {
                    $item->itemTax = [];
                }
                $items[] = $item;
            }
        }
        $invoice->itemData      = $items;
        $invoice->totalTaxPrice = $totalTaxPrice;
        $invoice->totalQuantity = $totalQuantity;
        $invoice->totalRate     = $totalRate;
        $invoice->totalDiscount = $totalDiscount;
        $invoice->taxesData     = $taxesData;
        if(module_is_active('CustomField')){
            $invoice->customField = \Modules\CustomField\Entities\CustomField::getData($invoice, 'Base','Invoice');
            $customFields             = \Modules\CustomField\Entities\CustomField::where('workspace_id', '=', $invoice->workspace)->where('module', '=', 'Base')->where('sub_module','Invoice')->get();
        }else{
            $customFields = null;
        }

        //Set your logo
        $company_logo = get_file(sidebar_logo());
        $invoice_logo = company_setting('invoice_logo',$invoice->created_by,$invoice->workspace);
        if(isset($invoice_logo) && !empty($invoice_logo))
        {
            $img  = get_file($invoice_logo);
        }
        else{
            $img  = $company_logo;
        }
        if($invoice)
        {
            $color      = '#'.(!empty(company_setting('invoice_color',$invoice->created_by,$invoice->workspace)) ? company_setting('invoice_color',$invoice->created_by,$invoice->workspace) : 'ffffff');
            $font_color = User::getFontColor($color);
            $invoice_template  = (!empty(company_setting('invoice_template',$invoice->created_by,$invoice->workspace)) ? company_setting('invoice_template',$invoice->created_by,$invoice->workspace) : 'template1');
            $settings['site_rtl'] = company_setting('site_rtl',$invoice->created_by,$invoice->workspace);
            $settings['company_name'] = company_setting('company_name',$invoice->created_by,$invoice->workspace);
            $settings['company_email'] = company_setting('company_email',$invoice->created_by,$invoice->workspace);
            $settings['company_telephone'] = company_setting('company_telephone',$invoice->created_by,$invoice->workspace);
            $settings['company_address'] = company_setting('company_address',$invoice->created_by,$invoice->workspace);
            $settings['company_city'] = company_setting('company_city',$invoice->created_by,$invoice->workspace);
            $settings['company_state'] = company_setting('company_state',$invoice->created_by,$invoice->workspace);
            $settings['company_zipcode'] = company_setting('company_zipcode',$invoice->created_by,$invoice->workspace);
            $settings['company_country'] = company_setting('company_country',$invoice->created_by,$invoice->workspace);
            $settings['registration_number'] = company_setting('registration_number',$invoice->created_by,$invoice->workspace);
            $settings['tax_type'] = company_setting('tax_type',$invoice->created_by,$invoice->workspace);
            $settings['vat_number'] = company_setting('vat_number',$invoice->created_by,$invoice->workspace);
            $settings['footer_title'] = company_setting('invoice_footer_title',$invoice->created_by,$invoice->workspace);
            $settings['footer_notes'] = company_setting('invoice_footer_notes',$invoice->created_by,$invoice->workspace);
            $settings['shipping_display'] = company_setting('invoice_shipping_display',$invoice->created_by,$invoice->workspace);
            $settings['invoice_template'] = company_setting('invoice_template',$invoice->created_by,$invoice->workspace);
            $settings['invoice_color'] = company_setting('invoice_color',$invoice->created_by,$invoice->workspace);
            return view('invoice.templates.' .$invoice_template, compact('invoice', 'color', 'settings', 'customer', 'img', 'font_color','customFields'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function product(Request $request)
    {
        $data['product']     = $product = \Modules\ProductService\Entities\ProductService::find($request->product_id);
        $data['unit']        = !empty($product) ? ((!empty($product->unit())) ? $product->unit()->name : '') : '';
        $data['taxRate']     = $taxRate = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0 ): 0;
        $data['taxes']       =  !empty($product) ? ( !empty($product->tax_id) ? $product->tax($product->tax_id) : 0) : 0;
        $salePrice           = !empty($product) ?  $product->sale_price : 0;
        $quantity            = 1;
        $taxPrice            = !empty($product) ? (($taxRate / 100) * ($salePrice * $quantity)) : 0;
        $data['totalAmount'] = !empty($product) ?  ($salePrice * $quantity) : 0;

        return json_encode($data);
    }

    public function productDestroy(Request $request)
    {

        if(Auth::user()->can('invoice product delete'))
        {
            InvoiceProduct::where('id', '=', $request->id)->delete();

            return response()->json(['success' => __('Invoice product successfully deleted.')]);
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')]);
        }
    }

    public function saveTemplateSettings(Request $request)
    {
        $user = Auth::user();
        if($request->hasFile('invoice_logo'))
        {
            $invoice_logo = $user->id.'_invoice_logo'.time().'.png';

            $uplaod = upload_file($request,'invoice_logo',$invoice_logo,'invoice_logo');
            if($uplaod['flag'] == 1)
            {
                $url = $uplaod['url'];
                $old_invoice_logo = company_setting('invoice_logo');
                if(!empty($old_invoice_logo) && check_file($old_invoice_logo))
                {
                    delete_file($old_invoice_logo);
                }
            }
            else
            {
                return redirect()->back()->with('error',$uplaod['msg']);
            }
        }

        $userContext = new Context(['user_id' => Auth::user()->id,'workspace_id'=>getActiveWorkSpace()]);
        \Settings::context($userContext)->set('invoice_template', $request->invoice_template);
        \Settings::context($userContext)->set('invoice_color', !empty($request->invoice_color) ? $request->invoice_color : 'ffffff');
        \Settings::context($userContext)->set('invoice_prefix', !empty($request->invoice_prefix) ? $request->invoice_prefix : '#INV');
        \Settings::context($userContext)->set('invoice_starting_number', !empty($request->invoice_starting_number) ? $request->invoice_starting_number : '1');
        \Settings::context($userContext)->set('invoice_footer_title', !empty($request->invoice_footer_title) ? $request->invoice_footer_title : '');
        \Settings::context($userContext)->set('invoice_footer_notes', !empty($request->invoice_footer_notes) ? $request->invoice_footer_notes : '');
        \Settings::context($userContext)->set('invoice_shipping_display', !empty($request->invoice_shipping_display) ? $request->invoice_shipping_display : 'off');
        if($request->hasFile('invoice_logo'))
        {
            \Settings::context($userContext)->set('invoice_logo', $url);
        }

        return redirect()->back()->with('success', __('Invoice Print setting save sucessfully.'));
    }

    public function previewInvoice($template, $color)
    {
        $invoice  = new Invoice();

        $customer                   = new \stdClass();
        $customer->email            = '<Email>';
        $customer->shipping_name    = '<Customer Name>';
        $customer->shipping_country = '<Country>';
        $customer->shipping_state   = '<State>';
        $customer->shipping_city    = '<City>';
        $customer->shipping_phone   = '<Customer Phone Number>';
        $customer->shipping_zip     = '<Zip>';
        $customer->shipping_address = '<Address>';
        $customer->billing_name     = '<Customer Name>';
        $customer->billing_country  = '<Country>';
        $customer->billing_state    = '<State>';
        $customer->billing_city     = '<City>';
        $customer->billing_phone    = '<Customer Phone Number>';
        $customer->billing_zip      = '<Zip>';
        $customer->billing_address  = '<Address>';

        $totalTaxPrice = 0;
        $taxesData     = [];

        $items = [];
        for($i = 1; $i <= 3; $i++)
        {
            $item           = new \stdClass();
            $item->name     = 'Item ' . $i;
            $item->quantity = 1;
            $item->tax      = 5;
            $item->discount = 50;
            $item->price    = 100;
            $item->description    = 'In publishing and graphic design, Lorem ipsum is a placeholder';

            $taxes = [
                'Tax 1',
                'Tax 2',
            ];

            $itemTaxes = [];
            foreach($taxes as $k => $tax)
            {
                $taxPrice         = 10;
                $totalTaxPrice    += $taxPrice;
                $itemTax['name']  = 'Tax ' . $k;
                $itemTax['rate']  = '10 %';
                $itemTax['price'] = '$10';
                $itemTaxes[]      = $itemTax;
                if(array_key_exists('Tax ' . $k, $taxesData))
                {
                    $taxesData['Tax ' . $k] = $taxesData['Tax 1'] + $taxPrice;
                }
                else
                {
                    $taxesData['Tax ' . $k] = $taxPrice;
                }
            }
            $item->itemTax = $itemTaxes;
            $item->tax_price = 10;
            $items[]       = $item;
        }

        $invoice->invoice_id = 1;
        $invoice->issue_date = date('Y-m-d H:i:s');
        $invoice->due_date   = date('Y-m-d H:i:s');
        $invoice->itemData   = $items;

        $invoice->totalTaxPrice = 60;
        $invoice->totalQuantity = 3;
        $invoice->totalRate     = 300;
        $invoice->totalDiscount = 10;
        $invoice->taxesData     = $taxesData;
        $invoice->customField   = [];
        $customFields           = [];

        $preview    = 1;
        $color      = '#' . $color;
        $font_color = User::getFontColor($color);

        $company_logo = get_file(sidebar_logo());

        $invoice_logo =  company_setting('invoice_logo');

        if(!empty($invoice_logo))
        {
            $img = get_file($invoice_logo);
        }
        else{
            $img          =  $company_logo;
        }
        $settings['site_rtl'] = company_setting('site_rtl');
        $settings['company_name'] = company_setting('company_name');
        $settings['company_address'] = company_setting('company_address');
        $settings['company_email'] = company_setting('company_email');
        $settings['company_telephone'] = company_setting('company_telephone');
        $settings['company_city'] = company_setting('company_city');
        $settings['company_state'] = company_setting('company_state');
        $settings['company_zipcode'] = company_setting('company_zipcode');
        $settings['company_country'] = company_setting('company_country');
        $settings['registration_number'] = company_setting('registration_number');
        $settings['tax_type'] = company_setting('tax_type');
        $settings['vat_number'] = company_setting('vat_number');
        $settings['footer_title'] = company_setting('invoice_footer_title');
        $settings['footer_notes'] = company_setting('invoice_footer_notes');
        $settings['shipping_display'] = company_setting('invoice_shipping_display');
        $settings['invoice_template'] = company_setting('invoice_template');
        $settings['invoice_color'] = company_setting('invoice_color');
        return view('invoice.templates.' . $template, compact('invoice', 'preview', 'color', 'img', 'settings', 'customer', 'font_color', 'customFields'));
    }

    public function items(Request $request)
    {
        $items = InvoiceProduct::where('invoice_id', $request->invoice_id)->where('product_id', $request->product_id)->first();

        return json_encode($items);
    }

    public function customer(Request $request)
    {
        if(module_is_active('Account'))
        {
            $customer = \Modules\Account\Entities\Customer::where('user_id', '=', $request->id)->first();
            if(empty($customer))
            {
                $user = User::where('id',$request->id)->where('workspace_id',getActiveWorkSpace())->where('created_by',creatorId())->first();
                $customer['name'] = !empty($user->name) ? $user->name : '';
                $customer['email'] = !empty($user->email) ? $user->email : '';
            }
        }
        else
        {
            $user = User::where('id',$request->id)->where('workspace_id',getActiveWorkSpace())->where('created_by',creatorId())->first();
            $customer['name'] = !empty($user->name) ? $user->name : '';
            $customer['email'] = !empty($user->email) ? $user->email : '';
        }
        return view('invoice.customer_detail', compact('customer'));
    }

    public function payinvoice($invoice_id)
    {
        if(!empty($invoice_id))
        {
            try {
                $id = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
            } catch (\Throwable $th) {
                return redirect('login');
            }

            $invoice = Invoice::where('id',$id)->first();
            if(!is_null($invoice))
            {
                $items         = [];
                $totalTaxPrice = 0;
                $totalQuantity = 0;
                $totalRate     = 0;
                $totalDiscount = 0;
                $taxesData     = [];

                foreach($invoice->items as $item)
                {
                    $totalQuantity += $item->quantity;
                    $totalRate     += $item->price;
                    $totalDiscount += $item->discount;
                    $taxes         = Invoice::tax($item->tax);
                    $itemTaxes = [];
                    foreach($taxes as $tax)
                    {
                        if(!empty($tax))
                        {
                            $taxPrice            = Invoice::taxRate($tax->rate, $item->price, $item->quantity,$item->discount);
                            $totalTaxPrice       += $taxPrice;
                            $itemTax['tax_name'] = $tax->tax_name;
                            $itemTax['tax']      = $tax->rate . '%';
                            $itemTax['price']    = currency_format_with_sym($taxPrice,$invoice->created_by);
                            $itemTaxes[]         = $itemTax;

                            if(array_key_exists($tax->name, $taxesData))
                            {
                                $taxesData[$itemTax['tax_name']] = $taxesData[$tax->tax_name] + $taxPrice;
                            }
                            else
                            {
                                $taxesData[$tax->tax_name] = $taxPrice;
                            }

                        }
                        else
                        {
                            $taxPrice            = Invoice::taxRate(0, $item->price, $item->quantity,$item->discount);
                            $totalTaxPrice       += $taxPrice;
                            $itemTax['tax_name'] = 'No Tax';
                            $itemTax['tax']      = '';
                            $itemTax['price']    = currency_format_with_sym($taxPrice,$invoice->created_by);
                            $itemTaxes[]         = $itemTax;

                            if(array_key_exists('No Tax', $taxesData))
                            {
                                $taxesData[$tax->tax_name] = $taxesData['No Tax'] + $taxPrice;
                            }
                            else
                            {
                                $taxesData['No Tax'] = $taxPrice;
                            }

                        }
                    }

                    $item->itemTax = $itemTaxes;
                    $items[]       = $item;
                }
                $invoice->items         = $items;
                $invoice->totalTaxPrice = $totalTaxPrice;
                $invoice->totalQuantity = $totalQuantity;
                $invoice->totalRate     = $totalRate;
                $invoice->totalDiscount = $totalDiscount;
                $invoice->taxesData     = $taxesData;
                $ownerId = $invoice->created_by;

                $users = User::where('id',$invoice->created_by)->first();

                if(!is_null($users))
                {
                    \App::setLocale($users->lang);
                }
                else
                {
                    \App::setLocale('en');
                }

                $invoice    = Invoice::where('id', $id)->first();
                $customer = $invoice->customer;
                $iteams   = $invoice->items;

                $company_payment_setting =[];

                if(module_is_active('Account'))
                {
                    $customer = \Modules\Account\Entities\Customer::where('user_id',$invoice->user_id)->first();
                }
                else
                {
                    $customer = $invoice->customer;
                }
                if(module_is_active('CustomField')){
                    $invoice->customField = \Modules\CustomField\Entities\CustomField::getData($invoice, 'Base','Invoice');
                    $customFields             = \Modules\CustomField\Entities\CustomField::where('workspace_id', '=', $invoice->workspace)->where('module', '=', 'Base')->where('sub_module','Invoice')->get();
                }else{
                    $customFields = null;
                }
                $company_id = $invoice->created_by;
                $workspace_id = $invoice->workspace;
                return view('invoice.invoicepay',compact('invoice','iteams','customer','users','company_payment_setting','customFields', 'company_id','workspace_id'));
            }
            else
            {
                return abort('404', 'The Link You Followed Has Expired');
            }
        }else{
            return abort('404', 'The Link You Followed Has Expired');
        }
    }

    public function payment($invoice_id)
    {
        if(Auth::user()->can('invoice payment create'))
        {
            $invoice = Invoice::where('id', $invoice_id)->first();

            if(module_is_active('Account'))
            {
                $accounts   = \Modules\Account\Entities\BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            else
            {
                $accounts = [];
            }

            return view('invoice.payment', compact('accounts', 'invoice'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function createPayment(Request $request, $invoice_id)
    {
        if(Auth::user()->can('invoice payment create'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'date' => 'required',
                                   'amount' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $amount = $request->amount;

            if($request->currency != company_setting('defult_currancy')){
                $amount = currency_conversion($amount, $request->currency, company_setting('defult_currancy'));
            }

            $invoicePayment                 = new InvoicePayment();

            if(module_is_active('Account'))
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'account_id' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $invoicePayment->account_id     = $request->account_id;
            }
            $invoicePayment->invoice_id     = $invoice_id;
            $invoicePayment->date           = $request->date;
            $invoicePayment->amount         = $request->amount;
            $invoicePayment->currency       = company_setting('defult_currancy');
            $invoicePayment->account_id     = $request->account_id;
            $invoicePayment->payment_method = 0;
            $invoicePayment->reference      = $request->reference;
            $invoicePayment->description    = $request->description;
            if(!empty($request->add_receipt))
            {
                $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                $uplaod = upload_file($request,'add_receipt',$fileName,'payment');
                if($uplaod['flag'] == 1)
                {
                    $url = $uplaod['url'];
                }
                else{
                    return redirect()->back()->with('error',$uplaod['msg']);
                }
                $invoicePayment->add_receipt = $url;
            }
            $invoicePayment->save();

            $invoice = Invoice::where('id', $invoice_id)->first();
            $due     = $invoice->getDue();
            $total   = $invoice->getTotal();
            if($invoice->status == 0)
            {
                $invoice->send_date = date('Y-m-d');
                $invoice->save();
            }
            if($due <= 0)
            {
                $invoice->status = 4;
                $invoice->save();
            }
            else
            {
                $invoice->status = 3;
                $invoice->save();
            }
            $invoicePayment->user_id    = $invoice->customer_id;
            $invoicePayment->user_type  = 'Customer';
            $invoicePayment->type       = 'Partial';
            $invoicePayment->created_by = Auth::user()->id;
            $invoicePayment->payment_id = $invoicePayment->id;
            $invoicePayment->category   = 'Invoice';
            $invoicePayment->account    = $request->account_id;

            $customer_acc =  \Modules\Account\Entities\Customer::where('id', $invoice->customer_id)->first();

            if(module_is_active('Account'))
            {
                // \Modules\Account\Entities\Transaction::addTransaction($invoicePayment);
                if(!empty($customer_acc))
                {
                    $customer = $customer_acc;
                }

                \Modules\Account\Entities\AccountUtility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'credit');

                // \Modules\Account\Entities\Transfer::bankAccountBalance($request->account_id, $request->amount, 'credit');
            }

            // Get the items associated with the invoice
            $items = $invoice->items;

            // Loop through each item and update sold status
            foreach($items as $item) {
                // update product status
                $product = \Modules\ProductService\Entities\ProductService::find($item->product_id);
                if($invoice->status == 3){
                    $product->sold_status = 'Partially Paid';
                }else if($invoice->status == 4){
                    $product->sold_status = 'Paid';
                }
                if($customer_acc){
                    $product->sold_to = $customer_acc->name;
                }
                $product->save();
            }

            $payment            = new InvoicePayment();
            $payment->name      = !empty($customer['name']) ? $customer['name'] : '-';
            $payment->method    = '-';
            $payment->date      = company_date_formate($request->date);
            $payment->amount    = currency_format_with_sym($request->amount);
            $payment->invoice   = 'invoice ' . Invoice::invoiceNumberFormat($invoice->invoice_id);
            $payment->dueAmount = currency_format_with_sym($invoice->getDue());

            // adding Journal Entries
            // Bank Account = Debit = Payment Amount
            // Account Recievable = Credit = Payment Amount

            $bank_account = \Modules\Account\Entities\BankAccount::find($request->account_id);

            $new_journal_entry = new \Modules\DoubleEntry\Entities\JournalEntry();
            $new_journal_entry->date = now();
            $new_journal_entry->reference = Invoice::invoiceNumberFormat($invoice->invoice_id);
            $new_journal_entry->description = 'Invoice Payment Made';
            $new_journal_entry->journal_id = $this->journalNumber();
            $new_journal_entry->currency = company_setting("defult_currancy");
            $new_journal_entry->workspace = getActiveWorkSpace();
            $new_journal_entry->created_by = \Auth::user()->id;
            $new_journal_entry->save();

            $first_journal_item = new \Modules\DoubleEntry\Entities\JournalItem();
            $first_journal_item->journal = $new_journal_entry->id;
            $first_journal_item->account = $bank_account->chart_account_id;
            $first_journal_item->description = '-';
            $first_journal_item->debit = $amount;
            $first_journal_item->credit = 0.00;
            $first_journal_item->workspace = getActiveWorkSpace();
            $first_journal_item->created_by = \Auth::user()->id;
            $first_journal_item->save();

            $first_transaction = add_quick_transaction('Debit', $bank_account->chart_account_id, $amount);

            $second_journal_item = new \Modules\DoubleEntry\Entities\JournalItem();
            $second_journal_item->journal = $new_journal_entry->id;
            $second_journal_item->account = 3;
            $second_journal_item->description = '-';
            $second_journal_item->debit = 0.00;
            $second_journal_item->credit = $amount;
            $second_journal_item->workspace = getActiveWorkSpace();
            $second_journal_item->created_by = \Auth::user()->id;
            $second_journal_item->save();

            $second_transaction = add_quick_transaction('Credit', 3, $amount);

            //Email notification
            if(!empty(company_setting('Invoice Payment Create')) && company_setting('Invoice Payment Create')  == true)
            {
                $uArr = [
                    'payment_name' => $payment->name,
                    'payment_amount' => $payment->amount,
                    'invoice_number' => $payment->invoice,
                    'payment_date' => $payment->date,
                    'payment_dueAmount' => $payment->dueAmount
                ];

                try
                {
                    $resp = EmailTemplate::sendEmailTemplate('Invoice Payment Create', [$customer->id => $customer->email], $uArr);
                }
                catch(\Exception $e)
                {
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }
            }

            event(new CreatePaymentInvoice($request ,$invoice));
            return redirect()->back()->with('success', __('Payment successfully added.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        }

    }

    public function paymentDestroy($invoice_id, $payment_id)
    {
        if(Auth::user()->can('invoice payment delete'))
        {
            $payment = InvoicePayment::find($payment_id);
            if(!empty($payment->add_receipt))
            {
                try
                {
                    delete_file($payment->add_receipt);
                }
                catch (\Exception $e)
                {
                }
            }
            $invoice = Invoice::where('id', $invoice_id)->first();
            $due     = $invoice->getDue();
            $total   = $invoice->getTotal();

            if($due > 0 && $total != $due)
            {
                $invoice->status = 3;

            }
            else
            {
                $invoice->status = 2;
            }

            $invoice->save();

            if(module_is_active('Account'))
            {
                $type = 'Partial';
                $user = 'Customer';

                \Modules\Account\Entities\Transaction::destroyTransaction($payment_id, $type, $user);

                \Modules\Account\Entities\AccountUtility::updateUserBalance('customer', $invoice->customer_id, $payment->amount, 'credit');

                \Modules\Account\Entities\Transfer::bankAccountBalance($payment->account_id, $payment->amount, 'debit');
            }
            // first parameter invoice second parameter payment
            event(new PaymentDestroyInvoice($invoice, $payment));

            $payment->delete();
            return redirect()->back()->with('success', __('Payment successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function invoiceNumber()
    {
        $latest = company_setting('invoice_starting_number');
        if($latest == null)
        {
            return 1;
        }
        else
        {
            return $latest;
        }
    }

    public function destroy(Invoice $invoice)
    {
        if(Auth::user()->can('invoice delete'))
        {
            if($invoice->workspace == getActiveWorkSpace())
            {
                    if(module_is_active('Account'))
                    {
                        foreach($invoice->payments as $invoices)
                        {
                            if(!empty($invoices->add_receipt))
                            {
                                try
                                {
                                    delete_file($invoices->add_receipt);
                                }
                                catch (\Exception $e)
                                {
                                }
                            }
                            \Modules\Account\Entities\Transfer::bankAccountBalance($invoices->account_id, $invoices->amount, 'debit');
                            $invoices->delete();
                        }
                        if(!empty($invoice->user_id) && $invoice->user_id != 0)
                        {
                            $customer = \Modules\Account\Entities\Customer::where('user_id',$invoice->user_id)->where('workspace',getActiveWorkSpace())->first();
                            if(!empty($customer)){
                                \Modules\Account\Entities\AccountUtility::updateUserBalance('customer', $customer->id, $invoice->getTotal(), 'debit');
                            }
                        }
                    }
                    $proposal=Proposal::where('converted_invoice_id',$invoice->id)->first();
                    if(!empty($proposal)){
                        $proposal->converted_invoice_id = Null;
                        $proposal->is_convert           = 0;
                        $proposal->save();

                    }
                    InvoiceProduct::where('invoice_id', '=', $invoice->id)->delete();

                    if(module_is_active('CustomField')){
                        $customFields = \Modules\CustomField\Entities\CustomField::where('module','Base')->where('sub_module','Invoice')->get();
                        foreach($customFields as $customField)
                        {
                            $value = \Modules\CustomField\Entities\CustomFieldValue::where('record_id', '=', $invoice->id)->where('field_id',$customField->id)->first();
                            if(!empty($value)){
                                $value->delete();
                            }
                        }
                    }

                    // first parameter invoice
                    event(new DestroyInvoice($invoice));
                    $invoice->delete();

                    return redirect()->route('invoice.index')->with('success', __('Invoice successfully deleted.'));
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

    public function InvoiceSectionGet(Request $request)
    {
        $type = $request->type;
        $acction = $request->acction;
        $invoice = [];
        if ($acction == 'edit') {
            $invoice = Invoice::find($request->invoice_id);
        }
    
        if ($request->type == "product" && module_is_active('Account')) {
            $product_services = \Modules\ProductService\Entities\ProductService::where('workspace_id', getActiveWorkSpace())
                ->select('sku', 'name', 'id')
                ->get()
                ->toArray();
    
            $product_services_count = count($product_services);
            
            if ($acction != 'edit') {
                // Add the placeholder at the beginning of the array
                array_unshift($product_services, ['id' => '', 'sku' => '', 'name' => '--']);
            }
    
            $product_type = ProductService::$product_type;
    
            $returnHTML = view('invoice.section', compact('product_services', 'type', 'acction', 'invoice', 'product_services_count', 'product_type'))->render();
            $response = [
                'is_success' => true,
                'message' => '',
                'html' => $returnHTML,
            ];
            return response()->json($response);
        } elseif ($request->type == "project" && module_is_active('Taskly')) {
            $projects = \Modules\Taskly\Entities\Project::where('workspace', getActiveWorkSpace())->projectonly();
            if ($request->project_id != 0) {
                $projects = $projects->where('id', $request->project_id);
            }
            $projects = $projects->first();
            $tasks = [];
            if (!empty($projects)) {
                $tasks = \Modules\Taskly\Entities\Task::where('project_id', $projects->id)->get()->pluck('title', 'id');
                if ($acction != 'edit') {
                    $tasks->prepend('--', '');
                }
            }
            $returnHTML = view('invoice.section', compact('tasks', 'type', 'acction', 'invoice'))->render();
            $response = [
                'is_success' => true,
                'message' => '',
                'html' => $returnHTML,
            ];
            return response()->json($response);
        } else {
            return [];
        }
    }
    

    public function pdf($id)
    {
        try {
            $id       = Crypt::decrypt($id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Invoice Not Found.'));
        }
        $invoice = Invoice::find($id);
        if($invoice)
        {
            if($invoice->workspace == getActiveWorkSpace())
            {
                $iteams   = $invoice->items;

                return view('invoice.pdf', compact('invoice', 'iteams'));
            }
            else
            {
                return response()->json(['error'=>__('Permission denied.')]);
            }
        }
        else
        {
            return response()->json(['error'=>__('This invoice is deleted.')]);
        }
    }

    public function invoiceAttechment(Request $request,$id)
    {
        $invoice = Invoice::find($id);
        $file_name = time() . "_" . $request->file->getClientOriginalName();

        $upload = upload_file($request,'file',$file_name,'invoice_attachment',[]);

        $fileSizeInBytes = \File::size($upload['url']);
        $fileSizeInKB = round($fileSizeInBytes / 1024, 2);

        if ($fileSizeInKB < 1024) {
            $fileSizeFormatted = $fileSizeInKB . " KB";
        } else {
            $fileSizeInMB = round($fileSizeInKB / 1024, 2);
            $fileSizeFormatted = $fileSizeInMB . " MB";
        }

        if($upload['flag'] == 1){
            $file                 = InvoiceAttechment::create(
                [
                    'invoice_id' => $invoice->id,
                    'file_name' => $file_name,
                    'file_path' => $upload['url'],
                    'file_size' => $fileSizeFormatted,
                ]
            );
            $return               = [];
            $return['is_success'] = true;

            return response()->json($return);
        }else{

            return response()->json(
                [
                    'is_success' => false,
                    'error' => $upload['msg'],
                ], 401
            );
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

    public function invoiceAttechmentDestroy($id)
    {
        $file = InvoiceAttechment::find($id);

        if (!empty($file->file_path)) {
            delete_file($file->file_path);
        }
        $file->delete();
        return redirect()->back()->with('success', __('File successfully deleted.'));
    }

}
