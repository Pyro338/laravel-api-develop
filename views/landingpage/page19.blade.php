@extends('api::landingpage.layout')
@section('title', 'Landing Page19')
@section('content')

  <!-- Wrapper -->
  <div id="wrapper" class="wrapper official-page landing-page19 slider-menu slider-active">
	<div class="bgImage"><div class="bgImage-inner"></div></div>

    <!-- register box -->
    <div class="register-box">
      <a href="#" class="register-toggler">Close</a>
      <!-- Logo -->
      <div id="logo" class="navbar-brand">
        <a href="/landing/page16" class="logo"><img src="{{ asset('gamebetr/images/logo.png') }}" alt="Playbetr"></a>
      </div><!-- #logo end -->
      <h4 class="title">Create your free account and claim your bonus!</h4>
      <form action="#">
        <div class="form-group"><label>Username</label><input class="form-control" type="text"
            placeholder="Name your account" required="">
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="email" class="form-control" id="email" placeholder="e.g. you@example.com">
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

    <main class="main">
      <div class="container">
	    <div class="official-body-text">
	      <div class="official-logo-section">
            <div class="blogo"><img src="{{ asset('gamebetr/images/officialpage/logos.png') }}" alt="Betting partner of paris saint-germain" /></div>
            <div class="tagline"><img src="{{ asset('gamebetr/images/officialpage/tagline.png') }}" alt="play with winning team today" /></div>
          </div>

          <div class="bonus-text">
            <img src="{{ asset('gamebetr/images/officialpage/bonus_text.png') }}" alt="Sign up today and receive a 2,000 credit deposit bonus and up to 10 free spins!" /></div>
        </div>
      </div>
    </main>
  </div><!-- #wrapper end -->

@endsection
