@extends('layouts.master')

@section('content')

<!--<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title disp-flex"><span class="m-t-7 m-r-10">Calendar</span>
            </h4>
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="#">Automusic</a></li>
                <li class="breadcrumb-item active">Calendar</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>-->
<div class="row">
    <div class="col-md-12">
        <div class="card-box">


            <ul class="nav nav-tabs tabs-bordered" style="border-bottom: 1px solid rgba(128, 137, 142, 0.2) !important">
                <li class="nav-item">
                    <a id="job-calendar" href="#job-calendar-data" data-toggle="tab" aria-expanded="false" class="nav-link job-tab-nav active">
                        <i class="fa fa-calendar"></i> Calendar
                    </a>
                </li>
                <li class="nav-item">
                    <a id="job-list" href="#job-list-data" data-toggle="tab" aria-expanded="true" class="nav-link job-tab-nav ">
                        <i class="fa fa-list-ul"></i> List
                    </a>
                </li>
                <li class="nav-item">
                    <a id="job-statistics" href="#job-statistics-data" data-toggle="tab" aria-expanded="true" class="nav-link job-tab-nav">
                        <i class="fa  fa-sort-amount-desc"></i> Statistics
                    </a>
                </li>

            </ul>

            <div class="tab-content">
                <div class="job-tab-nav tab-pane fade show active" id="job-calendar-data">
                    <div class="w-full m-b-10 d-flex justify-content-center" style="padding-bottom: 10px;border-bottom: 1px solid rgba(128, 137, 142, 0.2);">
                        <div class="d-flex flex-wrap align-items-center">

                            <button type="button" class="btn btn-sm btn-outline-secondary mr-3" data-toggle="modal" data-target="#filterModal" id="filterButton">
                                <i class="fa fa-filter"></i> Filters  
                                <span id="filterCount" class="badge badge-light m-l-5">0</span>
                                <span type="button" class="btn btn-sm clear-filters-btn disp-none" id="clearFiltersOnHover" data-toggle="tooltip" title="Clear All Filters">
                                    &times;
                                </span>
                            </button>

                            <!-- Other filter buttons -->
                            <button type="button" class="btn btn-sm btn-outline-secondary mr-3">
                                <span id="d-all"></span> All
                            </button>

                            <button id="filter_notdone" type="button" class="btn btn-sm btn-outline-secondary mr-3" onclick="changeFilter('filter_notdone')">
                                <span id="d-notdone"></span> Unfinished
                            </button>

                            <button id="filter_finished" type="button" class="btn btn-sm btn-outline-secondary mr-3" onclick="changeFilter('filter_finished')">
                                <span id="d-finish"></span> Finished
                            </button>

                            <button id="filter_progress" type="button" class="btn btn-sm btn-outline-secondary mr-3" onclick="changeFilter('filter_progress')">
                                <span id="d-progress"></span> In Progress
                            </button>

                            <button id="filter_overdue" type="button" class="btn btn-sm btn-outline-secondary mr-3" onclick="changeFilter('filter_overdue')">
                                <span id="d-overdue"></span> Overdue
                            </button>

                            <button id="filter_duesoon" type="button" class="btn btn-sm btn-outline-secondary mr-3" onclick="changeFilter('filter_duesoon')">
                                <span id="d-duesoon"></span> Due Soon
                            </button>
                            <button id="filter_duetoday" type="button" class="btn btn-sm btn-outline-secondary mr-3" onclick="changeFilter('filter_duetoday')">
                                <span id="d-duetoday"></span> Due Today
                            </button>
                            <button id="filter_dueweek" type="button" class="btn btn-sm btn-outline-secondary mr-3" onclick="changeFilter('filter_dueweek')">
                                <span id="d-dueweek"></span> Due Week
                            </button>
                        </div>
                    </div>
                    <div class="w-full div_scroll_70">
                        <div class="">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
                <div class="job-tab-nav tab-pane fade " id="job-list-data">
                    <div class="w-100 overflow-auto p-l-7 p-r-7">
                        <div class="w-full m-b-10 d-flex justify-content-end mw-80p mx-auto">
                            

<!--<div class="inline-flex rounded-md shadow-sm" role="group">
  <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
    <svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
      <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z"/>
    </svg>
    Profile
  </button>
  <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
    <svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12.25V1m0 11.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M4 19v-2.25m6-13.5V1m0 2.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M10 19V7.75m6 4.5V1m0 11.25a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5ZM16 19v-2"/>
    </svg>
    Settings
  </button>
  <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
    <svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
      <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
      <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
    </svg>
    Downloads
  </button>
