@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">No Claim</span>
                <button data-toggle="tooltip" data-placement="top" data-original-title="Add New No Claim Song" type="button" class="btn btn-outline-info btn-import-bom"><i class="fa fa-plus"></i></button>
                <!--<button data-toggle="tooltip" data-placement="top" data-original-title="Export to csv" type="button" class=" m-l-5 btn btn-outline-info btn-export"><i class="fa fa-file-excel-o"></i></button>-->
            </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">No Claim</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <form id="formSearchChannel" action="/noclaim">
                <input type="hidden" name="limit" id="limit" value="{{$limit}}">
                <div class="row">

                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Genre</label>
                            <div class="col-12">
                                <select class="form-control" name="channel_genre">
                                    {!!$genres!!}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">Song Name</label>
                            <div class="col-12">
                                <input id="name" class="form-control" type="text" name="name" value="{{$request->name}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">Artist</label>
                            <div class="col-12">
                                <input class="form-control" type="text" name="artist" value="{{$request->artist}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">&nbsp;</label>
                            <div class="col-12">
                                <button id="btnSearch" type="submit" class="btn btn-outline-info btn-micro"><i class="fa fa-search"></i> Search</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">LIST NO CLAIM ({{$datas->total()}})</h4>
            <div style="overflow: auto;padding-right: 2px;">
                <form id="form-boom" style="max-width: 99%">
                    {{ csrf_field() }}
                    <div class="row">
                        <table class="table mobile-table-width table-drag">
                            <thead class="thead-default">
                                <tr align="center">
                                    <th style="width: 5%;text-align: center">@sortablelink('id','ID')</th>
                                    <th style="width: 10%;text-align: center">Genre</th>
                                    <th style="width: 10%;text-align: left">Username</th>
                                    <th style="width: 15%;text-align: left">Song Name</th>
                                    <!--<th style="width: 10%;text-align: center">Deezer/Local ID</th>-->
                                    <th style="width: 10%;text-align: left">Artist</th>
                                    <th style="width: 10%;text-align: center">Created</th>
                                    <!--<th style="width: 10%;text-align: center">Last used</th>-->
                                    <!--<th style="width: 10%;text-align: center">User use</th>-->
                                    <!--<th style="width: 10%;text-align: center">Count</th>-->
                                    <th style="width: 10%;text-align: right">{{trans('label.col.function')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                                <tr class="odd gradeX" align="center">
                                    <td>{{$data->id}}</td>
                                    <td>{{$data->genre}}</td>
                                    <td style="text-align: left">{{$data->username}}</td>
                                    <td style="text-align: left"><a target="_blank" href="{{$data->direct_link}}">{{$data->song_name}}</a></td>
                                    <td style="text-align: left">{{$data->artist}}</td>
                                    <td>{{$data->created}}</td>
                                    <td style="text-align: right">
                                        @if($data->sync==0)
                                        <!--<span data-toggle='tooltip' data-placement='top' data-original-title='Sync this song to system'><button type="button" class="color-green btn btn-sync-song" data-boom-id="{{$data->id}}"><i class="ion-android-download"></i></button></span>-->
                                        @endif
                                        @if($data->lyric==0 && $data->sync==1)
                                        <span data-toggle='tooltip' data-placement='top' data-original-title='Make lyric for this song'><button type="button" class="color-green btn btn-make-lyric" data-boom-id="{{$data->id}}" {{$data->lyric_disable}}><i class="ion-music-note"></i></button></span>
                                        @elseif($data->lyric==1 && $data->sync==1)
                                        <span data-toggle='tooltip' data-placement='top' data-original-title='Already to use'><i class="fa fa-check"></i></span>
                                        <!--<span data-toggle='tooltip' data-placement='top' data-original-title='Finished make lyric'><button type="button" class="color-t btn btn-finish-make-lyric" data-boom-id="{{$data->id}}"><i class="ion-music-note"></i></button></span>-->
                                        @endif
                                        <!--<span data-toggle='tooltip' data-placement='top' data-original-title='Priority this song'><button type="button" class="<?php echo $data->priority==1?"color-red ":""; ?>btn btn-priority-song" data-boom-id="{{$data->id}}"><i class="ion-star"></i></button></span>-->
                                        <!--<span data-toggle='tooltip' data-placement='top' data-original-title='Make video for this song'><button type="button" class="color-green btn btn-show-hub" data-music-type="<?php echo $data->deezer_id != null ? "deezer" : "local"; ?>" data-music-id="<?php echo $data->deezer_id != null ? $data->deezer_id : $data->local_id; ?>" data-songname="{{$data->song_name}}" {{$data->cross_disable}}><i class="ion-videocamera"></i></button></span>-->
                                        <!--<span data-toggle='tooltip' data-placement='top' data-original-title='Edit this song'><button type="button" class="btn btn-edit-song" data-boom-id="{{$data->id}}"><i class="ion-edit"></i></button></span>-->
                                        <span data-toggle='tooltip' data-placement='top' data-original-title='Remove this song'><button type="button" class="color-red btn btn-remove-song" data-boom-id="{{$data->id}}"><i class="ion-trash-a"></i></button></span>

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="row">
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
                                </select>&nbsp;
                                <?php if (isset($datas)) { ?>
                                    {!!$datas->links()!!}
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('dialog.importnoclaim')
@include('dialog.boomupload')
@endsection

@section('script')
<script type="text/javascript">

    $(".btn-export").click(function () {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var url = "/exportBoom";
        window.location.href = url;
        $this.html($this.data('original-text'));


    });

    $(".btn-finish-make-lyric").click(function (e) {
        e.preventDefault();
        var boomId = $(this).attr("data-boom-id");
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }

        $.ajax({
            type: "GET",
            url: "noclaimFinishMakeLyric",
            data: {
                "boom_id": boomId
            },
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
                if (data.status === "success") {
                    setTimeout(location.reload(), 5000);
                }

            },
            error: function (data) {
                console.log('Error:', data);

            }
        });
    });

    $(".btn-make-lyric").click(function (e) {
        e.preventDefault();
        var boomId = $(this).attr("data-boom-id");
        $.ajax({
            type: "GET",
            url: "noclaimMakeLyric",
            data: {
                "boom_id": boomId
            },
            dataType: 'json',
            success: function (data) {
                if (data.status == "success") {
                    window.open('http://lyric.automusic.win?audio_url=' + data.url + '&username=' + data.username + '&artist='+data.artist+'&title='+data.title+'&lyric= &type=noclaim&deezer_art_id=-1&cam_id='+data.id, '_blank');
                    location.reload();
                }

            },
            error: function (data) {
                console.log('Error:', data);

            }
        });
    });

    $(".btn-remove-song").click(function (e) {
        e.preventDefault();
//        $.notify({
//  title: "abc"
//}, { 
//            style: 'metro',
//            className: "warning",
//            globalPosition:"top center",
//            showAnimation: "show",
//            showDuration: 0,
//            hideDuration: 0,
//            autoHide: false,
//            clickToHide: false
//});
        $.Notification.confirm('warning', 'top center', 'Are you sure?', 'boom');
        var boomId = $(this).attr("data-boom-id");
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        $(document).on('click', '.notifyjs-metro-base .boom_yes', function () {
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $(this).trigger('notify-hide');
            $.ajax({
                type: "GET",
                url: "noclaimRemove",
                data: {
                    "boom_id": boomId
                },
                dataType: 'json',
                success: function (data) {
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    if (data.status == "success") {
                        $this.closest("tr").hide();
                    }

                },
                error: function (data) {
                    $this.html($this.data('original-text'));
                    console.log('Error:', data);

                }
            });

        });
    });
    $(".btn-priority-song").click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var boomId = $(this).attr("data-boom-id");
            $.ajax({
                type: "GET",
                url: "boomRemove",
                data: {
                    "boom_id": boomId,"priority":1
                },
                dataType: 'json',
                success: function (data) {
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    if (data.status == "success") {
                       if(data.boom.priority===1){
                        $this.addClass("color-red");
                       }else{
                           $this.removeClass("color-red");
                       }
                    }

                },
                error: function (data) {
                    $this.html($this.data('original-text'));
                    console.log('Error:', data);

                }
            });

       
    });

    $(".btn-sync-song").click(function (e) {
        e.preventDefault();
        var boomId = $(this).attr("data-boom-id");
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }

        $.ajax({
            type: "GET",
            url: "boomSync",
            data: {
                "boom_id": boomId
            },
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
                setTimeout(location.reload(), 3000);

            },
            error: function (data) {
                $this.html($this.data('original-text'));
                console.log('Error:', data);

            }
        });
    });

    $(".btn-import-bom").click(function (e) {
        e.preventDefault();
        clearForm();
        $('#dialog_import_bom').modal({
            backdrop: false
        });
    });
    
    $(".btn-edit-song").click(function (e) {
        e.preventDefault();
        var id = $(this).attr("data-boom-id");
        $.ajax({
            type: "GET",
            url: "boom/" + id,
            data: {},
            dataType: 'json',
            success: function (data) {
                console.log(data);
                if (data.status === 'error') {
                    $.Notification.notify(data.status, 'top center', '', data.message);
                } else {
                    $("#songName").val(data.song_name);
                    $("#deezerId").val(data.deezer_id);
                    $("#artist").val(data.artist);
                    $("#deezerArtistId").val(data.deezer_artist_id);
                    $("#bom_id").val(data.id);
                    $("#social").val(data.social);
                    $("#localId").val(data.local_id);
                    $("#genre").val(data.genre).change();
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
        $('#dialog_import_bom').modal({
            backdrop: false
        });
    });

    function clearForm() {
        $("#songName").val("");
        $("#deezerId").val("");
        $("#artist").val("");
        $("#deezerArtistId").val("");
        $("#bom_id").val("");
        $("#social").val("");
        $("#genre").val("-1").change();
        $("#genre").val("-1").change();
    }

    $(".btn-save-local-song").click(function (e) {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var form = $("#frmLocalSong");
        var formData = form.serialize();
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "noclaimStore",
            data: formData,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
                if (data.status == "success") {
                    setTimeout(location.reload(), 3000);
                }
            },
            error: function (data) {
                console.log('Error:', data);
                $this.html($this.data('original-text'));

            }
        });
    });
    $("#channel_name").change(function () {
        refreshJsTree();
    });
    $("#limitChannel").change(function () {
        refreshJsTree();
    });
    $("#views").change(function () {
        refreshJsTree();
    });
    $("#subs").change(function () {
        refreshJsTree();
    });

    function refreshJsTree() {
        $('#jstree-channel').jstree("destroy").empty();
        var form = $("#filter_channel").serialize();
        console.log(form);
        $.ajax({
            type: "GET",
            url: "/getHubsByChannelId",
            data: form + "&crossType=3",
            dataType: 'json',
            success: function (data) {
                $("#dialog-loading").hide();
                jsonObj = [];
                $.each(data, function (key, value) {
                    var item = {};
                    item["id"] = value.channel_id;
                    item["parent"] = "#";
                    item["text"] = value.channel_name;
                    item["icon"] = "ti-user";
                    jsonObj.push(item);
                    if (value.hub.length > 0) {
                        $.each(value.hub, function (k, v) {
                            item = {};
                            item["id"] = v.id;
                            item["parent"] = value.channel_id;
                            item["parentname"] = value.channel_name;
                            item["text"] = v.name;
                            item["icon"] = "ti-server";
                            jsonObj.push(item);
                        });
                    }
                });
                $('#jstree-channel').jstree({
                    "core": {
                        'themes': {
                            'responsive': false
                        },
                        "check_callback": true,
                        'data': jsonObj,
                        'multiple': false
                    },
                    'types': {
                        'default': {
                            'icon': 'ti-user'
                        },
                        'file': {
                            'icon': 'fa fa-file'
                        }
                    },
                    'plugins': ['types', 'checkbox']
                            //                "checkbox" : {
                            //					 "three_state": false,
                            //					"keep_selected_style" : false
                            //				},
                            //                "plugins": ["checkbox"]

                }).bind("loaded.jstree", function (event, data) {
                    // you get two params - event & data - check the core docs for a detailed description
                    $(this).jstree("open_all");
                    //                    $(this).jstree("check_all");

                });
            },
            error: function (data) {

            }
        });
    }

    $(".btn-submit-multi-music").click(function () {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var hubs = [];
        var selectedNodes = $('#jstree-channel').jstree("get_selected", true);
        $.each(selectedNodes, function (key, value) {
            if (value.parent != "#") {
                var item = {};
                item["channel_id"] = value.parent;
                item["channel_name"] = value.original.parentname;
                item["hub_id"] = value.id;
                hubs.push(item);
            }
        });

        var trackId = $("#track-id").val();
        var trackType = $("#track-type").val();
        $.ajax({
            type: "GET",
            url: "/autoMakeLyricHub",
            data: {
                'trackId': trackId,
                'trackType': trackType,
                'hubs': hubs
            },
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                var mess = data.content;
                if (data.status == 'success') {
                    mess = '<ol>';
                    $.each(data.content, function (key, value) {
                        //                            console.log(value);
                        mess += '<li>' + value.channel_name + ' &rarr; ' + (value.status == 1 ? "success" : "fail") + '  </li>';
                    });
                }
                $.Notification.notify(data.status, 'top center', '', mess);
            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });

    $(".btn-show-hub").click(function (e) {
        e.preventDefault();
        var musicId = $(this).attr("data-music-id");
        var musicType = $(this).attr("data-music-type");
        $("#track-id").val(musicId);
        $("#track-type").val(musicType);
        //        console.log($("#track-id").val());
        var songName = $(this).attr("data-songname");
        //$("#content-dialog").html("");
        $("#dialog-loading").show();
        refreshJsTree();
        $("#dialog-multi-upload-title").html("Config make videos for " + songName);
        $(".dialog-icon").html('<i class="ti-music-alt"></i>');
        $('#dialog_multi_upload').modal({
            backdrop: false
        });
    });
</script>
@endsection