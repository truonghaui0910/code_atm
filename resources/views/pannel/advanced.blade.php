<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="text-dark  header-title m-t-0">ADVANCED</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">Status</label>
                        <div class="col-12">
                            <select id="privacy_status" name="privacy_status" class="form-control">
                                <option value="public">Public</option>
                                <option value="unlisted">Unlisted</option>
                                <option value="private">Private</option>                      
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">Location</label>
                        <div class="col-12">
                            <select id="location" name="location" class="form-control">
                                {!!$list_location!!}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">Language</label>
                        <div class="col-12">
                            <select id="language" name="language" class="form-control">
                                {!!$list_language!!}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">Cool Down: Subscribe</label>
                        <div class="col-12">
                            <div class="input-group bootstrap-touchspin">
                                <span class="input-group-btn">
                                    <input id="cool_down_subscribe" type="number" value="7" name="cool_down_subscribe" class="form-control" >
                                    <span class="input-group-addon bootstrap-touchspin-postfix">day</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">Upload a new video every X to Y hours</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-1">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">X</label>
                    </div>
                </div>
                <div class="col-md-2">                    
                    <div class="input-group bootstrap-touchspin">
                        <span class="input-group-btn">
                            <input id="txtCooldownFrom" type="number" value="1" name="txtCooldownFrom" class="form-control" >
                            <span class="input-group-addon bootstrap-touchspin-postfix">h</span>
                        </span>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">Y</label>
                    </div>
                </div>
                <div class="col-md-2">                    
                    <div class="input-group bootstrap-touchspin">
                        <span class="input-group-btn">
                            <input id="txtCooldownTo" type="number" value="2" name="txtCooldownTo" class="form-control" >
                            <span class="input-group-addon bootstrap-touchspin-postfix">h</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">Cut video from head</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-1">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">From</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group bootstrap-touchspin">
                        <span class="input-group-btn">
                            <input id="txtCutHeadFrom" type="number" value="0" name="txtCutHeadFrom" class="form-control" >
                            <span class="input-group-addon bootstrap-touchspin-postfix">s</span>
                        </span>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">To</label>
                    </div>
                </div>
                <div class="col-md-2">                    
                    <div class="input-group bootstrap-touchspin">
                        <span class="input-group-btn">
                            <input id="txtCutHeadTo" type="number" value="5" name="txtCutHeadTo" class="form-control" >
                            <span class="input-group-addon bootstrap-touchspin-postfix">s</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">Cut video from end</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-1">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">From</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group bootstrap-touchspin">
                        <span class="input-group-btn">
                            <input id="txtCutEndFrom" type="number" value="0" name="txtCutEndFrom" class="form-control" >
                            <span class="input-group-addon bootstrap-touchspin-postfix">s</span>
                        </span>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">To</label>
                    </div>
                </div>
                <div class="col-md-2">                    
                    <div class="input-group bootstrap-touchspin">
                        <span class="input-group-btn">
                            <input id="txtCutEndTo" type="number" value="5" name="txtCutEndTo" class="form-control" >
                            <span class="input-group-addon bootstrap-touchspin-postfix">s</span>
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>


</div>

