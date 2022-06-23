@extends('api::landingpage.layout')
@section('title', 'Register')
@section('content')
<style>
  #preloader .preloader-content .square {
    background: #00387d;
  }
</style>
<!-- Wrapper -->
<div id="wrapper" class="wrapper official-page landing-page30 slider-menu slider-active">
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
        <div class="win-big">
          <img src="{{ config('api.static_file_uri') }}/images/maria_solo/header_text1a.png" alt="WIN BIG!" />
        </div>
        <div class="register-hoy">
          <img src="{{ config('api.static_file_uri') }}/images/maria_solo/header_text2.png" alt="Registrese hoy y reciba un bona de 2000 creditors y hasta 10 hiros gratis!" />
        </div>
      </div>
    </div>
  </main>
</div><!-- #wrapper end -->

@endsection
