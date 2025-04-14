<div class="modal fade" id="dialog_import_campaign_rev" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="display: block">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <div class="row">
                    <div class="col-md-9">
                        <h5 class="modal-title"><span class="dialog-icon"><i class="ti-import"></i></span> <span id="dialog_import_campaign_rev_title">Import Report</span></h5>
                    </div>

                    <div class="col-md-3 pull-right">
                    </div>
                </div>
            </div>
            <br>

            <div class="modal-body">
                <form id="form-import-report">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-12">
                            <span class="color-red">Note</span>
                            <span class="color-red font-13" >
                                <ol>
                                    <li>Indiy: File .xlsx have to convert period -> shortdate before convert to .csv</li>
                                    <li>Indiy: File from Chris get 70% of column After Commission (index=14)</li>
                                    <li>Indiy: File from Sang get 100% of column After Commission (index=15)</li>
                                    <li>Indiy: Include Rate by structue: YYYYMM=Rate (remember add more month)</li>
                                    <li>Into The Upbeat -> Indiy,Sun City Vibe -> 51st_State</li>
                                    <li>51st_State sử dụng đơn vị tiền tệ GBP</li>
                                </ol>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 div_import_file_period">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Period</label>
                                <div class="col-12">
                                    <select id="import_period" name="period"
                                            class="select2_multiple form-control">
                                        {!!$monthSelect!!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Distributor</label>
                                <div class="col-12">
                                    <select id="import_distributor" name="distributor" data-show="file" class="select2_multiple form-control import_distributor">
                                        {!!$listDistributor!!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 div_import_file disp-none">
                            <div class="form-group row">
                                    <label class="col-12 col-form-label">Report File </label>
                                        <div class="row col-md-12">
                                            <div class="col-md-12">
                                                <input id="report_file" type="file" name="report_file" class="form-control" accept=".csv" style="line-height: 1.25;content: Button"> 
                                            </div>
                                        </div>
                            </div>

                        </div>
                        <div class="col-md-2 div_import_file_rate disp-none">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Rate (AUD &LongRightArrow; USD)</label>
                                    <div class="row col-md-12">
                                        <!--<input id="report_rate" type="text" name="report_rate" class="form-control" value="0.68445">--> 
                                        <textarea id="report_rate" name="report_rate" class="form-control" row="5" style="line-height: 1.25;height: 110px"></textarea>
                                    </div>
                            </div>
                        </div>
                        <div class="col-md-2 div_import_file_owner">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Owner</label>
                                <div class="col-12">
                                    <select id="owner" name="owner" class="select2_multiple form-control import_owner">
                                        <option value="hoa">Hoa</option>
                                        <option value="james">James</option>
                                        <option value="sang">Sang</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>                                    
                    <div class="row div_import_text">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Data <i>(Artist SongName Revenue)</i></label>
                                <div class="col-12">
                                    <textarea class="form-control" rows="5" id="report_data" name="report"
                                              spellcheck="false"
                                              style="line-height: 1.25;height: 200px"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-outline-info btn-sm btn-check-report"><i class="fa fa-check"></i> Check</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div id="import_campaign_rev_loading" class="disp-none" style="text-align: center;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
                <div id="import-report-check-result">

                </div>
            </div>

        </div>
    </div>
</div>