<div class="modal fade" id="dialog-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class=" ti-trash fa-fw"></i> <span>Delete video/playlist</span></h4>
            </div>
            <div id="dialog-delete-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-dialog-delete" class="modal-body">
                <form id="form-wakeup-add" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_channel_delete" id="id_channel_delete">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Views <=</label>
                                <div class="col-12">
                                    <input id="views_delete" type="number" name="views_delete" class="form-control" value="100">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Pages (1 page = 30 videos)</label>
                                <div class="col-12">
                                    <input id="pages_delete" type="number" name="pages_delete" class="form-control" value="1">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button data-type="1" type="button" class="btn btn-outline-danger btn-sm btn-delete-video"><i class="fa fa-trash"></i> Delete</button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="list-playlist">
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>