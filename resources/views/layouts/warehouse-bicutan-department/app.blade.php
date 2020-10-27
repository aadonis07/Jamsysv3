<!DOCTYPE html>
<html>
<head>
    <title>Jamsystem-v3 |@yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Office Furniture Solutions. Your Innovative Partner. Wide Selection of Furniture and Furnishing for Offices, Schools, Resto-Bar, Hotels and Home Environment.">
    <meta property="og:description" content="jecams inc. | office furniture solutions,office table,office chair,office,partition,office furniture,office fit out" />
    <!-- Vendor CSS-->
    <link rel="stylesheet" href="{{ asset('assets/css/vendors.bundle.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/app.bundle.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/notifications/sweetalert2/sweetalert2.bundle.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/notifications/toastr/toastr.css') }}"/>
    <!-- End-Vendor CSS-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600" rel="stylesheet">

    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.1.0/css/all.css" >
    <meta name="csrf_token" content="{{ csrf_token() }}">
    @yield('styles')
</head>
<body class="mod-bg-1">
    <style>
        .swal2-container {
            z-index: 10000;
        }
    </style>
    @php
        $active_menu = 'DASHBOARD';
        $active_sub_menu = '';
        if(isset($admin_menu)){
            $active_menu =$admin_menu;
        }
        if(isset($admin_sub_menu)){
            $active_sub_menu = $admin_sub_menu;
        }
        $user =Auth::user();
        $selectQuery =  App\User::where('id','=',$user->id)->with('employee')->with('position')->with('department')->first();
  
        $destination = 'assets/img/employee/profile/';
        $filename = $selectQuery->employee->employee_num;
        $imagePath = imagePath($destination.''.$filename,'//via.placeholder.com/400X400');
    @endphp
    <div class="page-wrapper">
        <div class="page-inner" >
            <!-- BEGIN Left Aside -->
            <aside class="page-sidebar" >
                <div class="page-logo">
                    <a href="#" class="page-logo-link press-scale-down d-flex align-items-center position-relative" data-toggle="modal" data-target="#modal-shortcut">
                        <img style="width: 180px;" src="{{ asset('assets/img/jecams-icon.png') }}" alt="SmartAdmin WebApp" aria-roledescription="logo">
                        {{-- <span class="page-logo-text mr-1">JECAMS INC.</span> --}}
                        <span class="position-absolute text-white opacity-50 small pos-top pos-right mr-2 mt-n2"></span>
                        <i class="fal fa-angle-down d-inline-block ml-1 fs-lg color-primary-300"></i>
                    </a>
                </div>
                <!-- BEGIN PRIMARY NAVIGATION -->
                <nav id="js-primary-nav" class="primary-nav" role="navigation">
                    <div class="nav-filter" >
                        <div class="position-relative">
                            <input type="text" id="nav_filter_input" placeholder="Filter menu" class="form-control" tabindex="0">
                            <a href="#" onclick="return false;" class="btn-primary btn-search-close js-waves-off" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar">
                                <i class="fal fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="info-card">
                        <img class="profile-image rounded-circle" src="{{ $imagePath }}" alt="Dr. Codex Lantern">
                        <div class="info-card-text">
                            <a href="#" class="d-flex align-items-center text-white">
                                <span class="text-truncate text-truncate-sm d-inline-block">
                                    {{ $selectQuery->employee->first_name  }} {{ $selectQuery->employee->last_name  }}
                                </span>
                            </a>
                            <span class="d-inline-block text-truncate text-truncate-sm" style="  font-size: 12px!important; color: #d9d9d9;">
                                <i class="ni ni-user mr-1"></i>{{ $selectQuery->position->name  }}</span>
                                <span class="d-inline-block text-truncate text-truncate-sm" style="  font-size: 12px!important; color: #d9d9d9;"><i class="ni ni-frame mr-1"></i>{{ $selectQuery->employee->employee_num  }}</span>
                        </div>
                        <img src="{{ asset('assets/img/card-backgrounds/cover-2-lg.png') }}" class="cover" alt="cover">
                        <a href="#" onclick="return false;" class="pull-trigger-btn" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar" data-focus="nav_filter_input">
                            <i class="fal fa-angle-down"></i>
                        </a>
                    </div>
                    <div class="text-center mb-0" style="background-color: #000000;">
                        <p class="p-2 mb-0" style="color: #d9d9d9;"><b>{{ strtoupper($selectQuery->department->name)  }} DEPARTMENT</b></p>
                    </div>
                    <ul id="js-nav-menu" class="nav-menu mt-0">
                        <li  class="{{ isSelected('DASHBOARD',$active_menu,'active open') }}">
                            <a href="{{ route('warehouse-bicutan-dashboard') }}" title="Dashboard" data-filter-tags="Dashboard">
                                <i class="fal fa-info-circle"></i>
                                <span class="nav-link-text">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-title">Configurations</li>
                        <li class="{{ isSelected('SETTINGS',$active_menu,'active open') }}">
                            <a href="javascript:;" title="Settings" data-filter-tags="Settings">
                                <i class="ni ni-settings"></i>
                                <span class="nav-link-text">Settings</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="{{ route('warehouse-bicutan-settings-profile') }}" title="User Profile" data-filter-tags="User Profile">
                                        {!! isSelected('USER-PROFILE',$active_sub_menu,'<i class="ni ni-chevron-right"></i>') !!}
                                        <span class="nav-link-text">User Profile</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <div class="filter-message js-filter-message bg-success-600"></div>
                </nav>
                <!-- END PRIMARY NAVIGATION -->
                <!-- NAV FOOTER -->
                <div class="nav-footer shadow-top">
                    <a href="#" onclick="return false;" data-action="toggle" data-class="nav-function-minify" class="hidden-md-down">
                        <i class="ni ni-chevron-right"></i>
                    </a>
                    <ul class="list-table m-auto nav-footer-buttons">
                        <li>
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Chat logs">
                                <i class="fal fa-comments"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Support Chat">
                                <i class="fal fa-life-ring"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Make a call">
                                <i class="fal fa-phone"></i>
                            </a>
                        </li>
                    </ul>
                </div> <!-- END NAV FOOTER -->
            </aside>
            <!-- END Left Aside -->
            <div class="page-content-wrapper">
                <!-- BEGIN Page Header -->
                <header class="page-header" role="banner">
                    <div class="hidden-lg-up">
                        <a href="#" class="header-btn btn press-scale-down" data-action="toggle" data-class="mobile-nav-on">
                            <i class="ni ni-menu"></i>
                        </a>
                    </div>
                    <div class="search">
                        <form class="app-forms hidden-xs-down" role="search" action="https://www.gotbootstrap.com/themes/smartadmin/4.0.2/page_search.html" autocomplete="off">
                            <input type="text" id="search-field" placeholder="Search for anything" class="form-control" tabindex="1">
                            <a href="#" onclick="return false;" class="btn-danger btn-search-close js-waves-off d-none" data-action="toggle" data-class="mobile-search-on">
                                <i class="fal fa-times"></i>
                            </a>
                        </form>
                    </div>
                    <div class="ml-auto d-flex">
                        <!-- activate app search icon (mobile) -->
                        <div class="hidden-sm-up">
                            <a href="#" class="header-icon" data-action="toggle" data-class="mobile-search-on" data-focus="search-field" title="Search">
                                <i class="fal fa-search"></i>
                            </a>
                        </div>
                        <!-- app settings -->
                        <div class="hidden-md-down">
                            <a href="{{route('warehouse-bicutan-settings-profile')}}" class="header-icon" >
                                <i class="fal fa-cog"></i>
                            </a>
                        </div>
                        <!-- app shortcuts -->
                        
                        <div>
                            <a href="#" data-toggle="dropdown" title="drlantern@gotbootstrap.com" class="header-icon d-flex align-items-center justify-content-center ml-2">
                                <img src="{{ $imagePath }}" class="profile-image rounded-circle" alt="Dr. Codex Lantern">
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated dropdown-lg">
                                <div class="dropdown-header bg-trans-gradient d-flex flex-row py-4 rounded-top">
                                    <div class="d-flex flex-row align-items-center mt-1 mb-1 color-white">
                                        <span class="mr-2">
                                            <img src="{{ $imagePath }}" class="rounded-circle profile-image" alt="Dr. Codex Lantern">
                                        </span>
                                        <div class="info-card-text">
                                            <div class="fs-lg text-truncate text-truncate-lg">{{ $selectQuery->employee->first_name  }} {{ $selectQuery->employee->last_name  }}</div>
                                            <span class="text-truncate text-truncate-md opacity-80">{{ $selectQuery->employee->email  }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown-divider m-0"></div>
                                <a href="{{ route('warehouse-bicutan-settings-profile') }}" class="dropdown-item" >
                                    <span data-i18n="drpdwn.settings">Settings</span>
                                </a>
                                
                                <div class="dropdown-divider m-0"></div>
                                <a class="dropdown-item fw-500 pt-3 pb-3" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                    <span >Logout</span>
                                    <span class="float-right fw-n">&commat;codexlantern</span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </header>
                {{-- <!-- END Page Header -->
                <!-- BEGIN Page Content -->
                <!-- the #js-page-content id is needed for some plugins to initialize --> --}}

                <main id="js-page-content" role="main" class="page-content">
                    <ol class="breadcrumb page-breadcrumb">
                        <li class="breadcrumb-item"><a href="{{  route('warehouse-bicutan-dashboard')  }}">Dashboard</a></li>
                        @yield('breadcrumbs')
                        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                    </ol>
                    @yield('content')
                </main>

                {{-- <!-- this overlay is activated only when mobile menu is triggered --> --}}
                <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div>
                {{-- <!-- END Page Content -->
                <!-- BEGIN Page Footer --> --}}
                <footer class="page-footer" role="contentinfo">
                    <div class="d-flex align-items-center flex-1 text-muted">
                        <span class="hidden-md-down fw-700">2020 © Syside Venture Inc. &nbsp;<a href='https://www.jecams.com.ph' class='text-primary fw-500' title='www.jecams.com.ph' target='_blank'>www.jecams.com.ph</a></span>
                    </div>
                    <div>
                        <ul class="list-table m-0">
                            <li><a href="intel_introduction.html" class="text-secondary fw-700">About</a></li>
                            <li class="pl-3"><a href="info_app_licensing.html" class="text-secondary fw-700">License</a></li>
                            <li class="pl-3"><a href="info_app_docs.html" class="text-secondary fw-700">Documentation</a></li>
                            <li class="pl-3 fs-xl"><a href="" class="text-secondary" target="_blank"><i class="fal fa-question-circle" aria-hidden="true"></i></a></li>
                        </ul>
                    </div>
                </footer>
            </div>
        </div>
    </div>
        <nav class="shortcut-menu d-none d-sm-block">
            <input type="checkbox" class="menu-open" name="menu-open" id="menu_open" />
            <label for="menu_open" class="menu-open-button ">
                <span class="app-shortcut-icon d-block"></span>
            </label>
            <a href="#" class="menu-item btn" data-toggle="tooltip" data-placement="left" title="Scroll Top">
                <i class="fal fa-arrow-up"></i>
            </a>
            <a href="page_login_alt.html" class="menu-item btn" data-toggle="tooltip" data-placement="left" title="Logout">
                <i class="fal fa-sign-out"></i>
            </a>
            <a href="#" class="menu-item btn" data-action="app-fullscreen" data-toggle="tooltip" data-placement="left" title="Full Screen">
                <i class="fal fa-expand"></i>
            </a>
            <a href="#" class="menu-item btn" data-action="app-print" data-toggle="tooltip" data-placement="left" title="Print page">
                <i class="fal fa-print"></i>
            </a>
            <a href="#" class="menu-item btn" data-action="app-voice" data-toggle="tooltip" data-placement="left" title="Voice command">
                <i class="fal fa-microphone"></i>
            </a>
        </nav>
        <!-- END Quick Menu -->
        <!-- BEGIN Page Settings -->
        <div class="modal fade js-modal-settings modal-backdrop-transparent" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-right modal-md">
                <div class="modal-content">
                    <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100">
                        <h4 class="m-0 text-center color-white">
                            Layout Settings
                            <small class="mb-0 opacity-80">User Interface Settings</small>
                        </h4>
                        <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                    </div>
                </div>
            </div>
        </div>
        <!-- END Page Settings -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="{{ asset('assets/js/vendors.bundle.js') }}"></script>
<script src="{{ asset('assets/js/app.bundle.js') }}"></script>
<script src="{{ asset('assets/js/notifications/sweetalert2/sweetalert2.bundle.js') }}"></script>
<script src="{{ asset('assets/js/notifications/toastr/toastr.js') }}"></script>
<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=csrf_token]').attr('content') }
    });
