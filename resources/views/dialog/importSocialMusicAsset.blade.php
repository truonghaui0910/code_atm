<div class="modal fade" id="dialog_import_social_music_asset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h5 class="modal-title"><span class="dialog-icon"><i class="fa fa-database fa-fw"></i></span> <span id="title-brand">Add Social Asset</span></h5>                        
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body">
                <form id="formSocialMusicAsset" class="form-horizontal" >
                    <input type="hidden" name='_token' value='{{csrf_token()}}'/>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Social Platform</label>
                                <div class="col-12">
                                    <select id="platform" name="platform" class="select2_multiple form-control">
                                        <option value="facebook">Facebook</option>
                                        <option value="tiktok">Tiktok</option>
                                    </select>  
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Campaign Tyoe</label>
                                <div class="col-12">
                                    <select id="campaign_type" name="campaign_type" class="select2_multiple form-control">
                                        <option value="2">Claim</option>
                                        <option value="1">Promo</option>
                                    </select>  
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Song</label>
                                <div class="col-12">
                                    <select id="song" name="song" class="select2_multiple form-control search_select" 
                                            data-show-subtext="true" data-live-search="true">
              
                                    </select>  
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Song</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="full_song_name" name="full_song_name"  value="">     
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Social Asset</label>
                                <div class="col-12">
                                    <textarea class="form-control" rows="5" id="social_asset" name="social_asset"
                                              spellcheck="false" placeholder="Social asset"
                                              style="line-height: 1.25;height: 110px"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row col-md-12">
                        <button type="button" class="btn btn-outline-success btn-save-social-asset"><i class="fa fa-upload"></i> Submit</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

</script>