@extends('layouts.master')

@section('content')

<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Spotify Database ({{number_format($datas->total(), 0, '.', ',')}} songs)</h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Spotify Music</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <div class="row">
                <div class="col-lg-12">
                    @if(count($suggests) >0)
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-box">
                                <h4 class="header-title m-t-0 m-b-30">Suggest</h4>
                                <div style="overflow: auto;padding-left: 0px;padding-right: 15px;height: 500px;">
                                    <div class="row">
                                        @foreach($suggests as $data)
                                        <div class="col-sm-3 col-xs-12 suggest_{{$data->id}} ">
                                            <div class="card m-b-20 card-body">
                                                <div>
                                                    @if($data->url_download != null)
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make lyric' href="http://api.automusic.win/spotify/lyric/{{$data->id}}/{{$user_login->user_name}}" class="<?php echo ($data->has_lyric == 1 ? "color-b" : "") ?>"><i class="fa fa-music"></i></a>
                                                    @else
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Google search' href="http://www.google.com/search?q={{$data->name}}+{{$data->artists}}+lyrics" class="<?php echo ($data->has_lyric == 1 ? "color-b" : "") ?>"><i class="fa fa-google"></i></a>
                                                    @endif
                                                    @if($data->has_lyric)
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make video lyric on automusic V1' class="color-b" href="http://automusic.win/lyricconfig?type=deezer&data={{$data->id_deezer}}"><i class="fa fa-video-camera"></i></a>
                                                    @endif
                                                    @if($data->has_lyric)
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make video lyric on automusic V2' class="color-red" href="http://studio.automusic.win/?deezerId={{$data->id_deezer}}"><i class="fa fa-video-camera"></i></a>
                                                    @endif
                                                    @if($is_admin_music==1)
                                                    <a class="float-right cur-poiter " onclick="markSuggest({{$data->id}})" data-toggle="remove"><i class="ion-close-round"></i></a>
                                                    <a class="float-right cur-poiter m-r-5" onclick="editNote({{$data->id}})"><i class="fa fa-sticky-note-o"></i></a>&nbsp;
                                                    @endif
                                                </div>
                                                <div class="">
                                                    <div class="card-title float-left">
                                                        <a target="blank" href="{{$data->url_download}}">
                                                            <span accesskey="" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $data->is_block == 1 ? "Bài hát này bị gậy" : ""; ?>">{{$data->id}} : {{$data->name}}
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <p class="card-text">{!!$data->artists!!}</p>
                                                <p class="card-text font-light">{{$data->note}}</p>
                                                <div class="">

                                                </div>
                                            </div>
                                        </div>
                                        @endforeach

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-box">
                                <h4 class="header-title m-t-0 m-b-30">Filter</h4>
                                <form id="form-search " action="/musicspotify">
                                    <div class="col-md-12 filter-class">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group row">
                                                    <label class="col-8 col-form-label">Song/Artist/Assigned To/Playlist Id</label>
                                                    <div class="col-12">
                                                        <input id="search_channel" class="form-control" type="text" name="c1" value="{{$request->c1}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group row">
                                                    <label class="col-8 col-form-label">Lyrics Completed?</label>
                                                    <div class="col-12">
                                                        <select id="search_status" class="form-control" name="c2">
                                                            {!!$status_lyric!!}
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group row">
                                                    <label class="col-8 col-form-label">Playlist Type</label>
                                                    <div class="col-12">
                                                        <select id="playlist_type" class="form-control" name="c3">
                                                            {!!$playlist_type!!}
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group row">
                                                    <label class="col-8 col-form-label">&nbsp;</label>
                                                    <div class="col-12">
                                                        <button id="btnSearch" type="submit" class="btn btn-outline-info btn-micro"><i class="fa fa-search"></i> Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="limit" id="limit" value="{{$limit}}">

                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="sliderDate" class="control-label"><b>Published</b></label>
                                            <div class="m-b-20">
                                                <input type="text" id="sliderDate" name="sliderDate" value="{{$request->sliderDate}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="popularity" class="control-label"><b>Popularity</b></label>
                                            <div class="m-b-20">
                                                <input type="text" id="popularity" name="popularity" value="{{$request->popularity}}">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    @if($is_admin_music==0 && count($carts) >0)
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-box">
                                <h4 class="header-title m-t-0 m-b-30">Cart</h4>
                                <div style="overflow: auto;padding-left: 0px;padding-right: 15px;height: 500px;">
                                    <div class="row">
                                        @foreach($carts as $data)
                                        <div class="col-sm-3 col-xs-12 cart_{{$data->id}} ">
                                            <div class="card m-b-20 card-body">
                                                <div>
                                                    @if($data->url_download != null)
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make lyric' href="http://api.automusic.win/spotify/lyric/{{$data->id}}/{{$user_login->user_name}}" class="<?php echo ($data->has_lyric == 1 ? "color-b" : "") ?>"><i class="fa fa-music"></i></a>
                                                    @else
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Google search' href="http://www.google.com/search?q={{$data->name}}+{{$data->artists}}+lyrics" class="<?php echo ($data->has_lyric == 1 ? "color-b" : "") ?>"><i class="fa fa-google"></i></a>
                                                    @endif
                                                    @if($data->has_lyric)
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make video lyric on automusic V1' class="color-b" href="http://automusic.win/lyricconfig?type=deezer&data={{$data->id_deezer}}"><i class="fa fa-video-camera"></i></a>
                                                    @endif
                                                    @if($data->has_lyric)
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make video lyric on automusic V2' class="color-red" href="http://studio.automusic.win/?deezerId={{$data->id_deezer}}"><i class="fa fa-video-camera"></i></a>
                                                    @endif
                                                    <a class="float-right cur-poiter " onclick="cartSong({{$data->id}})" data-toggle="remove"><i class="ion-close-round"></i></a>
                                                </div>
                                                <div class="">
                                                    <div class="card-title float-left">
                                                        <a target="blank" href="{{$data->url_download}}">
                                                            <span accesskey="" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $data->is_block == 1 ? "Bài hát này bị gậy" : ""; ?>">{{$data->id}} : {{$data->name}}
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <p class="card-text">{!!$data->artists!!}</p>
                                                <div class="">

                                                </div>
                                            </div>
                                        </div>
                                        @endforeach

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    @endif                    

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-box">
                                <h4 class="header-title m-t-0 m-b-30">Spotify music list (Đã chọn <span id="selected_count">0</span>) <i class="btn-copy-link fa fa-copy color-blue" data-toggle='tooltip' data-placement='top' data-original-title='Copy selected song'></i></h4>
                                <div id="channel-chart" style="overflow: auto;padding-left: 0px;padding-right: 0px;height: 600px;">
                                    <table id="spotify-table" class="fix-header-table table" style="width: 99.5%;table-layout:fixed;">
                                        <thead>
                                            <tr>
                                                <th style="width: 2%"></th>
                                                <!--<th style="width: 5%;text-align: center"><input type="checkbox"  id="select_all" value="1" ><label class="cust-all" for="select_all"></label> </th>-->
                                                <th style="width: 5%">#</th>
                                                <th style="width: 20%">Song</th>
                                                <th style="width: 10%">Artist</th>
                                                <th style="width: 7%">Followers</th>
                                                <th style="width: 7%">Popularity</th>
                                                <th style="width: 10%">Artist Genre</th>
                                                @if($is_admin_music==1)
                                                <th style="width: 10%">PlaylistId</th>
                                                @endif

                                                <th style="width: 7%">Published</th>
                                                <th style="width: 10%">Crawled</th>
                                                <!--<th style="width: 150px">Notes</th>-->
                                                <!--<th>Assigned To</th>-->
                                                <!--<th>Lyrics Completed</th>-->
                                                <!--<th style="width: 100px">Video</th>-->
                                                <th style="text-align: center;width: 150px" colspan="4">Action</th>
                                                <!--                                                <th style="text-align: center">Make lyrics</th>
                                                <th style="text-align: center">Make video</th>
             
                                                <th style="text-align: center">Make video (V2)</th>-->

                                            </tr>
                                        </thead>
                                        <?php $i = 1; ?>
                                        <form id="form-selected">
                                            @foreach($datas as $data)
                                            <tbody>
                                                <tr>
                                                    @if($data->url_download != null)
                                                    <td><input class="chkSelect" type="checkbox" name="chkSelect[]" id="ck-check{{$data->id}}" value="{{$data->url_download}}"><label class="cust-all" for="ck-check{{$data->id}}"></label></td>
                                                    @else
                                                    <td></td>
                                                    @endif
                                                    <th scope="row">{{$data->id}}</th>
                                                    <td class="text-ellipsis">
                                                        <i onclick="reportBlock('{{$data->id}}')" class="fa fa-trash color-blue font-1rem " data-toggle="tooltip" data-placement="top" data-original-title="Báo bài hát bị gậy"></i>
                                                        @if($is_admin_music==1)
                                                        <i onclick="markSuggest('{{$data->id}}')" class="fa fa-check color-blue font-1rem " data-toggle="tooltip" data-placement="top" data-original-title="Mark as suggest"></i>
                                                        @endif
                                                        @if($is_admin_music==0)
                                                        <i onclick="cartSong('{{$data->id}}')" class="fa fa-cart-plus color-blue font-1rem " data-toggle="tooltip" data-placement="top" data-original-title="Thêm vào giỏ"></i>
                                                        @endif
                                                        <a target="blank" href="{{$data->url_download}}">
                                                            <span id="song_name_{{$data->id}}" accesskey="" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $data->is_block == 1 ? "Bài hát này bị gậy" : ""; ?>" class="<?php
                                                                                                                                                                                                                                                    echo $data->is_suggest == 1 ? "color-g " : "";
                                                                                                                                                                                                                                                    echo $data->is_block == 1 ? "song-block " : "";
                                                                                                                                                                                                                                                    echo \App\Common\Utils::containString($data->cart,  $user_login->user_name)?"color-y " : "";
                                                                                                                                                                                                                                                    ?>">{{$data->name}}</span>
                                                        </a> 
                  
                                                    </td>
                                                    <td class="text-ellipsis" data-toggle='tooltip' data-placement='top' data-original-title='{!!$data->artists!!}'>{!!$data->artists!!} </td>
                                                    <td>{!!$data->art_followers!!} <?php echo (time() - strtotime($data->crawled_at) < 86400 * 2) ? "<img src='images/new.jpg' width='32'>" : "" ?></td>
                                                    <td>{{$data->art_popularity}}</td>
                                                    <td data-toggle='tooltip' data-placement='top' data-original-title='{{$data->art_genre}}' style="text-overflow: ellipsis;overflow: hidden;white-space: nowrap;">{{$data->art_genre}}</td>
                                                    @if($is_admin_music==1)
                                                    <?php $open = "copyToClipboard('$data->playlist_id')"; ?>
                                                    <td data-toggle='tooltip' data-placement='top' data-original-title='{{$data->playlist_id}}' style="text-overflow: ellipsis;overflow: hidden;white-space: nowrap;cursor: pointer"><span onclick="{{$open}}">{{substr($data->playlist_id,-10)}}</span></td>
                                                    @endif
                                                    <td>{{$data->release_date}}</td>
                                                    <td>{{str_replace("-2021","",substr($data->crawled_at,0,16))}}</td>

                                                    <td style="text-align: center">
                                                        @if($data->url_download != null)
                                                        <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make lyric' href="http://api.automusic.win/spotify/lyric/{{$data->id}}/{{$user_login->user_name}}" class="<?php echo ($data->has_lyric == 1 ? "color-b" : "") ?>"><i class="fa fa-music"></i></a>
                                                        @else
                                                        <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Google search' href="http://www.google.com/search?q={{$data->name}}+{{$data->artists}}+lyrics" class="<?php echo ($data->has_lyric == 1 ? "color-b" : "") ?>"><i class="fa fa-google"></i></a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($data->has_lyric)
                                                        <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make video lyric on automusic V1' class="color-b" href="http://automusic.win/lyricconfig?type=deezer&data={{$data->id_deezer}}"><i class="fa fa-video-camera"></i></a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($data->has_lyric)
                                                        <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make video lyric on automusic V2' class="color-red" href="http://studio.automusic.win/?deezerId={{$data->id_deezer}}"><i class="fa fa-video-camera"></i></a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($data->has_lyric)
                                                        <button class="color-green btn btn-show-hub" data-music-type="deezer" data-music-id="{{$data->id_deezer}}" data-songname="{{$data->name}}" data-toggle='tooltip' data-placement='top' data-original-title='Make multi videos lyric on automusic V2'><i class="ion-videocamera"></i></button>
                                                        @endif
                                                    </td>

                                                </tr>
                                            </tbody>
                                            @endforeach
                                        </form>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-md-6 ">
                            <div>

                                <?php
                                $info = str_replace('_START_', $datas->firstItem() != null ? $datas->firstItem() : '0', trans('label.title.sInfo'));
                                $info = str_replace('_END_', $datas->lastItem() != null ? $datas->lastItem() : '0', $info);
                                $info = str_replace('_TOTAL_', $datas->total(), $info);
                                echo $info;
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="pull-right disp-flex">
                                <select id="cbbLimit" name="limit" aria-controls="tbl-title" class="form-control input-sm">
                                    {!!$limitSelectbox!!}
                                </select>
                                <?php if (isset($datas)) { ?>
                                    {!!$datas->links()!!}
                                <?php } ?>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
