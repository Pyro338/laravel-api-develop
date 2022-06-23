@extends('api::user.layout')
@section('title', 'Cancel Withdrawal')
@section('content')
@include('api::user.default.css')
<div class="container">
  <div class="card">
    <div class="logo"><a target="_parent" href="{{ $variables['client_url'] }}"><img src="{{ $variables['logo'] }}"></a></div>
    <h3>{{ __('Your withdrawal has been cancelled') }}</h3>
  </div>
  @include('api::user.includes.footer')
</div>
@endsection
