@extends('layouts.master')

@section('content')
<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">Cover</span>
                <form id="frm" method="GET" action="/cover" class="m-r-10">
                    <select class="form-control input-sm select-status-campaign" name="status">
                        {!!$status!!}
                    </select>
                </form>
                <button data-toggle="tooltip" data-placement="top" data-original-title="Add New Cover" type="button"
                    class="btn btn-outline-info btn-import-campaign"><i class="fa fa-plus"></i></button>
            </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Cover</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
@if(in_array('20',explode(",", $user_login->role)) || in_array('1',explode(",", $user_login->role)))
@if(count($datas)>0)
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <div class="row">
                @foreach($datas as $data)
                <div class="col-lg-3 m-b-20">
                    <div class="card m-h-180 " style="<?php
                    if ($data->campaign_type == 'premium') {
                        echo"border: 1px solid #fbd445";
                    } else if ($data->campaign_type == 'medium') {
                        echo"border: 1px solid #4350ef";
                    } else if ($data->campaign_type == 'regular') {
                        echo"border: 1px solid #227b22";
                    }
                    ?>">
                        <div class="card-body ui-ribbon-wrapper">
                            <div class="ribbon campaign-status" data-id="{{$data->id}}" data-toggle="tooltip"
                                data-placement="top" data-original-title="Click to change status">
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
                            <div class="ribbon-right">
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

                            <h3 data-toggle="tooltip" data-placement="top"
                                data-original-title="{{$data->campaign_name}}"
                                class="m-0 <?php echo $data->status == 0 ? "color-red" : ""; ?>"
                                style="text-overflow:ellipsis;overflow: hidden;white-space: nowrap;font-size: 1.35rem;text-align: center">
                                {{$data->campaign_name}}</h3>
                            <h4 class="my-3 font-18"><p data-toggle="tooltip" data-placement="top"
                                    data-original-title="Lyric Videos Total Views">Lyric Videos: {{number_format($data->views_lyric_video, 0, '.', ',')}} <i
                                        class="fa fa-eye"></i></p> <p data-toggle="tooltip" data-placement="top"
                                    data-original-title="Compilations Total Views" class="">Compilations: {{number_format($data->views_compi, 0, '.', ',')}} <i
                                        class="fa fa-eye"></i></p></h4>
                            <p class="text-muted m-0">Lyric Videos Daily Views: {{number_format($data->daily_lyric_video, 0, '.', ',')}}</p>
                            <p class="text-muted m-0">Compilations Daily Views: {{number_format($data->daily_compi, 0, '.', ',')}}<span
                                    class="float-right">
<!--                                    <i onclick="#" class="fa fa-info-circle color-h font-1rem" data-toggle="tooltip"
                                        data-placement="top" data-original-title="Campaign Info"></i>-->
                                    <i onclick="editCampaign('{{$data->id}}')"
                                        class="ti-pencil-alt color-h font-1rem cur-poiter scale-02" data-toggle="tooltip"
                                        data-placement="top" data-original-title="Edit Campaign"></i>
                                    <i onclick="detailCampaign('{{$data->id}}')"
                                        class="fa fa-list-ul  color-h font-1rem  m-l-5" data-toggle="tooltip"
                                        data-placement="top" data-original-title="Video List"></i>
                                    <i class="fa fa-line-chart color-h campaign_detail font-1rem m-l-5"
                                        detail_id="{{$data->id}}" data-toggle="tooltip" data-placement="top"
                                        data-original-title="Chart"></i></span></p>

                        </div>
                    </div>
                </div>
                @endforeach
            </div>
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
@endif


@include('dialog.musicchannel')
@include('dialog.importcover')
@include('dialog.editcampaign')
@endsection

@section('script')
<script type="text/javascript">
$(".campaign_select").change(function() {
    $("#campaign_name").val($(this).val());
});
$(".select-status-campaign").change(function() {
    $("#frm").submit();
});
charts('.campaign_detail', '/getcampaignstatistics');

