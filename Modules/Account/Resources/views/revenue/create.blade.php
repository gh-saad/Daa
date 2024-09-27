{{ Form::open(array('url' => 'revenue','enctype' => 'multipart/form-data')) }}
<div class="modal-body">
    <div class="text-end">
        @if (module_is_active('AIAssistant'))
            @include('aiassistant::ai.generate_ai_btn',['template_module' => 'revenues','module'=>'Account'])
        @endif
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('type', __('Is Customer Included?'),['class'=>'form-label']) }}
            {{ Form::select('type', ['customer_included' => 'Customer Is Included', 'customer_not_included' => 'Customer Is Not Included'], 'customer_included', array('class' => 'form-control', 'id' => 'typeSelect')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('customer_id', __('Customer'),['class'=>'form-label']) }}
            {{ Form::select('customer_id', $customers,null, array('class' => 'form-control select2','required'=>'required','placeholder' => 'Select Customer', 'id' => 'customerSelect')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('chart_account_id', __('Revenue Account'),['class'=>'form-label']) }}
            {{ Form::select('chart_account_id', $revenue_chart_accounts,null, array('class' => 'form-control select2','required'=>'required','placeholder' => 'Select Revenue Account', 'id' => 'chartAccountSelect')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('account_id', __('Cash/Bank Account'),['class'=>'form-label']) }}
            {{ Form::select('account_id',$accounts,null, array('class' => 'form-control `','required'=>'required','placeholder' => 'Select Account')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('date', __('Date'),['class'=>'form-label']) }}
            <div class="form-icon-user">
                {{Form::date('date',null,array('class'=>'form-control ','required'=>'required','placeholder' => 'Select Date'))}}
            </div>
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}
            <div class="form-icon-user">
                {{ Form::number('amount',null, array('class' => 'form-control','required'=>'required','placeholder'=>'Enter Amount','step'=>'0.01','min'=>'0')) }}
            </div>
        </div>
        <div class="form-group col-md-3">
            {{Form::label('currency', __('Currency'), ['class'=>'form-label']) }}
            <select class="form-control select2" data-trigger name="currency" id="currency" data-default-currency-rate="{{ get_default_currency_rate() }}" placeholder="No Item Selected">
                @foreach (currency() as $c)
                    <option value="{{ $c->code }}" data-rate="{{ $c->rate }}" {{ company_setting('defult_currancy') == $c->code ? 'selected' : '' }}>
                        {{ $c->name }} - {{ $c->code }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('category_id', __('Category'),['class'=>'form-label']) }}
            {{ Form::select('category_id', $categories,null, array('class' => 'form-control `','required'=>'required','placeholder' => 'Select Category')) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('reference', __('Reference'),['class'=>'form-label']) }}
            <div class="form-icon-user">
                {{ Form::text('reference',null, array('class' => 'form-control','placeholder'=>'Enter Reference')) }}
            </div>
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {{ Form::textarea('description',null, array('class' => 'form-control','rows'=>3)) }}
        </div>
        <div class="form-group">
            {{ Form::label('add_receipt', __('Payment Receipt'), ['class' => 'form-label']) }}
            <div class="choose-files ">
                <label for="add_receipt">
                    <div class=" bg-primary "> <i class="ti ti-upload px-1"></i>{{ __('Choose file here') }}</div>
                    <input type="file" class="form-control file" name="add_receipt" id="add_receipt" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])" data-filename="add_receipt">
                </label>
                <img id="blah"  width="100" src="" />
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}


