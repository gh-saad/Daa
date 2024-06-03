{{ Form::model($taxdeduction, ['route' => ['taxdeduction.update', $taxdeduction->id], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}
                {{ Form::text('title', null, ['class' => 'form-control ', 'required' => 'required', 'placeholder' => 'Enter Title']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('salary_amount', __('Salary Amount'), ['class' => 'form-label']) }}
                <button id="net_pay_before_taxes" type="button" class="btn btn-sm btn-primary ml-3">{{ __('All') }}</button>
                {{ Form::number('salary_amount', null, ['class' => 'form-control ', 'required' => 'required', 'placeholder' => 'Enter Salary Amount', 'min' => '0', 'max' => $employee->get_net_pay_before_taxes(), 'step' => 'any']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('tax_deduction_value_type', __('Type'), ['class' => 'form-label']) }}
                {{ Form::select('tax_deduction_value_type', $taxdeduc, null, ['class' => 'form-control amount_type ', 'required' => 'required', 'placeholder' => 'Select Type']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('tax_deduction_value', __('Tax Value'), ['class' => 'form-label']) }}
                {{ Form::number('tax_deduction_value', null, ['class' => 'form-control ', 'required' => 'required', 'placeholder' => 'Enter Amount', 'min' => '0', 'step' => 'any']) }}
            </div>
        </div>
        <hr>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('salary_difference', __('Difference Amount'), ['class' => 'form-label']) }}
                {{ Form::number('salary_difference', 0, ['class' => 'form-control ', 'readonly' => 'readonly', 'min' => '0', 'step' => 'any']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('tax_deduction_calculated', __('Tax Calculated Amount'), ['class' => 'form-label']) }}
                {{ Form::number('tax_deduction_calculated', 0, ['class' => 'form-control ', 'readonly' => 'readonly', 'min' => '0', 'step' => 'any']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Save Changes'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
