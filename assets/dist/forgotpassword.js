/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/@gamebetr/api-client-js/src/Services/auth.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@gamebetr/api-client-js/src/Services/auth.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Auth)
/* harmony export */ });
/* harmony import */ var _client__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../client */ "./node_modules/@gamebetr/api-client-js/src/client.js");


class Auth {

  constructor(config = {}) {
    this.config = config;
  }

  // client side validation
  validateRegister(name, email, password) {
    // console.log('Client side validating: ' + name + email + 'pwd');
  }

  register(name, email, password, affiliate_id = 0) {
    let client = new _client__WEBPACK_IMPORTED_MODULE_0__["default"](this.config);
    let data = {
      'name': name,
      'email': email,
      'password': password
    };
    if (affiliate_id > 0) {
    	data.affiliate_id = affiliate_id;
    }
    return client.request('POST', 'user/register', data);
  }

  login(email, password) {
    let client = new _client__WEBPACK_IMPORTED_MODULE_0__["default"](this.config);
    let data = {
      'email': email,
      'password': password,
    };
    return client.request('POST', 'user/login', data);
  }

  login2fa(email, password, key) {
    let client = new _client__WEBPACK_IMPORTED_MODULE_0__["default"](this.config);
    let data = {
      'email': email,
      'password': password,
      'key': key,
    };
    return client.request('POST', 'user/login_2fa', data);
  }

  forgotPassword(email, new_password) {
    let client = new _client__WEBPACK_IMPORTED_MODULE_0__["default"](this.config);
    let data = {
      'email': email,
      'new_password': new_password,
    };
    return client.request('POST', 'user/password', data);
  }

  logout() {
    // console.log('logout');
  }

  enable2fa() {
    //
  }

  disable2fa() {
    //
  }

  updateAvatar() {
    //
  }

  updateProfile() {
    //
  }

}


/***/ }),

/***/ "./node_modules/@gamebetr/api-client-js/src/client.js":
/*!************************************************************!*\
  !*** ./node_modules/@gamebetr/api-client-js/src/client.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Client)
/* harmony export */ });
/* harmony import */ var _token__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./token */ "./node_modules/@gamebetr/api-client-js/src/token.js");


/**
 * Example usage:
 * let client = new Client({'baseUri': 'https://www.site.com'});
 * client.request('GET', 'endpoint').then(data => console.log(data))
 */
class Client {

  constructor(config = {}) {
    const token = new _token__WEBPACK_IMPORTED_MODULE_0__["default"]();

    this.baseUri = token.getApiUri();

    if (typeof config.baseUri !== 'undefined') {
      this.setBaseUri(config.baseUri);
    }
    if (typeof config.token !== 'undefined') {
      this.setToken(config.token);
    } else {
      // console.log('client instantiated with token: ' + token.getToken());
      this.setToken(token.getToken());
    }
  }

  setBaseUri(baseUri) {
    this.baseUri = baseUri;
  }

  setDomainId(domainId) {
    this.domainId = domainId;
  }

  setToken(token) {
    this.token = token;
  }

  request(method, uri, bodyData = null) {
    var myHeaders = {
      'Authorization': 'Bearer ' + this.token
    };

    var requestOptions = {
      method: method,
      headers: myHeaders,
    };

    // form data
    if (bodyData != null) {
      var myBody = new FormData();
      var dataMethods = ['POST', 'PUT', 'PATCH'];
      if (dataMethods.indexOf(method) !== -1) {
        for (const key in bodyData) {
          myBody.append(`${key}`, `${bodyData[key]}`);
        }
      };
      // append body to request
      requestOptions.body = myBody;

    }

    // return response Promise
    var requestUrl = this.baseUri + '/api/v1/' + uri

    // console.log(requestUrl);
    // console.log(requestOptions);

    return fetch(requestUrl, requestOptions)
      .then(response => response.json())
      .catch(function(error) {
        console.log('Fetch Error: ', error);
      });

  }

}


/***/ }),

/***/ "./node_modules/@gamebetr/api-client-js/src/token.js":
/*!***********************************************************!*\
  !*** ./node_modules/@gamebetr/api-client-js/src/token.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Token)
/* harmony export */ });
class Token {

  constructor() {
    this.tokenName = 'gamebetr_token';
    this.baseDomainName = 'gamebetr_base_domain';
    this.apiUriName = 'gamebetr_api_uri';
    this.webUriName = 'gamebetr_web_uri';
    this.domainIdName = 'gamebetr_domain_id';
  }