<input type="hidden" id="track-id"></input>
<input type="hidden" id="track-type"></input>
@include('dialog.log')
@include('dialog.editnote')
@include('dialog.musicchannel')


@endsection

@section('script')
<script type="text/javascript">
        function cartSong(id) {

        $.ajax({
            type: "GET",
            url: "/cartSong",
            data: {
                'id': id
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $.Notification.notify("success", 'top center', '', "Success");
                if (data.content == 1) {
                    $("#song_name_" + id).addClass("color-y");
                } else {
                    $("#song_name_" + id).removeClass("color-y");
                    $(".cart_" + id).fadeOut("fast");
                }
            },
            error: function(data) {
                //                    console.log('Error:', data);
            }
        });
    }
    
    $('.chkSelect').change(function() {
        var count = $("#selected_count").html();
        if (this.checked) {
            $("#selected_count").html(parseInt(count) + 1);
        } else {
            $("#selected_count").html(parseInt(count) - 1);
        }
    });
    
    $(".btn-copy-link").click(function(e) {
        e.preventDefault();
        var searchIDs = $("#spotify-table input:checkbox:checked").map(function() {
            return $(this).val();
        }).get();
        var data = searchIDs.join("<br/>");
        copyToClipboardNewLine(data);
    });

    $('#select_all').change(function() {
        var checkboxes = $(this).closest('#spotify-table').find('.chkSelect');
        checkboxes.prop('checked', $(this).is(':checked'));
    });
    var from = $("#sliderDate").val();
    if (from == "All") {
        from = 11;
    }
    from = from - 1;

    $("#sliderDate").ionRangeSlider({
        grid: true,
        from: from,
        values: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, "All"],
        postfix: " days",
        onFinish: function(data) {
            //            console.log(data);
            //            $("#btnSearch").click();
        },
    });
    var fromPop = 0;
    var toPop = 100;
    var fromTo = $("#popularity").val().split(";");
    if (fromTo.length == 2) {
        fromPop = fromTo[0];
        toPop = fromTo[1];
    }
    $("#popularity").ionRangeSlider({
        type: "double",
        grid: true,
        from: fromPop,
        to: toPop,
        min: 0,
        max: 100,
        onFinish: function(data) {
            //            console.log(data);
            //            console.log($("#popularity").val());

        },
    });
    //    setTimeout(function() {
    //        window.location.reload();
    //    }, 1000 * 60 * 3);


    
    $(".btn-edit-note").click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        $.ajax({
            type: "GET",
            url: "/updateNote",
            data: {
                'id': $("#spotify_music_id").val(),
                'note': $("#spotify_music_note").val()
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $.Notification.notify("success", 'top center', '', "Success");
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
        $('#dialog-note').modal('hide');
    });

    function editNote(id) {
        $("#spotify_music_id").val(id);
        $('#dialog-note').modal({
            backdrop: false
        });
    }

    function reportBlock(id) {
        $.ajax({
            type: "GET",
            url: "/reportBlock",
            data: {
                'id': id
            },
            dataType: 'json',
            success: function(data) {
                $.Notification.notify("success", 'top center', '', "Success");
                if (data.content == 1) {
                    $("#song_name_" + id).addClass("song-block");
                    $("#song_name_" + id).attr("data-original-title", "Báo bài hát bị gậy");
                } else {
                    $("#song_name_" + id).removeClass("song-block");
                    $("#song_name_" + id).attr("data-original-title", "");
                }
            },
            error: function(data) {
                //console.log('Error:', data);
            }
        });
    }

    function markSuggest(id) {

        $.ajax({
            type: "GET",
            url: "/markSuggest",
            data: {
                'id': id
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $.Notification.notify("success", 'top center', '', "Success");
                if (data.content == 1) {
                    $("#song_name_" + id).addClass("color-g");
                } else {
                    $("#song_name_" + id).removeClass("color-g");
                    $(".suggest_" + id).fadeOut("fast");
                }
            },
            error: function(data) {
                //                    console.log('Error:', data);
            }
        });
    }

    //    function openFilter(){
    //        $('.filter-class').fadeIn('slow');
    ////    $('.filter-class').addClass("disp-none")
    //    }
    $("#search_channel").keyup(function() {
        //    alert($(this).val());
    });

    function onDownload(id, link) {
        //    alert(id + link);
        $.ajax({
            type: "GET",
            url: "/ajaxUpdateMusicConfig",
            data: {
                'id': id,
                'is_download': 2
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
        window.open(link, '_blank');
    }
</script>
@endsection