<div id="custom-modal" class="modal-demo">
    <button type="button" class="close" onclick="Custombox.close();">
        <span>&times;</span><span class="sr-only">Close</span>
    </button>
    <h4 class="custom-modal-title">Filter</h4>
    <div class="custom-modal-text">
        <div class="row">
            <div class="col-md-12 text-xs-center">
                <div class="form-inline m-b-10">
                    <div class="form-group col-6">
                        <input type="text" id="txt_filter_channel_name"  class="form-control" placeholder="Channel name" value="{{$request->channel_name}}">
                    </div>
                    <div class="form-group col-6">
                    <select id="cbb_filter_status"  class="form-control input-sm ">
                        <option value="-1" <?php echo ($request->status == '-1') ? 'selected' : ''; ?>>--Select--</option>
                        <option value="1" <?php echo ($request->status == '1') ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo ($request->status == '0') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                    </div>
                </div>
                <div class="form-inline ">
                    <div class="form-group col-6">
                        <select id="cbb_filter_sub" class="form-control input-sm ">
                            <option value=">=" <?php echo ($request->cbb_sub == '>=') ? 'selected' : ''; ?>>&gt;=</option>
                            <option value="<=" <?php echo ($request->cbb_sub == '<=') ? 'selected' : ''; ?>>&lt;=</option>
                        </select>
                        <input type="number" id="txt_filter_sub"  class="form-control m-l-5" style="width: 111px;" value="{{$request->txt_sub}}" >
                        <label class="control-label m-l-5"> subs</label>
                    </div>  
                    <div class="form-group col-6">
                        <select id="cbb_filter_view" class="form-control input-sm ">
                            <option value=">=" <?php echo ($request->cbb_view == '>=') ? 'selected' : ''; ?>>&gt;=</option>
                            <option value="<=" <?php echo ($request->cbb_view == '<=') ? 'selected' : ''; ?>>&lt;=</option>
                        </select>
                        <input type="number" id="txt_filter_view"  class="form-control m-l-5" style="width: 111px;" value="{{$request->txt_view}}">
                        <label class="control-label m-l-5"> views</label>
                    </div>  

                </div>
            </div>
            <div class="col-12 m-t-20">
                <!--<a id='applyFilter' style="cursor: pointer;color: #000">APPLY</a>-->
                <button id="applyFilter" type="button" class="btn btn-outline-primary ">Apply</button>
            </div>
        </div>
        
    </div>
</div>