  // this will inherit cookies from parent source if this page is embeded in iframe
  inheritCookies() {
    window.addEventListener('message', e => {
      if (this.getSafeOrigins().indexOf(e.origin) == -1) {
        return;
      }
      if (typeof e.data.data === 'undefined') {
        return;
      }
      let data = e.data.data;
      if (data.type != 'event') {
        return;
      }
      if (data.attributes.event != 'auth_init') {
        return;
      }
      // set cookies
      this.setCookie(this.apiUriName, data.attributes.cookies[this.apiUriName]);
      this.setCookie(this.webUriName, data.attributes.cookies[this.webUriName]);
      this.setCookie(this.domainIdName, data.attributes.cookies[this.domainIdName]);
      console.log('cookies inherited');
    }, false);
  }

  // store the token as a cookie
  setToken(token, expire) {
    // use auth api expiration as cookie expiration
    let expires = new Date(expire).toUTCString();

    // use the current domain as wildcard domain for cookie
    // let domain = '.' + this.getBaseDomain();
    
    // document.cookie = this.tokenName + '=' + token + ';expires=' + expires + ';domain=' + domain + ';path=/';
    document.cookie = this.tokenName + '=' + token + ';expires=' + expires + ';path=/';
    // console.log('Cookie token set as: ' + token);
    // console.log(expires);
    // console.log(domain);
  }

  setCookie(name, value) {    
    document.cookie = name + '=' + value + ';path=/';
  }

  getToken() {
    return this.getCookie(this.tokenName);
  }

  getBaseDomain() {
    if (this.getCookie(this.baseDomainName)) {
      return this.getCookie(this.baseDomainName);
    } else {
      // default to current host
      let host = window.location.host;
      return host.split('.')[host.split('.').length-2]+'.'+host.split('.')[host.split('.').length-1];
    }
  }
  
  getApiUri() {
    if (this.getCookie(this.apiUriName)) {
      return this.getCookie(this.apiUriName);
    } else {
      return 'https://playerapi.' + this.getBaseDomain();
    }
  }

  getWebUri() {
    if (this.getCookie(this.webUriName)) {
      return this.getCookie(this.webUriName);
    } else {
      return 'https://www.' + this.getBaseDomain();
    }
  }

  getDomainId() {
    if (this.getCookie(this.domainIdName)) {
      return this.getCookie(this.domainIdName);
    } else {
      return false;
    }
  }

  getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }

  getSafeCookies() {
    let cookies = {};
    cookies[this.apiUriName] = this.getCookie(this.apiUriName);
    cookies[this.webUriName] = this.getCookie(this.webUriName);
    cookies[this.domainIdName] = this.getCookie(this.domainIdName);
    return cookies;
  }

  /**
   * These are safe origins to use with window.postMessage()
   */
  getSafeOrigins() {
    let origins = [];
    origins.push('https://www.playbetr.com');
    origins.push('https://www.betcoin.ag');
    origins.push('https://playerapi.playbetr.com');
    origins.push('https://playerapi.betcoin.ag');
    origins.push('https://staging.playbetr.com');
    origins.push('https://staging.betcoin.ag');
    origins.push('https://staging-playerapi.playbetr.com');
    origins.push('https://staging-playerapi.betcoin.ag');
    origins.push('http://staging.playbetr.com');
    origins.push('http://staging.betcoin.ag');
    origins.push('http://staging-playerapi.playbetr.com');
    origins.push('http://staging-playerapi.betcoin.ag');
    origins.push('http://playbetr.lndo.site');
    origins.push('http://betcoin.lndo.site');
    return origins;
  }

}


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!**************************************!*\
  !*** ./assets/src/forgotpassword.js ***!
  \**************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_gamebetr_api_client_js_src_Services_auth__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../node_modules/@gamebetr/api-client-js/src/Services/auth */ "./node_modules/@gamebetr/api-client-js/src/Services/auth.js");
/* harmony import */ var _node_modules_gamebetr_api_client_js_src_token__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../node_modules/@gamebetr/api-client-js/src/token */ "./node_modules/@gamebetr/api-client-js/src/token.js");



window.onload = function () {
  var token = new _node_modules_gamebetr_api_client_js_src_token__WEBPACK_IMPORTED_MODULE_1__["default"]();
  token.inheritCookies();
  var form = document.getElementById('forgot_password');
  var info = document.getElementById('info');
  var errors = document.getElementById('errors');
  var submit = document.getElementById('submit');
  var email = document.getElementById('email');
  var new_password = document.getElementById('new_password'); // instantiate

  var auth = new _node_modules_gamebetr_api_client_js_src_Services_auth__WEBPACK_IMPORTED_MODULE_0__["default"]();
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    info.classList.remove('d-block');
    errors.classList.remove('d-block');
    email.classList.remove('is-invalid');
    new_password.classList.remove('is-invalid');
    submit.setAttribute('disabled', '');
    submit.textContent = 'Validating...';
    auth.forgotPassword(email.value, new_password.value).then(function (data) {
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
})();

/******/ })()
;