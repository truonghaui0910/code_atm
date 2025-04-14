<div class="modal fade" id="dialog_import_campaign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-80">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class=" ti-plus fa-fw"></i> <span id="dialog_import_campaign_title">Add Campaign</span></h4>
            </div>
            <div id="dialog-loading-add-campaign" class="disp-none" style="text-align: center;"><i
                    class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>

            <div class="modal-body">
                <form id="formadd" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="cam_id" id="cam_id" class="form-control">
                    <div class="card-box">
                        <ul class="nav nav-tabs tabs-bordered">
                            <li class="nav-item">
                                <a href="#home-b1" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                    Official
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#profile-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                    Mix-Lyric
                                </a>
                            </li>
                            <li class="nav-item tab-meta">
                                <a href="#meta-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                    Meta Data
                                </a>
                            </li>
                            <li class="nav-item tab-meta">
                                <a href="#fulfillment-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                    Fulfillment
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="home-b1">
                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Official Video URL <span class="color-red">(Not save)</span> <span class="official-sync disp-none"><i class="fa fa-circle-o-notch fa-spin"></i></span></label>
                                            <div class="col-12">
                                                <input id="official_video" type="text" name="official_video" class="form-control" style="background: #ccc">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Start Views</label>
                                            <div class="col-12">
                                                <input id="start_view_official" type="text" name="start_view_official" class="form-control" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Start Likes</label>
                                            <div class="col-12">
                                                <input id="start_like_official" type="number" name="start_like_official" class="form-control" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Start Comments</label>
                                            <div class="col-12">
                                                <input id="start_cmt_official" type="number" name="start_cmt_official" class="form-control" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Start Subs</label>
                                            <div class="col-12">
                                                <input id="start_sub_official" type="number" name="start_sub_official" class="form-control" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="official_orther_version">

                                </div>

                                <div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Target Views</label>
                                            <div class="col-12">
                                                <div class="input-group">
                                                    <!--<span class="input-group-addon input-group-addon-custom">Week4</span>-->
                                                    <input id="target_view" type="text"  name="target_view" placeholder="ex: 1M"
                                                           class="form-control"
                                                           onkeypress="return validateInputTarget(event)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Target Likes</label>
                                            <div class="col-12">
                                                <input id="target_like" type="number" name="target_like" class="form-control" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Target Comments</label>
                                            <div class="col-12">
                                                <input id="target_cmt" type="number" name="target_cmt" class="form-control" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Target Subs</label>
                                            <div class="col-12">
                                                <input id="target_sub" type="number" name="target_sub" class="form-control" value="0">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Amount($)</label>
                                            <div class="col-12">
                                                <input id="money" type="number" name="money" class="form-control" value="0"
                                                       placeholder="Amount received ex: 5000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Amount Paid($)</label>
                                            <div class="col-12">
                                                <input id="amount_paid" type="number" name="amount_paid" class="form-control" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Bass Percent(%) <span class="text-muted font-13">(-1 not pay)</span></label>
                                            <div class="col-12">
                                                <input id="bass_percent" type="number" name="bass_percent" class="form-control" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <span id="badge-sub-count" class="position-absolute badge badge-danger" style="left: -7px;bottom: 45px;">0</span>
                                            <label class="col-12 col-form-label">&nbsp;</label>
                                            <button id="btn-configs-sub" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#allocationModal">
                                                <i class="fa fa-cogs"></i> Submission
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                <div class="row">
                                    <div class="col-md-6 target-w4">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Crypto Views</label>
                                            <div class="col-12 m-b-10">
                                                <div class="input-group">
                                                    <span class="input-group-addon input-group-addon-custom">USD</span>
                                                    <input id="crypto_usd" type="number"  name="crypto_usd" value="0" class="form-control">
                                                    <span class="input-group-addon input-group-addon-custom">Last</span>
                                                    <input id="crypto_usd_last" type="number"  name="crypto_usd_last" value="0" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-12 m-b-10">
                                                <div class="input-group">
                                                    <span class="input-group-addon input-group-addon-custom">Views</span>
                                                    <input id="crypto_view" type="text"  name="crypto_view" value="0" class="form-control m-r-5"
                                                           onkeypress="return validateInputTarget(event)">
                                                    <span class="input-group-addon input-group-addon-custom">Last</span>
                                                    <input id="crypto_view_last" type="text"  name="crypto_view_last" value="0" class="form-control m-r-5"
                                                           onkeypress="return validateInputTarget(event)">

                                                    <select id="crypto_view_run" name="crypto_view_run" class="select2_multiple form-control">
                                                        <option value="0">Prepare</option>
                                                        <option value="1">Running</option>
                                                        <option value="2">Finished</option>
                                                    </select>
                                                </div>

                                            </div>
                                            <div class="col-12 m-b-10">
                                                <div class="input-group">
                                                    <span class="input-group-addon input-group-addon-custom">Likes</span>
                                                    <input id="crypto_like" type="number"  name="crypto_like" value="0" class="form-control m-r-5">
                                                    <span class="input-group-addon input-group-addon-custom">Last</span>
                                                    <input id="crypto_like_last" type="number"  name="crypto_like_last" value="0" class="form-control m-r-5">
                                                    <select id="crypto_like_run" name="crypto_like_run" class="select2_multiple form-control">
                                                        <option value="0">Prepare</option>
                                                        <option value="1">Running</option>
                                                        <option value="2">Finished</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 m-b-10">
                                                <div class="input-group">
                                                    <span class="input-group-addon input-group-addon-custom">Subs</span>
                                                    <input id="crypto_sub" type="number"  name="crypto_sub" value="0" class="form-control m-r-5">
                                                    <span class="input-group-addon input-group-addon-custom">Last</span>
                                                    <input id="crypto_sub_last" type="number"  name="crypto_sub_last" value="0" class="form-control m-r-5">

                                                    <select id="crypto_sub_run" name="crypto_sub_run" class="select2_multiple form-control">
                                                        <option value="0">Prepare</option>
                                                        <option value="1">Running</option>
                                                        <option value="2">Finished</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 m-b-10">
                                                <div class="input-group">
                                                    <span class="input-group-addon input-group-addon-custom">Cmts</span>
                                                    <input id="crypto_cmt" type="number"  name="crypto_cmt" value="0" class="form-control m-r-5">
                                                    <span class="input-group-addon input-group-addon-custom">Last</span>
                                                    <input id="crypto_cmt_last" type="number"  name="crypto_cmt_last" value="0" class="form-control m-r-5">
                                                    <select id="crypto_cmt_run" name="crypto_cmt_run" class="select2_multiple form-control m-r-5">
                                                        <option value="0">Prepare</option>
                                                        <option value="1">Running</option>
                                                        <option value="2">Finished</option>
                                                    </select>
                                                    <span class="input-group-addon input-group-addon-custom">Group</span>
                                                    <input id="crypto_group_comment" type="text"  name="crypto_group_comment" value="" class="form-control m-r-5">
                                                </div>
                                            </div>

                                            <div class="col-12 m-b-10">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Comment VideoID</label>
                                                    <div class="col-12">
                                                        <input id="crypto_cmt_link" type="text"  name="crypto_cmt_link" value="" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 m-b-10">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Comment Content <span id="cmt_count">(0)</span></label>
                                                    <div class="col-12">
                                                        <textarea class="form-control" rows="5" id="crypto_cmt_content" name="crypto_cmt_content"
                                                                  spellcheck="false" placeholder="Comments Content"
                                                                  style="line-height: 1.25;height: 110px"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 m-b-10">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Comment Log <span id="cmt_count_finish">(0)</span></label>
                                                    <div class="col-12">
                                                        <textarea class="form-control" rows="5" id="crypto_cmt_content_finish" name="crypto_cmt_content_finish"
                                                                  spellcheck="false" placeholder=""
                                                                  style="line-height: 1.25;height: 110px"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6  m-b-10">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Schedules</label>
                                                    <div class="col-12">
                                                        <select id="cmt_schedule" name="cmt_schedule" class="select2_multiple form-control">
                                                            <option value="1">Daily</option>
                                                            <option value="7">Weekly</option>
                                                            <option value="30">Monthly</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6  m-b-10">
                                                <div class="form-group row">
                                                    <label class="col-12 col-form-label">Number Cmt</label>
                                                    <div class="col-12">
                                                        <input id="cmt_number" type="number" name="cmt_number" class="form-control" value="0"
                                                               placeholder="Number of comments">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-3 target-w4">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Adsense Views</label>
                                            <div class="col-12 m-b-10">
                                                <div class="input-group">
                                                    <span class="input-group-addon input-group-addon-custom">USD</span>
                                                    <input id="adsense_usd" type="number"  name="adsense_usd" value="0"
                                                           class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-12 m-b-10">
                                                <div class="input-group">
                                                    <span class="input-group-addon input-group-addon-custom">Views</span>
                                                    <input id="adsense_view" type="text"  name="adsense_view" value="0" class="form-control m-r-5"
                                                           onkeypress="return validateInputTarget(event)">
                                                    <!--                                                    <div class="checkbox checkbox-primary">
                                                                                                            <input id="adsense_view_run" type="checkbox" name="adsense_view_run">
                                                                                                            <label for="adsense_view_run" class="m-t-6">
                                                                                                                Running
                                                                                                            </label>
                                                                                                        </div>  -->
                                                    <select id="adsense_view_run" name="adsense_view_run" class="select2_multiple form-control">
                                                        <option value="0">Prepare</option>
                                                        <option value="1">Running</option>
                                                        <option value="2">Finished</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-3 target-w4">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Facebooks</label>
                                            <div class="col-12 m-b-10">
                                                <div class="input-group">
                                                    <span class="input-group-addon input-group-addon-custom">USD</span>
                                                    <input id="facebook_usd" type="number"  name="facebook_usd" value="0"
                                                           class="form-control">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane fade" id="profile-b1">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Campaign Type</label>
                                            <div class="col-12">
                                                <select id="cam_type" name="cam_type"
                                                        class="select2_multiple form-control cam_type">
                                                    <option value="1">Promos</option>
                                                    <!--<option value="4">Revshares</option>-->
                                                    <option value="5">Submission</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 div_cam_type_4 disp-none">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Revshare Client</label>
                                            <div class="col-12">
                                                <select id="revshare_client" name="revshare_client"
                                                        class="select2_multiple form-control">
                                                    <option value="-1">--Select--</option>
                                                    <option value="antifragile">ANTIFRAGILE</option>
                                                    <option value="spring_sound">SPRING_SOUND</option>
                                                    <option value="french_montana">FRENCH_MONTANA</option>
                                                    <option value="wmg">WMG</option>
                                                    <option value="empire">EMPIRE</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 div_cam_type_5 disp-none">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Channel Name</label>
                                            <div class="col-12">
                                                <input id="channelName" type="text" name="channelName"
                                                       class="form-control" placeholder="Name of channel">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Status</label>
                                            <div class="col-12">
                                                <select id="status" name="status" class="select2_multiple form-control">
                                                    {!!$status!!}
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2 div_cam_type_1">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Seller</label>
                                            <div class="col-12">
                                                <select id="seller" name="seller" class="select2_multiple form-control">
                                                    <option value="360PROMO">360PROMO</option>
                                                    <option value="JAMES">JAMES</option>
                                                    <option value="DARRELL">DARRELL</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">KPI Month</label>
                                        <div class="col-12">
                                            <select id="period" name="period" class="select2_multiple form-control">
                                                {!!$month_select!!}
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Campaign Name</label>
                                            <div class="col-12">
                                                <input id="campaign_name" type="text" name="campaign_name"
                                                       class="form-control" placeholder="Artist - Song name">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Campaign Start Date</label>
                                            <div class="col-12">
                                                <input id="campaign_start_date" type="date" name="campaign_start_date" class="form-control" readonly>
                                                <!--data-mask="99/99/9999"-->
                                                <!--value="<?php //echo gmdate("m/d/Y", time() + 7 * 3600);         ?>"-->
                                                <!--class="form-control">-->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Campaign Start Time</label>
                                            <div class="col-12">
                                                <input id="campaign_start_time" type="text" name="campaign_start_time" readonly
                                                       placeholder="H:i:s"
                                                       value="<?php echo gmdate("H:i:s", time() + 7 * 3600); ?>"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Campaign Duration</label>
                                            <div class="col-12">
                                                <select id="campaign_duration" name="campaign_duration"
                                                        class="select2_multiple form-control">
                                                    @for($i=1;$i<=6;$i++) @if($i==1) <option value="{{$i}}">{{$i}} month
                                                    </option>
                                                    @else
                                                    <option value="{{$i}}">{{$i}} months</option>
                                                    @endif
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Genre</label>
                                            <div class="col-12">
                                                <!--<input type="text" name="genre" class="form-control">-->
                                                <select id="genre" name="genre" class="select2_multiple form-control" readonly>
                                                    {!!$channel_genre!!}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Label <i class="fa fa-copy" onclick="copyInputValue('label')"></i></label>
                                            <div class="col-12">
                                                <input id="label" type="text" name="label" class="form-control" readonly="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Artist <i class="fa fa-copy" onclick="copyInputValue('artist')"></i></label>
                                            <div class="col-12">
                                                <input id="artist" type="text" name="artist" class="form-control" readonly="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Song Name <i class="fa fa-copy" onclick="copyInputValue('songname')"></i></label>
                                            <div class="col-12">
                                                <input id="songname" type="text" name="songname" class="form-control" readonly="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Artist Channel URL <i class="fa fa-copy" onclick="copyInputValue('artists_channel')"></i></label>
                                            <div class="col-12">
                                                <input id="artists_channel" type="text" name="artists_channel" readonly=""
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Artist Playlist URL <i class="fa fa-copy" onclick="copyInputValue('artists_playlist')"></i></label>
                                            <div class="col-12">
                                                <input id="artists_playlist" type="text" name="artists_playlist" readonly=""
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Artist Social Links <i class="fa fa-copy" onclick="copyInputValue('artists_social')"></i></label>
                                            <div class="col-12">
                                                <!--<input type="text" name="artists_social" class="form-control">-->
                                                <textarea id="artists_social" class="form-control" rows="5" readonly=""
                                                          name="artists_social" spellcheck="false"
                                                          style="line-height: 1.25;height: 130px"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 disp-none">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Video Description<i class="fa fa-copy" onclick="copyInputValue('desc_video')"></i></label>
                                            <div class="col-12">
                                                <!--<input type="text" name="artists_social" class="form-control">-->
                                                <textarea id="desc_video" class="form-control" rows="5" 
                                                          name="desc_video" spellcheck="false"
                                                          style="line-height: 1.25;height: 130px"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Spotify Song ID <i class="fa fa-copy" onclick="copyInputValue('spotify_id')"></i></label>
                                            <div class="col-12">
                                                <input id="spotify_id" type="text" name="spotify_id" class="form-control" readonly="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Deezer Artists ID</label>
                                            <div class="col-12">
                                                <input id="deezer_artist_id" type="number" name="deezer_artist_id"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Tier</label>
                                            <div class="col-12">
                                                <select id="tier" name="tier"
                                                        class="select2_multiple form-control tier">
                                                    <option value="2">Tier 2</option>
                                                    <option value="1">Tier 1</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-12 col-form-label">Audio File <span
                                                id="link_audio"></span></label>
                                        <div class="row col-md-12">
                                            <div class="col-md-8">
                                                <input type="hidden" id='audio_url' name='audio_url' value='' />
                                                <input id="audio_upload" type="file" name="audio_upload"
                                                       class="form-control" accept=".mp3,.wav"
                                                       style="line-height: 1.25;content: Button">
                                            </div>
                                            <div class="col-md-4">
                                                <button type="button" class="btn btn-outline-warning btn-submit-upload"
                                                        value='audio'><i class="fa fa-upload"></i> Upload</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Bitly URL <i class="fa fa-copy" onclick="copyInputValue('bitly_url')"></i></label>
                                            <div class="col-12">
                                                <input id="bitly_url" type="text" name="bitly_url" class="form-control" readonly="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Guest Artist Name 1 <i class="fa fa-copy" onclick="copyInputValue('guest_artist_1')"></i></label>
                                            <div class="col-12">
                                                <input id="guest_artist_1" type="text" name="guest_artist_1" readonly=""
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Guest Artist Name 2 <i class="fa fa-copy" onclick="copyInputValue('guest_artist_2')"></i></label>
                                            <div class="col-12">
                                                <input id="guest_artist_2" type="text" name="guest_artist_2" readonly=""
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Guest Artist Name 3 <i class="fa fa-copy" onclick="copyInputValue('guest_artist_3')"></i></label>
                                            <div class="col-12">
                                                <input id="guest_artist_3" type="text" name="guest_artist_3" readonly=""
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Promos keywords <i class="fa fa-copy" onclick="copyInputValue('promo_keywords')"></i></label>
                                            <div class="col-12">
