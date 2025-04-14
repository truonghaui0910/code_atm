<div class="modal fade" id="dialog_brand_channel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h5 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-image fa-fw"></i></span> <span id="title-brand">Config Channel</span></h5>                        
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body">
                <form id="frmBrand" name="frmBrand" method="POST" class="form-horizontal" novalidate="">
                    <input type="hidden" name ='_token' value='{{csrf_token()}}'/>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Channel Type</label>
                                <div class="col-12">
                                    <select id="channel_type" name="channel_type" class="select2_multiple form-control">
                                        {!!$channelType!!}
                                    </select>  
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Channel Genre</label>
                                <div class="col-12">
                                    <select id="channel_genre" name="channel_genre" class="select2_multiple form-control">
                                        {!!$channelGenre!!}
                                    </select>    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Channel SubGenre</label>
                                <div class="col-12">
                                    <select id="channel_subgenre" name="channel_subgenre[]" class="select2_multiple form-control" multiple style="height: 110px">
                                        {!!$channelSubGenre!!}
                                    </select>    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Channel Tags</label>
                                <div class="col-12">
                                    <select id="tags" name="tags[]" class="select2_multiple form-control" multiple style="height: 110px">
                                        {!!$channelTags!!}
                                    </select>    
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="col-12 col-form-label">&nbsp;</label>
                            <div class="row col-md-12">
                                <div class="col-md-8">
                                    <button type="button" class="btn btn-outline-success btn-set-info"><i class="fa fa-upload"></i> Set</button>
                                </div>

                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>