@extends('api::user.layout')
@section('title', 'Forgot Password')
@section('content')
@include('api::user.default.css')
<div class="container">
  <div class="card">
    <div class="logo"><a target="_parent" href="{{ $variables['client_url'] }}"><img src="{{ $variables['logo'] }}"></a></div>
    <h3 id="form-title">{{ __('Forgot Password') }}</h3>
      @include('api::user.includes.forgotpassword-form')
  </div>
</div>
@include('api::user.includes.footer')
@endsection
