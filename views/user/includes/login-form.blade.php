<form id="login">
  <div id="errors" class="alert alert-danger d-none" role="alert"></div>
  <div class="form-group">
    <label for="email">{{ __('Email address') }}</label>
    <input type="email" class="form-control" id="email" name="email" required>
  </div>
  <div class="form-group">
    <label for="password">{{ __('Password') }}</label>
    <input type="password" class="form-control" id="password" name="password" required>
  </div>
  <button id="submit" type="submit" class="btn btn-primary w-100">{{ __('Login') }}</button>
</form>
<form id="login-2fa" class="d-none">
  <div id="errors-2fa" class="alert alert-danger d-none" role="alert"></div>
  <div class="form-group">
    <label for="otp">{{ __('Authentication code') }}</label>
    <input class="form-control" id="otp" name="otp" placeholder="6-digit code" required>
  </div>
  <button id="verify" type="submit" class="btn btn-primary w-100">{{ __('Verify') }}</button>
</form>
<hr>
<div id="login-info">
  <div class="text-center"><small><a href="/{{ $variables['locale'] }}/user/register">{{ __('Create New Account') }}</a></small></div>
  <div class="text-center"><small><a href="/{{ $variables['locale'] }}/user/forgotpassword">{{ __('Forgot your password?') }}</a></small></div>
</div>
<div id="otp-info" class="d-none">
  <small>{{ __('Open the two-factor authenticator (TOTP) app on your mobile device to view your authentication code.') }}</small>
</div>
<script src="/gamebetr/dist/login.js"></script>
