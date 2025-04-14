<div class="modal fade" id="dialog_brand_submit_image" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h5 class="modal-title"><span class="dialog-icon"><i class="fa fa-image fa-fw"></i></span> <span id="title-brand">Submit image</span></h5>                        
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body">
                <form id="frmSubmitBrandImage" name="frmSubmitBrandImage" method="POST" class="form-horizontal" novalidate="">
                    <input type="hidden" name ='_token' value='{{csrf_token()}}'/>
                    <input type="hidden" id="brand_id_image" name="brand_id" />
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Banner (Drive image link)</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="banner" name="banner"  value="">     
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Avatar (Drive image link)</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="avatar" name="avatar"  value="">     
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Logo (Drive image link)</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="logo" name="logo"  value="">     
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <button type="button" class="btn btn-outline-success btn-submit-brand-image"><i class="fa fa-upload"></i> Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>