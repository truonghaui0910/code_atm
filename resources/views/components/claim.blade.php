@extends('layouts.master')

@section('content')
<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">Claim</span>
                <form id="frm" method="GET" action="/claim" class="m-r-10">
                    <select class="form-control input-sm select-status-campaign w-100px" name="status">
                        {!!$status!!}
                    </select>
                </form>
                <button data-toggle="tooltip" data-placement="top" data-original-title="Add New Claim" type="button" class="btn btn-outline-info btn-import-campaign m-r-5"><i class="fa fa-plus"></i></button>
                <input id="dzid" type="text" name="dzid" class="form-control m-l-5" placeholder="Deezer Id">
                <button data-toggle="tooltip" data-placement="top" data-original-title="Download by Deezer Id" type="button" class=" m-l-5 btn btn-outline-info btn-download-dz"><i class="fa fa-download"></i></button>
                <input id="handle" type="text" name="handle" class="form-control m-l-5" placeholder="Handle link">
                <button data-toggle="tooltip" data-placement="top" data-original-title="Get Channel Id" type="button" class=" m-l-5 btn btn-outline-info btn-get-channelid"><i class="fa fa-info-circle"></i></button>
            </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Claim</li>
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
            <div class="chart-all">
                <div class="row">
                    <!--                    <div class="col-md-1">
                        <select class="form-control col-md-12 input-sm select-video-type" name="video_type">
                            <option value="all">Mix-Lyric-Short</option>
                            <option value="5">Mix</option>
                            <option value="2">Lyric</option>
                            <option value="6">Short</option>
                            <option value="1">Official</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <select class="form-control col-md-2 input-sm select-video-type" id="rev_client" name="rev_client">
                            <option value="-1">--All--</option>
                            <option value="antifragile">AntiFragile</option>
                            <option value="empire">Empire</option>
                            <option value="french_montana">French_Montana</option>
                            <option value="wmg">WMG</option>
                        </select>
                    </div>-->
                    <div class="col-md-3 pull-right text-right">
                        <!--<button id="download-report-campaign" class="btn btn-sm"><i class="fa fa-download" data-toggle="tooltip" data-placement="top" data-original-title="Download report"></i></button>-->
                        <!--                        <button class="btn btn-sm btn-range-chart" value="7">7 days</button>
                        <button class="btn btn-sm btn-range-chart" value="30">30 days</button>
                        <button class="btn btn-sm btn-range-chart" value="90">90 days</button>
                        <button class="btn btn-sm btn-range-chart" value="0">All</button>
                        <input id="range_chart" type="hidden" value="30">-->


                        <div class="input-group">
                            @if($is_supper_admin)
                            <span class="input-group-addon import_social_music_asset_open cur-poiter " ><i class="ti-music" data-toggle="tooltip" data-placement="top" data-original-title="Mapping social music"></i></span>
                            <span class="input-group-addon import_report_usd_dialog_open cur-poiter " ><i class=" ti-export" data-toggle="tooltip" data-placement="top" data-original-title="Import Report"></i></span>
                            <span class="input-group-addon report_usd_dialog_open " data-id="0"><i class="fa  fa-usd " data-toggle="tooltip" data-placement="top" data-original-title="$ Report"></i></span>
                            @endif
                           
                            <span class="input-group-addon scan-campain-all " data-id="0"><i class="fa fa-refresh " data-toggle="tooltip" data-placement="top" data-original-title="Rescan views"></i></span>
                            <!--<span class="input-group-addon"><i id="download-report-listvideo-client" class="fa fa-download" data-toggle="tooltip" data-placement="top" data-original-title="Download list video revshare client"></i></span>-->
                            <!--                            <span class="input-group-addon"><i id="download-report-client-campaign" class="fa fa-download" data-toggle="tooltip" data-placement="top" data-original-title="Download report revshare client"></i></span>-->
                            <!--<span class="input-group-addon"><i id="download-report-campaign" class="fa fa-download" data-toggle="tooltip" data-placement="top" data-original-title="Download report"></i></span>-->
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" class="form-control has-feedback-left m-r-5" id="date_rage_picker_main" value="Last 30 days">
                            <select class="form-control input-sm select-period-users">
                                {!!$monthSelect!!}
                            </select>
                        </div>
                        <!--<span class="fa fa-calendar form-control-feedback left" aria-hidden="true" style="color: #000"></span>-->
                        <input type="hidden" id="userNameMain" value="-1" />
                        <input type="hidden" id="startDateMain" />
                        <input type="hidden" id="endDateMain" />


                    </div>
                    <!--<div id="control-chart" class="col-md-12">-->
                    <!--<canvas id="chart-all"></canvas>-->
                    <!--</div>-->

                </div>
            </div>

            <input type="hidden" id="list-campaign-id" value="{{json_encode($listCampignid)}}" />
            <input type="hidden" id="next-campaign-id" />
            <input type="hidden" id="prev-campaign-id" />
            <input type="hidden" id="filter_id" value="{{ $request->filter_id }}" />

        </div>
    </div>
</div>



<div class="row" id="content-monthly-users">
    <div class="col-md-12">
        <div class="portlet">
            <div class="portlet-heading portlet-default">
                <h3 class="portlet-title">

                </h3>
                <div class="portlet-widgets">
                    <a id="reload-1" href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
                    <span class="divider"></span>
                    <a data-toggle="collapse" data-parent="#accordion1" href="#portlet-1"><i class="ion-minus-round"></i></a>
                    <span class="divider"></span>
                    <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div id="portlet-1" class="panel-collapse collapse show">
                <div class="portlet-body ">
                    
                </div>
            </div>
        </div>
    </div>
</div>
@if($is_supper_admin)
<div class="row" id="content-monthly-distributor">
    <div class="col-md-12">
        <div class="portlet">
            <div class="portlet-heading portlet-default">
                <h3 class="portlet-title">

                </h3>
                <div class="portlet-widgets">
                    <a id="reload-1" href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
                    <span class="divider"></span>
                    <a data-toggle="collapse" data-parent="#accordion1" href="#portlet-1"><i class="ion-minus-round"></i></a>
                    <span class="divider"></span>
                    <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div id="portlet-1" class="panel-collapse collapse show">
                <div class="portlet-body ">

                </div>
            </div>
        </div>
    </div>
</div>
@endif


<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <div style="overflow: auto;padding-right: 2px;">
                <form id="form-campaign">
                    <table id="tbl-campaign" class="table mobile-table-width table-drag">
                        <thead class="thead-default">
                            <tr align="center">
                                <th style="width: 5%;text-align: center">
                                    <!--<div class="checkbox checkbox-primary tbl-chk">
