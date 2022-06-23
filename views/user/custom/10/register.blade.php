@extends('api::landingpage.layout')
@section('title', 'Register')
@section('content')

<style>
  #preloader .preloader-content .square {background:#00387d;}
</style>
  <!-- Wrapper -->
  <div id="wrapper" class="wrapper landing-page8 landing-page10 slider-menu slider-active">
    <!-- register box -->
    <div class="register-box">
      <a href="#" class="register-toggler">Close</a>
      <!-- Logo -->
      <div id="logo" class="navbar-brand">
        <a target="_parent" href="{{ $variables['client_url'] }}" class="logo"><img src="{{ config('api.static_file_uri') }}/images/logo.png" alt="Playbetr"></a>
      </div><!-- #logo end -->
      <h4 class="title">Create your free account and claim your bonus!</h4>
      @include('api::user.includes.register-form')
      <hr>
      @include('api::user.includes.1000001.footer')
    </div>

    <main class="main h-100">
      <div class="container h-100 d-flex flex-column">
        <div class="menu-section pt-5 pb-4">
          <ul class="feature-menu">
            <li class="create-account"><a href="#">Create an account</a></li>
            <li class="select-game"><a href="#">Select your game</a></li>
            <li class="place-bet last"><a href="#">Place your bet and enjoy</a></li>
          </ul>
        </div>

        <div class="text-center">
          <h1>#1 eSports Crypto Casino & Sportsbook</h1>
          <h1 class="text-xl backshadow">BET ON YOUR FAVORITE MATCHES</h1>
        </div>
      </div>
    </main>
    <div class="main-chars-bg mt-auto"><img src="{{ config('api.static_file_uri') }}/images/main_chars10.png" alt="main chars" /></div>
  </div><!-- #wrapper end -->

@endsection
