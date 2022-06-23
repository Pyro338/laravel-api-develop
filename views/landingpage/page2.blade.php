@extends('api::landingpage.layout')
@section('title', 'Landing Page2')
@section('content')

  <!-- Wrapper -->
  <div id="wrapper" class="wrapper landing-page2">
    <main class="main">
      <div class="container">
        <div class="row mb-5">
          <div class="col-lg-8 pb-5">
            <div class="main-chars-bg"><img src="{{ asset('gamebetr/images/main_chars.png') }}" alt="main chars" /></div>
            <!-- Logo -->
            <div id="logo" class="navbar-brand">
              <a href="/landing/page2" class="logo"><img src="{{ asset('gamebetr/images/logo-blue.png') }}" alt="Playbetr"></a>
            </div><!-- #logo end -->
            <h1><span class="highlight">Join Now</span> and Claim <br />Your <span class="highlight">Welcome Bonus</span>!</h1>
            <p>#1 Licensed Cryptocurrency Casino & Sportsbook</p>
            <ul class="features">
              <li class="vip">
                <b>Best VIP program</b> <br />
                Up to 33% Lossback
              </li>
              <li class="betback">
                <b>Betback</b> for Every<br />
                Casino and Sports Bet
              </li>
              <li class="btccurrency">
                <b>BTC, LTC, ETH, XRP,</b><br />
                XMR and More!
              </li>
              <li class="joinnow">
                <b>Join Now</b> for Your<br />
                100% Deposit Bonus
              </li>
            </ul>
          </div>
          <div class="col-lg-4">
            <div class="register-box">
              <h4 class="title">Register Now</h4>
              <form action="#">
                <div class="form-group"><input class="form-control" type="text" placeholder="Username" required="">
                </div>
                <div class="form-group">
                  <input type="password" class="form-control" id="pwd" placeholder="Password">
                </div>
                <div class="form-group">
                  <input type="email" class="form-control" id="email" placeholder="Email">
                </div>
                <div class="form-group form-row mt-5">
                  <div class="col-sm-3">
                    <input type="text" class="form-control" placeholder="DD">
                  </div>
                  <div class="col-sm-3">
                    <input type="text" class="form-control" placeholder="MM">
                  </div>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" placeholder="YYYY">
                  </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">REGISTER</button>
              </form>
              <div class="text-center text-muted text-sm mt-3">By creating an account, I hereby acknowledge that
                I am above 18 years old and agree to the <a href="#">Terms and Conditions</a></div>
            </div>
          </div>
        </div>

        <div class="menu-section">
          <ul class="feature-menu">
            <li class="create-account"><a href="#">Create an account</a></li>
            <li class="select-game"><a href="#">Select your game</a></li>
            <li class="place-bet last"><a href="#">Place your bet and enjoy</a></li>
          </ul>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer id="footer" class="footer">
      <div class="container">
        <div class="text-center">
          <p>Playbetr.com is operated and owned by Global Limited Holding EOOD. License GLH-OCCHKTW0706262018. Casino
            games offered on Playbetr.com are provided and regulated by <br />Gaming Services Provider, N.V., who are
            operating under the license 365/JAZ issued to CURACAO eGAMING by the Government of Curacao on August 18,
            1998.</p>
        </div>
        <div class="footer-icons text-center">
          <ul class="icons">
            <li><a href="#"><img src="{{ asset('gamebetr/images/18plus.png') }}"></a></li>
            <li><a href="#"><img src="{{ asset('gamebetr/images/gc-logo.png') }}"></a></li>
            <li><a href="#"><img src="{{ asset('gamebetr/images/gamcare.jpg') }}"></a></li>
            <li><a href="#"><img src="{{ asset('gamebetr/images/bsv.png') }}"></a></li>
          </ul>
        </div>
        <div class="copyright text-center">
          <ul class="footer-links">
            <li>© 2020 Playbetr</li>
            <li><a href="/about-us">About</a></li>
            <li><a href="/affiliates">Affiliates</a></li>
            <li><a href="/support">Support</a></li>
            <li><a href="/privacy-policy">Privacy</a></li>
            <li><a href="/terms-service">Terms</a></li>
          </ul>
        </div>
      </div>
    </footer>

  </div><!-- #wrapper end -->

@endsection
