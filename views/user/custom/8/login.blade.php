@extends('api::landingpage.layout')
@section('title', 'Login')
@section('content')
<style>
  #preloader .preloader-content .square {background:#1c61b2;}
</style>
  <!-- Wrapper -->
  <div id="wrapper" class="wrapper landing-page8 slider-menu slider-active">
    <!-- register box -->
    <div class="register-box">
      <a href="#" class="register-toggler">Close</a>
      <!-- Logo -->
      <div id="logo" class="navbar-brand">
        <a href="/landing/page8" class="logo"><img src="{{ config('api.static_file_uri') }}/images/logo.png" alt="Playbetr"></a>
      </div><!-- #logo end -->
      <h4 class="title">Create your free account and claim your bonus!</h4>
      @include('api::user.includes.login-form')
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
          <h1>Join the #1 Cryptocurrency Casino & Sportsbook</h1>
          <h1 class="text-xl">START PLAYING TODAY</h1>
        </div>
      </div>
    </main>
    <div class="main-chars-bg mt-auto"><img src="{{ config('api.static_file_uri') }}/images/main_chars6.png" alt="main chars" /></div>
  </div><!-- #wrapper end -->

@endsection
