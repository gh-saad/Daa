@extends('layouts.main')

@section('page-title')
    {{ __('Bill Create') }}
@endsection

@section('page-breadcrumb')
    {{__('Bill')}},
    {{ __('Bill Create') }}
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
        {{ Form::open(['url' => 'bill', 'class' => 'w-100', 'enctype' => 'multipart/form-data']) }}
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        @if (module_is_active('Account'))
            <input type="hidden" name="bill_type" id="bill_type" value="product">
        @elseif (module_is_active('Taskly'))
            <input type="hidden" name="bill_type" id="bill_type" value="project">
        @endif
        <div class="col-12">
            <div class="row mb-0">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header" style="padding-bottom: 10px !important;">
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="d-inline-block font-weight-400">{{ __('Vendor') }}</h5>
                                    <p class="text-muted">select a pre-existing vendor, or quickly add a new vendor</p>
                                </div>
                                <div class="col-6 text-end">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVendorModal">
                                        <i class="ti ti-plus"></i> {{ __('Add Vendor') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    {{ Form::label('vendor_id', __('Select Vendor'), ['class' => 'form-label']) }}
                                    {{ Form::select(
                                        'vendor_id', 
                                        $vendors_array, 
                                        $vendorId, 
                                        [
                                            'class' => 'form-control select2', 
                                            'id' => 'vendor', 
                                            'data-url' => route('bill.vendor.details', ':id'), 
                                            'required' => 'required', 
                                            'placeholder' => 'No Vendor Selected'
                                        ]
                                    ) }}
                                    @if (empty($vendors_array))
                                        <div class="text-xs">
                                            {{ __('Please create Vendor first.') }}
                                            <a @if (module_is_active('Account')) href="{{ route('vendors.index') }}"  @else href="{{ route('users.index') }}" @endif><b>{{ __('Create Vendor') }}</b></a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div id="vendor-details" style="display: none;">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                        <p class="mt-1 mb-0" style="font-weight: 600;">{{ __('Vendor Information') }}</p>
                                        <p id="vendor-information"></p>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                        <p class="mt-1 mb-0" style="font-weight: 600;">{{ __('Vendor Billing Details') }}</p>
                                        <p id="vendor-billing-details"></p>
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
                            <p class="text-muted">add relevant information about this purchase.</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if (module_is_active('Account') && module_is_active('Taskly'))
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="d-flex radio-check">
                                                <div class="form-check form-check-inline form-group col-md-3">
                                                    <input type="radio" id="product" value="product" name="bill_type_radio" class="form-check-input code" checked="checked">
                                                    <label class="custom-control-label" for="product">{{ __('Item Wise') }}</label>
                                                </div>
                                                <div class="form-check form-check-inline form-group col-lg-3 col-md-6">
                                                    <input type="radio" id="project1" value="project" name="bill_type_radio" class="form-check-input code">
                                                    <label class="custom-control-label" for="project1">{{ __('Project Wise') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('bill_date', __('Bill Date'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            {{ Form::date('bill_date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Select Issue Date']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('due_date', __('Due Date'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            {{ Form::date('due_date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Select Due Date']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('bill_number', __('Bill Number'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            <input type="text" class="form-control" value="{{ $bill_number }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group bill_div">
                                        @if (module_is_active('Account'))
                                            {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}
                                            {{ Form::select('category_id', $category, null, ['class' => 'form-control', 'required' => 'required','placeholder'=>'Select Category']) }}
                                            @if (empty($category->count()))
                                                <div class=" text-xs">
                                                    {{ __('Please add constant category.') }}<a href="{{ route('category.index') }}"><b>{{ __('Add Category') }}</b></a>
                                                </div>
                                            @endif
                                        @elseif (module_is_active('Taskly'))
                                            {{ Form::label('project', __('Project'), ['class' => 'form-label']) }}
                                            {{ Form::select('project', $projects, null, ['class' => 'form-control', 'required' => 'required']) }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('order_number', __('Order Number'), ['class' => 'form-label']) }}
                                        <div class="form-icon-user">
                                            {{ Form::number('order_number', null, ['class' => 'form-control']) }}
                                        </div>
                                    </div>
                                </div>
                                @if (module_is_active('Taskly'))
                                    <div class="col-md-6 tax_project_div {{ module_is_active('Account') ? 'd-none' : '' }}">
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
                                    <h5 class="d-inline-block font-weight-400">{{ __('Purchase Items') }}</h5>
                                    <p class="text-muted">add items here that are to be purchased</p>
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
                                            <th width="15%">{{__('Bill Acc.')}}</th>
                                            <th width="15%">{{__('Price')}}</th>
                                            <th width="15%">{{__('Discount')}}</th>
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
                    <input type="button" value="{{ __('Cancel') }}" onclick="location.href = '{{route("bill.index")}}';" class="btn btn-secondary">
                    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary mx-3">
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>

    <!-- Add Quick Vendor Modal -->
    <div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="addVendorForm" action="{{ route('add.quick.vendor') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addVendorModalLabel">{{ __('Add Quick Vendor') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="vendor_name">{{ __('Vendor Name') }}</label>
                                    <input type="text" name="vendor_name" id="vendor_name" class="form-control mt-2" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="vendor_contact">{{ __('Vendor Contact') }}</label>
                                    <input type="number" name="vendor_contact" id="vendor_contact" class="form-control mt-2" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="country">{{ __('Country') }}</label>
                                    <input type="text" name="country" id="country" class="form-control mt-2" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="state">{{ __('State') }}</label>
                                    <input type="text" name="state" id="state" class="form-control mt-2" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="city">{{ __('City') }}</label>
                                    <input type="text" name="city" id="city" class="form-control mt-2" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="zip_code">{{ __('Zip Code') }}</label>
                                    <input type="text" name="zip_code" id="zip_code" class="form-control mt-2" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="address">{{ __('Address') }}</label>
                                <textarea name="address" id="address" class="form-control mt-2" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" value="{{ __('Cancel') }}" data-bs-dismiss="modal" class="btn btn-light">
                        <input type="submit" value="{{ __('Create') }}" class="btn btn-primary mx-3">
                    </div>
                </form>
            </div>
        </div>
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
                                    {{ Form::select('item_type', ['product' => 'Products', 'service' => 'Services'], 'product', ['id' => 'typeSelect', 'class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-6" id="itemSelectContainer">
                                <div class="form-group">
                                    <label for="item_id" class="form-label">{{ __('Select Item') }} <span style='color: red; font-size: smaller;'>(required)</span></label>
                                    {{ Form::select('item_id', $company_product_array, null, ['id' => 'itemSelect', 'class' => 'form-control select2', 'placeholder' => 'No Item Selected', 'required'=>'required']) }}
                                </div>
                            </div>
                            <div class="col-6" id="accountSelectContainer">
                                <div class="form-group">
                                    <label for="account_id" class="form-label">{{ __('Select Bill Account') }} <span style='color: red; font-size: smaller;'>(required)</span></label>
                                    {{ Form::select('account_id', $chart_accounts, null, ['id' => 'itemAccount', 'class' => 'form-control select2', 'placeholder' => 'No Account Selected', 'required'=>'required']) }}
                                </div>
                            </div>
                            <div class="col-4">
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
                                    <div class="input-group mt-2">
                                        <input type="number" name="item_discount" id="itemDiscount" class="form-control" placeholder="{{ __('Discount Amount') }}" value="0" style="width: 70%;">
                                        <div class="input-group-prepend" style="width: 30%;">
                                            <select class="form-select" id="itemDiscountType" aria-label="Discount Type">
                                                <option value="fixed">Fixed</option>
                                                <option value="percent">%</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="item_desc">{{ __('Item Description') }}</label>
                                    <textarea name="item_desc" id="itemDesc" class="form-control mt-2" rows="4" placeholder="{{ __('Description') }}"></textarea>
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
    
    <!-- vendor details script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const vendorSelect = $('#vendor');
            const vendorDetailsDiv = document.getElementById('vendor-details');
            const vendorInformation = document.getElementById('vendor-information');
            const vendorBillingDetails = document.getElementById('vendor-billing-details');

            // Initialize Select2
            vendorSelect.select2({
                width: '100%' // Adjust as needed
            });

            vendorSelect.on('change', function () {
                const vendorId = this.value;
                const url = this.getAttribute('data-url').replace(':id', vendorId);

                if (vendorId) {
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                vendorDetailsDiv.style.display = 'none';
                                alert(data.error);
                            } else {
                                vendorDetailsDiv.style.display = 'block';
                                vendorInformation.innerHTML = `ID: ${data.vendor_information.vendor_id}<br>Name: ${data.vendor_information.name}<br>Email: ${data.vendor_information.email}<br>Contact: ${data.vendor_information.contact}<br>Tax Number: ${data.vendor_information.tax_number}`;
                                vendorBillingDetails.innerHTML = `Name: ${data.vendor_billing_details.billing_name}<br>Country: ${data.vendor_billing_details.billing_country}<br>State: ${data.vendor_billing_details.billing_state}<br>City: ${data.vendor_billing_details.billing_city}<br>Phone: ${data.vendor_billing_details.billing_phone}<br>ZIP: ${data.vendor_billing_details.billing_zip}<br>Address: ${data.vendor_billing_details.billing_address}`;
                            }
                        })
                        .catch(error => {
                            vendorDetailsDiv.style.display = 'none';
                            console.error('Error fetching vendor details:', error);
                        });
                } else {
                    vendorDetailsDiv.style.display = 'none';
                }
            });
        });
    </script>

    <!-- add a new vendor script -->
    <script>
        $(document).ready(function() {
            $('#addVendorForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#addVendorModal').modal('hide'); // Hide the modal
                            toastr.success(response.success, 'Success');

                            // Add new vendor to Select2 and select it
                            const newVendor = new Option(response.vendor.name, response.vendor.id, true, true);
                            $('#vendor').append(newVendor).trigger('change');

                            // Trigger the change event to fetch and display vendor details
                            $('#vendor').trigger('change');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            var errors = xhr.responseJSON.error;
                            var errorMessage = '';
                            for (var key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    errorMessage += errors[key] + '\n';
                                }
                            }
                            alert(errorMessage); // Display error message
                        }
                    }
                });
            });
        });
    </script>

    <!-- add item scripts -->
    <script>
        $(document).ready(function() {
            $('#addItemModal').on('shown.bs.modal', function () {

                // Initialize select2 for item and currency selects
                $('#itemSelect, #currency, #itemAccount').select2({
                    dropdownParent: $('#addItemModal'),
                    width: '100%'
                });

            });

            // Add item to table
            $('#addItemForm').on('submit', function(e) {
                e.preventDefault();

                // Get item details
                const itemId = $('#itemSelect').val();
                const itemAccount = $('#itemAccount').val();
                const itemType = $('#typeSelect').val();
                const itemName = $('#itemSelect option:selected').text();
                const itemAccountName = $('#itemAccount option:selected').text();
                const itemPrice = parseFloat($('#itemPrice').val());
                const itemDiscount = parseFloat($('#itemDiscount').val());
                const itemDiscountType = $('#itemDiscountType').val();
                const itemDesc = $('#itemDesc').val();

                // Get selected currency and conversion rate
                const selectedCurrency = $('#currency option:selected').val();
                const conversionRate = parseFloat($('#currency option:selected').data('rate'));
                const defaultRate = $('#currency').data('default-currency-rate');

                // Convert item price and discount to default currency
                const convertedPrice = ( itemPrice / conversionRate ) * defaultRate;

                let convertedDiscount;
                if (itemDiscountType === 'percent') {
                    convertedDiscount = (itemPrice * itemDiscount) / 100; // Calculate percentage discount
                } else {
                    convertedDiscount = ( itemDiscount / conversionRate ) * defaultRate; // Fixed amount
                }

                // Calculate net amount
                const netAmount = convertedPrice - convertedDiscount;

                // Append item details to table
                $('#items').append(`
                    <tr data-item-id="${itemId}">
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-item"><i class="ti ti-trash"></i></button>
                        </td>
                        <td>${itemName}</td>
                        <td>${itemAccountName}</td>
                        <td>${convertedPrice.toFixed(2)} {{ company_setting('defult_currancy') }}</td>
                        <td>${convertedDiscount.toFixed(2)} {{ company_setting('defult_currancy') }}</td>
                        <td class="net-amount">${netAmount.toFixed(2)} {{ company_setting('defult_currancy') }}</td>
                    </tr>
                `);

                // Append hidden inputs for form submission
                $('#item-inputs').append(`
                    <div class="item-input-group" data-item-id="${itemId}">
                        <input type="hidden" name="items[]" value="${itemId}">
                        <input type="hidden" name="item_accounts[]" value="${itemAccount}">
                        <input type="hidden" name="item_types[]" value="${itemType}">
                        <input type="hidden" name="item_prices[]" value="${convertedPrice.toFixed(2)}">
                        <input type="hidden" name="item_discounts[]" value="${convertedDiscount.toFixed(2)}">
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
                $('#itemDiscountType').val('fixed');
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
                let netAmount = 0;

                $('#items tr').each(function() {
                    const price = parseFloat($(this).find('td:nth-child(4)').text());
                    const discount = parseFloat($(this).find('td:nth-child(5)').text());
                    const net = parseFloat($(this).find('.net-amount').text());

                    subtotal += price;
                    totalDiscount += discount;
                    netAmount += net;
                });

                $('#sub-total').text(subtotal.toFixed(2) + ' ' + '{{ company_setting("defult_currancy") }}');
                $('#total-discount').text(totalDiscount.toFixed(2) + ' ' + '{{ company_setting("defult_currancy") }}');
                $('#net-amount').text(netAmount.toFixed(2) + ' ' + '{{ company_setting("defult_currancy") }}');
            }
        });
    </script>

    <!-- select item in modal -->
    <script>
        $(document).ready(function() {
            // Define your product, service, and other arrays
            const companyProductArray = @json($company_product_array);
            const companyServiceArray = @json($company_service_array);

            // Function to update itemSelect based on itemType selection
            function updateItemSelect(itemType) {
                const itemSelect = $('#itemSelect');
                let options = '';

                // No item selected
                options += `<option value="${null}">No Item Selected</option>`;

                let itemsArray;

                // Switch between product, service, and other arrays
                if (itemType === 'service') {
                    itemsArray = companyServiceArray;
                } else {
                    itemsArray = companyProductArray;
                }

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
