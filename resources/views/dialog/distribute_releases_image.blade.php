<div class="modal fade" id="dialog_labelgrid_releases_image" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-calendar-check-o fa-fw"></i> <span
                        id="dialog-list-tile">Releases Image</span></h4>
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i
                    class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-dialog" class="modal-body">
                <form id="form_release_image" method="POST" spellcheck="false" action="labelgridReleaseImage"> 
                    {{ csrf_field() }}
                    <input type="hidden" name="release_id" id="release_id_image">
                    <div class="row">
                        <div class="col-md-4" style="margin: 0 auto;">
                            <div class="member-card">
                                <div class="thumb-xl member-thumb m-b-10 center-block">

                                    <img src="images/default-avatar.png" class="rounded-circle img-thumbnail" alt="profile-image" style="width: 235px;height: 235px">

                                    <input style="width: 100%;height: 235px;position: absolute;top: 0;left: 0;opacity: 0;"
                                           id="release_image_upload" 
                                           data-width="3000"
                                           data-height="3000"
                                           type="file" 
                                           data-toggle="tooltip" 
                                           data-placement="top" 
                                           data-original-title="Click to upload image" 
                                           accept="image/jpeg,image/png"/>
                                    <input type="hidden" id="release_image_3000" name="release_image_3000" value="" />
                                    <span class="image_spinner disp-none" style="
                                          position: absolute;
                                          top: 42%;
                                          left: 44%;
                                          "><i class="fa fa-spinner fa-2x fa-spin"></i></span>
                                </div>

                            </div>

                        </div>
                        <div class="col-md-12 text-center" style="margin: 0 auto;">
                            <button id="btn_release_upload_image" type="button" class="btn btn-outline-warning btn-sm"><i class="fa fa-upload"></i> Upload</button>
                        </div>
                        <!--                        <div class="col-md-6">
                                                    <label class="col-4 col-form-label">Photos</label>
                                                    <div class="row col-md-12">
                                                        <div class="col-md-8">
                                                            <input type="file"  class="form-control" style="line-height: 1.25;content: Button">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <button type="button" class="btn btn-outline-warning btn-upload-img" data-width='3000' data-height='3000' value='photo_tmp'><i class="fa fa-upload"></i> Upload</button>
                                                        </div>
                                                    </div>
                                                </div>-->
                    </div>
                </form>
            </div>
        </div>
    </div>