<input id="select_all" type="checkbox" name="select_all">
<label for="select_all" class="m-b-22 p-l-0" style="margin-bottom: 1rem"></label>
</div>-->
                                </th>
                                <th style="width: 5%;text-align: center">@sortablelink('id','ID')</th>
                                <th style="width: 20%;">Campaign Name</th>
                                <th style="width: 10%;text-align: center">Username</th>
                                <th style="width: 10%;text-align: center">Genre</th>
                                <th style="width: 10%;text-align: center">Start Date</th>
               
                                <th style="width: 5%;text-align: center">Distributor</th>
                           
                                <th style="width: 7%;text-align: center">Mix</th>
                                <th style="width: 7%;text-align: center">Lyric</th>
                                <th style="width: 7%;text-align: center">Total</th>
                                <th style="width: 7%;text-align: center">Count</th>
                                <th style="width: 5%;text-align: center">Artist</th>
                                <th style="width: 10%;text-align: right">{{trans('label.col.function')}}</th>
                                <th  class="disp-none">PreName</th>
                                <th  class="disp-none">Status</th>
                                <th  class="disp-none">AssetId</th>
                                <th  class="disp-none">YT Distributor</th>
                                <th  class="disp-none">Distributor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datas as $data)
                            <tr class="odd gradeX" align="center">
                                <td>
                                    <div class="checkbox checkbox-primary tbl-chk">
                                        <input class="disp-none" type="checkbox" name="chk_position[]" value="{{$data->id}}" checked="checked">
                                        <input class="checkbox-multi chk-view-chart" type="checkbox" name="chk_campaign[]" id="ck-chart<?php echo $data->id; ?>" value="{{$data->id}}">
                                        <label class="m-b-18 p-l-0" for="ck-chart<?php echo $data->id; ?>"></label>
                                    </div>
                                </td>
                                <td>{{$data->id}}</td>
                                <td style="text-align: left">
                                    {{$data->campaign_name_short}}<br>
                                    @if($data->status==2)
                                    <i class="fa fa-exclamation-triangle cur-poiter" data-toggle="tooltip" data-placement="top" data-original-title="Upcoming campaign"></i>
                                    @endif
                                    @if($data->tier==1)
                                    <i class="fa fa-heart cur-poiter color-red" data-toggle="tooltip" data-placement="top" data-original-title="Tier 1"></i>
                                    @endif
                                    @if($data->status==4)
                                    <i class="fa  fa-pause" data-toggle="tooltip" data-placement="top" data-original-title="Pause campaign"></i>
                                    @endif
                                    @if($data->crypto_view_run==1)
                                    <span class="badge label-table badge-danger" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Crypto Running</span>
                                    @elseif($data->crypto_view_run==2)
                                    <span class="badge label-table badge-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Crypto Done</span>
                                    @endif
                                    @if($data->adsense_view_run==1)
                                    <span class="badge label-table badge-danger" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Adsense Running</span>
                                    @elseif($data->adsense_view_run==2)
                                    <span class="badge label-table badge-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="">Adsense Done</span>
                                    @endif

                                    @if($data->progress_mix!=null)
                                    <div class="progress progress-custom progress-lg m-b-20 ">
                                        <div class="color-cus progress-bar progress-bar-custom progress-bar-lg {{$data->progress_mix->color}}" role="progressbar" style="width: {{$data->progress_mix->percent}}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{$data->progress_mix->name}} {{$data->progress_mix->views}} / {{$data->progress_mix->target}} - <b>{{$data->progress_mix->percent}}%</b></div>
                                    </div>
                                    @endif
                                    @if($data->progress_official!='' )
                                    <div class="progress progress-custom progress-lg m-b-20 ">
                                        <div class="color-cus progress-bar progress-bar-custom progress-bar-lg {{$data->progress_official->color}}" role="progressbar" style="width: {{$data->progress_official->percent}}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{$data->progress_official->name}} {{$data->progress_official->achieved}} / {{$data->progress_official->target}} - <b>{{$data->progress_official->percent}}% </b></div>
                                    </div>
                                    @endif
                                </td>

                                <td>{{$data->username}}</td>
                                <td>{{$data->custom_genre}}_{{$data->genre}}</td>
                                <td>{{$data->campaign_start_date}}</td>
                                
                                <td>{{$data->distributor}}<br>
                                    @if($data->yt_distributor=='No_Claim')
                                        <span class="color-red">{{$data->yt_distributor}}</span>
                                    @elseif($data->yt_distributor=='Not_Check_Yet')
                                        <span class="color-y">{{$data->yt_distributor}}</span>
                                    @else
                                         <span class="">{{$data->yt_distributor}}</span>
                                    @endif
                                    
                                </td>
                              
                                <td>
                                    <div data-toggle="tooltip" data-placement="top" 
                                         data-original-title="View current month" 
                                         class="cur-poiter color-green"><b>{{number_format($data->monthly_views_mix, 0, '.', ',')}}</b></div>
                                         {{number_format($data->views_compi, 0, '.', ',')}}</td>
                                <td>
                                    <div data-toggle="tooltip" data-placement="top" 
                                         data-original-title="View current month" 
                                         class="cur-poiter color-green"><b>{{number_format($data->monthly_views_lyric, 0, '.', ',')}}</b></div>
                                         {{number_format($data->views_lyric, 0, '.', ',')}}</td>
                                <td>
                                    <div data-toggle="tooltip" data-placement="top" 
                                         data-original-title="View current month" 
                                         class="cur-poiter color-green"><b>{{number_format(($data->monthly_views_mix + $data->monthly_views_lyric + $data->monthly_views_short), 0, '.', ',')}}</b></div>
                                         {{number_format(($data->views_compi + $data->views_lyric + $data->views_short), 0, '.', ',')}}
                                </td>
                                <td>
                                    <div data-toggle="tooltip" data-placement="top" 
                                         data-original-title="Videos current month" 
                                         class="cur-poiter color-green"><b>{{number_format($data->monthly_count_videos, 0, '.', ',')}}</b></div>
                                         {{number_format($data->count_videos, 0, '.', ',')}}
                                </td>
                                <td>{{$data->artist}}</td>
                                <td style="text-align: right">
                                    @if(in_array('20',explode(",", $user_login->role)) || in_array('1',explode(",",$user_login->role)))

                                    @if($data->lyric_timestamp_id==null)
                                    <i onclick="getListUser('{{$data->id}}')" class="ti-music-alt  color-h font-22  m-l-5 cur-poiter " data-toggle="tooltip" data-placement="top" data-original-title="Assign make lyric"></i>
                                    @else
                                    <i onclick="getListChannel('{{$data->id}}')" class="btn-show-channel ti-user color-h font-22  m-l-5 cur-poiter " data-toggle="tooltip" data-placement="top" data-original-title="Assign manual channel" data-cam-id="{{$data->id}}" data-music-id="{{$data->lyric_timestamp_id}}" data-songname="{{$data->song_name}}"></i>


                                    @endif
                                    @endif
                                    <i onclick="editCampaign('{{$data->id}}')" class="ti-pencil-alt color-h font-22 m-l-5 cur-poiter" data-toggle="tooltip" data-placement="top" data-original-title="Edit Campaign"></i>
                                    <i class="btn-show-hub ti-video-clapper color-h font-22  m-l-5 cur-poiter " data-toggle="tooltip" data-placement="top" data-original-title="Crosspost video promos" data-music-id="{{$data->lyric_timestamp_id}}" data-music-type="local" data-cross-type="2" data-songname="{{$data->song_name}}"></i>
                                    <a href="claim#detail" onclick="detailCampaign('{{$data->id}}')" class="ti-view-list  color-h font-22  m-l-5 cur-poiter " data-toggle="tooltip" data-placement="top" data-original-title="Video List"></a>
                                    <!--<i onclick="checkList('{{$data->id}}')" class="ti-check color-h font-1rem m-l-5 cur-poiter scale-02" data-toggle="tooltip" data-placement="top" data-original-title="Tier Checklist"></i>-->
                                    <i onclick="chartsCampaign({{$data->id}})" class="ti-stats-up color-h font-22 m-l-5 cur-poiter" data-toggle="tooltip" data-placement="top" data-original-title="Chart"></i></span>
                                    <span onclick="scanCampaignAll(this)" class="cur-poiter td-info color-h font-22 m-l-5 " data-id="{{$data->id}}"><i data-toggle="tooltip" data-placement="top" data-original-title="Scan view list video" class="ti-reload"></i></span>
                                    <i data-toggle="tooltip" data-placement="top" data-original-title="Report by channel manager" class="ti-files cur-poiter td-info color-h font-22 m-l-5 report-user" data-id="{{$data->id}}"></i>
                                </td>
                                <td  class="disp-none">{{$data->pre_name}}</td>
                                <td  class="disp-none">{{$data->status}}</td>
                                <td  class="disp-none">{{$data->asset_id}}</td>
                                <td  class="disp-none">{{$data->yt_distributor}}</td>
                                <td  class="disp-none">{{$data->distributor}}</td>
                            </tr>
                            @php
                            $listCampignid[]=$data->id;
                            @endphp

                            @endforeach
                        </tbody>

                    </table>
                </form>
            </div>

            <input type="hidden" id="list-campaign-id" value="{{json_encode($listCampignid)}}" />
            <input type="hidden" id="next-campaign-id" />
            <input type="hidden" id="prev-campaign-id" />
            <!--</div>-->
            <!--            <div class="m-t-10 m-b-10"><span id="detail-name"></span>&nbsp;<span id="detail-list-video"></span></div>
            <div id="detail"></div>-->
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <div id="loading-list-video" class="disp-none" style="text-align: left;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="m-t-10 m-b-10"><span id="detail-name"></span>&nbsp;<span id="detail-list-video"></span></div>
            <div id="detail"></div>
        </div>
    </div>
