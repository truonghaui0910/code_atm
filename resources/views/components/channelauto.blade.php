@extends('layouts.master')

@section('content')

<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Welcome! @if(isset($user_login)) {{$user_login->name}} @endif</h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-6 col-lg-4">
        <div class="widget-simple-chart text-right card-box">
            <div class="row">
                <div class="col-4"><i class=" mdi mdi-account-circle mdi-size-dashboard gradient-h"></i></div>
                <div class="col-6">
                    <h3 class="text-success counter m-t-10">
                        {{number_format($generalDataUser[0]->total_channel, 0, '.', ',')}}
                    </h3>
                    <p class="text-muted text-nowrap m-b-10">Total Auto Channels</p>
                </div>
            </div>
        </div>
    </div>

    <!--    <div class="col-sm-6 col-lg-3">
        <div class="widget-simple-chart text-right card-box">
            <div class="row">
                <div class="col-4"><i class="mdi mdi-account-multiple mdi-size-dashboard gradient-s"></i></div>
                <div class="col-6">
                    <h3 class="text-success counter m-t-10">
                        {{number_format($generalDataUser[0]->total_sub, 0, '.', ',')}}
                    </h3>
                    <p class="text-muted text-nowrap m-b-10">Total Subs</p>
                </div>
            </div>
        </div>
    </div>-->

    <div class="col-sm-6 col-lg-4">
        <div class="widget-simple-chart text-right card-box">
            <div class="row">
                <div class="col-4"><i class="mdi mdi-eye mdi-size-dashboard gradient-t"></i></div>
                <div class="col-6">
                    <h3 class="text-success counter m-t-10">
                        {{number_format($generalDataUser[0]->total_view, 0, '.', ',')}}
                    </h3>
                    <p class="text-muted text-nowrap m-b-10">Total Views</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="widget-simple-chart text-right card-box">
            <div class="row">
                <div class="col-4"><i class="mdi mdi-chart-bar mdi-size-dashboard gradient-g"></i></div>
                <div class="col-6">
                    <h3 class="text-success counter m-t-10">
                        {{number_format($generalDataUser[0]->total_increasing, 0, '.', ',')}}
                    </h3>
                    <p class="text-muted text-nowrap m-b-10">Daily views</p>
                </div>
            </div>
        </div>
    </div>
</div>
<input id="token" type="hidden" name='_token' value='{{csrf_token()}}' />

<!--<div class="row">
    <div class="col-lg-12">
        <div class="portlet">
            <div id="promo-video-checker" class="portlet-heading portlet-default">
                <h3 class="portlet-title">
                    Channel Auto
                </h3>
                <div class="portlet-widgets">
                    <a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
                    <span class="divider"></span>
                    <a data-toggle="collapse" data-parent="#accordion1" href="#bg-default"><i class="ion-minus-round"></i></a>
                    <span class="divider"></span>
                    <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div id="bg-default" class="panel-collapse collapse show">
                <div class="portlet-body">
                    <div class="row">

                    </div>
                    <div style="overflow: auto">
                        <table id="tbl-channel-auto-ajax" class="table" style="width: 99%;table-layout:fixed;">
                            <thead class="thead-default">
                                <tr>
                            <th class="">Sub genre</th>
                            <th class="">Tags</th> 
                            <th class="">Wake</th> 
                            <th class="">Upload</th>
                            <th class="text-center">Manage</th>
                            <th style="text-align: center">Group</th>
                            <th style="text-align: center;width: 10%">Channel</th>
                            <th style="text-align: center">Channel Type</th>
                            <th style="text-align: center">Genre</th>
                            <th style="text-align: center">Total Uploads</th>
                            <th style="text-align: center">Daily Uploads</th>
                            <th style="text-align: center">Daily Views</th>
                            <th style="text-align: center">Total Views</th>
                            <th style="text-align: center">Sub Percent(%)</th>
                            <th style="text-align: center">Subscribers</th>
                            <th style="text-align: center">Start Date</th>
                            <th style="width: 10%;text-align:right;">AutoWakeup</th>

                                </tr>
                            </thead>
                       
                        </table>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>-->

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <!--<h4 class="header-title m-t-0 m-b-30">List channel 6 floor</h4>-->
            <div id="channel-chart" style="overflow: auto">
                <table id="tbl-channel-auto" class="table" style="width: 99%;table-layout: auto">
                    <thead>
                        <tr>
