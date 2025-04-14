<div class="modal fade" id="dialog_bom_group_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <span id="created_by_avatar"></span>
                            <strong id="dialog-task-title">Add music to group</strong>
                        </h5>
                    </div>
                    <div class="">

                        <button
                            class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light btn-close-modal"
                            data-id="modal-add-task" data-toggle="tooltip" data-placement="top" title="Close"
                            data-dismiss="modal" style="padding-bottom: 0.6rem;padding-top: 0.6rem;z-index: 1001">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                <path
                                    d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div id="dialog_bom_group_list-claim" style="text-align: center;display: none"><i
                    class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-dialog" class="modal-body">
                <form id="form_group_list" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="boom_add_id" id="boom_add_id" class="form-control">
                    <input type="hidden" name="add_type" id="add_type" class="form-control">
                    <div class="group_list_content">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="checkbox checkbox-primary">
                                    <input id="g_123" class="g_123" type="checkbox" onchange="addToGroup(123)">
                                    <label for="g_123">Lyric videos</label>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="checkbox checkbox-primary">
                                    <input id="p_123" class="p_123" type="checkbox" onchange="addPriority(123)">
                                    <label for="p_123"><i class="ion-star"></i></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="">
                        <button data-toggle="tooltip" data-placement="top" data-original-title="" type="button"
                            class="btn btn-sm btn-show-add-group-form w-100" onclick="showAddBomGroup()"><i
                                class="fa fa-plus"></i> Add new group</button>
                    </div>
<!--                    <div class="add_new_group_list disp-none fadeInDown">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">Group Name</label>
                                <div class="col-12">
                                    <input id="group_name" class="form-control input-sm" type="text"
                                        name="group_name" value="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-8 col-form-label">Description</label>
                                <div class="col-12">
                                    <textarea id="group_des" class="form-control" rows="5" name="group_des" spellcheck="false" placeholder=""
                                        style="line-height: 1.25;height: 100px"></textarea>
                                </div>
                            </div>
                            <button data-toggle="tooltip" data-placement="top" data-original-title="" type="button"
                                class="btn btn-sm" onclick="hideAddGroupForm()"><i class="fa fa-times"></i>
                                Close</button>
                            <button data-toggle="tooltip" data-placement="top" data-original-title="" type="button"
                                class="btn btn-sm" onclick="addGroup()"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </div>-->

                </form>

            </div>
        </div>
    </div>
</div>
