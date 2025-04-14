<div class="modal fade" id="dialog_import_brand" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h5 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-image fa-fw"></i></span> <span id="title-brand">Add New Brand</span></h5>                        
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body">
                <form id="frmAdd" name="frmAdd" method="POST" class="form-horizontal" novalidate="">
                    <input type="hidden" name ='_token' value='{{csrf_token()}}'/>
                    <input type="hidden" id="brand_id" name="brand_id"/>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Genre</label>
                                <div class="col-12">
                                    <select id="genre" name="genre" class="select2_multiple form-control">
                                        {!!$genres!!}
                                    </select>  
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">SubGenre</label>
                                <div class="col-12">
                                    <select id="channel_subgenre" name="channel_subgenre[]" class="select2_multiple form-control" multiple style="height: 110px">
                                        {!!$channelSubGenre!!}
                                    </select>    
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Channel Name</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="channel_name" name="channel_name"  value="">     
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Designer</label>
                                <div class="col-12">
                                    <select id="designer" name="designer" class="select2_multiple form-control">
                                        {!!$designer!!}
                                    </select>  
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Style</label>
                                <div class="col-12">
                                    <textarea class="form-control" rows="5" id="style" name="style"
                                              spellcheck="false" style="line-height: 1.25;height: 100px"></textarea>  
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row col-md-12">
                        <button type="button" class="btn btn-outline-success btn-save-brand"><i class="fa fa-upload"></i> Submit</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>