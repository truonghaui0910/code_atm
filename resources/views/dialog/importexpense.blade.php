<div class="modal fade" id="dialog_import_expense" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>-->
                <!--<h5 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-external-link fa-fw"></i></span> <span id="title-brand">Add New Expense</span></h5>-->                        
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <strong id="dialog-task-title">Add New Expense</strong>
                        </h5>
                    </div>
                    <div class="">
                        <button
                            class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light"
                            data-toggle="tooltip" data-placement="top" title="Close" data-dismiss="modal"
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
            <div class="modal-body modal-scroll">

                <form id="frmAddExpense" method="POST">
                    {{ csrf_field() }}
                    <input id="ms_id" type="hidden" name="ms_id"/>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Period</label>
                                <div class="col-12">
                                    <select id="period" name="period" class="select2_multiple form-control">
                                        {!!$month_select!!}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row radio-dash-wrap">
                        <div class="col-md-2 radio-dash mr-2">
                            <div class="radio form-check-inline">
                                <input type="radio" id="payment_type_2" class="radio-payment-type" value="out" name="payment_type" checked="">
                                    <label for="payment_type_2"> Expense </label>
                            </div>
                        </div>
                        <div class="col-md-2 radio-dash">
                            <div class="radio form-check-inline">
                                <input type="radio" id="payment_type_1" class="radio-payment-type" value="in" name="payment_type" >
                                    <label for="payment_type_1"> Revenue </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 div_radio-payment-type_out">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Vendor</label>
                                <div class="col-12">
                                    <select id="vendor_out" name="vendor_out" 
                                            class="select2_multiple form-control search_select custom-color" 
                                            data-show-subtext="true"
                                            data-live-search="true">
                                        {!!$vendorOut!!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 div_radio-payment-type_in disp-none">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Vendor</label>
                                <div class="col-12">
                                    <select id="vendor_in" name="vendor_in" 
                                            class="select2_multiple form-control search_select custom-color" 
                                            data-show-subtext="true"
                                            data-live-search="true">
                                        {!!$vendorIn!!}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row radio-dash-wrap">

                        <div class="col-md-2 radio-dash mr-2">
                            <div class="radio form-check-inline">
                                <input type="radio" id="currency" value="usd" name="currency" checked="" class="radio_currency">
                                    <label for="currency"> USD </label>
                            </div>
                        </div>
                        <div class="col-md-2 radio-dash">
                            <div class="radio form-check-inline">
                                <input type="radio" id="currency2" value="vnd" name="currency" class="radio_currency">
                                    <label for="currency2"> VND </label>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Amount</label>
                                <div class="col-12">
                                    <input id="amount" type="number" name="amount" class="form-control" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 div_radio_currency_vnd disp-none">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Rate</label>
                                <div class="col-12">
                                    <input id="rate" type="number" name="rate" class="form-control" value="23000">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Note</label>
                                <div class="col-12">
                                    <input id="money_note" type="text" name="money_note" class="form-control" placeholder="Some text for reason">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-outline-info color-g btn-sm btn-submit-expense" onclick="submitExpense()"><i
                                        class="fa fa-save"></i> Save</button>
                            </div>
                        </div>
                    </div>
                </form>
                <hr><br>
                        <div id="ms_report_table"></div>
                        <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div style="min-height: 400px;">
                                <table id="data-table-expense" class="table text-center" style="border-collapse: inherit;table-layout: fixed;">
                                <!--<table id="data-table-expense" class="table table-bordered " style="width: 100%">-->  
                                    <thead class="thead-default">
                                        <tr>  
                                            <th class="text-center" style="width: 5%">#</th>  
                                            <th class="text-center" style="width: 15%">Period</th>  
                                            <th class="text-center" style="width: 15%">Username</th>  
                                            <th class="text-center" style="width: 15%">Created</th>  
                                            <th class="text-center" style="width: 10%">Money In</th>  
                                            <th class="text-center" style="width: 10%">Money Out</th>  
                                            <th class="text-center" style="width: 15%">Vendor</th>  
                                            <th class="text-center" style="width: 20%">Note</th>  
                                            <th style="text-align: right;width: 12%" >Function</th>  
                                        </tr>  
                                    </thead>  
                                </table> 
                            </div>
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>