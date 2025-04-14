<div class="modal fade" id="dialog_create_add" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <span id="created_by_avatar"></span>
                            <strong id="dialog-task-title">Create Channel</strong>
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
                <form id="formCreateEmail" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="email_id" name="email_id"/>
                    <input type="hidden" id="automail_id" name="automail_id"/>
                    <div class="  fadeInDown">
                        <div class="row ">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Email <i class="fa fa-copy" onclick="copyInputValue('fake_email')"></i></label>
                                    <div class="col-12">
                                        <input id="fake_email" class="form-control" type="text" name="fake_email" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Name <i class="fa fa-copy" onclick="copyInputValue('fake_name')"></i></label>
                                    <div class="col-12">
                                        <input id="fake_name" class="form-control" type="text" name="fake_name" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Recovery Email <i class="fa fa-copy" onclick="copyInputValue('fake_recovery')"></i></label>
                                    <div class="col-12">
                                        <input id="fake_recovery" class="form-control" type="text" name="fake_recovery" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Phone <i class="fa fa-copy" onclick="copyInputValue('fake_phone')"></i></label>
                                    <div class="col-12">
                                        <input id="fake_phone" class="form-control" type="text" name="fake_phone" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Password <i class="fa fa-copy" onclick="copyInputValue('fake_pass')"></i></label>
                                    <div class="col-12">
                                        <input id="fake_pass" class="form-control" type="text" name="fake_pass" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Birth Date <i class="fa fa-copy" onclick="copyInputValue('fake_birth')"></i></label>
                                    <div class="col-12">
                                        <input id="fake_birth" class="form-control" type="text" name="fake_birth" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button id="btn-open-brower" data-toggle="tooltip" data-placement="top" data-original-title="" type="button" class="btn btn-outline float-right m-r-10 disp-none"><i class="fa fa-share-alt-square"></i> Open Brower</button>
                                <button id="btn-create-email" data-toggle="tooltip" data-placement="top" data-original-title="" type="button" class="btn btn-outline float-right m-r-10" onclick="createEmail(this)"><i class="fa fa-save"></i> Save</button>

                            </div>
                        </div>
                    </div>

                </form>                

            </div>
        </div>
    </div>
</div>