@extends('api::user.layout')
@section('title', 'Forgot Password')
@section('content')
@include('api::user.includes.forgotpassword-form')
@include('api::user.includes.footer')
@endsection