<!--                            <th>@sortablelink('user_name','Manager')</th>
                            <th style="text-align: center;width: 10%">@sortablelink('chanel_name','Channel')</th>
                            <th style="text-align: center">@sortablelink('channel_type','Channel Type')</th>
                            <th style="text-align: center">@sortablelink('channel_genre','Genre')</th>
                            <th style="text-align: center">@sortablelink('video_count','Total Uploads ')</th>
                            <th style="text-align: center">Daily Uploads</th>
                            <th style="text-align: center">@sortablelink('increasing','Daily Views')</th>
                            <th style="text-align: center">@sortablelink('view_count','Total Views')</th>
                            <th style="text-align: center">@sortablelink('sub_percent','Sub Percent(%)')</th>
                            <th style="text-align: center">@sortablelink('subscriber_count','Subscribers')</th>
                            <th style="text-align: center">@sortablelink('confirm_time','Start Date')</th>
                            <th style="width: 10%;text-align:right;">AutoWakeup</th>
                            <th class="disp-none">Sub genre</th>
                            <th class="disp-none">Tags</th>
                            <th class="disp-none">Wake</th>
                            <th class="disp-none">Upload</th>-->
                            <th class="disp-none">Sub genre</th>
                            <th class="disp-none">Tags</th> 
                            <th class="disp-none">Wake</th> 
                            <th class="disp-none">Upload</th>
                            <th>@sortablelink('user_name','Manager')</th>
                            <td style="text-align: center">Group</th>
                            <th style="text-align: center;width: 10%">@sortablelink('chanel_name','Channel')</th>
                            <th style="text-align: center">@sortablelink('channel_type','Channel Type')</th>
                            <th style="text-align: center">@sortablelink('channel_genre','Genre')</th>
                            <th style="text-align: center">@sortablelink('video_count','Total Uploads ')</th>
                            <th style="text-align: center">Daily Uploads</th>
                            <th style="text-align: center">@sortablelink('increasing','Daily Views')</th>
                            <th style="text-align: center">@sortablelink('view_count','Total Views')</th>
                            <th style="text-align: center">@sortablelink('sub_percent','Sub Percent(%)')</th>
                            <th style="text-align: center">@sortablelink('subscriber_count','Subscribers')</th>
                            <th style="text-align: center">@sortablelink('confirm_time','Start Date')</th>
                            <th style="width: 10%;text-align:right;">AutoWakeup</th>

                            
                        </tr>
                    </thead>
                    <?php
                    $i = 1;
                    $listId = [];
                    $listChannel = [];
                    ?>
                    <tbody>
                        @foreach($channels as $index => $data)
                        <tr class="<?php echo $index % 2 == 0 ? "even" : "odd"; ?> gradeX" align="center">
                            <td class="disp-none">{{$data->channel_subgenre}}</td>
                            <td class="disp-none">{{$data->tags}}</td>
                            <td class="disp-none">{{$data->wake_status_text}}</td>
                            <td class="disp-none">{{$data->last_time_upload}}</td>
                            <td class="" <?php echo ($data->status == 0) ? "style='background: #f31e1ead;'" : "" ?>>
                                {{$data->user_name}}
                            </td>
                            <td>
                                @foreach($groupChannels as $groupChannel)
                                    @if($groupChannel->id==$data->group_channel_id)
                                        {{$groupChannel->group_name}}
                                        @break
                                    @endif
                                @endforeach
                            </td>
                            <td class="text-center">
                                <a target="_blank" href="https://www.youtube.com/channel/{{$data->chanel_id}}">{{$data->chanel_name}}</a>
                                @if($data->gologin!=null)
                                <a target="_blank" href="https://automusic.win/channelmanagement?c1={{$data->chanel_id}}"><i class="fa  fa-moon-o color-red"></i></a>
                                @endif
                                @if($data->strike_data!=null)
                                @foreach($data->strike_data as $idx => $temp)
                                @if($idx <=2) <i id="flag-{{$temp->id}}" style="opacity: {{$temp->opacity}};font-size:1.5em" class="fa fa-flag color-red" ondblclick="removeFlag('{{$temp->id}}')" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$temp->strike_name}}"></i>
                                    @endif
                                    @endforeach
                                    @endif
                            </td>
                            <td>{{$data->channel_type}}</td>
                            <td style="overflow: hidden;white-space: normal;">{{$data->channel_genre}}</td>
                            <td style="text-align: center">{{number_format($data->video_count, 0, '.', ',')}}</td>
                            <td style="text-align: center">
                                {{number_format($data->video_daily, 0, '.', ',')}}/{{$data->limit}} <i onclick="videoDetail({{$data->id}})" class='fa fa-line-chart'></i>
                                <i daily="{{$data->count_daily}}" link="{{$data->chanel_id}}" class='fa fa-list-ul view_list_video'></i>
                            </td>
                            <td style="text-align: center">
                                <span style="font-size: 12px;margin-right: 5px;" class="<?php echo $data->daily_change < 0 ? "color-red" : "color-green"; ?>">
                                    <!--{{$data->daily_change}}%-->
                                </span>{{number_format($data->increasing, 0, '.', ',')}} <i onclick="viewDetail({{$data->id}})" class='fa fa-line-chart'></i>
                            </td>
                            <td style="text-align: center">{{number_format($data->view_count, 0, '.', ',')}} <i onclick="totalViewDetail({{$data->id}})" class='fa fa-line-chart'></i></td>
                            <td style="text-align: center">{{$data->sub_percent}}</td>
                            <td style="text-align: center">{{number_format($data->subscriber_count, 0, '.', ',')}}</td>
                            <td style="text-align: center">
                                @if($data->confirm_time!=null)
                                {{App\Common\Utils::convertToViewDate($data->confirm_time)}}
                                @endif
                            </td>
                            <td style="text-align: right">
                                @if($data->wake_status!=1)
                                @if($data->wake_status!=0)
                                <span id="error_{{$data->chanel_id}}">
                                <span data-toggle="tooltip" data-placement="top" title="" data-html="true" data-original-title="JobId: {{$data->wake_job_id}}<br>{{$data->wake_log}}">
                                    <a target="_blank" href="http://bas.reupnet.info/phpadmin/?username=bas_reup_user&db=bas_reup&edit=job&where%5Bid%5D={{$data->wake_job_id}}">
                                        <i class="fa fa-warning color-red"></i>
                                    </a>
                                </span>
                                <button onclick="retryWake({{$data->wake_id}})" data-toggle="tooltip" data-placement="top" title="" data-original-title="Retry" class="cur-poiter btn-icon btn-success" id="retry_{{$data->wake_id}}"><i class="ti-reload"></i></button>
                                </span>
                                @endif
                                <span id="next_{{$data->chanel_id}}">
                                @if($data->wake_status==0 && $data->next_time_run!=null)
                                <span data-toggle="tooltip" data-placement="top" title="" data-html="true" data-original-title="Next Time Run : {{gmdate("Y/m/d H:i:s", $data->next_time_run + 7 * 3600)}}">
                                    <i class="fa fa-warning color-green"></i>
                                </span>
                                </span>
                                @endif
                                <button onclick="openDialogAddPlaylist({{$data->id}})" data-type="2" data-original-title="Add new playlist use list videos" data-id="{{$data->id}}" class="cur-poiter btn-success" data-toggle="tooltip" data-placement="top" title=""><i class="ti-plus"></i>
                                </button>
                                <span id="run_{{$data->chanel_id}}">
                                @elseif($data->wake_status==1)
                                <span data-toggle="tooltip" data-placement="top" title="" data-html="true" data-original-title="JobId: {{$data->wake_job_id}}<br>RUNNING: {{$data->wake_last_excute}}">
                                    <a target="_blank" href="http://bas.reupnet.info/phpadmin/?username=bas_reup_user&db=bas_reup&edit=job&where%5Bid%5D={{$data->wake_job_id}}">
                                        <i class="fa fa-warning color-y"></i>
                                    </a>
                                </span>
                                @endif
                                </span>
                                <button onclick="openDialogDeletePlaylist({{$data->id}})" data-original-title="Delete playlists and video" data-id="{{$data->id}}" class="cur-poiter btn-danger" data-toggle="tooltip" data-placement="top" title="">{{$data->playlist_count}}<i class="ti-trash"></i>
                                </button>
                            </td>

                        </tr>
                        @php
                        $listId[]=$data->id;
                        $listChannel[]=$data->chanel_id;
                        @endphp
                        @endforeach
                    </tbody>
                </table>
                <input type="hidden" id="list-id" value="{{json_encode($listId)}}" />
                <input type="hidden" id="list-channel-id" value="{{json_encode($listChannel)}}" />
                <input type="hidden" id="next-id" />
                <input type="hidden" id="prev-id" />
            </div>
        </div>

    </div>
