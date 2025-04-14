<div class="col-md-4">
    <div class="div_type_reup_2 disp-none">
    <div class="card-box">
        <h4 class="text-dark  header-title m-t-0">DESCRIPTION</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <div class="col-12">
                        <select name="ck_replace_des" class="select2_multiple form-control ck_replace_des">
                            <option value="0">Do not replace</option>
                            <option value="1">Replace part</option>
                            <option value="2">Replace all</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="div_ck_replace_des_1 disp-none col-md-12">
                <div class="col-md-12">
                    <div class="checkbox checkbox-primary">
                        <input id="ck_des_add_begin" class='ck_des_add_begin'  name="ck_des_add_begin" type="checkbox">
                        <label for="ck_des_add_begin">
                            Add to the beginning
                        </label> <button class="btn btn-success waves-effect waves-light btn-sm m-b-5 disp-none div_add_time_1 "> <i class="fa fa-clock-o"></i> Add time</button>
                    </div>
                    <div class="form-group row div_ck_des_add_begin disp-none">
                        <div class="col-12">
                            <textarea id="txt_des_replace_first" class="form-control" name="txt_des_replace_first" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="checkbox checkbox-primary">
                        <input id="ck_des_add_end" class='ck_des_add_end' name="ck_des_add_end" type="checkbox">
                        <label for="ck_des_add_end">
                            Add to the end
                        </label> <button class="btn btn-success waves-effect waves-light btn-sm m-b-5 disp-none div_add_time_2"><i class="fa fa-clock-o"></i> Add time</button>
                    </div>
                    <div class="form-group row div_ck_des_add_end disp-none">
                        <div class="col-12">
                            <textarea id="txt_des_replace_last" class="form-control" name="txt_des_replace_last" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="checkbox checkbox-primary">
                        <input id="ck_des_replace" class='ck_des_replace' name="ck_des_replace" type="checkbox">
                        <label for="ck_des_replace">
                            Replace
                        </label>
                    </div>
                    <div class="disp-none div_ck_des_replace">
                        <div class="disp-flex ">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">From</label>
                                <div class="col-12">
                                    <input type="text" name="txt_des_replace_from" id="txt_des_replace_from" class="form-control " >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-6 col-form-label">To</label>
                                <div class="col-12">
                                    <input type="text" name="txt_des_replace_to" id="txt_des_replace_to" class="form-control " >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="checkbox checkbox-primary">
                        <input id="ck_des_remove_link" class='ck_des_remove_link'  name="ck_des_remove_link" type="checkbox" checked="true">
                        <label for="ck_des_remove_link">
                            Remove link in description
                        </label>
                    </div>
                </div>
            </div>
            <div class="div_ck_replace_des_2 disp-none" style="width: 100%">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Replace all</label> 
                            <button class="btn btn-success waves-effect waves-light btn-sm m-b-5 disp-none div_add_time_3"><i class="fa fa-clock-o"></i> Add time</button>
                            <div class="col-12">
                                <!--<input type="text" name="txt_des_replace_all" id="txt_des_replace_all" class="form-control " >-->
                                <textarea id="txt_des_replace_all" class="form-control" name="txt_des_replace_all" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--            <div class="div_ck_replace_des_3 disp-none">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="checkbox checkbox-primary">
                                        <input id="ck_des_remove_link" class='ck_des_remove_link'  name="ck_des_remove_link" type="checkbox" checked="true">
                                        <label for="ck_des_remove_link">
                                            Remove link in description
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>-->
        </div>

    </div>
    </div>
</div>