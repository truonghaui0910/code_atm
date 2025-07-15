<div class="modal fade" id="dialog_list_notify" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-80">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <strong>Notification</strong>
                        </h5>
                    </div>
                    <div >
                        <button onclick="loadNotify(0)"
                                class="btn btn-no-bg btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light m-r-10 "
                                onclick=""
                                data-toggle="tooltip" data-placement="top" title="Reload">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                 class="bi bi-arrow-clockwise m-t-1" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                      d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z" />
                                <path
                                    d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466" />
                            </svg>
                        </button>

                        <button
                            class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light"
                            data-dismiss="modal"
                            data-id="modal-add-task" data-toggle="tooltip" data-placement="top" title="Close"
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
            <div  class="modal-body" style="background: #F0F2F5">
                <div class="row">
                    <div class="col-xl-3 col-lg-4">
                        <div class="card-box">
                            <div class="p-20">
                            </div>
                            @if($is_admin_music)
                            <h4 class="font-18">360 Promo</h4>
                            <div class="list-group b-0 mail-list">
                                <a id="filter-group-campaign" onclick="changeGroup('campaign')" class="list-group-item b-0 cur-poiter mb-1"><span class="fa fa-circle text-info m-r-10"></span>360 Campaign</a>
                                <a id="filter-group-release" onclick="changeGroup('release')" class="list-group-item b-0 cur-poiter mb-1"><span class="fa fa-circle text-warning m-r-10"></span>360 Release</a>
                                <a id="filter-group-invoice" onclick="changeGroup('invoice')" class="list-group-item b-0 cur-poiter mb-1"><span class="fa fa-circle text-purple m-r-10"></span>360 Invoice</a>
                            </div>
                            @endif
                            <h4 class="font-18">AutoMusic</h4>
                            <div class="list-group b-0 mail-list">
                                <a id="filter-group-start_campaign" onclick="changeGroup('start_campaign')" class="list-group-item b-0 cur-poiter mb-1"><span class="fa fa-circle text-primary m-r-10"></span>Campaign Task</a>
                                <a id="filter-group-lyric_timestamp" onclick="changeGroup('lyric_timestamp')" class="list-group-item b-0 cur-poiter mb-1"><span class="fa fa-circle text-success m-r-10"></span>Lyric Timestamp</a>
                                <a id="filter-group-lyric_video" onclick="changeGroup('lyric_video')" class="list-group-item b-0 cur-poiter mb-1"><span class="fa fa-circle text-pink m-r-10"></span>Lyric Video</a>
                                <a id="filter-group-promo_mix" onclick="changeGroup('promo_mix')" class="list-group-item b-0 cur-poiter mb-1"><span class="fa fa-circle text-danger m-r-10"></span>Promo Mix</a>
                                <a id="filter-group-channel" onclick="changeGroup('channel')" class="list-group-item b-0 cur-poiter mb-1"><span class="fa fa-circle text-warning m-r-10"></span>Channel</a>
                            </div>
                            <h4 class="font-18">Moonseo</h4>
                            <div class="list-group b-0 mail-list">
                                <a id="filter-group-crosspost" onclick="changeGroup('crosspost')" class="list-group-item b-0 cur-poiter mb-1"><span class="fa fa-circle m-r-10"></span>Crosspost</a>
                            </div>
                            <h4 class="font-18">Status</h4>
                            <div class="list-group b-0 mail-list">

                                <a id="filter-status-0" onclick="changeStatus('0')" class="list-group-item b-0 cur-poiter mb-1"><span class="badge badge-danger ">New</span></a>
                                <a id="filter-status-1" onclick="changeStatus('1')" class="list-group-item b-0 cur-poiter mb-1"><span class="badge badge-primary">Read</span></a>
                                <a id="filter-status-2" onclick="changeStatus('2')" class="list-group-item b-0 cur-poiter mb-1"><span class="badge badge-warning ">Processing</span></a>
                                <a id="filter-status-3" onclick="changeStatus('3')" class="list-group-item b-0 cur-poiter mb-1"><span class="badge badge-success">Done</span></a>
                            </div> 
                        </div>

                    </div>

                    <div class="col-xl-9 col-lg-8">
                        <div class="card-box p-5">
                            <div class="panel-body p-0">

                                <div id="notify_loading" class="" style="text-align: center; display: none;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
                                @if($is_admin_music)
                                <button id="btn-read-all" type="button" class="btn btn-sm btn-outline-danger mr-3 float-right m-b-5" >
                                    Read all
                                </button>
                                @endif
                                <div id="table-noti-data" class="table-responsive">
                                    <table class="table table-hover mails m-0">
                                        <tbody>
                                            <tr class="unread">
                                                <td class="mail-select">
                                                    <i class="fa fa-circle m-l-5 text-warning"></i>
                                                </td>
                                                <td>
                                                    <a onclick="" class="email-name">Google Inc</a>
                                                </td>
                                                <td class="hidden-xs">
                                                    <a onclick="" class="email-msg">content</a>
                                                </td>
                                                <td class="text-right">time</td>
                                                <td><span class="badge badge-danger">Processing</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div> 
                        </div>
                    </div> 

                </div>

            </div>
        </div>
    </div>
</div>
