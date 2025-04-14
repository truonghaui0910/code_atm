<div class="modal fade" id="dialog_wakeup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class=" ti-plus fa-fw"></i> <span
                        id="dialog-wakeup-tile">Auto Wakeup happy</span></h4>
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-dialog" class="modal-body">
                <form id="form-wakeup-add" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_channel" id="id_channel">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Playlist link</label>
                                <div class="col-12">
                                    <input id="auto_wake_playlist_id" type="text" name="playlist_id" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Title</label>
                                <div class="col-12">
                                    <input id="title" type="text" name="title" class="form-control" onfocus="checkInfoPlaylist()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Run Time</label>
                                <div class="col-12">
                                    <input id="run_time" type="text" name="run_time" class="form-control" value="{{$current}}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-outline-info btn-sm btn-add-wakeup"><i class="fa fa-upload"></i> Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>