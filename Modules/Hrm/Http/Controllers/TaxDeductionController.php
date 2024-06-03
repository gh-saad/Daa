<?php

namespace Modules\Hrm\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Hrm\Entities\Employee;
use Modules\Hrm\Entities\TaxDeduction;
use Modules\Hrm\Events\CreateTaxDeduction;
use Modules\Hrm\Events\DestroyTaxDeduction;
use Modules\Hrm\Events\UpdateTaxDeduction;

class TaxDeductionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('hrm::index');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function tax_deduction_create($id)
    {
        if(Auth::user()->can('saturation deduction create'))
        {
            $employee = Employee::find($id);

            $tax_deduction = TaxDeduction::$tax_deduction_value_type;
            return view('hrm::taxdeduction.create', compact('employee', 'tax_deduction'));
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
        return view('hrm::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if(Auth::user()->can('saturation deduction create'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'employee_id' => 'required',
                                   'title' => 'required',
                                   'salary_amount' => 'required',
                                   'tax_deduction_value_type' => 'required',
                                   'tax_deduction_value' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $taxdeduction = new TaxDeduction;
            $taxdeduction->employee_id = $request->employee_id;
            $taxdeduction->title = $request->title;
            $taxdeduction->salary_amount = $request->salary_amount;
            $taxdeduction->difference = $request->salary_difference;
            $taxdeduction->tax_deduction_value_type = $request->tax_deduction_value_type;
            $taxdeduction->tax_deduction_value = $request->tax_deduction_value;
            $taxdeduction->tax_deduction_calculated = $request->tax_deduction_calculated;
            $taxdeduction->workspace = getActiveWorkSpace();
            $taxdeduction->created_by = creatorId();
            $taxdeduction->save();

            event(new CreateTaxDeduction($request, $taxdeduction));

            return redirect()->back()->with('success', __('Tax Deduction successfully created.'));
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
        return view('hrm::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(TaxDeduction $taxdeduction)
    {
        if(Auth::user()->can('saturation deduction edit'))
        {
            if($taxdeduction->created_by == creatorId() && $taxdeduction->workspace == getActiveWorkSpace())
            {
                $employee = Employee::find($taxdeduction->employee_id);
    
                $taxdeduc = TaxDeduction::$tax_deduction_value_type;

                return view('hrm::taxdeduction.edit', compact('employee', 'taxdeduction', 'taxdeduc'));
            }
            else
            {

                return response()->json(['error' => __('Permission denied.')], 401);
            }
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
    public function update(Request $request, TaxDeduction $taxdeduction)
    {
        if(Auth::user()->can('saturation deduction edit'))
        {
            if($taxdeduction->created_by == creatorId() && $taxdeduction->workspace == getActiveWorkSpace())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'title' => 'required',
                                       'salary_amount' => 'required',
                                       'tax_deduction_value_type' => 'required',
                                       'tax_deduction_value' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $taxdeduction->title = $request->title;
                $taxdeduction->salary_amount = $request->salary_amount;
                $taxdeduction->difference = $request->salary_difference;
                $taxdeduction->tax_deduction_value_type = $request->tax_deduction_value_type;
                $taxdeduction->tax_deduction_value = $request->tax_deduction_value;
                $taxdeduction->tax_deduction_calculated = $request->tax_deduction_calculated;
                $taxdeduction->save();

                event(new UpdateTaxDeduction($request, $taxdeduction));

                return redirect()->back()->with('success', __('Tax Deduction successfully updated.'));
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

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(TaxDeduction $taxdeduction)
    {
        if(Auth::user()->can('saturation deduction delete'))
        {
            if($taxdeduction->created_by == creatorId() && $taxdeduction->workspace == getActiveWorkSpace())
            {
                event(new DestroyTaxDeduction($taxdeduction));

                $taxdeduction->delete();

                return redirect()->back()->with('success', __('Tax Deduction successfully deleted.'));
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
