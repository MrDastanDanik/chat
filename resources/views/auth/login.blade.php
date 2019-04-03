@extends('layout')

@section('title', 'Login')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}
        <input type="text" name="username" placeholder="Name"/>
        <input type="password" name="password" placeholder="Password"/>
        <input type="submit" value="Login">
    </form>
@endsection