@extends('layouts.auth')
@section('page-title')
{{ __('Under Approval')}}
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form method="POST" action="{{ route('register.agency') }}" class="needs-validation" novalidate="" enctype="multipart/form-data">
                @csrf
                <div class="card-body text-center">
                    <h2>ONE FINAL STEP</h2>
                    <h4>-Check your Email-</h4>
                    <hr>
                    <p>Your account is currently pending approval by the administrator. An email has been sent to you with an agreement contract attached as a PDF file.</p>
                    <p>Please download the contract, fill out all required fields, and upload the completed PDF below to complete the process.</p>
                    <!-- contract document -->
                    <div class="form-group">
                        <input id="contract" type="file" class="form-control @error('contract') is-invalid @enderror"
                            name="contract" placeholder="Select Contract" required autocomplete="contract" autofocus>
                        @error('contract')
                            <span class="error invalid-name text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <hr style="margin: 0px 0px;">
                    <div class="row mt-4">
                        <div class="col-md-6 col-sm-12">
                            <button class="btn btn-secondary btn-block" type="cancel">Cancel</button>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <button class="btn btn-primary btn-block" type="submit">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
