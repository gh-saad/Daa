@extends('layouts.main')
@section('page-title')
    {{ __('Bill Create') }}
@endsection
@section('page-breadcrumb')
    {{ __('Bill Create') }}
@endsection

@push('css')
<!-- Add any custom CSS here if needed -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .select2-container .select2-selection--single {
        padding: 6px;
        font-size: 14px;
        height: 40px;
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
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="d-inline-block font-weight-400">{{ __('Vendor') }}</h5>
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
                                    @if ($vendors->isEmpty())
                                        <div class="text-xs">
                                            {{ __('Please create vendor/Client first.') }}
                                            <a @if (module_is_active('Account')) href="{{ route('vendors.index') }}"  @else href="{{ route('users.index') }}" @endif><b>{{ __('Create vendor/Client') }}</b></a>
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
            <div id="loader" class="card card-flush">
                <div class="card-body">
                    <div class="row">
                        <img class="loader" src="{{ asset('public/images/loader.gif') }}" alt="">
                    </div>
                </div>
            </div>
            <div class="col-12 section_div">

            </div>
            <div class="modal-footer">
                <input type="button" value="{{ __('Cancel') }}" class="btn btn-light">
                <input type="submit" value="{{ __('Create') }}" class="btn btn-primary mx-3">
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
@endsection

@push('scripts')

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/jquery-searchbox.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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
<Script>
        $(document).on('keyup', '.quantity', function () {
            var quntityTotalTaxPrice = 0;

            var el = $(this).parent().parent().parent().parent();

            var quantity = $(this).val();
            var price = $(el.find('.price')).val();
            var discount = $(el.find('.discount')).val();
            if(discount.length <= 0)
            {
                discount = 0 ;
            }

            var totalItemPrice = (quantity * price) - discount;

            var amount = (totalItemPrice);


            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice)+parseFloat(amount));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");
            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var totalAccount = 0;
            var accountInput = $('.accountAmount');

            for (var j = 0; j < accountInput.length; j++) {
                if (typeof accountInput[j].value != 'undefined') {
                    var accountInputPrice = parseFloat(accountInput[j].value);

                    if (isNaN(accountInputPrice)) {
                        totalAccount = 0;
                    } else {
                        totalAccount += accountInputPrice;
                    }
                }
            }

            var inputs = $(".amount");
            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }

            var sumAmount = totalItemPrice + totalAccount;

            $('.subTotal').html((sumAmount).toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));
            $('.totalAmount').html((parseFloat(subTotal)+totalAccount).toFixed(2));

        })

        $(document).on('keyup change', '.price', function () {
            var el = $(this).parent().parent().parent().parent();
            var price = $(this).val();
            var quantity = $(el.find('.quantity')).val();
            if(quantity.length <= 0)
            {
                quantity = 1 ;
            }
            var discount = $(el.find('.discount')).val();
            if(discount.length <= 0)
            {
                discount = 0 ;
            }
            var totalItemPrice = (quantity * price)-discount;

            var amount = (totalItemPrice);

            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice)+parseFloat(amount));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");
            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                if(inputs_quantity[j].value <= 0)
                {
                    inputs_quantity[j].value = 1 ;
                }
                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var totalAccount = 0;
            var accountInput = $('.accountAmount');

            for (var j = 0; j < accountInput.length; j++) {
                if (typeof accountInput[j].value != 'undefined') {
                    var accountInputPrice = parseFloat(accountInput[j].value);

                    if (isNaN(accountInputPrice)) {
                        totalAccount = 0;
                    } else {
                        totalAccount += accountInputPrice;
                    }
                }
            }


            var inputs = $(".amount");

            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }

            $('.subTotal').html((totalItemPrice+totalAccount).toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));
            $('.totalAmount').html((parseFloat(subTotal)+totalAccount).toFixed(2));
        })

        $(document).on('keyup change', '.discount', function () {
            var el = $(this).parent().parent().parent();
            var discount = $(this).val();
            if(discount.length <= 0)
            {
                discount = 0 ;
            }

            var price = $(el.find('.price')).val();
            var quantity = $(el.find('.quantity')).val();
            var totalItemPrice = (quantity * price) - discount;


            var amount = (totalItemPrice);


            var totalItemTaxRate = $(el.find('.itemTaxRate')).val();
            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (totalItemPrice));
            $(el.find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));

            $(el.find('.amount')).html(parseFloat(itemTaxPrice)+parseFloat(amount));

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');
            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            }


            var totalItemPrice = 0;
            var inputs_quantity = $(".quantity");

            var priceInput = $('.price');
            for (var j = 0; j < priceInput.length; j++) {
                totalItemPrice += (parseFloat(priceInput[j].value) * parseFloat(inputs_quantity[j].value));
            }

            var inputs = $(".amount");

            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {
                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }


            var totalItemDiscountPrice = 0;
            var itemDiscountPriceInput = $('.discount');

            for (var k = 0; k < itemDiscountPriceInput.length; k++) {
                if (itemDiscountPriceInput[k].value == '') {
                        itemDiscountPriceInput[k].value = parseFloat(0);
                    }
                totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k].value);
            }


            var totalAccount = 0;
            var accountInput = $('.accountAmount');
            for (var j = 0; j < accountInput.length; j++) {
                if (typeof accountInput[j].value != 'undefined') {
                    var accountInputPrice = parseFloat(accountInput[j].value);

                    if (isNaN(accountInputPrice)) {
                        totalAccount = 0;
                    } else {
                        totalAccount += accountInputPrice;
                    }
                }
            }


            $('.subTotal').html((totalItemPrice+totalAccount).toFixed(2));
            $('.totalTax').html(totalItemTaxPrice.toFixed(2));
            $('.totalAmount').html((parseFloat(subTotal)+totalAccount).toFixed(2));
            $('.totalDiscount').html(totalItemDiscountPrice.toFixed(2));
        })

        $(document).on('keyup change', '.accountAmount', function () {

            var el1 = $(this).parent().parent().parent().parent();
            var el = $(this).parent().parent().parent().parent().parent();

            var quantityDiv = $(el.find('.quantity'));
            var priceDiv = $(el.find('.price'));
            var discountDiv = $(el.find('.discount'));

            var itemSubTotal=0;
            for (var p = 0; p < priceDiv.length; p++) {
                var quantity=quantityDiv[p].value;
                var price=priceDiv[p].value;
                var discount=discountDiv[p].value;
                if(discount.length <= 0)
                {
                    discount = 0 ;
                }
                itemSubTotal += (quantity*price) - (discount);
            }


            // var totalItemTaxPrice = 0;
            // var itemTaxPriceInput = $('.itemTaxPrice');
            // for (var j = 0; j < itemTaxPriceInput.length; j++) {
            //
            //     totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
            //
            // }

            var totalItemTaxPrice = 0;
            var itemTaxPriceInput = $('.itemTaxPrice');

            for (var j = 0; j < itemTaxPriceInput.length; j++) {
                var parsedValue = parseFloat(itemTaxPriceInput[j].value);

                if (!isNaN(parsedValue)) {
                    totalItemTaxPrice += parsedValue;
                }
            }


            var amount = $(this).val();
            var amount =amount!=''?amount:0;
            el1.find('.accountamount').html(amount);
            var totalAccount = 0;
            var accountInput = $('.accountAmount');
            for (var j = 0; j < accountInput.length; j++) {
                var parsedAccountValue = parseFloat(accountInput[j].value);
                // totalAccount += (parseFloat(accountInput[j].value) );

                if (!isNaN(parsedAccountValue)) {
                    totalAccount += parsedAccountValue;
                }

            }


            var inputs = $(".accountamount");
            var subTotal = 0;
            for (var i = 0; i < inputs.length; i++) {

                subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
            }

            // console.log(subTotal)


            $('.subTotal').text((totalAccount+itemSubTotal).toFixed(2));
            $('.totalAmount').text((parseFloat((subTotal + itemSubTotal) + (totalItemTaxPrice))).toFixed(2));


        })
