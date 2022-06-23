@extends('api::landingpage.layout')
@section('title', 'Register')
@section('content')
<style>
  #preloader .preloader-content .square {
    background: #00387d;
  }
</style>
<!-- Wrapper -->
<div id="wrapper" class="wrapper official-page natalia-mc-kekel landing-page34 slider-menu slider-active">
  <div class="bgImage">
    <div class="bgImage-inner"></div>
  </div>

  <!-- register box -->
  <div class="register-box">
    <a href="#" class="register-toggler">Close</a>
    <!-- Logo -->
    <div id="logo" class="navbar-brand">
      <a target="_parent" href="{{ $variables['client_url'] }}" class="logo"><img src="{{ config('api.static_file_uri') }}/images/logo.png" alt="Playbetr"></a>
    </div><!-- #logo end -->
    <h4 class="title">{{ __('Create your free account and claim your bonus!') }}</h4>
    @include('api::user.includes.register-form')
    <hr>
    @include('api::user.includes.1000001.footer')
  </div>

  <main class="main">
    <div class="container">
      <div class="official-body-text">
        <div class="official-logo-section">
          <div class="blogo"><img src="{{ config('api.static_file_uri') }}/images/mc-kekel/header_1.png" alt="Playbetr" /></div>
          <div class="instalogo"><img src="{{ config('api.static_file_uri') }}/images/mc-kekel/header_2_mckekel.png" alt="Instagram" /></div>
        </div>

        <div class="bonus-text">
          <div class="bonus-txt1"><img src="{{ config('api.static_file_uri') }}/images/mc-kekel/header_text_1_mckekel.png" alt="Ganhar Muito" /></div>
          <div class="bonus-txt2"><img src="{{ config('api.static_file_uri') }}/images/mc-kekel/header_text_2.png" alt="Register" /></div>
        </div>
      </div>
    </div>
  </main>
</div><!-- #wrapper end -->

@endsection