</div>-->

                            <div class="inline-flex rounded-md shadow-sm" role="group">
                                <button type="button" value="today" class="btn-filter-list-job px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-primary focus:z-10  focus:text-primary ">
                                    Today <span id="count_today"></span>
                                </button>
                                <button type="button"  value="week"  class="btn-filter-list-job px-4 py-2 text-sm font-medium text-gray-900 bg-white border-l-r-0 border-t-b-1 hover:bg-gray-100 hover:text-primary focus:z-10  focus:text-primary ">
                                    Week <span id="count_week"></span>
                                </button>
                                <button type="button"  value="month" class="btn-filter-list-job px-4 py-2 text-sm font-medium text-gray-900 bg-white border-l-r-0 border-t-b-1 border-l-1 hover:bg-gray-100 hover:text-primary focus:z-10  focus:text-primary ">
                                    Month <span id="count_month"></span>
                                </button>
                                <button type="button"  value="all" class="btn-filter-list-job px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-primary focus:z-10  focus:text-primary ">
                                    All <span id="count_all"></span>
                                </button>
                            </div>

                        </div>
                        <div class="overflow-auto div_scroll_70_job_tab">

                            <table id="job-list-table" class="custom-table mw-80p">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th class="text-left">Name</th>
                                        <th>Estimate</th>
                                        <th>Due Date</th>
                                        <th>Comment</th>
                                        <th>Status</th>
                                        <th>Assignee</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <div class="row" id="item-container">
                                <!-- Items will be appended here -->
                            </div>
                            <div id="nodata" class="text-center m-t-20 disp-none">No Data</div>
                            <div cid="loading" style="display: none">
                                <div class="text-center">
                                    <p>Loading more items...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="job-tab-nav tab-pane fade " id="job-statistics-data">
                    <div id="calendar-statistics" >
                        <div class="w-80p overflow-auto mb-3" style="margin: 0 auto;">
                            <form id="filter-form">
                                <label for="month" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Month</label>
                                <select id="month" name="month" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                    @endfor
                                </select>

                                <label for="year" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Year</label>
                                <select id="year" name="year" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    @for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                                <div>
                                    <input type="radio" id="type-month" name="type" value="month" checked>
                                    <label for="type-month">Month</label>
                                    <input type="radio" id="type-year" name="type" value="year">
                                    <label for="type-year">Year</label>
                                </div>
                                <button type="submit">Filter</button>
                            </form>
                        </div>

                        <div class="w-100 overflow-auto div_scroll_70">
                            <table id="stats-table" class="custom-table mw-80p">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th class="text-left">Name</th>
                                        <th>Read/Total Notify</th>
                                        <th>Completed</th>
                                        <th>Completed on time</th>
                                        <th>Hours</th>
                                        <th>Score</th>
                                        <!--<th>Rating</th>-->
                                        <!--<th>Reward/Penalty</th>-->
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>
@include('dialog.calendar.add_task')


@endsection

