@extends('api::landingpage.layout')
@section('title', 'Register')
@section('content')
<style>
  #preloader .preloader-content .square {background:#00387d;}
</style>
  <!-- Wrapper -->
  <div id="wrapper" class="wrapper official-page landing-page25 slider-menu slider-active">
	<div class="bgImage"></div>

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
	    <div class="casinobox">
            <div class="leadborad-promo"><img src="{{ config('api.static_file_uri') }}/images/utb_section4.png" alt="Playbeter and Bitcoin" /></div>
            <div class="participate-now"><img src="{{ config('api.static_file_uri') }}/images/utb_section3_button.png" alt="Participate now" /></div>
            <div class="freebet-offer"><img src="{{ config('api.static_file_uri') }}/images/utb_section4_freebet_text.png" alt="Free bet offer" /></div>
        </div>
    </main>
  </div><!-- #wrapper end -->

@endsection
