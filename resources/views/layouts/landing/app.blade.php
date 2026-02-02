@php($backgroundChange = \App\Models\BusinessSetting::where(['key' => 'backgroundChange'])->first())
@php($backgroundChange = isset($backgroundChange->value) ? json_decode($backgroundChange->value, true) : [])

<!DOCTYPE html>
<?php
    $toggle_dm_registration = \App\Models\BusinessSetting::where(['key' => 'toggle_dm_registration'])->first()->value ?? '0';
    $toggle_store_registration = \App\Models\BusinessSetting::where(['key' => 'toggle_store_registration'])->first()->value ?? '0';
    
    // Optimized direction logic: check session first, fallback to DB, default to 'ltr'
    $landing_site_direction = session()->get('landing_site_direction') ?? \App\Models\BusinessSetting::where('key', 'landing_site_direction')->first()->value ?? 'ltr';
?>
<html dir="{{ $landing_site_direction }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>@yield('title')</title>

    <link rel="stylesheet" href="{{ asset('public/assets/landing/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/landing/css/customize-animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/landing/css/odometer.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/landing/css/owl.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/toastr.css') }}">
<link rel="stylesheet" href="{{ asset('public/assets/landing/css/select2.min.css') }}">
<img src="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}">
    @php($icon = \App\Models\BusinessSetting::where(['key' => 'icon'])->first()?->value ?? null)
    <link rel="icon" type="image/x-icon" href="{{ $icon ? asset('storage/business/' . $icon) : asset('public/assets/landing/img/favicon.svg') }}">
   <link rel="stylesheet" href="{{ asset('public/assets/landing/css/main.css') }}" />

    @stack('css_or_js')

    @if (isset($backgroundChange['primary_1_hex']) && isset($backgroundChange['primary_2_hex']))
<style>
    :root {
        --primary-color: #{{ $settings->where('key', 'primary_color')->first()->value }};
    }
</style>
    @endif
</head>

