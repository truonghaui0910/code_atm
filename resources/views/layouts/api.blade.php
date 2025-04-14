<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="description" content="Automusic.win">
        <meta name="author" content="Victorteam">

        <link rel="shortcut icon" href="images/favicon.ico">

        <title>Automusic</title>
        <base href="{{asset('')}}">

        <link href="assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
        <link href="assets/plugins/custombox/dist/custombox.min.css" rel="stylesheet" />
        <link href="assets/plugins/tablesaw/dist/tablesaw.css" rel="stylesheet" />
        <link href="assets/plugins/jquery-circliful/css/jquery.circliful.css" rel="stylesheet" type="text/css" />

        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css">
        <link href="assets/css/style.css" rel="stylesheet" type="text/css">
        <link href="css/style.css" rel="stylesheet" type="text/css">

        <script src="assets/js/modernizr.min.js"></script>


    </head>
    <body>
        <div class="wrapper-page">

            <div class="text-center">
                <a href="/" class="logo-lg">
                    <!--<img src="images/logo.png" style="width: 40px">--> 
                    <!--<span>Automusic</span> </a>-->
            </div>

            <form class="form-horizontal m-t-20" action="/login" method="POST">
                <div class="form-group m-t-20">
                    <div class="col-xs-12">
                        <a target="_blank" href="https://api.reupnet.info/apiReupTube/auth_bulk.php" class="btn btn-primary btn-custom w-md waves-effect waves-light">ADD</a>
                    </div>
                </div>
                
                
                <div class="form-group row">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="mdi mdi-account"></i></span>
                            <input id="link" class="form-control" type="text"  placeholder="Nhập link ở tab bên cạnh">
                        </div>
                    </div>
                </div>


                <div class="form-group m-t-20">
                    <div class="col-xs-12">
                        <button id="btn-submit" class="btn btn-primary btn-custom w-md waves-effect waves-light" type="button">SUBMIT</button>
                    </div>
                </div>
                
<!--                <hr>
                
                
                <div class="form-group row">
                    <div class="col-12">
                        <div class="input-group">
                            <input id="gmail" class="form-control" type="text" name="gmail" placeholder="Gmail">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-12">
                        <div class="input-group">
                            <input id="client_id" class="form-control" type="text" name="client_id" placeholder="Client Id">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-12">
                        <div class="input-group">
                            <input id="client_secret" class="form-control" type="text" name="client_secret" placeholder="Client Secret">
                        </div>
                    </div>
                </div>



                <div class="form-group text-right m-t-20">
                    <div class="col-xs-12">
                        <button id="btnRedirect" class="btn btn-danger btn-custom w-md waves-effect waves-light" type="button">Rediect
                        </button>
                    </div>
                </div>
                
                <div class="form-group row">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="mdi mdi-account"></i></span>
                            <input id="link2" class="form-control" type="text"  placeholder="Nhập link ở tab bên cạnh">
                        </div>
                    </div>
                </div>


                <div class="form-group m-t-20">
                    <div class="col-xs-12">
                        <button id="btn-submit2" class="btn btn-primary btn-custom w-md waves-effect waves-light" type="button">SUBMIT</button>
                    </div>
                </div>  -->


            </form>
            
        </div>

        <script>
            var resizefunc = [];
        </script>

        <!-- Plugins  -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/popper.min.js"></script><!-- Popper for Bootstrap -->
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/detect.js"></script>
        <script src="assets/js/fastclick.js"></script>
        <script src="assets/js/jquery.slimscroll.js"></script>
        <script src="assets/js/jquery.blockUI.js"></script>
        <script src="assets/js/waves.js"></script>
        <script src="assets/js/wow.min.js"></script>
        <script src="assets/js/jquery.nicescroll.js"></script>
        <script src="assets/js/jquery.scrollTo.min.js"></script>
        <script src="assets/plugins/switchery/switchery.min.js"></script>


        <!-- Custom main Js -->
        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>


        <script type="text/javascript">
jQuery(document).ready(function ($) {
    $("#btn-submit").click(function(){
       var link = $("#link").val(); 
       link = link.replace("http://localhost","https://api.reupnet.info/apiReupTube/auth_bulk.php");
       console.log(link);
       window.open(link);
    });
    
    $("#btnRedirect").click(function(){
       var gmail = $("#gmail").val();
       var clientId = $("#client_id").val();
       var clientSecret = $("#client_secret").val();
       window.location.href="http://95.217.208.195/api?client_id="+clientId+"&client_secret="+clientSecret+"&gmail="+gmail+"&user=musicuser";
    });
    
    $("#btn-submit2").click(function(){
       var link = $("#link2").val(); 
       link = link.replace("http://localhost","http://95.217.208.195/api");
       console.log(link);
       window.open(link);
    });
});


        </script>
        @yield('script')

    </body>
</html>