<div class="modal fade" id="dialog_labelgrid_track" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-music fa-fw"></i> <span
                        id="dialog-list-tile">Track</span></h4>
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i
                    class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-dialog" class="modal-body">
                <form id="form_track" method="POST" spellcheck="false">
                    {{ csrf_field() }}
                    <input type="hidden" name="total_contri" id="total_contri"/>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Artist</label>
                                <div class="col-12">
                                    <select name="artist_id" class="select2_multiple form-control">
                                        {!!$labelGridArtist!!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Release</label>
                                <div class="col-12">
                                    <select name="release_id" class="select2_multiple form-control">
                                        {!!$labelGridRelease!!}
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
                                    <input id="title" type="text" name="title" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-12 col-form-label">Contributors</label>
                        <table class="border-0 table-contri">
                            <tr>
                                <td class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Party Name</label>
                                        <div class="col-12">
                                            <input id="party_name1" curr_contri="1" type="text"  class="form-control party_name text-left">
                                        </div>
                                    </div>
                                </td>
                                <td class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Roles</label>
                                        <div class="col-12">
                                            <select curr_contri="1" class="select2_multiple form-control roles" multiple="" style="height: 56px;overflow: hidden">
                                                <option value="Composer">Composer</option>
                                                <option value="Lyricst">Lyricst</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                                <input type="hidden" name="contri[]" class="contri1"/>
                                <td class="col-md-1">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">&nbsp;</label>
                                        <div class="col-12">
                                            <button type="button"
                                                class="btn btn-outline-info btn-sm btn-add-cointributors"
                                                onclick="addContributors()"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-md-12" <div class="form-group row"><label
                                class="col-12 col-form-label">Lyric</label>
                            <textarea class="form-control" rows="5"
                                    name="lyrics" spellcheck="false"
                                    style="line-height: 1.25;height: 200px"></textarea>
                        </div>
                    </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <button type="button" class="btn btn-outline-info btn-sm btn-submit-track"><i
                                class="fa fa-upload"></i> Submit</button>
                    </div>
                </div>
            </div>

            </form>
        </div>
    </div>
</div>
</div>