$(".btn-import-campaign").click(function(e) {
    e.preventDefault();

    $('#dialog_import_campaign').modal({
        backdrop: false
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

function editCampaign(id) {
    $.ajax({
        type: "GET",
        url: "/getcampaignstatistics",
        data: {
            "id": id
        },
        dataType: 'json',
        success: function(data) {
            //            console.log(data);
            $("#campaign_name").val(data.campaign_name);
            $("#campaign_start_date").val(data.campaign_start_date);
            $("#artists_channel").val(data.artists_channel);
            $("#genre").val(data.genre);
            $("#label").val(data.label);
            $("#cam_id").val(id);
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
    $(".modal-dialog").addClass("modal-80");
    $('#dialog_edit_campaign').modal({
        backdrop: false
    });
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

$(".btn-import-video").click(function(e) {
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
//    $this.attr("disabled",true);
    $.ajax({
        type: "POST",
        url: "/addcover",
        data: formData,
        dataType: 'json',
        success: function(data) {
            $this.html($this.data('original-text'));
//            $this.attr("disabled",false);
            $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
            $(".campaign_select").html(data.option);
            
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
            //            console.log(data);
            makeTableDetail(data);
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
}

function makeTableDetail(data) {
    var html = '<div class="m-t-10 m-b-10">Campaign Details: ' + data[0].campaign_name + ' </div>';
    html += '<div id="channel-chart" style="overflow: auto">';
    html +=
        '<table class="table" style="border-collapse: inherit"><thead><tr><th>#</th><th>Channel</th><th>Type</th><th>Published</th><th>Title</th>';
    html += '<th style="text-align: left">Views</th>';
    html += '<th style="text-align: left">Likes</th>';
    html += '<th style="text-align: left">Dislikes</th>';
    html += '<th style="text-align: left">Comments</th>';
    var i = 0;
    $.each(data, function(key, value) {
        i = i + 1;
        var type = '';
        if (value.video_type == 4) {
            type = 'Lyric Video';
        } else if (value.video_type == 5) {
            type = 'Compilations';
        }
        var publish = getTIMESTAMP(value.publish_date);
        var dailyChart =
            html += '<tr><td scope="row">' + i + '</td><td>' + value.channel_name + '</td><td>' + type +
            '</td><td>' + publish + '</td>';
        html += '<td><a target="_blank" href="https://www.youtube.com/watch?v=' + value.video_id +
            '"><img class="video-img_thumb" src="https://i.ytimg.com/vi/' + value.video_id + '/default.jpg">' +
            value.video_title + '</a>&nbsp;<a taget="_blank" href="downloadVideoInfo?id='+value.id+'"><i class="ti-download cur-poiter td-info color-h"></i></a></td>';
        html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_views, value
            .per_daily_views, value.views, 'view') + '</td>';
        html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_like, value
            .per_daily_like, value.like, 'like') + '</td>';
        html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_dislike, value
            .per_daily_dislike, value.dislike, 'dislike') + '</td>';
        html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_comment, value
            .per_daily_comment, value.comment, 'comment') + '</td>';
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
                    '<div class="row"><div class="col-md-12"><canvas id="chart-video-daily"></canvas></div></div>';
                $("#content-dialog").html(html);
                $("#dialog-loading").hide();
                var dataChart = JSON.parse(data.views_detail);
                var label = new Array();
                var dataDailyLyricVideo = new Array();
                var dataDailyCompilation = new Array();
                var datasets = new Array();
                $.each(dataChart, function(key, value) {
                    if (value.hasOwnProperty('oficial')) {
                        label.push(value.date);
                        dataDailyLyricVideo.push(value.lyric_video);
                        dataDailyCompilation.push(parseInt(value.compi));
                    }

                });
                var dataset1 = {
                    label: 'Lyric Video Daily Views',
                    data: dataDailyLyricVideo,
                    fill: false,
                    backgroundColor: 'rgb(25, 165, 253)',
                    borderColor: 'rgb(25, 165, 253)',
                    borderWidth: 1
                };
                datasets.push(dataset1);
                var dataset2 = {
                    label: 'Compilation Video Daily Views',
                    data: dataDailyCompilation,
                    fill: false,
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: 'rgb(255, 99, 132)',
                    borderWidth: 1
                };
                datasets.push(dataset2);
                drawLineCharts('chart-video-daily', label, datasets);
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