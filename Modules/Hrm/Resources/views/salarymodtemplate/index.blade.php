@extends('layouts.main')
@section('page-title')
    {{ __('Manage Default Salary Modifications') }}
@endsection
@section('page-breadcrumb')
{{ __('Default Salary Modifications') }}
@endsection
@section('page-action')
@endsection
@section('content')
<div class="row">
    <div class="col-sm-3">
        @include('hrm::layouts.hrm_setup')
    </div>
    <div class="col-sm-9">
        <!-- Allowance -->
        @can('allowance manage')
        <div class="card set-card mb-2">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        <h5>{{ __('Allowance') }}</h5>
                    </div>
                    @can('allowance create')
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#create_allowance_modal">
                            <i class="ti ti-plus"></i>
                        </button>
                    </div>
                    @endcan
                </div>
            </div>
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Allowance Option') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Min Salary') }}</th>
                                <th>{{ __('Max Salary') }}</th>
                                @if (Gate::check('allowance edit') || Gate::check('allowance delete'))
                                    <th>{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @isset($allowances)
                                @foreach($allowances as $allowance)
                                    @php
                                        $option = $allowanceoptions->firstWhere('id', $allowance['option_id']);
                                    @endphp
                                    <tr>
                                        <td>{{ $option ? $option->name : __('N/A') }}</td>
                                        <td>{{ $allowance['name'] }}</td>
                                        <td>{{ ucfirst($allowance['amount_type']) }}</td>
                                        <td>{{ $allowance['amount_type'] === 'percentage' ? $allowance['amount'] . '%' : $allowance['amount'] }}</td>
                                        <td>{{ $allowance['min_salary'] ?? 'N/A' }}</td>
                                        <td>{{ $allowance['max_salary'] ?? 'N/A' }}</td>
                                        @if (Gate::check('allowance edit') || Gate::check('allowance delete'))
                                            <td>
                                                @can('allowance edit')
                                                    <a type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#edit_allowance_modal" data-entry="allowance" data-name="{{ $allowance['name'] }}">{{ __('Edit') }}</a>
                                                @endcan
                                                @can('allowance delete')
                                                    <a class="btn btn-sm btn-danger" href="{{ route('salarymodtemplate.entry.delete', ['entry' => 'allowance', 'name' => $allowance['name']]) }}">{{ __('Delete') }}</a>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan
        <!-- END Allowance -->
        <!-- Commission -->
        @can('commission manage')
        <div class="card set-card mb-2">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        <h5>{{ __('Commission') }}</h5>
                    </div>
                    @can('commission create')
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#create_commission_modal">
                            <i class="ti ti-plus"></i>
                        </button>
                    </div>
                    @endcan
                </div>
            </div>
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Min Salary') }}</th>
                                <th>{{ __('Max Salary') }}</th>
                                @if (Gate::check('commission edit') || Gate::check('commission delete'))
                                    <th>{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @isset($commissions)
                                @foreach($commissions as $commission)
                                    <tr>
                                        <td>{{ $commission['name'] }}</td>
                                        <td>{{ ucfirst($commission['amount_type']) }}</td>
                                        <td>{{ $commission['amount_type'] === 'percentage' ? $commission['amount'] . '%' : $commission['amount'] }}</td>
                                        <td>{{ $commission['min_salary'] ?? 'N/A' }}</td>
                                        <td>{{ $commission['max_salary'] ?? 'N/A' }}</td>
                                        @if (Gate::check('commission edit') || Gate::check('commission delete'))
                                            <td>
                                                @can('commission edit')
                                                    <a type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#edit_commission_modal" data-entry="commission" data-name="{{ $commission['name'] }}">{{ __('Edit') }}</a>
                                                @endcan
                                                @can('commission delete')
                                                    <a class="btn btn-sm btn-danger" href="{{ route('salarymodtemplate.entry.delete', ['entry' => 'commission', 'name' => $commission['name']]) }}">{{ __('Delete') }}</a>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan
        <!-- END Commission -->
        <!-- Loan -->
        @can('loan manage')
        <div class="card set-card mb-2">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        <h5>{{ __('Loan') }}</h5>
                    </div>
                    @can('loan create')
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#create_loan_modal">
                            <i class="ti ti-plus"></i>
                        </button>
                    </div>
                    @endcan
                </div>
            </div>
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Loan Option') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Start Date') }}</th>
                                <th>{{ __('End Date') }}</th>
                                <th>{{ __('Min Salary') }}</th>
                                <th>{{ __('Max Salary') }}</th>
                                @if (Gate::check('loan edit') || Gate::check('loan delete'))
                                    <th>{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @isset($loans)
                                @foreach($loans as $loan)
                                    @php
                                        $option = $loanoptions->firstWhere('id', $loan['option_id']);
                                    @endphp
                                    <tr>
                                        <td>{{ $option ? $option->name : __('N/A') }}</td>
                                        <td>{{ $loan['name'] }}</td>
                                        <td>{{ ucfirst($loan['amount_type']) }}</td>
                                        <td>{{ $loan['amount_type'] === 'percentage' ? $loan['amount'] . '%' : $loan['amount'] }}</td>
                                        <td>{{ $loan['start_date'] }}</td>
                                        <td>{{ $loan['end_date'] }}</td>
                                        <td>{{ $loan['min_salary'] ?? 'N/A' }}</td>
                                        <td>{{ $loan['max_salary'] ?? 'N/A' }}</td>
                                        @if (Gate::check('loan edit') || Gate::check('loan delete'))
                                            <td>
                                                @can('loan edit')
                                                    <a type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#edit_loan_modal" data-entry="loan" data-name="{{ $loan['name'] }}">{{ __('Edit') }}</a>
                                                @endcan
                                                @can('loan delete')
                                                    <a class="btn btn-sm btn-danger" href="{{ route('salarymodtemplate.entry.delete', ['entry' => 'loan', 'name' => $loan['name']]) }}">{{ __('Delete') }}</a>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan
        <!-- END Loan -->
        <!-- Saturation Deduction -->
        @can('saturation deduction manage')
        <div class="card set-card mb-2">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        <h5>{{ __('Saturation Deduction') }}</h5>
                    </div>
                    @can('saturation deduction create')
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#create_saturation_deduction_modal">
                            <i class="ti ti-plus"></i>
                        </button>
                    </div>
                    @endcan
                </div>
            </div>
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Deduction Option') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Min Salary') }}</th>
                                <th>{{ __('Max Salary') }}</th>
                                @if (Gate::check('saturation deduction edit') || Gate::check('saturation deduction delete'))
                                    <th>{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @isset($saturation_deductions)
                                @foreach($saturation_deductions as $saturation_deduction)
                                    @php
                                        $option = $deductionoptions->firstWhere('id', $saturation_deduction['option_id']);
                                    @endphp
                                    <tr>
                                        <td>{{ $option ? $option->name : __('N/A') }}</td>
                                        <td>{{ $saturation_deduction['name'] }}</td>
                                        <td>{{ ucfirst($saturation_deduction['amount_type']) }}</td>
                                        <td>{{ $saturation_deduction['amount_type'] === 'percentage' ? $saturation_deduction['amount'] . '%' : $saturation_deduction['amount'] }}</td>
                                        <td>{{ $saturation_deduction['min_salary'] ?? 'N/A' }}</td>
                                        <td>{{ $saturation_deduction['max_salary'] ?? 'N/A' }}</td>
                                        @if (Gate::check('saturation deduction edit') || Gate::check('saturation deduction delete'))
                                            <td>
                                                @can('saturation deduction edit')
                                                    <a type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#edit_saturation_deduction_modal" data-entry="saturation_deduction" data-name="{{ $saturation_deduction['name'] }}">{{ __('Edit') }}</a>
                                                @endcan
                                                @can('saturation deduction delete')
                                                    <a class="btn btn-sm btn-danger" href="{{ route('salarymodtemplate.entry.delete', ['entry' => 'saturation_deduction', 'name' => $saturation_deduction['name']]) }}">{{ __('Delete') }}</a>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan
        <!-- END Saturation Deduction -->
        <!-- Tax Deduction -->
        @can('saturation deduction manage')
        <div class="card set-card mb-2">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        <h5>{{ __('Tax Deduction') }}</h5>
                    </div>
                    @can('saturation deduction create')
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#create_tax_deduction_modal">
                            <i class="ti ti-plus"></i>
                        </button>
                    </div>
                    @endcan
                </div>
            </div>
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Min Salary') }}</th>
                                <th>{{ __('Max Salary') }}</th>
                                @if (Gate::check('saturation deduction edit') || Gate::check('saturation deduction delete'))
                                    <th>{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @isset($tax_deductions)
                                @foreach($tax_deductions as $tax_deduction)
                                    <tr>
                                        <td>{{ $tax_deduction['name'] }}</td>
                                        <td>{{ ucfirst($tax_deduction['amount_type']) }}</td>
                                        <td>{{ $tax_deduction['amount_type'] === 'percentage' ? $tax_deduction['amount'] . '%' : $tax_deduction['amount'] }}</td>
                                        <td>{{ $tax_deduction['min_salary'] ?? 'N/A' }}</td>
                                        <td>{{ $tax_deduction['max_salary'] ?? 'N/A' }}</td>
                                        @if (Gate::check('saturation deduction edit') || Gate::check('saturation deduction delete'))
                                            <td>
                                                @can('saturation deduction edit')
                                                    <a type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#edit_tax_deduction_modal" data-entry="tax_deduction" data-name="{{ $tax_deduction['name'] }}">{{ __('Edit') }}</a>
                                                @endcan
                                                @can('saturation deduction delete')
                                                    <a class="btn btn-sm btn-danger" href="{{ route('salarymodtemplate.entry.delete', ['entry' => 'tax_deduction', 'name' => $tax_deduction['name']]) }}">{{ __('Delete') }}</a>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan
        <!-- END Tax Deduction -->
        <!-- Tax Relief -->
        @can('saturation deduction manage')
        <div class="card set-card mb-2">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        <h5>{{ __('Tax Relief') }}</h5>
                    </div>
                    @can('saturation deduction create')
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#create_tax_relief_modal">
                            <i class="ti ti-plus"></i>
                        </button>
                    </div>
                    @endcan
                </div>
            </div>
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Min Salary') }}</th>
                                <th>{{ __('Max Salary') }}</th>
                                @if (Gate::check('saturation deduction edit') || Gate::check('saturation deduction delete'))
                                    <th>{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @isset($tax_reliefs)
                                @foreach($tax_reliefs as $tax_relief)
                                    <tr>
                                        <td>{{ $tax_relief['name'] }}</td>
                                        <td>{{ ucfirst($tax_relief['amount_type']) }}</td>
                                        <td>{{ $tax_relief['amount_type'] === 'percentage' ? $tax_relief['amount'] . '%' : $tax_relief['amount'] }}</td>
                                        <td>{{ $tax_relief['min_salary'] ?? 'N/A' }}</td>
                                        <td>{{ $tax_relief['max_salary'] ?? 'N/A' }}</td>
                                        @if (Gate::check('saturation deduction edit') || Gate::check('saturation deduction delete'))
                                            <td>
                                                @can('saturation deduction edit')
                                                    <a type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#edit_tax_relief_modal" data-entry="tax_relief" data-name="{{ $tax_relief['name'] }}">{{ __('Edit') }}</a>
                                                @endcan
                                                @can('saturation deduction delete')
                                                    <a class="btn btn-sm btn-danger" href="{{ route('salarymodtemplate.entry.delete', ['entry' => 'tax_relief', 'name' => $tax_relief['name']]) }}">{{ __('Delete') }}</a>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan
        <!-- END Tax Relief -->
        <!-- Other Payment -->
        @can('other payment manage')
        <div class="card set-card mb-2">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        <h5>{{ __('Other Payment') }}</h5>
                    </div>
                    @can('other payment create')
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#create_other_payment_modal">
                            <i class="ti ti-plus"></i>
                        </button>
                    </div>
                    @endcan
                </div>
            </div>
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Min Salary') }}</th>
                                <th>{{ __('Max Salary') }}</th>
                                @if (Gate::check('other payment edit') || Gate::check('other payment delete'))
                                    <th>{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @isset($other_payments)
                                @foreach($other_payments as $other_payment)
                                    <tr>
                                        <td>{{ $other_payment['name'] }}</td>
                                        <td>{{ ucfirst($other_payment['amount_type']) }}</td>
                                        <td>{{ $other_payment['amount_type'] === 'percentage' ? $other_payment['amount'] . '%' : $other_payment['amount'] }}</td>
                                        <td>{{ $other_payment['min_salary'] ?? 'N/A' }}</td>
                                        <td>{{ $other_payment['max_salary'] ?? 'N/A' }}</td>
                                        @if (Gate::check('other payment edit') || Gate::check('other payment delete'))
                                            <td>
                                                @can('other payment edit')
                                                    <a type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#edit_other_payment_modal" data-entry="other_payment" data-name="{{ $other_payment['name'] }}">{{ __('Edit') }}</a>
                                                @endcan
                                                @can('other payment delete')
                                                    <a class="btn btn-sm btn-danger" href="{{ route('salarymodtemplate.entry.delete', ['entry' => 'other_payment', 'name' => $other_payment['name']]) }}">{{ __('Delete') }}</a>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan
        <!-- END Other Payment -->
        <!-- Overtime -->
        @can('overtime manage')
        <div class="card set-card mb-2">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        <h5>{{ __('Overtime') }}</h5>
                    </div>
                    @can('overtime create')
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#create_overtime_modal">
                            <i class="ti ti-plus"></i>
                        </button>
                    </div>
                    @endcan
                </div>
            </div>
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('No. of Days') }}</th>
                                <th>{{ __('Hours') }}</th>
                                <th>{{ __('Rate') }}</th>
                                <th>{{ __('Min Salary') }}</th>
                                <th>{{ __('Max Salary') }}</th>
                                @if (Gate::check('overtime edit') || Gate::check('overtime delete'))
                                    <th>{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @isset($overtimes)
                                @foreach($overtimes as $overtime)
                                    <tr>
                                        <td>{{ $overtime['name'] }}</td>
                                        <td>{{ $overtime['no_of_days'] }}</td>
                                        <td>{{ $overtime['hours'] }}</td>
                                        <td>{{ $overtime['rate'] }}</td>
                                        <td>{{ $overtime['min_salary'] ?? 'N/A' }}</td>
                                        <td>{{ $overtime['max_salary'] ?? 'N/A' }}</td>
                                        @if (Gate::check('overtime edit') || Gate::check('overtime delete'))
                                            <td>
                                                @can('overtime edit')
                                                    <a type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#edit_overtime_modal" data-entry="overtime" data-name="{{ $overtime['name'] }}">{{ __('Edit') }}</a>
                                                @endcan
                                                @can('overtime delete')
                                                    <a class="btn btn-sm btn-danger" href="{{ route('salarymodtemplate.entry.delete', ['entry' => 'overtime', 'name' => $overtime['name']]) }}">{{ __('Delete') }}</a>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan
        <!-- END Overtime -->
    </div>
</div>

<!-- Create Allowance Modal -->
<div class="modal fade" id="create_allowance_modal" tabindex="-1" aria-labelledby="createAllowanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAllowanceModalLabel">{{ __('Create Allowance') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createAllowanceForm" method="POST" action="{{ route('salarymodtemplate.store') }}">
                    @csrf
                    <!-- hidden input to define what is being added -->
                    <input type="hidden" name="mod_type" value="allowance">
                    <!-- END hidden input to define what is being added -->
                    <div class="row">
                        <div class="col-6">
                            <!-- select allowance option -->
                            <div class="mb-3">
                                <label for="allowanceOption" class="form-label">{{ __('Allowance Option') }}</label>
                                <select class="form-select" id="allowanceOption" name="allowance_option_id" required>
                                    <option value="">--</option>
                                    @isset($allowanceoptions)
                                        @foreach($allowanceoptions as $option)
                                            <option value="{{ $option->id }}">{{ $option->name }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <!-- END select allowance option -->
                            <!-- input allowance name -->
                            <div class="mb-3">
                                <label for="allowanceName" class="form-label">{{ __('Allowance Name') }}</label>
                                <input type="text" class="form-control" id="allowanceName" name="allowance_name" required>
                            </div>
                            <!-- END input allowance name -->
                        </div>
                        <div class="col-6">
                            <!-- select allowance amount type -->
                            <div class="mb-3">
                                <label for="allowanceAmountType" class="form-label">{{ __('Allowance Amount Type') }}</label>
                                <select class="form-select update_symbol" id="allowanceAmountType" name="allowance_amount_type" required>
                                    <option value="fixed" selected>Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <!-- END select allowance amount type -->
                            <!-- input allowance amount -->
                            <div class="mb-3">
                                <label for="allowanceAmount" class="form-label">{{ __('Allowance Amount') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="allowanceAmount" name="allowance_amount" aria-describedby="allowanceAmountSymbol" required>
                                    <span class="input-group-text" id="allowanceAmountSymbol">{{ currency(admin_setting('defult_currancy'))->code }}</span>
                                </div>
                            </div>
                            <!-- END input allowance amount -->
                        </div>
                    </div>
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="allowanceThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="allowanceThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="allowanceThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="createAllowanceForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Create Allowance Modal -->

<!-- Edit Allowance Modal -->
<div class="modal fade" id="edit_allowance_modal" tabindex="-1" aria-labelledby="editAllowanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAllowanceModalLabel">{{ __('Edit Allowance') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAllowanceForm" method="POST" action="{{ isset($allowance) ? route('salarymodtemplate.entry.update', ['entry' => 'allowance', 'name' => $allowance['name']]) : '#' }}">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <!-- select allowance option -->
                            <div class="mb-3">
                                <label for="allowanceOption" class="form-label">{{ __('Allowance Option') }}</label>
                                <select class="form-select" id="allowanceOption" name="allowance_option_id" required>
                                    <option value="">--</option>
                                    @isset($allowanceoptions)
                                        @foreach($allowanceoptions as $option)
                                            <option value="{{ $option->id }}">{{ $option->name }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <!-- END select allowance option -->
                            <!-- input allowance name -->
                            <div class="mb-3">
                                <label for="allowanceName" class="form-label">{{ __('Allowance Name') }}</label>
                                <input type="text" class="form-control" id="allowanceName" name="allowance_name" required>
                            </div>
                            <!-- END input allowance name -->
                        </div>
                        <div class="col-6">
                            <!-- select allowance amount type -->
                            <div class="mb-3">
                                <label for="allowanceAmountType" class="form-label">{{ __('Allowance Amount Type') }}</label>
                                <select class="form-select update_symbol" id="allowanceAmountType" name="allowance_amount_type" required>
                                    <option value="fixed" selected>Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <!-- END select allowance amount type -->
                            <!-- input allowance amount -->
                            <div class="mb-3">
                                <label for="allowanceAmount" class="form-label">{{ __('Allowance Amount') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="allowanceAmount" name="allowance_amount" aria-describedby="allowanceAmountSymbol" required>
                                    <span class="input-group-text" id="allowanceAmountSymbol">{{ currency(admin_setting('defult_currancy'))->code }}</span>
                                </div>
                            </div>
                            <!-- END input allowance amount -->
                        </div>
                    </div>
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="allowanceThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="allowanceThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="allowanceThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="editAllowanceForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Edit Allowance Modal -->

<!-- Create Commission Modal -->
<div class="modal fade" id="create_commission_modal" tabindex="-1" aria-labelledby="createCommissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCommissionModalLabel">{{ __('Create Commission') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createCommissionForm" method="POST" action="{{ route('salarymodtemplate.store') }}">
                    @csrf
                    <!-- hidden input to define what is being added -->
                    <input type="hidden" name="mod_type" value="commission">
                    <!-- END hidden input to define what is being added -->
                    <div class="row">
                        <!-- input commission name -->
                        <div class="mb-3">
                            <label for="commissionName" class="form-label">{{ __('Commission Name') }}</label>
                            <input type="text" class="form-control" id="commissionName" name="commission_name" required>
                        </div>
                        <!-- END input commission name -->
                        <div class="col-6">
                            <!-- select commission amount type -->
                            <div class="mb-3">
                                <label for="commissionAmountType" class="form-label">{{ __('Commission Amount Type') }}</label>
                                <select class="form-select update_symbol" id="commissionAmountType" name="commission_amount_type" required>
                                    <option value="fixed" selected>Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <!-- END select commission amount type -->
                        </div>
                        <div class="col-6">
                            <!-- input commission amount -->
                            <div class="mb-3">
                                <label for="commissionAmount" class="form-label">{{ __('Commission Amount') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="commissionAmount" name="commission_amount" aria-describedby="commissionAmountSymbol" required>
                                    <span class="input-group-text" id="commissionAmountSymbol">{{ currency(admin_setting('defult_currancy'))->code }}</span>
                                </div>
                            </div>
                            <!-- END input commission amount -->
                        </div>
                    </div>
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="commissionThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="commissionThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="commissionThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="createCommissionForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Create Commission Modal -->

<!-- Edit Commission Modal -->
<div class="modal fade" id="edit_commission_modal" tabindex="-1" aria-labelledby="editCommissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCommissionModalLabel">{{ __('Edit Commission') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCommissionForm" method="POST" action="{{ isset($commission) ? route('salarymodtemplate.entry.update', ['entry' => 'commission', 'name' => $commission['name']]) : '#' }}">
                    @csrf
                    <div class="row">
                            <!-- input commission name -->
                            <div class="mb-3">
                                <label for="commissionName" class="form-label">{{ __('Commission Name') }}</label>
                                <input type="text" class="form-control" id="commissionName" name="commission_name" required>
                            </div>
                            <!-- END input commission name -->
                        <div class="col-6">
                            <!-- select commission amount type -->
                            <div class="mb-3">
                                <label for="commissionAmountType" class="form-label">{{ __('Commission Amount Type') }}</label>
                                <select class="form-select update_symbol" id="commissionAmountType" name="commission_amount_type" required>
                                    <option value="fixed" selected>Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <!-- END select commission amount type -->
                        </div>
                        <div class="col-6">
                            <!-- input commission amount -->
                            <div class="mb-3">
                                <label for="commissionAmount" class="form-label">{{ __('Commission Amount') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="commissionAmount" name="commission_amount" aria-describedby="commissionAmountSymbol" required>
                                    <span class="input-group-text" id="commissionAmountSymbol">{{ currency(admin_setting('defult_currancy'))->code }}</span>
                                </div>
                            </div>
                            <!-- END input commission amount -->
                        </div>
                    </div>
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="commissionThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="commissionThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="commissionThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="editCommissionForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Edit Commission Modal -->

<!-- Create Loan Modal -->
<div class="modal fade" id="create_loan_modal" tabindex="-1" aria-labelledby="createLoanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createLoanModalLabel">{{ __('Create Loan') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createLoanForm" method="POST" action="{{ route('salarymodtemplate.store') }}">
                    @csrf
                    <!-- hidden input to define what is being added -->
                    <input type="hidden" name="mod_type" value="loan">
                    <!-- END hidden input to define what is being added -->
                    <div class="row">
                        <div class="col-6">
                            <!-- select loan option -->
                            <div class="mb-3">
                                <label for="loanOption" class="form-label">{{ __('Loan Option') }}</label>
                                <select class="form-select" id="loanOption" name="loan_option_id" required>
                                    <option value="">--</option>
                                    @isset($loanoptions)
                                        @foreach($loanoptions as $loanoption)
                                            <option value="{{ $loanoption->id }}">{{ $loanoption->name }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <!-- END select loan option -->
                            <!-- input loan name -->
                            <div class="mb-3">
                                <label for="loanName" class="form-label">{{ __('Loan Name') }}</label>
                                <input type="text" class="form-control" id="loanName" name="loan_name" required>
                            </div>
                            <!-- END input loan name -->
                            <!-- date loan start -->
                            <div class="mb-3">
                                <label for="loanStartDate" class="form-label">{{ __('Start Date') }}</label>
                                <input type="text" class="form-control" id="loanStartDate" name="start_date" required>
                            </div>
                            <!-- END date loan start -->
                        </div>
                        <div class="col-6">
                            <!-- select loan amount type -->
                            <div class="mb-3">
                                <label for="loanAmountType" class="form-label">{{ __('Loan Amount Type') }}</label>
                                <select class="form-select update_symbol" id="loanAmountType" name="loan_amount_type" required>
                                    <option value="fixed" selected>Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <!-- END select loan amount type -->
                            <!-- input loan amount -->
                            <div class="mb-3">
                                <label for="loanAmount" class="form-label">{{ __('Loan Amount') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="loanAmount" name="loan_amount" aria-describedby="loanAmountSymbol" required>
                                    <span class="input-group-text" id="loanAmountSymbol">{{ currency(admin_setting('defult_currancy'))->code }}</span>
                                </div>
                            </div>
                            <!-- END input loan amount -->
                            <!-- date loan end -->
                            <div class="mb-3">
                                <label for="loanEndDate" class="form-label">{{ __('End Date') }}</label>
                                <input type="text" class="form-control" id="loanEndDate" name="end_date" required>
                            </div>
                            <!-- END date loan end -->
                        </div>
                    </div>
                    <!-- text loan reason -->
                    <div class="mb-3">
                        <label for="loanReason" class="form-label">{{ __('Loan Reason') }}</label>
                        <textarea class="form-control" row="3" id="loanReason" name="loan_reason" cols="50" required></textarea>
                    </div>
                    <!-- END text loan reason -->
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="loanThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="loanThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="loanThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="createLoanForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Create Loan Modal -->

<!-- Edit Loan Modal -->
<div class="modal fade" id="edit_loan_modal" tabindex="-1" aria-labelledby="editLoanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLoanModalLabel">{{ __('Edit Loan') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editLoanForm" method="POST" action="{{ isset($loan) ? route('salarymodtemplate.entry.update', ['entry' => 'loan', 'name' => $loan['name']]) : '#' }}">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <!-- select loan option -->
                            <div class="mb-3">
                                <label for="loanOption" class="form-label">{{ __('Loan Option') }}</label>
                                <select class="form-select" id="loanOption" name="loan_option_id" required>
                                    <option value="">--</option>
                                    @isset($loanoptions)
                                        @foreach($loanoptions as $option)
                                            <option value="{{ $option->id }}">{{ $option->name }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <!-- END select loan option -->
                            <!-- input loan name -->
                            <div class="mb-3">
                                <label for="loanName" class="form-label">{{ __('Loan Name') }}</label>
                                <input type="text" class="form-control" id="loanName" name="loan_name" required>
                            </div>
                            <!-- END input loan name -->
                            <!-- date loan start -->
                            <div class="mb-3">
                                <label for="loanStartDate" class="form-label">{{ __('Start Date') }}</label>
                                <input type="text" class="form-control" id="loanStartDate" name="start_date" required>
                            </div>
                            <!-- END date loan start -->
                        </div>
                        <div class="col-6">
                            <!-- select loan amount type -->
                            <div class="mb-3">
                                <label for="loanAmountType" class="form-label">{{ __('Loan Amount Type') }}</label>
                                <select class="form-select update_symbol" id="loanAmountType" name="loan_amount_type" required>
                                    <option value="fixed" selected>Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <!-- END select loan amount type -->
                            <!-- input loan amount -->
                            <div class="mb-3">
                                <label for="loanAmount" class="form-label">{{ __('Loan Amount') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="loanAmount" name="loan_amount" aria-describedby="loanAmountSymbol" required>
                                    <span class="input-group-text" id="loanAmountSymbol">{{ currency(admin_setting('defult_currancy'))->code }}</span>
                                </div>
                            </div>
                            <!-- END input loan amount -->
                            <!-- date loan end -->
                            <div class="mb-3">
                                <label for="loanEndDate" class="form-label">{{ __('End Date') }}</label>
                                <input type="text" class="form-control" id="loanEndDate" name="end_date" required>
                            </div>
                            <!-- END date loan end -->
                        </div>
                    </div>
                    <!-- text loan reason -->
                    <div class="mb-3">
                        <label for="loanReason" class="form-label">{{ __('Loan Reason') }}</label>
                        <textarea class="form-control" row="3" id="loanReason" name="loan_reason" cols="50" required></textarea>
                    </div>
                    <!-- END text loan reason -->
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="loanThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="loanThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="loanThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="editLoanForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Edit Loan Modal -->

<!-- Create Saturation Deduction Modal -->
<div class="modal fade" id="create_saturation_deduction_modal" tabindex="-1" aria-labelledby="createSaturationDeductionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSaturationDeductionModalLabel">{{ __('Create Saturation Deduction') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createSaturationDeductionForm" method="POST" action="{{ route('salarymodtemplate.store') }}">
                    @csrf
                    <!-- hidden input to define what is being added -->
                    <input type="hidden" name="mod_type" value="saturation_deduction">
                    <!-- END hidden input to define what is being added -->
                    <div class="row">
                        <div class="col-6">
                            <!-- select saturation deduction option -->
                            <div class="mb-3">
                                <label for="deductionOption" class="form-label">{{ __('Deduction Option') }}</label>
                                <select class="form-select" id="deductionOption" name="saturation_deduction_option_id" required>
                                    <option value="">--</option>
                                    @isset($deductionoptions)
                                        @foreach($deductionoptions as $deductionoption)
                                            <option value="{{ $deductionoption->id }}">{{ $deductionoption->name }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <!-- END select saturation deduction option -->
                            <!-- input saturation deduction name -->
                            <div class="mb-3">
                                <label for="deductionName" class="form-label">{{ __('Deduction Name') }}</label>
                                <input type="text" class="form-control" id="deductionName" name="saturation_deduction_name" required>
                            </div>
                            <!-- END input saturation deduction name -->
                        </div>
                        <div class="col-6">
                            <!-- select saturation deduction amount type -->
                            <div class="mb-3">
                                <label for="deductionAmountType" class="form-label">{{ __('Deduction Amount Type') }}</label>
                                <select class="form-select update_symbol" id="deductionAmountType" name="saturation_deduction_amount_type" required>
                                    <option value="fixed" selected>Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <!-- END select saturation deduction amount type -->
                            <!-- input saturation deduction amount -->
                            <div class="mb-3">
                                <label for="deductionAmount" class="form-label">{{ __('Deduction Amount') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="deductionAmount" name="saturation_deduction_amount" aria-describedby="deductionAmountSymbol" required>
                                    <span class="input-group-text" id="deductionAmountSymbol">{{ currency(admin_setting('defult_currancy'))->code }}</span>
                                </div>
                            </div>
                            <!-- END input saturation deduction amount -->
                        </div>
                    </div>
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="deductionThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="deductionThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="deductionThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="createSaturationDeductionForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Create Saturation Deduction Modal -->

<!-- Edit Saturation Deduction Modal -->
<div class="modal fade" id="edit_saturation_deduction_modal" tabindex="-1" aria-labelledby="editSaturationDeductionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSaturationDeductionModalLabel">{{ __('Edit Saturation Deduction') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editDeductionForm" method="POST" action="{{ isset($saturation_deduction) ? route('salarymodtemplate.entry.update', ['entry' => 'saturation_deduction', 'name' => $saturation_deduction['name']]) : '#' }}">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <!-- select saturation deduction option -->
                            <div class="mb-3">
                                <label for="deductionOption" class="form-label">{{ __('Deduction Option') }}</label>
                                <select class="form-select" id="deductionOption" name="saturation_deduction_option_id" required>
                                    <option value="">--</option>
                                    @isset($deductionoptions)
                                        @foreach($deductionoptions as $option)
                                            <option value="{{ $option->id }}">{{ $option->name }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <!-- END select saturation deduction option -->
                            <!-- input saturation deduction name -->
                            <div class="mb-3">
                                <label for="deductionName" class="form-label">{{ __('Deduction Name') }}</label>
                                <input type="text" class="form-control" id="deductionName" name="saturation_deduction_name" required>
                            </div>
                            <!-- END input saturation deduction name -->
                        </div>
                        <div class="col-6">
                            <!-- select saturation deduction amount type -->
                            <div class="mb-3">
                                <label for="deductionAmountType" class="form-label">{{ __('Deduction Amount Type') }}</label>
                                <select class="form-select update_symbol" id="deductionAmountType" name="saturation_deduction_amount_type" required>
                                    <option value="fixed" selected>Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <!-- END select saturation deduction amount type -->
                            <!-- input saturation deduction amount -->
                            <div class="mb-3">
                                <label for="deductionAmount" class="form-label">{{ __('Deduction Amount') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="deductionAmount" name="saturation_deduction_amount" aria-describedby="deductionAmountSymbol" required>
                                    <span class="input-group-text" id="deductionAmountSymbol">{{ currency(admin_setting('defult_currancy'))->code }}</span>
                                </div>
                            </div>
                            <!-- END input saturation deduction amount -->
                        </div>
                    </div>
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="deductionThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="deductionThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="deductionThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="editDeductionForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Edit Saturation Deduction Modal -->

<!-- Create Tax Deduction Modal -->
<div class="modal fade" id="create_tax_deduction_modal" tabindex="-1" aria-labelledby="createTaxDeductionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTaxDeductionModalLabel">{{ __('Create Tax Deduction') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createTaxDeductionForm" method="POST" action="{{ route('salarymodtemplate.store') }}">
                    @csrf
                    <!-- hidden input to define what is being added -->
                    <input type="hidden" name="mod_type" value="tax_deduction">
                    <!-- END hidden input to define what is being added -->
                    <div class="row">
                        <!-- input tax deduction name -->
                        <div class="mb-3">
                            <label for="taxDeductionName" class="form-label">{{ __('Tax Deduction Name') }}</label>
                            <input type="text" class="form-control" id="taxDeductionName" name="tax_deduction_name" required>
                        </div>
                        <!-- END input tax deduction name -->
                        <div class="col-6">
                            <!-- select tax deduction amount type -->
                            <div class="mb-3">
                                <label for="taxDeductionAmountType" class="form-label">{{ __('Tax Deduction Amount Type') }}</label>
                                <select class="form-select update_symbol" id="taxDeductionAmountType" name="tax_deduction_amount_type" required>
                                    <option value="fixed" selected>Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <!-- END select tax deduction amount type -->
                        </div>
                        <div class="col-6">
                            <!-- input tax deduction amount -->
                            <div class="mb-3">
                                <label for="taxDeductionAmount" class="form-label">{{ __('Tax Deduction Amount') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="taxDeductionAmount" name="tax_deduction_amount" aria-describedby="taxDeductionAmountSymbol" required>
                                    <span class="input-group-text" id="taxDeductionAmountSymbol">{{ currency(admin_setting('defult_currancy'))->code }}</span>
                                </div>
                            </div>
                            <!-- END input tax deduction amount -->
                        </div>
                    </div>
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="taxDeductionThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="taxDeductionThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="taxDeductionThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="createTaxDeductionForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Create Tax Deduction Modal -->

<!-- Edit Tax Deduction Modal -->
<div class="modal fade" id="edit_tax_deduction_modal" tabindex="-1" aria-labelledby="editTaxDeductionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaxDeductionModalLabel">{{ __('Edit Tax Deduction') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTaxDeductionForm" method="POST" action="{{ isset($tax_deduction) ? route('salarymodtemplate.entry.update', ['entry' => 'tax_deduction', 'name' => $tax_deduction['name']]) : '#' }}">
                    @csrf
                    <div class="row">
                            <!-- input tax deduction name -->
                            <div class="mb-3">
                                <label for="taxDeductionName" class="form-label">{{ __('Tax Deduction Name') }}</label>
                                <input type="text" class="form-control" id="taxDeductionName" name="tax_deduction_name" required>
                            </div>
                            <!-- END input tax deduction name -->
                        <div class="col-6">
                            <!-- select tax deduction amount type -->
                            <div class="mb-3">
                                <label for="taxDeductionAmountType" class="form-label">{{ __('Tax Deduction Amount Type') }}</label>
                                <select class="form-select update_symbol" id="taxDeductionAmountType" name="tax_deduction_amount_type" required>
                                    <option value="fixed" selected>Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <!-- END select tax deduction amount type -->
                        </div>
                        <div class="col-6">
                            <!-- input tax deduction amount -->
                            <div class="mb-3">
                                <label for="taxDeductionAmount" class="form-label">{{ __('Tax Deduction Amount') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="taxDeductionAmount" name="tax_deduction_amount" aria-describedby="taxDeductionAmountSymbol" required>
                                    <span class="input-group-text" id="taxDeductionAmountSymbol">{{ currency(admin_setting('defult_currancy'))->code }}</span>
                                </div>
                            </div>
                            <!-- END input tax deduction amount -->
                        </div>
                    </div>
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="taxDeductionThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="taxDeductionThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="taxDeductionThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="editTaxDeductionForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Edit Tax Deduction Modal -->

<!-- Create Tax Relief Modal -->
<div class="modal fade" id="create_tax_relief_modal" tabindex="-1" aria-labelledby="createTaxReliefModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTaxReliefModalLabel">{{ __('Create Tax Relief') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createTaxReliefForm" method="POST" action="{{ route('salarymodtemplate.store') }}">
                    @csrf
                    <!-- hidden input to define what is being added -->
                    <input type="hidden" name="mod_type" value="tax_relief">
                    <!-- END hidden input to define what is being added -->
                    <div class="row">
                        <!-- input tax relief name -->
                        <div class="mb-3">
                            <label for="taxReliefName" class="form-label">{{ __('Tax Relief Name') }}</label>
                            <input type="text" class="form-control" id="taxReliefName" name="tax_relief_name" required>
                        </div>
                        <!-- END input tax relief name -->
                        <div class="col-6">
                            <!-- select tax relief amount type -->
                            <div class="mb-3">
                                <label for="taxReliefAmountType" class="form-label">{{ __('Tax Relief Amount Type') }}</label>
                                <select class="form-select update_symbol" id="taxReliefAmountType" name="tax_relief_amount_type" required>
                                    <option value="fixed" selected>Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <!-- END select tax relief amount type -->
                        </div>
                        <div class="col-6">
                            <!-- input tax relief amount -->
                            <div class="mb-3">
                                <label for="taxReliefAmount" class="form-label">{{ __('Tax Relief Amount') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="taxReliefAmount" name="tax_relief_amount" aria-describedby="taxReliefAmountSymbol" required>
                                    <span class="input-group-text" id="taxReliefAmountSymbol">{{ currency(admin_setting('defult_currancy'))->code }}</span>
                                </div>
                            </div>
                            <!-- END input tax relief amount -->
                        </div>
                    </div>
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="taxReliefThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="taxReliefThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="taxReliefThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="createTaxReliefForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Create Tax Relief Modal -->

<!-- Edit Tax Relief Modal -->
<div class="modal fade" id="edit_tax_relief_modal" tabindex="-1" aria-labelledby="editTaxReliefModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaxReliefModalLabel">{{ __('Edit Tax Relief') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTaxReliefForm" method="POST" action="{{ isset($tax_relief) ? route('salarymodtemplate.entry.update', ['entry' => 'tax_relief', 'name' => $tax_relief['name']]) : '#' }}">
                    @csrf
                    <div class="row">
                        <!-- input tax relief name -->
                        <div class="mb-3">
                            <label for="taxReliefName" class="form-label">{{ __('Tax Relief Name') }}</label>
                            <input type="text" class="form-control" id="taxReliefName" name="tax_relief_name" required>
                        </div>
                        <!-- END input tax relief name -->
                        <div class="col-6">
                            <!-- select tax relief amount type -->
                            <div class="mb-3">
                                <label for="taxReliefAmountType" class="form-label">{{ __('Tax Relief Amount Type') }}</label>
                                <select class="form-select update_symbol" id="taxReliefAmountType" name="tax_relief_amount_type" required>
                                    <option value="fixed" selected>Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <!-- END select tax relief amount type -->
                        </div>
                        <div class="col-6">
                            <!-- input tax relief amount -->
                            <div class="mb-3">
                                <label for="taxReliefAmount" class="form-label">{{ __('Tax Relief Amount') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="taxReliefAmount" name="tax_relief_amount" aria-describedby="taxReliefAmountSymbol" required>
                                    <span class="input-group-text" id="taxReliefAmountSymbol">{{ currency(admin_setting('defult_currancy'))->code }}</span>
                                </div>
                            </div>
                            <!-- END input tax relief amount -->
                        </div>
                    </div>
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="taxReliefThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="taxReliefThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="taxReliefThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="editTaxReliefForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Edit Tax Relief Modal -->

<!-- Create Other Payment Modal -->
<div class="modal fade" id="create_other_payment_modal" tabindex="-1" aria-labelledby="createOtherPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createOtherPaymentModalLabel">{{ __('Create Other Payment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createOtherPaymentForm" method="POST" action="{{ route('salarymodtemplate.store') }}">
                    @csrf
                    <!-- hidden input to define what is being added -->
                    <input type="hidden" name="mod_type" value="other_payment">
                    <!-- END hidden input to define what is being added -->
                    <div class="row">
                        <!-- input other payment name -->
                        <div class="mb-3">
                            <label for="otherPaymentName" class="form-label">{{ __('Other Payment Name') }}</label>
                            <input type="text" class="form-control" id="otherPaymentName" name="other_payment_name" required>
                        </div>
                        <!-- END input other payment name -->
                        <div class="col-6">
                            <!-- select other payment amount type -->
                            <div class="mb-3">
                                <label for="otherPaymentAmountType" class="form-label">{{ __('Other Payment Amount Type') }}</label>
                                <select class="form-select update_symbol" id="otherPaymentAmountType" name="other_payment_amount_type" required>
                                    <option value="fixed" selected>Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <!-- END select other payment amount type -->
                        </div>
                        <div class="col-6">
                            <!-- input other payment amount -->
                            <div class="mb-3">
                                <label for="otherPaymentAmount" class="form-label">{{ __('Other Payment Amount') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="otherPaymentAmount" name="other_payment_amount" aria-describedby="otherPaymentAmountSymbol" required>
                                    <span class="input-group-text" id="otherPaymentAmountSymbol">{{ currency(admin_setting('defult_currancy'))->code }}</span>
                                </div>
                            </div>
                            <!-- END input other payment amount -->
                        </div>
                    </div>
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="otherPaymentThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="otherPaymentThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="otherPaymentThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="createOtherPaymentForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Create Other Payment Modal -->

<!-- Edit Other Payment Modal -->
<div class="modal fade" id="edit_other_payment_modal" tabindex="-1" aria-labelledby="editOtherPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editOtherPaymentModalLabel">{{ __('Edit Other Payment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editOtherPaymentForm" method="POST" action="{{ isset($other_payment) ? route('salarymodtemplate.entry.update', ['entry' => 'other_payment', 'name' => $other_payment['name']]) : '#' }}">
                    @csrf
                    <div class="row">
                        <!-- input other payment name -->
                        <div class="mb-3">
                            <label for="otherPaymentName" class="form-label">{{ __('Other Payment Name') }}</label>
                            <input type="text" class="form-control" id="otherPaymentName" name="other_payment_name" required>
                        </div>
                        <!-- END input other payment name -->
                        <div class="col-6">
                            <!-- select other payment amount type -->
                            <div class="mb-3">
                                <label for="otherPaymentAmountType" class="form-label">{{ __('Other Payment Amount Type') }}</label>
                                <select class="form-select update_symbol" id="otherPaymentAmountType" name="other_payment_amount_type" required>
                                    <option value="fixed" selected>Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <!-- END select other payment amount type -->
                        </div>
                        <div class="col-6">
                            <!-- input other payment amount -->
                            <div class="mb-3">
                                <label for="otherPaymentAmount" class="form-label">{{ __('Other Payment Amount') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="otherPaymentAmount" name="other_payment_amount" aria-describedby="otherPaymentAmountSymbol" required>
                                    <span class="input-group-text" id="otherPaymentAmountSymbol">{{ currency(admin_setting('defult_currancy'))->code }}</span>
                                </div>
                            </div>
                            <!-- END input other payment amount -->
                        </div>
                    </div>
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="otherPaymentThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="otherPaymentThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="otherPaymentThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="editOtherPaymentForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Edit Other Payment Modal -->

<!-- Create Overtime Modal -->
<div class="modal fade" id="create_overtime_modal" tabindex="-1" aria-labelledby="createOvertimeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createOvertimeModalLabel">{{ __('Create Overtime') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createOvertimeForm" method="POST" action="{{ route('salarymodtemplate.store') }}">
                    @csrf
                    <!-- hidden input to define what is being added -->
                    <input type="hidden" name="mod_type" value="overtime">
                    <!-- END hidden input to define what is being added -->
                    <div class="row">
                        <div class="col-6">
                            <!-- input overtime name -->
                            <div class="mb-3">
                                <label for="overtimeName" class="form-label">{{ __('Overtime Name') }}</label>
                                <input type="text" class="form-control" id="overtimeName" name="overtime_name" required>
                            </div>
                            <!-- END input overtime name -->
                            <!-- input overtime number of days -->
                            <div class="mb-3">
                                <label for="overtimeNumberOfDays" class="form-label">{{ __('Number of days') }}</label>
                                <input type="number" class="form-control" id="overtimeNumberOfDays" name="no_of_days" required>
                            </div>
                            <!-- END input overtime number of days -->
                        </div>
                        <div class="col-6">
                            <!-- input overtime hours -->
                            <div class="mb-3">
                                <label for="overtimeHours" class="form-label">{{ __('Hours') }}</label>
                                <input type="number" class="form-control" id="overtimeHours" name="hours" required>
                            </div>
                            <!-- END input overtime hours -->
                            <!-- input overtime rate -->
                            <div class="mb-3">
                                <label for="overtimeRate" class="form-label">{{ __('Rate') }}</label>
                                <input type="number" class="form-control" id="overtimeRate" name="rate" required>
                            </div>
                            <!-- END input overtime rate -->
                        </div>
                    </div>
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="overtimeThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="overtimeThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="overtimeThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="createOvertimeForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Create Overtime Modal -->

<!-- Edit Overtime Modal -->
<div class="modal fade" id="edit_overtime_modal" tabindex="-1" aria-labelledby="editOvertimeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editOvertimeModalLabel">{{ __('Edit Overtime') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editOvertimeForm" method="POST" action="{{ isset($overtime) ? route('salarymodtemplate.entry.update', ['entry' => 'overtime', 'name' => $overtime['name']]) : '#' }}">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <!-- input overtime name -->
                            <div class="mb-3">
                                <label for="overtimeName" class="form-label">{{ __('Overtime Name') }}</label>
                                <input type="text" class="form-control" id="overtimeName" name="overtime_name" required>
                            </div>
                            <!-- END input other payment name -->
                            <!-- input overtime number of days -->
                            <div class="mb-3">
                                <label for="overtimeNumberOfDays" class="form-label">{{ __('Number of days') }}</label>
                                <input type="number" class="form-control" id="overtimeNumberOfDays" name="no_of_days" required>
                            </div>
                            <!-- END input overtime number of days -->
                        </div>
                        <div class="col-6">
                            <!-- input overtime hours -->
                            <div class="mb-3">
                                <label for="overtimeHours" class="form-label">{{ __('Hours') }}</label>
                                <input type="number" class="form-control" id="overtimeHours" name="hours" required>
                            </div>
                            <!-- END input overtime hours -->
                            <!-- input overtime rate -->
                            <div class="mb-3">
                                <label for="overtimeRate" class="form-label">{{ __('Rate') }}</label>
                                <input type="number" class="form-control" id="overtimeRate" name="rate" required>
                            </div>
                            <!-- END input overtime rate -->
                        </div>
                    </div>
                    <!-- Threshold Switch -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input switch_update" type="checkbox" id="overtimeThresholdSwitch" name="salary_threshold">
                        <label class="form-check-label" for="overtimeThresholdSwitch">{{ __('Threshold') }}</label>
                    </div>
                    <!-- END Threshold Switch -->
                    <!-- Min and Max Salary Fields -->
                    <div id="overtimeThresholdFields" class="switch_fields" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="minSalary" class="form-label">{{ __('Min Salary') }}</label>
                                    <input type="number" class="form-control" id="minSalary" name="min_salary">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="maxSalary" class="form-label">{{ __('Max Salary') }}</label>
                                    <input type="number" class="form-control" id="maxSalary" name="max_salary">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Min and Max Salary Fields -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" form="editOvertimeForm">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- END Edit Overtime Modal -->

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var currencyCode = "{{ currency(admin_setting('defult_currancy'))->code }}";

        // Function to update amount symbol
        function updateAmountSymbol(selectId, symbolId) {
            var amountTypeSelect = document.getElementById(selectId);
            var amountSymbol = document.getElementById(symbolId);
            amountTypeSelect.addEventListener('change', function() {
                if (amountTypeSelect.value === 'percentage') {
                    amountSymbol.textContent = '%';
                } else {
                    amountSymbol.textContent = currencyCode;
                }
            });
        }

        // Function to toggle threshold fields
        function toggleThresholdFields(switchId, fieldsId) {
            var thresholdSwitch = document.getElementById(switchId);
            var thresholdFields = document.getElementById(fieldsId);
            thresholdSwitch.addEventListener('change', function() {
                if (thresholdSwitch.checked) {
                    thresholdFields.style.display = 'block';
                } else {
                    thresholdFields.style.display = 'none';
                }
            });
        }

        // Update amount symbols
        updateAmountSymbol('allowanceAmountType', 'allowanceAmountSymbol');
        updateAmountSymbol('commissionAmountType', 'commissionAmountSymbol');
        updateAmountSymbol('loanAmountType', 'loanAmountSymbol');
        updateAmountSymbol('deductionAmountType', 'deductionAmountSymbol');
        updateAmountSymbol('taxDeductionAmountType', 'taxDeductionAmountSymbol');
        updateAmountSymbol('taxReliefAmountType', 'taxReliefAmountSymbol');
        updateAmountSymbol('otherPaymentAmountType', 'otherPaymentAmountSymbol');

        // Toggle threshold fields
        toggleThresholdFields('allowanceThresholdSwitch', 'allowanceThresholdFields');
        toggleThresholdFields('commissionThresholdSwitch', 'commissionThresholdFields');
        toggleThresholdFields('loanThresholdSwitch', 'loanThresholdFields');
        toggleThresholdFields('deductionThresholdSwitch', 'deductionThresholdFields');
        toggleThresholdFields('taxDeductionThresholdSwitch', 'taxDeductionThresholdFields');
        toggleThresholdFields('taxReliefThresholdSwitch', 'taxReliefThresholdFields');
        toggleThresholdFields('otherPaymentThresholdSwitch', 'otherPaymentThresholdFields');
        toggleThresholdFields('overtimeThresholdSwitch', 'overtimeThresholdFields');

        // Datepickers for loan
        flatpickr("#loanStartDate", {
            dateFormat: "Y-m-d"
        });

        flatpickr("#loanEndDate", {
            dateFormat: "Y-m-d"
        });

        // Edit Allowance Modal
        $('#edit_allowance_modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var entry = button.data('entry'); // Extract info from data-* attributes
            var name = button.data('name');

            // Update modal content with the entry and name
            var modal = $(this);
            modal.find('.modal-body #entry_input').val(entry);
            modal.find('.modal-body #name_input').val(name);

            // Find option ID based on the entry name
            var allowances = @json($allowances); // Convert PHP array to JavaScript

            // Check if allowances is an object
            if (typeof allowances === 'object' && allowances !== null) {
                var matchedEntry = allowances[name]; // Access property by name

                if (matchedEntry) {
                    // Update the inputs
                    modal.find('.modal-body #allowanceOption').val(matchedEntry.option_id);
                    modal.find('.modal-body #allowanceName').val(matchedEntry.name);
                    modal.find('.modal-body #allowanceAmountType').val(matchedEntry.amount_type);
                    modal.find('.modal-body #allowanceAmount').val(matchedEntry.amount);

                    // Properly toggle the switch
                    if (matchedEntry.threshold) {
                        modal.find('.modal-body #allowanceThresholdSwitch').prop('checked', true);
                        modal.find('.modal-body #allowanceThresholdFields').show();
                    } else {
                        modal.find('.modal-body #allowanceThresholdSwitch').prop('checked', false);
                        modal.find('.modal-body #allowanceThresholdFields').hide();
                    }

                    // Set the min and max salary fields
                    modal.find('.modal-body #minSalary').val(matchedEntry.min_salary);
                    modal.find('.modal-body #maxSalary').val(matchedEntry.max_salary);

                    // Update amount symbol on change
                    var editAmountTypeSelect = modal.find('.modal-body #allowanceAmountType');
                    var editAmountSymbol = modal.find('.modal-body #allowanceAmountSymbol');
                    editAmountTypeSelect.off('change').on('change', function() {
                        if (editAmountTypeSelect.val() === 'percentage') {
                            editAmountSymbol.text('%');
                        } else {
                            editAmountSymbol.text(currencyCode);
                        }
                    });

                    // Toggle threshold fields
                    var editThresholdSwitch = modal.find('.modal-body #allowanceThresholdSwitch');
                    editThresholdSwitch.off('change').on('change', function() {
                        if (editThresholdSwitch.prop('checked')) {
                            modal.find('.modal-body #allowanceThresholdFields').show();
                        } else {
                            modal.find('.modal-body #allowanceThresholdFields').hide();
                        }
                    });

                }
            } else {
                console.log(allowances);
            }
        });
        
        // Edit Commission Modal
        $('#edit_commission_modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var entry = button.data('entry'); // Extract info from data-* attributes
            var name = button.data('name');

            // Update modal content with the entry and name
            var modal = $(this);
            modal.find('.modal-body #entry_input').val(entry);
            modal.find('.modal-body #name_input').val(name);

            // Find option ID based on the entry name
            var commissions = @json($commissions); // Convert PHP array to JavaScript

            // Check if commissions is an object
            if (typeof commissions === 'object' && commissions !== null) {
                var matchedEntry = commissions[name]; // Access property by name

                if (matchedEntry) {
                    // Update the inputs
                    modal.find('.modal-body #commissionName').val(matchedEntry.name);
                    modal.find('.modal-body #commissionAmountType').val(matchedEntry.amount_type);
                    modal.find('.modal-body #commissionAmount').val(matchedEntry.amount);

                    // Properly toggle the switch
                    if (matchedEntry.threshold) {
                        modal.find('.modal-body #commissionThresholdSwitch').prop('checked', true);
                        modal.find('.modal-body #commissionThresholdFields').show();
                    } else {
                        modal.find('.modal-body #commissionThresholdSwitch').prop('checked', false);
                        modal.find('.modal-body #commissionThresholdFields').hide();
                    }

                    // Set the min and max salary fields
                    modal.find('.modal-body #minSalary').val(matchedEntry.min_salary);
                    modal.find('.modal-body #maxSalary').val(matchedEntry.max_salary);

                    // Update amount symbol on change
                    var editAmountTypeSelect = modal.find('.modal-body #commissionAmountType');
                    var editAmountSymbol = modal.find('.modal-body #commissionAmountSymbol');
                    editAmountTypeSelect.off('change').on('change', function() {
                        if (editAmountTypeSelect.val() === 'percentage') {
                            editAmountSymbol.text('%');
                        } else {
                            editAmountSymbol.text(currencyCode);
                        }
                    });

                    // Toggle threshold fields
                    var editThresholdSwitch = modal.find('.modal-body #commissionThresholdSwitch');
                    editThresholdSwitch.off('change').on('change', function() {
                        if (editThresholdSwitch.prop('checked')) {
                            modal.find('.modal-body #commissionThresholdFields').show();
                        } else {
                            modal.find('.modal-body #commissionThresholdFields').hide();
                        }
                    });

                }
            } else {
                console.log(commissions);
            }
        });
        
        // Edit Loan Modal
        $('#edit_loan_modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var entry = button.data('entry'); // Extract info from data-* attributes
            var name = button.data('name');

            // Update modal content with the entry and name
            var modal = $(this);
            modal.find('.modal-body #entry_input').val(entry);
            modal.find('.modal-body #name_input').val(name);

            // Find option ID based on the entry name
            var loans = @json($loans); // Convert PHP array to JavaScript

            // Check if loans is an object
            if (typeof loans === 'object' && loans !== null) {
                var matchedEntry = loans[name]; // Access property by name

                if (matchedEntry) {
                    // Update the inputs
                    modal.find('.modal-body #loanOption').val(matchedEntry.option_id);
                    modal.find('.modal-body #loanName').val(matchedEntry.name);
                    modal.find('.modal-body #loanAmountType').val(matchedEntry.amount_type);
                    modal.find('.modal-body #loanAmount').val(matchedEntry.amount);
                    modal.find('.modal-body #loanStartDate').val(matchedEntry.start_date);
                    modal.find('.modal-body #loanEndDate').val(matchedEntry.end_date);
                    modal.find('.modal-body #loanReason').val(matchedEntry.reason);

                    // Properly toggle the switch
                    if (matchedEntry.threshold) {
                        modal.find('.modal-body #loanThresholdSwitch').prop('checked', true);
                        modal.find('.modal-body #loanThresholdFields').show();
                    } else {
                        modal.find('.modal-body #loanThresholdSwitch').prop('checked', false);
                        modal.find('.modal-body #loanThresholdFields').hide();
                    }

                    // Set the min and max salary fields
                    modal.find('.modal-body #minSalary').val(matchedEntry.min_salary);
                    modal.find('.modal-body #maxSalary').val(matchedEntry.max_salary);

                    // Update amount symbol on change
                    var editAmountTypeSelect = modal.find('.modal-body #loanAmountType');
                    var editAmountSymbol = modal.find('.modal-body #loanAmountSymbol');
                    editAmountTypeSelect.off('change').on('change', function() {
                        if (editAmountTypeSelect.val() === 'percentage') {
                            editAmountSymbol.text('%');
                        } else {
                            editAmountSymbol.text(currencyCode);
                        }
                    });

                    // Toggle threshold fields
                    var editThresholdSwitch = modal.find('.modal-body #loanThresholdSwitch');
                    editThresholdSwitch.off('change').on('change', function() {
                        if (editThresholdSwitch.prop('checked')) {
                            modal.find('.modal-body #loanThresholdFields').show();
                        } else {
                            modal.find('.modal-body #loanThresholdFields').hide();
                        }
                    });

                }
            } else {
                console.log(loans);
            }
        });
        
        // Edit Saturation Deduction Modal
        $('#edit_saturation_deduction_modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var entry = button.data('entry'); // Extract info from data-* attributes
            var name = button.data('name');

            // Update modal content with the entry and name
            var modal = $(this);
            modal.find('.modal-body #entry_input').val(entry);
            modal.find('.modal-body #name_input').val(name);

            // Find option ID based on the entry name
            var saturation_deductions = @json($saturation_deductions); // Convert PHP array to JavaScript

            // Check if saturation_deductions is an object
            if (typeof saturation_deductions === 'object' && saturation_deductions !== null) {
                var matchedEntry = saturation_deductions[name]; // Access property by name

                if (matchedEntry) {
                    // Update the inputs
                    modal.find('.modal-body #deductionOption').val(matchedEntry.option_id);
                    modal.find('.modal-body #deductionName').val(matchedEntry.name);
                    modal.find('.modal-body #deductionAmountType').val(matchedEntry.amount_type);
                    modal.find('.modal-body #deductionAmount').val(matchedEntry.amount);

                    // Properly toggle the switch
                    if (matchedEntry.threshold) {
                        modal.find('.modal-body #deductionThresholdSwitch').prop('checked', true);
                        modal.find('.modal-body #deductionThresholdFields').show();
                    } else {
                        modal.find('.modal-body #deductionThresholdSwitch').prop('checked', false);
                        modal.find('.modal-body #deductionThresholdFields').hide();
                    }

                    // Set the min and max salary fields
                    modal.find('.modal-body #minSalary').val(matchedEntry.min_salary);
                    modal.find('.modal-body #maxSalary').val(matchedEntry.max_salary);

                    // Update amount symbol on change
                    var editAmountTypeSelect = modal.find('.modal-body #deductionAmountType');
                    var editAmountSymbol = modal.find('.modal-body #deductionAmountSymbol');
                    editAmountTypeSelect.off('change').on('change', function() {
                        if (editAmountTypeSelect.val() === 'percentage') {
                            editAmountSymbol.text('%');
                        } else {
                            editAmountSymbol.text(currencyCode);
                        }
                    });

                    // Toggle threshold fields
                    var editThresholdSwitch = modal.find('.modal-body #deductionThresholdSwitch');
                    editThresholdSwitch.off('change').on('change', function() {
                        if (editThresholdSwitch.prop('checked')) {
                            modal.find('.modal-body #deductionThresholdFields').show();
                        } else {
                            modal.find('.modal-body #deductionThresholdFields').hide();
                        }
                    });

                }
            } else {
                console.log(saturation_deductions);
            }
        });
        
        // Edit Tax Deduction Modal
        $('#edit_tax_deduction_modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var entry = button.data('entry'); // Extract info from data-* attributes
            var name = button.data('name');

            // Update modal content with the entry and name
            var modal = $(this);
            modal.find('.modal-body #entry_input').val(entry);
            modal.find('.modal-body #name_input').val(name);

            // Find option ID based on the entry name
            var tax_deductions = @json($tax_deductions); // Convert PHP array to JavaScript

            // Check if tax_deductions is an object
            if (typeof tax_deductions === 'object' && tax_deductions !== null) {
                var matchedEntry = tax_deductions[name]; // Access property by name

                if (matchedEntry) {
                    // Update the inputs
                    modal.find('.modal-body #taxDeductionName').val(matchedEntry.name);
                    modal.find('.modal-body #taxDeductionAmountType').val(matchedEntry.amount_type);
                    modal.find('.modal-body #taxDeductionAmount').val(matchedEntry.amount);

                    // Properly toggle the switch
                    if (matchedEntry.threshold) {
                        modal.find('.modal-body #taxDeductionThresholdSwitch').prop('checked', true);
                        modal.find('.modal-body #taxDeductionThresholdFields').show();
                    } else {
                        modal.find('.modal-body #taxDeductionThresholdSwitch').prop('checked', false);
                        modal.find('.modal-body #taxDeductionThresholdFields').hide();
                    }

                    // Set the min and max salary fields
                    modal.find('.modal-body #minSalary').val(matchedEntry.min_salary);
                    modal.find('.modal-body #maxSalary').val(matchedEntry.max_salary);

                    // Update amount symbol on change
                    var editAmountTypeSelect = modal.find('.modal-body #taxDeductionAmountType');
                    var editAmountSymbol = modal.find('.modal-body #taxDeductionAmountSymbol');
                    editAmountTypeSelect.off('change').on('change', function() {
                        if (editAmountTypeSelect.val() === 'percentage') {
                            editAmountSymbol.text('%');
                        } else {
                            editAmountSymbol.text(currencyCode);
                        }
                    });

                    // Toggle threshold fields
                    var editThresholdSwitch = modal.find('.modal-body #taxDeductionThresholdSwitch');
                    editThresholdSwitch.off('change').on('change', function() {
                        if (editThresholdSwitch.prop('checked')) {
                            modal.find('.modal-body #taxDeductionThresholdFields').show();
                        } else {
                            modal.find('.modal-body #taxDeductionThresholdFields').hide();
                        }
                    });

                }
            } else {
                console.log(tax_deductions);
            }
        });
        
        // Edit Tax Relief Modal
        $('#edit_tax_relief_modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var entry = button.data('entry'); // Extract info from data-* attributes
            var name = button.data('name');

            // Update modal content with the entry and name
            var modal = $(this);
            modal.find('.modal-body #entry_input').val(entry);
            modal.find('.modal-body #name_input').val(name);

            // Find option ID based on the entry name
            var tax_reliefs = @json($tax_reliefs); // Convert PHP array to JavaScript

            // Check if tax_reliefs is an object
            if (typeof tax_reliefs === 'object' && tax_reliefs !== null) {
                var matchedEntry = tax_reliefs[name]; // Access property by name

                if (matchedEntry) {
                    // Update the inputs
                    modal.find('.modal-body #taxReliefName').val(matchedEntry.name);
                    modal.find('.modal-body #taxReliefAmountType').val(matchedEntry.amount_type);
                    modal.find('.modal-body #taxReliefAmount').val(matchedEntry.amount);

                    // Properly toggle the switch
                    if (matchedEntry.threshold) {
                        modal.find('.modal-body #taxReliefThresholdSwitch').prop('checked', true);
                        modal.find('.modal-body #taxReliefThresholdFields').show();
                    } else {
                        modal.find('.modal-body #taxReliefThresholdSwitch').prop('checked', false);
                        modal.find('.modal-body #taxReliefThresholdFields').hide();
                    }

                    // Set the min and max salary fields
                    modal.find('.modal-body #minSalary').val(matchedEntry.min_salary);
                    modal.find('.modal-body #maxSalary').val(matchedEntry.max_salary);

                    // Update amount symbol on change
                    var editAmountTypeSelect = modal.find('.modal-body #taxReliefAmountType');
                    var editAmountSymbol = modal.find('.modal-body #taxReliefAmountSymbol');
                    editAmountTypeSelect.off('change').on('change', function() {
                        if (editAmountTypeSelect.val() === 'percentage') {
                            editAmountSymbol.text('%');
                        } else {
                            editAmountSymbol.text(currencyCode);
                        }
                    });

                    // Toggle threshold fields
                    var editThresholdSwitch = modal.find('.modal-body #taxReliefThresholdSwitch');
                    editThresholdSwitch.off('change').on('change', function() {
                        if (editThresholdSwitch.prop('checked')) {
                            modal.find('.modal-body #taxReliefThresholdFields').show();
                        } else {
                            modal.find('.modal-body #taxReliefThresholdFields').hide();
                        }
                    });

                }
            } else {
                console.log(tax_reliefs);
            }
        });
        
        // Edit Other Payments Modal
        $('#edit_other_payment_modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var entry = button.data('entry'); // Extract info from data-* attributes
            var name = button.data('name');

            // Update modal content with the entry and name
            var modal = $(this);
            modal.find('.modal-body #entry_input').val(entry);
            modal.find('.modal-body #name_input').val(name);

            // Find option ID based on the entry name
            var other_payments = @json($other_payments); // Convert PHP array to JavaScript

            // Check if other_payments is an object
            if (typeof other_payments === 'object' && other_payments !== null) {
                var matchedEntry = other_payments[name]; // Access property by name

                if (matchedEntry) {
                    // Update the inputs
                    modal.find('.modal-body #otherPaymentName').val(matchedEntry.name);
                    modal.find('.modal-body #otherPaymentAmountType').val(matchedEntry.amount_type);
                    modal.find('.modal-body #otherPaymentAmount').val(matchedEntry.amount);

                    // Properly toggle the switch
                    if (matchedEntry.threshold) {
                        modal.find('.modal-body #otherPaymentThresholdSwitch').prop('checked', true);
                        modal.find('.modal-body #otherPaymentThresholdFields').show();
                    } else {
                        modal.find('.modal-body #otherPaymentThresholdSwitch').prop('checked', false);
                        modal.find('.modal-body #otherPaymentThresholdFields').hide();
                    }

                    // Set the min and max salary fields
                    modal.find('.modal-body #minSalary').val(matchedEntry.min_salary);
                    modal.find('.modal-body #maxSalary').val(matchedEntry.max_salary);

                    // Update amount symbol on change
                    var editAmountTypeSelect = modal.find('.modal-body #otherPaymentAmountType');
                    var editAmountSymbol = modal.find('.modal-body #otherPaymentAmountSymbol');
                    editAmountTypeSelect.off('change').on('change', function() {
                        if (editAmountTypeSelect.val() === 'percentage') {
                            editAmountSymbol.text('%');
                        } else {
                            editAmountSymbol.text(currencyCode);
                        }
                    });

                    // Toggle threshold fields
                    var editThresholdSwitch = modal.find('.modal-body #otherPaymentThresholdSwitch');
                    editThresholdSwitch.off('change').on('change', function() {
                        if (editThresholdSwitch.prop('checked')) {
                            modal.find('.modal-body #otherPaymentThresholdFields').show();
                        } else {
                            modal.find('.modal-body #otherPaymentThresholdFields').hide();
                        }
                    });

                }
            } else {
                console.log(other_payments);
            }
        });
        
        // Edit Overtime Modal
        $('#edit_overtime_modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var entry = button.data('entry'); // Extract info from data-* attributes
            var name = button.data('name');

            // Update modal content with the entry and name
            var modal = $(this);
            modal.find('.modal-body #entry_input').val(entry);
            modal.find('.modal-body #name_input').val(name);

            // Find option ID based on the entry name
            var overtimes = @json($overtimes); // Convert PHP array to JavaScript

            // Check if overtimes is an object
            if (typeof overtimes === 'object' && overtimes !== null) {
                var matchedEntry = overtimes[name]; // Access property by name

                if (matchedEntry) {
                    // Update the inputs
                    modal.find('.modal-body #overtimeName').val(matchedEntry.name);
                    modal.find('.modal-body #overtimeNumberOfDays').val(matchedEntry.no_of_days);
                    modal.find('.modal-body #overtimeHours').val(matchedEntry.hours);
                    modal.find('.modal-body #overtimeRate').val(matchedEntry.rate);

                    // Properly toggle the switch
                    if (matchedEntry.threshold) {
                        modal.find('.modal-body #overtimeThresholdSwitch').prop('checked', true);
                        modal.find('.modal-body #overtimeThresholdFields').show();
                    } else {
                        modal.find('.modal-body #overtimeThresholdSwitch').prop('checked', false);
                        modal.find('.modal-body #overtimeThresholdFields').hide();
                    }

                    // Set the min and max salary fields
                    modal.find('.modal-body #minSalary').val(matchedEntry.min_salary);
                    modal.find('.modal-body #maxSalary').val(matchedEntry.max_salary);

                    // Toggle threshold fields
                    var editThresholdSwitch = modal.find('.modal-body #overtimeThresholdSwitch');
                    editThresholdSwitch.off('change').on('change', function() {
                        if (editThresholdSwitch.prop('checked')) {
                            modal.find('.modal-body #overtimeThresholdFields').show();
                        } else {
                            modal.find('.modal-body #overtimeThresholdFields').hide();
                        }
                    });

                }
            } else {
                console.log(overtimes);
            }
        });
        
    });
</script>
@endpush