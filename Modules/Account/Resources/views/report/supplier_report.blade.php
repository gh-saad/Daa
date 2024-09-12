@extends('layouts.main')
@section('page-title')
    {{ __('Supplier Summary') }}
@endsection
@section('page-breadcrumb')
    {{ __('Report') }},
    {{ __('Supplier Summary') }}
@endsection
@push('css')
    <style>
        .select2-selection__rendered {
            line-height: 31px !important;
        }
        .select2-container .select2-selection--single {
            height: 35px !important;
        }
        .select2-selection__arrow {
            height: 34px !important;
        }
        .form-label {
            font-size: 14px;
            font-weight: 600;
        }
        .badge-pill {
            display: inline-block;
            padding: 0.5em 1em;
            border-radius: 50px;
            color: #fff;
            font-size: 0.9em;
            text-align: center;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-danger {
            background-color: #dc3545;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        var filename = $('#filename').val();
        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {type: 'jpeg', quality: 1},
                html2canvas: {scale: 4, dpi: 72, letterRendering: true},
                jsPDF: {unit: 'in', format: 'A4'}
            };
            html2pdf().set(opt).from(element).save();
        }

        document.addEventListener('DOMContentLoaded', function () {
            var vehicleSelect = document.getElementById('vehicle_chasis_no');

            // Initial state
            vehicleSelect.disabled = true;
            $('#vendor-detail').html(`
                <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                <p class="text-muted"> - SELECT A SUPPLIER - </p>
            `);
            $('#product-detail').html(`
                <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                <p class="text-muted"> - SELECT A SUPPLIER - </p>
            `);
            $('#purchase-detail').html(`
                <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                <p class="text-muted"> - SELECT A SUPPLIER - </p>
            `);

            // Listen for changes on the vendor select input
            $('#vendor_select').on('change.select2', function () {
                var selectedVendor = $(this).val();

                if (selectedVendor) {
                    fetch('/get-vehicles-for-vendor/' + selectedVendor)
                        .then(response => response.json())
                        .then(data => {
                            $('#vehicle_chasis_no').html('<option selected>{{ __('Open this select menu') }}</option>');

                            if (data.vehicles.length > 0) {
                                data.vehicles.forEach(function(vehicle) {
                                    $('#vehicle_chasis_no').append('<option value="' + vehicle.id + '">' + vehicle.name + '</option>');
                                });
                                $('#vehicle_chasis_no').prop('disabled', false);
                                $('#product-detail').addClass('text-center').html(`
                                    <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                                    <p class="text-muted"> - SELECT A VEHICLE - </p>
                                `);
                                $('#purchase-detail').addClass('text-center').html(`
                                    <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                                    <p class="text-muted"> - SELECT A VEHICLE - </p>
                                `);
                            } else {
                                $('#vehicle_chasis_no').prop('disabled', true);
                                $('#product-detail').addClass('text-center').html(`
                                    <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                                    <p class="text-muted"> - NO VEHICLE WAS EVER PURCHASED FROM THIS SUPPLIER - </p>
                                `);
                                $('#purchase-detail').addClass('text-center').html(`
                                    <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                                    <p class="text-muted"> - NO VEHICLE WAS EVER PURCHASED FROM THIS SUPPLIER - </p>
                                `);
                            }

                            if (data.vendor) {
                                $('#vendor-detail').removeClass('text-center');
                                $('#vendor-detail').html(`
                                    <div class="col-3"><p><strong>Name</strong></p></div>:<div class="col-8">${data.vendor.billing_name}</div>
                                    <div class="col-3"><p><strong>Email</strong></p></div>:<div class="col-8">${data.vendor.email}</div>
                                    <div class="col-3"><p><strong>Contact</strong></p></div>:<div class="col-8">${data.vendor.billing_phone}</div>
                                    <div class="col-3"><p><strong>Address</strong></p></div>:<div class="col-8">${data.vendor.billing_address}</div>
                                    <div class="col-3"><p><strong>Country</strong></p></div>:<div class="col-8">${data.vendor.billing_country}</div>
                                    <div class="col-3"><p><strong>State</strong></p></div>:<div class="col-8">${data.vendor.billing_state}</div>
                                    <div class="col-3"><p><strong>City</strong></p></div>:<div class="col-8">${data.vendor.billing_city}</div>
                                    <div class="col-3"><p><strong>Zipcode</strong></p></div>:<div class="col-8">${data.vendor.billing_zip}</div>
                                    <div class="col-3"><p><strong>Tax No.</strong></p></div>:<div class="col-8">${data.vendor.tax_number}</div>
                                `);
                            } else {
                                $('#vendor-detail').html(`
                                    <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                                    <p class="text-muted"> - SELECT A SUPPLIER - </p>
                                `).addClass('text-center');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching vehicles:', error);
                            $('#vendor-detail').addClass('text-center').html(`
                                <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                                <p class="text-muted"> - SELECT A SUPPLIER - </p>
                            `);
                            $('#vehicle_chasis_no').prop('disabled', true);
                        });
                } else {
                    $('#vendor-detail').addClass('text-center').html(`
                        <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                        <p class="text-muted"> - SELECT A SUPPLIER - </p>
                    `);
                    $('#vehicle_chasis_no').prop('disabled', true);
                }
            });
            
            // Listen for changes on the vehicle select input
            $('#vehicle_chasis_no').on('change', function () {
                var selectedVehicle = $(this).val();

                if (selectedVehicle) {
                    fetch('/get-product-details/' + selectedVehicle)
                        .then(response => response.json())
                        .then(data => {
                            if (data.product && data.purchase) {
                                // Calculate price after discount
                                var priceAfterDiscount = data.purchase.price - data.purchase.discount;
                                var priceStillDue = priceAfterDiscount - data.purchase.price_paid;

                                // Set status using pills
                                var paidStatus = `<span class="badge-pill badge-success">Paid</span>`;
                                var partiallyPaidStatus = `<span class="badge-pill badge-warning">Partially Paid</span>`;
                                var awaitingStatus = `<span class="badge-pill badge-danger">Awaiting Payment</span>`;
                                var finalStatus;

                                if (data.purchase.status == 4) {
                                    finalStatus = paidStatus;
                                } else if (data.purchase.status == 3) {
                                    finalStatus = partiallyPaidStatus;
                                } else {
                                    finalStatus = awaitingStatus;
                                }

                                // Calculate vehicle age
                                var purchaseDate = new Date(data.purchase.date);
                                var today = new Date();
                                var diffTime = Math.abs(today - purchaseDate);
                                var diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                                var vehicleAge = "";

                                if (diffDays < 30) {
                                    vehicleAge = `${diffDays} days`;
                                } else if (diffDays < 365) {
                                    var diffMonths = Math.floor(diffDays / 30);
                                    vehicleAge = `${diffMonths} month${diffMonths > 1 ? 's' : ''}`;
                                } else {
                                    var diffYears = Math.floor(diffDays / 365);
                                    vehicleAge = `${diffYears} year${diffYears > 1 ? 's' : ''}`;
                                }

                                // Update product details
                                $('#product-detail').removeClass('text-center').html(`
                                    <div class="col-3"><p><strong>Chasis No</strong></p></div>:<div class="col-8">${data.product.sku}</div>
                                    <div class="col-3"><p><strong>Name</strong></p></div>:<div class="col-8">${data.product.name}</div>
                                    <div class="col-3"><p><strong>Color</strong></p></div>:<div class="col-8">${data.product.colour}</div>
                                    <div class="col-3"><p><strong>Fuel Type</strong></p></div>:<div class="col-8">${data.product.fuel}</div>
                                    <div class="col-3"><p><strong>Year</strong></p></div>:<div class="col-8">${data.product.mfg_year}</div>
                                    <div class="col-3"><p><strong>Status</strong></p></div>:<div class="col-8">${data.product.vehicle_status}</div>
                                    <div class="col-3"><p><strong>Engine No</strong></p></div>:<div class="col-8">${data.product.engine_no}</div>
                                    <div class="col-3"><p><strong>Engine CC</strong></p></div>:<div class="col-8">${data.product.engine_cc}</div>
                                `);

                                var price_display = Number(data.purchase.price).toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });

                                var discount_display = Number(data.purchase.discount).toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });

                                var price_after_discount_display = Number(priceAfterDiscount).toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });

                                var price_paid_display = Number(data.purchase.price_paid).toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });

                                var price_still_due_display = Number(priceStillDue).toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });

                                // Update purchase details
                                $('#purchase-detail').removeClass('text-center').html(`
                                    <div class="col-6">
                                        <div class="row">
                                            <div class="col-3"><p><strong>Lot No</strong></p></div>:<div class="col-8">${data.purchase.lot_no}</div>
                                            <div class="col-3"><p><strong>BL No</strong></p></div>:<div class="col-8">${data.purchase.bl_no}</div>
                                            <div class="col-3"><p><strong>Price</strong></p></div>:<div class="col-8">${price_display} {{ company_setting('defult_currancy') }}</div>
                                            <div class="col-3"><p><strong>Discount</strong></p></div>:<div class="col-8">${discount_display} {{ company_setting('defult_currancy') }}</div>
                                            <div class="col-3"><p><strong>Price After Discount</strong></p></div>:<div class="col-8">${price_after_discount_display} {{ company_setting('defult_currancy') }}</div>
                                            <div class="col-3"><p><strong>Amount Paid</strong></p></div>:<div class="col-8">${price_paid_display} {{ company_setting('defult_currancy') }}</div>
                                            <div class="col-3"><p><strong>Amount Due</strong></p></div>:<div class="col-8">${price_still_due_display} {{ company_setting('defult_currancy') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row">
                                            <div class="col-3"><p><strong>Status</strong></p></div>:<div class="col-8">${finalStatus}</div>
                                            <div class="col-3"><p><strong>Last Payment Date</strong></p></div>:<div class="col-8">${data.purchase.last_payment_date}</div>
                                            <div class="col-3"><p><strong>Purchase Date</strong></p></div>:<div class="col-8">${data.purchase.date}</div>
                                            <div class="col-3"><p><strong>Vehicle Age</strong></p></div>:<div class="col-8">${vehicleAge}</div>
                                        </div>
                                    </div>
                                `);
                            } else {
                                $('#product-detail').addClass('text-center').html(`
                                    <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                                    <p class="text-muted"> - SELECT A VEHICLE - </p>
                                `);
                                $('#purchase-detail').addClass('text-center').html(`
                                    <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                                    <p class="text-muted"> - SELECT A VEHICLE - </p>
                                `);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching product details:', error);
                            $('#product-detail').addClass('text-center').html(`
                                <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                                <p class="text-muted"> - SELECT A VEHICLE - </p>
                            `);
                            $('#purchase-detail').addClass('text-center').html(`
                                <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                                <p class="text-muted"> - SELECT A VEHICLE - </p>
                            `);
                        });
                } else {
                    $('#product-detail').addClass('text-center').html(`
                        <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                        <p class="text-muted"> - SELECT A VEHICLE - </p>
                    `);
                    $('#purchase-detail').addClass('text-center').html(`
                        <p style="font-weight: 600; font-size: 40px;"> - {{ __('NO DATA') }} - </p>
                        <p class="text-muted"> - SELECT A VEHICLE - </p>
                    `);
                }
            });
        });
    </script>
