

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
            <input type="hidden" name="music_type" value="3">
            <input type="hidden" name="source_type" value="1">
            <!--nguon tu tool make lyric  timestamp-->
            <input type="hidden" id="type_lyric_source" name="type_lyric_source" value="{{$type}}">
            <input type="hidden" id="lyric_source_from_tool_make_lyric" name="lyric_source_from_tool_make_lyric" value="{{$lyric}}">
            <input type="hidden" id="music_title" name="music_title" value="{{$music_title}}">
            <input type="hidden" id="music_artist" name="music_artist" value="{{$music_artist}}">

            <div class="div_source_type_1">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Deezer Link</label>
                            <div class="col-12">
                                <textarea class="form-control" rows="5" id="source" name="source"><?php if(isset($link) && $link!=''){echo trim($link);}else{echo $datas[6];}?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>