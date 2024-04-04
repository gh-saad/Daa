@extends('layouts.main')
@php
    if(Auth::user()->type=='super admin')
    {
        $plural_name = __('Customers');
        $singular_name = __('Customer');
    }
    else{

        $plural_name =__('Dealers');
        $singular_name =__('Dealer');
    }
@endphp

@section('page-title')
    {{$plural_name }}
@endsection

@section('page-breadcrumb')
    {{__('Edit')}}
@endsection

@section('page-action')
    <!-- leave empty -->
@endsection

@section('content')
    <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <!-- submitable form to edit the request -->
                        <h2>Edit Request</h4>
                        <hr>
                        
                    </div>
                </div>
            </div>
        </div>
    <!-- [ Main Content ] end -->
@endsection