<div class="modal fade" id="dialog_add_email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-envelope fa-fw"></i> <span>Add Email</span></h4>
            </div>
            <div id="dialog-add-email-loading" class="disp-none" style="text-align: center;"><i
                    class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-dialog" class="modal-body">
                <form id="formAddEmail" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="us_id" name="us_id">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Email</label>
                                <div class="col-12">
                                    <input id="email" type="text" name="email" class="form-control" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Name</label>
                                <div class="col-12">
                                    <input id="us_name" type="text" name="us_name" class="form-control" value="" 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Instagram</label>
                            <div class="col-12">
                                <input id="instagram" type="text" name="instagram" class="form-control" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Link Music</label>
                            <div class="col-12">
                                <input id="link_music" type="text" name="link_music" class="form-control" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Roles</label>
                            <div class="col-12">
                                <select id="role" name="role[]" class="select2_multiple form-control" multiple style="height: 110px">
                                    <option value="PROMO">PROMO</option>
                                    <option value="SUBMISSION">SUBMISSION</option>
                                    <option value="CLAIM">CLAIM</option>
                                    <!--<option value="PROMO_PLUS">PROMO_PLUS</option>-->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 div_password">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Password</label>
                            <div class="col-12">
                                <input id="password" type="text" name="password" class="form-control" value="" readonly="true">
                            </div>
                        </div>
                    </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <button type="button" class="btn btn-outline-info color-g btn-sm btn-add-email" onclick="submitEmail()"><i
                                class="fa fa-save"></i> Save</button>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <div class="form-group">
                        <button type="button" class="btn btn-outline-info color-g btn-sm btn-login-dash" onclick="loginEmail()"><i class="fa fa-share-alt"></i> Login</button>
                    </div>
                </div>
            </div>
            </form>


        </div>
    </div>
</div>
</div>