@extends('layouts.main')
@section('page-title')
    {{ __('Bank Expance') }}
@endsection
@section('page-breadcrumb')
    {{ __('Bank Expance') }}
@endsection
@section('page-action')
    <div>
        @if (module_is_active('ProductService'))
            @can('bill create')
                <a href="{{ route('category.index') }}"data-size="md" class="btn btn-sm btn-primary"
                    data-bs-toggle="tooltip"data-title="{{ __('Setup') }}" title="{{ __('Setup') }}"><i
                        class="ti ti-settings"></i></a>
            @endcan
        @endif
        @can('expense payment create')
            <a  class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg"
                data-title="{{ __('Create New Payment') }}" data-url="{{ route('payment.create') }}" data-bs-toggle="tooltip"
                data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class=" multi-collapse mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['payment.index'], 'method' => 'GET', 'id' => 'payment_form']) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row">
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                                            {{ Form::text('date', isset($_GET['date']) ? $_GET['date'] : null, ['class' => 'form-control flatpickr-to-input', 'placeholder' => 'Select Date']) }}
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-auto mt-4">
                                <div class="row">
                                    <div class="col-auto">
                                        <a  class="btn btn-sm btn-primary"
                                            onclick="document.getElementById('payment_form').submit(); return false;"
                                            data-toggle="tooltip" title="{{ __('Apply') }}"
                                            data-original-title="{{ __('apply') }}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>
                                        <a href="{{ route('payment.index') }}" class="btn btn-sm btn-danger"
                                            data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                                            <span class="btn-inner--icon"><i
                                                    class="ti ti-trash-off text-white-off "></i></i></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    @if (Gate::check('expense payment delete') || Gate::check('expense payment edit'))
                                        <th>{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr class="font-style">
                                        <td>{{ company_date_formate($payment->date) }}</td>
                                        <td>{{ $payment->description ?? 'Payments' }}</td>
                                        <td>{{ currency_format_with_sym($payment->amount) }}
                                        </td>
                                        @if (Gate::check('expense payment delete') || Gate::check('expense payment edit'))
                                            <td class="action text-end">
                                                @can('expense payment edit')
                                                    <div class="action-btn bg-info ms-2">
                                                        <a  class="mx-3 btn btn-sm align-items-center"
                                                            data-url="{{ route('payment.edit', $payment->id) }}"
                                                            data-ajax-popup="true" data-title="{{ __('Edit Payment') }}"
                                                            data-size="lg" data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                            data-original-title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('expense payment delete')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {{ Form::open(['route' => ['payment.destroy', $payment->id], 'class' => 'm-0']) }}
                                                        @method('DELETE')
                                                        <a 
                                                            class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Delete" aria-label="Delete"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $payment->id }}"><i
                                                                class="ti ti-trash text-white text-white"></i></a>
                                                        {{ Form::close() }}
                                                    </div>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach

                                @foreach ($billPayments as $payment)
                                    <tr class="font-style">
                                        <td>{{ company_date_formate($payment->date) }}</td>
                                        <td>{{ $payment->description ?? 'Bill Payments'}}</td>
                                        <td>{{ currency_format_with_sym($payment->amount) }}
                                        </td>
                                        @if (Gate::check('expense payment delete') || Gate::check('expense payment edit'))
                                            <td class="action text-end">
                                                @can('expense payment edit')
                                                    <div class="action-btn bg-info ms-2">
                                                        <a  class="mx-3 btn btn-sm align-items-center"
                                                            data-url="{{ route('payment.edit', $payment->id) }}"
                                                            data-ajax-popup="true" data-title="{{ __('Edit Payment') }}"
                                                            data-size="lg" data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                            data-original-title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('expense payment delete')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {{ Form::open(['route' => ['payment.destroy', $payment->id], 'class' => 'm-0']) }}
                                                        @method('DELETE')
                                                        <a 
                                                            class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Delete" aria-label="Delete"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $payment->id }}"><i
                                                                class="ti ti-trash text-white text-white"></i></a>
                                                        {{ Form::close() }}
                                                    </div>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach

                                @foreach ($purchasePayments as $payment)
                                    <tr class="font-style">
                                        <td>{{ company_date_formate($payment->date) }}</td>
                                        <td>{{ $payment->description ?? 'Purchase Payments' }}</td>
                                        <td>{{ currency_format_with_sym($payment->amount) }}
                                        </td>
                                        @if (Gate::check('expense payment delete') || Gate::check('expense payment edit'))
                                            <td class="action text-end">
                                                @can('expense payment edit')
                                                    <div class="action-btn bg-info ms-2">
                                                        <a  class="mx-3 btn btn-sm align-items-center"
                                                            data-url="{{ route('payment.edit', $payment->id) }}"
                                                            data-ajax-popup="true" data-title="{{ __('Edit Payment') }}"
                                                            data-size="lg" data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                            data-original-title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('expense payment delete')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {{ Form::open(['route' => ['payment.destroy', $payment->id], 'class' => 'm-0']) }}
                                                        @method('DELETE')
                                                        <a 
                                                            class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Delete" aria-label="Delete"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $payment->id }}"><i
                                                                class="ti ti-trash text-white text-white"></i></a>
                                                        {{ Form::close() }}
                                                    </div>
                                                @endcan
                                            </td>
                                        @endif
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
