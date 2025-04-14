<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="text-dark  header-title m-t-0">BACKGROUND</h4>
            <br>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group row">
                        <label class="col-6 col-form-label">Background type</label>
                        <div class="col-12">
                            <select id="background_type" name="background_type" class="form-control input-sm background_type" >
                                <option value="1">Gradient</option>
                                <option value="2">Image</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="div_background_type_1">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-6 col-form-label">Choose gradient</label>
                            <div class="col-12">
                                <select id="gradient_color" class="form-control input-sm background_type" >
                                    @foreach($list_gradient as $data)
                                    <option value="{{$data->color1}},{{$data->color2}}">{{$data->name}} </option>
                                    @endforeach     
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-6 col-form-label">From Color</label>
                            <div class="col-12">
                                <input type="text" name="g_color_s" id="g_color_s" class="form-control colorpicker-element" value="051937">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-6 col-form-label">To Color</label>
                            <div class="col-12">
                                <input type="text" name="g_color_e" id="g_color_e" class="form-control colorpicker-element" value="A8EB12">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-6 col-form-label">Orientation</label>
                            <div class="col-12">
                                <select id="g_type" name="g_type" class="form-control input-sm background_type" >
                                    <option value="1">Top</option>
                                    <option value="2">Left</option>
                                    <option value="3">Bottom</option>
                                    <option value="4">Right</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="div_normal_bg div_background_type_2 disp-none">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-6 col-form-label">Image By</label>
                            <div class="col-12">
                                <select id="bg_type" class="bg_type form-control input-sm" >
                                    <option value="2">Artists</option>
                                    <option value="1">Other</option>                        
                                    <option value="6">Lyric</option>                        
                                    <option value="8">Upload</option>                        
                                    <option value="9">Drive</option>                        
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 div_bg_type_2 m-l-15">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Artists</label> 
                            <div class="col-12">
                                <input id="txt_search_artists_bg" type="text" name="txt_search_artists" class="form-control txt_search_artists" placeholder="Enter artists name">
                            </div>
                            <div class="col-12">
                                <select id="bg_artists" class="form-control input-sm ck_artists" multiple style="height: 200px">
                                    @foreach($list_artists_img as $data)
                                    <option style="background-image:url( http://51.75.243.130/music/artists_thumb/{{$data->artists}}.jpg);" value="{{$data->artists}}">
                                        <?php
                                        echo ucwords(str_replace("_", " ", $data->artists));
                                        ?> 
                                    </option>
                                    @endforeach                      
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 div_bg_type_1 m-l-15 disp-none">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Other</label>
                            <div class="col-12">
                                <select id="bg_other" class="form-control input-sm" multiple style="height: 100px">
                                    @foreach($list_artists_img_other as $data)
                                    <option value="{{$data->topic}}">{{$data->topic}} </option>
                                    @endforeach                        
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 div_bg_type_6 m-l-15 disp-none">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Lyric</label>
                            <div class="col-12">
                                <select id="bg_lyric" class="form-control input-sm" multiple style="height: 100px">
                                    @foreach($list_artists_img_lyric as $data)
                                    <option value="{{$data->topic}}">{{$data->topic}} </option>
                                    @endforeach                        
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 div_bg_type_8 m-l-15 disp-none">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Upload</label>
                            <div class="row col-md-12">
                                <div class="col-md-8">
                                    <input id="image_upload" type="file" name="image_upload" class="form-control" style="line-height: 1.25;content: Button">
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-outline-warning btn-submit-upload"><i class="fa fa-upload"></i> Upload</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 div_bg_type_9 m-l-15 disp-none">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Link Drive</label>
                            <div class="row col-md-12">
                                <div class="col-md-8">
                                    <input id="drive_file" type="text" name="drive_file" class="form-control" >
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-outline-warning btn-get-image"><i class="fa fa-download"></i> Get Image</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="div_bg_image">
                    <div class="row">
                        <div class="col-md-12">
                            <select id="background" name="background[]" multiple="multiple" class="image-picker">
                                <?php
                                foreach ($list_image as $image) {
                                    echo '<option data-img-src=" http://51.75.243.130/resize.php?filename=' . $image->link . '" value="' . $image->link . '">' . $image->id . '</option>';
                                }
                                ?>
                            </select>            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" value="30" class="btn btn-t btn-sm btn-loadding" id="btnLoadMore" onclick="getBackgroundByOffset()"
                                data-loading-text="<i class='fa fa-spinner fa-spin '></i> {{ trans('label.button.loadding') }}">
                            <i class="fa fa-refresh fa-fw"></i>Load More
                        </button>
                    </div>
                </div>


            </div>
        </div>
    </div>  
</div>


