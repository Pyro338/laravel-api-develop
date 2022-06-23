@extends('api::landingpage.layout')
@section('title', 'Register')
@section('content')
<style>
  #preloader .preloader-content .square {background:#00387d;}
</style>
  <!-- Wrapper -->
  <div id="wrapper" class="wrapper official-page landing-page19 slider-menu slider-active">
	<div class="bgImage"><div class="bgImage-inner"></div></div>

    <!-- register box -->
    <div class="register-box">
      <a href="#" class="register-toggler">Close</a>
      <!-- Logo -->
      <div id="logo" class="navbar-brand">
        <a target="_parent" href="{{ $variables['client_url'] }}" class="logo"><img src="{{ config('api.static_file_uri') }}/images/logo.png" alt="Playbetr"></a>
      </div><!-- #logo end -->
      <h4 class="title">¡Crea tu cuenta gratis y reclama por tus bonos!</h4>
      @include('api::user.includes.register-form')
      <hr>
      @include('api::user.includes.1000001.footer-es')
    </div>

    <main class="main">
      <div class="container">
	    <div class="official-body-text">
	      <div class="official-logo-section">
            <div class="blogo"><img src="{{ config('api.static_file_uri') }}/images/psg/logos_esp.png" alt="Betting partner of paris saint-germain" /></div>
            <div class="tagline"><img src="{{ config('api.static_file_uri') }}/images/psg/tagline_esp.png" alt="play with winning team today" /></div>
          </div>

          <div class="bonus-text">
            <img src="{{ config('api.static_file_uri') }}/images/psg/bonus_text_esp.png" alt="Sign up today and receive a 2,000 credit deposit bonus and up to 10 free spins!" /></div>
        </div>
      </div>
    </main>
  </div><!-- #wrapper end -->

@endsection
