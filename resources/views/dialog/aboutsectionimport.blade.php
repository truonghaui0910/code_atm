<div class="modal fade" id="dialog_import_about_section" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h5 class="modal-title" ><span class="dialog-icon"><i class="fa fa-gear fa-fw"></i></span> <span>Add New About Section</span></h5>                        
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body">
                <form id="frmAddAbout" name="frmAddAbout" method="POST" class="form-horizontal" novalidate="">
                    <input type="hidden" name ='_token' value='{{csrf_token()}}'/>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Genre</label>
                                <div class="col-12">
                                    <select name="genre" class="select2_multiple form-control">
                                        {!!$genres!!}
                                    </select>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row" id="section-append">
                                <label class="col-6 col-form-label">About Section <button onclick="appendSection()" data-toggle="tooltip" data-placement="top" data-original-title="Add More Section" type="button" class="btn btn-outline-info btn-sm"><i class="fa fa-plus-circle"></i></button></label> 
                                <div class="col-12">
                                    <textarea class="form-control m-b-5" rows="5" name="about_section[]" spellcheck="false" style="line-height: 1.25;height: 65px"></textarea>  
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row col-md-12">
                        <button type="button" class="btn btn-outline-success btn-save-about-section"><i class="fa fa-upload"></i> Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>