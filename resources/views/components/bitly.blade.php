@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">Bitly/MoonAz</span>
                <button data-toggle="tooltip" data-placement="top" data-original-title="Add New Bitly" type="button" class="btn btn-outline-info btn-import-bitly m-r-5"><i class="fa fa-plus"></i></button>
                <button data-toggle="tooltip" data-placement="top" data-original-title="$ Report Bitly/Moonaz" type="button" class="btn btn-outline-info btn-report-usd m-r-5"><i class="fa  fa-usd"></i></button>
                <!--<button data-toggle="tooltip" data-placement="top" data-original-title="$ Report Moonaz" type="button" class="btn btn-outline-info btn-report-usd-moonaz m-r-5"><i class="fa  fa-usd"></i></button>-->
            </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Bitly</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <form id="formSearch" action="/bitly">
                <input type="hidden" name="limit" id="limit" value="{{$limit}}">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-12 col-form-label">Group Bitly</label>
                            <div class="col-12">
                                <select class="form-control" name="group_bit">
                                    {!!$groupBit!!}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">Custom</label>
                            <div class="col-12">
                                <input class="form-control" type="text" name="custom" value="{{$request->custom}}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group row">
                            <label class="col-8 col-form-label">&nbsp;</label>
                            <div class="col-12">
                                <button id="btnSearch" type="submit" class="btn btn-outline-info btn-micro"><i class="fa fa-search"></i> Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<input id="token" type="hidden" name='_token' value='{{csrf_token()}}' />
<!--<div class="row">
    <div class="col-lg-12">
        <div class="portlet">
            <div class="portlet-heading portlet-default">
                <h3 class="portlet-title">
                    Stats
                </h3>
                <div class="portlet-widgets">
                    <a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
                    <span class="divider"></span>
                    <a data-toggle="collapse" data-parent="#accordion1" href="#portlet-stats"><i class="ion-minus-round"></i></a>
                    <span class="divider"></span>
                    <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div id="portlet-stats" class="panel-collapse collapse show">
                <div class="portlet-body">
                    
                </div>
            </div>
        </div>
    </div>
