@extends('api::landingpage.layout')
@section('title', 'Register')
@section('content')
<style>
  #preloader .preloader-content .square {
    background: #00387d;
  }
</style>
<!-- Wrapper -->
<div id="wrapper" class="wrapper official-page landing-page27 slider-menu slider-active">
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
          <div class="blogo"><img src="{{ config('api.static_file_uri') }}/images/maria_psg/header_image.png" alt="SPONSOR DE APUESTAS OFICIAL DE PARIS SAINT-GERMAN" /></div>
        </div>

        <div class="bonus-text">
          <img src="{{ config('api.static_file_uri') }}/images/maria_psg/header_text_combined.png" alt="WIN BIG!" />
        </div>
      </div>
    </div>
  </main>
</div><!-- #wrapper end -->

@endsection
