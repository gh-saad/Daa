<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{company_setting('site_rtl') == 'on'?'rtl':''}}">
    <head>

        <title>@yield('page-title') | {{ !empty(company_setting('title_text')) ? company_setting('title_text') : (!empty(admin_setting('title_text')) ? admin_setting('title_text') :'DAA ERP') }}</title>

        <meta name="title" content="{{ !empty(admin_setting('meta_title')) ? admin_setting('meta_title') : 'DAA ERP' }}">
        <meta name="keywords" content="{{ !empty(admin_setting('meta_keywords')) ? admin_setting('meta_keywords') : 'DAA ERP,SaaS solution,Multi-workspace' }}">
        <meta name="description" content="{{ !empty(admin_setting('meta_description')) ? admin_setting('meta_description') : 'Discover the efficiency of Dash, a user-friendly web application by Rajodiya Apps.'}}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ env('APP_URL') }}">
        <meta property="og:title" content="{{ !empty(admin_setting('meta_title')) ? admin_setting('meta_title') : 'DAA ERP' }}">
        <meta property="og:description" content="{{ !empty(admin_setting('meta_description')) ? admin_setting('meta_description') : 'Discover the efficiency of Dash, a user-friendly web application by Rajodiya Apps.'}} ">
        <meta property="og:image" content="{{ get_file( (!empty(admin_setting('meta_image'))) ? (check_file(admin_setting('meta_image'))) ?  admin_setting('meta_image') : 'uploads/meta/meta_image.png' : 'uploads/meta/meta_image.png'  ) }}{{'?'.time() }}">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ env('APP_URL') }}">
        <meta property="twitter:title" content="{{ !empty(admin_setting('meta_title')) ? admin_setting('meta_title') : 'DAA ERP' }}">
        <meta property="twitter:description" content="{{ !empty(admin_setting('meta_description')) ? admin_setting('meta_description') : 'Discover the efficiency of Dash, a user-friendly web application by Rajodiya Apps.'}} ">
        <meta property="twitter:image" content="{{ get_file( (!empty(admin_setting('meta_image'))) ? (check_file(admin_setting('meta_image'))) ?  admin_setting('meta_image') : 'uploads/meta/meta_image.png' : 'uploads/meta/meta_image.png'  ) }}{{'?'.time() }}">

        <meta name="author" content="DAA ERP.io">

        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="url" content="{{ url('').'/'.config('chatify.routes.prefix') }}" data-user="{{ Auth::user()->id }}">

        <!-- Favicon icon -->
        <link rel="icon" href="{{ get_file(favicon())}}{{'?'.time()}}" type="image/x-icon" />

        <!-- font css -->
        <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/fonts/material.css')}}">

        <!-- vendor css -->
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/datepicker-bs5.min.css') }}" >
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}" >
        <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custome.css') }}">
        @if (company_setting('site_rtl') == 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
        @endif

        @if(company_setting('cust_darklayout') == 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}" id="main-style-link">
        @endif
        @if (company_setting('site_rtl') != 'on' && company_setting('cust_darklayout') != 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
        @else
            <link rel="stylesheet" href="" id="main-style-link">
        @endif

        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        @stack('css')
        <style>
            /* Custom switch size */
            .large-switch {
                width: 60px;
                height: 34px;
            }

            .large-switch:checked {
                background-color: #0d6efd;
            }

            .large-switch::before {
                width: 28px;
                height: 28px;
                transform: translateX(4px);
            }

            .large-switch:checked::before {
                transform: translateX(30px);
            }
        </style>
        @stack('availabilitylink')
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <link rel='stylesheet' href='https://unpkg.com/nprogress@0.2.0/nprogress.css'/>
        <script src='https://unpkg.com/nprogress@0.2.0/nprogress.js'></script>
        <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    </head>
        <body class="{{ !empty(company_setting('color'))?company_setting('color'):'theme-1' }}">
        <!-- [ Pre-loader ] start -->
        <div class="loader-bg">
            <div class="loader-track">
            <div class="loader-fill">

            </div>
            </div>
        </div>
  <!-- [ Pre-loader ] End -->
        <!-- [ auth-signup ] end -->
        @include('partials.sidebar')
        @include('partials.header')
        <section class="dash-container">
            <div class="dash-content">
                <!-- [ breadcrumb ] start -->
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-auto">
                                <div class="page-header-title">
                                    <h4 class="m-b-10">@yield('page-title')</h4>
                                </div>
                                <ul class="breadcrumb">
                                    @php
                                        if(isset(app()->view->getSections()['page-breadcrumb']))
                                        {
                                            $breadcrumb = explode(',',app()->view->getSections()['page-breadcrumb']);
                                        }else{
                                            $breadcrumb =[];
                                        }
                                    @endphp
                                    @if(!empty($breadcrumb))
                                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                                      @foreach ($breadcrumb as $item)
                                        <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">{{ $item }}</li>
                                      @endforeach
                                    @endif

                                </ul>
                            </div>
                            <div class="col-auto row">
                                @yield('page-action')
                            </div>
                        </div>
                    </div>
                </div>
                @yield('content')
            </div>
        </section>
        <footer class="dash-footer">
            <div class="footer-wrapper">
              <div class="py-1">
                <span class="text-muted">@if (!empty(company_setting('footer_text'))) {{company_setting('footer_text')}} @elseif(!empty(admin_setting('footer_text'))) {{admin_setting('footer_text')}} @else {{__('Copyright')}} &copy; {{ config('app.name', 'WorkGo') }}@endif{{date('Y')}}</span>
              </div>
            </div>
          </footer>
        @if(Route::currentRouteName() !== 'chatify')
        <div  id="commonModal" class="modal" tabindex="-1" aria-labelledby="exampleModalLongTitle" aria-modal="true" role="dialog" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="body">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="commonModalOver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="body">
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="loader-wrapper d-none">
            <span class="site-loader"> </span>
        </div>
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
            <div id="liveToast" class="toast text-white  fade" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body"> </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <!-- Required Js -->


        <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
        <script src="{{asset('assets/js/plugins/tinymce/tinymce.min.js')}}"></script>
        <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/js/dash.js') }}"></script>
        <script src="{{asset('assets/js/plugins/simple-datatables.js')}}"></script>
        <script src="{{ asset('assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/datepicker-full.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/flatpickr.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
        <script src="{{ asset('js/jquery.form.js') }}"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script src="{{ asset('js/custom.js') }}"></script>
        @if($message = Session::get('success'))
            <script>
                toastrs('Success', '{!! $message !!}', 'success');
            </script>
        @endif
        @if($message = Session::get('error'))
            <script>
                toastrs('Error', '{!! $message !!}', 'error');
            </script>
        @endif
        @stack('scripts')
        @include('Chatify::layouts.footerLinks')

        @if(admin_setting('enable_cookie') == 'on')
            @include('layouts.cookie_consent')
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', (event) => {

                // Initialize Select2 function
                function initializeSelect2(selector) {
                    $(selector).select2({
                        width: '100%' // Adjust as needed
                    });
                }

                initializeSelect2('.select2');
            });
        </script>

        <script>
            // ajax
            $('#sync-btn').click(function() {
                $('#sync-btn i').addClass('ti-refresh-animate');
                fetch("{{url('synchronizer/get-data')}}").then(response => {
                    if (!response.ok) {
                        $('#sync-btn i').removeClass('ti-refresh-animate');
                        toastrs('Error', 'Network response was not ok', 'error');
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                }).then(data => {
                    $('#sync-btn i').removeClass('ti-refresh-animate');
                    if (data.status =='ok') {
                        toastrs('Success', 'System is up to date :)', 'success');
                    }else{
                        toastrs('Error', 'API Is not Working', 'error');
                    }
                }).catch(error => {
                    $('#sync-btn i').removeClass('ti-refresh-animate');
                    toastrs('Error', 'There was a problem with the fetch operation:', 'error');
                    console.error(error);
                });
                
            });
        </script>

        <!-- static rate script -->
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Store the initial rates for each currency
                const initialRates = {
                    'KES': '{{ currency("KES")->rate }}',
                    'AED': '{{ currency("AED")->rate }}',
                    'EUR': '{{ currency("EUR")->rate }}'
                };

                // Select all rate type checkboxes
                const rateTypeCheckboxes = document.querySelectorAll('.form-check-input');

                // Loop through checkboxes and attach event listeners
                rateTypeCheckboxes.forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        // Get corresponding input based on checkbox id
                        const rateInputId = checkbox.id.replace('rate-type', 'currency-rate');
                        const rateInput = document.getElementById(rateInputId);

                        // Extract the currency code (KES, AED, EUR, etc.) from the input id
                        const currencyCode = rateInputId.split('-')[0].toUpperCase();

                        if (checkbox.checked) {
                            rateInput.removeAttribute('readonly');
                            rateInput.disabled = false;
                        } else {
                            rateInput.setAttribute('readonly', true);
                            rateInput.disabled = true;

                            // Reset the input value to the original rate for that currency
                            rateInput.value = initialRates[currencyCode];
                        }
                    });
                });
            });
        </script>
    </body>
</html>
