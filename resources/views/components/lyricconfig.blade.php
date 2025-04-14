@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Lyric config</h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Lyric config</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<form id="formMusic" class="form-horizontal form-label-left"  method="POST" enctype="multipart/form-data">
    <input type="hidden" name ='_token' value='{{csrf_token()}}'/>
    <input type="hidden" name ='id_music_config' value='{{$datas[3]}}'/>
    @include('pannel_lyric.source')
    @include('pannel_lyric.background')
    @include('pannel_lyric.style_text')


    <div class="row">    
        @include('pannel_lyric.title')
    </div>
    <div class="row">    
        @include('pannel.outtro')
    </div>
    <div class="div_type_reup_2 disp-none">
        @include('pannel_lyric.advanced')
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <button type="button" class="btn btn-outline-info btn-sm btn-submit-music"><i class="fa fa-upload"></i> Submit</button>
            </div>
        </div>
        <div class="col-md-12">
            <div id="messageValidate"></div>
        </div>
    </div>
</form>
@endsection

@section('script')
<script type="text/javascript">


//    $("#lyric-box").resizable({
//        containment: '#preview-image',
//        handles: "n, e, s, w , se, ne, nw, sw",
//        stop: function (event, ui) {
//            var width = ui.size.width;
//            var height = ui.size.height;
//            console.log(width + ' - ' + height);
//            console.log(("top = " + ui.position.top +
//                     ", left = " + ui.position.left +
//                     ", width = " + ui.size.width +
//                     ", height = " + ui.size.height));
//            
//        }
//    });
//
//    $('#lyric-box').draggable({
//        cursor: "move",
//        containment: '#preview-image',
//        stop: function (event,ui) {
////            var offset = $(this).offset();
////            var xPos = offset.left;
////            var yPos = offset.top;
////            console.log($(window).height());
////            console.log($(window).width());
////            console.log(window.screen.height);
////            console.log(window.screen.width);
////            console.log(xPos + ' - ' + yPos);
//                       console.log(("top = " + ui.position.top +
//                     ", left = " + ui.position.left));
//        }
//    });

    $(".image-picker").imagepicker();
    $(".style_bg_picker").imagepicker();
    $('#text_stroke_color').colorpicker();
    $('#g_color_s').colorpicker();
    $('#g_color_e').colorpicker();
    $('#font_color').colorpicker();
    $('#font_color_title').colorpicker();
    $('#text_stroke_color_title').colorpicker();
    $('#font_color_lyric').colorpicker();
    $('#mix_bg').change(function () {
        if (this.checked) {
            $('.div_mix_bg').fadeIn('slow');
            $('.div_normal_bg').fadeOut('fast');
        } else {
            $('.div_normal_bg').fadeIn('slow');
            $('.div_mix_bg').fadeOut('fast');
        }
    });
    $('#bg_other').change(function () {
        var val = $(this).val();
        getBackgroundByTopicOrArtists(val);
    });
    $('#bg_artists').change(function () {
        var val = $(this).val();
        getBackgroundByTopicOrArtists(val);
    });
    $('#bg_lyric').change(function () {
        var val = $(this).val();
        getBackgroundByTopicOrArtists(val);
    });
    $('#gradient_color').change(function () {
        var val = $(this).val();
        var split = val.split(',');
        $("#g_color_s").val('#' + split[0]);
        $("#g_color_e").val('#' + split[1]);
        $("#g_color_s").css({"background": '#' + split[0]});
        $("#g_color_e").css({"background": '#' + split[1]});
    });
    function selectAll() {
        var selectBox = document.getElementById("background");
        for (var i = 0; i < selectBox.options.length; i++) {
            selectBox.options[i].selected = true;
        }
        $(".thumbnail").addClass("selected");
    }
    function unSelectAll() {
        var selectBox = document.getElementById("background");
        for (var i = 0; i < selectBox.options.length; i++) {
            selectBox.options[i].selected = false;
        }
        $(".thumbnail").removeClass("selected");
    }
    function removeAll() {
        $('.image-picker option').remove();
    }
    function getBackgroundByOffset() {
        var but = $("#btnLoadMore");
        var bg_type = $("#bg_type").val();
        var datas;
        if (bg_type == 2) {
            datas = $("#bg_artists").val();
        } else if (bg_type == 1) {
            datas = $("#bg_other").val();
        } else if (bg_type == 6) {
            datas = $("#bg_lyric").val();
        }
        $.ajax({
            type: "GET",
            url: "/getdataimage",
            data: {'offset': but.val(), bg_type: bg_type, datas: datas},
            dataType: 'json',
            success: function (data) {
                but.button('reset');
                but.val(parseInt(but.val()) + 30);
                for (var i = 0; i < data.length; i++) {
                    $('.image-picker').append($('<option>',
                            {'data-img-src': 'http://51.75.243.130/resize.php?filename=' + data[i]['link'],
                                'value': data[i]['link']},
                            ).text(data[i]['id']));
                }
//                var size = $('.image-picker option').size();
//                $("#total").html(size);
                $(".image-picker").imagepicker();
            },
            error: function (data) {
                but.button('reset');
            }
        });

    }
    function getBackgroundByTopicOrArtists(datas) {
        var but = $("#btnLoadMore");
        var bg_type = $("#bg_type").val();
        $.ajax({
            type: "GET",
            url: "/getdataimage",
            data: {'offset': 0, bg_type: bg_type, datas: datas},
            dataType: 'json',
            success: function (data) {
//                but.button('reset');
                but.val(parseInt(30));
                $('.image-picker option').remove();
                for (var i = 0; i < data.length; i++) {
                    $('.image-picker').append($('<option>',
                            {'data-img-src': 'http://51.75.243.130/resize.php?filename=' + data[i]['link'],
                                'value': data[i]['link']},
                            ).text(data[i]['id']));
                }
//                var size = $('.image-picker option').size();
//                $("#total").html(size);
//                $("#topic-name").html(topic);
                $(".image-picker").imagepicker();
            },
            error: function (data) {
//                but.button('reset');
            }
        });
    }
    $('#ck_topic_music').change(function () {
        var topic = $(this).val();
        if (topic !== "-1") {
            $.ajax({
                type: "GET",
                url: "/ajaxGetArtistsFromTopic",
                data: {'topic': topic},
                dataType: 'text',
                success: function (data) {
                    $("#ck_artists_music").html(data);

                },
                error: function (data) {

                }
            });
        }

    });
    $('#ck_artists_music').change(function () {
        var artists = $(this).val();
        $("#arti_selected").html('(' + artists.length + ')');
        if (artists.length > 5) {
            bootbox.alert({
                message: "You are only allowed to select 5 artists",
                callback: function () {
                    var selectBox = document.getElementById("ck_artists_music");
                    for (var i = 0; i < selectBox.options.length; i++) {
                        selectBox.options[i].selected = false;
                    }

                }
            });
            return;
        }
        if (artists.length !== 0) {
            $.ajax({
                type: "GET",
                url: "/ajaxGetSongsFromArtists",
                data: {'artists': artists},
                dataType: 'text',
                success: function (data) {
                    console.log(data);
                    $("#ck_song_music").html(data);

                },
                error: function (data) {

                }
            });
        }

    });
    $('#ck_song_music').change(function () {
        var song = $(this).val();
        $("#song_selected").html('(' + song.length + ')');
    });
    $(".btn-submit-music").click(function (e) {
        $("#messageValidate").html('');
        var form = $("#formMusic");
        var formData = form.serialize();
        var but = $(this);
        but.button('loading');
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/addlyric",
            data: formData + "&bg_mix_type=" + $('#ck_background_template option:selected').text(),
            dataType: 'json',
            success: function (data) {
                $('#btn-save').addClass('disabled');
                but.button('reset');

                var content = data.content;
                var html = '<div class="alert alert-' + data.status + '"><ul>';
                for (var i = 0; i < content.length; i++) {
                    html += '<li>' + content[i] + '</li>';
                }
                html += '</ul>';
                $("#messageValidate").html(html);

            },
            error: function (data) {
                but.button('reset');
            }
        });
    });

    function previewFunction() {
        debugger;
        var ck_style_lyric = 0;
        var ck_style_text = 0;
        var text_data = $("#text_data").val();
        if ($(".ck_style_text").prop("checked") == true) {
            ck_style_text = 1;
            if (text_data == '') {
                $.Notification.notify('warning', 'top center', 'Notification', 'You need enter Text');
                return;
            }
        }
        if ($(".ck_style_lyric").prop("checked") == true) {
            ck_style_lyric = 1;
        }

        var background_type = $("#background_type").val();
        var g_color_s = $("#g_color_s").val().replace("#", "");
        var g_color_e = $("#g_color_e").val().replace("#", "");
        var g_type = $("#g_type").val();

        var font_name = $("#font_name").val();
        var font_size = $("#font_size").val();
        var pos = $("#pos").val();
        var pos_x = $("#pos_x").val();
        var pos_y = $("#pos_y").val();
        var font_color = $("#font_color").val().replace("#", "");
        var text_stroke_size = $("#text_stroke_size").val();
        var text_stroke_color = $("#text_stroke_color").val().replace("#", "");
        var rotation = $("#rotation").val();

        var font_name_lyric = $("#font_name_lyric").val();
        var font_color_lyric = $("#font_color_lyric").val().replace("#", "");

        var number_line_lyric = $("#number_line_lyric").val();
        var type_lyric_source = $("#type_lyric_source").val();
        var lyric_source_from_tool_make_lyric = $("#lyric_source_from_tool_make_lyric").val();
        var id_deezer = $("#id_deezer").val();
        var source = $("#source").val();

        $("#img-preview").attr("src", "/ajaxPreviewLyric?text_data=" + text_data + "&font_size=" + font_size +
                "&ck_style_lyric=" + ck_style_lyric +
                "&ck_style_text=" + ck_style_text +
                "&font_name=" + font_name +
                "&pos=" + pos +
                "&pos_x=" + pos_x +
                "&pos_y=" + pos_y +
                "&font_color=" + font_color +
                "&text_stroke_size=" + text_stroke_size +
                "&text_stroke_color=" + text_stroke_color +
                "&rotation=" + rotation +
                "&number_line_lyric=" + number_line_lyric +
                "&font_name_lyric=" + font_name_lyric +
                "&font_color_lyric=" + font_color_lyric +
                "&background_type=" + background_type +
                "&g_color_s=" + g_color_s +
                "&g_color_e=" + g_color_e +
                "&g_type=" + g_type +
                "&type_lyric_source=" + type_lyric_source +
                "&lyric_source_from_tool_make_lyric=" + lyric_source_from_tool_make_lyric +
                "&id_deezer=" + id_deezer +
                "&source=" + source
                );
            $("#preview-image").fadeIn('fast');
    }

    $('.ck_replace_des').change(function () {
        var value = $('.ck_replace_des').val();
        if (value != 0) {
            $('.div_ck_replace_des_3').fadeIn('slow');
        } else {
            $('.div_ck_replace_des_3').fadeOut('fast');
        }

    });
    $('#ck_des_add_begin').change(function () {
        if (this.checked) {
            if ($(".music_type").val() == 2) {
                $('.div_add_time_1').fadeIn('slow');
            }
        } else {
            $('.div_add_time_1').fadeOut('fast');
        }
    });
    $('#ck_des_add_end').change(function () {
        if (this.checked) {
            if ($(".music_type").val() == 2) {
                $('.div_add_time_2').fadeIn('slow');
            }
        } else {
            $('.div_add_time_2').fadeOut('fast');
        }
    });

    $('.ck_replace_des').change(function () {
        if ($('.ck_replace_des').val() == 2) {
            if ($(".music_type").val() == 2) {
                $('.div_add_time_3').fadeIn('slow');
            }
        } else {
            $('.div_add_time_3').fadeOut('fast');
        }
    });
    $(".div_add_time_1").click(function (e) {
        e.preventDefault();
        $("#txt_des_replace_first").val($("#txt_des_replace_all").val() + "@@@time_mapping");
    });
    $(".div_add_time_2").click(function (e) {
        e.preventDefault();
        $("#txt_des_replace_last").val($("#txt_des_replace_all").val() + "@@@time_mapping");
    });
    $(".div_add_time_3").click(function (e) {
        e.preventDefault();
        $("#txt_des_replace_all").val($("#txt_des_replace_all").val() + "@@@time_mapping");
    });
    suggestArtists(".txt_search_artists", "#bg_artists");
    suggestArtists(".txt_search_artists", "#ck_artists_music");
    function suggestArtists(id_search, id_result) {
        $(id_search).keyup(function () {
            var artists = $(this).val();
            $.ajax({
                type: "GET",
                url: "/ajaxGetArtistsFromName",
                data: {'artists': artists},
                dataType: 'text',
                success: function (data) {
//                    console.log(data);
//                $('#bg_artists').remove();
                    $(id_result).html(data);

                },
                error: function (data) {
//                    console.log(data);
                }
            });
        });
    }
    var text = new Array();
    text[0] = new Array('White Top', 'Top Music 2019', 'http://automusic.win/rs/font/title/Cucho_Bold.otf', 'LT', '0', '50', '50', '100', '#ffffff', '1', '#000000');
    text[1] = new Array('Honey Cream', 'Top Music 2019', 'http://automusic.win/rs/font/title/Honey_Cream.ttf', 'LB', '0', '100', '50', '100', '#db0d0d', '2', '#ffffff');
    text[2] = new Array('PinkPro', 'Top Music 2019', 'http://automusic.win/rs/font/title/VLABELPRO-BOLD.OTF', 'MB', '0', '0', '0', '120', '#ff00aa', '3', '#baf2ea');
    text[3] = new Array('Arrow Blue', 'Top Music 2019', 'http://automusic.win/rs/font/title/VLALLROUNDGOTHIC-BOOK.OTF', 'MB', '30', '0', '200', '120', '#00e5ff', '3', '#ffffff');
    text[4] = new Array('Skype Arris', 'Top Music 2019', 'http://automusic.win/rs/font/title/VLGARRIS.OTF', 'MM', '0', '0', '0', '120', '#00e5ff', '3', '#f7f76a');
    var title_song = new Array();
    title_song[0] = new Array('Basic White', 'http://automusic.win/rs/font/title/iCiel_Altus_Serif.otf', 'LT', '0', '50', '100', '30', '#ffffff', '1', '#000000');
    title_song[1] = new Array('Gold Lady', 'http://automusic.win/rs/font/title/VLBANDUNG.OTF', 'LT', '0', '200', '200', '30', '#eec30f', '1', '#000000');
    var option = '<option value="-1">--Select--</option>';
    for (var i = 0; i < text.length; i++) {

        option += '<option value="' + i + '">' + text[i][0] + '</option>';
    }
    $("#select_style").html(option);
    option = '<option value="-1">--Select--</option>';
    for (var i = 0; i < title_song.length; i++) {

        option += '<option value="' + i + '">' + title_song[i][0] + '</option>';
    }
    $("#select_style_title").html(option);

    $('#select_style').change(function () {
        var curr = $(this).val();
        var style = text[curr];
        $("#text_data").val(style[1]);
        $("#font_name").val(style[2]);
        $("#pos").val(style[3]);
        $("#rotation").val(style[4]);
        $("#pos_x").val(style[5]);
        $("#pos_y").val(style[6]);
        $("#font_size").val(style[7]);
        $("#font_color").val(style[8]);
        $("#text_stroke_size").val(style[9]);
        $("#text_stroke_color").val(style[10]);
        $("#btn-preview").click();
    });
    $('#select_style_title').change(function () {
        var curr = $(this).val();
        var style = title_song[curr];
        $("#font_name_title").val(style[1]);
        $("#pos_title").val(style[2]);
        $("#rotation_title").val(style[3]);
        $("#pos_x_title").val(style[4]);
        $("#pos_y_title").val(style[5]);
        $("#font_sizev").val(style[6]);
        $("#font_color_title").val(style[7]);
        $("#text_stroke_size_title").val(style[8]);
        $("#text_stroke_color_title").val(style[9]);
        $("#btn-preview").click();
    });

    $(".btn-submit-upload").click(function (e) {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var form_file = new FormData();
        var file = $("#image_upload").val();
        if (file != '') {
            console.log(file);
        } else {
            $.Notification.notify('error', 'top center', '', "You must choose a image");
            return;
        }
        form_file.append('image', $("#image_upload")[0].files[0]);
        form_file.append('_token', '{{csrf_token()}}');
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/image-upload",
            data: form_file,
            contentType: false,
            processData: false,
            success: function (data) {
//                console.log(data);
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
                $('.image-picker option').remove();
                $('.image-picker').append($('<option>',
                        {'data-img-src': '' + data.uploaded_image,
                            'value': 'http://automusic.win/' + data.uploaded_image},
                        ).text(data.uploaded_image));
                $(".image-picker").imagepicker();
                $(".image_picker_image").css({"width": "364px"});

            },
            error: function (data) {
                but.button('reset');
            }
        });
    });
    $(".btn-get-image").click(function (e) {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var file = $("#drive_file").val();
        console.log(file);
        if (file == '') {
            $.Notification.notify('error', 'top center', '', "You must enter link google drive");
            return;
        }
        e.preventDefault();
        $.ajax({
            type: "GET",
            url: "/image_from_drive",
            data: {"link": file},
            dataType: 'json',
//            contentType: false,
//            processData: false,
            success: function (data) {
//                console.log(data);
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
                $('.image-picker option').remove();
                $('.image-picker').append($('<option>',
                        {'data-img-src': '' + data.uploaded_image,
                            'value': 'http://automusic.win/' + data.uploaded_image},
                        ).text(data.uploaded_image));
                $(".image-picker").imagepicker();
                $(".image_picker_image").css({"width": "364px"});

            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });

</script>
@endsection

