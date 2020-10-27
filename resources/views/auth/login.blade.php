<!DOCTYPE html>
<html>
<head>
    <title>Jecams Inc. | Login Authentication</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Office Furniture Solutions. Your Innovative Partner. Wide Selection of Furniture and Furnishing for Offices, Schools, Resto-Bar, Hotels and Home Environment.">
    <meta property="og:description" content="jecams inc. | office furniture solutions,office table,office chair,office,partition,office furniture,office fit out" />
    <!-- Vendor CSS-->
    <link rel="stylesheet" href="{{ asset('assets/css/vendors.bundle.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/app.bundle.css') }}"/>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
    <div class="row">
        <div class="col-lg-12">
                <div class="page-wrapper">
            <div class="page-inner bg-brand-gradient">
                <div class="page-content-wrapper bg-transparent m-0">
                    <div class="height-10 w-100 shadow-lg px-4 bg-brand-gradient">
                        <div class="d-flex align-items-center container p-0">
                            <div class="page-logo width-mobile-auto m-0 align-items-center justify-content-center p-0 bg-transparent bg-img-none shadow-0 height-9">
                                <a href="javascript:void(0)" class="page-logo-link press-scale-down d-flex align-items-center">
                                     <img style="width: 180px;" src="{{ asset('assets/img/jecams-icon.png') }}" alt="SmartAdmin WebApp" aria-roledescription="logo">
                                
                                </a>
                            </div>
                            <span class="text-white opacity-50 ml-auto mr-2 hidden-sm-down">
                                Jecams Events
                            </span>
                            <a href="page_login_alt.html" class="btn-link text-white ml-auto ml-sm-0">
                                HR's Modules
                            </a>
                        </div>
                    </div>
                    <div class="flex-1" style="background: url(img/svg/pattern-1.svg) no-repeat center bottom fixed; background-size: cover;">
                        <div class="container py-4 py-lg-5 my-lg-5 px-4 px-sm-0">
                            <div class="row">
                                <div class="col-xl-6">
                                    <h2 class="fs-xxl fw-500 mt-4 text-white text-center">
                                        LOGIN
                                        <small class="h3 fw-300 mt-3 mb-5 text-white opacity-60 hidden-sm-down">
                                            Your registration is free for a limited time.
                                        </small>
                                    </h2>
                                </div>
                                <div class="col-xl-6 ml-auto mr-auto">
                                        <div class="card">
                                            <div class="card-header">{{ __('Login') }}</div>
                            
                                            <div class="card-body">
                                                <form method="POST" action="{{ route('login') }}">
                                                    @csrf
                            
                                                    <div class="form-group row">
                                                        <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Username') }}</label>
                            
                                                        <div class="col-md-6">
                                                            <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                            
                                                            @error('username')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                            
                                                    <div class="form-group row">
                                                        <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
                            
                                                        <div class="col-md-6">
                                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            
                                                            @error('password')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group row mb-0">
                                                        <div class="col-md-8 offset-md-4">
                                                            <button type="submit" class="btn btn-primary">
                                                                {{ __('Login') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="position-absolute pos-bottom pos-left pos-right p-3 text-center text-white">
                            2020 © ERP by&nbsp;<a href='www.syside.com' class='text-white opacity-40 fw-500' title='gotbootstrap.com' target='_blank'>syside.com</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="col-lg-6"></div>
    </div>
    
    
    
<script src="{{ asset('assets/js/vendors.bundle.js') }}"></script>
<script src="{{ asset('assets/js/app.bundle.js') }}"></script>
</body>
</html>
