@extends('layouts.master')

@section('content')
<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">Campaign</span>
                <form id="frm" method="GET" action="/campaign" class="m-r-10">
                    <select class="form-control input-sm select-status-campaign" name="status">
                        {!!$status!!}
                    </select>
                </form>
                <button data-toggle="tooltip" data-placement="top" data-original-title="Add New Campaign" type="button" class="btn btn-outline-info btn-import-campaign"><i class="fa fa-plus"></i></button>               
            </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Campaign</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
@if(count($datas)>0)

@php
$listCampignid=[];
@endphp
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <div class="row">
                @foreach($datas as $data)
                <div class="col-lg-3 m-b-20">
                    <div class="card m-h-180 " style="<?php
                        if ($data->campaign_type == 'premium') {
                            echo "border: 1px solid #fbd445";
                        } else if ($data->campaign_type == 'medium') {
                            echo "border: 1px solid #4350ef";
                        } else if ($data->campaign_type == 'regular') {
                            echo "border: 1px solid #227b22";
                        }
                        ?>">
                        <div class="card-body ui-ribbon-wrapper">
                            <div class="ribbon campaign-status" data-id="{{$data->id}}" data-toggle="tooltip" data-placement="top" data-original-title="Click to change status">
                                <span class="sp-{{$data->id}}
                                <?php
                                if ($data->status == 0) {
                                    echo "ribbon-red";
                                } else if ($data->status == 2) {
                                    echo "ribbon-yellow";
                                }
                                ?>">
                                    <?php
                                    if ($data->status == 0) {
                                        echo "Finish";
                                    } else if ($data->status == 1) {
                                        echo "Active";
                                    } else {
                                        echo "Upcoming";
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="ribbon-right campaign-type" data-id="{{$data->id}}" data-toggle="tooltip" data-placement="top" data-original-title="Click to change">
                                <span class="
                                <?php
                                if ($data->campaign_type == 'premium') {
                                    echo "ribbon-right-yellow";
                                } else if ($data->campaign_type == 'medium') {
                                    echo "ribbon-right-blue";
                                } else if ($data->campaign_type == 'regular') {
                                    echo "ribbon-right-green";
                                }
                                ?>">{{$data->campaign_type}}</span>
                            </div>

                            <h3 data-toggle="tooltip" data-placement="top" data-original-title="{{$data->id}}-{{$data->genre}} {{$data->campaign_name}}" class="m-0 <?php echo $data->status == 0 ? "color-red" : ""; ?>" style="text-overflow:ellipsis;overflow: hidden;white-space: nowrap;font-size: 1.35rem;text-align: center">
                                {{$data->campaign_name}}
                            </h3>
                            <h6 data-toggle="tooltip" data-placement="top" data-original-title="This campaign was started on {{$data->campaign_start_date}}" class="m-0 text-muted cur-poiter" style="text-overflow:ellipsis;overflow: hidden;white-space: nowrap;text-align: center">
                                {{$data->campaign_start_date}}
                                <!--({{App\Common\Utils::countDayLeft($data->campaign_start_date)}})-->
                            </h6>
                            <h4 class="my-3 font-18">
                                <p class="m-b-1r"><span class="cur-poiter" data-toggle="tooltip" data-placement="top" data-original-title="Official Video Total Views">Official: {{number_format($data->views_official, 0, '.', ',')}}</span></p>
                                <p class="m-b-1r"><span class="cur-poiter" data-toggle="tooltip" data-placement="top" data-original-title="Lyric Total Views">Lyric: {{number_format($data->views_lyric, 0, '.', ',')}}</span></p>
                                <p class="m-b-1r"><span class="cur-poiter" data-toggle="tooltip" data-placement="top"data-original-title="Mix Total Views">Mix: {{number_format($data->views_compi, 0, '.', ',')}}</span></p>
                                <p class="m-b-1r"><span class="cur-poiter" data-toggle="tooltip" data-placement="top"data-original-title="Short Total Views">Short: {{number_format($data->views_short, 0, '.', ',')}}</span></p>
                            </h4>
<!--                            <h4 class="my-3 font-18">
                                <p class="m-b-1r"><span class="cur-poiter" data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="{!!$data->tooltipCard!!}">Card Clicks: {{number_format($data->cardClick, 0, '.', ',')}}</span></p>
                                <p class="m-b-1r"><span class="cur-poiter" data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="{!!$data->tooltipEns!!}">Endscreen: {{number_format($data->esClicks, 0, '.', ',')}}</span></p>
                            </h4>-->
<!--                            <p class="text-muted m-0">Official Daily Views: {{number_format($data->daily_views_official, 0, '.', ',')}}</p>
                            <p class="text-muted m-0 ">Lyric Daily Views: {{number_format($data->daily_views_lyric, 0, '.', ',')}}</p>-->
                            <!--<p class="text-muted m-0 ">Tiktok Daily Views: {{number_format($data->daily_views_tiktok, 0, '.', ',')}}-->
                            <span class="float-right ">
                                @if(in_array('20',explode(",", $user_login->role)) || in_array('1',explode(",", $user_login->role)))
                                    
                                    @if($data->lyric_timestamp_id==null)
                                        <i onclick="getListUser('{{$data->id}}')" class="ti-music-alt  color-h font-1rem  m-l-5 cur-poiter scale-02" 
                                           data-toggle="tooltip" data-placement="top" data-original-title="Assign make lyric"></i>
                                    @else
                                        <i onclick="getListChannel('{{$data->id}}')" class="btn-show-channel ti-user color-h font-1rem  m-l-5 cur-poiter scale-02" 
                                           data-toggle="tooltip" data-placement="top" data-original-title="Assign manual channel" data-cam-id="{{$data->id}}"
                                           data-music-id="{{$data->lyric_timestamp_id}}" data-songname="{{$data->song_name}}"></i>
                                    
                                        <i class="btn-show-hub ti-video-clapper color-h font-1rem  m-l-5 cur-poiter scale-02" 
                                           data-toggle="tooltip" data-placement="top" data-original-title="Crosspost video promos" 
                                           data-music-id="{{$data->lyric_timestamp_id}}" data-music-type="local" data-cross-type="2" data-songname="{{$data->song_name}}"></i>
                                    @endif
                                    <i onclick="editCampaign('{{$data->id}}')" class="ti-pencil-alt color-h font-1rem m-l-5 cur-poiter scale-02" data-toggle="tooltip" data-placement="top" data-original-title="Edit Campaign"></i>
                                @endif
                                <i onclick="detailCampaign('{{$data->id}}')" class="ti-view-list  color-h font-1rem  m-l-5 cur-poiter scale-02" data-toggle="tooltip" data-placement="top" data-original-title="Video List"></i>
                                <i onclick="checkList('{{$data->id}}')" class="ti-check color-h font-1rem m-l-5 cur-poiter scale-02" data-toggle="tooltip" data-placement="top" data-original-title="Tier Checklist"></i>
                                <i onclick="chartsCampaign({{$data->id}})" class="ti-stats-up color-h font-1rem m-l-5 cur-poiter scale-02" data-toggle="tooltip" data-placement="top" data-original-title="Chart"></i></span></p>

                        </div>
                    </div>
                </div>

                @php
                $listCampignid[]=$data->id;
                @endphp

                @endforeach
                <input type="hidden" id="list-campaign-id" value="{{json_encode($listCampignid)}}" />
                <input type="hidden" id="next-campaign-id" />
                <input type="hidden" id="prev-campaign-id" />
            </div>
            <!--<div class="m-t-10 m-b-10">Detail for campaign: </div>-->
            <div class="m-t-10 m-b-10"><span id="detail-name"></span>&nbsp;<span id="detail-list-video"></span></div>
            <div id="detail">
                <!--                <div class="m-t-10 m-b-10">Detail for campaign: </div>
                <div id="channel-chart" style="overflow: auto;width:99%">
                    <table class="table" style="border-collapse: inherit">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Channel</th>
                                <th>Type</th>
                                <th>Publish</th>
                                <th>Title</th>
                                <th style="text-align: center">Views</th>
                                <th style="text-align: center">Like</th>
                                <th style="text-align: center">Dislike</th>
                                <th style="text-align: center">Comment</th>
                                <th style="text-align: center">Daily Views</th>
                                <th style="text-align: center">Views Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td scope="row">1</td>
                                <td>YUNGBLUD</td>
                                <td>Official</td>
                                <td>10/08/2020</td>
                                <td><a target="_blank" href="https://www.youtube.com/watch?v=8APJv1oJOhs"><img
                                            class="video-img_thumb"
                                            src="https://i.ytimg.com/vi/8APJv1oJOhs/default.jpg">YUNGBLUD - cotton candy
                                        (Official Lyric Video)</a></td>
                                <td style="text-align: center">
                                    <div detail_id="22" class="td-info cur-poiter video_detail" data-toggle="tooltip"
                                        data-html="true" data-placement="top"
                                        data-original-title="Click to open chart<br>Daily: 2132">
                                        <div class="td-daily"><i class="font-25 color-green mdi mdi-trending-up"></i>
                                            <span>999%</span>
                                        </div> 894648
                                    </div>
                                </td>
                                <td style="text-align: center">83000</td>
                                <td style="text-align: center">564</td>
                                <td style="text-align: center">4</td>
                                <td style="text-align: center">32715</td>
                                <td style="text-align: center"><i detail_id="22" class="fa fa-line-chart video_detail"></i></td>
                            </tr>

                        </tbody>
                    </table>
                </div>-->
            </div>
        </div>
    </div>
</div>
@endif


<div class="row">
    <div class="col-md-12">
        <div class="card-box">
                <form id="frmReportGeo">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group row">
                        <label class="col-12 col-form-label">Start Date</label>
                        <div class="col-12">
                            <input id="start_date" type="text" name="start_date"  placeholder="YYYYMMDD" value="<?php echo gmdate("Ymd", time() + 7 * 3600); ?>"
                                   class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group row">
                        <label class="col-12 col-form-label">End Date</label>
                        <div class="col-12">
                            <input id="end_date" type="text" name="end_date"  placeholder="YYYYMMDD" value="<?php echo gmdate("Ymd", time() + 7 * 3600); ?>"
                                   class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-12 col-form-label">List Video</label>
                        <div class="col-12">
                            <textarea class="form-control" rows="5" id="list_video" name="list_video"
                                      spellcheck="false" style="line-height: 1.25;height: 200px"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-outline-info btn-sm btn-get-report-geo"><i
                                        class="fa fa-upload"></i> Submit</button>
                            </div>
                        </div>
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>

<!--<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="text-dark  header-title m-t-0">IMPORT VIDEO</h4>
            <form id="formadd" method="POST">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Campaign Name</label>
                            <div class="col-12">
                                <input type="text" name="campaign_name" id="campaign_name"
                                    class="form-control colorpicker-element">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">&nbsp;</label>
                            <div class="col-12">
                                <select class="form-control input-sm campaign_select">
                                    <option value="">--Select--</option>
                                    @foreach($listCampaign as $campaign)
                                    <option value="{{$campaign->campaign_name}}">{{$campaign->campaign_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Campaign Type</label>
                            <div class="col-12">
                                <select class="form-control input-sm " name="campaign_type">
                                    <option value="premium">Premium</option>
                                    <option value="medium">Medium</option>
                                    <option value="regular">Regular</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Video type</label>
                            <div class="col-12">
                                <select class="form-control input-sm " name="video_type">
                                    <option value="1">Official</option>
                                    <option value="2">Lyric</option>
                                    <option value="3">Tiktok</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">List video</label>
                            <div class="col-12">
                                <textarea class="form-control" rows="5" id="list_video" name="list_video"
                                    spellcheck="false" style="line-height: 1.25;height: 300px"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <button type="button" class="btn btn-outline-info btn-sm btn-import-video"><i
                                    class="fa fa-upload"></i> Import</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>-->

@include('dialog.musicchannel')

@include('dialog.checklist')
@include('dialog.assignmakelyric')
@include('dialog.assignchannel')
@include('dialog.multiupload')
@endsection

@section('script')
<script type="text/javascript">

    $(".btn-get-report-geo").click(function() {
        
        var listVideo = $("#list_video").val();
        var startDate = $("#start_date").val();
        var endDate = $("#end_date").val();
        if(listVideo==""){
            $.Notification.notify("error", 'top center', '', "Please enter list video");
            return;
        }
        var form = $("#frmReportGeo").serialize();
        console.log(form);
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        
//            $.ajax({
//        type: "GET",
//        url: "/exportReportGeo",
//        data: form,
//        dataType: 'json',
//        success: function(data) {
//            console.log(data);
//            $this.html($this.data('original-text'));
//            $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
//        },
//        error: function(data) {
//            $this.html($this.data('original-text'));
//        }
//    });
        var url = "/exportReportGeo?"+form;
        window.location.href = url;

        

    });

    $(".video-sync").click(function(e) {
    e.preventDefault();
    var $this = $(this);
    var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
    if ($(this).html() !== loadingText) {
        $this.data('original-text', $(this).html());
        $this.html(loadingText);
    }
    var id = $(this).attr("data-id");
    $.ajax({
        type: "GET",
        url: "/reSynVideo",
        data: "id="+id,
        dataType: 'json',
        success: function(data) {
            $this.html($this.data('original-text'));
            $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
        },
        error: function(data) {
            $this.html($this.data('original-text'));
            //                but.button('reset');
        }
    });

});


    $("#group_channel_search").change(function(){
        refreshJsTree();
    });
    $("#channel_genre").change(function(){
        refreshJsTree();
        $.ajax({
            type: "GET",
            url: "loadSubgenre",
            data: {"channel_genre": $(this).val()},
            dataType: 'text',
            success: function (data) {
                $("#channel_subgenre").html(data);
            },
            error: function (data) {
                console.log('Error:', data);

            }
        });
    });
    $("#channel_subgenre").change(function(){
        refreshJsTree();
    });
    $("#channel_name").change(function(){
        refreshJsTree();
    });
    $("#limitChannel").change(function(){
        refreshJsTree();
    });
    $("#views").change(function(){
        refreshJsTree();
    });
    $("#subs").change(function(){
        refreshJsTree();
    });
    function refreshJsTree(){
        $('#jstree-channel').jstree("destroy").empty();
        var form = $("#filter_channel").serialize();
        console.log(form);
        $.ajax({
            type: "GET",
            url: "/getHubsByChannelId",
            data: form+"&crossType=2",
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
            var crossType = $("#cross-type").val();
            $.ajax({
                type: "GET",
                url: "/autoMakeLyricHub",
                data: {
                    'trackId': trackId,
                    'trackType': trackType,
                    "crossType": crossType,
                    'hubs': hubs
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    console.log(data.content);
                    var mess = data.content;
                    if(data.status == 'success'){
                        mess= '<ol>';
                        $.each(data.content,function(key, value){
//                            console.log(value);
                            mess+='<li>'+value.channel_name+' &rarr; '+(value.status==1?"success":"fail")+'  </li>';
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
        var crossType = $(this).attr("data-cross-type");
        $("#track-id").val(musicId);
        $("#track-type").val(musicType);
        $("#cross-type").val(crossType);
        var songName = $(this).attr("data-songname");
        $("#dialog-loading").show();
        refreshJsTree();
        $("#dialog-multi-upload-title").html("Config auto upload videos for " + songName);
        $(".dialog-icon").html('<i class="ti-music-alt"></i>');
        $('#dialog_multi_upload').modal({
            backdrop: false
        });
    });   
    
    
    function getListChannel(id) {
        $(".formAssignChannel_content").html("");
        $(".dialog_assign_channel_loadding").show();
        $("#channel_cam_id").val(id);
        $.ajax({
            type: "GET",
            url: "/getListChannelManualPromos",
            data: {
                "id": id
            },
            dataType: 'json',
            success: function(data) {

               $(".dialog_assign_channel_loadding").hide();
               var html='<div class="col-md-12">';
                html+='<table class="table" style="border-collapse: inherit"><thead><tr><th style="width: 5%;text-align: center">#</th>';
                html+='<th><div class="checkbox checkbox-primary">';
                html+='<input id="select_all" type="checkbox" name="select_all">';
                html+='<label for="select_all" class="m-b-22 p-l-0" style="margin-bottom: 1rem"></label>';
                html+='</div></th><th>Channel</th><th>Genre</th>';
                var i = 0;
                $.each(data,function(key,value){
                    i = i + 1;
                html += '<tr><td scope="row">' + i + '</td>';
                html += '<td>';
                html += '<div class="checkbox checkbox-primary">';
                html += '<input class="checkbox-multi" type="checkbox" name="chkChannelAll[]" id="ck-video'+value.id+'" value="'+value.id+'" '+value.checked+'>';
                html += '<label class="m-b-18 p-l-0" for="ck-video'+value.id+'"></label></div>';
                html += '</td>';
                html += '<td>' + value.chanel_name +'</td>';
                html +='<td>' + value.channel_genre +'</td></tr>';
                });
                html+='</table></div>';
                $(".formAssignChannel_content").html(html);
                $('#select_all').change(function () {
                    var checkboxes = $(this).closest('.table').find('.checkbox-multi');
                    checkboxes.prop('checked', $(this).is(':checked'));

                });
                
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
        $(".modal-dialog").removeClass("modal-80");
        $('#dialog_assign_channel').modal({
            backdrop: false
        });
    } 
    
    function assgignChannel() {
        var $this = $(".btn-assign-channel");
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($this.html() !== loadingText) {
            $this.data('original-text', $this.html());
            $this.html(loadingText);
        }
        var formData = $("#formAssignChannel").serialize();
        $.ajax({
            type: "POST",
            url: "/channelAssign",
            data: formData,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    }    
    
    function getListUser(id) {
        
        $.ajax({
            type: "GET",
            url: "/lyricGetListUser",
            data: {
                "id": id
            },
            dataType: 'text',
            success: function(data) {
                $(".formAssignLyric_content").html(data);

            },
            error: function(data) {
                console.log('Error:', data);
            }
        });

        $('#dialog_assign_lyric').modal({
            backdrop: false
        });
    }    
    
    function assgignMakeLyric() {
        var $this = $(".btn-assign-lyric");
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($this.html() !== loadingText) {
            $this.data('original-text', $this.html());
            $this.html(loadingText);
        }
        var formData = $("#formAssignLyric").serialize();
        $.ajax({
            type: "GET",
            url: "/lyricAssign",
            data: formData,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    }    
    
    $(".btn-submit-upload").click(function (e) {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var type = $(this).val();
        var form_file = new FormData();
        var file = $("#" + type + "_upload").val();
        if (file != '') {
            console.log(file);
        } else {
            $.Notification.notify('error', 'top center', '', "You must choose a file");
            return;
        }

        form_file.append('promoUpload', $("#" + type + "_upload")[0].files[0]);
        form_file.append('_token', '{{csrf_token()}}');
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/promo-upload",
            data: form_file,
            contentType: false,
            processData: false,
            success: function (data) {
                $this.html($this.data('original-text'));
                $.Notification.notify('success', 'top center', '', data.message);
                var url = "http://automusic.win/" + data.uploaded_promo;
                $("#"+type+"_url").val(url);
            },
            error: function (data) {
                $this.html($this.data('original-text'));
//                but.button('reset');
            }
        });
    });
    
    function nextChartsCampaign(data){
        var nextId =  $("#"+data+"-campaign-id").val();
        chartsCampaign(nextId,0);
    }
    
    function chartsCampaign(id,show=1) {
        $("#content-dialog").html("");
        $("#dialog-loading").show();

        var listIdStr = $("#list-campaign-id").val();
        var listId = JSON.parse(listIdStr);
//        console.log(listId);
        var total = listId.length;
        $.each(listId, function(k, v) {

            if (v == id) {
                if (k == 0) {
                    $("#next-campaign-id").val(listId[1]);
                    $("#prev-campaign-id").val(listId[total - 1]);
             
                } else if (k == total - 1) {
                    $("#next-campaign-id").val(listId[0]);
                    $("#prev-campaign-id").val(listId[total - 2]);
                   
                } else {
                    $("#next-campaign-id").val(listId[k + 1]);
                    $("#prev-campaign-id").val(listId[k - 1]);
                }
//                console.log("curr:"+id+" next:"+$("#next-campaign-id").val()+" prev:"+$("#prev-campaign-id").val());
            }
        });
        $.ajax({
            type: "GET",
            url: '/getcampaignstatistics',
            data: {
                'id': id
            },
            dataType: 'json',
            success: function(data) {
                var html =
                    '<div class="row"><div class="col-md-12"><div class="control-chart"><i onclick="nextChartsCampaign(\'prev\')" class="ion-chevron-left float-left cur-poiter"></i><i onclick="nextChartsCampaign(\'next\')" class="ion-chevron-right float-right cur-poiter m-r-10"></i></div><canvas id="chart-video-daily"></canvas></div></div>';
                $("#content-dialog").html(html);
                $("#dialog-loading").hide();
                var dataChart = JSON.parse(data.views_detail);
                var label = new Array();
                //                var dataDailyCampaign = new Array();
                var dataDailyLyric = new Array();
                var dataDailyMix = new Array();
                var dataDailyOfficial = new Array();
                var dataDailyShort = new Array();
                var datasets = new Array();
                $.each(dataChart, function(key, value) {
                    if (value.hasOwnProperty('oficial')) {
                        label.push(value.date);
                        dataDailyOfficial.push(value.oficial);
                        dataDailyLyric.push(parseInt(value.lyric_athen));
                        dataDailyMix.push(parseInt(value.compi_athen));
                        dataDailyShort.push(parseInt(value.short_athen));
                    }

                });
                var dataset1 = {
                    label: 'Lyric Daily Views',
                    data: dataDailyLyric,
                    fill: false,
                    backgroundColor: 'rgb(25, 165, 253)',
                    borderColor: 'rgb(25, 165, 253)',
                    borderWidth: 1
                };
                datasets.push(dataset1);
                var dataset2 = {
                    label: 'Official Video Daily Views',
                    data: dataDailyOfficial,
                    fill: false,
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: 'rgb(255, 99, 132)',
                    borderWidth: 1
                };
                datasets.push(dataset2);
                
                var dataset3 = {
                    label: 'Mix Daily Views',
                    data: dataDailyMix,
                    fill: false,
                    backgroundColor: 'rgb(82,187,86)',
                    borderColor: 'rgb(82,187,86)',
                    borderWidth: 1
                };
                datasets.push(dataset3);
                
                
                var dataset4 = {
                    label: 'Short Daily Views',
                    data: dataDailyShort,
                    fill: false,
                    backgroundColor: '#f78829',
                    borderColor: '#f78829',
                    borderWidth: 1
                };
                datasets.push(dataset4);
                drawLineCharts('chart-video-daily', label, datasets);
                $("#dialog-list-tile").text(data.campaign_name);
            },
            error: function(data) {
                console.log(data);
            }
        });
        if(show==1){
        $(".modal-dialog").addClass("modal-80");
        $('#dialog_music_channel').modal({
            backdrop: false
        });
        }
    }
    
    $(".campaign_select").change(function() {
        $("#campaign_name_add").val($(this).val());
    });

    $(".select-status-campaign").change(function() {
        $("#frm").submit();
    });

    //charts('.campaign_detail', '/getcampaignstatistics');

    $(".btn-import-campaign").click(function(e) {
        e.preventDefault();
        clearForm();
        $('#dialog_import_campaign').modal({
            backdrop: false
        });
    });
    
    function editCampaign(id) {
        $("#dialog-loading-edit").show();
        $.ajax({
            type: "GET",
            url: "/getcampaignstatistics",
            data: {
                "id": id
            },
            dataType: 'json',
            success: function(data) {
                $("#dialog-loading-edit").hide();
                console.log(data);
                $("#campaign_name").val(data.campaign_name);
                $("#campaign_start_date").val(data.campaign_start_date);
                $("#campaign_start_time").val(data.campaign_start_time);
                $("#artists_channel").val(data.artists_channel);
                $("#genre").val(data.genre).change();
                $("#label").val(data.label);
                $("#artist").val(data.artist);
                $("#songname").val(data.song_name);
                $("#artists_channel").val(data.artists_channel);
                $("#artists_playlist").val(data.artists_playlist);
                $("#artists_social").val(data.artists_social);
                $("#official_video").val(data.official_video);
                $("#bitly_url").val(data.bitly_url);
                $("#guest_artist_1").val(data.guest_artist_1);
                $("#guest_artist_2").val(data.guest_artist_2);
                $("#guest_artist_3").val(data.guest_artist_3);
                $("#promo_keywords").val(data.promo_keywords);
                $("#audio_url").val(data.audio_url);
                $("#lyric_url").val(data.lyric_url);
                $("#lyrics").val(data.lyrics);
                $("#deezer_artist_id").val(data.deezer_artist_id);
                $("#cam_id").val(id);
                
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
        $(".modal-dialog").addClass("modal-80");
        $('#dialog_import_campaign').modal({
            backdrop: false
        });
    }
    
    function clearForm(){
        $("#campaign_name").val("");
        $("#campaign_start_date").val(getTIMESTAMP(0));
        $("#campaign_start_time").val('00:00:00');
        $("#artists_channel").val("");
        $("#genre").val("-1");
        $("#label").val("");
        $("#artist").val("");
        $("#songname").val("");
        $("#artists_channel").val("");
        $("#artists_playlist").val("");
        $("#artists_social").val("");
        $("#official_video").val("");
        $("#bitly_url").val("");
        $("#guest_artist_1").val("");
        $("#guest_artist_2").val("");
        $("#guest_artist_3").val("");
        $("#promo_keywords").val("");
        $(".audio_url").val("");
        $(".lyric_url").val("");
        $("#lyrics").val("");
        $("#deezer_artist_id").val("0");
        $("#cam_id").val("");
    }
    
    $(".campaign-status").click(function(e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var rbclass = '';
        var text = '';
        $.ajax({
            type: "GET",
            url: '/campaignstatus',
            data: {
                'id': id
            },
            dataType: 'json',
            success: function(data) {
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
                if (data.newStatus == 0) {
                    rbclass = 'ribbon-red';
                    text = 'Finish';
                } else if (data.newStatus == 2) {
                    rbclass = 'ribbon-yellow';
                    text = 'Upcoming';
                } else if (data.newStatus == 1) {
                    rbclass = '';
                    text = 'Active';
                }
                $(".sp-" + id).removeClass("ribbon-red ribbon-yellow").addClass(rbclass);
                $(".sp-" + id).text(text);
            },
            error: function(data) {
                console.log(data);
            }
        });
    });

    $(".btn-add-campaign").click(function(e) {
        e.preventDefault();
        var form = $("#formadd");
        var formData = form.serialize();
        //        console.log(formData);

        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        $this.attr("disabled",true);
        $.ajax({
            type: "POST",
            url: "/addcampaign",
            data: formData,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $this.attr("disabled",false);
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
                $(".campaign_select").html(data.option);
            },
            error: function(data) {
                $this.html($this.data('original-text'));
                console.log('Error:', data);
            }
        });
    });

    $(".btn-edit-campaign").click(function(e) {
        e.preventDefault();
        var form = $("#formedit");
        var formData = form.serialize();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        $.ajax({
            type: "POST",
            url: "/addcampaign",
            data: formData,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.contentedit);
                //            $(".campaign_select").html(data.option);
            },
            error: function(data) {
                $this.html($this.data('original-text'));
                console.log('Error:', data);
            }
        });
    });

    function detailCampaign(id) {
        $("#detail").slideUp('fast');
        $("#detail").html('');
        $.ajax({
            type: "GET",
            url: "/detailcampaign",
            data: {
                "id": id
            },
            dataType: 'json',
            success: function(data) {
                //                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
                //                        console.log(data);
                $("#detail-name").html('Campaign Details: ' + data[0].campaign_name);
                $("#detail-list-video").html('<i onclick="downloadListvideo(\'' + id +
                    '\')" data-toggle="tooltip" data-placement="top" data-original-title="Download list video" class="ti-download cur-poiter td-info color-h"></i>'
                )
                makeTableDetail(data);
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    }



    function checkList(id) {
        $(".dialog_check_list_loadding").show();
        $(".formchecklist_content").html("");
        $("#campaign_id").val(id);
        $.ajax({
            type: "GET",
            url: "/getCheckList",
            data: {
                "id": id
            },
            dataType: 'text',
            success: function(data) {
                console.log(data);
                $(".dialog_check_list_loadding").hide();
                $(".formchecklist_content").html(data);
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });

        //    $(".modal-dialog").addClass("modal-80");
        $('#dialog_check_list').modal({
            backdrop: false
        });

    }

    function importChecklist() {
        var $this = $(".btn-import-checklist");
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($this.html() !== loadingText) {
            $this.data('original-text', $this.html());
            $this.html(loadingText);
        }
        var formData = $("#formchecklist").serialize();
        $.ajax({
            type: "GET",
            url: "/addCheckList",
            data: formData,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    }

    function downloadListvideo(id) {
        window.location.href = "/downloadListvideo?id=" + id;
    }

    function makeTableDetail(data) {

        var html = '';
        html += '<div id="channel-chart" style="overflow: auto">';
        html +=
            '<table class="table" style="border-collapse: inherit"><thead><tr><th>#</th><th>Channel</th><th>Type</th><th>Published</th><th>VideoId</th><th>Title</th>';
        html += '<th style="text-align: left">Views</th>';
        html += '<th style="text-align: left">Likes</th>';
        html += '<th style="text-align: left">Dislikes</th>';
        html += '<th style="text-align: left">Comments</th>';
        html += '<th style="text-align: left">Cards</th>';
        html += '<th style="text-align: left">EndScreens</th>';
        var i = 0;
        $.each(data, function(key, value) {
            i = i + 1;
            var type = '';
            if (value.video_type == 1) {
                type = 'Official';
            } else if (value.video_type == 2) {
                type = 'Lyric';
            } else if (value.video_type == 5){
                type = 'Mix';
            }else if(value.video_type == 6){
                type = 'Short';
            }
            var publish = getTIMESTAMP(value.publish_date);
            var dailyChart =
                html += '<tr><td scope="row">' + i + '</td><td>' + value.channel_name + '</td><td>' + type +
                '</td><td>' + publish + '</td><td>'+ value.video_id + '</td>';
            html += '<td><a target="_blank" href="https://www.youtube.com/watch?v=' + value.video_id +
                '"><img class="video-img_thumb" src="https://i.ytimg.com/vi/' + value.video_id + '/default.jpg">' +
                value.video_title + '</a>&nbsp;<a taget="_blank" href="downloadVideoInfo?id=' + value.id + '"><i class="ti-download cur-poiter td-info color-h"></i></a>&nbsp;<a class="cur-poiter video-sync btn-success" data-id="'+value.id+'"><i class="ti-reload" ></i></a></td>';
            html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_views, value
                .per_daily_views, value.views, 'view') + '</td>';
            html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_like, value
                .per_daily_like, value.like, 'like') + '</td>';
            html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_dislike, value
                .per_daily_dislike, value.dislike, 'dislike') + '</td>';
            html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_comment, value
                .per_daily_comment, value.comment, 'comment') + '</td>';
            html += '<td style="text-align: left">' + value.card_clicks + '</td>';
            html += '<td style="text-align: left">' + value.es_clicks + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        $("#detail").html(html);
        $('#detail').slideDown("fast");
        chart('.video_detail_view', '/getcampaign', 'view');
        chart('.video_detail_like', '/getcampaign', 'like');
        chart('.video_detail_dislike', '/getcampaign', 'dislike');
        chart('.video_detail_comment', '/getcampaign', 'comment');
        $('.td-info').tooltip();
    }

    function chart(control, url, type) {
        $(control).click(function(e) {
            e.preventDefault();
            $("#content-dialog").html("");
            $("#dialog-loading").show();
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    'id': $(this).attr("detail_id")
                },
                dataType: 'json',
                success: function(data) {
                    //                console.log(data);
                    var html =
                        '<div class="row"><div class="col-md-12"><canvas id="chart-video-daily"></canvas></div></div>';
                    $("#content-dialog").html(html);
                    $("#dialog-loading").hide();
                    var dataChart = JSON.parse(data.views_detail);
                    var label = new Array();
                    var dataTotal = new Array();
                    var dataDaily = new Array();
                    $.each(dataChart, function(key, value) {
                        label.push(value.date);
                        if (type == 'view') {
                            dataDaily.push(value.daily);
                        } else if (type == 'like') {
                            dataDaily.push(value.daily_like);
                        } else if (type == 'dislike') {
                            dataDaily.push(value.daily_dislike);
                        } else if (type == 'comment') {
                            dataDaily.push(value.daily_comment);
                        }
                    });
                    drawLineChart('chart-video-daily', dataDaily, label, 'Daily ' + type);
                    $("#dialog-list-tile").text(data.video_title);
                },
                error: function(data) {
                    console.log(data);
                }
            });
            $(".modal-dialog").addClass("modal-80");
            $('#dialog_music_channel').modal({
                backdrop: false
            });
        });
    }

    function charts(control, url) {
        $(control).click(function(e) {
            e.preventDefault();
            $("#content-dialog").html("");
            $("#dialog-loading").show();
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    'id': $(this).attr("detail_id")
                },
                dataType: 'json',
                success: function(data) {
                    var html =
                        '<div class="row"><div class="col-md-12"><div class="control-chart"><i class="ion-chevron-left float-left cur-poiter"></i><i class="ion-chevron-right float-right cur-poiter m-r-10"></i></div><canvas id="chart-video-daily"></canvas></div></div>';
                    $("#content-dialog").html(html);
                    $("#dialog-loading").hide();
                    var dataChart = JSON.parse(data.views_detail);
                    var label = new Array();
                    //                var dataDailyCampaign = new Array();
                    var dataDailyLyric = new Array();
                    var dataDailyTiktok = new Array();
                    var dataDailyOfficial = new Array();
                    var datasets = new Array();
                    $.each(dataChart, function(key, value) {
                        if (value.hasOwnProperty('oficial')) {
                            label.push(value.date);
                            dataDailyOfficial.push(value.oficial);
                            dataDailyLyric.push(parseInt(value.lyric));
                            dataDailyTiktok.push(parseInt(value.tiktok));
                        }

                    });
                    var dataset1 = {
                        label: 'Lyric Daily Views',
                        data: dataDailyLyric,
                        fill: false,
                        backgroundColor: 'rgb(25, 165, 253)',
                        borderColor: 'rgb(25, 165, 253)',
                        borderWidth: 1
                    };
                    datasets.push(dataset1);
                    var dataset2 = {
                        label: 'Official Video Daily Views',
                        data: dataDailyOfficial,
                        fill: false,
                        backgroundColor: 'rgb(255, 99, 132)',
                        borderColor: 'rgb(255, 99, 132)',
                        borderWidth: 1
                    };
                    datasets.push(dataset2);
                    var dataset3 = {
                        label: 'Tiktok Video Daily Views',
                        data: dataDailyTiktok,
                        fill: false,
                        backgroundColor: 'rgb(82,187,86)',
                        borderColor: 'rgb(82,187,86)',
                        borderWidth: 1
                    };
                    datasets.push(dataset3);
                    drawLineCharts('chart-video-daily', label, datasets);
                    $("#dialog-list-tile").text(data.campaign_name);
                },
                error: function(data) {
                    console.log(data);
                }
            });
            $(".modal-dialog").addClass("modal-80");
            $('#dialog_music_channel').modal({
                backdrop: false
            });
        });
    }

    function caculatePercentDaily(id, daily, percentDaily, total, type) {
        var temp = '<div class="td-daily"><i class="font-25 color-green mdi mdi-trending-up"></i>';
        if (percentDaily < 0) {
            temp = '<div class="td-daily"><i class="font-25 color-red mdi mdi-trending-down"></i>';
        } else if (percentDaily == 0) {
            temp = '<div class="td-daily"><i class="font-25 color-y mdi mdi-trending-neutral"></i>';
        }
        var html = '<div detail_id="' + id +
            '" class="td-info cur-poiter video_detail_' + type + '" data-toggle="tooltip" data-html="true"';
        html += 'data-placement="top" data-original-title="Click to open chart<br>Daily: ' + daily + '">';
        html += temp;
        html += '<span>' + Math.abs(percentDaily) + '%</span></div> ' + total + '';
        html += '</div>';
        return html;
    }
</script>
@endsection