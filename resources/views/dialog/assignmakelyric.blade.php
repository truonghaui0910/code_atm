<div class="modal fade" id="dialog_assign_lyric" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="ti-music-alt"></i> <span id="dialog-list-tile">Assign make lyric</span></h4>                        
            </div>
            <br>
            <div class="disp-none dialog_assign_lyric_loadding" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body dialog_assign_lyric_content">
                <form id="formAssignLyric" method="POST">
                    {{ csrf_field() }}
                    <!--<input type="hidden" name="lyric_cam_id" id="lyric_cam_id">-->
                    <div class="row formAssignLyric_content">

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-outline-info btn-sm btn-assign-lyric" onclick="assgignMakeLyric()"><i
                                        class="fa fa-upload"></i> Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>