</script>
<script>
    var formData = [];
    var confirmResult = false;
    function readURL(input,displayTo,baseurl,width = null,height = null) {
        var file = document.getElementById(input);
        if (document.getElementById(input).files.length == 0) {
            $('#' + displayTo).attr('src', baseurl);
        }
        else {
            if (file.files && file.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#' + displayTo).attr('src', e.target.result);
                    if (height != null && width != null) {
                        $('#' + displayTo).attr('height', height);
                        $('#' + displayTo).attr('width', width);
                    }
                }
                reader.readAsDataURL(file.files[0]);
            }
            else {
                $('#' + displayTo).attr('src', baseurl);
            }
        }
    }
    $(document).ready(function(){
        $("#nav_filter_input").on("keyup", function() {
          var value = $(this).val().toLowerCase();
          $("#js-nav-menu li").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
          });
        });
      });
      function alert_message(title,text,success = 'info'){
        var classHead = 'info';
        confirmResult = false;
        if(success == 'danger'){
            classHead = 'error';
        }else if(success == 'success'){
            classHead = 'success';
        }
        Swal.fire({
            type: classHead,
            title: title,
            text: text,
            width: 600,
            padding: "3em",
            backdrop: '\n\t\t\t rgba(0,0,123,0.4)\n\t\t\t center left\n\t\t\t no-repeat\n\t\t\t'
        });
    }
    function toastMessage(title,message,mode,position = 'toast-top-right'){
        //
        /**
        * MODE *
            *success
            *info
            *warning
            *error
        * POSITION CLASS *
            *toast-top-center
            *toast-top-right
            *toast-bottom-right
            *toast-bottom-left
            *toast-top-left
            *toast-top-full-width
            *toast-bottom-full-width
            *toast-top-center
            *toast-bottom-center
        */
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": position,
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": 300,
            "hideDuration": 100,
            "timeOut": 5000,
            "extendedTimeOut": 1000,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        toastr[mode](message,title);
    }
</script>
@if(Session::has('success'))
    @if(Session::get('success') == '1')
        <script>
            alert_message('Success',"{{ Session::get('message') }}",'success');
        </script>
    @elseif(Session::get('success') == '2')
        <script>
            alert_message("Info","{{ Session::get('message') }}",'info');
        </script>
    @else
        <script>
            alert_message("Failed","{{ Session::get('message') }}",'danger');
        </script>
    @endif
@endif
@yield('scripts')
</body>
</html>
