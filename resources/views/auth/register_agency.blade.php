@extends('layouts.auth')
@section('page-title')
    {{ __('Register') }}
@endsection
@section('language-bar')
<div class="lang-dropdown-only-desk">
    <li class="dropdown dash-h-item drp-language">
        <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="drp-text"> {{ Str::upper($lang) }}
            </span>
        </a>
        <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
            @foreach (languages() as $key => $language)
                <a href="{{ route('register.agency', $key) }}"
                    class="dropdown-item @if ($lang == $key) text-primary @endif">
                    <span>{{ Str::ucfirst($language) }}</span>
                </a>
            @endforeach
        </div>
    </li>
</div>
@endsection
@section('content')
    <div class="card" style="width: 100% !important;">
        <form method="POST" action="{{ route('register.agency') }}" class="needs-validation" novalidate="" enctype="multipart/form-data">
            @csrf
            <div class="card-body" style="max-width: 100% !important;">
                <div class="text-center">
                    <h2 class="mb-3 f-w-600">Register As Dealer</h2>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <!-- company name -->
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Company Name') }}</label>
                            <input id="company_name" type="text" class="form-control @error('company_name') is-invalid @enderror"
                                name="company_name" value="{{ old('company_name') }}" placeholder="Company Name" required autocomplete="company_name" autofocus>
                            @error('company_name')
                                <span class="error invalid-name text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!-- for pricing plan currently not used -->
                        <input type="hidden" name = "type" value="register" id="type">
                        <!-- company email -->
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" placeholder="email@example.com" required="" >
                            @error('email')
                                <span class="error invalid-email text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!-- company acc password -->
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control  @error('password') is-invalid @enderror"
                                name="password" required placeholder="Password" autocomplete="new-password">
                            @error('password')
                                <span class="error invalid-password text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!-- company acc confirm password -->
                        <div class="form-group">
                            <label class="form-label">{{ __('Confirm password') }}</label>
                            <input id="password-confirm" type="password"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
                            @error('password_confirmation')
                                <span class="error invalid-password_confirmation text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <!-- select from available users for relationship manager -->
                        {{-- <div class="form-group mb-3">
                            <label class="form-label">{{ __('Relationship Manager') }}</label>
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
                        </div> --}}
                         <!-- tax registration document -->
                         <div class="form-group mb-3">
                            <label class="form-label">{{ __('Tax Document') }}</label>
                            <input id="tax_registration" type="file" class="form-control @error('tax_registration') is-invalid @enderror"
                                name="tax_registration" placeholder="Select Tax Document" value="{{ old('tax_registration') }}" required autocomplete="tax_registration" autofocus>
                            @error('tax_registration')
                                <span class="error invalid-name text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!-- company whatsapp number -->
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Company Whatsapp') }}</label>
                            <input id="company_whatsapp" type="text" class="form-control @error('company_whatsapp') is-invalid @enderror"
                                name="company_whatsapp" placeholder="Company Whatsapp Number" value="{{ old('company_whatsapp') }}" required autocomplete="company_whatsapp" autofocus>
                            @error('company_whatsapp')
                                <span class="error invalid-name text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!-- dealer registration document -->
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Dealer Registration Document') }}</label>
                            <input id="dealer_registration" type="file" class="form-control @error('dealer_registration') is-invalid @enderror"
                                name="dealer_registration" placeholder="Select Dealer Registration Document" value="{{ old('dealer_registration') }}" required autocomplete="dealer_registration" autofocus>
                            @error('dealer_registration')
                                <span class="error invalid-name text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!-- trade license document -->
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Trade License Document') }}</label>
                            <input id="trade_license" type="file" class="form-control @error('trade_license') is-invalid @enderror"
                                name="trade_license" placeholder="Select Trade License Document" value="{{ old('trade_license') }}" required autocomplete="trade_license" autofocus>
                            @error('trade_license')
                                <span class="error invalid-name text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                       
                        <!-- passport copy document -->
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Copy of Passport') }}</label>
                            <input id="passport_copy" type="file" class="form-control @error('passport_copy') is-invalid @enderror"
                                name="passport_copy" placeholder="Select Copy of Passport" value="{{ old('passport_copy') }}" required autocomplete="passport_copy" autofocus>
                            @error('passport_copy')
                                <span class="error invalid-name text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!-- emirate document -->
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Emirate Document') }}</label>
                            <input id="emirate_document" type="file" class="form-control @error('emirate_document') is-invalid @enderror"
                                name="emirate_document" placeholder="Select Emirate Document" value="{{ old('emirate_document') }}" required autocomplete="emirate_document" autofocus>
                            @error('emirate_document')
                                <span class="error invalid-name text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!-- security deposit cheque copy -->
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Copy of Security Deposit Cheque') }}</label>
                            <input id="security_deposit_cheque_copy" type="file" class="form-control @error('security_deposit_cheque_copy') is-invalid @enderror"
                                name="security_deposit_cheque_copy" placeholder="Select Copy of Security Deposit Cheque" value="{{ old('security_deposit_cheque_copy') }}" required autocomplete="security_deposit_cheque_copy" autofocus>
                            @error('security_deposit_cheque_copy')
                                <span class="error invalid-name text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    @if (module_is_active('GoogleCaptcha') && admin_setting('google_recaptcha_is_on') == 'on')
                        <div class="form-group col-lg-12 col-md-12 mt-3">
                            {!! NoCaptcha::display() !!}
                            @error('g-recaptcha-response')
                                <span class="error small text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @endif
                    <div class="d-grid">
                        <button class="btn btn-primary btn-block mt-2" type="submit">{{ __('Register') }}</button>
                    </div>
                </div>
                <p class="mb-2 my-4 text-center">{{ __('Already have an account?') }} <a
                        href="{{ route('login', $lang) }}" class="f-w-400 text-primary">{{ __('Login') }}</a></p>
            </div>
        </form>
    </div>
@endsection
@push('custom-scripts')
    @if (module_is_active('GoogleCaptcha') && admin_setting('google_recaptcha_is_on') == 'on')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush
