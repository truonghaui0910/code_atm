@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">Money</span>
                    @if ($is_supper_admin)
                        <button data-toggle="tooltip" data-placement="top" data-original-title="Add New Expense" type="button"
                            class="btn btn-outline-info btn-import-expense m-r-5 disp-none menu-financial"><i class="fa fa-plus"></i></button>
                    @endif
                    <!--<button data-toggle="tooltip" data-placement="top" data-original-title="$ Report Bitly/Moonaz" type="button" class="btn btn-outline-info btn-report-usd m-r-5"><i class="fa  fa-usd"></i></button>-->
                    <!--<button data-toggle="tooltip" data-placement="top" data-original-title="$ Report Moonaz" type="button" class="btn btn-outline-info btn-report-usd-moonaz m-r-5"><i class="fa  fa-usd"></i></button>-->
                </h4>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                    <li class="breadcrumb-item active">Money</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    @if ($is_supper_admin)
    <div class="disp-none menu-financial">
        <div class="row">
            <div class="col-xl-12 ">
                <div class="portlet">
                    <div class="portlet-heading portlet-default">
                        <h3 class="portlet-title">

                        </h3>

                        <div class="clearfix"></div>
                    </div>
                    <div id="portlet-filter" class="panel-collapse collapse show">
                        <div class="portlet-body ">
                            <div id="date-filter" class="mx-auto">
                                <div class="w-full d-flex m-b-10 justify-content-center mw-80p mx-auto">
                                    <div class="inline-flex rounded-md shadow-sm" role="group">
                                        <button type="button" value="all"
                                            class="btn-filter-list-job px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-primary focus:z-10  focus:text-primary ">
                                            All
                                        </button>
                                        <button type="button" value="year"
                                            class="btn-filter-list-job px-4 py-2 text-sm font-medium text-gray-900 bg-white border-l-r-0 border-t-b-1 hover:bg-gray-100 hover:text-primary focus:z-10  focus:text-primary ">
                                            Year
                                        </button>
                                        <button type="button" value="month"
                                            class="btn-filter-list-job px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-primary focus:z-10  focus:text-primary ">
                                            Month
                                        </button>
                                    </div>


                                </div>
                                <div class="">
                                    <form id="filter-form">
                                        <select id="month" name="month"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            @php
                                                $months = [
                                                    1 => 'January',
                                                    2 => 'February',
                                                    3 => 'March',
                                                    4 => 'April',
                                                    5 => 'May',
                                                    6 => 'June',
                                                    7 => 'July',
                                                    8 => 'August',
                                                    9 => 'September',
                                                    10 => 'October',
                                                    11 => 'November',
                                                    12 => 'December',
                                                ];
                                            @endphp
                                            @for ($m = 1; $m <= 12; $m++)
                                                @if($month==$m)
                                                <option seleted value="{{ $m }}">{{ $months[$m] }}</option>
                                                @else
                                                <option value="{{ $m }}">{{ $months[$m] }}</option>
                                                @endif
                                            @endfor
                                        </select>

                                        <select id="year" name="year"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            @for ($y = date('Y') - 3; $y <= date('Y'); $y++)
                                                @if($year==$y)
                                                    <option selected value="{{ $y }}">{{ $y }}</option>
                                                @else
                                                    <option value="{{ $y }}">{{ $y }}</option>
                                                @endif
                                            @endfor
                                        </select>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 ">
                <div class="portlet">
                    <div class="portlet-heading portlet-default">
                        <h3 class="portlet-title">

                        </h3>
                        <div class="portlet-widgets">
                            <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div id="money-summary" class="panel-collapse collapse show">
                        <div class="portlet-body"></div>

                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="portlet">
                    <div class="portlet-heading portlet-default">
                        <h3 class="portlet-title">

                        </h3>
                        <div class="portlet-widgets">
                            <a id="reload-1" href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
                            <span class="divider"></span>
                            <a data-toggle="collapse" data-parent="#accordion1" href="#portlet-bar"><i
                                    class="ion-minus-round"></i></a>
                            <span class="divider"></span>
                            <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div id="portlet-bar" class="panel-collapse collapse show">
                        <div class="portlet-body ">
                            <div id="charts"></div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-xl-5">
                <div class="portlet">
                    <div class="portlet-heading portlet-default">
                        <h3 class="portlet-title">

                        </h3>
                        <div class="portlet-widgets">
                            <a id="reload-1" href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
                            <span class="divider"></span>
                            <a data-toggle="collapse" data-parent="#accordion1" href="#portlet-pie"><i
                                    class="ion-minus-round"></i></a>
                            <span class="divider"></span>
                            <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div id="portlet-pie" class="panel-collapse collapse show">
                        <div class="portlet-body ">
                            <div id="pie-charts"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    <div class="row disp-none menu-financial">
            <div class="col-xl-12 ">
                <div class="portlet">
                    <div class="portlet-heading portlet-default">
                        <h3 class="portlet-title">

                        </h3>
                        <div class="clearfix"></div>
                    </div>
                    <div id="portlet-month" class="panel-collapse collapse show">
                        <div class="portlet-body ">
                            <div id="bass_money">
                                <ul class="nav nav-tabs tabs-bordered">
                                    <li class="nav-item">
                                        <a href="#home-b1" data-toggle="tab" aria-expanded="true"
                                            class="nav-link active">
                                            Revenue - Expense
                                        </a>
                                    </li>

                                    <li class="nav-item tab-meta">
                                        <a href="#salary-2" data-toggle="tab" aria-expanded="false" class="nav-link">
                                            Salary
                                        </a>
                                    </li>
                                    <li class="nav-item tab-meta disp-none">
                                        <a href="#salary-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                            Salary
                                        </a>
                                    </li>

                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="home-b1">
                                        <div class="disp-flex justify-content-center">
                                            <div class="table-container m-r-10">
                                                <h6>Revenue</h6>
                                                <table id="moneyInTable" class="table">
                                                </table>
                                            </div>
                                            <div class="table-container">
                                                <h6>Expense</h6>
                                                <table id="moneyOutTable" class="table">
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="salary-2">
                                        <div class="disp-flex justify-content-center">
                                            <div class="table-container m-r-10">
<!--                                                <h6>Revenue</h6>-->
                                                <table id="salary_table" class="table">
                                                </table>
                                            </div>
  
                                        </div>
                                    </div>
                                    <div class="tab-pane fade tab-salart" id="salary-b1">
                                        <div id="salary_montly" class="modal-body">
                                            <div class="fv-row mb-10 text-center" show-id="4">
                                                <label class="fs-6 fw-semibold mb-2">Profit</label>
                                                <div class="d-flex flex-column text-center">
                                                    <div class="d-flex align-items-start justify-content-center mb-7">
                                                        <span class="fw-bold fs-4 mt-1 me-2">$</span>
                                                        <span id="profit_before" class="fw-bold fs-3x">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mx-auto mw-50p">
                                                <div class="col-lg-6 col-md-6 m-b-20">
                                                    <div class="widget-bg-color-icon card-custom widget-user">
                                                        <img src="images/avatar/chrismusic.jpg"
                                                            class="rounded-circle w-72px h-72px">
                                                        <div class="text-right">
                                                            <h3 class="text-dark m-t-10">$<b
                                                                    class="couter chrismusic-salary">0</b>
                                                            </h3>
                                                            <p class="text-muted mb-0">Chris Mohoney</p>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6">
                                                    <div class="widget-bg-color-icon card-custom widget-user">
                                                        <img src="images/avatar/jamesmusic.jpg"
                                                            class="rounded-circle w-72px h-72px">
                                                        <div class="text-right">
                                                            <h3 class="text-dark m-t-10">$<b
                                                                    class="couter jamesmusic-salary">0</b>
                                                            </h3>
                                                            <p class="text-muted mb-0">James Dunn</p>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="widget-bg-color-icon card-custom widget-user">
                                                        <img src="images/avatar/hoadev.jpg"
                                                            class="rounded-circle w-72px h-72px">
                                                        <div class="text-right">
                                                            <h3 class="text-dark m-t-10">$<b
                                                                    class="couter hoadev-salary">0</b>
                                                            </h3>
                                                            <p class="text-muted mb-0">Hòa Bùi</p>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="widget-bg-color-icon card-custom widget-user">
                                                        <img src="images/avatar/sangmusic.jpg"
                                                            class="rounded-circle img-cover w-72px h-72px">
                                                        <div class="text-right">
                                                            <h3 class="text-dark m-t-10">$<b
                                                                    class="couter sangmusic-salary">0</b>
                                                            </h3>
                                                            <p class="text-muted mb-0">Sáng Nguyễn</p>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="widget-bg-color-icon card-custom widget-user">
                                                        <img src="images/avatar/truongpv.jpg"
                                                            class="rounded-circle w-72px h-72px">
                                                        <div class="text-right">
                                                            <h3 class="text-dark m-t-10">$<b
                                                                    class="couter truongpv-salary">0</b>
                                                            </h3>
                                                            <p class="text-muted mb-0">Trường Phạm</p>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    @endif

    <div class="row disp-none menu-money">
        <div class="col-xl-12 ">
            <div class="portlet">
                <div class="portlet-heading portlet-default">
                    <h3 class="portlet-title">

                    </h3>
                    <div class="clearfix"></div>
                </div>
                <div id="portlet-month" class="panel-collapse collapse show">
                    <div class="portlet-body ">
                        <div id="bass_money">
                            <ul class="nav nav-tabs tabs-bordered">
                                <li class="nav-item">
                                    <a href="#promo-b1" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                        Promos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#claim-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        Claims Orchard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#claim-6-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        Claims Indiy
                                    </a>
                                </li>
