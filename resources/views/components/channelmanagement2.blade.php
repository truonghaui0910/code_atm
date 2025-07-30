@extends('layouts.master')

@section('content')
    <link href="css/channel.css?v1.02" rel="stylesheet" type="text/css" />
    <div id="filterPanel" class="filter-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Advanced Filters</h5>
            <button id="closeFilterBtn" class="btn-circle-cus2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                    class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                    <path
                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z">
                    </path>
                </svg>
            </button>
        </div>


        <form id="form-search" action="/channelmanagement/v2">
            <input type="hidden" name="limit" id="limit" value="{{ $limit }}">
            <input id="is_change_info_error" type="hidden" name="is_change_info_error" value="{{$request->is_change_info_error}}"/>
            <input id="is_upload_error" type="hidden" name="is_upload_error" value="{{$request->is_upload_error}}"/>
            <div class="filter-groups-row">
                <!-- Left Column -->
                <div class="col-md-6 filter-group-wrapper">
                    <!-- Group 1: Basic Information -->
                    <div class="filter-group">
                        <div class="filter-group-header" data-toggle="collapse" data-target="#basicInfoGroup">
                            <h6 class="filter-group-title">
                                <i class="fas fa-info-circle"></i> Basic Information
                            </h6>
                        </div>
                        <div class="filter-group-content collapse show" id="basicInfoGroup">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">ID/Name/Email</label>
                                        <div class="col-12">
                                            <input id="search_channel" class="form-control" type="text" name="c1"
                                                value="{{ $request->c1 }}" placeholder="Search ID, name or email...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Status</label>
                                        <div class="col-12">
                                            <select id="search_status" class="form-control" name="c2">
                                                {!! $status !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                @if ($is_admin_music)
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-6 col-form-label">User</label>
                                            <div class="col-12">
                                                <select id="user_search" class="form-control search_select" name="c5"
                                                    data-show-subtext="true" data-live-search="true">
                                                    {!! $listusercode !!}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <label class="col-12 col-form-label">Group <span class="btn_add_group_channel"><i
                                                class="fa fa-plus-circle color-red"
                                                style="font-size: 20px;"></i></span></label>
                                    <div id="group_channel_1" class="select-dropdown-container"></div>
                                    <!--                                    <div class="select-dropdown-container">
                                                            <button class="btn dropdown-toggle btn-dropdown-group" type="button" id="dropdownMenuButton">
                                                                Select Group
                                                            </button>

                                                            <div class="select-dropdown-menu">
                                                                <input type="text" id="searchBox" class="search-box" placeholder="Search...">
                                                                <ul id="sortableList" class="sortable-list div_scroll_50"></ul>
                                                            </div>
                                                        </div>-->
                                    <input type="hidden" id="group_channel_search" name="c3"
                                        value="{{ $request->c3 }}" />
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="gmail_log">Log</label>
                                    <input id="gmail_log" class="form-control" type="text" name="gmail_log" value="{{$request->gmail_log}}"
                                        placeholder="Enter log information...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Group 2: Content Classification -->
                    <div class="filter-group mt-4">
                        <div class="filter-group-header" data-toggle="collapse" data-target="#contentGroup">
                            <h6 class="filter-group-title">
                                <i class="fas fa-tags"></i> Content Classification
                            </h6>
                        </div>
                        <div class="filter-group-content collapse show" id="contentGroup">
                            <div class="row">


                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-6 col-form-label">Genre</label>
                                        <div class="col-12">
                                            <select class="form-control" name="channel_genre">
                                                {!! $channelGenre !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
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
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Studio</label>
                                        <div class="col-12">
                                            <select id="studio" class="form-control" name="studio">
                                                {!! $studio !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Tags</label>
                                        <div class="col-12">
                                            <select id="tags" name="tags[]" class="select2_multiple form-control "
                                                multiple style="height: 140px">
                                                {!! $channelTags !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
<!--                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Email Frequency</label>
                                        <div class="col-12">
                                            <select id="email_frequency" class="form-control" 
                                                    name="email_frequency[]" multiple
                                                    multiple style="height: 140px">>
                                                <option value="1" {{ in_array('1', $request->email_frequency ?? []) ? 'selected' : '' }}>1 channel</option>
                                                <option value="2" {{ in_array('2', $request->email_frequency ?? []) ? 'selected' : '' }}>2 channels</option>
                                                <option value="3" {{ in_array('3', $request->email_frequency ?? []) ? 'selected' : '' }}>3 channels</option>
                                                <option value="4" {{ in_array('4', $request->email_frequency ?? []) ? 'selected' : '' }}>4 channels</option>
                                                <option value="5" {{ in_array('5', $request->email_frequency ?? []) ? 'selected' : '' }}>5 channels</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>-->
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Email Frequency</label>
                                        <div class="col-12">
                                            <div class="email-frequency-container">
                                                <div class="frequency-option">
                                                    <input type="checkbox" id="freq_1" name="email_frequency[]" value="1" 
                                                           {{ in_array('1', $request->email_frequency ?? []) ? 'checked' : '' }}>
                                                    <label for="freq_1" class="frequency-label">
                                                        <span class="frequency-number">1</span>
                                                        <span class="frequency-text">channel</span>
                                                    </label>
                                                </div>
                                                <div class="frequency-option">
                                                    <input type="checkbox" id="freq_2" name="email_frequency[]" value="2"
                                                           {{ in_array('2', $request->email_frequency ?? []) ? 'checked' : '' }}>
                                                    <label for="freq_2" class="frequency-label">
                                                        <span class="frequency-number">2</span>
                                                        <span class="frequency-text">channels</span>
                                                    </label>
                                                </div>
                                                <div class="frequency-option">
                                                    <input type="checkbox" id="freq_3" name="email_frequency[]" value="3"
                                                           {{ in_array('3', $request->email_frequency ?? []) ? 'checked' : '' }}>
                                                    <label for="freq_3" class="frequency-label">
                                                        <span class="frequency-number">3</span>
                                                        <span class="frequency-text">channels</span>
                                                    </label>
                                                </div>
                                                <div class="frequency-option">
                                                    <input type="checkbox" id="freq_4" name="email_frequency[]" value="4"
                                                           {{ in_array('4', $request->email_frequency ?? []) ? 'checked' : '' }}>
                                                    <label for="freq_4" class="frequency-label">
                                                        <span class="frequency-number">4</span>
                                                        <span class="frequency-text">channels</span>
                                                    </label>
                                                </div>
                                                <div class="frequency-option">
                                                    <input type="checkbox" id="freq_5" name="email_frequency[]" value="5"
                                                           {{ in_array('5', $request->email_frequency ?? []) ? 'checked' : '' }}>
                                                    <label for="freq_5" class="frequency-label">
                                                        <span class="frequency-number">5</span>
                                                        <span class="frequency-text">channels</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
</div>

                            </div>
                        </div>
                    </div>


                </div>

                <!-- Right Column -->
                <div class="col-md-6 filter-group-wrapper">
                    <!-- Group 4: Management Methods -->
                    <div class="filter-group">
                        <div class="filter-group-header" data-toggle="collapse" data-target="#methodsGroup">
                            <h6 class="filter-group-title">
                                <i class="fas fa-sliders-h"></i> Management Methods
                            </h6>
                        </div>
                        <div class="filter-group-content collapse show" id="methodsGroup">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Manage Type</label>
                                        <div class="col-12">
                                            <select class="form-control" name="c6">
                                                {!! $channelManageType !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Upload Type</label>
                                        <div class="col-12">
                                            <select class="form-control" name="c7">
                                                {!! $channelUploadType !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Wakeup Type</label>
                                        <div class="col-12">
                                            <select class="form-control" name="c8">
                                                {!! $channelWakeupType !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
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
                            </div>
                        </div>
                    </div>

                    <!-- Group 5: System Status -->
                    <div class="filter-group mt-4">
                        <div class="filter-group-header" data-toggle="collapse" data-target="#statusGroup">
                            <h6 class="filter-group-title">
                                <i class="fas fa-server"></i> System Status
                            </h6>
                        </div>
                        <div class="filter-group-content collapse show" id="statusGroup">
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Hub</label>
                                        <div class="col-12">
                                            <select id="statusHub" class="form-control" name="statusHub">
                                                {!! $statusHubs !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Storm</label>
                                        <div class="col-12">
                                            <select id="level" class="form-control" name="level">
                                                {!! $level !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Is Sync</label>
                                        <div class="col-12">
                                            <select id="level" class="form-control" name="is_sync">
                                                {!! $isSync !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Comment</label>
                                        <div class="col-12">
                                            <select id="status_cmt" class="form-control" name="status_cmt">
                                                {!! $statusCmt !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Tracking</label>
                                        <div class="col-12">
                                            <select id="sub_tracking" class="form-control" name="sub_tracking">
                                                {!! $subTracking !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                @if ($is_admin_music)
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Not update MS</label>
                                            <div class="col-12">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="outofdate_moonshot_stat" class="" type="checkbox"
                                                        name="outofdate_moonshot_stat" value="1"
                                                        {{ $request->moonshot_stat }}>
                                                    <label class=""
                                                        for="outofdate_moonshot_stat">{{ $stats }} channel</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Group 3: Technical Management -->
                    <div class="filter-group mt-4">
                        <div class="filter-group-header" data-toggle="collapse" data-target="#technicalGroup">
                            <h6 class="filter-group-title">
                                <i class="fas fa-cogs"></i> Technical Management
                            </h6>
                        </div>
                        <div class="filter-group-content collapse show" id="technicalGroup">
                            <div class="row">

                                @if ($is_admin_music)
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-6 col-form-label">API</label>
                                            <div class="col-12">
                                                <select class="form-control" name="c4">
                                                    {!! $statusApi !!}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">AutoLogin</label>
                                            <div class="col-12">
                                                <select class="form-control" name="bas_new_status">
                                                    {!! $basNewStatus !!}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Brand</label>
                                        <div class="col-12">
                                            <select id="brand" class="form-control" name="brand">
                                                {!! $brand !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Change Info</label>
                                        <div class="col-12">
                                            <select id="is_changeinfo" class="form-control" name="is_changeinfo">
                                                {!! $isChangeInfo !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Opt</label>
                                        <div class="col-12">
                                            <select id="is_add_otp" class="form-control" name="is_add_otp">
                                                {!! $isUpdateOtp !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Space for Visual Balance -->
                    <div class="mt-4">&nbsp;</div>
                </div>
            </div>

            <div class="d-flex justify-content-start mt-4 ">
                <button id="btnSearch" type="submit" class="btn btn-primary mr-3">
                    <i class="fas fa-filter mr-2"></i> Apply filter
                </button>
                <a href="/channelmanagement/v2" class="btn btn-outline mr-3" id="resetBtn">
                    <i class="fas fa-undo mr-2"></i> Reset
                </a>
            </div>

        </form>

    </div>

    <form id="formChannel" class="form-horizontal form-label-left w-100" method="POST">
        {{ csrf_field() }}

        <div id="actionPanel" class="action-panel">
            <div class="action-panel-header">
                <button id="toggleActionPanelBtn" class="btn-circle-cus2">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="text-center">
                    <h5 class="mb-0">Channel Actions</h5>
                    <div class="selected-counter mr-2 font-14"><span id="selectedCount"></span><span> channel
                            selected</span></div>
                </div>
                <div class="d-flex align-items-center">
                    <!--<div class="selected-counter mr-2" id="selectedCount">0</div>-->
                    <button id="closeActionPanelBtn" class="btn-circle-cus2" title="Close panel">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="action-panel-body div_scroll_50">
                <div class="action-search">
                    <input type="text" class="form-control" id="actionSearch" placeholder="Search actions...">
                </div>

                <div id="actionGroups">
                    <!-- Channel Management -->
                    <div class="action-group" data-category="management">
                        <div class="action-group-title">
                            <i class="fas fa-users"></i> Channel Management
                        </div>
                        <div class="action-btn-group">
                            @if ($is_admin_music)
                                <div class="action-item">
                                    <button class="btn btn-sm btn-outline-secondary action-btn" data-value="2"
                                        data-requires-form="true" data-form="moveChannelForm">Move Channel</button>
                                    <div id="moveChannelForm" class="action-form">
                                        <h6 class="mb-3">Move Channel to User</h6>
                                        <div class="form-group">
                                            <select id="targetUser" class="search_select" name="action_user"
                                                data-show-subtext="true" data-live-search="true" data-size="5"
                                                data-container="body" data-width="100%">
                                                {!! $listusercode !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="action-item">
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="1"
                                    data-requires-form="true" data-form="addGroupForm">Add to Group</button>
                                <div id="addGroupForm" class="action-form">
                                    <h6 class="mb-3">Add Channel to Group <span class="btn_add_group_channel"><i
                                                class="fa fa-plus-circle color-red" style="font-size: 20px;"></i></span>
                                    </h6>
                                    <div class="form-group">
                                        <select id="targetGroup" class="select_group_channel search_select"
                                            name="action_group_channel" data-show-subtext="true" data-live-search="true"
                                            data-size="5" data-container="body" data-width="100%">
                                            {!! $group_channel_search !!}
                                        </select>

                                    </div>
                                </div>
                            </div>
                            <div class="action-item">
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="17"
                                    data-requires-form="true" data-form="configForm">Config</button>
                                <div id="configForm" class="action-form">
                                    <h6 class="form-section-title">Configuration Settings</h6>
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <label for="profileName">Channel Id <span
                                                    class="font-13">(Youtube)</span></label>
                                            <input id="config_channel_id" class="form-control" type="text"
                                                name="config_channel_id">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <label for="genre">Genre</label>
                                            <select class="form-control " id="genre" name="channel_genre">
                                                {!! $channelGenre !!}
                                            </select>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label for="channelTags">Channel Tags</label>
                                            <select class="form-control" id="channelTags" multiple name="tags[]"
                                                style="height: 110px">
                                                {!! $channelTags !!}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <label for="profileName">Profile Name</label>
                                            <input id="profile_name" class="form-control" type="text"
                                                name="profile_name" placehoder="Name show on Mooonshots">
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label for="otp_key">OTP Key</label>
                                            <input id="otp_key" class="form-control" type="text" name="otp_key"
                                                value="">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <label for="alarm">Alarm</label>
                                            <select id="upload_alert" name="upload_alert" class="form-control">
                                                <option selected value="-1">Select</option>
                                                <option value="1">Get Alarm</option>
                                                <option value="0">Do not get alarm</option>
                                            </select>
                                        </div>
                                        @if ($request->is_admin_music)
                                            <div class="col-12 mb-2">
                                                <label for="expire_get_pass">Date Get Pass</label>
                                                <select class="form-control" id="expire_get_pass" name="expire_get_pass">
                                                    <option value="-1">Select</option>
                                                    <option value="5">5 days</option>
                                                    <option value="4">4 days</option>
                                                    <option value="3">5 days</option>
                                                    <option value="2">2 days</option>
                                                    <option value="1">1 day</option>
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <label for="uploadBy">Upload By</label>
                                            <select class="form-control" id="uploadBy" name="version">
                                                <option value="-1">Select</option>
                                                <option value="1">Moonshots</option>
                                                <option value="2">API</option>
                                            </select>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label for="channel_chacking">Channel Tracking</label>
                                            <select class="form-control" id="channel_chacking" name="sub_tracking">
                                                <option value="-1">Select</option>
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="action-item">
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="27"
                                    data-requires-form="true" data-form="changeIpForm">Set IP</button>
                                <div id="changeIpForm" class="action-form">
                                    <div class="form-group">
                                        <select class="form-control" name="client_27">
                                            <option value="dev2-new">dev2-new</option>
                                            <option value="client_led">client_led</option>
                                            <option value="linux_bas_v2">linux_bas_v2</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="action-item">
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="41"
                                    data-requires-form="true" data-form="deleteVideosForm">Delete Videos</button>
                                <div id="deleteVideosForm" class="action-form">
                                    <h6 class="mb-3">Delete Videos by Views</h6>
                                    <div class="form-group">
                                        <label for="views_delete">Views threshold</label>
                                        <input id="views_delete" class="form-control" type="number" 
                                               name="views_delete" placeholder="Enter view count (e.g. 1000)" min="0">
                                        <small class="form-text text-muted">Videos with views less than this number will be deleted (oldest first)</small>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="3">Check
                                Views</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="30">Create
                                Channel</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="38">Delete
                                Channel</button>
                        </div>
                    </div>
                    <!-- Settings -->
                    <div class="action-group" data-category="settings">
                        <div class="action-group-title">
                            <i class="fas fa-cogs"></i> Settings
                        </div>
                        <div class="action-btn-group">
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="32">Change
                                Pass</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="35">Change
                                Info</button>
                        </div>
                    </div>

                    <!-- Upload Settings -->
                    <div class="action-group" data-category="upload">
                        <div class="action-group-title">
                            <i class="fas fa-upload"></i> Upload Settings
                        </div>
                        <div class="action-btn-group">
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="7">Set Upload
                                Manual</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="8">Set Upload
                                Auto</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="18">Set API Upload
                                Auto</button>
                        </div>
                    </div>

                    <!-- Comment Settings -->
                    <div class="action-group" data-category="comments">
                        <div class="action-group-title">
                            <i class="fas fa-comments"></i> Comment Settings
                        </div>
                        <div class="action-btn-group">
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="36">Comment: Turn
                                Off</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="37">Comment: Turn
                                On</button>
                        </div>
                    </div>

                    <!-- Wake Up -->
                    <div class="action-group" data-category="wakeup">
                        <div class="action-group-title">
                            <i class="fas fa-sync-alt"></i> Wake Up
                        </div>
                        <div class="action-btn-group">
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="9">Set Manual</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="10">Set Auto</button>
                        </div>
                    </div>

                    <!-- Promotion -->
                    <div class="action-group" data-category="promotion">
                        <div class="action-group-title">
                            <i class="fas fa-bullhorn"></i> Promotion
                        </div>
                        <div class="action-btn-group">
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="11">Disable
                                Cross</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="12">Enable
                                Cross</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="13">Enable Promos
                                Lyric</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="14">Disable Promos
                                Lyric</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="15">Enable Promos
                                Mix</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="16">Disable Promos
                                Mix</button>
                        </div>
                    </div>

                    <!-- Hub Settings -->
                    <div class="action-group" data-category="hub">
                        <div class="action-group-title">
                            <i class="fas fa-project-diagram"></i> Hub Settings
                        </div>
                        <div class="action-btn-group">
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="20">Hub: Turn
                                Off</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="21">Hub: Turn
                                On</button>
                        </div>
                    </div>

                    <!-- Moonshots -->

                    <div class="action-group" data-category="moonshots">
                        <div class="action-group-title">
                            <i class="fas fa-moon"></i> Moonshots
                        </div>
                        <div class="action-btn-group">
                            @if ($is_admin_music)
                                <div class="action-item">
                                    <button class="btn btn-sm btn-outline-secondary action-btn" data-value="24"
                                        data-requires-form="true" data-form="autoLoginForm">Auto Login</button>
                                    <div id="autoLoginForm" class="action-form">
                                        <div class="form-group">
                                            <label for="loginIp">Ip</label>
                                            <select id="loginIp" class="form-control" name="client">
                                                <option value="linux_bas_v2">linux_bas_v2</option>
                                                <option value="dev2-new">dev2-new</option>
                                                <option value="client_led">client_led</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="loginProxy">Proxy</label>
                                            <select id="loginProxy" class="form-control" name="proxy">
                                                <option value="">None</option>
                                                <option value="tinsoft">Tinsoft</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="26">Clear
                                Profile Moonshots</button>
                            @if ($is_admin_music)
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="29">Clear Profile
                                Bas</button>
                            @endif
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="28">Sync
                                Cookie</button>
                        </div>
                    </div>

                    <!-- Special Features -->
                    <div class="action-group" data-category="special" >
                        <div class="action-group-title">
                            <i class="fas fa-rocket"></i> Special Features
                        </div>
                        <div class="action-btn-group">
                            @if ($is_admin_music)
                                <div class="action-item">
                                    <button class="btn btn-sm btn-outline-secondary action-btn" data-value="19"
                                        data-requires-form="true" data-form="vipRender">VIP
                                        Render</button>
                                    <div id="vipRender" class="action-form">
                                        <!--<h6 class="mb-3">Add Channel to Group</h6>-->
                                        <div class="form-group">
                                            <select class="search_select" data-show-subtext="true"
                                                data-live-search="true" data-size="5" data-container="body"
                                                data-width="100%" name="vip_day">
                                                <option value="1">1 day</option>
                                                <option value="2">2 days</option>
                                                <option value="3">3 days</option>
                                                <option value="4">4 days</option>
                                                <option value="5">5 days</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="22">Boom VIP
                                Active</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="23">Boom VIP
                                Inactive</button>
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="34">Add to Social</button>
                            @if ($is_admin_music)
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="33">YT Device OAuth</button>
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="39">Set Cant Resolved Change Info</button>
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="40">Set Sent user to check Change Info</button>
                            @endif
                        </div>
                    </div>
                    
                    <div class="action-group" data-category="studio" style="margin-bottom: 70px">
                        <div class="action-group-title">
                            <i class="fas fa-project-diagram"></i> Studio
                        </div>
                        <div class="action-btn-group">
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="4">Show</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="31">Hide</button>
                        </div>
                    </div>                    
                </div>


            </div>
            <div class="action-panel-footer">
                <div class="row">
                    <div class="col-6 pr-1">
                        <button id="executeBtn" class="btn btn-primary execute-btn" data-reload="false" disabled>
                            <i class="fas fa-play-circle"></i> Execute
                        </button>
                    </div>
                    <div class="col-6 pl-1">
                        <button id="executeReloadBtn" class="btn btn-success execute-btn" data-reload="true" disabled>
                            <i class="fas fa-sync"></i> Execute & Reload
                        </button>
                    </div>
                </div>
                <input type="hidden" id="actionValue" name="action" value="">
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center ">
                    <div class="row w-50p">
                        <div class="header-search-container col-md-2">
                            <div class="input-group">
                                <input type="text" class="form-control search-input" id="search_channel_header"
                                    value="{{ $request->c1 }}" placeholder="Search ID, name or email..."
                                    onkeyup="autoSubmitSearch(event, this)">
                                <div class="search-icon">
                                    <i class="fas fa-search text-muted"></i>
                                </div>
                            </div>
                        </div>
                        @if ($is_admin_music)
                            <div class="col-md-3 btn-100">
                                <select id="user_search_header" class="form-control search_select" name="c5"
                                    data-show-subtext="true" data-live-search="true" data-size="5"
                                    data-container="body" data-width="100%">
                                    {!! $listusercode !!}
                                </select>
                            </div>
                        @endif
                        <div class="col-md-3 btn-100">
                            <div id="group_channel_2" class="select-dropdown-container"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            
                                <button type="button" id="chartTrackButton" onclick="chartTrackChannels()"
                                    class="btn btn-outline-info mr-2 btn-100 position-relative" style="overflow: visible"
                                    data-toggle="tooltip" data-placement="top" data-original-title="">
                                    <i class="fas fa-chart-line"></i> Chart
                                </button>
                            
                            <button type="button"  onclick="filterUploadError(this)"
                                class="btn btn-outline-warning mr-2 btn-100 position-relative <?php echo $request->is_upload_error==1?"active":""; ?>" style="overflow: visible"
                                data-toggle="tooltip" data-placement="top"
                                data-original-title="Error upload, render">
                                <i class="fas fa-exclamation-triangle"></i> Upload 
                                @if($errorCountUpload>0)
                                    <span class="filter-badge ">{{ $errorCountUpload }}</span>
                                @endif    
                            </button>
                            <button type="button" id="errorChangeInfoBtn" onclick="filterError(this)"
                                class="btn btn-outline-warning mr-2 btn-100 position-relative <?php echo $request->is_change_info_error==1?"active":""; ?>" style="overflow: visible"
                                data-toggle="tooltip" data-placement="top"
                                data-original-title="Error change info channel">
                                <i class="fas fa-exclamation-triangle"></i> Error 
                                @if($errorCountChangeInfo>0)
                                    <span class="filter-badge ">{{ $errorCountChangeInfo }}</span>
                                @endif    
                            </button>
                            <button type="button" id="showFilterBtn"
                                class="btn btn-outline-primary mr-2 btn-100 position-relative" style="overflow: visible">
                                <i class="fas fa-filter mr-1"></i> Advanced
                            </button>
                            <button type="button" class="btn btn-outline-success btn-add-channel mr-2 btn-100"
                                data-toggle="tooltip" data-placement="top" data-original-title="Add Channel">
                                <i class="fas fa-plus mr-1"></i> Channel
                            </button>
                            <button type="button" class="btn btn-outline-info btn-create-channel btn-100"
                                data-toggle="tooltip" data-placement="top" data-original-title="Create Email">
                                <i class="fas fa-envelope mr-1"></i> Email
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%;text-align: center">
                                    <div class="checkbox checkbox-primary tbl-chk">
                                        <input id="select_all" type="checkbox" name="select_all">
                                        <label for="select_all" class="m-b-22 p-l-0" style="margin-bottom: 1rem"></label>
                                    </div>
                                </th>
                                <?php $count = number_format($datas->total(), 0, ',', '.'); ?>
                                <th class="th-channel" colspan="4">@sortablelink('id', "Channel Details ($count)")</th>
                                <th class="th-tags">Tags</th>
                                <th class="th-increase">@sortablelink('increasing', 'Growth')</th>
                                <th class="th-views">@sortablelink('view_count', 'Views')</th>
                                <th class="th-hub">Hub</th>
                                <th class="th-created text-center">@sortablelink('chanel_create_date', 'Date Created')</th>
                                <th class="th-subs">@sortablelink('subscriber_count', 'Subs')</th>
                                <th class="th-start">@sortablelink('confirm_time', 'Start Date')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $data)
                                <tr id="channel-{{ $data->id }}" class="channel-row" data-id="{{ $data->id }}">
                                    <td class="text-center">
                                        <div class="checkbox checkbox-primary tbl-chk">
                                            <input class="checkbox-multi" type="checkbox" name="chkChannelAll[]"
                                                id="ck-video<?php echo $data->id; ?>" value="{{ $data->id }}">
                                            <label class="m-b-18 p-l-0" for="ck-video<?php echo $data->id; ?>"></label>
                                        </div>
                                    </td>
                                    <?php
                                    $brand = 'Brand';
                                    $disable = '';
                                    $color = '';
                                    $tooltip = '';
                                    if ($data->is_rebrand == 1) {
                                        $brand = 'Branding';
                                        $disable = 'disabled';
                                        $color = 'not-branded';
                                        $tooltip = "Running bas_id $data->bas_id";
                                    } elseif ($data->is_rebrand == 5) {
                                        $brand = 'Branded';
                                        $disable = '';
                                        $color = 'branded';
                                        $tooltip = 'Brand successful';
                                    } elseif ($data->is_rebrand == 4) {
                                        $brand = 'Brand Err';
                                        $disable = '';
                                        $color = 'brand-error';
                                        $tooltip = "$data->bas_id $data->gmail_log";
                                    } elseif ($data->is_rebrand == 0) {
                                        $tooltip = 'Brand this channel';
                                    }
                                    ?>

                                    <td colspan="4">
                                        <div class="d-flex align-items-center">
                                            <div class="channel-avatar-v1 position-relative {{ $color }}">
                                                <img data-id="{{ $data->id }}" src="{{ $data->channel_clickup }}"
                                                    class="rounded-circle" alt="Channel Avatar"
                                                    onerror="this.src='/images/default-avatar.png'">

                                                <!-- Edit button overlay -->
                                                <button type="button" class="avatar-sync-btn cur-poiter"
                                                    value="{{ $data->id }}">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                                <button type="button" class="avatar-edit-btn cur-poiter btn-brand"
                                                    value="{{ $data->id }}" channelName="{{ $data->chanel_name }}"
                                                    data-toggle="tooltip" data-placement='bottom'
                                                    title="{{ $tooltip }}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                            </div>

                                            <div class="channel-info">
                                                <div class="channel-name-container">
                                                    <h5 class="mb-0 d-flex align-items-center">
                                                        <span id="channel_name_{{ $data->id }}"
                                                            class="channel-name copyable-channel <?php echo $data->status == 0 ? 'song-block' : ''; ?>"
                                                            data-channel-id="https://www.youtube.com/channel/{{ $data->chanel_id }}">{{ $data->chanel_name }}</span>
                                                        @if ($data->epid_status == 'approved')
                                                            <i class="fas fa-check-circle text-success ml-1"
                                                                data-toggle="tooltip" title="Epid channel"></i>
                                                        @elseif($data->epid_status == 'admin_rejected' || $data->epid_status == 'rejected')
                                                            <i class="fas fa-check-circle text-danger ml-1"
                                                                data-toggle="tooltip" title="Epid Rejected"></i>
                                                        @elseif($data->epid_status == 'pending' || $data->epid_status == 'sent_to_epid')
                                                            <i class="fas fa-check-circle text-warning ml-1"
                                                                data-toggle="tooltip" title="Waiting for response"></i>
                                                        @endif
                                                    </h5>
                                                </div>
                                                <div class="channel-details mt-2">
                                                    <div class="detail-row d-flex text-muted">
                                                        <div class="detail-item mr-3">
                                                            <i class="fas fa-hashtag fa-fw text-muted mr-2"></i> <span
                                                                class="copyable-text"
                                                                data-copy="{{ $data->id }}">{{ $data->id }}</span>
                                                        </div>
                                                        <div class="detail-item mr-3"  id="email-container-{{ $data->id }}">
                                                            <i class="fas fa-user fa-fw text-muted mr-2"></i> <span
                                                                class="copyable-text"
                                                                data-copy="{{ substr($data->user_name, 0, strripos($data->user_name, '_')) }}">{{ substr($data->user_name, 0, strripos($data->user_name, '_')) }}</span>
                                                        
                                                            @if($data->reco_email != null)
                                                                <div class="recovery-email-container detail-item ml-2" data-id="{{$data->id}}">
                                                                    <i class="fas fa-shield-alt text-muted mr-2"></i>
                                                                    <span class="copyable-text" data-copy="{{$data->reco_email}}">{{$data->reco_email}}</span>
                                                                </div>
                                                                <button type="button" 
                                                                        class="btn-finish-recovery cur-poiter" 
                                                                        data-id="{{ $data->id }}"
                                                                        onclick="handleFinishRecovery({{ $data->id }})"
                                                                        data-toggle="tooltip" 
                                                                        title="Change Recovery Email to {{ $data->reco_email }} completed">
                                                                    <i class="fas fa-rocket"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="detail-row d-flex text-muted">
                                                        <div class="detail-item mr-3">
                                                            <i class="fas fa-at fa-fw text-muted mr-2"></i> <span
                                                                class="copyable-text"
                                                                data-copy="{{ $data->handle }}">{{ $data->handle }}</span>
                                                        </div>
                                                        <?php
                                                        $gCountText = '';
//                                                        foreach ($gmailCounts as $gCount) {
//                                                            if ($data->note == $gCount->note) {
//                                                                $gCountText = "<span class='gmail-count'>$gCount->total</span>";
//                                                                break;
//                                                            }
//                                                        }
//                                                        ?>
<!--                                                        <div class="detail-item">
                                                            <i class="fas fa-envelope fa-fw text-muted mr-2"></i> <span
                                                                class="copyable-text"
                                                                data-copy="{{ $data->note }}">{{ $data->note }}</span>
                                                            {!! $gCountText !!}

                                                        </div>-->
                                                    <div class="detail-item">
                                                        <i class="fas fa-envelope fa-fw text-muted mr-2"></i>

                                                        <div class="email-with-channels">
                                                            <div class="email-text">
                                                                <span class="copyable-text" data-copy="{{ $data->note }}">{{ $data->note }}</span>
                                                                @php
                                                                    $emailChannels = $channelsByEmail[$data->note] ?? collect();
                                                                    $channelCount = $emailChannels->count();
                                                                @endphp

                                                                @if($channelCount > 1)
                                                                    <span class="gmail-count">{{ $channelCount }}</span>
                                                                @endif
                                                            </div>

                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="detail-row d-flex text-muted">
                                                        <div class="detail-item">
                                                            <?php
                                                            $groupName = '';
                                                            if (isset($listGroupChannel)) {
                                                                foreach ($listGroupChannel as $groupChannel) {
                                                                    if ($groupChannel->id == $data->group_channel_id) {
                                                                        $groupName = $groupChannel->group_name;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                            @if($groupName!="")
                                                            <i class="fas fa-users fa-fw text-muted mr-2"></i>
                                                            <a href="/channelmanagement/v2?c3={{ $data->group_channel_id }}">{{ $groupName }}</a>
                                                            @endif
                                                        </div>
                                                        <div class="detail-item">
                                                            @if($channelCount > 1)
                                                                <div class="channel-avatars-container" title="Channels in this email">
                                                                    @php
                                                                        $maxAvatarsToShow = 4;
                                                                        $channelsToShow = $emailChannels->take($maxAvatarsToShow);
                                                                        $remainingChannels = $channelCount - $maxAvatarsToShow;
                                                                    @endphp

                                                                    @foreach($channelsToShow as $channel)
                                                                        @php
                                                                            $brandClass = 'not-branded';
                                                                            if(isset($channel->brand_status)) {
                                                                                switch($channel->brand_status) {
                                                                                    case 'branded':
                                                                                    case 1:
                                                                                        $brandClass = 'branded';
                                                                                        break;
                                                                                    case 'error':
                                                                                    case -1:
                                                                                        $brandClass = 'brand-error';
                                                                                        break;
                                                                                    default:
                                                                                        $brandClass = 'not-branded';
                                                                                }
                                                                            }

                                                                            $avatarUrl = $channel->avatar_url;
                                                                            if(empty($avatarUrl)) {
                                                                                $firstLetter = strtoupper(substr($channel->chanel_name, 0, 1));
                                                                                $avatarUrl = "https://via.placeholder.com/48x48/6c757d/ffffff?text=" . urlencode($firstLetter);
                                                                            }

                                                                            $shortChannelName = strlen($channel->chanel_name) > 20 
                                                                                ? substr($channel->chanel_name, 0, 20) . '...' 
                                                                                : $channel->chanel_name;
                                                                        @endphp

                                                                        <img src="{{ $avatarUrl }}" 
                                                                             alt="{{ $channel->chanel_name }}" 
                                                                             class="mini-channel-avatar {{ $brandClass }}"
                                                                             title="{{ $shortChannelName }} ({{ $brandClass }})"
                                                                             data-channel-id="{{ $channel->chanel_id }}"
                                                                             onclick="showChannelDetail('{{ $channel->chanel_id }}')">
                                                                    @endforeach

                                                                    @if($remainingChannels > 0)
                                                                        <div class="more-channels-indicator" 
                                                                             title="{{ $remainingChannels }} more channels"
                                                                             onclick="showAllChannelsModal('{{ $data->note }}')">
                                                                            +{{ $remainingChannels }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif
    
</div>
                                                    </div>
                                                </div>
                                                <div class="action-buttons mt-3">
                                                    @if($data->host_url != null)
                                                    @if($is_admin_music)
                                                    <button type="button" class="btn btn-sm btn-action btn-moonspace-login"
                                                        data-toggle="tooltip" data-profile="{{ $data->gologin }}"
                                                        value="{{ $data->hash_pass }}"
                                                        title="Login to Moon Space">
                                                        <i class="fas fa-rocket"></i> Space
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-action btn-moonspace-destroy"
                                                        data-toggle="tooltip" data-profile="{{ $data->gologin }}"
                                                        value="{{ $data->hash_pass }}"
                                                        title="Destroy session Moon Space">
                                                        <i class="fas fa-trash"></i> Destroy
                                                    </button>
                                                    @endif
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-action btn-gologin"
                                                        data-toggle="tooltip" data-profile="{{ $data->gologin }}"
                                                        value="{{ $data->hash_pass }}"
                                                        title="Login to account {{ $data->gologin }}">
                                                        <i class="fas fa-sign-in-alt"></i> Login
                                                    </button>
                                                    @if($is_admin_music || $data->is_autoseo != 3)
                                                        <a id="commit-{{ $data->hash_pass }}"
                                                            data-mail="{{ $data->note }}" target="_blank"
                                                            href="AutoProfile://profile/commit/?id={{ $data->gologin }}&gmail={{ $data->note }}&force=1"
                                                            class="btn btn-sm btn-action" data-toggle="tooltip"
                                                            title="Commit Moonshots">
                                                            <i class="fas fa-rocket"></i> Commit
                                                        </a>
                                                    @endif
                                                    @if ($data->otp_key != null)
                                                        <button type="button" data-toggle="tooltip"
                                                            title="Get login code"
                                                            class="btn-getcode cur-poiter btn btn-sm btn-action"
                                                            value="{{ $data->hash_pass }}"><i
                                                                class="fas fa-key mr-2"></i> Code</button>
                                                    @endif
                                                    @if ($data->chanel_id == null || App\Common\Utils::containString($data->chanel_id, '@'))
                                                        <button id="btn-add-channel-id-{{ $data->id }}"
                                                            type="button" class="btn btn-sm btn-action"
                                                            data-toggle="tooltip" title="Add channel ID"
                                                            style="color:red"
                                                            onclick="showChannelIdInput({{ $data->id }})">
                                                            <i class="fas fa-plus-circle"></i> Add ID
                                                        </button>
                                                    @endif
                                                    @if ($data->otp_key == null || $data->is_add_otp == 0)
                                                        <button id="btn-insert-otp-{{ $data->id }}" type="button"
                                                            class="btn btn-sm btn-action" data-toggle="tooltip"
                                                            title="Add OTP code" style="color:red"
                                                            onclick="insertOtpKey({{ $data->id }})">
                                                            <i class="fas fa-key"></i> Add OTP
                                                        </button>
                                                    @endif
                                                    <!-- <button type="button"
                                                        class="btn btn-sm btn-action btn-recheck-channel"
                                                        data-toggle="tooltip" value="{{ $data->id }}"
                                                        title="Check channel views">
                                                        <i class="fas fa-eye"></i> Check
                                                    </button> -->
                                                    <!-- <button type="button"
                                                        class="btn btn-sm btn-action view-realtime-chart cur-poiter"
                                                        data-channel='{{ $data->chanel_id }}'
                                                        data-name='{{ $data->chanel_name }}'><i
                                                            class="fas fa-chart-line mr-2"></i> Chart</button> -->
                                                    <div class="dropdown d-inline-block">
                                                        <button type="button"
                                                            class="btn btn-sm btn-action dropdown-toggle" type="button"
                                                            data-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-h"></i> More
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                        <button type="button"
                                                                class="dropdown-item btn-recheck-channel cur-poiter"
                                                                data-toggle="tooltip" value="{{ $data->id }}"
                                                                title="Check channel views">
                                                                <i class="fas fa-eye"></i> Check
                                                        </button>
                                                        <button type="button"
                                                                class="dropdown-item view-realtime-chart cur-poiter"
                                                                data-channel='{{ $data->chanel_id }}'
                                                                data-name='{{ $data->chanel_name }}'><i
                                                                class="fas fa-chart-line mr-2"></i> Chart</button>
                                                            <button type="button"
                                                                class="dropdown-item cur-poiter track-channel-btn"
                                                                data-channel-id="{{ $data->id }}"></button>
                                                            @if ($data->epid_status == null)
                                                                <button type="button" class="dropdown-item cur-poiter"
                                                                    onclick="submitEpid({{ $data->id }})"><i
                                                                        class="fas fa-music mr-2"></i> Submit
                                                                    Epidemic</button>
                                                            @endif
                                                             @if ($data->reco_email == null && 
                                                                    ($data->last_change_pass==4 || $data->last_change_pass==6 || $data->last_change_pass==7))
                                                                <button type="button" class="dropdown-item cur-poiter btn-get-recovery-email" value="{{ $data->hash_pass }}" data-id="{{ $data->id }}">
                                                                <i class="fas fa-envelope mr-2"></i> New Recovery Email
                                                                </button>
                                                             @endif
                                                             @if ($data->reco_email != null)
                                                                <button type="button" class="dropdown-item cur-poiter btn-getcode-change-recovery" 
                                                                        value="{{ $data->hash_pass }}" data-id="{{ $data->id }}"
                                                                        onclick="getCodeRecoveryForChangeRecovery(this)">
                                                                <i class="fas fa-code mr-2"></i> Code to Change Recovery
                                                                </button>
                                                             @endif
                                                            @if ($data->otp_key != null)
                                                                <button type="button"
                                                                    class="dropdown-item btn-getcode-recovery cur-poiter"
                                                                    value="{{ $data->hash_pass }}"><i
                                                                        class="fas fa-undo mr-2"></i> Get Recovery
                                                                    Code</button>
                                                            @endif
                                                            <a class="dropdown-item copyable-channel"
                                                                data-channel-id="{{ $data->gologin }}"><i
                                                                    class="fas fa-moon mr-2"></i> Copy Profile ID</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item text-danger cur-poiter"
                                                                onclick="deleteChannel({{ $data->id }})"><i
                                                                    class="fas fa-trash-alt mr-2"></i> Delete</a>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tags-container">
                                            @foreach ($data->tags_array as $tag)
                                                @if ($tag != null)
                                                    <span
                                                        class="badge badge-primary d-block mb-1 cur-poiter position-relative">
                                                        {{ $tag }}
                                                        <i class="fas fa-times tag-delete"></i>
                                                    </span>
                                                @endif
                                            @endforeach
                                            <button type="button"
                                                class="btn btn-sm btn-outline-secondary add-tag-btn mt-2">
                                                <i class="fas fa-plus-circle"></i> Add Tag
                                            </button>
                                        </div>
                                    </td>
                                    <td id="growth-views-{{ $data->id }}">
                                        <span>
                                            {{ number_format($data->increasing, 0, ',', '.') }}
                                            @if ($data->inc_percent >= 0)
                                                <i class="fas fa-arrow-up growth-arrow text-success"></i>
                                                <span
                                                    class="growth-percentage text-success">{{ $data->inc_percent }}%</span>
                                            @else
                                                <i class="fas fa-arrow-down growth-arrow text-danger"></i>
                                                <span
                                                    class="growth-percentage text-danger">{{ $data->inc_percent }}%</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td id="total-views-{{ $data->id }}">
                                        <div>{{ number_format($data->view_count, 0, ',', '.') }}</div>
                                        <div class="small text-muted">{{ $data->inc_time }}</div>
                                    </td>
                                    <td>
                                        @if ($data->turn_off_hub)
                                            <span class="hub-status text-danger">Off</span>
                                        @else
                                            <span class="hub-status text-success">On</span>
                                        @endif
                                    </td>
                                    <td style="width:160px">
                                        <?php
                                        echo $data->chanel_create_date != null ? '<div>' . gmdate('Y/m/d', $data->chanel_create_date + $user_login->timezone * 3600) . '</div>' : '';
                                        ?>
                                        <?php
                                            //4: lỗi, 6: không thể giải quyết,7:cần bassteam giải quyết
//                                            $errorMessage = $data->message;
//
//                                            // Split the error message into lines
//                                            $errorLines = preg_split('/\r\n|\r|\n/', $errorMessage);
//
//                                            // First line is the title/header, rest is the detail
//                                            $errorTitle = isset($errorLines[0]) ? $errorLines[0] : 'Error';
//
//                                            // Combine remaining lines as details (if any)
//                                            $errorDetail = '';
//                                            if (count($errorLines) > 1) {
//                                                unset($errorLines[0]);
//                                                $errorDetail = implode("\n", $errorLines);
//                                            }
                                        ?>
                                        @if ($data->last_change_pass > 7)
                                            <div class="small text-muted">{{ $data->message == null ? 'Change info' : $data->message }}</div>
                                            <div class="small text-muted">{{ App\Common\Utils::calcTimeText($data->last_change_pass) }}</div>
                      
                                        @elseif($data->last_change_pass == 4 || $data->last_change_pass == 6 || $data->last_change_pass == 7)
                                            <div class="error-container position-relative" data-id="{{ $data->id }}">
                                                    <div class="error-actions ml-auto">
                                                        <button class="error-action-btn btn-resolve-error" data-action-type="resolve" title="Mark as resolved">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        @if($is_admin_music && $data->last_change_pass != 7)
                                                            <button class="error-action-btn btn-check-error" data-action-type="check" title="Send user to check">
                                                                <i class="fas fa-user-check"></i>
                                                            </button>
                                                        @endif
                                                        @if($data->last_change_pass != 6)
                                                            <button class="error-action-btn btn-remove-error" data-action-type="not_resolve" title="Mark as cant resolved">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                <div class="error-display d-flex align-items-center">
                                                    <span class="error-text text-danger">{{ $data->message }}</span>
                                                </div>
                                                @if (!empty($data->message))
                                                    <div class="error-details d-none">{{ $data->message }}</div>
                                                @endif
                                            </div>
                                        @endif
                                        @if ($data->last_upload !== null && $data->status_upload==3)
                                            @php
                                                $err = json_decode($data->last_upload);
                                                $count = !empty($err[0]->count) ?  ("(".$err[0]->count.")") : "";
                                            @endphp

                                            @if (!empty($err[0]->error_message))
                                                <div class="error-container position-relative" data-id="{{ $data->id }}">
                                                    <div class="error-actions ml-auto">
                                                        <button class="error-action-btn btn-resolve-error" data-action-type="resolve" data-error-type="upload" title="Mark as resolved">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        @if($data->status_upload != 4)
                                                            <button class="error-action-btn btn-remove-error" data-action-type="not_resolve" data-error-type="upload" title="Mark as cant resolved">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <div class="error-display d-flex align-items-center">
                                                        <span class="error-text text-danger">{{$count}} • {{ $err[0]->error_message }}</span>
                                                    </div>
                                                    <div class="error-details d-none">job {{$err[0]->job_id}} - {{  $err[0]->error_message }} {{$count}}</div>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ number_format($data->subscriber_count, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($data->confirm_time != null)
                                            {{ App\Common\Utils::convertToViewDate($data->confirm_time) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <div class="records-info">
                    <?php
                    $info = str_replace('_START_', $datas->firstItem() != null ? $datas->firstItem() : '0', trans('label.title.sInfo'));
                    $info = str_replace('_END_', $datas->lastItem() != null ? $datas->lastItem() : '0', $info);
                    $info = str_replace('_TOTAL_', $datas->total(), $info);
                    echo $info;
                    ?>
                </div>
                <div class="d-flex align-items-center">
                    <div class="records-per-page mr-3">
                        <select id="cbbLimit" name="limit" class="form-control">
                            {!! $limitSelectbox !!}
                        </select>
                    </div>
                    @if (isset($datas))
                        {!! $datas->links() !!}
                    @endif
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
    @include('dialog.channel.multichartrealtime')
@endsection

@section('script')
    <script type="text/javascript">
function showAllChannelsModal(email) {
    console.log('Show all channels for email:', email);
    
    // Lấy tất cả channels của email này
    @if(isset($channelsByEmail))
        const allChannels = @json($channelsByEmail);
        const emailChannels = allChannels[email] || [];
        
        // Tạo modal content
        let modalContent = `
            <div class="modal fade" id="allChannelsModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">All Channels for ${email}</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
        `;
        
        emailChannels.forEach(channel => {
            const brandClass = channel.brand_status === 'branded' || channel.brand_status === 1 ? 'branded' 
                             : channel.brand_status === 'error' || channel.brand_status === -1 ? 'brand-error' 
                             : 'not-branded';
            
            const avatarUrl = channel.avatar_url || `https://via.placeholder.com/64x64/6c757d/ffffff?text=${encodeURIComponent(channel.chanel_name.charAt(0).toUpperCase())}`;
            
            modalContent += `
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center p-2 border rounded">
                        <img src="${avatarUrl}" 
                             class="mini-channel-avatar ${brandClass} mr-3" 
                             style="width: 48px; height: 48px; margin-left: 0;"
                             alt="${channel.chanel_name}">
                        <div>
                            <div class="font-weight-bold">${channel.chanel_name}</div>
                            <small class="text-muted">${channel.chanel_id}</small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        modalContent += `
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Xóa modal cũ nếu có
        $('#allChannelsModal').remove();
        
        // Thêm modal mới và hiển thị
        $('body').append(modalContent);
        $('#allChannelsModal').modal('show');
    @endif
}        
    function handleFinishRecovery(channelId) {
        var button = $('.btn-finish-recovery[data-id="' + channelId + '"]');

        button.tooltip('hide');
        button.html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        button.prop('disabled', true);

        $.ajax({
            type: "POST",
            url: "finishRecoveryEmail",
            data: {
                "_token": "{{ csrf_token() }}",
                "id": channelId
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == "success") {
                    button.fadeOut(300, function() {
                        $(this).remove();
                    });
                    showNotification("Recovery email has been confirmed", "success");
//                    location.reload();
                } else {
                    button.html('<i class="fas fa-shield-alt"></i>');
                    button.prop('disabled', false);
                    showNotification(data.message || "Failed to confirm recovery email", "error");
                }
            },
            error: function(data) {
                button.html('<i class="fas fa-shield-alt"></i>');
                button.prop('disabled', false);
                showNotification("Error confirming recovery email", "error");
                console.log('Error:', data);
            }
        });
    }        
        $('.btn-get-recovery-email').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var hashPass = $(this).val();
            var channelId = $(this).data('id');
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';

            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }

            $.ajax({
                type: "GET",
                url: "getRecoveryEmail",
                data: {
                    "id": channelId,
                    "hash": hashPass
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));

                    if (data.status == "success") {
                        // Copy email to clipboard
                        copyToClipboard2(data.email);
                        showNotification("Recovery email copied: " + data.email, "success");

                        // Add Finish button after the email container
                        var emailContainer = $('#email-container-' + channelId);

                        $('.recovery-email-container[data-id="' + channelId + '"]').remove();
                        $('.btn-finish-recovery[data-id="' + channelId + '"]').remove();
                        emailContainer.append(
                             `<div class="recovery-email-container detail-item ml-2" data-id="${channelId}">
                                 <i class="fas fa-shield-alt text-muted mr-2"></i>
                                 <span class="copyable-text" data-copy="${data.email}">${data.email}</span>
                              </div>`
                         );
                        emailContainer.append(
                            `<button type="button" 
                             class="btn-finish-recovery cur-poiter" 
                             data-id="${channelId}" 
                             onclick="handleFinishRecovery(${channelId})"
                             data-toggle="tooltip" 
                             title="Change Recovery Email to ${data.email} completed ">
                                <i class="fas fa-rocket"></i>
                             </button>`
                        );
                 
                        $('.btn-finish-recovery[data-id="' + channelId + '"]').tooltip(); 

                        var changeRecoveryButton = $('.btn-getcode-change-recovery[data-id="' + channelId + '"]');
                        if (changeRecoveryButton.length === 0) {
                            // Thêm nút mới vào sau nút hiện tại trong dropdown
                            $this.after(
                                `<button type="button" class="dropdown-item cur-poiter btn-getcode-change-recovery" 
                                    value="${hashPass}" data-id="${channelId}" 
                                    onclick="getCodeRecoveryForChangeRecovery(this)">
                                    <i class="fas fa-code mr-2"></i> Code to Change Recovery
                                </button>`
                            );
                        }
                    } else {
                        showNotification(data.message || "Failed to get recovery email", "error");
                    }
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                    showNotification("Error getting recovery email", "error");
                    console.log('Error:', data);
                }
            });
        }); 
        function getCodeRecoveryForChangeRecovery(button) {
            var $button = $(button);
            var hashPass = $button.val();
            var channelId = $button.data('id');
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';

            if ($button.html() !== loadingText) {
                $button.data('original-text', $button.html());
                $button.html(loadingText);
            }

            $.ajax({
                type: "GET",
                url: "getCodeRecoveryForChangeRecovery",
                data: {
                    "id": channelId,
                    "hash": hashPass
                },
                dataType: 'json',
                success: function(data) {
                    $button.html($button.data('original-text'));

                    if (data.status == "success") {
                        // Copy code to clipboard
                        copyToClipboard2(data.code);
                        showNotification(`Code ${data.code} copied to clipboard`, "success");
                    } else {
                        showNotification(data.message || "Failed to get recovery change code", "error");
                    }
                },
                error: function(data) {
                    $button.html($button.data('original-text'));
                    showNotification("Error getting recovery change code", "error");
                    console.log('Error:', data);
                }
            });
        }
        
        // Handle hover effect for error container
        $(document).on('mouseenter', '.error-container', function() {
            $(this).find('.error-details').removeClass('d-none');
        }).on('mouseleave', '.error-container', function() {
            $(this).find('.error-details').addClass('d-none');
        });

        $(document).on('click', '.error-action-btn', function(e) {
            e.stopPropagation();
            e.preventDefault();

            var $this = $(this);
            const channelId = $(this).closest('.error-container').data('id');
            const errorContainer = $(this).closest('.error-container');
            const actionType = $(this).data('action-type');
            const errorType = $(this).data('error-type');

            // Get current error details (if any)
            const errorDetail = errorContainer.find('.error-details').text().trim();

            // Show input dialog for not_resolve and check actions
            if (actionType === 'not_resolve' || actionType === 'check') {
                // Configure dialog based on action type
                let title = actionType === 'not_resolve' ? 
                    'Mark Error as Cannot Be Resolved' : 
                    'Send to User for Checking';

                // Use $.confirm to display input dialog
                $.confirm({
                    title: title,
                    content: '' +
                    '<form action="" class="formName">' +
                    '<div class="form-group">' +
                    '<label>Enter message</label>' +
                    '<textarea class="message form-control" style="line-height:1.25" rows="5">' + errorDetail + '</textarea>' +
                    '</div>' +
                    '</form>',
                    buttons: {
                        confirm: {
                            text: 'Confirm',
                            btnClass: 'btn-blue',
                            action: function () {
                                var message = this.$content.find('.message').val();
                                processErrorAction($this, channelId, errorContainer, actionType, message,errorType);
                            }
                        },
                        cancel: {
                            text: 'Cancel'
                        }
                    }
                });
            } else {
                // For 'resolve' action, proceed immediately without dialog
                processErrorAction($this, channelId, errorContainer, actionType, errorDetail,errorType);
            }
        });

        // Function to process action after input is provided
        function processErrorAction($button, channelId, errorContainer, actionType, message,errorType="") {
            // Show loading state
            $button.html('<i class="fas fa-spinner fa-spin"></i>');

            // Call API to handle the message
            $.ajax({
                url: '/ajaxChannel',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    action: 17,
                    action_type: actionType,
                    error_type: errorType,
                    chkChannelAll: [channelId],
                    message: message 
                },
                success: function(response) {
                    if (actionType === 'resolve') {
                        $button.html('<i class="fas fa-check"></i>');
                    } else if (actionType === 'check') {
                        $button.html('<i class="fas fa-user-check"></i>');
                        showNotification("Success", "success");
                        return;
                    } else if (actionType === 'not_resolve') {
                        $button.html('<i class="fas fa-times"></i>');
                    }

                    // Remove error container with effect (only for resolve and not_resolve)
                    errorContainer.fadeOut(300, function() {
                        $(this).remove();
                    });

                    showNotification("Success", "success");
                    location.reload();
                },
                error: function(xhr) {
                    console.error('Error processing message:', xhr.responseText);

                    // Restore icon based on action type
                    if (actionType === 'resolve') {
                        $button.html('<i class="fas fa-check"></i>');
                    } else if (actionType === 'check') {
                        $button.html('<i class="fas fa-user-check"></i>');
                    } else if (actionType === 'not_resolve') {
                        $button.html('<i class="fas fa-times"></i>');
                    }

                    showNotification("Failed to process error message", "error");
                }
            });
        }

        function filterError($this) {

            if($($this).hasClass("active")){
                $("#is_change_info_error").val(null);
            }else{
                $("#is_change_info_error").val(1);
            }
             $('#btnSearch').click();

//            // Lấy URL hiện tại
//            var currentUrl = window.location.href;
//
//            // Kiểm tra xem URL đã có dấu ? chưa
//            var newUrl = currentUrl.includes('?') ?
//                currentUrl + '&is_change_info_error=1' :
//                currentUrl + '?is_change_info_error=1';
//
//            // Chuyển hướng đến URL mới
//            window.location.href = newUrl;

               
        }
        function filterUploadError($this) {

            if($($this).hasClass("active")){
                $("#is_upload_error").val(null);
            }else{
                $("#is_upload_error").val(1);
            }
             $('#btnSearch').click();

//            // Lấy URL hiện tại
//            var currentUrl = window.location.href;
//
//            // Kiểm tra xem URL đã có dấu ? chưa
//            var newUrl = currentUrl.includes('?') ?
//                currentUrl + '&is_change_info_error=1' :
//                currentUrl + '?is_change_info_error=1';
//
//            // Chuyển hướng đến URL mới
//            window.location.href = newUrl;

               
        }

        function submitEpid(id) {
            $.ajax({
                type: "GET",
                url: "/channel/epid/status",
                data: {
                    "id": id
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    if (data.status == "success") {

                    }


                },
                error: function(data) {
                    console.log('Error:', data);

                }
            });
        }

        $(".avatar-sync-btn").click(function() {
            const button = $(this);
            const avatarContainer = button.closest('.channel-avatar-v1');
            const img = avatarContainer.find('img');
            const channelRow = avatarContainer.closest('tr.channel-row');
            const id = channelRow.data('id');
            button.css({
                "opacity": "1"
            });
            button.html('<i class="fas fa-spinner fa-spin"></i>');
            button.prop('disabled', true);
            $.ajax({
                type: "GET",
                url: "syncAvatar",
                data: {
                    "id": id
                },
                dataType: 'json',
                success: function(data) {
                    button.css({
                        "opacity": "0"
                    });
                    button.html('<i class="fas fa-sync-alt"></i>');
                    button.prop('disabled', false);
                    if (data.status == "success") {
                        img.attr("src", data.thumb);

                    }


                },
                error: function(data) {
                    console.log('Error:', data);
                    button.html('<i class="fas fa-sync-alt"></i>');
                    button.prop('disabled', false);

                }
            });

        });

        function initTagManagement() {
            $(document).on('click', '.add-tag-btn', handleAddTagClick);
            $(document).on('click', '.tag-delete', handleDeleteTagClick);
            $(document).on('click', '.badge', handleTagClick);
        }
        initTagManagement();

        function handleAddTagClick() {
            const id = $(this).closest('tr').data('id');
            const tagsContainer = $(this).closest('.tags-container');
            console.log(id, tagsContainer);
            const tagStyles = `
                                <style>
                                .tag-suggestions-list {
                                    max-height: 200px;
                                    overflow-y: auto;
                                    background: white;
                                    border: 1px solid #ced4da;
                                    border-radius: 0.25rem;
                                    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
                                    display: none;
                                }

                                .tag-suggestion-item {
                                    padding: 8px 12px;
                                    cursor: pointer;
                                    position: relative;
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                }

                                .tag-suggestion-item:hover {
                                    background-color: #f8f9fa;
                                }
                                
                                .tag-delete-btn {
                                    color: #dc3545;
                                    cursor: pointer;
                                    margin-left: 8px;
                                    opacity: 0;
                                    transition: opacity 0.2s;
                                }
                                
                                .tag-suggestion-item:hover .tag-delete-btn {
                                    opacity: 1;
                                }
                                </style>`;

            // Thêm style vào head khi function được gọi
            $('head').append(tagStyles);

            // Lấy danh sách tag từ API
            let availableTags = [];
            $.ajax({
                type: "GET",
                url: "/getTags", // URL API để lấy danh sách tag
                data: {
                    query: "" // Có thể thêm query từ input nếu cần
                },
                dataType: 'json',
                async: false, // Chờ kết quả để hiển thị dialog với danh sách đã có
                success: function(data) {
                    if (data.status == "success") {
                        availableTags = data.tags;
                    } else if (Array.isArray(data)) {
                        // Định dạng mới: [{"username":"system","tag":"tag1"},{"username":"truongpv","tag":"tag2"}]
                        availableTags = data;
                    } else {
                        showNotification("Không thể tải danh sách tag", "error");
                    }
                },
                error: function(data) {
                    console.log('Error loading tags:', data);
                }
            });

            $.confirm({
                title: "Add New Tag",
                content: `<div class="tag-autocomplete-container">
                    <input type="text" id="txt_tag_name" value="" class="form-control">
                  </div>
                  <small class="form-text text-muted">Nhập tên tag mới hoặc chọn từ danh sách</small>`,
                onContentReady: function() {
                    const $input = this.$content.find("#txt_tag_name");

                    // Tạo danh sách gợi ý NGOÀI dialog (append vào body)
                    // Để tránh bị ẩn bởi z-index của dialog
                    const $suggestions = $('<div id="tag-suggestions" class="tag-suggestions-list"></div>');
                    $('body').append($suggestions);

                    // Cập nhật vị trí của danh sách gợi ý khi hiển thị
                    function updateSuggestionPosition() {
                        const inputPos = $input.offset();
                        const inputHeight = $input.outerHeight();
                        const inputWidth = $input.outerWidth();

                        $suggestions.css({
                            position: 'absolute',
                            top: inputPos.top + inputHeight,
                            left: inputPos.left,
                            width: inputWidth,
                            zIndex: 999999999 // z-index cao hơn jQuery Confirm
                        });
                    }

                    // Hiển thị gợi ý ngay khi click vào input
                    $input.on('focus', function() {
                        updateSuggestions($(this).val());
                        updateSuggestionPosition();
                        $suggestions.show();
                    });

                    // Cập nhật gợi ý khi gõ
                    $input.on('input', function() {
                        updateSuggestions($(this).val());
                        updateSuggestionPosition();
                    });

                    // Ẩn gợi ý khi click ra ngoài
                    $(document).on('click', function(e) {
                        if (!$(e.target).closest('.tag-autocomplete-container').length &&
                            !$(e.target).closest('#tag-suggestions').length) {
                            $suggestions.hide();
                        }
                    });

                    // Hàm cập nhật danh sách gợi ý
                    function updateSuggestions(query) {
                        query = query.toLowerCase().trim();
                        let filtered = availableTags;

                        // Lọc tag theo query nếu có
                        if (query) {
                            if (Array.isArray(availableTags) && availableTags.length > 0 &&
                                typeof availableTags[0] === 'object') {
                                // Định dạng mới: [{"username":"system","tag":"tag1"},{"username":"truongpv","tag":"tag2"}]
                                filtered = availableTags.filter(item =>
                                    item.tag.toLowerCase().includes(query)
                                );
                            } else {
                                // Định dạng cũ: ["tag1", "tag2", ...]
                                filtered = availableTags.filter(tag =>
                                    tag.toLowerCase().includes(query)
                                );
                            }
                        }

                        // Hiển thị danh sách gợi ý
                        if (filtered.length > 0) {
                            $suggestions.empty();

                            filtered.forEach(item => {
                                // Xác định dữ liệu tag và username
                                let tagName, username, channelCount;
                                if (typeof item === 'object') {
                                    // Định dạng mới: {username: "...", tag: "..."}
                                    tagName = item.tag;
                                    username = item.username;
                                    channelCount = item.channel_count > 1 ? item.channel_count +
                                        ' channels' : (item.channel_count < 0 ? 0 : item
                                        .channel_count) + ' channel';
                                }

                                // Chỉ hiển thị nút xóa nếu username không phải là "system"
                                const deleteButton = username !== "system" ?
                                    `<i class="fas fa-times tag-delete-btn"></i>` : '';

                                const $item = $(`<div class="tag-suggestion-item" data-username="${username}">
                            <span class="tag-name">${tagName} <span class="text-muted font-13">${channelCount}</span></span>
                            ${deleteButton}
                        </div>`);

                                // Xử lý sự kiện click vào tag (chọn tag)
                                $item.find('.tag-name').on('click', function() {
                                    $input.val(tagName);
                                    $suggestions.hide();
                                });

                                // Chỉ xử lý sự kiện xóa nếu có nút xóa
                                if (username !== "system") {
                                    // Xử lý sự kiện click vào nút xóa
                                    $item.find('.tag-delete-btn').on('click', function(e) {
                                        e.stopPropagation(); // Ngăn sự kiện bubbling lên
                                        $('body').append(
                                            '<div id="confirm-container" style="position: relative; z-index: 999999999999;"></div>'
                                            );
                                        $.confirm({
                                            title: 'Delete Tag',
                                            content: `Are you sure you want to delete this tag? "${tagName}"?`,
                                            container: '#confirm-container',
                                            zIndex: 1,
                                            buttons: {
                                                "Cancel": function() {},
                                                "Delete": {
                                                    btnClass: "btn-danger",
                                                    action: function() {
                                                        // Gọi API xóa tag
                                                        $.ajax({
                                                            type: "GET",
                                                            url: "/deleteTag",
                                                            data: {
                                                                "tag_name": tagName
                                                            },
                                                            dataType: 'json',
                                                            success: function(
                                                                data) {
                                                                if (data
                                                                    .status ==
                                                                    "success"
                                                                    ) {
                                                                    // Xóa tag khỏi danh sách
                                                                    availableTags
                                                                        =
                                                                        availableTags
                                                                        .filter(
                                                                            t => {
                                                                                return t
                                                                                    .tag !==
                                                                                    tagName;
                                                                            }
                                                                            );
                                                                    $item
                                                                        .remove();
                                                                    // Xóa tag khỏi giao diện nếu có
                                                                    $(tagsContainer)
                                                                        .find(
                                                                            '.badge'
                                                                            )
                                                                        .each(
                                                                            function() {
                                                                                if ($(
                                                                                        this)
                                                                                    .text()
                                                                                    .trim() ===
                                                                                    tagName
                                                                                    ) {
                                                                                    $(this)
                                                                                        .remove();
                                                                                }
                                                                            }
                                                                            );
                                                                }
                                                                showNotification
                                                                    (data
                                                                        .message,
                                                                        data
                                                                        .status
                                                                        );
                                                            },
                                                            error: function(
                                                                data) {
                                                                console
                                                                    .log(
                                                                        'Error:',
                                                                        data
                                                                        );
                                                                showNotification
                                                                    ("Delete error",
                                                                        "error"
                                                                        );
                                                            }
                                                        });
                                                    }
                                                }
                                            }
                                        });
                                    });
                                }

                                $suggestions.append($item);
                            });
                            $suggestions.show();
                        } else {
                            $suggestions.hide();
                        }
                    }
                },
                onClose: function() {
                    // Xóa danh sách gợi ý khỏi DOM khi đóng dialog
                    $('#tag-suggestions').remove();
                },
                buttons: {
                    "Cancel": function() {},
                    "Save": {
                        btnClass: "btn-blue",
                        action: function() {
                            let tagName = this.$content.find("#txt_tag_name").val().trim();
                            const dialog = this;
                            $.ajax({
                                type: "GET",
                                url: "channelTag",
                                data: {
                                    "id": id,
                                    "tag_name": tagName,
                                    "action": "add"
                                },
                                dataType: 'json',
                                success: function(data) {
                                    console.log(data);
                                    if (data.status == "success") {
                                        const newTag = $(`<span class="badge badge-primary d-block mb-1 cur-poiter position-relative">
                                    ${tagName}
                                    <i class="fas fa-times tag-delete"></i>
                                </span>`);
                                        $(tagsContainer).find('.add-tag-btn').before(newTag);
                                        dialog.close();
                                    } else {
                                        // Trường hợp lỗi
                                        let message = data.message || "Add Tag Fail";
                                        let status = data.status || "error";
                                        showNotification(message, status);
                                        return false;
                                    }
                                },
                                error: function(data) {
                                    console.log('Error:', data);
                                    return false;
                                }
                            });
                            return false;
                        }
                    }
                }
            });
        }

        function handleDeleteTagClick(e) {
            e.stopPropagation(); // Ngăn sự kiện bubbling lên thẻ cha
            const tagElement = $(this).parent();
            const tagName = tagElement.text().trim();
            const id = $(this).closest('tr').data('id');
            $.confirm({
                title: 'Delete Tag',
                content: `Are you sure to remove tag ${tagName} from this channel?`,
                buttons: {
                    confirm: {
                        text: 'Delete',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                type: "GET",
                                url: "channelTag",
                                data: {
                                    "id": id,
                                    "tag_name": tagName,
                                    "action": "delete"
                                },
                                dataType: 'json',
                                success: function(data) {
                                    console.log(data);
                                    if (data.status == "success") {
                                        tagElement.fadeOut(300, function() {
                                            $(this).remove();
                                        });
                                    } else {
                                        //                                        $.alert(data.message);
                                        showNotification(data.message, data.status);

                                    }
                                },
                                error: function(data) {
                                    console.log('Error:', data);
                                    return false;

                                }
                            });
                        }
                    },
                    cancel: function() {
                        // Close
                    }
                }
            });
        }

        function handleTagClick(e) {
            if (!$(e.target).hasClass('tag-delete')) {
                const tagName = $(this).text().trim();
                window.location.href = window.location.pathname + '?tags[]=' + tagName;
            }
        }

        //<editor-fold defaultstate="collapsed" desc="Group channel">
        $("#group_channel_search").val({{ $request->c3 }});
        $('#group_channel_1').groupDropdown({
            inputSelector: '#group_channel_search',
            defaultSelected: Number($("#group_channel_search").val()),
            onSelect: function(selectedIds) {
                $("#group_channel_search").val(selectedIds[0]);
                $("#form-search").submit();
            }
        });
        $('#group_channel_2').groupDropdown({
            inputSelector: '#group_channel_search',
            defaultSelected: Number($("#group_channel_search").val()),
            onSelect: function(selectedIds) {
                $("#group_channel_search").val(selectedIds[0]);
                $("#form-search").submit();
            }
        });

        //</editor-fold>

        //<editor-fold defaultstate="collapsed" desc="action new">

        // Variables to track selected channels and actions
        let selectedChannels = [];
        let selectedAction = null;
        let selectedActionForm = null;
        let isPanelCollapsed = false;

        // Toggle floating action panel
        function toggleActionPanel() {
            if (selectedChannels.length > 0) {
                $('#actionPanel').addClass('show');
                $('.execute-btn').prop('disabled', selectedAction === null);
                $("#executeBtn").html('<i class="fas fa-play-circle"></i> Execute');
                $("#executeReloadBtn").html('<i class="fas fa-sync"></i> Execute & Reload');
            } else {
                $('#actionPanel').removeClass('show');
                $('.execute-btn').prop('disabled', true);
            }

            // Update selected count
            $('#selectedCount').text(selectedChannels.length);
        }

        // Initialize panel state
        toggleActionPanel();

        $('.checkbox-multi').change(function() {
            // Kiểm tra xem có checkbox nào được chọn hay không

            if ($('.checkbox-multi:checked').length > 0) {
                selectedChannels = $('.checkbox-multi:checked').map(function() {
                    return $(this).val();
                }).get();
            } else {
                selectedChannels = [];
            }
            toggleActionPanel();
        });

        // Theo dõi checkbox "Select All"
        $('#select_all').change(function() {
            $('.checkbox-multi').prop('checked', this.checked).trigger('change');
        });

        // Toggle panel collapse/expand
        $('#toggleActionPanelBtn').click(function(e) {
            e.preventDefault();
            if (isPanelCollapsed) {
                // Expand panel
                $('#actionPanel').css('right', '1.5rem');
                $(this).find('i').removeClass('fa-chevron-left').addClass('fa-chevron-right');
                isPanelCollapsed = false;
            } else {
                // Collapse panel
                $('#actionPanel').css('right', '-365px');
                $(this).find('i').removeClass('fa-chevron-right').addClass('fa-chevron-left');
                isPanelCollapsed = true;
            }
        });


        $('.action-btn').click(function(e) {
            e.preventDefault();
            const actionValue = $(this).data('value');
            const requiresForm = $(this).data('requires-form') === true;
            const formId = $(this).data('form');

            // Kiểm tra nếu button đang active, thì hủy active
            if ($(this).hasClass('btn-primary')) {
                // Hủy chọn action này
                $(this).removeClass('btn-primary').addClass('btn-outline-secondary').removeClass("active");
                selectedAction = null;

                // Ẩn form nếu đang hiển thị
                if (requiresForm && formId) {
                    $('#' + formId).removeClass('show');
                    selectedActionForm = null;
                }

                // Vô hiệu hóa nút Execute
                $('#executeBtn').prop('disabled', true);
                $('#executeReloadBtn').prop('disabled', true);

                // Reset hidden input
                $('#actionValue').val('');

                return; // Thoát khỏi hàm, không thực hiện các bước tiếp theo
            }

            // Nếu không phải là button đang active, tiến hành chọn nó

            // Deselect previously selected action
            $('.action-btn').removeClass('btn-primary').addClass('btn-outline-secondary').removeClass("active");

            // Hide all forms
            $('.action-form').removeClass('show');

            // Select this action
            $(this).removeClass('btn-outline-secondary').addClass('btn-primary').addClass("active");
            selectedAction = actionValue;

            // Update the hidden input
            $('#actionValue').val(actionValue);

            // Show form if required
            if (requiresForm && formId) {
                $('#' + formId).addClass('show');
                selectedActionForm = formId;
            } else {
                selectedActionForm = null;
            }

            // Enable execute button
            $('#executeBtn').prop('disabled', false);
            $('#executeReloadBtn').prop('disabled', false);
        });
        // Handle execute button click
        $('.execute-btn').click(function(e) {
            e.preventDefault();
            $('#executeBtn').prop('disabled', true);
            $('#executeReloadBtn').prop('disabled', true);
            const shouldReload = $(this).data('reload') === true;
            if (selectedChannels.length > 0 && selectedAction !== null) {
                let actionData = {
                    action: selectedAction,
                    channels: selectedChannels
                };
                var $this = $(this);
                var org = $this.html();
                $this.html(`<i class="fas fa-spinner fa-spin"></i> Loading...`);
                var formChannel = $("#formChannel").serialize();
                $.ajax({
                    type: "POST",
                    url: "/ajaxChannel",
                    data: formChannel,
                    dataType: 'json',
                    success: function(data) {
                        $this.html(org);
                        $('#executeBtn').prop('disabled', false);
                        $('#executeReloadBtn').prop('disabled', false);
                        for (var i = 0; i < data.content.length; i++) {
                            showNotification(data.content[i], data.status);
                            notify(data.content[i], "https://automusic.win/channelmanagement", "");
                        }
                        setTimeout(function() {
                            if (shouldReload) {
                                location.reload();
                            }
                        }, 1000);

                    },
                    error: function(data) {
                        $this.html($this.data('original-text'));
                    }
                });
            }
        });

        // Filter actions based on search input
        $('#actionSearch').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();

            if (searchTerm === '') {
                // If search is empty, show all groups
                $('.action-group').show();
                $('.action-btn').show();
            } else {
                // First, hide all groups
                $('.action-group').hide();

                // Show buttons that match and their parent groups
                $('.action-btn').each(function() {
                    const buttonText = $(this).text().toLowerCase();
                    if (buttonText.includes(searchTerm)) {
                        $(this).show();
                        $(this).closest('.action-group').show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });


        $('#closeActionPanelBtn').click(function(e) {
            e.preventDefault();
            // Thêm class closing để tạo hiệu ứng
            $('#actionPanel').addClass('closing');

            // Sau khi animation hoàn tất, ẩn panel và bỏ chọn tất cả
            setTimeout(function() {
                // Bỏ chọn tất cả các channel
                $('.channel-select').prop('checked', false);
                $('.channel-row').removeClass('selected');

                // Reset các biến theo dõi
                selectedChannels = [];
                selectedAction = null;
                selectedActionForm = null;

                // Ẩn panel
                $('#actionPanel').removeClass('show closing');

                // Bỏ active của tất cả action buttons
                $('.action-btn').removeClass('btn-primary').addClass('btn-outline-secondary');

                // Ẩn tất cả form
                $('.action-form').removeClass('show');

                // Cập nhật số lượng đã chọn
                $('#selectedCount').text('0');
            }, 300);
        });

        //</editor-fold>

        //<editor-fold defaultstate="collapsed" desc="table">

        $(document).on('click', '.copyable-text', function() {
            const textToCopy = $(this).data('copy');
            copyToClipboard2(textToCopy);
            showCopiedEffect($(this));
        });

        // For channel name that copies ID
        $(document).on('click', '.copyable-channel', function() {
            const channelId = $(this).data('channel-id');
            copyToClipboard2(channelId);
            showCopiedEffect($(this));
        });



        function showChannelIdInput(id) {
            $.confirm({
                title: "Update Channel Id",
                content: '<input type="text" id="txt_channel_id" value="" class="form-control">',
                buttons: {
                    "Cancel": function() {},
                    "Update": {
                        btnClass: "btn-blue",
                        action: function() {
                            let channelId = this.$content.find("#txt_channel_id").val().trim();
                            $.ajax({
                                type: "GET",
                                url: "updateChannelId",
                                data: {
                                    "id": id,
                                    "channel_id": channelId
                                },
                                dataType: 'json',
                                success: function(data) {
                                    showNotification(data.message, data.status);
                                    if (data.status == "success") {
                                        $(`#btn-add-channel-id-${id}`).fadeOut();
                                        $(`#channel_name_${id}`).attr("data-channel-id",
                                            `https://www.youtube.com/channel/${channelId}`);
                                    }

                                },
                                error: function(data) {
                                    console.log('Error:', data);
                                }
                            });
                        }
                    }
                }
            });
        }

        function insertOtpKey(id) {
            console.log(id);
            $.confirm({
                title: "Add OTP Key",
                content: '<input type="text" id="txt_otp_key" value="" class="form-control">',
                buttons: {
                    "Cancel": function() {},
                    "Save": {
                        btnClass: "btn-blue",
                        action: function() {
                            let otpkey = this.$content.find("#txt_otp_key").val().trim();
                            $.ajax({
                                type: "GET",
                                url: "insertOtpKey",
                                data: {
                                    "id": id,
                                    "otpkey": otpkey
                                },
                                dataType: 'json',
                                success: function(data) {
                                    console.log(data);
                                    if (data.status == "error") {
                                        $.Notification.notify(data.status, 'top center', '', data
                                            .message);
                                    } else {
                                        $(`#btn-insert-otp-${id}`).fadeOut();
                                        copyToClipboard(data.data);
                                    }


                                },
                                error: function(data) {
                                    console.log('Error:', data);


                                }
                            });
                        }
                    }
                }
            });
        }

        function deleteChannel(id) {
            $.confirm({
                animation: 'rotateXR',
                title: "Confirm",
                content: "Are you sure you want to delete this channel?",

                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-red',
                        action: function() {
                            $.ajax({
                                url: "/updateChannelId",
                                method: "GET",
                                data: {
                                    id: id,
                                    delete: 1
                                },
                                success: function(response) {
                                    logger("Del:", response);
                                    $(`#channel-${id}`).hide();
                                },
                                error: function(xhr, status, error) {
                                    logger("error:", error);
                                }
                            });
                        }
                    },
                    cancel: function() {

                    }

                }
            });
        }

        //</editor-fold>



        //filter
        $('#showFilterBtn').click(function(e) {
            e.preventDefault();
            $('#filterPanel').toggleClass('show');
        });

        $('#closeFilterBtn').click(function(e) {
            e.preventDefault();
            $('#filterPanel').removeClass('show');
        });

        function autoSubmitSearch(event, input) {
            // Cập nhật giá trị vào ô search gốc để đồng bộ
            document.getElementById('search_channel').value = input.value;

            // Tự động submit sau khi người dùng ngừng gõ 500ms
//            clearTimeout(input.timer);
//            input.timer = setTimeout(() => {
//                $("#form-search").submit();
//            }, 500);

            // Nếu nhấn Enter thì submit ngay
            if (event.key === 'Enter') {
                clearTimeout(input.timer);
                $("#form-search").submit();
            }
        }

        // Đồng bộ giá trị khi người dùng nhập vào ô search gốc
        document.getElementById('search_channel').addEventListener('input', function() {
            document.getElementById('search_channel_header').value = this.value;
        });

        $("#user_search_header").change(function() {
            $("#user_search").val($(this).val()).selectpicker('refresh');
            $("#form-search").submit();
        });

        const $filterPanel = $('#filterPanel');
        const $showFilterBtn = $('#showFilterBtn');
        const $closeFilterBtn = $('#closeFilterBtn');
        const $formSearch = $('#form-search');

        // Function to check if a form control has a value
        function hasValue($element) {
            if (!$element.length) return false;

            const elementId = $element.attr('id');
            const elementValue = $element.val();

            // Đối với các phần tử select
            if ($element.is('select')) {
                // Với tất cả các select, không tính khi giá trị là -1 (tùy chọn "Select" mặc định)
                if (elementValue === '-1' || elementValue === 'Select' || elementValue === '') {
                    return false;
                }
                return true;
            }

            // Đối với checkbox và radio
            if ($element.is(':checkbox') || $element.is(':radio')) {
                return $element.is(':checked');
            }


            if ($element.prop('multiple')) {
                // Đảm bảo có các giá trị được chọn thực sự (không chỉ là một mảng trống)
                return elementValue && elementValue.length > 0;
            }

            // Đối với các input ẩn có ID cụ thể cần loại trừ khi có giá trị mặc định
            if ($element.attr('type') === 'hidden' && elementId === 'group_channel_search' && elementValue === '-1') {
                return false;
            }


            // Đối với text input, textarea, v.v.
            return elementValue && $.trim(elementValue) !== '';
        }

        // Function to count filled form controls
        function countFilledControls() {
            let count = 0;

            $formSearch.find('input, select, textarea').each(function() {
                const $element = $(this);
                const elementId = $element.attr('id');

                // Skip hidden inputs used for technical purposes
                if ($element.attr('type') === 'hidden' && $element.attr('name') === 'limit') {
                    return;
                }
                if ($element.attr('type') === 'hidden' && $element.attr('name') === 'is_change_info_error') {
                    return;
                }
                // Xử lý đặc biệt cho #tags - bỏ qua nếu không có tags được chọn
                if (elementId === 'tags') {
                    //                    const selectedTags = $element.val();
                    //                    if (!selectedTags || selectedTags.length === 0) {
                    //                        return; // Không đếm trường tags nếu không có tag nào được chọn
                    //                    }
                    return;
                }

                // Xử lý đặc biệt cho #group_channel_search và #search_channel, nếu 2 phần tử này thì không hiện ra
                if (elementId === 'group_channel_search' || elementId == 'search_channel' || elementId ==
                    'user_search') {
                    return;
                }
                if (hasValue($element)) {
                    logger("element", $element);
                    count++;
                }
            });

            return count;
        }

        // Function to update badge with count
        function updateFilterBadge() {
            const count = countFilledControls();
            // Remove existing badge if any
            $showFilterBtn.find('.filter-badge').remove();

            // Add badge if count > 0
            if (count > 0) {
                const $badge = $('<span class="filter-badge ">' + count + '</span>');
                $showFilterBtn.append($badge);

                // Show filter panel if there are filled filters
//                $filterPanel.addClass('show');
                //không hiện advance filter sau khi filter
                $filterPanel.removeClass('show');
            } else {
                // Hide filter panel by default if no filters are applied
                $filterPanel.removeClass('show');
            }
        }

        // Initialize on page load
        updateFilterBadge();

        $(".btn-add-channel").click(function(e) {
            e.preventDefault();
            $("#dialog_channel_add").modal("show");
        });
        $(".btn-create-channel").click(function(e) {
            e.preventDefault();
            $("#dialog_create_add").modal("show");
            $.ajax({
                type: "GET",
                url: "/genEmailInfo",
                data: {},
                dataType: 'json',
                success: function(data) {
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
                error: function(data) {
                    $(element).html($(element).data('original-text'));

                }
            });
        });
        $("#btn-open-brower").click(function() {
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var hash = $(this).attr('hash');
            goLogin(hash);
            setTimeout(function() {
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
                success: function(data) {
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
                error: function(data) {
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
                success: function(data) {
                    console.log(data);
                    $(element).html($(element).data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                    if (data.status == "success") {
                        location.reload();
                    }
                },
                error: function(data) {
                    $(element).html($(element).data('original-text'));

                }
            });
        }

        $(".copy_profile_id").click(function() {
            var profileId = $(this).attr("data-profile");
            navigator.clipboard.writeText(profileId);
            $.Notification.notify("success", 'top center', '', 'Copied: ' + profileId);
        });

        $("#btnDesEdit").click(function() {
            $('#dialog_description_edit').modal({
                backdrop: true
            });
        });

        $(".view-realtime-chart").click(function() {
            $("#dialog_realtime_view_loading").show();
            $("#table-chart").html("");
            $("#chartHour-wrap").html("");
            $("#chartMinute-wrap").html("");
            var channel_id = $(this).attr("data-channel");
            var channel_name = $(this).attr("data-name");
            var $this = $(this);
            var loadingText = '<i class="fas fa-spinner fa-spin"></i> Chart';
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
                success: function(data) {
                    logger("getDataChart", data);
                    $this.html($this.data('original-text'));
                    if (data.status == "success") {
                        $("#dialog_realtime_view_loading").hide();
                        $("#chartHour-wrap").html('<canvas id="chartHour"></canvas>');
                        $("#chartMinute-wrap").html('<canvas id="chartMinute"></canvas>');
                        drawBarChart("chartHour", "Views · Last 48 hours", data.data48hour.label, data
                            .data48hour.value);
                        drawBarChart("chartMinute", "Views · Last 60 minutes", data.data60minutes.label,
                            data
                            .data60minutes.value);
                        $("#last-48-hour").html(number_format(data.data48hour.total48, 0, ',', '.'));
                        $("#last-60-minute").html(number_format(data.data60minutes.total60, 0, ',',
                            '.'));
                        var channelHeaderInfo = `<div class="d-flex align-items-center mb-2 mb-md-0">
                                    <img class="video-img_thumb" src="${data.channel_thumb}" 
                                         style="width: 50px; height: 50px; border-radius: 50%; margin-right: 15px; object-fit: cover;">
                                    <div>
                                        <h4 class="m-0 font-weight-bold">${channel_name}</h4>
                                        <span class="badge badge-primary" style="background: #6c5ce7;">Subscribers: ${number_format(data.subs, 0, ',', '.')}</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap stats-container">
                                    <div class="stat-item text-center mx-3 my-1">
                                        <div class="stat-value">${data.level}</div>
                                        <div class="stat-label">Level</div>
                                    </div>
                                    <div class="stat-item text-center mx-3 my-1">
                                        <div class="stat-value">${number_format(data.viewRate42, 0, ',', '.')} </div>
                                        <div class="stat-label">Rate 48h</div>
                                    </div>
                                    <div class="stat-item text-center mx-3 my-1">
                                        <div class="stat-value">${number_format(data.viewRate6, 0, ',', '.')}</div>
                                        <div class="stat-label">Rate 6h</div>
                                    </div>
                                    <div class="stat-item text-center mx-3 my-1">
                                        <div class="stat-value">${number_format(data.viewAvg, 0, ',', '.')}</div>
                                        <div class="stat-label">View Avg 6h</div>
                                    </div>
                                    <div class="stat-item text-center mx-3 my-1">
                                        <div class="stat-value">${data.last_sync}</div>
                                        <div class="stat-label">Last Sync</div>
                                    </div>
                                </div>
                                <div class="status-indicator ml-auto">
             
                                <button
                                    class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light btn-close-modal"
                                    data-id="modal-add-task" data-toggle="tooltip" data-placement="top" title="Close" data-dismiss="modal"
                                    style="    padding: 0.5rem 0.7rem 0.5rem 0.7rem;z-index: 1001;border-radius: 50%;line-height: 1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                         class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                        <path
                                            d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                                    </svg>
                                </button>
                                </div>`;
                        $("#channel-header-info").html(channelHeaderInfo);
                        $("#table-chart").html(
                            '<tr><th style="top:-20px">Content</th><th style="top:-20px">Published</th><th colspan="2" style="top:-20px;text-align: center">Last 48 hours</th><th colspan="2" style="top:-20px;text-align: center">Last 60 minutes</th></tr>'
                        );
                        var html = '';
                        $.each(data.topVideos, function(key, value) {
                            i = i + 1;
                            html =
                                '<tr><td><a target="_blank" href="https://www.youtube.com/watch?v=' +
                                value.video_id +
                                '"><img class="video-img_thumb" src="https://i.ytimg.com/vi/' +
                                value
                                .video_id + '/default.jpg">' + value.video_title +
                                '</a></td><td>' +
                                value.published + '</td><td><span>' + number_format(value
                                    .total_view_hour, 0, ',', '.') +
                                '</span></td><td><div><canvas id="chartHourMini' + key +
                                '"></canvas></div></td><td><span>' + number_format(value
                                    .total_view_minute, 0, ',', '.') +
                                '</span></td><td><div><canvas id="chartMinuteMini' + key +
                                '"></canvas></div></td></tr>';
                            $("#table-chart").append(html);
                            var ratio48 = value.total_view_hour / data.maxViewTopVideoHour *
                            100;
                            if (ratio48 < 25) {
                                ratio48 = 25;
                            }
                            drawBarChartMini("chartHourMini" + key, "48", value.times_hour,
                                value
                                .views_hour, ratio48);
                            ratio48 = value.total_view_minute / data.maxViewTopVideoMinute *
                            100;
                            if (ratio48 < 25) {
                                ratio48 = 25;
                            }
                            drawBarChartMini("chartMinuteMini" + key, "60", value.times_minute,
                                value
                                .views_minute, ratio48);
                        });
                        $('#dialog_realtime_view').modal({
                            backdrop: true
                        });

                    } else {
                        showNotification(data.message, data.status);
                    }

                },
                error: function(data) {
                    console.log('Error:', data);
                    $this.html($this.data('original-text'));
                }
            });
        });

        function viewChart(channel_id) {
            //        $("#content-dialog").html("");

        }
        let currentHighlightedButton = null;
        let buttonTimers = {};
        $('.btn-gologin').click(function(e) {
            e.preventDefault();
//            var id = $(this).val();
            var $this = $(this);
            var id = $this.val();
             // Kiểm tra nếu nút đang bị khóa
            if ($this.hasClass('locked')) {
                return; // Không làm gì nếu nút đang bị khóa
            }

            // Xóa highlight từ nút trước đó
            if (currentHighlightedButton && !currentHighlightedButton.is($this)) {
                currentHighlightedButton.removeClass('highlighted');
            }

            // Đặt nút hiện tại làm nút được highlight
            currentHighlightedButton = $this;

            // Thêm class highlighted và locked
            $this.addClass('highlighted locked');

            // Lưu trữ text gốc của nút
            var originalText = $this.html();
                    goLogin(id);
                     // Đặt timer để đếm ngược 10 giây
            var countdown = 10;

            // Cập nhật text của nút để hiển thị đếm ngược
            function updateButtonText() {
                if (countdown > 0) {
                    $this.html(`<i class="fas fa-hourglass-half"></i> ${countdown}s`);
                } else {
                    $this.html(originalText);
                    $this.removeClass('locked');
                    // Giữ trạng thái highlighted
                }
            }

            // Bắt đầu đếm ngược
            updateButtonText();

            var countdownInterval = setInterval(() => {
                countdown -= 1;

                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    $this.html(originalText);
                    $this.removeClass('locked');
                } else {
                    updateButtonText();
                }
            }, 1000);
        });

        function goLogin(id) {
            $.get('goLogin?hash=' + id, function(data) {
                logger('goLogin', data);
                //        $this.html($this.data('original-text'));
                if (data.gologin == null) {
                    $.Notification.notify("error", 'top center', '', "Got error, Gologin Id empty");
                } else {
                    navigator.clipboard.writeText(data.gologin);
                    const commitBtn = $(`#commit-${id}`);
                    const gmailValue = commitBtn.attr('data-mail');
                    const newHref = `AutoProfile://profile/commit/?id=${data.gologin}&gmail=${gmailValue}&force=1`;
                    commitBtn.attr('href', newHref);
                    window.open(
                        `AutoProfile://profile/login/?id=${data.gologin}&gmail=${data.note}&hash_id=${data.hash_pass}&startup_url=https://www.youtube.com/channel_switcher`,
                        "_blank");
                    //            location.reload();
                }
            });
        }

        $('.btn-copy-gologin').click(function(e) {
            e.preventDefault();
            var profile = $(this).attr("data-profile");
            navigator.clipboard.writeText(profile);
            $.Notification.notify("error", 'top center', '', "Copied " + profile);
        });

        $('.btn-getcode').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var id = $(this).val();
            $.get('getCodeLogin?hash=' + id, function(data) {
                $this.html($this.data('original-text'));
                if (data.status == "error") {
                    $.Notification.notify("error", 'top center', '', "Got error");
                } else {
                    //                $.Notification.notify("success", 'top center', '', data.data);
                    copyToClipboard(data.data);
                }

            });
        });

        $('.btn-getcode-recovery').click(function(e) {
            e.preventDefault();
            //            var $this = $(this);
            //            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            //            if ($(this).html() !== loadingText) {
            //                $this.data('original-text', $(this).html());
            //                $this.html(loadingText);
            //            }
            var id = $(this).val();
            $.get('getCodeRecovery?hash=' + id, function(data) {
                if (data.status == "error") {
                    $.Notification.notify("error", 'top center', '', "Got error");
                } else {
                    //                $.Notification.notify("success", 'top center', '', data.data);
                    copyToClipboard(data.data);
                }

            });
        });

        // Xử lý nút Moon Space Login
        $('.btn-moonspace-login').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            if ($this.hasClass('locked')) return;
            // Xóa highlight từ nút trước đó nếu có
            if (window.currentMoonspaceBtn && !window.currentMoonspaceBtn.is($this)) {
                window.currentMoonspaceBtn.removeClass('highlighted');
            }
            window.currentMoonspaceBtn = $this;
            $this.addClass('highlighted locked');
            var originalText = $this.html();
            var hash_pass = $this.val();
            var countdown = 10;
            function updateButtonText() {
                if (countdown > 0) {
                    $this.html(`<i class=\"fas fa-hourglass-half\"></i> ${countdown}s`);
                } else {
                    $this.html(originalText);
                    $this.removeClass('locked');
                }
            }
            updateButtonText();
            var countdownInterval = setInterval(() => {
                countdown -= 1;
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    $this.html(originalText);
                    $this.removeClass('locked');
                } else {
                    updateButtonText();
                }
            }, 1000);
            // Gọi API như cũ
            $.ajax({
                type: "GET",
                url: "/moonSpaceLogin",
                data: {
                    hash_pass: hash_pass,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    setTimeout(function() { $this.prop('disabled', false); }, 10000);
                    if (data.status === "success") {
                        window.open(data.url, '_blank');
                        $.Notification.notify("success", 'top center', '', "Moon Space session opened successfully!");
                    } else {
                        $.Notification.notify("error", 'top center', '', data.message || "Failed to open Moon Space session!");
                    }
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                    setTimeout(function() { $this.prop('disabled', false); }, 10000);
                    $.Notification.notify("error", 'top center', '', "Error occurred while connecting to Moon Space!");
                }
            });
        });

        // Xử lý nút Moon Space Destroy
        $('.btn-moonspace-destroy').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            if ($this.prop('disabled')) return;
            var originalText = $this.html();
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            $this.data('original-text', originalText);
            $this.html(loadingText);
            $this.prop('disabled', true);
            var hash_pass = $this.val();
            
            $.ajax({
                type: "GET",
                url: "/moonSpaceDestroy",
                data: {
                    hash_pass: hash_pass,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    setTimeout(function() { $this.prop('disabled', false); }, 10000);
                    if(data.status === 'success') {
                        $.Notification.notify('success', 'top center', '', data.message || "Moon Space session destroyed successfully!");
                    } else {
                        $.Notification.notify('error', 'top center', '', data.message || "Failed to destroy Moon Space session!");
                    }
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                    setTimeout(function() { $this.prop('disabled', false); }, 10000);
                    $.Notification.notify("error", 'top center', '', "Error occurred while destroying Moon Space session!");
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
                success: function(data) {
                    if (data.length > 0) {
                        $("#aboutSectionId").val(data[0].id);
                        $("#about_section").val(data[0].content);
                        $("#about_section_use").html(' (Used ' + data[0].count + ' times)');
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        }

        $("#brand_select").change(function() {
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

        $("#channel_genre").change(function() {

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

        $(".btn-vip-render").click(function(e) {
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
                success: function(data) {
                    console.log(data);
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);

                },
                error: function(data) {
                    console.log('Error:', data);
                    $.Notification.notify('error', 'top center', '', "Error");
                    $this.html($this.data('original-text'));

                }
            });
        });

        $(".btn-set-info").click(function(e) {
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
                success: function(data) {
                    console.log(data);
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);

                },
                error: function(data) {
                    console.log('Error:', data);

                }
            });
        });

        $(".btn-save-brand").click(function(e) {
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
                success: function(data) {
                    console.log(data);
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function(data) {
                    console.log('Error:', data);

                }
            });
        });

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
                $.Notification.notify('error', 'top center', '', "You must choose a image");
                return;
            }

            form_file.append('image', $("#" + type + "_upload")[0].files[0]);
            form_file.append('_token', '{{ csrf_token() }}');
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "/image-upload",
                data: form_file,
                contentType: false,
                processData: false,
                success: function(data) {
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
                error: function(data) {
                    but.button('reset');
                }
            });
        });

        $(".btn-brand").click(function(e) {
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
                success: function(data) {
                    $("#brand_user").html(data.brandOwner);
                    $("#channel_type").html(data.channelType);
                },
                error: function(data) {
                    console.log('Error:', data);

                }
            });


        });

        $('.btn-sync-athena').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var id = $(this).val();
            $.get('ajaxSyncAthena/' + id, function(data) {
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

        $('.btn-recheck-channel').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var loadingText = '<i class="fa fa-spinner fa-spin"></i> Check';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var id = $(this).val();
            $.get('ajaxRecheckChannel/' + id, function(data) {
                $this.html($this.data('original-text'));
                showNotification(data.message, data.status);

                if (data.status === 'success' && data.data) {
                    // Sử dụng ID động để chọn TD cần cập nhật
                    var $growthViewsCell = $('#growth-views-' + id);
                    var $totalViewsCell = $('#total-views-' + id);

                    // Định dạng số views
                    var formattedTotalViews = number_format(data.data.views, 0, ',', '.');
                    var formattedGrowthViews = number_format(data.data.views_increase, 0, ',', '.');

                    // Xử lý thông tin tăng trưởng
                    var growthPercentage = data.data.growth_percentage + '%';
                    var growthArrow, growthClass;

                    if (data.data.growth_percentage >= 0) {
                        growthArrow = '<i class="fas fa-arrow-up growth-arrow text-success"></i>';
                        growthClass = 'text-success';
                    } else {
                        growthArrow = '<i class="fas fa-arrow-down growth-arrow text-danger"></i>';
                        growthClass = 'text-danger';
                    }

                    // Cập nhật ô growth views (lượt xem tăng)
                    $growthViewsCell.html(
                        '<span>' +
                        formattedGrowthViews + ' ' +
                        growthArrow + ' ' +
                        '<span class="growth-percentage ' + growthClass + '">' + growthPercentage +
                        '</span>' +
                        '</span>'
                    );

                    // Cập nhật ô tổng views và thời gian
                    $totalViewsCell.html(
                        '<div>' + formattedTotalViews + '</div>' +
                        '<div class="small text-muted">just now</div>'
                    );
                }
            });
        });

        $('.btn-sync-gologin').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var gologin = $this.attr("data-gologin");
            var gmail = $this.attr("data-gmail");
            $.Notification.confirm('warning', 'top center', 'Are you sure ?', 'boom');
            $(document).on('click', '.notifyjs-metro-base .boom_yes', function() {
                $(this).trigger('notify-hide');
                window.open("AutoProfile://profile/commit/?id=" + gologin + "&gmail=" + gmail + "&force=1",
                    "_blank");

            });
        });

        $('#btnExcute').click(function(e) {
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
                success: function(data) {
                    $this.html($this.data('original-text'));
                    for (var i = 0; i < data.content.length; i++) {
                        $.Notification.notify(data.status, 'top center', '', data.content[i]);
                        notify(data.content[i], "https://automusic.win/channelmanagement", "");

                    }
                    //                location.reload();
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                }
            });
        });

        $('#btnCloseDialogGroupChannel').click(function() {
            $("#data-table-group-channel").DataTable().clear().destroy();
            $.ajax({
                type: "GET",
                url: '/ajaxGetGroupChannel',
                dataType: 'json',
                success: function(data) {
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
                error: function(data) {}
            });

        });

        $("#btnCard").click(function(e) {
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
            html += '        <table class="w-100"><tr><td class="w-50"><h5 class="mb-0 mt-0 font-16"><a >' + type
                .capitalize() +
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

        $("#btnCardSubmit").click(function(e) {

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
                success: function(data) {
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    $this.html($this.data('original-text'));
                    console.log(data);
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                }
            });
        });

        Object.defineProperty(String.prototype, 'capitalize', {
            value: function() {
                return this.charAt(0).toUpperCase() + this.slice(1);
            },
            enumerable: false
        });

        $(".endsTemplate").click(function() {
            $(".endsTemplate").removeClass("endscreen-template-active");
            $(this).addClass("endscreen-template-active");
            var value = $(this).attr("data-tpl");
            $("#template_encscreen").val(value);
        });

        $("#btnCardManagement").click(function(e) {
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
                success: function(data) {
                    console.log(data);
                    $("#dialog_card_management_loading").fadeOut('fast');
                    var i = 0
                    if (data.length > 0) {
                        $.each(data, function(key, value) {
                            i = i + 1;
                            var type = 'Card';
                            if (value.type === 2) {
                                type = "Enscreen";
                            }
                            $("#tbl-card").append(
                                `<tr><td>${i}</td><td>${type}</td><td>${value.promo_link}</td><td>${value.total}</td></tr>`
                            );
                        });
                    }

                },
                error: function(data) {}
            });
        });

let channelsData = [];
let processedChannelsCount = 0; 
let successChannelsCount = 0;   
let totalChannelsCount = 0;
function chartTrackChannels() {
    processedChannelsCount = 0; 
    successChannelsCount = 0;   
    totalChannelsCount = 0;
    channelsData = [];
    
    let channelIds = [];
    try {
        const savedChannelIds = localStorage.getItem('trackChannelIds');
        if (savedChannelIds) {
            channelIds = JSON.parse(savedChannelIds);
            totalChannelsCount = channelIds.length;
        }
    } catch (error) {
        console.error("Error parsing channel IDs from localStorage:", error);
    }

    if (!channelIds.length) {
        showNotification("No channels selected for tracking", "error");
        return;
    }
    
    updateModalTitle(processedChannelsCount, successChannelsCount, totalChannelsCount);

    $('#modal_multi_chart_realtime').modal({
        backdrop: true
    });

    $("#modal_multi_chart_realtime_loading").show();
    $("#charts-container").html("");

    var chartButton = $("#chartTrackButton");

    var loadingText = '<i class="fas fa-spinner fa-spin"></i> Chart';
    var originalText = chartButton.html();
    chartButton.html(loadingText);

    if (window.eventSource) {
        window.eventSource.close();
    }

    // Khởi tạo grid layout
    initializeGrid();
    
    // Khởi tạo mảng dữ liệu trước theo thứ tự của localStorage
    channelsData = new Array(channelIds.length);
    
    const idsParam = encodeURIComponent(JSON.stringify(channelIds));
    window.eventSource = new EventSource(`getDataCharts?ids=${idsParam}`);

    window.eventSource.onmessage = function(event) {
        try {
            const data = JSON.parse(event.data);
            logger("getDataCharts event", data);
            
            if (data.status == "success" || data.status == "error") {
                $("#modal_multi_chart_realtime_loading").hide();
                processedChannelsCount++;
                
                if (data.status == "success") {
                    successChannelsCount++;
                }
                
                const index = channelIds.indexOf(data.channel_id.toString());
                if (index !== -1) {
                    channelsData[index] = data;
                    addChannelToGrid(data, index);
                } else {
                    channelsData.push(data);
                    addChannelToGrid(data, channelsData.length - 1);
                }
                
                updateModalTitle(processedChannelsCount, successChannelsCount, totalChannelsCount);
                
            } else if (data.status == "complete") {
                window.eventSource.close();
                chartButton.html(originalText);
                updateModalTitle(processedChannelsCount, successChannelsCount, totalChannelsCount);
                updateGridLayout();
                
            } else {
                processedChannelsCount++;
                updateModalTitle(processedChannelsCount, successChannelsCount, totalChannelsCount);
                showNotification(data.message || "Unknown status received", data.status || "warning");
            }
        } catch (error) {
            console.error("Error processing SSE event:", error);
        }
    };

    window.eventSource.onerror = function(error) {
        console.error("SSE Error:", error);
        window.eventSource.close();
        chartButton.html(originalText); 
        showNotification("Error receiving channel data", "error");
    };

    $('#modal_multi_chart_realtime').on('hidden.bs.modal', function() {
        if (window.eventSource) {
            window.eventSource.close();
        }
        updateChartButtonBadge();
    });
}

function initializeGrid() {
    const container = $("#charts-container");
    
    if (container.find('.sort-buttons').length === 0) {
        container.append(`
            <div class="sort-buttons">
                <button type="button" class="sort-btn" onclick="sortChannels('48h')">
                    <i class="fas fa-sort-amount-down"></i> Sort by 48h views
                </button>
                <button type="button" class="sort-btn" onclick="sortChannels('60m')">
                    <i class="fas fa-sort-amount-down"></i> Sort by 60m views
                </button>
            </div>
        `);
    }
}

function addChannelToGrid(data, index) {
    const container = $("#charts-container");
    const channel_id = data.channel_id;
    
    if ($(`.channel-col[data-channel-id="${channel_id}"]`).length > 0) {
        return;
    }
    
    const rowIndex = Math.floor(index / 2);
    const isFirstColumn = index % 2 === 0;
    
    let row = $(`#channel-row-${rowIndex}`);
    if (row.length === 0) {
        container.append(`<div id="channel-row-${rowIndex}" class="channel-row"></div>`);
        row = $(`#channel-row-${rowIndex}`);
    }
    
    const columnCount = row.children('.channel-col').length;
    
    if ((isFirstColumn && columnCount === 0) || (!isFirstColumn && columnCount === 1)) {
        row.append(createChannelColumn(data, index));
        setTimeout(() => {
            if (data.status === "success") {
                drawBarChart(`chartHour-${channel_id}`, `${data.channel_name} - Views · Last 48 hours`, 
                    data.data48hour.label, data.data48hour.value);
                drawBarChart(`chartMinute-${channel_id}`, `${data.channel_name} - Views · Last 60 minutes`, 
                    data.data60minutes.label, data.data60minutes.value);
            }
        }, 100);
    } else {
        updateGridLayout();
    }
}

function updateGridLayout() {
    const container = $("#charts-container");
    container.find('.channel-row').remove();
    const validChannelsData = channelsData.filter(item => item !== undefined);
    for (let i = 0; i < Math.ceil(validChannelsData.length / 2); i++) {
        container.append(`<div id="channel-row-${i}" class="channel-row"></div>`);
    }
    
    validChannelsData.forEach((data, index) => {
        const rowIndex = Math.floor(index / 2);
        const row = $(`#channel-row-${rowIndex}`);
        const channel_id = data.channel_id;
        let col = $(`.channel-col[data-channel-id="${channel_id}"]`);
        
        if (col.length > 0) {
            row.append(col.detach());
            col.attr('data-index', index);
        } else {
            row.append(createChannelColumn(data, index));
            setTimeout(() => {
                if (data.status === "success") {
                    drawBarChart(`chartHour-${channel_id}`, `${data.channel_name} - Views · Last 48 hours`, 
                        data.data48hour.label, data.data48hour.value);
                    drawBarChart(`chartMinute-${channel_id}`, `${data.channel_name} - Views · Last 60 minutes`, 
                        data.data60minutes.label, data.data60minutes.value);
                }
            }, 100);
        }
    });
    
    container.find('.channel-row').each(function() {
        if ($(this).children('.channel-col').length < 2) {
            $(this).append(`<div class="channel-col"></div>`);
        }
    });
}


function createChannelColumn(data, index) {
    const channel_id = data.channel_id;
    const channel_name = data.channel_name || "Unknown Channel";
    
    if (data.status === "success") {
        return `
            <div class="channel-col" data-channel-id="${channel_id}" data-index="${index}">
                <div class="channel-container">
                    <div class="channel-header p-3">
                        ${generateChannelInfoHtml(data)}
                        <div class="channel-actions">
                            <button type="button" class="action-btn move-up-btn" onclick="moveChannel(${index}, 'up')" title="Move Up">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <button type="button" class="action-btn move-down-btn" onclick="moveChannel(${index}, 'down')" title="Move Down">
                                <i class="fas fa-arrow-down"></i>
                            </button>
                            <button type="button" class="action-btn remove-chart-btn" onclick="removeChannelFromCharts(${channel_id}, '${channel_name.replace(/'/g, "\\'")}')" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="chart-content p-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div id="chartHour-wrap-${channel_id}" class="chart-container">
                                    <canvas id="chartHour-${channel_id}"></canvas>
                                </div>
                                <div class="text-center mt-2">
                                    <small>Views (Last 48 hours): <strong id="last-48-hour-${channel_id}">${number_format(data.data48hour.total48, 0, ',', '.')}</strong></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="chartMinute-wrap-${channel_id}" class="chart-container">
                                    <canvas id="chartMinute-${channel_id}"></canvas>
                                </div>
                                <div class="text-center mt-2">
                                    <small>Views (Last 60 minutes): <strong id="last-60-minute-${channel_id}">${number_format(data.data60minutes.total60, 0, ',', '.')}</strong></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else {
        return `
            <div class="channel-col" data-channel-id="${channel_id}" data-index="${index}">
                <div class="channel-container">
                    <div class="channel-header p-3">
                        ${generateChannelInfoHtml(data)}
                        <div class="channel-actions">
                            <button type="button" class="action-btn move-up-btn" onclick="moveChannel(${index}, 'up')" title="Move Up">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <button type="button" class="action-btn move-down-btn" onclick="moveChannel(${index}, 'down')" title="Move Down">
                                <i class="fas fa-arrow-down"></i>
                            </button>
                            <button type="button" class="action-btn remove-chart-btn" onclick="removeChannelFromCharts(${channel_id}, '${channel_name.replace(/'/g, "\\'")}')" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="chart-content p-3">
                        ${generateErrorHtml(channel_id, channel_name, data.message || "Unknown error occurred")}
                    </div>
                </div>
            </div>
        `;
    }
}

function moveChannel(index, direction) {
    let newIndex;
    
    if (direction === 'up') {
        if (index === 0) return; 
        newIndex = index - 1;
    } else { 
        if (index === channelsData.length - 1) return; // Đã ở vị trí cuối cùng
        newIndex = index + 1;
    }
    const validChannelsData = channelsData.filter(item => item !== undefined);
    [validChannelsData[index], validChannelsData[newIndex]] = [validChannelsData[newIndex], validChannelsData[index]];
    channelsData = validChannelsData;
    updateLocalStorageOrder();
    updateGridLayout();
}

function updateLocalStorageOrder() {
    const channelIds = channelsData
        .filter(item => item !== undefined)
        .map(data => data.channel_id);
    localStorage.setItem('trackChannelIds', JSON.stringify(channelIds));
    updateChartButtonBadge();
}

function sortChannels(criterion) {
    // Đánh dấu nút sắp xếp đang active
    $(".sort-btn").removeClass("active");
    $(`.sort-btn:contains('${criterion === '48h' ? '48h' : '60m'}')`).addClass("active");
    
    // Lọc để loại bỏ các phần tử undefined
    let validData = channelsData.filter(item => item !== undefined);
    
    validData.sort((a, b) => {
        if (a.status !== "success" && b.status !== "success") return 0;
        if (a.status !== "success") return 1; // Đưa kênh lỗi xuống dưới
        if (b.status !== "success") return -1;
        
        if (criterion === '48h') {
            return b.data48hour.total48 - a.data48hour.total48; // Sắp xếp giảm dần theo 48h
        } else {
            return b.data60minutes.total60 - a.data60minutes.total60; // Sắp xếp giảm dần theo 60m
        }
    });
    
    // Gán lại mảng đã sắp xếp
    channelsData = validData;
    
    // Cập nhật localStorage với thứ tự mới
    updateLocalStorageOrder();
    
    // Vẽ lại grid
    updateGridLayout();
}

// Hàm tạo HTML cho phần hiển thị lỗi
function generateErrorHtml(channel_id, channel_name, errorMessage) {
    return `
        <div class="horizontal-error">
            <i class="fas fa-exclamation-circle text-danger error-icon"></i>
            <div class="error-info">
                <p class="mb-0">${errorMessage}</p>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="syncookie(${channel_id}, '${channel_name.replace(/'/g, "\\'")}')">
                Sync cookie
            </button>
        </div>
    `;
}

// Hàm tạo HTML cho phần channel-info
function generateChannelInfoHtml(data) {
    return `
        <div class="d-flex align-items-center">
            <div class="channel-avatar mr-3">
                <img src="${data.channel_thumb || 'path/to/default-avatar.png'}" alt="${data.channel_name}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
            </div>
            <div>
                <h6 class="mb-1">${data.channel_name}</h6>
                <div class="small text-muted">
                    <span class="mr-2"><i class="fas fa-users"></i> ${number_format(data.subs || 0, 0, ',', '.')}</span>
                    <span><i class="fas fa-eye"></i> ${number_format(data.views || 0, 0, ',', '.')}</span>
                </div>
            </div>
        </div>
    `;
}

function removeChannelFromCharts(channelId, channelName) {
    // Tìm vị trí của kênh trong mảng dữ liệu
    const index = channelsData.findIndex(data => data && data.channel_id === channelId);
    
    if (index !== -1) {
        // Xóa kênh khỏi mảng dữ liệu
        channelsData.splice(index, 1);
        
        // Giảm số lượng kênh đã xử lý
        successChannelsCount--;
        totalChannelsCount--;
        updateModalTitle(processedChannelsCount, successChannelsCount, totalChannelsCount);
        
        // Hủy chart để tránh memory leak
        const hourChart = document.getElementById(`chartHour-${channelId}`);
        const minuteChart = document.getElementById(`chartMinute-${channelId}`);
        
        if (hourChart && hourChart.__chart) {
            hourChart.__chart.destroy();
        }
        
        if (minuteChart && minuteChart.__chart) {
            minuteChart.__chart.destroy();
        }
        
        // Cập nhật localStorage
        updateLocalStorageOrder();
        
        // Cập nhật lại grid layout
        updateGridLayout();
        
        // Hiển thị thông báo
        showNotification(`Channel "${channelName}" removed from chart`, "info");
        
        // Nếu không còn kênh nào, hiển thị thông báo
        if (channelsData.filter(item => item !== undefined).length === 0) {
            $("#charts-container").html(`
                <div class="text-center p-5">
                    <div class="mb-3"><i class="fas fa-chart-bar fa-4x text-muted"></i></div>
                    <h4 class="text-muted">No channels to display</h4>
                    <p>All channels have been removed. Close this window and add channels to track.</p>
                </div>
            `);
        }
    }
}

function updateLocalStorageOrder() {
    const channelIds = channelsData
        .filter(item => item !== undefined)
        .map(data => data.channel_id);
    
    localStorage.setItem('trackChannelIds', JSON.stringify(channelIds));
    
    // Cập nhật badge trên nút Chart
    updateChartButtonBadge();
}

        function updateModalTitle(processed, success, total) {
            const titleElement = $('#modal_multi_chart_realtime .modal-title');
            const isLoading = processed < total;
            const loadingIcon = isLoading ? '<i class="fas fa-spinner fa-spin ml-2"></i>' : '';
            titleElement.html(
                `<span class="mr-2">Channel Analytics</span> <span class="badge badge-info mb-0">${success}/${total} channels loaded ${loadingIcon}</span>`
                );
        }

        function updateAllTrackButtons() {
            let channelIds = getTrackedChannels();
            $('.track-channel-btn').each(function() {
                const channelId = parseInt($(this).data('channel-id'));
                const isTracked = channelIds.includes(channelId);

                updateSingleTrackButton($(this), isTracked);
            });
        }

        function updateSingleTrackButton(button, isTracked) {
            if (isTracked) {
                button.html(
                    '<i class="fas fa-times mr-2 text-danger"></i> <span class="text-danger">Remove Tracking</span>');
                button.attr('onclick', `removeChannelFromTracking(${button.data('channel-id')})`);
            } else {
                button.html('<i class="fas fa-plus mr-2"></i> Add Tracking');
                button.attr('onclick', `addChannelToTracking(${button.data('channel-id')})`);
            }
        }

        function getTrackedChannels() {
            try {
                const savedChannelIds = localStorage.getItem('trackChannelIds');
                return savedChannelIds ? JSON.parse(savedChannelIds) : [];
            } catch (error) {
                console.error("Error parsing tracked channels:", error);
                return [];
            }
        }

        function addChannelToTracking(channelId) {
            let channelIds = getTrackedChannels();
            channelId = parseInt(channelId);
            if (channelIds.length >= 10) {
                showNotification("You can track maximum 10 channels. Please remove some channels first.", "warning");
                return;
            }
            if (!channelIds.includes(channelId)) {
                channelIds.push(channelId);
                localStorage.setItem('trackChannelIds', JSON.stringify(channelIds));
                updateChartButtonBadge();
                const button = $(`.track-channel-btn[data-channel-id="${channelId}"]`);
                updateSingleTrackButton(button, true);

                showNotification("Channel added to tracking list", "success");
            }
        }

        function removeChannelFromTracking(channelId) {
            let channelIds = getTrackedChannels();
            channelId = parseInt(channelId);
            const initialLength = channelIds.length;
            channelIds = channelIds.filter(id => id !== channelId);

            if (channelIds.length < initialLength) {
                localStorage.setItem('trackChannelIds', JSON.stringify(channelIds));
                updateChartButtonBadge();
                const button = $(`.track-channel-btn[data-channel-id="${channelId}"]`);
                updateSingleTrackButton(button, false);
                showNotification("Channel removed from tracking list", "info");
            }
        }

        function updateChartButtonBadge() {
            let channelIds = getTrackedChannels();

            const channelCount = channelIds.length;
            const chartButton = $('#chartTrackButton');
            chartButton.find('.filter-badge').remove();
            if (channelCount > 0) {
                chartButton.html(
                    `<i class="fas fa-chart-line"></i> Chart <span style="background-color: #52bb56;" class="filter-badge">${channelCount}</span>`
                    );
            } else {
                chartButton.html(`<i class="fas fa-chart-line"></i> Chart`);
            }
        }
        
        function syncookie(channelId, channelName) {
            const syncButton = $(`#channel-container-${channelId} .btn-outline-primary`);
            const originalText = syncButton.html();
            syncButton.html('<i class="fas fa-spinner fa-spin"></i> Syncing...');
            syncButton.prop('disabled', true);
            $.ajax({
                url: '/ajaxChannel',
                type: 'POST',

                data: {
                    _token: '{{ csrf_token() }}',
                    action: 28,
                    chkChannelAll: [channelId]
                },
                success: function(response) {
                    syncButton.html(originalText);
                    syncButton.prop('disabled', false);
                    if(response.status=="success"){
                        showNotification("Send command sync cookie successfully. Please wait for the system to work", "success");
                    }
                },
                error: function(xhr) {
                    console.error('Error syncookie:', xhr.responseText);
                    showNotification("Failed to sync cookie", "error");
                    syncButton.html(originalText);
                    syncButton.prop('disabled', false);
                }
            });

        }
    
        $(document).ready(function() {
            updateAllTrackButtons();
            updateChartButtonBadge();

//            $(document).on('click', '.gmail-count', function(e) {
//                e.preventDefault();
//                e.stopPropagation();
//
//                var count = $(this).text(); // Lấy số count (1, 2, 3, ...)
//
//                // Set giá trị cho selectbox email_frequency
//                $('#email_frequency').val([count]);
//
//                // Trigger form submit để filter
//                $('#form-search').submit();
//            });
            $(document).on('click', '.gmail-count', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var count = $(this).text();

                // Clear tất cả checkbox trước
                $('input[name="email_frequency[]"]').prop('checked', false);

                // Check checkbox tương ứng
                $('input[name="email_frequency[]"][value="' + count + '"]').prop('checked', true);

                // Submit form
                $('#form-search').submit();
            });
            $('.gmail-count').attr('title', 'Click to filter by this email count');
        });
    </script>
@endsection
