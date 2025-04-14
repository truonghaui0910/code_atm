<div class="modal fade" id="dialog_card_endscreen" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-80">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                
                <h5 class="modal-title"><span class="dialog-icon"><i class="fa fa-id-card"></i></span> <span>ADD CARD & ENDSCREEN</span></h5>                        
                <span id="btnCardSubmit"  class="btn btn-outline-success btn-micro pull-right">ADD</span>
            </div>
            <div id="dialog_card_endscreen_loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="dialog_card_endscreen_content" class="modal-body">
                <div class="row">
                    
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-box" style="box-shadow: 0 4px 6px -3px #cbbcbc">
                                    <div class="btn-group m-b-20">
                                        <button type="button" class="btn btn-success dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Card <span class="caret"></span></button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" onclick="addCard('video')">Video</a>
                                            <a class="dropdown-item" onclick="addCard('playlist')">Playlist</a>
                                            <a class="dropdown-item" onclick="addCard('channel')">Channel</a>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div id="accordion" role="tablist" aria-multiselectable="true" class="m-b-20">

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-box" style="box-shadow: 0 4px 6px -3px #cbbcbc">
                                    <h4>Endscreen</h4>
                                    <div class="row">
                                        <div class="col-md-6 " >
                                            <div class="card-box endsTemplate endscreen-template-active" data-tpl="tpl1" >    
                                                <div>
                                                    <div style="width: 100px;height: 55px;margin-bottom: 0.5rem;background: #ccc"></div>
                                                    <div style="width: 100px;height: 55px;background: #ccc"></div>
                                                </div>
                                                <div style="width: 55px;height: 55px;border-radius: 50%;background: #ccc"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 " >
                                            <div class="card-box endsTemplate " data-tpl="tpl2" style="align-items: flex-end">    
                                                <div style="width: 100px;height: 55px;background: #ccc"></div>
                                                <div style="width: 55px;height: 55px;border-radius: 50%;background: #ccc"></div>
                                                <div style="width: 100px;height: 55px;background: #ccc"></div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-12">
                                            <div class="radio">
                                                <input class="radio_endscreen" type="radio" name="video_type_ends" id="radio1" value="1">
                                                <label for="radio1">
                                                    Most recent upload <span class="font-13">(Automatically feature the most recently uploaded video)</span>
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <input class="radio_endscreen" type="radio" name="video_type_ends" id="radio2" value="2" checked="true">
                                                <label for="radio2">
                                                    Best for viewer (Allow YouTube to select a video from your channel to best suit the viewer)
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <input class="radio_endscreen" type="radio" name="video_type_ends" id="radio3" value="3">
                                                <label for="radio3">
                                                    Choose specific video (Select from your videos, or from any video on YouTube)
                                                </label>
                                            </div>
                                            <div class="col-md-12 disp-none div_radio_endscreen_3">
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <input class="form-control radius-6" type="text"  name="video_link_endscreen" placeholder="Video link">     
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <input class="form-control radius-6" type="text"  name="playlist_link_endscreen" placeholder="Playlist link">     
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input id="template_encscreen" type="hidden" name="template_encscreen" value="tpl1"/>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="row">
                    <div class="col-md-12"></div>

                </div>

            </div>
        </div>
    </div>
</div>