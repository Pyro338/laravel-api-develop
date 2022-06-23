<form id="forgot_password">
  <div id="info" class="alert alert-success d-none" role="alert"></div>
  <div id="errors" class="alert alert-danger d-none" role="alert"></div>
  <div class="form-group">
    <label for="email">{{ __('Email address') }}</label>
    <input type="email" class="form-control" id="email" name="email" required>
  </div>
  <div class="form-group">
    <label for="new_password">{{ __('New password') }}</label>
    <input type="password" class="form-control" id="new_password" name="new_password" required>
  </div>
  <button id="submit" type="submit" class="btn btn-primary w-100">{{ __('Reset Password') }}</button>
</form>
<hr>
<div class="text-center"><small><a href="/{{ $variables['locale'] }}/user/login"><i class="fas fa-caret-left"></i> {{ __('Back To Login') }}</a></small></div>
<p class="info text-muted"><small><strong>{{ __('Has your email changed?') }}</strong><br>{{ __('If you no longer use the email address associated with your account, you may contact') }} <a target="_parent" href="{{ $variables['client_url'] }}/support">{{ __('Customer Service') }}</a> {{ __('for help restoring access to your account') }}.</small></p>
<script src="/gamebetr/dist/forgotpassword.js"></script>
