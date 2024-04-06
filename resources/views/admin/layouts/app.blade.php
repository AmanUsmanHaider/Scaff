<!DOCTYPE html>
<html lang="en">
<head>
{{--    <title>@yield('page_title')</title>--}}
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    {!! JsonLd::generate() !!}
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
