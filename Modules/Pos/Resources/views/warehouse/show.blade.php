@extends('layouts.main')

@section('page-title')
    {{__('Warehouse Stock Details')}}
@endsection

@push('scripts')
@endpush

@section('page-breadcrumb')
   {{__('Warehouse Stock Details')}}
@endsection

@section('action-btn')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th>Lot No</th>
                                    <th>BL No</th>
                                    <th>Chasis No</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Colour</th>
                                    <th>Fuel</th>
                                    <th>Year</th>
                                    <th>Engine CC</th>
                                    <th>Engine No</th>
                                    <th>Push Price</th>
                                    <th>Purchased from</th>
                                    <th>Purchased Status</th>
                                    <th>Bid No</th>
                                    <th>Bid Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($warehouseProducts as $warehouses)
                                    <tr class="font-style">
                                        <td>{{ !empty($warehouses->product()->purchase_product()->purchase())? $warehouses->product()->purchase_product()->purchase()->lot_number : '' }}</td>
                                        <td>{{ !empty($warehouses->product()->purchase_product()->purchase())? $warehouses->product()->purchase_product()->purchase()->bl_number : '' }}</td>
                                        <td>{{ !empty($warehouses->product())? $warehouses->product()->sku:'' }}</td>
                                        <td>{{ !empty($warehouses->product())? $warehouses->product()->name:'' }}</td>
                                        <td>{{ !empty($warehouses->product())? $warehouses->product()->category->name:'' }}</td>
                                        <td>{{ !empty($warehouses->product())? $warehouses->product()->colour:'' }}</td>
                                        <td>{{ !empty($warehouses->product())? $warehouses->product()->fuel:'' }}</td>
                                        <td>{{ !empty($warehouses->product())? $warehouses->product()->mfg_year:'' }}</td>
                                        <td>{{ !empty($warehouses->product())? $warehouses->product()->engine_cc:'' }}</td>
                                        <td>{{ !empty($warehouses->product())? $warehouses->product()->engine_no:'' }}</td>
                                        <td>{{ !empty($warehouses->product()->purchase_product())? number_format(currency_conversion($warehouses->product()->purchase_product()->getPriceAfterDiscount(), $warehouses->product()->purchase_product()->currency, company_setting('defult_currancy')), 2) . ' ' . company_setting('defult_currancy') :'' }}</td>
                                        <td>{{ !empty($warehouses->product())? $warehouses->product()->purchased_from:'' }}</td>
                                        <td>{{ !empty($warehouses->product())? $warehouses->product()->purchased_status:'' }}</td>
                                        <td>{{ !empty($warehouses->product())? $warehouses->product()->bid_no:'' }}</td>
                                        <td>{{ !empty($warehouses->product())? $warehouses->product()->bid_date:'' }}</td>
                                        <td>{{ !empty($warehouses->product())? $warehouses->product()->vehicle_status:'' }}</td>
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

