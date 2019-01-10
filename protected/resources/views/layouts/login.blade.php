<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link rel="icon" href="{{ ('img/favicon.png') }}">
    <title>Login | Aplikasi Ujian Berbasis Komputer</title>

    <link rel="stylesheet" href="{{ url('/assets/bootstrap/css/bootstrap.min.css') }}">
    <link href="{!! url('css/login.css') !!}" rel="stylesheet">

  </head>
  <body>
    <div class="container">
    	@yield('content')
	</div>
	<script src="{{ url('/assets/assets/vendor/jquery.min.js') }}"></script>
  <script src="{{ url('lib/bootstrap/js/bootstrap.js') }}"></script>
  <script src="{{ url('/js/jquery.backstretch.min.js') }}"></script>
  <script>
    $.backstretch("{{ url('/img/bg2.jpg') }}", {speed: 150});
    
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
  </script>
  </body>
</html>