</div>

<!--<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="text-dark  header-title m-t-0">CrossPost</h4>
            <form id="form-cross-filter">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group row">
                        <label class="col-8 col-form-label">From</label>
                        <div class="col-12">
                            <input id="from" class="form-control" type="text" name="from" value="{{$currFrom}}">
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group row">
                        <label class="col-8 col-form-label">To</label>
                        <div class="col-12">
                            <input id="to" class="form-control" type="text" name="to" value="{{$currTo}}">
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group row">
                        <label class="col-12 col-form-label">Upload Status</label>
                        <div class="col-12">
                            <select id="search_status" class="form-control" name="up_status">
                                <option value="2">All</option>
                                <option value="1">Uploaded</option>
                                <option value="0">Upload Error</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group row">
                        <label class="col-8 col-form-label">&nbsp;</label>
                        <div class="col-12">
                            <button id="btn-Caculate" type="button" class="btn btn-outline-info btn-cross-report"><i class="ti-view-list-alt"></i> Report</button>   
                        </div>
                    </div>
                </div>
            </div>
            </form>
            <div class="row">
                <div class="col-lg-12">

                    <form id="form-search" action="/autochannel">
                        <div class="row">
                            <input type="hidden" name="limit" id="limit" value="{{$limit}}">
                        </div>
                    </form>
                    <form id="formChannel" class="form-horizontal form-label-left" method="POST">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-12">
                                <div style="overflow: auto;padding-right: 2px;">
                                    <table class="table mobile-table-width">
                                        <thead class="thead-default">
                                            <tr align="center">
                                                <th style="width: 5%;">@sortablelink('id','ID')</th>
                                                <th style="width: 5%;">TrackID</th>
                                                <th style="width: 10%;">User CrossPost</th>
                                                <th style="width: 10%;">Owner</th>
                                                <th style="width: 20%;">@sortablelink('channel_name',trans('label.title.channel'))</th>
                                                <th style="width: 20%;text-align: left">Video</th>
                                                <th style="width: 12%;text-align: center">@sortablelink('publish_date','Publish date')</th>
                                                <th style="width: 10%;text-align: center">@sortablelink('views','Views')</th>
                                                <th style="width: 10%;text-align: center">@sortablelink('daily_views','Daily Views')</th>
                                                <th style="width: 10%;text-align: center">Function</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach($crossPosts as $data)
                                            <tr class="odd gradeX" align="left">
                                                <td>{{$data->id}}</td>
                                                <td>{{$data->track_id}}</td>
                                                <td>{{$data->user_make}}</td>
                                                <td>{{$data->user_owner}}</td>
                                                <td ><a target="_blank" href="https://www.youtube.com/channel/{{$data->channel_id}}">{{$data->channel_name}}</a></td>
                                                <td>
                                                    <a target="_blank" href="https://www.youtube.com/watch?v={{$data->video_id}}">
                                                        <img class="video-img_thumb" src="https://i.ytimg.com/vi/{{$data->video_id}}/default.jpg">{{$data->video_title}}
                                                    </a>
                                                </td>
                                                <td style="text-align: center">{{gmdate("Y/m/d",$data->publish_date)}}</td>
                                                <td style="text-align: center">{{$data->views}}</td>
                                                <td style="text-align: center">{{$data->daily_views}}</td>
                                                <td class="disp-flex">
                                                    @if($data->track_id!=null)
                                                        <button type="button" class="btn btn-danger waves-effect m-r-5 btn-sm delete-cross"
                                                                    value="{{$data->track_id}}"
                                                                    data-type="1"
                                                                    data-container="body" 
                                                                    data-toggle="popover" 
                                                                    data-placement="top"
                                                                    data-html="true"
                                                                    data-content='<button type="button" class="btn btn-danger waves-effect m-b-5 delete-cross" data-type="1" value="{{$data->track_id}}">Delete All</button>
                                                                    <button type="button" class="btn btn-danger waves-effect m-b-5 delete-cross" data-type="2" value="{{$data->id}}">Delete One</button>'>
                                                            Delete All
                                                        </button>
                                                @endif
                                                    <button type="button" class="btn btn-danger waves-effect btn-sm delete-cross" data-type="2" value="{{$data->id}}">Delete One</button>
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
                                <div>

                                    <?php
                                    //                                    $info = str_replace('_START_', $crossPosts->firstItem() != null ? $crossPosts->firstItem() : '0', trans('label.title.sInfo'));
                                    //                                    $info = str_replace('_END_', $crossPosts->lastItem() != null ? $crossPosts->lastItem() : '0', $info);
                                    //                                    $info = str_replace('_TOTAL_', $crossPosts->total(), $info);
                                    //                                    echo $info;
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="pull-right disp-flex">
                                    <select id="cbbLimit" name="limit" aria-controls="tbl-title" class="form-control input-sm">
                                        {!!$limitSelectbox!!}
                                    </select>&nbsp;
                                    <?php // if (isset($crossPosts)) { 
                                    ?>
                                        {!!$crossPosts->links()!!}
                                    <?php // } 
                                    ?>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>-->


