<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="text-dark  header-title m-t-0">BACKGROUND</h4>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="div_music_type_2 disp-none">
                        <div class="checkbox checkbox-primary">
                            <input id="mix_bg" type="checkbox" name="mix_bg">
                            <label for="mix_bg">
                                Mix background
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="div_mix_bg disp-none ">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="col-4 col-form-label">Artists</label>
                                    <div class="col-12">
                                        <select id="mix_bg_artists" class="form-control input-sm" name="mix_bg_artists[]" multiple style="height: 100px">
                                            @foreach($list_artists_img as $data)
                                            <option value="{{$data->artists}}">{{$data->artists}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-6 col-form-label">Style background</label>
                            <div class="col-12 style_bg_picker_class">
                                <select id="ck_background_template" class="style_bg_picker" name="background_template">
                                    <option data-img-src="images/3_f_slash.jpg" value="http://automusic.win/rs/template/mix3_right.zip">3</option>
                                    <option data-img-src="images/3_b_slash.jpg" value="http://automusic.win/rs/template/mix3_left.zip">3</option>
                                    <option data-img-src="images/3_vertical.jpg" value="http://automusic.win/rs/template/mix3_straight.zip">3</option>
                                    <option data-img-src="images/5_f_slash.jpg" value="http://automusic.win/rs/template/mix5_right.zip">5</option>
                                    <option data-img-src="images/5_b_slash.jpg" value="http://automusic.win/rs/template/mix5_left.zip">5</option>
                                    <option data-img-src="images/5_vertical.jpg" value="http://automusic.win/rs/template/mix5_straight.zip">5</option>
                                </select>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="div_normal_bg ">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group row">
                            <label class="col-6 col-form-label">Background Type</label>
                            <div class="col-12">
                                <select id="bg_type" class="bg_type form-control input-sm" >
                                    <option value="2">Artists</option>
                                    <option value="1">Other</option>
                                    <option value="6">Lyrics</option>
                                    <option value="8">Uploads</option> 
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 div_bg_type_2 m-l-15">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Artists</label> 
                            <div class="col-12">
                                <input id="txt_search_artists_bg" type="text" name="txt_search_artists" class="form-control txt_search_artists" placeholder="Enter artist name">
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


