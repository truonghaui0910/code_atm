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
@if (Session::has('message'))
<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-danger">
            <ul>
                @foreach(Session::get('message') as $msg)
                <li>{!!$msg!!}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-sm-6 col-lg-4">
        <div class="widget-simple-chart text-right card-box">
            <div class="row">
                <div class="col-4"><i class=" mdi mdi-account-circle mdi-size-dashboard gradient-h"></i></div>
                <div class="col-6">
                    <h3 class="text-success counter m-t-10">
                        {{number_format($generalDataUser[0]->total_channel, 0, '.', ',')}}
                    </h3>
                    <p class="text-muted text-nowrap m-b-10">Total Channels <i id="daily_channel_total" class='fa fa-line-chart'></i></p>
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
                    <p class="text-muted text-nowrap m-b-10">Daily views <i id="daily_view_total" class='fa fa-line-chart'></i></p>
                </div>
            </div>
        </div>
    </div>
</div>
<input id="token" type="hidden" name='_token' value='{{csrf_token()}}' />


@if(!in_array('22',explode(",", $user_login->role)))
<div class="row">
    <div class="col-lg-12">
        <div class="portlet">
            <div id="promo-video-checker" class="portlet-heading portlet-default">
                <h3 class="portlet-title">
                    Promos videos checker ({{count($promosCheck)}})
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
                        @if($is_admin_music)
                        <div class="col-md-1">
                            <button class="btn-sm on-default remove-row btn btn-info m-r-5 confirm_promo" status="100" value="100"><i class="fa fa-check"></i> Confirm All</button>
                        </div>
                        <div class="col-md-1">
                            <button id="btn-confirm-filter" class="btn-sm on-default remove-row btn btn-info m-r-5" status="100" value="100" onclick="confirmAllFilter()"><i class="fa fa-check"></i> Confirm Filter</button>
                        </div>
                        @endif
                        <div class="col-md-1">
                            <button class="btn-sm on-default remove-row btn btn-warning m-r-5 video-sync" data-id="0"><i class="ti-reload"></i> Scan All</button>
                        </div>
                    </div>
                    <div style="overflow: auto">
                        <table id="tbl-promos-checker" class="table" style="width: 99%;table-layout:fixed;">
                            <thead class="thead-default">
                                <tr>
                                    <th style="text-align: center;width: 2%">Index</th>
                                    <th style="text-align: left;width: 5%">@sortablelink('username','Manager')</th>
                                    <th style="text-align: left;width: 10%">Channel</th>
                                    <th style="text-align: left;width: 15%">Campaign</th>
                                    <th style="text-align: left;width: 5%">Type</th>
                                    <th style="text-align: center;width: 2%">Claim</th>
                                    <th style="text-align: left;width: 20%">Video</th>
                                    <th style="text-align: center;width: 7%">Views</th>
                                    <th class="disp-none" style="text-align: center;width: 1%">Match Claim</th>
                                    <th class="disp-none" style="text-align: center;width: 1%">Log Claim</th>
                                    <th class="disp-none" style="text-align: center;width: 1%">Campaign Type</th>
                                    <th class="disp-none" style="text-align: center;width: 1%">Changed Title</th>
                                    <th class="disp-none" style="text-align: center;width: 1%">Boommix</th>
                                    <th class="disp-none" style="text-align: center;width: 1%">Distributor</th>
                                    <th style="text-align: center;width: 5%">Public</th>
                                    <th style="text-align: center;width: 12%">Function</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($promosCheck as $data)
                                <tr id="pro_check_{{$data->id}}">
                                    <td style="text-align: center">{{$data->id}}</td>
                                    <td>{{$data->username_short}}</td>
                                    <td>
                                        @if($data->channel_id!="")
                                        <a class="cur-poiter" onclick="copyText('https://www.youtube.com/channel/{{$data->channel_id}}')">{{$data->channel_name}}</a>
                                        <a target="_blank" href="/channelmanagement?c1={{$data->channel_id}}"><i class="fa fa-external-link"></i></a>
                                        @else
                                        <button class="cur-poiter video-sync btn-success" data-id="{{$data->id}}"><i class="ti-reload"></i></button>
                                        @endif
                                    </td>
                                    <td class="text-ellipsis" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$data->campaign_name}}">
                                        <span class="badge badge-success">{{$data->campaign_id}}</span> {{$data->campaign_name}}                                        
                                    </td>
                                    <td>
                                        @if($data->video_type==2)
                                        Lyric
                                        @elseif($data->video_type==5)
                                        Mix
                                        @else($data->video_type==6)
                                        Short
                                        @endif
                                        @if(\App\Common\Utils::containString($data->log,"auto submit"))
                                        <i class="mdi mdi-brightness-auto color-green cur-poiter" data-toggle="tooltip" data-placement="top" title="" data-original-title="This video is submited auto"></i>
                                        @endif
                                        @if($data->is_match_claim ==1)
                                        <i class="fa fa-copyright color-green cur-poiter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Match claim"></i>
                                        @endif
                                    </td>
                                    <td style="text-align: center" class="cur-poiter <?php echo ($data->number_claim == 0) ? 'color-red' : ""; ?>">
                                        <?php
                                        $claimLog = "Untested";
                                        if ($data->number_claim == 0) {
                                            $claimLog = $data->log_claim;
                                        } else if ($data->number_claim > 0) {
                                            $claimLog = "$data->number_claim claim on this video";
                                        }
                                        ?>
                                        <span data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$claimLog}}">{{$data->number_claim}}</span>
                                    </td>
                                    <td class="text-ellipsis">
                                        <span class="cur-poiter" onclick="copyText('https://www.youtube.com/watch?v={{$data->video_id}}')" >
                                            <span class="{{$data->alert_change_title}}">
                                                @if($data->video_title!=null && $data->video_title!="" && $data->video_title!="[]")
                                                <span data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$data->video_title}}">{{$data->video_title}}</span>
                                                @else
                                                Unknown <button class="cur-poiter video-sync btn-success" data-id="{{$data->id}}"><i class="ti-reload"></i></button>
                                                @endif
                                            </span>
                                        </span></td>
                                    <td class="text-center">{{number_format($data->views, 0, '.', ',')}}</td>
                                    <td class="disp-none">{{$data->match_claim}}</td>
                                    <td class="disp-none">{{$data->log_claim}}</td>
                                    <td class="disp-none">{{$data->cam_type}}</td>
                                    <td class="disp-none">{{$data->is_changed_title_text}}</td>
                                    <td class="disp-none">{{$data->is_bommix_text}}</td>
                                    <td class="disp-none">{{$data->distributor}}</td>
                                    <td><span data-toggle="tooltip" data-placement="top" title="" data-original-title="Submited: {{$data->create_time}}">{{$data->public}}</span></td>
                                    <td>
                                        <div class="">
                                    @if($is_admin_music)
                                        @if($data->status_confirm==1)
         
                                            <button id="confirm_promo_{{$data->id}}" class="rounded btn-sm on-default remove-row btn btn-info m-r-5 confirm_promo" data-toggle="tooltip" data-placement="top" title="{{$data->message}}" status="3" value="{{$data->id}}"><i class="fa fa-check"></i></button>
                                            <button id="confirm_promo_reject_{{$data->id}}" class="rounded btn-sm on-default remove-row btn btn-warning m-r-5 confirm_promo" data-toggle="tooltip" data-placement="top" title="" data-original-title="Reject with message to manager" status="2" value="{{$data->id}}"><i class="fa fa-remove"> </i></button>
                                            <button id="confirm_delete_{{$data->id}}" class="rounded btn-sm on-default remove-row btn btn-danger m-r-5 confirm_promo" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete" status="4" value="{{$data->id}}"><i class="fa fa-trash"></i> </button>
                                            @if($data->is_match_claim==0)