</Script>

@if (module_is_active('Account'))
    <script>
        $(document).on('change', '.item', function() {
            items($(this));
        });

        function items(data)
        {
            var in_type = $('#bill_type').val();
            if (in_type == 'product') {
                var iteams_id = data.val();
                var url = data.data('url');
                var el = data;
                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': jQuery('#token').val()
                    },
                    data: {
                        'product_id': iteams_id
                    },
                    cache: false,
                    success: function(data) {
                        var item = JSON.parse(data);
                        $(el.parent().parent().find('.quantity')).val(1);
                        if(item.product != null)
                        {
                            $(el.parent().parent().find('.price')).val(item.product.sale_price);
                            $(el.parent().parent().parent().find('.pro_description')).val(item.product.description);

                        }
                        else
                        {
                            $(el.parent().parent().find('.price')).val(0);
                            $(el.parent().parent().parent().find('.pro_description')).val('');

                        }

                        var taxes = '';
                        var tax = [];

                        var totalItemTaxRate = 0;

                        if (item.taxes == 0) {
                            taxes += '-';
                        } else {
                            for (var i = 0; i < item.taxes.length; i++) {
                                taxes += '<span class="badge bg-primary p-2 px-3 rounded mt-1 mr-1">' +
                                    item.taxes[i].name + ' ' + '(' + item.taxes[i].rate + '%)' +
                                    '</span>';
                                tax.push(item.taxes[i].id);
                                totalItemTaxRate += parseFloat(item.taxes[i].rate);
                            }
                        }
                        var itemTaxPrice = 0;
                        if(item.product != null)
                        {
                            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (item.product.sale_price * 1));
                        }
                        $(el.parent().parent().find('.itemTaxPrice')).val(itemTaxPrice.toFixed(2));
                        $(el.parent().parent().find('.itemTaxRate')).val(totalItemTaxRate.toFixed(2));
                        $(el.parent().parent().find('.taxes')).html(taxes);
                        $(el.parent().parent().find('.tax')).val(tax);
                        $(el.parent().parent().find('.unit')).html(item.unit);
                        $(el.parent().parent().find('.discount')).val(0);
                        $(el.parent().parent().find('.amount')).html(item.totalAmount);


                        var inputs = $(".amount");
                        var subTotal = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                        }

                        var accountinputs = $(".accountamount");
                        var accountSubTotal = 0;
                        for (var i = 0; i < accountinputs.length; i++)
                        {
                            var currentInputValue = parseFloat(accountinputs[i].innerHTML);
                            if (!isNaN(currentInputValue))
                            {
                                accountSubTotal += currentInputValue;
                            }
                        }

                        var totalItemPrice = 0;
                        var priceInput = $('.price');
                        for (var j = 0; j < priceInput.length; j++) {
                            totalItemPrice += parseFloat(priceInput[j].value);
                        }

                        var totalItemTaxPrice = 0;
                        var itemTaxPriceInput = $('.itemTaxPrice');
                        for (var j = 0; j < itemTaxPriceInput.length; j++) {
                            totalItemTaxPrice += parseFloat(itemTaxPriceInput[j].value);
                            if(item.product != null)
                            {
                                $(el.parent().parent().find('.amount')).html(parseFloat(item.totalAmount)+parseFloat(itemTaxPriceInput[j].value));
                            }
                        }



                        var totalItemDiscountPrice = 0;
                        var itemDiscountPriceInput = $('.discount');

                        for (var k = 0; k < itemDiscountPriceInput.length; k++) {

                            totalItemDiscountPrice += parseFloat(itemDiscountPriceInput[k].value);
                        }

                        $('.subTotal').html(totalItemPrice.toFixed(2));
                        $('.totalTax').html(totalItemTaxPrice.toFixed(2));
                        $('.totalAmount').html((parseFloat(totalItemPrice) - parseFloat(totalItemDiscountPrice) + parseFloat(totalItemTaxPrice)).toFixed(2));

                    },
                });
            }
        }
    </script>
