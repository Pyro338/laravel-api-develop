import Auth from '../../node_modules/@gamebetr/api-client-js/src/Services/auth';
import Event from '../../node_modules/@gamebetr/api-client-js/src/Services/event';
import Token from '../../node_modules/@gamebetr/api-client-js/src/token';

window.onload = function () {

  let token = new Token();
  token.inheritCookies();

  let form = document.getElementById('login');
  let form_2fa = document.getElementById('login-2fa');
  let form_title = document.getElementById('form-title');
  let login_info = document.getElementById('login-info');
  let otp_info = document.getElementById('otp-info');
  let errors = document.getElementById('errors');
  let errors_2fa = document.getElementById('errors-2fa');
  let submit = document.getElementById('submit');
  let email = document.getElementById('email');
  let password = document.getElementById('password');
  let otp = document.getElementById('otp');
  let verify = document.getElementById('verify');

  // instantiate
  let auth = new Auth();
  let event = new Event();

  // main login form
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    errors.classList.remove('d-block');
    email.classList.remove('is-invalid');
    password.classList.remove('is-invalid');
    submit.setAttribute('disabled', '');
    submit.textContent = 'Validating...';

    // auth.validateEmail(email.value);
    // auth.validatePassword(password.value);

    auth.login(email.value, password.value)
      .then(data => {

        if (typeof data.data !== 'undefined') {
          // 2fa
          if (typeof data.data.verify_2fa !== 'undefined') {
            form.classList.add('d-none');
            form_2fa.classList.add('d-block');
            login_info.classList.add('d-none');
            otp_info.classList.add('d-block');
            form_title.innerHTML = '<i class="fas fa-lock"></i> Two-Factor Authentication';
            return;
          }

          // regular login
          else if (typeof data.data.id !== 'undefined') {
            dataSuccess(data);
          }
        } else {
          submit.removeAttribute('disabled');
          submit.textContent = 'Login';
          errors.textContent = data.errors[0].detail;
          errors.classList.add('d-block');
          email.classList.add('is-invalid');
          password.classList.add('is-invalid');
        }
      }); 
  });

  // 2fa form
  form_2fa.addEventListener('submit', function (e) {
    e.preventDefault();
    errors_2fa.classList.remove('d-block');
    otp.classList.remove('is-invalid');
    verify.setAttribute('disabled', '');
    verify.textContent = 'Verifying...';
    
    auth.login2fa(email.value, password.value, otp.value)
      .then(data => {
        if (data.data) {
          dataSuccess(data);

        // error
        } else {
          verify.removeAttribute('disabled');
          verify.textContent = 'Verify';
          errors_2fa.textContent = 'Invalid authentication code';
          errors_2fa.classList.add('d-block');
          otp.classList.add('is-invalid');
        }
    });
  });

  function dataSuccess(data) {
    console.log(data);
    submit.textContent = 'Logging In...';
    verify.textContent = 'Logging In...';
    
    // fire pixels
    event.login(data);
  }

};