</div>
@endif
@if($is_admin_music)
<div class="row">
    <div class="col-md-4">
        <div class="card-box">
            <h4><span class="m-t-7 m-r-10">Artists</span><button data-toggle="tooltip" data-placement="top" data-original-title="Add artist to labelgrid.com" type="button" class="btn btn-outline-info btn-import-artist m-r-5 btn-sm"><i class="fa fa-user"></i></button></h4>
            <div style="overflow: auto;padding-right: 2px;">
                <form id="form-campaign">
                    <table class="table mobile-table-width table-drag">
                        <thead class="thead-default">
                            <tr align="center">
                                <th style="width: 5%;text-align: center">Artist Id</th>
                                <th style="width: 10%;text-align: left">Artist Name</th>
                                <th style="width: 10%;text-align: right">Function</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gridArtists as $data)
                            <tr class="odd gradeX" align="center">
                                <td style="text-align: center">{{$data->artist_id}}</td>
                                <td style="text-align: left">{{$data->artist_name}}</td>
                                <td style="text-align: right">
                                    <!--                                    <i onclick="getListChannel('{{$data->id}}')"
                                        class="btn-show-channel ti-user color-h font-1rem  m-l-5 cur-poiter scale-02"
                                        data-toggle="tooltip" data-placement="top" data-original-title=""></i>-->
                                </td>
                            </tr>

                            @endforeach
                        </tbody>

                    </table>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-box">
            <h4><span class="m-t-7 m-r-10">Releases</span> <button data-toggle="tooltip" data-placement="top" data-original-title="Add releases to labelgrid.com" type="button" class="btn btn-outline-info btn-import-releases m-r-5 btn-sm"><i class="fa fa-calendar-check-o"></i></button></h4>
            <div style="overflow: auto;padding-right: 2px;">
                <form id="form-campaign">
                    <table class="table mobile-table-width table-drag">
                        <thead class="thead-default">
                            <tr align="center">
                                <th style="width: 10%;text-align: center">ReleaseID</th>
                                <th style="width: 10%;text-align: left">Title</th>
                                <th style="width: 10%;text-align: center">Date</th>
                                <th style="width: 10%;text-align: center">Artist Id</th>
                                <th style="width: 10%;text-align: right">Function</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gridReleases as $data)
                            <tr class="odd gradeX" align="center">
                                <td style="text-align: center">{{$data->release_id}}</td>
                                <td style="text-align: left">{{$data->title}}</td>
                                <td style="text-align: center">{{$data->release_date}}</td>
                                <td style="text-align: center">{{$data->artist_id}}</td>
                                <td style="text-align: right">
                                    @if($data->image_s3==null)
                                    <i class="btn-release-image fa fa-image color-h font-1rem  m-l-5 cur-poiter scale-02" data-id="{{$data->release_id}}" data-toggle="tooltip" data-placement="top" data-original-title="Upload image"></i>
                                    @endif

                                </td>
                            </tr>

                            @endforeach
                        </tbody>

                    </table>
                </form>
            </div>

        </div>
    </div>
    <div class="col-md-4">
        <div class="card-box">
            <h4><span class="m-t-7 m-r-10">Tracks</span><button data-toggle="tooltip" data-placement="top" data-original-title="Add track to labelgrid.com" type="button" class="btn btn-outline-info btn-import-track m-r-5 btn-sm"><i class="fa fa-music"></i></button></h4>

        </div>
    </div>
</div>
@endif


@include('dialog.musicchannel')
@include('dialog.importclaim')
@include('dialog.assignmakelyric')
@include('dialog.assignchannel')
@include('dialog.multiupload')

@include('dialog.viewchart')
<!--@include('dialog.distribute_releases_image')-->
@include('dialog.report_campaign_rev')
@include('dialog.import_campaign_rev')
@include('dialog.importSocialMusicAsset')

@endsection

