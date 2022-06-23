@extends('api::landingpage.layout')
@section('title', 'Register')
@section('content')
<style>
  #preloader .preloader-content .square {background:#008e9c;}
</style>

  <!-- Wrapper -->
  <div id="wrapper" class="wrapper">
    <main class="main">
      <div class="container">
        <div class="row mb-5">
          <div class="col-lg-8 pb-5">
            <div class="main-chars-bg"><img src="{{ config('api.static_file_uri') }}/images/main_chars.png" alt="main chars" /></div>
            <!-- Logo -->
            <div id="logo" class="navbar-brand">
              <a target="_parent" href="{{ $variables['client_url'] }}" class="logo"><img src="{{ config('api.static_file_uri') }}/images/logo.png" alt="Playbetr"></a>
            </div><!-- #logo end -->
            <h1>Join Now and Claim <br />Your Welcome Bonus!</h1>
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
              @include('api::user.includes.register-form')
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
    @include('api::user.includes.1000001.footer')

  </div><!-- #wrapper end -->

@endsection
