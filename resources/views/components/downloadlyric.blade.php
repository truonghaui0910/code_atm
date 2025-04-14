@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <h4 class="page-title">Download lyric ({{ $count }})</h4>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                    <li class="breadcrumb-item active">Download lyric</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <!--<h4 class="text-dark  header-title m-t-0">Management</h4>-->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="m-b-15 mdi-size-filter">
                            <a href="javascript:openFilter()"><i class="mdi mdi-filter-variant"></i> Filter</a>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <form id="form-search " action="/showlyricdownload">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-8 col-form-label">Song name</label>
                                                <input id="title" class="form-control" type="text" name="title"
                                                    value="{{ $request->title }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-8 col-form-label">Artist</label>
                                                <input id="artist" class="form-control" type="text" name="artist"
                                                    value="{{ $request->artist }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-8 col-form-label">Username</label>
                                                <input id="user-name" class="form-control" type="text" name="user-name"
                                                    value="{{ $request['user-name'] }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-8 col-form-label">&nbsp;</label>
                                                <div class="col-12">
                                                    <button id="btnSearch" type="submit"
                                                        class="btn btn-outline-info btn-micro"><i class="fa fa-search"></i>
                                                        Search</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form method="GET" action="/lyricdownloadbydeezerid">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label class="col-8 col-form-label">Deezer ID</label>
                                                <div class="col-12">
                                                    <input id="deezerid" name="deezerid" class="form-control"
                                                        type="number">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label class="col-8 col-form-label">&nbsp;</label>
                                                <div class="col-12">
                                                    <button type="submit" id="btnDownload" target="_blank"
                                                        href="/lyricdownloadbydeezerid"
                                                        class="btn btn-outline-info btn-micro"><i
                                                            class="fa fa-download"></i> Download</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <form id="formChannel" class="form-horizontal form-label-left" method="POST" style="width: 100%">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-12">

                                <div style="overflow: auto;padding-right: 2px;" id="data-table">
                                    <table class="table mobile-table-width">
                                        <thead class="thead-default">
                                            <tr align="center">
                                                <th style="width: 5%;text-align: center">ID</th>
                                                <th style="text-align: left">User</th>
                                                <th style="text-align: left">Song name</th>
                                                <th style="text-align: left">Artists</th>
                                                <th style="text-align: left">Deezer Artists Id </th>
                                                <th style="text-align: center">Date added</th>
                                                <!--<th style="text-align: center">Preview</th>-->
                                                <th style="width: 5%;text-align: center">Download</th>
                                                <th style="width: 5%;text-align: center">Function</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($datas as $data)
                                                <tr align="center">
                                                    <td>{{ $data->id }} <i class="fa fa-copy"
                                                            onclick="copyToClipboard('https://soundhex.com/timestamp/{{ $data->id }}')"></i>
                                                    </td>
                                                    <td align="left">{{ $data->user_name }} </td>
                                                    <td align="left">{{ $data->title }}</td>
                                                    <td align="left">{{ $data->artist }}</td>
                                                    <td align="left">
                                                        @if ($data->deezer_artist_id != null)
                                                            {{ $data->deezer_artist_id }}
                                                        @else
                                                            <a href="#" class="xedit-able"
                                                                data-url="/update/deezerArtistsId" data-type="text"
                                                                data-pk="{{ $data->id }}" data-placement="right"
                                                                data-placeholder="Required"
                                                                data-title="Enter your deezer artist id"></a>
                                                        @endif
                                                    </td>
                                                    <td>{{ str_replace('Z', '', str_replace('T', ' ', $data->added_time)) }}
                                                    </td>
                                                    <!--<td><span data-toggle="tooltip" data-placement="top" data-original-title="{{ $data->lyric }}"><i class="fa fa-eye"></i></span></td>-->
                                                    <?php $copy = "copyToClipboard('https://automusic.win/lyricdownload/?id=$data->id')"; ?>
                                                    <td><a class="cur-poiter color-h" herf="#"
                                                            onclick="{{ $copy }}" data-toggle="tooltip"
                                                            data-placement="top"
                                                            data-original-title="Copy Link download to clipboard""><i
                                                                class="fa fa-copy"></i></a></td>
                                                    <td>
                                                        <span data-toggle='tooltip' data-placement='top'
                                                            data-original-title='{{ $data->tooltip }}'>
                                                            <button type="button" {{ $data->disable }}
                                                                class="color-green btn btn-show-hub"
                                                                data-music-type="local"
                                                                data-music-id="{{ $data->id }}"
                                                                data-songname="{{ $data->title }}">
                                                                <i class="ion-videocamera"></i>
                                                            </button>
                                                        </span>
                                                        <span data-toggle='tooltip' data-placement='top'
                                                            data-original-title='Save to boom database'>
                                                            <button type="button" class="color-green btn btn-save-bom"
                                                                data-deezer-artist-id="{{ $data->deezer_artist_id }}"
                                                                data-artist="{{ $data->artist }}"
                                                                data-local-id="{{ $data->id }}"
                                                                data-songname="{{ $data->title }}">
                                                                <i class="fa fa-bomb"></i>
                                                            </button>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-md-6 ">
                                <div class="pull-right disp-flex">
                                    <ul class="pagination">
                                        @if ($previous != null)
                                            <li><a href="/showlyricdownload{{ $previous }}" rel="previous">«</a></li>
                                        @endif
                                        @if ($next != null)
                                            <li><a href="/showlyricdownload{{ $next }}" rel="next">»</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>

    @include('dialog.multiupload')
