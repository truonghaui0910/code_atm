<div class="modal fade" id="addAlbumModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <span id="created_by_avatar"></span>
                            <strong id="dialog-task-title">Add Album</strong>
                        </h5>
                    </div>
                    <div class="">

                        <button
                            class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light btn-close-modal"
                            data-id="modal-add-task" data-toggle="tooltip" data-placement="top" title="Close" data-dismiss="modal"
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
            <div id="content-dialog" class="modal-body">
                <form id="form-album" method="POST">
                    {{ csrf_field() }}
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div class="album-form-container">                    
                                <form id="createAlbumForm">
                                    <div class="album-info-section">

                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="albumTitle">Album Title <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="albumTitle" name='albumTitle' placeholder="Enter album title" required>
                                                </div>

<!--                                                <div class="form-group">
                                                    <label for="albumArtist">Artist/Band <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="albumArtist" name='albumArtist' placeholder="Artist or band name" required>
                                                </div>-->


                                                <div class="form-group">
                                                    <label >Artist <span class="color-red">*</span> <span onclick="modalAddArtist()"><i class="fa fa-plus-circle color-red"
                                                                                                                                       style="font-size: 20px;"></i></span></label>
                                                    <div>
                                                        <select id="albumArtist" class="albumArtist form-control" name="albumArtist" class="select2_multiple form-control search_select" 
                                                                data-show-subtext="true" 
                                                                data-live-search="true"
                                                                data-size="5" data-container="body">
                                                        </select>  
                                                    </div>
                                                </div>


<!--                                                <div class="form-group">
                                                    <label for="albumDescription">Description</label>
                                                    <textarea class="form-control" id="albumDescription" name='albumDescription' rows="3" placeholder="Enter album description"></textarea>
                                                </div>-->

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="albumGenre">Genre <span class="text-danger">*</span></label>
                                                            <select class="form-control" id="albumGenre" name='albumGenre' required>
                                                                <option value="" selected disabled>Select genre</option>

                                                                <option value="1">Electro House</option>
                                                                <option value="2">Hip-Hop/Rap</option>
                                                                <option value="3">Rock</option>
                                                                <option value="4">Techno</option>
                                                                <option value="5">Soundtrack</option>
                                                                <option value="6">Experimental</option>
                                                                <option value="7">Electronic</option>
                                                                <option value="8">K-Pop</option>
                                                                <option value="9">Alternative Rap</option>
                                                                <option value="10">Pop/Rock</option>
                                                                <option value="11">Reggaeton</option>
                                                                <option value="12">Electronica</option>
                                                                <option value="13">Indie Rock</option>
                                                                <option value="14">Salsa</option>
                                                                <option value="15">Christian & Gospel</option>
                                                                <option value="16">Children's Music</option>
                                                                <option value="17">Spoken Word</option>
                                                                <option value="18">Soft Rock</option>
                                                                <option value="19">Regional Mexicano</option>
                                                                <option value="20">World</option>
                                                                <option value="21">Folk</option>
                                                                <option value="22">House</option>
                                                                <option value="23">Reggae</option>
                                                                <option value="24">Funk</option>
                                                                <option value="25">Breakbeat</option>
                                                                <option value="26">Country</option>
                                                                <option value="27">Dancehall</option>
                                                                <option value="28">Pop</option>
                                                                <option value="29">Jazz</option>
                                                                <option value="30">Holiday</option>
                                                                <option value="31">Metal</option>
                                                                <option value="32">Christmas</option>
                                                                <option value="33">Blues</option>
                                                                <option value="34">Ambient</option>
                                                                <option value="35">Latin</option>
                                                                <option value="36">Alternative</option>
                                                                <option value="37">R&B/Soul</option>
                                                                <option value="38">Afro-Beat</option>
                                                                <option value="39">Dance</option>
                                                                <option value="40">Adult Contemporary</option>
                                                                <option value="41">New Age</option>
                                                                <option value="42">Classical</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="releaseDate">Release Date</label>
                                                            <input type="date" class="form-control" name="releaseDate" id="releaseDate">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Album Cover <span class="text-danger">*</span></label>
                                                    <div class="preview-image" id="imagePreview">
                                                        <i class="fas fa-image fa-3x music-icon"></i>
                                                    </div>
                                                    <div class="">
                                                        <input type="file" class="custom-file-input disp-none" id="albumCover" name='albumCover' accept="image/*" required>
                                                    </div>
                                                    <small class="form-text text-muted">Minimum size: 1400x1400px</small>
                                                </div>

                                                <!--                                    <div class="form-group mt-3">
                                                                                        <label for="recordLabel">Record Label/Producer</label>
                                                                                        <input type="text" class="form-control" id="recordLabel" placeholder="Record label name">
                                                                                    </div>-->
                                            </div>
                                        </div>


                                    </div>



                                    <div class="album-actions">
                                        <!--                            <button type="button" class="btn btn-light" id="resetBtn">
                                                                        <i class="fas fa-undo mr-1"></i>Reset
                                                                    </button>-->
                                        <button type="button" class="btn btn-primary btn-create" id="submitAlbum">
                                            <span class="spinner-border spinner-border-sm loading-spinner" id="loadingSpinner" role="status" aria-hidden="true"></span>
                                            <i class="fas fa-plus-circle mr-1" id="submitIcon"></i>Create Album
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </form>                

            </div>
        </div>
    </div>
</div>