<!--                                                <input id="promo_keywords" type="text" name="promo_keywords"
                                                       class="form-control">-->
                                                    <textarea id="promo_keywords" class="form-control" rows="5" 
                                                          name="promo_keywords" spellcheck="false"
                                                          style="line-height: 1.25;height: 110px"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Hashtags <i class="fa fa-copy" onclick="copyInputValue('hashtags')"></i></label>
                                            <div class="col-12">
                                                <input id="hashtags" type="text" name="hashtags"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Content Drive <i class="fa fa-copy" onclick="copyInputValue('content_drive')"></i></label>
                                            <div class="col-12">
                                                <input id="content_drive" type="text" name="content_drive"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </div> 
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Total Target <i
                                                    class="fa fa-question-circle-o" data-html="true"
                                                    data-toggle="tooltip" data-placement="top"
                                                    data-template="<div class='tooltip' role='tooltip'><div class='arrow'></div><div class='tooltip-inner' style='max-width: 400px;'></div></div>"
                                                    title="<p style='text-align:left'>K = 1000 M =1000,000<br><br>Target total: The number of views the campaign wants to achieve.</p>"></i>
                                            </label>
                                            <div class="col-12">
                                                <input id="target_total" type="text" name="target_total" readonly=""
                                                       class="form-control" onkeypress="return validateInputTarget(event)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Note</label>
                                            <div class="col-12">
                                                <input id="some_note" type="text" name="some_note" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-md-6 col-12 col-form-label">Hook <i class="fa fa-copy" onclick="copyInputValue('hook')"></i></label>
                                            <label id="hookCount" class="col-md-6 col-12 col-form-label text-right"></label>
                                            <div class="col-12">
                                                <input id="hook" type="text" name="hook" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <input id="target" type="hidden" name="target" class="form-control">
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Lyrics <i class="fa fa-copy" onclick="copyInputValue('lyrics')"></i></label>
                                            <div class="col-12">
                                                <textarea class="form-control" rows="5" id="lyrics" name="lyrics" readonly=""
                                                          spellcheck="false"
                                                          style="line-height: 1.25;height: 200px"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane fade tab-meta" id="meta-b1">

                                <div class="row">
                                    <div class="col col-sm-6 d-none d-sm-flex flex-column">
                                        <div class="row flex-grow-1">
                                            <div class="col-sm-12">
                                                <div class="card-box" style="border: 1px solid rgb(54 64 74 / 28%);">
                                                    <h4 class="m-t-0 m-b-20 header-title"><b>ARTIST</b></h4>
                                                    <div class="todoapp">
                                                        <div class="text-center">
                                                            <div class="member-card">
                                                                <div class="thumb-xl member-thumb m-b-10 center-block">
                                                                    <a id="artist_picture_href" target="_blank" href=""><img id="artist_picture"  src="" class="rounded-circle img-thumbnail" style="width: 150px;height: 150px"></a>
                                                                </div>

                                                                <div class="">
                                                                    <h5 id="artis_info_name" class="m-b-5">Mark McKnight</h5>
                                                                    <p id="artis_info_email" class="text-muted">xxx@email.com</p>
                                                                </div>

                                                                <div class="text-left m-t-20">
                                                                    <p class="text-muted m-b-0"><strong>Hometown :</strong> <span id="hometown" class="m-l-15">Mark A. McKnight</span></p>

                                                                    <p class="text-muted m-b-0"><strong>City :</strong><span id="city"class="m-l-15">City</span></p>


                                                                    <p class="text-muted m-b-0"><strong>Describe Artist :</strong><br> 
                                                                        <textarea id="describe_yourself" class="form-control" rows="5"spellcheck="false" style="line-height: 1.25;height: 100px"></textarea></p>

                                                                    <p class="text-muted m-b-0"><strong>Style Music :</strong><br> 
                                                                        <textarea id="style_music" class="form-control" rows="5"spellcheck="false" style="line-height: 1.25;height: 100px"></textarea></p>

                                                                    <p class="text-muted m-b-0"><strong>Bio :</strong><br> 
                                                                        <textarea id="bio" class="form-control" rows="5"spellcheck="false" style="line-height: 1.25;height: 100px"></textarea></p>

                                                                    <p class="text-muted m-b-0"><strong>Related Artists :</strong><br> 
                                                                        <textarea id="related" class="form-control" rows="5"spellcheck="false" style="line-height: 1.25;height: 100px"></textarea></p>

                                                                    <p class="text-muted m-b-0"><strong>Similar Artists :</strong><br> 
                                                                        <textarea id="similar" class="form-control" rows="5"spellcheck="false" style="line-height: 1.25;height: 100px"></textarea></p>

                                                                    <p class="text-muted m-b-0"><strong>Biggest media coverage :</strong><br> 
                                                                        <textarea id="biggest_media_coverage" class="form-control" rows="5"spellcheck="false" style="line-height: 1.25;height: 100px"></textarea></p>

                                                                    <p class="text-muted m-b-0"><strong>Most popular songs :</strong><br> 
                                                                        <textarea id="popular_songs" class="form-control" rows="5"spellcheck="false" style="line-height: 1.25;height: 100px"></textarea></p>

                                                                    <p class="text-muted m-b-0"><strong>Biggest collaborators :</strong><br> 
                                                                        <textarea id="biggest_collaborators" class="form-control" rows="5"spellcheck="false" style="line-height: 1.25;height: 100px"></textarea></p>

                                                                    <p class="text-muted m-b-0"><strong>Publishing Company :</strong> <span id="publishing_company" class="m-l-15">USA</span></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row flex-grow-1">
                                            <div class="col-sm-12">
                                                <div class="card-box" style="border: 1px solid rgb(54 64 74 / 28%);">
                                                    <h4 class="m-t-0 m-b-20 header-title"><b>Press Release</b></h4>
                                                    <div class="todoapp">
                                                        <div class="">
                                                            <div class="member-card">
                                                                <input type="hidden" name="press_release" id="press_release"/>
                                                                <!--<textarea id="press_release" name="press_release" class="form-control" rows="5" spellcheck="false" style="line-height: 1.25;height: 400px"></textarea>-->
                                                            <div id="summernote_press_release"></div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="card-box" style="border: 1px solid rgb(54 64 74 / 28%);">
                                            <h4 class="m-t-0 m-b-20 header-title"><b>RELEASE</b></h4>
                                            <div class="todoapp">
                                                <div class="row">
                                                    <div id="div_pic_single" class="col-md-4 thumb-xl member-thumb m-b-10 center-block">
                                                        <h6 class="m-b-5">Single Art</h6>
                                                        <a id="pic_single_href" target="_blank" href=""><img id="pic_single" src="assets/images/users/avatar-1.jpg" class=" img-thumbnail" style="width: 150px"></a>
                                                    </div>
                                                    <div id="div_pic_album" class="col-md-4 thumb-xl member-thumb m-b-10 center-block">
                                                        <h6 class="m-b-5">Album Art</h6>
                                                        <a id="pic_album_href" target="_blank" href=""><img id="pic_album" src="assets/images/users/avatar-1.jpg" class=" img-thumbnail"  style="width: 150px"></a>
                                                    </div>
                                                    <div id="div_pic_logo" class="col-md-4 thumb-xl member-thumb m-b-10 center-block">
                                                        <h6 class="m-b-5">Logo</h6>
                                                        <a id="pic_logo_href" target="_blank" href=""><img id="pic_logo" src="assets/images/users/avatar-1.jpg" class=" img-thumbnail"  style="width: 150px"></a>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div id="div_pic_banner" class="col-md-4 thumb-xl member-thumb m-b-10 center-block">
                                                        <h6 class="m-b-5">Banner</h6>
                                                        <a id="pic_banner_href" target="_blank" href=""><img id="pic_banner" src="assets/images/users/avatar-1.jpg" class=" img-thumbnail"  style="width: 150px"></a>
                                                    </div>

                                                    <div id="div_pic_main_profile" class="col-md-4 thumb-xl member-thumb m-b-10 center-block">
                                                        <h6 class="m-b-5">Main Profile Pic</h6>
                                                        <a id="pic_main_profile_href" target="_blank" href=""><img id="pic_main_profile" src="assets/images/users/avatar-1.jpg" class=" img-thumbnail"  style="width: 150px"></a>
                                                    </div>

                                                </div>
                                                <div id="div_pic_press" class="row">
                                                </div>
                                                <p class="text-muted m-b-0"><strong>Song Quote :</strong> <span id="song_quote" class="m-l-15">Song Quote</span></p>
                                                <div class="col-md-12 ">
                                                    <div class="form-group row">
                                                        <label class="col-12 col-form-label"><strong>Smart Link :</strong></label>
                                                        <div class="col-12">
                                                            <input id="smart_link" type="text" name="smart_link" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--<p class="text-muted m-b-0"><strong>Smart Link :</strong> <span id="smart_link" class="m-l-15">Smart Link</span></p>-->
                                                <p class="text-muted m-b-0"><strong>Preferred Font :</strong> <span id="font" class="m-l-15">Font</span></p>
                                                <p class="text-muted m-b-0"><strong>Producer :</strong><br> 
                                                    <textarea id="producer" class="form-control" rows="5" spellcheck="false" style="line-height: 1.25;height: 100px"></textarea></p>

                                                <p class="text-muted m-b-0"><strong>Producer Collaborators :</strong><br> 
                                                    <textarea id="producer_collaborators" class="form-control" rows="5" spellcheck="false" style="line-height: 1.25;height: 100px"></textarea></p>

                                                <p class="text-muted m-b-0"><strong>Guests :</strong><br> 
                                                    <textarea id="guest_artists" class="form-control" rows="5" spellcheck="false" style="line-height: 1.25;height: 100px"></textarea></p>

                                                <p class="text-muted m-b-0"><strong>Engagement :</strong> 
                                                    <span id="engagement" class="m-l-15"></span></p>

                                                <p class="text-muted m-b-0"><strong>Video Clean :</strong> 
                                                    <span id="official_is_video_clean" class="m-l-15"></span></p>

                                                <p class="text-muted m-b-0"><strong>Video URL :</strong> 
                                                    <span id="official_video_url" class="m-l-15"></span></p>

                                                <p class="text-muted m-b-0"><strong>Director :</strong> 
                                                    <span id="official_director" class="m-l-15"></span></p>

                                                <p class="text-muted m-b-0"><strong>Intended release date :</strong> 
                                                    <span id="official_intended_release_date" class="m-l-15"></span></p>

                                                <div id="div_official_shoot_pics" class="col-md-4 thumb-xl member-thumb m-b-10 center-block">
                                                    <span class="m-b-5"><strong>Video Shoot Pics</strong></span>
                                                    <a id="official_shoot_pics_href" target="_blank" href=""><img id="official_shoot_pics" src="assets/images/users/avatar-1.jpg" class=" img-thumbnail" style="width: 150px"></a>
                                                </div>

                                                <p id="div_lyric_video_bg" class="text-muted m-b-0 "><strong>Lyric Video Background :</strong> 
                                                    <span id="lyric_video_bg" class="m-l-15"></span></p>
                                                <hr>
                                                <div id="audio_link"></div>
                                                <p class="text-muted m-b-0"><strong>Audio Clean :</strong><span id="audio_clean" class="m-l-15"></span></p>
                                                <p class="text-muted m-b-0"><strong>Audio Dirty :</strong><span id="audio_dirty" class="m-l-15"></span></p>
                                                <p class="text-muted m-b-0"><strong>Audio Instrumental :</strong><span id="audio_instrumental" class="m-l-15"></span></p>
                                                <p class="text-muted m-b-0"><strong>Audio Acapella :</strong><span id="audio_acapella" class="m-l-15"></span></p>
                                                <hr>
                                                <p class="text-muted m-b-0"><strong>Album Name :</strong> 
                                                    <span id="album_name" class="m-l-15"></span></p>
                                                <p class="text-muted m-b-0"><strong>Album Release Date :</strong> 
                                                    <span id="album_release_date" class="m-l-15"></span></p>
                                                <p class="text-muted m-b-0"><strong>Album Quote :</strong> 
                                                    <span id="album_quote" class="m-l-15">Album Quote</span></p>
                                                <p class="text-muted m-b-0"><strong>Album Guests :</strong><br> 
                                                    <textarea id="album_guests" class="form-control" rows="5" spellcheck="false" style="line-height: 1.25;height: 100px"></textarea></p>
                                                <p class="text-muted m-b-0"><strong>Album Producers :</strong><br> 
                                                    <textarea id="album_producers" class="form-control" rows="5" spellcheck="false" style="line-height: 1.25;height: 100px"></textarea></p>
                                                <p class="text-muted m-b-0"><strong>Album Stream Link :</strong> 
                                                    <span id="album_stream_link" class="m-l-15">Album Stream Link</span></p>
                                                <p class="text-muted m-b-0"><strong>Previous Album Name :</strong> 
                                                    <span id="album_previous_name" class="m-l-15">Previous Album Name</span></p>
                                                <p class="text-muted m-b-0"><strong>Album UPC :</strong> 
                                                    <span id="album_upc" class="m-l-15">Album UPC</span></p>
                                                <div class="row">
                                                    <div class="col-md-6"><p class="text-muted m-b-0"><strong>Short-Form Video URL</strong> </p>

                                                    </div>
                                                    <div class="col-md-6 text-right"><p class="text-muted m-b-0"><strong>Suggested Text</strong> </p>

                                                    </div>
                                                </div>
                                                <div id="viral_video" class="row">
                                                    <div class="col-md-8">
                                                        <span class="m-l-10 text-ellipsis">https://drive.google.com/232323/dsadsa/dadsad/dsad/da2</span>
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                        <span class="m-l-10">Album Stream Link</span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <span class="m-l-10">https://drive.google.com/232323/dsadsa/dadsad/dsad/da2</span>
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                        <span class="m-l-10">Album Stream Link</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade tab-fulfillment" id="fulfillment-b1">
                                <div id="fulfillment-data"></div>
                                <br>
                            </div>
                        </div>
                        @if($is_admin_music)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="button" class="btn btn-outline-info btn-sm btn-add-campaign"><i class="fa fa-upload"></i> Submit</button>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>