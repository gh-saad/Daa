<?php

namespace Modules\Hrm\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Hrm\Entities\Allowance;
use Modules\Hrm\Entities\AllowanceOption;
use Modules\Hrm\Entities\Commission;
use Modules\Hrm\Entities\DeductionOption;
use Modules\Hrm\Entities\Employee;
use Modules\Hrm\Entities\Loan;
use Modules\Hrm\Entities\LoanOption;
use Modules\Hrm\Entities\OtherPayment;
use Modules\Hrm\Entities\Overtime;
use Modules\Hrm\Entities\PayslipType;
use Modules\Hrm\Entities\SaturationDeduction;
use Modules\Hrm\Entities\TaxDeduction;
use Modules\Hrm\Entities\TaxRelief;
use Modules\Hrm\Events\UpdateEmployeeSalary;

class SetSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if(Auth::user()->can('setsalary manage'))
        {
            if(!in_array(Auth::user()->type, Auth::user()->not_emp_type))
            {
                $employees = Employee::where('user_id',Auth::user()->id)->where('workspace',getActiveWorkSpace())->get();
            }
            else
            {
                $employees = Employee::where('workspace',getActiveWorkSpace())->get();
            }
            return view('hrm::setsalary.index', compact('employees'));
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
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $payslip_type      = PayslipType::where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
        $allowance_options = AllowanceOption::where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
        $loan_options      = LoanOption::where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
        $deduction_options = DeductionOption::where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
        if(!in_array(Auth::user()->type, Auth::user()->not_emp_type))
        {
            $currentEmployee      = Employee::where('user_id', '=', \Auth::user()->id)->where('workspace',getActiveWorkSpace())->first();
            $allowances           = Allowance::where('employee_id', $currentEmployee->id)->where('workspace',getActiveWorkSpace())->get();
            $commissions          = Commission::where('employee_id', $currentEmployee->id)->where('workspace',getActiveWorkSpace())->get();
            $loans                = Loan::where('employee_id', $currentEmployee->id)->where('workspace',getActiveWorkSpace())->get();
            $saturationdeductions = SaturationDeduction::where('employee_id', $currentEmployee->id)->where('workspace',getActiveWorkSpace())->get();
            $taxdeductions        = TaxDeduction::where('employee_id', $currentEmployee->id)->where('workspace',getActiveWorkSpace())->get();
            $taxreliefs           = TaxRelief::where('employee_id', $currentEmployee->id)->where('workspace',getActiveWorkSpace())->get();
            $otherpayments        = OtherPayment::where('employee_id', $currentEmployee->id)->where('workspace',getActiveWorkSpace())->get();
            $overtimes            = Overtime::where('employee_id', $currentEmployee->id)->where('workspace',getActiveWorkSpace())->get();
            $employee             = Employee::where('user_id', '=', \Auth::user()->id)->where('workspace',getActiveWorkSpace())->first();

            foreach ( $allowances as  $value)
            {
                if(  $value->type == 'percentage' )
                {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ( $commissions as  $value)
            {
                if(  $value->type == 'percentage' )
                {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ( $loans as  $value)
            {
                if(  $value->type == 'percentage' )
                {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ( $saturationdeductions as  $value)
            {
                if(  $value->type == 'percentage' )
                {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ( $otherpayments as  $value)
            {
                if(  $value->type == 'percentage' )
                {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }
            return view('hrm::setsalary.employee_salary', compact('employee', 'payslip_type', 'allowance_options', 'commissions', 'loan_options', 'overtimes', 'otherpayments', 'saturationdeductions', 'taxdeductions', 'taxreliefs', 'loans', 'deduction_options', 'allowances'));

        }
        else
        {
            $allowances           = Allowance::where('employee_id', $id)->where('workspace',getActiveWorkSpace())->get();
            $commissions          = Commission::where('employee_id', $id)->where('workspace',getActiveWorkSpace())->get();
            $loans                = Loan::where('employee_id', $id)->where('workspace',getActiveWorkSpace())->get();
            $saturationdeductions = SaturationDeduction::where('employee_id', $id)->where('workspace',getActiveWorkSpace())->get();
            $taxdeductions        = TaxDeduction::where('employee_id', $id)->where('workspace',getActiveWorkSpace())->get();
            $taxreliefs           = TaxRelief::where('employee_id', $id)->where('workspace',getActiveWorkSpace())->get();
            $otherpayments        = OtherPayment::where('employee_id', $id)->where('workspace',getActiveWorkSpace())->get();
            $overtimes            = Overtime::where('employee_id', $id)->where('workspace',getActiveWorkSpace())->get();
            $employee             = Employee::find($id);

            foreach ( $allowances as  $value)
            {
                if(  $value->type == 'percentage' )
                {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ( $commissions as  $value)
            {
                if(  $value->type == 'percentage' )
                {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ( $loans as  $value)
            {
                if(  $value->type == 'percentage' )
                {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ( $saturationdeductions as  $value)
            {
                if(  $value->type == 'percentage' )
                {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ( $otherpayments as  $value)
            {
                if(  $value->type == 'percentage' )
                {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            return view('hrm::setsalary.employee_salary', compact('employee', 'payslip_type', 'allowance_options', 'commissions', 'loan_options', 'overtimes', 'otherpayments', 'saturationdeductions', 'taxdeductions', 'taxreliefs', 'loans', 'deduction_options', 'allowances'));
        }
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

    public function employeeBasicSalary($id)
    {
        $payslip_type = PayslipType::where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
        $employee     = Employee::find($id);
        return view('hrm::setsalary.basic_salary', compact('employee', 'payslip_type'));
    }
    
    public function employeeUpdateSalary(Request $request, $id)
    {
        // validate request
        $validator = \Validator::make(
            $request->all(), [
                               'salary_type' => 'required',
                               'salary' => 'required|numeric|min:0',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        try {
            // find employee by id
            $employee = Employee::findOrFail($id);
        
            // round the salary to 2 decimal points
            $salary = round($request->input('salary'), 2);
            
            // update the employee salary
            $employee->fill([
                'salary_type' => $request->input('salary_type'),
                'salary' => round($salary, 2),
            ])->save();
        
            // Remove all deductions and additions
            SaturationDeduction::where('employee_id', '=', $employee->id)->delete();
            Allowance::where('employee_id', '=', $employee->id)->delete();
            Commission::where('employee_id', '=', $employee->id)->delete();
            Loan::where('employee_id', '=', $employee->id)->delete();
            OtherPayment::where('employee_id', '=', $employee->id)->delete();
            Overtime::where('employee_id', '=', $employee->id)->delete();
            TaxDeduction::where('employee_id', '=', $employee->id)->delete();
            TaxRelief::where('employee_id', '=', $employee->id)->delete();
        
            // Store id of deduction option
            $ddo_id = 1;
        
            // Check if deduction option is available
            $deduction_option_exist = DeductionOption::where('name', '=', 'nssf')->get();
            if ($deduction_option_exist->isEmpty()) {
                // Create a new deduction option
                $deductionoption = new DeductionOption();
                $deductionoption->name = 'nssf';
                $deductionoption->workspace = getActiveWorkSpace();
                $deductionoption->created_by = creatorId();
                $deductionoption->save();
                // Store id in $ddo_id variable
                $ddo_id = $deductionoption->id;
            } else {
                // Deduction option entry found, save its id to $ddo_id variable
                $ddo_id = $deduction_option_exist->first()->id;
            }
        
            // Saturation Deduction Section
            
            // check to see if it can be applied
            if ($employee->salary > 2160){
                // Create New Saturation Deduction
                $saturationdeduction                   = new SaturationDeduction;
                $saturationdeduction->employee_id      = $employee->id;
                $saturationdeduction->deduction_option = $ddo_id;
                $saturationdeduction->title            = 'NSSF Deduction';
                $saturationdeduction->type             = 'fixed';
                $saturationdeduction->amount           = 2160;
                $saturationdeduction->workspace        = getActiveWorkSpace();
                $saturationdeduction->created_by       = creatorId();
                $saturationdeduction->save();    
            }
            // END Saturation Deduction Section
        
            // Tax Deduction Section
        
            // Taxable Salary
            $taxable_salary = $employee->salary - 2160;
            $remaining_salary = $taxable_salary;
            $combined = 0;
        
            // Check if taxable salary is within the first tax bracket (0 - 24000) 10%
            if ($taxable_salary > 0) {
                if ($taxable_salary <= 24000) {
                    // If taxable salary is within the bracket
                    $tax_deduction_rate = 0.10; // 10%
                    $tax_deduction_calculated = round($taxable_salary * $tax_deduction_rate, 2);
                    $difference = $taxable_salary;
                    
                    // Create tax deduction
                    $taxdeduction = new TaxDeduction;
                    $taxdeduction->employee_id = $employee->id;
                    $taxdeduction->title = 'First Tax Bracket';
                    $taxdeduction->salary_amount = round($taxable_salary, 2);
                    $taxdeduction->difference = round($difference, 2);
                    $taxdeduction->tax_deduction_value_type = 'percentage';
                    $taxdeduction->tax_deduction_value = '10';
                    $taxdeduction->tax_deduction_calculated = $tax_deduction_calculated;
                    $taxdeduction->workspace = getActiveWorkSpace();
                    $taxdeduction->created_by = creatorId();
                    $taxdeduction->save();
                    
                    // Set remaining salary to 0 as it has been fully taxed
                    $remaining_salary = 0;
                } else {
                    // If taxable salary exceeds the first bracket
                    $tax_deduction_rate = 0.10; // 10%
                    $tax_deduction_calculated = round(24000 * $tax_deduction_rate, 2);
                    $difference = 24000;
                    
                    // Create tax deduction for the first bracket
                    $taxdeduction = new TaxDeduction;
                    $taxdeduction->employee_id = $employee->id;
                    $taxdeduction->title = 'First Tax Bracket';
                    $taxdeduction->salary_amount = round($combined + $difference, 2);
                    $taxdeduction->difference = round($difference, 2);
                    $taxdeduction->tax_deduction_value_type = 'percentage';
                    $taxdeduction->tax_deduction_value = '10';
                    $taxdeduction->tax_deduction_calculated = $tax_deduction_calculated;
                    $taxdeduction->workspace = getActiveWorkSpace();
                    $taxdeduction->created_by = creatorId();
                    $taxdeduction->save();
                    
                    // Calculate remaining salary
                    $remaining_salary = round($taxable_salary - 24000, 2);
                    $combined += $taxdeduction->difference;
                }
            }
            // END First Tax Bracket
        
            // Second Tax Bracket (24001 - 32333) 25%
            // Check if remaining salary is within the second tax bracket (24001 - 32333) 25%
            if ($remaining_salary > 0) {
                if ($remaining_salary <= (32333 - 24000)) {
                    // If remaining salary is within the second bracket
                    $tax_deduction_rate = 0.25; // 25%
                    $tax_deduction_calculated = round($remaining_salary * $tax_deduction_rate, 2);
                    $difference = $remaining_salary;
                    
                    // Create tax deduction for the second bracket
                    $taxdeduction = new TaxDeduction;
                    $taxdeduction->employee_id = $employee->id;
                    $taxdeduction->title = 'Second Tax Bracket';
                    $taxdeduction->salary_amount = round($remaining_salary, 2);
                    $taxdeduction->difference = round($difference, 2);
                    $taxdeduction->tax_deduction_value_type = 'percentage';
                    $taxdeduction->tax_deduction_value = '25';
                    $taxdeduction->tax_deduction_calculated = $tax_deduction_calculated;
                    $taxdeduction->workspace = getActiveWorkSpace();
                    $taxdeduction->created_by = creatorId();
                    $taxdeduction->save();
                    
                    // Set remaining salary to 0 as it has been fully taxed
                    $remaining_salary = 0;
                } else {
                    // If remaining salary exceeds the second bracket
                    $tax_deduction_rate = 0.25; // 25%
                    $tax_deduction_calculated = round((32333 - 24000) * $tax_deduction_rate, 2);
                    $difference = 32333 - 24000;
                    
                    // Create tax deduction for the second bracket
                    $taxdeduction = new TaxDeduction;
                    $taxdeduction->employee_id = $employee->id;
                    $taxdeduction->title = 'Second Tax Bracket';
                    $taxdeduction->salary_amount = round($combined + $difference, 2);
                    $taxdeduction->difference = round($difference, 2);
                    $taxdeduction->tax_deduction_value_type = 'percentage';
                    $taxdeduction->tax_deduction_value = '25';
                    $taxdeduction->tax_deduction_calculated = $tax_deduction_calculated;
                    $taxdeduction->workspace = getActiveWorkSpace();
                    $taxdeduction->created_by = creatorId();
                    $taxdeduction->save();
                    
                    // Calculate remaining salary for next brackets
                    $remaining_salary = round($remaining_salary - (32333 - 24000), 2);
                    $combined += $taxdeduction->difference;
                }
            }
            // END Second Tax Bracket
        
            // Third Tax Bracket (Above 32333) 30%
            // Check if remaining salary is within the third tax bracket (Above 32333) 30%
            if ($remaining_salary > 0) {
                // Apply tax for the third bracket
                $tax_deduction_rate = 0.30; // 30%
                $tax_deduction_calculated = round($remaining_salary * $tax_deduction_rate, 2);
                $difference = $remaining_salary;
                
                // Create tax deduction for the third bracket
                $taxdeduction = new TaxDeduction;
                $taxdeduction->employee_id = $employee->id;
                $taxdeduction->title = 'Third Tax Bracket';
                $taxdeduction->salary_amount = round($remaining_salary, 2);
                $taxdeduction->difference = round($difference, 2);
                $taxdeduction->tax_deduction_value_type = 'percentage';
                $taxdeduction->tax_deduction_value = '30';
                $taxdeduction->tax_deduction_calculated = $tax_deduction_calculated;
                $taxdeduction->workspace = getActiveWorkSpace();
                $taxdeduction->created_by = creatorId();
                $taxdeduction->save();
            }
            // END Third Tax Bracket
        
            // END Tax Deduction Section
        
            // Tax Relief Section
        
            // Personal Relief
            $taxrelief = new TaxRelief;
            $taxrelief->employee_id = $employee->id;
            $taxrelief->title = 'Personal Relief';
            $taxrelief->tax_relief_value_type = 'fixed';
            $taxrelief->tax_relief_value = round(2400, 2);
            $taxrelief->workspace = getActiveWorkSpace();
            $taxrelief->created_by = creatorId();
            $taxrelief->save();
        
            // Insurance Relief
            $taxrelief = new TaxRelief;
            $taxrelief->employee_id = $employee->id;
            $taxrelief->title = 'Insurance Relief';
            $taxrelief->tax_relief_value_type = 'fixed';
            $taxrelief->tax_relief_value = round(255, 2);
            $taxrelief->workspace = getActiveWorkSpace();
            $taxrelief->created_by = creatorId();
            $taxrelief->save();
        
            // Affordable Housing Relief
            $taxrelief = new TaxRelief;
            $taxrelief->employee_id = $employee->id;
            $taxrelief->title = 'Affordable Housing Relief';
            $taxrelief->tax_relief_value_type = 'fixed';
            $taxrelief->tax_relief_value = round(337.5, 2);
            $taxrelief->workspace = getActiveWorkSpace();
            $taxrelief->created_by = creatorId();
            $taxrelief->save();
        
            // END Tax Relief Section
        
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'An error occured.');
        }
        
        event(new UpdateEmployeeSalary($request, $employee));

        return redirect()->back()->with('success', 'Employee Salary Updated.');
    }
}
