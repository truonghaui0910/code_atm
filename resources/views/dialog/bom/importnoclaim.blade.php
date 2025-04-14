<div class="modal fade" id="dialog_import_noclaim" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
<!--            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>×</span></button>
                <h5 class="modal-title"><span class="dialog-icon"><i class="fa fa-database fa-fw"></i></span> <span id="title-brand">Add Local Song</span></h5>                        
            </div>-->
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <strong id="dialog_add_invoice_title">Add Local Song</strong>
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
                <form id="frmLocalSong" name="frmLocalSong" method="POST" class="form-horizontal" novalidate="">
                    <input type="hidden" name='_token' value='{{csrf_token()}}'/>
                    <input type="hidden" name="noclaim_id" id="noclaim_id" value=""/>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Genre <span class="color-red">*</span></label>
                                <div class="col-12">
                                    <select id="local_genre" name="genre" class="select2_multiple form-control">
                                        {!!$genres!!}
                                    </select>  
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Group <span class="color-red">*</span> <span onclick="showAddBomGroup()"><i class="fa fa-plus-circle color-red"
                                            style="font-size: 20px;"></i></span></label>
                                <div class="col-12">
                                    <select id="local_group" class="local_group" name="local_group[]" class="select2_multiple form-control" 
                                            data-show-subtext="true" 
                                            data-live-search="true"
                                            data-size="5" data-container="body"
                                            multiple="">
                                    </select>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="checkbox-radius checkbox-circle d-flex">
                                   <input id="chk_overtone" name="chk_overtone" class="" type="checkbox" onchange="showOverTone(this)" value="1">
                                   <label for="chk_overtone" class="m-b-18 m-l-15"></label>
                                   <span>Over Tone</span>
                            </div>
                        </div>
                    </div>
                    <div class="row div_overtone disp-none">
                        <div class="col-md-8">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Overtone Playlist</label>
                                <div class="col-12">
                                    <select id="overtone_playlist_id" class="form-control search_select" name="overtone_playlist_id" data-show-subtext="true" data-live-search="true">

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row div_other">
                        <div class="col-md-8">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Youtube/Drive Link/Epidemic sound</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="local_source_link" name="source_link"  value="" onchange="sourceChange(this)">     
                                </div>
                                <span class="col-12 font-13 text-muted">https://www.udio.com/playlists/<b>playlist_id</b>/ for udio AI</span>
                                <span class="col-12 font-13 text-muted">https://www.universalmusicforcreators.com/library/playlists/<b>playlist_id</b>/ for universal</span>
                                <span class="col-12 font-13 text-muted">https://www.epidemicsound.com/saved/<b>playlist_id</b>/ for epidemicsound</span>
                                <span class="col-12 font-13 text-muted">https://www.youtube.com/watch?v=<b>video_id</b>/ for youtube</span>
                            </div>
                        </div>
                    </div>
                    <div class="row div_other ">
                        <div class="col-md-6 div_1_song disp-none">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Song Name</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="local_songName" name="songName"  value="">     
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 div_1_song disp-none">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Artist Name</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="local_artist" name="artist"  value="">     
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row div_other ">
                        <div class="div_check_udio disp-none">
                            <div class="col-md-12">
                                <div class="checkbox-radius checkbox-circle d-flex">
                                       <input id="chk_keep_name" name="chk_keep_name" class="" type="checkbox" onchange="showCustomName(this)" value="1">
                                       <label for="chk_keep_name" class="m-b-18 m-l-15"></label>
                                       <span>Custom Artist Name and Song Name</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row div_other ">
                        <div class="col-md-12 div_2_song disp-none">
                            <div class="col-12 m-b-10">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">List Artist</label>
                                    <div class="col-12">
                                        <textarea class="form-control" rows="5" id="list_artist" name="list_artist" spellcheck="false" placeholder="List of artist, separated by line breaks" style="line-height: 1.25;height: 210px"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 m-b-10">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">List Song Name</label>
                                    <div class="col-12">
                                        <textarea class="form-control" rows="5" id="list_song_name" name="list_song_name" spellcheck="false" placeholder="List of song name, separated by line breaks" style="line-height: 1.25;height: 210px"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row col-md-12 m-t-20">
                        <button type="button" class="btn btn-outline-success btn-save-local-song"><i class="fa fa-upload"></i> Submit</button>

                    </div>
                </form>
                <div class="row mt-3">
                    <div class="col-md-12">
                        
                        <div id="import_result"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>