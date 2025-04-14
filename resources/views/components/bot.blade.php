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


    <center>
        <div class="container">
            <br>
            <div class="row">
                <div class="col-md-8 col-md-offset-2 center">
                    <!--<div><h1>TẠM BIỆT EM <i class="fa  fa-heartbeat"></i></h1></div>-->
                    <div class="panel panel-default" style="min-height: 270px;">
                        <!--                        <div class="panel-heading" style="
                                                    background: rgb(109,101,245);
                        background: linear-gradient(90deg, rgba(109,101,245,1) 0%, rgba(213,123,242,1) 53%);
                        "></div>-->

                        <div class="panel-body">
                            <form id="frm-msg" class="form-horizontal" method="POST" action="">
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <!--<label for="msg" class="col-md-4 control-label">Msg</label>-->

                                    <div class="col-md-6">
                                        <input id="msg" type="text" class="form-control" name="msg" required autofocus value="Get error"> 

                                    </div>
                                    <br>
                                    <div class="col-md-3">
                                        <button onclick="clearText()" type="button" class="btn btn-success " >Clear</button>

                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">

                                        <div class="control-login">
                                            <button style="float:left;" id="btn-send" type="button" class="btn btn-success btn-loadding btn-send "  value="1" >OK</button>
                                            <button style="float:right" id="btn-send2" type="button" class="btn btn-danger btn-loadding btn-send"  value="2" >OK</button>
                                        </div>


                                    </div>
                                </div>
                            </form>
                            <div id="message" style="display: none;margin-top: 50px">
                                <div class="alert alert-success animated rubberBand message_content">Success</div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </center>

    <script type="text/javascript">
        function clearText() {
            $("#msg").val("");
        }
        $(".btn-send").click(function (e) {
            var form = $("#frm-msg");
            var formData = form.serialize();
            var type = $(this).val();
            $("#message").hide();
            var $this = $(this);
            if(type==1){
            $this.attr("disabled","true");
            }

            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            e.preventDefault();
            $.ajax({
                type: "GET",
                url: "/msg",
                data: formData + '&type=' + type,
                dataType: 'json',
                success: function (data) {

                    $this.html($this.data('original-text'));
                    $(".message_content").html(data.message);
                    $("#message").show();
                    setTimeout(function(){
                         $("#message").hide();
                    },5000);
//                    var html = '<div class="alert alert-success animated rubberBand">' + data.status + '</div>';
//                    $("#message").html(html);
                },
                error: function (data) {
//                    console.log('Error:', data);
                   $this.html($this.data('original-text'));
                }
            });
        });
    </script>
</body>
</html>
