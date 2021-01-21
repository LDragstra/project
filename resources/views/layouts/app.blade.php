<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
  <link rel="shortcut icon" href="{{ asset('favicon.ico.png') }}" type="image/x-icon">
  <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
    integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

  <!-- Styles -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <link href="{{ asset('css/toastr.css') }}" rel="stylesheet">
</head>

<body>
  <div id="app">
    @include('layouts.partials.nav')

    <div class="d-flex" id="wrapper">

      <!-- Sidebar -->
      <div class="bg-light border-right" id="sidebar-wrapper">

        @include('layouts.partials.sidenav')
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
          <nav class="navbar navbar-expand-lg navbar-dark bg-primary border-bottom">
            <button class="btn btn-primary" id="menu-toggle"> {{ ucfirst(Request::path()) }} </button>
          </nav>

          <div class="container-fluid">

            @yield('content')
          </div>
        </div>
      </div>

    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/index.js') }}" defer></script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
      integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8=" crossorigin="anonymous"></script>

    <script>
      $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
      });
    </script>
    <script>
      $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
      });
    </script>
</body>

</html>
