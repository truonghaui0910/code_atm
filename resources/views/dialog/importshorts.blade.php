<div class="modal fade" id="dialog_import_shorts" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h5 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-video-camera fa-fw"></i></span> <span id="title-brand">Add Shorts</span></h5>                        
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body">
                <form id="frmShorts" name="frmBom" method="POST" class="form-horizontal" novalidate="">
                    <input type="hidden" name='_token' value='{{csrf_token()}}'/>
                    <input type="hidden" name="bom_id" id="bom_id" value=""/>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Shorts Links</label>
                                <div class="col-12">
                                    <textarea class="form-control" rows="5" name="shorts" id="shorts" spellcheck="false" style="line-height: 1.25;height: 100px"></textarea>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">&nbsp;</label>
                                <div class="col-12">
                                    <select id="topic_select" class="select2_multiple form-control search_select" data-show-subtext="true" data-live-search="true">
                                        {!!$topicSelect!!}
                                    </select>  
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Topic</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="topic" name="topic"  value="">     
                                </div>
                            </div>
                        </div>
                    </div>
<!--                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Deezer Link/Id</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="deezerId" name="deezerId"  value="">     
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Local Id</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="localId" name="localId"  value="">     
                                </div>
                            </div>
                        </div>
                    </div>-->
<!--                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Artist Name</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="artist" name="artist"  value="">     
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Deezer Artist ID</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="deezerArtistId" name="deezerArtistId"  value="">     
                                </div>
                            </div>
                        </div>
                    </div>-->
<!--                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Social</label>
                                <div class="col-12">
                                    <textarea class="form-control" rows="5" name="social" id="social" spellcheck="false" style="line-height: 1.25;height: 100px"></textarea>  
                                </div>
                            </div>
                        </div>
                    </div>-->
                    <div class="row col-md-12">
                        <button type="button" class="btn btn-outline-success btn-save-shorts"><i class="fa fa-upload"></i> Submit</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>