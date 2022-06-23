<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" media="all" href="//fonts.googleapis.com/css?family=Titillium+Web:400,700&amp;display=swap" />
    <link rel="stylesheet" href="{{ asset('gamebetr/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('gamebetr/css/colors.css') }}" />
    <title>@yield('title')</title>

  </head>
  <body>
    @yield('content')

    <div id="preloader">
      <div class="preloader-content">
        <div class="spinner">
          <div class="square"></div>
          <div class="square"></div>
          <div class="square"></div>
          <div class="square"></div>
        </div>
        <div>Loading...</div>
      </div>
    </div><!-- #preloader end -->

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" integrity="sha512-F5QTlBqZlvuBEs9LQPqc1iZv2UMxcVXezbHzomzS6Df4MZMClge/8+gXrKw2fl5ysdk4rWjR0vKS7NNkfymaBQ==" crossorigin="anonymous"></script>
    <script src="{{ asset('gamebetr/js/global.js') }}"></script>
    <script src="{{ asset('gamebetr/js/sportsradarpixel.js') }}"></script>
    <script src="{{ asset('gamebetr/js/facebookpixel.js') }}"></script>
  </body>
</html>
