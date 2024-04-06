<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('page_title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <!-- Vendors styles-->
    <link rel="stylesheet" href="{{asset('admin/coreui/vendors/simplebar/css/simplebar.css')}}">
    <link rel="stylesheet" href="{{asset('admin/coreui/css/vendors/simplebar.css')}}">
    <!-- Main styles for this application-->
    <link href="{{asset('admin/coreui/icons/css/all.css')}}" rel="stylesheet">
    <link href="{{asset('admin/coreui/css/style.css')}}" rel="stylesheet">
    <!-- We use those styles to show code examples, you should remove them in your application.-->
    <link href="{{asset('admin/coreui/css/examples.css')}}" rel="stylesheet">
    <link href="{{asset('admin/toastr/toastr.min.css')}}" rel="stylesheet">
    @stack('head-scripts')
    <style>
        html {
            position: relative;
            min-height: 100%;
        }

        body {
            margin-bottom: 60px;
        }

        .main-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 60px;
            line-height: 60px;
            background-color: #E8EAF6;
        }

        .main-footer p {
            margin-bottom: 0;
        }

        .main-footer .fa.fa-heart {
            color: #C62828;
        }

        .page-header {
            border-bottom: 1px solid #8a8a8a;
        }

        /*
         * Navbar
         */

        .navbar-brand {
            padding: .75rem 1rem;
            font-size: 1rem;
        }

        .navbar-nav .nav-link {
            padding-right: .5rem;
            padding-left: .5rem;
        }

        /*
         * Boxes
         */

        .box {
            display: block;
            padding: 0;
            min-height: 70px;
            background: #fff;
            width: 100%;
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
            border-radius: .25rem;
        }

        .box > .box-icon > i,
        .box .box-content .box-text,
        .box .box-content .box-number {
            color: #FFF;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.3);
        }

        .box > .box-icon {
            border-radius: 2px 0 0 2px;
            display: block;
            float: left;
            height: 70px; width: 70px;
            text-align: center;
            font-size: 40px;
            line-height: 70px;
            background: rgba(0,0,0,0.2);
        }

        .box .box-content {
            padding: 5px 10px;
            margin-left: 70px;
        }

        .box .box-content .box-text {
            display: block;
            font-size: 1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 600;
        }

        .box .box-content .box-number {
            display: block;
        }

        .box .box-content .progress {
            background: rgba(0,0,0,0.2);
            margin: 5px -10px 5px -10px;
        }

        .box .box-content .progress .progress-bar {
            background-color: #FFF;
        }

        /*
         * Log Menu
         */

        .log-menu .list-group-item.disabled {
            cursor: not-allowed;
        }

        .log-menu .list-group-item.disabled .level-name {
            color: #D1D1D1;
        }

        /*
         * Log Entry
         */

        .stack-content {
            color: #AE0E0E;
            font-family: consolas, Menlo, Courier, monospace;
            white-space: pre-line;
            font-size: .8rem;
        }

        /*
         * Colors: Badge & Infobox
         */

        .badge.badge-env,
        .badge.badge-level-all,
        .badge.badge-level-emergency,
        .badge.badge-level-alert,
        .badge.badge-level-critical,
        .badge.badge-level-error,
        .badge.badge-level-warning,
        .badge.badge-level-notice,
        .badge.badge-level-info,
        .badge.badge-level-debug,
        .badge.empty {
            color: #FFF;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.3);
        }

        .badge.badge-level-all,
        .box.level-all {
            background-color: {{ log_styler()->color('all') }};
        }

        .badge.badge-level-emergency,
        .box.level-emergency {
            background-color: {{ log_styler()->color('emergency') }};
        }

        .badge.badge-level-alert,
        .box.level-alert  {
            background-color: {{ log_styler()->color('alert') }};
        }

        .badge.badge-level-critical,
        .box.level-critical {
            background-color: {{ log_styler()->color('critical') }};
        }

        .badge.badge-level-error,
        .box.level-error {
            background-color: {{ log_styler()->color('error') }};
        }

        .badge.badge-level-warning,
        .box.level-warning {
            background-color: {{ log_styler()->color('warning') }};
        }

        .badge.badge-level-notice,
        .box.level-notice {
            background-color: {{ log_styler()->color('notice') }};
        }

        .badge.badge-level-info,
        .box.level-info {
            background-color: {{ log_styler()->color('info') }};
        }

        .badge.badge-level-debug,
        .box.level-debug {
            background-color: {{ log_styler()->color('debug') }};
        }

        .badge.empty,
        .box.empty {
            background-color: {{ log_styler()->color('empty') }};
        }

        .badge.badge-env {
            background-color: #6A1B9A;
        }

        #entries {
            overflow-wrap: anywhere;
        }
    </style>
</head>
<body>
@include('admin.layouts.leftSidebar')
<div class="wrapper d-flex flex-column min-vh-100 bg-light">
    @include('admin.layouts.header')
    <div class="body flex-grow-1 px-3">
        <div class="container-lg">
            @yield('content')
        </div>
    </div>
    @include('admin.layouts.footer')
</div>
<!-- CoreUI and necessary plugins-->
<script src="{{asset('admin/js/jquery-3.7.0.min.js')}}"></script>
<script src="{{asset('admin/coreui/vendors/@coreui/coreui/js/coreui.bundle.min.js')}}"></script>
<script src="{{asset('admin/coreui/vendors/simplebar/js/simplebar.min.js')}}"></script>
<!-- Plugins and scripts required by this view-->
<script src="{{asset('admin/coreui/vendors/@coreui/utils/js/coreui-utils.js')}}"></script>
<script src="{{asset('admin/toastr/toastr.min.js')}}"></script>
<script src="{{asset('admin/js/scripts.js')}}"></script>
@stack('footer-scripts')
<script>
    $(document).ready(function (){
        $('#switch').click(function (){
            $.ajax({
                type:'GET',
                url:"{{route('get.switch-module')}}",
                dataType:'json',
                success:function(data){
                    if(data.length > 0){
                        $("#switch-table").empty();
                        var table = "<table class='table table-bordered'>";
                        table += "<thead>";
                        table += "<tr>";
                        table += "<td>#</td>";
                        table += "<td>Name</td>";
                        table += "<td>Action</td>";
                        table += "</tr>";
                        table += "</thead>";
                        table += "<tbody>";
                        $.each(data, function(index,item){
                            let URL = item.route;
                            table += "<tr>";
                            table += "<td>"+item.id+"</td>";
                            table += "<td>"+item.name+"</td>";
                            table += "<td>";
                            table += "<a href='"+URL+"' class='badge bg-primary text-white'>View</a>";
                            table += "</td>";
                            table += "</tr>";
                        });
                        table += "</tbody>";
                        table += "</table>";
                        // then finally
                        $("#switch-table").append(table);
                    }
                }
            });
        })
    });
</script>
</body>
</html>
