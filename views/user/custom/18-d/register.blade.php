@extends('api::landingpage.layout')
@section('title', 'Register')
@section('content')
<style>
  #preloader .preloader-content .square {background:#002041;}
</style>
  <!-- Wrapper -->
  <div id="wrapper" class="wrapper official-page landing-page18 slider-menu slider-active">
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
	    <div class="official-body-text">
	      <div class="official-logo-section">
            <img src="{{ config('api.static_file_uri') }}/images/officialpage/top_heading_combined_B.png" alt="official regional betting partner" />
          </div>

          <div class="bonus-text">
            <img src="{{ config('api.static_file_uri') }}/images/officialpage/bonus_text.png" alt="Sign up today and receive a 2,000 credit deposit bonus and up to 10 free spins!" /></div>
        </div>
      </div>
    </main>
  </div><!-- #wrapper end -->

@endsection
