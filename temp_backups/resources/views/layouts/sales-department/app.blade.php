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
                        <img class="profile-image rounded-circle" src="{{ asset('assets/img/demo/avatars/avatar-admin.png') }}" alt="Dr. Codex Lantern">
                        <div class="info-card-text">
                            <a href="#" class="d-flex align-items-center text-white">
                                <span class="text-truncate text-truncate-sm d-inline-block">
                                    Ms. Teresa Tat
                                </span>
                            </a>
                            <span class="d-inline-block text-truncate text-truncate-sm" style="  font-size: 12px!important; color: #d9d9d9;">
                                <i class="ni ni-user mr-1"></i>Sale Executive</span>
                                <span class="d-inline-block text-truncate text-truncate-sm" style="  font-size: 12px!important; color: #d9d9d9;"><i class="ni ni-frame mr-1"></i>029-2093-000</span>
                        </div>
                        <img src="{{ asset('assets/img/card-backgrounds/cover-2-lg.png') }}" class="cover" alt="cover">
                        <a href="#" onclick="return false;" class="pull-trigger-btn" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar" data-focus="nav_filter_input">
                            <i class="fal fa-angle-down"></i>
                        </a>
                    </div>
                    <div class="text-center mb-0" style="background-color: #000000;">
                        <p class="p-2 mb-0" style="color: #d9d9d9;"><b>SALES DEPARTMENT</b></p>
                    </div>
                    <ul id="js-nav-menu" class="nav-menu mt-0">
                        <li  class="{{ isSelected('DASHBOARD',$active_menu,'active open') }}">
                            <a href="{{ route('it-dashboard') }}" title="Dashboard" data-filter-tags="Dashboard">
                                <i class="fal fa-info-circle"></i>
                                <span class="nav-link-text">Dashboard</span>
                            </a>
                        </li>
                        <li class="{{ isSelected('PRODUCTS',$active_menu,'active open') }}">
                            <a href="#" title="Products Section" data-filter-tags="Products Section">
                                <i class="ni ni-layers"></i>
                                <span class="nav-link-text" data-i18n="nav.theme_settings">Products</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="{{ route('product-list') }}" title="All Products" data-filter-tags="All Products" >
                                        {!! isSelected('LIST',$active_sub_menu,'<i class="ni ni-chevron-right"></i>') !!}
                                        <span class="nav-link-text">All Products</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('product-create') }}" title="Create Product">
                                        {!! isSelected('CREATE',$active_sub_menu,'<i class="ni ni-chevron-right"></i>') !!}
                                        <span  class="nav-link-text">Create Product</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" title="Theme Settings" data-filter-tags="theme settings">
                                <i class="ni ni-user-following"></i>
                                <span class="nav-link-text" data-i18n="nav.theme_settings">Attendance</span>
                            </a>
                        </li>

                        <li class="nav-title">SALES DEPT.</li>
                        <li>
                            <a href="#" title="UI Components" data-filter-tags="ui components">
                                <i class="ni ni-trophy"></i>
                                <span class="nav-link-text" data-i18n="nav.ui_components">Sales Ranking</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" title="UI Components" data-filter-tags="ui components">
                                <i class="ni ni-check"></i>
                                <span class="nav-link-text" data-i18n="nav.ui_components">Collected Quotations</span>
                            </a>

                        </li>
                        <li>
                            <a href="#" title="UI Components" data-filter-tags="ui components">
                                <i class="ni ni-wallet"></i>
                                <span class="nav-link-text" data-i18n="nav.ui_components">Commisions</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="utilities_borders.html" title="Borders" data-filter-tags="utilities borders">
                                        <span class="nav-link-text" data-i18n="nav.utilities_borders">Collected Quotation</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="utilities_clearfix.html" title="Clearfix" data-filter-tags="utilities clearfix">
                                        <span class="nav-link-text" data-i18n="nav.utilities_clearfix">Sale Executive</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" title="Utilities" data-filter-tags="utilities">
                                <i class="fal fa-bolt"></i>
                                <span class="nav-link-text" data-i18n="nav.utilities">Clients</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="utilities_borders.html" title="Borders" data-filter-tags="utilities borders">
                                        <span class="nav-link-text" data-i18n="nav.utilities_borders">All Clients</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="utilities_clearfix.html" title="Clearfix" data-filter-tags="utilities clearfix">
                                        <span class="nav-link-text" data-i18n="nav.utilities_clearfix">Prospect Client</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" title="Font Icons" data-filter-tags="font icons">
                                <i class="ni ni-folder-alt"></i>
                                <span class="nav-link-text" data-i18n="nav.font_icons">Quotations</span>
                                <span class="dl-ref badge-danger hidden-nav-function-minify hidden-nav-function-top">23</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="javascript:void(0);" title="FontAwesome" data-filter-tags="font icons fontawesome">
                                        <span class="nav-link-text" data-i18n="nav.font_icons_fontawesome">Create Quotation</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" title="NextGen Icons" data-filter-tags="font icons nextgen icons">
                                        <span class="nav-link-text" data-i18n="nav.font_icons_nextgen_icons">Pending Quotation</span>
                                        <span class="dl-ref badge-warning hidden-nav-function-minify hidden-nav-function-top">8</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" title="Stack Icons" data-filter-tags="font icons stack icons">
                                        <span class="nav-link-text" data-i18n="nav.font_icons_stack_icons">Process Quotation</span>
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="icons_stack_showcase.html" title="Showcase" data-filter-tags="font icons stack icons showcase">
                                                <span class="nav-link-text" data-i18n="nav.font_icons_stack_icons_showcase">Moved</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="icons_stack_generate7cd4.html?layers=3" title="Generate Stack" data-filter-tags="font icons stack icons generate stack">
                                                <span class="nav-link-text" data-i18n="nav.font_icons_stack_icons_generate_stack">Purchasing</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="icons_stack_generate7cd4.html?layers=3" title="Generate Stack" data-filter-tags="font icons stack icons generate stack">
                                                <span class="nav-link-text" data-i18n="nav.font_icons_stack_icons_generate_stack">Accounting</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="icons_stack_generate7cd4.html?layers=3" title="Generate Stack" data-filter-tags="font icons stack icons generate stack">
                                                <span class="nav-link-text" data-i18n="nav.font_icons_stack_icons_generate_stack">Approved</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" title="NextGen Icons" data-filter-tags="font icons nextgen icons">
                                        <span class="nav-link-text" data-i18n="nav.font_icons_nextgen_icons">Request for Rejection</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" title="NextGen Icons" data-filter-tags="font icons nextgen icons">
                                        <span class="nav-link-text" data-i18n="nav.font_icons_nextgen_icons">Rejected</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" title="NextGen Icons" data-filter-tags="font icons nextgen icons">
                                        <span class="nav-link-text" data-i18n="nav.font_icons_nextgen_icons">Cancelled</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" title="Tables" data-filter-tags="tables">
                                <i class="fal fa-edit"></i>
                                <span class="nav-link-text" data-i18n="nav.tables">Job Request</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="tables_basic.html" title="Basic Tables" data-filter-tags="tables basic tables">
                                        <span class="nav-link-text" data-i18n="nav.tables_basic_tables">Basic Tables</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="tables_generate_style.html" title="Generate Table Style" data-filter-tags="tables generate table style">
                                        <span class="nav-link-text" data-i18n="nav.tables_generate_table_style">Generate Table Style</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" title="Form Stuff" data-filter-tags="form stuff">
                                <i class="ni ni-plane"></i>
                                <span class="nav-link-text" data-i18n="nav.form_stuff">Transmittals</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="form_basic_inputs.html" title="Basic Inputs" data-filter-tags="form stuff basic inputs">
                                        <span class="nav-link-text" data-i18n="nav.form_stuff_basic_inputs">Basic Inputs</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="form_checkbox_radio.html" title="Checkbox & Radio" data-filter-tags="form stuff checkbox & radio">
                                        <span class="nav-link-text" data-i18n="nav.form_stuff_checkbox_&_radio">Checkbox & Radio</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="form_input_groups.html" title="Input Groups" data-filter-tags="form stuff input groups">
                                        <span class="nav-link-text" data-i18n="nav.form_stuff_input_groups">Input Groups</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="form_validation.html" title="Validation" data-filter-tags="form stuff validation">
                                        <span class="nav-link-text" data-i18n="nav.form_stuff_validation">Validation</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-title">Accounting Dept.</li>
                        <li>
                            <a href="#" title="Plugins" data-filter-tags="plugins">
                                <i class="fal fa-shield-alt"></i>
                                <span class="nav-link-text" data-i18n="nav.plugins">Core Plugins</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="plugin_faq.html" title="Plugins FAQ" data-filter-tags="plugins plugins faq">
                                        <span class="nav-link-text" data-i18n="nav.plugins_plugins_faq">Plugins FAQ</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="plugin_waves.html" title="Waves" data-filter-tags="plugins waves">
                                        <span class="nav-link-text" data-i18n="nav.plugins_waves">Waves</span>
                                        <span class="dl-ref label bg-primary-400 ml-2">9 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="plugin_pacejs.html" title="PaceJS" data-filter-tags="plugins pacejs">
                                        <span class="nav-link-text" data-i18n="nav.plugins_pacejs">PaceJS</span>
                                        <span class="dl-ref label bg-primary-500 ml-2">13 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="plugin_smartpanels.html" title="SmartPanels" data-filter-tags="plugins smartpanels">
                                        <span class="nav-link-text" data-i18n="nav.plugins_smartpanels">SmartPanels</span>
                                        <span class="dl-ref label bg-primary-600 ml-2">9 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="plugin_bootbox.html" title="BootBox" data-filter-tags="plugins bootbox alert sound">
                                        <span class="nav-link-text" data-i18n="nav.plugins_bootbox">BootBox</span>
                                        <span class="dl-ref label bg-primary-600 ml-2">15 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="plugin_slimscroll.html" title="Slimscroll" data-filter-tags="plugins slimscroll">
                                        <span class="nav-link-text" data-i18n="nav.plugins_slimscroll">Slimscroll</span>
                                        <span class="dl-ref label bg-primary-700 ml-2">5 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="plugin_throttle.html" title="Throttle" data-filter-tags="plugins throttle">
                                        <span class="nav-link-text" data-i18n="nav.plugins_throttle">Throttle</span>
                                        <span class="dl-ref label bg-primary-700 ml-2">1 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="plugin_navigation.html" title="Navigation" data-filter-tags="plugins navigation">
                                        <span class="nav-link-text" data-i18n="nav.plugins_navigation">Navigation</span>
                                        <span class="dl-ref label bg-primary-700 ml-2">2 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="plugin_i18next.html" title="i18next" data-filter-tags="plugins i18next">
                                        <span class="nav-link-text" data-i18n="nav.plugins_i18next">i18next</span>
                                        <span class="dl-ref label bg-primary-700 ml-2">10 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="plugin_appcore.html" title="App.Core" data-filter-tags="plugins app.core">
                                        <span class="nav-link-text" data-i18n="nav.plugins_app.core">App.Core</span>
                                        <span class="dl-ref label bg-success-700 ml-2">14 KB</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" title="Datatables" data-filter-tags="datatables datagrid">
                                <i class="fal fa-table"></i>
                                <span class="nav-link-text" data-i18n="nav.datatables">Datatables</span>
                                <span class="dl-ref bg-primary-500 hidden-nav-function-minify hidden-nav-function-top">235 KB</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="datatables_basic.html" title="Basic" data-filter-tags="datatables datagrid basic">
                                        <span class="nav-link-text" data-i18n="nav.datatables_basic">Basic</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_autofill.html" title="Autofill" data-filter-tags="datatables datagrid autofill">
                                        <span class="nav-link-text" data-i18n="nav.datatables_autofill">Autofill</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_buttons.html" title="Buttons" data-filter-tags="datatables datagrid buttons">
                                        <span class="nav-link-text" data-i18n="nav.datatables_buttons">Buttons</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_export.html" title="Export" data-filter-tags="datatables datagrid export tables pdf excel print csv">
                                        <span class="nav-link-text" data-i18n="nav.datatables_export">Export</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_colreorder.html" title="ColReorder" data-filter-tags="datatables datagrid colreorder">
                                        <span class="nav-link-text" data-i18n="nav.datatables_colreorder">ColReorder</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_columnfilter.html" title="ColumnFilter" data-filter-tags="datatables datagrid columnfilter">
                                        <span class="nav-link-text" data-i18n="nav.datatables_columnfilter">ColumnFilter</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_fixedcolumns.html" title="FixedColumns" data-filter-tags="datatables datagrid fixedcolumns">
                                        <span class="nav-link-text" data-i18n="nav.datatables_fixedcolumns">FixedColumns</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_fixedheader.html" title="FixedHeader" data-filter-tags="datatables datagrid fixedheader">
                                        <span class="nav-link-text" data-i18n="nav.datatables_fixedheader">FixedHeader</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_keytable.html" title="KeyTable" data-filter-tags="datatables datagrid keytable">
                                        <span class="nav-link-text" data-i18n="nav.datatables_keytable">KeyTable</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_responsive.html" title="Responsive" data-filter-tags="datatables datagrid responsive">
                                        <span class="nav-link-text" data-i18n="nav.datatables_responsive">Responsive</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_responsive_alt.html" title="Responsive Alt" data-filter-tags="datatables datagrid responsive alt">
                                        <span class="nav-link-text" data-i18n="nav.datatables_responsive_alt">Responsive Alt</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_rowgroup.html" title="RowGroup" data-filter-tags="datatables datagrid rowgroup">
                                        <span class="nav-link-text" data-i18n="nav.datatables_rowgroup">RowGroup</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_rowreorder.html" title="RowReorder" data-filter-tags="datatables datagrid rowreorder">
                                        <span class="nav-link-text" data-i18n="nav.datatables_rowreorder">RowReorder</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_scroller.html" title="Scroller" data-filter-tags="datatables datagrid scroller">
                                        <span class="nav-link-text" data-i18n="nav.datatables_scroller">Scroller</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_select.html" title="Select" data-filter-tags="datatables datagrid select">
                                        <span class="nav-link-text" data-i18n="nav.datatables_select">Select</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="datatables_alteditor.html" title="AltEditor" data-filter-tags="datatables datagrid alteditor">
                                        <span class="nav-link-text" data-i18n="nav.datatables_alteditor">AltEditor</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" title="Statistics" data-filter-tags="statistics chart graphs">
                                <i class="fal fa-chart-pie"></i>
                                <span class="nav-link-text" data-i18n="nav.statistics">Statistics</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="statistics_flot.html" title="Flot" data-filter-tags="statistics chart graphs flot bar pie">
                                        <span class="nav-link-text" data-i18n="nav.statistics_flot">Flot</span>
                                        <span class="dl-ref label bg-primary-500 ml-2">36 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="statistics_chartjs.html" title="Chart.js" data-filter-tags="statistics chart graphs chart.js bar pie">
                                        <span class="nav-link-text" data-i18n="nav.statistics_chart.html">Chart.js</span>
                                        <span class="dl-ref label bg-primary-500 ml-2">205 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="statistics_chartist.html" title="Chartist.js" data-filter-tags="statistics chart graphs chartist.js">
                                        <span class="nav-link-text" data-i18n="nav.statistics_chartist.html">Chartist.js</span>
                                        <span class="dl-ref label bg-primary-600 ml-2">39 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="statistics_c3.html" title="C3 Charts" data-filter-tags="statistics chart graphs c3 charts">
                                        <span class="nav-link-text" data-i18n="nav.statistics_c3_charts">C3 Charts</span>
                                        <span class="dl-ref label bg-primary-600 ml-2">197 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="statistics_peity.html" title="Peity" data-filter-tags="statistics chart graphs peity small">
                                        <span class="nav-link-text" data-i18n="nav.statistics_peity">Peity</span>
                                        <span class="dl-ref label bg-primary-700 ml-2">4 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="statistics_sparkline.html" title="Sparkline" data-filter-tags="statistics chart graphs sparkline small tiny">
                                        <span class="nav-link-text" data-i18n="nav.statistics_sparkline">Sparkline</span>
                                        <span class="dl-ref label bg-primary-700 ml-2">42 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="statistics_easypiechart.html" title="Easy Pie Chart" data-filter-tags="statistics chart graphs easy pie chart">
                                        <span class="nav-link-text" data-i18n="nav.statistics_easy_pie_chart">Easy Pie Chart</span>
                                        <span class="dl-ref label bg-primary-700 ml-2">4 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="statistics_dygraph.html" title="Dygraph" data-filter-tags="statistics chart graphs dygraph complex">
                                        <span class="nav-link-text" data-i18n="nav.statistics_dygraph">Dygraph</span>
                                        <span class="dl-ref label bg-primary-700 ml-2">120 KB</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" title="Notifications" data-filter-tags="notifications">
                                <i class="fal fa-exclamation-circle"></i>
                                <span class="nav-link-text" data-i18n="nav.notifications">Notifications</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="notifications_sweetalert2.html" title="SweetAlert2" data-filter-tags="notifications sweetalert2">
                                        <span class="nav-link-text" data-i18n="nav.notifications_sweetalert2">SweetAlert2</span>
                                        <span class="dl-ref label bg-primary-500 ml-2">40 KB</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="notifications_toastr.html" title="Toastr" data-filter-tags="notifications toastr">
                                        <span class="nav-link-text" data-i18n="nav.notifications_toastr">Toastr</span>
                                        <span class="dl-ref label bg-primary-600 ml-2">5 KB</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" title="Form Plugins" data-filter-tags="form plugins">
                                <i class="fal fa-credit-card-front"></i>
                                <span class="nav-link-text" data-i18n="nav.form_plugins">Form Plugins</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="form_plugins_colorpicker.html" title="Color Picker" data-filter-tags="form plugins color picker">
                                        <span class="nav-link-text" data-i18n="nav.form_plugins_color_picker">Color Picker</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="form_plugins_datepicker.html" title="Date Picker" data-filter-tags="form plugins date picker">
                                        <span class="nav-link-text" data-i18n="nav.form_plugins_date_picker">Date Picker</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="form_plugins_daterange_picker.html" title="Date Range Picker" data-filter-tags="form plugins date range picker">
                                        <span class="nav-link-text" data-i18n="nav.form_plugins_date_range_picker">Date Range Picker</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="form_plugins_dropzone.html" title="Dropzone" data-filter-tags="form plugins dropzone">
                                        <span class="nav-link-text" data-i18n="nav.form_plugins_dropzone">Dropzone</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="form_plugins_ionrangeslider.html" title="Ion.RangeSlider" data-filter-tags="form plugins ion.rangeslider">
                                        <span class="nav-link-text" data-i18n="nav.form_plugins_ion.rangeslider">Ion.RangeSlider</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="form_plugins_inputmask.html" title="Inputmask" data-filter-tags="form plugins inputmask">
                                        <span class="nav-link-text" data-i18n="nav.form_plugins_inputmask">Inputmask</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="form_plugin_imagecropper.html" title="Image Cropper" data-filter-tags="form plugins image cropper">
                                        <span class="nav-link-text" data-i18n="nav.form_plugins_image_cropper">Image Cropper</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="form_plugin_select2.html" title="Select2" data-filter-tags="form plugins select2">
                                        <span class="nav-link-text" data-i18n="nav.form_plugins_select2">Select2</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="form_plugin_summernote.html" title="Summernote" data-filter-tags="form plugins summernote texteditor editor">
                                        <span class="nav-link-text" data-i18n="nav.form_plugins_summernote">Summernote</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" title="Miscellaneous" data-filter-tags="miscellaneous">
                                <i class="fal fa-globe"></i>
                                <span class="nav-link-text" data-i18n="nav.miscellaneous">Miscellaneous</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="miscellaneous_fullcalendar.html" title="FullCalendar" data-filter-tags="miscellaneous fullcalendar">
                                        <span class="nav-link-text" data-i18n="nav.miscellaneous_fullcalendar">FullCalendar</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="miscellaneous_lightgallery.html" title="Light Gallery" data-filter-tags="miscellaneous light gallery">
                                        <span class="nav-link-text" data-i18n="nav.miscellaneous_light_gallery">Light Gallery</span>
                                        <span class="dl-ref label bg-primary-500 ml-2">61 KB</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-title">Layouts & Apps</li>
                        <li >
                            <a href="#" title="User Profile">
                                <i class="fal fa-user"></i>
                                <span class="nav-link-text" >User Profile</span>
                            </a>
                        </li>
                        <li class="{{ isSelected('SETTINGS',$active_menu,'active open') }}">
                            <a href="javascript:;" title="Settings" data-filter-tags="Settings">
                                <i class="ni ni-settings"></i>
                                <span class="nav-link-text">Settings</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="javascript:;" title="Teams" data-filter-tags="Teams">
                                        {!! isSelected('TEAMS',$active_sub_menu,'<i class="ni ni-chevron-right"></i>') !!}
                                        <span class="nav-link-text">Teams</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('settings-swatches') }}" title="Swatches" data-filter-tags="Swatches">
                                        {!! isSelected('SWATCHES',$active_sub_menu,'<i class="ni ni-chevron-right"></i>') !!}
                                        <span class="nav-link-text">Swatches</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('settings-departments') }}" title="Departments" data-filter-tags="Departments">
                                        {!! isSelected('DEPARTMENTS',$active_sub_menu,'<i class="ni ni-chevron-right"></i>') !!}
                                        <span class="nav-link-text">Departments</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('settings-categories') }}" title="Categories" data-filter-tags="Categories">
                                        {!! isSelected('CATEGORIES',$active_sub_menu,'<i class="ni ni-chevron-right"></i>') !!}
                                        <span class="nav-link-text">Categories</span>
                                        {{--
                                            Includes attributes
                                            Includes sub category
                                            Includes swatches
                                        --}}
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
                            <a href="#" class="header-icon" data-toggle="modal" data-target=".js-modal-settings">
                                <i class="fal fa-cog"></i>
                            </a>
                        </div>
                        <!-- app shortcuts -->
                        <div>
                            <a href="#" class="header-icon" data-toggle="dropdown" title="My Apps">
                                <i class="fal fa-cube"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated w-auto h-auto">
                                <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center rounded-top">
                                    <h4 class="m-0 text-center color-white">
                                        Quick Shortcut
                                        <small class="mb-0 opacity-80">User Applications & Addons</small>
                                    </h4>
                                </div>
                                <div class="custom-scroll h-100">
                                    <ul class="app-list">
                                        <li>
                                            <a href="#" class="app-list-item hover-white">
                                                <span class="icon-stack">
                                                    <i class="base-2 icon-stack-3x color-primary-600"></i>
                                                    <i class="base-3 icon-stack-2x color-primary-700"></i>
                                                    <i class="ni ni-settings icon-stack-1x text-white fs-lg"></i>
                                                </span>
                                                <span class="app-list-name">
                                                    Services
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="app-list-item hover-white">
                                                <span class="icon-stack">
                                                    <i class="base-2 icon-stack-3x color-primary-400"></i>
                                                    <i class="base-10 text-white icon-stack-1x"></i>
                                                    <i class="ni md-profile color-primary-800 icon-stack-2x"></i>
                                                </span>
                                                <span class="app-list-name">
                                                    Account
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="app-list-item hover-white">
                                                <span class="icon-stack">
                                                    <i class="base-9 icon-stack-3x color-success-400"></i>
                                                    <i class="base-2 icon-stack-2x color-success-500"></i>
                                                    <i class="ni ni-shield icon-stack-1x text-white"></i>
                                                </span>
                                                <span class="app-list-name">
                                                    Security
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="app-list-item hover-white">
                                                <span class="icon-stack">
                                                    <i class="base-18 icon-stack-3x color-info-700"></i>
                                                    <span class="position-absolute pos-top pos-left pos-right color-white fs-md mt-2 fw-400">28</span>
                                                </span>
                                                <span class="app-list-name">
                                                    Calendar
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="app-list-item hover-white">
                                                <span class="icon-stack">
                                                    <i class="base-7 icon-stack-3x color-info-500"></i>
                                                    <i class="base-7 icon-stack-2x color-info-700"></i>
                                                    <i class="ni ni-graph icon-stack-1x text-white"></i>
                                                </span>
                                                <span class="app-list-name">
                                                    Stats
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="app-list-item hover-white">
                                                <span class="icon-stack">
                                                    <i class="base-4 icon-stack-3x color-danger-500"></i>
                                                    <i class="base-4 icon-stack-1x color-danger-400"></i>
                                                    <i class="ni ni-envelope icon-stack-1x text-white"></i>
                                                </span>
                                                <span class="app-list-name">
                                                    Messages
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="app-list-item hover-white">
                                                <span class="icon-stack">
                                                    <i class="base-4 icon-stack-3x color-fusion-400"></i>
                                                    <i class="base-5 icon-stack-2x color-fusion-200"></i>
                                                    <i class="base-5 icon-stack-1x color-fusion-100"></i>
                                                    <i class="fal fa-keyboard icon-stack-1x color-info-50"></i>
                                                </span>
                                                <span class="app-list-name">
                                                    Notes
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="app-list-item hover-white">
                                                <span class="icon-stack">
                                                    <i class="base-16 icon-stack-3x color-fusion-500"></i>
                                                    <i class="base-10 icon-stack-1x color-primary-50 opacity-30"></i>
                                                    <i class="base-10 icon-stack-1x fs-xl color-primary-50 opacity-20"></i>
                                                    <i class="fal fa-dot-circle icon-stack-1x text-white opacity-85"></i>
                                                </span>
                                                <span class="app-list-name">
                                                    Photos
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="app-list-item hover-white">
                                                <span class="icon-stack">
                                                    <i class="base-19 icon-stack-3x color-primary-400"></i>
                                                    <i class="base-7 icon-stack-2x color-primary-300"></i>
                                                    <i class="base-7 icon-stack-1x fs-xxl color-primary-200"></i>
                                                    <i class="base-7 icon-stack-1x color-primary-500"></i>
                                                    <i class="fal fa-globe icon-stack-1x text-white opacity-85"></i>
                                                </span>
                                                <span class="app-list-name">
                                                    Maps
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="app-list-item hover-white">
                                                <span class="icon-stack">
                                                    <i class="base-5 icon-stack-3x color-success-700 opacity-80"></i>
                                                    <i class="base-12 icon-stack-2x color-success-700 opacity-30"></i>
                                                    <i class="fal fa-comment-alt icon-stack-1x text-white"></i>
                                                </span>
                                                <span class="app-list-name">
                                                    Chat
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="app-list-item hover-white">
                                                <span class="icon-stack">
                                                    <i class="base-5 icon-stack-3x color-warning-600"></i>
                                                    <i class="base-7 icon-stack-2x color-warning-800 opacity-50"></i>
                                                    <i class="fal fa-phone icon-stack-1x text-white"></i>
                                                </span>
                                                <span class="app-list-name">
                                                    Phone
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="app-list-item hover-white">
                                                <span class="icon-stack">
                                                    <i class="base-6 icon-stack-3x color-danger-600"></i>
                                                    <i class="fal fa-chart-line icon-stack-1x text-white"></i>
                                                </span>
                                                <span class="app-list-name">
                                                    Projects
                                                </span>
                                            </a>
                                        </li>
                                        <li class="w-100">
                                            <a href="#" class="btn btn-default mt-4 mb-2 pr-5 pl-5"> Add more apps </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="#" data-toggle="dropdown" title="drlantern@gotbootstrap.com" class="header-icon d-flex align-items-center justify-content-center ml-2">
                                <img src="{{ asset('assets/img/demo/avatars/avatar-admin.png') }}" class="profile-image rounded-circle" alt="Dr. Codex Lantern">
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated dropdown-lg">
                                <div class="dropdown-header bg-trans-gradient d-flex flex-row py-4 rounded-top">
                                    <div class="d-flex flex-row align-items-center mt-1 mb-1 color-white">
                                        <span class="mr-2">
                                            <img src="{{ asset('assets/img/demo/avatars/avatar-admin.png') }}" class="rounded-circle profile-image" alt="Dr. Codex Lantern">
                                        </span>
                                        <div class="info-card-text">
                                            <div class="fs-lg text-truncate text-truncate-lg">Dr. Codex Lantern</div>
                                            <span class="text-truncate text-truncate-md opacity-80">drlantern@gotbootstrap.com</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown-divider m-0"></div>
                                <a href="#" class="dropdown-item" data-action="app-reset">
                                    <span data-i18n="drpdwn.reset_layout">Reset Layout</span>
                                </a>
                                <a href="#" class="dropdown-item" data-toggle="modal" data-target=".js-modal-settings">
                                    <span data-i18n="drpdwn.settings">Settings</span>
                                </a>
                                <div class="dropdown-divider m-0"></div>
                                <a href="#" class="dropdown-item" data-action="app-fullscreen">
                                    <span data-i18n="drpdwn.fullscreen">Fullscreen</span>
                                    <i class="float-right text-muted fw-n">F11</i>
                                </a>
                                <a href="#" class="dropdown-item" data-action="app-print">
                                    <span data-i18n="drpdwn.print">Print</span>
                                    <i class="float-right text-muted fw-n">Ctrl + P</i>
                                </a>
                                <div class="dropdown-multilevel dropdown-multilevel-left">
                                    <div class="dropdown-item">
                                        Language
                                    </div>
                                    <div class="dropdown-menu">
                                        <a href="#?lang=fr" class="dropdown-item" data-action="lang" data-lang="fr">Français</a>
                                        <a href="#?lang=en" class="dropdown-item active" data-action="lang" data-lang="en">English (US)</a>
                                        <a href="#?lang=es" class="dropdown-item" data-action="lang" data-lang="es">Español</a>
                                        <a href="#?lang=ru" class="dropdown-item" data-action="lang" data-lang="ru">Русский язык</a>
                                        <a href="#?lang=jp" class="dropdown-item" data-action="lang" data-lang="jp">日本語</a>
                                        <a href="#?lang=ch" class="dropdown-item" data-action="lang" data-lang="ch">中文</a>
                                    </div>
                                </div>
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
                        <li class="breadcrumb-item"><a href="{{  route('it-dashboard')  }}">Dashboard</a></li>
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
