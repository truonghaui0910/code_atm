<div class="modal fade" id="dialog_import_mooncoin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <span id="created_by_avatar"></span>
                            <strong id="dialog-task-title">Add Mooncoin</strong>
                        </h5>
                    </div>
                    <div class="">

                        <button
                            class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light btn-close-modal"
                            data-id="modal-add-task" data-toggle="tooltip" data-placement="top" title="Close" data-dismiss="modal"
                            style="padding-bottom: 0.6rem;padding-top: 0.6rem;z-index: 1001">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                 class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                <path
                                    d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div class="modal-body">
                <form id="formMoonCoin" name="formMoonCoin" method="POST" class="form-horizontal" novalidate="">
                    <input type="hidden" name='_token' value='{{csrf_token()}}'/>
                    <!--<input type="hidden" name="bom_id" id="bom_id" value=""/>-->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Year</label>
                                <div class="col-12">
                                    <select id="moon_year" name="moon_year" class="select2_multiple form-control select_date" >
                                        
                                    </select>  
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Month</label>
                                <div class="col-12">
                                    <select id="moon_month" name="moon_month" class="select2_multiple form-control select_date" >
                                        
                                    </select>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">User</label>
                                <div class="col-12">
                                    <select id="user" name="user" class="select2_multiple form-control search_select "        
                                            data-show-subtext="true" 
                                            data-live-search="true"
                                            data-size="5" data-container="body">
                                        {!!$users!!}
                                    </select>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-t-10">

                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Description <span class="color-red">*</span> <span onclick="showMooncoinDesc()"><i class="fa fa-plus-circle color-red"
                                                                                                                                                       style="font-size: 20px;"></i></span></label>
                                <div class="col-12">
                                    <select id="moon_desc" name="moon_desc" class="moon_desc select2_multiple form-control search_select" 
                                            data-show-subtext="true" 
                                            data-live-search="true"
                                            data-size="5" data-container="body"
                                            multiple="">
                                    </select>  
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-md-12">
                            <button data-toggle="tooltip" data-placement="top" data-original-title="" data-close="true" type="button" class="btn float-right m-l-10" onclick="saveMooncoin(this)"><i class="fa fa-save"></i> Save & Close</button>
                            <button data-toggle="tooltip" data-placement="top" data-original-title="" data-close="false" type="button" class="btn float-right" onclick="saveMooncoin(this)"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>