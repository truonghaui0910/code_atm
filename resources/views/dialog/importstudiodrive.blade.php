<div class="modal fade" id="dialog_import_studio_drive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h5 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-image fa-fw"></i></span> <span id="title-brand">Add New Studio Drive</span></h5>                        
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body">
                <form id="frmAdd" name="frmAdd" method="POST" class="form-horizontal" novalidate="">
                    <input type="hidden" name ='_token' value='{{csrf_token()}}'/>
                    <input type="hidden" name="import_type" value="1"/>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Channel Name</label>
                                <div class="col-12 position-relative">
                                    <input type="text" class="form-control" name="channel_name" id="channel_name"  placeholder="Enter channel name..."/>
                                    <ul id="channel_list" class="channel-list"></ul>
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Drive Link</label>
                                <div class="col-12">
                                    <textarea class="form-control" rows="5" id="drive_links" name="drive_links"
                                              spellcheck="false" style="line-height: 1.25;height: 100px"></textarea>
                                              <span class="text-muted font-12 font-italic">Link to videos or photos on the drive that you want to save permanently.</span><br>
                                              <span class="text-muted font-12 font-italic">You can enter multiple links, separated by newlines</span><br>
                                              <span class="text-muted font-12 font-italic">Link Format: https://drive.google.com/file/d/<b>drive_id</b>/view or only <b>drive_id</b></span>
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <div class="row col-md-12">
                        <button type="button" class="btn btn-outline-success btn-save-studio-drive"><i class="fa fa-upload"></i> Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>