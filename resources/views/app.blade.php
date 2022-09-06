<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('settings.appSettings.title') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->

    <!-- Theme css -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap4.min.css') }}">


    <!-- Scripts -->
    @routes
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <script src="{{ mix('js/app.js') }}" defer></script>

    <!-- Theme js -->
    <script src="{{ asset('assets/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/jquery.slimscroll.js') }}"></script>--}}
    {{-- <script src="{{ asset('assets/js/Chart.bundle.js') }}"></script>--}}
    {{-- <script src="{{ asset('assets/js/chart.js') }}"></script>--}}
    {{-- <script src="{{ asset('assets/js/app.js') }}"></script>--}}

    <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>

    <script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>
</head>

<body onload="myFunction()" class="font-sans antialiased">
    <div id="loader" style="position: fixed;top: 50%;left: 50%;width: 5rem; height: 5rem;" class="spinner-grow text-info" role="status">
        <span class="sr-only">Loading...</span>
    </div>

    <div style="display:none;" id="myDiv" class="animate-bottom">
        @inertia
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
</body>

</html>

<script>
    var myVar;

    function myFunction() {
        myVar = setTimeout(showPage, 3000);
    }

    function showPage() {
        document.getElementById("loader").style.display = "none";
        document.getElementById("myDiv").style.display = "block";
    }
</script>