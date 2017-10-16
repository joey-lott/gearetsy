<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'GearEtsy') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
                  <div class="row">
                    <div class="col-md-2">
                      <a class="navbar-brand" href="{{ url('/') }}">
                          {{ config('app.name', 'GearEtsy') }}
                      </a>
                    </div>
                    <div class="col-md-8"></div>
                    <div class="col-md-2">
                      @if(auth()->check())
                        <a href="/logout">logout</a>
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
