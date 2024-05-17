<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Controller;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if(\Auth::user()->can('purchase manage'))
        {
            $vender=[];
            $vender = \Modules\Account\Entities\Vender::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $vender->prepend('Select Vendor', '');

            $status =  \Modules\Pos\Entities\Purchase::$statues;
            $purchases =  \Modules\Pos\Entities\Purchase::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get();
            return view('account::purchase.index', compact('purchases', 'status','vender'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function grid()
    {
        if(\Auth::user()->can('purchase manage'))
        {
            $vender=[];
            $vender = \Modules\Account\Entities\Vender::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $vender->prepend('Select Vendor', '');

            $status =  \Modules\Pos\Entities\Purchase::$statues;
            $purchases =  \Modules\Pos\Entities\Purchase::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get();
            return view('account::purchase.grid', compact('purchases', 'status','vender'));
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
    public function create($vendorId)
    {
        if(\Auth::user()->can('purchase create'))
        {
            if(module_is_active('ProductService'))
            {
                $category=[];
                if(module_is_active('ProductService'))
                {
                    $category     = \Modules\ProductService\Entities\Category::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->where('type', 2)->get()->pluck('name', 'id');
                    $category->prepend('Select Category', '');

                }
                if(module_is_active('CustomField')){
                    $customFields =  \Modules\CustomField\Entities\CustomField::where('workspace_id',getActiveWorkSpace())->where('module', '=', 'pos')->where('sub_module','purchase')->get();
                }else{
                    $customFields = null;
                }

                $purchase_number = \Modules\Pos\Entities\Purchase::purchaseNumberFormat($this->purchaseNumber());

                $venders=[];
                $venders     =  User::where('type','vendor')->where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
                $venders->prepend('Select Vendor', '');
                
                $warehouse     = Modules\Pos\Entities\Warehouse::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
                $warehouse->prepend('Select Warehouse', '');

                $product_services=[];
                $product_type=[];
                if(module_is_active('ProductService'))
                {
                    $product_services =  \Modules\ProductService\Entities\ProductService::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->select('sku', 'name', 'id')->get();
                    $product_services->prepend(['id'=>null, 'sku' => "Select Items", 'name'=>null]);
                    
                    $product_type =\Modules\ProductService\Entities\ProductService::$product_type;
                }

            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
         return view('account::', compact('venders', 'purchase_number', 'product_services', 'category','vendorId','warehouse','customFields','product_type'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
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
