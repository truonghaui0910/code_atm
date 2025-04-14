<!--<div class="modal fade bs-example-modal-lg dialog-import" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">Import Channel</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-6">
                        <input type="text" id="txt_filter_channel_name"  class="form-control" placeholder="Channel name" value="{{$request->channel_name}}">
                        <textarea class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div> /.modal-content 
    </div> /.modal-dialog 
</div> /.modal -->

<div id="dialog-import" class="modal-demo">
    <button type="button" class="close" onclick="Custombox.close();">
        <span>&times;</span><span class="sr-only">Close</span>
    </button>
    <h4 class="custom-modal-title">Import Channel</h4>
    <div class="custom-modal-text">
        <div class="row">
            <div class="col-md-12 ">
                <div class="form-inline m-b-10">
                    <div class="form-group col-12">
                        <form id="form-import-channel" style="width: 100%" method="POST" action="">
                            <textarea name="channel" class="form-control" placeholder="channel_id,channel_name,email"
                                cols="10" style="width: 100%  ;  min-height: 300px;"></textarea>
                        </form>
                    </div>

                </div>

            </div>
            <div class="col-12 m-t-20">
                <!--<a id='import' style="cursor: pointer;color: #000">Import</a>-->
                <button id='import' type="button" class="btn btn-outline-primary ">Import</button>
            </div>
        </div>

    </div>
</div>