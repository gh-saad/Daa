@extends('layouts.main')
@section('page-title')
    {{ __('Balance Sheet') }}
@endsection
@section('page-breadcrumb')
    {{__('Balance Sheet')}}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('Modules/DoubleEntry/Resources/assets/css/app.css') }}">
@endpush

@push('scripts')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var filename = $('#filename').val();

        function saveAsPDF() {
            var printContents = document.getElementById('printableArea').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>

    <script>
        $(document).ready(function () {
            $("#filter").click(function () {
                $("#show_filter").toggle();
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            callback();

            function callback() {
                var start_date = $(".startDate").val();
                var end_date = $(".endDate").val();

                $('.start_date').val(start_date);
                $('.end_date').val(end_date);
            }
        });
    </script>
@endpush


@section('page-action')
    <div>
        <input type="hidden" name="start_date" class="start_date">
        <input type="hidden" name="end_date" class="end_date">
        <a href="#" onclick="saveAsPDF()" class="btn btn-sm btn-primary me-1" data-bs-toggle="tooltip"
           title="{{ __('Print') }}"
           data-original-title="{{ __('Print') }}"><i class="ti ti-printer"></i></a>

        <div class="float-end" id="filter">
            <button id="filter" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                    title="{{ __('Filter') }}"><i class="ti ti-filter"></i></button>
        </div>

        <div class="float-end me-1">
            <a href="{{ route('report.balance.sheet', 'vertical') }}" class="btn btn-sm btn-primary"
               data-bs-toggle="tooltip"
               title="{{ __('Vertical View') }}" data-original-title="{{ __('Vertical View') }}"><i
                    class="ti ti-separator-horizontal"></i></a>
        </div>
    </div>
@endsection

@section('content')
    <div class="mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="mt-2" id="multiCollapseExample1">
                    <div class="card" id="show_filter" style="display:none;">
                        <div class="card-body">
                            {{ Form::open(['route' => ['report.balance.sheet'], 'method' => 'GET', 'id' => 'report_balance_sheet']) }}
                            <div class="row align-items-center justify-content-end">
                                <div class="col-xl-10">
                                    <div class="row">
                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                                {{ Form::date('start_date', $filter['startDateRange'], ['class' => 'startDate form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                                {{ Form::date('end_date', $filter['endDateRange'], ['class' => 'endDate form-control']) }}
                                            </div>
                                        </div>
                                        <input type="hidden" name="view" value="horizontal">
                                    </div>
                                </div>
                                <div class="col-auto mt-4">
                                    <div class="row">
                                        <div class="col-auto">
                                            <a href="#" class="btn btn-sm btn-primary"
                                               onclick="document.getElementById('report_balance_sheet').submit(); return false;"
                                               data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                               data-original-title="{{ __('apply') }}">
                                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                            </a>

                                            <a href="{{ route('report.balance.sheet' ,'horizontal')}}"
                                               class="btn btn-sm btn-danger "
                                               data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                               data-original-title="{{ __('Reset') }}">
                                                <span class="btn-inner--icon"><i
                                                        class="ti ti-trash-off text-white-off "></i></span>
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
        </div>

        @php
            $authUser = creatorId();
            $user = App\Models\User::find($authUser);
        @endphp

        @php
            $authUser = creatorId();
            $user = App\Models\User::find($authUser);
        @endphp
        <div class="row justify-content-center" id="printableArea">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body {{ $collapseview == 'expand' ? 'collapse-view' : '' }}">
                        <div class="account-main-title mb-5">
                            <h5>{{ 'Balance Sheet of ' . $user->name . ' as of ' . $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}
                                </h4>
                        </div>

                        @php
                            $totalAssets = 0;
                            $totalLiabilitiesEquity = 0;
                        @endphp

                        <div class="row">
                            <div class="col-md-6">
                                <div class="account-title d-flex align-items-center justify-content-between border py-2">
                                    <h5 class="mb-0 ms-3">{{ __('Assets') }}</h5>
                                </div>
                                <div class="border-start border-end">
                                    @foreach ($totalAccounts as $typeName => $subTypes)
                                        @if ($typeName == 'Assets')
                                            <div class="account-main-inner py-2">
                                                <p class="fw-bold ps-2 mb-2">{{ $typeName }}</p>
                                                @php
                                                    $total = 0;
                                                @endphp
                                                @foreach ($subTypes as $subTypeName => $accounts)
                                                    <div class="border-bottom py-2">
                                                        <p class="fw-bold ps-4 mb-2">{{ $subTypeName }}</p>
                                                        @foreach ($accounts as $account)
                                                            <div class="account-inner d-flex align-items-center justify-content-between ps-5">
                                                                <p class="mb-2 ms-3">
                                                                    <a href="{{ route('report.ledger', $account['account_id']) }}?account={{ $account['account_id'] }}" class="text-primary">
                                                                        {{ $account['account_name'] }}
                                                                    </a>
                                                                </p>
                                                                <p class="mb-2 text-center">{{ $account['account_code'] }}</p>
                                                                <p class="text-primary mb-2 float-end text-end me-3">
                                                                    @if ($account['total_amount'] < 0)
                                                                        @php
                                                                            $removedNegative = abs($account['total_amount']);
                                                                        @endphp
                                                                        {{ '( ' . number_format($removedNegative, 2) . ' ' . company_setting('defult_currancy') . ' )' }}
                                                                    @else
                                                                        {{ number_format($account['total_amount'], 2) . ' ' . company_setting('defult_currancy') }}
                                                                    @endif
                                                                </p>
                                                            </div>
                                                            @php
                                                                $total += $account['total_amount'];
                                                            @endphp
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                                <div class="account-title d-flex align-items-center justify-content-between border-top border-bottom py-2 px-2 pe-0">
                                                    <h6 class="fw-bold mb-0">{{ 'Total for ' . $typeName }}</h6>
                                                    <h6 class="fw-bold mb-0 text-end me-3">
                                                        @if ($total < 0)
                                                            @php
                                                                $removedNegative = abs($total);
                                                            @endphp
                                                            {{ '( ' . number_format($removedNegative, 2) . ' ' . company_setting('defult_currancy') . ' )' }}
                                                        @else
                                                            {{ number_format($total, 2) . ' ' . company_setting('defult_currancy') }}
                                                        @endif
                                                    </h6>
                                                @php
                                                    $totalAssets += $total;
                                                @endphp
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="account-title d-flex align-items-center justify-content-between border py-2">
                                    <h5 class="mb-0 ms-3">{{ __('Liabilities & Equity') }}</h5>
                                </div>
                                <div class="border-start border-end">
                                    @foreach ($totalAccounts as $typeName => $subTypes)
                                        @if ($typeName != 'Assets')
                                            <div class="account-main-inner py-2">
                                                <p class="fw-bold ps-2 mb-2">{{ $typeName }}</p>
                                                @php
                                                    $total = 0;
                                                @endphp
                                                @foreach ($subTypes as $subTypeName => $accounts)
                                                    <div class="border-bottom py-2">
                                                        <p class="fw-bold ps-4 mb-2">{{ $subTypeName }}</p>
                                                        @foreach ($accounts as $account)
                                                            <div class="account-inner d-flex align-items-center justify-content-between ps-5">
                                                                <p class="mb-2 ms-3">
                                                                    <a href="{{ route('report.ledger', $account['account_id']) }}?account={{ $account['account_id'] }}" class="text-primary">
                                                                        {{ $account['account_name'] }}
                                                                    </a>
                                                                </p>
                                                                <p class="mb-2 text-center">{{ $account['account_code'] }}</p>
                                                                <p class="text-primary mb-2 float-end text-end me-3">
                                                                    @if ($account['total_amount'] < 0)
                                                                        @php
                                                                            $removedNegative = abs($account['total_amount']);
                                                                        @endphp
                                                                        {{ '( ' . number_format($removedNegative, 2) . ' ' . company_setting('defult_currancy') . ' )' }}
                                                                    @else
                                                                        {{ number_format($account['total_amount'], 2) . ' ' . company_setting('defult_currancy') }}
                                                                    @endif
                                                                </p>
                                                            </div>
                                                            @php
                                                                $total += $account['total_amount'];
                                                            @endphp
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                                <div class="account-title d-flex align-items-center justify-content-between border-top border-bottom py-2 px-2 pe-0">
                                                    <h6 class="fw-bold mb-0">{{ 'Total for ' . $typeName }}</h6>
                                                    <h6 class="fw-bold mb-0 text-end me-3">
                                                        @if ($total < 0)
                                                            @php
                                                                $removedNegative = abs($total);
                                                            @endphp
                                                            {{ '( ' . number_format($removedNegative, 2) . ' ' . company_setting('defult_currancy') . ' )' }}
                                                        @else
                                                            {{ number_format($total, 2) . ' ' . company_setting('defult_currancy') }}
                                                        @endif
                                                    </h6>
                                                @php
                                                    $totalLiabilitiesEquity += $total;
                                                @endphp
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="account-title d-flex align-items-center justify-content-between border-top border-bottom py-2 px-2 pe-0">
                                    <h6 class="fw-bold mb-0">{{ 'Total for Assets' }}</h6>
                                    <h6 class="fw-bold mb-0 text-end me-3">
                                        @if ($totalAssets < 0)
                                            @php
                                                $removedNegative = abs($totalAssets);
                                            @endphp
                                            {{ '( ' . number_format($removedNegative, 2) . ' ' . company_setting('defult_currancy') . ' )' }}
                                        @else
                                            {{ number_format($totalAssets, 2) . ' ' . company_setting('defult_currancy') }}
                                        @endif
                                    </h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="account-title d-flex align-items-center justify-content-between border-top border-bottom py-2 px-2 pe-0">
                                    <h6 class="fw-bold mb-0">{{ 'Total for Liabilities & Equity' }}</h6>
                                    <h6 class="fw-bold mb-0 text-end me-3">
                                        @if ($totalLiabilitiesEquity < 0)
                                            @php
                                                $removedNegative = abs($totalLiabilitiesEquity);
                                            @endphp
                                            {{ '( ' . number_format($removedNegative, 2) . ' ' . company_setting('defult_currancy') . ' )' }}
                                        @else
                                            {{ number_format($totalLiabilitiesEquity, 2) . ' ' . company_setting('defult_currancy') }}
                                        @endif
                                    </h6>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
