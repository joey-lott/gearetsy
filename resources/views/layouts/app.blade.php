<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>GB Lightning Lister</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
                  <div class="row">
                    <div class="col-md-12">
                      <a style="position:relative;top:5px;left:10px" href="{{ url('/') }}"><img src="/logo.png"></a>
                      @if(auth()->check())
                        <a href="/logout" style="position:absolute;right:30px;top:4px">logout</a>
                      @endif
                    </div>
                  </div>
        </nav>
        <div class="col-md-8 col-md-offset-2">
          <div class="panel panel-default">
            @yield('content')
          </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