@include('dialog.musicchannel')
@include('dialog.addplaylist')
@include('dialog.delete')
@endsection

@section('script')
<script type="text/javascript">
    
    function initAutoChannelTable(){

        $("#tbl-channel-auto-ajax").DataTable().clear().destroy();
        $('#tbl-channel-auto-ajax').DataTable({
        searching: true,
        searchPane: true,
        ordering: false,
        processing: true,
        select: false,
        responsive: false,
        paging: true,
        serverSide: true,
//        ajax:"/listChannelAuto"
        ajax: {
            url: '/listChannelAuto',
            type: "GET",
//            dataSrc: function (d) {
//                return d
//        }
        },
        lengthMenu: [
            [10,50, 100, 200],
            [10,50, 100, 200]
        ],
        searchPanes: {
            //            viewTotal: true,
            viewCount: true,
            //            controls: false,
            collapse: true,
            //            initCollapsed: true,
            columns: [4, 2, 3, 8, 1, 13]
        },
        dom: 'Plfrtip',
        columns: [
            {data: "channel_subgenre"},
            {data: "tags"},
            {data: "wake_status_text"},
            {data: "last_time_upload"},
            {data: "user_name"},
            {data: "user_name"},
            {data: "chanel_name"},
            {data: "channel_type"},
            {data: "channel_genre"},
            {data: "video_count"},
            {data: "video_daily"},
            {data: "increasing"},
            {data: "view_count"},
            {data: "sub_percent"},
            {data: "subscriber_count"},
            {data: "confirm_time"},
            {data: "id"},

        ],
        columnDefs: [
            {"className": "text-center", "targets": "_all"},
            {
                targets: 16,
                data: "download_link",
                render: function(data, type, row, meta) {
                    console.log(data, type, row, meta);
                    return '<button class="btn btn-danger waves-effect waves-light btn-sm btn-ssm m-b-5 btn-del" onclick="deleteGroup(\'' +
                        data + '\')" value="' + data +
                        '"><i class="fa fa-trash"></i> Delete</button>';
                }
            },
            {
                targets: [7, 0, 1, 2, 11, 10, 9, 3],
                visible: false,
                searchable: true
            },
            {
                targets: [9, 10, 11,12, 14, 15,16],
                visible: true,
                searchable: false
            },
            {
                searchPanes: {
                    show: true,
                    header: 'User'
                },
                targets: [4]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Wakeup'
                },
                targets: [2]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Upload'
                },
                targets: [3]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Genre'
                },
                targets: [8]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Sub Genre'
                },
                targets: [0]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Tags'
                },
                targets: [1]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Sub Percent',
                    options: [{
                            label: '0% to 20%',
                            value: function(rowData, rowIdx) {
                                return rowData[13] < 20;
                            }
                        },
                        {
                            label: '20% to 40%',
                            value: function(rowData, rowIdx) {
                                return rowData[13] < 40 && rowData[8] >= 20;
                            }
                        },
                        {
                            label: '40% to 60%',
                            value: function(rowData, rowIdx) {
                                return rowData[13] < 60 && rowData[8] >= 40;
                            }
                        },
                        {
                            label: '60% to 100%',
                            value: function(rowData, rowIdx) {
                                return rowData[13] < 100 && rowData[8] >= 60;
                            }
                        },
                        {
                            label: 'Over 100%',
                            value: function(rowData, rowIdx) {
                                return rowData[13] >= 100;
                            }
                        }
                    ]
                },
                targets: [13]
            }
        ]

    });
    $('#tbl-channel-auto-ajax').DataTable().searchPanes.rebuildPane();
    }
