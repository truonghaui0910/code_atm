<div class="modal fade" id="dialog_description_edit" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>

                <h5 class="modal-title"><span class="dialog-icon"><i class="fa fa-id-card"></i></span> <span>DESCRIPTION EDIT</span></h5>                        

            </div>
            <div id="dialog_description_edit_loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="dialog_description_edit_content" class="modal-body">
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group row">
                            <!--<label class="col-6 col-form-label">Brand Type</label>-->
                            <div class="col-12">
                                <select id="" name="type" class="select2_multiple form-control">
                                    <option value="insert">Insert</option>
                                    <option value="replace">Replace</option>
                                </select>    
                            </div>
                        </div>
                    </div>  
                    <div class="col-md-3">
                        <div class="form-group row">
                            <!--<label class="col-6 col-form-label">Brand Type</label>-->
                            <div class="col-12">
                                <input type="number" class="form-control" id="index" name="index"  value="1">     
                            </div>
                        </div>
                    </div>  
                    <div class="col-md-4">
                        <div class="form-group row">
                            <!--<label class="col-6 col-form-label">Brand Type</label>-->
                            <div class="col-12">
                                <textarea name="content" id="content" class="form-control" placeholder="Description"></textarea>
                            </div>
                        </div>
                    </div>  

                </div>
            </div>
        </div>
    </div>
</div>