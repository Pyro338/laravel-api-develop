@extends('api::landingpage.layout')
@section('title', 'Register')
@section('content')
<style>
  #preloader .preloader-content .square {background:#002041;}
</style>
  <!-- Wrapper -->
  <div id="wrapper" class="wrapper official-page landing-page26 slider-menu slider-active">
	<div class="bgImage"><div class="bgImage-inner"></div></div>

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

    <main class="main">
      <div class="container">
	    <div class="esp-body-text">
	      <div class="esp-logo">
            <img src="{{ config('api.static_file_uri') }}/images/logo_esp.png" alt="esp" />
          </div>
          <div class="divider-line">
            <img src="{{ config('api.static_file_uri') }}/images/esp_divider_line.png" alt="line" />
          </div>
          <div class="esp-bonus">
            <img src="{{ config('api.static_file_uri') }}/images/bonus_esp.png" alt="bonus!" /></div>
        </div>
      </div>
    </main>
  </div><!-- #wrapper end -->

@endsection
