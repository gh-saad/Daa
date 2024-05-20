<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Models\User;
use Modules\Account\Entities\Purchase;
use Modules\Account\Entities\Vender;
use Modules\Account\Entities\Warehouse;
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
            $vender = Vender::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $vender->prepend('Select Vendor', '');

            $status =  Purchase::$statues;
            $purchases =  Purchase::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get();
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
            $vender = Vender::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $vender->prepend('Select Vendor', '');

            $status =  Purchase::$statues;
            $purchases =  Purchase::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get();
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

                $purchase_number = Purchase::purchaseNumberFormat($this->purchaseNumber());

                $venders=[];
                $venders     =  User::where('type','vendor')->where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
                $venders->prepend('Select Vendor', '');
                
                $warehouse     = Warehouse::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
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
         return view('account::purchase.create', compact('venders', 'purchase_number', 'product_services', 'category','vendorId','warehouse','customFields','product_type'));
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
        // if user has permission to create a new purchase
        if(\Auth::user()->can('purchase create'))
        {
            // validate request
            $validator = \Validator::make(
                $request->all(), [
                    'vender_id' => 'required',
                    'warehouse_id' => 'required',
                    'purchase_date' => 'required',
                    'category_id' => 'required',
                    'items' => 'required',
                    'lot_number'=> 'required',
                    'bl_number'=> 'required',
                ]
            );
            // if validation fails return error
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            // locate vendor with id
            $vender = Vender::where('user_id',$request->vender_id)->first();

            dd($request->all());
        }
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

    // other functions

    // to get purchase number
    function purchaseNumber()
    {
        $latest = Purchase::where('created_by', '=',creatorId())->where('workspace',getActiveWorkSpace())->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->purchase_id + 1;
    }

    // to fetch vendor
    public function vender(Request $request)
    {
        $vender = Vender::where('user_id', '=', $request->id)->first();
        if(empty($vender))
        {
            $user = User::find($request->id);
            $vender['name'] =!empty($user->name)? $user->name:'';
            $vender['email'] =!empty($user->email)? $user->email:'';
        }

        return view('account::purchase.vender_detail', compact('vender'));
    }

    // to fetch product data
    public function product(Request $request)
    {
        $data['product']     = $product = \Modules\ProductService\Entities\ProductService::find($request->product_id);
        $data['unit']        = !empty($product) ? ((!empty($product->unit())) ? $product->unit()->name : '') : '';
        $data['taxRate']     = $taxRate = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0 ): 0;
        $data['taxes']       =  !empty($product) ? ( !empty($product->tax_id) ? $product->tax($product->tax_id) : 0) : 0;
        $salePrice           = !empty($product) ?  $product->purchase_price : 0;
        $quantity            = 1;
        $taxPrice            = !empty($product) ? (($taxRate / 100) * ($salePrice * $quantity)) : 0;
        $data['totalAmount'] = !empty($product) ?  ($salePrice * $quantity) : 0;
        return json_encode($data);
    }

    // to delete a product
    public function productDestroy(Request $request)
    {
        if(\Auth::user()->can('purchase delete'))
        {
            \Modules\ProductService\Entities\ProductService::where('id', '=', $request->id)->delete();

            return redirect()->back()->with('success', __('Purchase product successfully deleted.'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
