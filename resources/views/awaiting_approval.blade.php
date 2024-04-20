@extends('layouts.auth')
@section('page-title')
{{ __('Under Approval')}}
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form id="approval-form" method="POST" action="{{ route('approval.submit') }}" class="needs-validation" novalidate="" enctype="multipart/form-data">
                @csrf
                <div class="card-body text-center">
                    <h2>ONE FINAL STEP</h2>
                    <hr style="margin: 10px 0px;">
                    <a class="btn btn-success btn-block text-white mb-3 mt-3" href="https://drive.google.com/uc?export=download&id=1DMKSEFt_bpzlzi_6N-7nuvy1mTM1F_KP">Download Contract</a>
                    <p class="mb-3">Please download the contract, fill out all required fields, and upload the completed PDF below to complete the proccess.</p>
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
                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                    <hr style="margin: 0px 0px;">
                    <div class="row mt-4">
                        <div class="col-md-6 col-sm-12">
                            <!-- Logout button -->
                            <a class="btn btn-secondary btn-block text-white" href="{{ route('logout.new') }}">Logout</a>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <!-- Submit button -->
                            <button class="btn btn-primary btn-block" type="submit">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection