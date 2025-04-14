

<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="text-dark  header-title m-t-0">SOURCE</h4>
            <?php //echo json_encode($datas) ;?>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-4 col-form-label">Reup Type</label>

                        <div class="col-12">
                            <select class="form-control input-sm type_reup" name="type_reup">
                                <option value="1">Download</option>
                                <option value="2">Upload</option>
                            </select>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row div_type_reup_2 disp-none">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-4 col-form-label">Channel ({{$datas[1]}})</label>

                        <div class="col-12">
                            <select class="form-control input-sm" name="channel">
                                {!!$datas[0]!!}
                            </select>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-4 col-form-label">Music Type</label>
                        <div class="col-12">
                            <select class="music_type form-control input-sm" name="music_type">
                                <!--                                <option value="1">Single music</option>
                                                                <option value="2">Mix music</option>                        -->
                                {!!$datas[4]!!}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="div_music_type_2 m-l-15 <?php echo ($datas[2]->type == 2) ? "" : "disp-none"; ?>">
                <div class="row">
                    <!--                    <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-4 col-form-label">Number Video</label>
                                                <div class="col-12">
                                                    <input type="text" id="mix_number_video" name="mix_number_video" class="form-control" value="20">
                                                </div>
                                            </div>
                                        </div>-->
                    <div class="col-md-4">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Songs/Video</label>
                            <div class="col-12">
                                <input type="text" id="mix_number_song" name="mix_number_song" class="form-control" value="<?php echo $datas[2]->choose_video_number == null ? "10" : $datas[2]->choose_video_number; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">Config get music</label>
                            <div class="col-12">
                                <select class="group_mix_music form-control input-sm" name="choose_video_option_op1" >
                                    <option value="0">All</option>
                                    <option value="1">Based on a certain index</option>                        
                                    <option value="2">Evenly select between singers sequential</option>                        
                                    <option value="3">Take each singer until it is equal to the Songs/Video number</option>                        
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="div_group_mix_music_1">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Order</label>
                                <div class="col-12">
                                    <select class="form-control input-sm" name="choose_video_option_op2">
                                        <option value="1">Sequential</option>                       
                                        <option value="0">Random</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
                <!--                <div class=" m-l-15 disp-none div_group_mix_music_2">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-4 col-form-label">Group Artists</label>
                                                <div class="col-12">
                                                    <select class="form-control input-sm" name="group_mix_music_val_2">
                                                                                                <option value="1">So sánh tất cả rồi đưa ra 1 list</option>
                                                                                                <option value="2">Top theo tuần tự</option>
                                                                                                <option value="3">Lấy từng cụm cho bằng tổng số lượng->random</option>
                                                                                                <option value="4">Lấy từng cụm cho bằng tổng số lượng->tuần tự</option>                                  
                                                        <option value="1">Evenly select between singers</option>
                                                        <option value="2">Top theo tuần tự</option>
                                                        <option value="3">Lấy từng cụm cho bằng tổng số lượng->random</option>
                                                        <option value="4">Lấy từng cụm cho bằng tổng số lượng->tuần tự</option>                                  
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                    
                                </div>-->
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-4 col-form-label">Source type</label>

                        <div class="col-12">
                            <select class="source_type form-control input-sm" name="source_type">
                                <!--                                <option value="1">Youtube & Deezer</option>
                                                                <option value="2">Database</option>                        -->
                                {!!$datas[5]!!}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="div_source_type_1 <?php echo ($datas[2]->source_type == 2) ? "disp-none" : ""; ?>">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Link</label>
                            <div class="col-12">
                                <textarea class="form-control" rows="5" name="source" style="line-height: 1.25;height: 200px">{!!$datas[6]!!}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="div_source_type_2 <?php echo ($datas[2]->source_type == 2) ? "" : "disp-none"; ?>">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Topic Music</label>
                            <div class="col-12">
                                <select id="ck_topic_music" class="form-control input-sm" name="ck_topic_music">
                                    <option value="-1">--Select--</option>    
                                    @foreach($list_topic_music as $data)
                                    <option value="{{$data->topic}}">{{$data->topic}}</option>                                
                                    @endforeach                      
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Artists <span id="arti_selected"></span></label>
                            <div class="col-12">
                                <input id="txt_search_artists_music" type="text" name="txt_search_artists" class="form-control txt_search_artists" placeholder="Enter artists name">
                            </div>
                            <div class="col-12">
                                <select id="ck_artists_music" class="form-control input-sm ck_artists" name="ck_artists_music[]" multiple style="height: 200px">

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Songs <span id="song_selected"></span></label>
                            <div class="col-12">
                                <select id="ck_song_music" class="form-control input-sm" name="ck_song_music[]" multiple style="height: 237px">

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>