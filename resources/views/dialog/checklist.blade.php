<div class="modal fade" id="dialog_check_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class="ti-check fa-fw"></i> <span id="dialog_check_list_title">TIER CHECKLIST</span></h4>                        
            </div>
            <br>
            <div class="disp-none dialog_check_list_loadding" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body dialog_check_list_content">
                <form id="formchecklist" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="campaign_id" id="campaign_id">
                    <div class="row formchecklist_content">

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-outline-info btn-sm btn-import-checklist" onclick="importChecklist()"><i
                                        class="fa fa-upload"></i> Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>