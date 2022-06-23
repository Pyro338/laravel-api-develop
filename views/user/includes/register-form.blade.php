<form id="register">
  <div id="errors" class="alert alert-danger d-none" role="alert"></div>
  <div class="form-group">
    <label for="username">{{ __('Username') }}</label>
    <input type="textfield" class="form-control" id="username" name="username" required>
  </div>
  <div class="form-group">
    <label for="email">{{ __('Email address') }}</label>
    <input type="email" class="form-control" id="email" name="email" required>
  </div>
  <div class="form-group">
    <label for="password">{{ __('Password') }}</label>
    <input type="password" class="form-control" id="password" name="password" minlength="8" required>
  </div>
  <div class="form-group">
    <label for="promo_code">{{ __('Promo Code') }}</label>
    <input type="textfield" class="form-control" id="promo_code" name="promo_code">
  </div>
  <button id="submit" type="submit" class="btn btn-primary w-100">{{ __('Register') }}</button>
</form>
<hr>
<div class="text-center"><small>{{ __('Already have an account?') }} <a href="/{{ $variables['locale'] }}/user/login">{{ __('Login') }} <i class="fas fa-caret-right"></i></a></small></div>
<script src="/gamebetr/dist/register.js"></script>
