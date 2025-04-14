<div class="modal fade" id="dialog_multi_upload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>×</span></button>
                <h5 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-line-chart fa-fw"></i></span> <span id="dialog-multi-upload-title"></span></h5>                        
            </div>
            <br>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-dialog" class="modal-body">
                <form id="filter_channel">
                    <input type="hidden" id="track-id" name="trackId"></input>
                    <input type="hidden" id="track-type" name="trackType"></input>
                    <input type="hidden" id="cross-type" name="crossType"></input>

                    <div id="filter" class="row">


                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Channel Name</label>
                                <div class="col-12">
                                     <input id="channel_name" class="form-control" type="text" name="channel_name">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">Views</label>
                                <div class="col-12">
                                    <input id="views" class="form-control" type="number" name="views" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">Subs</label>
                                <div class="col-12">
                                    <input id="subs" class="form-control" type="number" name="subs" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">Limit</label>
                                <div class="col-12">
                                    <input id="limitChannel" class="form-control" type="number" name="limitChannel" value="10">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div id="jstree-channel"></div>
                <br>
                <div>
                    <button class="color-green btn btn-submit-multi-music"><i class="ion-videocamera"></i> Make Videos</button>
                </div>

            </div>
        </div>
    </div>
</div>


 
