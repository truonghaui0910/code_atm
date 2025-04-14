<div class="col-md-12">
    <div class="card-box">
        <h4 class="text-dark  header-title m-t-0">TITLE</h4>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <div class="col-12">
                        <select name="ck_replace_title" class="select2_multiple form-control ck_replace_title">
                            <option value="0">Do not replace</option>
                            <option value="1">Replace part</option>
                            <option value="2">Replace all</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="div_ck_replace_title_1 disp-none col-md-12">
                <div class="col-md-12">
                    <div class="checkbox checkbox-primary">
                        <input id="ck_title_add_begin" class='ck_title_add_begin'  name="ck_title_add_begin" type="checkbox">
                        <label for="ck_title_add_begin">
                            Add to the beginning
                        </label>
                    </div>
                    <div class="form-group row div_ck_title_add_begin disp-none">
                        <div class="col-12">
                            <input type="text" name="txt_title_replace_first" id="txt_title_replace_first" class="form-control " >
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="checkbox checkbox-primary">
                        <input id="ck_title_add_end" class='ck_title_add_end'  name="ck_title_add_end" type="checkbox">
                        <label for="ck_title_add_end">
                            Add to the end
                        </label>
                    </div>
                    <div class="form-group row div_ck_title_add_end disp-none">
                        <div class="col-12">
                            <input type="text" name="txt_title_replace_last" id="txt_title_replace_last" class="form-control " >
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="checkbox checkbox-primary">
                        <input id="ck_title_replace" class='ck_title_replace' name="ck_title_replace" type="checkbox">
                        <label for="ck_title_replace">
                            Replace
                        </label>
                    </div>
                    <div class="disp-none div_ck_title_replace">
                        <div class="disp-flex ">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">From</label>
                                <div class="col-12">
                                    <input type="text" name="txt_title_replace_from" id="txt_title_replace_from" class="form-control" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-6 col-form-label">To</label>
                                <div class="col-12">
                                    <input type="text" name="txt_title_replace_to" id="txt_title_replace_to" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="checkbox checkbox-primary">
                        <input id="ck_song_title_replace" class='ck_song_title_replace' name="ck_song_title_replace" type="checkbox">
                        <label for="ck_song_title_replace">
                            Replace song title
                        </label>
                    </div>
                    <div class="disp-none div_ck_song_title_replace">
                        <div class="disp-flex ">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">From</label>
                                <div class="col-12">
                                    <input type="text" name="txt_title_replace_from" id="txt_title_replace_from" class="form-control" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-6 col-form-label">To</label>
                                <div class="col-12">
                                    <input type="text" name="txt_title_replace_to" id="txt_title_replace_to" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="checkbox checkbox-primary m-t-5 m-l-10">
                    <input type="checkbox" name="ck_auto_increament" id="ck_auto_increament" value="1">
                    <label class="cust-checkbox" for="ck_auto_increament">Auto increase</label> <br>  
                </div>
                <div class="div_ck_auto_increament disp-none" >
                    <div class="disp-flex margin-top-10" >
                        <div class="col-md-8">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Prefix</label>
                                <div class="col-12">
                                    <input type="text" name="auto_increament_prefix" id="auto_increament_prefix" class="form-control " placeholder="Part,Ep,#...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Count</label>
                                <div class="col-12">
                                    <input type="text" name="auto_increament_count" id="auto_increament_count" class="form-control " value="1">
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="div_ck_replace_title_2 disp-none" style="width: 100%">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">Replace all</label>
                        <div class="col-12">
                            <input type="text" name="txt_title_replace_all" id="txt_title_replace_all" class="form-control " >
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>