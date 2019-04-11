@extends('layouts.app')

@push('scripts')
    <script src="js/chat.js"></script>
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
@endpush



@section('content')
   <!-- <div class="container">

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


                    </br>

                    <div class="card-footer hid">
                        <div class="input-group">
                            <input type="text" class="form-control type_msg" maxlength="200"
                                      placeholder="Type your message...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Send</button>
                            </div>
                        </div>
                    </div>
                    <div class="msg hid"></div>
                </div>

            </div>
        </div>
    </div>-->
    <script>
        const USER_TOKEN = `{{$user}}`;
    </script>


    <!-- -------------------------------------------------------------------------------------------------------- -->

<div class="chat body">

    <div class="chat container">
        <div class="chat row">
            <div class="chat col-sm-4">
                <div class="chat panel panel-primary">
                    <div class="chat panel-heading top-bar">
                        <div class="chat col-md-8 col-xs-8">
                            <h3 class="chat panel-title"><span class="chat glyphicon glyphicon-book"></span> Contacts</h3>
                        </div>
                    </div>

                    <table class="chat table table-striped table-hover">
                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>


            <div class="chat col-sm-8">
                <div class="chat chatbody">
                    <div class="chat panel panel-primary">
                        <div class="chat panel-heading top-bar">
                            <div class="chat col-md-8 col-xs-8">
                                <h3 class="chat panel-title"><span class="chat glyphicon glyphicon-comment"></span> Chat
                                </h3>
                            </div>
                        </div>
                        <div class="chat panel-body container-fluid msg_container_base" style="height: 300px;overflow-y: scroll;overflow-x: hidden;">

                            <!--<div class="chat row msg_container base_receive">
                                <div class="chat col-md-10 col-xs-10">
                                    <div class="chat messages msg_receive">
                                        <p>that mongodb thing looks good, huh?
                                            tiny master db, and huge document store</p>
                                        <time datetime="2009-11-13T20:00">Timothy • 51 min</time>
                                    </div>
                                </div>
                            </div>-->

                        </div>

                        <div class="card-footer hid">
                            <div class="input-group">
                                <input type="text" class="form-control type_msg" maxlength="200"
                                       placeholder="Type your message...">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </div>
                            </div>
                        </div>
                        <!--<div class="chat panel-footer">
                            <div class="chat input-group">
                                <input id="btn-input" type="text" class="chat form-control input-sm chat_input"
                                       placeholder="Write your message here..."/>
                                <span class="chat input-group-btn">
                        <button class="chat btn btn-primary btn-sm" id="btn-chat"><i class="chat fa fa-send fa-1x" aria-hidden="true"></i></button>
                        </span>
                            </div>
                        </div>-->

                    </div>

                </div>
            </div>
        </div>
</div>

@endsection
