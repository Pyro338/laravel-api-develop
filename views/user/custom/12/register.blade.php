@extends('api::landingpage.layout')
@section('title', 'Register')
@section('content')
<style>
  #preloader .preloader-content .square {background:#d6a60c;}
</style>
  <!-- Wrapper -->
  <div id="wrapper" class="wrapper landing-page8 video-page landing-page12 slider-menu slider-active">
    <video autoplay muted loop id="bgVideo" preload="auto" poster="{{ config('api.static_file_uri') }}/images/videopage/bscscan_bg_withgraphic_static.jpg">
	  <source src="{{ config('api.static_file_uri') }}/video/bscscan_bg_withgraphic_animated.mp4" type="video/mp4">
	</video>

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
    
    <main class="main h-100">
      <div class="container h-100 d-flex flex-column">
	    <div class="video-hightlight-content">
          <div class="maintext-section">
            <img src="{{ config('api.static_file_uri') }}/images/videopage/bscscan_maintext.png" alt="Playbetr">
          </div>

          <div class="logo-tagline">
	        <img src="{{ config('api.static_file_uri') }}/images/videopage/logos.png" alt="Playbetr"> <br />
            <img src="{{ config('api.static_file_uri') }}/images/videopage/tagline.png" alt="Playbetr">
          </div>
        </div>
      </div>
    </main>
  </div><!-- #wrapper end -->

@endsection
