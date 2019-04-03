@extends('layouts.app')

@push('scripts')
    <script src="js/chat.js"></script>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                        <script>
                            const USER_TOKEN = `{{$user}}`;
                        </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
