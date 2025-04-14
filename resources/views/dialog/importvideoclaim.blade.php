<div class="modal fade" id="dialog_import_campaign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-80">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class=" ti-plus fa-fw"></i> <span
                        id="dialog-list-tile">Add Campaign / Add videos</span></h4>
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i
                    class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-dialog" class="modal-body">
                <form id="formadd" method="POST">
                    <input id="form-csrf-token" type="hidden" name ='_token' value='{{csrf_token()}}'/>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Campaign Name</label>
                                <div class="col-12">
                                    <input id="campaign_name_add" type="text" name="campaign_name" class="form-control">
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
                                        <option value="{{$campaign->campaign_name}}">{{$campaign->campaign_name}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Campaign Type</label>
                                <div class="col-12">
                                    <select class="form-control input-sm " name="campaign_type">
                                        <option value="premium">Premium</option>
                                        <option value="medium">Medium</option>
                                        <option value="regular">Regular</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Campaign Start Date</label>
                            <div class="col-12">
                                <input type="text" name="campaign_start_date"  placeholder="MM/DD/YYYY" value="<?php echo gmdate("m/d/Y", time() + 7 * 3600);?>"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Link to artists channel</label>
                                <div class="col-12">
                                    <input type="text" name="artists_channel" class="form-control">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Genre</label>
                                <div class="col-12">
                                    <input type="text" name="genre" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Label</label>
                                <div class="col-12">
                                    <input type="text" name="label" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Artist</label>
                                <div class="col-12">
                                    <input type="text" name="artist" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Song Name</label>
                                <div class="col-12">
                                    <input type="text" name="songname" class="form-control">
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
                                        <option value="6">Short</option>
                                        <option value="5">Mix</option>
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
                                        spellcheck="false" style="line-height: 1.25;height: 200px"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-outline-info btn-sm btn-import-video"><i
                                        class="fa fa-upload"></i> Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>