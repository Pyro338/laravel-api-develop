@extends('api::user.layout')
@section('title', 'Login')
@section('content')
@include('api::user.default.css')
<div class="container">
  <div class="card">
    <div class="logo"><a target="_parent" href="{{ $variables['client_url'] }}"><img src="{{ $variables['logo'] }}"></a></div>
    <h3 id="form-title">{{ __('Login') }}</h3>
      @include('api::user.includes.login-form')
  </div>
</div>
@include('api::user.includes.footer')
@endsection
