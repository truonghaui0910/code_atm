<div class="modal fade" id="dialog" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button id="btnCloseDialogGroupChannel" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-user fa-fw"></i> Add Group Channel</h4>
            </div>
            <br>
            <center>
                <div style='width: 90%;text-align: left' id="messageValidate"></div>
            </center>
            <div class="modal-body">
                <div id="datatable_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                    <div class="row">
                        <div class="col-sm-12">
                            <div style="min-height: 400px;">
                                <table id="data-table-group-channel" class="table table-bordered " style="width: 100%">  
                                    <thead class="thead-default">
                                        <tr>  
                                            <th style="width: 20px;">ID</th>  
                                            <th>Group Channel</th>  
                                            <th style="text-align: center;width: 20px">Function</th>  
                                        </tr>  
                                    </thead>  
                                </table> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div style="display: contents;width: 100%;">
                    <span>Group Channel</span>
                    <input id="group_name_add" type="text" class="form-control m-r-10" placeholder="" aria-controls="data-table-group-channel">
                    <button type="button" class="btn btn-primary btn-loadding btn-save-group-channel"
                            data-loading-text="<i class='fa fa-spinner fa-spin '></i> {{ trans('label.button.loadding') }}">
                        {{ trans('label.button.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>