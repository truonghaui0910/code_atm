<div class="modal fade" id="dialog_import_campaign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!--            <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalLabel"><i class=" ti-money fa-fw"></i> <span
                                    id="dialog_import_campaign_title">Add Claim</span></h4>
                        </div>-->
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <strong id="dialog_import_campaign_title"></strong>
                        </h5>
                    </div>
                    <div >
                        <button id="btn-reload-claim"
                                class="btn btn-no-bg btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light m-r-10 "
                                onclick=""
                                data-toggle="tooltip" data-placement="top" title="Reload">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                 class="bi bi-arrow-clockwise m-t-1" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                      d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z" />
                                <path
                                    d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466" />
                            </svg>
                        </button>

                        <button
                            class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light"
                            data-dismiss="modal"
                            data-id="modal-add-task" data-toggle="tooltip" data-placement="top" title="Close"
                            style="padding-bottom: 0.6rem;padding-top: 0.6rem;z-index: 1001">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                 class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                <path
                                    d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div id="dialog-loading-claim" style="text-align: center;display: none"><i
                    class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-dialog" class="modal-body">
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
                                        Claim Info
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="home-b1">
                                    <div class="row">

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-12 col-form-label">Official Video URL <span class="official-sync disp-none"><i class="fa fa-circle-o-notch fa-spin"></i></span></label>
                                                <div class="col-12">
                                                    <input id="official_video" type="text" name="official_video" class="form-control">
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
                                                                                                                                    <select id="crypto_cmt_run" name="crypto_cmt_run" class="select2_multiple form-control">
                                                                                                                                        <option value="0">Prepare</option>
                                                                                                                                        <option value="1">Running</option>
                                                                                                                                        <option value="2">Finished</option>
                                                                                                                                    </select>
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
                                                                                                                                            <div class="col-md-6">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Claim Campaign Name</label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="campaign_name" type="text" name="campaign_name" class="form-control">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-6">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-6 col-form-label">User</label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <select class="form-control search_select" id="username" name="username" data-show-subtext="true" data-live-search="true">
                                                                                                                                                            {!!$listUser!!}
                                                                                                                                                        </select>
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>

                                                                                                                                        </div>

                                                                                                                                        <div class="row">
                                                                                                                                            <div class="col-md-3">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Status</label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <!--<input type="text" name="genre" class="form-control">-->
                                                                                                                                                        <select id="status" name="status" class="select2_multiple form-control">
                                                                                                                                                            {!!$status!!}
                                                                                                                                                        </select> 
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-3">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Genre</label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <!--<input type="text" name="genre" class="form-control">-->
                                                                                                                                                        <select id="genre" name="genre" class="select2_multiple form-control">
                                                                                                                                                            {!!$channel_genre!!}
                                                                                                                                                        </select> 
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-3">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Custom Genre</label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <select id="custom_genre" name="custom_genre" class="select2_multiple form-control">
                                                                                                                                                            <option value="WHITE_CLAIM">WHITE_CLAIM</option>
                                                                                                                                                            <!--                                                    <option value="CLAIM_WMG">CLAIM_WMG</option>
                                                                                                                                                                                                        <option value="CLAIM_SME">CLAIM_SME</option>-->
                                                                                                                                                            <option value="BLACK_AUDIO">BLACK_AUDIO</option>
                                                                                                                                                            <option value="BLACK_GOOD">BLACK_GOOD</option>
                                                                                                                                                        </select> 
                                                                                                                                                        <!--<input id="label" type="text" name="label" class="form-control">-->
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-3">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Start Date</label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="campaign_start_date" type="date" name="campaign_start_date" class="form-control">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                        <div class="row">
                                                                                                                                            <div class="col-md-4">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Artist <i class="fa fa-copy" onclick="copyText('artist')"></i></label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="artist" type="text" name="artist" class="form-control">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-4">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Song Name <i class="fa fa-copy" onclick="copyText('songname')"></i></label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="songname" type="text" name="songname" class="form-control">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            @if($is_admin_music)
                                                                                                                                            <div class="col-md-4">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Distributor <i class="fa fa-copy" onclick="copyText('distributor')"></i></label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="distributor" type="text" name="distributor" class="form-control">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            @endif
                                                                                                                                        </div>
                                                                                                                                        @if($is_admin_music)
                                                                                                                                        <div class="row">
                                                                                                                                            <div class="col-md-4">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">YT Artist <i class="fa fa-copy" onclick="copyText('yt_artist')"></i></label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="yt_artist" type="text" class="form-control" readonly="true">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-4">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <div class="col-12 w-100p d-flex justify-content-between align-items-center">
                                                                                                                                                        <label class="col-6 col-form-label w-80p">YT Song Name <i class="fa fa-copy" onclick="copyText('yt_songname')"></i></label>
                                                                                                                                                        <div class="d-flex justify-content-end w-20p">
                                                                                                                                                            <label class="col-6 col-form-label btn-video-remove text-right"></label>
                                                                                                                                                            <label class="col-6 col-form-label btn-video-checked text-right"></label>
                                                                                                                                                            <label class="col-6 col-form-label btn-login-channel text-right"></label>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="yt_songname" type="text" class="form-control" readonly="true">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>

                                                                                                                                            <div class="col-md-4">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <div style="width: 100%;display: flex;justify-content: space-between;align-items: center;">
                                                                                                                                                        <label class="col-6 col-form-label">YT Distributor <i class="fa fa-copy" onclick="copyText('yt_distributor')"></i></label>
                                                                                                                                                        @if($is_admin_music)
                                                                                                                                                        <label class="col-6 col-form-label btn-check-claim text-right" ><i class="fa fa-refresh"></i></label>
                                                                                                                                                        @endif
                                                                                                                                                    </div>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="yt_distributor" type="text" class="form-control" readonly="true">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                        @endif


                                                                                                                                        <div class="row">

                                                                                                                                            <!--                                    <div class="col-md-6">
                                                                                                                                                                                    <div class="form-group row">
                                                                                                                                                                                        <label class="col-12 col-form-label">Official Video URL <i class="fa fa-copy" onclick="copyText('official_video')"></i></label>
                                                                                                                                                                                        <div class="col-12">
                                                                                                                                                                                            <input id="official_video" type="text" name="official_video" class="form-control">
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>-->
                                                                                                                                            <div class="col-md-4">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Bitly URL <i class="fa fa-copy" onclick="copyText('bitly_url')"></i></label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="bitly_url" type="text" name="bitly_url" class="form-control">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-4">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">AssetId <i class="fa fa-copy" onclick="copyText('asset_id')"></i></label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="asset_id" type="text" name="asset_id" class="form-control">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>

                                                                                                                                        </div>
                                                                                                                                        <div class="row">
                                                                                                                                            <div class="col-md-6">
                                                                                                                                                <label class="col-12 col-form-label">Audio File <span id="link_audio"></span></label>
                                                                                                                                                <div class="row col-md-12">
                                                                                                                                                    <div class="col-md-8">
                                                                                                                                                        <input type="hidden" id='audio_url' name ='audio_url' value=''/>
                                                                                                                                                        <input id="audio_upload" type="file" name="audio_upload" class="form-control" accept=".mp3,.wav" style="line-height: 1.25;content: Button"> 
                                                                                                                                                    </div>
                                                                                                                                                    <div class="col-md-4">
                                                                                                                                                        <button type="button" class="btn btn-outline-warning btn-submit-upload" value='audio'><i class="fa fa-upload"></i> Upload</button>
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                        <div class="row">
                                                                                                                                            <div class="col-md-4">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Deezer Artists ID <i class="fa fa-copy" onclick="copyText('deezer_artist_id')"></i></label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="deezer_artist_id" type="number" name="deezer_artist_id" class="form-control">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-4">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Deezer Song ID <i class="fa fa-copy" onclick="copyText('deezer_id')"></i></label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="deezer_id" type="number" name="deezer_id" class="form-control">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-4">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Spotify Song ID <i class="fa fa-copy" onclick="copyText('spotify_id')"></i></label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="spotify_id" type="text" name="spotify_id" class="form-control">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-4">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">ISRC <i class="fa fa-copy" onclick="copyText('isrc')"></i></label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="isrc" type="text" name="isrc" class="form-control">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-4">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">UPC <i class="fa fa-copy" onclick="copyText('upc')"></i></label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="upc" type="text" name="upc" class="form-control">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-4">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Promos keywords</label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="promo_keywords" type="text" name="promo_keywords"
                                                                                                                                                               class="form-control">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>

                                                                                                                                        </div>
                                                                                                                                        <div class="row">
                                                                                                                                            <div class="col-md-12">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Artist Social Links <i class="fa fa-copy" onclick="copyText('artists_social')"></i></label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <!--<input type="text" name="artists_social" class="form-control">-->
                                                                                                                                                        <textarea id="artists_social" class="form-control" rows="5" name="artists_social"
                                                                                                                                                                  spellcheck="false" style="line-height: 1.25;height: 150px"></textarea>
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                        <div class="row">
                                                                                                                                            <div class="col-md-3">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Total Target <i
                                                                                                                                                            class="fa fa-question-circle-o" data-html="true"
                                                                                                                                                            data-toggle="tooltip" data-placement="top"
                                                                                                                                                            data-template="<div class='tooltip' role='tooltip'><div class='arrow'></div><div class='tooltip-inner' style='max-width: 400px;'></div></div>"
                                                                                                                                                            title="<p style='text-align:left'>K = 1000 M =1000,000<br><br>Target total: The number of views the campaign wants to achieve.<br><br>Week1: The number of views that the campaign wants to achieve after 1 week.<br><br>Month1: The number of views that the campaign wants to achieve after 1 month (include Weeek1).</p>"></i>
                                                                                                                                                    </label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <input id="target_total" type="text" name="target_total"
                                                                                                                                                               class="form-control" onkeypress="return validateInputTarget(event)" value="10M">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <input id="target" type="hidden" name="target" class="form-control">
                                                                                                                                                <div class="col-md-2">
                                                                                                                                                    <div class="form-group row">
                                                                                                                                                        <label class="col-12 col-form-label">Tax <i
                                                                                                                                                                class="fa fa-question-circle-o" data-html="true"
                                                                                                                                                                data-toggle="tooltip" data-placement="top"
                                                                                                                                                                title="Percentage of tax"></i>
                                                                                                                                                        </label>
                                                                                                                                                        <div class="col-12">
                                                                                                                                                            <input id="tax_percent" type="text" name="tax_percent" class="form-control">
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                                <div class="col-md-2">
                                                                                                                                                    <div class="form-group row">
                                                                                                                                                        <label class="col-12 col-form-label">Artist Percent <i
                                                                                                                                                                class="fa fa-question-circle-o" data-html="true"
                                                                                                                                                                data-toggle="tooltip" data-placement="top"
                                                                                                                                                                data-template="<div class='tooltip' role='tooltip'><div class='arrow'></div><div class='tooltip-inner' style='max-width: 400px;'></div></div>"
                                                                                                                                                                title="<p style='text-align:left'>Percentage is agreed with the artist</p>"></i>
                                                                                                                                                        </label>
                                                                                                                                                        <div class="col-12">
                                                                                                                                                            <input id="artist_percent" type="text" name="artist_percent"
                                                                                                                                                                   class="form-control">
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                                <div class="col-md-2">
                                                                                                                                                    <div class="form-group row">
                                                                                                                                                        <label class="col-12 col-form-label">Bassteam Percent <i
                                                                                                                                                                class="fa fa-question-circle-o" data-html="true"
                                                                                                                                                                data-toggle="tooltip" data-placement="top"
                                                                                                                                                                data-template="<div class='tooltip' role='tooltip'><div class='arrow'></div><div class='tooltip-inner' style='max-width: 400px;'></div></div>"
                                                                                                                                                                title="<p style='text-align:left'>Percent for bassteam</p>"></i>
                                                                                                                                                        </label>
                                                                                                                                                        <div class="col-12">
                                                                                                                                                            <input id="bass_percent" type="text" name="bass_percent"
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
                                                                                                                                        </div>
                                                                                                                                        <div class="row">
                                                                                                                                            <div class="col-md-12">
                                                                                                                                                <div class="form-group row">
                                                                                                                                                    <label class="col-12 col-form-label">Lyrics <i class="fa fa-copy" onclick="copyText('lyrics')"></i></label>
                                                                                                                                                    <div class="col-12">
                                                                                                                                                        <textarea class="form-control" rows="5" id="lyrics" name="lyrics"
                                                                                                                                                                  spellcheck="false" style="line-height: 1.25;height: 200px"></textarea>
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                    </div>

                                                                                                                                    @if(in_array('20',explode(",", $user_login->role)) || in_array('1',explode(",",$user_login->role)))
                                                                                                                                    <div class="row">
                                                                                                                                        <div class="col-md-12">
                                                                                                                                            <div class="form-group">
                                                                                                                                                <button type="button" class="btn btn-outline-info btn-sm btn-add-claim"><i
                                                                                                                                                        class="fa fa-upload"></i> Submit</button>
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