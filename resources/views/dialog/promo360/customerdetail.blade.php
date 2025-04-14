<div class="modal fade" id="dialog_customer_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-80">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <strong id="dialog_customer_detail_title">Create Invoice</strong>
                        </h5>
                    </div>
                    <div>
                        <button
                            class="btn btn-no-bg btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light m-r-10 "
                            onclick="loadCustomer()" data-toggle="tooltip" data-placement="top" title="Reload">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-arrow-clockwise m-t-1" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z" />
                                <path
                                    d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466" />
                            </svg>
                        </button>

                        <button class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light"
                            data-dismiss="modal" data-id="modal-add-task" data-toggle="tooltip" data-placement="top"
                            title="Close" style="padding-bottom: 0.6rem;padding-top: 0.6rem;z-index: 1001">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                <path
                                    d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div id="dialog-loading" class="disp-none" style="text-align: center;"><i
                    class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
            <div id="content-dialog" class="modal-body div_scroll_50">
                <form id="formCommon">
                    {{ csrf_field() }}
                    <input type="hidden" id="curr_email" name="curr_email" />

                </form>
                <div class="row m-b-20">
                    <div class="col-md-3">
                        <div class="widget-bg-color-icon card-custom widget-user">
                            <div class="text-center">
                                <h3 id="curr_balance" class="text-dark m-t-10 font-bold"></h3>
                                <h6 class="text-muted mb-0">Balance</h6>
                                <span id="" class="text-right text-muted font-12 font-italic">Unused budget
                                    balance</span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget-bg-color-icon card-custom widget-user">
                            <div class="text-center">
                                <h3 id="curr_campaign" class="text-dark m-t-10 font-bold"></h3>
                                <h6 class="text-muted mb-0">Limit Campaign</h6>
                                <span id="" class="text-right text-muted font-12 font-italic">Agreed campaign
                                    quantity</span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget-bg-color-icon card-custom widget-user">
                            <div class="text-center">
                                <h3 id="curr_debit_total" class="m-t-10 color-red font-bold"></h3>
                                <h6 class="text-muted mb-0">Outstanding Amount</h6>
                                <span id="" class="text-right text-muted font-12 font-italic">Total
                                    debt that the customer needs to pay</span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget-bg-color-icon card-custom widget-user">
                            <div class="text-center">
                                <h3 id="amount_spent" class="m-t-10 color-green font-bold"></h3>
                                <h6 class="text-muted mb-0">Total Payments</h6>
                                <span id="" class="text-right text-muted font-12 font-italic">The
                                    total amount paid for our services</span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 d-none d-sm-flex flex-column">
                        <fieldset class="fieldset-custom m-b-10" style="padding: 15px">
                            <legend class="legend-custom">Add Budget</legend>
                            <form id="formBudget" method="POST" class=" mx-auto form-style">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Artist 
                                            <span type="button" class="btn btn-outline-info btn-sm " onclick="loginEmail('artist')"><i class="fa fa-user"></i> View 360 Account</span>
                                            
                                            </label> 
                                            <select id="artist" name="artist" data-show-subtext="true"
                                                data-live-search="true" data-size="5" data-container="body"
                                                class="select2_multiple form-control form-control-sm search_select">

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Amount ($)</label>
                                            <div class="col-12">
                                                <input id="budget_amount" type="number" name="budget_amount"
                                                    class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Number Of Campaigns</label>
                                            <div class="col-12">
                                                <input id="campaign_number" type="number" name="campaign_number"
                                                    class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-4 col-form-label">Description</label>
                                            <div class="col-12">
                                                <textarea id="budget_desc" type="text" name="budget_desc" style="line-height: 1.25;height: 110px;padding: 10px;" class="form-control form-control-sm"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-outline-info m-t-10 btn-add-budget"
                                                style="border-radius: 0.475rem" onclick="addBudget()"><i
                                                    class="fa fa-usd"></i> Add Budget</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </fieldset>
                        <hr>
                        <fieldset class="fieldset-custom m-b-10 m-t-10 p-15">
                            <legend class="legend-custom">Invoice</legend>
                            <form id="formInvoice" method="POST" class="mx-auto form-style">
                                <input type="hidden" name="invoice_id" id="invoice_id"/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Artist</label>
                                            <select id="invoice_artist" name="invoice_artist"
                                                data-show-subtext="true" data-live-search="true" data-size="5"
                                                data-container="body"
                                                class="select2_multiple form-control form-control-sm search_select">

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Payment Method</label>
                                            <div class="col-12">
                                                <select id="payment_method" name="payment_method"
                                                    class="select2_multiple form-control form-control-sm payment_method">
                                                    {!! $method !!}

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Amount ($)</label>
                                            <div class="col-12">
                                                <input id="invoice_amount" type="number" name="invoice_amount"
                                                    class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">P.O Number</label>
                                            <div class="col-12">
                                                <input id="po_number" type="text" name="po_number"
                                                    class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Artist/Company Name</label>
                                            <div class="col-12">
                                                <input id="company_name" type="text" name="company_name"
                                                    class="form-control form-control-sm" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Address</label>
                                            <div class="col-12">
                                                <input id="customer_address" type="text" name="customer_address"
                                                    class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Contact Name</label>
                                            <div class="col-12">
                                                <input id="customer_name" type="text" name="customer_name"
                                                    class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Contact Email</label>
                                            <div class="col-12">
                                                <input id="customer_email" type="text" name="customer_email"
                                                    class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="checkbox-radius checkbox-circle">
                                            <input id="is_save_info"  name="is_save_info" class="chk_status_pay" type="checkbox" value="1">
                                            <label for="is_save_info" class="m-b-18 m-l-15">Save info for next invoice</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-4 col-form-label">Description</label>
                                            <div class="col-12">
                                                <textarea id="invoice_content" type="text" name="invoice_content"
                                                    style="line-height: 1.25;height: 160px;padding: 10px;" class="form-control form-control-sm"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div id="create_invoice_result" class="col-md-12">

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="button"
                                                class="btn btn-outline-info m-t-10 btn-submit-invoice"
                                                style="border-radius: 0.475rem" onclick="submitInvoice()"><i
                                                    class="fa fa-plus"></i> Create Invoice</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset class="fieldset-custom m-b-10 p-15">
                            <legend class="legend-custom">Campaign</legend>
                            <form id="formCampaign" method="POST" class=" mx-auto form-style">
                                {{ csrf_field() }}
                                <input type="hidden" id="invoice_email" name="invoice_email" />
                                <!--<input type="hidden" id="campaign_debit" name="campaign_debit"/>-->
                                <textarea class="mail_content disp-none"></textarea>
                                <div class="row radio-dash-wrap mb-7">

                                    <div class="col-md-3 radio-dash mr-2">
                                        <div class="radio form-check-inline">
                                            <input type="radio" id="cam_type_pro" value="promo"
                                                name="campaign_type" checked="" class="radio_campaign_type">
                                            <label for="cam_type_pro"> PROMO </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 radio-dash">
                                        <div class="radio form-check-inline">
                                            <input type="radio" id="cam_type_sub" value="sub"
                                                name="campaign_type" class="radio_campaign_type">
                                            <label for="cam_type_sub"> SUBMISSION </label>
                                        </div>

                                    </div>
                                </div>
                                <div class="div_radio_campaign_type_sub disp-none">
                                    <input type="hidden" id="channel_id" name="channel_id">
                                    <label class="col-12 col-form-label">Submission Channel </label>
                                    <input type="text" id="filterInput" class="form-control mb-3"
                                        placeholder="Filter by channel name...">
                                    <div id="selectedChannels" class="grid mb-7 d-flex flex-wrap gap-5px"></div>
                                    <div id="channel_list">
                                        <div class="grid mb-7 d-flex flex-wrap gap-5px "
                                            style="height: 320px;overflow-y: scroll">
                                            @foreach ($channelsData as $main)
                                                <div class="w-155px h-155px btn-outline-dashed p-0 btn-channel-id position-relative"
                                                    data-channel="{{ $main->chanel_id }}">
                                                    <img class="rounded-circle w-80px h-80px m-t-5"
                                                        src="{{ $main->channel_clickup }}">
                                                    <div><strong>{{ $main->chanel_name }}</strong></div>
                                                    <div class="text-muted font-13">
                                                        {{ App\Common\Utils::number2ShortNumber($main->subscriber_count) }}
                                                        subscribers</div>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                                <hr>

                                <div class="div_invoice_type mb-7" show-id="2">
                                    <input type="hidden" id="campaign_id" name="campaign_id">
     
                                    <label class="col-12 col-form-label">Campaign list <i id="campaing_list_loading"
                                            class="fa fa-circle-o-notch fa-spin"></i></label>
                                    <div
                                        class="mh-200px border-dashed-primary p-l-15 p-r-15  campaign_list_content">
                                    </div>
                                </div>

                                <div class="col-md-12 div_promo_select div_package disp-none" show-id="1"
                                    show-id="2">
                                    <label class="col-12 col-form-label">Package</label>
                                    <div class="d-flex gap-15px justify-between mb-7">
                                        <input type="hidden" id="invoice_package">
                                        <div class="btn-package btn-outline-dashed btn-outline-dashed-active"
                                            pack-id="0" pack-cost="750" pack-music="50K" pack-official="50K">
                                            <strong>STARTER</strong><br>
                                            <span class="text-muted font-13"><strong>$750</strong></span><br>
                                            <span class="text-muted font-13"><strong>50K</strong> Music Streams
                                                Views</span><br>
                                            <span class="text-muted font-13"><strong>50K</strong> Official Video
                                                Views</span><br>
                                        </div>
                                        <div class="btn-package btn-outline-dashed" pack-id="1" pack-cost="1000"
                                            pack-music="100K" pack-official="80K">
                                            <strong>INDIE</strong><br>
                                            <span class="text-muted font-13"><strong>$1000</strong></span><br>
                                            <span class="text-muted font-13"><strong>100K</strong> Music Streams
                                                Views</span><br>
                                            <span class="text-muted font-13"><strong>80K</strong> Official Video
                                                Views</span><br>
                                        </div>
                                        <div class="btn-package btn-outline-dashed" pack-id="2" pack-cost="3000"
                                            pack-music="1M" pack-official="200K">
                                            <strong>MAJOR</strong><br>
                                            <span class="text-muted font-13"><strong>$3000</strong></span><br>
                                            <span class="text-muted font-13"><strong>1M</strong> Music Streams
                                                Views</span><br>
                                            <span class="text-muted font-13"><strong>200K</strong> Official Video
                                                Views</span><br>
                                        </div>
                                    </div>

                                </div>
                                <div class="row d-flex align-item-center">
                                    <div class="col-md-6 div_package">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Youtube Music Streams</label>
                                            <div class="col-12">
                                                <input id="invoice_youtube_music" type="text"
                                                    name="invoice_youtube_music" class="form-control form-control-sm"
                                                    value="50K" onkeypress="return validateInputTarget(event)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 div_promo_select div_package">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Official Video Views</label>
                                            <div class="col-12">
                                                <input id="invoice_official_video" type="text"
                                                    name="invoice_official_video" class="form-control form-control-sm"
                                                    value="50K" onkeypress="return validateInputTarget(event)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Total Campaign Amount ($)</label>
                                            <div class="col-12">
                                                <input id="campaign_amount" type="number" name="campaign_amount"
                                                    class="form-control form-control-sm" value="750">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3  remaining-budget">
                                        <div class="widget-bg-color-icon card-custom widget-user">
                                            <div class="text-center">
                                                <h4 id="remaining_amount" class="text-dark m-t-10 font-bold" >$0</h4>
                                                <!--<h6 class="text-muted mb-0">Remaining Amount</h6>-->
                                                <span id="" class="text-right text-muted font-12 font-italic">Remaining Amount</span>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3  remaining-budget">
                                        <div class="widget-bg-color-icon card-custom widget-user">
                                            <div class="text-center">
                                                <h4 id="remaining_campaign" class="text-dark m-t-10 font-bold" >0</h4>
                                                <!--<h6 class="text-muted mb-0">Remaining Campaign</h6>-->
                                                <span id="" class="text-right text-muted font-12 font-italic">Remaining Campaign</span>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-outline-info btn-submit-campaign"
                                                style="border-radius: 0.475rem" onclick="activeCampaign()"><i
                                                    class="fa fa-plus"></i> Active Campaign</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </fieldset>
                    </div>

                </div>
                <div class="row">
                    <div id="invoice_loading" class="mx-auto disp-none"><i class="fa fa-circle-o-notch fa-spin"></i>
                        Loading...</div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="fieldset-custom m-b-10 p-15">
                            <legend class="legend-custom">History</legend>
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs tabs-bordered"
                                        style="border-bottom: 1px solid rgba(128, 137, 142, 0.2) !important">
                                        <li class="nav-item">
                                            <a id="job-calendar" href="#job-calendar-data" data-toggle="tab"
                                                aria-expanded="false" class="nav-link job-tab-nav active">
                                                Budget
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a id="job-list" href="#job-list-data" data-toggle="tab"
                                                aria-expanded="true" class="nav-link job-tab-nav ">
                                                Invoice
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a id="job-statistics" href="#job-statistics-data" data-toggle="tab"
                                                aria-expanded="true" class="nav-link job-tab-nav">
                                                Campaign Transaction
                                            </a>
                                        </li>

                                    </ul>

                                    <div class="tab-content">
                                        <div class="job-tab-nav tab-pane fade show active" id="job-calendar-data">
                                            <div id="list_budget_table"></div>
                                        </div>
                                        <div class="job-tab-nav tab-pane fade " id="job-list-data">
                                            <div class="w-100  p-l-7 p-r-7">
