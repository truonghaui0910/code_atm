<div class="modal fade" id="dialog_report_campaign_rev" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-80">
        <div class="modal-content">
            <div class="modal-header" style="display: block">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <div class="row">
                    <div class="col-md-9">
                        <h5 class="modal-title"><span class="dialog-icon"><i class="fa fa-usd fa-fw"></i></span> <span id="dialog_report_campaign_rev_title"></span></h5>

                    </div>

                    <div class="col-md-3 pull-right">

                        <div class="input-group">
<!--                            <select id="period_rev" class="form-control" name="period_rev" >
                                    {//!!$month_select!!}
                            </select>-->
                        </div>
                        <!--<span class="fa fa-calendar form-control-feedback left" aria-hidden="true" style="color: #000"></span>-->
                        <input type="hidden" id="userName" value="-1"/>
                        <input type="hidden" id="startDate"/>
                        <input type="hidden" id="endDate"/>
                        <input type="hidden" id="dialogType"/>

                    </div>
                </div>
            </div>
            <br>
            <div id="report_campaign_rev_loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="report_campaign_rev_content" class="modal-body">

            </div>
            <div class="row">
                <div id="report_user_rev_content_div" class="col-md-4">
                <div id="report_user_rev_content"></div>
                    
                </div>
                <div id="report_user_rev_content_detail_div" class="col-md-8">
            <div id="report_user_rev_content_detail"></div>
                    
                </div>
                
            </div>
        </div>
    </div>
</div>