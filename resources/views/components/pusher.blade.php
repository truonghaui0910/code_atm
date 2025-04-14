    <!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Automusic</title>
        <base href="{{asset('')}}">
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css">
        <link href="assets/css/style.css" rel="stylesheet" type="text/css">
        <link href="css/animate.css" rel="stylesheet" type="text/css">
        <!-- Bootstrap Core CSS -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">

        <!-- jQuery -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/popper.min.js"></script>
        <script src="//js.pusher.com/3.1/pusher.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <!-- Styles -->
        <style>
            html,body{
                background: #0A0B18;
            }
            .btn:focus{
                outline: none;
                box-shadow: none;
            }
            
             .btn-icon {
                background: #393A42;
                color: #dee7eb;
                border: none;
                padding: 3px 6px 3px 6px;
            }
            .btn-icon:hover {
               background: #535460;
            }
            .center{
                 margin: 0 auto;
            }
        </style>
    </head>
    <script>
$(document).ready(function () {

});


    </script>
    <body id="home">



        <div class="container">
            <br>
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default" style="min-height: 270px;">
                        <div class="panel-body">
                            
                            <form id="frm-msg" class="form-horizontal" method="POST" action="">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <div class="col-md-12">

                                        <div class="control-login">
                                            <button style="float:left;" id="btn-send" type="button" class="btn btn-success btn-loadding btn-send "  value="1" >OK</button>
                                            <!--<button style="float:right" id="btn-send2" type="button" class="btn btn-danger btn-loadding btn-send"  value="2" >OK</button>-->
                                        </div>


                                    </div>
                                </div>
                                <div id="message" style="display: none;margin-top: 50px;">
                                    <div class="alert alert-success animated rubberBand">Success</div>
                                </div>
                            </form>

<!--                            <div class="chat-messages">
                                <div class="chat">
                                    <div class="chat-content clearfix">
                                        <span class="friend last">
                                            Hi, How are You?
                                            <span class="time">
                                                7:30 PM
                                            </span>
                                        </span>
                                        <span class="you first">
                                            Hi, I am fine.How about you?Hi, I am fine.How about you?Hi, I am fine.How about you?Hi, I am fine.How about you?Hi, I am fine.How about you?
                                            <span class="time">
                                                7:31 PM
                                            </span>
                                        </span>
                                        <span class="you first">
                                            Hi, I am fine.
                                            How about you?
                                            <span class="time">
                                                7:31 PM
                                            </span>
                                        </span>
                                        <span class="you first">
                                            Hi, I am fine.
                                            How about you?
                                            <span class="time">
                                                7:31 PM
                                            </span>
                                        </span>
                                        <span class="you first">
                                            Hi, I am fine.
                                            How about you?
                                            <span class="time">
                                                7:31 PM
                                            </span>
                                        </span>
                                        <span class="you first">
                                            Hi, I am fine.
                                            How about you?
                                            <span class="time">
                                                7:31 PM
                                            </span>
                                        </span>
                                        <span class="you first">
                                            Hi, I am fine.
                                            How about you?
                                            <span class="time">
                                                7:31 PM
                                            </span>
                                        </span>
                                        <span class="you first">
                                            Hi, I am fine.
                                            How about you?
                                            <span class="time">
                                                7:31 PM
                                            </span>
                                        </span>
                                        <span class="you first">
                                            Hi, I am fine.
                                            How about you?
                                            <span class="time">
                                                7:31 PM
                                            </span>
                                        </span>
                                        <span class="you first">
                                            Hi, I am fine.
                                            How about you?
                                            <span class="time">
                                                7:31 PM
                                            </span>
                                        </span>
                                        <span class="you last">
                                            lets meet,
                                            this sunday!
                                            <span class="time">
                                                7:31 PM
                                            </span>
                                        </span>
                                    </div>

                                    <div class="msg-box">
                                        <input type="text" class="ip-msg" placeholder="type something.." />
                                        <span class="btn-group">
                                            <i class="fa fa-paper-plane"></i>
                                            <i class="fa fa-paperclip"></i>
                                        </span>
                                    </div>

                                </div>
                            </div>-->


                        </div>
                    </div>
                </div>
            </div>
        </div>


    <script type="text/javascript">
        Pusher.logToConsole = true;
        var pusher = new Pusher('f4a56fb4a7d911625576', {
            encrypted: true,
            cluster: 'ap1'
        });
        var channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function (data) {
            console.log(data);
        });

        $(".btn-send").click(function (e) {
            var form = $("#frm-msg");
            var formData = form.serialize();
            var type = $(this).val();
            $("#message").hide();
            var $this = $(this);

            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            e.preventDefault();
            $.ajax({
                type: "GET",
                url: "/msghx",
                data: formData + '&type=' + type,
                dataType: 'json',
                success: function (data) {
                    $this.html($this.data('original-text'));
                    $("#message").show();
                },
                error: function (data) {
                    $this.html($this.data('original-text'));
                }
            });
        });
    </script>
</body>
</html>