<!--                                                <div class="w-full m-b-10 d-flex justify-content-end mw-80p mx-auto">
                                                    <div class="inline-flex rounded-md shadow-sm" role="group">
                                                        <button type="button" value="today"
                                                            class="btn-filter-list-job px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-primary focus:z-10  focus:text-primary ">
                                                            Today
                                                        </button>
                                                        <button type="button" value="week"
                                                            class="btn-filter-list-job px-4 py-2 text-sm font-medium text-gray-900 bg-white border-l-r-0 border-t-b-1 hover:bg-gray-100 hover:text-primary focus:z-10  focus:text-primary ">
                                                            Week
                                                        </button>
                                                        <button type="button" value="month"
                                                            class="btn-filter-list-job px-4 py-2 text-sm font-medium text-gray-900 bg-white border-l-r-0 border-t-b-1 border-l-1 hover:bg-gray-100 hover:text-primary focus:z-10  focus:text-primary ">
                                                            Month
                                                        </button>
                                                        <button type="button" value="all"
                                                            class="btn-filter-list-job px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-primary focus:z-10  focus:text-primary ">
                                                            All
                                                        </button>
                                                    </div>
                                                </div>-->
                                                <div id="list_invoice_table"></div>
                                            </div>
                                        </div>
                                        <div class="job-tab-nav tab-pane fade " id="job-statistics-data">
                                             <div id="list_campaign_trans_table"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


