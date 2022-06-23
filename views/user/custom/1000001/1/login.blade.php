@extends('api::user.layout')
@section('title', 'Login')
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-2 bg-dark">
      <div class="card text-white bg-dark mb-3 mt-3">
        <div class="card-header">Login</div>
        <div class="card-body">
          @include('api::user.includes.login-form')
        </div>
      </div>
      @include('api::user.includes.footer')
    </div>
    <div class="col-10" style="height:100vh;background:url(https://free4kwallpapers.com/uploads/originals/2020/09/20/cool-geometric-triangular-abstract-wallpaper.jpg);background-size:cover;"></div>
  </div>
</div>
@endsection