@section('script')
<script type="text/javascript">
    

    function addMoreField(element,startIndex, baseName){
//        let currentIndex = startIndex + $('input[name^="' + baseName + '"]').length;

//         //Tạo tên mới cho input
//        let newName = baseName + currentIndex;
//
//            // Tạo input mới
//            let newInput = $('<input>', {
//                class: 'form-control form-control-sm m-b-2',
//                value: '',
//                placeholder: '',
//                type: 'text',
//                name: newName
//            });
//        // Tạo nút xóa
//        let deleteButton = $('<button>', {
//            class: 'btn btn-danger btn-sm m-l-2',
//            text: 'Remove',
//            click: function() {
//                $(this).parent('.input-container').remove(); // Xóa cả input và nút
//            }
//        });
//
//        // Tạo container mới cho input và nút xóa
//        let inputContainer = $('<div>', {
//            class: 'input-container'
//        });
//
//        // Thêm input và nút xóa vào container
//        inputContainer.append(newInput).append(deleteButton);
//        if ($('.input-container').length === 0) {
//            $('input[name^="' + baseName + '"]').last().after(inputContainer);
//        } else {
//            $('.input-container').last().after(inputContainer);
//        }
        let currentIndex = startIndex + $(element).closest('.form-group').find('.input-container').length;
        let newName = baseName + currentIndex;
        let newFieldHtml = `
        <div class="input-container">
            <input id='${newName}' class="form-control form-control-sm m-b-5" value="" placeholder="" type="text" name="${newName}">
            <button class="btn btn-danger btn-sm m-l-2" onclick="removeField(this,'${baseName}','${newName}')">Remove</button>
        </div>
    `;
      $(element).closest('.form-group').append(newFieldHtml);
        $.ajax({
            url: "/calendar/updateJobDetail",
            type: 'GET',
            data: {action: 'add', job_id: $("#job_id").val(),parent_id:baseName,child_id:newName,value:$(`${newName}`).val()},
            success: function (response) {
  
            },
            error: function (xhr) {
                console.log('Error:', xhr);
                isLoading = false;
                $('#loading').hide();
            }
        });

    }
    
    function removeField(button,parent_id,child_id) {
    $(button).parent('.input-container').remove();
            $.ajax({
            url: "/calendar/updateJobDetail",
            type: 'GET',
            data: {action: 'remove', job_id: $("#job_id").val(),parent_id:parent_id,child_id:child_id},
            success: function (response) {
  
            },
            error: function (xhr) {
                console.log('Error:', xhr);
                isLoading = false;
                $('#loading').hide();
            }
        });
}
    
    
    $(".footer").remove();
    $('.content-page > .content').css({'padding': '0px', 'margin-bottom': '0px'});
    var currTab = 'job-calendar';
    if (localStorage.calendar_tab != null) {
        currTab = localStorage.calendar_tab;
        $(`#${currTab}`).click();
    }