@endpush
@section('page-action')
    <div>
        <a class="btn btn-sm btn-primary" onclick="saveAsPDF()" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Download') }}">
            <i class="ti ti-download"></i>
        </a>
    </div>
@endsection
@section('content')
    <!-- search and filter START -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row d-flex justify-content-end">
                        <div class="col-md-4 col-sm-6">
                            <label for="vendor_select" class="form-label">{{ __('Select Supplier') }}</label>
                            <select id="vendor_select" name="vendor_select" class="form-select select2">
                                <option selected>{{ __('Open this select menu') }}</option>
                                @foreach($venders as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label for="vehicle_chasis_no" class="form-label">{{ __('Select Vehicle') }}</label>
                            <select id="vehicle_chasis_no" name="vehicle_chasis_no" class="form-select select2" disabled>
                                <option selected>{{ __('Please select a supplier first') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- search and filter END -->
    <!-- information START -->
    <div id="printableArea" class="row">
        <div class="col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mt-1 mb-0">{{ __('Vendor Details') }}</h5>
                </div>
                <div class="card-body">
                    <div id="vendor-detail" class="row text-center">
                        <!-- vendor related details will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mt-1 mb-0">{{ __('Product Details') }}</h5>
                </div>
                <div class="card-body">
                    <div id="product-detail" class="row text-center">
                        <!-- product related details will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mt-1 mb-0">{{ __('Purchase Details') }}</h5>
                </div>
                <div class="card-body">
                    <div id="purchase-detail" class="row text-center">
                        <!-- purchase related details will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- information END -->
@endsection
