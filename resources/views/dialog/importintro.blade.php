<div class="modal fade" id="dialog_import_intro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h5 class="modal-title"><span class="dialog-icon"><i class="fa fa-image fa-fw"></i></span> <span id="title-intro">Add New Intro</span></h5>                        
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body">
                <form id="frmAdd" name="frmAdd" method="POST" class="form-horizontal" novalidate="">
                    <input type="hidden" name ='_token' value='{{csrf_token()}}'/>
                    <input type="hidden" name="intro_id" id="intro_id" value=""/>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">App</label>
                                <div class="col-12">
                                    <select id="app" name="app" class="select2_multiple form-control">
                                        <option value="MOONAZ">MOONAZ</option>
                                    </select>  
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Channel Type</label>
                                <div class="col-12">
                                    <select id="channel_type" name="channel_type"  class="select2_multiple form-control channel_type">
                                        <option value="BOOM">BOOM</option>
                                        <option value="SINGLE">SINGLE</option>
                                    </select>  
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 div_channel_type_SINGLE disp-none">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Channel</label>
                                <div class="col-12">
                                    <select id="channel_id" name="channel_id" class="select2_multiple form-control search_select" data-show-subtext="true"
                                            data-live-search="true">
                                        {!!$channels!!}
                                    </select>  
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Video Type</label>
                                <div class="col-12">
                                    <select id="video_type" name="video_type" class="select2_multiple form-control">
                                        <option value="LONG">LONG</option>
                                        <option value="SHORT">SHORT</option>
                                    </select>  
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Intro Type</label>
                                <div class="col-12">
                                    <select id="intro_type" name="intro_type" class="select2_multiple form-control">
                                        <option value="TEXT">TEXT</option>
                                        <option value="LOGO">LOGO</option>
                                    </select>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">Intro Name</label>
                                <div class="col-12">
                                    <input id="intro_name" class="form-control" type="text" name="intro_name">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="col-4 col-form-label">Intro Thumb</label>
                            <div class="row col-md-12">
                                <div class="col-md-8">
                                    <input id="thumb_upload" type="file" name="thumb_upload" class="form-control" style="line-height: 1.25;content: Button">
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-outline-warning btn-submit-upload"><i class="fa fa-upload"></i> Upload</button>
                                </div>
                            </div>
                        </div>
                        <input id="intro_thumb" type="hidden" id='intro_thumb' name='intro_thumb' value=''/>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">Intro link</label>
                                <div class="col-12">
                                    <input id="intro_link" class="form-control" type="text" name="intro_link" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row col-md-12">
                        <button type="button" class="btn btn-outline-success btn-save-intro"><i class="fa fa-upload"></i> Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>