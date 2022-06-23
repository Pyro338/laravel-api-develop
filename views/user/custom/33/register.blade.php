@extends('api::landingpage.layout')
@section('title', 'Register')
@section('content')
<style>
  #preloader .preloader-content .square {
    background: #00387d;
  }
</style>
<!-- Wrapper -->
<div id="wrapper" class="wrapper official-page landing-page33 slider-menu slider-active">

  <!-- register box -->
  <div class="register-box">
    <a href="#" class="register-toggler">Close</a>
    <!-- Logo -->
    <div id="logo" class="atropay-logo">
      <a target="_parent" href="{{ $variables['client_url'] }}" class="logo"><img src="{{ config('api.static_file_uri') }}/images/atropay/logo.png" alt="Playbetr"></a>
    </div><!-- #logo end -->
    <h4 class="title">{{ __('Create your free account and claim your bonus!') }}</h4>
    @include('api::user.includes.register-form')
    <hr>
    @include('api::user.includes.1000001.footer')
  </div>

  <main class="main h-100 pt-5">
    <div class="astropay"><img src="{{ config('api.static_file_uri') }}/images/atropay/astropay_graphic.png" alt="astropay" /></div>
    <div class="promotion-text"></div>
    <div class="main-chars-bg"></div>
  </main>
</div><!-- #wrapper end -->

@endsection
