<div class="modal fade" id="dialog_add_artist" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <span id="created_by_avatar"></span>
                            <strong id="dialog-task-title">Add Artist</strong>
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
                <form id="form-add-artist" method="POST">
                    {{ csrf_field() }}
                    <div class="add_new_group_list  fadeInDown">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">Artist</label>
                                <div class="col-12">
                                    <input id="artist_name" class="form-control" type="text" name="artist_name" value="">
                                </div>
                            </div>
<!--                            <div class="form-group row">
                                <label class="col-8 col-form-label">Description</label>
                                <div class="col-12">
                                    <textarea id="group_des" class="form-control" rows="5" name="group_des" spellcheck="false" placeholder=""
                                        style="line-height: 1.25;height: 100px"></textarea>
                                </div>
                            </div>-->

                            <button data-toggle="tooltip" data-placement="top" data-original-title="" type="button"
                                class="btn float-right" onclick="addArtist()"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </div>
                   
                </form>                

            </div>
        </div>
    </div>
</div>