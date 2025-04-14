<div class="modal fade" id="dialog_channel_add" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <span id="created_by_avatar"></span>
                            <strong id="dialog-task-title">Add Channel</strong>
                        </h5>
                    </div>
                    <div class="">

                        <button
                            class="btn-circle-cus2 btn-circle-hover waves-effect waves-light btn-close-modal"
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
                <form id="formAddChannel" method="POST">
                    {{ csrf_field() }}
                    <div class="  fadeInDown">
                        <div class="row ">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Channel</label>
                                    <div class="col-12">
                                        <input id="add_channel_id" class="form-control" type="text" name="add_channel_id" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Choose Email</label>
                                    <div class="col-12">
                                        <select id="select_rmail" class="form-control search_select" name="select_mail" data-container="body" data-show-subtext="true" data-live-search="true">
                                            {!!$email!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Group</label>
                                    <div class="col-12">
                                        <select class="form-control search_select" name="group_channel"
                                                data-show-subtext="true" data-live-search="true" data-size="5">
                                            {!!$group_channel_search!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                            <button data-toggle="tooltip" data-placement="top" data-original-title="" type="button"
                                    class="btn btn-outline float-right" onclick="addChannel(this)"><i class="fa fa-save"></i> Save</button>
                                
                            </div>
                        </div>
                    </div>

                </form>                

            </div>
        </div>
    </div>
</div>