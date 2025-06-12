
!function ($) {
    "use strict";

    var filterKey = ["filter_notdone", "filter_finished", "filter_progress", "filter_overdue", "filter_duesoon", "filter_duetoday", "filter_dueweek"];

    var adminFilterKey = ["is_hide_finished"];

    var getJobStatistics = function () {
        //        var isHide = localStorage.getItem("is_hide_finished");
//        var textFilter = localStorage.getItem("text_filter");
//        if (textFilter == null) {
//            textFilter = "";
//        }
//        var isAssigned = localStorage.getItem("is_assigned");
//        if(isAssigned=="true"){
//            $("#div-select-user").show();
//        }else{
//            $("#div-select-user").hide();
//        }
//        var myTask = localStorage.getItem("is_my_task");
        var start = localStorage.getItem("calendar_start");
        var end = localStorage.getItem("calendar_end");
        var month = localStorage.getItem("calendar_month");

        $.ajax({
            type: "GET",
            url: "/calendar/job/statistics",
            data: {
                filter_assignees: $("#selectedAssignees").val(),
                filter_createdby: $("#selectedCreatedBy").val(),
//                is_my_task: myTask,
                start: start,
                end: end,
                month: month
            },
            dataType: 'json',
            success: function (data) {
                $("#text_filter_loading").hide();
                logger('getJobStatistics', data);
//                $(".fc-right").html(`<div class="d-flex flex-wrap align-items-center">
//         
//                                            <button type="button" class="btn btn-outline-secondary mr-3" data-toggle="modal" data-target="#filterModal" id="filterButton">
//                                                <i class="fa fa-filter"></i> Filters  
//                                                <span id="filterCount" class="badge badge-light m-l-5">0</span>
//                                                <span type="button" class="btn btn-sm clear-filters-btn disp-none" id="clearFiltersOnHover" data-toggle="tooltip" title="Clear All Filters">
//                                                    &times;
//                                                </span>
//                                            </button>
//
//                                            <!-- Other filter buttons -->
//                                            <button type="button" class="btn btn-outline-secondary mr-3">
//                                                <span id="d-all"></span> All
//                                            </button>
//
//                                            <button id="filter_notdone" type="button" class="btn btn-outline-secondary mr-3" onclick="changeFilter('filter_notdone')">
//                                                <span id="d-notdone"></span> Unfinished
//                                            </button>
//
//                                            <button id="filter_finished" type="button" class="btn btn-outline-secondary mr-3" onclick="changeFilter('filter_finished')">
//                                                <span id="d-finish"></span> Finished
//                                            </button>
//
//                                            <button id="filter_progress" type="button" class="btn btn-outline-secondary mr-3" onclick="changeFilter('filter_progress')">
//                                                <span id="d-progress"></span> In Progress
//                                            </button>
//
//                                            <button id="filter_overdue" type="button" class="btn btn-outline-secondary mr-3" onclick="changeFilter('filter_overdue')">
//                                                <span id="d-overdue"></span> Overdue
//                                            </button>
//
//                                            <button id="filter_duesoon" type="button" class="btn btn-outline-secondary mr-3" onclick="changeFilter('filter_duesoon')">
//                                                <span id="d-duesoon"></span> Due Soon
//                                            </button>
//                                            <button id="filter_duetoday" type="button" class="btn btn-outline-secondary mr-3" onclick="changeFilter('filter_duetoday')">
//                                                <span id="d-duetoday"></span> Due Today
//                                            </button>
//                                            <button id="filter_dueweek" type="button" class="btn btn-outline-secondary mr-3" onclick="changeFilter('filter_dueweek')">
//                                                <span id="d-dueweek"></span> Due Week
//                                            </button>
//                                        </div>
//                
//
//                                            `);
                $("#d-all").html(data.statistics.all);
                $("#d-notdone").html(data.statistics.notdone);
                $("#d-finish").html(data.statistics.finish);
                $("#d-progress").html(data.statistics.processing);
                $("#d-overdue").html(data.statistics.overdue);
                $("#d-duesoon").html(data.statistics.duesoon);
                $("#d-duetoday").html(data.statistics.duetoday);
                $("#d-dueweek").html(data.statistics.dueweek);

                $('[data-toggle="tooltip"]').tooltip();
                $.each(filterKey, function (key, value) {
                    var local = localStorage.getItem(value);
                    if (local == 1) {
                        $(`#${value}`).addClass("btn-outline-secondary-hover");
                    } else {
                        localStorage.setItem(value, 0);
                        $(`#${value}`).removeClass("btn-outline-secondary-hover");
                    }

                });
                $.each(adminFilterKey, function (k, v) {
                    var adminfilterTask = localStorage.getItem(v);
                    if (adminfilterTask === "true") {
                        $(`#${v}`).prop("checked", true);
                    } else {
                        $(`#${v}`).prop("checked", false);
                    }
                    $(`#${v}`).unbind('change').change(function () {

                        var $this = $(this);
                        var check = $this.is(':checked');
                        localStorage.setItem(v, check);
                        setTimeout(function () {
                            calendar.fullCalendar('refetchEvents');
                            getJobStatistics();
                        }, 500);

                    });

                });

                var typingTimer;
                var doneTypingInterval = 300;
                $('#text_filter').unbind('keyup').keyup(function () {
                    $("#text_filter_loading").show();
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(doneTyping, doneTypingInterval);

                });
                $('#text_filter_job').unbind('keyup').keyup(function () {
                    $("#text_filter_job_loading").show();
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(doneTyping, doneTypingInterval);

                });
                $('#filterButton').unbind('hover').hover(function () {
                    if ($('#filterCount').text() > 0) {
                        $('#clearFiltersOnHover').css({"display": "flex"});
                    }
                }, function () {
                    $('#clearFiltersOnHover').hide();
                });
                $('#clearFiltersOnHover').unbind('click').on('click', function (e) {
                    e.stopPropagation();
                    clearAllFilters();
                    $('#filterButton').trigger('mouseout');
                });
                updateFilterCount();

            },
            error: function (data) {
                $("#text_filter_loading").hide();
                console.log(data);
            }
        });
    };

    var doneTyping = function () {
//        setTimeout(function () {
        setTimeout(function () {
            calendar.fullCalendar('refetchEvents');
        }, 500);
//        }, 500);
    };

    var updateFilterCount = function () {

        var filterCount = 0;

        if ($('#text_filter').val() !== '') {
            filterCount++;
        }
        if ($('#text_filter_job').val() !== '') {
            filterCount++;
        }
        if ($('#selectedCreatedBy').val() != "") {
            filterCount++;
        }
        if ($('#selectedAssignees').val() != "") {
            filterCount++;
        }
        if ($('#is_hide_finished').is(':checked')) {
            filterCount++;
        }

        $('#filterCount').text(filterCount);
    };

    var clearAllFilters = function () {
        $('#text_filter').val('');
        $('#text_filter_job').val('');
//        $('#is_assigned').prop('checked', false);
//        localStorage.setItem("is_my_task", false);
//        $('#is_my_task').prop('checked', false);
//        localStorage.setItem("is_assigned", false);
        $('#is_hide_finished').prop('checked', false);
//        localStorage.setItem("text_filter", "");
        localStorage.setItem("is_hide_finished", false);
//        localStorage.setItem("filter_assignees", "");
//        localStorage.setItem("filter_createdby", "");
        $("#selectedAssignees").val("");
        $("#selectedCreatedBy").val("");
        updateFilterCount();
        // Clear previous results
        $('#results-list').empty();
        $('.selected-users').empty();

        calendar.fullCalendar('refetchEvents');
        getJobStatistics();
    };

//    var getEventLimit = function () {
//        if (window.innerWidth < 1400) {
//            return 3; // Giới hạn sự kiện hiển thị trên màn hình nhỏ
//        } else {
//            return true; // Không giới hạn hoặc dùng mặc định
//        }
//    };

    var CalendarApp = function () {
        this.$body = $("body");
        this.$modal = $('#modal-add-task');
        this.$formAdd = $("#form-add-task");
        this.$btnSave = $('.btn-save-task');
        this.$btnDelete = $('.btn-delete-task');
        this.$event = ('#external-events div.external-event');
        this.$calendar = $('#calendar');
        this.$saveCategoryBtn = $('.save-category');
        this.$categoryForm = $('#add-category form');
        this.$extEvents = $('#external-events');
        this.defaultEvents = [{
                title: 'Alo',
                start: '2023/12/16',
                className: 'bg-purple'
            }];
        this.$calendarObj = {name: "", start_date: null, due_date: null};
    };

    //lưu task lên server
    CalendarApp.prototype.createTask = function (object) {
        var $this = this;
        object._token = $("input[name=_token]").val();
        $.ajax({
            type: "POST",
            url: "/calendar/createTask",
            data: object,
            dataType: 'json',
            success: function (data) {
                logger('createTask', data);
                if (data.status === "success") {
                    $this.$modal.modal('hide');
                } else {
                    $.Notification.autoHideNotify(data.status, 'top center', "Notify", data.message);
                }
            },
            error: function (data) {
                console.log(data);
            }
        });
    };

    CalendarApp.prototype.deleteTask = function (object) {
        object._token = $("input[name=_token]").val();
        $.ajax({
            type: "POST",
            url: "/calendar/deleteTask",
            data: object,
            dataType: 'json',
            success: function (data) {
                logger('deleteTask', data);
            },
            error: function (data) {
                console.log(data);
            }
        });
    };

    // on drop */
    CalendarApp.prototype.onDrop = function (eventObj, date) {
        var $this = this;
        // retrieve the dropped element's stored Event Object
        var originalEventObject = eventObj.data('eventObject');
        var $categoryClass = eventObj.attr('data-class');
        // we need to copy it, so that multiple events don't have a reference to the same object
        var copiedEventObject = $.extend({}, originalEventObject);
        // assign it the date that was reported
        copiedEventObject.start = date;
        if ($categoryClass)
            copiedEventObject['className'] = [$categoryClass];
        // render the event on the calendar
        $this.$calendar.fullCalendar('renderEvent', copiedEventObject, true);
        // is the "remove after drop" checkbox checked?
        if ($('#drop-remove').is(':checked')) {
            // if so, remove the element from the "Draggable Events" list
            eventObj.remove();
        }
    };
    // on click vào 1 task cụ thể */
    CalendarApp.prototype.onEventClick = function (calEvent, jsEvent, view) {
        logger('calEvent', calEvent);
        detailTask(calEvent.id);
//        var $this = this;
//        $("#div_jobs").show();
//        $('.btn-delete-task').show();
//        $("#task_id").val(calEvent.id);
//        $("#campaign_id").val(calEvent.campaign_id).change();
//        $("#task_name").val(calEvent.title);
//        $("#task_priority").val(calEvent.className).change();
//        $("#task_type").val(calEvent.task_type).change();
//        $("#task_move").val("");
//        if (calEvent.created_by != null) {
//            var avatar = `<img class="rounded-circle img-cover m-r-5" src="/images/avatar/${calEvent.created_by}.jpg" height="40" width="40" data-toggle="tooltip" data-placement="top" title="">`;
//            $("#created_by_avatar").html(avatar);
//        } else {
//            $("#created_by_avatar").html("");
//        }
//        $("#dialog-task-title").html(`Task #${calEvent.id}`);
//        $(".btn-view-campaign").attr("href", `/campaign2?filter_id=${calEvent.campaign_id}`);
//        $(".div_job_group").hide();
//        $(".div_other").hide();
//        $('[data-toggle="tooltip"]').tooltip();
//        listJobs(calEvent.id);
//        listHistory(calEvent.id);
//        $("#modal-add-job").modal("hide");
//        var $modal = $("#modal-add-job").find(".modal-dialog");
//        var $pd = $modal.find('.panel-disabled');
//        $pd.remove();
//        if (!$this.$modal.is(':visible')) {
//            $this.$modal.modal({
//                backdrop: 'static',
//                keyboard: true
//            });
//        }
//        if (r == 1) {
//            const myInput = document.querySelector(".btn-repeat");
//            const fp = flatpickr(myInput, {
//                mode: "multiple",
//                dateFormat: "Y-m-d",
//                onChange: function (selectedDates, dateStr, instance) {
//                    $("#task_repeat").val(dateStr);
//                }
//            });
//            const move = flatpickr(".btn-move", {
//                dateFormat: "Y-m-d",
//                defaultDate: calEvent.start.format("YYYY-MM-DD"),
//                onChange: function (selectedDates, dateStr, instance) {
//                    $("#task_move").val(dateStr);
//                }
//            });
//
//            $.ajax({
//                type: "GET",
//                url: "/calendar/getRepeatTasks",
//                data: {campaign_id: calEvent.campaign_id},
//                dataType: 'json',
//                success: function (data) {
//                    logger('getRepeatTasks', data);
//                    fp.setDate(data.start_str, false, 'Y-m-d');
//                    $("#task_repeat").val(data.start_str);
//                },
//                error: function (data) {
//                    console.log(data);
//                }
//            });
//        }
//
//        $this.$btnSave.unbind('click').click(function () {
//
//            var taskType = $this.$formAdd.find("#task_type").val();
//            var campaignId = $this.$formAdd.find("#campaign_id").val();
//            var taskName = $this.$formAdd.find("#task_name").val();
//            var taskPriority = $this.$formAdd.find("#task_priority").val();
//            var taskRepeat = $this.$formAdd.find("#task_repeat").val();
//            var taskMove = $this.$formAdd.find("#task_move").val();
//            var object = {
//                "task_type": taskType,
//                "task_id": calEvent.id,
//                "start": calEvent.start.format("YYYY-MM-DD"),
//                "title": taskName,
//                "campaign_id": campaignId,
//                "task_priority": taskPriority,
//                "task_repeat": taskRepeat,
//                "task_move": taskMove
//            };
//            if (taskName !== null && taskName.length !== 0) {
//                calEvent.title = taskName;
//                calEvent.campaign_id = campaignId;
//                calendar.fullCalendar('updateEvent', calEvent);
//                $this.createTask(object);
//                setTimeout(function () {
//                    calendar.fullCalendar('refetchEvents');
//                }, 500);
//
//                //                $this.$modal.modal('hide');
//            } else {
//                alert('Enter task name');
//            }
//        });
//        $this.$btnDelete.unbind('click').click(function () {
//            $.confirm({
//                animation: 'rotateXR',
//                title: 'Confirm!',
//                content: 'Are you sure to delete this task?',
//                buttons: {
//                    confirm: {
//                        text: 'Confirm',
//                        btnClass: 'btn-red',
//                        action: function () {
//                            var object = {"task_id": calEvent.id, "task_repeat": $this.$formAdd.find("#task_repeat").val()};
//                            calendar.fullCalendar('removeEvents', function (ev) {
//                                return (ev._id === calEvent._id);
//                            });
//                            $this.$modal.modal('hide');
//                            $this.deleteTask(object);
//                            setTimeout(function () {
//                                calendar.fullCalendar('refetchEvents');
//                            }, 500);
//                        }
//                    },
//                    cancel: function () {
//
//                    }
//
//                }
//            });
//
//        });

    };
    //on select bấm vào calendar trống
    CalendarApp.prototype.onSelect = function (start, end, allDay) {
        if (r == 0) {
            return;
        }
        var $this = this;
        $("#curr_start").val(start.format("YYYY-MM-DD"));
        $this.$btnDelete.hide();
        $this.$formAdd.find("#task_id").val("");
        $this.$formAdd.find("#task_name").val("");
        $this.$formAdd.find("#task_type").val("other").change();
        $this.$formAdd.find("#campaign_id").val("").change();
        $this.$formAdd.find("#task_priority").val("b_medium").change();
        $("#dialog-task-title").html("Add Task");
        $("#created_by_avatar").html("");
        $("#div_jobs").hide();
        $(".div_job_group").hide();
        //clear dữ liệu cũ
        $('.btn-job-group').each(function (i, obj) {
            $(this).removeClass("btn-outline-dashed-active");
            var divId = $(this).attr("data-id");
            $(`#${divId}`).hide();
            $(`#${divId}_jobs`).jstree("destroy").empty();
        });
        $("#ck_auto_job").prop("checked", true).change();
        $("#summernote_job_des_task").summernote("destroy");
        $("#summernote_job_des_task").html("");
        initSummernote('#summernote_job_des_task', false);
        $("#username_task").val([""]).change();
        $("#job_member_task").val([""]).change();
        $("#job_type_task").val("other");
        $("#job_man_hour_task").val(null);
        $("#job_man_hour_unit_task").val("hours");
        $('#job_duedate_task').val(getDefaultDuaDate('17:30'));
        $("#penalty_task").val(20);
        $this.$modal.modal({
            backdrop: 'static'
        });
        $this.$btnSave.unbind('click').click(function () {
            var taskType = $this.$formAdd.find("#task_type").val();
            var campaignId = $this.$formAdd.find("#campaign_id").val();
            var taskName = $this.$formAdd.find("#task_name").val();
            var taskPriority = $this.$formAdd.find("#task_priority").val();
            var artist_setup_duedate = $("#artist_setup_duedate").val();
            var campaign_setup_duedate = $("#campaign_setup_duedate").val();
            var release_day_duedate = $("#release_day_duedate").val();
            var campaign_mid_duedate = $("#campaign_mid_duedate").val();
            var campaign_end_duedate = $("#campaign_end_duedate").val();
            var markup = $('#summernote_job_des_task').summernote('code');
            $("#job_des_task").val(markup);
            if (taskName !== null && taskName.length !== 0) {
                var initJobs = [];
                $('.btn-job-group').each(function (i, obj) {
                    if ($(this).hasClass("btn-outline-dashed-active")) {
                        var divId = $(this).attr("data-id");
                        var selectedNodes = $(`#${divId}_jobs`).jstree("get_selected", true);
                        $.each(selectedNodes, function (key, value) {
                            if (value.parent != "#") {
                                //                                var item = {};
                                //                                item["channel_id"] = value.parent;
                                //                                item["channel_name"] = value.original.parentname;
                                //                                item["hub_id"] = value.id;
                                initJobs.push(value.id);
                            }
                        });

                    }
                });



                var object = {
                    task_type: taskType,
                    title: taskName,
                    start: start.format("YYYY-MM-DD"),
                    end: end.format("YYYY-MM-DD"),
                    campaign_id: campaignId,
                    task_priority: taskPriority,
                    init_jobs: initJobs,
                    artist_setup_duedate: artist_setup_duedate,
                    campaign_setup_duedate: campaign_setup_duedate,
                    release_day_duedate: release_day_duedate,
                    campaign_mid_duedate: campaign_mid_duedate,
                    campaign_end_duedate: campaign_end_duedate,
                    ck_auto_job: $("#ck_auto_job").is(':checked'),
                    username_task: $("#username_task").val(),
                    job_type_task: $("#job_type_task").val(),
                    job_des_task: $("#job_des_task").val(),
                    job_member_task: $("#job_member_task").val(),
                    job_priority_task: $("#job_priority_task").val(),
                    job_man_hour_task: $("#job_man_hour_task").val(),
                    job_man_hour_unit_task: $("#job_man_hour_unit_task").val(),
                    job_duedate_task: $("#job_duedate_task").val(),
                    penalty_task: $("#penalty_task").val()
                };
                $this.createTask(object);
                setTimeout(function () {
                    //                    $this.$calendarObj.fullCalendar('refetchEvents');
                    calendar.fullCalendar('refetchEvents');
                }, 500);

            } else {
                alert('Enter task name');
            }
        });
        //        $this.$calendarObj.fullCalendar('unselect');
        calendar.fullCalendar('unselect');
    };
    // drag event */
    CalendarApp.prototype.enableDrag = function () {
        //init events
        $(this.$event).each(function () {
            // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
            // it doesn't need to have a start or end
            var eventObject = {
                title: $.trim($(this).text()) // use the element's text as the event title
            };
            //            console.log(eventObject);
            // store the Event Object in the DOM element so we can get to it later
            $(this).data('eventObject', eventObject);
            // make the event draggable using jQuery UI
            $(this).draggable({
                zIndex: 999,
                revert: true, // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
            });
        });
    };

    var today = new Date($.now());

    // Initializing */
    CalendarApp.prototype.init = function () {
        this.enableDrag();
        /*  Initialize the calendar  */
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        var form = '';

        var $this = this;
        var isHide = localStorage.getItem("is_hide_finished");
        var myTask = localStorage.getItem("is_my_task");
        var isAssigned = localStorage.getItem("is_assigned");
        //        var month = localStorage.getItem("calendar_month");
        var filter = {
            "filter_notdone": localStorage.getItem("filter_notdone"),
            "filter_finished": localStorage.getItem("filter_finished"),
            "filter_progress": localStorage.getItem("filter_progress"),
            "filter_overdue": localStorage.getItem("filter_overdue"),
            "filter_duesoon": localStorage.getItem("filter_duesoon")
        };

        calendar = $this.$calendar.fullCalendar({
            slotDuration: '00:15:00', /* If we want to split day time each 15minutes */
            minTime: '08:00:00',
            maxTime: '19:00:00',
            defaultView: 'month',
            //            height: 650,
            //            handleWindowResize: true,   
            //            height: $(window).height() - 200,   
            header: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
//            height: 'parent', // Để chiều cao lịch tự động điều chỉnh theo phần tử cha
//            contentHeight: 'auto', // Điều chỉnh chiều cao nội dung lịch tự động
            eventOrder: function (event1, event2) {
                if (event1.count_job > event2.count_job) {
                    return -1;
                } else if (event1.count_job < event2.count_job) {
                    return 1;
                } else {
                    return 0;
                }
            },
            events: function (start, end, timezone, callback) {
                $.ajax({
                    url: 'calendar/get',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        start: start.format(),
                        end: end.format(),
                        is_hide: localStorage.getItem("is_hide_finished"),
//                        is_assigned: localStorage.getItem("is_assigned"),
                        filter_assignees: $("#selectedAssignees").val(),
                        filter_createdby: $("#selectedCreatedBy").val(),
                        result_type: localStorage.getItem("result_type"),
                        filter: JSON.stringify({
                            "filter_notdone": localStorage.getItem("filter_notdone"),
                            "filter_finished": localStorage.getItem("filter_finished"),
                            "filter_progress": localStorage.getItem("filter_progress"),
                            "filter_overdue": localStorage.getItem("filter_overdue"),
                            "filter_duesoon": localStorage.getItem("filter_duesoon"),
                            "filter_duetoday": localStorage.getItem("filter_duetoday"),
                            "filter_dueweek": localStorage.getItem("filter_dueweek")
                        }),
                        text_filter: $(`#text_filter`).val(),
                        text_filter_job: $(`#text_filter_job`).val()

                    },
                    success: function (response) {
                        logger('events', response);
                        var events = response.tasks;
                        callback(events);
                        $("#text_filter_loading").hide();
                        $("#text_filter_job_loading").hide();
                        if ($(`#text_filter`).val() != "" || $(`#text_filter_job`).val() != "") {
                            if (response.result_task.length > 3) {
                                $("#results-task-ul").slimScroll({
                                    height: '335px',
                                    position: 'right',
                                    size: "5px",
                                    color: '#98a6ad',
                                    wheelStep: 30
                                });
                            }
                            if(response.result_job.length > 3){
                                $("#results-job-ul").slimScroll({
                                    height: '335px',
                                    position: 'right',
                                    size: "5px",
                                    color: '#98a6ad',
                                    wheelStep: 30
                                });
                            }
                        }
                        updateFilterCount();
                        $('#results-task-ul').empty();
                        $('#results-job-ul').empty();
//                        localStorage.result_type =='tasks';
                        if (response.result_task.length > 0 || response.result_job.length > 0) {
                            $("#result-tab").show();
                        } else {
                            $("#result-tab").hide();
                        }
                        response.result_task.forEach((task, index) => {
                            var img = "";
                            if (task.created_by != null) {
                                img = `<img class="rounded-circle img-cover m-r-5" src="/images/avatar/${task.created_by}.jpg"  height="35" width="35"  data-toggle="tooltip" data-placement="top" title="${task.created_by}"> `;
                            }
                            var resultItem = `
                                <div class="cur-poiter list-group-item list-group-item-action flex-column align-items-start result-item" data-tid="${task.id}" data-date="${task.start}">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div class="d-flex align-items-center w-80p">
                                            <span class="index mr-2">${index + 1}</span>
                                            ${img}
                                            <span class="mb-1">${task.title}</span>
                                        </div>
                                        <small>${task.start}</small>
                                    </div>
                                </div>
                            `;
                            $('#results-task-ul').append(resultItem);
                        });
                        var filterTextJob = $("#text_filter_job").val();
                        response.result_job.forEach((job, index) => {
                            var status = '';
                            var color = 'bg-primary';
                            var tooltip = `Job #${job.id} is new`;
                            if (job.status === 3) {
                                status = '<i class="fa fa-check"></i>';
                                color = 'bg-done';
                                tooltip = `Job #${job.id} is completed`;
                            } else if (job.status === 1) {
                                status = '<i class="fa fa-cogs"></i>';
                                color = 'badge-warning';
                                tooltip = `Job #${job.id} is in progress`;
                            } else if (job.status === 2) {
                                status = '<i class="fa fa-hourglass-1"></i>';
                                color = 'badge-secondary';
                                tooltip = `Job #${job.id} is on hold`;
                            }
                            var img = "";
                            if (job.admin !== null) {
                                img = `<img class="rounded-circle img-cover m-r-5" src="/images/avatar/${job.admin}.jpg"  height="35" width="35"  data-toggle="tooltip" data-placement="top" title="${job.admin}"> `;
                            }
                            var jobTit = `${job.name}`;
                            if (job.job_group !== null) {
                                jobTit = `${job.job_group} | ${job.name}`;
                            }
                            const regex = new RegExp(filterTextJob, 'gi');
                            const highlightedName = jobTit.replace(regex, (match) => `<span class="highlight-text-red">${match}</span>`);
                            var avatar =
                            `<div class="avatar ml-40 mr-40">
                                <img class="rounded-circle img-cover img-list" src="/images/avatar/${job.username}.jpg" onerror="this.onerror=null;this.src='images/default-avatar.png';" alt="avatar" height="35" width="35" data-toggle="tooltip" data-placement="top" title="${job.username}">`;
                            var members = JSON.parse(job.member);
                            $.each(members, function (k, v) {
                                avatar +=
                                        `<img class="rounded-circle img-cover img-list" src="/images/avatar/${v}.jpg" onerror="this.onerror=null;this.src='images/default-avatar.png';" alt="avatar" height="35" width="35" data-toggle="tooltip" data-placement="top" title="${v}">`;
                            });
                            avatar += `</div>`;
                            var estimateDiv = "";
                            if (job.man_hour !== 0) {
                            var estimate = `${job.man_hour} hours`;
                            if(job.man_hour<=1){
                                estimate = `${job.man_hour} hour`;
                            }
                            estimateDiv += `<div class="ml-40 mr-40 text-nowrap" data-toggle="tooltip" data-placement="top" title="Estimate ${estimate}">
                                    <i class="fa fa fa-hourglass-o"></i> <span >${estimate}</span>
                                </div>`;
                            }
                            var resultItem = `
                                <div class="cur-poiter list-group-item list-group-item-action flex-column align-items-start result-item" data-jid="${job.id}" >
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div class="d-flex flex-1 align-items-center w-80p">
                                            <span class="index mr-2">${index + 1}</span>
                                            ${img}
                                            <span class="mb-1">${highlightedName}</span>
                                        </div>
                            
                                        ${estimateDiv}
                                        <div class="todo-badge-wrapper d-flex badge-${job.id}">
                                        <span class="badge ${color} ml-40 mr-120" data-toggle="tooltip" data-placement="top" title="${tooltip}">${status} ${job.job_type.toUpperCase()}</span>
                                    </div>
                                        ${avatar}
                                        <small><a href="javascript:detailTask(${job.task_id});" onclick="event.stopPropagation();">Task<br>#${job.task_id}</a></small>
                                    </div>
                                </div>
                            `;
                            $('#results-job-ul').append(resultItem);
                        });

                        $('#results-task-ul').unbind("click").on('click', '.result-item', function () {
                            var dateStart = $(this).data('date');
                            var tid = $(this).data('tid');
                            calendar.fullCalendar('gotoDate', dateStart);
                            $("#filterModal").modal("hide");
                            setTimeout(function () {
                                detailTask(tid);
                            }, 1000);
                        });
                        $('#results-job-ul').unbind("click").on('click', '.result-item', function () {
                            var jid = $(this).data('jid');
//                            $("#filterModal").modal("hide");
                            detailJob(jid);
                        });
                        $('#clearFilters').unbind("click").on('click', function () {
                            clearAllFilters();
                        });
                        getJobStatistics();

                    },
                    error: function () {

                    }
                });
            },
            viewRender: function (view, element) {

                var start = view.start.format('YYYY-MM-DD');
                var end = view.end.format('YYYY-MM-DD');
                var month = view.title;
                localStorage.setItem("calendar_start", start);
                localStorage.setItem("calendar_end", end);
                localStorage.setItem("calendar_month", month);
                logger("date", `${start} - ${end} - ${month}`);
                //                localStorage.setItem("text_filter", "");
                //                $(`#text_filter`).val("");

                $('[data-toggle="tooltip"]').tooltip();

            },
            editable: true,
            droppable: true, // this allows things to be dropped onto the calendar !!!
            eventLimit: getEventLimit(), // allow "more" link when too many events
            selectable: true,
            drop: function (date) {
                $this.onDrop($(this), date);
            },
            select: function (start, end, allDay) {
                $this.onSelect(start, end, allDay);
            },
            eventClick: function (calEvent, jsEvent, view) {
                $this.onEventClick(calEvent, jsEvent, view);
            },
            eventRender: function (event, element) {
                //                logger("eventRender", event);
                //                element.find(".fc-title").prepend("<i class='fa fa-check'></i>");
                element.addClass(`task-click-${event.id}`);
                if (event.count_job > 0) {
                    element.addClass("relative").append(`<span id="notify-count" class="notify-badge notify-badge-calendar">${event.count_job}</span>`);
                } else {
                    element.addClass("bg-done");
                }
                //                if (event.icon) {
                //                }
            }

        });

    };

    //init CalendarApp
    $.CalendarApp = new CalendarApp;
    $.CalendarApp.Constructor = CalendarApp;

}(window.jQuery),
        function ($) {
            "use strict";
            $.CalendarApp.init();
        }(window.jQuery);
