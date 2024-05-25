@extends('layouts.main')
@section('page-title')
    {{ __('Profit & Loss') }}
@endsection
@section('page-breadcrumb')
    {{__('Profit and Loss')}}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('Modules/DoubleEntry/Resources/assets/css/app.css') }}" id="main-style-link">
@endpush

@push('scripts')

    <script src="{{ asset('Modules/DoubleEntry/Resources/assets/js/html2pdf.bundle.min.js') }}"></script>

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
            <button id="filter" class="btn btn-sm btn-primary"><i class="ti ti-filter"></i></button>
        </div>

        <div class="float-end me-1">
            <a href="{{ route('report.profit.loss' , 'horizontal')}}" class="btn btn-sm btn-primary"
               data-bs-toggle="tooltip" title="{{ __('Horizontal View') }}"
               data-original-title="{{ __('Horizontal View') }}"><i class="ti ti-separator-vertical"></i></a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-sm-8">
            <div class="mt-2 " id="multiCollapseExample1">
                <div class="card" id="show_filter" style="display:none;">
                    <div class="card-body">
                        {{ Form::open(['route' => ['report.profit.loss'], 'method' => 'GET', 'id' => 'report_profit_loss']) }}
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
                                </div>
                            </div>
                            <div class="col-auto mt-4">
                                <div class="row">
                                    <div class="col-auto">
                                        <a href="#" class="btn btn-sm btn-primary"
                                           onclick="document.getElementById('report_profit_loss').submit(); return false;"
                                           data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                           data-original-title="{{ __('apply') }}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>

                                        <a href="{{ route('report.profit.loss') }}" class="btn btn-sm btn-danger "
                                           data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                           data-original-title="{{ __('Reset') }}">
                                            <span class="btn-inner--icon">
                                                <i class="ti ti-trash-off text-white-off "></i></span>
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

    <div class="row justify-content-center" id="printableArea">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body {{ $collapseView == 'expand' ? 'collapse-view' : '' }}">
                    <div class="account-main-title mb-5">
                        <h5>{{ 'Profit & Loss of ' . $user->name . ' as of ' . $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}
                        </h5>
                    </div>
                    <div
                        class="account-title d-flex align-items-center justify-content-between border-top border-bottom py-2">
                        <h6 class="mb-0">{{ __('Account') }}</h6>
                        <h6 class="mb-0 text-center">{{ _('Account Code') }}</h6>
                        <h6 class="mb-0 text-end">{{ __('Total') }}</h6>

                    </div>
                    @php
                    $total_credit = 0;
                @endphp

                @foreach ($total_accounts as $type => $sub_types)
                    <div class="account-main-inner border-bottom py-2">
                        <p class="fw-bold ps-2 mb-2">{{ $type }}</p>
                        @foreach ($sub_types as $sub_type => $accounts)
                            <div class="border-bottom py-2">
                                <p class="fw-bold ps-4 mb-2">{{ $sub_type }}</p>
                                @foreach ($accounts as $account)
                                    @php
                                        $total_credit += $account['credit'];
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-between ps-4">
                                        <p class="mb-2 ms-3">
                                            <a href="" class="text-primary">
                                                {{ $account['name'] }}</a>
                                        </p>
                                        <p class="mb-2 ms-3 text-center">{{ $account['code'] }}</p>
                                        
                                        <p class="text-dark">{{ currency_format_with_sym($account['credit']) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endforeach


                @if ($total_accounts != [])
                    <div
                        class="account-title d-flex align-items-center justify-content-between border-top border-bottom py-2 px-2 pe-0">
                        <h6 class="fw-bold mb-2 ms-3">Gross Profit</h6>
                        <h6 class="fw-bold mb-2 ms-3"></h6>
                        <h6 class="fw-bold mb-2 ms-3"></h6>
                        <h6 class="fw-bold mb-0 text-end ms-3">{{ currency_format_with_sym($total_credit) }}</h6>

                    </div>
                @endif

                </div>
            </div>
        </div>
    </div>
@endsection
