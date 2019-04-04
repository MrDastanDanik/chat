@extends('layouts.app')

@push('scripts')
    <script src="js/chat.js"></script>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Chat</div>

                <br class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <div class="online hid">
                        </div></br>
                        <div class="card-footer hid">
                            <div class="input-group">
                                <textarea class="form-control type_msg" placeholder="Type your message..."></textarea>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </div>
                            </div>
                        </div>
                        <button class="btn in">Подключиться</button>
                        <button class="btn out hid">Отключиться</button>
                        <div class="msg hid"></div>

                        <script>
                            const USER_TOKEN = `{{$user}}`;
                        </script>
                        <script src="js/jquery-3.3.1.min.js"></script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