//     $(".job-tab-nav").remove("active").remove("show");
//    $(`#${currTab}`).addClass("active");
//    $(`#${currTab}-data`).addClass("active").addClass("show");
    $('[data-toggle="tab"]').click(function () {
        var id = $(this).attr('id');
        localStorage.calendar_tab = id;
        if (id == 'job-calendar') {
            calendar.fullCalendar('refetchEvents');
        }
//            $(`#${id}`).addClass("active");
//            $(`#${id}-data`).addClass("active").addClass("show");

    });

    var currentPage = 1;
    var isLoading = false;
    var hideCompleted = true;
    var id = 0;

    var filterListJob = "all";
    if (localStorage.filter_list_job != null) {
        filterListJob = localStorage.filter_list_job;
    }
    $('.btn-filter-list-job[value="'+filterListJob+'"]').addClass("text-primary");
    $('.btn-filter-list-job').click(function () {
        currentPage =1;
        localStorage.filter_list_job = $(this).val();
        filterListJob = $(this).val();
        $('.btn-filter-list-job').removeClass("text-primary");
        $(this).addClass("text-primary");
        loadMore(currentPage);

    });

    function loadMore(page, append = false) {
        isLoading = true;
        $('#loading').show();
        $("#nodata").hide();

        $.ajax({
            url: "/calendar/listJobs",
            type: 'GET',
            data: {page: page, hide_completed: hideCompleted,filter:filterListJob},
            success: function (response) {
                logger("listJobs", response);
                var items = response.items;
//                var html = '';
                var tbody = $('#job-list-table tbody');
                if (!append) {
                    tbody.empty();
                    id=0;

                }
                
                $(`#count_${filterListJob}`).html(`(${response.total})`);
                if(items.length>0){
                $.each(items, function (index, item) {
//                    html += '<div class="col-md-2">';
//                    html += '<div class="card mb-4">';
//                    html += '<div class="card-body">';
//                    html += '<h6 class="card-title">#' + item.id + " " + item.name + '</h6>';
//                    html += '<p class="card-text">Estimate Time: ' + item.man_hour + '</p>';
//                    html += '<p class="card-text">Due Date: ' + item.due_date + '</p>';
//                    html += '<p class="card-text">Status: ' + item.status + '</p>';
//                    html += '<div class="stacked-avatars">';
//                    $.each(item.users, function (index, user) {
//                        html += '<img src="' + user.avatar_url + '" height="35" width="35" title="' + user.name + '">';
//                    });
//                    html += '</div>';
//                    html += '</div>';
//                    html += '</div>';
//                    html += '</div>';
                    id++;
                    var priority = '';
                    var finish = '';
                    var checked = '';
                    var status = '';
                    var color = 'bg-primary';
                    var tooltip = `Job #${item.id} is new`;
                    if (item.status === 3) {
                        finish = "text-line-through";
                        checked = "checked";
                        status = '<i class="fa fa-check"></i>';
                        color = 'bg-done';
                        tooltip = `Job #${item.id} is completed`;
                    } else if (item.status === 1) {
                        status = '<i class="fa fa-cogs"></i>';
                        color = 'badge-warning';
                        tooltip = `Job #${item.id} is in progress`;
                    } else if (item.status === 2) {
                        status = '<i class="fa fa-hourglass-1"></i>';
                        color = 'badge-secondary';
                        tooltip = `Job #${item.id} is on hold`;
                    }
                    var colorDue = "";
                    if (0 < item.remain && item.remain < 7200) {
                        colorDue = "color-y";
                    } else if (item.remain <= 0) {
                        colorDue = "color-red";
                    }
                    var due = "";
                    if (item.cal_duedate !== "") {
                        due = `<div class="ml-40 mr-40 text-nowrap ${colorDue}" data-toggle="tooltip" data-placement="top" title="Expires on ${item.duatime}">
                                        <i class="fa fa-calendar"></i> <span >${item.cal_duedate}</span>
                                    </div>`;
                    }
                    var avatar =
                            `<div class="avatar ml-40 mr-40">
                                <img class="rounded-circle img-cover img-list w-35px h-35px mw-35px" src="/images/avatar/${item.username}.jpg" onerror="this.onerror=null;this.src='images/default-avatar.png';" alt="avatar" data-toggle="tooltip" data-placement="top" title="${item.username}">`;
                    var members = JSON.parse(item.member);
                    $.each(members, function (k, v) {
                        avatar +=
                                `<img class="rounded-circle img-cover img-list w-35px h-35px mw-35px" src="/images/avatar/${v}.jpg" onerror="this.onerror=null;this.src='images/default-avatar.png';" data-toggle="tooltip" data-placement="top" title="${v}">`;
                    });
                    avatar += `</div>`;
                    var comment = "";
                                    if (item.comment > 0) {
                    comment= `<div class="ml-40 mr-40 text-nowrap" data-toggle="tooltip" data-placement="top" title="${item.comment} on this job">
                                        <i class="fa fa-comment m-r-3"></i> <span class="font-12">${item.comment}</span>
                                    </div>`;
                }
                    tbody.append(
                            `<tr class="cur-poiter">' +
                        <td>${(id)}</td>
                        <td class="text-left cur-poiter">
                            <div class="d-flex align-items-center">
                                <div data-toggle="tooltip" data-placement="top" title="" data-original-title="${item.admin}">
                                    <img class="rounded-circle img-cover w-35px h-35px mw-35px" src="/images/avatar/${item.admin}.jpg">
                                </div>
                                <div>
                            <div class="m-l-5" onclick="detailJob(${item.id})"><span class="font-16 text-wrap hover-under-line"><b>#${item.id}</b> ${item.name}</span></div>            
                            <div class="m-l-5" onclick="detailTask(${item.task_id})"><span class="font-14 text-wrap hover-under-line"><b>#${item.task_id}</b> ${item.task_name}</span></div>            
                                        
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="ml-40 mr-40 text-nowrap" data-toggle="tooltip" data-placement="top" title="Estimate ${item.job_man_hour} ${item.job_man_hour_unit}">
                                        <i class="fa fa fa-hourglass-o"></i> <span >${item.job_man_hour} ${item.job_man_hour_unit}</span>
                            </div>
                        </td>    
                        <td>${due}</td>
                        <td>${comment}</td>
                        <td>
                            <span class="badge ${color} ml-40 mr-120" data-toggle="tooltip" data-placement="top" title="${tooltip}">${status} ${item.job_type}</span>
                        </td>
                        <td>${avatar}</td>
                           
                           
                        </tr>`
                            );
                });
                }else{
                    $("#nodata").show();
                }


//                $('#item-container').append(html);

                if (response.hasMorePages) {
                    currentPage = response.nextPage;
                } else {
                    $('.div_scroll_70_job_tab').off('scroll'); // Ngừng lắng nghe sự kiện scroll
                }

                isLoading = false;
                $('#loading').hide();
            },
            error: function (xhr) {
                console.log('Error:', xhr);
                isLoading = false;
                $('#loading').hide();
            }
        });
    }

    $('.div_scroll_70_job_tab').slimScroll({
        height: '70vh',
        position: 'right',
        size: "5px",
        color: '#98a6ad',
        wheelStep: 30
    });


    // Initial load
    loadMore(currentPage);
    $('.div_scroll_70_job_tab').on('scroll', function () {
        var scrollTop = $(this).scrollTop();
        var scrollHeight = $(this).prop('scrollHeight');
        var containerHeight = $(this).height();
        if (scrollTop + containerHeight >= scrollHeight - 1) {
            if (!isLoading) {
                loadMore(currentPage, true);
            }
        }
    });



