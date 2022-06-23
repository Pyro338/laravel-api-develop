@extends('api::landingpage.layout')
@section('title', 'Register')
@section('content')
<style>
  #preloader .preloader-content .square {background:#d00;}
</style>
  <!-- Wrapper -->
  <div id="wrapper" class="wrapper landing-page5 landing-page21 slider-menu slider-active">
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

    <main class="main h-100 pt-5">
        <div class="etherscan_graphic"><img src="{{ config('api.static_file_uri') }}/images/etherscan_graphic.png" alt="etherscan graphic" /></div>
        <div class="promotion-text"></div>
        <div class="main-chars-bg mt-auto"><img src="{{ config('api.static_file_uri') }}/images/main_chars21.png" alt="main chars" /></div>
    </main>
  </div><!-- #wrapper end -->

@endsection
