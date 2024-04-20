@extends('layouts.auth')
@section('page-title')
{{ __('Under Approval')}}
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center">
                <h2>CONTRACT SUBMITED SUCCESSFULLY</h2>
                <hr style="margin: 10px 0px;">
                <div class="text-left">
                    <p class="mb-3">your contract was submited successfully, and will be reviewed by the administrator.</p>
                    <p class="mb-3">after that your account will be activated, and you will be notified by email.</p>
                    <p class="mb-3">you can safely logout from this page.</p>
                    <p class="mb-3">Thank you for your patience.</p>
                </div>
                <hr style="margin: 10px 0px;">
                <a class="btn btn-secondary btn-block text-white" href="{{ route('logout.new') }}">Logout</a>
            </div>
        </div>
    </div>
</div>
@endsection