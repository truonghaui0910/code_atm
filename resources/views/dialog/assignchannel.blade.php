<div class="modal fade" id="dialog_assign_channel" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="ti-music-alt"></i> <span id="dialog_assign_channel_title">Assign Task</span></h4>                        
            </div>
            <br>
            <div class="disp-none dialog_assign_channel_loadding" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body dialog_assign_channel_content">
                <form id="formAssignChannel" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="channel_cam_id" id="channel_cam_id">

                    <div class="row formAssignChannel_content" style="min-height: 400px">

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button id="btn-assign-12" type="button" class="btn btn-outline-info btn-sm btn-assign-channel" onclick="assgignChannel(12)"><i
                                        class="fa fa-tasks"></i> Start Promo</button>
                                <button id="btn-assign-6" type="button" class="btn btn-outline-info btn-sm btn-assign-channel" onclick="assgignChannel(6)"><i
                                        class="fa fa-tasks"></i> Lyric Video</button>

                                <button id="btn-assign-9" type="button" class="btn btn-outline-info btn-sm btn-assign-channel" onclick="assgignChannel(9)"><i
                                        class="fa fa-tasks"></i> Promos Mix</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>