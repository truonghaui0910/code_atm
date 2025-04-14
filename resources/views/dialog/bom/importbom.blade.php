<div class="modal fade" id="dialog_import_bom" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
<!--            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>×</span></button>
                <h5 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-bomb fa-fw"></i></span> <span id="title-brand">Add Boom</span></h5>                        
            </div>-->
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <strong id="dialog_add_invoice_title">Add Boom</strong>
                        </h5>
                    </div>
                    <div >
                        <button
                            class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light"
                            data-dismiss="modal"
                            data-id="modal-add-task" data-toggle="tooltip" data-placement="top" title="Close"
                            style="padding-bottom: 0.6rem;padding-top: 0.6rem;z-index: 1001">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                <path
                                    d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body div_scroll_50">
                <form id="frmBom" name="frmBom" method="POST" class="form-horizontal" novalidate="">
                    <input type="hidden" name='_token' value='{{csrf_token()}}'/>
                    <input type="hidden" name="bom_id" id="bom_id" value=""/>
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
                        <div class="col-md-6 div_group_show">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Group <span class="color-red">*</span> <span onclick="showAddBomGroup()"><i class="fa fa-plus-circle color-red"
                                            style="font-size: 20px;"></i></span></label>
                                <div class="col-12">
                                    <select id="local_group" name="local_group[]" class="local_group select2_multiple form-control" 
                                            data-show-subtext="true" 
                                            data-live-search="true"
                                            data-size="5" data-container="body"
                                            multiple="">
                                    </select>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row div_add_deezer">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Spotify Playlist/Id</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="spotify_playlist_id" name="spotify_playlist_id"  value="">     
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">&nbsp;</label>
                                 <button type="button" class="btn btn-outline-success btn-check-spotify"><i class="fa fa-check"></i> Check</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="error_song">
                                
                            </div>
                        </div>
                    </div>
                    <div class="row div_add_deezer">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Deezer Link/Id <span id="dz_count" class="color-green"></span></label>
                                <div class="col-12">
                                    <!--<input type="text" class="form-control" id="deezerId" name="deezerId"  value="">--> 
                                    <textarea class="form-control" rows="5" name="deezerId" id="deezerId" spellcheck="false" style="line-height: 1.25;height: 100px"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
<!--                    <div class="row">
                        <div class="col-md-8">
                            <div class="checkbox-radius checkbox-circle d-flex">
                                <input id="chk_add_local" name="chk_add_local" class="" type="checkbox" onchange="showAddLocal(this)" value="1">
                                <label for="chk_add_local" class="m-b-18 m-l-15"></label>
                                <span>Add song by Local Id</span>
                            </div>
                        </div>
                    </div>-->
                    <div class="row ">
                        <div class="col-md-4 div_add_local ">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Local Id</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="localId" name="localId"  value="">     
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 div_add_local ">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Artist Name</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="artist" name="artist"  value="">     
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 div_add_local ">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Song Name</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="songName" name="songName"  value="">     
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row div_add_local">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Social</label>
                                <div class="col-12">
                                    <textarea class="form-control" rows="5" name="social" id="social" spellcheck="false" style="line-height: 1.25;height: 100px"></textarea>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <button type="button" class="btn btn-outline-success btn-save-bom"><i class="fa fa-upload"></i> Submit</button>

                    </div>
                </form>
                <div class="row mt-3">
                    <div class="col-md-12">
                        
                        <div id="import_result_deezer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>