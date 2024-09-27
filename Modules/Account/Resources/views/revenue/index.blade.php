@extends('layouts.main')
@section('page-title')
    {{ __('Manage Revenues') }}
@endsection
@section('page-breadcrumb')
    {{ __('Revenues') }}
@endsection
@section('page-action')
    <div>
        @stack('addButtonHook')
        @if (module_is_active('ProductService'))
            <a href="{{ route('category.index') }}"data-size="md" class="btn btn-sm btn-primary"
                data-bs-toggle="tooltip"data-title="{{ __('Setup') }}" title="{{ __('Setup') }}"><i
                    class="ti ti-settings"></i></a>
        @endif
        @can('revenue create')
            <a  class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg"
                data-title="{{ __('Create New Revenue') }}" data-url="{{ route('revenue.create') }}" data-bs-toggle="tooltip"
                data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@push('css')
    <!-- Add any custom CSS here if needed -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="mt-2" id="multiCollapseExample1">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => ['revenue.index'], 'method' => 'GET', 'id' => 'revenue_form']) }}
                    <div class="row align-items-center justify-content-end">
                        <div class="col-xl-10">
                            <div class="row">
                                <div class="col-3">
                                    {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                                    {{ Form::text('date', isset($_GET['date']) ? $_GET['date'] : null, ['class' => 'month-btn form-control flatpickr-to-input', 'placeholder' => 'Select Date']) }}
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 month">
                                    <div class="btn-box">
                                        {{ Form::label('account', __('Account'), ['class' => 'form-label']) }}
                                        {{ Form::select('account', $account, isset($_GET['account']) ? $_GET['account'] : '', ['class' => 'form-control ', 'placeholder' => 'Select Account']) }}
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 date">
                                    <div class="btn-box">
                                        {{ Form::label('customer', __('Customer'), ['class' => 'form-label']) }}
                                        {{ Form::select('customer', $customer, isset($_GET['customer']) ? $_GET['customer'] : '', ['class' => 'form-control ', 'placeholder' => 'Select Customer']) }}
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('category', __('Category'), ['class' => 'form-label']) }}
                                        {{ Form::select('category', $category, isset($_GET['category']) ? $_GET['category'] : '', ['class' => 'form-control ', 'placeholder' => 'Select Category']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto mt-4">
                            <div class="row">
                                <div class="col-auto">
                                    <a  class="btn btn-sm btn-primary"
                                        onclick="document.getElementById('revenue_form').submit(); return false;"
                                        data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                        data-original-title="{{ __('apply') }}">
                                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                    </a>
                                    <a href="{{ route('revenue.index') }}" class="btn btn-sm btn-danger "
                                        data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                        data-original-title="{{ __('Reset') }}">
                                        <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off "></i></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th> {{ __('Date') }}</th>
                                    <th> {{ __('Amount') }}</th>
                                    <th> {{ __('Account') }}</th>
                                    <th> {{ __('Customer') }}</th>
                                    <th> {{ __('Category') }}</th>
                                    <th> {{ __('Reference') }}</th>
                                    <th> {{ __('Description') }}</th>
                                    <th>{{ __('Payment Receipt') }}</th>
                                    <!-- @if (Gate::check('revenue edit') || Gate::check('revenue delete'))
                                        <th width="10%"> {{ __('Action') }}</th>
                                    @endif -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($revenues as $revenue)
                                    <tr class="font-style">
                                        <td>{{ company_date_formate($revenue->date) }}</td>
                                        <td>{{ number_format(currency_conversion($revenue->amount, $revenue->currency, company_setting('defult_currancy')), 2) . ' ' . company_setting('defult_currancy') }}</td>
                                        <td>{{ !empty($revenue->bankAccount) ? $revenue->bankAccount->bank_name . ' ' . $revenue->bankAccount->holder_name : '' }}
                                        </td>
                                        <td>{{ !empty($revenue->customer) ? $revenue->customer->name : '-' }}</td>
                                        @if (module_is_active('ProductService'))
                                            <td>{{ !empty($revenue->category) ? $revenue->category->name : '-' }}</td>
                                        @else
                                            <td>-</td>
                                        @endif
                                        <td>{{ !empty($revenue->reference) ? $revenue->reference : '-' }}</td>
                                        <td>
                                            <p style="white-space: nowrap;
                                                width: 200px;
                                                overflow: hidden;
                                                text-overflow: ellipsis;">{{  !empty($revenue->description) ? $revenue->description : '' }}
                                            </p>
                                        </td>
                                        <td>
                                            @if (!empty($revenue->add_receipt))
                                                <div class="action-btn bg-primary ms-2">
                                                    <a href="{{ get_file($revenue->add_receipt) }}" download=""
                                                        class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip"
                                                        title="{{ __('Download') }}" target="_blank">
                                                        <i class="ti ti-download text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="action-btn bg-secondary ms-2">
                                                    <a href="{{ get_file($revenue->add_receipt) }}"
                                                        class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip"
                                                        title="{{ __('Show') }}" target="_blank">
                                                        <i class="ti ti-crosshair text-white"></i>
                                                    </a>
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <!-- @if (Gate::check('revenue edit') || Gate::check('revenue delete'))
                                            <td class="Action">
                                                <span>
                                                    @can('revenue edit')
                                                        <div class="action-btn bg-info ms-2">
                                                            <a  class="mx-3 btn btn-sm align-items-center"
                                                                data-url="{{ route('revenue.edit', $revenue->id) }}"
                                                                data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" t
                                                                title="{{ __('Edit') }}"
                                                                data-title="{{ __('Edit Revenue') }}">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('revenue delete')
                                                        <div class="action-btn bg-danger ms-2">
                                                            {{ Form::open(['route' => ['revenue.destroy', $revenue->id], 'class' => 'm-0']) }}
                                                            @method('DELETE')
                                                            <a
                                                                class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-bs-original-title="Delete" aria-label="Delete"
                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-form-{{ $revenue->id }}"><i
                                                                    class="ti ti-trash text-white text-white"></i></a>
                                                            {{ Form::close() }}
                                                        </div>
                                                    @endcan
                                                </span>
                                            </td>
                                        @endif -->
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- select2 modal init -->
    <script>
        $(document).ready(function() {
            $('#commonModal').on('shown.bs.modal', function () {
                $('.select2').select2({
                    dropdownParent: $('#commonModal'),
                    width: '100%'
                });
            });
        });
    </script>

    <!-- revenue type -->
    <script>
        $(document).ready(function() {
            $('#commonModal').on('shown.bs.modal', function () {
                // Function to toggle customer field visibility and required status
                function toggleCustomerField() {
                    var typeValue = $('#typeSelect').val();

                    if (typeValue === 'customer_not_included') {
                        // Hide customer select and remove 'required' attribute
                        $('#customerSelect').closest('.form-group').hide();
                        $('#customerSelect').prop('required', false);
                    } else {
                        // Show customer select and add 'required' attribute
                        $('#customerSelect').closest('.form-group').show();
                        $('#customerSelect').prop('required', true);
                    }
                }

                // Run on modal load
                toggleCustomerField();

                // Run when 'typeSelect' changes
                $('#typeSelect').change(function() {
                    toggleCustomerField();
                });
            });
        });
    </script>
@endpush