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
                            {{-- <tr>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Quantity') }}</th>
                            </tr> --}}
                            <tr>
                                <th >Lot No</th>
                                <th >BL No</th>
                                <th >Chasis No</th>
                                <th >{{__('Name')}}</th>
                                <th>Type</th>
                                <th >Colour</th>
                                <th >Fuel</th>
                                <th >Year</th>
                                <th >Engine CC</th>
                                <th >Engine No</th>
                                <th>Push Price</th>
                                <th>Purchased by</th>
                                <th>Purchased Status</th>
                                <th>Bid No</th>
                                <th>Bid Date</th>
                                <th>Status</th>
    
                                {{-- <th>{{__('Purchase Price')}}</th> --}}
                                {{-- <th>{{__('Tax')}}</th> --}}
                                
                                {{-- <th>{{__('Unit')}}</th> --}}
                                {{-- <th>{{__('Quantity')}}</th> --}}
                                {{-- <th>{{__('Type')}}</th> --}}
                               
                            </tr>
                            </thead>
                            <tbody>
                            
                            @foreach ($warehouse as $warehouses)
                                <tr class="font-style">
                                    
                                    <td>{{ !empty($warehouses->product()->purchase_product()->purchase())? $warehouses->product()->purchase_product()->purchase()->lot_number : '' }}</td>
                                    <td></td>
                                    {{-- <td>{{ !empty($warehouses->purchase_product()->purchase())? $warehouses->purchase_product()->purchase()->lot_number:'' }}</td> --}}
                                    <td>{{ !empty($warehouses->product())? $warehouses->product()->sku:'' }}</td>
                                    <td>{{ !empty($warehouses->product())? $warehouses->product()->name:'' }}</td>
                                    <td>{{ !empty($warehouses->product())? $warehouses->product()->category->name:'' }}</td>
                                    <td>{{ !empty($warehouses->product())? $warehouses->product()->colour:'' }}</td>
                                    <td>{{ !empty($warehouses->product())? $warehouses->product()->fuel:'' }}</td>
                                    <td>{{ !empty($warehouses->product())? $warehouses->product()->mfg_year:'' }}</td>
                                    <td>{{ !empty($warehouses->product())? $warehouses->product()->engine_cc:'' }}</td>
                                    <td>{{ !empty($warehouses->product())? $warehouses->product()->engine_no:'' }}</td>
                                    <td>{{ !empty($warehouses->product())? currency_format_with_sym($warehouses->product()->sale_price):'' }}</td>
                                    <td>{{ !empty($warehouses->product())? $warehouses->product()->purchased_by:'' }}</td>
                                    <td>{{ !empty($warehouses->product())? $warehouses->product()->purchased_status:'' }}</td>
                                    <td>{{ !empty($warehouses->product())? $warehouses->product()->bid_no:'' }}</td>
                                    <td>{{ !empty($warehouses->product())? $warehouses->product()->bid_date:'' }}</td>
                                    <td>{{ !empty($warehouses->product())? $warehouses->product()->vehicle_status:'' }}</td>
                                    {{-- <td>{{ $warehouses->quantity }}</td> --}}
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

