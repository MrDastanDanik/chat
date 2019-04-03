@extends('layout')

@section('title', 'Home')

@section('content')
    <script>
        let conn = new WebSocket('ws://localhost:8090?token={{$user}}');
        conn.onopen = function (e) {
            console.log("Connection established!");
        };

        conn.onmessage = function (e) {
            console.log(e.data);
        };
    </script>
@endsection