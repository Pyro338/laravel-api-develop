@extends('api::landingpage.layout')
@section('title', 'Landing Page8')
@section('content')

  <!-- Wrapper -->
  <div id="wrapper" class="wrapper landing-page8 slider-menu slider-active">
    <!-- register box -->
    <div class="register-box">
      <a href="#" class="register-toggler">Close</a>
      <!-- Logo -->
      <div id="logo" class="navbar-brand">
        <a href="/landing/page8" class="logo"><img src="{{ asset('gamebetr/images/logo.png') }}" alt="Playbetr"></a>
      </div><!-- #logo end -->
      <h4 class="title">Create your free account and claim your bonus!</h4>
      <form action="#">
        <div class="form-group"><label>Username</label><input class="form-control" type="text"
            placeholder="Name your account" required="">
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="email" class="form-control" id="email" placeholder="Email">
        </div>
        <div class="form-group form-row">
          <label class="col-12">Date of Birth</label>
          <div class="col-3">
            <input type="text" class="form-control" placeholder="DD">
          </div>
          <div class="col-3">
            <input type="text" class="form-control" placeholder="MM">
          </div>
          <div class="col-6">
            <input type="text" class="form-control" placeholder="YYYY">
          </div>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" class="form-control" id="pwd" placeholder="Password">
        </div>
        <div class="text-center text-muted text-sm mb-3">By creating an account, I hereby acknowledge that
          I am above 18 years old and agree to the <a href="#">Terms and Conditions</a></div>

        <button type="submit" class="btn btn-primary btn-block">REGISTER</button>
      </form>
      <div class="text-center text-sm mt-3">Have an account? <a href="#">Sign In</a></div>

      <div class="footer-icons text-center mt-5">
        <ul class="icons">
          <li><a href="#"><img src="{{ asset('gamebetr/images/18plus.png') }}"></a></li>
          <li><a href="#"><img src="{{ asset('gamebetr/images/gc-logo.png') }}"></a></li>
          <li><a href="#"><img src="{{ asset('gamebetr/images/gamcare.jpg') }}"></a></li>
          <li><a href="#"><img src="{{ asset('gamebetr/images/bsv.png') }}"></a></li>
        </ul>
      </div>
    </div>

    <main class="main h-100">
      <div class="container h-100 d-flex flex-column">
        <div class="menu-section pt-5 pb-4">
          <ul class="feature-menu">
            <li class="create-account"><a href="#">Create an account</a></li>
            <li class="select-game"><a href="#">Select your game</a></li>
            <li class="place-bet last"><a href="#">Place your bet and enjoy</a></li>
          </ul>
        </div>

        <div class="text-center">
          <h1>Join the #1 Cryptocurrency Casino & Sportsbook</h1>
          <h1 class="text-xl">START PLAYING TODAY</h1>
        </div>
      </div>
    </main>
    <div class="main-chars-bg mt-auto"><img src="{{ asset('gamebetr/images/main_chars6.png') }}" alt="main chars" /></div>
  </div><!-- #wrapper end -->

@endsection
