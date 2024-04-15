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
<div style="margin: 40px 0px;">
    <form method="POST" action="{{ route('backend.dealers.update', $dealer->id) }}" class="needs-validation" novalidate="" enctype="multipart/form-data">
        @csrf
        <div id="main" class="row">
            <div class="col-md-6 col-sm-12">
                <!-- edit the dealer -->
                <div class="card">
                    <div class="card-body">
                        <h2>Edit Dealer</h4>
                        <hr>
                        <div id="sub-one" class="row">
                            <div class="col-md-6 col-sm-12">
                                <!-- dealer name -->
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Dealer Name') }} <span style="color: red;">*</span></label>
                                    <input id="dealer_name" type="text" class="form-control @error('dealer_name') is-invalid @enderror" name="dealer_name" value="{{ $dealer->company_name }}" required autocomplete="dealer_name" autofocus>
                                    @error('dealer_name')
                                        <span class="error invalid-name text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- dealer website -->
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Dealer Website') }}</label>
                                    <input id="dealer_website" type="text" class="form-control @error('dealer_website') is-invalid @enderror" name="dealer_website" value="{{ $dealer->website }}" required autocomplete="dealer_website" autofocus>
                                    @error('dealer_website')
                                        <span class="error invalid-name text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- dealer whatsapp -->
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Dealer Whatsapp') }} <span style="color: red;">*</span></label>
                                    <input id="dealer_whatsapp" type="text" class="form-control @error('dealer_whatsapp') is-invalid @enderror" name="dealer_whatsapp" value="{{ $dealer->company_whatsapp }}" required autocomplete="dealer_whatsapp" autofocus>
                                    @error('dealer_whatsapp')
                                        <span class="error invalid-name text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- dealer p.o box -->
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('P.O Box') }}</label>
                                    <input id="po_box" type="text" class="form-control @error('po_box') is-invalid @enderror" name="po_box" value="{{ $dealer->po_box }}" required autocomplete="po_box" autofocus>
                                    @error('po_box')
                                        <span class="error invalid-name text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <!-- general manager whatsapp -->
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('General Manager Whatsapp') }}</label>
                                    <input id="gm_whatsapp" type="text" class="form-control @error('gm_whatsapp') is-invalid @enderror" name="gm_whatsapp" value="{{ $dealer->GM_whatsapp }}" required autocomplete="gm_whatsapp" autofocus>
                                    @error('gm_whatsapp')
                                        <span class="error invalid-name text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- marketing director phone -->
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Marketing Director Phone') }}</label>
                                    <input id="marketing_director_no" type="text" class="form-control @error('marketing_director_no') is-invalid @enderror" name="marketing_director_no" value="{{ $dealer->marketing_director_no }}" required autocomplete="marketing_director_no" autofocus>
                                    @error('marketing_director_no')
                                        <span class="error invalid-name text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- select relationship manager from available users -->
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Relationship Manager') }} <span style="color: red;">*</span></label>
                                    <select name="relationship_manager" class="form-control @error('relationship_manager') is-invalid @enderror">
                                        <option value="">Select Relationship Manager</option>
                                        @foreach($relationshipManagers as $manager)
                                            <option value="{{ $manager->id }}" {{ $dealer->relational_manager == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('relationship_manager')
                                        <span class="error invalid-relationship_manager text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- working currency -->
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Currency') }}</label>
                                    <input id="currency" type="text" class="form-control @error('currency') is-invalid @enderror" name="currency" value="{{ $dealer->currency }}" required autocomplete="currency" autofocus>
                                    @error('currency')
                                        <span class="error invalid-name text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- dealer logo -->
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Dealer Logo') }}</label>
                            <div class="d-flex">
                                <div class="choose-files mt-2">
                                    <label for="dealer-logo-input" class="bg-primary">
                                        <div class=" bg-primary "><i class="ti ti-upload px-1"></i>{{ __('Choose Logo') }}</div>
                                    </label>
                                    <input type="file" accept="image/png, image/gif, image/jpeg, image/jpg" class="form-control @error('dealer_logo') is-invalid @enderror" name="dealer_logo" id="dealer-logo-input" onchange="previewImage(this)">
                                </div>
                                <img id="dealer-logo-preview" src="{{ url($dealer->logo) }}" alt="dealer-logo" class="rounded-circle img-thumbnail m-2 w-25">
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
            <div class="col-md-6 col-sm-12">
                <!-- edit bank account details of this dealer -->
                <div class="card">
                    <div class="card-body">
                        <h2>Edit Bank Details</h4>
                        <hr>
                        <div id="sub-one" class="row">
                            <div class="col-md-6 col-sm-12">
                                <!-- bank name -->
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Bank Name') }}</label>
                                    <input id="bank_name" type="text" class="form-control @error('bank_name') is-invalid @enderror" name="bank_name" value="{{ $dealer->bank_name }}" required autocomplete="bank_name" autofocus>
                                    @error('bank_name')
                                        <span class="error invalid-name text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- account name -->
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Account Name') }}</label>
                                    <input id="account_name" type="text" class="form-control @error('account_name') is-invalid @enderror" name="account_name" value="{{ $dealer->ac_name }}" required autocomplete="account_name" autofocus>
                                    @error('account_name')
                                        <span class="error invalid-name text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- bank address -->
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Bank Address') }}</label>
                                    <input id="bank_address" type="text" class="form-control @error('bank_address') is-invalid @enderror" name="bank_address" value="{{ $dealer->branch_address }}" required autocomplete="bank_address" autofocus>
                                    @error('bank_address')
                                        <span class="error invalid-name text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <!-- swift code -->
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Swift Code') }}</label>
                                    <input id="swift_code" type="text" class="form-control @error('swift_code') is-invalid @enderror" name="swift_code" value="{{ $dealer->swift_code }}" required autocomplete="swift_code" autofocus>
                                    @error('swift_code')
                                        <span class="error invalid-name text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- international bank account number -->
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Account Number') }}</label>
                                    <input id="iban" type="text" class="form-control @error('iban') is-invalid @enderror" name="iban" value="{{ $dealer->iban }}" required autocomplete="iban" autofocus>
                                    @error('iban')
                                        <span class="error invalid-name text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- branch name -->
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ __('Branch Name') }}</label>
                                    <input id="branch_name" type="text" class="form-control @error('branch_name') is-invalid @enderror" name="branch_name" value="{{ $dealer->branch_name }}" required autocomplete="branch_name" autofocus>
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
                <!-- change documents -->
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