<body>
    @php($landing_page_text = json_decode(\App\Models\BusinessSetting::where(['key' => 'landing_page_text'])->first()->value ?? '{}', true))
    @php($landing_page_links = json_decode(\App\Models\BusinessSetting::where(['key' => 'landing_page_links'])->first()->value ?? '{}', true))
    
    <div id="landing-loader"></div>

    <header>
        <div class="navbar-bottom">
            <div class="container">
                <div class="navbar-bottom-wrapper">
                    @php($logo = \App\Models\BusinessSetting::where(['key' => 'logo'])->first()->value ?? null)
                    <a href="{{route('home')}}" class="logo">
                        {{-- Logo space can be filled here if needed --}}
                    </a>
                    <ul class="menu">
                        <li><a href="{{route('home')}}" class="{{ Request::is('/') ? 'active' : '' }}"><span>{{ translate('messages.home') }}</span></a></li>
                        <li><a href="{{route('about-us')}}" class="{{ Request::is('about-us') ? 'active' : '' }}"><span>{{ translate('messages.about_us') }}</span></a></li>
                        <li><a href="{{route('privacy-policy')}}" class="{{ Request::is('privacy-policy') ? 'active' : '' }}"><span>{{ translate('messages.privacy_policy') }}</span></a></li>
                        <li><a href="{{route('terms-and-conditions')}}" class="{{ Request::is('terms-and-conditions') ? 'active' : '' }}"><span>{{ translate('messages.terms_and_condition') }}</span></a></li>
                        <li><a href="{{route('contact-us')}}" class="{{ Request::is('contact-us') ? 'active' : '' }}"><span>{{ translate('messages.contact_us') }}</span></a></li>
                        
                        @if (isset($landing_page_links['web_app_url_status']) && $landing_page_links['web_app_url_status'])
                            <div class="mt-2">
                                <a class="cmn--btn me-xl-auto py-2" href="{{ $landing_page_links['web_app_url'] }}" target="_blank">{{ translate('messages.browse_web') }}</a>
                            </div>
                        @endif
                    </ul>

                    <div class="nav-toggle d-lg-none ms-auto me-3">
                        <span></span><span></span><span></span>
                    </div>

                    @php($local = session()->has('landing_local') ? session('landing_local') : 'en')
                    @php($lang_setting = \App\Models\BusinessSetting::where('key', 'system_language')->first())
                    
                    @if ($lang_setting)
                        <div class="dropdown--btn-hover position-relative">
                            <a class="dropdown--btn border-0 px-3 header--btn text-capitalize d-flex" href="javascript:void(0)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="20">
                                    <path d="M16.555 5.412a8.028 8.028 0 00-3.503-2.81 14.899 14.899 0 011.663 4.472 8.547 8.547 0 001.84-1.662zM13.326 7.825a13.43 13.43 0 00-2.413-5.773 8.087 8.087 0 00-1.826 0 13.43 13.43 0 00-2.413 5.773A8.473 8.473 0 0010 8.5c1.18 0 2.304-.24 3.326-.675zM6.514 9.376A9.98 9.98 0 0010 10c1.226 0 2.4-.22 3.486-.624a13.54 13.54 0 01-.351 3.759A13.54 13.54 0 0110 13.5c-1.079 0-2.128-.127-3.134-.366a13.538 13.538 0 01-.352-3.758zM5.285 7.074a14.9 14.9 0 011.663-4.471 8.028 8.028 0 00-3.503 2.81c.529.638 1.149 1.199 1.84 1.66zM17.334 6.798a7.973 7.973 0 01.614 4.115 13.47 13.47 0 01-3.178 1.72 15.093 15.093 0 00.174-3.939 10.043 10.043 0 002.39-1.896zM2.666 6.798a10.042 10.042 0 002.39 1.896 15.196 15.196 0 00.174 3.94 13.472 13.472 0 01-3.178-1.72 7.973 7.973 0 01.615-4.115zM10 15c.898 0 1.778-.079 2.633-.23a13.473 13.473 0 01-1.72 3.178 8.099 8.099 0 01-1.826 0 13.47 13.47 0 01-1.72-3.178c.855.151 1.735.23 2.633.23zM14.357 14.357a14.912 14.912 0 01-1.305 3.04 8.027 8.027 0 004.345-4.345c-.953.542-1.971.981-3.04 1.305zM6.948 17.397a8.027 8.027 0 01-4.345-4.345c.953.542 1.971.981 3.04 1.305a14.912 14.912 0 001.305 3.04z" />
                                </svg>
                                @foreach(json_decode($lang_setting['value'], true) as $data)
                                    @if($data['code'] == $local)
                                        <span class="ms-1">{{$data['code']}}</span>
                                    @endif
                                @endforeach
                            </a>
                            <ul class="dropdown-list py-0" style="min-width:120px; top:100%">
                                @foreach(json_decode($lang_setting['value'], true) as $data)
                                    @if($data['status'] == 1)
                                        <li class="py-0"><a href="{{route('lang',[$data['code']])}}">{{$data['code']}}</a></li>
                                        <li><hr class="dropdown-divider my-0"></li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($toggle_dm_registration == '1' || $toggle_store_registration == '1')
                        <div class="dropdown--btn-hover position-relative">
                            <a class="dropdown--btn header--btn text-capitalize d-flex align-items-center" href="javascript:void(0)">
                                <span class="me-1">{{ translate('Join us') }}</span>
                                <svg width="12" height="7" viewBox="0 0 12 7" fill="none"><path d="M6.00224 5.46105L1.33333 0.415128C1.21002 0.290383 1 0.0787335 1 0.0787335C1 0.0787335 0.708488 -0.0458817 0.584976 0.0788632L0.191805 0.475841C0.0680976 0.600389 7.43292e-08 0.766881 7.22135e-08 0.9443C7.00978e-08 1.12172 0.0680976 1.28801 0.191805 1.41266L5.53678 6.80682C5.66068 6.93196 5.82624 7.00049 6.00224 7C6.17902 7.00049 6.34439 6.93206 6.46839 6.80682L11.8082 1.41768C11.9319 1.29303 12 1.12674 12 0.949223C12 0.771804 11.9319 0.605509 11.8082 0.480765L11.415 0.0838844C11.1591 -0.174368 10.9225 0.222512 10.6667 0.480765L6.00224 5.46105Z" fill="#000000" /></svg>
                            </a>
                            <ul class="dropdown-list">
                                @if ($toggle_store_registration == '1')
                                    <li><a href="{{ route('restaurant.create') }}">{{ translate('messages.store_registration') }}</a></li>
                                    @if ($toggle_dm_registration == '1') <li><hr class="dropdown-divider"></li> @endif
                                @endif
                                @if ($toggle_dm_registration == '1')
                                    <li><a href="{{ route('deliveryman.create') }}">{{ translate('messages.deliveryman_registration') }}</a></li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </header>

    @yield('content')

    <footer>
        @php($footer_data = \App\Models\DataSetting::where(['type' => 'admin_landing_page'])->whereIn('key', ['fixed_newsletter_title','fixed_newsletter_sub_title','fixed_footer_article_title'])->pluck('value','key'))
        
        <div class="newsletter-section">
            <div class="container">
                <div class="newsletter-wrapper">
                    <div class="newsletter-content position-relative">
                        <h3 class="title">{{ $footer_data['fixed_newsletter_title'] ?? '' }}</h3>
                        <div class="text">{{ $footer_data['fixed_newsletter_sub_title'] ?? '' }}</div>
                        <form method="post" action="{{route('newsletter.subscribe')}}">
                            @csrf
                            <div class="input--grp">
                                <input type="email" name="email" required class="form-control" placeholder="Enter your email address">
                                <button class="search-btn" type="submit">
                                    <svg width="46" height="46" viewBox="0 0 46 46" fill="none">
                                        <rect width="46" height="46" rx="23" fill="url(#newsletter_grad)" />
                                        <path d="M25.9667 22.997L19.3001 29.2222C19.1353 29.3866 18.8556 29.6667 18.8556 29.6667C18.8556 29.6667 18.691 30.0553 18.8558 30.22L19.3803 30.7443C19.5448 30.9092 19.7648 31 19.9992 31C20.2336 31 20.4533 30.9092 20.618 30.7443L27.7448 23.6176C27.9101 23.4524 28.0006 23.2317 28 22.997C28.0006 22.7613 27.9102 22.5408 27.7448 22.3755L20.6246 15.2557C20.46 15.0908 20.2403 15 20.0057 15C19.7713 15 19.5516 15.0908 19.3868 15.2557L18.8624 15.78C18.5212 16.1212 19.0456 16.4367 19.3868 16.7778L25.9667 22.997Z" fill="white" />
                                        <defs><linearGradient id="newsletter_grad" x1="0" y1="23" x2="46" y2="23" gradientUnits="userSpaceOnUse"><stop stop-color="#34DD8E" /><stop offset="1" stop-color="#00D571" /></linearGradient></defs>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="footer-wrapper ps-xl-5">
                    <div class="footer-widget">
                        <div class="footer-logo">
                            <a class="logo">
                            <img
                            src="{{ !empty($logo)
                            ? asset('storage/business/'.$logo)
                            : asset('public/assets/admin/img/160x160/img2.jpg') }}"
                            onerror="this.onerror=null;this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}';"
                            alt="Logo">
                            </a>

                        </div>
                        <div class="txt">{{ $footer_data['fixed_footer_article_title'] ?? '' }}</div>
                        <ul class="social-icon">
                            @foreach (\App\Models\SocialMedia::where('status', 1)->get() as $social)
                                <li><a href="{{ $social->link }}" target="_blank"><img src="{{ asset('public/assets/landing/img/footer/' . $social->name . '.svg') }}" alt=""></a></li>
                            @endforeach
                        </ul>
                    </div>

                    @php($policy_data = \App\Models\DataSetting::where('type', 'admin_landing_page')->whereIn('key', ['shipping_policy_status','refund_policy_status','cancellation_policy_status'])->pluck('value','key'))
                    <div class="footer-widget widget-links">
                        <h5 class="subtitle mt-2 text-white">{{translate("messages.Suppport")}}</h5>
                        <ul>
                            <li><a href="{{route('privacy-policy')}}">{{ translate('messages.privacy_policy') }}</a></li>
                            <li><a href="{{route('terms-and-conditions')}}">{{ translate('messages.terms_and_condition') }}</a></li>
                            @if (($policy_data['refund_policy_status'] ?? 0) == 1) <li><a href="{{route('refund')}}">{{ translate('messages.Refund Policy') }}</a></li> @endif
                            @if (($policy_data['shipping_policy_status'] ?? 0) == 1) <li><a href="{{route('shipping-policy')}}">{{ translate('messages.Shipping Policy') }}</a></li> @endif
                            @if (($policy_data['cancellation_policy_status'] ?? 0) == 1) <li><a href="{{route('cancelation')}}">{{ translate('messages.Cancelation Policy') }}</a></li> @endif
                        </ul>
                    </div>

                    <div class="footer-widget widget-links">
                        <h5 class="subtitle mt-2 text-white">{{translate("messages.Contact")}} {{translate("messages.Us")}} </h5>
                        <ul>
                            <li><a><i class="tio-poi-outlined me-2"></i> {{ \App\CentralLogics\Helpers::get_settings('address') }}</a></li>
                            <li><a href="mailto:{{ \App\CentralLogics\Helpers::get_settings('email_address') }}"><i class="tio-email-outlined me-2"></i> {{ \App\CentralLogics\Helpers::get_settings('email_address') }}</a></li>
                            <li><a href="tel:{{ \App\CentralLogics\Helpers::get_settings('phone') }}"><i class="tio-call-talking-quiet me-2"></i> {{ \App\CentralLogics\Helpers::get_settings('phone') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="copyright text-center mt-3">
                    &copy; {{ \App\CentralLogics\Helpers::get_settings('footer_text') }} by {{ \App\CentralLogics\Helpers::get_settings('business_name') }}
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('public/assets/landing/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('public/assets/landing/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/assets/landing/js/viewport.jquery.js') }}"></script>
    <script src="{{ asset('public/assets/landing/js/wow.min.js') }}"></script>
    <script src="{{ asset('public/assets/landing/js/odometer.min.js') }}"></script>
    <script src="{{ asset('public/assets/landing/js/owl.min.js') }}"></script>
    <script src="{{ asset('public/assets/landing/js/main.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/toastr.js') }}"></script>

    {!! Toastr::message() !!}
    @if ($errors->any())
        <script>
            @foreach($errors->all() as $error)
                toastr.error('{{$error}}', 'Error', { CloseButton: true, ProgressBar: true });
            @endforeach
        </script>
    @endif

    @stack('script_2')

    <script>
        const isRtl = {{ $landing_site_direction === 'rtl' ? 'true' : 'false' }};
        
        $(".main-category-slider").owlCarousel({
            loop: true, nav: false, dots: true, items: 1, margin: 12, autoplay: true, rtl: isRtl
        });

        $(".testimonial-slider").owlCarousel({
            loop: false, margin: 22, nav: false, dots: false, autoplay: true, items: 1, rtl: isRtl,
            responsive: { 768: { items: 2 }, 992: { items: 3 } }
        });
    </script>
</body>
</html>