</div>-->
<div class="row">
    <div class="col-lg-12">
        <div class="portlet">
            <div class="portlet-heading portlet-default">
                <h3 class="portlet-title">
                    Bitly ({{$bitlys->total()}} links - {{$sum}} clicks)
                </h3>
                <div class="portlet-widgets">
                    <a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
                    <span class="divider"></span>
                    <a data-toggle="collapse" data-parent="#accordion1" href="#bg-default"><i class="ion-minus-round"></i></a>
                    <span class="divider"></span>
                    <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div id="bg-default" class="panel-collapse collapse show">
                <div class="portlet-body">
                    <div style="overflow: auto">
                        <table id="tbl-bitly" class="table" style="width: 99%;table-layout:fixed;">
                            <thead class="thead-default">
                                <tr>
                                    <th style="text-align: center;width: 2%">Index</th>
                                    @if($is_admin_music)
                                    <th style="text-align: center;width: 7%">Username</th>
                                    @endif
                                    <th style="text-align: center;width: 5%">Group</th>
                                    <th style="text-align: center;width: 5%">Bitly Id</th>
                                    <th style="text-align: left;width: 10%">Custom Bitly</th>
                                    <th style="text-align: center;width: 5%">@sortablelink('clicked','Clicked')</th>
                                    <th style="text-align: left;width: 25%">Title</th>
                                    <th style="text-align: center;width: 7%">@sortablelink('created','Created')</th>
                                    <th style="text-align: center;width: 7%">Last Sync</th>
                                    <th style="text-align: right;width: 10%">Function</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bitlys as $data)
                                <tr>
                                    <td class="text-center">{{$data->id}}</td>
                                    @if($is_admin_music)
                                    <td class="text-center">{{$data->username}}</td>
                                    @endif
                                    <td class="text-center">{{$data->group_bitlink}}</td>
                                    <td class="text-center"><a target="_blank" href="https://bit.ly/{{$data->bitly_id}}">{{$data->bitly_id}}</td>
                                    <td class="text-left text-ellipsis"><a target="_blank" href="{{$data->long_url}}">{{$data->custom_bitlinks}}</td>
                                    <td class="text-center">{{$data->clicked}}</td>
                                    <td class="text-left text-ellipsis">{{$data->title}}</td>
                                    <td class="text-center">{{$data->created}}</td>
                                    <td class="text-center">{{$data->last_craw}}</td>
                                    <td class="text-right">
                                        <span data-toggle="tooltip" data-placement="top" data-original-title="Remove this bitly">
                                            <button id="bit-{{$data->id}}" type="button" class="color-red btn btn-remove-song" onclick="removeBitly({{$data->id}})"><i class="ion-trash-a"></i></button>
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>

                    <div class="row">
                        <div class="col-md-6 ">
                            <div>

                                <?php
                                $info = str_replace('_START_', $bitlys->firstItem() != null ? $bitlys->firstItem() : '0', trans('label.title.sInfo'));
                                $info = str_replace('_END_', $bitlys->lastItem() != null ? $bitlys->lastItem() : '0', $info);
                                $info = str_replace('_TOTAL_', $bitlys->total(), $info);
                                echo $info;
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="pull-right disp-flex">
                                <select id="cbbLimit" name="limit" aria-controls="tbl-title" class="form-control input-sm">
                                    {!!$limitSelectbox!!}
                                </select>&nbsp;
                                <?php if (isset($bitlys)) { ?>
                                    {!!$bitlys->links()!!}
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('dialog.bitlyimport')
@include('dialog.report_campaign_rev')

@endsection

@section('script')
<script type="text/javascript">
    $(".btn-import-bitly").click(function(e) {
        e.preventDefault();
        $('#dialog_import_bitly').modal({
            backdrop: false
        });
    });
    $(".btn-save-bitly").click(function(e) {
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var form = $("#frmAdd");
        var formData = form.serialize();
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "bitly",
            data: formData,
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
                if (data.status === "success") {
                    var html = `<tr><td class="text-center">${data.data.id}</td>`;
                    @if($is_admin_music)
                    html += `<td class="text-center">${data.data.username}</td>`;
                    @endif
                    html += `<td class="text-center">${data.data.bitly_id}</td>
                                <td class="text-left text-ellipsis"><a target="_blank" href="${data.data.long_url}">${data.data.custom_bitlinks}</td>
                                <td class="text-center">0</td>
                                <td class="text-left text-ellipsis">${data.data.title}</td>
                                <td class="text-center">${data.data.created}</td>
                                <td class="text-center">${data.data.last_craw}</td>
                                <td class="text-right">
                                    <span data-toggle="tooltip" data-placement="top" data-original-title="Remove this bitly">
                                        <button type="button" class="color-red btn btn-remove-song" onclick="removeBitly(${data.data.id})"><i class="ion-trash-a"></i></button>
                                    </span>
                                </td>
                            </tr>
                            `;
                   
                    $('#tbl-bitly').prepend(html);
                    $('[data-toggle="tooltip"]').tooltip();
                }
            },
            error: function(data) {
                console.log('Error:', data);
                $this.html($this.data('original-text'));
            }
        });
    });
    function removeBitly(id) {
        var btn = $("#bit-" + id);
        var loadingText = '<i class="ion-load-c fa-spin"></i>';
        if (btn.html() !== loadingText) {
            btn.data('original-text', btn.html());
            btn.html(loadingText);
        }
        $.ajax({
            type: "PUT",
            url: "/bitly",
            data: {
                "id": id,
                "_token": $("input[name=_token]").val()
            },
            dataType: 'json',
            success: function(data) {
                btn.html(btn.data('original-text'));
                if (data.status == "success") {
                    btn.closest("tr").hide();
                }

                $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
    
    $(".btn-report-usd").click(function(e) {
        e.preventDefault();
        getChartMonth();

    });
    
    function getChartMonth(show=1){
        $("#report_campaign_rev_content").html("");
        $("#report_user_rev_content").html("");
        $("#report_user_rev_content_detail").html("");
        $("#report_campaign_rev_loading").show();
        $.ajax({
            type: "GET",
            url: "/getChartMonth",
            data: {},
            dataType: 'json',
            success: function(data) {
                console.log(data);
                var html = '<div class="row">';
                html += '<div class="col-md-12">';
                html += `<table class="table text-center" style="border-collapse: inherit"><thead><tr>
                            <th class='text-center'>#</th>
                            <th class='text-center'>Period</th>
                            <th class='text-center'>Bitly</th>
                            <th class='text-center'>Clicked</th>
                            <th class='text-center'>Amount</th>
                            <th class='text-center'>Moonaz Download</th>
                            <th class='text-center'>Moonaz Amount</th>
                            <th class='text-center'>Detail</th>`;
                var i = 0;
                $.each(data.report, function(key, value) {
                    i = i + 1;
                    html += `<tr><td scope="row">${i}</td>
                            <td><b>${value.period_text}</b></td>
                            <td>${value.count}</td>
                            <td>${ number_format(value.clicked, 0, '.', ',')}</td>
                            <td>$${ number_format(value.amount, 0, '.', ',')}</td>
                            <td>${ number_format(value.moonaz_download, 0, '.', ',')}</td>
                            <td>$${ number_format(value.moonaz_amount, 0, '.', ',')}</td>
                            <td><button id="btn${value.period}" type="button" onclick='getChartMonthDetail("${value.period}")' class="btn btn-outline-info btn-sm"> Detail</button></td></tr>`;
             
                });

                html += '</table></div></div>';
                if(!$("#report_user_rev_content_div").hasClass("col-md-6")){
                    $("#report_user_rev_content_div").addClass("col-md-6");
                }
                if(!$("#report_user_rev_content_detail_div").hasClass("col-md-6")){
                    $("#report_user_rev_content_detail_div").removeClass("col-md-8").addClass("col-md-6");
                }
                $("#report_campaign_rev_content").html(html);
                $("#report_campaign_rev_loading").hide();

                $("#dialog_report_campaign_rev_title").text("Report Bitly/Moonaz");
                $(".modal-dialog").addClass("modal-80");
                if(show==1){
                $('#dialog_report_campaign_rev').modal({
                    backdrop: false
                });
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
    
    function getChartMonthDetail(period){
        $("#report_user_rev_content").html("");
        $("#report_user_rev_content").html("");
        $("#report_user_rev_content_detail").html("");
        var $this = $("#btn"+period);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($this.html() !== loadingText) {
            $this.data('original-text', $this.html());
            $this.html(loadingText);
        }
        $.ajax({
            type: "GET",
            url: "/getChartMonthDetail",
            data: {
                'period': period
            },
            dataType: 'json',
            success: function(data) {
                $this.html($this.data('original-text'));
                console.log(data);
                var html = '';
                html += `<span class="m-l-15">Detail of <b>${data.period}</b></span><table class="table text-center" style="border-collapse: inherit"><thead><tr>
                        <th class='text-center'>#</th>
                        <th class='text-left'>User</th>
                        <th class='text-center'>Bitly</th>
                        <th class='text-center'>Click</th>
                        <th class='text-center'>$Bitly</th>
                        <th class='text-center'>Download</th>
                        <th class='text-center'>$Moonaz</th>
                        <th class='text-center'>$Total</th>
                        <th class='text-center'>Detail</th>`;
                var i = 0;
                $.each(data.report, function(key, value) {
                    i = i + 1;
                    html += `<tr><td scope="row">${i}</td>
                            <td class='text-left'>${value.username}</td>
                            <td>${value.count}</td>
                            <td>${value.clicked}</td>
                            <td>$${value.kpi}</td>
                            <td>${value.moonaz_download}</td>
                            <td>$${value.moonaz_amount}</td>
                            <td><b>$${(value.moonaz_amount+value.kpi).toFixed(2)}</b></td>
                            <td><button id="btn${value.username}" type="button" onclick='getChartUser("${value.period}","${value.username}")' class="btn btn-outline-info btn-sm"> Detail</button></td>
                    </tr>`;
                });
                html += '</table>';
                $("#report_user_rev_content").html(html);

            },
            error: function(data) {
                console.log(data);
            }
        });
    }   
    
    function getChartUser(period,username){
        $("#report_user_rev_content_detail").html("");
        var $this = $("#btn"+username);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
        if ($this.html() !== loadingText) {
            $this.data('original-text', $this.html());
            $this.html(loadingText);
        }
        $.ajax({
            type: "GET",
            url: "/getChartUser",
            data: {
                'period': period,'username':username
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $this.html($this.data('original-text'));
                var html = '';
                html += `<span class="m-l-15">Detail of <b>${data.username}</b><table class="table text-center" style="border-collapse: inherit"><thead><tr>
                            <th class='text-center'>#</th>
                            <th class='text-left'>User</th>
                            <th class='text-left'>Bitly</th>
                            <th class='text-center'>Created</th>
                            <th class='text-center'>Click</th>
                            <th class='text-center'>KPI</th>`;
                var i = 0;
                $.each(data.report, function(key, value) {
                    i = i + 1;
                    html += `<tr>
                                <td scope="row">${i}</td>
                                <td class='text-left'>${value.username}</td>
                                <td class='text-left'><a target="_blank" href="https://bit.ly/${value.custom_bitlinks}">${value.custom_bitlinks}</td>
                                <td>${value.created}</td>
                                <td>${number_format(value.clicked, 0, '.', ',')}</td>
                                <td><b>$${value.kpi}</b></td></tr>`;
                });
                html += '</table>';
                $("#report_user_rev_content_detail").html(html);

            },
            error: function(data) {
                console.log(data);
            }
        });
    }    
</script>
@endsection