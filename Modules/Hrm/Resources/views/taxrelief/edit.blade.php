{{ Form::model($taxrelief, ['route' => ['taxrelief.update', $taxrelief->id], 'method' => 'PUT']) }}
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
                {{ Form::label('tax_relief_value_type', __('Type'), ['class' => 'form-label']) }}
                {{ Form::select('tax_relief_value_type', $tax_relief_type, null, ['class' => 'form-control amount_type ', 'required' => 'required', 'placeholder' => 'Select Type']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('tax_relief_value', __('Tax Relief Value'), ['class' => 'form-label']) }}
                {{ Form::number('tax_relief_value', null, ['class' => 'form-control ', 'required' => 'required', 'placeholder' => 'Enter Tax Relief Value', 'min' => '0', 'step' => 'any']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Save Changes'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}
