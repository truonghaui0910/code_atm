<div class="modal fade" id="dialog_add_playlist" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class=" ti-plus fa-fw"></i> <span
                        id="dialog-wakeup-tile">Add playlist</span></h4>
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-dialog" class="modal-body">
                <form id="form-playlist-add" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_channel" id="playlist_id_channel">
                    <!--<input type="hidden" name="wakeup_type" id="wakeup_type" value="3">-->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Playlist Type</label>
                                <div class="col-12">
                                    <select id="wakeup_type" class="form-control" name="wakeup_type">
                                        <option value="1">Normal playlist</option>
                                        <option value="2">Wakeup playlist</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Title</label>
                                <div class="col-12">
                                    <input id="title" type="text" name="title" class="form-control" >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Run Time</label>
                                <div class="col-12">
                                    <input id="run_time" type="text" name="run_time" class="form-control" value="{{$current}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Source Type</label>
                                <div class="col-12">
                                    <select id="source_wakeup_type" class="source_wakeup_type form-control" name="source_wakeup_type">
                                        <option value="1">Playlist</option>
                                        <option value="2">List Video</option>
                                        <option value="3">Videos on Channel</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="div_source_wakeup_type_1">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Playlist Link</label>
                                    <div class="col-12">
                                        <input id="playlist_source" type="text" name="playlist_source" class="form-control" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="div_source_wakeup_type_2 disp-none">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-4 col-form-label">List Video</label>
                                    <div class="col-12">
                                        <textarea class="form-control" rows="5" id="videos_list_source" name="videos_list_source"
                                                  spellcheck="false" style="line-height: 1.25;height: 200px"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="div_source_wakeup_type_3 disp-none">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Sort by</label>
                                    <div class="col-12">
                                        <select  id="wakeup_sort" class="form-control" name="wakeup_sort">
                                            <option value="1">Views</option>
                                            <option value="2">Daily views</option>
                                            <option value="3">Daily views percent</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Number videos</label>
                                    <div class="col-12">
                                        <input id="number_videos" type="text" name="number_videos" value="20" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Priority List</label>
                                <div class="col-12">
                                    <textarea class="form-control" rows="5" id="priority_promo_list" name="priority_promo_list"
                                              spellcheck="false" style="line-height: 1.25;height: 200px"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button data-type="2" type="button" class="btn btn-outline-info btn-sm btn-add-wakeup"><i class="fa fa-upload"></i> Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>