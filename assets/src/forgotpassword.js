import Auth from '../../node_modules/@gamebetr/api-client-js/src/Services/auth';
import Token from '../../node_modules/@gamebetr/api-client-js/src/token';

window.onload = function () {

  let token = new Token();
  token.inheritCookies();

  let form = document.getElementById('forgot_password');
  let info = document.getElementById('info');
  let errors = document.getElementById('errors');
  let submit = document.getElementById('submit');
  let email = document.getElementById('email');
  let new_password = document.getElementById('new_password');

  // instantiate
  let auth = new Auth();

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    info.classList.remove('d-block');
    errors.classList.remove('d-block');
    email.classList.remove('is-invalid');
    new_password.classList.remove('is-invalid');
    submit.setAttribute('disabled', '');
    submit.textContent = 'Validating...';

    auth.forgotPassword(email.value, new_password.value)
      .then(data => {
        console.log(data);
        submit.removeAttribute('disabled');
        submit.textContent = 'Reset Password';
        if (data.length == 0) {
          resetSuccess();
        } else {
          if (data.errors[0].status == 422) {
            errors.textContent = data.errors[0].detail;
            errors.classList.add('d-block');
            email.classList.add('is-invalid');
            new_password.classList.add('is-invalid');
          } else {
            resetSuccess();
          }
        }
      }); 
  });

  function resetSuccess() {
    info.textContent = 'If the email address entered exists you will be emailed a confirmation link to complete the password reset process.';
    info.classList.add('d-block');
  }

};
