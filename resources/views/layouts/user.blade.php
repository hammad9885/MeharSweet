<!DOCTYPE html>
<html lang="en" data-layout-mode="detached" data-topbar-color="dark" data-menu-color="light" data-sidenav-user="true">


<!-- Mirrored from coderthemes.com/hyper_2/modern/dashboard-analytics.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 08 Sep 2024 09:07:45 GMT -->
<head>
    <meta charset="utf-8" />
    <title>Analytics Dashboard | Hyper - Responsive Bootstrap 5 Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
     <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{asset('assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendor/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendor/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendor/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendor/datatables.net-select-bs5/css/select.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset("assets/images/favicon.ico")}}">
 <!-- Select2 css -->
 <link href="{{asset("assets/vendor/select2/css/select2.min.css")}}" rel="stylesheet" type="text/css" />

 <!-- Daterangepicker css -->
 <link href="{{asset('assets/vendor/daterangepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />

 <!-- Bootstrap Touchspin css -->
 <link href="{{asset('assets/vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css')}}" rel="stylesheet" type="text/css" />

 <!-- Bootstrap Datepicker css -->
 <link href="{{asset('assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet" type="text/css" />

 <!-- Bootstrap Timepicker css -->
 <link href="{{asset('assets/vendor/bootstrap-timepicker/css/bootstrap-timepicker.min.css')}}" rel="stylesheet" type="text/css" />

 <!-- Flatpickr Timepicker css -->
 <link href="{{asset('assets/vendor/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- Plugin css -->
    <link href="{{asset('assets/vendor/daterangepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/vendor/jsvectormap/css/jsvectormap.min.css')}}" rel="stylesheet" type="text/css">

    <!-- Theme Config Js -->
    <script src="{{asset('assets/js/hyper-config.js')}}"></script>

    <!-- App css -->
    <link href="{{asset('assets/css/app-modern.min.css')}}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        @media print {
            @page {
                margin: 0;
            }
            body {
                margin: 1cm; /* Adjust as needed */
            }
            /* Hide header and footer */
            header, footer {
                display: none;
            }
        }
    </style>

</head>

<body class="authentication-bg position-relative">
    <div class="position-absolute start-0 end-0 start-0 bottom-0 w-100 h-100">
        <svg xmlns='http://www.w3.org/2000/svg' width='100%' height='100%' viewBox='0 0 800 800'>
            <g fill-opacity='0.22'>
                <circle style="fill: rgba(var(--ct-primary-rgb), 0.1);" cx='400' cy='400' r='600' />
                <circle style="fill: rgba(var(--ct-primary-rgb), 0.2);" cx='400' cy='400' r='500' />
                <circle style="fill: rgba(var(--ct-primary-rgb), 0.3);" cx='400' cy='400' r='300' />
                <circle style="fill: rgba(var(--ct-primary-rgb), 0.4);" cx='400' cy='400' r='200' />
                <circle style="fill: rgba(var(--ct-primary-rgb), 0.5);" cx='400' cy='400' r='100' />
            </g>
        </svg>
    </div>
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">

                        <!-- Logo -->
                        <div class="card-header py-4 text-center bg-primary">
                            <a href="index.html">
                                <span><img src="assets/images/logo.png" alt="logo" height="22"></span>
                            </a>
                        </div>

                        <div class="card-body p-4">

                            {{-- <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center pb-0 fw-bold">Sign In</h4>
                                <p class="text-muted mb-4">Enter your email address and password to access admin panel.</p>
                            </div> --}}

                            {{-- <form action="#">

                                <div class="mb-3">
                                    <label for="emailaddress" class="form-label">Email address</label>
                                    <input class="form-control" type="email" id="emailaddress" required="" placeholder="Enter your email">
                                </div>

                                <div class="mb-3">
                                    <a href="pages-recoverpw.html" class="text-muted float-end"><small>Forgot your password?</small></a>
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" class="form-control" placeholder="Enter your password">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="checkbox-signin" checked>
                                        <label class="form-check-label" for="checkbox-signin">Remember me</label>
                                    </div>
                                </div>

                                <div class="mb-3 mb-0 text-center">
                                    <button class="btn btn-primary" type="submit"> Log In </button>
                                </div>

                            </form> --}}
                            @yield('content')
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    {{-- <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">Don't have an account? <a href="pages-register.html" class="text-muted ms-1"><b>Sign Up</b></a></p>
                        </div> <!-- end col -->
                    </div> --}}
                    <!-- end row -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

    <footer class="footer footer-alt">
        2018 -
        <script>document.write(new Date().getFullYear())</script> © Hyper - Coderthemes.com
    </footer>
    <!-- Vendor js -->
    <script src="assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

</body>


<script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="z"></script>
    <script src="{{asset('assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-fixedcolumns-bs5/js/fixedColumns.bootstrap5.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-buttons/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-keytable/js/dataTables.keyTable.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-select/js/dataTables.select.min.js')}}"></script>

    <!-- Datatable Demo Aapp js -->
    <script src="{{asset('assets/js/pages/demo.datatable-init.js')}}"></script>
    <!-- Vendor js -->
    <script src="{{asset('assets/js/vendor.min.js')}}"></script>
  <!-- Code Highlight js -->
  <script src="{{asset('assets/vendor/highlightjs/highlight.pack.min.js')}}"></script>
  <script src="{{asset('assets/vendor/clipboard/clipboard.min.js')}}"></script>
  <script src="{{asset('assets/js/hyper-syntax.js')}}"></script>
<!-- Include jQuery from a local file -->
<script src="{{asset('assets/js/jquery-item.js')}}"></script>
<script src="{{asset('assets/js/jquery-status.min.js')}}"></script>
  <!--  Select2 Plugin Js -->
  <script src="{{asset('assets/vendor/select2/js/select2.min.js')}}"></script>
    <!-- Daterangepicker js -->
    <script src="{{asset('assets/vendor/daterangepicker/moment.min.js')}}"></script>
    <script src="{{asset("assets/vendor/daterangepicker/daterangepicker.js")}}"></script>

    <!-- Charts js -->
    <script src="{{asset('assets/vendor/chart.js/chart.min.js')}}"></script>
    <script src="{{asset('assets/vendor/apexcharts/apexcharts.min.js')}}"></script>

    <!-- Vector Map js -->
    <script src="{{asset('assets/vendor/jsvectormap/js/jsvectormap.min.js')}}"></script>
    <script src="{{asset('assets/vendor/jsvectormap/maps/world-merc.js')}}"></script>
    <script src="{{asset('assets/vendor/jsvectormap/maps/world.js')}}"></script>
    <!-- Analytics Dashboard App js -->
    <script src="{{asset('assets/js/pages/demo.dashboard-analytics.js')}}"></script>
 <!-- Bootstrap Datepicker Plugin js -->
 <script src="{{asset('assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>

 <!-- Bootstrap Timepicker Plugin js -->
 <script src="{{asset('assets/vendor/bootstrap-timepicker/js/bootstrap-timepicker.min.js')}}"></script>

 <!-- Input Mask Plugin js -->
 <script src="{{asset('assets/vendor/jquery-mask-plugin/jquery.mask.min.js')}}"></script>

 <!-- Bootstrap Touchspin Plugin js -->
 <script src="{{asset('assets/vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js')}}"></script>

 <!-- Bootstrap Maxlength Plugin js -->
 <script src="{{asset('assets/vendor/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script>

 <!-- Typehead Plugin js -->
 <script src="{{asset('assets/vendor/handlebars/handlebars.min.js')}}"></script>
 <script src="{{asset('assets/vendor/typeahead.js/typeahead.bundle.min.js')}}"></script>

 <!-- Flatpickr Timepicker Plugin js -->
 <script src="{{asset('assets/vendor/flatpickr/flatpickr.min.js')}}"></script>

 <!-- Typehead Demo js -->
 <script src="{{asset('assets/js/pages/demo.typehead.js')}}"></script>

 <!-- Timepicker Demo js -->
 <script src="{{asset('assets/js/pages/demo.timepicker.js')}}"></script>
    <!-- App js -->
    <script src="{{asset('assets/js/app.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Apply Select2 to ALL select fields
            $("select").select2({
                placeholder: "Select an option",
                allowClear: true,
                width: '100%'
            });
        });

    </script>
    
</body>


<!-- Mirrored from coderthemes.com/hyper_2/modern/dashboard-analytics.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 08 Sep 2024 09:07:46 GMT -->
</html>