<!--                                                <button class="rounded btn-sm on-default remove-row btn btn-info m-r-5 " id="claim_{{$data->id}}"
                                                        data-toggle="tooltip" data-placement="top" title="Recheck claim" 
                                                        data-original-title="Check claim" 
                                                        onclick="checkClaim('{{$data->id}}')">
                                                    <i class="fa fa-refresh"></i> Claim</button>-->
                                            @endif
                                            <div id="div_message_{{$data->id}}" class="disp-flex disp-none">
                                                <div><input id="message_{{$data->id}}" class="form-control m-r-5 input-sm" type="text" value=""></div>
                                                <div><button stype="float: right;" class="btn-sm on-default remove-row btn btn-info m-r-5 m-t-5 confirm_submit_message" status="2" id="{{$data->id}}"><i class="fa fa-check"></i>Submit</button></div>
                                            </div>

                                        @elseif($data->status_confirm==2)
                                        <span class="badge label-table badge-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$data->message}}">User checking</span>
                                        @else

                                        @endif
                                    @else

                                        @if($data->status_confirm==2)
                                        <span class="badge label-table badge-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$data->message}}">Message</span>
                                        <!--<button id="confirm_delete_{{$data->id}}" class="btn-sm on-default remove-row btn btn-danger confirm_promo" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$data->message}}" status="4" value="{{$data->id}}"><i class="fa fa-trash"></i> Delete</button>-->
                                        @endif
                                        <button id="confirm_delete_{{$data->id}}" class="rounded btn-sm on-default remove-row btn btn-danger m-r-5 confirm_promo" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete" status="4" value="{{$data->id}}"><i class="fa fa-trash"></i> </button>
                                    @endif
                                    @if($data->is_match_claim==0)
<!--                                        <button class="rounded btn-sm on-default remove-row btn btn-info m-r-5 "
                                                id="claim_{{$data->id}}"
                                                data-toggle="tooltip" data-placement="top" 
                                                title="Check claim for video" 
                                                onclick="checkClaim('id','{{$data->id}}')"><i class="fa fa-refresh"></i></button>
                                        <button class="rounded btn-sm on-default remove-row btn btn-warning m-r-5 claim_{{$data->channel_id}}"
                                                
                                                data-toggle="tooltip" data-placement="top" 
                                                title="Check claim for channel" 
                                                onclick="checkClaim('channel_id','{{$data->channel_id}}')"><i class="fa fa-refresh"></i></button>
                                        <button class="rounded btn-sm on-default remove-row btn btn-info m-r-5 cookie_{{$data->channel_id}}"
                                                
                                                data-toggle="tooltip" data-placement="top" 
                                                title="Sync cookie" 
                                                onclick="checkClaim('cookie','{{$data->channel_id}}')"><i class="fa fa-retweet"></i></button>-->
                                    @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endif

@if(!in_array('22',explode(",", $user_login->role)))
<div class="row">
    <div class="col-lg-3">
        <div class="portlet">
            <div id="channel-confirm" class="portlet-heading portlet-default">
                <h3 class="portlet-title">
                    Channel Confirm Count
                </h3>
                <div class="portlet-widgets">
                    <a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
                    <span class="divider"></span>
                    <a data-toggle="collapse" data-parent="#accordion1" href="#bg-channel-checker-count"><i class="ion-minus-round"></i></a>
                    <span class="divider"></span>
                    <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div id="bg-channel-checker-count" class="panel-collapse collapse show">
                <div class="portlet-body">
                    <div id="channel-chart" style="overflow: auto">
                        <table class="table" style="width: 99%">
                            <thead class="thead-default">
                                <tr>
                                    <th>Manager</th>
                                    <th>Admin</th>
                                    <th>User</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($channelConfirmSyncCount as $data)

                                <tr>
                                    <td>
                                        <?php
                                        $pos = strripos($data->user_name, '_');
                                        echo substr($data->user_name, 0, $pos);
                                        ?>
                                    </td>
                                    <td>{{$data->admin_check}}</td>
                                    <td>{{$data->user_check}}</td>

                                </tr>

                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="portlet">
	<div class="portlet-heading portlet-default">
		<h3 class="portlet-title">
                    Channel Confirm ({{count($channelConfirmSync)}})
		</h3>
		<div class="portlet-widgets">
			<a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
			<span class="divider"></span>
			<a data-toggle="collapse" data-parent="#accordion1" href="#bg-channel-checker"><i class="ion-minus-round"></i></a>
			<span class="divider"></span>
			<a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="bg-channel-checker" class="panel-collapse collapse show">
		<div class="portlet-body">
		<div id="channel-chart" style="overflow: auto">
                <table class="table" style="width: 99%">
                    <thead class="thead-default">
                        <tr>
                            <th style="text-align: center">Index</th>
                            <th>Manager</th>
                            <th>Channel</th>
                            <th>Channel Type</th>
                            <th>Channel Genre</th>
                            <th>Channel Tag</th>
                            <th>Function</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($channelConfirmSync as $data)
                                <?php
                                $pos = strripos($data->user_name, '_');
                                $us =  substr($data->user_name, 0, $pos);
                                ?>
                        @if($is_admin_music || (!$is_admin_music && $data->user_name == $user_login->user_code))
                        <tr>
                            <td style="text-align: center">{{$data->id}}</td>
                            <td>{{$us}}</td>
                            <td><a target="_blank" href="https://www.youtube.com/channel/{{$data->chanel_id}}">{{$data->chanel_name}}</a></td>
                            <td>{{$data->channel_type}}</td>
                            <td>{{$data->channel_genre}}</td>
                            <td>{{$data->tags}}</td>
                            <td>
                                @if($is_admin_music)
                                    @if($data->is_sync==2)
                                        <div class="disp-flex">
                                            <button id="confirm_promo_{{$data->id}}" class="on-default remove-row btn btn-info m-r-5 confirm_promo btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$data->message}}" ac_type="channel" status="1" value="{{$data->id}}"><i class="fa fa-check"></i>OK</button>
                                            <button id="confirm_promo_reject_{{$data->id}}" class="on-default remove-row btn btn-warning m-r-5 confirm_promo btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Reject with message to manager" ac_type="channel" status="3" value="{{$data->id}}"><i class="fa fa-remove"></i> Reject</button>
                                            <div id="div_message_{{$data->id}}" class="disp-flex disp-none">
                                                <div><input id="message_{{$data->id}}" class="input-sm form-control m-r-5" type="text" value=""></div>
                                                <div><button class="on-default remove-row btn btn-info m-r-5 m-t-5 confirm_submit_message btn-sm" ac_type="channel" status="3" id="{{$data->id}}"><i class="fa fa-check"></i>Submit</button></div>
                                            </div>
                                        </div>
                                    @elseif($data->is_sync==3)
                                        <span class="badge label-table badge-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$data->message}}">User checking</span>    
                                    @endif
                                @else
                                    @if($data->is_sync==3)
                                        <div class="disp-flex">
                                            <button id="confirm_promo_{{$data->id}}" class="on-default remove-row btn btn-info m-r-5 confirm_promo btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$data->message}}" ac_type="channel" status="2" value="{{$data->id}}"><i class="fa fa-check"></i>Confirm</button>
     
                                        </div>
                                    @elseif($data->is_sync==2)
                                        <span class="badge label-table badge-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$data->message}}">Admin checking</span>    
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>

            </div>
		</div>
	</div>
</div>


    </div>
</div>
@endif

