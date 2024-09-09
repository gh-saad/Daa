@extends('layouts.main')
@section('page-title')
    {{__('Cash Summary')}}
@endsection
@section('page-breadcrumb')
    {{ __('Report') }},
    {{__('Cash Summary')}}
@endsection
@push('scripts')
<script src="{{ asset('Modules/Account/Resources/assets/js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var year = '{{$currentYear}}';
        var filename = $('#filename').val();

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {type: 'jpeg', quality: 1},
                html2canvas: {scale: 4, dpi: 72, letterRendering: true},
                jsPDF: {unit: 'in', format: 'A2'}
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
@endpush
@section('page-action')
    <div>
        <a  class="btn btn-sm btn-primary" onclick="saveAsPDF()"  data-bs-toggle="tooltip"  data-bs-original-title="{{ __('Download') }}">
            <i class="ti ti-download"></i>
        </a>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class=" multi-collapse mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                    {{ Form::open(array('route' => array('report.profit.loss.summary'),'method' => 'GET','id'=>'report_profit_loss_summary')) }}
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
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                                            {{ Form::input('date', 'date', date('m/d/Y'), ['class' => 'form-control']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="row">
                                    <div class="col-auto mt-4">
                                        <a  class="btn btn-sm btn-primary"
                                        onclick="document.getElementById('report_profit_loss_summary').submit(); return false;"
                                        data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                        data-original-title="{{ __('apply') }}">
                                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                    </a>
                                    <a href="{{ route('report.profit.loss.summary') }}" class="btn btn-sm btn-danger"
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
        </div>
    </div>
    @php
        $rate = getRate('KES')
    @endphp
    <div id="printableArea">
        <div class="row">
            <div class="col-7">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div class="row">
                            <div class="col-sm-12">
                                <h3 class="pb-3">AAA Daily KES Summary As at {{$date}}</h3>
                                <h5 class="pb-3">{{__('Income')}}</h5>
                                <div class="table-responsive mt-3 mb-3">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th width="25%"></th>
                                                <th>{{__('CASH')}}</th>
                                                <th>{{__('EQUITY KES')}}</th>
                                                <th>{{__('PRIME KES')}}</th>
                                                <th>{{__('TOTAL (KES)')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>OPENING</td>
                                                <td>{{ currency_format_with_code($openingCash, "KES") }}</td>
                                                <td>{{ currency_format_with_code($openingEquity, "KES") }}</td>
                                                <td>{{ currency_format_with_code($openingPrime, "KES") }}</td>
                                                <td>{{ currency_format_with_code($openingTotal, "KES") }}</td>
                                            </tr>
                                            <tr>
                                                <td>SALES </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>{{ currency_format_with_code($salesTotal, "KES")}}</td>
                                            </tr>
                                            <tr>
                                                <td>SALE BALANCES</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>{{ currency_format_with_code($salesBalance, "KES") }}</td>
                                            </tr>
                                            <tr>
                                                <td>REGISTRATION</td>
                                                <td>{{$subscriptionCashTotal}}</td>
                                                <td>{{$subscriptionEquityKesTotal}}</td>
                                                <td>{{$subscriptionPrimeKesTotal}}</td>
                                                <td>{{$subscriptionTotal}}</td>
                                            </tr>
                                            <tr>
                                                <td>OTHER INCOME </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>BANK TRANSFER FROM EQUITY KES </td>
                                                <td>{{$transferCashToPrimeKesTotal}}</td>
                                                <td>-</td>
                                                <td>{{$transferEquityKesToPrimeKesTotal}}</td>
                                                <td>{{$tranferFromEquityKes}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>


                            <div class="col-sm-12">
                                <h5>{{__('Expense')}}</h5>
                                <div class="table-responsive mt-4">
                                    <table class="table  mb-0" id="dataTable-manual">
                                        <thead>
                                            <tr>
                                                <th width="25%"></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        
                                            <tr>
                                                <td>LESS:-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>DAILY EXPENSES</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>BANK TRANSFER TO PRIME KES</td>
                                                <td>{{$transferCashToPrimeKesTotal}}</td>
                                                <td>{{$transferEquityKesToPrimeKesTotal}}</td>
                                                <td>-</td>
                                                <td>{{ $transferPrimeKesTotal }}</td>
                                            </tr>
                                            <tr>
                                                <td>BANK TRANSFER TO  PRIME USD</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                                
                            </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-5">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div class="row">
                            <div class="col-sm-12">
                                <h3 class="pb-3"> AAA Daily USD Summary As at  {{$date}}</h3>
                                <h5 class="pb-3">{{__('Income')}}</h5>
                                <div class="table-responsive mt-3 mb-3">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th width="25%"></th>
                                                <th>{{__('PRIME USD')}}</th>
                                                <th>{{__('EQUITY USD')}}</th>
                                                <th>{{__('TOTAL')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>OPENING</td>
                                                <td>{{ currency_format_with_code($openingPrimeUsd / $rate, "USD") }}</td>
                                                <td>{{ currency_format_with_code($openingEquity / $rate, "USD") }}</td>
                                                <td>{{ currency_format_with_code($openingTotal / $rate, "USD") }}</td>
                                            </tr>
                                            <tr>

                                            </tr>
                                            <tr>
                                                <td>SALES </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>{{ currency_format_with_code($salesTotal / $rate, "USD") }}</td>
                                            </tr>
                                            <tr>
                                                <td>SALE BALANCES</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>{{ currency_format_with_code($salesBalance / $rate, "USD")}}</td>
                                            </tr>
                                            <tr>
                                                <td>DOLLAR CONVERSION</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>OTHER INCOME </td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>


                            <div class="col-sm-12">
                                <h5>{{__('Expense')}}</h5>
                                <div class="table-responsive mt-4">
                                    <table class="table  mb-0" id="dataTable-manual">
                                        {{-- <thead>
                                            <tr>
                                                <th width="25%">{{__('Category')}}</th>
                                                <th>{{__('CASH')}}</th>
                                                <th>{{__('EQUITY KES')}}</th>
                                                <th>{{__('TOTAL (KES)')}}</th>
                                            </tr>
                                        </thead> --}}
                                        <tbody>
                                            <tr>
                                                <td>LESS:-</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>DAILY EXPENSES</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                            </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


