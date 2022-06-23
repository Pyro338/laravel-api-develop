@extends('api::landingpage.layout')
@section('title', 'Register')
@section('content')
<style>
  #preloader .preloader-content .square {background:#d00;}
</style>
  <!-- Wrapper -->
  <div id="wrapper" class="wrapper landing-page5 landing-page22 slider-menu slider-active">
	<div class="bgImage"></div>
    <!-- register box -->
    <div class="register-box">
      <a href="#" class="register-toggler">Close</a>
      <!-- Logo -->
      <div id="logo" class="navbar-brand">
        <a target="_parent" href="{{ $variables['client_url'] }}" class="logo"><img src="{{ config('api.static_file_uri') }}/images/betcoin-ag.png" alt="Betcoin"></a>
      </div><!-- #logo end -->
      <h4 class="title mb-0">Create a free account and claim your 100% bonus!</h4>
      <p class="text-muted">(UP TO 2 BTC + 10 FREE SPINS)</p>
      @include('api::user.includes.register-form')
      <hr>
      @include('api::user.includes.1000008.footer')
    </div>

    <main class="main pt-5">
	  <div class="interbox">
        <div class="utb-section"><img src="{{ config('api.static_file_uri') }}/images/utb_section.png" alt="utb_section" /></div>
        <ul>
	      <li class="bitcion">
	        <h4>UseTheBitcoin Signup Bonus</h4>
	        Up to 2 BTC + 10 Free Spins
	      </li>
	      <li class="choices">
	        <h4>Your Choice Matters!</h4>
	        1,000+ Casino Games & 40,000 Sports!
	      </li>
	      <li class="bonus">
	        <h4>Claim Bonus Now!</h4>
	        Sign up today and claim your bonus
	      </li>
        </ul>
      </div>
    </main>
  </div><!-- #wrapper end -->

@endsection