@if(in_array('20',explode(",", $user_login->role)) || in_array('21',explode(",", $user_login->role)))
@if(!in_array('22',explode(",", $user_login->role)))
<div class="row">
    <div class="col-lg-12">
        <div class="portlet">
	<div class="portlet-heading portlet-default">
		<h3 class="portlet-title">
                    List channels
		</h3>
		<div class="portlet-widgets">
			<a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
			<span class="divider"></span>
			<a data-toggle="collapse" data-parent="#accordion1" href="#bg-list-channel"><i class="ion-minus-round"></i></a>
			<span class="divider"></span>
			<a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="bg-list-channel" class="panel-collapse collapse show">
		<div class="portlet-body">
		            <div id="channel-chart" style="overflow: auto">
                <form id="form-filter" action="/dashboard">
                    <table class="table" style="width: 99%;table-layout: auto">
                        <thead class="thead-default">
                            <tr>
                                <!--<th>#</th>-->
                                <th>@sortablelink('user_name','Manager')</th>
                                <th>@sortablelink('chanel_name','Channel')</th>
                                <!--<th>@sortablelink('channel_type','Channel Type')</th>-->
                                <th>
                                    <select id="channelType" class="form-control" name="channel_type">
                                        {!!$channelType!!}
                                    </select>
                                </th>
                                <th>@sortablelink('channel_genre','Channel Genre')</th>
                                <th style="width: 15%;">SubGenre</th>
                                <th style="text-align: center">@sortablelink('video_count','Total Uploads ')</th>
                                <th style="text-align: center">Daily Uploads</th>
                                <!--<th style="text-align: center">Total/Daily Uploads</th>-->
                                <th style="text-align: center">Today's Uploads</th>
                                <th style="text-align: center">@sortablelink('increasing','Daily Views')</th>
                                <th style="text-align: center">@sortablelink('view_count','Total Views')</th>
                                <th style="text-align: center">@sortablelink('subscriber_count','Subscribers')</th>
                                <!--<th style="text-align: center">Channel Clickup</th>-->
                                <th style="text-align: center">@sortablelink('status','Status')</th>
                                <!--<th style="text-align: center">Function</th>-->


                            </tr>
                        </thead>

                        <?php
                        $i = 1;
                        $listId = [];
                        $listChannel = [];
                        ?>
                        <tbody>
                            @foreach($channels as $data)
                            <tr>
                                <!--                            <th scope="row">{{$i++}}</th>-->
                                <td class="{{$data->checkUpload}}" <?php //echo ($data->video_daily < $data->limit) ? "style='background: #f31e1ead;'" : "" 
                                                                    ?>>
                                    {{$data->user_name}}
                                </td>
                                <td>
                                    <a target="_blank" href="https://www.youtube.com/channel/{{$data->chanel_id}}">{{$data->chanel_name}}</a>
                                    @if($data->gologin!=null)
                                    <i class="fa fa-moon-o color-red"></i>
                                    @endif
                                </td>
                                <td>{{$data->channel_type}}</td>
                                <td>{{$data->channel_genre}}</td>
                                <td style="overflow: hidden;white-space: normal;">
                                    <?php
                                    if ($data->channel_subgenre != null) {
                                        $subs = explode(",", $data->channel_subgenre);
                                        foreach ($subs as $sub) {
                                            echo "<span class='subgenre'>$sub</span>";
                                        }
                                    }
                                    ?>
                                </td>
                                <td style="text-align: center">{{number_format($data->video_count, 0, '.', ',')}}</td>
                                <td style="text-align: center">
                                    {{number_format($data->video_daily, 0, '.', ',')}}/{{$data->limit}} <i onclick="videoDetail({{$data->id}})" class='fa fa-line-chart'></i>
                                </td>
                                <!--<td style="text-align: center"><i onclick="videoDetail({{$data->id}})" class='fa fa-line-chart'></i> </td>-->
                                <td style="text-align: center"><i daily="{{$data->video_daily}}" link="{{$data->chanel_id}}" class='fa fa-list-ul view_list_video'></i> </td>
                                <td style="text-align: center"><span style="font-size: 12px;margin-right: 5px;" class="<?php echo $data->daily_change < 0 ? "color-red" : "color-green"; ?>">{{$data->daily_change}}%</span>{{number_format($data->increasing, 0, '.', ',')}} <i onclick="viewDetail({{$data->id}})" class='fa fa-line-chart'></i></td>
                                <td style="text-align: center">{{number_format($data->view_count, 0, '.', ',')}}</td>
                                <td style="text-align: center">{{number_format($data->subscriber_count, 0, '.', ',')}}</td>
                                <!--<td style="text-align: center"><a target="_blank" href="{{$data->channel_clickup}}">Clickup</a></td>-->
                                <td style="text-align: center"><?php echo $data->status == 1 ? "Active" : "Inactive"; ?>
                                </td>


                            </tr>
                            @php
                            $listId[]=$data->id;
                            $listChannel[]=$data->chanel_id;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>

                </form>
                <input type="hidden" id="list-id" value="{{json_encode($listId)}}" />
                <input type="hidden" id="list-channel-id" value="{{json_encode($listChannel)}}" />
                <input type="hidden" id="next-id" />
                <input type="hidden" id="prev-id" />
            </div>
		</div>
	</div>
</div>


    </div>
</div>
@endif
@endif

@if(in_array('20',explode(",", $user_login->role)) && !in_array('22',explode(",", $user_login->role)))
<div class="row">
    <div class="col-lg-12">
        <div class="portlet">
	<div class="portlet-heading portlet-default">
		<h3 class="portlet-title">
                    Wakeup happy list
		</h3>
		<div class="portlet-widgets">
			<a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
			<span class="divider"></span>
			<a data-toggle="collapse" data-parent="#accordion1" href="#bg-wakeup-happy"><i class="ion-minus-round"></i></a>
			<span class="divider"></span>
			<a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="bg-wakeup-happy" class="panel-collapse collapse show">
		<div class="portlet-body">
		<div id="channel-chart" style="overflow: auto">
                <div class="col-md-1">
                    <select id="month" class="form-control" name="month">
                        {!!$months!!}
                    </select>

                </div>
                <table id="tbl-playlist-wakeup" class="table" style="width: 99%">
                    <thead class="thead-default">
                        <tr>
                            <th>Username</th>
                            <th>Channel</th>
                            <th>Playlist</th>
                            <th>Efficiency(%)</th>
                            <th>Check Time</th>
                            <th>Create Time</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <?php $i = 1; ?>
                    <tbody>
                        @foreach($channels as $data)
                        <tr>
                            <td class="{{$data->check_wake}}">{{$data->user_name}}</td>
                            <td><a target="_blank" href="https://www.youtube.com/channel/{{$data->chanel_id}}">{{$data->chanel_name}}</a>
                                @if(isset($data->next_auto_wake_time))
                                <i class="mdi mdi-brightness-auto color-green cur-poiter" data-toggle="tooltip" data-placement="top" title="" data-original-title="This channel used Autowakeup"></i>
                                @endif
                            </td>
                            <td><a target="_blank" href="https://www.youtube.com/playlist?list={{$data->playlist_wake_id}}">{{$data->playlist_wake_id}}</a></td>
                            <td><span class="efficiency_wake cur-poiter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Click to view list videos" channel_name="{{$data->chanel_name}}" playlist_id="{{$data->playlist_wake_id}}" list_video_wake="{{json_encode($data->wake_video_list)}}">{{$data->wake_percent}}

                                </span>
                            </td>
                            <td onclick="wakePosition('{{$data->chanel_id}}')" class="cur-poiter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Position of promos videos" channel_name="{{$data->chanel_name}}" channel_id="{{$data->chanel_id}}">{{$data->check_time}}</td>
                            <td><span class="wakeup_report cur-poiter" channel_id="{{$data->chanel_id}}">{{$data->last_wake_time}}</span></td>
                            <td>{{$data->wake_total}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
		</div>
	</div>
</div>



    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="portlet">
	<div class="portlet-heading portlet-default">
		<h3 class="portlet-title">
                    Stats By Manager (all channels)
		</h3>
		<div class="portlet-widgets">
			<a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
			<span class="divider"></span>
			<a data-toggle="collapse" data-parent="#accordion1" href="#bg-stats"><i class="ion-minus-round"></i></a>
			<span class="divider"></span>
			<a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="bg-stats" class="panel-collapse collapse show">
		<div class="portlet-body">
                    <div id="channel-chart" style="overflow: auto">
                <table class="table" style="border-collapse: inherit">
                    <thead class="thead-default">
                        <tr>
                            <th>#</th>
                            <th>Manager</th>
                            <th>Total Daily Views</th>
                            <th>Total Views</th>
                            <th>Total Subscribers</th>
                            <th>Total Wakeup Daily</th>
                            <th># of Channels</th>

                        </tr>
                    </thead>
                    <?php $i = 1; ?>
                    <tbody>
                        @foreach($listMusicAccount as $data)
                        <tr>
                            <th scope="row">{{$i++}}</th>
                            <td>{{$data->user_name}}</td>
                            <td>{{number_format($data->increasing, 0, '.', ',')}}</td>
                            <td>{{number_format($data->view, 0, '.', ',')}}</td>
                            <td>{{number_format($data->sub, 0, '.', ',')}}</td>
                            <td>{{$data->total_wake}}</td>
                            <td>{{number_format($data->channel, 0, '.', ',')}}</td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
		</div>
	</div>
</div>


    </div>
</div>

@endif



@if(in_array('11',explode(",", $user_login->role)) && !in_array('22',explode(",", $user_login->role)) )

@if(count($promosCheck)>0)
<div class="row">
    <div class="col-lg-12">
        <div class="portlet">
	<div class="portlet-heading portlet-default">
		<h3 class="portlet-title">
                    Promos videos
		</h3>
		<div class="portlet-widgets">
			<a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
			<span class="divider"></span>
			<a data-toggle="collapse" data-parent="#accordion1" href="#bg-promo-video"><i class="ion-minus-round"></i></a>
			<span class="divider"></span>
			<a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="bg-promo-video" class="panel-collapse collapse show">
		<div class="portlet-body">
		<div id="channel-chart" style="overflow: auto">
                <table class="table" style="width: 99%">
                    <thead class="thead-default">
                        <tr>
                            <th style="text-align: center">Index</th>
                            <th style="text-align: left">Channel</th>
                            <th style="text-align: left">Campaign</th>
                            <th style="text-align: left">Type</th>
                            <th style="text-align: left">Video</th>
                            <th style="text-align: left">Message</th>
                            <th style="text-align: center">Function</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promosCheck as $data)
                        @if($data->status_confirm==2)
                        <tr>
                            <td style="text-align: center">{{$data->id}}</td>
                            <td><a target="_blank" href="https://www.youtube.com/channel/{{$data->channel_id}}">{{$data->channel_name}}</a></td>
                            <td>{{$data->campaign_name}}</td>
                            <td>
                                @if($data->video_type==2)
                                Lyric
                                @elseif($data->video_type==5)
                                Mix
                                @else($data->video_type==6)
                                Short
                                @endif
                            </td>
                            <td><a target="_blank" href="https://www.youtube.com/watch?v={{$data->video_id}}">
                                    @if($data->video_title!=null && $data->video_title!="")
                                    {{$data->video_title}}
                                    @else
                                    Unknown
                                    @endif
                                </a></td>

                            <td>{{$data->message}}</td>
                            <td>
                                <div class="disp-flex">
                                    <button class="on-default remove-row btn btn-info m-r-5 confirm_promo" data-toggle="tooltip" data-placement="top" title="" data-original-title="Fix message already" status="1" value="{{$data->id}}"><i class="fa fa-check"></i>Confirm</button>
                                    <button id="confirm_delete_{{$data->id}}" class="on-default remove-row btn btn-danger confirm_promo" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete" status="4" value="{{$data->id}}"><i class="fa fa-trash"></i> Delete</button>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>

            </div>
		</div>
	</div>
