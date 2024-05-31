<?php

namespace Modules\Hrm\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Hrm\Entities\Employee;
use Modules\Hrm\Entities\TaxRelief;
use Modules\Hrm\Events\CreateTaxRelief;
use Modules\Hrm\Events\DestroyTaxRelief;
use Modules\Hrm\Events\UpdateTaxRelief;

class TaxReliefController extends Controller
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
    public function tax_relief_create($id)
    {
        if(Auth::user()->can('saturation deduction create'))
        {
            $employee = Employee::find($id);

            $tax_relief = TaxRelief::$tax_relief_value_type;
            return view('hrm::taxrelief.create', compact('employee', 'tax_relief'));
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
                                   'tax_relief_value_type' => 'required',
                                   'tax_relief_value' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $taxrelief = new TaxRelief;
            $taxrelief->employee_id = $request->employee_id;
            $taxrelief->title = $request->title;
            $taxrelief->tax_relief_value_type = $request->tax_relief_value_type;
            $taxrelief->tax_relief_value = $request->tax_relief_value;
            $taxrelief->workspace = getActiveWorkSpace();
            $taxrelief->created_by = creatorId();
            $taxrelief->save();

            event(new CreateTaxRelief($request, $taxrelief));

            return redirect()->back()->with('success', __('Tax Relief successfully created.'));
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
    public function edit(TaxRelief $taxrelief)
    {
        if(Auth::user()->can('saturation deduction edit'))
        {
            if($taxrelief->created_by == creatorId() && $taxrelief->workspace == getActiveWorkSpace())
            {
                $tax_relief_type = TaxRelief::$tax_relief_value_type;

                return view('hrm::taxrelief.edit', compact('taxrelief', 'tax_relief_type'));
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
    public function update(Request $request, TaxRelief $taxrelief)
    {
        if(Auth::user()->can('saturation deduction edit'))
        {
            if($taxrelief->created_by == creatorId() && $taxrelief->workspace == getActiveWorkSpace())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'title' => 'required',
                                       'tax_relief_value_type' => 'required',
                                       'tax_relief_value' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $taxrelief->title = $request->title;
                $taxrelief->tax_relief_value_type = $request->tax_relief_value_type;
                $taxrelief->tax_relief_value = $request->tax_relief_value;
                $taxrelief->save();

                event(new UpdateTaxRelief($request, $taxrelief));

                return redirect()->back()->with('success', __('Tax Relief successfully updated.'));
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
    public function destroy(TaxRelief $taxrelief)
    {
        if(Auth::user()->can('saturation deduction delete'))
        {
            if($taxrelief->created_by == creatorId() && $taxrelief->workspace == getActiveWorkSpace())
            {
                event(new DestroyTaxRelief($taxrelief));

                $taxrelief->delete();

                return redirect()->back()->with('success', __('Tax Relief successfully deleted.'));
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