<!--                                <li class="nav-item">
                                    <a href="#claim-5-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        Claims Adrev
                                    </a>
                                </li>
                                <li class="nav-item tab-meta">
                                    <a href="#meta-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        Moonaz
                                    </a>
                                </li>-->
                                <li class="nav-item tab-epic">
                                    <a href="#epid-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        Epidemic
                                    </a>
                                </li>
                                <li class="nav-item tab-meta">
                                    <a href="#summary-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        Summary
                                    </a>
                                </li>

                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="promo-b1">
                                    <div id="promo_monthly" class="modal-body"></div>
                                    <div class="row">
                                        <div id="div_promo_monthly_detail" class="col-md-4">
                                            <div id="promo_monthly_detail"></div>

                                        </div>
                                        <div id="div_promo_user_detail" class="col-md-8">
                                            <div id="promo_user_detail"></div>

                                        </div>

                                    </div>

                                </div>
                                <div class="tab-pane fade" id="claim-b1">
                                    <div id="claim_2_monthly" class="modal-body"></div>
                                    <div class="row">
                                        <div id="div_claim_2_monthly_detail" class="col-md-4">
                                            <div id="claim_2_monthly_detail"></div>

                                        </div>
                                        <div id="div_claim_2_user_detail" class="col-md-8">
                                            <div id="claim_2_user_detail"></div>
                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane fade" id="claim-6-b1">
                                    <div id="claim_6_monthly" class="modal-body"></div>
                                    <div class="row">
                                        <div id="div_claim_6_monthly_detail" class="col-md-4">
                                            <div id="claim_6_monthly_detail"></div>

                                        </div>
                                        <div id="div_claim_6_user_detail" class="col-md-8">
                                            <div id="claim_6_user_detail"></div>
                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane fade" id="claim-5-b1">
                                    <div id="claim_5_monthly" class="modal-body"></div>
                                    <div class="row">
                                        <div id="div_claim_5_monthly_detail" class="col-md-4">
                                            <div id="claim_5_monthly_detail"></div>

                                        </div>
                                        <div id="div_claim_5_user_detail" class="col-md-8">
                                            <div id="claim_5_user_detail"></div>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane fade tab-meta" id="meta-b1">
                                    <div id="moonaz_monthly" class="modal-body"></div>
                                    <div class="row">
                                        <div id="div_moonaz_monthly_detail" class="col-md-6">
                                            <div id="moonaz_monthly_detail"></div>

                                        </div>
                                        <div id="div_moonaz_user_detail" class="col-md-6">
                                            <div id="moonaz_user_detail"></div>

                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane fade tab-epid" id="epid-b1">
                                    @if($is_supper_admin)
                                    <button class="btn btn-sm btn-secondary btn-epid-summary" >Summary</button>
                                    @endif
                                    <div id="epid_monthly" class="modal-body"></div>
                                    <div class="row">
                                        <div id="div_epid_monthly_detail" class="col-md-4">
                                            <div id="epid_monthly_detail"></div>

                                        </div>
                                        <div id="div_epid_user_detail" class="col-md-8">
                                            <div id="epid_user_detail"></div>

                                        </div>

                                    </div>
                                </div>

                                <div class="tab-pane fade tab-summary" id="summary-b1">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group row">
                                                <!--                                            <label class="col-12 col-form-label">Period</label>-->
                                                <div class="col-12">
                                                    <select id="period_summary" name="period_summary"
                                                        class="select2_multiple form-control">
                                                        {!! $month_select_v2 !!}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div id="summary-data_loading" class="disp-none" style="text-align: center;"><i
                                            class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
                                    <div id="summary-data"></div>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dialog.importexpense')
    @include('dialog.money_summary')
@endsection