//    initAutoChannelTable();
    
    //truongpv 2023/01/05
    $('#tbl-channel-auto').DataTable({
        searching: true,
        ordering: false,
        processing: false,
        select: false,
        responsive: false,
        paging: true,
//        ajax: '/listChannelAuto',
//                ajax: {
//            url: '/listChannelAuto',
//            type: "GET",
//
//        },
        lengthMenu: [
            [50, 100, 200],
            [50, 100, 200]
        ],
        searchPanes: {
            //            viewTotal: true,
            viewCount: true,
            //            controls: false,
            collapse: true,
            //            initCollapsed: true,
            columns: [4, 2, 3, 8, 1, 13]
        },
        dom: 'Plfrtip',
        columnDefs: [
            {
                "targets": [7, 0, 1, 2, 11, 10, 9, 3],
                "visible": false,
                "searchable": true
            },
            {
                "targets": [9, 10, 11,12, 14, 15, 16],
                "visible": true,
                "searchable": false
            },
            {
                searchPanes: {
                    show: true,
                    header: 'User'
                },
                targets: [4]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Wakeup'
                },
                targets: [2]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Genre'
                },
                targets: [8]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Sub Genre'
                },
                targets: [0]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Tags'
                },
                targets: [1]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Sub Percent',
                    options: [{
                            label: '0% to 20%',
                            value: function(rowData, rowIdx) {
                                return rowData[13] < 20;
                            }
                        },
                        {
                            label: '20% to 40%',
                            value: function(rowData, rowIdx) {
                                return rowData[13] < 40 && rowData[8] >= 20;
                            }
                        },
                        {
                            label: '40% to 60%',
                            value: function(rowData, rowIdx) {
                                return rowData[13] < 60 && rowData[8] >= 40;
                            }
                        },
                        {
                            label: '60% to 100%',
                            value: function(rowData, rowIdx) {
                                return rowData[13] < 100 && rowData[8] >= 60;
                            }
                        },
                        {
                            label: 'Over 100%',
                            value: function(rowData, rowIdx) {
                                return rowData[13] >= 100;
                            }
                        }
                    ]
                },
                targets: [13]
            }
        ]

    });
    $("div.toolbar").html('<b>Custom tool bar! Text/images etc.</b>');
    $(".btn-delete-video").click(function(e) {
        e.preventDefault();

        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var idChannel = $("#id_channel_delete").val();
        var views_delete = $("#views_delete").val();
        var pages_delete = $("#pages_delete").val();
        $.ajax({
            type: "GET",
            url: "/deleteVideosManual",
            data: {
                "idChannel": idChannel,
                "views_delete": views_delete,
                "pages_delete": pages_delete
            },
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);

            },
            error: function(data) {
                $this.html($this.data('original-text'));
            }
        });
    });

    function deletePlaylist() {
        $(".btn-delete-playlist").click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Delete';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var idChannel = $(this).attr("data-id-channel");
            var playlistId = $(this).attr("data-playlist-id");

            $.ajax({
                type: "GET",
                url: "/deletePlaylistManual",
                data: {
                    "idChannel": idChannel,
                    "playlistId": playlistId
                },
                dataType: 'json',
                success: function(data) {
                    $this.closest("tr").hide();
                    $this.html($this.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);

                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                }
            });
        });
    }

    function openDialogDeletePlaylist(id) {
        $(".list-playlist").html("");

        $("#id_channel_delete").val(id);
        $('#dialog-delete').modal({
            backdrop: false
        });
        $("#dialog-delete-loading").show();
        $.ajax({
            type: "GET",
            url: "/getListPlaylistChannel",
            data: {
                "id": id
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                var html = '<div class="row">';
                html += '<div class="col-md-12">';
                html += '<table class="table" style="border-collapse: inherit;table-layout:fixed"><thead><tr><th style="width:5%">#</th><th style="width:45%">Playlist</th><th style="width:25%">Time</th><th>Videos</th><th>Funtion</th>';
                var i = 0;
                $.each(data, function(key, value) {
                    i = i + 1;
                    html += '<tr><td scope="row">' + i + '</td><td class="text-ellipsis"><a href="https://www.youtube.com/playlist?list=' + value.playlistId + '" target="_blank">' + value.playlistName + '</a></td><td>' + value.publishedTimeText + '</td><td>' + value.videoCountText + '</td>';
                    html += '<td><button data-id-channel="' + id + '" data-playlist-id="' + value.playlistId + '" type="button" class="btn btn-outline-danger btn-sm btn-delete-playlist"><i class="fa fa-trash"></i> Delete</button></td>';
                    html += '</tr>';
                });
                html += '</table></div>';
                html += '</table></div></div>';
                $(".list-playlist").html(html);
                $("#dialog-delete-loading").hide();
                deletePlaylist();
            },
            error: function(data) {

            }
        });
    }
    //    $(".btn-delete-dialog").click(function(e) {
    //        e.preventDefault();
    //        $(".list-playlist").html("");
    //        var id = $(this).attr("data-id");
    //        $("#id_channel_delete").val(id);
    //        $('#dialog-delete').modal({
    //            backdrop: false
    //        });
    //        $("#dialog-delete-loading").show();
    //        $.ajax({
    //            type: "GET",
    //            url: "/getListPlaylistChannel",
    //            data: {"id":id},
    //            dataType: 'json',
    //            success: function(data) {
    //                console.log(data);
    //                var html ='<div class="row">';
    //                html+='<div class="col-md-12">';
    //                html+='<table class="table" style="border-collapse: inherit"><thead><tr><th>#</th><th>Playlist</th><th>Time</th><th>Videos</th><th>Funtion</th>';
    //                var i = 0;
    //                 $.each(data,function(key,value){
    //                    i = i + 1;
    //                    html += '<tr><td scope="row">' + i + '</td><td><a href="https://www.youtube.com/playlist?list='+value.playlistId+'" target="_blank">' + value.playlistName + '</a></td><td>' + value.publishedTimeText +'</td><td>' + value.videoCountText +'</td>';
    //                    html+='<td><button data-id-channel="'+id+'" data-playlist-id="'+value.playlistId+'" type="button" class="btn btn-outline-danger btn-sm btn-delete-playlist"><i class="fa fa-trash"></i> Delete</button></td>';
    //                    html+='</tr>';
    //                });
    //                html+='</table></div>';
    //                html+='</table></div></div>';    
    //                $(".list-playlist").html(html);
    //                $("#dialog-delete-loading").hide();
    //                deletePlaylist();
    //            },
    //            error: function(data) {
    //
    //            }
    //        });
    //    });
    $(".delete-cross").click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var type = $(this).attr("data-type");
        var data = $(this).attr("value");
        $.ajax({
            type: "GET",
            url: "/crossPostDelete",
            data: {
                "type": type,
                "data": data
            },
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

    function makeTableReportCrossPost(data) {
        var html = '<div class="row">';
        html += '<div class="col-md-12">';
        html += '<table class="table" style="border-collapse: inherit"><thead><tr><th>#</th><th>User</th><th>Channels</th><th>Tracks</th><th>Videos</th><th>Views</th>';
        var i = 0;
        $.each(data.report, function(key, value) {
            i = i + 1;
            html += '<tr><td scope="row">' + i + '</td><td>' + value.user_make + '</td><td>' + value.channels + '</td><td>' + value.tracks + '</td><td>' + value.videos + '</td><td>' + value.views + '</td></tr>';
        });
        html += '</table></div>';

        html += '</table></div></div>';
        return html;
    }

    $(".btn-cross-report").click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        $("#content-dialog").html("");
        $("#dialog-loading").show();
        var wakeId = $(this).attr("data-id");
        var form = $("#form-cross-filter").serialize();
        $.ajax({
            type: "GET",
            url: "/crossPostReport",
            data: form,
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $this.html($this.data('original-text'));
                $("#dialog-loading").hide();
                var html = makeTableReportCrossPost(data);
                $("#content-dialog").html(html);
                $("#dialog-list-tile").text("Crosspost Report");
                $(".modal-dialog").addClass("modal-80");
                $('#dialog_music_channel').modal({
                    backdrop: false
                });
            },
            error: function(data) {
                $this.html($this.data('original-text'));
                //                but.button('reset');
            }
        });

    });

    function openDialogAddPlaylist(id) {
        $("#wakeup_type").val(1).change();
        $("#title").val("");
        $("#source_wakeup_type").val(1).change();
        $("#playlist_source").val("");
        $("#videos_list_source").val("");
        $("#priority_promo_list").val("");
        $("#wakeup_sort").val(1).change();
        $("#number_videos").val("20");
        $("#playlist_id_channel").val(id);
        $('#dialog_add_playlist').modal({
            backdrop: false
        });
    }
    //    $(".btn-add-playlist-dialog").click(function(e) {
    //        e.preventDefault();
    //        $("#wakeup_type").val(1).change();
    //        $("#title").val("");
    //        $("#source_wakeup_type").val(1).change();
    //        $("#playlist_source").val("");
    //        $("#videos_list_source").val("");
    //        $("#priority_promo_list").val("");
    //        $("#wakeup_sort").val(1).change();
    //        $("#number_videos").val("20");
    //        $("#playlist_id_channel").val($(this).attr("data-id"));
    //        $('#dialog_add_playlist').modal({
    //            backdrop: false
    //        });
    //    });
    function retryWake(id) {
//        debugger;
        var $this = $("#retry_" + id);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($this.html() !== loadingText) {
            $this.data('original-text', $this.html());
            $this.html(loadingText);
        }

        $.ajax({
            type: "GET",
            url: "/refreshAutoWake",
            data: "id=" + id,
            dataType: 'json',
            success: function(data) {
//                console.log(data);
                $(`#error_${data.wake.channel_id}`).html("");
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                var html = `<span data-toggle="tooltip" data-placement="top" title="" data-html="true" data-original-title="JobId: ${data.wake.job_id}<br>RUNNING: ${data.wake.last_excute_time_text}">
                    <a target="_blank" href="http://bas.reupnet.info/phpadmin/?username=bas_reup_user&db=bas_reup&edit=job&where%5Bid%5D=${data.wake.job_id}">
                        <i class="fa fa-warning color-y"></i>
                    </a>
                </span>`;
                $(`#error_${data.wake.channel_id}`).html(html);
                $('[data-toggle="tooltip"]').tooltip();
            },
            error: function(data) {
                $this.html($this.data('original-text'));
                //                but.button('reset');
            }
        });
    }
    $(".refresh-wake").click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var wakeId = $(this).attr("data-id");
        $.ajax({
            type: "GET",
            url: "/refreshAutoWake",
            data: "id=" + wakeId,
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify('error', 'top center', 'Notify', data.message);
            },
            error: function(data) {
                $this.html($this.data('original-text'));
                //                but.button('reset');
            }
        });

    });

    $(".btn-add-wakeup").click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        debugger;
        var type = $(this).attr("data-type");
        if (type == 1) {
            var datas = $("#form-wakeup-add").serialize();
        } else {
            var datas = $("#form-playlist-add").serialize();
        }
        $this.attr("disabled", true);
        $.ajax({
            type: "POST",
            url: "/addWakeup",
            data: datas,
            dataType: 'json',
            success: function(data) {
                $this.attr("disabled", false);
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
            },
            error: function(data) {
                $this.html($this.data('original-text'));
                $this.removeAttr()("disabled");
            }
        });
    });

    function removeFlag(id) {
        $.ajax({
            type: "GET",
            url: '/strikeStatus',
            data: {
                'id': id
            },
            dataType: 'json',
            success: function(data) {
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
                $("#flag-" + id).css({
                    "opacity": data.opacity
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    function nextwakePosition(data) {
        var nextId = $("#" + data + "-id").val();
        wakePosition(nextId, 0);
    }

    function nextChartsDailyView(data) {
        var nextId = $("#" + data + "-id").val();
        viewDetail(nextId, 0);
    }

    function nextChartsTotalView(data) {
        var nextId = $("#" + data + "-id").val();
        totalViewDetail(nextId, 0);
    }

    function nextChartsDailyVideo(data) {
        var nextId = $("#" + data + "-id").val();
        videoDetail(nextId, 0);
    }

    function wakePosition(channelId, show = 1) {
        $("#content-dialog").html("");
        $("#dialog-loading").show();
        var colors = ['#3f33f1', '#ef00ff', '#49eabe', '#c8ea0a', '#ea710a', '#eaf909', '#f336ca', '#7a00ff', '#2bb4e8'];
        var listIdStr = $("#list-channel-id").val();
        var listId = JSON.parse(listIdStr);
        var total = listId.length;
        $.each(listId, function(k, v) {

            if (v == channelId) {
                if (k == 0) {
                    $("#next-id").val(listId[1]);
                    $("#prev-id").val(listId[total - 1]);
                } else if (k == total - 1) {
                    $("#next-id").val(listId[0]);
                    $("#prev-id").val(listId[total - 2]);
                } else {
                    $("#next-id").val(listId[k + 1]);
                    $("#prev-id").val(listId[k - 1]);
                }
            }


        });
        $.ajax({
            type: "GET",
            url: "/promosWakeupVideos",
            data: {
                'channel_id': channelId
            },
            dataType: 'json',
            success: function(data) {
                //                console.log(data);
                //                var html = '<div class="row"><div class="col-md-12"><canvas id="chart-wakeup-position"></canvas></div></div>';
                var html = '<div class="row"><div class="col-md-12">';
                html += '<div class="control-chart"><i onclick="nextwakePosition(\'prev\')" class="ion-chevron-left float-left cur-poiter"></i><i onclick="nextwakePosition(\'next\')" class="ion-chevron-right float-right cur-poiter m-r-10"></i></div>';
                html += '<canvas id="chart-wakeup-position"></canvas></div></div>';
                $("#content-dialog").html(html);
                $("#dialog-loading").hide();
                //                    var label = new Array();
                //                    var dataTotal = new Array();
                //                    var dataDaily = new Array();
                var datasets = new Array();
                var label = new Array();
                $.each(data.data, function(key, video) {
                    var wake = new Array();
                    var pointColor = new Array();
                    var borderColor = new Array();
                    var borderWith = new Array();
                    var position = new Array();
                    $.each(video, function(k, v) {
                        label.push(v.date);
                        position.push(v.position);
                        wake.push(v.wakeup == 1 ? "Linked" : "Unlink");
                        pointColor.push(v.wakeup == 0 ? "#ff0000" : "#04d604");
                        borderColor.push("#fff");
                        borderWith.push(3);
                    });
                    var dataset = {
                        label: video[0].title,
                        data: position,
                        wake: wake,
                        fill: false,
                        //                        pointRadius: 3,
                        pointBorderWidth: borderWith,
                        pointBorderColor: pointColor,
                        pointBackgroundColor: pointColor,
                        borderColor: colors[key],
                        backgroundColor: colors[key],
                        borderWidth: 2
                    };
                    datasets.push(dataset);
                });
                drawLineCharts('chart-wakeup-position', label, datasets);
                $("#dialog-list-tile").html("Promo videos of " + data.channel_name);
            },
            error: function(data) {
                //                but.button('reset');
            }
        });
        if (show == 1) {
            $(".modal-dialog").addClass("modal-80");
            $('#dialog_music_channel').modal({
                backdrop: false
            });
        }

    }

    function totalViewDetail(id, show = 1) {
        $("#content-dialog").html("");
        $("#dialog-loading").show();
        var listIdStr = $("#list-id").val();
        var listId = JSON.parse(listIdStr);
        var total = listId.length;
        $.each(listId, function(k, v) {

            if (v == id) {
                if (k == 0) {
                    $("#next-id").val(listId[1]);
                    $("#prev-id").val(listId[total - 1]);
                } else if (k == total - 1) {
                    $("#next-id").val(listId[0]);
                    $("#prev-id").val(listId[total - 2]);
                } else {
                    $("#next-id").val(listId[k + 1]);
                    $("#prev-id").val(listId[k - 1]);
                }
            }


        });
        $.ajax({
            type: "GET",
            url: "/getvideochart",
            data: {
                'id': id
            },
            dataType: 'json',
            success: function(data) {
                var html = '<div class="row"><div class="col-md-12">';
                html += '<div class="control-chart"><i onclick="nextChartsTotalView(\'prev\')" class="ion-chevron-left float-left cur-poiter"></i><i onclick="nextChartsTotalView(\'next\')" class="ion-chevron-right float-right cur-poiter m-r-10"></i></div>';
                html += '<canvas id="chart-views"></canvas></div></div>';
                $("#content-dialog").html(html);
                $("#dialog-loading").hide();
                var dataChart = JSON.parse(data.view_detail);
                var label = new Array();
                var dataDaily = new Array();
                console.log(dataChart);
                $.each(dataChart, function(key, value) {
                    var tick = value.date.toString()
                    label.push(tick.substring(0, 4) + "/" + tick.substring(4, 6) + "/" + tick.substring(6, 8));
                    dataDaily.push(value.view >= 0 ? value.view : 0);
                });
                drawLineChart('chart-views', dataDaily, label, 'Total Views');
                $("#dialog-list-tile").text(data.chanel_name);
            },
            error: function(data) {}
        });
        if (show == 1) {
            $(".modal-dialog").addClass("modal-80");
            $('#dialog_music_channel').modal({
                backdrop: false
            });
        }

    }

    function viewDetail(id, show = 1) {
        $("#content-dialog").html("");
        $("#dialog-loading").show();
        var listIdStr = $("#list-id").val();
        var listId = JSON.parse(listIdStr);
        var total = listId.length;
        $.each(listId, function(k, v) {

            if (v == id) {
                if (k == 0) {
                    $("#next-id").val(listId[1]);
                    $("#prev-id").val(listId[total - 1]);
                } else if (k == total - 1) {
                    $("#next-id").val(listId[0]);
                    $("#prev-id").val(listId[total - 2]);
                } else {
                    $("#next-id").val(listId[k + 1]);
                    $("#prev-id").val(listId[k - 1]);
                }
            }


        });
        $.ajax({
            type: "GET",
            url: "/getvideochartdaily",
            data: {
                'id': id
            },
            dataType: 'json',
            success: function(data) {
                var html = '<div class="row"><div class="col-md-12">';
                html += '<div class="control-chart"><i onclick="nextChartsDailyView(\'prev\')" class="ion-chevron-left float-left cur-poiter"></i><i onclick="nextChartsDailyView(\'next\')" class="ion-chevron-right float-right cur-poiter m-r-10"></i></div>';
                html += '<canvas id="chart-views"></canvas></div></div>';
                $("#content-dialog").html(html);
                $("#dialog-loading").hide();
                var dataChart = JSON.parse(data.view_detail);
                var label = new Array();
                var dataDaily = new Array();
                $.each(dataChart, function(key, value) {
                    var tick = value.time.toString()
                    label.push(tick.substring(0, 4) + "/" + tick.substring(4, 6) + "/" + tick.substring(6, 8));
                    dataDaily.push(value.daily >= 0 ? value.daily : 0);
                });
                drawLineChart('chart-views', dataDaily, label, 'Daily Views');
                $("#dialog-list-tile").text(data.chanel_name);
            },
            error: function(data) {}
        });
        if (show == 1) {
            $(".modal-dialog").addClass("modal-80");
            $('#dialog_music_channel').modal({
                backdrop: false
            });
        }

    }

    function videoDetail(id, show = 1) {
        $("#content-dialog").html("");
        $("#dialog-loading").show();
        var listIdStr = $("#list-id").val();
        var listId = JSON.parse(listIdStr);
        var total = listId.length;
        $.each(listId, function(k, v) {

            if (v == id) {
                if (k == 0) {
                    $("#next-id").val(listId[1]);
                    $("#prev-id").val(listId[total - 1]);
                } else if (k == total - 1) {
                    $("#next-id").val(listId[0]);
                    $("#prev-id").val(listId[total - 2]);
                } else {
                    $("#next-id").val(listId[k + 1]);
                    $("#prev-id").val(listId[k - 1]);
                }
            }


        });
        $.ajax({
            type: "GET",
            url: "/getvideochart",
            data: {
                'id': id
            },
            dataType: 'json',
            success: function(data) {
                var html = '<div class="row">';
                html += '<div class="control-chart" style="z-index:999;"><i onclick="nextChartsDailyVideo(\'prev\')" class="ion-chevron-left float-left cur-poiter"></i><i onclick="nextChartsDailyVideo(\'next\')" class="ion-chevron-right float-right cur-poiter m-r-10"></i></div>';
                html += '<div class="col-md-6"><canvas id="chart-video"></canvas></div>';
                html += '<div class="col-md-6"><canvas id="chart-video-daily"></canvas></div></div>';
                $("#content-dialog").html(html);
                $("#dialog-loading").hide();
                var dataChart = JSON.parse(data.count_video);
                var label = new Array();
                var dataTotal = new Array();
                var dataDaily = new Array();
                $.each(dataChart, function(key, value) {
                    label.push(value.time);
                    dataTotal.push(value.count);
                    dataDaily.push(value.count_daily);
                });
                drawLineChart('chart-video', dataTotal, label, 'Uploaded');
                drawLineChart('chart-video-daily', dataDaily, label, 'Uploaded Daily');
                $("#dialog-list-tile").text(data.chanel_name);
            },
            error: function(data) {}
        });
        if (show == 1) {
            $(".modal-dialog").addClass("modal-80");
            $('#dialog_music_channel').modal({
                backdrop: false
            });
        }

    }

    $(".view_list_video").click(function() {
        $("#content-dialog").html("");
        $("#dialog-loading").show();
        $.ajax({
            type: "GET",
            url: "/getlistvideo",
            data: {
                'daily': $(this).attr("daily"),
                'link': $(this).attr("link")
            },
            dataType: 'json',
            success: function(data) {
                var html = "";
                //                console.log(data);
                for (var i = 0; i < data.length; i++) {
                    html += '<div class="col-md-12"><ul style="list-style: none">';
                    html += '<div class="row"><div class="col-md-3 m-r-5">';
                    html += '<a target="_blank" href="https://www.youtube.com/watch?v=' + data[i]
                        .video_id + '"><img width="170px" height="110" src="https://i.ytimg.com/vi/' +
                        data[i].video_id + '/hqdefault.jpg"></a>';
                    html += '</div><div class="col-md-8"><span><h6>' + data[i].title + '</h6></span>';
                    //                    html += '<div class="text-ellipsis cur-poiter '+ data[i].colorArtist+'">Artists: ' + data[i].artist + '</div>';
                    html += '<div class="text-ellipsis cur-poiter ' + data[i].colorArtist +
                        '">Artists: ' + data[i].artist + '</div>';
                    html += '<div data-toggle="tooltip" data-placement="top" data-original-title="' +
                        data[i].label + '"  class="text-ellipsis cur-poiter ' + data[i].colorLabel +
                        '">License: ' + data[i].label + '</div>';
                    html += '</div></div></li></ul></div>';
                }

                $("#content-dialog").html(html);
                $("#dialog-loading").hide();
                $('.text-ellipsis').tooltip();
            },
            error: function(data) {
                //                but.button('reset');
            }
        });
        $(".modal-dialog").removeClass("modal-80");
        $('#dialog_music_channel').modal({
            backdrop: false
        });
    });
    $(".video_detail").click(function() {
        $("#content-dialog").html("");
        $("#dialog-loading").show();
        $.ajax({
            type: "GET",
            url: "/getvideochart",
            data: {
                'id': $(this).attr("detail_id")
            },
            dataType: 'json',
            success: function(data) {
                var html = '<div class="row"><div class="col-md-6"><canvas id="chart-video"></canvas></div><div class="col-md-6"><canvas id="chart-video-daily"></canvas></div></div>';
                $("#content-dialog").html(html);
                $("#dialog-loading").hide();
                var dataChart = JSON.parse(data.count_video);
                var label = new Array();
                var dataTotal = new Array();
                var dataDaily = new Array();
                $.each(dataChart, function(key, value) {
                    label.push(value.time);
                    dataTotal.push(value.count);
                    dataDaily.push(value.count_daily);
                });
                drawLineChart('chart-video', dataTotal, label, 'Uploaded');
                drawLineChart('chart-video-daily', dataDaily, label, 'Uploaded Daily');
                $("#dialog-list-tile").text(data.chanel_name);
            },
            error: function(data) {
                //but.button('reset');
            }
        });
        $(".modal-dialog").addClass("modal-80");
        $('#dialog_music_channel').modal({
            backdrop: false
        });
    });
    $(".views_detail").click(function() {
        $("#content-dialog").html("");
        $("#dialog-loading").show();
        $.ajax({
            type: "GET",
            url: "/getvideochart",
            data: {
                'id': $(this).attr("detail_id")
            },
            dataType: 'json',
            success: function(data) {
                //                console.log(data);
                //                var html = '<canvas id="chart-video"></canvas><canvas id="chart-video-daily"></canvas>';
                var html =
                    '<div class="row"><div class="col-md-12"><canvas id="chart-views"></canvas></div></div>';
                $("#content-dialog").html(html);
                $("#dialog-loading").hide();
                var dataChart = JSON.parse(data.view_detail);
                var label = new Array();
                var dataDaily = new Array();
                $.each(dataChart, function(key, value) {
                    label.push(getTIMESTAMP(value.time));
                    dataDaily.push(value.daily >= 0 ? value.daily : 0);
                });
                //                console.log(label);
                //                console.log(dataTotal);
                //                console.log(dataDaily);
                drawLineChart('chart-views', dataDaily, label, 'Daily Views');
                $("#dialog-list-tile").text(data.chanel_name);
            },
            error: function(data) {
                //                but.button('reset');
            }
        });
        $(".modal-dialog").addClass("modal-80");
        $('#dialog_music_channel').modal({
            backdrop: false
        });
    });
</script>
@endsection