<!--<div class="modal fade" id="modal_multi_chart_realtime" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-90">
        <div class="modal-content">
            <div id="modal_multi_chart_realtime_loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="modal_multi_chart_realtime_content" class="modal-body">
                <div class="row">
                    <div class="col-md-12 m-b-15">                        
                        <div class="channel-header" style="border-radius: 8px; padding: 15px; background: #fff;">
                            <div id="channel-header-info-multi" class="d-flex align-items-center justify-content-between flex-wrap">

                            </div>
                        </div>                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div>
                            <span>Views · Last 48 hours</span>
                            <b><span id="last-48-hour-"class="pull-right"></span></b>
                        </div>
                        <br>
                        <div id="chartHour-wrap" style="height: 250px">
                            
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <span>Views · Last 60 minutes</span>
                            <b><span id="last-60-minute"class="pull-right"></span></b>
                        </div>
                        <br>
                        <div id="chartMinute-wrap" style="height: 250px">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>-->

<div class="modal fade" id="modal_multi_chart_realtime" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-90" role="document">
        <div class="modal-content">            
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <span class="mr-2">Channel Analytics</span> <span class="badge badge-info mb-0">0/0 channels loaded</span>
                        </h5>
                    </div>
                    <div>
                        <button class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light btn-close-modal" data-id="modal-add-task" data-toggle="tooltip" data-placement="top" title="" data-dismiss="modal" style="    padding: 0.5rem 0.7rem 0.5rem 0.7rem;z-index: 1001;border-radius: 50%;line-height: 1" data-original-title="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>            
            <div class="modal-body">
                <div id="modal_multi_chart_realtime_loading" class="text-center my-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 w-100">Loading channel data...</p>
                </div>
                <div id="charts-container">
                    <!-- SSE sẽ thêm dữ liệu vào đây -->
                </div>
            </div>
<!--            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>-->
        </div>
    </div>
</div>