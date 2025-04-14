@extends('layouts.master')

@section('content')
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
                                        <th style="text-align: left;width: 20%">Email</th>
                                        <th style="text-align: center;width: 20%">Info</th>
                                        <th style="text-align: left;width: 10%">Last Payment</th>
                                        <th style="text-align: center;width: 10%">Created Date</th>
                                        <th style="text-align: left;width: 10%">Link</th>
                                        <th style="text-align: center;width: 5%">Sent</th>
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
                                                        onclick="dialogInvoice({{ $data->id }},'{{ $data->email }}')"><i
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
                                            <td class="text-center">
                                                <div style="display: inline-grid">
                                                    <span class="badge badge-success">{{ $data->campaign }}
                                                        campaigns</span>
                                                    <span class="badge badge-success">{{ $data->invoice }} invoices</span>
                                                    @if ($data->budget_debit > 0)
                                                        <span class="badge badge-danger">Budget
                                                            ${{ $data->budget_debit }}</span>
                                                    @endif
                                                    @if ($data->campaign_debit > 0)
                                                        <span class="badge badge-danger">Campaign
                                                            ${{ $data->campaign_debit }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-left text-ellipsis">
                                                Budget: {{ App\Common\Utils::calcTimeText($data->budget_time) }}<br>
                                                Direct: {{ App\Common\Utils::calcTimeText($data->invoice_time) }}
                                            </td>
                                            <td class="text-center">{{ $data->created }}</td>
                                            <td class="text-left">
                                                <a href="{{ $data->link }}"><i class="fa fa-music"></i></a>
                                                <a href="{{ $data->instagram }}"><i class="fa fa-instagram"></i></a>
                                            </td>
                                            <td class="text-center">
                                                <div class="checkbox checkbox-primary tbl-chk">
                                                    <input class="checkbox-multi ck-email" type="checkbox"
                                                        name="chkStatus[]" id="ck-email-{{ $data->id }}"
                                                        value="{{ $data->id }}"
                                                        {{ $data->status_sent == 1 ? 'checked' : '' }}>
                                                    <label class="m-b-18 p-l-0"
                                                        for="ck-email-{{ $data->id }}"></label>
                                                </div>
                                            </td>
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

    @include('dialog.promo360.addinvoice')
    @include('dialog.promo360.add_email_manual')
@endsection

@section('script')
    <script type="text/javascript">
        var currBudget = 0;
        var currDebitBudget = 0;
        var currLimitInvoice = 0;
        var listCampaign = [];
        var currId = 0;
        var currEmail = "";
        autoCompleteInput();
        autloadInvoice();
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

//        //filter submission channel
//        $(".btn-channel-id").hide();
//        $("#filterInput").on("keyup", function() {
//            var value = $(this).val().toLowerCase();
//            $(".btn-channel-id").filter(function() {
//                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
//            });
//        });
//        $(".btn-channel-id").on("click", function() {
//            var channelId = $(this).attr("data-channel");
//            var selectedChannels = $("#channel_id").val().split(",").filter(Boolean);;
//
//            if ($(this).hasClass("btn-outline-dashed-active")) {
//                // Nếu kênh đã được chọn, bỏ chọn kênh
//                $(this).removeClass("btn-outline-dashed-active");
//                selectedChannels = selectedChannels.filter(function(value) {
//                    return value !== channelId;
//                });
//            } else {
//                // Nếu kênh chưa được chọn, chọn kênh
//                $(this).addClass("btn-outline-dashed-active");
//                selectedChannels.push(channelId);
//            }
//
//            // Cập nhật giá trị cho input ẩn
//            $("#channel_id").val(selectedChannels.join(","));
//        });        
//        $(".btn-channel-id").click(function() {
//            var channelId = $(this).attr("data-channel");
//            $("#channel_id").val(channelId).change();
//            $(".btn-channel-id").removeClass("btn-outline-dashed-active");
//            $(`.btn-channel-id[data-channel=${channelId}]`).addClass("btn-outline-dashed-active");
//        });        

        function loginEmail() {
            //            window.open('https://dash.360promo.net/login');
            var email = $("#email").val();
            var p = $("#password").val();
            let newWindow = open(`https://dash.360promo.net/login?u=${email}&p=${p}`, 'example2', 'width=1920,height=1080')
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

        //2023/07/28 approve invoice
        function approveInvoice(invoice_id, user) {
            var $this = $("#btn_iv_" + invoice_id);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "get",
                url: "/360promo/approveInvoice",
                data: {
                    "invoice_id": invoice_id,
                    'username': user
                },
                success: function(data) {
                    logger('approveInvoice', data);
                    $this.html($this.data('original-text'));
                    var mesage = "Failed";
                    if (data.status = 'success') {
                        mesage = "success";
                        var tooltip = "Approve this invoice";
                        var icon = '<i class="fa fa-check"></i>';
                        var badge = '<span class="badge badge-danger">Unpaid</span>';
                        if (data.invoice.status_pay == 1) {
                            $this.hide();
                            tooltip = "Cancel this invoice";
                            icon = '<i class="fa fa-times"></i>';
                            badge = '<span class="badge badge-success">Paid</span>';
                            $this.removeClass("color-g").addClass("color-red");
                        } else {
                            $this.removeClass("color-red").addClass("color-g");
                        }
//                        $this.html(icon);
//                        $("#span_iv_" + invoice_id).attr("data-original-title", tooltip);
                        $("#badge_iv_" + invoice_id).html(badge);
                    }
                    $.Notification.notify(data.status, 'top center', '', mesage);
                    loadInvoice();
                },
                error: function(data) {
                    logger('Error:', data);
                    $this.html($this.data('original-text'));
                }
            });
        }

        //2023/08/03 debit
        function clear() {
            $("#cam_type_pro").prop("checked", true).change();
            $("#payment_method").val('360promo_hk').change();
            $("#campaign_id").val("").change();
            $("#limit_campaign").val("");
            $("#invoice_type").val(2).change();
            $("#invoice_package").val(0).change();
            $("#invoice_youtube_music").val("50K");
            $("#invoice_youtube_music").val("50K");
            $("#invoice_amount").val("750");
            $("#campaign_amount").val("750");
        }

        //2023/07/12 invoice
        function dialogInvoice(id, email) {
            $("#invoice_email").val(email);
            $("#create_invoice_result").html("");
            $('#campaign_list').html("");
            $("#invoice_result_table").html("");
            currId = id;
            currEmail = email;
            clear();
            loadInvoice();
            $("#dialog_add_invoice_title").html(email);
            $("#invoice_content").val(`360 Promo Campaign\nVideo Creation\nChannel/Ad Mgmt\nViews/Subs\nCurator Campaign\nPR/Radio`);
            $('#dialog_add_invoice').modal({
                backdrop: false
            });
        }


        function autloadInvoice() {
            var refEmmail = $("#email_ref").val();
            if (refEmmail != "") {
                $('[type="search"]').val(refEmmail).change();
                currEmail = refEmmail;
                dialogInvoice(0, refEmmail);
            }
        }

        function loadInvoice() {
            $("#invoice_loading").show();
            $("#campaing_list_loading").show();
            $("#curr_balance,#curr_campaign,#curr_debit,#curr_debit_invoice,#curr_debit_total,#amount_spent").addClass("skeleton-box").css({ "width": "100%", "height": "30px" }).html("");
            $("#debit_time,#debit_time_invoice").addClass("skeleton-box").css({ "width": "100%", "height": "16px" }).html("");
            $.ajax({
                type: "get",
                url: "/360promo/listInvoice",
                data: {
                    "id": currId,
                    "email": currEmail
                },
                success: function(data) {
                    $("#invoice_loading").hide();
                    logger('loadInvoice', data);
                    $("#po_number").val(data.po_number);
                    if (data.user.role.includes("SUBMISSION")) {
                        $("#cam_type_sub").prop("checked", true).change();
                    }
                    var html = `<div class="row">`;
                    html += '<div class="col-md-12">';
                    html += `<table id="tbl-invoice" class="table text-center" style="border-collapse: inherit;table-layout: fixed;"><thead><tr>
                    <th class='text-center' style='width:5%'>#</th>
                    <th class='text-center' style='width:10%'>Type</th>
                    <th class='text-center' style='width:10%'>Campaign</th>
                    <th class='text-center' style='width:10%'>Method</th>
                    <th class='text-left' style='width:30%'>Description</th>
                    <th class='text-center' style='width:10%'></th>
                    <th class='text-center' style='width:15%'>Date</th>
                    <th class='text-center' style='width:10%'>Amount</th>
                    <th class='text-center' style='width:10%'>Deal Total</th>
                    <th class='text-center'style='width:10%'>Status</th></tr>`;
                    var i = 0;
                    $.each(data.invoices, function(key, value) {
                        var statusText = "Unpaid";
                        var badgeColor = "danger";
                        var color = "color-g";
                        var tooltip = "Confirm this invoice";
                        var icon = "fa-check";
                        var btnText = 'Approve';
                        if (value.status_pay == 1) {
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
                        var button = "";
                        if (value.status_pay == 0 || value.status_pay == 2) {
                            button = `<span id="span_iv_${value.id}" data-toggle="tooltip" data-placement="top" data-original-title="${tooltip}">
                            <button id="btn_iv_${value.id}" type="button" class="${color} btn btn-sm btn-circle" onclick="approveInvoice('${value.id}','{{ $user_login->user_name }}')"><i class="fa ${icon}"></i></button>
                        </span>`;
                        }
                        var path = "payment";
                        var link = `https://dash.360promo.net/${path}?invoice_id=${value.id}`;
                        if (value.type == 2) {
                            link =
                                `https://dash.360promo.net/${path}?invoice_id=${value.id}&campaign_id=${value.campaign_id}`;
                        }
                        if (value.submission_channel != null) {
                            link += `&sub_cid=${value.submission_channel}`;
                        }
                        var type = "";
                        if (value.type == 3) {
                            type = "Add Budget";
                        } else if (value.type == 4) {
                            type = "Debit Budget";
                        } else if (value.type == 2) {
                            type = "Exists Campaign";
                        } else if (value.type == 1) {
                            type = "New Campaign";
                        }
                        html +=
                            `<tr>
                    <td scope="row"><span class="cur-poiter" data-toggle="tooltip" data-placement="top" data-original-title="Copy payment link" onclick="copyText('${link}')">${value.id}</span></td>
                    <td class='text-center'>${type}</td>
                    <td class='text-center'><span data-toggle="tooltip" data-placement="top" data-original-title="${value.artist} - ${value.song_name}">${value.ref_id == ""?"":value.ref_id}</span></td>
                    <td class='text-center'>${value.payment_method}</td>
                    <td class="text-ellipsis text-left" data-toggle="tooltip" data-placement="top" data-original-title="${value.content}">${value.content}</td>
                    <td class='text-center'>${button}</td>
                    <td class='text-center'>${dateSplit}</td>
                    <td class='text-center'>$${ number_format(value.amount, 0, '.', ',')}</td>
                    <td class='text-center'>$${ number_format(value.amount_total, 0, '.', ',')}</td>
                    <td id="badge_iv_${value.id}" class='text-center'><span class="badge badge-${badgeColor}">${statusText}</span></td></tr>`;
                    });
                    html += '</table></div></div>';
                    $("#invoice_result_table").html(html);
                    $('[data-toggle="tooltip"]').tooltip();
                    var option = "";
                    $('#campaign_list').html("");
                    listCampaign = data.campaigns;
                    loadCampaign();
                    $("#campaing_list_loading").hide();
                    //                    $.each(data.campaigns, function(key, value) {
                    //                        var debit = 0;
                    //                        //show những campaign chưa đc thanh toán, nợ tiền, và campaing đã được thanh toán nhưng chưa có invoice_id
                    //                        if (value.amount_paid < value.amount_total || value.status_pay == 0 || value.invoice_id==null) {
                    //                            debit = value.amount_total - value.amount_paid;
                    //                            var invoiceId = value.invoice_id==null?'No Invoice':'IV#'+value.invoice_id;
                    //                            var active = value.status_pay==1?'Actived':'Inactive';
                    //                            var debitText = debit == 0 ? '' : `Debit $${debit}`;
                    //                            debitCampaign.push({
                    //                                cam_id: value.cam_id,
                    //                                name: value.campaign_name,
                    //                                debit: debit
                    //                            });
                    //                            $('#campaign_list').append(
                    //                                `<option value="${value.cam_id}">${value.campaign_name} (${debitText} - ${active} - ${invoiceId})</option>`
                    //                            );
                    //                        }
                    //                    });

//                    $("#current_budget").val(data.user.budget);
                    currBudget = data.user.budget_total - data.user.budget_use;
                    $("#current_budget").val(data.user.budget_total - data.user.budget_use);
                    currLimitInvoice = data.user.limit_invoice;
                    $("#current_limit_invoice").val(data.user.limit_invoice);
                    currDebitBudget = data.user.budget_total - data.user.budget
                    $("#budget_debit_amount").html(currDebitBudget);
                    $("#budget_debit_amount_value").val(currDebitBudget);
                    
//                    $("#curr_balance").html(currBudget);
//                    $("#curr_campaign").html(currLimitInvoice);
//                    $("#curr_debit").html(currDebitBudget);
//                    $("#curr_debit_invoice").html(data.debitCampaign);
//                    $("#debit_time").html(data.user.budget_time_text);
//                    $("#debit_time_invoice").html(data.user.invoice_time_text);
//                    $("#curr_debit_total").html(data.debitCampaign + currDebitBudget);
//                    $("#amount_spent").html(data.amountSpent);
                    $("#curr_balance").removeClass("skeleton-box").css({ "width": "", "height": "" }).html("$" + currBudget);
                    $("#curr_campaign").removeClass("skeleton-box").css({ "width": "", "height": "" }).html(currLimitInvoice);
                    $("#curr_debit").removeClass("skeleton-box").css({ "width": "", "height": "" }).html("$" + currDebitBudget);
                    $("#curr_debit_invoice").removeClass("skeleton-box").css({ "width": "", "height": "" }).html("$" + data.debitCampaign);
                    $("#debit_time").removeClass("skeleton-box").css({ "width": "", "height": "" }).html(data.user.budget_time_text);
                    $("#debit_time_invoice").removeClass("skeleton-box").css({ "width": "", "height": "" }).html(data.user.invoice_time_text);
                    $("#curr_debit_total").removeClass("skeleton-box").css({ "width": "", "height": "" }).html("$" + (data.debitCampaign + currDebitBudget));
                    $("#amount_spent").removeClass("skeleton-box").css({ "width": "", "height": "" }).html("$" + data.amountSpent);
                },
                error: function(data) {
                    logger('Error:', data);
                }
            });
        }

        function loadCampaign() {
            $(".campaign_list_content").html("");
            var html = "";
            $.each(listCampaign, function(key, value) {
                var debit = 0;
                var pic = value.pic_album;
                if (pic == null) {
                    pic = value.pic_single;
                }
                //show những campaign chưa đc thanh toán, nợ tiền, và campaing đã được thanh toán nhưng chưa có invoice_id
                if (value.amount_paid < value.amount_total || value.status_pay == 0 || value.invoice_id == null) {
                    debit = value.amount_total - value.amount_paid;
                    var invoiceId = value.invoice_id == null ? 'No Invoice' :
                        `IV# <strong>${value.invoice_id}</strong>`;
                    var active = value.status_pay == 1 ? '<span class="color-h">Actived</span>' :
                        '<span class="color-red">Inactive</span>';
                    var debitText = debit == 0 ? '' : `<span class="color-red">Debit $${debit}</span>`;
                    html += `<div class="d-flex flex-stack py-2 cur-poiter border-dashed-bt-ccc campaign_list" data-id="${value.cam_id}">
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
                            ${debitText}
                        </div>
                        <div class="mr-6">
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
            });
        }

        function submitInvoice() {
            $("#create_invoice_result").html("");
            var $but = $(".btn-create-invoice");
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            if ($but.html() !== loadingText) {
                $but.data('original-text', $but.html());
                $but.html(loadingText);
            }
            var form = $("#formCreateInvoice").serialize();
            $.ajax({
                type: "POST",
                url: "/360promo/addInvoice",
                data: form,
                dataType: 'json',
                success: function(data) {
                    $but.html($but.data('original-text'));
                    logger('submitInvoice', data);
                    var title = "";
                    if (data.status == "success") {
                        if (data.invoice.payment_method != 'budget' && $("#invoice_amount").val() != 0) {
                            $(".mail_content").text(data.mail);
                            title =
                                `<div class="mb-2"><span><b>Invoice link</b>&nbsp;<i class="fa fa-envelope" onclick="copy('.mail_content')"></i></span></div>`
                            navigator.clipboard.writeText(data.message);
                            $.Notification.notify('success', 'top center', '',
                                'Copied invoice link, please send it to the customer ');
                            $('[data-toggle="tooltip"]').tooltip();
                            var html =
                                `${title}<div class="alert alert-${data.status}" role="alert"><span>${data.message}</span></div>`
                            $("#create_invoice_result").html(html);
                        } else {
                            $.Notification.notify('success', 'top center', '',
                                `Successfully created invoice #ID ${data.invoice.id}`);
                        }
                        loadInvoice();
                    } else {
                        var html =
                            `<div class="mb-2"><span><b>Error</b></span></div><div style="border-radius: 0.475rem" class="alert alert-${data.status}" role="alert"><span>${data.message}</span></div>`
                        $("#create_invoice_result").html(html);
                    }

                },
                error: function(data) {
                    logger('Error:', data);
                    $but.html($but.data('original-text'));
                    //                $this.html($this.data('original-text'));
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
            $("#campaign_id").change(function() {
                $("#payment_method").val('360promo_hk').change();
                if ($("#payment_method").val() != 'budget') {
                    if (listCampaign.length > 0) {
                        var id = $(this).val();
                        let campaign = listCampaign.filter(function(el) {
                            return el.cam_id == id;
                        });
                        var debit = campaign[0]?.amount_total - campaign[0]?.amount_paid;
                        $("#campaign_debit_value").val(debit);
                        if (debit > 0) {
                            //campaign debit
                            $("#is_campaign_debit").val(1);
                            $("#invoice_amount").val(debit);
                            $("#campaign_amount").val(debit);
                            $("#invoice_content").val(
                            `Pay off the debt for campaign ${campaign[0]?.campaign_name}`);
                            $("#payment_method option[value='budget']").hide();
                        } else {
                            //campaign mới
                            $("#is_campaign_debit").val(0);
                            //                            $("#invoice_package").val(0).change();
                            $("#invoice_content").val(`360 Promo Campaign\nVideo Creation\nChannel/Ad Mgmt\nViews/Subs\nCurator Campaign\nPR/Radio`);
                            $("#payment_method option[value='budget']").show();
                        }
                    }
                }

            });

            $(".btn-invoice-type").click(function() {
                var ivType = $(this).val()
                $("#invoice_type").val(ivType).change();
            });
            $("#invoice_type").change(function() {
                var ivType = $(this).val();
                $(".btn-invoice-type").removeClass("btn-outline-dashed-active");
                $(`.btn-invoice-type[value=${ivType}]`).addClass("btn-outline-dashed-active");
                $(`.div_invoice_type[show-id=${ivType}]`).show();
                $(`.div_invoice_type[show-id!=${ivType}]`).hide();
                $(`.div_package[show-id=${ivType}]`).show();
                $(`.div_package[show-id!=${ivType}]`).hide();
                if (ivType == 1) {
                    $(".div_package").show();
                    $("#invoice_package").val(0).change();
                    $("#invoice_content").val(`360 Promo Campaign\nVideo Creation\nChannel/Ad Mgmt\nViews/Subs\nCurator Campaign\nPR/Radio`);
                    if ($("#campaign_debit_value").val() == 0) {
                        $("#payment_method option[value='budget']").show();
                    } else {
                        $("#payment_method option[value='budget']").hide();
                    }
                } else if (ivType == 2) {
                    $(".div_package").fadeIn('fast');
                    $("#payment_method option[value='budget']").hide();
                    //                    $("#campaign_id").val(debitCampaign[0]?.cam_id).change();
                } else if (ivType == 3) {
                    $("#payment_method").val('360promo_hk').change();
                    $("#invoice_content").val(`360 Promo Campaign\nVideo Creation\nChannel/Ad Mgmt\nViews/Subs\nCurator Campaign\nPR/Radio`);
                    $("#payment_method option[value='budget']").hide();
                } else if (ivType == 4) {
                    //debit budget
                    $("#payment_method").val('360promo_hk').change();
                    $("#invoice_content").val(`360 Promo Campaign\nVideo Creation\nChannel/Ad Mgmt\nViews/Subs\nCurator Campaign\nPR/Radio`);
                    $("#invoice_amount").val(currDebitBudget);
                    $("#campaign_amount").val(currDebitBudget);
                    $("#payment_method option[value='budget']").hide();
                }
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
                $("#invoice_amount").val(cost);
                $("#campaign_amount").val(cost);
            });
            $("#payment_method").change(function() {
                var value = $(this).val();
                var invoiceType = $("#invoice_type").val();
                if (value == 'budget') {
                    if (invoiceType == 3 || invoiceType == 4) {
                        $("#invoice_type").val(1).change();
                    }
                    $(".div_payment_method_budget").show();
                    $(".div_budget_hide").hide();
                    if (currLimitInvoice != 0) {
                        var iv = Math.round(currBudget / currLimitInvoice);
                        $("#invoice_amount").val(iv)
                        $("#campaign_amount").val(iv);
                    }
                } else {
                    $(".div_budget_hide").show();
                    $(".div_payment_method_budget").hide();
                }
            });
            $("#invoice_amount").change(function() {

                var paymentMenthod = $("#payment_method").val();
                var pay = $(this).val();
                var total = $("#campaign_amount").val();
                var currentBudget = $("#current_budget").val();
                var ivType = $("#invoice_type").val();
                if (ivType == 3) {
                    //add budget thì số pay = total
                    $("#campaign_amount").val(pay);
                } else if (ivType == 4) {

                } else if (paymentMenthod == 'budget') {
                    if (currLimitInvoice != 0) {
                        var iv = Math.round(currentBudget / currLimitInvoice);
                        $("#campaign_amount").val(iv);
                        if (parseInt(pay) > parseInt(currentBudget)) {
                            $(this).val(iv);
                        }
                    }
                } else {
                    //trường hợp bình thường thì số pay <= total
                    if (parseInt(pay) > parseInt(total)) {
                        total = pay;
                    }
                    $("#campaign_amount").val(total);
                }
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
        }

        //2023/06/27 datatable
        $('#tbl-360promo').DataTable({
            oSearch: {"sSearch": $("#email_ref").val()},
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
    </script>
@endsection
