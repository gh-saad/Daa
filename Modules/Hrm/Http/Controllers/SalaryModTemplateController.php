<?php

namespace Modules\Hrm\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Hrm\Entities\AllowanceOption;
use Modules\Hrm\Entities\DeductionOption;
use Modules\Hrm\Entities\LoanOption;
use Modules\Hrm\Entities\SalaryModificationTemplate;

class SalaryModTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->can('employee manage')) {
            $allowanceoptions = AllowanceOption::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $deductionoptions = DeductionOption::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $loanoptions = LoanOption::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $salaryModTemplate = SalaryModificationTemplate::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->first();

            $allowances = $salaryModTemplate ? json_decode($salaryModTemplate->allowance, true) : [];
            $commissions = $salaryModTemplate ? json_decode($salaryModTemplate->commission, true) : [];
            $loans = $salaryModTemplate ? json_decode($salaryModTemplate->loan, true) : [];
            $saturation_deductions = $salaryModTemplate ? json_decode($salaryModTemplate->saturation_deduction, true) : [];
            $tax_deductions = $salaryModTemplate ? json_decode($salaryModTemplate->tax_deduction, true) : [];
            $tax_reliefs = $salaryModTemplate ? json_decode($salaryModTemplate->tax_relief, true) : [];
            $other_payments = $salaryModTemplate ? json_decode($salaryModTemplate->other_payment, true) : [];
            $overtimes = $salaryModTemplate ? json_decode($salaryModTemplate->overtime, true) : [];

