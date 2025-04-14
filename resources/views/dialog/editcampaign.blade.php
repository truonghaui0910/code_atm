<div class="modal fade" id="dialog_edit_campaign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-80">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="ti-pencil-alt fa-fw"></i> <span id="dialog-list-tile">Edit Campaign</span></h4>                        
            </div>
            <div id="dialog-loading-edit" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-dialog-edit" class="modal-body">
                <form id="formedit" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="cam_id" id="cam_id" class="form-control">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Campaign Name</label>
                                <div class="col-12">
                                    <input id="campaign_name_edit" type="text" name="campaign_name" class="form-control">
                                </div>
                            </div>
                        </div>
                        <!--                        <div class="col-md-3">
                                                    <div class="form-group row">
                                                        <label class="col-4 col-form-label">&nbsp;</label>
                                                        <div class="col-12">
                                                            <select class="form-control input-sm campaign_select">
                                                                <option value="">--Select--</option>
                                                                @foreach($listCampaign as $campaign)
                                                                <option value="{{$campaign->campaign_name}}">{{$campaign->campaign_name}}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>-->
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Campaign Type</label>
                                <div class="col-12">
                                    <select class="form-control input-sm " name="campaign_type">
                                        <option value="premium">Premium</option>
                                        <option value="medium">Medium</option>
                                        <option value="regular">Regular</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Campaign Start Date</label>
                                <div class="col-12">
                                    <input type="text" id="campaign_start_date_edit" name="campaign_start_date"  placeholder="MM/DD/YYYY" value="<?php echo gmdate("m/d/Y", time() + 7 * 3600); ?>"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Genre</label>
                                <div class="col-12">
                                    <input type="text" id="genre_edit" name="genre" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Label</label>
                                <div class="col-12">
                                    <input type="text" id="label_edit" name="label" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Artist</label>
                                <div class="col-12">
                                    <input type="text" id="artist_edit" name="artist" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Song Name</label>
                                <div class="col-12">
                                    <input type="text" id="songname_edit" name="songname" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>                    

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Artist Channel URL</label>
                                <div class="col-12">
                                    <input type="text" id="artists_channel_edit" name="artists_channel" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Artist Playlist URL</label>
                                <div class="col-12">
                                    <input type="text" id="artists_playlist_edit" name="artists_playlist" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Artist Social Links</label>
                                <div class="col-12">
                                    <!--<input type="text"id="artists_social_edit" name="artists_social" class="form-control">-->
                                    <textarea class="form-control" rows="5" id="artists_social_edit" name="artists_social"
                                              spellcheck="false" style="line-height: 1.25;height: 50px"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Official Video URL</label>
                                <div class="col-12">
                                    <input type="text" id="official_video_edit" name="official_video" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Bitly URL</label>
                                <div class="col-12">
                                    <input type="text" id="bitly_url_edit" name="bitly_url" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Guest Artist Name 1</label>
                                <div class="col-12">
                                    <input type="text"  id="guest_artist_1_edit" name="guest_artist_1" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Guest Artist Name 2</label>
                                <div class="col-12">
                                    <input type="text" id="guest_artist_2_edit" name="guest_artist_2" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Guest Artist Name 3</label>
                                <div class="col-12">
                                    <input type="text" id="guest_artist_3_edit" name="guest_artist_3" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Promos keywords</label>
                                <div class="col-12">
                                    <input type="text" id="promo_keywords_edit" name="promo_keywords" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Update Homepage Video</label>
                                <div class="col-12">
                                    <select class="form-control input-sm " name="homepage_video_update">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="col-4 col-form-label">Audio File</label>
                            <div class="row col-md-12">
                                <div class="col-md-8">
                                    <input type="hidden" id='audio_url' class="audio_url" name ='audio_url' value=''/>
                                    <input id="audio_upload" type="file" name="audio_upload" class="form-control" accept=".mp3,.wav" style="line-height: 1.25;content: Button"> 
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-outline-warning btn-submit-upload" value='audio'><i class="fa fa-upload"></i> Upload</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="col-4 col-form-label">Lyric File</label>
                            <div class="row col-md-12">
                                <div class="col-md-8">
                                    <input type="hidden" id='lyric_url' class="lyric_url" name ='lyric_url' value=''/>
                                    <input id="lyric_upload" type="file" name="lyric_upload" class="form-control" accept=".txt" style="line-height: 1.25;content: Button">
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-outline-warning btn-submit-upload" value='lyric'><i class="fa fa-upload"></i> Upload</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Lyrics</label>
                                <div class="col-12">
                                    <textarea class="form-control" rows="5" id="lyrics_edit" name="lyrics"
                                              spellcheck="false" style="line-height: 1.25;height: 200px"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>                    

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-outline-info btn-sm btn-edit-campaign"><i class="fa fa-upload"></i> Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>