//    $(window).resize(function () {
//        calendar.fullCalendar('option', 'eventLimit', getEventLimit());
//        calendar.fullCalendar('refetchEvents');
//    });
    //    const datetimeInput = document.getElementById('job_duedate');
    //    datetimeInput.addEventListener('input', function() {
    //
    //    const value = datetimeInput.value.split('T')[1]; // Lấy phần giờ từ giá trị của input
    //    const hours = Number(value.split(':')[0]); // Lấy giờ
    //    const minutes = value.split(':')[1]; // Lấy phút
    //
    //    // Chuyển đổi giờ 12h sang giờ 24h
    //    let convertedHours = hours;
    //    if (datetimeInput.value.includes('PM') && hours !== 12) {
    //      convertedHours += 12;
    //    } else if (datetimeInput.value.includes('AM') && hours === 12) {
    //      convertedHours = 0;
    //    }
    //
    //    // Định dạng lại giá trị của input
    //    const formattedValue = `${convertedHours.toString().padStart(2, '0')}:${minutes}`;
    //    datetimeInput.value = datetimeInput.value.split('T')[0] + 'T' + formattedValue;
    //  });
//  $('#modal-add-job').on('hidden.bs.modal', function (e) {
//    console.log('Modal has been closed.');
//    $("#job_status").unbind('change');
//    // Thực hiện các hành động khác tại đây
//  });


    $('.btn-result-type').on('click', function () {
        $('#grid-view').addClass('active');
        localStorage.setItem('result_type', $(this).attr("data-type"));
    });


    var users = [];
    $.get("/calendar/getUserDropdown", function (data) {
        users = data;
        $('#customUserButton1').userSelect({
            users: users,
            selectedUserIdsInput: '#selectedCreatedBy',
            onSelect: function (username) {
                var selected = $("#selectedCreatedBy").val();
                localStorage.filter_createdby = selected;
                calendar.fullCalendar('refetchEvents');
            },
            onRemove: function (username) {
                var selected = $("#selectedCreatedBy").val();
                localStorage.filter_createdby = selected;
                calendar.fullCalendar('refetchEvents');
            }
        });
        $('#customUserButton2').userSelect({
            users: users,
            selectedUserIdsInput: '#selectedAssignees',
            onSelect: function (username) {
                var selected = $("#selectedAssignees").val();
                localStorage.filter_assignees = selected;
                calendar.fullCalendar('refetchEvents');
            },
            onRemove: function (username) {
                var selected = $("#selectedAssignees").val();
                localStorage.filter_assignees = selected;
                calendar.fullCalendar('refetchEvents');
            }
        });
        //            loadSelectedUsers();
    }, "json");

    function loadSelectedUsers() {
        var selectedUserIds = localStorage.getItem('filter_assignees');
        if (selectedUserIds) {
            $('#selectedUserIds1').val(selectedUserIds);
            selectedUserIds.split(',').forEach(function (username) {
                var user = users.find(function (u) {
                    return u.username === username;
                });
                if (user) {
                    var $selectedUsers = $('#customUserButton1').siblings('.user-select-container').find(
                            '.selected-users');
                    $selectedUsers.append(
                            '<div class="selected-user" data-username="' + user.username + '"  title="' + user
                            .username + '">' +
                            '<img src="' + user.avatar + '" alt="">' +
                            '<span class="remove-user">&times;</span>' +
                            '</div>'
                            );
                }
            });

        }
    }

    $('#filter-form').on('submit', function (e) {
        e.preventDefault();
        var month = $('#month').val();
        var year = $('#year').val();
        var type = $('input[name="type"]:checked').val();
        fetchStats(month, year, type);
    });

    // Fetch initial data
    var currentDate = new Date();
    var month = currentDate.getMonth() + 1; // Months are zero-based
    var year = currentDate.getFullYear();
    $('#month').val(month);
    $('#year').val(year);
    fetchStats(month, year, 'month');

    function fetchStats(month, year, type) {
        $.ajax({
            url: '/calendar/statistics',
            data: {
                month: month,
                year: year,
                type: type
            },
            success: function (data) {
                logger('user-statistics', data);
                var tbody = $('#stats-table tbody');
                tbody.empty();
                $("#calendar-statistics").show();
                $.each(data.statistics, function (index, user) {
//                        var badgeClass = getBadgeClass(user.final_rating_label);
                    tbody.append(
                            `<tr>' +
                        <td>${(index + 1)}</td>
                        <td class="text-left">
                            <div class="d-flex align-items-center">
                                <div data-toggle="tooltip" data-placement="top" title="" data-original-title="${user.username}">
                                    <img class="rounded-circle img-cover h-10" src="/images/avatar/${user.username}.jpg" height="40" width="40">
                                </div>
                                <div class="m-l-5">
                                    <div><span>${user.username}</span></div>
                                    <div><span class="text-muted font-12">${user.last_act}</span></div>
                                </div>
                            </div>
                        </td>
                        <td>${user.read_notify}/${user.total_notify} <span class="font-13 text-muted">${user.read_notify_percentage}%</span></td>    
                        <td>${user.completed_jobs}/${user.total_jobs} <span class="font-13 text-muted">${user.completed_percentage}%</span></td>
                        <td>${(user.completed_ontime_jobs)}/${user.total_jobs} <span class="font-13 text-muted">${user.completed_on_time_percentage}%</span></td>
                        <td>${user.adjusted_man_hour.toFixed(1)}/${user.total_man_hour.toFixed(1)}</td>
                        <td>${user.efficiency.toFixed()}</td>
                           
                           
                        </tr>`
                            );
                });
            }
        });
    }

    function getBadgeClass(rating) {
        switch (rating) {
            case 'Excellent':
                return 'badge-success';
            case 'Good':
                return 'badge-primary';
            case 'Fair':
                return 'badge-warning';
            case 'Average':
                return 'badge-secondary';
            case 'Poor':
                return 'badge-danger';
            default:
                return 'badge-light';
        }
    }

    var emojis = ['thumbs_up', 'sparkling_heart', 'smiling_face_with_heart_eyes',
        'winking_face_with_tongue', 'smiling_face_with_sunglasses', 'pleading_face',
        'pouting_face', 'pile_of_poo'
    ];
    var emojiPicker = $(".emoji-picker");
    emojis.forEach(function (emoji) {
        var img = $('<img>').attr('src', `/images/emojis/${emoji}.gif`).attr('name', emoji);
        emojiPicker.append(img);
    });

    $(".btn-plus-hour").click(function (e) {

        e.preventDefault();
        var hour = $(this).val();
        var input = $("#job_duedate").val();
        let datetime = new Date();
        datetime.setHours(datetime.getHours() + 7);
        if (input !== "") {
            datetime = new Date(input + "Z"); // Thêm "Z" để xác định là múi giờ UTC
        }
        datetime.setHours(datetime.getHours() + parseInt(hour));

        // Format lại thời gian theo định dạng của datetime-local
        let formattedDateTime = datetime.toISOString().slice(0, 16).replace("T", " ");
        $("#job_duedate").val(formattedDateTime);

    });

    $(".btn-plus-hour-task").click(function (e) {

        e.preventDefault();
        var hour = $(this).val();
        var input = $("#job_duedate_task").val();
        let datetime = new Date();
        datetime.setHours(datetime.getHours() + 7);
        if (input !== "") {
            datetime = new Date(input + "Z"); // Thêm "Z" để xác định là múi giờ UTC
        }
        datetime.setHours(datetime.getHours() + parseInt(hour));

        // Format lại thời gian theo định dạng của datetime-local
        let formattedDateTime = datetime.toISOString().slice(0, 16).replace("T", " ");
        $("#job_duedate_task").val(formattedDateTime);

    });

    var changeFilter = function (key) {
        var filterKey = localStorage.getItem(key);
        if (filterKey == 1) {
            localStorage.setItem(key, 0);
            $(`#${key}`).removeClass("btn-outline-secondary-hover");
        } else {
            localStorage.setItem(key, 1);
            $(`#${key}`).addClass("btn-outline-secondary-hover");
        }
        //            location.reload();
        calendar.fullCalendar('refetchEvents');
    };
    var filter = localStorage.getItem("calendar_job_filter");
    if (filter != null) {
        $("#job_owner_filter").val(filter);
    } else {
        $("#job_owner_filter").val("all");
        localStorage.setItem("calendar_job_filter", "all");
    }
    filter = localStorage.getItem("calendar_job_filter");
    if (filter == "all") {
        $(".toggle-filter").html(`<i class="fa fa-users font-20"></i>`);
    } else {
        $(".toggle-filter").html(`<i class="fa fa-user font-20"></i>`);
    }

    $("#job_status").change(function () {
        if (jobStatusChanged) {
            var jobId = $("#job_id").val();
            var jobStatus = $(this).val();
            $.ajax({
                type: "POST",
                url: "/calendar/createJob",
                data: {
                    job_id: jobId,
                    job_status: jobStatus,
                    _token: $("input[name=_token]").val(),
                    action_type: 'quick'
                },
                dataType: 'json',
                success: function (data) {
                    logger('statusChange', data);
                    if (data.status === "success") {
                        listJobs(data.data.task_id);
                        listHistory(data.data.task_id);
                    }
                    $.Notification.autoHideNotify(data.status, 'top center', "Notify", data.message);
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }
        jobStatusChanged = true;
    });

    $("#task_type").change(function () {
        $("#ck_auto_job").prop("checked", true).change();
        if ($(this).val() !== "campaign") {
            $(".div_campaign").hide();
            $(".div_other").show();
        } else {
            $(".div_campaign").show();
            $(".div_other").hide();
        }
    });

    $("#campaign_id").change(function () {
        if ($(this).val() === "") {
            $("#task_name").val("");
        } else {
            $("#task_name").val($("#campaign_id option:selected").text());
        }
    });

    $(".btn-job-group").click(function () {
        var divId = $(this).attr("data-id");
        var group = $(this).val();
        var campaignId = $("#campaign_id").val();
        var start = $("#curr_start").val();
        if (campaignId == "") {
            $.Notification.autoHideNotify("error", 'top center', "Notify", "Please select a campaign");
            return;
        }

        if ($(this).hasClass("btn-outline-dashed-active")) {
            $(this).removeClass("btn-outline-dashed-active");
            $(`#${divId}`).hide();
        } else {
            $(this).addClass("btn-outline-dashed-active");
            $(`#${$(this).attr("data-id")}`).show();
            refreshJobGroup(`${divId}_jobs`, group, campaignId, start);

        }
    });

    function refreshJobGroup(divId, group, campaignId, start) {
        $(`#${divId}`).jstree("destroy").empty();

        $.ajax({
            type: "GET",
            url: "/calendar/checklist/get",
            data: {
                group: group,
                campaign_id: campaignId,
                start: start
            },
            dataType: 'json',
            success: function (data) {
                var jsonObj = [];
                var item = {};

                $.each(data.checklist, function (key, value) {
                    if (key === 0) {
                        item["id"] = value.id;
                        item["parent"] = "#";
                        item["text"] = value.location;
                        item["icon"] = "ti-user";
                        jsonObj.push(item);
                    }
                    var childItem = {};
                    childItem["id"] = value.key;
                    childItem["parent"] = item["id"];
                    childItem["parentname"] = item["text"];
                    childItem["text"] = value.name;
                    childItem["icon"] = "fa fa-tasks";
                    jsonObj.push(childItem);


                });


                $(`#${divId}`).jstree({
                    "core": {
                        'themes': {
                            'responsive': false
                        },
                        "check_callback": true,
                        'data': jsonObj
                    },
                    'types': {
                        'default': {
                            'icon': 'ti-user'
                        },
                        'file': {
                            'icon': 'fa fa-file'
                        }
                    },
                    'plugins': ['types', 'checkbox']
                }).bind("loaded.jstree", function (event, data) {
                    $(this).jstree("open_all");
                    $(this).jstree("check_all");

                });
            },
            error: function (data) {

            }
        });
    }

    $(".dialog-job").click(function () {
        formJobClear();
        var jobId = $("#job_id").val();
        if (jobId === "") {
            $("#comment").hide();
            $(".btn-delete-job").hide();
        } else {
            $(".btn-delete-job").show();
            $("#comment").show();
        }
        //            $('#modal-add-task').modal('hide');
        $("#modal-add-job").modal({
            backdrop: 'static',
            keyboard: true
        });
        $("body").css("padding-right", "0px");
        //            $("body").addClass('modal-open');
        $("#dialog-job-title").html(`<h5>Add Job</h5>`);
        $("#modal-add-job").on('shown.bs.modal', function () {
            if (jobId === "") {
                $("#summernote_job_des").summernote("destroy");
                $("#summernote_job_des").html("<ul><li></li></ul>");
                initSummernote('#summernote_job_des', false);
                $("#modal-add-job").off();
                $('#job_duedate').val(getDefaultDuaDate('17:30'));
                $("#job_man_hour_unit").val("hours");
            }
        });

    });

    function eventClose() {
        $(".btn-close-modal").unbind("click").click(function () {
            var id = $(this).attr("data-id");
            $(`#${id}`).modal('hide');
//                $("#job_status").unbind('change');
        });
    }

    eventClose();

    $(".btn-save-job").click(function () {
        var markup = $('#summernote_job_des').summernote('code');
        //            const markup = editor.getData();
        $("#job_des").val(markup);
        var taskId = $("#task_id").val();
        var formJob = $("#form-add-job").serialize();
        formJob += `&task_id=${taskId}`;
        
        $.ajax({
            type: "POST",
            url: "/calendar/createJob",
            data: formJob + `&action_type=normal`,
            dataType: 'json',
            success: function (data) {
                //                console.log(data);
                if (data.status === "success") {
                    listJobs(taskId);
                    $("#modal-add-job").modal('hide');
                    $("#job_status").unbind('change');

                }
                $.Notification.autoHideNotify(data.status, 'top center', "Notify", data.message);
            },
            error: function (data) {
                console.log(data);
            }
        });
    });

    $(".btn-save-comment").click(function () {
        var $this = $(this);
        $this.prop("disabled", true);
        var markup = $('#summernote_job_comment').summernote('code');
        var jobomment = $("#job_comment").val();
        var jobId = $("#job_id").val();
        var taskId = $("#task_id").val();
        var highlight = $(".hwt-highlights").html();
        $.ajax({
            type: "POST",
            url: "/calendar/createComment",
            data: {
                job_id: jobId,
                task_id: taskId,
                job_comment: markup,
                _token: $("input[name=_token]").val(),
                highlight: highlight,
            },
            dataType: 'json',
            success: function (data) {
                logger('comment', data);
                $("#job_comment").val("");
                $this.prop("disabled", false);
                if (data.status === 'success') {
                    $(".div_comment_content").show();
                    $(".hwt-highlights").html("");
                    $('#summernote_job_comment').summernote('reset');
                    appendComment(data.comment);
                } else {
                    $.Notification.autoHideNotify("error", 'top center', "Notify", data.message);
                }
            },
            error: function (data) {
                console.log(data);
            }
        });
    });

    $(".toggle-filter").click(function () {
        var filter = localStorage.getItem("calendar_job_filter");
        if (filter == "all") {
            $(".toggle-filter").html(`<i class="fa fa-user font-20"></i>`);
            localStorage.setItem("calendar_job_filter", "one");
            $("#job_owner_filter").val("one");
        } else {
            $(".toggle-filter").html(`<i class="fa fa-users font-20"></i>`);
            localStorage.setItem("calendar_job_filter", "all");
            $("#job_owner_filter").val("all");
        }
        listJobs($("#task_id").val());
    });

    $('.div_scroll_300px').slimScroll({
        height: '200px',
        position: 'right',
        size: "5px",
        color: '#98a6ad',
        start: 'bottom',
        wheelStep: 30
    });



    

    initSummernote('#summernote_job_des', true);
    var source = <?php echo json_encode($highlightUser); ?>;
    $('#summernote_job_comment').summernote({
        height: 50,
        minHeight: null,
        maxHeight: null,
        focus: false,
        spellCheck: false,
        airMode: true,
        placeholder: 'You can enter text or send an image...',
        dialogsInBody: true,
        tabDisable: false,
        disableAutoParagraph: true,
        cleaner: true,
        isNotSplitEdgePoint: true,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            // Bỏ chức năng chèn liên kết
            // ['link', ['link']],
            // ['insert', ['picture', 'video']],
            ['view', ['fullscreen', 'codeview']],
        ],
        hint: {
            mentions: source,
            match: /\B@(\w*)$/,
            search: function (keyword, callback) {
                callback($.grep(this.mentions, function (item) {
                    return item.indexOf(keyword) >= 0;
                }));
            },
            content: function (item) {
                var match = item.match(/data-user="([^"]+)"/);
                var userName = match ? match[1] : null;
                return $(`<span class="highlight-us-blue">@${userName}</span>`)[0];
            }
        }
    });

    $('#summernote_job_comment').siblings(".note-editor").addClass("summernote-size-md");
    //        var date = new Date();
    //        var timezoneOffset = date.getTimezoneOffset();
    //        console.log(timezoneOffset);
    var jid = $("#job_id_open").val();
    var tid = $("#task_id_open").val();
    if (jid != null && jid != "") {
        detailJob(jid);
        $("#task_id").val(tid);

    }
</script>
@endsection