@endif
@if (module_is_active('Taskly'))
    <script>
        $(document).on('change', '.item', function() {
            var iteams_id = $(this).val();
            var el = $(this);
            $(el.parent().parent().find('.price')).val(0);
            $(el.parent().parent().find('.amount')).html(0);
            $(el.parent().parent().find('.taxes')).val(0);
            var proposal_type =  $("#proposal_type").val();
            if (proposal_type == 'project') {
                $("#tax_project").change();
            }
        });

        $(document).on('change', '#tax_project', function() {
            var tax_id = $(this).val();
            if (tax_id.length != 0) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('get.taxes') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tax_id: tax_id,
                    },
                    beforeSend: function() {
                        $("#loader").removeClass('d-none');
                    },
                    success: function(response) {
                        var response = jQuery.parseJSON(response);
                        if (response != null) {
                            $("#loader").addClass('d-none');
                            var TaxRate = 0;
                            if (response.length > 0) {
                                $.each(response, function(i) {
                                    TaxRate = parseInt(response[i]['rate']) + TaxRate;
                                });
                            }
                            $(".itemTaxRate").val(TaxRate);
                            $(".price").change();
                        } else {
                            $(".itemTaxRate").val(0);
                            $(".price").change();
                            $('.section_div').html('');
                            toastrs('Error', 'Something went wrong please try again !', 'error');
                        }
                    },
                });
            }
            else
            {
                $(".itemTaxRate").val(0);
                $('.taxes').html("");
                $(".price").change();
                $("#loader").addClass('d-none');
            }
        });
    </script>
