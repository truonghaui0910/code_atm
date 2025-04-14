<div class="modal fade" id="dialog_view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-80">
        <div class="modal-content">
            <div class="modal-header" style="display: block">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <div class="row">
                    <div class="col-md-9">
                        <h5 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-line-chart fa-fw"></i></span> <span id="dialog-view-tile"></span></h5>

                    </div>
<!--                    <div class="col-md-3">
                        <select id="cbbUserName" class="form-control search_select" data-show-subtext="true" data-live-search="true">
                            {!!$listUser!!}
                        </select>
                    </div>-->
                    <div class="col-md-3 pull-right">

                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" class="form-control has-feedback-left" id="date_rage_picker" value="Last 30 days">
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
            <div id="dialog-view-loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-view-dialog" class="modal-body">

            </div>
        </div>
    </div>
</div>