</div>

 

    </div>
</div>
@endif



<div class="row">
    <div class="col-lg-6">
        <div class="portlet">
	<div id="submit-promo-claim" class="portlet-heading portlet-default">
		<h3 class="portlet-title">
                    Promo & Claim
		</h3>
		<div class="portlet-widgets">
			<a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
			<span class="divider"></span>
			<a data-toggle="collapse" data-parent="#accordion1" href="#bg-promo-claim"><i class="ion-minus-round"></i></a>
			<span class="divider"></span>
			<a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="bg-promo-claim" class="panel-collapse collapse show">
		<div class="portlet-body">
		            <form id="form-mix">
                <input type="hidden" name='_token' value='{{csrf_token()}}' />
                <div class="">
                    <div class="row ">
                        <div class="col-md-12">
                            <div class="checkbox checkbox-primary">
                                <input id="claim" class="claim" type="checkbox" name="is_claim">
                                <label for="claim">USE OUR SONG</label>
                            </div>
                        </div>
                        <div class="div_claim disp-none">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Submit Type</label>
                                    <div class="col-12">
                                        <select id="submit_type" class="form-control submit_type" name="submit_type">
                                            <option value="1">Auto</option>
                                            <option value="2">Manual</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="div_submit_type_2 disp-none">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Claims</label>
                                        <div class="col-12">
                                            <select multiple id="mix_claim" class="form-control search_select" name="mix_claim[]" style="height: 100px" data-show-subtext="true" data-live-search="true">
                                                @foreach($claims as $claim)
                                                <option value="{{$claim->id}}">[{{$claim->id}}] {{$claim->campaign_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Promo</label>
                                        <div class="col-12">
                                            <select multiple id="mix_promo" class="form-control search_select" name="mix_promo[]" style="height: 300px" data-show-subtext="true" data-live-search="true">

                                                @foreach($promos as $promo)
                                                <option value="{{$promo->id}}">[{{$promo->id}}] [ {{$promo->genre}}] [{{$promo->wake_position}}] {{$promo->campaign_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">Link</label>
                                <div class="col-12">
                                    <!--<input id="mix_link" class="form-control" type="text" name="mix_link">-->
                                    <textarea id="mix_link" class="form-control" rows="5" name="mix_link" spellcheck="false" style="line-height: 1.25"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="radio radio-success m-r-10">
                                    <input id="link_type_lyric" class="link_type_lyric" type="radio" name="link_type" value="2" checked="true">
                                    <label for="link_type_lyric">Lyric</label>&nbsp;
                                    <input id="link_type_mix" class="link_type_mix" type="radio" name="link_type" value="5">
                                    <label for="link_type_mix">Mix</label>&nbsp;
                                    <input id="link_type_short" class="link_type_short" type="radio" name="link_type" value="6">
                                    <label for="link_type_short">Short</label>&nbsp;
                                </div>
                                <div class="checkbox checkbox-primary div_link_type_mix disp-none">
                                    <input id="bommix" class="bommix" type="checkbox" name="is_bommix">
                                    <label for="bommix">Boom mix</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">

                            <button id="btnMix" type="button" class="btn btn-outline-info btn-micro"><i class="fa fa-upload"></i> Submit</button>
                        </div>
                    </div>
                </div>
            </form>
            <br>
            <div id="channel-chart" style="overflow: auto">
                <table id="tbl-mix" class="table" style="width: 99%">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Video</th>
                            <th>Create Time</th>
                        </tr>
                    </thead>
                    <?php $i = 1; ?>
                    <tbody>
                        @foreach($mixs as $data)
                        <tr class="odd">
                            <td>{{$data->campaign_id}}</td>
                            <td><a target="_blank" href="https://www.youtube.com/watch?v={{$data->content}}">{{$data->content}}</a></td>
                            <td>{{\App\Common\Utils::calcTimeText($data->create_time)}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
		</div>
	</div>
</div>

    </div>
    <div class="col-lg-6">
        <div class="portlet">
	<div class="portlet-heading portlet-default">
		<h3 class="portlet-title">
                    Wakeup happy
		</h3>
		<div class="portlet-widgets">
			<a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
			<span class="divider"></span>
			<a data-toggle="collapse" data-parent="#accordion1" href="#bg-wake"><i class="ion-minus-round"></i></a>
			<span class="divider"></span>
			<a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="bg-wake" class="panel-collapse collapse show">
		<div class="portlet-body">
		            <div class="">
                <div class="row ">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">Channel</label>
                            <div class="col-12">
                                <select id="channelWake" class="form-control" name="c2">
                                    @foreach($listWakeManual as $channel)
                                    <option value="{{$channel->chanel_id}}">{{$channel->chanel_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">Playlist wakeup</label>
                            <div class="col-12">
                                <input id="link_playlist" class="form-control" type="text" name="link_playlist">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="col-8 col-form-label">&nbsp;</label>
                        <button id="btnWakeup" type="button" class="btn btn-outline-info btn-micro"><i class="fa fa-upload"></i> Submit</button>
                    </div>
                </div>
            </div>
            <br>
            <div id="channel-chart" style="overflow: auto">
                <table id="tbl-playlist" class="table" style="width: 99%">
                    <thead class="thead-default">
                        <tr>
                            <th>Channel</th>
                            <th>Playlist</th>
                            <th>Create Time</th>
                            <th>Efficiency</th>
                        </tr>
                    </thead>
                    <?php $i = 1; ?>
                    <tbody>
                        @foreach($wakeups as $data)
                        <tr>
                            <td><a target="_blank" href="https://www.youtube.com/channel/{{$data->channel_id}}">{{$data->channel_name}}</a></td>
                            <td><a target="_blank" href="https://www.youtube.com/playlist?list={{$data->content}}">{{substr($data->content,0,5)}}</a></td>
                            <td>{{\App\Common\Utils::calcTimeText($data->create_time)}}</td>
                            <td>
                                @if($data->wakeup_percent==null)
                                N/A
                                @else
                                <span class="cur-poiter efficiency_wake" data-toggle="tooltip" data-placement="top" title="" data-original-title="Click to view list videos" playlist_id="{{$data->content}}" list_video_wake="{{($data->wakeup_result)}}">{{$data->wakeup_percent}}%

                                </span>
                                @endif

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
		</div>
	</div>
</div>

    </div>

    <!--    <div class="col-lg-4">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">Tasks</h4>
    <?php ?>
            <form id="frm-tasks">

                @foreach($listTasks as $data)
                <div class="checkbox checkbox-primary">
                    <input id="{{$data->code}}" class='{{$data->code}} tasks' type="checkbox" {{ $data->status }} name="{{$data->code}}">
                    <label for="{{$data->code}}">
                        {{$data->name}} <span class="color-red">{{$data->note}}</span>
                    </label>
                </div>
                @endforeach

            </form>
        </div>
</div>-->

</div>

<div class="row">
    <div class="col-lg-12">
        <div class="portlet">
	<div class="portlet-heading portlet-default">
		<h3 class="portlet-title">
                    Channel Lists
		</h3>
		<div class="portlet-widgets">
			<a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
			<span class="divider"></span>
			<a data-toggle="collapse" data-parent="#accordion1" href="#bg-channel-list"><i class="ion-minus-round"></i></a>
			<span class="divider"></span>
			<a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="bg-channel-list" class="panel-collapse collapse show">
		<div class="portlet-body">
		 <div id="channel-chart" style="overflow: auto">
                <table class="table" style="width: 99%">
                    <thead>
                        <tr>

                            <th>@sortablelink('chanel_name','Channel')</th>
                            <th>@sortablelink('channel_genre','Genre')</th>
                            <th>SubGenre</th>
                            <th style="text-align: center">@sortablelink('video_count','Total Uploads ')</th>
                            <th style="text-align: center">Daily Uploads</th>
                            <th style="text-align: center">Total/Daily Uploads</th>
                            <th style="text-align: center">Today's Uploads</th>
                            <th style="text-align: center">@sortablelink('increasing','Daily Views')</th>
                            <th style="text-align: center">@sortablelink('view_count','Total Views')</th>
                            <th style="text-align: center">@sortablelink('subscriber_count','Subscribers')</th>
                            <th style="text-align: center">Auto Wakeup</th>
                            <th style="text-align: center">Last Wakeup</th>
                            <th style="text-align: center">Next Wakeup</th>
                            <!--<th style="text-align: center">Function</th>-->
                        </tr>
                    </thead>
                    <?php $i = 1; ?>
                    <tbody>
                        @foreach($listWakeManual as $data)
                        <tr>
                            <!--<th scope="row">{{$i++}}</th>-->
                            <td class="{{$data->checkUpload}}" <?php echo ($data->count_daily < $data->limit) ? "style='background: #f31e1ead;'" : "" ?>>
                                <a target="_blank" href="https://www.youtube.com/channel/{{$data->chanel_id}}">{{$data->chanel_name}}</a>
                            </td>
                            <td>{{$data->channel_genre}}</td>
                            <td>
                                <?php
                                if ($data->channel_subgenre != null) {
                                    $subs = explode(",", $data->channel_subgenre);
                                    foreach ($subs as $sub) {
                                        echo "<span class='subgenre'>$sub</span>";
                                    }
                                }
                                ?>
                            </td>
                            <td style="text-align: center">{{number_format($data->video_count, 0, '.', ',')}}</td>
                            <td style="text-align: center">{{number_format($data->video_daily, 0, '.', ',')}}/{{$data->limit}}</td>
                            <td style="text-align: center"><i detail_id="{{$data->id}}" class='fa fa-line-chart video_detail'></i> </td>
                            <td style="text-align: center"><i daily="{{$data->video_daily}}" link="{{$data->chanel_id}}" class='fa fa-video-camera view_list_video'></i> </td>
                            <td style="text-align: center">{{number_format($data->increasing, 0, '.', ',')}} <i detail_id="{{$data->id}}" class='fa fa-line-chart views_detail'></i></td>
                            <td style="text-align: center">{{number_format($data->view_count, 0, '.', ',')}}</td>
                            <td style="text-align: center">{{number_format($data->subscriber_count, 0, '.', ',')}}</td>
                            <td style="text-align: center"><i data-id="{{$data->id}}" class='fa fa-list-ul make-wakeup-happy'></i> </td>
                            <td style="text-align: center">
                                {{$data->last_auto_wake_time}}
                            </td>
                            <td style="text-align: center">
                                {{$data->next_auto_wake_time}}
                            </td>

                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
		</div>
	</div>
</div>


    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="portlet">
	<div class="portlet-heading portlet-default">
		<h3 class="portlet-title">
                    DAILY VIDEO UPLOAD LIST
		</h3>
		<div class="portlet-widgets">
			<a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
			<span class="divider"></span>
			<a data-toggle="collapse" data-parent="#accordion1" href="#bg-daily-upload"><i class="ion-minus-round"></i></a>
			<span class="divider"></span>
			<a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="bg-daily-upload" class="panel-collapse collapse show">
		<div class="portlet-body">
		<div id="channel-chart" style="overflow: auto">
                <table class="table" style="border-collapse: inherit">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Channel Name</th>
                            <th>Video</th>
                            <th>Artists</th>
                            <th>Label</th>
                        </tr>
                    </thead>
                    <?php
                    $i = 1;
                    $countFuck = 0;
                    ?>
                    <tbody>
                        @foreach($channels as $data)
                        <?php
                        $lastUploads = array();
                        if ($data->last_upload_label != null && $data->last_upload_label != "") {
                            $lastUploads = json_decode($data->last_upload_label);
                        }
                        ?>
                        @foreach($lastUploads as $lastUpload)
                        <?php
                        $colorArtist = "";
                        $colorLabel = "";
                        foreach ($fuckLabelArtists as $fuck) {
                            if (App\Common\Utils::containString($lastUpload->label, $fuck->name)) {
                                $colorLabel = "color-red";
                                $countFuck++;
                            }
                            if (App\Common\Utils::containString($lastUpload->artist, $fuck->name)) {
                                $colorArtist = "color-red";
                                $countFuck++;
                            }
                        }
                        ?>
                        <tr>
                            <th scope="row">{{$i++}}</th>
                            <td><a target="_blank" href="https://www.youtube.com/channel/{{$data->chanel_id}}">{{$data->chanel_name}}</a></td>
                            <td><a target="_blank" href="https://www.youtube.com/watch?v={{$lastUpload->video_id}}">{{$lastUpload->title}}</a></td>
                            <td class="{{$colorArtist}}">{{$lastUpload->artist}}</td>
                            <td class="{{$colorLabel}}">{{$lastUpload->label}}</td>
                        </tr>
                        @endforeach

                        @endforeach
                    </tbody>
                </table>
            </div>
		</div>
	</div>
</div>


    </div>
</div>

@endif
@if(!in_array('22',explode(",", $user_login->role)))
<!--<audio controls autoplay="true">
  <source src="Ngay-Tet-Que-Em-Thuy-Chi.ogg" type="audio/ogg">
  <source src="Ngay-Tet-Que-Em-Thuy-Chi.mp3" type="audio/mpeg">
Your browser does not support the audio element.
</audio>-->
<!--<div class="row">
    <div class="col-lg-6">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">Report Sub</h4>

            <div id="sub-chart">
                <div id="sub-chart-container" class="flot-chart" style="height: 320px;">
                </div>
            </div>
        </div>

    </div>
    <div class="col-lg-6">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">Report View</h4>

            <div id="view-chart">
                <div id="view-chart-container" class="flot-chart" style="height: 320px;">
                </div>
            </div>
        </div>

    </div>
</div>-->
@endif

@include('dialog.musicchannel')
@include('dialog.viewchart')
@include('dialog.wakeuphappy')
@endsection

@section('script')
<script type="text/javascript">
    
    function checkClaim(type,id){
        var $this = $(`#claim_${id}`);

        var input = {id:id};
        if(type=='channel_id'){
            input = {channel_id:id};
            $this = $(`.claim_${id}`);
        }else if(type=='cookie'){
            input = {cookie:id};
            $this = $(`.cookie_${id}`);
        }
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($this.html() !== loadingText) {
            $this.data('original-text', $this.html());
            $this.html(loadingText);
        }
            $.ajax({
            type: "GET",
            url: 'checkClaim',
            data: input,
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $.Notification.notify(data.status, 'top center', 'Notification', data.message);
                $this.html($this.data('original-text'));
                $this.attr("disabled",true);
            },
            error: function(data) {
                $this.html($this.data('original-text'));
            }
        });
    }
    //2023/00/05 copy link
    function copyText(text) {
        navigator.clipboard.writeText(text);
        $.Notification.notify('success', 'top center', 'Notification', 'Copied: ' + text);
    }
    //2023/03/09 bitly
    function removeBitly(id) {

    }
    //2023/02/21 filter promos video checker
    function confirmAllFilter() {
        var filter = [];
        var searchPanes = tblPromos.context[0]._searchPanes.s.panes;
        searchPanes.forEach(searchPane => {
            console.log(searchPane.s.name);
            var key = string_to_slug(searchPane.s.name);
            var tmp = [];
            if (searchPane.s.selections.length > 0) {
                searchPane.s.selections.forEach(elem => {
                    tmp.push(elem);
                });
            }
            var text = '{"' + key + '":' + JSON.stringify(tmp) + '}'
            var object = JSON.parse(text);
            filter.push(object);
        });
        const obj = {};
        for (const item of filter) {
            for (const key in item) {
                obj[key] = item[key];
            }
        }
        obj._token = $("#token").val();
        console.log(obj);
        var btn = $("#btn-confirm-filter");
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if (btn.html() !== loadingText) {
            btn.data('original-text', btn.html());
            btn.html(loadingText);
        }
        $.ajax({
            type: "POST",
            url: "/confirmPromoFilter",
            data: obj,
            dataType: 'json',
            success: function(data) {
                console.log(data);
                btn.html(btn.data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                if (data.campaigns.length > 0) {
                    $.each(data.campaigns, function(key, campaign) {
                        $("#pro_check_" + campaign.id).remove();
                    });
                }

            },
            error: function(data) {
                btn.html(btn.data('original-text'));
            }
        });
    }
    var tblPromos = $('#tbl-promos-checker').DataTable({

        searching: true,
        ordering: true,
        processing: false,
        select: false,
        responsive: false,
        paging: false,
        lengthMenu: [
            [1000],
            [1000]
        ],
        searchPanes: {
            //            viewTotal: true,
            viewCount: true,
            //            controls: false,
            collapse: true,
            //            initCollapsed: true,
            @if($is_admin_music)
            columns: [1, 8, 9, 10, 11, 12, 13]
            @else
            columns: [8, 9, 10, 11, 12, 13]
            @endif

        },
        dom: 'Plfrtip',
        columnDefs: [{
                "targets": [8, 9, 10, 11, 12, 13],
                "visible": false,
                "searchable": true
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Manager',
                },
                targets: [1]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Log',
                },
                targets: [9]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Match Claim',
                },
                targets: [8]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Campaign Type',
                },
                targets: [10]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Changed Title',
                },
                targets: [11]
            },
            {
            searchPanes: {
                    show: true,
                    header: 'Boommix',
                },
                targets: [12]
            },
            {
                searchPanes: {
                    show: true,
                    header: 'Distributor',
                },
                targets: [13]
            },
        ]

    });
    $('#tbl-mix').DataTable({
        "stripeClasses": [],
        "ordering": false
    });
    $('#tbl-playlist').DataTable({
        "stripeClasses": [],
        "ordering": false
    });
    $("#cbbUserName").change(function() {
        $("#userName").val($(this).val());
        updateDataViewChart($("#startDate").val(), $("#endDate").val(), $("#userName").val());
    });
    var chartContent = 'viewsTotalChannel';
    //id cua channel dang dc xem chart view
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
        'Life Time': ['20210601', moment()]
    };
    $('#date_rage_picker').daterangepicker(options, function(start, end, label) {

        //                console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        if (label === "Custom Range") {
            $("#date_rage_picker").val(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        } else {
            $("#date_rage_picker").val(label);
        }
        $("#startDate").val(start.format('YYYYMMDD'));
        $("#endDate").val(end.format('YYYYMMDD'));
        if (chartContent == 'viewChannel') {
            viewDetail(curr_id, 0);
        } else {
            updateDataViewChart(start.format('YYYYMMDD'), end.format('YYYYMMDD'), $("#userName").val());
        }
    });

    function updateDataViewChart(startDate, endDate, username = '') {
        $("#content-view-dialog").html("");
        $("#dialog-view-loading").show();
        var url = "/getChartTotalDailyViews";
        var title = "Daily Views";
        if (chartContent == 'countChannels') {
            url = "/getChartTotalChannelsCount";
            title = "Daily Channels";
        }
        $.ajax({
            type: "GET",
            url: url,
            data: {
                "start": startDate,
                "end": endDate,
                "user": username
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                var html = '<div class="row"><div class="col-md-12 data_user"></div><div class="col-md-12"><canvas id="chart-total-views-daily"></canvas></div></div>';
                $("#content-view-dialog").html(html);
                $("#dialog-view-loading").hide();
                var label = new Array();
                var dataTotal = new Array();
                var dataDaily = new Array();
                var colors = ['#0071d1', '#63b300', '#e6990e', '#912fc0', '#d94d6c', '#0f8071', '#f270b9', '#f2c428', '#2fa5cb', '#5b54d5', '#00b4a2', '#eb3c97'];
                var formatDate = '';
                var datasets = new Array();
                if (chartContent == 'countChannels') {
                    var label = new Array();
                    var htmlUs = '<ul>'
                    $.each(data.data_user, function(i, us) {
                        htmlUs += '<li>' + us.user_name + ' : <b>' + us.channels + '</b></li>';
                    });
                    $(".data_user").html(htmlUs);
                    $.each(data.charts, function(key, value) {
                        var footer = new Array();
                        var channels = new Array();
                        $.each(value.data, function(k, v) {
                            //                            formatDate = (v.date.toString().substring(0, 4) + "/" + v.date.toString().substring(4, 6) + "/" + v.date.toString().substring(6, 8));
                            formatDate = (v.date);
                            label.push(formatDate);
                            channels.push(v.channels);
                            footer.push(v.detail);
                        });
                        var dataset = {
                            label: "Daily Channel",
                            data: channels,
                            footer: footer,
                            fill: false,
                            borderColor: colors[key],
                            backgroundColor: colors[key],
                            borderWidth: 1

                        };
                        datasets.push(dataset);
                    });
                    drawLineCharts('chart-total-views-daily', label, datasets);
                    $("#dialog-view-tile").html(title + ' (' + number_format(Math.round(data.charts[0].total * 100) / 100, 0, ',', '.') + ')');
                } else {
                    var footer = new Array();
                    var footer2 = new Array();
                    $.each(data.data, function(key, value) {

                        //                        formatDate = (value.date.toString().substring(0, 4) + "/" + value.date.toString().substring(4, 6) + "/" + value.date.toString().substring(6, 8));
                        //                        label.push(formatDate + " (" + value.channels + ")");
                        //                        dataTotal.push(value.views);
                        //                    drawLineChart('chart-total-views-daily', dataTotal, label, title);
                        formatDate = (value.date.toString().substring(0, 4) + "/" + value.date.toString().substring(4, 6) + "/" + value.date.toString().substring(6, 8));
                        label.push(formatDate + " (" + value.channels + ")");
                        dataTotal.push(value.views);
                        footer.push(value.detail);
                    });
                    var dataset = {
                        label: title,
                        data: dataTotal,
                        fill: false,
                        footer: footer,
                        borderColor: colors[0],
                        backgroundColor: colors[0],
                        borderWidth: 1
                    };
                    datasets.push(dataset);
                    var dataPromos = new Array();
                    $.each(data.data_promo, function(key, value) {
                        footer2.push(value.detail);
                        dataPromos.push(value.views);
                    });
                    var dataset2 = {
                        label: "Monetize views",
                        data: dataPromos,
                        fill: false,
                        footer: footer2,
                        borderColor: colors[1],
                        backgroundColor: colors[1],
                        borderWidth: 1
                    };
                    datasets.push(dataset2);
                    drawLineCharts('chart-total-views-daily', label, datasets);
                    $("#dialog-view-tile").html(title + ' (' + number_format(Math.round(data.total * 100) / 100, 0, ',', '.') + ' - ' + number_format(Math.round(data.totalPromo * 100) / 100, 0, ',', '.') + ' - ' + Math.round(data.totalPromo / data.total * 100) + '%)');
                }


            },
            error: function(data) {}
        });
    }

    function copyPromoSocialInfo(id) {
        $.ajax({
            type: "GET",
            url: "/getcampaignstatistics",
            data: {
                "id": id
            },
            dataType: 'json',
            success: function(data) {
                copyToClipboardNewLine(data.artists_social);
            },
            error: function(data) {

            }
        });
    }

    function copyPromoLyric(id) {
        $.ajax({
            type: "GET",
            url: "/getcampaignstatistics",
            data: {
                "id": id
            },
            dataType: 'json',
            success: function(data) {
                copyToClipboardNewLine(data.lyrics);
            },
            error: function(data) {

            }
        });
    }

    $(".btn-finish-task").click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var id = $this.attr("data-task-id");
        var camId = $this.attr("data-cam-id");
        var taskType = $this.attr("data-task-type");
        var link = $("#task_" + id).val();
        var action = $this.attr("data-action");
        $.ajax({
            type: "GET",
            url: "/finishPromoTask",
            data: {
                "task_id": id,
                "cam_id": camId,
                "link": link,
                "task_type": taskType,
                "action": action
            },
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                if (taskType == 10 && data.status == 'success') {
                    $this.closest("tr").hide();
                }
                if (action == 0 && data.status == 'success') {
                    $this.closest("tr").hide();
                }
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
            },
            error: function(data) {
                $this.html($this.data('original-text'));
            }
        });
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
    $("#channelType").change(function() {
        $("#form-filter").submit();
    });
    $("#daily_view_total").click(function() {
        chartContent = 'viewsTotalChannel';
        updateDataViewChart($("#startDate").val(), $("#endDate").val(), $("#userName").val());
        $('#dialog_view').modal({
            backdrop: false
        });
    });
    $("#daily_channel_total").click(function() {
        chartContent = 'countChannels';
        updateDataViewChart($("#startDate").val(), $("#endDate").val(), $("#userName").val());
        $('#dialog_view').modal({
            backdrop: false
        });
    });
    $(".promo_wake").change(function() {
        var status = 0;
        if ($(this).is(":checked")) {
            status = 1;
        }
        var promo_id = $(this).attr("promo_id");
        $.ajax({
            type: "GET",
            url: "/promoWake",
            data: "id=" + promo_id + "&status=" + status,
            dataType: 'json',
            success: function(data) {
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
            },
            error: function(data) {
                //                but.button('reset');
            }
        });
    });
    $(".confirm_promo").click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }

        var id = $(this).val();
        var status = $(this).attr("status");
        var token = $("#token").val();
        var url = '/confirmPromo';
        var actionType = $(this).attr("ac_type");
        if (actionType == 'channel') {
            //chạy cho channel
            url = '/confirmChannel';
            if (status == 3) {
                $("#div_message_" + id).removeClass("disp-none");
                $("#confirm_promo_" + id).addClass("disp-none");
                $("#confirm_promo_reject_" + id).addClass("disp-none");
                $("#confirm_delete_" + id).addClass("disp-none");
                return;
            }

        } else {
            //chạy cho promo video
            if (status == 2) {
                $("#div_message_" + id).removeClass("disp-none");
                $("#confirm_promo_" + id).addClass("disp-none");
                $("#confirm_promo_reject_" + id).addClass("disp-none");
                $("#confirm_delete_" + id).addClass("disp-none");
                return;
            } else if (status == 100) {
                window.location.reload();
            }
        }

        $.ajax({
            type: "POST",
            url: url,
            data: "id=" + id + "&_token=" + token + "&status=" + status,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                if (actionType == 'channel') {
                    if (status == 1) {
                        $this.closest("tr").hide();
                    }else if(status ==2){
                        $this.closest("td").html('<span class="badge label-table badge-warning">Admin checking</span>');
                    }
                } else {
                    if (status == 3 || status == 4) {
                        $this.closest("tr").hide();
                    }
                }

            },
            error: function(data) {
                $this.html($this.data('original-text'));
            }
        });
    });
    $(".confirm_submit_message").click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var id = $(this).attr("id");
        var status = $(this).attr("status");
        var token = $("#token").val();
        var message = $("#message_" + this.id).val();
        var url = '/confirmPromo';
        var actionType = $(this).attr("ac_type");
        if (actionType == 'channel') {
            //chạy cho channel
            url = '/confirmChannel';
        }
        $.ajax({
            type: "POST",
            url: url,
            data: "id=" + id + "&_token=" + token + "&status=" + status + "&mess=" + message,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                if (data.status == "success") {
                    if (actionType == 'channel') {
                        if (status == 3) {
                            $this.closest("td").html('<span class="badge label-table badge-warning">User checking</span>');
                        } else if (status == 1) {
                            $this.closest("tr").hide();
                        }
                    } else {
                        if (status == 2) {
                            $this.closest("td").html('<span class="badge label-table badge-warning">User checking</span>');
                        } else if (status == 3) {
                            $this.closest("tr").hide();
                        }
                    }
                }



            },
            error: function(data) {
                $this.html($this.data('original-text'));
            }
        });
    });
    $('#tbl-playlist-wakeup').DataTable({
        "stripeClasses": [],
        "order": [
            [0, "asc"]
        ],
        "paging": false,
    });
    $(".wake_position").click(function() {
        $("#content-dialog").html("");
        $("#dialog-loading").show();
        var colors = ['#3f33f1', '#ef00ff', '#49eabe', '#c8ea0a', '#ea710a', '#eaf909', '#f336ca', '#7a00ff', '#2bb4e8'];
        var channelId = $(this).attr("channel_id");
        var channelName = $(this).attr("channel_name");
        $.ajax({
            type: "GET",
            url: "/promosWakeupVideos",
            data: {
                'channel_id': channelId
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
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
                $.each(data, function(key, video) {
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
                        label: video[0].video_id,
                        data: position,
                        wake: wake,
                        fill: false,
                        //pointRadius: 3,
                        pointBorderWidth: borderWith,
                        pointBorderColor: pointColor,
                        pointBackgroundColor: pointColor,
                        borderColor: colors[key],
                        backgroundColor: colors[key],
                        borderWidth: 2
                    };
                    datasets.push(dataset);
                });
                console.log(datasets);
                drawLineCharts('chart-wakeup-position', label, datasets);
                $("#dialog-list-tile").html("Promo videos of " + channelName);
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
    $(".wakeup_report").click(function() {
        $("#content-dialog").html("");
        $("#dialog-loading").show();
        $.ajax({
            type: "GET",
            url: "/getTasksByChannelId",
            data: {
                'channel_id': $(this).attr("channel_id"),
                'month': $("#month").val()
            },
            dataType: 'json',
            success: function(data) {
                //                console.log(data);
                var html = '<div class="row"><div class="col-md-12"><canvas id="chart-wakeup"></canvas></div></div>';
                $("#content-dialog").html(html);
                $("#dialog-loading").hide();
                if (data.status == 1) {
                    var label = new Array();
                    var dataTotal = new Array();
                    var dataDaily = new Array();
                    $.each(data.chart_data, function(key, value) {
                        label.push(value.date);
                        dataTotal.push(value.total);
                    });
                    drawLineChart('chart-wakeup', dataTotal, label, 'Wakeup Count');
                    $("#dialog-list-tile").html(data.maked + '/' + dataTotal.length + ' days      Average : ' + data.avgPercent + '%');
                }

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
    $(".efficiency_wake").click(function() {
        $("#content-dialog").html("");
        $("#dialog-loading").show();
        var data = $(this).attr("list_video_wake");
        var playlist = $(this).attr("playlist_id");
        if (data == null || data == "") {
            $.Notification.autoHideNotify('error', 'top center', 'Notify', "Not found data");
            return;
        }
        $("#dialog-list-tile").html("<a target='_blank' href='https://www.youtube.com/playlist?list=" + playlist + "'>List video of " + playlist + "</a>");
        //        var html = "<ol>";
        var html = '<table class="table-no-boder" style="border-collapse: inherit;"> ';
        var lists = JSON.parse(data);
        var i = 0;
        $.each(lists, function(key, value) {
            i++;
            var color = "color-green";
            var link = 'ti-link';
            if (value.wakeup == 0) {
                color = "color-red";
                link = 'ti-unlink';
            }
            if (key == lists.length - 1) {
                link = "";
            }
            //            html+='<li class="'+color+'"><img src=""><a target="_blank" href="https://www.youtube.com/watch?v='+value.video_id+'">'+value.title+'</a></li>';
            html += '<tr class="border-0"><td>' + i + '</td><td><a target="_blank" href="https://www.youtube.com/watch?v=' + value.video_id + '">';
            html += '<img class="video-img_thumb" src="https://i.ytimg.com/vi/' + value.video_id + '/default.jpg">' + value.title + '</a></td></tr>';
            html += '<tr><td></td><td class="' + color + '" style="padding-left: 17px;font-size: 22px;padding-bottom:0px;padding-top:0px"><i class="' + link + '"></i></td></tr>';
        });
        html += '</table>';
        //         html+='</ol>';
        $("#content-dialog").html(html);
        $("#dialog-loading").hide();
        //        $.ajax({
        //            type: "GET",
        //            url: "/getlistvideo",
        //            data: {
        //                'daily': $(this).attr("daily"),
        //                'link': $(this).attr("link")
        //            },
        //            dataType: 'json',
        //            success: function (data) {
        //  
        //
        //                $("#content-dialog").html(html);
        //                $("#dialog-loading").hide();
        //                $('.text-ellipsis').tooltip();
        //            },
        //            error: function (data) {
        //                //                but.button('reset');
        //            }
        //        });
        $(".modal-dialog").removeClass("modal-80");
        $('#dialog_music_channel').modal({
            backdrop: false
        });
    });
    $(".tasks").change(function() {
        var frm = $("#frm-tasks").serialize();
        console.log(frm);
        var name = $(this).attr("name");
        $(this).attr("disabled", true);
        $.ajax({
            type: "POST",
            url: "/updateTask",
            data: frm + "&type=1&content=" + name,
            dataType: 'json',
            success: function(data) {

            },
            error: function(data) {
                //                but.button('reset');
            }
        });
    });
    $(".make-wakeup-happy").click(function(e) {
        e.preventDefault();
        $("#id_channel").val($(this).attr("data-id"));
        $('#dialog_wakeup').modal({
            backdrop: false
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

        $.ajax({
            type: "POST",
            url: "/addWakeup",
            data: $("#form-wakeup-add").serialize(),
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify('error', 'top center', 'Notify', data.message);
            },
            error: function(data) {
                $this.html($this.data('original-text'));
                //                but.button('reset');
            }
        });
    });
    $("#btnWakeup").click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        //        $("#tbl-playlist tbody").append(newRowContent);
        var channel = $("#channelWake").val();
        var channelName = $("#channelWake option:selected").text();
        var link = $("#link_playlist").val();
        var token = $("#token").val();
        $.ajax({
            type: "POST",
            url: "/updateTask",
            data: "content=" + link + "&type=2" + "&_token=" + token + "&channel=" + channel,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify('error', 'top center', 'Notify', data.message);
                if (data.status == 1) {
                    var row = '<tr><td>x' + channelName + '</td><td><a target="_blank" href="' + link + '">' + link + '</a></td><td>A few seconds</td></tr>';
                    $("#tbl-playlist tbody").append(row);
                }

            },
            error: function(data) {
                $this.html($this.data('original-text'));
                //                but.button('reset');
            }
        });
    });
    $("#btnMix").click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        //        $("#tbl-playlist tbody").append(newRowContent);
        var mixCover = $("#mix_cover").val();
        var link = $("#mix_link").val();
        var claim = $("#claim").is(':checked');
        var token = $("#token").val();
        var form = $("#form-mix").serialize();
        console.log(form);
        $.ajax({
            type: "POST",
            url: "/updateTask",
            data: "type=3&" + form,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify('error', 'top center', 'Notify', data.message);
                if (data.status == 1) {
                    var row = '<tr><td><a target="_blank" href="' + link + '">' + link + '</a></td><td>A few seconds</td></tr>';
                    $("#tbl-mix tbody").append(row);
                    window.location.reload();
                }
            },
            error: function(data) {
                $this.html($this.data('original-text'));
                //                but.button('reset');
            }
        });
    });

    function checkInfoPlaylist() {
        var playlistId = $("#auto_wake_playlist_id").val();
        $.ajax({
            type: "GET",
            url: "/playlist/info/",
            data: {
                "playlistId": playlistId
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if (data.status == 0) {
                    $.Notification.autoHideNotify('error', 'top center', '', data.message);
                } else {
                    $("#title").val(data.message.playlistName);
                }
            },
            error: function(data) {}
        });
    }

    var ticks = [];
    var dataView = [];
    var dataSub = [];
    var dataChannel = [];
    var dataVideo = [];
    var labelView = ["View Chart"];
    var labelSub = ["Sub Chart"];
    var labelChannel = ["Channel Chart"];
    var labelVideo = ["Video Chart"];


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
                    var wake2 = new Array();
                    var pointColor = new Array();
                    var borderColor = new Array();
                    var borderWith = new Array();
                    var position = new Array();
                    $.each(video, function(k, v) {
                        label.push(v.date);
                        position.push(v.position);
                        wake.push(v.wakeup == 1 ? "Linked" : "Unlink");
                        wake2.push(["dong1", "dong2", "dong3"]);
                        pointColor.push(v.wakeup == 0 ? "#ff0000" : "#04d604");
                        borderColor.push("#fff");
                        borderWith.push(3);
                    });
                    var dataset = {
                        label: video[0].title,
                        data: position,
                        wake: wake,
                        wake2: wake2,
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
                console.log(datasets);
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
        chartContent = 'viewChannel';
        $("#content-dialog").html("");
        $("#dialog-loading").show();
        var listIdStr = $("#list-id").val();
        var listId = JSON.parse(listIdStr);
        var total = listId.length;
        curr_id = id;
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
                'id': curr_id,
                'start': $("#startDate").val(),
                'end': $("#endDate").val()
            },
            dataType: 'json',
            success: function(data) {
                var html = '<div class="row"><div class="col-md-12">';
                html += '<div class="control-chart"><i onclick="nextChartsDailyView(\'prev\')" class="ion-chevron-left float-left cur-poiter"></i><i onclick="nextChartsDailyView(\'next\')" class="ion-chevron-right float-right cur-poiter m-r-10"></i></div>';
                html += '<canvas id="chart-views"></canvas></div></div>';
                //                $("#content-dialog").html(html);
                //                $("#dialog-loading").hide();
                $("#content-view-dialog").html(html);
                $("#dialog-view-loading").hide();
                var dataChart = JSON.parse(data.view_detail);
                var label = new Array();
                var dataDaily = new Array();
                $.each(dataChart, function(key, value) {
                    var tick = value.time.toString();
                    label.push(tick.substring(0, 4) + "/" + tick.substring(4, 6) + "/" + tick.substring(6, 8));
                    dataDaily.push(value.daily >= 0 ? value.daily : 0);
                });
                drawLineChart('chart-views', dataDaily, label, 'Daily Views');
                //                $("#dialog-list-tile").text(data.chanel_name);
                $("#dialog-view-tile").text(data.chanel_name);
            },
            error: function(data) {}
        });
        if (show == 1) {
            $(".modal-dialog").addClass("modal-80");
            $('#dialog_view').modal({
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
                    dataDaily.push(value.video_daily);
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