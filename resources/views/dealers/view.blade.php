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
    {{__('View')}}
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
                        <!-- show information of the request in a vertical table -->
                        <h2>Dealer Request Information 
                            <!-- add an if condition if @dealer has status -->
                            @if ($dealer->status == 'Rejected')
                                <span class="badge bg-danger p-2 px-3 rounded rounded" style="margin:0px 10px; text-transform: uppercase;">rejected</span>
                            @elseif ($dealer->status == 'Approved')
                                <span class="badge bg-primary p-2 px-3 rounded rounded" style="margin:0px 10px; text-transform: uppercase;">approved</span>
                            @else
                                <span class="badge bg-warning p-2 px-3 rounded rounded" style="margin:0px 10px; text-transform: uppercase;">pending</span>
                            @endif
                        </h2>
                        <hr>
                        <h5><b>Request Submitted by: </b>{{ $dealer->user->name }} - {{ $dealer->user->email }}<h5>
                        <h5><b>Company Name: </b>{{ $dealer->company_name }}<h5>
                    </div>
                </div>
            </div>
        </div>
    <!-- [ Main Content ] end -->
@endsection