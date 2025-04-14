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


        <style>
            body{
                background: #313131;
            }
            .wrap{
                width: 100%;
                height: auto;
                background: #313131;
                text-align: center;
            }
            .iframe-result{
                width: 800px;
                height: 450px;
                border: 0px;
            }
        </style>
    </head>
    
    <body>
        <div class="wrap">
        <iframe class="iframe-result" src="{{$result}}/preview" allow="autoplay"></iframe>
            
        </div>
    </body>
</html>