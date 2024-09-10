<?php

namespace Modules\Hrm\Entities;

use App\Models\User;
use Modules\Hrm\Entities\TaxDeduction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\WorkSpace;
use Rawilk\Settings\Support\Context;
use Symfony\Component\Mailer\Transport\Dsn;
use Illuminate\Support\Facades\Log;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'dob',
        'gender',
        'phone',
        'address',
        'email',
        'password',
        'employee_id',
        'branch_id',
        'department_id',
        'designation_id',
        'company_doj',
        'documents',
        'account_holder_name',
        'account_number',
        'bank_name',
        'bank_identifier_code',
        'branch_location',
        'tax_payer_id',
        'salary_type',
        'salary',
        'is_active',
        'workspace',
        'created_by',
    ];

    protected static function newFactory()
    {
        return \Modules\Hrm\Database\factories\EmployeeFactory::new();
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }
    public static function Branchs($id)
    {
        return Branch::where('id', $id)->first();
    }
    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }
    public static function Departments($id)
    {
        return Department::where('id', $id)->first();
    }
    public function designation()
    {
        return $this->hasOne(Designation::class, 'id', 'designation_id');
    }
    public static function Designations($id)
    {
        return Designation::where('id', $id)->first();
    }

    public static function employeeIdFormat($number)
    {
        $employee_prefix = !empty(company_setting('employee_prefix')) ? company_setting('employee_prefix') : '#EMP000';
        return $employee_prefix . sprintf("%05d", $number);
    }
    public static function present_status($employee_id, $data)
    {
        return Attendance::where('employee_id', $employee_id)->where('date', $data)->first();
    }
    public function documents()
    {
        return $this->hasOne(EmployeeDocument::class, 'employee_id', 'employee_id');
    }
    public static function getEmployee($employee)
    {
        $employee = User::where('id', '=', $employee)->first();
        $employee = !empty($employee) ? $employee : null;
        return $employee;
    }
    public static function GetEmployeeByEmp($employee)
    {
        $employee = Employee::where('id', '=', $employee)->first();
        $employee = !empty($employee) ? $employee : null;
        return $employee;
    }
    public function salary_type()
    {
        return $this->hasOne(PayslipType::class, 'id', 'salary_type')->pluck('name')->first();
    }

    public function get_gross_income()
    {
        //allowance
        $allowances      = Allowance::where('employee_id', '=', $this->id)->get();
        $total_allowance = 0;
        foreach ($allowances as $allowance) {
            if ($allowance->type == 'percentage') {
                $employee          = Employee::find($allowance->employee_id);
                $total_allowance  = round($allowance->amount, 2) * round($employee->salary, 2) / 100  + round($total_allowance, 2);
            } else {
                $total_allowance = round($allowance->amount, 2) + round($total_allowance, 2);
            }
        }

        //commission
        $commissions      = Commission::where('employee_id', '=', $this->id)->get();

        $total_commission = 0;
        foreach ($commissions as $commission) {
            if ($commission->type == 'percentage') {
                $employee          = Employee::find($commission->employee_id);
                $total_commission  = round($commission->amount, 2) * round($employee->salary, 2) / 100 + round($total_commission, 2);
            } else {
                $total_commission = round($commission->amount, 2) + round($total_commission, 2);
            }
        }

        //OtherPayment
        $other_payments      = OtherPayment::where('employee_id', '=', $this->id)->get();
        $total_other_payment = 0;
        foreach ($other_payments as $other_payment) {
            if ($other_payment->type == 'percentage') {
                $employee          = Employee::find($other_payment->employee_id);
                $total_other_payment  = round($other_payment->amount, 2) * round($employee->salary, 2) / 100  + round($total_other_payment, 2);
            } else {
                $total_other_payment = round($other_payment->amount, 2) + round($total_other_payment, 2);
            }
        }

        //Overtime
        $over_times      = Overtime::where('employee_id', '=', $this->id)->get();
        $total_over_time = 0;
        foreach ($over_times as $over_time) {
            $total_work      = $over_time->number_of_days * $over_time->hours;
            $amount          = $total_work * $over_time->rate;
            $total_over_time = round($amount, 2) + round($total_over_time, 2);
        }


        //Net Salary Calculate
        $advance_salary = round($total_allowance, 2) + round($total_commission, 2) + round($total_other_payment, 2) + round($total_over_time, 2);

        $employee       = Employee::where('id', '=', $this->id)->first();

        $gross_income     = (!empty($employee->salary) ? $employee->salary : 0) + round($advance_salary, 2);

        return $gross_income;
    }

    public function get_net_pay_before_taxes()
    {
        
        //Loan
        $loans      = Loan::where('employee_id', '=', $this->id)->get();
        $total_loan = 0;
        foreach ($loans as $loan) {
            if ($loan->type == 'percentage') {
                $employee = Employee::find($loan->employee_id);
                $total_loan  = round($loan->amount, 2) * round($employee->salary, 2) / 100   + round($total_loan, 2);
            } else {
                $total_loan = round($loan->amount, 2) + round($total_loan, 2);
            }
        }

        //Saturation Deduction
        $saturation_deductions      = SaturationDeduction::where('employee_id', '=', $this->id)->get();
        $total_saturation_deduction = 0;
        foreach ($saturation_deductions as $saturation_deduction) {
            if ($saturation_deduction->type == 'percentage') {
                $employee          = Employee::find($saturation_deduction->employee_id);
                $total_saturation_deduction  = round($saturation_deduction->amount, 2) * round($employee->salary, 2) / 100 + round($total_saturation_deduction, 2);
            } else {
                $total_saturation_deduction = round($saturation_deduction->amount, 2) + round($total_saturation_deduction, 2);
            }
        }

        $employee       = Employee::where('id', '=', $this->id)->first();

        $calc_salary_one = round($employee->get_gross_income(), 2) - round($total_loan, 2);
        $calc_salary_two = round($calc_salary_one, 2) - round($total_saturation_deduction, 2);

        $net_pay_before_taxes = round($calc_salary_two, 2);

        return $net_pay_before_taxes;
    }

    public function get_net_tax_liability()
    {
        
        //Tax Deductions
        $tax_deductions      = TaxDeduction::where('employee_id', '=', $this->id)->get();
        $total_tax_deduction = 0;
        foreach ($tax_deductions as $tax_deduction) {
            $total_tax_deduction  = round($tax_deduction->tax_deduction_calculated, 2) + round($total_tax_deduction, 2);
        }

        //Tax Relief
        $tax_reliefs      = TaxRelief::where('employee_id', '=', $this->id)->get();
        $total_tax_relief = 0;
        foreach ($tax_reliefs as $tax_relief) {
            if ($tax_relief->tax_relief_value_type == 'percentage') {
                $employee          = Employee::find($tax_relief->employee_id);
                $total_tax_relief  = round($tax_relief->tax_relief_value, 2) * round($total_tax_deduction, 2) / 100 + round($total_tax_relief, 2);
            } else {
                $total_tax_relief = round($tax_relief->tax_relief_value, 2) + round($total_tax_relief, 2);
            }
        }

        $employee       = Employee::where('id', '=', $this->id)->first();

        $calc_net_tax_liability = round($total_tax_deduction, 2) - round($total_tax_relief, 2);

        if($calc_net_tax_liability < 0){
            $calc_net_tax_liability = 0;
        }

        $net_tax_liability = round($calc_net_tax_liability, 2);
        
        return $net_tax_liability;
    }

    public function get_net_salary()
    {

        $employee       = Employee::where('id', '=', $this->id)->first();
        $payslip = payroll_calculator_kenya($employee->get_gross_income());

        // $net_salary     = round($employee->get_net_pay_before_taxes(), 2) - round($employee->get_net_tax_liability(), 2);
        
        return $payslip['netSalary'];
    }

    public static function allowance($id)
    {
        //allowance
        $allowances      = Allowance::where('employee_id', '=', $id)->get();
        $total_allowance = 0;
        foreach ($allowances as $allowance) {
            $total_allowance = $allowance->amount + $total_allowance;
        }

        $allowance_json = json_encode($allowances);

        return $allowance_json;
    }

    public static function commission($id)
    {
        //commission
        $commissions      = Commission::where('employee_id', '=', $id)->get();
        $total_commission = 0;

        foreach ($commissions as $commission) {
            $total_commission = $commission->amount + $total_commission;
        }
        $commission_json = json_encode($commissions);

        return $commission_json;
    }

    public static function loan($id)
    {
        //Loan
        $loans      = Loan::where('employee_id', '=', $id)->get();
        $total_loan = 0;
        foreach ($loans as $loan) {
            $total_loan = $loan->amount + $total_loan;
        }
        $loan_json = json_encode($loans);

        return $loan_json;
    }

    public static function saturation_deduction($id)
    {
        //Saturation Deduction
        $saturation_deductions      = SaturationDeduction::where('employee_id', '=', $id)->get();
        $total_saturation_deduction = 0;
        foreach ($saturation_deductions as $saturation_deduction) {
            $total_saturation_deduction = $saturation_deduction->amount + $total_saturation_deduction;
        }
        $saturation_deduction_json = json_encode($saturation_deductions);

        return $saturation_deduction_json;
    }

    public static function other_payment($id)
    {
        //OtherPayment
        $other_payments      = OtherPayment::where('employee_id', '=', $id)->get();
        $total_other_payment = 0;
        foreach ($other_payments as $other_payment) {
            $total_other_payment = $other_payment->amount + $total_other_payment;
        }
        $other_payment_json = json_encode($other_payments);

        return $other_payment_json;
    }

    public static function overtime($id)
    {
        //Overtime
        $over_times      = Overtime::where('employee_id', '=', $id)->get();
        $total_over_time = 0;
        foreach ($over_times as $over_time) {
            $total_work      = $over_time->number_of_days * $over_time->hours;
            $amount          = $total_work * $over_time->rate;
            $total_over_time = $amount + $total_over_time;
        }
        $over_time_json = json_encode($over_times);

        return $over_time_json;
    }
    public static function employeePayslipDetail($employeeId, $month)
    {

        $payslip_data = PaySlip::where('employee_id', $employeeId)->where('salary_month', $month)->first();
        $totalAllowance = 0;
        $totalCommission = 0;
        $totalotherpayment = 0;
        $ot = 0;
        $totalloan = 0;
        $totaldeduction = 0;
        // allowance

       if(!empty($payslip_data))
       {
        $allowances = json_decode($payslip_data->allowance);
        foreach ($allowances as $allowance) {
            if ($allowance->type == 'percentage') {
                $empall  = $allowance->amount * $payslip_data->basic_salary / 100;
            } else {
                $empall = $allowance->amount;
            }
            $totalAllowance += $empall;
        }
        // commission

        $commissions = json_decode($payslip_data->commission);
        foreach ($commissions as $commission) {

            if ($commission->type == 'percentage') {
                $empcom  = $commission->amount * $payslip_data->basic_salary / 100;
            } else {
                $empcom = $commission->amount;
            }
            $totalCommission += $empcom;
        }

        // otherpayment


        $otherpayments = json_decode($payslip_data->other_payment);
        foreach ($otherpayments as $otherpayment) {
            if ($otherpayment->type == 'percentage') {
                $empotherpay  = $otherpayment->amount * $payslip_data->basic_salary / 100;
            } else {
                $empotherpay = $otherpayment->amount;
            }
            $totalotherpayment += $empotherpay;
        }
        //overtime

        $overtimes = json_decode($payslip_data->overtime);
        foreach ($overtimes as $overtime) {
            $OverTime = $overtime->number_of_days * $overtime->hours * $overtime->rate;
            $ot += $OverTime;
        }

        // loan


        $loans = json_decode($payslip_data->loan);

        foreach ($loans as $loan)
        {
            if ($loan->type == 'percentage') {
                $emploan  = $loan->amount * $payslip_data->basic_salary / 100;
            } else {
                $emploan = $loan->amount;
            }
            $totalloan += $emploan;
        }

        // saturation_deduction

        $deductions = json_decode($payslip_data->saturation_deduction);
        foreach ($deductions as $deduction)
        {
            if ($deduction->type == 'percentage')
            {
                $empdeduction  = $deduction->amount * $payslip_data->basic_salary / 100;
            }
            else
            {
                $empdeduction = $deduction->amount;
            }
            $totaldeduction += $empdeduction;
        }

        // net tax deduction
        $net_tax_deduction = $payslip_data->net_tax_liability;

       }

        $payslip['payslip']        = $payslip_data;
        $payslip['totalEarning']   = $totalAllowance + $totalCommission + $totalotherpayment + $ot;
        $payslip['totalDeduction'] = $totalloan + $totaldeduction;
        $payslip['NetTaxDeduction']= $net_tax_deduction;

        $payslip['allowance'] = $totalAllowance;
        $payslip['commission'] = $totalCommission;
        $payslip['other_payment'] = $totalotherpayment;
        $payslip['overtime'] = $ot;
        $payslip['loan'] = $totalloan;
        $payslip['saturation_deduction'] = $totaldeduction;
        return $payslip;
    }
    public static function countEmployees($id = null)
    {
        if ($id == null) {
            $id = \Auth::user()->id;
        }
        return Employee::where('created_by', '=', $id)->count();
    }
    public static function defaultJoiningLetterRegister($user_id)
    {

        foreach ($defaultTemplate as $lang => $content) {
            JoiningLetter::create(
                [
                    'lang' => $lang,
                    'content' => $content,
                    'created_by' => $user_id,

                ]
            );
        }
    }
    public static function defaultdata($company_id = null, $workspace_id = null)
    {
        $company_setting = [
            "employee_prefix" => "#EMP",
            "company_start_time" => "09:00",
            "company_end_time" => "18:00",
        ];

        if ($company_id == Null) {
            $companys = User::where('type', 'company')->get();
            foreach ($companys as $company) {
                $WorkSpaces = WorkSpace::where('created_by', $company->id)->get();
                foreach ($WorkSpaces as $WorkSpace) {
                    JoiningLetter::defaultJoiningLetter($company->id, $WorkSpace->id);
                    ExperienceCertificate::defaultExpCertificat($company->id, $WorkSpace->id);
                    NOC::defaultNocCertificate($company->id, $WorkSpace->id);

                    $userContext = new Context(['user_id' => $company->id, 'workspace_id' => !empty($WorkSpace->id) ? $WorkSpace->id : 0]);
                    foreach ($company_setting as $key =>  $p) {
                        \Settings::context($userContext)->set($key, $p);
                    }
                }
            }
        } elseif ($workspace_id == Null) {
            $company = User::where('type', 'company')->where('id', $company_id)->first();
            $WorkSpaces = WorkSpace::where('created_by', $company->id)->get();
            foreach ($WorkSpaces as $WorkSpace) {
                JoiningLetter::defaultJoiningLetter($company->id, $WorkSpace->id);
                ExperienceCertificate::defaultExpCertificat($company->id, $WorkSpace->id);
                NOC::defaultNocCertificate($company->id, $WorkSpace->id);
                $userContext = new Context(['user_id' => $company->id, 'workspace_id' => !empty($WorkSpace->id) ? $WorkSpace->id : 0]);
                foreach ($company_setting as $key =>  $p) {
                    \Settings::context($userContext)->set($key, $p);
                }
            }
        } else {
            $company = User::where('type', 'company')->where('id', $company_id)->first();
            $WorkSpace = WorkSpace::where('created_by', $company->id)->where('id', $workspace_id)->first();
            $userContext = new Context(['user_id' => $company->id, 'workspace_id' => !empty($WorkSpace->id) ? $WorkSpace->id : 0]);
            foreach ($company_setting as $key =>  $p) {
                JoiningLetter::defaultJoiningLetter($company->id, $WorkSpace->id);
                ExperienceCertificate::defaultExpCertificat($company->id, $WorkSpace->id);
                NOC::defaultNocCertificate($company->id, $WorkSpace->id);
                \Settings::context($userContext)->set($key, $p);
            }
        }
    }
    public static function GivePermissionToRoles($role_id = null, $rolename = null)
    {
        $staff_permission = [
            'hrm dashboard manage',
            'document manage',
            'attendance manage',
            'employee profile manage',
            'employee profile show',
            'hrm manage',
            'companypolicy manage',
            'leave manage',
            'leave create',
            'leave edit',
            'award manage',
            'transfer manage',
            'resignation manage',
            'travel manage',
            'promotion manage',
            'complaint manage',
            'complaint create',
            'complaint edit',
            'complaint delete',
            'warning manage',
            'termination manage',
            'announcement manage',
            'holiday manage',
            'attendance report manage',
            'leave report manage',
            'setsalary show',
            'setsalary manage',
            'setsalary pay slip manage',
            'allowance manage',
            'commission manage',
            'loan manage',
            'saturation deduction manage',
            'other payment manage',
            'overtime manage',
            'sidebar hr admin  manage',
            'sidebar payroll manage',
            'employee manage',
            'employee show',



        ];

        if ($role_id == Null) {

            // staff
            $roles_v = Role::where('name', 'staff')->get();

            foreach ($roles_v as $role) {
                foreach ($staff_permission as $permission_v) {
                    $permission = Permission::where('name', $permission_v)->first();
                    $role->givePermissionTo($permission);
                }
            }
        } else {
            if ($rolename == 'staff') {
                $roles_v = Role::where('name', 'staff')->where('id', $role_id)->first();
                foreach ($staff_permission as $permission_v) {
                    $permission = Permission::where('name', $permission_v)->first();
                    $roles_v->givePermissionTo($permission);
                }
            }
        }
    }
    public static function PayrollCalculation($EmpID = null,$months = null,$type = null)
    {
        if(!empty($EmpID) && !empty($type) && count($months) > 0)
        {
            $data = [];
            foreach ($months as $key => $month)
            {
                $payslip_data = Employee::employeePayslipDetail($EmpID,$month);
                $data[] = $payslip_data[$type];
            }
            $data[] = array_sum($data);
            return $data;
        }
        else
        {
            return [];
        }
    }
}
