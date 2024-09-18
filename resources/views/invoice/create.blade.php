@extends('layouts.main')

@section('page-title')
    {{ __('Invoice Create') }}
@endsection

@section('page-breadcrumb')
    {{__('Invoice')}},
    {{ __('Invoice Create') }}
@endsection

@push('css')
    <!-- Add any custom CSS here if needed -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .select2-selection__rendered {
            line-height: 36px !important;
        }
        .select2-container .select2-selection--single {
            height: 40px !important;
        }
        .select2-selection__arrow {
            height: 39px !important;
        }
        .required-text {
            color: red;
            font-size: smaller;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        {{ Form::open(['url' => 'invoice', 'class' => 'w-100', 'enctype' => 'multipart/form-data']) }}
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <div class="col-12">
            <div class="row mb-0">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header" style="padding-bottom: 10px !important;">
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="d-inline-block font-weight-400">{{ __('Customer') }}</h5>
                                    <p class="text-muted">select a customer from the list provided below.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    {{ Form::label('customer_id', __('Select Customer'), ['class' => 'form-label']) }}
                                    {{ Form::select(
                                        'customer_id', 
                                        $customers_array, 
                                        $customerId, 
                                        [
                                            'class' => 'form-control select2', 
                                            'id' => 'customer', 
                                            'data-url' => route('get.customer.details', ':id'), 
                                            'required' => 'required', 
                                            'placeholder' => 'No Customer Selected'
                                        ]
                                    ) }}
                                    @if (empty($customers_array))
                                        <div class="text-xs">
                                            {{ __('Please create Customer first.') }}
                                            <a @if (module_is_active('Account')) href="{{ route('customers.index') }}"  @else href="{{ route('users.index') }}" @endif><b>{{ __('Create Customer') }}</b></a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div id="customer-details" style="display: none;">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                        <p class="mt-1 mb-0" style="font-weight: 600;">{{ __('Customer Information') }}</p>
                                        <p id="customer-information"></p>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                        <p class="mt-1 mb-0" style="font-weight: 600;">{{ __('Customer Billing Details') }}</p>
                                        <p id="customer-billing-details"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mt-1 mb-0">{{ __('General Information') }}</h5>
                            <p class="text-muted">add relevant information about this invoice.</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if (module_is_active('Account') && module_is_active('Taskly'))
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="d-flex radio-check">
                                                <div class="form-check form-check-inline form-group col-md-3">
                                                    <input type="radio" id="product" value="product"
                                                        name="invoice_type_radio" class="form-check-input code"
                                                        checked="checked">
                                                    <label class="custom-control-label "
                                                        for="product">{{ __('Item Wise') }}</label>
                                                </div>
                                                <div class="form-check form-check-inline form-group col-lg-3 col-md-6">
                                                    <input type="radio" id="project1" value="project"
                                                        name="invoice_type_radio" class="form-check-input code">
                                                    <label class="custom-control-label"
                                                        for="project1">{{ __('Project Wise') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('issue_date', __('Issue Date'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            {{ Form::date('issue_date',date('Y-m-d'), ['class' => 'form-control ', 'required' => 'required', 'placeholder' => 'Select Issue Date']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('due_date', __('Due Date'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            {{ Form::date('due_date',date('Y-m-d'), ['class' => 'form-control ', 'required' => 'required', 'placeholder' => 'Select Due Date']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('invoice_number', __('Invoice Number'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            <input type="text" class="form-control" value="{{ $invoice_number }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group invoice_div">
                                        @if (module_is_active('Account'))
                                            {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}
                                            {{ Form::select('category_id', $category, null, ['class' => 'form-control ', 'required' => 'required']) }}
                                            @if (empty($category->count()))
                                                <div class=" text-xs">
                                                    {{ __('Please add constant category. ') }}<a
                                                        href="{{ route('category.index') }}"><b>{{ __('Add Category') }}</b></a>
                                                </div>
                                            @endif
                                        @elseif (module_is_active('Taskly'))
                                            {{ Form::label('project', __('Project'), ['class' => 'form-label']) }}
                                            {{ Form::select('project', $projects, null, ['class' => 'form-control ', 'required' => 'required']) }}
                                        @endif
                                    </div>
                                </div>
                                @if (module_is_active('Taskly'))
                                    <div
                                        class="col-md-6 tax_project_div {{ module_is_active('Account') ? 'd-none' : '' }}">
                                        <div class="form-group">
                                            {{ Form::label('tax_project', __('Tax'), ['class' => 'form-label']) }}
                                            {{ Form::select('tax_project[]', $taxs, null, ['class' => 'form-control get_tax multi-select choices', 'data-toggle' => 'select2', 'multiple' => 'multiple', 'id' => 'tax_project', 'data-placeholder' => 'Select Tax']) }}
                                        </div>
                                    </div>
                                @endif
                                @if(module_is_active('CustomField') && !$customFields->isEmpty())
                                    <div class="col-md-12">
                                        <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                                            @include('customfield::formBuilder')
                                        </div>
                                    </div>
                                @endif
                                @stack('add_invoices_agent_filed')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="row mb-0">
                <div class="col-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="d-inline-block font-weight-400">{{ __('Invoice Items') }}</h5>
                                    <p class="text-muted">add items here for this invoice</p>
                                </div>
                                <div class="col-6 text-end">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                        <i class="ti ti-plus"></i> {{ __('Add Item') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th width="15%">{{__('Action')}}</th>
                                            <th width="25%">{{__('Item')}}</th>
                                            <th width="15%">{{__('Price')}}</th>
                                            <th width="15%">{{__('Discount')}}</th>
                                            <th width="15%">{{__('Tax')}}</th>
                                            <th width="15%">{{__('Net Amount')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="items">
                                        <!-- items that are added by the user will be appended here for display and calulations -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-none" id="item-inputs"><!-- this container and all its content must remain hidden, items that are added by the user will be added here as hidden inputs to be sent along the parent form proper --></div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mt-1 mb-0">{{ __('Calculated Price') }}</h5>
                            <p class="text-muted">calculations will be displayed here</p>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1" style="font-weight: 600;">Subtotal:</div>
                                <div id="sub-total" style="font-weight: 600;">0 {{ company_setting('defult_currancy') }}</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1" style="font-weight: 600;">Total Discount:</div>
                                <div id="total-discount" style="font-weight: 600;">0 {{ company_setting('defult_currancy') }}</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1" style="font-weight: 600;">Total Tax Calculated:</div>
                                <div id="total-tax-calculated" style="font-weight: 600;">0 {{ company_setting('defult_currancy') }}</div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1" style="font-weight: 600;">Total Amount:</div>
                                <div id="net-amount" style="font-weight: 600;">0 {{ company_setting('defult_currancy') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="modal-footer">
                    <input type="button" value="{{ __('Cancel') }}" onclick="location.href = '{{route("purchase.index")}}';" class="btn btn-secondary">
                    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary mx-3">
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
    
    <!-- Add Items Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="addItemForm" action="" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addItemModalLabel">{{ __('Add Item') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="item_type" class="form-label">{{ __('Select Item Type') }} <span style='color: red; font-size: smaller;'>(required)</span></label>
                                    {{ Form::select('item_type', ['product' => 'Products', 'service' => 'Services'], 'product', array('id' => 'typeSelect', 'class' => 'form-control')) }}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="item_id" class="form-label">{{ __('Select Item') }} <span style='color: red; font-size: smaller;'>(required)</span></label>
                                    {{ Form::select('item_id', $company_product_array, null, array('id' => 'itemSelect', 'class' => 'form-control select2', 'placeholder' => 'No Item Selected', 'required'=>'required')) }}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('currency', __('Currency'), ['class'=>'form-label']) }}
                                    <select class="form-control select2" data-trigger name="currency" id="currency" data-default-currency-rate="{{ get_default_currency_rate() }}" placeholder="No Item Selected">
                                        @foreach (currency() as $c)
                                            <option value="{{ $c->code }}" data-rate="{{ $c->rate }}" {{ company_setting('defult_currancy') == $c->code ? 'selected' : '' }}>
                                                {{ $c->name }} - {{ $c->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="item_price">{{ __('Item Price') }} <span style='color: red; font-size: smaller;'>(required)</span></label>
                                    <input type="number" name="item_price" id="itemPrice" class="form-control mt-2" placeholder="{{ __('Original Price') }}" required>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="item_discount">{{ __('Item Discount') }}</label>
                                    <input type="number" name="item_discount" id="itemDiscount" class="form-control mt-2" placeholder="{{ __('Discount Amount') }}" value="0">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="item_tax">{{ __('Item Tax') }}</label>
                                    <div class="input-group mt-2">
                                        <input type="number" name="item_tax" id="itemTax" class="form-control" placeholder="{{ __('Tax Amount') }}" value="0">
                                        <span class="input-group-text bg-transparent">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="item_desc">{{ __('Item Description') }}</label>
                                    <textarea name="item_desc" id="itemDesc" class="form-control mt-2"  rows="4" placeholder="{{ __('Description') }}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" value="{{ __('Cancel') }}" data-bs-dismiss="modal" class="btn btn-secondary">
                        <input type="submit" value="{{ __('Add') }}" class="btn btn-primary mx-3">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <!-- customer details script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const customerSelect = $('#customer');
            const customerDetailsDiv = document.getElementById('customer-details');
            const customerInformation = document.getElementById('customer-information');
            const customerBillingDetails = document.getElementById('customer-billing-details');

            // Initialize Select2
            customerSelect.select2({
                width: '100%' // Adjust as needed
            });

            customerSelect.on('change', function () {
                const customerId = this.value;
                const url = this.getAttribute('data-url').replace(':id', customerId);

                if (customerId) {
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                customerDetailsDiv.style.display = 'none';
                                alert(data.error);
                            } else {
                                customerDetailsDiv.style.display = 'block';
                                customerInformation.innerHTML = `ID: ${data.customer_information.customer_id}<br>Name: ${data.customer_information.name}<br>Email: ${data.customer_information.email}<br>Contact: ${data.customer_information.contact}<br>Tax Number: ${data.customer_information.tax_number}`;
                                customerBillingDetails.innerHTML = `Name: ${data.customer_billing_details.billing_name}<br>Country: ${data.customer_billing_details.billing_country}<br>State: ${data.customer_billing_details.billing_state}<br>City: ${data.customer_billing_details.billing_city}<br>Phone: ${data.customer_billing_details.billing_phone}<br>ZIP: ${data.customer_billing_details.billing_zip}<br>Address: ${data.customer_billing_details.billing_address}`;
                            }
                        })
                        .catch(error => {
                            customerDetailsDiv.style.display = 'none';
                            console.error('Error fetching customer details:', error);
                        });
                } else {
                    customerDetailsDiv.style.display = 'none';
                }
            });
        });
    </script>

    <!-- add item scripts -->
    <script>
        $(document).ready(function() {
            $('#addItemModal').on('shown.bs.modal', function () {

                // Initialize select2 for item and currency selects
                $('#itemSelect, #currency').select2({
                    dropdownParent: $('#addItemModal'),
                    width: '100%'
                });

            });

            // Add item to table
            $('#addItemForm').on('submit', function(e) {
                e.preventDefault();

                // Get item details
                const itemId = $('#itemSelect').val();
                const itemType = $('#typeSelect').val();
                const itemName = $('#itemSelect option:selected').text();
                const itemPrice = parseFloat($('#itemPrice').val());
                const itemDiscount = parseFloat($('#itemDiscount').val());
                const itemTax = parseFloat($('#itemTax').val());
                const itemDesc = $('#itemDesc').val();

                // Get selected currency and conversion rate
                const selectedCurrency = $('#currency option:selected').val();
                const conversionRate = parseFloat($('#currency option:selected').data('rate'));
                const defaultRate = $('#currency').data('default-currency-rate');

                // Convert item price and discount to default currency
                const convertedPrice = ( itemPrice / conversionRate ) * defaultRate;
                const convertedDiscount = ( itemDiscount / conversionRate ) * defaultRate;
                
                // Calculate net amount
                const discountedPrice = convertedPrice - convertedDiscount;

                const taxAmount = (discountedPrice * itemTax) / 100;
                const netAmount = discountedPrice + taxAmount;

                // Append item details to table
                $('#items').append(`
                    <tr data-item-id="${itemId}">
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-item"><i class="ti ti-trash"></i></button>
                        </td>
                        <td>${itemName}</td>
                        <td>${convertedPrice.toFixed(2)} {{ company_setting('defult_currancy') }}</td>
                        <td>${convertedDiscount.toFixed(2)} {{ company_setting('defult_currancy') }}</td>
                        <td>${itemTax.toFixed(2)}%</td>
                        <td class="net-amount">${netAmount.toFixed(2)} {{ company_setting('defult_currancy') }}</td>
                    </tr>
                `);

                // Append hidden inputs for form submission
                $('#item-inputs').append(`
                    <div class="item-input-group" data-item-id="${itemId}">
                        <input type="hidden" name="items[]" value="${itemId}">
                        <input type="hidden" name="item_types[]" value="${itemType}">
                        <input type="hidden" name="item_prices[]" value="${convertedPrice.toFixed(2)}">
                        <input type="hidden" name="item_discounts[]" value="${convertedDiscount.toFixed(2)}">
                        <input type="hidden" name="item_taxes[]" value="${itemTax}">
                        <input type="hidden" name="item_desc[]" value="${itemDesc}">
                        <input type="hidden" name="item_net_amounts[]" value="${netAmount.toFixed(2)}">
                    </div>
                `);

                // Recalculate totals
                recalculateTotals();

                // Reset form fields
                $('#itemSelect').val(null).trigger('change');
                $('#itemPrice').val(null);
                $('#itemDiscount').val('0');
                $('#itemTax').val('0');
                $('#itemDesc').val(null);

                // Hide modal
                $('#addItemModal').modal('hide');
            });

            // Remove item from table and corresponding hidden inputs
            $('#items').on('click', '.remove-item', function() {
                const itemId = $(this).closest('tr').attr('data-item-id');
                $(this).closest('tr').remove(); // Remove the row from the table
                $(`#item-inputs .item-input-group[data-item-id="${itemId}"]`).remove(); // Remove corresponding hidden inputs
                recalculateTotals(); // Recalculate totals after removing an item
            });

            // Recalculate totals function
            function recalculateTotals() {
                let subtotal = 0;
                let totalDiscount = 0;
                let totalTax = 0;
                let netAmount = 0;

                $('#items tr').each(function() {
                    const price = parseFloat($(this).find('td:nth-child(3)').text());
                    const discount = parseFloat($(this).find('td:nth-child(4)').text());
                    const tax = parseFloat($(this).find('td:nth-child(5)').text());
                    const net = parseFloat($(this).find('.net-amount').text());

                    const calculatedTax = (price - discount) * tax / 100;

                    subtotal += price;
                    totalDiscount += discount;
                    totalTax += calculatedTax;
                    netAmount += net;
                });

                $('#sub-total').text(subtotal.toFixed(2) + ' ' + '{{ company_setting("defult_currancy") }}');
                $('#total-discount').text(totalDiscount.toFixed(2) + ' ' + '{{ company_setting("defult_currancy") }}');
                $('#total-tax-calculated').text(totalTax.toFixed(2) + ' ' + '{{ company_setting("defult_currancy") }}');
                $('#net-amount').text(netAmount.toFixed(2) + ' ' + '{{ company_setting("defult_currancy") }}');
            }
        });
    </script>
    
    <!-- select item in modal -->
    <script>
        $(document).ready(function() {
            // Define your product and service arrays
            const companyProductArray = @json($company_product_array);
            const companyServiceArray = @json($company_service_array);

            // Function to update itemSelect based on itemType selection
            function updateItemSelect(itemType) {
                const itemSelect = $('#itemSelect');
                let options = '';

                // No item selected
                options += `<option value="${null}">No Item Selected</option>`;

                // Switch between product and service arrays
                const itemsArray = (itemType === 'service') ? companyServiceArray : companyProductArray;

                // Populate itemSelect with the corresponding array
                $.each(itemsArray, function(key, value) {
                    options += `<option value="${key}">${value}</option>`;
                });

                itemSelect.html(options).trigger('change');
            }

            // Initialize select2 for item and currency selects
            $('#itemSelect, #currency').select2({
                width: '100%'
            });

            // Handle typeSelect change event
            $('#typeSelect').on('change', function() {
                const selectedType = $(this).val();
                updateItemSelect(selectedType);
            });

        });
    </script>
@endpush
