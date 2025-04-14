<div class="modal fade" id="dialog_realtime_view" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-90">
        <div class="modal-content">
            <div id="dialog_realtime_view_loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="dialog_realtime_view_content" class="modal-body">
                <div class="row">
                    <div class="col-md-12 m-b-15">
<!--                        <table id="tbl-channel-info" class="table" style="box-shadow: 0 4px 6px -3px #cbbcbc">
                            
                        </table>-->
                        
                        <div class="channel-header" style="border-radius: 8px; padding: 15px; background: #fff;">
                            <div id="channel-header-info" class="d-flex align-items-center justify-content-between flex-wrap">

                            </div>
                        </div>                        
                        
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div>
                            <span>Views · Last 48 hours</span>
                            <b><span id="last-48-hour"class="pull-right"></span></b>
                        </div>
                        <br>
                        <div id="chartHour-wrap" style="height: 250px">
                            
                        </div>
<!--                        <canvas id="chartHour"></canvas>-->
                    </div>
                    <div class="col-md-6">
                        <div>
                            <span>Views · Last 60 minutes</span>
                            <b><span id="last-60-minute"class="pull-right"></span></b>
                        </div>
                        <br>
                        <div id="chartMinute-wrap" style="height: 250px">
                            
                        </div>
                        <!--<canvas id="chartMinute"></canvas>-->
                    </div>
                    <div class="col-md-12">
                        <table id="table-chart" class="table">
                            <tr><th style="top:-20px">Content</th><th style="top:-20px">Published</th><th colspan="2" style="top:-20px;text-align: center">Last 48 hours</th><th colspan="2" style="top:-20px;text-align: center">Last 60 minutes</th></tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12"></div>

                </div>
            </div>
        </div>
    </div>
</div>