@section('script')
    <script type="text/javascript">
        $(".btn-epid-summary").click(function(){
            $('#table-body').empty();
            $('#dialog_money_summary').modal({
                backdrop: false
            });
            $.ajax({
                type: "GET",
                url: "/epidSummary",
                data: {},
                dataType: 'json',
                success: function(data) {
                    logger("epidSummary", data);
            // Get unique owners sorted alphabetically
            const owners = [...new Set(data.map(item => item.owner))].sort();
            
            // Get unique periods sorted in descending order
            const periods = [...new Set(data.map(item => item.period))].sort().reverse();

            // Add owner columns to header
            owners.forEach(owner => {
                $('#header-row').append(`
                    <th class="text-center">
                        ${owner}<br>
                        <small class="text-muted font-13">Revenue / Growth%</small>
                    </th>
                `);
            });

            // Create rows for each period
            periods.forEach(period => {
                let row = `<tr><td>${formatPeriod(period)}</td>`;
                
                owners.forEach(owner => {
                    const currentData = data.find(item => 
                        item.period === period && item.owner === owner
                    );
                    
                    const previousPeriod = getPreviousPeriod(period);
                    const previousData = data.find(item => 
                        item.period === previousPeriod && item.owner === owner
                    );
                    
                    let growth = '';
                    if (currentData && previousData) {
                        const growthRate = previousData.revenue>0?((currentData.revenue - previousData.revenue) / previousData.revenue * 100):0;

                        const growthClass = growthRate >= 0 ? 'color-green' : 'color-red';
                        growth = `<span class="${growthClass}">(${growthRate.toFixed(0)}%)</span>`;
                    }
                    
                    const revenue = currentData ? '$'+currentData.revenue.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0}) : '-';
                    row += `<td class="text-center">${revenue}<br>${growth}</td>`;
                });
                
                row += '</tr>';
                $('#table-body').append(row);
            });

                },
                error: function(data) {
                    console.log(data);
                }
            });
            
        });

        // Helper function to get previous period
        function getPreviousPeriod(period) {
            let year = parseInt(period.substring(0, 4));
            let month = parseInt(period.substring(4, 6));

            month--;
            if (month === 0) {
                month = 12;
                year--;
            }

            return `${year}${month.toString().padStart(2, '0')}`;
        }
        
        
        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = currentDate.getMonth() + 1;
        var year = {{$year}};
        var month = {{$month}};
        var filterType = "month";
        $(document).ready(function() {
            $('#month').val(month);
            $('#year').val(year);
            $('.btn-filter-list-job[value="month"]').addClass("text-primary");
            $('.btn-filter-list-job').click(function() {
                filterType = $(this).val();
                $('.btn-filter-list-job').removeClass("text-primary");
                $(this).addClass("text-primary");
                if (filterType == "all") {
                    $('#month').hide();
                    $('#year').hide();
                } else if (filterType == "year") {
                    $('#month').hide();
                    $('#year').show();
                } else if (filterType == "month") {
                    $('#month').show();
                    $('#year').show();
                }
                updateMoneyChart(year, month, filterType);
            });
            $('#month').change(function() {
                month = $(this).val();
                updateMoneyChart(year, month, filterType);
            });
            $('#year').change(function() {
                year = $(this).val();
                updateMoneyChart(year, month, filterType);
            });
            console.log(location.href);
            if (location.href.includes('financial')) {
                updateMoneyChart(year, month, filterType);
                $(".menu-financial").show();
            } else {
                $(".menu-money").show();
                getReportRev();
                getReportTabSummary();
            }
        });
        $("#period_summary").change(function() {
            getReportTabSummary();
        });

        function getReportRev() {
            $("#report_campaign_rev_content").html("");
            $("#report_user_rev_content").html("");
            $("#report_user_rev_content_detail").html("");

            $.ajax({
                type: "GET",
                url: "/getReportAll",
                data: {},
                dataType: 'json',
                success: function(data) {
                    logger("getReportAll", data);
                    genPromosHtml(data);
                    genClaimHtml(data, 2);
                    genClaimHtml(data, 5);
                    genClaimHtml(data, 6);
                    genMoonazHtml(data);
                    genEpidHtml(data);
                    genSummmaryHtml(data);
                    $('[data-plugin="switchery"]').each(function(idx, obj) {
                        new Switchery($(this)[0], $(this).data());
                    });
                    $('[data-toggle="tooltip"]').tooltip();

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function getReportTabSummary() {
            $("#summary-data").html("");
            var period = $("#period_summary").val();
            $("#summary-data_loading").show();
            $.ajax({
                type: "GET",
                url: "/getReportSummary",
                data: {
                    period: period
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $("#summary-data_loading").hide();
                    genSummmaryHtml(data);

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function genPromosHtml(data) {
            var html = '<div class="row">';
            html += '<div class="col-md-12">';
            @if ($is_supper_admin)
                html += `<table class="table text-center" style="border-collapse: inherit"><thead><tr>
                            <th class='text-center'>#</th>
                            <th class='text-center'>Period</th>
                            <th class='text-center'>Campaign</th>
                            <th class='text-center'>Amount</th>
                            <th class='text-center'>Customer Paid</th>
                            <th class='text-center'>Expense</th>
                            <th class='text-center'>KPI</th>
                            <th class='text-center'>Debt</th>
                            <th class='text-center'>Current Profit</th>
                            <th class='text-center'>Last Profit</th>
                            <th class='text-center'>Pay</th>
                            <th class='text-center'>Show</th>
                            <th class='text-center'>Detail</th>`;
            @else
                html += `<table class="table text-center" style="border-collapse: inherit"><thead><tr>
                            <th class='text-center'>#</th>
                            <th class='text-center'>Period</th>
                            <th class='text-center'>Campaign</th>
                            <th class='text-center'>KPI</th>
                            <th class='text-center'>Status Pay</th>
                            <th class='text-center'>Detail</th>`;
            @endif
            var i = 0;
            $.each(data.promos, function(key, value) {
                i = i + 1;
                var evenOdd = "even";
                if (i % 2 == 0) {
                    evenOdd = "odd";
                }
                var checked = "";
                if (value.is_paid == 1) {
                    checked = "checked";
                }

                @if ($is_supper_admin)
                    html += `<tr class="${evenOdd}"><td scope="row">${i}</td>
                            <td><b>${value.period_text}</b></td>
                            <td>${value.count}</td>
                            <td data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="Promo $${ number_format(value.amount - value.amount_submission, 0, '.', ',')}<br>Sub $${ number_format(value.amount_submission, 0, '.', ',')}">$${ number_format(value.amount, 0, '.', ',')}</td>
                            <td>$${number_format(value.paid, 0, '.', ',')}</td>
                            <td>$${number_format(value.spent, 0, '.', ',')}</td>
                            <td>$${number_format(value.payment, 0, '.', ',')}</td>
                            <td>$${number_format(value.debt, 0, '.', ',')}</td>
                            <td>$${number_format(value.curr_profit, 0, '.', ',')}</td>
                            <td>$${number_format(value.last_profit, 0, '.', ',')}</td>
                            <td>
                                <div class="checkbox checkbox-primary tbl-chk checkbox-circle">
                                    <input id="chk_pay_${i}" class="chk_status_pay" type="checkbox" onclick="changeStatusPay('${value.period}',1,'pay')" ${checked}>
                                    <label for="chk_pay_${i}" class="m-b-18 p-l-0"></label>
                                </div>
                            </td>
                            <td>`;
                    html += genStatusShow(value.period, 1, value.is_show);
                    html +=
                        `</td> 
                            <td><button id="btn${value.period}" type="button" onclick='promoMonthlyDetail("${value.period}")' class="btn btn-outline-info btn-sm"> Detail</button></td></tr>`;
                @else
                    if (value.is_show == 1) {
                        html +=
                            `<tr class="${evenOdd}"><td scope="row">${i}</td>
                            <td><b>${value.period_text}</b></td>
                            <td>${value.count}</td>
                            <td>$${number_format(value.payment, 0, '.', ',')}</td>
                            <td>
                                <div class="checkbox checkbox-primary tbl-chk checkbox-circle">
                                    <input id="promo_${i}" class="chk_status_pay" type="checkbox"  ${checked} @if (!$is_admin_music) disabled @endif>
                                    <label for="promo_${i}" class="m-b-18 p-l-0"></label>
                                </div>
                            </td>  
                            <td><button id="btn${value.period}" type="button" onclick='promoMonthlyDetail("${value.period}")' class="btn btn-outline-info btn-sm"> Detail</button></td></tr>`;
                    }
                @endif
            });

            html += '</table></div></div>';

            $("#promo_monthly").html(html);
            $('[data-toggle="tooltip"]').tooltip();
        }

        function genClaimHtml(data, type) {
            var html = '<div class="row">';
            html += '<div class="col-md-12">';
            @if ($is_supper_admin)
                html += `<table class="table text-center" style="border-collapse: inherit"><thead><tr>
                            <th class='text-center'>#</th>
                            <th class='text-center'>Period</th>
                            <th class='text-center'>Claim</th>
                            <th class='text-center'>Revenue</th>
                            <th class='text-center'>Profit</th>
                            <th class='text-center'>Bass Revenue</th>
                            <th class='text-center'>Dev Revenue</th>
                            <th class='text-center'>Pay</th>
                            <th class='text-center'>Show</th>    
                            <th class='text-center'>Detail</th>`;
            @else
                html += `<table class="table text-center" style="border-collapse: inherit"><thead><tr>
            <th class='text-center'>#</th>
            <th class='text-center'>Period</th>
            <th class='text-center'>Claim</th>
            <th class='text-center'>Bass Revenue</th>
            <th class='text-center'>Dev Revenue</th>
            <th class='text-center'>Status Pay</th>                            
            <th class='text-center'>Detail</th>`;
            @endif
            var i = 0;
            var datas = data.claims;
            if (type == 5) {
                datas = data.claims_adrev;
            }else if(type == 6){
                datas = data.claims_indiy;
            }
            $.each(datas, function(key, value) {
                i = i + 1;
                var evenOdd = "even";
                if (i % 2 == 0) {
                    evenOdd = "odd";
                }
                var checked = "";
                if (value.paid == 1) {
                    checked = "checked";
                }

                @if ($is_supper_admin)
                    html += `<tr class="${evenOdd}"><td scope="row">${i}</td>
                    <td><b>${value.period_text}</b></td>
                    <td>${ number_format(value.claim, 0, '.', ',')}</td>
                    <td>$${ number_format(value.revenue, 0, '.', ',')}</td>
                    <td>$${ number_format(value.profit, 0, '.', ',')}</td>
                    <td>$${ number_format(value.bass_revenue, 0, '.', ',')}</td>
                    <td >$${ number_format(value.dev_revenue, 0, '.', ',')}</td>
                    <td>
                        <div class="checkbox checkbox-primary tbl-chk checkbox-circle">
                            <input id="claim_${type}_${i}" class="chk_status_pay" type="checkbox" onclick="changeStatusPay('${value.period}',${type},'pay')" ${checked} @if (!$is_admin_music) disabled @endif>
                            <label for="claim_${type}_${i}" class="m-b-18 p-l-0"></label>
                        </div>
                    </td>
                    <td>`;
                    html += genStatusShow(value.period, type, value.show);
                    html +=
                        `</td> 
                    <td><button id="claim_${type}_${value.period}" type="button" onclick='claimMonthlyDetail("${value.period}",${type})' class="btn btn-outline-info btn-sm"> Detail</button></td></tr>`;
                @else
                    if (value.show == 1) {
                        html +=
                            `<tr class="${evenOdd}"><td scope="row">${i}</td>
                    <td><b>${value.period_text}</b></td>
                    <td>${ number_format(value.claim, 0, '.', ',')}</td>
                    <td>$${ number_format(value.bass_revenue, 0, '.', ',')}</td>
                    <td>$${ number_format(value.dev_revenue, 0, '.', ',')}</td>
                    <td>
                        <div class="checkbox checkbox-primary tbl-chk checkbox-circle">
                            <input id="claim_${type}_${i}" class="chk_status_pay" type="checkbox" onclick="changeStatusPay('${value.period}',${type},'pay')" ${checked} @if (!$is_admin_music) disabled @endif>
                            <label for="claim_${type}_${i}" class="m-b-18 p-l-0"></label>
                        </div>
                    </td>    
                    <td><button id="claim_${type}_${value.period}" type="button" onclick='claimMonthlyDetail("${value.period}",${type})' class="btn btn-outline-info btn-sm"> Detail</button></td></tr>`;
                    }
                @endif

            });

            html += '</table></div></div>';

            $(`#claim_${type}_monthly`).html(html);
        }

        function genMoonazHtml(data) {
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
                            <th class='text-center'>Pay</th>    
                            @if ($is_supper_admin)                                    
                            <th class='text-center'>Show</th>
                            @endif
                            <th class='text-center'>Detail</th>`;
            var i = 0;
            var type = 3; //moonaz
            $.each(data.moonaz, function(key, value) {
                i = i + 1;
                var evenOdd = "even";
                if (i % 2 == 0) {
                    evenOdd = "odd";
                }
                var checked = "";
                if (value.paid == 1) {
                    checked = "checked";
                }
                var show = value.show;

                @if ($is_supper_admin)
                    show = 1;
                @endif
                if (show == 1) {
                    html +=
                        `<tr class="${evenOdd}"><td scope="row">${i}</td>
                            <td><b>${value.period_text}</b></td>
                            <td>${value.count}</td>
                            <td>${ number_format(value.clicked, 0, '.', ',')}</td>
                            <td>$${ number_format(value.amount, 0, '.', ',')}</td>
                            <td>${ number_format(value.moonaz_download, 0, '.', ',')}</td>
                            <td>$${ number_format(value.moonaz_amount, 0, '.', ',')}</td>
                            <td>
                                <div class="checkbox checkbox-primary tbl-chk checkbox-circle">
                                <input id="moonaz_${type}_${i}" class="chk_status_pay" type="checkbox" onclick="changeStatusPay('${value.period}',${type},'pay')" ${checked} @if (!$is_admin_music) disabled @endif>
                                <label for="moonaz_${type}_${i}" class="m-b-18 p-l-0"></label>
                                </div>
                            </td>
                            @if ($is_supper_admin)
                            <td>`;
                    html += genStatusShow(value.period, type, value.show);
                    html +=
                        `</td>
                            @endif
                            <td><button id="moonaz_${value.period}" type="button" onclick='moonazMonthlyDetail("${value.period}")' class="btn btn-outline-info btn-sm"> Detail</button></td></tr>`;
                }

            });

            html += '</table></div></div>';
            $("#moonaz_monthly").html(html);
        }

        function genEpidHtml(data) {
            var html = '<div class="row">';
            html += '<div class="col-md-12">';
            html += `<table class="table text-center" style="border-collapse: inherit"><thead><tr>
                            <th class='text-center'>#</th>
                            <th class='text-center'>Period</th>
                            @if ($is_supper_admin)    
                            <th class='text-center'>Revenue</th>
                            <th class='text-center'>Profit</th>
                            @endif
                            <th class='text-center'>Bass Revenue</th>
                            <th class='text-center'>Pay</th>
                            @if ($is_supper_admin)
                            <th class='text-center'>Show</th>        
                            @endif       
                            <th class='text-center'>Detail</th>`;
            var i = 0;
            var type = 4; //epid
            $.each(data.epids, function(key, value) {
                i = i + 1;
                var evenOdd = "even";
                if (i % 2 == 0) {
                    evenOdd = "odd";
                }
                var checked = "";
                if (value.paid == 1) {
                    checked = "checked";
                }
                var show = value.show;
                @if ($is_supper_admin)
                    show = 1;
                @endif
                if (show == 1) {
                    html +=
                        `<tr class="${evenOdd}"><td scope="row">${i}</td>
                            <td><b>${value.period_text}</b></td>
                                @if ($is_supper_admin)    
                            <td>$${ number_format(value.revenue, 0, '.', ',')}</td>
                            <td>$${ number_format(value.profit, 0, '.', ',')}</td>
                                @endif
                            <td>$${ number_format(value.bass_revenue, 0, '.', ',')}</td>
                            <td>
                                <div class="checkbox checkbox-primary tbl-chk checkbox-circle">
                                    <input id="epid_${i}" class="chk_status_pay" type="checkbox" onclick="changeStatusPay('${value.period}',4,'pay')" ${checked} @if (!$is_admin_music) disabled @endif>
                                    <label for="epid_${i}" class="m-b-18 p-l-0"></label>
                                </div>
                            </td>    
                            @if ($is_supper_admin)
                            <td>`;
                    html += genStatusShow(value.period, type, value.show);
                    html +=
                        `</td>        
                            @endif   
                            <td><button id="epid_${value.period}" type="button" onclick='epidMonthlyDetail("${value.period}")' class="btn btn-outline-info btn-sm"> Detail</button></td></tr>`;
                }
            });

            html += '</table></div></div>';

            $("#epid_monthly").html(html);
        }

        function genSummmaryHtml(data) {
            var html = '';
            html += `<table class="table text-center" style="border-collapse: inherit"><thead><tr>
                        <th class='text-center'>#</th>
                        <th class='text-left'>User</th>
                        <th class='text-center'>Role</th>
                        <th class='text-center'>Money</th>`;
            var i = 0;
            $.each(data.summary, function(key, value) {
                i = i + 1;
                var evenOdd = "even";
                if (i % 2 == 0) {
                    evenOdd = "odd";
                }
                html += `<tr class="${evenOdd}"><td scope="row">${i}</td>
                            <td class='text-left'>${value.user_name}</td>
                            <td>${value.role}</td>
                            <td>$${ number_format(value.summary, 0, '.', ',')}</td>
                    </tr>`;
            });
            html += '</table>';
            $("#summary-data").html(html);
        }

        function genStatusShow(period, type, isShow) {
            var show = "";
            if (isShow === 1) {
                show = 'checked';
            }
            return `<input type="checkbox" ${show} onchange="changeStatusPay('${period}',${type},'show')" data-plugin="switchery" data-color="#00b19d" data-size="small"/>`;

        }

        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        function formatPeriod(period) {
            const year = period.substring(0, 4);
            const month = monthNames[parseInt(period.substring(4, 6)) - 1];
            return `${month}-${year}`;
        }


        @if($is_supper_admin)
            //        function generateSalaryTable(data){
//            data.sort((a, b) => b.period - a.period);
//            var table = `<thead><tr><th>Month</th>
//                        <th>Revenue</th>
//                        <th>Expense</th>
//                        <th>Profit</th>
//                        <th>Chris Mohoney</th>
//                        <th>James Dunn</th>
//                        <th>Hòa Bùi</th>
//                        <th>Sáng Nguyễn</th>
//                        <th>Trường Phạm</th></tr></thead>`;
//            $.each(data, function(index, value) {
//              if(value.period>=202406){  
//              table+=`<tr class="${index % 2 === 0 ? 'even' : 'odd'}">
//                        <td class="text-center">${formatPeriod(value.period)}</td>
//                        <td class="text-center">$${number_format(value.m_in, 0, '.', ',')}</td>
//                        <td class="text-center">$${number_format(value.m_out, 0, '.', ',')}</td>
//                        <td class="text-center">$${number_format(value.profit, 0, '.', ',')}</td>
//                        <td class="text-center">$${number_format(value.chrismusic, 0, '.', ',')}</td>
//                        <td class="text-center">$${number_format(value.jamesmusic, 0, '.', ',')}</td>
//                        <td class="text-center">$${number_format(value.hoadev, 0, '.', ',')}</td>
//                        <td class="text-center">$${number_format(value.sangmusic, 0, '.', ',')}</td>
//                        <td class="text-center">$${number_format(value.truongpv, 0, '.', ',')}</td>
//                    </tr>`;
//                }
//            });
//            return table;            
//        }
        function generateSalaryTable(data) {
            data.sort((a, b) => b.period - a.period);
            var table = `<thead><tr><th>Month</th>
                        <th>Revenue</th>
                        <th>Expense</th>
                        <th>Profit</th>
                        <th>Chris Mohoney</th>
                        <th>James Dunn</th>
                        <th>Hòa Bùi</th>
                        <th>Sáng Nguyễn</th>
                        <th>Trường Phạm</th></tr></thead>`;

            $.each(data, function(index, value) {
                if (value.period >= 202406) {  
                    let prev = data[index + 1]; // Lấy tháng trước (nếu có)

                    let revenueChange = prev ? calcGrowth(value.m_in, prev.m_in) : "";
                    let expenseChange = prev ? calcGrowth(value.m_out, prev.m_out, 2 ) : "";
                    let profitChange = prev ? calcGrowth(value.profit, prev.profit) : "";

                    table += `<tr class="${index % 2 === 0 ? 'even' : 'odd'}">
                                <td class="text-center">${formatPeriod(value.period)}</td>
                                <td class="text-center">$${number_format(value.m_in, 0, '.', ',')} ${revenueChange}</td>
                                <td class="text-center">$${number_format(value.m_out, 0, '.', ',')} ${expenseChange}</td>
                                <td class="text-center">$${number_format(value.profit, 0, '.', ',')} ${profitChange}</td>
                                <td class="text-center">$${number_format(value.chrismusic, 0, '.', ',')}</td>
                                <td class="text-center">$${number_format(value.jamesmusic, 0, '.', ',')}</td>
                                <td class="text-center">$${number_format(value.hoadev, 0, '.', ',')}</td>
                                <td class="text-center">$${number_format(value.sangmusic, 0, '.', ',')}</td>
                                <td class="text-center">$${number_format(value.truongpv, 0, '.', ',')}</td>
                            </tr>`;
                }
            });
            return table;            
        }
        //type=1 thì tăng là xanh,đỏ là giảm, type khác thì ngược lại
        function calcGrowth(current, previous, type = 1) {
            if (previous === 0 || previous === undefined) return ""; // Tránh chia cho 0
            let change = ((current - previous) / previous) * 100;
            let className = "badge-success";
            if(type==1){
                className = change >= 0  ? "badge-success" : "badge-danger";
            }else{
                className = change < 0  ? "badge-success" : "badge-danger";
            }
            let icon = change >= 0  ? "ion-arrow-up-b" : "ion-arrow-down-b";
            return `<span class="badge ${className} ml-2"><i class="${icon}"></i> ${Math.abs(change).toFixed(1)}%</span>`;
        }
        
        function generateTable(data, type) {
            let vendors = [...new Set(data.map(item => item.vendor))];
            let periods = [...new Set(data.map(item => item.period))].sort((a, b) => b - a);

            let tableData = periods.map(period => {
                let row = { period: formatPeriod(period), total: 0 };
                vendors.forEach(vendor => {
                    let item = data.find(d => d.period == period && d.vendor == vendor);
                    row[vendor] = item ? item[type] : 0;
                    row.total += row[vendor];
                });
                return row;
            });

            if (type === 'money_in') {
                vendors = vendors.filter(vendor => tableData.some(row => row[vendor] > 0));
            } else {
                vendors = vendors.filter(vendor => tableData.some(row => row[vendor] > 0));
            }

            let table = `<thead><tr><th>Month</th>${vendors.map(vendor => `<th>${vendor}</th>`).join('')}<th>Total</th></tr></thead>`;
            table += `<tbody>${tableData.map((row,index) => `<tr class="${index % 2 === 0 ? 'even' : 'odd'}"><td>${row.period}</td>${vendors.map(vendor => `<td>$${number_format(row[vendor], 0, '.', ',')}</td>`).join('')}<td><b>$${number_format(row.total, 0, '.', ',')}</b></td></tr>`).join('')}</tbody>`;

            return table;
        }

        function updateMoneyChart(year, month, type) {
            $("#charts").html("");
            var $portlet = $(".portlet");
            $portlet.append('<div class="panel-disabled"><div class="loader-1"></div></div>');
            var $pd = $portlet.find('.panel-disabled');
            $.ajax({
                type: "GET",
                url: "/updateMoneyChart",
                data: {
                    year: year,
                    month: month,
                    type: type
                },
                dataType: 'json',
                success: function(data) {
                    logger('updateMoneyChart', data);
                    $pd.remove();
                    var html =
                        '<div class="row"><div class="col-md-12 data_user"></div><div class="col-md-12"><canvas id="chart-money"></canvas></div></div>';
                    $("#charts").html(html);
                    var html2 =
                        '<div class="row"><div class="col-md-12"></div><div class="col-md-12"><canvas id="pie-chart"></canvas></div></div>';
                    $("#pie-charts").html(html2);
                    $("#dialog-view-loading").hide();
                    var label = new Array();
                    var dataIn = new Array();
                    var dataOut = new Array();
                    var colors = ['#0071d1', '#d94d6c', '#e6990e', '#912fc0', '#d94d6c', '#0f8071', '#f270b9',
                        '#f2c428', '#2fa5cb', '#5b54d5', '#00b4a2', '#eb3c97'
                    ];
                    var formatDate = '';
                    var datasets = new Array();
                    var datasetsPie = new Array();

                    var footer = new Array();
                    var footer2 = new Array();
                    $.each(data.bar_charts, function(key, value) {
                        formatDate = (value.period.toString().substring(0, 4) + "/" + value.period
                            .toString().substring(4, 6));
                        label.push(formatDate);
                        dataIn.push(value.m_in);
                        dataOut.push(value.m_out);
                        footer.push(value.detail);
                        footer2.push(value.detail_out);
                    });
                    var dataset = {
                        currency: "$",
                        label: "Revenue",
                        data: dataIn,
                        fill: false,
                        footer: footer,
                        borderColor: colors[0],
                        backgroundColor: colors[0],
                        borderWidth: 1
                    };
                    datasets.push(dataset);

                    var dataset2 = {
                        currency: "$",
                        label: "Expense",
                        data: dataOut,
                        fill: false,
                        footer: footer2,
                        borderColor: colors[1],
                        backgroundColor: colors[1],
                        borderWidth: 1
                    };
                    datasets.push(dataset2);
                    drawBarChartsGroup('chart-money', label, datasets);
                    var labelPie = new Array();
                    var dataPie = new Array();
                    //                    var colorPie = ['#0071d1', '#d94d6c', '#e6990e', '#912fc0'];
                    var colorPie = ["#1f77b4", "#ff7f0e", "#2ca02c", "#d62728", "#9467bd", "#8c564b", "#e377c2",
                        "#7f7f7f", "#bcbd22", "#17becf"
                    ];
                    //                        var colorPie = ['#0071d1', '#d94d6c', '#e6990e', '#912fc0', '#9467bd', '#0f8071', '#f270b9',
                    //                        '#f2c428', '#2fa5cb', '#5b54d5', '#00b4a2', '#eb3c97'
                    //                    ];
                    $.each(data.pie_charts, function(key, value) {
                        labelPie.push(value.vendor);
                        dataPie.push(value.m_in);
                    });
                    var datasetPie = {
                        currency: "$",
                        data: dataPie,
                        backgroundColor: colorPie,
                        borderWidth: 1
                    };
                    datasetsPie.push(datasetPie);
                    drawPieChart('pie-chart', labelPie, datasetsPie);
                    var profit_per_state =
                        `<span class="badge badge-success ml-2" data-toggle="tooltip" data-placement="top" title="${data.profit_per_tooltip}"><i class="ion-arrow-up-b"></i> ${data.profit_per}%</span>`;
                    if (data.profit_per_state == 0) {
                        profit_per_state =
                            `<span class="badge badge-danger ml-2"><i class="ion-arrow-down-b"></i> ${data.profit_per}%</span>`;
                    }
                    var money_in_per_state =
                        `<span class="badge badge-success ml-2"><i class="ion-arrow-up-b"></i> ${data.money_in_per}%</span>`;
                    if (data.money_in_per_state == 0) {
                        money_in_per_state =
                            `<span class="badge badge-danger ml-2"><i class="ion-arrow-down-b"></i> ${data.money_in_per}%</span>`;
                    }
                    var money_out_per_state =
                        `<span class="badge badge-danger ml-2"><i class="ion-arrow-up-b"></i> ${data.money_out_per}%</span>`;
                    if (data.money_out_per_state == 0) {
                        money_out_per_state =
                            `<span class="badge badge-success ml-2"><i class="ion-arrow-down-b"></i> ${data.money_out_per}%</span>`;
                    }
                    $("#money-summary").html(`
                    <div class="portlet-body p-t-0 p-b-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="text-dark m-t-10">$<b class="counter">${number_format(data.profit, 0, '.', ',')}</b> </h3>
                            ${profit_per_state}
                        </div>
                        <span class="text-muted mb-0">Profit</span>
                    </div>
                    <div class="portlet-body p-t-0 p-b-0">
                        <hr class="m-b-0 m-t-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="text-dark m-t-10"><span class="text-dark align-items-start">$</span><b class="counter">${number_format(data.balance, 0, '.', ',')}</b> </h3>
                        </div>
                        <span class="text-muted mb-0">Balance</span>
                    </div>
                    <div class="portlet-body p-t-0 p-b-0">
                        <hr class="m-b-0 m-t-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="text-dark m-t-10"><span class="text-dark align-items-start">$</span><b class="counter">${number_format(data.money_in, 0, '.', ',')}</b> </h3>
                            ${money_in_per_state}
                        </div>
                        <span class="text-muted mb-0">Revenue</span>
                    </div>
                    <div class="portlet-body pb-4">
                        <hr class="m-b-0 m-t-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="text-dark m-t-10"><span class="text-dark">$</span><b class="counter">${number_format(data.money_out, 0, '.', ',')}</b> </h3>
                             ${money_out_per_state}
                        </div>
                        <span class="text-muted mb-0">Expense</span>
                    </div>
                `);
                    $(`#profit_before`).html(number_format(data.profit, 0, '.', ','));
                    $(`.chrismusic-salary`).html(number_format(data.salary.chrismusic, 0, '.', ','));
                    $(`.jamesmusic-salary`).html(number_format(data.salary.jamesmusic, 0, '.', ','));
                    $(`.hoadev-salary`).html(number_format(data.salary.hoadev, 0, '.', ','));
                    $(`.sangmusic-salary`).html(number_format(data.salary.sangmusic, 0, '.', ','));
                    $(`.truongpv-salary`).html(number_format(data.salary.truongpv, 0, '.', ','));

            $('#moneyInTable').html(generateTable(data.moneyTypeIns, 'm_in'));
            $('#moneyOutTable').html(generateTable(data.moneyTypeIns, 'm_out'));
            $('#salary_table').html(generateSalaryTable(data.bar_charts));
            $('[data-toggle="tooltip"]').tooltip();
                },
                error: function(data) {}
            });
        }
        
        $(".btn-import-expense").click(function() {
            $("#ms_report_table").html("");
            $(".modal-dialog").addClass("modal-80");
            $("#create_date").val(moment().format('YYYY-MM-DD'));
            initTable();
            $('#dialog_import_expense').modal({
                backdrop: false
            });
        });

        function submitExpense() {
            var $but = $(".btn-submit-expense");
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            if ($but.html() !== loadingText) {
                $but.data('original-text', $but.html());
                $but.html(loadingText);
            }

            var form = $("#frmAddExpense").serialize();
            var validate = Validator({
                form: '#frmAddExpense',
                notify: true,
                rules: [
                    Validator.isRequired({
                        input: "#amount",
                        name: "Amount"
                    }),
                ]
            });
            if (validate) {
                $but.html($but.data('original-text'));
                return;
            }
            $.ajax({
                type: "POST",
                url: "/saveMsReport",
                data: form,
                dataType: 'json',
                success: function(data) {
                    $but.html($but.data('original-text'));
                    console.log(data);
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    initTable();
                    updateMoneyChart(year, month, filterType);
                    if(data.status=="success"){
                        $('#ms_id').val(null);
                        $("#amount").val(null);
                        $("#money_note").val("");
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                    $but.html($but.data('original-text'));
                }
            });
        }
        
        function editExpense(id){
            $.ajax({
                type: "GET",
                url: "/findMsReport",
                data: {
                    ms_id: id,
                    _token: $("input[name=_token]").val()
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                        $('#ms_id').val(data.id);
                        $('#period').val(data.period);
                        $('input[name="payment_type"][value="' + (data.money_out ? 'out' : 'in') + '"]').prop('checked', true);

                        if (data.money_out) {
                            $('#vendor_out').val(data.vendor).change();
                            $('.div_radio-payment-type_out').show();
                            $('.div_radio-payment-type_in').hide();
                        } else {
                            $('#vendor_in').val(data.vendor).change();
                            $('.div_radio-payment-type_out').hide();
                            $('.div_radio-payment-type_in').show();
                        }

                        $('#amount').val(data.money_out ? data.money_out : data.money_in);
                        $('#money_note').val(data.note);
                        $(".modal-scroll").animate({
                        scrollTop: $("#frmAddExpense").offset().top
                    }, 500);
                    
                },
                error: function(data) {
                    console.log('Error:', data);
                    $but.html($but.data('original-text'));
                }
            });
        }

        function deleteExpense(id) {

            $.ajax({
                type: "POST",
                url: "/deleteMsReport",
                data: {
                    ms_id: id,
                    _token: $("input[name=_token]").val()
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    initTable();
                    updateMoneyChart(year, month, filterType);
                },
                error: function(data) {
                    console.log('Error:', data);
                    $but.html($but.data('original-text'));
                }
            });
        }

        function initTable() {
            $("#data-table-expense").DataTable().clear().destroy();
            $('#data-table-expense').DataTable({
                autoWidth: false,
                searching: true,
                ordering: true,
                processing: true,
                select: false,
                responsive: true,
                lengthMenu: [
                    [10, 25, 100, 500],
                    [10, 25, 100, 500]
                ],
                order: [
                    [0, 'desc']
                ],
                //        serverSide: true,
                ajax: {
                    "url": '/getMsReport',
                    "type": "GET",
                    "dataSrc": ""
                },
                columns: [{
                        "data": "id",
                        "width": "3%"
                    },
                    {
                        "data": "period",
                        "width": "7%"
                    },
                    {
                        "data": "username",
                        "width": "10%"
                    },
                    {
                        "data": "created_date",
                        "width": "10%"
                    },
                    {
                        "data": "money_in",
                        "width": "7%"
                    },
                    {
                        "data": "money_out",
                        "width": "7%"
                    },
                    {
                        "data": "vendor",
                        "width": "15%"
                    },
                    {
                        "data": "note",
                        "width": "10%"
                    },
                    {
                        title: "Funtions",
                        defaultContent: '',
                        "width": "10%"
                    }
                ],
                columnDefs: [{
                        targets: 7,
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).addClass('text-wrap');
                        }
                    },                   
                    {
                        targets: 8,
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).addClass('text-right');
                            $(td).html(
                                `<button class="btn btn-warning waves-effect waves-light btn-sm m-b-5 m-r-5" onclick="editExpense(${rowData.id})"><i class="fa fa-edit"></i> Edit</button>
                                <button class="btn btn-danger waves-effect waves-light btn-sm m-b-5 btn-del" onclick="deleteExpense(${rowData.id})"><i class="fa fa-trash"></i> Delete</button>`
                            );
                        },
                        //                        render: function (data, type, row, meta) {
                        //                            console.log(data,type,row,meta);
                        //                            return `<button class="btn btn-danger waves-effect waves-light btn-sm m-b-5 btn-del" onclick="deleteExpense(${row.id})"><i class="fa fa-trash"></i> Delete</button>`;
                        //                        }
                    }
                ]
            });
            $('[data-toggle="tooltip"]').tooltip();
        }
        
        function changeStatusPay(period, revType, valueChange) {
            $.ajax({
                type: "GET",
                url: "/changeStatusPay",
                data: {
                    'period': period,
                    'rev_type': revType,
                    'value': valueChange
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    if (data.status == 'error') {
                        $.Notification.notify('error', 'top center', 'Notification', data.message);
                    }

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
        @endif


        //promo
        function promoMonthlyDetail(period) {
            $("#promo_monthly_detail").html("");
            $("#promo_user_detail").html("");
            var $this = $("#btn" + period);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/getReportPeriodRevDetail",
                data: {
                    'period': period
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    console.log(data);
                    var html = '';
                    html += `<span class="m-l-15">Detail of <b>${period}</b></span><table class="table text-center" style="border-collapse: inherit"><thead><tr>
                        <th class='text-center'>#</th>
                        <th class='text-left'>User</th>
                        <th class='text-center'>Role</th>
                        <th class='text-center'>KPI</th>
                        <th class='text-center'>Detail</th>`;
                    var i = 0;
                    $.each(data.users, function(key, value) {
                        i = i + 1;
                        html += `<tr><td scope="row">${i}</td>
                            <td class='text-left'>${value.user_name}</td>
                            <td>${value.role}</td>
                            <td>$${value.kpi}</td>
                            <td><button id="btn${value.user_name}" type="button" onclick='promoUserDetail("${value.period}","${value.user_name}")' class="btn btn-outline-info btn-sm"> Detail</button></td>
                    </tr>`;
                    });
                    html += '</table>';
                    $("#promo_monthly_detail").html(html);

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function promoUserDetail(period, username) {
            $("#promo_user_detail").html("");
            var $this = $("#btn" + username);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/getReportUserRevDetail",
                data: {
                    'period': period,
                    'username': username
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $this.html($this.data('original-text'));
                    var html = '';
                    html += `<span class="m-l-15">Detail of <b>${username}</b></span><table class="table text-center" style="border-collapse: inherit"><thead><tr>
                            <th class='text-center'>#</th>
                            <th class='text-left'>User</th>
                            <th class='text-center'>Campaign</th>
                            <th class='text-center'>Type</th>
                            <th class='text-center'>Genre</th>
                            <th class='text-center'>Views</th>
                            <th class='text-center'>Views Mix</th>
                            <th class='text-center'>Views Lyric</th>
                            <th class='text-center'>Views Short</th>
                            <th class='text-center'>KPI</th>`;
                    var i = 0;
                    $.each(data.users, function(key, value) {
                        i = i + 1;
                        html += `<tr>
                                <td scope="row">${i}</td>
                                <td class='text-left'>${value.username}</td>
                                <td>${value.campaign_id}</td>
                                <td>${value.type}</td>
                                <td>${value.genre}</td>
                                <td>${number_format(value.views_money, 0, '.', ',')}/${ number_format(value.views_money_total, 0, '.', ',')}</td>
                                <td>${number_format(value.views_mix, 0, '.', ',')}</td>
                                <td>${number_format(value.views_lyric, 0, '.', ',')}</td>
                                <td>${number_format(value.views_short, 0, '.', ',')}</td>
                                <td><b>$${value.money}</b></td></tr>`;
                    });
                    html += '</table>';
                    html +=
                        `<table class="table"><tr><td><strong>PROMO: $${data.camTypeMoney.PROMOS.toFixed(2)}</strong></td></tr><tr><td class=""><strong>SUBMISSION: $${data.camTypeMoney.SUBMISSION.toFixed(2)}</strong></td></tr><table>`;
                    $("#promo_user_detail").html(html);

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        //claim, 5:adrev,6:indiy
        function claimMonthlyDetail(period, type = 2) {
            $(`claim_${type}_monthly_detail`).html("");
            $(`#claim__${type}_user_detail`).html("");
            var $this = $(`#claim_${type}_${period}`);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/listClaimMontlyDetail",
                data: {
                    'period': period,
                    'type': type
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    console.log(data);
                    var html = '';
                    html += `<span class="m-l-15">Detail of <b>${period}</b></span><table class="table text-center" style="border-collapse: inherit"><thead><tr>
                        <th class='text-center'>#</th>
                        <th class='text-left'>User</th>
                        <th class='text-left'>Role</th>
                        <th class='text-center'>Money Views</th>
                        <th class='text-center'>Money</th>
                        <th class='text-center'>Detail</th>`;
                    var i = 0;
                    $.each(data.users, function(key, value) {
                        i = i + 1;
                        html += `<tr><td scope="row">${i}</td>
                            <td class='text-left'>${value.user_name}</td>
                            <td class='text-left'>${value.role}</td>
                            <td>${ number_format(value.views_money, 0, '.', ',')}</td>
                            <td>$${ number_format(value.money, 0, '.', ',')}</td>
                            <td><button id="claim_${type}_${value.user_name}" type="button" onclick='claimUserDetail("${period}","${value.user_name}",${type})' class="btn btn-outline-info btn-sm"> Detail</button></td>
                    </tr>`;
                    });
                    html += '</table>';
                    $(`#claim_${type}_monthly_detail`).html(html);

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function claimUserDetail(period, username, type) {
            $(`#claim_${type}_user_detail`).html("");
            var $this = $(`#claim_${type}_${username}`);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/listClaimMontlyDetail",
                data: {
                    'period': period,
                    'username': username,
                    'type': type
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $this.html($this.data('original-text'));
                    var html = '';
                    html += `<span class="m-l-15">Detail of <b>${username}</b></span><table class="table text-center" style="border-collapse: inherit"><thead><tr>
                            <th class='text-center'>#</th>
                            <th class='text-left'>User</th>
                            <th class='text-center'>Campaign</th>
                            <th class='text-center'>Money Views</th>
                            <th class='text-center'>Mix</th>
                            <th class='text-center'>Lyric</th>
                            <th class='text-center'>Total</th>
                            <th class='text-center'>Money</th>`;
                    var i = 0;
                    $.each(data.campaigns, function(key, value) {
                        var tooltip = "";
                        if (value.tooltip.length > 0) {
                            tooltip =
                                `data-html="true" 
                                        data-toggle="tooltip" 
                                        data-placement="left" 
                                        data-original-title="<ol class='text-left font-13' style='padding: 1px 1px 1px 14px;'>`;
                            $.each(value.tooltip, function(k, v) {
                                console.log(v);
                                tooltip +=
                                    `<li>${v.username} - ${v.views_money_per}% - $${v.money.toFixed()}</li>`;
                            });
                            tooltip += `</ol>"`;
                        }
                        i = i + 1;
                        var moneyPer = value.views_money_user_per;
                        var mixPer = value.views_mix_user_per;
                        var lyricPer = value.views_lyric_user_per;
                        var totalPer = value.views_total_user_per;
                        if (value.views_money_user_per == null) {
                            mixPer = 0;
                        }
                        if (value.views_mix_user_per == null) {
                            mixPer = 0;
                        }
                        if (value.views_lyric_user_per == null) {
                            lyricPer = 0;
                        }
                        if (value.views_total_user_per == null) {
                            totalPer = 0;
                        }
                        html +=
                            `<tr>
                                <td scope="row">${i}</td>
                                <td class='text-left'>${value.username}</td>
                                <td>${value.campaign_id}</td> 
                                <td><div class="td-info cur-poiter"><span class="font-13 color-green">${moneyPer}%</span><div class="td-daily"><span>${number_format(value.views_money_user, 0, '.', ',')}</span><span>${ number_format(value.views_money, 0, '.', ',')}</span></div></div></td>
                                <td><div class="td-info cur-poiter"><span class="font-13 color-green">${mixPer}%</span><div class="td-daily"><span>${number_format(value.views_mix_user, 0, '.', ',')}</span><span>${ number_format(value.views_mix, 0, '.', ',')}</span></div></div></td>
                                <td><div class="td-info cur-poiter"><span class="font-13 color-green">${lyricPer}%</span><div class="td-daily"><span>${number_format(value.views_lyric_user, 0, '.', ',')}</span><span>${ number_format(value.views_lyric, 0, '.', ',')}</span></div></div></td>
                                <td><div class="td-info cur-poiter"><span class="font-13 color-green">${totalPer}%</span><div class="td-daily"><span>${number_format(value.views_total_user, 0, '.', ',')}</span><span>${ number_format(value.views_total, 0, '.', ',')}</span></div></div></td>
                                <td class="cur-poiter" ${tooltip}><div class="color-green"><b>$${number_format(value.money, 0, '.', ',')}</b></div><b>$${number_format(value.total_money, 0, '.', ',')}</b></td></tr>`;
                    });
                    html += '</table>';
                    $(`#claim_${type}_user_detail`).html(html);
                    //                    $('[data-toggle="tooltip"]').tooltip({placement: 'left',trigger: 'manual'}).tooltip('show');
                    $('[data-toggle="tooltip"]').tooltip();

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        //moonaz/bitly
        function moonazMonthlyDetail(period) {
            $("#moonaz_monthly_detail").html("");
            $("#moonaz_user_detail").html("");
            var $this = $("#moonaz_" + period);
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
                            <td><button id="moonaz_${value.username}" type="button" onclick='moonazUserDetail("${value.period}","${value.username}")' class="btn btn-outline-info btn-sm"> Detail</button></td>
                    </tr>`;
                    });
                    html += '</table>';
                    $("#moonaz_monthly_detail").html(html);

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function moonazUserDetail(period, username) {
            $("#moonaz_user_detail").html("");
            var $this = $("#moonaz_" + username);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/getChartMonthDetail",
                data: {
                    'period': period,
                    'username': username
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $this.html($this.data('original-text'));

                    var html = '';
                    html += `<span class="m-l-15">Detail of <b>${data.username}</b><table class="table text-center" style="border-collapse: inherit"><thead><tr>
                            <th class='text-center'>#</th>
                            <th class='text-left'>User</th>
                            <th class='text-left'>Bitly/Moonaz</th>
                            <th class='text-center'>Created</th>
                            <th class='text-center'>Click/Download</th>
                            <th class='text-center'>KPI</th>`;
                    var i = 0;
                    $.each(data.reportUser, function(key, value) {
                        var bitly =
                            `<a target="_blank" href="https://bit.ly/${value.custom_bitlinks}">${value.custom_bitlinks}`;
                        if (value.type == 'moonaz') {
                            bitly = 'moonaz';
                        }
                        i = i + 1;
                        html += `<tr>
                                <td scope="row">${i}</td>
                                <td class='text-left'>${value.username}</td>
                                <td class='text-left'>${bitly}</td>
                                <td>${value.created}</td>
                                <td>${number_format(value.clicked, 0, '.', ',')}</td>
                                <td><b>$${value.kpi}</b></td></tr>`;
                    });
                    html += '</table>';
                    $("#moonaz_user_detail").html(html);

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        //epid
        function epidMonthlyDetail(period) {
            $("#epid_monthly_detail").html("");
            $("#epid_user_detail").html("");
            var $this = $("#epid_" + period);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($this.html() !== loadingText) {
                $this.data('original-text', $this.html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/epidMonthlyDetail",
                data: {
                    'period': period
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    console.log(data);
                    var html = '';
                    html += `<span class="m-l-15">Detail of <b>${period}</b></span><table class="table text-center" style="border-collapse: inherit"><thead><tr>
                        <th class='text-center'>#</th>
                        <th class='text-left'>User</th>
                        <th class='text-center'>Money</th>
                        <th class='text-center'>Detail</th>`;
                    var i = 0;
                    $.each(data.epids, function(key, value) {
                        i = i + 1;
                        html += `<tr><td scope="row">${i}</td>
                            <td class='text-left'>${value.user_name}</td>
                            <td>$${ number_format(value.money, 0, '.', ',')}</td>
                            <td><button id="epid_${value.user_name}" type="button" onclick='epidUserDetail("${period}","${value.user_name}")' class="btn btn-outline-info btn-sm"> Detail</button></td>
                    </tr>`;
                    });
                    html += '</table>';
                    $("#epid_monthly_detail").html(html);

                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

//        function epidUserDetail(period, username) {
//            $("#epid_user_detail").html("");
//            var $this = $("#epid_" + username);
//            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
//            if ($this.html() !== loadingText) {
//                $this.data('original-text', $this.html());
//                $this.html(loadingText);
//            }
//            $.ajax({
//                type: "GET",
//                url: "/epidUserDetail",
//                data: {
//                    'period': period,
//                    'username': username
//                },
//                dataType: 'json',
//                success: function(data) {
//                    console.log(data);
//                    $this.html($this.data('original-text'));
//                    var html = '';
//                    html += `<span class="m-l-15">Detail of <b>${username}</b></span><table class="table text-center" style="border-collapse: inherit"><thead><tr>
//                            <th class='text-center'>#</th>
//                            <th class='text-left'>User</th>
//                            <th class='text-center'>Channel ID</th>
//                            <th class='text-center'>Money</th>`;
//                    var i = 0;
//                    $.each(data.epids, function(key, value) {
//                        i = i + 1;
//                        html += `<tr>
//                                <td scope="row">${i}</td>
//                                <td class='text-left'>${username}</td>
//                                <td>${value.channel_id}</td> 
//                                <td><b>$${number_format(value.money, 0, '.', ',')}</b></td></tr>`;
//                    });
//                    html += '</table>';
//                    $("#epid_user_detail").html(html);
//
//                },
//                error: function(data) {
//                    console.log(data);
//                }
//            });
//        }

function epidUserDetail(period, username) {
    $("#epid_user_detail").html("");
    var $this = $("#epid_" + username);
    var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
    if ($this.html() !== loadingText) {
        $this.data('original-text', $this.html());
        $this.html(loadingText);
    }
    $.ajax({
        type: "GET",
        url: "/epidUserDetail",
        data: {
            'period': period,
            'username': username
        },
        dataType: 'json',
        success: function(data) {
            console.log(data);
            $this.html($this.data('original-text'));
            var html = '';
            html += `<span class="m-l-15">Detail of <b>${username}</b></span><table class="table text-center" style="border-collapse: inherit"><thead><tr>
                    <th class='text-center'>#</th>
                    <th class='text-left'>User</th>
                    <th class='text-center'>Channel Name</th>
                    <th class='text-center'>Views</th>
                    <th class='text-center'>RPM</th>
                    <th class='text-center'>Money</th>`;
            var i = 0;
            $.each(data.epids, function(key, value) {
                i = i + 1;
                html += `<tr>
                        <td scope="row">${i}</td>
                        <td class='text-left'>${username}</td>
                        <td>
                            <div style="position: relative;">
                                <span class="channel-name" data-channel-id="${value.channel_id}" style="cursor: pointer;">${value.channel_name || 'Channel ' + i}</span>
                                <div class="copy-notification" style="display: none; position: absolute; top: -20px; left: 50%; transform: translateX(-50%); background-color: rgba(0,0,0,0.7); color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px;">Copied</div>
                            </div>
                        </td>
                        <td>${number_format(value.views || 0, 0, '.', ',')}</td>
                        <td>$${number_format(value.rpm || 0, 2, '.', ',')}</td>
                        <td><b>$${number_format(value.money, 0, '.', ',')}</b></td></tr>`;
            });
            html += '</table>';
            $("#epid_user_detail").html(html);
            
            // Add click event listener for copying channel links
            $(".channel-name").on("click", function() {
                var channelId = $(this).data("channel-id");
                var channelUrl = "https://www.youtube.com/channel/" + channelId;
                
                // Copy to clipboard
                var tempInput = document.createElement("input");
                tempInput.value = channelUrl;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);
                
                // Show and then hide the "Copied" notification
                var $notification = $(this).siblings(".copy-notification");
                $notification.show();
                setTimeout(function() {
                    $notification.fadeOut();
                }, 2000);
            });
        },
        error: function(data) {
            console.log(data);
        }
    });
}
    </script>
@endsection
