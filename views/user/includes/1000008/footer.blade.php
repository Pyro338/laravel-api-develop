<footer id="footer" class="footer">
  <div class="container">
    <div class="footer-icons text-center">
      <ul class="icons">
        <li><a href="#"><img src="{{ config('api.static_file_uri') }}/images/18plus.png"></a></li>
        <li><a href="#"><img src="{{ config('api.static_file_uri') }}/images/gc-logo.png"></a></li>
        <li><a href="#"><img src="{{ config('api.static_file_uri') }}/images/gamcare.jpg"></a></li>
        <li><a href="#"><img src="{{ config('api.static_file_uri') }}/images/bsv.png"></a></li>
      </ul>
    </div>
    <div class="text-center text-muted">
      <p>{{ __('Betcoin.ag is licensed by Dutch Antilles Management N.V., License GLH-OCCHKTW0706262018. Casino games offered on Betcoin.ag are provided and regulated by Gaming Services Provider, N.V., who are operating under the license 365/JAZ issued to CURACAO eGAMING by the Government of Curacao. Company registration number 10692 and corporate address 9 Abraham de Veerstraat.') }}</p>
    </div>
    <div class="copyright text-center">
      <ul class="footer-links">
        <li>&copy; {{ date('Y') }} Betcoin.ag</li>
        <li><a href="{{ $variables['client_url'] }}/privacy">{{ __('Privacy') }}</a></li>
        <li><a href="{{ $variables['client_url'] }}/terms">{{ __('Terms') }}</a></li>
      </ul>
    </div>
  </div>
</footer>