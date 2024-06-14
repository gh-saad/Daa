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
use Modules\Hrm\Entities\SalaryModificationTemplate;

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
        
            // Get the template
            $salaryModTemplate = SalaryModificationTemplate::where('workspace',getActiveWorkSpace())->first();

            // Decode JSON fields
            $allowances = json_decode($salaryModTemplate->allowance, true);
            $commissions = json_decode($salaryModTemplate->commission, true);
            $loans = json_decode($salaryModTemplate->loan, true);
            $saturationDeductions = json_decode($salaryModTemplate->saturation_deduction, true);
            $taxDeductions = json_decode($salaryModTemplate->tax_deduction, true);
            $taxReliefs = json_decode($salaryModTemplate->tax_relief, true);
            $otherPayments = json_decode($salaryModTemplate->other_payment, true);
            $overtimes = json_decode($salaryModTemplate->overtime, true);
            
            // Allowance Section
            if(isset($allowances)){
                foreach ($allowances as $name => $allowance) {
                    // if threshold is not defined
                    if(!(isset($allowance['min_salary']) || isset($allowance['max_salary']))){
                        // create a new entry
                        $allowanceNew = new Allowance;
                        $allowanceNew->employee_id = $employee->id;
                        $allowanceNew->allowance_option = $allowance['option_id'];
                        $allowanceNew->title = $allowance['name'];
                        $allowanceNew->type = $allowance['amount_type'];
                        $allowanceNew->amount = $allowance['amount'];
                        $allowanceNew->workspace = getActiveWorkSpace();
                        $allowanceNew->created_by = creatorId();
                        $allowanceNew->save();
                    }else{
                        // threshold was defined
                        // assign variables
                        $min = 0;
                        $max = 99999999999999;
                        if (isset($allowance['min_salary'])){
                            $min = $allowance['min_salary'];
                        }
                        if (isset($allowance['max_salary'])){
                            $max = $allowance['max_salary'];
                        }
                        // check if employee salary falls in the threshold
                        if ($employee->salary >= $min && $employee->salary <= $max){
                            // create a new entry
                            $allowanceNew = new Allowance;
                            $allowanceNew->employee_id = $employee->id;
                            $allowanceNew->allowance_option = $allowance['option_id'];
                            $allowanceNew->title = $allowance['name'];
                            $allowanceNew->type = $allowance['amount_type'];
                            $allowanceNew->amount = $allowance['amount'];
                            $allowanceNew->workspace = getActiveWorkSpace();
                            $allowanceNew->created_by = creatorId();
                            $allowanceNew->save();
                        }
                    }
                }
            }
            // END Allowance Section
            
            // Commission Section
            if(isset($commissions)){
                foreach ($commissions as $name => $commission) {
                    // if threshold is not defined
                    if(!(isset($commission['min_salary']) || isset($commission['max_salary']))){
                        // create a new entry
                        $commissionNew = new Commission;
                        $commissionNew->employee_id = $employee->id;
                        $commissionNew->title = $commission['name'];
                        $commissionNew->type = $commission['amount_type'];
                        $commissionNew->amount = $commission['amount'];
                        $commissionNew->workspace = getActiveWorkSpace();
                        $commissionNew->created_by = creatorId();
                        $commissionNew->save();
                    }else{
                        // threshold was defined
                        // assign variables
                        $min = 0;
                        $max = 99999999999999;
                        if (isset($commission['min_salary'])){
                            $min = $commission['min_salary'];
                        }
                        if (isset($commission['max_salary'])){
                            $max = $commission['max_salary'];
                        }
                        // check if employee salary falls in the threshold
                        if ($employee->salary >= $min && $employee->salary <= $max){
                            // create a new entry
                            $commissionNew = new Commission;
                            $commissionNew->employee_id = $employee->id;
                            $commissionNew->title = $commission['name'];
                            $commissionNew->type = $commission['amount_type'];
                            $commissionNew->amount = $commission['amount'];
                            $commissionNew->workspace = getActiveWorkSpace();
                            $commissionNew->created_by = creatorId();
                            $commissionNew->save();
                        }
                    }
                }
            }
            // END Commission Section
            
            // Other Payment Section
            if(isset($loans)){
                foreach ($otherPayments as $name => $otherPayment) {
                    // if threshold is not defined
                    if(!(isset($otherPayment['min_salary']) || isset($otherPayment['max_salary']))){
                        // create a new entry
                        $otherPaymentNew = new OtherPayment;
                        $otherPaymentNew->employee_id = $employee->id;
                        $otherPaymentNew->title = $otherPayment['name'];
                        $otherPaymentNew->type = $otherPayment['amount_type'];
                        $otherPaymentNew->amount = $otherPayment['amount'];
                        $otherPaymentNew->workspace = getActiveWorkSpace();
                        $otherPaymentNew->created_by = creatorId();
                        $otherPaymentNew->save();
                    }else{
                        // threshold was defined
                        // assign variables
                        $min = 0;
                        $max = 99999999999999;
                        if (isset($otherPayment['min_salary'])){
                            $min = $otherPayment['min_salary'];
                        }
                        if (isset($otherPayment['max_salary'])){
                            $max = $otherPayment['max_salary'];
                        }
                        // check if employee salary falls in the threshold
                        if ($employee->salary >= $min && $employee->salary <= $max){
                            // create a new entry
                            $otherPaymentNew = new OtherPayment;
                            $otherPaymentNew->employee_id = $employee->id;
                            $otherPaymentNew->title = $otherPayment['name'];
                            $otherPaymentNew->type = $otherPayment['amount_type'];
                            $otherPaymentNew->amount = $otherPayment['amount'];
                            $otherPaymentNew->workspace = getActiveWorkSpace();
                            $otherPaymentNew->created_by = creatorId();
                            $otherPaymentNew->save();
                        }
                    }
                }
            }
            // END Other Payment Section
            
            // Overtime Section
            if(isset($overtimes)){
                foreach ($overtimes as $name => $overtime) {
                    // if threshold is not defined
                    if(!(isset($overtime['min_salary']) || isset($overtime['max_salary']))){
                        // create a new entry
                        $overtimeNew = new Overtime;
                        $overtimeNew->employee_id = $employee->id;
                        $overtimeNew->title = $overtime['name'];
                        $overtimeNew->number_of_days = $overtime['no_of_days'];
                        $overtimeNew->hours = $overtime['hours'];
                        $overtimeNew->rate = $overtime['rate'];
                        $overtimeNew->workspace = getActiveWorkSpace();
                        $overtimeNew->created_by = creatorId();
                        $overtimeNew->save();
                    }else{
                        // threshold was defined
                        // assign variables
                        $min = 0;
                        $max = 99999999999999;
                        if (isset($overtime['min_salary'])){
                            $min = $overtime['min_salary'];
                        }
                        if (isset($overtime['max_salary'])){
                            $max = $overtime['max_salary'];
                        }
                        // check if employee salary falls in the threshold
                        if ($employee->salary >= $min && $employee->salary <= $max){
                            // create a new entry
                            $overtimeNew = new Overtime;
                            $overtimeNew->employee_id = $employee->id;
                            $overtimeNew->title = $overtime['name'];
                            $overtimeNew->number_of_days = $overtime['no_of_days'];
                            $overtimeNew->hours = $overtime['hours'];
                            $overtimeNew->rate = $overtime['rate'];
                            $overtimeNew->workspace = getActiveWorkSpace();
                            $overtimeNew->created_by = creatorId();
                            $overtimeNew->save();
                        }
                    }
                }
            }
            // END Overtime Section
            
            // Saturation Deduction Section
            if(isset($saturationDeductions)){
                foreach ($saturationDeductions as $name => $deduction) {
                    // if threshold is not defined
                    if(!(isset($deduction['min_salary']) || isset($deduction['max_salary']))){
                        // create a new entry
                        $saturationDeduction = new SaturationDeduction;
                        $saturationDeduction->employee_id = $employee->id;
                        $saturationDeduction->deduction_option = $deduction['option_id'];
                        $saturationDeduction->title = $deduction['name'];
                        $saturationDeduction->type = $deduction['amount_type'];
                        $saturationDeduction->amount = $deduction['amount'];
                        $saturationDeduction->workspace = getActiveWorkSpace();
                        $saturationDeduction->created_by = creatorId();
                        $saturationDeduction->save();
                    }else{
                        // threshold was defined
                        // assign variables
                        $min = 0;
                        $max = 99999999999999;
                        if (isset($deduction['min_salary'])){
                            $min = $deduction['min_salary'];
                        }
                        if (isset($deduction['max_salary'])){
                            $max = $deduction['max_salary'];
                        }
                        // check if employee salary falls in the threshold
                        if ($employee->get_gross_income() >= $min && $employee->get_gross_income() <= $max){
                            // create a new entry
                            $saturationDeduction = new SaturationDeduction;
                            $saturationDeduction->employee_id = $employee->id;
                            $saturationDeduction->deduction_option = $deduction['option_id'];
                            $saturationDeduction->title = $deduction['name'];
                            $saturationDeduction->type = $deduction['amount_type'];
                            $saturationDeduction->amount = $deduction['amount'];
                            $saturationDeduction->workspace = getActiveWorkSpace();
                            $saturationDeduction->created_by = creatorId();
                            $saturationDeduction->save();
                        }
                    }
                }
            }
            // END Saturation Deduction Section

            // Loan Section
            if(isset($loans)){
                foreach ($loans as $name => $loan) {
                    // if threshold is not defined
                    if(!(isset($loan['min_salary']) || isset($loan['max_salary']))){
                        // create a new entry
                        $loanNew = new Loan;
                        $loanNew->employee_id = $employee->id;
                        $loanNew->loan_option = $loan['option_id'];
                        $loanNew->title = $loan['name'];
                        $loanNew->type = $loan['amount_type'];
                        $loanNew->amount = $loan['amount'];
                        $loanNew->start_date = $loan['start_date'];
                        $loanNew->end_date = $loan['end_date'];
                        $loanNew->reason = $loan['reason'];
                        $loanNew->workspace = getActiveWorkSpace();
                        $loanNew->created_by = creatorId();
                        $loanNew->save();
                    }else{
                        // threshold was defined
                        // assign variables
                        $min = 0;
                        $max = 99999999999999;
                        if (isset($loan['min_salary'])){
                            $min = $loan['min_salary'];
                        }
                        if (isset($loan['max_salary'])){
                            $max = $loan['max_salary'];
                        }
                        // check if employee salary falls in the threshold
                        if ($employee->get_gross_income() >= $min && $employee->get_gross_income() <= $max){
                            // create a new entry
                            $loanNew = new Loan;
                            $loanNew->employee_id = $employee->id;
                            $loanNew->loan_option = $loan['option_id'];
                            $loanNew->title = $loan['name'];
                            $loanNew->type = $loan['amount_type'];
                            $loanNew->amount = $loan['amount'];
                            $loanNew->start_date = $loan['start_date'];
                            $loanNew->end_date = $loan['end_date'];
                            $loanNew->reason = $loan['reason'];
                            $loanNew->workspace = getActiveWorkSpace();
                            $loanNew->created_by = creatorId();
                            $loanNew->save();
                        }
                    }
                }
            }
            // END Loan Section

            // Tax Deduction Section
            if(isset($taxDeductions)){
                // sort the tax brackets
                usort($taxDeductions, function($a, $b) {
                    return $a['min_salary'] - $b['min_salary'];
                });            

                // taxable salary
                $taxable_salary = $employee->get_net_pay_before_taxes();
                $remaining_salary = $taxable_salary;
                $combined = 0;
                
                // apply tax deductions
                foreach ($taxDeductions as $taxDeduction) {
                    // if no salary remains break the loop
                    if ($remaining_salary <= 0) {
                        break;
                    }
                    // if threshold is not defined
                    if (!(isset($deduction['min_salary']) || isset($deduction['max_salary']))){
                        // if a fixed amount is being applied
                        if ($taxDeduction['amount_type'] == 'fixed'){
                            // create a new entry
                            $taxDeductionNew = new TaxDeduction;
                            $taxDeductionNew->employee_id = $employee->id;
                            $taxDeductionNew->title = $taxDeduction['name'];
                            $taxDeductionNew->salary_amount = $taxable_salary;
                            $taxDeductionNew->difference = 0;
                            $taxDeductionNew->tax_deduction_value_type = $taxDeduction['amount_type'];
                            $taxDeductionNew->tax_deduction_value = $taxDeduction['amount'];
                            $taxDeductionNew->tax_deduction_calculated = $taxDeduction['amount'];
                            $taxDeductionNew->workspace = getActiveWorkSpace();
                            $taxDeductionNew->created_by = creatorId();
                            $taxDeductionNew->save();

                            // keep remaining same
                            $remaining_salary = $taxable_salary;
                        }else{
                            // calculate tax
                            $tax_deduction_rate = $taxDeduction['amount'] / 100;
                            $tax_deduction_calculated = round($taxable_salary * $tax_deduction_rate, 2);

                            // create a new entry
                            $taxDeductionNew = new TaxDeduction;
                            $taxDeductionNew->employee_id = $employee->id;
                            $taxDeductionNew->title = $taxDeduction['name'];
                            $taxDeductionNew->salary_amount = $taxable_salary;
                            $taxDeductionNew->difference = 0;
                            $taxDeductionNew->tax_deduction_value_type = $taxDeduction['amount_type'];
                            $taxDeductionNew->tax_deduction_value = $taxDeduction['amount'];
                            $taxDeductionNew->tax_deduction_calculated = $tax_deduction_calculated;
                            $taxDeductionNew->workspace = getActiveWorkSpace();
                            $taxDeductionNew->created_by = creatorId();
                            $taxDeductionNew->save();

                            // keep remaining same
                            $remaining_salary = $taxable_salary;
                        }
                    }else{
                        // threshold was defined
                        // assign variables
                        $min = isset($taxDeduction['min_salary']) ? $taxDeduction['min_salary'] : 0;
                        $max = isset($taxDeduction['max_salary']) ? $taxDeduction['max_salary'] : PHP_INT_MAX;

                        // if a fixed amount is being applied
                        if ($taxDeduction['amount_type'] == 'fixed'){
                            $taxable_amount = min($remaining_salary, $max - $min);
                            $max_available_salary = min($remaining_salary, $max);
                            // create a new entry
                            $taxDeductionNew = new TaxDeduction;
                            $taxDeductionNew->employee_id = $employee->id;
                            $taxDeductionNew->title = $taxDeduction['name'];
                            $taxDeductionNew->salary_amount = round($max_available_salary, 2);
                            $taxDeductionNew->difference = round($taxable_amount, 2);
                            $taxDeductionNew->tax_deduction_value_type = $taxDeduction['amount_type'];
                            $taxDeductionNew->tax_deduction_value = $taxDeduction['amount'];
                            $taxDeductionNew->tax_deduction_calculated = $taxDeduction['amount'];
                            $taxDeductionNew->workspace = getActiveWorkSpace();
                            $taxDeductionNew->created_by = creatorId();
                            $taxDeductionNew->save();

                            // keep remaining same
                            $remaining_salary -= $taxable_amount;
                            $combined += $taxable_amount;
                        }else{
                            // calculate tax
                            $taxable_amount = min($remaining_salary, $max - $min);
                            $max_available_salary = min($remaining_salary, $max);
                            $tax_deduction_rate = $taxDeduction['amount'] / 100;
                            $tax_deduction_calculated = round($taxable_amount * $tax_deduction_rate, 2);

                            // create a new entry
                            $taxDeductionNew = new TaxDeduction;
                            $taxDeductionNew->employee_id = $employee->id;
                            $taxDeductionNew->title = $taxDeduction['name'];
                            $taxDeductionNew->salary_amount = round($max_available_salary, 2);
                            $taxDeductionNew->difference = round($taxable_amount, 2);
                            $taxDeductionNew->tax_deduction_value_type = $taxDeduction['amount_type'];
                            $taxDeductionNew->tax_deduction_value = $taxDeduction['amount'];
                            $taxDeductionNew->tax_deduction_calculated = $tax_deduction_calculated;
                            $taxDeductionNew->workspace = getActiveWorkSpace();
                            $taxDeductionNew->created_by = creatorId();
                            $taxDeductionNew->save();

                            // keep remaining same
                            $remaining_salary -= $taxable_amount;
                            $combined += $taxable_amount;
                        }
                    }
                }
            }
            // END Tax Deduction Section
        
            // Tax Relief Section
            if(isset($taxReliefs)){
                foreach ($taxReliefs as $name => $taxRelief) {
                    // if threshold is not defined
                    if(!(isset($taxRelief['min_salary']) || isset($taxRelief['max_salary']))){
                        // create a new entry
                        $taxReliefNew = new TaxRelief;
                        $taxReliefNew->employee_id = $employee->id;
                        $taxReliefNew->title = $taxRelief['name'];
                        $taxReliefNew->tax_relief_value_type = $taxRelief['amount_type'];
                        $taxReliefNew->tax_relief_value = $taxRelief['amount'];
                        $taxReliefNew->workspace = getActiveWorkSpace();
                        $taxReliefNew->created_by = creatorId();
                        $taxReliefNew->save();
                    }else{
                        // threshold was defined
                        // assign variables
                        $min = 0;
                        $max = 99999999999999;
                        if (isset($taxRelief['min_salary'])){
                            $min = $taxRelief['min_salary'];
                        }
                        if (isset($taxRelief['max_salary'])){
                            $max = $taxRelief['max_salary'];
                        }
                        // check if employee salary falls in the threshold
                        if ($employee->salary >= $min && $employee->salary <= $max){
                            // create a new entry
                            $taxReliefNew = new TaxRelief;
                            $taxReliefNew->employee_id = $employee->id;
                            $taxReliefNew->title = $taxRelief['name'];
                            $taxReliefNew->tax_relief_value_type = $taxRelief['amount_type'];
                            $taxReliefNew->tax_relief_value = $taxRelief['amount'];
                            $taxReliefNew->workspace = getActiveWorkSpace();
                            $taxReliefNew->created_by = creatorId();
                            $taxReliefNew->save();
                        }
                    }
                }
            }
            // END Tax Relief Section
        
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'An error occured.');
        }
        
        event(new UpdateEmployeeSalary($request, $employee));

        return redirect()->back()->with('success', 'Employee Salary Updated.');
    }
}
