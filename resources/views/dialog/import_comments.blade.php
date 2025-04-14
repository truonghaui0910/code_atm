<div class="modal fade" id="dialog_import_comments" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="display: block">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <div class="row">
                    <div class="col-md-9">
                        <h5 class="modal-title"><span class="dialog-icon"><i class="ti-import"></i></span> <span id="dialog_import_comments_title">Import Comment</span></h5>
                    </div>

                    <div class="col-md-3 pull-right">
                    </div>
                </div>
            </div>
            <br>

            <div class="modal-body">
                <form id="form-import-comments">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-8  m-b-10">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Campaign</label>
                                <div class="col-12">
                                    <select id="comment_campaign" name="comment_campaign[]" multiple="" class="select2_multiple form-control search_select" data-show-subtext="true" data-live-search="true" style="height: 100px">
                                        @foreach($promoClaims as $data)
                                        <option value="{{$data->id}}">{{$data->id}} {{$data->cam_type}}[{{$data->genre}}] [{{$data->artist}}] [{{$data->song_name}}] [{{$data->remain_comment}} remain] [{{$data->video_comment }} videos > 10000 views]</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-12 m-b-10">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Comment Content <span id="comment_count">(0)</span></label>
                            <div class="col-12">
                                <textarea class="form-control" rows="5"  id="comments" name="comments"
                                          spellcheck="false" placeholder="Comments Content"
                                          style="line-height: 1.25;height: 110px"></textarea>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4  m-b-10">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Schedules</label>
                                <div class="col-12">
                                    <select id="comment_schedule" name="comment_schedule" class="select2_multiple form-control">
                                        <option value="1">Daily</option>
                                        <option value="7">Weekly</option>
                                        <option value="30">Monthly</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4  m-b-10">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Number comment</label>
                                <div class="col-12">
                                    <input id="comment_number" type="number" name="comment_number" class="form-control" value="1"
                                           placeholder="Number of comments">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4  m-b-10">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Status</label>
                                <div class="col-12">
                                    <select id="comment_status" name="comment_status" class="select2_multiple form-control">
                                        <option value="1">Running</option>
                                        <option value="0">Stopping</option>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
<!--                    <div class="row">
                        <div class="col-md-6  m-b-10">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Last Run</label>
                                <div class="col-12">
                                    <input id="last_run" type="text"  class="form-control" readonly="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6  m-b-10">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Next Run</label>
                                <div class="col-12">
                                    <input id="next_run" type="text"  class="form-control" readonly="true">
                                </div>
                            </div>
                        </div>
                    </div>-->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-outline-info btn-sm btn-import-comment"><i class="fa fa-save"></i> Save</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div id="import_comments_loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
                <div id="import_comments_result">

                </div>
            </div>

        </div>
    </div>
</div>