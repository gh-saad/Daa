{{ Form::open(['route' => 'create-service-store', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="text-end">
        @if (module_is_active('AIAssistant'))
            @include('aiassistant::ai.generate_ai_btn', [
                'template_module' => 'product',
                'module' => 'ProductService',
            ])
        @endif
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                <div class="form-icon-user">
                    {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('sku', __('SKU'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                <div class="form-icon-user">
                    {{ Form::text('sku', null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('sale_price', __('Sale price'), ['class' => 'form-label']) }}<span
                    class="text-danger">*</span>
                <div class="form-icon-user">
                    {{ Form::text('sale_price', null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('purchase_price', __('Purchase Price'), ['class' => 'form-label']) }}<span
                    class="text-danger">*</span>
                <div class="form-icon-user">
                    {{ Form::number('purchase_price', null, ['class' => 'form-control', 'required' => 'required', 'step' => '0.01']) }}
                </div>
            </div>
        </div>
        

        <div class="form-group  col-md-6">
            {{ Form::label('category_id', __('Type'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
            {{ Form::select('category_id', $category, null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>

        <div class="form-group  col-md-6">
            {{ Form::label('tax_id', __('Tax'), ['class' => 'form-label']) }}
            {{ Form::select('tax_id[]', $tax, null, ['class' => 'form-control choices', 'id' => 'choices-multiple1', 'multiple' => '']) }}
        </div>

        <div class="form-group  col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '2']) !!}
        </div>

        @stack('add_column_in_productservice')


        <div class="form-group col-md-6">
            {{ Form::label('unit_id', __('Unit'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
            {{ Form::select('unit_id', $unit, null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        

        <div class="form-group col-md-6 quantity">
            {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
            {{ Form::text('quantity', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('sale_chartaccount_id', __('Sale Chart of Account'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
            {{ Form::select('sale_chartaccount_id', ['' => 'Select a Sale Chart of Account'] + $chart_of_account->toArray(), null, ['class' => 'form-control select2', 'required' => 'required']) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('expense_chartaccount_id', __('Expense Chart Of Account'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
            {{ Form::select('expense_chartaccount_id', ['' => 'Select a Expense Chart of Account'] + $chart_of_account->toArray(), null, ['class' => 'form-control select2', 'required' => 'required']) }}
        </div>

        @if (module_is_active('CustomField') && !$customFields->isEmpty())
            <div class="col-md-12">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('customfield::formBuilder')
                </div>
            </div>
        @endif
        
    </div>

    
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}

<script>
    //hide & show quantity
    
    $(document).on('click', '.type', function() {
        var type = $(this).val();
        if (type == 'product') {
            $('.quantity').removeClass('d-none')
            $('.quantity').addClass('d-block');
        } else {
            $('.quantity').addClass('d-none')
            $('.quantity').removeClass('d-block');
        }
    });
</script>
