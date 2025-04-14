<div class="modal fade" id="dialog_labelgrid_releases" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-calendar-check-o fa-fw"></i> <span
                        id="dialog-list-tile">Releases</span></h4>
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i
                    class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-dialog" class="modal-body">
                <form id="form_release" method="POST" spellcheck="false">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Artist</label>
                                <div class="col-12">
                                    <select name="artist" class="select2_multiple form-control">
                                        {!!$labelGridArtist!!}
                                    </select> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Release Date</label>
                                <div class="col-12">
                                    <input type="text" name="releaseDate" class="form-control" value="{{$defaultReleaseDate}}" data-mask="9999-99-99">
                                </div>
                            </div>
                        </div>
                        <!--                        <div class="col-md-6">
                                                    <label class="col-4 col-form-label">Photos</label>
                                                    <div class="row col-md-12">
                                                        <div class="col-md-8">
                                                            <input id="photo_tmp_upload" type="file"  class="form-control" style="line-height: 1.25;content: Button">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <button type="button" class="btn btn-outline-warning btn-upload-img" value='photo_tmp'><i class="fa fa-upload"></i> Upload</button>
                                                        </div>
                                                    </div>
                                                </div>-->
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Title</label>
                                <div class="col-12">
                                    <input id="title" type="text" name="title" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Description Short</label>
                                <div class="col-12">
                                    <input id="descriptionShort" type="text" name="descriptionShort" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Description Long</label>
                                <div class="col-12">
                                    <textarea class="form-control" rows="5" id="descriptionLong" name="descriptionLong"
                                              spellcheck="false" style="line-height: 1.25;height: 200px"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-outline-info btn-sm btn-submit-release"><i class="fa fa-upload"></i> Submit</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>