@section('script')
<script type="text/javascript">
    $(function() {
        var filterId = $("#filter_id").val();
        if (filterId != "") {
//                $('[type="search"]').val(filterId).change();
            editCampaign(filterId);
        }
    });
    
    function checkclaim(id,remake=0){
        var org = $(".btn-check-claim").html();
        $(".btn-check-claim").html(`<i class="fa fa-circle-o-notch fa-spin"></i> Making video...`);

            $.ajax({
            type: "GET",
            url: "/checkClaimDistributor",
            data: {
                id: id,
                us:usr,
                remake:remake //remake=1 => làm lại từ đầu
            },
            dataType: 'json',
            success: function(data) {
                if(data.message=="finished"){
                    $(".btn-check-claim").html(`<i class="fa fa-refresh"></i>`);
                }else{
                    $(".btn-check-claim").html(`<i class="fa fa-circle-o-notch fa-spin"></i> Uploading...`);
                }
                if(data.reload){
                    editCampaign(id,0);
                }

                if(data.status=="error"){
                    logger('cmd', data.cmd);
                    logger('rs', data.rs);
                    $.Notification.notify('error', 'top center', 'Notification', data.message);
                    $(".btn-check-claim").html(`<i class="fa fa-refresh"></i>`);
                }
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    }
    

        window.onkeydown = function(e){
            if(e.keyCode == 70 && e.ctrlKey){
                e.preventDefault();
                $('input[type="search"]').focus();
            }
        }
    //2024/03/04 import social asset để mapping bài hat vs db của mình
    $(".import_social_music_asset_open").click(function(){
        $('#dialog_import_social_music_asset').modal({
            backdrop: false
        });
    });
    $("#platform").change(function(){
        listSongNotMapping( $("#platform").val(),$("#campaign_type").val());
    });
    function listSongNotMapping(platform,type){
        $.ajax({
            type: "GET",
            url: "/listSongNotMapping",
            data: {platform:platform,campaign_type:type},
            dataType: 'json',
            success: function(data) {
                console.log(data);
                var html = '';
                $.each(data, function(key, value) {
                    if(key==0){
                        $("#full_song_name").val(`${value.artist} - ${value.song_name}`);
                    }
                    html+=`<option value="${value.id}">${value.artist} - ${value.song_name}</option>`;
                });
                $('#song').find('option').remove().end().append(html);
                $('.search_select').selectpicker('destroy');
                $('.search_select').selectpicker('render');
                   
            },
            error: function(data) {

            }
        });
    }
    listSongNotMapping( $("#platform").val(),$("#campaign_type").val());
    $("#campaign_type").change(function(){
        var type = $(this).val();
        var platform = $("#platform").val();
        listSongNotMapping(platform,type);

    });
    
    $("#song").change(function(){
        var name= $("#song option:selected").text();
        $("#full_song_name").val(name);

    });
    $(".btn-save-social-asset").click(function(){

        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($this.html() !== loadingText) {
            $this.data('original-text', $this.html());
            $this.html(loadingText);
        }
        var formData = $("#formSocialMusicAsset").serialize();
        $.ajax({
            type: "POST",
            url: "/saveSocialMappingSong",
            data: formData,
            dataType: 'json',
            success: function(data) {
                logger('saveSocialMappingSong',data)
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                if(data.status=='success'){
                $(`#song option[value='${data.data.id}']`).remove();
                $('.search_select').selectpicker('destroy');
                $('.search_select').selectpicker('render');
                $("#full_song_name").val($("#song option:selected").text());
                }
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });

    });
    
    
    //2023/06/02 import báo cáo claim
    $(".import_report_usd_dialog_open").click(function(){
        $("#import-report-check-result").html("");
        $("#report_data").text("");
        $(".modal-dialog").addClass("modal-80");
        $('#dialog_import_campaign_rev').modal({
            backdrop: false
        });
    });
    
    $("#import_distributor").change(function(){
       var val = $(this).val();
       if(val=='Tunecore_Heddo' || val=='Indiy'|| val=='Orchard_Indiy'|| val=='51st_State' || val=='AdRev' || val=='Orchard' || val=='Cygnus'){
           $(".div_import_file").removeClass('disp-none');
           $(".div_import_text").addClass('disp-none');
           if(val=='Indiy' || val=='Orchard_Indiy'|| val=='51st_State'){
               //Indiy,AdRev ko cần nhập period,rate
               $(".div_import_file_rate").removeClass('disp-none');
//               $(".div_import_file_period").addClass('disp-none');
              
               var rate=  localStorage.getItem("rate_indiy");
               if(rate!=null){
                     $("#report_rate").val(rate);
               }else{
                    $("#report_rate").val("202305=0.6535526048\n202306=0.6373582896\n202307=0.6406990325");
               }

           }else if(val=='Cygnus'){
               //Cygnus cần nhập rate,period
               $(".div_import_file_rate").removeClass('disp-none');
               $(".div_import_file_period").removeClass('disp-none');
               $("#report_rate").val("1.2241");
           }else{
               //Tunecore_Heddo,Orchard ko cần nhập rate, cần nhập period
               $(".div_import_file_rate").addClass('disp-none');
               $(".div_import_file_period").removeClass('disp-none');
           }
       }else{
           $(".div_import_file").addClass('disp-none');
           $(".div_import_text").removeClass('disp-none');
           $(".div_import_file_rate").addClass('disp-none');
           $(".div_import_file_period").removeClass('disp-none');
       }
    });
    
    $(".btn-check-report").click(function(){
        $("#import-report-check-result").html("");
        $("#report_campaign_rev_loading").show();
//        var input = $("#form-import-report").serialize();
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        var $this = $(this);
        if ($this.html() !== loadingText) {
            $this.data('original-text', $this.html());
            $this.html(loadingText);
        }
        //save rate
        if($("#import_distributor").val()=='Indiy'){
            localStorage.setItem("rate_indiy",$("#report_rate").val());
        }
        
        var input = new FormData();
        input.append('report_file', $("#report_file")[0].files[0]);
        input.append('_token', '{{csrf_token()}}');
        input.append('distributor', $("#import_distributor").val());
        input.append('period', $("#import_period").val());
        input.append('report', $("#report_data").val());
        input.append('report_rate', $("#report_rate").val());
        input.append('owner', $("#owner").val());
        $.ajax({
            type: "POST",
            url: "/checkImportReport",
            data: input,
//            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(data) {
                 $this.html($this.data('original-text'));
                console.log(data);
                if(data.status=="success"){
                var html = `Success ${data.check}<div class="row">`;
                html += '<div class="col-md-12">';
                html += `<table class="table text-center" style="border-collapse: inherit"><thead><tr>
                            <th class='text-center'>#</th>
                            <th class='text-center'>Period</th>
                            <th class='text-center'>Claim</th>
                            <th class='text-left'>Artist</th>
                            <th class='text-left'>Song Name</th>
                            <th class='text-center'>Isrc</th>
                            <th class='text-center'>Revenue</th>
                            <th class='text-center'>Status</th>`;
                var i = 0;
                $.each(data.results, function(key, value) {
                    i = i + 1;
                    var evenOdd = "even";
                    if(i % 2==0){
                        evenOdd = "odd";
                    }
                    html += `<tr class="${evenOdd}"><td scope="row">${i}</td>
                            <td><b>${value.period_text}</b></td>
                            <td>${value.campaign_id}</td>
                            <td class='text-left'>${value.artist}</td>
                            <td class='text-left'>${value.song_name}</td>
                            <td class='text-center'>${value.isrc}</td>
                            <td>$${ number_format(value.revenue, 0, '.', ',')}</td>
                            <td><span class="${value.color}">${value.status}</span></td></tr>`;
                });

                html += '</table></div></div>';
                
                $("#import-report-check-result").html(html);
                $("#report_campaign_rev_loading").hide();
            }else{
                $.Notification.notify('error', 'top center', 'Notification', data.message);
            }
//                $("#dialog_report_campaign_rev_title").text("Report Revenue");

            },
            error: function(data) {
                 $this.html($this.data('original-text'));
                console.log(data);
            }
        });
    });
    
    //2023/05/26 hàm lấy dữ liệu views thang distributor,user
    var cur = moment().format("YYYYMM");
    getMonthlyClaimViews(cur);

    $(".select-period-users").change(function(){
        getMonthlyClaimViews($(this).val());
    });

    function getMonthlyClaimViews(period){
        //show loading
        var $portlet = $(".portlet");
        $portlet.append('<div class="panel-disabled"><div class="loader-1"></div></div>');
        var $pd = $portlet.find('.panel-disabled');
        $.ajax({
            type: "GET",
            url: "/claimMontlyDistributor",
            data: {
                'period': period
            },
            dataType: 'json',
            success: function(data) {
                $pd.remove();
                console.log(data);
                var html = '';                
                var html2 = '';    
                $.each(data, function(key, value) {
               
                html2 += `  <div class="col-md-4">
                                <div class="portlet">
                                    <div class="portlet-heading portlet-default">
                                        <h3 class="portlet-title">
                                            ${value.month}
                                        </h3>
                                        <div class="portlet-widgets">
                                            <a href="javascript:;" data-toggle="reload"><i class="ion-refresh reload-user"></i></a>
                                            <span class="divider"></span>
                                            <a data-toggle="collapse" data-parent="#accordion1" href="#portlet-1"><i class="ion-minus-round"></i></a>
                                            <span class="divider"></span>
                                            <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div id="portlet-1" class="panel-collapse collapse show">
                                        <div class="portlet-body ">`;
                html2 += genMonthlyViewProgress(value.user,value.user_views_sum);
                html2+=`                </div>
                                    </div>
                                </div>
                            </div>`;
                html += `  <div class="col-md-4">
                                <div class="portlet">
                                    <div class="portlet-heading portlet-default">
                                        <h3 class="portlet-title">
                                            ${value.month} - revenue: <b>$${number_format(value.revenue_sum, 0, ',', '.')}</b> - net: <b>$${number_format(value.profit_sum, 0, ',', '.')}</b> - bass: <b>$${number_format(value.bass_sum, 0, ',', '.')}</b>
                                        </h3>
                                        <div class="portlet-widgets">
                                            <a href="javascript:;" data-toggle="reload"><i class="ion-refresh reload-user"></i></a>
                                            <span class="divider"></span>
                                            <a data-toggle="collapse" data-parent="#accordion1" href="#portlet-1"><i class="ion-minus-round"></i></a>
                                            <span class="divider"></span>
                                            <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div id="portlet-1" class="panel-collapse collapse show">
                                        <div class="portlet-body ">`;
                html += genMonthlyViewProgress(value.distributor,value.distributor_views_sum,true);
                html+=`                </div>
                                    </div>
                                </div>
                            </div>`;
                });

                $("#content-monthly-users").html(html2);
                $("#content-monthly-distributor").html(html);

                //hiệu ứng chuyển động
                $('.progress-montly .progress-bar').css("width",function() {
                    return $(this).attr("aria-valuenow") + "%";
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
    
    function genMonthlyViewProgress(datas,views_total_sum,isDistributor=false){
        var html = "";
        $.each(datas, function(key, data) {
            html+=`<div class="w_left w_25">`;
            if(isDistributor){
                html+=`<span>${data.object} - ${number_format(data.videos, 0, ',', '.')} videos @if($is_supper_admin) - revenue: <b>$${number_format(data.revenue, 0, ',', '.')}</b> - net: <b>$${number_format(data.profit, 0, ',', '.')}</b> - bass: <b>$${number_format(data.bass, 0, ',', '.')}</b> @endif</span>`;
            }else{
                html+=`<span>${data.object} - ${number_format(data.videos, 0, ',', '.')} videos</span>`;
            }
            html+=`</div>
            <div class="w_center w_55">
                <div class="progress-montly progress progress-custom progress-lg m-b-20 " style="white-space: nowrap;">
                    <div class="color-cus progress-bar bg-success progress-lg" role="progressbar" aria-valuenow="${views_total_sum==0?0:data.views_total/views_total_sum * 100}" aria-valuemin="0" aria-valuemax="100" style="width: 1%;">
                        <span class="m-l-5">${number_format(data.views_money, 0, ',', '.')} / ${number_format(data.views_total, 0, ',', '.')} views</span>
                    </div>
                </div>
            </div>`;
        });
   return html;
    }
    //2023/02/13 report claim
    $(".report_usd_dialog_open").click(function(e) {
        e.preventDefault();
        getReportRev();

    });

    function changeStatusPay(period){
        $.ajax({
            type: "GET",
            url: "/changeStatusPay",
            data: {
                'period': period,'rev_type':2
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if(data.status=='error'){
                    $.Notification.notify('error', 'top center', 'Notification', data.message);
                }

            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    $(".btn-get-channelid").click(function() {
        var handle = $("#handle").val();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        $.ajax({
            type: "GET",
            url: "/getChanelByHandle",
            data: {
                'handle': handle
            },
            headers: {
                "platform": "AutoMusic"
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $this.html($this.data('original-text'));
                if (data != 0) {
                    navigator.clipboard.writeText(data.channelId);
                    $.Notification.notify('success', 'top center', 'Notification', 'Copied ' + data.channelId + ' to cliploard');
                } else {
                    $.Notification.notify('success', 'top center', 'Notification', 'Error');
                }

            },
            error: function(data) {
                console.log(data);
            }
        });
    });
    
    $(".btn-download-dz").click(function() {
        //window.location.href = "http://65.21.242.170:5003/render/"+$("dzid").val();
        var text = "http://65.21.242.170:5003/render/" + $("#dzid").val();
        navigator.clipboard.writeText(text);
        $.Notification.notify('success', 'top center', 'Notification', 'Copied link to cliploard ');
        //window.open("http://65.21.242.170:5003/render/"+$("#dzid").val(), '_blank');
    });
    
    $('#tbl-campaign').DataTable({
        searching: true,
        ordering: false,
        processing: false,
        select: true,
        responsive: false,
        paging: true,
        lengthMenu: [
            [50,100,200,500,1000,2000],
            [50,100,200,500,1000,2000]
        ],
        searchPanes: {
            //            viewTotal: true,
            viewCount: true,
            //            controls: false,
            collapse: true,
            //            initCollapsed: true,
            @if($is_admin_music)
            columns: [3, 4, 17, 16, 13,14,15,11]
            @else
            columns: [3, 4, 17, 16, 13,14,15,11]
            @endif
        },
        dom: 'Plfrtip',
        columnDefs: [{
                "targets": @if($is_admin_music)[3, 17,16, 13,14,15,11] @else[3, 17,16, 13,14,15,11] @endif,
                "visible": false,
                "searchable": true
            },
            //            {
            //                "targets": [6, 7, 8, 9],
            //                "visible": true,
            //                "searchable": false
            //            },
            {
                searchPanes: {
                    show: true,
                    header: 'User'
                },
                targets: [3]
            },
            {
                searchPanes: {
                    header: 'Genre'
                },
                targets: [4]
            },
            @if($is_admin_music) 
            {
                searchPanes: {
                    show: true,
                    header: 'Distributor'
                },
                targets: [17]
            },
            @endif
            {
                searchPanes: {
                    header: 'Status',
                    options: [{
                            label: 'Active',
                            value: function(rowData, rowIdx) {
                                return rowData[14]==1;
                            }
                        },
                        {
                            label: 'Pause',
                            value: function(rowData, rowIdx) {
                                return rowData[14] == 4;
                            }
                        }
                    ]
                },
                targets: [14]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'AssetId',
                    options: [{
                            label: 'Exists',
                            value: function(rowData, rowIdx) {
                                return rowData[15]!= "";
                            }
                        },
                        {
                            label: 'Not Exists',
                            value: function(rowData, rowIdx) {
                                return rowData[15] == "";
                            }
                        }
                    ]
                },
                targets: [15]
            },
            {
                searchPanes: {
                    header: 'YT Distributor'
                },
                targets: [16]
            }

        ]

    });
    
    $("div.toolbar").html('<b>Custom tool bar! Text/images etc.</b>');

    function copyText(id) {
        navigator.clipboard.writeText($("#" + id).val());
        $.Notification.notify('success', 'top center', 'Notification', 'Copied: ' + $("#" + id).val());
    }

    var curr_id = 0;
    var options = {};
    options.autoApply = true;
    options.autoUpdateInput = false;
    options.startDate = moment().subtract(29, 'days');
    options.endDate = moment();
    $("#startDate").val(options.startDate.format('YYYYMMDD'));
    $("#endDate").val(options.endDate.format('YYYYMMDD'));
    options.ranges = {
        //            'Today': [moment(), moment()],
        //            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        'Life Time': [moment().subtract(3, 'years'), moment()]
    };

    $('#date_rage_picker').daterangepicker(options, function(start, end, label) {
        if (label === "Custom Range") {
            $("#date_rage_picker").val(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        } else {
            $("#date_rage_picker").val(label);
        }
        $("#startDate").val(start.format('YYYYMMDD'));
        $("#endDate").val(end.format('YYYYMMDD'));
        if ($("#dialogType").val() == "reportClaim") {
            getReportUser(curr_id, 0);
        } else {
            chartsCampaign(curr_id, 0);
        }

    });
    $("#startDateMain").val(options.startDate.format('YYYYMMDD'));
    $("#endDateMain").val(options.endDate.format('YYYYMMDD'));
    $('#date_rage_picker_main').daterangepicker(options, function(start, end, label) {
        if (label === "Custom Range") {
            $("#date_rage_picker_main").val(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        } else {
            $("#date_rage_picker_main").val(label);
        }
        $("#startDateMain").val(start.format('YYYYMMDD'));
        $("#endDateMain").val(end.format('YYYYMMDD'));
        if ($("#dialogType").val() == "reportClaim") {
            getReportUser(curr_id, 0);
        } else {
            chartsCampaign(curr_id, 0);
        }

    });


    function getReportUser(id, show = 1) {
        curr_id = id;
        $("#content-view-dialog").html("");
        $("#dialog-view-loading").show();
        $("#dialogType").val("reportClaim");
        $.ajax({
            type: "GET",
            url: "/reportCampaignUser",
            data: {
                'id': id,
                'start': $("#startDate").val(),
                'end': $("#endDate").val()
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                var html = '';
                var html = '<div class="row">';
                html += '<div class="col-md-12">';
                html +=
                    '<table class="table" style="border-collapse: inherit"><thead><tr><th>#</th><th>User</th><th>Type</th><th>Videos</th><th>Bulk Views</th><th>Real View</th><th>Total</th>';
                var i = 0;
                $.each(data.report, function(key, value) {
                    i = i + 1;
                    html += '<tr><td scope="row">' + i + '</td><td>' + value.username +
                        '</td><td>' + value.video_type_text + '</td><td>' + value.videos +
                        '</td><td>' + number_format(value.bulk_views, 0, ',', '.') + '</td><td>' + number_format(value.real_views, 0, ',', '.') + '</td><td>' + number_format(value.views, 0, ',', '.') + '</td></tr>';
                });
                html += '</table></div>';
                html += '</table></div></div>';
                $("#content-view-dialog").html(html);
                $("#dialog-view-loading").hide();
                $("#dialog-view-tile").text(data.name);
            },
            error: function(data) {
                console.log(data);
            }
        });
        if (show == 1) {
            $(".modal-dialog").addClass("modal-80");
            $('#dialog_view').modal({
                backdrop: false
            });
        }
    }

    $(".report-user").click(function(e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        getReportUser(id, 1);

    });

    function videoSync() {
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
                data: "id=" + id,
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
    }
    function scanCampaignAll($this){
        var $this = $($this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($this.html() !== loadingText) {
            $this.data('original-text', $this.html());
            $this.html(loadingText);
        }
                var id = $this.attr("data-id");
        $.ajax({
            type: "GET",
            url: "/scanViewPromoCampaign",
            data: "id=" + id,
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
    }
    $(".scan-campain-all").click(function(e) {
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
            url: "/scanViewPromoCampaign",
            data: "id=" + id,
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


    var listChartId = [];
    var charts;
    <?php
    foreach ($datas as $index => $data) {
        if ($index < 3) {
            echo "listChartId.push($data->id);";
            echo " $('#ck-chart$data->id').prop('checked', true);";
        } else {
            break;
        }
    }
    ?>

//    drawChart();
    $(".btn-range-chart").click(function(e) {
        e.preventDefault();
        $("#range_chart").val($(this).val());
        drawChart();
    })

    $(".select-video-type").change(function() {
        drawChart();
    });
    $(".chk-view-chart").change(function() {
        var id = $(this).val();
        id = parseInt(id);
        if (this.checked) {
            listChartId.push(id);
        } else {
            const index = listChartId.indexOf(id);
            if (index > -1) {
                listChartId.splice(index, 1);
            }
        }
        drawChart();
    });

    function drawChart(id = 0) {
        //        console.log(listChartId);
        //    var colors = ['#3f33f1', '#ef00ff', '#49eabe', '#c8ea0a', '#ea710a', '#eaf909', '#f336ca', '#7a00ff','#2bb4e8'];
        var colors = ['#0071d1', '#63b300', '#e6990e', '#912fc0', '#d94d6c', '#0f8071', '#f270b9', '#f2c428', '#2fa5cb',
            '#5b54d5', '#00b4a2', '#eb3c97'
        ];
        var video_type = $(".select-video-type").val();
        var range_chart = $("#range_chart").val();
        $.ajax({
            type: "GET",
            url: "getCampaignChart",
            data: {
                'ids': listChartId,
                "video_type": video_type,
                "range_chart": range_chart
            },
            dataType: 'json',
            success: function(data) {
                //                console.log(data);
                var html =
                    '<canvas id="chart-all"></canvas>';
                $("#control-chart").html(html);
                var datasets = new Array();
                var label = new Array();
                $.each(data.campaigns, function(key, campaign) {
                    var viewsData = new Array();
                    $.each(campaign.data, function(k, value) {
                        var temp = {
                            "x": value.date,
                            "y": value.views
                        };
                        viewsData.push(temp);
                    });
                    var dataset = {
                        label: campaign.campaign_name,
                        data: viewsData,
                        fill: false,
                        borderColor: colors[key],
                        backgroundColor: colors[key],
                        borderWidth: 1.5
                    };
                    datasets.push(dataset);
                });
                drawLineCharts('chart-all', data.dates, datasets);
            },
            error: function(data) {
                console.log(data);
            }
        });
    }


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
            data: form + "&crossType=2",
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
                if (data.status == 'success') {
                    mess = '<ol>';
                    $.each(data.content, function(key, value) {
                        //                            console.log(value);
                        mess += '<li>' + value.channel_name + ' &rarr; ' + (value.status == 1 ?
                            "success" : "fail") + '  </li>';
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

    $("#channel_genre_assign").change(function() {
        genreAssign = $(this).val();
        $.ajax({
            type: "GET",
            url: "loadSubgenre",
            data: {
                "channel_genre": $(this).val()
            },
            dataType: 'text',
            success: function(data) {
                $("#channel_subgenre_assign").html(data);
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
        subGenreAssign = [];
        getListChannel(camIdAssign, genreAssign, 0);

    });
    var camIdAssign;
    var genreAssign;
    var subGenreAssign;

    function getListChannel(id, genre, show = 1) {

        camIdAssign = id;
        $(".formAssignChannel_content").html("");
        $(".dialog_assign_channel_loadding").show();
        $("#channel_cam_id").val(id);
        $.ajax({
            type: "GET",
            url: "/getListChannelManualPromos",
            data: {
                "id": camIdAssign,
                "genre": genre,
                "subGenre": subGenreAssign
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $(".dialog_assign_channel_loadding").hide();
                var html = "";
                //                html += '<div class="col-md-12">';
                //                html += '<table class="table" style="border-collapse: inherit"><thead><tr><th style="width: 5%;text-align: center">#</th>';
                //                html += '<th style="width: 5%"></th><th>Promos</th><th>Genre</th>';
                //                var j = 0;
                //                $.each(data.promos, function(k, v) {
                //                    j = j + 1;
                //                    html += '<tr><td scope="row">' + j + '</td>';
                //                    html += '<td>';
                //                    html += '<div class="checkbox checkbox-primary">';
                //                    html += '<input class="checkbox-multi" type="checkbox" name="chkPromos[]" id="ck-promo' + v.id + '" value="' + v.id + '" ' + v.id + '>';
                //                    html += '<label class="m-b-18 p-l-0" for="ck-promo' + v.id + '"></label></div>';
                //                    html += '</td>';
                //                    html += '<td>' + v.campaign_name + '</td>';
                //                    html += '<td>' + v.genre + '</td></tr>';
                //                });
                //                html += '</table></div>';
                html += '<div class="col-md-12">';
                html += '<table class="table" style="border-collapse: inherit"><thead><tr><th style="width: 5%;text-align: center">#</th>';
                html += '<th style="width: 5%"><div class="checkbox checkbox-primary">';
                html += '<input id="select_all" type="checkbox" name="select_all">';
                html += '<label for="select_all" class="m-b-22 p-l-0" style="margin-bottom: 1rem"></label>';
                html += '</div></th><th>User</th><th>Channel</th><th>Genre</th>';
                var i = 0;
                $.each(data.channels, function(key, value) {
                    i = i + 1;
                    html += '<tr><td scope="row">' + i + '</td>';
                    html += '<td>';
                    html += '<div class="checkbox checkbox-primary">';
                    html +=
                        '<input class="checkbox-multi" type="checkbox" name="chkChannelAll[]" id="ck-video' +
                        value.id + '" value="' + value.id + '" ' + value.checked + '>';
                    html += '<label class="m-b-18 p-l-0" for="ck-video' + value.id + '"></label></div>';
                    html += '</td>';
                    html += '<td>' + value.user + '</td>';
                    html += '<td>' + value.chanel_name + '</td>';
                    html += '<td>' + value.channel_genre + '</td></tr>';
                });
                html += '</table></div>';
                $(".formAssignChannel_content").html(html);
                $('#select_all').change(function() {
                    var checkboxes = $(this).closest('.table').find('.checkbox-multi');
                    checkboxes.prop('checked', $(this).is(':checked'));
                });
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
        if (show == 1) {
            $(".modal-dialog").removeClass("modal-80");
            $('#dialog_assign_channel').modal({
                backdrop: false
            });
        }
    }

    function assgignChannel(type) {
        var $this = $("#btn-assign-" + type);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($this.html() !== loadingText) {
            $this.data('original-text', $this.html());
            $this.html(loadingText);
        }
        var formData = $("#formAssignChannel").serialize();
        $.ajax({
            type: "POST",
            url: "/channelAssign",
            data: formData + "&type=" + type,
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

    $(".btn-submit-upload").click(function(e) {
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
        form_file.append('fd', 'claims');
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/promo-upload",
            data: form_file,
            contentType: false,
            processData: false,
            success: function(data) {
                $this.html($this.data('original-text'));
                $.Notification.notify('success', 'top center', '', data.message);
                var url = "http://automusic.win/" + data.uploaded_promo;
                $("#" + type + "_url").val(url);
            },
            error: function(data) {
                $this.html($this.data('original-text'));
                //                but.button('reset');
            }
        });
    });

    function nextChartsCampaign(data) {
        var nextId = $("#" + data + "-campaign-id").val();
        chartsCampaign(nextId, 0);
    }

    function chartsCampaign(id, show = 1) {
        $("#content-dialog").html("");
        $("#dialog-loading").show();
        var listIdStr = $("#list-campaign-id").val();
        var listId = JSON.parse(listIdStr);
        //        console.log(listId);
        var total = listId.length;
        curr_id = id;
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
            }
        });
        $.ajax({
            type: "GET",
            url: '/getCampaignChart',
            data: {
                'ids': [curr_id],
                'video_type': 'all',
                'start': $("#startDate").val(),
                'end': $("#endDate").val()
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                var html =
                    '<div class="row"><div class="col-md-12"><div class="control-chart"><i onclick="nextChartsCampaign(\'prev\')" class="ion-chevron-left float-left cur-poiter"></i><i onclick="nextChartsCampaign(\'next\')" class="ion-chevron-right float-right cur-poiter m-r-10"></i></div><canvas id="chart-video-daily"></canvas></div></div>';
                //            $("#content-dialog").html(html);
                //            $("#dialog-loading").hide();
                $("#content-view-dialog").html(html);
                $("#dialog-view-loading").hide();
                var dataChart = data.campaigns[0].data;
                var label = new Array();
                //                var dataDailyCampaign = new Array();
                var dataDaily = new Array();

                var datasets = new Array();
                var total = 0;
                $.each(dataChart, function(key, value) {
                    label.push(value.date);
                    dataDaily.push(value.views);
                    total = total + parseInt(value.views);
                });
                var dataset1 = {
                    label: 'Daily Views',
                    data: dataDaily,
                    fill: false,
                    backgroundColor: 'rgb(25, 165, 253)',
                    borderColor: 'rgb(25, 165, 253)',
                    borderWidth: 1
                };
                datasets.push(dataset1);

                drawLineCharts('chart-video-daily', label, datasets);
                $("#dialog-view-tile").text(data.campaigns[0].campaign_name + " (" + number_format(total, 0, ',', '.') + ")");
            },
            error: function(data) {
                console.log(data);
            }
        });
        if (show == 1) {
            $(".modal-dialog").addClass("modal-80");
            $('#dialog_view').modal({
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
        $("#dialog_import_campaign_title").html("Add Claim");
        $(".modal-dialog").addClass("modal-80");
        $('#dialog_import_campaign').modal({
            backdrop: false
        });
    });

    $(".btn-import-artist").click(function(e) {
        e.preventDefault();
        $('#dialog_labelgrid_artist').modal({
            backdrop: false
        });
    });
    $(".btn-submit-artist").click(function(e) {
        e.preventDefault();
        var form = $("#form_artist");
        var formData = form.serialize();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        $this.attr("disabled", true);
        $.ajax({
            type: "POST",
            url: "/labelgridAddArtist",
            data: formData,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $this.attr("disabled", false);
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
            },
            error: function(data) {
                $this.html($this.data('original-text'));
                console.log('Error:', data);
            }
        });
    });

    $(".btn-import-releases").click(function(e) {
        e.preventDefault();
        $('#dialog_labelgrid_releases').modal({
            backdrop: false
        });
    });
    $(".btn-submit-release").click(function(e) {
        e.preventDefault();
        var form = $("#form_release");
        var formData = form.serialize();
        //        console.log(formData);

        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        $this.attr("disabled", true);
        $.ajax({
            type: "POST",
            url: "/labelgridAddRelease",
            data: formData,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $this.attr("disabled", false);
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
            },
            error: function(data) {
                $this.html($this.data('original-text'));
                console.log('Error:', data);
            }
        });
    });
    $(".btn-import-track").click(function(e) {
        e.preventDefault();
        $('#dialog_labelgrid_track').modal({
            backdrop: false
        });
    });
    $(".btn-submit-track").click(function(e) {
        e.preventDefault();
        var form = $("#form_track");
        var formData = form.serialize();
        //        console.log(formData);

        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        //        $this.attr("disabled", true);
        $.ajax({
            type: "POST",
            url: "/labelgridAddTrack",
            data: formData,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $this.attr("disabled", false);
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
            },
            error: function(data) {
                $this.html($this.data('original-text'));
                console.log('Error:', data);
            }
        });
    });
    onchage();

    function addContributors() {
        totalContri = totalContri + 1;
        var html =
            '<tr><td class="col-md-4"><div class="form-group row"><label class="col-12 col-form-label">Party Name</label>';
        html +=
            '<div class="col-12"><input curr_contri="' + totalContri + '" type="text" class="form-control party_name text-left"></div></div></td>';
        html +=
            '<td class="col-md-3"><div class="form-group row"><label class="col-8 col-form-label">Roles</label><div class="col-12">';
        html +=
            '<select curr_contri="' + totalContri + '" class="select2_multiple form-control roles" multiple="" style="height: 56px;overflow: hidden">';
        html +=
            '<option value="Composer">Composer</option><option value="Lyricst">Lyricst</option></select></div></div></td><input type="hidden" name="contri[]" class="contri' + totalContri + '"/></tr>';
        $(".table-contri").append(html);
        $("#total_contri").val(totalContri);
        onchage();
    }
    var contributors = [];
    var totalContri = 1;

    function onchage() {
        $(".party_name").unbind();
        $(".party_name").change(function() {
            console.log($(this).val());
            var partyName = $(this).val();
            var curr_contri = $(this).attr("curr_contri");
            var roles = $(this).closest("tr").find(".roles").val();
            console.log(roles);
            var json = {
                "party_name": partyName,
                "indirect_roles": roles
            };
            //        $(this).closest("tr").find(".contri").val(JSON.stringify(json));
            $(this).closest("tr").find(".contri" + curr_contri).val(JSON.stringify(json));
        });
        $(".roles").unbind();
        $(".roles").change(function() {

            var curr_contri = $(this).attr("curr_contri");
            console.log($(this).val());
            var partyName = $(this).closest("tr").find(".party_name").val();
            var roles = $(this).val();
            console.log(partyName);
            var json = {
                "party_name": partyName,
                "indirect_roles": roles
            };
            $(this).closest("tr").find(".contri" + curr_contri).val(JSON.stringify(json));
        });
    }

    $(".btn-release-image").click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var releaseId = $this.attr("data-id");
        $("#release_id_image").val(releaseId);
        $('#dialog_labelgrid_releases_image').modal({
            backdrop: false
        });
    });
    $("#release_image_upload").change(function(e) {
        e.preventDefault();
        var input = $(this);
        if (input[0].files && input[0].files[0]) {
            load(input, function(e) {
                var size = parseFloat(input[0].files[0].size / 1024).toFixed(2);
                if (size > 2048) {
                    $.Notification.notify('error', 'top center', '', "File size > 2MB");
                    return;
                }
                var form_file = new FormData();
                form_file.append('image', input[0].files[0]);
                form_file.append('_token', '{{csrf_token()}}');
                form_file.append('width', input.attr("data-width"));
                form_file.append('height', input.attr("data-height"));

                var form = $("#form_release_image").serialize();
                $(".image_spinner").show();
                $.ajax({
                    type: "POST",
                    url: "/labelgrid-image-upload",
                    data: form_file,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        //                    console.log(data);
                        $(".image_spinner").hide();
                        if (data.status == 'error') {
                            $.Notification.notify(data.status, 'top center', '', data.message);
                        } else {
                            $("#release_image_3000").val(data.uploaded_image);
                            $('.img-thumbnail').attr('src', e.target.result).fadeIn('slow');
                        }
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

            });
        }
    });
    $("#btn_release_upload_image").click(function(e) {
        e.preventDefault();
        var form = $("#form_release_image");
        var formData = form.serialize();
        //        console.log(formData);

        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        //    $this.attr("disabled", true);
        $.ajax({
            type: "POST",
            url: "/labelgridReleaseImage",
            data: formData,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $this.attr("disabled", false);
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
            },
            error: function(data) {
                $this.html($this.data('original-text'));
                console.log('Error:', data);
            }
        });
    });

    function load(input, callback) {
        var reader = new FileReader();
        reader.onload = callback;
        reader.readAsDataURL(input[0].files[0]);
    }

    $("#official_video").change(function() {
        $(".official-sync").show();
        var link = $(this).val();
        $.ajax({
            type: "GET",
            url: "/videoinfo",
            data: {
                'link': link
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $(".official-sync").hide();
                $("#start_view_official").val(data.view);
                $("#start_like_official").val(data.like);
                $("#start_cmt_official").val(data.comment);
                $("#start_sub_official").val(data.channel_sub);

            },
            error: function(data) {
                console.log(data);
            }
        });
    });

    editCampaign = function(id,dialog=1) {
        clearForm();
        $("#dialog-loading-claim").show();
        $.ajax({
            type: "GET",
            url: "/getcampaignstatistics",
            data: {
                "id": id
            },
            dataType: 'json',
            success: function(data) {
                $("#dialog-loading-claim").hide();
                $("#btn-reload-claim").attr("onclick",`editCampaign(${id},0)`);
                $(".btn-check-claim").attr("onclick",`checkclaim(${id})`);
                console.log(data);
                if(data.short_text!=null){
                    var shortText = JSON.parse(data.short_text);
                    $("#yt_artist").val(JSON.stringify(shortText.artists));
                    $("#yt_songname").val(shortText.title);
                    $("#yt_distributor").val(shortText.distributor);
                    if(shortText.video_id!=null && shortText.video_id!=""){
                        $(".btn-video-remove").html(`<i class="fa fa-refresh color-red" onclick="checkclaim(${id},1)"></i>`);
                        $(".btn-video-checked").html(`<a href="https://www.youtube.com/watch?v=${shortText.video_id}" target="_blank"><i class="fa fa-share"></i></a>`);
                    }

                }
                $("#asset_id").val(data.asset_id);
                $("#campaign_name").val(data.campaign_name);
                $("#campaign_start_date").val(data.start_date_edit);
                $("#genre").val(data.genre).change();
                $("#status").val(data.status).change();
                $("#username").val(data.username).change();
                $("#custom_genre").val(data.custom_genre).change();
                $("#artist").val(data.artist);
                $("#songname").val(data.song_name);
                $("#distributor").val(data.distributor);
                $("#official_video").val(data.official_video);
                $("#artists_social").val(data.artists_social);
                $("#bitly_url").val(data.bitly_url);
                $("#audio_url").val(data.audio_url);
                $("#lyrics").val(data.lyrics);
                $("#deezer_artist_id").val(data.deezer_artist_id);
                $("#deezer_id").val(data.deezer_id);
                $("#isrc").val(data.isrc);
                $("#upc").val(data.upc);
                $("#promo_keywords").val(data.promo_keywords);
                $("#spotify_id").val(data.spotify_id);
                $("#cam_id").val(id);
                $("#artist_percent").val(data.artist_percent);
                $("#bass_percent").val(data.bass_percent);
                $("#tax_percent").val(data.tax_percent);
                $("#tier").val(data.tier).change();
                if (data.audio_url != null) {
                    $("#link_audio").html("<a href='" + data.audio_url + "' target='_blank'><i class='fa fa-music'></i></a>")
                }
                $("#target_total").val(data.target);
                var official = JSON.parse(data.official);
                if (official != null) {
                    $("#start_view_official").val(official.start_view);
                    $("#start_like_official").val(official.start_like);
                    $("#start_cmt_official").val(official.start_cmt);
                    $("#start_sub_official").val(official.start_sub);
                    $("#target_view").val(official.target_view);
                    $("#target_like").val(official.target_like);
                    $("#target_cmt").val(official.target_cmt);
                    $("#target_sub").val(official.target_sub);
                    $("#crypto_usd").val(official.crypto_usd);
                    $("#crypto_view").val(official.crypto_view);
                    $("#crypto_like").val(official.crypto_like);
                    $("#crypto_sub").val(official.crypto_sub);
                    $("#crypto_cmt").val(official.crypto_cmt);

                    $("#crypto_usd_last").val(official.crypto_usd_last);
                    $("#crypto_view_last").val(official.crypto_view_last);
                    $("#crypto_like_last").val(official.crypto_like_last);
                    $("#crypto_sub_last").val(official.crypto_sub_last);
                    $("#crypto_cmt_last").val(official.crypto_cmt_last);

                    $("#crypto_cmt_link").val(official.crypto_cmt_link);
                    $("#crypto_cmt_content").val(official.crypto_cmt_content);
                    $("#crypto_cmt_content_finish").val(official.crypto_cmt_content_finish);
                    $("#cmt_schedule").val(official.cmt_schedule);
                    $("#cmt_number").val(official.cmt_number);
                    $("#adsense_usd").val(official.adsense_usd);
                    $("#adsense_view").val(official.adsense_view);

                    $("#crypto_view_run").val(official.crypto_view_run).change();
                    $("#crypto_like_run").val(official.crypto_like_run).change();
                    $("#crypto_sub_run").val(official.crypto_sub_run).change();
                    $("#crypto_cmt_run").val(official.crypto_cmt_run).change();
                    $("#adsense_view_run").val(official.adsense_view_run).change();
                    $("#facebook_usd").val(official.facebook_usd);
                    $("#cmt_count").html(`(${data.number_comment})`);
                    $("#cmt_count_finish").html(`(${data.number_comment_finish})`);
                }

            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
        $("#dialog_import_campaign_title").html("Edit Claim " + id);
        if(dialog){
        $(".modal-dialog").addClass("modal-80");
        $('#dialog_import_campaign').modal({
            backdrop: false
        });
        }
    }

    function clearForm() {
       
        $("#campaign_name").val("");
        $("#campaign_start_date").val(moment().format('YYYY-MM-DD'));
        $("#campaign_start_time").val('00:00:00');
        $("#artists_channel").val("");
        $("#genre").val("-1");
        $("#status").val("1");
        $("#label").val("");
        $("#artist").val("");
        $("#songname").val("");
        $("#distributor").val("");
        $("#artists_channel").val("");
        $("#artists_playlist").val("");
        $("#artists_social").val("");
        $("#official_video").val("");
        $("#bitly_url").val("");
        $("#guest_artist_1").val("");
        $("#guest_artist_2").val("");
        $("#guest_artist_3").val("");
        $("#promo_keywords").val("");
        $("#audio_url").val("");
        $(".lyric_url").val("");
        $("#lyrics").val("");
        $("#deezer_artist_id").val("0");
        $("#deezer_id").val("0");
        $("#isrc").val("");
        $("#upc").val("");
        $("#promo_keywords").val("");
        $("#spotify_id").val("");
        $("#cam_id").val("");
        $("#artist_percent").val("0");
        $("#bass_percent").val("0");
        $("#tax_percent").val("0");
        $("#link_audio").html("");
        $("#tier").val("2");

        $("#official_video").val("");
        $("#start_view_official").val(0);
        $("#start_like_official").val(0);
        $("#start_cmt_official").val(0);
        $("#start_sub_official").val(0);
        $("#target_view").val("");
        $("#target_like").val(0);
        $("#target_cmt").val(0);
        $("#target_sub").val(0);
        $("#crypto_usd").val(0);
        $("#crypto_usd_last").val(0);
        $("#crypto_view").val(0);
        $("#crypto_view_last").val(0);
        $('#crypto_view_run').prop('checked', false);
        $("#crypto_like").val(0);
        $("#crypto_like_last").val(0);
        $('#crypto_like_run').prop('checked', false);
        $("#crypto_subs").val(0);
        $("#crypto_subs_last").val(0);
        $('#crypto_subs_run').prop('checked', false);
        $("#crypto_cmt").val(0);
        $("#crypto_cmt_last").val(0);
        $('#crypto_cmt_run').prop('checked', false);
        $("#crypto_cmt_link").val("");
        $("#crypto_cmt_content").val("");
        $("#crypto_cmt_content_finish").val("");
        $("#cmt_schedule").val(1).change();
        $("#cmt_number").val(0);
        $("#adsense_usd").val(0);
        $("#adsense_view").val(0);
        $('#adsense_view_run').prop('checked', false);
        $("#facebook_usd").val(0);
        $("#money").val("0");
        $("#amount_paid").val("0");
        $("#asset_id").val("");
        $("#yt_artist").val("");
        $("#yt_songname").val("");
        $("#yt_distributor").val("");
        $(".btn-check-claim").html(`<i class="fa fa-refresh"></i>`);
        $(".btn-video-checked").empty();
        $(".btn-video-remove").empty();
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
    $(".btn-add-claim").click(function(e) {
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
        $this.attr("disabled", true);
        $.ajax({
            type: "POST",
            url: "/addClaim",
            data: formData,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $this.attr("disabled", false);
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.content);
            },
            error: function(data) {
                $this.html($this.data('original-text'));
                console.log('Error:', data);
            }
        });
    });

    $(".btn-upload-img").click(function(e) {
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
            $.Notification.notify('error', 'top center', '', "You must choose a image");
            return;
        }

        form_file.append('image', $("#" + type + "_upload")[0].files[0]);
        form_file.append('_token', '{{csrf_token()}}');
        form_file.append('width', $this.attr("data-width"));
        form_file.append('height', $this.attr("data-height"));
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/labelgrid-image-upload",
            data: form_file,
            contentType: false,
            processData: false,
            success: function(data) {
                $this.html($this.data('original-text'));
                console.log(data);
                if (data.status == 'success') {
                    //                    var url = "http://automusic.win/" + data.uploaded_image;  
                    $("#photo_tmp").val(data.uploaded_image);
                }
                $.Notification.notify(data.status, 'top center', '', data.message[0]);
            },
            error: function(data) {
                but.button('reset');
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
            url: "/addClaim",
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
        $("#detail-name").html('');
        $("#detail-list-video").html('');
        $("#loading-list-video").show();
        $.ajax({
            type: "GET",
            url: "/detailcampaign",
            data: {
                "id": id
            },
            dataType: 'json',
            success: function(data) {
                logger('detailCampaign',data);
                data = data.datas;
                $("#loading-list-video").hide();
                if (data.length > 0) {
                    $("#detail-name").html('Campaign Details: ' + data[0].campaign_name);
                    $("#detail-list-video").html('<i onclick="downloadListvideo(\'' + id +
                        '\')" data-toggle="tooltip" data-placement="top" data-original-title="Download list video" class="ti-download cur-poiter td-info color-h"></i>'
                    );
                    makeTableDetail(data);
                    videoSync();
                } else {
                    $("#detail").html("No Data");
                    $('#detail').slideDown("fast");
                }
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
            '<table class="table" style="border-collapse: inherit"><thead><tr><th>#</th><th>User</th><th>Channel</th><th>Type</th><th>Published</th><th>VideoId</th><th>Title</th>';
        html += '<th style="text-align: left">Views</th>';
        html += '<th style="text-align: left">Likes</th>';
        //    html += '<th style="text-align: left">Dislikes</th>';
        //    html += '<th style="text-align: left">Comments</th>';
//        html += '<th style="text-align: left">Cards</th>';
//        html += '<th style="text-align: left">EndScreens</th>';
        var i = 0;
        $.each(data, function(key, value) {
            i = i + 1;
            var type = '';
            if (value.video_type == 1) {
                type = 'Official';
            } else if (value.video_type == 2) {
                type = 'Lyric';
            } else if (value.video_type == 5) {
                type = 'Mix';
            } else if (value.video_type == 6) {
                type = 'Short';
            }
            var publish = getTIMESTAMP(value.publish_date);
            var dailyChart =
                html += '<tr><td scope="row">' + i + '</td><td>' + value.username + '</td><td>' + value.channel_name + '</td><td>' + type +
                '</td><td>' + publish + '</td><td>' + value.video_id + '</td>';
            html += '<td><a target="_blank" href="https://www.youtube.com/watch?v=' + value.video_id +
                '"><img class="video-img_thumb" src="https://i.ytimg.com/vi/' + value.video_id + '/default.jpg">' +
                value.video_title + '</a>&nbsp;<a taget="_blank" href="downloadVideoInfo?id=' + value.id +
                '"><i class="ti-download cur-poiter td-info color-h"></i></a>&nbsp;<a class="cur-poiter video-sync btn-success" data-id="' +
                value.id + '"><i class="ti-reload" ></i></a></td>';
            html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_views, value
                .per_daily_views, value.views, 'view') + '</td>';
            html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_like, value
                .per_daily_like, value.like, 'like') + '</td>';
            //        html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_dislike, value
            //            .per_daily_dislike, value.dislike, 'dislike') + '</td>';
            //        html += '<td style="text-align: left">' + caculatePercentDaily(value.id, value.daily_comment, value
            //            .per_daily_comment, value.comment, 'comment') + '</td>';
//            html += '<td style="text-align: left">' + value.card_clicks + '</td>';
//            html += '<td style="text-align: left">' + value.es_clicks + '</td>';
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
//                    drawBarChart("chart-video-daily", 'Daily ' + type, dataDaily, dataDaily);
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
//    });
</script>
@endsection