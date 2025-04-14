@extends('layouts.master')

@section('content')
<style>
    .select-dropdown-container {
        position: relative;
        display: inline-block;
        width: 100%;
    }
    .select-dropdown-menu {
        display: none;
        position: absolute;
        width: 550px;
        max-height: 550px;
        /*overflow-y: auto;*/
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        padding: 10px;
        background: white;
        z-index: 1000;
    }
    .sortable-list {
        list-style: none;
        padding: 0;
        margin: 0;
        overflow-y: auto;
        max-height: 470px;
    }
    .sortable-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #ddd;
        margin-bottom: 8px;
        cursor: pointer;
        transition: 0.3s;
    }
    .sortable-list li:hover {
        background: #e9ecef;
    }
    .group-name {
        font-size: 16px;
        font-weight: 500;
        flex-grow: 1;
    }
    .action-icons {
        display: flex;
        gap: 8px;
    }
    .selected {
        background: #007bff !important;
        color: white;
    }
    .selected i {
        color: white;
    }
    .search-box {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    #dropdownMenuButton{
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Channel Management
                <button data-toggle="tooltip" data-placement="top" data-original-title="Add Channel" type="button" class="btn btn-outline-info btn-add-channel m-r-5"><i class="fa fa-plus"></i></button>
                <button data-toggle="tooltip" data-placement="top" data-original-title="Create Email" type="button" class="btn btn-outline-info btn-create-channel m-r-5"><i class="fa fa-envelope"></i></button>

            </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Channel Management</li>

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
                    <form id="form-search" action="/channelmanagement">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">ID/Name/Email</label>
                                    <div class="col-12">
                                        <input id="search_channel" class="form-control" type="text" name="c1"
                                               value="{{$request->c1}}">
                                    </div>
                                </div>
                            </div>
                            @if($is_admin_music)
                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">User</label>
                                    <div class="col-12">
                                        <select class="form-control search_select" name="c5" data-show-subtext="true"
                                                data-live-search="true">
                                            {!!$listusercode!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif
<!--                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Group <span id="btn_add_group_channel"><i
                                                class="fa fa-plus-circle color-red"
                                                style="font-size: 20px;"></i></span></label>
                                    <div class="col-12">
                                        <select id="group_channel_search" class="form-control search_select" name="c3"
                                                data-show-subtext="true" data-live-search="true">
                                            {!!$group_channel_search!!}
                                        </select>
                                    </div>
                                </div>
                            </div>-->
                            <div class="col-md-1">
                                <label class="col-12 col-form-label">Group <span id="btn_add_group_channel"><i
                                                class="fa fa-plus-circle color-red"
                                                style="font-size: 20px;"></i></span></label>
                                <div class="select-dropdown-container">
                                    <button class="btn  dropdown-toggle" type="button" id="dropdownMenuButton">
                                        Select Group
                                    </button>

                                    <div class="select-dropdown-menu">
                                        <input type="text" id="searchBox" class="search-box" placeholder="Search...">
                                        <ul id="sortableList" class="sortable-list div_scroll_50"></ul>
                                    </div>
                                </div>
                                <input type="hidden" id="group_channel_search" name="c3"/>
                            </div>
                            
                            
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Genre</label>
                                    <div class="col-12">
                                        <select class="form-control" name="channel_genre">
                                            {!!$channelGenre!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @if($is_admin_music)
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">API</label>
                                    <div class="col-12">
                                        <select class="form-control" name="c4">
                                            {!!$statusApi!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">AutoLogin</label>
                                    <div class="col-12">
                                        <select class="form-control" name="bas_new_status">
                                            {!!$basNewStatus!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Manage Type</label>
                                    <div class="col-12">
                                        <select class="form-control" name="c6">
                                            {!!$channelManageType!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Upload Type</label>
                                    <div class="col-12">
                                        <select class="form-control" name="c7">
                                            {!!$channelUploadType!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Wakeup Type</label>
                                    <div class="col-12">
                                        <select class="form-control" name="c8">
                                            {!!$channelWakeupType!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Status</label>
                                    <div class="col-12">
                                        <select id="search_status" class="form-control" name="c2">
                                            {!!$status!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Studio</label>
                                    <div class="col-12">
                                        <select id="studio" class="form-control" name="studio">
                                            {!!$studio!!}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Brand</label>
                                    <div class="col-12">
                                        <select id="brand" class="form-control" name="brand">
                                            {!!$brand!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Hub</label>
                                    <div class="col-12">
                                        <select id="statusHub" class="form-control" name="statusHub">
                                            {!!$statusHubs!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Tags</label>
                                    <div class="col-12">
                                        <select name="tags[]" class="select2_multiple form-control " multiple
                                                style="height: 100px">
                                            {!!$channelTags!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                         
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Storm</label>
                                    <div class="col-12">
                                        <select id="level" class="form-control" name="level">
                                            {!!$level!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Is Sync</label>
                                    <div class="col-12">
                                        <select id="level" class="form-control" name="is_sync">
                                            {!!$isSync!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Channel Type</label>
                                    <div class="col-12">
                                        <select id="other_filter" class="form-control" name="other_filter">
                                            <option value="-1">Select</option>
                                            <option value="1">Moonshots</option>
                                            <option value="2">Bas</option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Upload By</label>
                                    <div class="col-12">
                                        <select id="version" class="form-control" name="version">
                                            <option value="-1">Select</option>
                                            <option value="2">Api Upload</option>
                                            <option value="1">Moonshots or Bas Upload</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Tracking</label>
                                    <div class="col-12">
                                        <select id="sub_tracking" class="form-control" name="sub_tracking">
                                            <!--                                            <option value="-1">Select</option>
                                                                                        <option value="0">No</option>
                                                                                        <option value="1">Yes</option>-->
                                            {!!$subTracking!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @if($is_admin_music)
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Not update MS</label>
                                    <div class="col-12">
                                        <div class="checkbox checkbox-primary">
                                            <input id="outofdate_moonshot_stat"  class="checkbox-multi" type="checkbox" name="outofdate_moonshot_stat" value="1" {{$request->moonshot_stat}} >
                                            <label class="" for="outofdate_moonshot_stat">{{$stats}} channel</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Log</label>
                                    <div class="col-12">
                                        <input id="gmail_log" class="form-control" type="text" name="gmail_log" value="{{$request->gmail_log}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Change Info</label>
                                    <div class="col-12">
                                        <select id="is_changeinfo" class="form-control" name="is_changeinfo">
                                            {!!$isChangeInfo!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Opt</label>
                                    <div class="col-12">
                                        <select id="is_add_otp" class="form-control" name="is_add_otp">
                                            {!!$isUpdateOtp!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Comment</label>
                                    <div class="col-12">
                                        <select id="status_cmt" class="form-control" name="status_cmt">
                                            {!!$statusCmt!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="limit" id="limit" value="{{$limit}}">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">&nbsp;</label>
                                    <div class="col-12">
                                        <button id="btnSearch" type="submit" class="btn btn-outline-info btn-micro"><i
                                                class="fa fa-filter"></i> Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>


                </div>

            </div>
        </div>
    </div>
</div>
<form id="formChannel" class="form-horizontal form-label-left w-100" method="POST">
    {{ csrf_field() }}
    <div class="col-md-12">
        <div class="card-box">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row actions">
                        <label class="col-12 col-form-label">Action</label>
                        <div class="col-4">
                            <!--max=32-->
                            <select id="action" class="form-control action_command" name="action" data-show-subtext="true" data-live-search="true">
                                <option value="0">Select</option>
                                <option value="17">17. Config</option>
                                <option value="1">1. Add channel to group</option>
                                <option value="28">28. Sync Cookie</option>
                                <option value="26">26. Clear Profile Moonshots</option>
                                <option value="34">34. Add to Social</option>
                                <option value="38">38. Delete Channel</option>
                                
                                @if($is_admin_music || $is_supper_admin)
                                <option value="33">33. yt_device_oauth</option>
                                <option value="32">32.Change Pass</option>
                                <option value="35">35.Change Info</option>
                                <option value="19">19.Vip Render</option>
                                <option value="2">2.Move channel</option>
                                <option value="18">18.Set Api Upload Auto</option>
                                <optgroup label="MoonShots">
                                    <option value="24">24.Moonshots Auto Login</option>
                                    <!--<option value="25">Moonshots Make Api</option>-->
                                    <option value="26">26.Clear Profile Moonshots</option>
                                    <option value="29">29.Clear Profile Bas</option>
                                    <option value="27">27.Set Ip</option>
                                    <option value="28">28.Sync Cookie</option>
                                    <option value="30">30.Create Channel</option>
                                </optgroup>
                                <optgroup label="Boom vip">
                                    <option value="22">Boom Vip Active</option>
                                    <option value="23">Boom Vip Inactive</option>
                                </optgroup>
                                @endif
                                <optgroup label="Comment">
                                    <option value="36">Set Manual</option>
                                    <option value="37">Set Auto</option>
                                </optgroup>
                                <optgroup label="Channel">
                                    <option value="5">Set Manual</option>
                                    <option value="6">Set Auto</option>
                                </optgroup>
                                <optgroup label="Upload">
                                    <option value="7">Set Upload Manual</option>
                                    <option value="8">Set Upload Auto</option>
                                </optgroup>
                                <optgroup label="Wakeup">
                                    <option value="9">Set Wakeup Manual</option>
                                    <option value="10">Set Wakeup Auto</option>
                                </optgroup>
                                <optgroup label="Crosspost">
                                    <option value="11">Disable Cross</option>
                                    <option value="12">Enable Cross</option>
                                </optgroup>
                                <optgroup label="Promos lyric">
                                    <option value="13">Enable Assign Promos Lyric video</option>
                                    <option value="14">Disable Assign Promos Lyric video</option>
                                </optgroup>
                                <optgroup label="Promos mix">
                                    <option value="15">Enable Assign Promos mix</option>
                                    <option value="16">Disable Assign Promos mix</option>
                                </optgroup>
                                <optgroup label="Studio">
                                    <option value="4">Show</option>
                                    <option value="31">Hide</option>
                                </optgroup>
                                <optgroup label="Hubs">
                                    <option value="20">Turn off hub</option>
                                    <option value="21">Turn on hub</option>
                                </optgroup>
                                <option value="3">Check views</option>

                            </select>
                        </div>
                        <div class="col-1">
                            <button id="btnExcute" type="button" class="btn btn-outline-info btn-micro"><i
                                    class="fa fa-upload"></i> Excute</button>
                        </div>

                    </div>
                </div>
                @if($is_admin_music)

                <div class="col-sm-6">
                    <label class="col-12 col-form-label">&nbsp;</label>
                    <div class="row pull-right">
<!--                    <button id="btnDesEdit" type="button" class="btn btn-outline-info btn-micro pull-right m-r-15"><i
                            class="fa fa-sticky-note-o"></i> Description</button>-->
                        <button id="btnCardManagement" type="button" class="btn btn-outline-warning btn-micro pull-right m-r-15"><i
                                class="fa fa-id-card"></i> Card Management</button>
                        <button id="btnCard" type="button" class="btn btn-outline-danger btn-micro pull-right"><i
                                class="fa fa-id-card"></i> Card & Endscreen</button>
                    </div>
                </div>
                <!--                <div class="col-sm-3">
                                </div>-->
                @endif
                <div class="col-sm-12">
                    <hr>
                    <div class="form-group row actions">
                        <div class="col-2 div_action_command_1 disp-none">
                            <select id="g_c_add" class="form-control search_select" name="action_group_channel"
                                    data-show-subtext="true" data-live-search="true">
                                {!!$group_channel_search!!}
                            </select>
                        </div>
                        @if(in_array('20',explode(",", $user_login->role)))
                        <div class="col-2 div_action_command_2 disp-none">
                            <select class="form-control search_select" name="action_user" data-show-subtext="true"
                                    data-live-search="true">
                                {!!$listusercode!!}
                            </select>
                        </div>
                        <div class="col-2 div_action_command_19 disp-none">
                            <select class="form-control search_select" name="vip_day">
                                <option value="1">1 day</option>
                                <option value="2">2 days</option>
                                <option value="3">3 days</option>
                                <option value="4">4 days</option>
                                <option value="5">5 days</option>
                            </select>
                        </div>
                        @endif
                        <!--                                <div class="col-1">
                                                                <button id="btnExcute" type="button" class="btn btn-outline-info btn-micro"><i
                                                                        class="fa fa-upload"></i> Excute</button>
                                                            </div>-->
                    </div>
                    <div class="div_action_command_17 disp-none">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Genre</label>
                                    <div class="col-12">
                                        <select class="form-control" name="channel_genre">
                                            {!!$channelGenre!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Channel Tags</label>
                                    <div class="col-12">
                                        <select id="tags" name="tags[]" class="select2_multiple form-control" multiple
                                                style="height: 110px">
                                            {!!$channelTags!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Alarm</label>
                                    <div class="col-12">
                                        <select id="upload_alert" name="upload_alert" class="form-control">
                                            <option selected value="-1">Select</option>
                                            <option value="1">Get Alarm</option>
                                            <option value="0">Do not get alarm</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">OTP key</label>
                                    <div class="col-12">
                                        <input id="otp_key" class="form-control" type="text" name="otp_key" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">ProfileName</label>
                                    <div class="col-12">
                                        <input id="profile_name" class="form-control" type="text" name="profile_name"
                                               placehoder="Name show on Mooonshots">
                                    </div>
                                </div>
                            </div>
                            @if($is_admin_music)
                            <div class="col-md-1">
                                <label class="col-12 col-form-label">Date get pass</label>
                                <select class="form-control" name="expire_get_pass">
                                    <option value="-1">Select</option>
                                    <option value="5">5 days</option>
                                    <option value="4">4 days</option>
                                    <option value="3">5 days</option>
                                    <option value="2">2 days</option>
                                    <option value="1">1 day</option>
                                </select>
                            </div>
                            @endif
                            <div class="col-md-1">
                                <label class="col-12 col-form-label">Upload By</label>
                                <select class="form-control" name="version">
                                    <option value="-1">Select</option>
                                    <option value="1">Moonshots</option>
                                    <option value="2">API</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="col-12 col-form-label">Channel Tracking</label>
                                <select class="form-control" name="sub_tracking">
                                    <option value="-1">Select</option>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 div_action_command_24 disp-none">
                        <div class="row">

                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Client</label>
                                    <div class="col-12">
                                        <select class="form-control" name="client">
                                            <option value="dev2-new">dev2-new</option>
                                            <option value="client_led">client_led</option>
                                            <option value="linux_bas_v2">linux_bas_v2</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Proxy</label>
                                    <select class="form-control" name="proxy">
                                        <option value="">None</option>
                                        <option value="tinsoft">Tinsoft</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 div_action_command_27 disp-none">
                        <div class="row">

                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Client</label>
                                    <div class="col-12">
                                        <select class="form-control" name="client_27">
                                            <option value="dev2-new">dev2-new</option>
                                            <option value="client_led">client_led</option>
                                            <option value="linux_bas_v2">linux_bas_v2</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-md-12">
        <div class="card-box">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="text-dark  header-title m-t-0">CHANNEL LIST ({{$datas->total()}})</h4>
                    <div style="overflow: auto;padding-right: 2px;">
                        <table class="table mobile-table-width hover-button-table">
                            <thead class="thead-default">
                                <tr align="center">
                                    <th style="width: 5%;text-align: center">
                                        <div class="checkbox checkbox-primary tbl-chk">
                                            <input id="select_all" type="checkbox" name="select_all">
                                            <label for="select_all" class="m-b-22 p-l-0"
                                                   style="margin-bottom: 1rem"></label>
                                        </div>
                                    </th>
                                    <th style="width: 5%;text-align: center">@sortablelink('id','ID')</th>
                                    @if(in_array('20',explode(",", $user_login->role)))
                                    <th style="width: 10%;text-align: left">User</th>
                                    @endif
                                    <th style="width: 10%;">@sortablelink('group_channel_id','Group')</th>
                                    <th style="width: 20%;">
                                        @sortablelink('channel_name',trans('label.title.channel'))</th>
                                    <th style="width: 20%;text-align: center">Tags</th>
                                    <th style="width: 20%;text-align: center">Information</th>
                                    <th style="width: 10%;text-align: center">
                                        @sortablelink('increasing','Increase')
                                    </th>
                                    <th style="width: 13%;text-align: center">
                                        @sortablelink('view_count','Views')
                                    </th>
                                    <th style="width: 5%;text-align: center">Hub</th>
                                    <th style="width: 12%;text-align: center">
                                        @sortablelink('chanel_create_date','Date Created')</th>
                                    <th style="width: 13%;text-align: center">
                                        @sortablelink('subscriber_count','Subs')</th>
                                    <th style="width: 5%;text-align: center">
                                        @sortablelink('confirm_time','Start Date')
                                    </th>
                                    <!--                                            <th style="width: 10%;text-align: right">{{trans('label.col.function')}}
                                        </th>-->
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($datas as $data)
                                <tr class="odd gradeX" align="center">
                                    <td>
                                        <div class="checkbox checkbox-primary tbl-chk">
                                            <input class="checkbox-multi" type="checkbox" name="chkChannelAll[]"
                                                   id="ck-video<?php echo $data->id; ?>" value="{{$data->id}}">
                                            <label class="m-b-18 p-l-0" for="ck-video<?php echo $data->id; ?>"></label>
                                        </div>
                                    </td>
                                    <td><?php
                                        echo $data->id;
                                        ?>
                                    </td>
                                    @if(in_array('20',explode(",", $user_login->role)))
                                    <td class="text-left">
                                        {{substr($data->user_name, 0, strripos($data->user_name, '_'))}}</td>
                                    @endif
                                    <td style="text-align: left;">
                                        <?php
                                        if (isset($listGroupChannel)) {
                                            foreach ($listGroupChannel as $groupChannel) {
                                                if ($groupChannel->id == $data->group_channel_id) {
                                                    echo $groupChannel->group_name;
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="position-relative" style="text-align: left;<?php
                                    if ($data->increasing >= 200) {
                                        echo "border-bottom: 3px solid #06d23b;";
                                    }
                                    ?>">
                                        <span
                                            class="<?php echo $data->status == 0 ? 'song-block' : '' ?>">{{$data->chanel_name}}</span>


                                        <?php
                                        $open = "";
                                        if ($data->chanel_id != null) {
                                            $link = 'https://www.youtube.com/channel/' . $data->chanel_id;
                                            $open = "copyToClipboard('$link')";
                                        }
                                        ?>
                                        <span class="m-r-10" data-toggle='tooltip' data-placement='top'
                                              data-original-title='Copy link channel to clipboard'><i onclick="{{$open}}"
                                                                                                class='fa fa-copy'></i> </span>
                                        <span class='m-r-10 view-realtime-chart' data-channel='{{$data->chanel_id}}'
                                              data-name='{{$data->chanel_name}}' data-toggle='tooltip'
                                              data-placement='top' data-original-title='View Chart'><i
                                                class='fa fa-line-chart'></i> </span>

                                        <br>
                                        <span class="font-italic <?php echo $data->status == 0 ? 'song-block' : '' ?>">{{$data->note}}</span>
                                        <?php
                                        $info = $data->note;
                                        $open = "#";
                                        if ($info != null && $info != '') {
                                            $open = "copyToClipboard('$info')";
                                        }
                                        ?>
                                        <span class="m-r-10" data-toggle='tooltip' data-placement='top'
                                              data-original-title='Copy email to clipboard'><i onclick="{{$open}}"
                                                                                         class='fa fa-envelope'></i> </span>
                                        @if($data->is_rebrand==5)
                                        <!--                                            <i class="fa fa-image"
                                                                         data-toggle='tooltip' data-placement='top'
                                                                         data-original-title='Branded successfully'></i>-->
                                        @endif
                                        @if($data->version==2) <i class="fa fa-google-plus-official"
                                                                  data-toggle='tooltip' data-placement='top'
                                                                  data-original-title='Upload via API'></i>
                                        @endif
                                        @if($data->gologin!=null)
                                        <i class="fa fa-moon-o copy_profile_id" data-profile="{{$data->gologin}}" data-toggle='tooltip' data-placement='top'
                                           data-original-title='This is Moonshots channel, click to copy profile Id'></i>

                                        @endif
                                        <br>
                                        <span class="font-italic">{{$data->handle}}</span>
                                        <br>
                                        <div class="disp-flex div_button_action position-absolute">
                                            <!--                                            <a target="_blank" href="AutoProfile://profile/login/?id={{$data->gologin}}&gmail={{$data->note}}"
                                                                                            class="m-r-10 btn btn-success waves-effect waves-light btn-sm btn-ssm m-b-5"
                                                                                            value="{{$data->chanel_id}}" data-toggle='tooltip' data-placement='top'
                                                                                            data-original-title='Profile Id: {{$data->gologin}}'><i
                                                                                                class="fa fa-google"></i></a>-->
                                            <button
                                                class="m-r-10 btn btn-success waves-effect waves-light btn-sm btn-ssm m-b-5 btn-gologin"
                                                data-profile="{{$data->gologin}}"
                                                value="{{$data->hash_pass}}" 
                                                data-toggle='tooltip' 
                                                data-placement='top'
                                                data-original-title='Profile Id: {{$data->gologin}}'

                                                ><i class="fa fa-google"></i></button>
                                            <!--                                            <button
                                                                                            class="m-r-10 btn btn-success waves-effect waves-light btn-sm btn-ssm m-b-5 btn-copy-gologin"
                                                                                            data-profile="{{$data->gologin}}"
                                                                                            data-toggle='tooltip' 
                                                                                            data-placement='top'
                                                                                            data-original-title='Copy profile Id'
                                                                                            
                                                                                            ><i class="fa fa-copy"></i></button>-->
                                            @if($data->gologin!=null)
                                            <!--                                            <button data-gologin="{{$data->gologin}}" data-gmail="{{$data->note}}"
                                                                                            class="m-r-10 btn btn-success waves-effect waves-light btn-sm btn-ssm m-b-5 btn-sync-gologin"
                                                                                            data-toggle='tooltip' data-placement='top'
                                                                                            data-original-title='Commit MoonShots'><i
                                                                                                class="fa fa-upload"></i></button>-->
                                            <a  target="_blank" href="AutoProfile://profile/commit/?id={{$data->gologin}}&gmail={{$data->note}}&force=1"
                                                class="m-r-10 btn btn-success waves-effect waves-light btn-sm btn-ssm m-b-5 "
                                                data-toggle='tooltip' data-placement='top'
                                                data-original-title='Commit MoonShots'><i
                                                    class="fa fa-upload"></i></a>

                                            @endif
                                            @if($data->chanel_id==null || App\Common\Utils::containString($data->chanel_id,'@'))
                                            <button type="button"
                                                    class="m-r-10 btn btn-danger waves-effect m-b-5 m-r-5 btn-sm btn-ssm "
                                                    data-type="1" data-container="body" data-toggle="popover"
                                                    data-placement="top" data-html="true"
                                                    data-content='<input placeholder="Channel Id" class="form-control m-b-5 channel_id_{{$data->id}}" type="text" name="txt_channel_id"><button type="button" class="btn btn-sm btn-ssm btn-success waves-effect m-b-5 " onclick="updateChannelId({{$data->id}})" data-id={{$data->id}} >Submit</button>'>

                                                <i class="fa fa-folder"></i>
                                            </button>
                                            @endif
                                            @if($data->otp_key==null || $data->is_add_otp==0)
                                            <button type="button"
                                                    class="m-r-10 btn btn-danger waves-effect m-b-5 m-r-5 btn-sm btn-ssm "
                                                    data-type="1" data-container="body" 
                                                    data-toggle="popover"
                                                    data-placement="top" data-html="true"
                                                    data-content='<input placeholder="opt key" class="form-control m-b-5 otp_key_{{$data->id}}" type="text" name="otp_key"><button type="button" class="btn btn-sm btn-ssm btn-success waves-effect m-b-5 input_otp_key" onclick="insertOtpKey({{$data->id}})" data-id={{$data->id}} >Submit</button>'>

                                                <i class="fa fa-key"></i>
                                            </button>
                                            @endif
                                            @if($data->otp_key!=null)
                                            <button
                                                class="m-r-10 btn btn-success waves-effect waves-light btn-sm btn-ssm m-b-5 btn-getcode"
                                                value="{{$data->hash_pass}}" data-toggle='tooltip' data-placement='top'
                                                data-original-title='Get Code login'><i class="ion-code"></i></button>
                                            <button
                                                class="m-r-10 btn btn-success waves-effect waves-light btn-sm btn-ssm m-b-5 btn-getcode-recovery"
                                                value="{{$data->hash_pass}}" data-toggle='tooltip' data-placement='top'
                                                data-original-title='Get Code Recovery'><i class="ion-code-working"></i></button>

                                            @endif
                                            @if($data->expire_vip_render > time())
                                            <button
                                                class="m-r-10 btn btn-success waves-effect waves-light btn-sm btn-ssm m-b-5 btn-vip-render"
                                                value="{{$data->chanel_id}}" data-toggle='tooltip' data-placement='top'
                                                data-original-title='Set render priority'><i class="fa fa-flash"></i>
                                                Render</button>
                                            @endif
                                            <button
                                                class="m-r-10 btn btn-success waves-effect waves-light btn-sm btn-ssm m-b-5 btn-recheck-channel"
                                                value="{{$data->id}}" data-toggle='tooltip' data-placement='top'
                                                data-original-title='Check status channel'><i
                                                    class="fa fa-check"></i></button>
                                                <?php
                                                $success = "success";
                                                $sync = "Sync";
                                                $upload = "upload";
                                                $tooltip = "Sync channel to athena";
                                                $disable = '';
                                                if ($data->is_sync == 1) {
//                                                        if (in_array('1', explode(",", $user_login->role))) {
//                                                            $disable = '';
//                                                        } else {
//                                                            $disable = 'disabled';
//                                                        }
                                                    $success = "danger";
                                                    $sync = "UnSync";
                                                    $upload = "download";
                                                    $tooltip = "UnSync channel from athena";
                                                } else if ($data->is_sync == 2) {
                                                    $success = "warning";
                                                    $tooltip = "Admin is checking to sync to athena";
                                                    $sync = "Checking";
                                                    $upload = "download";
                                                } else if ($data->is_sync == 3) {
                                                    $success = "danger";
                                                    $sync = "Sync";
                                                    $tooltip = "Admin is reject by " . $data->message;
                                                    $upload = "upload";
                                                }
                                                ?>

<!--                                            <button id="btn-sync-athena-{{$data->id}}" {{$disable}}
                                                    class="m-r-10 btn btn-{{$success}}  waves-effect waves-light btn-sm btn-ssm m-b-5 btn-sync-athena"
                                                    value="{{$data->id}}" data-toggle='tooltip' data-placement='top'
                                                    data-original-title='{{$tooltip}}'>
                                                <i class="fa fa-cloud-{{$upload}}"></i></button>-->
                                            <?php
                                            $brand = "Brand";
                                            $disable = "";
                                            $color = "";
                                            $tooltip = "";
                                            if ($data->is_rebrand == 1) {
                                                $brand = "Branding";
                                                $disable = 'disabled';
                                                $color = "btn-h";
                                                $tooltip = "Running bas_id $data->bas_id";
                                            } else if ($data->is_rebrand == 5) {
                                                $brand = "Branded";
                                                $disable = '';
                                                $color = "btn-s";
                                                $tooltip = "Brand successful";
                                            } else if ($data->is_rebrand == 4) {
                                                $brand = "Brand Err";
                                                $disable = '';
                                                $color = "btn-r";
                                                $tooltip = "$data->bas_id$data->gmail_log";
                                            } else if ($data->is_rebrand == 0) {
                                                $tooltip = "$data->gmail_log";
                                            }
                                            ?>
                                            <button id="btn-brand-{{$data->id}}" {{$disable}}
                                                    channelName="{{$data->chanel_name}}"
                                                    class="m-r-10 btn btn-success {{$color}} waves-effect waves-light btn-sm btn-ssm m-b-5 btn-brand"
                                                    value="{{$data->id}}" data-toggle='tooltip' data-placement='top'
                                                    data-original-title='{{$tooltip}}'>
                                                <i class="fa fa-image"></i> {{$brand}}</button>
                                        </div>

                                    </td>
                                    <!--BOOM,BOOM_LYRICS_A,BOOM_TIMER,BOOM,BOOM_LYRICS_A,BOOM_TIMER-->
                                    <td >
                                        <div style="display: inline-grid" >
                                            @foreach($data->tags_array as $tag)
                                            <span class="badge badge-success m-r-5">{{$tag}}</span>
                                            @endforeach
                                        </div>
                                        @if($data->bas_new_status==4)
                                        <br>
                                        <?php
                                        $l = ($data->gmail_log != null && $data->gmail_log != "") ? json_decode($data->gmail_log) : null;
                                        if ($l != null) {
                                            echo str_replace("WAS_ERROR:", "", $l->result);
                                        }
                                        ?>
                                        @endif

                                    </td>
                                    <td>
                                        <div style="display: inline-grid" >
                                            @if($data->is_card ==1)
                                            <span data-toggle='tooltip' data-placement='top'
                                                  data-original-title='{{$data->count_card}} videos on this channel are already carded and endscreen' class="badge badge-danger m-r-5">Card & Enscreen {{$data->count_card}} </span>
                                            @endif
                                            @if($data->gologin == null)
                                            <span class="badge badge-success m-r-5">Bas</span>
                                            @else
                                            <span class="badge badge-success m-r-5">Moonshots</span>
                                            @endif

                                            @if($data->is_music_channel == 1)
                                            <span class="badge badge-success m-r-5">Manual</span>
                                            @elseif($data->is_music_channel == 2)
                                            <span class="badge badge-success m-r-5">Auto</span>
                                            @endif
                                            @if($data->upload_type == 1)
                                            <span class="badge badge-success m-r-5">Upload Manual</span>
                                            @elseif($data->upload_type == 2)
                                            <span class="badge badge-success m-r-5">Upload Auto 
                                                @if($data->version == 2)
                                                (Api)
                                                @else
                                                (Other)
                                                @endif
                                            </span>
                                            @endif
                                            @if($data->wakeup_type == 1)
                                            <span class="badge badge-success m-r-5">ManualWake</span>
                                            @elseif($data->wakeup_type == 2)
                                            <span class="badge badge-success m-r-5">AutoWake</span>
                                            @endif
                                        </div>

                                    </td>
                                    <td>
                                        {{number_format($data->increasing, 0, ',', '.')}}
                                        <?php
                                        if ($data->inc_percent > 0) {
                                            echo " <span class='font-13 color-green'><i class='ion-arrow-up-b'></i> $data->inc_percent%</span>";
                                        }elseif($data->inc_percent < 0){
                                            echo " <span class='font-13 color-red'><i class='ion-arrow-down-b'></i> $data->inc_percent%</span>";
                                        }
                                        ?>
                                    </td>
                                    <td data-toggle='tooltip' data-placement='top' data-original-title='{{$data->inc_time}}'> 
                                        {{number_format($data->view_count, 0, ',', '.')}}
                                        <?php
                                            echo "<br> <span class='text-muted font-12'>" .$data->inc_time."</span>";
                                        ?>
                                    </td>
                                    <td>
                                        @if($data->turn_off_hub)
                                        Off
                                        @else
                                        On
                                        @endif
                                    </td>
                                    <td>
                                        <?php
                                        echo $data->chanel_create_date != null ? gmdate('d/m/Y', $data->chanel_create_date + $user_login->timezone * 3600) : "";
                                        if ($data->last_change_pass > 0) {
                                            echo "<br><span class='text-muted font-12'>Changed pass</span><br> <span class='text-muted font-12'>" . App\Common\Utils::calcTimeText($data->last_change_pass)."</span>";
                                        }
                                        ?>
                                    </td>
                                    <td>{{number_format($data->subscriber_count, 0, ',', '.')}}</td>
                                    <td style="text-align: center">
                                        @if($data->confirm_time!=null)
                                        {{App\Common\Utils::convertToViewDate($data->confirm_time)}}
                                        @endif
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
        </div>
    </div>
    @include('dialog.card_endscreen')
</form>

@include('dialog.card_management')
@include('dialog.groupchannel')
@include('dialog.brand')
@include('dialog.realtimeviews')
@include('dialog.description_edit')
@include('dialog.channel.channeladd')
@include('dialog.channel.channelcreate')

@endsection

@section('script')
<script type="text/javascript">
   
    let mockData = [-1];
    $("#group_channel_search").val({{$request->c3}});
    let multipleSelect = false; // true: chọn nhiều nhóm, false: chọn 1 nhóm
    let selectedGroups = [{{$request->c3}}];

    function fetchGroups() {
        $.ajax({
            url: "/ajaxListGroupChannel",
            type: "GET",
            dataType: "json",
            success: function (response) {
                logger("response",response);
                mockData = response.map(item => ({
                    id: item.id,
                    name: item.group_name,
                    username: item.user_name,
                    channels: item.channels
                }));
                logger("mockData",mockData);
                loadGroups();
   
            },
            error: function () {
                alert("Lỗi khi tải dữ liệu nhóm!");
            }
        });
    }

    function loadGroups() {
        logger("run","loadGroups");
        let listHtml = '<li data-id="-1" class=""><span class="group-name">Select Group</span></li><li data-id="0" class=""><span class="group-name">NO_GROUP</span></li>';
        mockData.forEach(group => {
            let isSelected = selectedGroups.includes(group.id);
            listHtml += `
                <li data-id="${group.id}" class="${isSelected ? 'selected' : ''}">
                    <span class="group-name">${group.name}</span>

                    <span class="font-12 m-r-5">${group.username}</span>
                    <span class="font-12 m-r-5 font-italic w-80px text-center">${group.channels} channels</span>
                        
                    <div class="action-icons">
                        <i class="fa fa-solid fa-pencil-square-o edit-btn"></i>
                        <i class="fa fa-solid fa-trash delete-btn"></i>
                        ${isSelected ? '<i class="fa fa-solid fa-check"></i>' : ''}
                    </div>
                </li>`;

        });
        $("#sortableList").html(listHtml);
        updateDropdownText();

    }

    fetchGroups(); // Gọi API khi trang tải



    // Mở dropdown khi click vào button
    $("#dropdownMenuButton").on("click", function (event) {
        event.stopPropagation();
        $(".select-dropdown-menu").toggle();
    });

    // Đóng dropdown khi click ra ngoài
    $(document).on("click", function (event) {
        if (!$(event.target).closest(".select-dropdown-container, .jconfirm-box").length) {
            if (!multipleSelect) {
                $(".select-dropdown-menu").hide();
            }
        }
    });

//    $(".select-dropdown-menu").on("scroll", function () {
//           $("#searchBox").css("top", $(this).scrollTop() + "px");
//    });

    // Cập nhật tên nhóm đã chọn trên button
    function updateDropdownText() {
        let selectedNames = mockData
            .filter(group => selectedGroups.includes(group.id))
            .map(group => group.name)
            .join(", ");
        $("#dropdownMenuButton").text(selectedNames || "Select Group");
    }

    // Chọn nhóm
    $(document).on("click", ".sortable-list li", function () {
        let id = $(this).data("id");

        if (multipleSelect) {
            if (selectedGroups.includes(id)) {
                selectedGroups = selectedGroups.filter(gid => gid !== id);
            } else {
                selectedGroups.push(id);
            }
        } else {
            selectedGroups = [id];
            $("#group_channel_search").val(id);
            $(".select-dropdown-menu").hide();
        }

        loadGroups();
        logger("Nhóm đã chọn:", selectedGroups);
    });

    // Sửa nhóm
    $(document).on("click", ".edit-btn", function (event) {
        event.stopPropagation();
        let li = $(this).closest("li");
        let id = li.data("id");
        let name = li.find(".group-name").text();

        $.confirm({
            title: "Update Group",
            content: '<input type="text" id="editGroupName" value="' + name + '" class="form-control">',
            buttons: {
                "Cancel": function () {},
                "Update": {
                    btnClass: "btn-blue",
                    action: function () {
                        let newName = this.$content.find("#editGroupName").val().trim();
                        if (newName && newName !== name) {
                            mockData = mockData.map(group => group.id === id ? {...group, name: newName} : group);
                            loadGroups();
                        }
                        $.ajax({
                        url: "/ajaxUpdateGroupChannel",
                        method: "GET",
                        data: { id: id ,group_name:newName},
                        success: function (response) {
                            logger("rp",response);
                        },
                        error: function (xhr, status, error) {
                            logger("Lỗi khi xóa:", error);
                        }
                    });
                    }
                }
            }
        });
    });

    // Xóa nhóm
    $(document).on("click", ".delete-btn", function (event) {
        event.stopPropagation();
        let groupId = $(this).closest("li").data("id");
        $.confirm({
            animation: 'rotateXR',
            title: "Confirm",
            content: "Are you sure you want to delete this group?",

            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-red',
                    action: function () {
                        $.ajax({
                            url: "/ajaxDelGroupChannel",
                            method: "GET",
                            data: { id: groupId },
                            success: function (response) {
                               logger("Del:", response);
                                $(`li[data-id='${groupId}']`).remove();
                            },
                            error: function (xhr, status, error) {
                                logger("error:", error);
                            }
                        });
                    }
                },
                cancel: function () {

                }

            }
        });
    });


    $("#sortableList").sortable({
        update: function (event, ui) {
            // Lấy danh sách ID theo thứ tự mới
            let order = [];
            $("#sortableList li").each(function () {
                order.push($(this).data("id"));
            });

            // Gửi thứ tự mới lên server
            $.ajax({
                url: "/ajaxUpdateGroupChannel",
                method: "GET",
                data: { order: order },
                success: function (response) {
                    console.log("Cập nhật thành công:", response);
                },
                error: function (xhr, status, error) {
                    console.error("Lỗi khi cập nhật:", error);
                }
            });
        }
    });

    // Cho phép sắp xếp được,không kéo thả vào text
    $("#sortableList").disableSelection();

    // Tìm kiếm nhóm
    $("#searchBox").on("keyup", function () {
        let value = $(this).val().toLowerCase();
        $("#sortableList li").each(function () {
            let text = $(this).find(".group-name").text().toLowerCase();
            $(this).toggle(text.includes(value));
        });
    });












    $(".btn-add-channel").click(function () {
        $("#dialog_channel_add").modal("show");
    });
    $(".btn-create-channel").click(function () {
        $("#dialog_create_add").modal("show");
        $.ajax({
            type: "GET",
            url: "/genEmailInfo",
            data: {},
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $("#email_id").val("");
                $("#automail_id").val("");
                $("#fake_email").val(data.last_full_email);
                $("#fake_name").val(data.name);
                $("#fake_recovery").val(data.recovery);
                $("#fake_phone").val(data.phone);
                $("#fake_pass").val(data.pass);
                $("#fake_birth").val(data.birthday);
                $("#btn-create-email").html('<i class="fa fa-save"></i> Save');
                $("#btn-open-brower").hide();
                $("#btn-open-brower").attr("hash", "");
            },
            error: function (data) {
                $(element).html($(element).data('original-text'));

            }
        });
    });
    $("#btn-open-brower").click(function () {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var hash = $(this).attr('hash');
        goLogin(hash);
        setTimeout(function () {
            $this.html($this.data('original-text'));
        }, 2000);
    });
    function createEmail(element) {
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(element).html() !== loadingText) {
            $(element).data('original-text', $(element).html());
            $(element).html(loadingText);
        }
        var form = $("#formCreateEmail").serialize();
        $.ajax({
            type: "GET",
            url: "/createEmail",
            data: form,
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $(element).html($(element).data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                if (data.status === "success") {
                    $(element).html('<i class="fa fa-edit"></i> Update');
                    $("#email_id").val(data.data.id);
                    $("#automail_id").val(data.data.api_job_id);
                    $("#btn-open-brower").show();
                    $("#btn-open-brower").attr("hash", data.data.hash_pass);
                }
            },
            error: function (data) {
                $(element).html($(element).data('original-text'));
            }
        });
    }

    function addChannel(element) {
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(element).html() !== loadingText) {
            $(element).data('original-text', $(element).html());
            $(element).html(loadingText);
        }
        var form = $("#formAddChannel").serialize();
        $.ajax({
            type: "GET",
            url: "/addChannel",
            data: form,
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $(element).html($(element).data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                if (data.status == "success") {
                    location.reload();
                }
            },
            error: function (data) {
                $(element).html($(element).data('original-text'));

            }
        });
    }

    $(".copy_profile_id").click(function () {
        var profileId = $(this).attr("data-profile");
        navigator.clipboard.writeText(profileId);
        $.Notification.notify("success", 'top center', '', 'Copied: ' + profileId);
    });

    $("#btnDesEdit").click(function () {
        $('#dialog_description_edit').modal({
            backdrop: true
        });
    });

    $(".view-realtime-chart").click(function () {
        $("#dialog_realtime_view_loading").show();
        $("#table-chart").html("");
        $("#chartHour-wrap").html("");
        $("#chartMinute-wrap").html("");
        var channel_id = $(this).attr("data-channel");
        var channel_name = $(this).attr("data-name");
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        $.ajax({
            type: "GET",
            url: "getDataChart",
            data: {
                "channel_id": channel_id
            },
            dataType: 'json',
            success: function (data) {
                //                console.log(data);
                $this.html($this.data('original-text'));
                $("#dialog_realtime_view_loading").hide();
                $("#chartHour-wrap").html('<canvas id="chartHour"></canvas>');
                $("#chartMinute-wrap").html('<canvas id="chartMinute"></canvas>');
                drawBarChart("chartHour", "Views · Last 48 hours", data.data48hour.label, data
                        .data48hour.value);
                drawBarChart("chartMinute", "Views · Last 60 minutes", data.data60minutes.label, data
                        .data60minutes.value);
                $("#last-48-hour").html(number_format(data.data48hour.total48, 0, ',', '.'));
                $("#last-60-minute").html(number_format(data.data60minutes.total60, 0, ',', '.'));
                $("#tbl-channel-info").html('<tr><td><img class="video-img_thumb" src="' + data
                        .channel_thumb + '">' + channel_name + '</td><td>Subs: ' + data.subs +
                        '</td><td>Level: ' + data.level + '</td><td>Rate 48h: ' + data.viewRate42 +
                        '</td><td>Rate 6h: ' + data.viewRate6 + '</td><td>View Avg 6h: ' + data
                        .viewAvg + '</td><td>Last Sync: ' + data.last_sync +
                        '</td><td><i class="fa color-green fa-circle heart"></i> Updating</td></tr>');
                $("#table-chart").html(
                        '<tr><th>Content</th><th>Published</th><th colspan="2" style="text-align: center">Last 48 hours</th><th colspan="2" style="text-align: center">Last 60 minutes</th></tr>'
                        );
                var html = '';
                $.each(data.topVideos, function (key, value) {
                    i = i + 1;
                    html = '<tr><td><a target="_blank" href="https://www.youtube.com/watch?v=' +
                            value.video_id +
                            '"><img class="video-img_thumb" src="https://i.ytimg.com/vi/' + value
                            .video_id + '/default.jpg">' + value.video_title + '</a></td><td>' +
                            value.published + '</td><td><span>' + number_format(value
                                    .total_view_hour, 0, ',', '.') +
                            '</span></td><td><div><canvas id="chartHourMini' + key +
                            '"></canvas></div></td><td><span>' + number_format(value
                                    .total_view_minute, 0, ',', '.') +
                            '</span></td><td><div><canvas id="chartMinuteMini' + key +
                            '"></canvas></div></td></tr>';
                    $("#table-chart").append(html);
                    var ratio48 = value.total_view_hour / data.maxViewTopVideoHour * 100;
                    if (ratio48 < 25) {
                        ratio48 = 25;
                    }
                    drawBarChartMini("chartHourMini" + key, "48", value.times_hour, value
                            .views_hour, ratio48);
                    ratio48 = value.total_view_minute / data.maxViewTopVideoMinute * 100;
                    if (ratio48 < 25) {
                        ratio48 = 25;
                    }
                    drawBarChartMini("chartMinuteMini" + key, "60", value.times_minute, value
                            .views_minute, ratio48);
                });
                $('#dialog_realtime_view').modal({
                    backdrop: true
                });

            },
            error: function (data) {
                console.log('Error:', data);
                $this.html($this.data('original-text'));
            }
        });
    });

    function viewChart(channel_id) {
        //        $("#content-dialog").html("");

    }

    function insertOtpKey(id) {
        var otpkey = $(".otp_key_" + id).val();
        console.log(id, otpkey);
        $.ajax({
            type: "GET",
            url: "insertOtpKey",
            data: {
                "id": id,
                "otpkey": otpkey
            },
            dataType: 'json',
            success: function (data) {
                console.log(data);
                if (data.status == "error") {
                    $.Notification.notify(data.status, 'top center', '', data.message);
                } else {
                    copyToClipboard(data.data);
                }

            },
            error: function (data) {
                console.log('Error:', data);


            }
        });
    }
    
    function updateChannelId(id) {
        var channelId = $(".channel_id_" + id).val();

        $.ajax({
            type: "GET",
            url: "updateChannelId",
            data: {
                "id": id,
                "channel_id": channelId
            },
            dataType: 'json',
            success: function (data) {
                $.Notification.notify(data.status, 'top center', '', data.message);

            },
            error: function (data) {
                console.log('Error:', data);


            }
        });
    }


    $('.btn-gologin').click(function (e) {
        e.preventDefault();
//    var $this = $(this);
//    var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
//    if ($(this).html() !== loadingText) {
//        $this.data('original-text', $(this).html());
//        $this.html(loadingText);
//    }
        var id = $(this).val();
        goLogin(id);
    });

    function goLogin(id) {
        $.get('goLogin?hash=' + id, function (data) {
            logger('goLogin', data);
//        $this.html($this.data('original-text'));
            if (data.gologin == null) {
                $.Notification.notify("error", 'top center', '', "Got error, Gologin Id null");
            } else {
                navigator.clipboard.writeText(data.gologin);
                window.open(`AutoProfile://profile/login/?id=${data.gologin}&gmail=${data.note}&hash_id=${data.hash_pass}&startup_url=https://www.youtube.com/channel_switcher`, "_blank");
//            location.reload();
            }
        });
    }

    $('.btn-copy-gologin').click(function (e) {
        e.preventDefault();
        var profile = $(this).attr("data-profile");
        navigator.clipboard.writeText(profile);
        $.Notification.notify("error", 'top center', '', "Copied " + profile);
    });

    $('.btn-getcode').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var id = $(this).val();
        $.get('getCodeLogin?hash=' + id, function (data) {
            $this.html($this.data('original-text'));
            if (data.status == "error") {
                $.Notification.notify("error", 'top center', '', "Got error");
            } else {
                //                $.Notification.notify("success", 'top center', '', data.data);
                copyToClipboard(data.data);
            }

        });
    });
    
    $('.btn-getcode-recovery').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var id = $(this).val();
        $.get('getCodeRecovery?hash=' + id, function (data) {
            $this.html($this.data('original-text'));
            if (data.status == "error") {
                $.Notification.notify("error", 'top center', '', "Got error");
            } else {
                //                $.Notification.notify("success", 'top center', '', data.data);
                copyToClipboard(data.data);
            }

        });
    });

    function randomAboutSection() {
        var genre = $("#channel_genre").val();
        if (genre === '-1') {
            $.Notification.notify('error', 'top center', '', "Please choose Genre first");
            return;
        }
        $.ajax({
            type: "GET",
            url: "randomAboutSection",
            data: {
                "genre": genre
            },
            dataType: 'json',
            success: function (data) {
                if (data.length > 0) {
                    $("#aboutSectionId").val(data[0].id);
                    $("#about_section").val(data[0].content);
                    $("#about_section_use").html(' (Used ' + data[0].count + ' times)');
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }

    $("#brand_select").change(function () {
        var data = atob($(this).val()).split("@;@");
        $("#firstName").val(data[3]);
        $("#channel_genre").val(data[4]);
        $("#channel_subgenre").val(data[7].split(','));
        $("#brand_user").val(data[2]);
        $("#profile").val(data[5]);
        $("#banner").val(data[6]);
        $("#avatarView").attr("src", data[5]);
        var css = "url('" + data[6] + "')";
        $(".banner-view").css({
            "background-image": css
        });
    });

    $("#channel_genre").change(function () {

        //        $.ajax({
        //            type: "GET",
        //            url: "getSubgenre",
        //            data: {"genre": $(this).val()},
        //            dataType: 'text',
        //            success: function (data) {
        //                ("#channel_genre").html(data);
        //
        //            },
        //            error: function (data) {
        //                console.log('Error:', data);
        //
        //            }
        //        });
    });

    $(".btn-vip-render").click(function (e) {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }

        e.preventDefault();
        $.ajax({
            type: "GET",
            url: "vipRender",
            data: {
                "channel_id": $this.val()
            },
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);

            },
            error: function (data) {
                console.log('Error:', data);
                $.Notification.notify('error', 'top center', '', "Error");
                $this.html($this.data('original-text'));

            }
        });
    });

    $(".btn-set-info").click(function (e) {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var form = $("#frmBrand");
        var formData = form.serialize();
        console.log(formData);
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "saveChannelInfo",
            data: formData,
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);

            },
            error: function (data) {
                console.log('Error:', data);

            }
        });
    });

    $(".btn-save-brand").click(function (e) {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var form = $("#frmBrand");
        var formData = form.serialize();
        console.log(formData);
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "saveBrandChannel",
            data: formData,
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
                setTimeout(function () {
                    location.reload();
                }, 2000);
            },
            error: function (data) {
                console.log('Error:', data);

            }
        });
    });

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
            $.Notification.notify('error', 'top center', '', "You must choose a image");
            return;
        }

        form_file.append('image', $("#" + type + "_upload")[0].files[0]);
        form_file.append('_token', '{{csrf_token()}}');
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/image-upload",
            data: form_file,
            contentType: false,
            processData: false,
            success: function (data) {
                $this.html($this.data('original-text'));
                console.log(data);
                if (data.status == 'success') {
                    var url = "http://automusic.win/" + data.uploaded_image;
                    $(".channel-name-view").html($("#firstName").val());
                    if (type == "banner") {
                        $("#banner").val(url);
                        var css = "url('http://automusic.win/" + data.uploaded_image + "')";
                        $(".banner-view").css({
                            "background-image": css
                        });
                    } else {
                        $("#profile").val(url);
                        $("#avatarView").attr("src", 'http://automusic.win/' + data.uploaded_image);
                    }

                } else {
                    $.Notification.notify(data.status, 'top center', '', data.message[0]);
                }
            },
            error: function (data) {
                but.button('reset');
            }
        });
    });

    $(".btn-brand").click(function (e) {
        e.preventDefault();
        $("#idBrand").val($(this).val());
        $("#aboutSectionId").val('');
        $("#title-brand").html("Branding " + $(this).attr("channelName"));
        $(".modal-dialog").addClass("modal-80");
        $('#dialog_brand_channel').modal({
            backdrop: false
        });

        $.ajax({
            type: "GET",
            url: "loadChannelInfo",
            data: {
                "id": $(this).val()
            },
            dataType: 'json',
            success: function (data) {
                $("#brand_user").html(data.brandOwner);
                $("#channel_type").html(data.channelType);
            },
            error: function (data) {
                console.log('Error:', data);

            }
        });


    });

    $('.btn-sync-athena').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var id = $(this).val();
        $.get('ajaxSyncAthena/' + id, function (data) {
            $this.html($this.data('original-text'));
            $.Notification.notify(data.status, 'top center', '', data.content);
            $("#btn-sync-athena" + id).html('<i class="fa fa-cloud-' + data.btnIcon + '"></i> ' + data
                    .btnText);
            $("#btn-sync-athena" + id).attr("data-original-title", data.btnTooltip);
            $("#btn-sync-athena" + id).removeClass("btn-danger");
            $("#btn-sync-athena" + id).removeClass("btn-success");
            $("#btn-sync-athena" + id).addClass("btn-" + data.btnColor);
//        setTimeout(function() {
//            location.reload();
//        }, 2000);

        });
    });

    $('.btn-recheck-channel').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var id = $(this).val();
        $.get('ajaxRecheckChannel/' + id, function (data) {
            $this.html($this.data('original-text'));
            $.Notification.notify(data.status, 'top center', '', data.content[0]);
        });
    });

    $('.btn-sync-gologin').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var gologin = $this.attr("data-gologin");
        var gmail = $this.attr("data-gmail");
        $.Notification.confirm('warning', 'top center', 'Are you sure ?', 'boom');
        $(document).on('click', '.notifyjs-metro-base .boom_yes', function () {
            $(this).trigger('notify-hide');
            window.open("AutoProfile://profile/commit/?id=" + gologin + "&gmail=" + gmail + "&force=1", "_blank");

        });
    });

    $("#action").change(function () {

    });

    $('#btnExcute').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        if ($("#action").val() == '0') {
            $this.html($this.data('original-text'));
            $.Notification.notify('error', 'top center', '', 'Choose one action');
            return;
        }
        var formChannel = $("#formChannel").serialize();
        $.ajax({
            type: "POST",
            url: "/ajaxChannel",
            data: formChannel,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                for (var i = 0; i < data.content.length; i++) {
                    $.Notification.notify(data.status, 'top center', '', data.content[i]);
                    notify(data.content[i], "https://automusic.win/channelmanagement", "");

                }
                //                location.reload();
            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });

    $('#btnCloseDialogGroupChannel').click(function () {
        $("#data-table-group-channel").DataTable().clear().destroy();
        $.ajax({
            type: "GET",
            url: '/ajaxGetGroupChannel',
            dataType: 'json',
            success: function (data) {
                var option = '<option value="-1" ><?php echo trans('label.value.select'); ?></option>';
                if (data !== null && data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        option += '<option value="' + data[i].id + '">' + data[i].group_name +
                                '</option>';

                    }
                    $('#g_c_add').html(option);
                    $('#group_channel_search').html(option);
                }
            },
            error: function (data) {}
        });

    });

    $("#btnCard").click(function (e) {
        e.preventDefault();
        var selected = [];
        var n = $("input:checked.checkbox-multi").length;
        //    $(' input:checked.checkbox-multi').each(function() {
        //        selected.push($(this).val());
        //    });
        if (n === 0) {
            $.Notification.notify("error", 'top center', '', "You have to choose channel");
            return;
        }
        $('#dialog_card_endscreen').modal({
            backdrop: true
        });

    });
    var id = 0;

    function addCard(type) {
        id = id + 1;
        $(".card-header").addClass("collapsed");
        $(".card-header").attr("aria-expanded", false);
        $(".collapse").removeClass("show");
        var html = '';
        //            html+='<div id="card" class="card">';
        html += `<div id="card${id}" class="card">`;
        html += '    <div class="card-header cur-poiter" role="tab" id="heading' + id +
                '" data-toggle="collapse" data-parent="#accordion" href="#collapse' + id +
                '" aria-expanded="true" aria-controls="collapse' + id + '">';
        html += '        <table class="w-100"><tr><td class="w-50"><h5 class="mb-0 mt-0 font-16"><a >' + type.capitalize() +
                ' Card</a></h5></td>';
        html +=
                `                <td class="w-30"><input name="${type}_time[]" type="text" data-mask="9:99:99" value="0:00:00" class="form-control input-sm pull-right radius-6 w-70px"></td><td><i class="ion-trash-b font-35 pull-right" onclick="deleteCard('card${id}')"></i></td></tr></table>`;
        html += '    </div>';
        html += '    <div id="collapse' + id + '" class="collapse show" role="tabpanel" aria-labelledby="heading' + id +
                '">';
        html += '        <div class="card-body">';
        html += '            <div class="row">';
        html += '                <div class="col-md-12">';
        html += '                    <div class="form-group row">';
        html += '                        <div class="col-12">';
        html +=
                `                            <input class="form-control radius-6" type="text"  name="${type}_link_card[]" placeholder="${type.capitalize()}">`;
        html += '                        </div>';
        html += '                    </div>';
        html += '                </div>';
        html += '                <div class="col-md-12">';
        html += '                    <div class="form-group row">';
        html += '                        <div class="col-12">';
        html +=
                `                            <textarea class="h-50px form-control line-heigh-125 radius-6" rows="5"  name="${type}_message_card[]" placeholder="Custom message 30 characters (${type == 'channel' ? 'require' : 'optional'})" spellcheck="false" maxlength="30"></textarea>`;
        html += '                        </div>';
        html += '                    </div>';
        html += '                </div>';
        html += '                <div class="col-md-12">';
        html += '                    <div class="form-group row">';
        html += '                        <div class="col-12">';
        html +=
                `                            <textarea class="h-50px form-control line-heigh-125 radius-6" rows="5"  name="${type}_intro_card[]" placeholder="Intro content 30 characters (${type == 'channel' ? 'require' : 'optional'})" spellcheck="false" maxlength="30"></textarea>`;
        html += '                        </div>';
        html += '                    </div>';
        html += '                </div>';
        html += '            </div>';
        html += '        </div>';
        html += '    </div>';
        html += '</div>';
        $("#accordion").append(html);
    }

    function deleteCard(id) {
        $("#" + id).remove();
    }
    $("#btnCardSubmit").click(function (e) {

        e.preventDefault();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var formChannel = $("#formChannel").serialize();
        $.ajax({
            type: "POST",
            url: "/addCardEndscreen",
            data: formChannel,
            dataType: 'json',
            success: function (data) {
                $.Notification.notify(data.status, 'top center', '', data.message);
                $this.html($this.data('original-text'));
                console.log(data);
            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });
    Object.defineProperty(String.prototype, 'capitalize', {
        value: function () {
            return this.charAt(0).toUpperCase() + this.slice(1);
        },
        enumerable: false
    });

    $(".endsTemplate").click(function () {
        $(".endsTemplate").removeClass("endscreen-template-active");
        $(this).addClass("endscreen-template-active");
        var value = $(this).attr("data-tpl");
        $("#template_encscreen").val(value);
    });

    $("#btnCardManagement").click(function (e) {
        e.preventDefault();
        $('#dialog_card_management').modal({
            backdrop: true
        });
        $("#dialog_card_management_loading").fadeIn();
        $.ajax({
            type: "GET",
            url: "/getCardEndscreen",
            data: {},
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $("#dialog_card_management_loading").fadeOut('fast');
                var i = 0
                if (data.length > 0) {
                    $.each(data, function (key, value) {
                        i = i + 1;
                        var type = 'Card';
                        if (value.type === 2) {
                            type = "Enscreen";
                        }
                        $("#tbl-card").append(`<tr><td>${i}</td><td>${type}</td><td>${value.promo_link}</td><td>${value.total}</td></tr>`);
                    });
                }

            },
            error: function (data) {
            }
        });
    });
</script>
@endsection