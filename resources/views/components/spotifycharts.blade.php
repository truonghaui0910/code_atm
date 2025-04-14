@extends('layouts.master')

@section('content')

<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Spotify Charts </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Spotify Charts</li>
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
                    <div class="col-6">
                        <div class="m-b-15 mdi-size-filter">
                            <a  href="javascript:openFilter()" ><i class="mdi mdi-filter-variant"></i> Filter</a>
                        </div>
                    </div>
<!--                    <form id="form-search " action="/spotifycharts">
                        <div class="col-md-12 filter-class">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Song/Artist/Assigned To</label>
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
                                <input type="hidden" name="limit" id="limit" value="{{$limit}}">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">&nbsp;</label>
                                        <div class="col-12">
                                            <button id="btnSearch" type="submit" class="btn btn-outline-info btn-micro"><i class="fa fa-search"></i> Search</button>   
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>-->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-box">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h4 class="header-title m-t-0 m-b-30"><a id="spotifycharts-top" href="/spotifycharts?type=regional&country=global">TOP 200 ON SPOTIFY CHARTS</a> | <a id="spotifycharts-viral" href="/spotifycharts?type=viral&country=global">VIRAL 50</a></h4>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="country" class="form-control pull-right col-3" name="country">
                                            <option value="global">Global</option>
                                            <option value="us">US</option>
                                            <option value="gb">UK</option>
                                        </select>
                                    </div>
                                </div>
                                

                                <div id="channel-chart" style="overflow: auto;padding-left: 0px;padding-right: 0px;height: 600px;">
                                    <table id="spotify-table" class="fix-header-table table" style="width: 99.5%;table-layout:fixed;">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">#</th>
                                                <th style="width: 20%">Song</th>
                                                <th style="width: 20%">Artist</th>
                                                <th style="width: 10%">Followers</th>
                                                <th style="width: 10%">Popularity</th>
                                                <th style="width: 10%">Artist Genre</th>
                                                <th style="width: 10%">Published</th>
                                                <th style="text-align: center;width: 120px" colspan="3">Action</th>
                                            </tr>
                                        </thead>
                                        <?php $i = 1; ?>
                                        @foreach($datas as $data)
                                        <tbody>
                                            <tr>
                                                <th scope="row">{{$data->id}}</th>
                                                <td><a target="blank" href="{{$data->url_download}}"><span id="song_name_{{$data->id}}" data-toggle="tooltip" data-placement="top"
                                                                                                           data-original-title="<?php echo $data->is_block == 1 ? "Bài hát này bị gậy" : ""; ?>" class="<?php echo $data->is_block == 1 ? "song-block" : ""; ?>">{{$data->name}}</span>  <i class="fa fa-download"></i> </a> <i onclick="reportBlock('{{$data->id}}')"
                                                                                                                                                                                                        class="fa fa-trash color-blue font-1rem " data-toggle="tooltip" data-placement="top"
                                                                                                                                                                                                        data-original-title="Báo bài hát bị gậy"></i>
                                                    <?php echo (time() - strtotime($data->release_date) < 86400) ? "<img src='images/new.jpg' width='32'>" : "" ?></td>
                                                <td>{!!$data->artists!!} </td>
                                                <td>{!!$data->art_followers!!}</td>
                                                <td>{{$data->art_popularity}}</td>
                                                <td data-toggle='tooltip' data-placement='top' data-original-title='{{$data->art_genre}}' style="text-overflow: ellipsis;overflow: hidden;white-space: nowrap;">{{$data->art_genre}}</td>
                                                <td>{{$data->release_date}}</td>
                                                <td style="text-align: center">
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make lyric' href="http://api.automusic.win/spotify/lyric/{{$data->id}}/{{$user_login->user_name}}" class="<?php echo ($data->has_lyric == 1 ? "color-b" : "") ?>"><i class="fa fa-music"></i></a>
                                                </td>
                                                <td>
                                                    @if($data->has_lyric)
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make video lyric on automusic V1' class="color-b" href="http://automusic.win/lyricconfig?type=deezer&data={{$data->id_deezer}}" ><i class="fa fa-video-camera"></i></a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($data->has_lyric)
                                                    <a target="_blank" data-toggle='tooltip' data-placement='top' data-original-title='Make video lyric on automusic V2' class="color-red" href="http://studio.automusic.win/?deezerId={{$data->id_deezer}}" ><i class="fa fa-video-camera"></i></a>
                                                    @endif
                                                </td>

                                            </tr>
                                        </tbody>
                                        @endforeach
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

@include('dialog.log')

@endsection

@section('script')
<script type="text/javascript">

    $("#country").change(function(){
        var url = window.location.href;
        var country = $(this).val();
        var data = url.split("&");
        url = data[0]+"&"+"country="+country;
        window.location.href = url;
        
    });

    function getLog(id) {
        $.ajax({
            type: "GET",
            url: "/ajaxGetLog",
            data: {'id': id},
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $("#log-id").val(data.message[data.message.length - 1].detail);
            },
            error: function (data) {
                //                    console.log('Error:', data);
            }
        });
        $('#dialog').modal({backdrop: false});
    }
    function reportBlock(id) {
        $.ajax({
            type: "GET",
            url: "/reportBlock",
            data: {'id': id},
            dataType: 'json',
            success: function (data) {
                $.Notification.notify("success", 'top center', '', "Success");
                if(data.content==1){
                    $("#song_name_"+id).addClass("song-block");
                    $("#song_name_"+id).attr("data-original-title","Báo bài hát bị gậy");
                }else{
                    $("#song_name_"+id).removeClass("song-block");
                    $("#song_name_"+id).attr("data-original-title","");
                }
            },
            error: function (data) {
                //                    console.log('Error:', data);
            }
        });
       
    }

//    function openFilter(){
//        $('.filter-class').fadeIn('slow');
////    $('.filter-class').addClass("disp-none")
//    }
    $("#search_channel").keyup(function () {
//    alert($(this).val());
    });
    function onDownload(id, link) {
//    alert(id + link);
        $.ajax({
            type: "GET",
            url: "/ajaxUpdateMusicConfig",
            data: {'id': id, 'is_download': 2},
            dataType: 'json',
            success: function (data) {
                console.log(data);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
        window.open(link, '_blank');
    }




</script>
@endsection