@endsection

@section('script')
    <script type="text/javascript">
        $(".btn-save-bom").click(function(e) {
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var localId = $this.attr("data-local-id");
            var deezerArtId = $this.attr("data-local-id");
            var songName = $this.attr("data-songname");
            var artist = $this.attr("data-artist");
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "boomStore",
                data: {
                    _token: '{{csrf_token()}}',
                    genre: "",
                    localId: localId,
                    songName: songName,
                    artist: artist
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    if (data.status == "success") {
                        setTimeout(location.reload(), 3000);
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                    $this.html($this.data('original-text'));

                }
            });
        });

        $("#group_channel_search").change(function() {
            refreshJsTree();
        });
        $("#channel_genre").change(function() {
            refreshJsTree();
            $.ajax({
                type: "GET",
                url: "loadSubgenre",
                data: {
                    "channel_genre": $(this).val()
                },
                dataType: 'text',
                success: function(data) {
                    $("#channel_subgenre").html(data);
                },
                error: function(data) {
                    console.log('Error:', data);

                }
            });
        });
        $("#channel_tags").change(function() {
            refreshJsTree();
        });
        $("#channel_subgenre").change(function() {
            refreshJsTree();
        });
        $("#channel_name").change(function() {
            refreshJsTree();
        });
        $("#limitChannel").change(function() {
            refreshJsTree();
        });
        $("#views").change(function() {
            refreshJsTree();
        });
        $("#subs").change(function() {
            refreshJsTree();
        });

        function refreshJsTree() {
            $('#jstree-channel').jstree("destroy").empty();
            var form = $("#filter_channel").serialize();
            console.log(form);
            $.ajax({
                type: "GET",
                url: "/getHubsByChannelId",
                data: form + "&crossType=1",
                dataType: 'json',
                success: function(data) {
                    $("#dialog-loading").hide();
                    jsonObj = [];
                    $.each(data, function(key, value) {
                        var item = {};
                        item["id"] = value.channel_id;
                        item["parent"] = "#";
                        item["text"] = value.channel_name;
                        item["icon"] = "ti-user";
                        jsonObj.push(item);
                        if (value.hub.length > 0) {
                            $.each(value.hub, function(k, v) {
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
                            'data': jsonObj
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

                    }).bind("loaded.jstree", function(event, data) {
                        // you get two params - event & data - check the core docs for a detailed description
                        $(this).jstree("open_all");
                        $(this).jstree("check_all");

                    });
                },
                error: function(data) {

                }
            });
        }

        $(".btn-submit-multi-music").click(function() {
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var hubs = [];
            var selectedNodes = $('#jstree-channel').jstree("get_selected", true);
            $.each(selectedNodes, function(key, value) {
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
                success: function(data) {
                    $this.html($this.data('original-text'));
                    console.log(data.content);
                    var mess = data.content;
                    if (data.status == 'success') {
                        mess = '<ol>';
                        $.each(data.content, function(key, value) {
                            //                            console.log(value);
                            mess += '<li>' + value.channel_name + ' &rarr; ' + (value.status ==
                                1 ? "success" : "fail") + '  </li>';
                        });
                    }
                    $.Notification.notify(data.status, 'top center', '', mess);
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                }
            });
        });


        $(".btn-show-hub").click(function(e) {
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
