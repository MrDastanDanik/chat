@extends('layout')

@section('title', 'Registration')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        {{ csrf_field() }}
        <input type="text" name="name" placeholder="Name"/>
        <input type="password" name="password" placeholder="Password"/>
        <input type="submit" value="Register">
    </form>
@endsection