<div class="modal fade" id="dialog_import_campaign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-80">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="ti-plus fa-fw"></i> <span
                        id="dialog-list-tile">Add Cover</span></h4>
            </div>
            <br>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i
                    class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-dialog" class="modal-body">
                <form id="formadd" method="POST">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Campaign Name</label>
                                <div class="col-12">
                                    <input type="text" name="campaign_name" 
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
                                        <option value="4">Lyric Video</option>
                                        <option value="5">Compilations</option>
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
                                    <textarea class="form-control" rows="5" name="list_video"
                                        spellcheck="false" style="line-height: 1.25;height: 300px"></textarea>
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