@endif

@if (module_is_active('Account'))
    <script>
        $(document).ready(function() {
            SectionGet('product');
        });
    </script>
@elseif (module_is_active('Taskly'))
    <script>
        $(document).ready(function() {
            SectionGet('project');
        });
    </script>
@endif
<script>
    $(document).on('click', '[data-repeater-delete]', function () {
        $(".price").change();
        $(".discount").change();
    });
</script>
<script>
    $(document).on('change', "[name='bill_type_radio']", function() {
        var val = $(this).val();
        $(".bill_div").empty();
        if (val == 'product') {
            $(".discount_apply_div").removeClass('d-none');
            $(".tax_project_div").addClass('d-none');
            $(".discount_project_div").addClass('d-none');

            var label =
                `{{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }} {{ Form::select('category_id', $category, null, ['class' => 'form-control', 'required' => 'required']) }}`;
            $(".bill_div").append(label);
            $("#bill_type").val('product');
            SectionGet(val);
        } else if (val == 'project') {
            $(".discount_apply_div").addClass('d-none');
            $(".tax_project_div").removeClass('d-none');
            $(".discount_project_div").removeClass('d-none');

            var label =
                ` {{ Form::label('project', __('Project'), ['class' => 'form-label']) }} {{ Form::select('project', $projects, null, ['class' => 'form-control', 'required' => 'required']) }}`
            $(".bill_div").append(label);
            $("#bill_type").val('project');
            var project_id = $("#project").val();
            SectionGet(val, project_id);
        }

        choices();
    });

    function SectionGet(type = 'product', project_id = "0",title = 'Project') {
        $.ajax({
            type: 'post',
            url: "{{ route('bill.section.type') }}",
            data: {
                _token: "{{ csrf_token() }}",
                type: type,
                project_id: project_id,
                acction: 'create',
            },
            beforeSend: function() {
                $("#loader").removeClass('d-none');
            },
            success: function(response) {
                if (response != false) {
                    $('.section_div').html(response.html);
                    $("#loader").addClass('d-none');
                    $('.pro_name').text(title)
                    // for item SearchBox ( this function is  custom Js )
                    JsSearchBox();
                } else {
                    $('.section_div').html('');
                    toastrs('Error', 'Something went wrong please try again !', 'error');
                }
            },
        });
    }
    $(document).on('change', "#project", function() {
        var title = $(this).find('option:selected').text();
        var project_id = $(this).val();
        SectionGet('project', project_id,title);

    });
</script>
@endpush
