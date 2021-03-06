<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ ('img/favicon.png') }}">
    <title>Aplikasi Ujian Berbasis Komputer</title>

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
    $.backstretch("{{ url('/img/banner.jpg') }}", {speed: 150});
    
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
  </script>
  </body>
</html>