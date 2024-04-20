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
    {{ __('Create Dealer') }}
@endsection

@section('page-action')
    <!-- leave empty -->
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div style="margin: 40px 0px;">
        <form method="POST" action="{{ route('backend.dealers.store') }}" class="needs-validation" novalidate="" enctype="multipart/form-data">
            @csrf
            <div id="main" class="row">
                <div class="col-md-6 col-sm-12">
                    <!-- add details for the user that needs to be associated with this dealer -->
                    <div class="card">
                        <div class="card-body">
                            <h2>Personal Details</h4>
                            <hr>
                            <div id="sub" class="row">
                                <div class="col-md-6 col-sm-12">
                                    <!-- user name -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Name') }} <span style="color: red;">*</span></label>
                                        <input id="user_name" type="text" class="form-control @error('user_name') is-invalid @enderror" name="user_name" placeholder="Enter Name" required autocomplete="user_name" autofocus>
                                        @error('user_name')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- email -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Email') }} <span style="color: red;">*</span></label>
                                        <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror" name="email" placeholder="email@example.com" required="">
                                        @error('email')
                                            <span class="error invalid-email text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- password -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Password') }} <span style="color: red;">*</span></label>
                                        <input id="password" type="password" class="form-control  @error('password') is-invalid @enderror" name="password" required placeholder="Enter Password" autocomplete="new-password">
                                        @error('password')
                                            <span class="error invalid-password text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- confirm password -->
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Confirm password') }} <span style="color: red;">*</span></label>
                                        <input id="password-confirm" type="password"
                                            class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" placeholder="Confirm Entered Password" required autocomplete="new-password">
                                        @error('password_confirmation')
                                            <span class="error invalid-password_confirmation text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <!-- workspace name -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Workspace Name') }} <span style="color: red;">*</span></label>
                                        <input id="workspace_name" type="text" class="form-control @error('workspace_name') is-invalid @enderror" name="workspace_name" placeholder="Enter Workspace Name" required autocomplete="workspace_name" autofocus>
                                        @error('workspace_name')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- country -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Country') }}</label>
                                        <input id="country" type="text" class="form-control @error('country') is-invalid @enderror" name="country" placeholder="Enter Country" required autocomplete="country" autofocus>
                                        @error('country')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- phone -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Phone') }}</label>
                                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="Enter Phone" required autocomplete="phone" autofocus>
                                        @error('phone')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- address -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Address') }}</label>
                                        <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" placeholder="Enter Address" required autocomplete="address" autofocus>
                                        @error('address')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- add details for the dealer -->
                    <div class="card">
                        <div class="card-body">
                            <h2>Dealer Details</h4>
                            <hr>
                            <div id="sub-two" class="row">
                                <div class="col-md-6 col-sm-12">
                                    <!-- dealer name -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Dealer Name') }} <span style="color: red;">*</span></label>
                                        <input id="dealer_name" type="text" class="form-control @error('dealer_name') is-invalid @enderror" name="dealer_name" placeholder="Enter Dealer Name" required autocomplete="dealer_name" autofocus>
                                        @error('dealer_name')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- dealer website -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Dealer Website') }}</label>
                                        <input id="dealer_website" type="text" class="form-control @error('dealer_website') is-invalid @enderror" name="dealer_website" placeholder="Enter Dealer Website" required autocomplete="dealer_website" autofocus>
                                        @error('dealer_website')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- dealer whatsapp -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Dealer Whatsapp') }} <span style="color: red;">*</span></label>
                                        <input id="dealer_whatsapp" type="text" class="form-control @error('dealer_whatsapp') is-invalid @enderror" name="dealer_whatsapp" placeholder="Enter Dealer Whatsapp" required autocomplete="dealer_whatsapp" autofocus>
                                        @error('dealer_whatsapp')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <!-- select relationship manager from available users -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Relationship Manager') }} <span style="color: red;">*</span></label>
                                        <select id="relationship_manager" name="relationship_manager" class="form-control @error('relationship_manager') is-invalid @enderror">
                                            <option value="">Select Relationship Manager</option>
                                            @foreach($relationshipManagers as $manager)
                                                <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('relationship_manager')
                                            <span class="error invalid-relationship_manager text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- P.O Box -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('P.O Box') }}</label>
                                        <input id="p_o_box" type="number" class="form-control @error('p_o_box') is-invalid @enderror" name="p_o_box" placeholder="Enter P.O Box Number" required autocomplete="p_o_box" autofocus>
                                        @error('p_o_box')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- dealer logo -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Dealer Logo') }}</label>
                                        <div class="d-flex">
                                            <div class="choose-files mt-2">
                                                <label for="dealer-logo-input" class="bg-primary">
                                                    <div class=" bg-primary "><i class="ti ti-upload px-1"></i>{{ __('Choose Logo') }}</div>
                                                </label>
                                                <input type="file" accept="image/png, image/gif, image/jpeg, image/jpg" class="form-control" name="dealer_logo" id="dealer-logo-input" onchange="previewImage(this)">
                                            </div>
                                            <img id="dealer-logo-preview" src="{{ asset('uploads/dealer-logos/dealer1.png') }}" alt="dealer-logo" class="rounded-circle img-thumbnail m-2 w-25">
                                        </div>
                                        @error('dealer_logo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <!-- add bank details of the dealer -->
                    <div class="card">
                        <div class="card-body">
                            <h2>Bank Account Details</h4>
                            <hr>
                            <div id="sub-two" class="row">
                                <div class="col-md-6 col-sm-12">
                                    <!-- account holder name -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Account Holder Name') }}</label>
                                        <input id="account_holder_name" type="text" class="form-control @error('account_holder_name') is-invalid @enderror" name="account_holder_name" placeholder="Enter Account Holder Name" required autocomplete="account_holder_name" autofocus>
                                        @error('account_holder_name')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- bank name -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Bank Name') }}</label>
                                        <input id="bank_name" type="text" class="form-control @error('bank_name') is-invalid @enderror" name="bank_name" placeholder="Enter Bank Name" required autocomplete="bank_name" autofocus>
                                        @error('bank_name')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- bank address -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Bank Address') }}</label>
                                        <input id="bank_address" type="text" class="form-control @error('bank_address') is-invalid @enderror" name="bank_address" placeholder="Enter Bank Address" required autocomplete="bank_address" autofocus>
                                        @error('bank_address')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <!-- account number -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Account Number') }}</label>
                                        <input id="account_number" type="number" class="form-control @error('account_number') is-invalid @enderror" name="account_number" placeholder="Enter Account Number" required autocomplete="account_number" autofocus>
                                        @error('account_number')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- swift code -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Swift Code') }}</label>
                                        <input id="swift_code" type="text" class="form-control @error('swift_code') is-invalid @enderror" name="swift_code" placeholder="Enter Swift Code" required autocomplete="swift_code" autofocus>
                                        @error('swift_code')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- branch name -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Branch Name') }}</label>
                                        <input id="branch_name" type="text" class="form-control @error('branch_name') is-invalid @enderror" name="branch_name" placeholder="Enter Branch Name" required autocomplete="branch_name" autofocus>
                                        @error('branch_name')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- add documents -->
                    <div class="card">
                        <div class="card-body">
                            <h2>Documents</h4>
                            <hr>
                            <div id="sub-two" class="row">
                                <div class="col-md-6 col-sm-12">
                                    <!-- dealer document -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Dealer Registration Document') }} <span style="color: red;">*</span></label>
                                        <input id="dealer_document" type="file" class="form-control @error('dealer_document') is-invalid @enderror" name="dealer_document" placeholder="Enter Dealer Registration Document" required autocomplete="dealer_document" autofocus>
                                        @error('dealer_document')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- passport copy -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Copy of Passport') }} <span style="color: red;">*</span></label>
                                        <input id="passport_copy" type="file" class="form-control @error('passport_copy') is-invalid @enderror" name="passport_copy" placeholder="Enter Copy of Passport" required autocomplete="passport_copy" autofocus>
                                        @error('passport_copy')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- trade license document -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Trade License Document') }} <span style="color: red;">*</span></label>
                                        <input id="trade_license_document" type="file" class="form-control @error('trade_license_document') is-invalid @enderror" name="trade_license_document" placeholder="Enter Trade License Document" required autocomplete="trade_license_document" autofocus>
                                        @error('trade_license_document')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <!-- emirates document -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Emirates Document') }} <span style="color: red;">*</span></label>
                                        <input id="emirates_document" type="file" class="form-control @error('emirates_document') is-invalid @enderror" name="emirates_document" placeholder="Enter Emirates Document" required autocomplete="emirates_document" autofocus>
                                        @error('emirates_document')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- tax document -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Tax Document') }} <span style="color: red;">*</span></label>
                                        <input id="tax_document" type="file" class="form-control @error('tax_document') is-invalid @enderror" name="tax_document" placeholder="Enter Tax Document" required autocomplete="tax_document" autofocus>
                                        @error('tax_document')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- security deposit cheque copy -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Copy of Security Deposit Cheque') }} <span style="color: red;">*</span></label>
                                        <input id="security_deposit_cheque_copy" type="file" class="form-control @error('security_deposit_cheque_copy') is-invalid @enderror" name="security_deposit_cheque_copy" placeholder="Enter Copy of Security Deposit Cheque" required autocomplete="security_deposit_cheque_copy" autofocus>
                                        @error('security_deposit_cheque_copy')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!-- contract -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Contract') }} <span style="color: red;">*</span></label>
                                        <input id="contract" type="file" class="form-control @error('contract') is-invalid @enderror" name="contract" placeholder="Enter Contract" required autocomplete="contract" autofocus>
                                        @error('contract')
                                            <span class="error invalid-name text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary btn-block mt-2" type="submit">{{ __('Submit Form') }}</button>
            </div>
        </form>
    </div>
    <!-- [ Main Content ] end -->
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#dealer-logo-preview')
                    .attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush