<div class="modal fade" id="dialog_import_bitly" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h5 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-external-link fa-fw"></i></span> <span id="title-brand">Add New Bitly</span></h5>                        
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body">
                <form id="frmAdd" name="frmAdd" method="POST" class="form-horizontal" novalidate="">
                    <input type="hidden" name ='_token' value='{{csrf_token()}}'/>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Destination</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="long_url" name="long_url"  value="">     
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Title (optional)</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="title" name="title"  value="">     
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">&nbsp;</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" value="bit.ly/" disabled="">     
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Custom back-half (optional)</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="custom_bitlink" name="custom_bitlink" value="" >     
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <div class="row col-md-12">
                        <button type="button" class="btn btn-outline-success btn-save-bitly"><i class="fa fa-upload"></i> Submit</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>