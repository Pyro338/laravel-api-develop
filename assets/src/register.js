import Affiliate from '../../node_modules/@gamebetr/api-client-js/src/Services/affiliate';
import Auth from '../../node_modules/@gamebetr/api-client-js/src/Services/auth';
import Event from '../../node_modules/@gamebetr/api-client-js/src/Services/event';
import Token from '../../node_modules/@gamebetr/api-client-js/src/token';

window.onload = function () {

  let token = new Token();
  token.inheritCookies();

  let form = document.getElementById('register');
  let errors = document.getElementById('errors');
  let submit = document.getElementById('submit');
  let username = document.getElementById('username');
  let email = document.getElementById('email');
  let password = document.getElementById('password');
  let promo_code = document.getElementById('promo_code');
  
  let flag = false; // for pixels

  let auth = new Auth();
  let event = new Event();

  // pixel events
  username.oninput = handleOnInput;
  email.oninput = handleOnInput;
  password.oninput = handleOnInput;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    errors.classList.add('d-none');
    submit.setAttribute('disabled', '');
    submit.textContent = 'Validating...';

    auth.validateRegister(username.value, email.value, password.value);

    // set affiliate if cookie exists
    let affiliate = new Affiliate();
    let affiliate_id = affiliate.getCookieAffiliateId();

    auth.register(username.value, email.value, password.value, affiliate_id)
      .then(data => {
        if (data.data) {
          console.log(data);
          submit.textContent = 'Registering...';

          // fire event
          event.registerEnd(data);

          // do conversion ids here
          var url_string = window.location.href;
          var url = new URL(url_string);
          affiliate.createConversion(
            token.getDomainId(),
            data.data.attributes.integer_id,
            affiliate_id,
            url.searchParams.get('t'),
            url.searchParams.get('c'),
            promo_code.value
          );

          // login and get token
          auth.login(email.value, password.value)
            .then(data => {
              if (data.data) {
                console.log(data);
                submit.textContent = 'Logging In...';

                // fire event
                event.login(data);
              } else {
                submit.removeAttribute('disabled');
                submit.textContent = 'Register';
                errors.textContent = 'There was an error setting up your account. Please try again.';
              }
            });
        
        } else {
          submit.removeAttribute('disabled');
          submit.textContent = 'Register';
          errors.textContent = data.errors[0].detail;
          errors.classList.add('d-block');
        }
      });
  });

  function handleOnInput(e) {
    if (flag) {
      console.log("flag hit");
      return;
    }
    else {
      console.log(e.target.id);
      console.log(e.target.value);
      flag = true;
      event.registerStart();
    }
  }

};
