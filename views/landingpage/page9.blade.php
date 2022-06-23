@extends('api::landingpage.layout')
@section('title', 'Landing Page9')
@section('content')

  <!-- Wrapper -->
  <div id="wrapper" class="wrapper landing-page5 landing-page9 slider-menu slider-active">
    <!-- register box -->
    <div class="register-box">
      <a href="#" class="register-toggler">Close</a>
      <!-- Logo -->
      <div id="logo" class="navbar-brand">
        <a href="/landing/page9" class="logo"><img src="{{ asset('gamebetr/images/betcoin-ag.png') }}" alt="Betcoin"></a>
      </div><!-- #logo end -->
      <h4 class="title mb-0">Create a free account and claim your 100% bonus!</h4>
      <P class="text-muted">(UP TO 1 BTC + 10 FREE SPINS)</P>
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

      <div class="footer-icons row mt-5">
        <div class="col-sm-6 text-left">
          <div class="logo"><a href="/"><img src="{{ asset('gamebetr/images/betcoin_icon.png') }}" alt="betcoin" /></a></div>
          <div class="copy text-muted">© 2013-2020 Betcoin.ag</div>
        </div>
        <div class="col-sm-6 text-right">
          <div class="license">
            <a href="https://licensing.gaming-curacao.com/validator/?lh=d514a95f2f32f8625a16439b5ab9625f">
              <img src="{{ asset('gamebetr/images/gc-logo-color.png') }}" alt="gc-logo" />
            </a>
          </div>
          <div class="social">
            <a class="facebook" href="https://www.facebook.com/Betcoin-762073850474822/"><img src="{{ asset('gamebetr/images/facebook.svg') }}" alt="facebook" /></a>
            <a class="twitter" href="https://www.twitter.com/betcoinag"><img src="{{ asset('gamebetr/images/twitter.svg') }}" alt="twitter" /></a>
          </div>
        </div>
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

        <div class="text-center font-italic">
          <h4>THE PREMIER GAMING DESTINATION FOR BITCOIN & CRYPTOCURRENCY GAMING</h4>
          <h1 class="text-xl headingimg"><img src="{{ asset('gamebetr/images/heading9.png') }}" alt="ESPORTS SPORTSBOOK & CASINO" /></h1>
        </div>
      </div>
    </main>
    <div class="main-chars-bg mt-auto"><img src="{{ asset('gamebetr/images/main_chars9.png') }}" alt="main chars" /></div>
  </div><!-- #wrapper end -->

@endsection
