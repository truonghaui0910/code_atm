@extends('layouts.master')

@section('content')
<style>
    .bg-gradient-primary {
        background: linear-gradient(45deg, #4e73df, #224abe);
    }
    .bg-gradient-success {
        background: linear-gradient(45deg, #1cc88a, #13855c);
    }
    .bg-gradient-info {
        background: linear-gradient(45deg, #36b9cc, #258391);
    }
    .bg-gradient-warning {
        background: linear-gradient(45deg, #f6c23e, #dda20a);
    }

    .email-card {
        background-color: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    .email-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
    .info-item {
        background-color: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        /*box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);*/
        border: 1px solid #ccc;
    }
    .progress {
        height: 10px;
        border-radius: 5px;
    }
    .tag {
        border-radius: 20px;
        padding: 5px 10px;
        font-size: 0.8em;
        font-weight: bold;
    }
    .tooltip-inner {
        max-width: 350px; /* Chiều rộng tối đa */
        /*width: 300px;      Chiều rộng cố định */
        white-space: normal; /* Cho phép xuống dòng */
    }
</style>
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">360Promo</span>
                    <!--<button data-toggle="tooltip" data-placement="top" data-original-title="Add New Bitly" type="button" class="btn btn-outline-info btn-import-bitly m-r-5"><i class="fa fa-plus"></i></button>-->
                    <!--<button data-toggle="tooltip" data-placement="top" data-original-title="$ Report" type="button" class="btn btn-outline-info btn-report-usd m-r-5"><i class="fa  fa-usd"></i></button>-->
                </h4>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                    <li class="breadcrumb-item active">360Promo</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>


    <input id="token" type="hidden" name='_token' value='{{ csrf_token() }}' />
    <input id="email_ref" type="hidden" value="{{ $request->email }}" />

    <!--<div class="container-fluid mt-4">-->
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="widget-simple-chart text-right card-box bg-gradient-primary text-white">
                    <div class="row">
                        <div class="col-4"><i class="fa fa-dollar mdi-size-dashboard "></i></div>
                        <div class="col-6">
                            <h5 class="card-title">Monthly Revenue</h5>
                            <h2 class="card-text">${{number_format($widgetInfo->revenue, 0, '.', ',')}}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="widget-simple-chart text-right card-box bg-gradient-success text-white">
                    <div class="row">
                        <div class="col-4"><i class="fa fa-bullhorn mdi-size-dashboard "></i></div>
                        <div class="col-6">
                            <h5 class="card-title">Active Campaigns</h5>
                            <h2 class="card-text">{{$widgetInfo->campaign}}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="widget-simple-chart text-right card-box bg-gradient-info text-white">
                    <div class="row">
                        <div class="col-4"><i class="fa fa-users mdi-size-dashboard "></i></div>
                        <div class="col-6">
                            <h5 class="card-title">New Customer</h5>
                            <h2 class="card-text">{{$widgetInfo->customer}}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="widget-simple-chart text-right card-box bg-gradient-warning text-white">
                    <div class="row">
                        <div class="col-4"><i class="fa fa-folder mdi-size-dashboard "></i></div>
                        <div class="col-6">
                            <h5 class="card-title">Unpaid</h5>
                            <h2 class="card-text">${{number_format($widgetInfo->debit, 0, '.', ',')}}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!--</div>-->    
        <div class="row">
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-chart-line mr-2"></i>Revenue by Month
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4" >
                <div class="card" style="min-height: 663px">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-chart-pie mr-2"></i>Top Debit
                    </div>
                    <div class="card-body div_scroll_50">
                        @if(!empty($budgetEmail))
                        <table class="table text-center" style="border-collapse: inherit;table-layout: fixed;"><thead><tr>
                            <th class='text-center' style='width:5%'>#</th>
                            <th class='text-left' style='width:20%'>Email</th>
                            <th class='text-center' style='width:10%'>Money</th>
                            <th class='text-center' style='width:10%'>Last Paid</th>
                            @foreach($budgetEmail as $index  => $tmp)
                                @if($tmp->outstanding_amount>0)
                                @php
                                    $i = $index +1;
                                @endphp
                               <tr class="">
                                    <td scope="row"><span class="cur-poiter" >{{$i }}</span></td>
                                    <td class="text-ellipsis text-left cur-poiter" onclick="dialogCustomerDetail(0,'{{ $tmp->email }}')">{{$tmp->email}}</td>
                                    <td class='text-center color-red font-bold'>${{number_format($tmp->outstanding_amount, 0, '.', ',')}}</td>
                                    <td class='text-center'>{{ App\Common\Utils::calcTimeText($tmp->last_payment_time) }}</td>
                               </tr> 
                               @endif
                            @endforeach
                        </table>
                        @else
                            No Data
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
<!--        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-trophy mr-2"></i>Top 5 Artists by Revenue
                    </div>
                    <div class="card-body">
                        <ul class="list-group" id="topArtists"></ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>Invoice Payment Rate
                    </div>
                    <div class="card-body">
                        <canvas id="invoiceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>-->
    <div class="row">
        <div class="col-lg-12">
            <div class="portlet">
                <div class="portlet-heading portlet-default">
                    <h3 class="portlet-title">
                        360Promo ({{ $sum }} records) <span data-toggle="tooltip" data-placement="top"
                            data-original-title="Add email manual">
                            <button type="button" class="color-g btn-circle btn btn-md " onclick="dialogEmail()"><i
                                    class="fa fa-plus"></i></button>
                        </span>
                    </h3>
                    <div class="portlet-widgets">
                        <a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
                        <span class="divider"></span>
                        <a data-toggle="collapse" data-parent="#accordion1" href="#bg-default"><i
                                class="ion-minus-round"></i></a>
                        <span class="divider"></span>
                        <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div id="bg-default" class="panel-collapse collapse show">
                    <div class="portlet-body">
                        <div style="overflow: auto">
                            <table id="tbl-360promo" class="table table-hover-tr" style="width: 99%;table-layout:fixed;">
                                <thead class="thead-default">
                                    <tr>
                                        <th class="disp-none">status_send</th>
                                        <th class="disp-none">date</th>
                                        <th class="disp-none">source</th>
                                        <th class="disp-none">debit</th>
                                        <th class="disp-none">create_invoice_status</th>
                                        <th style="text-align: center;width: 5%">Index</th>
                                        <th style="text-align: left;width: 15%">Email</th>
                                        <th style="text-align: left;width: 7%">Last Payment</th>
                                        <th style="text-align: center;width: 7%">Created Date</th>
                                        <th style="text-align: left;width: 5%">Link</th>
                                        <th style="text-align: center;width: 7%">Banlance</th>
                                        <th style="text-align: center;width: 7%">Debit</th>
                                        <th style="text-align: center;width: 7%">Used Campaign</th>
                                        <th style="text-align: center;width: 7%">Remain Campaign</th>
                                        <th style="text-align: right;width: 30%">Budget</th>
                                        <!--<th style="text-align: center;width: 5%">Sent</th>-->
                                        <!--<th style="text-align: right;width: 10%">Function</th>-->

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $data)
                                        <tr>
                                            <td class="disp-none">{{ $data->status_sent }}</td>
                                            <td class="disp-none">{{ $data->date_add }}</td>
                                            <td class="disp-none">{{ $data->source }}</td>
                                            <td class="disp-none">{{ $data->is_debit }}</td>
                                            <td class="disp-none">{{ $data->create_invoice_status }}</td>
                                            <td class="text-center">{{ $data->id }}</td>
                                            <td class="text-left">
                                                <a target="_blank"
                                                    href="mailto:{{ $data->email }}">{{ $data->email }}</a>
                                                <br>
                                                <br>
                                                <span data-toggle="tooltip" data-placement="top"
                                                    data-original-title="Create invoice">
                                                    <button type="button" class="color-g btn btn-circle btn-md"
                                                        onclick="dialogCustomerDetail({{ $data->id }},'{{ $data->email }}')"><i
                                                            class="fa fa-usd" style="width: 14px"></i></button>
                                                </span>
                                                <span data-toggle="tooltip" data-placement="top"
                                                    data-original-title="Get Info user">
                                                    <button id="user-{{ $data->id }}" type="button"
                                                        class="color-g btn btn-circle btn-md"
                                                        onclick="getInfo({{ $data->id }},'{{ $data->email }}')"><i
                                                            class="fa fa-user"></i></button>
                                                </span>
                                                <span data-toggle="tooltip" data-placement="top"
                                                    data-original-title="Reset pass">
                                                    <button id="pass-{{ $data->id }}" type="button"
                                                        class="color-g btn btn-circle btn-md"
                                                        onclick="resetPass({{ $data->id }},'{{ $data->email }}')"><i
                                                            class="fa fa-unlock-alt"></i></button>
                                                </span>
                                                <span data-toggle="tooltip" data-placement="top"
                                                    data-original-title="Remove">
                                                    <button id="email-{{ $data->id }}" type="button"
                                                        class="color-red btn btn-circle btn-md"
                                                        onclick="updatePromo({{ $data->id }}, 2, 0)"><i
                                                            class="ion-trash-a"></i></button>
                                                </span>
                                            </td>

                                            <td class="text-left text-ellipsis">
                                                {{ App\Common\Utils::calcTimeText($data->last_payment_time) }}
                                            </td>
                                            <td class="text-center">{!! $data->created !!}</td>
                                            <td class="text-left">
                                                <a href="{{ $data->link }}"><i class="fa fa-music"></i></a>
                                                <a href="{{ $data->instagram }}"><i class="fa fa-instagram"></i></a>
                                            </td>
                                            <td class="text-center">${{number_format($data->total_budget - $data->used_amount, 0, '.', ',')}}</td>
                                            <td class="text-center color-red font-bold">${{number_format($data->outstanding_amount, 0, '.', ',')}}</td>
                                            <td class="text-center">{{$data->used_campaigns}}</td>
                                            <td class="text-center">{{$data->remaining_campaigns}}</td>
                                            <td class="text-center">
<!--                                            <div class="col-12 mb-4">
                                                <div class="email-card p-4">-->

                                                    <div class="row">
                                                        <div class="col-md-10">
                                                            <!--<div class="info-item">-->
                                                                <!--<h5>Budget</h5>-->
                                                                <div class="d-flex justify-content-between mb-2">
                                                                    <!--<span>Total: ${{$data->total_budget}}</span>-->
                                                                    <span class="tag bg-primary text-white">Used: {{$data->usedPercentage}}%</span>
                                                                </div>
                                                                <div class="progress">
                                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{$data->usedPercentage}}%" aria-valuenow="{{$data->usedPercentage}}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            <!--</div>-->
                                                                                        <!--<div class="info-item">-->
                                                                <!--<h5>Payment</h5>-->
                                                                <div class="d-flex justify-content-between mb-2">
                                                                    <!--<span>Total: ${{$data->total_budget}}</span>-->
                                                                    <span class="tag bg-success text-white">Paid: {{$data->paidPercentage}}%</span>
                                                                </div>
                                                                <div class="progress">
                                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{$data->paidPercentage}}%" aria-valuenow="{{$data->paidPercentage}}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            <!--</div>-->
 
                                                        </div>
                          
                                                    </div>
<!--                                                </div>
                                            </div>-->
                                            </td>
<!--                                            <td class="text-center">
                                                <div class="checkbox checkbox-primary tbl-chk">
                                                    <input class="checkbox-multi ck-email" type="checkbox"
                                                        name="chkStatus[]" id="ck-email-{{ $data->id }}"
                                                        value="{{ $data->id }}"
                                                        {{ $data->status_sent == 1 ? 'checked' : '' }}>
                                                    <label class="m-b-18 p-l-0"
                                                        for="ck-email-{{ $data->id }}"></label>
                                                </div>
                                            </td>-->
                                            <!--                                    <td class="text-right">
                                                                    <span data-toggle="tooltip" data-placement="top" data-original-title="Get Info user">
                                                                        <button type="button" class="color-g btn btn-circle btn-md" onclick="getInfo('{{ $data->email }}')"><i class="fa fa-user"></i></button>
                                                                    </span>
                                                                    <span data-toggle="tooltip" data-placement="top" data-original-title="Create invoice">
                                                                        <button type="button" class="color-g btn btn-circle btn-md" onclick="dialogInvoice('{{ $data->email }}')"><i class="fa fa-usd" style="width: 14px"></i></button>
                                                                    </span>
                                                                    <span data-toggle="tooltip" data-placement="top" data-original-title="Reset pass">
                                                                        <button type="button" class="color-g btn btn-circle btn-md" onclick="resetPass('{{ $data->email }}')"><i class="fa fa-unlock-alt"></i></button>
                                                                    </span>
                                                                    <span data-toggle="tooltip" data-placement="top" data-original-title="Remove">
                                                                        <button id="email-{{ $data->id }}" type="button" class="color-red btn btn-circle btn-md" onclick="updatePromo({{ $data->id }},2,0)"><i class="ion-trash-a"></i></button>
                                                                    </span>
                                                                </td>-->

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dialog.promo360.customerdetail')
    @include('dialog.promo360.add_email_manual')
@endsection

@section('script')
    <script type="text/javascript">

        drawBarChartsGroup('revenueChart', @json($data_chart->label), @json($data_chart->datasets));    
        
        var currBudget = 0;
        var totalBudget = 0;
        var currDebitBudget = 0;
        var currLimitInvoice = 0;
        var listCampaign = [];
        var listArtist = [];
        var budgetArtist = [];
        var budgetEmail = [];
        var currId = 0;
        var currEmail = "";
        var domain = "https://dash.360promo.net";
//        var domain = "http://localhost:8004";

        window.onkeydown = function(e) {
            if (e.keyCode == 70 && e.ctrlKey) {
                e.preventDefault();
                $('input[type="search"]').focus();
            }
        }

        // Ẩn danh sách kênh khi tải trang
        $('#channel_list').hide();

        // Biến để lưu trữ các kênh đã chọn
        let selectedChannels = [];

        // Xử lý sự kiện khi gõ vào ô input filter
        $('#filterInput').on('input', function() {
            let filterText = $(this).val().toLowerCase();
            if (filterText) {
                $('#channel_list').show();
                $('#selectedChannels').hide();
                $('.btn-channel-id').each(function() {
                    let channelName = $(this).find('strong').text().toLowerCase();
                    if (channelName.includes(filterText)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            } else {
                $('#channel_list').hide();
                $('#selectedChannels').show();
            }
        });

        // Xử lý sự kiện khi chọn một kênh
        $('#channel_list').on('click', '.btn-channel-id', function() {
            let channelId = $(this).data('channel');
            let channelHtml = $(this).clone();

            if (!selectedChannels.includes(channelId)) {
                selectedChannels.push(channelId);
                updateChannelIdInput();
                addSelectedChannel(channelId, channelHtml);
                $(this).remove(); // Xóa khỏi channel_list
            }

            $('#channel_list').hide();
            $('#filterInput').val('');
            $('#selectedChannels').show();
        });

        // Hàm cập nhật input channel_id
        function updateChannelIdInput() {
            $('#channel_id').val(selectedChannels.join(','));
        }

        // Hàm thêm kênh đã chọn vào danh sách hiển thị
        function addSelectedChannel(channelId, channelHtml) {
            channelHtml.removeClass('btn-channel-id').addClass('selected-channel');
            let removeButton = $('<button>', {
                class: 'remove-btn',
                html: '&times;'
            }).click(function(e) {
                e.stopPropagation();
                removeSelectedChannel(channelId, channelHtml);
            });

            channelHtml.append(removeButton);
            $('#selectedChannels').append(channelHtml);
        }

        // Hàm xóa kênh khỏi danh sách đã chọn
        function removeSelectedChannel(channelId, channelHtml) {
            selectedChannels = selectedChannels.filter(id => id !== channelId);
            updateChannelIdInput();
            channelHtml.find('.remove-btn').remove();
            channelHtml.removeClass('selected-channel').addClass('btn-channel-id');
            $('#channel_list .grid').append(channelHtml); // Thêm lại vào channel_list
            $(`.selected-channel[data-channel="${channelId}"]`).remove();
        }

        function loginEmail(redirectUrl=null) {
            //            window.open('https://dash.360promo.net/login');
            var email = $("#email").val();
            var p = $("#password").val();
            var rediect ="";
            if(redirectUrl!=null){
                rediect = `&redirect=${redirectUrl}`;
            }
            let newWindow = open(`${domain}/login?u=${email}&p=${p}${rediect}`, 'login', 'width=1920,height=1080')
            newWindow.focus();
        }

        //2023/08/01 add email manual
        function dialogEmail() {
            $("#email").val("");
            $("#us_id").val("");
            $("#instagram").val("");
            $("#link_music").val("");
            $("#role").val(["PROMO"]);
            $(".div_password").hide();
            $('#dialog_add_email').modal({
                backdrop: false
            });
        }

        //2023/06/26 getinfo,reset pass
        function getInfo(id, email) {
            var $this = $("#user-" + id);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/360promo/getInfoUser",
                data: {
                    "us_id": id
                },
                dataType: 'json',
                success: function(data) {
                    logger('getInfo', data);
                    $this.html($this.data('original-text'));
                    var text =
                        `Email: ${data.email}\nPassword: ${data.pass_text}\nLink: https://dash.360promo.net/artist`;
                    navigator.clipboard.writeText(text);
                    $.Notification.notify('success', 'top right', '', 'Copied: ' + text);
                    $("#email").val(data.email);
                    $("#us_name").val(data.name);
                    $("#instagram").val(data.instagram);
                    $("#link_music").val(data.link);
                    $("#us_id").val(data.id);
                    $("#role").val(data.role);
                    $(".div_password").show();
                    $("#password").val(data.pass_text);
                    $("#dialog_add_email").modal("show");
                    $('#dialog_add_email').modal({
                        backdrop: false
                    });
                },
                error: function(data) {
                    logger('Error:', data);
                    $this.html($this.data('original-text'));
                }
            });
        }

        function submitEmail() {
            var $but = $(".btn-add-email");
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            if ($but.html() !== loadingText) {
                $but.data('original-text', $but.html());
                $but.html(loadingText);
            }
            var id = $("#us_id").val();
            var form = $("#formAddEmail").serialize();
            $.ajax({
                type: "POST",
                url: "/360promo/addEmail",
                data: form,
                dataType: 'json',
                success: function(data) {
                    $but.html($but.data('original-text'));
                    logger('submitEmail', data);
                    if (data.status == "success") {
                        $.Notification.notify(data.status, 'top center', '', 'Success');
                        if (id == "") {
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    } else {
                        $.Notification.notify(data.status, 'top center', '', data.message);
                    }



                },
                error: function(data) {
                    logger('Error:', data);
                    $but.html($but.data('original-text'));
                }
            });
        }

        function resetPass(id, email) {
            var $this = $("#pass-" + id);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/360promo/resetPass",
                data: {
                    "email": email
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    logger('resetPass', data);
                    var text = `Email: ${data.user.email}\nPassword: ${data.user.pass_text}`;
                    navigator.clipboard.writeText(text);
                    $.Notification.notify('success', 'top center', '', 'Copied: ' + text);
                },
                error: function(data) {
                    logger('Error:', data);
                    $this.html($this.data('original-text'));
                }
            });
        }

        function clear() {
            $("#cam_type_pro").prop("checked", true).change();
            $("#payment_method").val('360promo_hk').change();
            $("#campaign_id").val("").change();
            $("#limit_campaign").val("");
            $("#invoice_package").val(0).change();
            $("#invoice_youtube_music").val("50K");
            $("#invoice_youtube_music").val("50K");
//            $("#invoice_amount").val("750");
            $("#campaign_amount").val("750");
            $("#invoice_id").val(null);
        }
        
        function autloadCustomerDetail() {
            var refEmmail = $("#email_ref").val();
            if (refEmmail != "") {
                $('[type="search"]').val(refEmmail).change();
                currEmail = refEmmail;
                dialogCustomerDetail(0, refEmmail);
            }
        }

        function loadCampaign() {
            $(".campaign_list_content").html("");
            var html = "";
            $.each(listCampaign, function(key, value) {
         
                var pic = value.pic_album;
                if (pic == null) {
                    pic = value.pic_single;
                }
                if(pic == null){
                    pic ='images/360promo-192x192.png';
                }
                //show campaign chua dc ket noi và chưa được thanh toán
                if (value.status_pay == 0 || value.budget_trans_id == null) {
                   var startDate = "";
                   if(value.start_date!=null){
                    startDate = value.start_date;
                   }
                    var invoiceId = value.budget_trans_id == null ? 'No Transaction' :
                        `Trans# <strong>${value.budget_trans_id}</strong>`;
                    var active = value.status_pay == 1 ? '<span class="color-h">Active</span>' : '<span class="color-red">Inactive</span>';
                   
                    html += `<div class="d-flex flex-stack py-2 cur-poiter border-dashed-bt-ccc campaign_list" data-id="${value.cam_id}" data-artist-id="${value.artist_id}">
                        <div class="d-flex align-items-center">                            
                            <div >
                                <img class="rounded-circle w-80px h-80px" src="${pic}" onerror="this.onerror=null;this.src='images/360promo-192x192.png';">
                            </div>

                            <div class="ms-6">
                                <span class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">${value.artist} - ${value.song_name} | ${value.campaign_name}</span>
                                <div class="fw-semibold text-muted">${active}</div>
                            </div>
                        </div>
                        <div>
                            
                        </div>
                        <div class="">
                            ${invoiceId}
                        </div>
                    </div>`;
                }
            });
            $(".campaign_list_content").html(html);
            eventClickCampaignList();
        }

        function eventClickCampaignList() {
            $(".campaign_list").click(function() {
                $(".campaign_list").removeClass("campaign_list_active");
                $(this).addClass("campaign_list_active");
                var id = $(this).attr("data-id");
                $("#campaign_id").val(id).change();
                
                var selectedArtistId = $(this).attr("data-artist-id");
                var amount = 0;
                var campaign = 0;
                const selectedArtist = budgetArtist.find(artist => artist.artist_id == selectedArtistId);
                if (selectedArtist) {
                    amount = selectedArtist.remaining_amount;
                    campaign = selectedArtist.remaining_campaigns;
                    $("#remaining_amount").html(`$${amount}`);
                    $("#remaining_campaign").html(`${campaign}`);
                    $('.remaining-budget').show();
                }
          
                
            });
        }

        function autoCompleteInput() {

            $('.div_scroll').slimScroll({
                height: '230px',
                position: 'right',
                size: "5px",
                color: '#98a6ad',
                wheelStep: 30
            });

            $(".btn-package").click(function() {
                var value = $(this).attr("pack-id")
                $("#invoice_package").val(value).change();
            });
            
            $("#invoice_package").change(function() {
                var value = $(this).val();
                $(".btn-package").removeClass("btn-outline-dashed-active");
                $(`.btn-package[pack-id=${value}]`).addClass("btn-outline-dashed-active");
                var cost = $(`.btn-package[pack-id=${value}]`).attr("pack-cost");
                var packMusic = $(`.btn-package[pack-id=${value}]`).attr("pack-music");
                var packOfficial = $(`.btn-package[pack-id=${value}]`).attr("pack-official");
                $("#invoice_youtube_music").val(packMusic);
                $("#invoice_official_video").val(packOfficial);
                $("#campaign_amount").val(cost);
            });

            $(".radio_campaign_type").change(function() {
                $(this).each(function(i, obj) {
                    var value = $(this).attr('value');
                    if (value == 'sub') {
                        $('.div_promo_select').fadeOut('fast');
                    } else {
                        $('.div_promo_select').fadeIn('slow');
                    }
                });
            });
            
            $("#invoice_artist").change(function() {
                var selectedArtistId = $(this).val();
                var amount = 0;
                if (selectedArtistId != "") {
                    const selectedArtist = budgetArtist.find(artist => artist.artist_id == $(this).val());
                    if (selectedArtist) {
                        amount = selectedArtist.outstanding_amount;
                    }
                } else if (budgetEmail.length > 0) {
                    amount = budgetEmail[0].outstanding_amount
                }
                $("#invoice_amount").val(amount);

            });

        }

        //2023/06/27 datatable
        $('#tbl-360promo').DataTable({
            oSearch: {
                "sSearch": $("#email_ref").val()
            },
            //bỏ odd, even
            stripeClasses: [],
            searching: true,
            ordering: false,
            processing: false,
            select: false,
            responsive: false,
            paging: true,
            lengthMenu: [
                [20, 50, 100, 200, 1000, 5000],
                [20, 50, 100, 200, 1000, 5000]
            ],
            searchPanes: {
                //            viewTotal: true,
                viewCount: true,
                //            controls: false,
                collapse: true,
                //            initCollapsed: true,

                columns: [0, 1, 2, 3, 4]

            },
            dom: 'Plfrtip',
            columnDefs: [{
                    "targets": [0, 1, 2, 3, 4],
                    "visible": false,
                    "searchable": true
                },
                {
                    searchPanes: {
                        header: 'Type',
                        options: [{
                                label: 'Sent',
                                value: function(rowData, rowIdx) {
                                    return rowData[0] == 1;
                                }
                            },
                            {
                                label: 'Not sent yet',
                                value: function(rowData, rowIdx) {
                                    return rowData[0] == 0;
                                }
                            }
                        ]
                    },
                    targets: [0]
                },
                {
                    searchPanes: {
                        show: true,
                        header: 'Date'
                    },
                    targets: [1]
                },
                {
                    searchPanes: {
                        show: true,
                        header: 'Source'
                    },
                    targets: [2]
                },
                {
                    searchPanes: {
                        header: 'Debit',
                        options: [{
                                label: 'Unpaid',
                                value: function(rowData, rowIdx) {
                                    return rowData[3] == 1;
                                }
                            },
                            {
                                label: 'Paid',
                                value: function(rowData, rowIdx) {
                                    return rowData[3] == 0;
                                }
                            }
                        ]
                    },
                    targets: [3]
                },
                {
                    searchPanes: {
                        header: 'Invoice',
                        options: [{
                                label: 'Need Create Invoice',
                                value: function(rowData, rowIdx) {
                                    return rowData[4] == 1;
                                }
                            },
                            {
                                label: 'No Need',
                                value: function(rowData, rowIdx) {
                                    return rowData[4] == 0;
                                }
                            }
                        ]
                    },
                    targets: [4]
                },
            ]

        });
        //2023/06/12 update promo
        $(".ck-email").change(function() {

            var status = 0;
            if ($(this).is(":checked")) {
                status = 1;
            }
            updatePromo($(this).val(), 1, status);
        });

        function updatePromo(id, type, status_sent) {
            var $this = $("#email-" + id);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "360promoUpdate",
                data: {
                    "id": id,
                    "type": type,
                    "status_sent": status_sent
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    logger('updatePromo', data);
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    if (data.status == "success" && type == 2) {
                        $("#email-" + id).closest("tr").hide();
                    }

                },
                error: function(data) {
                    logger('Error:', data);
                    $this.html($this.data('original-text'));
                }
            });
        }

        function copy(selector) {
            var text = $(selector).text();
            navigator.clipboard.writeText(text);
            $.Notification.notify('success', 'top center', '', 'Copied email content ');
        }

        function copyText(text) {
            navigator.clipboard.writeText(text);
            $.Notification.notify('success', 'top center', '', 'Copied');
        }

        //2024/08/29 new function
        function dialogCustomerDetail(id, email) {
            $("#curr_email").val(email);
            $("#create_invoice_result").html("");
            $('#campaign_list').html("");
            $("#list_budget_table").html("");
            currId = id;
            currEmail = email;
            clear();
            loadCustomer();
            $("#dialog_customer_detail_title").html(email);
            $("#invoice_content").val(
                `360 Promo Campaign\nVideo Creation\nChannel/Ad Mgmt\nViews/Subs\nCurator Campaign\nPR/Radio`);
            $('#dialog_customer_detail').modal({
                backdrop: false
            });
        }        
        
        function loadCustomer() {
            $('.remaining-budget').hide();
            $("#campaing_list_loading").show();
            $('#artist').html("");
            $('#artist').selectpicker('render');
            //            $('#artist').selectpicker('destroy');
            $("#curr_balance,#curr_campaign,#curr_debit,#curr_debit_invoice,#curr_debit_total,#amount_spent").addClass(
                "skeleton-box").css({
                "width": "100%",
                "height": "30px"
            }).html("");
            $("#debit_time,#debit_time_invoice").addClass("skeleton-box").css({
                "width": "100%",
                "height": "16px"
            }).html("");
            selectedChannels = [];
            $('#channel_id').val(selectedChannels.join(','));
            $('#selectedChannels').empty();
            $.ajax({
                type: "GET",
                url: "/360promo2/loadCustomer",
                data: {
                    "id": currId,
                    "email": currEmail
                },
                success: function(data) {
                    logger('loadCustomer', data);
                    $('#artist').selectpicker('destroy');
                    $('#artist').html(data.artistOptionBudget);
                    $('#artist').selectpicker('render');
                    listArtist = data.artistOptionInvoice;
                    $('#invoice_artist').selectpicker('destroy');
                    $('#invoice_artist').html(
                        `<option  data-content="<img class='rounded-circle img-cover m-r-5 disp-inline h-40px w-40px mw-40px' src='images/default-avatar.png'> All Artist" value=''></option>${listArtist}`
                        );
                    $('#invoice_artist').selectpicker('render');
                    $("#invoice_loading").hide();
                    $("#po_number").val(data.po_number);
                    if (data.user.role.includes("SUBMISSION")) {
                        $("#cam_type_sub").prop("checked", true).change();
                    }
                    if (data.user.customer_info != null) {
                        var cusInfo = JSON.parse(data.user.customer_info);
                        $("#company_name").val(cusInfo.company_name);
                        $("#customer_address").val(cusInfo.customer_address);
                        $("#customer_name").val(cusInfo.customer_name);
                        $("#customer_email").val(cusInfo.customer_email);
                    }

                    genBudgetTable(data.budget.budget);
                    genCampaignTransTable(data.campaign_trans);
                    $('[data-toggle="tooltip"]').tooltip();
                    var option = "";
                    $('#campaign_list').html("");
                    listCampaign = data.campaigns;
                    loadCampaign();
                    $("#campaing_list_loading").hide();
                    budgetEmail = data.budget.budget_email;
                    budgetArtist = data.budget.budget_artist;
                    if (budgetEmail.length) {
                        currBudget = budgetEmail[0].remaining_amount;
                        currDebitBudget = budgetEmail[0].outstanding_amount;
                        currLimitInvoice = budgetEmail[0].remaining_campaigns;
                        totalBudget = budgetEmail[0].total_budget;
                    }
                        $("#curr_balance").removeClass("skeleton-box").css({
                            "width": "",
                            "height": ""
                        }).html("$" + currBudget);
                        $("#curr_campaign").removeClass("skeleton-box").css({
                            "width": "",
                            "height": ""
                        }).html(currLimitInvoice);
                        $("#curr_debit_total").removeClass("skeleton-box").css({
                            "width": "",
                            "height": ""
                        }).html("$" + currDebitBudget);
                        $("#amount_spent").removeClass("skeleton-box").css({
                            "width": "",
                            "height": ""
                        }).html("$" + totalBudget);
                        $("#invoice_amount").val(currDebitBudget);
                    loadInvoice();
                    $("#email").val(data.user.email);
                    $("#password").val(data.user.pass_text);

                },
                error: function(data) {
                    logger('Error:', data);
                }
            });
        }

        function genBudgetTable(budgets){
            var html = `<table class="table text-center" style="border-collapse: inherit;table-layout: fixed;"><thead><tr>
            <th class='text-center' style='width:5%'>#</th>
            <th class='text-left' style='width:10%'>Artist</th>
            <th class='text-center' style='width:5%'>Amount</th>
            <th class='text-center' style='width:5%'>Campaign</th>
            <th class='text-left' style='width:15%'>Description</th>
            <th class='text-center' style='width:10%'>Created time</th>
            <th class='text-center'style='width:10%'>Created by</th></tr>`;
            var i = 0;
            $.each(budgets, function(key, value) {
                i = i + 1;
                var evenOdd = "even";
                if (i % 2 == 0) {
                    evenOdd = "odd";
                }
                var createdTime = formatDateToGMT7(value.created);
                var desc = "";
                if(value.description!=null){
                    desc = value.description;
                }
                html +=
                    `<tr class="${evenOdd}">
            <td scope="row"><span class="cur-poiter" >${value.budget_id}</span></td>
            <td class="text-ellipsis text-left" data-toggle="tooltip" data-placement="top" data-original-title="${value.name}">${value.name}</td>
            <td class='text-center'>$${ number_format(value.total_amount, 0, '.', ',')}</td>
            <td class='text-center'>${value.total_campaigns}</td>
            <td class='text-left text-wrap'>${desc}</td>
            <td class='text-center'>${createdTime}</td>
            <td class='text-center'>${value.created_by}</td></tr>`;
            });
            html += '</table>';
            $("#list_budget_table").html(html);
        }

        function genCampaignTransTable(campaignTrans){
            var html = `<table class="table text-center" style="border-collapse: inherit;table-layout: fixed;"><thead><tr>
            <th class='text-center' style='width:5%'>#</th>
            <th class='text-left' style='width:15%'>Artist</th>
            <th class='text-center' style='width:10%'>Cam Id/Ref Id</th>
            <th class='text-left' style='width:15%'>Campaign</th>
            <th class='text-center' style='width:5%'>Amount</th>
            <th class='text-center' style='width:15%'>Created time</th>
            <th class='text-center'style='width:10%'>Created by</th></tr>`;
            var i = 0;
            $.each(campaignTrans, function(key, value) {
                i = i + 1;
                var evenOdd = "even";
                if (i % 2 == 0) {
                    evenOdd = "odd";
                }
                var refId = "-";
                var campaign_id = "-";
                var artist_name = "-";
                var campaignName = "Old Campaigns";
                if(value.ref_id!=null){
                    refId = value.ref_id;
                }
                if(value.campaign_id!=null){
                    campaign_id = value.campaign_id;
                }
                if(value.campaign_name!=null){
                    campaignName = value.campaign_name;
                }
                if(value.artist_name!=null){
                    artist_name = value.artist_name;
                }
                
                html +=
                    `<tr class="${evenOdd}">
            <td scope="row"><span class="cur-poiter" >${value.id}</span></td>
            <td class="text-ellipsis text-left" data-toggle="tooltip" data-placement="top" data-original-title="${artist_name}">${artist_name}</td>
            <td class='text-center'>${campaign_id} / ${refId}</td>
            <td class='text-ellipsis text-left'>${campaignName}</td>
            <td class='text-center'>$${ number_format(value.amount, 0, '.', ',')}</td>
            <td class='text-center'>${value.created}</td>
            <td class='text-center'>${value.created_by}</td></tr>`;
            });
            html += '</table>';
            $("#list_campaign_trans_table").html(html);
        }

        function clearBudget() {
            $("#budget_amount").val("");
            $("#budget_desc").val("");
            $("#campaign_number").val("");
        }

        function addBudget() {
            $("#create_invoice_result").html("");
            var $but = $(".btn-add-budget");
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            if ($but.html() !== loadingText) {
                $but.data('original-text', $but.html());
                $but.html(loadingText);
            }
            var form = $("#formBudget").serialize();
            console.log(form);
            var formCommon = $("#formCommon").serialize();
            $.ajax({
                type: "POST",
                url: "/360promo2/addBudget",
                data: `${form}&${formCommon}`,
                dataType: 'json',
                success: function(data) {
                    $but.html($but.data('original-text'));
                    logger('addBudget', data);
                    if (data.status == "error") {
                        $.Notification.notify('error', 'top center', '', data.message);
                    } else {
                        clearBudget();
                        loadCustomer();
                    }

                },
                error: function(data) {
                    logger('Error:', data);
                    $but.html($but.data('original-text'));
                }
            });
        }

        function loadInvoice() {
            $("#invoice_loading").show();
            $.ajax({
                type: "GET",
                url: "/360promo2/loadInvoice",
                data: {
                    "id": currId,
                    "email": currEmail
                },
                success: function(data) {
                    $("#invoice_loading").hide();
                    logger('loadInvoice', data);

                    var html = `<div class="row">`;
                    html += '<div class="col-md-12">';
                    html += `<table id="tbl-invoice" class="table text-center" style="border-collapse: inherit;table-layout: fixed;"><thead><tr>
                    <th class='text-center' style='width:5%'>#</th>
                    <th class='text-center' style='width:10%'>Artist</th>
                    <th class='text-center' style='width:15%'>Method</th>
                    <th class='text-left' style='width:30%'>Description</th>
                    <th class='text-center' style='width:10%'>Action</th>
                    <th class='text-center' style='width:15%'>Date</th>
                    <th class='text-center' style='width:10%'>Amount</th>
                    <th class='text-center'style='width:5%'>Status</th></tr>`;
                    var i = 0;
                    $.each(data.invoices, function(key, value) {
                        i = i + 1;
                        var evenOdd = "even";
                        if (i % 2 == 0) {
                            evenOdd = "odd";
                        }
                        var artistName = "";
                        const selectedArtist = budgetArtist.find(artist => artist.artist_id == value.artist_id);
                        if (selectedArtist) {
                            artistName = selectedArtist.artist_name;
                        }
                        var statusText = "Unpaid";
                        var badgeColor = "danger";
                        var color = "color-g";
                        var tooltip = "Confirm this invoice";
                        var icon = "fa-check";
                        var btnText = 'Approve';
                        if (value.status == 'paid') {
                            statusText = "Paid";
                            badgeColor = "success";
                            color = "color-red";
                            tooltip = "Cancel this invoice";
                            icon = "fa-times";
                            btnText = 'Cancel';
                        }

                        var date = value.created.split(" ");
                        if (value.updated != null) {
                            date = value.updated.split(" ");
                        }
                        var dateSplit = `<div>${date[0]}</div><div>${date[1]}</div>`;
                        var fee = 'N/A';
                        if (value.paypal_fee != null) {
                            fee = "$" + value.paypal_fee;
                        }
                        var link = `${domain}/payment/v2?invoice_id=${value.invoice_id}`;
                        var button = "";
                        var editBtn = "";
                        var copyBtn = "";
                        if(value.status != 'paid'){
                            button = `<span id="span_iv_${value.invoice_id}" class="m-r-5" data-toggle="tooltip" data-placement="top" data-original-title="${tooltip}">
                                <button id="btn_iv_${value.invoice_id}" type="button" class="${color} btn btn-sm btn-circle" onclick="confirmInvoice('${value.invoice_id}','{{ $user_login->user_name }}')"><i class="fa ${icon}"></i></button>
                            </span>`;
                            editBtn = `<span class="m-r-5" data-toggle="tooltip" data-placement="top" data-original-title="Edit invoice">
                                <button id="edit_iv_${value.invoice_id}" type="button" class="text-warning btn btn-sm btn-circle" onclick="editInvoice('${btoaUnicode(JSON.stringify(value))}')"><i class="fa fa-edit"></i></button>
                            </span>`;
                        }
                        copyBtn = `<span class="m-r-5" data-toggle="tooltip" data-placement="top" data-original-title="Copy invoice link">
                            <button id="copy_iv_${value.invoice_id}" type="button" class="text-primary btn btn-sm btn-circle" onclick="copyText('${link}')"><i class="fa fa-copy"></i></button>
                        </span>`;
                        
                        var tooltip = "";
                        var logs = value.log.split("\n");

                        if (logs.length > 0) {
                            tooltip =
                                `data-html="true"
                                        data-bs-custom-class="custom-tooltip"
                                        data-toggle="tooltip" 
                                        data-placement="left" 
                                        data-original-title="<ul class='text-left font-13' style='padding: 1px 1px 1px 14px;'>`;
                            $.each(logs, function(k, v) {
                                tooltip +=
                                    `<li>${v}</li>`;
                            });
                            tooltip += `</ul>"`;
                        }       
                        
                        html +=
                            `<tr class="${evenOdd}">
                    <td scope="row"><span class="cur-poiter" data-toggle="tooltip" data-placement="top" data-original-title="Copy payment link" onclick="copyText('${link}')">${value.invoice_id}</span></td>
                    <td class='text-center'>${artistName}</td>
                    <td class='text-center'>${value.payment_method}</td>
                    <td class="text-ellipsis text-left" data-toggle="tooltip" data-placement="top" data-original-title="">${value.description}</td>
                    <td class='text-center'>${button} ${editBtn} ${copyBtn}</td>
                    <td class='text-center'>${dateSplit}</td>
                    <td class='text-center'>$${ number_format(value.amount, 0, '.', ',')}</td>
                    <td id="badge_iv_${value.invoice_id}" class='text-center'><span ${tooltip} class="badge badge-${badgeColor}">${statusText}</span></td></tr>`;
                    });
                    html += '</table></div></div>';
                    $("#list_invoice_table").html(html);
                    $('[data-toggle="tooltip"]').tooltip();
                },
                error: function(data) {
                    logger('Error:', data);
                }
            });
        }
        
        function submitInvoice() {
            $("#create_invoice_result").html("");
            var $but = $(".btn-submit-invoice");
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            if ($but.html() !== loadingText) {
                $but.data('original-text', $but.html());
                $but.html(loadingText);
            }
            var form = $("#formInvoice").serialize();
            var formCommon = $("#formCommon").serialize();
            $.ajax({
                type: "POST",
                url: "/360promo2/submitInvoice",
                data: `${form}&${formCommon}`,
                dataType: 'json',
                success: function(data) {
                    $but.html($but.data('original-text'));
                    logger('submitInvoice', data);
                    if (data.status == "success") {
                        var title = `<div class="mb-2"><span><b>Invoice link</b>&nbsp;<i class="fa fa-envelope" onclick="copy('.mail_content')"></i></span></div>`;
                        navigator.clipboard.writeText(data.message);
                        $.Notification.notify('success', 'top center', '','Copied invoice link, please send it to the customer ');
                        $('[data-toggle="tooltip"]').tooltip();
                        var html =`${title}<div class="alert alert-${data.status}" role="alert"><span>${data.message}</span></div>`;
                        $("#create_invoice_result").html(html);
                        loadInvoice();
//                        clearInvoice();
                        $("#invoice_id").val(null);
                    } else {
                        var html =
                            `<div class="mb-2"><span><b>Error</b></span></div><div style="border-radius: 0.475rem" class="alert alert-${data.status}" role="alert"><span>${data.message}</span></div>`
                        $("#create_invoice_result").html(html);
                    }

                },
                error: function(data) {
                    logger('Error:', data);
                    $but.html($but.data('original-text'));
                }
            });
        }
        
        function clearInvoice() {
            $("#is_save_info").prop("checked", false);
            $("#invoice_id").val(null);
            $("#payment_method").val('360promo_hk').change();
            $("#invoice_artist").val("").change();
//            $("#invoice_amount").val("");
        }        
        
        function confirmInvoice(invoice_id) {
        $.confirm({
            animation: 'rotateXR',
            title: 'Confirm!',
            content: 'Are you sure to confirm this invoice?',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-red',
                    action: function () {
                        var $this = $("#btn_iv_" + invoice_id);
                        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
                        if ($this.html() !== loadingText) {
                            $this.data('original-text', $this.html());
                            $this.html(loadingText);
                        }
                        $.ajax({
                            type: "GET",
                            url: "/360promo2/confirmInvoice",
                            data: {
                                "invoice_id": invoice_id
                            },
                            success: function(data) {
                                logger('approveInvoice', data);
                                $this.html($this.data('original-text'));
                                $.Notification.notify(data.status, 'top center', '', data.message);
                                loadCustomer();
                            },
                            error: function(data) {
                                logger('Error:', data);
                                $this.html($this.data('original-text'));
                            }
                        });
                    }
                },
                cancel: function () {

                }

            }
        });

        }
        
        function editInvoice(data) {
            var iv = JSON.parse(atobUnicode(data));
            var $this = $("#edit_iv_" + iv.invoice_id);
                var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
                if ($this.html() !== loadingText) {
                    $this.data('original-text', $this.html());
                    $this.html(loadingText);
                }
                $("#invoice_artist").val(iv.artist_id==null?"":iv.artist_id);
                $('#invoice_artist').selectpicker('refresh');
                $("#invoice_content").val(iv.description);
                $("#invoice_id").val(iv.invoice_id);
                $("#invoice_amount").val(iv.amount);
                $("#payment_method").val(iv.payment_method);
                $("#po_number").val(iv.po_number);
                $("#company_name").val(iv.company_name);
                $("#customer_address").val(iv.customer_address);
                $("#customer_name").val(iv.customer_name);
                $("#customer_email").val(iv.customer_email);
                $this.html($this.data('original-text'));
                var modalBody = $("#dialog_customer_detail").find('.modal-body');
                var target = $('#formInvoice');
                var targetOffset = target.offset().top - modalBody.offset().top + modalBody.scrollTop();
                modalBody.animate({ scrollTop: targetOffset }, 500); 

        }

        function activeCampaign() {
            var $but = $(".btn-submit-campaign");
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            if ($but.html() !== loadingText) {
                $but.data('original-text', $but.html());
                $but.html(loadingText);
            }
            var form = $("#formCampaign").serialize();
            var formCommon = $("#formCommon").serialize();
            $.ajax({
                type: "POST",
                url: "/360promo2/activeCampaign",
                data: `${form}&${formCommon}`,
                dataType: 'json',
                success: function(data) {
                    $but.html($but.data('original-text'));
                    logger('activeCampaign', data);
                    $.Notification.notify(data.status, 'top center', '',data.message);
                    if(data.status=="success"){
                        loadCustomer();
                    }
                    

                },
                error: function(data) {
                    logger('Error:', data);
                    $but.html($but.data('original-text'));
                }
            });
        }
        
        autoCompleteInput();
        autloadCustomerDetail();        
    </script>
@endsection
