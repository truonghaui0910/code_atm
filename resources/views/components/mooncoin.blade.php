@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">Mooncoin</span>
                @if($is_supper_admin)
                <button data-toggle="tooltip" data-placement="top" data-original-title="Add New" type="button" class="btn btn-outline-info btn-import-mooncoin m-r-5"><i class="fa fa-plus"></i></button>
                @endif
            </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Mooncoin</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<input id="token" type="hidden" name='_token' value='{{csrf_token()}}' />

<div class="row">
    <div class="col-lg-12">
        <div class="portlet">
            <div class="portlet-heading portlet-default">
                <h3 class="portlet-title">
                    Total MoonCoin per Year
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
                    <div class="form-group">
                        <label for="yearFilter">Select Year:</label>
                        <select id="yearFilter" class="form-control" style="width: 200px;">
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-8 position-relative">
                            <input type="text" id="customSearchBox" class="form-control position-absolute" placeholder="search..." style="right: 20px;width: 200px;display: none">
                            <table id="monthlyTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Mooncoin</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th>Content Description</th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                        <div class="col-md-4 position-relative">
                            <!--<input type="text" id="customSearchBox2" class="form-control position-absolute" placeholder="search..." style="right: 20px;width: 200px;display: none">-->
                            <table id="yearlyTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Total Mooncoin</th>
                                        <th>Year</th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="portlet">
            <div class="portlet-heading portlet-default">
                <h3 class="portlet-title">
                    Chart
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
                    <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-chart-line mr-2"></i>Mooncoin
                            </div>
                            <div class="card-body">
                                <canvas id="mooncoin_chart"></canvas>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('dialog.mooncoin.addmooncoin')
@include('dialog.mooncoin.addmooncoindesc')
@endsection

@section('script')
<script type="text/javascript">
    $('#customSearchBox').on('keyup', function () {
        monthlyTable.search(this.value).draw();
    });
    const monthlyTable = $('#monthlyTable').DataTable({
        dom: 'lrtip',
//        processing: true,
        serverSide: false,
//        paging: false,
        ajax: {
            url: '/api/mooncoins',
            data: function (d) {
                d.year = $('#yearFilter').val(); // Gửi giá trị năm tới API
            },
            dataSrc: 'monthly_data' // Nguồn dữ liệu là monthly_data
        },
        order: [],
        columns: [
            {data: 'username', name: 'username'},
            {data: 'mooncoin_value', name: 'mooncoin_value',className: 'text-center'},
            {data: 'month', name: 'month'},
            {data: 'year', name: 'year'},
            {data: 'content_description', name: 'content_description'}
        ],
        initComplete: function () {
            // Hiển thị ô tìm kiếm sau khi dữ liệu được load
            $('#customSearchBox').show();
        }
    });

    // DataTable cho bảng tổng theo năm
    const yearlyTable = $('#yearlyTable').DataTable({
        serverSide: false,
        paging: false,
        ajax: {
            url: '/api/mooncoins',
            data: function (d) {
                d.year = $('#yearFilter').val(); // Gửi giá trị năm tới API
            },
            dataSrc: function (json) {
                console.log(json);
                draw(json.chart_label,json.datasets);
                return json.yearly_totals; // Trả dữ liệu cho DataTables
            }
        },
        order: [],
        columns: [
            {data: 'username', name: 'username'},
            {data: 'total_mooncoin', name: 'total_mooncoin',className: 'text-center'},
            {data: 'year', name: 'year'}
        ]
    });

    function draw(label,datasets){
        drawBarChartsGroup('mooncoin_chart',label,datasets);  
    }

    // Reload cả hai bảng khi bấm nút Filter
    $('#yearFilter').on('change', function () {
        monthlyTable.ajax.reload();
        yearlyTable.ajax.reload();
    });

    $(".btn-import-mooncoin").click(function (e) {
        e.preventDefault();
        getMooncoinDesc();
        monthYear();
        $('#dialog_import_mooncoin').modal({
            backdrop: false
        });
    });
    
    function monthYear(){
            const currentYear = new Date().getFullYear();
            const currentMonth = new Date().getMonth() + 1; // Month is zero-based

            // Populate #moon_year with the current year and one year before
            const $yearSelect = $('#moon_year');
            $yearSelect.empty();
            for (let year = currentYear; year >= currentYear - 1; year--) {
                $yearSelect.append(`<option value="${year}">${year}</option>`);
            }
            $yearSelect.val(currentYear); // Set default value to current year

            // Populate #moon_month with 12 months
            const $monthSelect = $('#moon_month');
            $monthSelect.empty();
            for (let month = 1; month <= 12; month++) {
                const monthName = month.toString().padStart(2, '0'); // Format as "01", "02", ...
                $monthSelect.append(`<option value="${month}">${monthName}</option>`);
            }
            $monthSelect.val(currentMonth); // Set default value to current month
    }
    $(".select_date").change(function(){
        getMooncoinDesc();
    });
    function getMooncoinDesc(){
        var month = $("#moon_month").val();
        var year = $("#moon_year").val();
        $.ajax({
            type: "GET",
            url: "getDescMooncoin",
            data: {
                month:month,year:year
            },
            dataType: 'json',
            success: function (data) {
                console.log(data);
                                var option = "";
                $.each(data, function (k, v) {
                    option += `<option value='${v.id}'  data-content='${v.id} - ${v.content_description} <span class="font-12 text-muted font-italic"> ${v.moon_value} coin</span>'></option>`;
                });
                $("select.moon_desc").empty();
                $("select.moon_desc").html(option);
                $('select.moon_desc').selectpicker('refresh');

            },
            error: function (data) {
                $this.html($this.data('original-text'));
                console.log('Error:', data);

            }
        });
    }
    
    function showMooncoinDesc(){
    
        $("#dialog_import_mooncoin_desc").modal('show');
    }
    
    function saveMooncoin(btn){
        var $this = $(btn);
        var close = $this.attr("data-close");
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Saving...';
        if ($this.html() !== loadingText) {
            $this.data('original-text', $this.html());
            $this.html(loadingText);
        }
        var form = $("#formMoonCoin").serialize();
        $.ajax({
            type: "POST",
            url: "addMooncoin",
            data: form,
            dataType: 'json',
            success: function (data) {
                getMooncoinDesc();
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
                if(close=='true'){
                    $("#dialog_import_mooncoin").modal('hide');
                }

            },
            error: function (data) {
                $this.html($this.data('original-text'));
                console.log('Error:', data);

            }
        });
    }
    
    function saveMooncoinDesc(btn){
        var $this = $(btn);
        var close = $this.attr("data-close");
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Saving...';
        if ($this.html() !== loadingText) {
            $this.data('original-text', $this.html());
            $this.html(loadingText);
        }
        var form = $("#formMoonCoinDes").serialize();
        $.ajax({
            type: "POST",
            url: "addDescMooncoin",
            data: form,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.message);
                if(close=='true'){
                    $("#dialog_import_mooncoin_desc").modal('hide');
                }
                if(data.status=="success"){
                    getMooncoinDesc();
                }

            },
            error: function (data) {
                $this.html($this.data('original-text'));
                console.log('Error:', data);

            }
        });
    }

</script>
@endsection