            return view('hrm::salarymodtemplate.index', compact('allowanceoptions', 'deductionoptions', 'loanoptions', 'allowances', 'commissions', 'loans', 'saturation_deductions', 'tax_deductions', 'tax_reliefs', 'other_payments', 'overtimes'));
        } else {
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
        if ($request->input('mod_type') == 'allowance'){

            // Validate the request data
            $validatedData = $request->validate([
                'allowance_option_id' => 'required|integer',
                'allowance_name' => 'required|string|max:255',
                'allowance_amount_type' => 'required|string|in:fixed,percentage',
                'allowance_amount' => 'required|numeric',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);

            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';

            // Prepare the allowance data
            $allowanceData = [
                'option_id' => $validatedData['allowance_option_id'],
                'name' => $validatedData['allowance_name'],
                'amount_type' => $validatedData['allowance_amount_type'],
                'amount' => $validatedData['allowance_amount'],
                'threshold' => $salaryThreshold,
                'min_salary' => $validatedData['min_salary'] ?? null,
                'max_salary' => $validatedData['max_salary'] ?? null,
            ];

            // Check to see if salary modification entry exists already
            $salary_mod = SalaryModificationTemplate::where('created_by', '=', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();
                
            if (!$salary_mod) {
                // Entry does not exist, create a parent array and append this into it
                $parentArray = [$allowanceData['name'] => $allowanceData];
                $salaryModTemplate = new SalaryModificationTemplate();
                $salaryModTemplate->name = 'Template' . creatorId();
                $salaryModTemplate->allowance = json_encode($parentArray);
                $salaryModTemplate->workspace = getActiveWorkSpace();
                $salaryModTemplate->created_by = auth()->user()->id;
                $salaryModTemplate->save();
            } else {
                // check to see if a parent array exist for the allowance in the salary_mod
                $existingAllowances = json_decode($salary_mod->allowance, true);
                if ($existingAllowances !== null){
                    // Check if the allowance already exists
                    if (!array_key_exists($allowanceData['name'], $existingAllowances)) {
                        // Allowance does not exist, append it
                        $existingAllowances[$allowanceData['name']] = $allowanceData;
                        $salary_mod->allowance = json_encode($existingAllowances);
                        $salary_mod->save();
                    } else {
                        return redirect()->back()->with('error', __('Allowance already exists.'));
                    }
                }else{
                    // no parent array exist
                    $existingAllowances = [];
                    $existingAllowances[$allowanceData['name']] = $allowanceData;
                    $salary_mod->allowance = json_encode($existingAllowances);
                    $salary_mod->save();
                }
            }

            // Redirect with a success message
            return redirect()->route('salarymodtemplate.index')->with('success', __('Allowance created successfully.'));

        }else if($request->input('mod_type') == 'commission'){

            // Validate the request data
            $validatedData = $request->validate([
                'commission_name' => 'required|string|max:255',
                'commission_amount_type' => 'required|string|in:fixed,percentage',
                'commission_amount' => 'required|numeric',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);

            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';

            // Prepare the commission data
            $commissionData = [
                'name' => $validatedData['commission_name'],
                'amount_type' => $validatedData['commission_amount_type'],
                'amount' => $validatedData['commission_amount'],
                'threshold' => $salaryThreshold,
                'min_salary' => $validatedData['min_salary'] ?? null,
                'max_salary' => $validatedData['max_salary'] ?? null,
            ];

            // Check to see if salary modification entry exists already
            $salary_mod = SalaryModificationTemplate::where('created_by', '=', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();
                
            if (!$salary_mod) {
                // Entry does not exist, create a parent array and append this into it
                $parentArray = [$commissionData['name'] => $commissionData];
                $salaryModTemplate = new SalaryModificationTemplate();
                $salaryModTemplate->name = 'Template' . creatorId();
                $salaryModTemplate->commission = json_encode($parentArray);
                $salaryModTemplate->workspace = getActiveWorkSpace();
                $salaryModTemplate->created_by = auth()->user()->id;
                $salaryModTemplate->save();
            } else {
                // check to see if a parent array exist for the commission in the salary_mod
                $existingCommissions = json_decode($salary_mod->commission, true);
                if ($existingCommissions !== null){
                    // Check if the commission already exists
                    if (!array_key_exists($commissionData['name'], $existingCommissions)) {
                        // Commission does not exist, append it
                        $existingCommissions[$commissionData['name']] = $commissionData;
                        $salary_mod->commission = json_encode($existingCommissions);
                        $salary_mod->save();
                    } else {
                        return redirect()->back()->with('error', __('Commission already exists.'));
                    }
                }else{
                    // no parent array exist
                    $existingCommissions = [];
                    $existingCommissions[$commissionData['name']] = $commissionData;
                    $salary_mod->commission = json_encode($existingCommissions);
                    $salary_mod->save();
                }
            }

            // Redirect with a success message
            return redirect()->route('salarymodtemplate.index')->with('success', __('Commission created successfully.'));

        }else if($request->input('mod_type') == 'loan'){

            // Validate the request data
            $validatedData = $request->validate([
                'loan_option_id' => 'required|integer',
                'loan_name' => 'required|string|max:255',
                'loan_amount_type' => 'required|string|in:fixed,percentage',
                'loan_amount' => 'required|numeric',
                'start_date' => 'required',
                'end_date' => 'required',
                'loan_reason' => 'nullable',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);

            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';

            // Prepare the loan data
            $loanData = [
                'option_id' => $validatedData['loan_option_id'],
                'name' => $validatedData['loan_name'],
                'amount_type' => $validatedData['loan_amount_type'],
                'amount' => $validatedData['loan_amount'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'reason' => $validatedData['loan_reason'],
                'threshold' => $salaryThreshold,
                'min_salary' => $validatedData['min_salary'] ?? null,
                'max_salary' => $validatedData['max_salary'] ?? null,
            ];

            // Check to see if salary modification entry exists already
            $salary_mod = SalaryModificationTemplate::where('created_by', '=', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();
                
            if (!$salary_mod) {
                // Entry does not exist, create a parent array and append this into it
                $parentArray = [$loanData['name'] => $loanData];
                $salaryModTemplate = new SalaryModificationTemplate();
                $salaryModTemplate->name = 'Template' . creatorId();
                $salaryModTemplate->loan = json_encode($parentArray);
                $salaryModTemplate->workspace = getActiveWorkSpace();
                $salaryModTemplate->created_by = auth()->user()->id;
                $salaryModTemplate->save();
            } else {
                // check to see if a parent array exist for the loan in the salary_mod
                $existingLoans = json_decode($salary_mod->loan, true);
                if ($existingLoans !== null){
                    // Check if the loan already exists
                    if (!array_key_exists($loanData['name'], $existingLoans)) {
                        // Loan does not exist, append it
                        $existingLoans[$loanData['name']] = $loanData;
                        $salary_mod->loan = json_encode($existingLoans);
                        $salary_mod->save();
                    } else {
                        return redirect()->back()->with('error', __('Loan already exists.'));
                    }
                }else{
                    // no parent array exist
                    $existingLoans = [];
                    $existingLoans[$loanData['name']] = $loanData;
                    $salary_mod->loan = json_encode($existingLoans);
                    $salary_mod->save();
                }
            }

            // Redirect with a success message
            return redirect()->route('salarymodtemplate.index')->with('success', __('Loan created successfully.'));

        }else if($request->input('mod_type') == 'saturation_deduction'){

            // Validate the request data
            $validatedData = $request->validate([
                'saturation_deduction_option_id' => 'required|integer',
                'saturation_deduction_name' => 'required|string|max:255',
                'saturation_deduction_amount_type' => 'required|string|in:fixed,percentage',
                'saturation_deduction_amount' => 'required|numeric',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);

            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';

            // Prepare the saturation deduction data
            $deductionData = [
                'option_id' => $validatedData['saturation_deduction_option_id'],
                'name' => $validatedData['saturation_deduction_name'],
                'amount_type' => $validatedData['saturation_deduction_amount_type'],
                'amount' => $validatedData['saturation_deduction_amount'],
                'threshold' => $salaryThreshold,
                'min_salary' => $validatedData['min_salary'] ?? null,
                'max_salary' => $validatedData['max_salary'] ?? null,
            ];

            // Check to see if salary modification entry exists already
            $salary_mod = SalaryModificationTemplate::where('created_by', '=', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();
                
            if (!$salary_mod) {
                // Entry does not exist, create a parent array and append this into it
                $parentArray = [$deductionData['name'] => $deductionData];
                $salaryModTemplate = new SalaryModificationTemplate();
                $salaryModTemplate->name = 'Template' . creatorId();
                $salaryModTemplate->saturation_deduction = json_encode($parentArray);
                $salaryModTemplate->workspace = getActiveWorkSpace();
                $salaryModTemplate->created_by = auth()->user()->id;
                $salaryModTemplate->save();
            } else {
                // check to see if a parent array exist for the saturation_deduction in the salary_mod
                $existingDeductions = json_decode($salary_mod->saturation_deduction, true);
                if ($existingDeductions !== null){
                    // Check if the Deduction already exists
                    if (!array_key_exists($deductionData['name'], $existingDeductions)) {
                        // Deduction does not exist, append it
                        $existingDeductions[$deductionData['name']] = $deductionData;
                        $salary_mod->saturation_deduction = json_encode($existingDeductions);
                        $salary_mod->save();
                    } else {
                        return redirect()->back()->with('error', __('Deduction already exists.'));
                    }
                }else{
                    // no parent array exist
                    $existingDeductions = [];
                    $existingDeductions[$deductionData['name']] = $deductionData;
                    $salary_mod->saturation_deduction = json_encode($existingDeductions);
                    $salary_mod->save();
                }
            }

            // Redirect with a success message
            return redirect()->route('salarymodtemplate.index')->with('success', __('Deduction created successfully.'));

        }else if($request->input('mod_type') == 'tax_deduction'){

            // Validate the request data
            $validatedData = $request->validate([
                'tax_deduction_name' => 'required|string|max:255',
                'tax_deduction_amount_type' => 'required|string|in:fixed,percentage',
                'tax_deduction_amount' => 'required|numeric',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);

            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';

            // Prepare the tax deduction data
            $taxDeductionData = [
                'name' => $validatedData['tax_deduction_name'],
                'amount_type' => $validatedData['tax_deduction_amount_type'],
                'amount' => $validatedData['tax_deduction_amount'],
                'threshold' => $salaryThreshold,
                'min_salary' => $validatedData['min_salary'] ?? null,
                'max_salary' => $validatedData['max_salary'] ?? null,
            ];

            // Check to see if salary modification entry exists already
            $salary_mod = SalaryModificationTemplate::where('created_by', '=', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();
                
            if (!$salary_mod) {
                // Entry does not exist, create a parent array and append this into it
                $parentArray = [$taxDeductionData['name'] => $taxDeductionData];
                $salaryModTemplate = new SalaryModificationTemplate();
                $salaryModTemplate->name = 'Template' . creatorId();
                $salaryModTemplate->tax_deduction = json_encode($parentArray);
                $salaryModTemplate->workspace = getActiveWorkSpace();
                $salaryModTemplate->created_by = auth()->user()->id;
                $salaryModTemplate->save();
            } else {
                // check to see if a parent array exist for the tax deduction in the salary_mod
                $existingTaxDeductions = json_decode($salary_mod->tax_deduction, true);
                if ($existingTaxDeductions !== null){
                    // Check if the tax deduction already exists
                    if (!array_key_exists($taxDeductionData['name'], $existingTaxDeductions)) {
                        // Tax Deduction does not exist, append it
                        $existingTaxDeductions[$taxDeductionData['name']] = $taxDeductionData;
                        $salary_mod->tax_deduction = json_encode($existingTaxDeductions);
                        $salary_mod->save();
                    } else {
                        return redirect()->back()->with('error', __('Tax Deduction already exists.'));
                    }
                }else{
                    // no parent array exist
                    $existingTaxDeductions = [];
                    $existingTaxDeductions[$taxDeductionData['name']] = $taxDeductionData;
                    $salary_mod->tax_deduction = json_encode($existingTaxDeductions);
                    $salary_mod->save();
                }
            }

            // Redirect with a success message
            return redirect()->route('salarymodtemplate.index')->with('success', __('Tax Deduction created successfully.'));

        }else if ($request->input('mod_type') == 'tax_relief') {

            // Validate the request data
            $validatedData = $request->validate([
                'tax_relief_name' => 'required|string|max:255',
                'tax_relief_amount_type' => 'required|string|in:fixed,percentage',
                'tax_relief_amount' => 'required|numeric',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);
        
            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';
        
            // Prepare the tax relief data
            $taxReliefData = [
                'name' => $validatedData['tax_relief_name'],
                'amount_type' => $validatedData['tax_relief_amount_type'],
                'amount' => $validatedData['tax_relief_amount'],
                'threshold' => $salaryThreshold,
                'min_salary' => $validatedData['min_salary'] ?? null,
                'max_salary' => $validatedData['max_salary'] ?? null,
            ];
        
            // Check to see if salary modification entry exists already
            $salary_mod = SalaryModificationTemplate::where('created_by', '=', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();
        
            if (!$salary_mod) {
                // Entry does not exist, create a parent array and append this into it
                $parentArray = [$taxReliefData['name'] => $taxReliefData];
                $salaryModTemplate = new SalaryModificationTemplate();
                $salaryModTemplate->name = 'Template' . creatorId();
                $salaryModTemplate->tax_relief = json_encode($parentArray);
                $salaryModTemplate->workspace = getActiveWorkSpace();
                $salaryModTemplate->created_by = auth()->user()->id;
                $salaryModTemplate->save();
            } else {
                // check to see if a parent array exist for the tax relief in the salary_mod
                $existingTaxReliefs = json_decode($salary_mod->tax_relief, true);
                if ($existingTaxReliefs !== null) {
                    // Check if the tax relief already exists
                    if (!array_key_exists($taxReliefData['name'], $existingTaxReliefs)) {
                        // Tax Relief does not exist, append it
                        $existingTaxReliefs[$taxReliefData['name']] = $taxReliefData;
                        $salary_mod->tax_relief = json_encode($existingTaxReliefs);
                        $salary_mod->save();
                    } else {
                        return redirect()->back()->with('error', __('Tax Relief already exists.'));
                    }
                } else {
                    // no parent array exist
                    $existingTaxReliefs = [];
                    $existingTaxReliefs[$taxReliefData['name']] = $taxReliefData;
                    $salary_mod->tax_relief = json_encode($existingTaxReliefs);
                    $salary_mod->save();
                }
            }
        
            // Redirect with a success message
            return redirect()->route('salarymodtemplate.index')->with('success', __('Tax Relief created successfully.'));

        }else if($request->input('mod_type') == 'other_payment'){
            
            // Validate the request data
            $validatedData = $request->validate([
                'other_payment_name' => 'required|string|max:255',
                'other_payment_amount_type' => 'required|string|in:fixed,percentage',
                'other_payment_amount' => 'required|numeric',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);

            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';

            // Prepare the other payment data
            $otherPaymentData = [
                'name' => $validatedData['other_payment_name'],
                'amount_type' => $validatedData['other_payment_amount_type'],
                'amount' => $validatedData['other_payment_amount'],
                'threshold' => $salaryThreshold,
                'min_salary' => $validatedData['min_salary'] ?? null,
                'max_salary' => $validatedData['max_salary'] ?? null,
            ];

            // Check to see if salary modification entry exists already
            $salary_mod = SalaryModificationTemplate::where('created_by', '=', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();

            if (!$salary_mod) {
                // Entry does not exist, create a parent array and append this into it
                $parentArray = [$otherPaymentData['name'] => $otherPaymentData];
                $salaryModTemplate = new SalaryModificationTemplate();
                $salaryModTemplate->name = 'Template' . creatorId();
                $salaryModTemplate->other_payment = json_encode($parentArray);
                $salaryModTemplate->workspace = getActiveWorkSpace();
                $salaryModTemplate->created_by = auth()->user()->id;
                $salaryModTemplate->save();
            } else {
                // check to see if a parent array exist for the other payment in the salary_mod
                $existingOtherPayments = json_decode($salary_mod->other_payment, true);
                if ($existingOtherPayments !== null) {
                    // Check if the other payment already exists
                    if (!array_key_exists($otherPaymentData['name'], $existingOtherPayments)) {
                        // Other Payment does not exist, append it
                        $existingOtherPayments[$otherPaymentData['name']] = $otherPaymentData;
                        $salary_mod->other_payment = json_encode($existingOtherPayments);
                        $salary_mod->save();
                    } else {
                        return redirect()->back()->with('error', __('Other Payment already exists.'));
                    }
                } else {
                    // no parent array exist
                    $existingOtherPayments = [];
                    $existingOtherPayments[$otherPaymentData['name']] = $otherPaymentData;
                    $salary_mod->other_payment = json_encode($existingOtherPayments);
                    $salary_mod->save();
                }
            }

            // Redirect with a success message
            return redirect()->route('salarymodtemplate.index')->with('success', __('Other Payment created successfully.'));
            
        }else if($request->input('mod_type') == 'overtime'){
            
            // Validate the request data
            $validatedData = $request->validate([
                'overtime_name' => 'required|string|max:255',
                'no_of_days' => 'required|numeric',
                'hours' => 'required|numeric',
                'rate' => 'required|numeric',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);

            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';

            // Prepare the overtime data
            $overtimeData = [
                'name' => $validatedData['overtime_name'],
                'no_of_days' => $validatedData['no_of_days'],
                'hours' => $validatedData['hours'],
                'rate' => $validatedData['rate'],
                'threshold' => $salaryThreshold,
                'min_salary' => $validatedData['min_salary'] ?? null,
                'max_salary' => $validatedData['max_salary'] ?? null,
            ];

            // Check to see if salary modification entry exists already
            $salary_mod = SalaryModificationTemplate::where('created_by', '=', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();

            if (!$salary_mod) {
                // Entry does not exist, create a parent array and append this into it
                $parentArray = [$overtimeData['name'] => $overtimeData];
                $salaryModTemplate = new SalaryModificationTemplate();
                $salaryModTemplate->name = 'Template' . creatorId();
                $salaryModTemplate->overtime = json_encode($parentArray);
                $salaryModTemplate->workspace = getActiveWorkSpace();
                $salaryModTemplate->created_by = auth()->user()->id;
                $salaryModTemplate->save();
            } else {
                // check to see if a parent array exist for the overtime in the salary_mod
                $existingOvertimes = json_decode($salary_mod->overtime, true);
                if ($existingOvertimes !== null) {
                    // Check if the overtime already exists
                    if (!array_key_exists($overtimeData['name'], $existingOvertimes)) {
                        // Overtime does not exist, append it
                        $existingOvertimes[$overtimeData['name']] = $overtimeData;
                        $salary_mod->overtime = json_encode($existingOvertimes);
                        $salary_mod->save();
                    } else {
                        return redirect()->back()->with('error', __('Overtime already exists.'));
                    }
                } else {
                    // no parent array exist
                    $existingOvertimes = [];
                    $existingOvertimes[$overtimeData['name']] = $overtimeData;
                    $salary_mod->overtime = json_encode($existingOvertimes);
                    $salary_mod->save();
                }
            }

            // Redirect with a success message
            return redirect()->route('salarymodtemplate.index')->with('success', __('Overtime created successfully.'));
            
        }else{
            return redirect()->back()->with('error', __('An error occured.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('hrm::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('hrm::edit');
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

    public function update_entry(Request $request, $entry, $name){
        
        if ($entry == 'allowance'){

            // Validate the request data
            $validatedData = $request->validate([
                'allowance_option_id' => 'required|integer',
                'allowance_name' => 'required|string|max:255',
                'allowance_amount_type' => 'required|string|in:fixed,percentage',
                'allowance_amount' => 'required|numeric',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);
        
            // boolean state for salary threshold
            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';
        
            // if salaryThreshold is false set min_salary and max_salary to null
            if($salaryThreshold){
                $min = $validatedData['min_salary'] ?? null;
                $max = $validatedData['max_salary'] ?? null;
            }else{
                $min = null;
                $max = null;
            }
        
            // get the salary modification entry
            $salary_mod = SalaryModificationTemplate::where('created_by', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();
        
            // Check if the salary_mod entry exists
            if (!$salary_mod) {
                return redirect()->back()->with('error', __('Salary modification entry not found.'));
            }
        
            // Decode the JSON string into an associative array
            $existing_entries = json_decode($salary_mod->$entry, true);
        
            // Check if the entry exists in the array
            if (!array_key_exists($name, $existing_entries)) {
                return redirect()->back()->with('error', __('Entry does not exist.'));
            }
        
            // Remove the old entry
            unset($existing_entries[$name]);
        
            // Add the updated entry with the new name
            $existing_entries[$validatedData['allowance_name']] = [
                'option_id' => $validatedData['allowance_option_id'],
                'name' => $validatedData['allowance_name'],
                'amount_type' => $validatedData['allowance_amount_type'],
                'amount' => $validatedData['allowance_amount'],
                'threshold' => $salaryThreshold,
                'min_salary' => $min,
                'max_salary' => $max,
            ];
        
            // Encode the modified array back into a JSON string
            $updated_entries_json = json_encode($existing_entries);
        
            // Update the database field with the new JSON string
            $salary_mod->$entry = $updated_entries_json;
            $salary_mod->save();
        
            // Redirect back with success message
            return redirect()->back()->with('success', __('Entry updated successfully.'));
        
        }else if($entry == 'commission'){

            // Validate the request data
            $validatedData = $request->validate([
                'commission_name' => 'required|string|max:255',
                'commission_amount_type' => 'required|string|in:fixed,percentage',
                'commission_amount' => 'required|numeric',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);
        
            // boolean state for salary threshold
            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';
        
            // if salaryThreshold is false set min_salary and max_salary to null
            if($salaryThreshold){
                $min = $validatedData['min_salary'] ?? null;
                $max = $validatedData['max_salary'] ?? null;
            }else{
                $min = null;
                $max = null;
            }
        
            // get the salary modification entry
            $salary_mod = SalaryModificationTemplate::where('created_by', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();
        
            // Check if the salary_mod entry exists
            if (!$salary_mod) {
                return redirect()->back()->with('error', __('Salary modification entry not found.'));
            }
        
            // Decode the JSON string into an associative array
            $existing_entries = json_decode($salary_mod->$entry, true);
        
            // Check if the entry exists in the array
            if (!array_key_exists($name, $existing_entries)) {
                return redirect()->back()->with('error', __('Entry does not exist.'));
            }
        
            // Remove the old entry
            unset($existing_entries[$name]);
        
            // Add the updated entry with the new name
            $existing_entries[$validatedData['commission_name']] = [
                'name' => $validatedData['commission_name'],
                'amount_type' => $validatedData['commission_amount_type'],
                'amount' => $validatedData['commission_amount'],
                'threshold' => $salaryThreshold,
                'min_salary' => $min,
                'max_salary' => $max,
            ];
        
            // Encode the modified array back into a JSON string
            $updated_entries_json = json_encode($existing_entries);
        
            // Update the database field with the new JSON string
            $salary_mod->$entry = $updated_entries_json;
            $salary_mod->save();
        
            // Redirect back with success message
            return redirect()->back()->with('success', __('Entry updated successfully.'));
        
        }else if($entry == 'loan'){

            // Validate the request data
            $validatedData = $request->validate([
                'loan_option_id' => 'required|integer',
                'loan_name' => 'required|string|max:255',
                'loan_amount_type' => 'required|string|in:fixed,percentage',
                'loan_amount' => 'required|numeric',
                'start_date' => 'required',
                'end_date' => 'required',
                'loan_reason' => 'nullable',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);
        
            // boolean state for salary threshold
            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';
        
            // if salaryThreshold is false set min_salary and max_salary to null
            if($salaryThreshold){
                $min = $validatedData['min_salary'] ?? null;
                $max = $validatedData['max_salary'] ?? null;
            }else{
                $min = null;
                $max = null;
            }
        
            // get the salary modification entry
            $salary_mod = SalaryModificationTemplate::where('created_by', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();
        
            // Check if the salary_mod entry exists
            if (!$salary_mod) {
                return redirect()->back()->with('error', __('Salary modification entry not found.'));
            }
        
            // Decode the JSON string into an associative array
            $existing_entries = json_decode($salary_mod->$entry, true);
        
            // Check if the entry exists in the array
            if (!array_key_exists($name, $existing_entries)) {
                return redirect()->back()->with('error', __('Entry does not exist.'));
            }
        
            // Remove the old entry
            unset($existing_entries[$name]);
        
            // Add the updated entry with the new name
            $existing_entries[$validatedData['loan_name']] = [
                'option_id' => $validatedData['loan_option_id'],
                'name' => $validatedData['loan_name'],
                'amount_type' => $validatedData['loan_amount_type'],
                'amount' => $validatedData['loan_amount'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'reason' => $validatedData['loan_reason'],
                'threshold' => $salaryThreshold,
                'min_salary' => $min,
                'max_salary' => $max,
            ];
        
            // Encode the modified array back into a JSON string
            $updated_entries_json = json_encode($existing_entries);
        
            // Update the database field with the new JSON string
            $salary_mod->$entry = $updated_entries_json;
            $salary_mod->save();
        
            // Redirect back with success message
            return redirect()->back()->with('success', __('Entry updated successfully.'));
        
        }else if($entry == 'saturation_deduction'){

            // Validate the request data
            $validatedData = $request->validate([
                'saturation_deduction_option_id' => 'required|integer',
                'saturation_deduction_name' => 'required|string|max:255',
                'saturation_deduction_amount_type' => 'required|string|in:fixed,percentage',
                'saturation_deduction_amount' => 'required|numeric',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);
        
            // boolean state for salary threshold
            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';
        
            // if salaryThreshold is false set min_salary and max_salary to null
            if($salaryThreshold){
                $min = $validatedData['min_salary'] ?? null;
                $max = $validatedData['max_salary'] ?? null;
            }else{
                $min = null;
                $max = null;
            }
        
            // get the salary modification entry
            $salary_mod = SalaryModificationTemplate::where('created_by', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();
        
            // Check if the salary_mod entry exists
            if (!$salary_mod) {
                return redirect()->back()->with('error', __('Salary modification entry not found.'));
            }
        
            // Decode the JSON string into an associative array
            $existing_entries = json_decode($salary_mod->$entry, true);
        
            // Check if the entry exists in the array
            if (!array_key_exists($name, $existing_entries)) {
                return redirect()->back()->with('error', __('Entry does not exist.'));
            }
        
            // Remove the old entry
            unset($existing_entries[$name]);
        
            // Add the updated entry with the new name
            $existing_entries[$validatedData['saturation_deduction_name']] = [
                'option_id' => $validatedData['saturation_deduction_option_id'],
                'name' => $validatedData['saturation_deduction_name'],
                'amount_type' => $validatedData['saturation_deduction_amount_type'],
                'amount' => $validatedData['saturation_deduction_amount'],
                'threshold' => $salaryThreshold,
                'min_salary' => $min,
                'max_salary' => $max,
            ];
        
            // Encode the modified array back into a JSON string
            $updated_entries_json = json_encode($existing_entries);
        
            // Update the database field with the new JSON string
            $salary_mod->$entry = $updated_entries_json;
            $salary_mod->save();
        
            // Redirect back with success message
            return redirect()->back()->with('success', __('Entry updated successfully.'));
        
        }else if($entry == 'tax_deduction'){

            // Validate the request data
            $validatedData = $request->validate([
                'tax_deduction_name' => 'required|string|max:255',
                'tax_deduction_amount_type' => 'required|string|in:fixed,percentage',
                'tax_deduction_amount' => 'required|numeric',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);
        
            // boolean state for salary threshold
            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';
        
            // if salaryThreshold is false set min_salary and max_salary to null
            if($salaryThreshold){
                $min = $validatedData['min_salary'] ?? null;
                $max = $validatedData['max_salary'] ?? null;
            }else{
                $min = null;
                $max = null;
            }
        
            // get the salary modification entry
            $salary_mod = SalaryModificationTemplate::where('created_by', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();
        
            // Check if the salary_mod entry exists
            if (!$salary_mod) {
                return redirect()->back()->with('error', __('Salary modification entry not found.'));
            }
        
            // Decode the JSON string into an associative array
            $existing_entries = json_decode($salary_mod->$entry, true);
        
            // Check if the entry exists in the array
            if (!array_key_exists($name, $existing_entries)) {
                return redirect()->back()->with('error', __('Entry does not exist.'));
            }
        
            // Remove the old entry
            unset($existing_entries[$name]);
        
            // Add the updated entry with the new name
            $existing_entries[$validatedData['tax_deduction_name']] = [
                'name' => $validatedData['tax_deduction_name'],
                'amount_type' => $validatedData['tax_deduction_amount_type'],
                'amount' => $validatedData['tax_deduction_amount'],
                'threshold' => $salaryThreshold,
                'min_salary' => $min,
                'max_salary' => $max,
            ];
        
            // Encode the modified array back into a JSON string
            $updated_entries_json = json_encode($existing_entries);
        
            // Update the database field with the new JSON string
            $salary_mod->$entry = $updated_entries_json;
            $salary_mod->save();
        
            // Redirect back with success message
            return redirect()->back()->with('success', __('Entry updated successfully.'));
        
        }else if($entry == 'tax_relief'){

            // Validate the request data
            $validatedData = $request->validate([
                'tax_relief_name' => 'required|string|max:255',
                'tax_relief_amount_type' => 'required|string|in:fixed,percentage',
                'tax_relief_amount' => 'required|numeric',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);
        
            // boolean state for salary threshold
            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';
        
            // if salaryThreshold is false set min_salary and max_salary to null
            if ($salaryThreshold) {
                $min = $validatedData['min_salary'] ?? null;
                $max = $validatedData['max_salary'] ?? null;
            } else {
                $min = null;
                $max = null;
            }
        
            // get the salary modification entry
            $salary_mod = SalaryModificationTemplate::where('created_by', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();
        
            // Check if the salary_mod entry exists
            if (!$salary_mod) {
                return redirect()->back()->with('error', __('Salary modification entry not found.'));
            }
        
            // Decode the JSON string into an associative array
            $existing_entries = json_decode($salary_mod->$entry, true);
        
            // Check if the entry exists in the array
            if (!array_key_exists($name, $existing_entries)) {
                return redirect()->back()->with('error', __('Entry does not exist.'));
            }
        
            // Remove the old entry
            unset($existing_entries[$name]);
        
            // Add the updated entry with the new name
            $existing_entries[$validatedData['tax_relief_name']] = [
                'name' => $validatedData['tax_relief_name'],
                'amount_type' => $validatedData['tax_relief_amount_type'],
                'amount' => $validatedData['tax_relief_amount'],
                'threshold' => $salaryThreshold,
                'min_salary' => $min,
                'max_salary' => $max,
            ];
        
            // Encode the modified array back into a JSON string
            $updated_entries_json = json_encode($existing_entries);
        
            // Update the database field with the new JSON string
            $salary_mod->$entry = $updated_entries_json;
            $salary_mod->save();
        
            // Redirect back with success message
            return redirect()->back()->with('success', __('Entry updated successfully.'));
        }else if($entry == 'other_payment'){

            // Validate the request data
            $validatedData = $request->validate([
                'other_payment_name' => 'required|string|max:255',
                'other_payment_amount_type' => 'required|string|in:fixed,percentage',
                'other_payment_amount' => 'required|numeric',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);
        
            // boolean state for salary threshold
            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';
        
            // if salaryThreshold is false set min_salary and max_salary to null
            if ($salaryThreshold) {
                $min = $validatedData['min_salary'] ?? null;
                $max = $validatedData['max_salary'] ?? null;
            } else {
                $min = null;
                $max = null;
            }
        
            // get the salary modification entry
            $salary_mod = SalaryModificationTemplate::where('created_by', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();
        
            // Check if the salary_mod entry exists
            if (!$salary_mod) {
                return redirect()->back()->with('error', __('Salary modification entry not found.'));
            }
        
            // Decode the JSON string into an associative array
            $existing_entries = json_decode($salary_mod->$entry, true);
        
            // Check if the entry exists in the array
            if (!array_key_exists($name, $existing_entries)) {
                return redirect()->back()->with('error', __('Entry does not exist.'));
            }
        
            // Remove the old entry
            unset($existing_entries[$name]);
        
            // Add the updated entry with the new name
            $existing_entries[$validatedData['other_payment_name']] = [
                'name' => $validatedData['other_payment_name'],
                'amount_type' => $validatedData['other_payment_amount_type'],
                'amount' => $validatedData['other_payment_amount'],
                'threshold' => $salaryThreshold,
                'min_salary' => $min,
                'max_salary' => $max,
            ];
        
            // Encode the modified array back into a JSON string
            $updated_entries_json = json_encode($existing_entries);
        
            // Update the database field with the new JSON string
            $salary_mod->$entry = $updated_entries_json;
            $salary_mod->save();
        
            // Redirect back with success message
            return redirect()->back()->with('success', __('Entry updated successfully.'));

        }else if($entry == 'overtime'){

            // Validate the request data
            $validatedData = $request->validate([
                'overtime_name' => 'required|string|max:255',
                'no_of_days' => 'required|numeric',
                'hours' => 'required|numeric',
                'rate' => 'required|numeric',
                'salary_threshold' => 'nullable',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);
        
            // boolean state for salary threshold
            $salaryThreshold = $request->has('salary_threshold') && $request->input('salary_threshold') === 'on';
        
            // if salaryThreshold is false set min_salary and max_salary to null
            if ($salaryThreshold) {
                $min = $validatedData['min_salary'] ?? null;
                $max = $validatedData['max_salary'] ?? null;
            } else {
                $min = null;
                $max = null;
            }
        
            // get the salary modification entry
            $salary_mod = SalaryModificationTemplate::where('created_by', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->first();
        
            // Check if the salary_mod entry exists
            if (!$salary_mod) {
                return redirect()->back()->with('error', __('Salary modification entry not found.'));
            }
        
            // Decode the JSON string into an associative array
            $existing_entries = json_decode($salary_mod->$entry, true);
        
            // Check if the entry exists in the array
            if (!array_key_exists($name, $existing_entries)) {
                return redirect()->back()->with('error', __('Entry does not exist.'));
            }
        
            // Remove the old entry
            unset($existing_entries[$name]);
        
            // Add the updated entry with the new name
            $existing_entries[$validatedData['overtime_name']] = [
                'name' => $validatedData['overtime_name'],
                'no_of_days' => $validatedData['no_of_days'],
                'hours' => $validatedData['hours'],
                'rate' => $validatedData['rate'],
                'threshold' => $salaryThreshold,
                'min_salary' => $min,
                'max_salary' => $max,
            ];
        
            // Encode the modified array back into a JSON string
            $updated_entries_json = json_encode($existing_entries);
        
            // Update the database field with the new JSON string
            $salary_mod->$entry = $updated_entries_json;
            $salary_mod->save();
        
            // Redirect back with success message
            return redirect()->back()->with('success', __('Entry updated successfully.'));

        }

    }
    
    public function delete_entry($entry, $name){
        // get the salary modification entry
        $salary_mod = SalaryModificationTemplate::where('created_by', creatorId())
            ->where('workspace', getActiveWorkSpace())
            ->first();
        
        // Check if the salary_mod entry exists
        if (!$salary_mod) {
            return redirect()->back()->with('error', __('Salary modification entry not found.'));
        }
        
        // Decode the JSON string into an associative array
        $existing_entries = json_decode($salary_mod->$entry, true);
    
        // Check if the entry exists in the array
        if (!array_key_exists($name, $existing_entries)) {
            return redirect()->back()->with('error', __('Entry does not exist.'));
        }
    
        // Remove the entry from the array
        unset($existing_entries[$name]);
    
        // Encode the modified array back into a JSON string
        $updated_entries_json = json_encode($existing_entries);
    
        // Update the database field with the new JSON string
        $salary_mod->$entry = $updated_entries_json;
        $salary_mod->save();
    
        // Redirect back with success message
        return redirect()->back()->with('success', __('Entry deleted successfully.'));
    }
    
}
