@extends('layouts.auth')
@section('page-title')
    {{ __('Register Type') }}
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
                <a href="{{ route('register', $key) }}"
                    class="dropdown-item @if ($lang == $key) text-primary @endif">
                    <span>{{ Str::ucfirst($language) }}</span>
                </a>
            @endforeach
        </div>
    </li>
</div>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div>
                <h2 class="mb-3 f-w-600">{{ __('Do You Want to Register as Agency or Agent?') }}</h2>
                <small>Register as <b>Dealer</b> reguires all company documents for example: trade license, Bank details etc..</small>
                <hr>
                <div class="d-flex justify-content-around">
                    <a href="{{ route('register.agency') }}" class="btn btn-lg btn-primary btn-icon">
                        <i class="fa fa-users"></i>
                        <span class="text-white" style="text-transform: uppercase;">Dealer</span>
                    </a>
                    <a href="{{ route('register.agent') }}" class="btn btn-lg btn-primary btn-icon">
                        <i class="fa fa-user"></i>
                        <span class="text-white" style="text-transform: uppercase;">Agent</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('custom-scripts')
    @if (module_is_active('GoogleCaptcha') && admin_setting('google_recaptcha_is_on') == 'on')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush
