
var r = $("meta[name='rs']").attr('content');
var usr = $("meta[name='us']").attr('content');
var calendar = null;
var jobStatusChanged = true;

var getDefaultDuaDate = function (time) {
    const now = new Date();
    const year = now.getFullYear();
    const month = (now.getMonth() + 1).toString().padStart(2, '0');
    const day = now.getDate().toString().padStart(2, '0');
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const defaultValue = `${year}-${month}-${day}T${time}`;
    return defaultValue;
};

var getEventLimit = function () {
    if (window.innerWidth < 1400) {
        return 3; // Giới hạn sự kiện hiển thị trên màn hình nhỏ
    } else {
        return true; // Không giới hạn hoặc dùng mặc định
    }
};

function changeSwitchery(element, checked) {
    if ((element.is(':checked') && checked == false) || (!element.is(':checked') && checked == true)) {
        element.parent().find('.switchery').trigger('click');
    }
}

function initShowRead() {
    var isShowRead = localStorage.getItem("is_show_read");
    if (isShowRead == null || isShowRead == "true") {
        localStorage.setItem('is_show_read', true);
        changeSwitchery($("#ck_show_read"), true);
    } else {
        localStorage.setItem('is_show_read', false);
        changeSwitchery($("#ck_show_read"), false);
    }
}

$("#ck_show_read").change(function () {
    var $this = $(this);
    var check = $this.is(':checked');
    localStorage.setItem('is_show_read', check);
    listNotify();
});

$(".noti-title").click(function (e) {
    e.stopPropagation();
});

function preventBtnEvent() {
    $('.btn-action-notify').unbind("click").click(function (event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).siblings().each(function () {
            //            if($(this).hasClass("show")){
            //                $(this).removeClass("show");
            //            }else{
            //                $(this).addClass("show");
            //            }
            //            $(this).toggleClass("show")
        });

    });
    $('.sub-aciton-item').unbind("click").click(function (event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).closest('.sub-aciton-menu').removeClass("show");
        console.log('click on sub');
    });
    //    $(".notify-item").unbind("hover").hover(function(){
    //       $(this).find(".div-dropdown-action").show();
    //    });
    $('.notify-item').off();
    $('.notify-item').on('mouseenter', function () {
        $(this).find(".div-dropdown-action").show();
    });

    $('.notify-item').on('mouseleave', function () {
        $(this).find(".div-dropdown-action").hide();

    });

}

var progress = {
    done: 0,
    total: 0,
    per: 0
};

var loadProgress = function () {
    $("#pro-done").html(`${progress.done} job completed`);
    $("#pro-total").html(`${progress.total} total`);
    $("#pro-task-per").html(`${progress.per}%`);
    $("#pro-task").css({
        "width": `${progress.per}%`
    });
};

function event() {

    $(".btn-reload-job").unbind('click').click(function () {
        var jobId = $("#job_id").val();
        detailJob(jobId);
    });

    $(".btn-reload-list-job").unbind('click').click(function () {

        var taskId = $("#task_id").val();
        listJobs(taskId);
        listHistory(taskId);
    });

    $(".btn-clone-job").unbind('click').click(function () {
        var jobId = $(this).attr("data-job-id");
        $.confirm({
            animation: 'rotateXR',
            title: 'Confirm!',
            content: 'Are you sure to clone this job?',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-red',
                    action: function () {
                        $.ajax({
                            type: "POST",
                            url: "/calendar/cloneJob",
                            data: {
                                job_id: jobId,
                                _token: $("input[name=_token]").val()
                            },
                            dataType: 'json',
                            success: function (data) {
                                logger('cloneJob', data);
                                if (data.status === 'success') {
                                    listJobs(data.task_id);
                                }
                                $.Notification.autoHideNotify(data.status, 'top center', "Notify", data
                                        .message);
                            },
                            error: function (data) {
                                console.log(data);
                            }
                        });
                    }
                },
                cancel: function () {

                }

            }
        });


    });

    $(".btn-delete-job").unbind('click').click(function () {
        var jobId = $(this).attr("data-job-id");
        var taskId = $("#task_id").val();
        $.confirm({
            animation: 'rotateXR',
            title: 'Confirm!',
            content: 'Are you sure to delete this job?',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-red',
                    action: function () {
                        $.ajax({
                            type: "POST",
                            url: "/calendar/deleteJob",
                            data: {
                                job_id: jobId,
                                _token: $("input[name=_token]").val()
                            },
                            dataType: 'json',
                            success: function (data) {
                                logger('deleteJob', data);
                                if (data.status === 'success') {
                                    $(`#li-${jobId}`).remove();
                                    $("#modal-add-job").modal("hide");
//                                     $("#job_status").unbind('change');
                                }
                                $.Notification.autoHideNotify(data.status, 'top center', "Notify", data
                                        .message);
                            },
                            error: function (data) {
                                console.log(data);
                            }
                        });
                    }
                },
                cancel: function () {

                }

            }
        });


    });

    $(".li-job").unbind('click').click(function () {
        var jobId = $(this).attr("data-job-id");
        detailJob(jobId);

    });

    $(".ck-job").unbind().change(function () {
        var $this = $(this);
        var check = $this.is(':checked');
        $.confirm({
            animation: 'rotateXR',
            title: 'Confirm!',
            content: 'Are you sure to change the status of this job?',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-red',
                    action: function () {

                        var taskId = $("#task_id").val();
                        var jobId = $this.attr('data-job-id');
                        var lastStatus = $this.attr('data-last-status');
                        if (lastStatus == 3) {
                            lastStatus = 0;
                        }
                        var title = $this.attr('data-job-title');
                        $.ajax({
                            type: "POST",
                            url: "/calendar/createJob",
                            data: {
                                job_id: jobId,
                                job_status: (check ? 3 : lastStatus),
                                _token: $("input[name=_token]").val(),
                                job_title: title,
                                action_type: 'quick'
                            },
                            dataType: 'json',
                            success: function (data) {
                                logger('createJob', data);
                                if (data.status === "success") {
                                    //                        $this.attr('data-last-status', data.data.status);
                                    var status = '';
                                    var color = 'bg-primary';
                                    var tooltip = `Job #${data.data.id} is new`;
                                    if (data.data.status == 3) {
                                        status = '<i class="fa fa-check"></i>';
                                        color = 'bg-done';
                                        tooltip = `Job #${data.data.id} is completed`;
                                        $(`#job-title-${jobId}`).addClass("text-line-through");
                                        progress.done = progress.done + 1;
                                        progress.per = Math.round(progress.done / progress.total * 100);
                                    } else if (data.data.status == 1) {
                                        status = '<i class="fa fa-cogs"></i>';
                                        color = 'badge-warning';
                                        tooltip = `Job #${data.data.id} is in progress`;
                                        $(`#job-title-${jobId}`).removeClass("text-line-through");
                                    } else if (data.data.status == 0) {
                                        $(`#job-title-${jobId}`).removeClass("text-line-through");
                                        progress.done = progress.done - 1;
                                        progress.per = Math.round(progress.done / progress.total * 100);
                                    }
                                    $(`.badge-${jobId}`).html(
                                            `<span class="badge ${color} ml-40 mr-120" data-toggle="tooltip" data-placement="top" title="${tooltip}">${status} ${data.data.job_type.toUpperCase()}</span>`
                                            );
                                    listHistory(taskId);
                                    loadProgress();

                                } else {
                                    $.Notification.autoHideNotify(data.status, 'top center', "Notify", data
                                            .message);
                                    if (check) {
                                        $this.prop('checked', false);
                                    } else {
                                        $this.prop('checked', true);
                                    }
                                }
                            },
                            error: function (data) {
                                console.log(data);
                            }
                        });

                    }
                },
                cancel: function () {
                    //                    $.alert('Canceled!');
                    if (check) {
                        $this.prop('checked', false);
                    } else {
                        $this.prop('checked', true);
                    }
                }

            }
        });

    });

    $(".reaction").unbind('click').click(function () {
        var messageElem = $(this).siblings('.comment-bg');
        var offset = $(this).offset();
        if ($("#emojiModal").is(":visible")) {
            $('#emojiModal').hide();
        } else {
            $('#emojiModal').data('messageElem', messageElem).css({
                top: offset.top - 210,
                left: offset.left - 90
            }).show();
        }
    });

    $(".emoji-picker img").unbind('click').click(function () {
        var messageElem = $('#emojiModal').data('messageElem');
        $('#emojiModal').hide();
        var messageId = $(messageElem).attr("data-id");
        var emojiSrc = $(this).attr('name');
        $.ajax({
            method: 'POST',
            url: '/calendar/updateComment',
            data: {
                emoji: emojiSrc,
                id: messageId,
                _token: $("input[name=_token]").val()
            },
            success: function (response) {
                // Xử lý sau khi lưu thành công
                logger("updateComment", response);
                if (response.status === "success") {

                    // Duyệt qua mỗi emoji và danh sách người dùng
                    var reactionDiv = $('<div class="div_emoji"></div>');
                    $.each(response.reactions, function (emoji, usernames) {
                        // Tạo thẻ div để chứa emoji và danh sách người dùng

                        // Hiển thị emoji
                        var imgTag = $(`<img src="/images/emojis/${emoji}.gif" class="emoji-show">`);
                        // Hiển thị danh sách người dùng trong tooltip
                        var usernamesList = $('<ul class="list-unstyled text-left"></ul>');
                        $.each(usernames, function (index, username) {
                            var usernameItem = $('<li></li>').text(username);
                            usernamesList.append(usernameItem);
                        });
                        var couter = $('<span class="font-13 text-muted"></span>').text(usernames.length > 1 ? usernames.length : "");

                        // Tạo tooltip mới
                        imgTag.addClass('emoji-tooltip').attr('data-toggle', 'tooltip').attr('data-html', true).attr('title', usernamesList[0].outerHTML);
                        reactionDiv.append(imgTag);
                        reactionDiv.append(couter);
                        messageElem.find(".emojis_wrap").html(reactionDiv);

                    });

                    // Kích hoạt tooltip của Bootstrap
                    $('[data-toggle="tooltip"]').tooltip();
                }
            }
        });
    });

    $('.comment-wrap').off();
    $('.comment-wrap').on('mouseenter', function () {
        $(this).find(".comment-toolbar").show();
    });

    $('.comment-wrap').on('mouseleave', function () {
        $(this).find(".comment-toolbar").hide();

    });

    $(document).click(function (event) {
        var target = $(event.target);
        if (!target.closest('#emojiModal').length && !target.closest('.reaction').length) {
            $('#emojiModal').hide();
        }
    });

}

markReadNotify = function (object) {
    $.ajax({
        type: "GET",
        url: "/calendar/notify/update",
        data: object,
        dataType: 'json',
        success: function (data) {
            logger('markReadNotify', data);
            listNotify();

        },
        error: function (data) {
            console.log(data);
        }
    });
};

actionMarkAsRead = function (id) {
    $(`#read-${id}`).toggleClass("font-bold");
    var icon = $(`#icon-${id}`);
    if (icon.hasClass("fa-eye")) {
        icon.removeClass("fa-eye");
        icon.addClass("fa-eye-slash");
    } else {
        icon.removeClass("fa-eye-slash");
        icon.addClass("fa-eye");
    }
    markReadNotify({id: id});


};

actionDeleteComment = function (div) {

    var id = $(div).attr("data-id");
    $.confirm({
        animation: 'zoom',
        title: 'Confirm!',
        content: 'Are you sure you want to delete this comment?',
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-red',
                action: function () {
                    $.ajax({
                        type: "POST",
                        url: "/calendar/deleteComment",
                        data: {
                            id: id,
                            _token: $("input[name=_token]").val()
                        },
                        dataType: 'json',
                        success: function (data) {
                            logger('deleteComment', data);
                            if (data.status === "success") {
                                $(div).closest(".comment-wrap").remove();
                            } else {
                                $.Notification.autoHideNotify("error", 'top center', "Notify", data.message);
                            }

                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });
                }
            },
            cancel: function () {

            }

        }
    });
};

readNotify = function (jobId) {
    markReadNotify({job_id: jobId});
    if (window.location.pathname === "/calendar") {
        detailJob(jobId);
        listNotify();
    } else {
        window.open(`/calendar?jid=${jobId}`, "_blank");
    }
};

listHistory = function (taskId) {
    $(".div_task_history").html("");
    $.ajax({
        type: "GET",
        url: "/calendar/getHistory",
        data: {
            task_id: taskId
        },
        dataType: 'json',
        success: function (data) {
            logger('task_history', data);
            var html = `<ul>`;
            $.each(data.data, function (key, value) {
                html += `<li class="m-b-5">
                <div class="d-flex align-items-center">
                    <div data-toggle="tooltip" data-placement="top" title="${value.username}">
                        <img class="rounded-circle img-cover" src="${value.avatar}" onerror="this.onerror=null;this.src='images/default-avatar.png';" alt="avatar" height="40" width="40">
                    </div>
                    <div class="m-l-5">
                        <div><span><strong>${value.username}</strong> has ${value.title} <strong><a class="cur-poiter" onclick="detailJob(${value.job_id})">#${value.job_id}</a></strong></span></div>
                        <div><span class="text-muted font-12">${value.date} at ${value.time}</span></div>
                    </div>
                </div>
            </li>`;

            });
            html += `</ul>`;
            $(".div_task_history").html(html);
            $('[data-toggle="tooltip"]').tooltip();
            event();
        },
        error: function (data) {
            console.log(data);
        }
    });
};

listComment = function (jobId) {
    $(".div_list_comment").html("");
    $(".div_comment_content").hide();
    $(".div_comment_content").attr("data-comment-job-id", jobId);
    $.ajax({
        type: "GET",
        url: "/calendar/getComments",
        data: {
            job_id: jobId
        },
        dataType: 'json',
        success: function (data) {
            logger('list_comment', data);
            if (data.data.length > 0) {
                $(".div_comment_content").show();
            }
            var html = `<ul id="comment_content" class="list-unstyled">`;
            $.each(data.data, function (key, value) {
                var mb = "m-b-20";
                if (!$.isEmptyObject(value.new_reaction)) {
                    mb = "m-b-30";
                    // Duyệt qua mỗi emoji và danh sách người dùng
                    var reactionDiv = $('<div class="div_emoji"></div>');
                    $.each(value.new_reaction, function (emoji, usernames) {
                        // Tạo thẻ div để chứa emoji và danh sách người dùng

                        // Hiển thị emoji
                        var imgTag = $(`<img src="/images/emojis/${emoji}.gif" class="emoji-show">`);
                        // Hiển thị danh sách người dùng trong tooltip
                        var usernamesList = $('<ul class="list-unstyled text-left"></ul>');
                        $.each(usernames, function (index, username) {
                            var usernameItem = $('<li></li>').text(username);
                            usernamesList.append(usernameItem);
                        });
                        var couter = $('<span class="font-13 text-muted"></span>').text(usernames.length > 1 ? usernames.length : "");

                        // Tạo tooltip mới
                        imgTag.addClass('emoji-tooltip').attr('data-toggle', 'tooltip').attr('data-html', true).attr('title', usernamesList[0].outerHTML);
                        reactionDiv.append(imgTag);
                        reactionDiv.append(couter);


                    });
                } else {
                    var reactionDiv = $('<span></span>');
                }

                if (value.username !== data.user) {
                    html += `<li class="${mb} comment-wrap">
                            <div class="d-flex align-items-center ">
                                <div class="" data-toggle="tooltip" data-placement="top" title="${value.username}">
                                    <img class="rounded-circle img-cover" src="/images/avatar/${value.username}.jpg" onerror="this.onerror=null;this.src='images/default-avatar.png';" alt="avatar" height="40" width="40">
                                </div>
                                <div class="m-l-5 comment-bg position-relative" data-id="${value.id}">
                                    <div class="comment-bg-content"><span>${value.content}</span></div>
                                    <div><span class="text-muted font-12">${value.date}</span></div>
                                    <div class="emojis_wrap">${reactionDiv.length > 0 ? reactionDiv[0].outerHTML : ""}</div>
                                </div>
                                <div class="m-l-10 disp-none btn-circle btn-circle-hover comment-toolbar reaction "><i class="ion-happy font-20"></i></div>
                                <div onclick="actionDeleteComment(this)" data-id="${value.id}" class="m-l-10 disp-none btn-circle btn-circle-hover comment-toolbar" data-toggle="tooltip" data-placement="top" title="Remove this comment">
                                    <i class="ion-trash-a font-20"></i>
                                </div>
                            </div>
                        </li>`;

                } else {
                    html += `<li class="${mb} comment-wrap">
                            <div class="d-flex align-items-center justify-content-end ">
                                <div onclick="actionDeleteComment(this)" data-id="${value.id}" class="m-l-10 disp-none btn-circle btn-circle-hover comment-toolbar" data-toggle="tooltip" data-placement="top" title="Remove this comment">
                                    <i class="ion-trash-a font-20"></i>
                                </div>
                                <div class="m-l-10 disp-none btn-circle btn-circle-hover comment-toolbar reaction"><i class="ion-happy font-20"></i></div>
                                <div class="m-l-10 comment-bg position-relative" data-id="${value.id}">
                                    <div class="comment-bg-content"><span>${value.content}</span></div>
                                    <div><span class="text-muted font-12">${value.date}</span></div>
                                    <div class="emojis_wrap">${reactionDiv.length > 0 ? reactionDiv[0].outerHTML : ""}</div>
                                </div>
                                <div class="m-l-5" data-toggle="tooltip" data-placement="top" title="${value.username}">
                                    <img class="rounded-circle img-cover" src="/images/avatar/${value.username}.jpg" onerror="this.onerror=null;this.src='images/default-avatar.png';" alt="avatar" height="40" width="40">
                                </div>
                            </div>
                        </li>`;

                }

            });
            html += `</ul>`;
            $(".div_list_comment").html(html);
            $('[data-toggle="tooltip"]').tooltip();
            event();

        },
        error: function (data) {
            console.log(data);
        }
    });
};

listJobs = function (taskId) {
    var $modal = $("#modal-add-task").find(".modal-dialog");
    $modal.append('<div class="panel-disabled"><div class="loader-1"></div></div>');
    var $pd = $modal.find('.panel-disabled');
    $(".div_list_jobs").html("");
    $.ajax({
        type: "GET",
        url: "/calendar/getJobs",
        data: {
            task_id: taskId,
            job_owner_filter: $("#job_owner_filter").val()
        },
        dataType: 'json',
        success: function (data) {
            logger('getJobs', data);
            var html = `<ul class="list-unstyled">`;
            var filterTextJob = $("#text_filter_job").val();
            $.each(data.data, function (key, value) {
                var priority = '';
                var finish = '';
                var checked = '';
                var status = '';
                var color = 'bg-primary';
                var tooltip = `Job #${value.id} is new`;
                if (value.priority === 'd_urgent') {
                    priority =
                            `<a class="todo-item-favorite ml-40 mr-40 color-y"><i class="ti-star"></i></a>`;
                }
                if (value.status === 3) {
                    finish = "text-line-through";
                    checked = "checked";
                    status = '<i class="fa fa-check"></i>';
                    color = 'bg-done';
                    tooltip = `Job #${value.id} is completed`;
                } else if (value.status === 1) {
                    status = '<i class="fa fa-cogs"></i>';
                    color = 'badge-warning';
                    tooltip = `Job #${value.id} is in progress`;
                } else if (value.status === 2) {
                    status = '<i class="fa fa-hourglass-1"></i>';
                    color = 'badge-secondary';
                    tooltip = `Job #${value.id} is on hold`;
                }
                var colorDue = "";
                if (0 < value.remain && value.remain < 7200) {
                    colorDue = "color-y";
                } else if (value.remain <= 0) {
                    colorDue = "color-red";
                }
                var jobTit = `${value.name}`;
                if (value.job_group != null) {
                    jobTit = `${value.job_group} | ${value.name}`;
                }

                const regex = new RegExp(filterTextJob, 'gi');
                const highlightedName = jobTit.replace(regex, (match) => `<span class="highlight-text-red">${match}</span>`);
                var members = JSON.parse(value.member);
                var wid = "";
                if(members!=null && members.length>4){
                    wid = "w-100px flex-wrap";
                }
                var avatar =
                        `<div class="avatar ml-40 mr-40"><div class="d-flex p-t-5 p-b-5 ${wid}">
                                <img class="rounded-circle img-cover img-list w-35px h-35px mw-35px m-b-5" src="${value.avatar}" onerror="this.onerror=null;this.src='images/default-avatar.png';" data-toggle="tooltip" data-placement="top" title="${value.username}">`;
                $.each(members, function (k, v) {
                    avatar +=
                            `<img class="rounded-circle img-cover img-list w-35px h-35px mw-35px m-b-5" src="/images/avatar/${v}.jpg" onerror="this.onerror=null;this.src='images/default-avatar.png';" data-toggle="tooltip" data-placement="top" title="${v}">`;
                });
                avatar += `</div></div>`;
                html += `<li id="li-${value.id}" class="border border-dashed m-b-5 cur-poiter " >
                            <div class="d-flex justify-content-sm-between justify-content-end align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="custom-control">                                                   
                                        <div class="checkbox checkbox-primary d-flex align-items-center checkbox-rounded">
                                            <input class="checkbox-multi ck-job" type="checkbox" data-job-id="${value.id}" data-last-status="${value.status}"  data-job-title='${value.name}' ${checked}>
                                            <label class="m-b-18 p-l-0" for="ck-job-${value.id}"></label>
                                        </div>
                                    </div>
                                    <p id="job-title-${value.id}" data-job-id="${value.id}" class="li-job w-100 m-0 truncate ${finish}">${highlightedName}</p>
                                </div>
                                <div class="todo-item-action d-flex align-items-center">`;
                if (value.job_man_hour !== "") {
                    html += `<div class="ml-40 mr-40 text-nowrap" data-toggle="tooltip" data-placement="top" title="Estimate ${value.job_man_hour} ${value.job_man_hour_unit}">
                                        <i class="fa fa fa-hourglass-o"></i> <span >${value.job_man_hour} ${value.job_man_hour_unit}</span>
                                    </div>`;
                }
                if (value.cal_duedate !== "") {
                    html += `<div class="ml-40 mr-40 text-nowrap ${colorDue}" data-toggle="tooltip" data-placement="top" title="Expires on ${value.duatime}">
                                        <i class="fa fa-calendar"></i> <span >${value.cal_duedate}</span>
                                    </div>`;
                }
                if (value.comment > 0) {
                    html += `<div class="ml-40 mr-40 text-nowrap" data-toggle="tooltip" data-placement="top" title="${value.comment} on this job">
                                        <i class="fa fa-comment m-r-3"></i> <span class="font-12">${value.comment}</span>
                                    </div>`;
                }

                html += `<div class="todo-badge-wrapper d-flex badge-${value.id}">
                                        <span class="badge ${color} ml-40 mr-120" data-toggle="tooltip" data-placement="top" title="${tooltip}">${status} ${value.job_type}</span>
                                    </div>
                                    ${avatar}
                                    ${priority}`;
                if (value.is_admin_music || value.admin==usr) {
                    html +=
                            `<a class="todo-item-delete ml-40 mr-40 btn-clone-job" data-job-id="${value.id}" data-toggle="tooltip" data-placement="top" title="Clone"><i class="ti-layers font-20"></i></a>
                        <a class="todo-item-delete ml-40 mr-40 btn-delete-job" data-job-id="${value.id}" data-toggle="tooltip" data-placement="top" title="Delete"><i class="ti-trash color-red font-20"></i></a>
                        `;
                }
                html += `</div></div></li>`;

            });
            html += `</ul>`;

            $(".div_list_jobs").html(html);
            progress.done = data.done;
            progress.total = data.total;
            progress.per = data.per;
            loadProgress();
            $('[data-toggle="tooltip"]').tooltip();
            event();
            $pd.remove();
        },
        error: function (data) {
            console.log(data);
            $pd.remove();
        }
    });
};

detailJob = function (jobId) {
    formJobClear();
    $("#job_comment").val("");
    var $modal = $("#modal-add-job").find(".modal-dialog");
    $modal.append('<div class="panel-disabled"><div class="loader-1"></div></div>');
    var $pd = $modal.find('.panel-disabled');
    if (!$("#modal-add-job").hasClass('show')) {
        $("#modal-add-job").modal("show");
    }

    $.ajax({
        type: "GET",
        url: "/calendar/findJob",
        data: {
            job_id: jobId,
            _token: $("input[name=_token]").val()
        },
        dataType: 'json',
        success: function (data) {
            logger('job', data);
            if (data.status === 'success') {
                $("#job_code").val(data.data.job_code);
                $("#job_title").val(data.data.name);
                if (data.data.tutorial != null) {
                    $("#job-tut-link").html(`<a target="_blank" href="${data.data.tutorial}">${data.data.tutorial}</a>`);
                }
                $("#job_des").val(data.data.des);
                $("#job_type").val(data.data.job_type).change();
                $("#username").val(data.data.username).change();
                $("#job_member").val(JSON.parse(data.data.member)).change();
//                $("#username").prop("disabled", true);
                $('.search_select').selectpicker('destroy');
                $('.search_select').selectpicker('render');
                $("#job_priority").val(data.data.priority).change();
                jobStatusChanged = false;
                $("#job_status").val(data.data.status).trigger('change');
                $("#job_man_hour").val(data.data.job_man_hour);
                $("#job_man_hour_unit").val(data.data.job_man_hour_unit);
                $("#job_result").val(data.data.result);
                $("#job_id").val(data.data.id);
                $("#job_duedate").val(data.data.due_date);
                $("#penalty").val(data.data.penalty);
                if (jobId === "") {
                    $("#comment").hide();
                    $(".btn-delete-job").hide();
                } else {
                    listComment(jobId);
                    $(".btn-delete-job").show();
                    $("#comment").show();
                    $("#btn-delete-job").attr("data-job-id", jobId);
                }
                if (data.data.job_detail_html !== null) {
                    $("#job_detail").html(data.data.job_detail_html);
                    $("#job_detail_wrap").show();
                } else {
                    $("#job_detail_wrap").hide();
                }
                var taskName = "";
                if (data.data.task_name != "") {
                    taskName = `<div><span class="font-14 text-muted">${data.data.task_name}</span></div>`;
                }
                var h =
                        `<img class="rounded-circle img-cover m-r-5" src="/images/avatar/${data.data.admin}.jpg" height="40" width="40">
                        <div>
                            <h5 class="modal-title mt-0 justify-content-start"><span>Job #${jobId} - <a class="color-h cur-poiter" onclick="detailTask(${data.data.task_id})">Task #${data.data.task_id}</a></span></h5>
                            ${taskName}
                            <div><span class="font-12 text-muted">${data.data.created_beauty}</span></div>
                        </div>`;
                $("#dialog-job-title").html(h);
                if (data.data.des == null) {
                    data.data.des = "";
                }
                $("#summernote_job_des").summernote("destroy");
                $("#summernote_job_des").html(data.data.des);
                initSummernote('#summernote_job_des', false);
            }
            $pd.remove();

        },
        error: function (data) {
            logger("calendar/findJob", data);
            $pd.remove();
        }
    });
};

createTask = function (object) {
    object._token = $("input[name=_token]").val();
    $.ajax({
        type: "POST",
        url: "/calendar/createTask",
        data: object,
        dataType: 'json',
        success: function (data) {
            logger('createTask', data);
            if (data.status === "success") {
                $("#modal-add-task").modal('hide');
                setTimeout(function () {
                    calendar.fullCalendar('refetchEvents');
                }, 500);
            } else {
                $.Notification.autoHideNotify(data.status, 'top center', "Notify", data.message);
            }
        },
        error: function (data) {
            console.log(data);
        }
    });
};

deleteTask = function (object) {
    object._token = $("input[name=_token]").val();
    $.ajax({
        type: "POST",
        url: "/calendar/deleteTask",
        data: object,
        dataType: 'json',
        success: function (data) {
            logger('deleteTask', data);
            setTimeout(function () {
                calendar.fullCalendar('refetchEvents');
            }, 500);
        },
        error: function (data) {
            console.log(data);
        }
    });
};

detailTask = function (taskId) {
//    $(`.task-click-${taskId}`).click();
    var $modal = $("#modal-add-task").find(".modal-dialog");
    $modal.append('<div class="panel-disabled"><div class="loader-1"></div></div>');
    var $pd = $modal.find('.panel-disabled');
    $.ajax({
        type: "GET",
        url: "/calendar/findTask",
        data: {
            task_id: taskId,
            _token: $("input[name=_token]").val()
        },
        dataType: 'json',
        success: function (data) {
            logger('task', data);
            if (data.status === 'success') {
                $("#div_jobs").show();
                $('.btn-delete-task').show();
                $("#task_id").val(data.data.id);
                $("#campaign_id").val(data.data.campaign_id).change();
                $("#task_name").val(data.data.title);
                $("#task_priority").val(data.data.priority).change();
                $("#task_type").val(data.data.task_type).change();
                $("#task_move").val("");
                if (data.data.created_by !== null) {
                    var avatar = `<img class="rounded-circle img-cover m-r-5 w-40px h-40px mw-40px" src="/images/avatar/${data.data.created_by}.jpg" data-toggle="tooltip" data-placement="top" title="${data.data.created_by}">`;
                    $("#created_by_avatar").html(avatar);
                } else {
                    $("#created_by_avatar").html("");
                }
                $("#dialog-task-title").html(`Task #${data.data.id}`);
                $(".btn-view-campaign").attr("href", `/campaign2?filter_id=${data.data.campaign_id}`);
                $(".div_job_group").hide();
                $(".div_other").hide();
                $('[data-toggle="tooltip"]').tooltip();
                listJobs(data.data.id);
                listHistory(data.data.id);
                $("#modal-add-job").modal("hide");
                var $modalJob = $("#modal-add-job").find(".modal-dialog");
                var $pdJob = $modalJob.find('.panel-disabled');
                $pdJob.remove();
                if (!$("#modal-add-task").is(':visible')) {
                    $("#modal-add-task").modal({
                        backdrop: 'static',
                        keyboard: true
                    });
                }
                if (r == 1) {
                    const myInput = document.querySelector(".btn-repeat");
                    const fp = flatpickr(myInput, {
                        mode: "multiple",
                        dateFormat: "Y-m-d",
                        onChange: function (selectedDates, dateStr, instance) {
                            $("#task_repeat").val(dateStr);
                        }
                    });
                    const move = flatpickr(".btn-move", {
                        dateFormat: "Y-m-d",
                        defaultDate: data.data.start,
                        onChange: function (selectedDates, dateStr, instance) {
                            $("#task_move").val(dateStr);
                        }
                    });

                    $.ajax({
                        type: "GET",
                        url: "/calendar/getRepeatTasks",
                        data: {campaign_id: data.data.campaign_id},
                        dataType: 'json',
                        success: function (dataRepeat) {
                            logger('getRepeatTasks', dataRepeat);
                            fp.setDate(dataRepeat.start_str, false, 'Y-m-d');
                            $("#task_repeat").val(dataRepeat.start_str);
                        },
                        error: function (dataRepeat) {
                            console.log(dataRepeat);
                        }
                    });
                }

                $('.btn-save-task').unbind('click').click(function () {

                    var taskType = $("#task_type").val();
                    var campaignId = $("#campaign_id").val();
                    var taskName = $("#task_name").val();
                    var taskPriority = $("#task_priority").val();
                    var taskRepeat = $("#task_repeat").val();
                    var taskMove = $("#task_move").val();
                    var object = {
                        "task_type": taskType,
                        "task_id": data.data.id,
                        "start": data.data.start,
                        "title": taskName,
                        "campaign_id": campaignId,
                        "task_priority": taskPriority,
                        "task_repeat": taskRepeat,
                        "task_move": taskMove
                    };
                    if (taskName !== null && taskName.length !== 0) {
                        data.data.title = taskName;
                        data.data.campaign_id = campaignId;
                        createTask(object);

                    } else {
                        alert('Enter task name');
                    }
                });
                $('.btn-delete-task').unbind('click').click(function () {
                    $.confirm({
                        animation: 'rotateXR',
                        title: 'Confirm!',
                        content: 'Are you sure to delete this task?',
                        buttons: {
                            confirm: {
                                text: 'Confirm',
                                btnClass: 'btn-red',
                                action: function () {
                                    var object = {"task_id": data.data.id, "task_repeat": $("#task_repeat").val()};
                                    $("#modal-add-task").modal('hide');
                                    deleteTask(object);
                                }
                            },
                            cancel: function () {

                            }

                        }
                    });

                });
            }
            $pd.remove();

        },
        error: function (data) {
            logger("calendar/findTask", data);
            $pd.remove();
        }
    });



};

listNotify = function () {
    var isShowRead = localStorage.getItem("is_show_read");
    $.ajax({
        type: "GET",
        url: "/calendar/notify/list",
        data: {
            is_show_read: isShowRead
        },
        dataType: 'json',
        success: function (data) {
            logger('calendarNotify', data);
            if (data.count > 0) {
                $("#calendar-count").html(data.count);
                $("#calendar-count").css({"display": "flex"});
            } else {
                $("#calendar-count").hide();
            }
            var html = "";
            if (data.notify.length > 0) {
                $.each(data.notify, function (key, value) {

                    var content = value.content;
                    if (value.title != null) {
                        content = `#${value.job_id} ${value.title}`;
                    }
                    var newHTML = content.replace(/<a/g, "<span").replace(/<\/a>/g,
                            "</span>");
                    var read = "font-bold";
                    var icon = "fa-eye";
                    if (value.is_read == 1) {
                        read = "";
                        icon = "fa-eye-slash";
                    }
                    html += `

                            <a href="javascript:readNotify(${value.job_id});" class="dropdown-item notify-item" >
                                    <div class="d-flex justify-content-center">
                                            <div><img data-toggle="tooltip" data-placement="top" title="${value.username}" class="notify-icon img-cover" src="/images/avatar/${value.username}.jpg"></div>
                                            <div style="width:200px" id="read-${value.id}" class="notify-details ${read} position-relative">
                                                <div>${newHTML}</div>
                                                <small class="text-muted">${value.created_text}</small>
                                            </div>
                                            
                                            <div class="div-dropdown-action  disp-none " style="z-index:1001">
                                                    <span class="btn-circle btn-circle-cus btn-action-notify dropdown-item dropdown-toggle" onclick="actionMarkAsRead(${value.id})" role="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i id="icon-${value.id}" class="fa ${icon}"></i>
                                                    </span>
           
                                            </div>
                                           
                                    </div>
                            </a>

                            `;

                });
                $("#calendar-notify-list").removeClass("text-center").addClass("div_scroll_50")
                        .html(html);
                $('[data-toggle="tooltip"]').tooltip();
            } else {
                $("#calendar-notify-list").removeClass("div_scroll_50").addClass("text-center")
                        .html("No Data");
            }
            initShowRead();
            preventBtnEvent();
        },
        error: function (data) {
            console.log(data);
        }
    });
};

function formJobClear() {
    $("#job-tut-link").html("");
    $('#summernote_job_des').summernote('reset');
    $("#job_id").val("");
    $("#job_code").val("");
    $("#job_title").val("");
    $("#job_des").val("");
    $("#job_type").val("promo").change();
    $("#username").val([""]).change();
    $("#job_member").val([""]).change();
    $("#username").prop("disabled", false);
    $('.search_select').selectpicker('destroy');
    $('.search_select').selectpicker('render');
    $("#job_priority").val("b_medium").change();
    jobStatusChanged = false;
    $("#job_status").val("0").trigger('change');
    $("#job_man_hour").val(null);
    $("#job_man_hour_unit").val('hours');
    $("#job_result").val("");
    $("#job_comment").val("");
    $("#job_duedate").val(null);
    $("#penalty").val(20);
//    console.log("innit summernote_job_comment");
    $('#summernote_job_comment').summernote('reset');
//    $("#summernote_job_comment").summernote("destroy");
//    $("#summernote_job_comment").html("");
//    initSummernote('#summernote_job_comment', true,"You can enter text or send an image...");
    if ($("#task_type").val() == 'campaign') {
        activeJobTitle();
    } else {
        deactiveJobTitle();
    }
}

appendComment = function (data) {
    var html = "";
    if (data.username !== usr) {
        html += `<li class="m-b-20 comment-wrap">
                        <div class="d-flex align-items-center ">
                            <div class="" data-toggle="tooltip" data-placement="top" title="${data.username}">
                                <img class="rounded-circle img-cover" src="/images/avatar/${data.username}.jpg" onerror="this.onerror=null;this.src='images/default-avatar.png';" alt="avatar" height="40" width="40">
                            </div>
                            <div class="m-l-5 comment-bg position-relative" data-id="${data.id}">
                                <div class="comment-bg-content"><span>${data.content}</span></div>
                                <div><span class="text-muted font-12">${data.date}</span></div>
                                <div class="emojis_wrap"></div>
                            </div>
                            <div class="m-l-10 disp-none btn-circle btn-circle-hover comment-toolbar reaction "><i class="ion-happy font-20"></i></div>
                            <div onclick="actionDeleteComment(this)" data-id="${data.id}" class="m-l-10 disp-none btn-circle btn-circle-hover comment-toolbar" data-toggle="tooltip" data-placement="top" title="Remove this comment">
                                <i class="ion-trash-a font-20"></i>
                            </div>
                        </div>
                    </li>`;

    } else {
        html += `<li class="m-b-20 comment-wrap">
            <div class="d-flex align-items-center justify-content-end ">
                <div onclick="actionDeleteComment(this)" data-id="${data.id}" class="m-l-10 disp-none btn-circle btn-circle-hover comment-toolbar" data-toggle="tooltip" data-placement="top" title="Remove this comment">
                    <i class="ion-trash-a font-20"></i>
                </div>
                <div class="m-l-10 disp-none btn-circle btn-circle-hover comment-toolbar reaction"><i class="ion-happy font-20"></i></div>
                <div class="m-l-10 comment-bg position-relative" data-id="${data.id}">
                    <div class="comment-bg-content"><span>${data.content}</span></div>
                    <div><span class="text-muted font-12">${data.date}</span></div>
                    <div class="emojis_wrap"></div>
                </div>
                <div class="m-l-5" data-toggle="tooltip" data-placement="top" title="${data.username}">
                    <img class="rounded-circle img-cover" src="/images/avatar/${data.username}.jpg" onerror="this.onerror=null;this.src='images/default-avatar.png';" alt="avatar" height="40" width="40">
                </div>
            </div>
        </li>`;

    }
    $(`[data-comment-job-id='${data.job_id}'] ul`).append(html);
    event();
};

chatNotify = function (title, message, icon, redirect, notiId) {
    if (Notification.permission !== 'granted') {
        Notification.requestPermission();
    } else {
        var notification = new Notification(title, {
            icon: icon,
            body: message,
            tag: notiId
        });
        notification.onclick = function () {
            window.open(redirect);
        };
    }
};

////var source = <?php echo json_encode($highlightUser); ?>;
var mentions = [];
$.get("/calendar/getUserMention", function (data) {
    mentions = data;
}, "json");

var initSummernote = function (id, airMode, placeholder = 'Description here...') {
    $(id).summernote({
        //        height: 250,
        minHeight: null,
        maxHeight: null,
        focus: false,
        spellCheck: false,
        airMode: airMode,
        placeholder: placeholder,
        dialogsInBody: true,
        tabDisable: false,
        disableAutoParagraph: true,
        cleaner: true,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['link', ['link']],
            ['insert', ['picture']],
            ['view', ['fullscreen']],
        ],
        hint: {
            mentions: mentions,
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
};

$('.div_scroll_50').slimScroll({
    height: '80vh',
    position: 'right',
    size: "5px",
    color: '#98a6ad',
    wheelStep: 30
});
$('.div_scroll_70').slimScroll({
    height: '70vh',
    position: 'right',
    size: "5px",
    color: '#98a6ad',
    wheelStep: 30
});
loadCheckList();
var checklist = [];

function loadCheckList() {
    $.ajax({
        type: "GET",
        url: "/calendar/checklist/get",
        data: {},
        dataType: 'json',
        success: function (data) {
            $.each(data.checklist, function (key, value) {
                checklist.push({
                    label: value.location + ' | ' + value.name,
                    value: value.location + ' | ' + value.name,
                    des: value.des,
                    user: value.persion,
                    key: value.key,
                    type: value.job_type,
                    estimate: value.estimate
                });
            });
            logger('checklist', checklist);
            $("#release_day_duedate").val(data.default);
            $("#campaign_setup_duedate").val(data.default);
            $("#artist_setup_duedate").val(data.default);
        },
        error: function (data) {
            logger('error', data);
        }
    });
}

function activeJobTitle() {
    $("#job_title").autocomplete({
        source: function (request, response) {
            var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
            response($.grep(checklist, function (item) {
                if (!matcher.test(item.label)) {
                    $("#job_code").val("");
                }
                return matcher.test(item.label);
            }));
        },
        select: function (value, data) {
            //                console.log('selected', value, data);

            //            $("#job_title").val(data.item.label);
            $('#summernote_job_des').summernote('reset');
            $('#summernote_job_des').summernote('insertText', data.item.des);
            $("#job_des").val(data.item.des);
            $("#job_code").val(data.item.key);
            $("#username").val(data.item.user).change();
            $("#job_type").val(data.item.type).change();
            $("#job_man_hour").val(data.item.estimate);
        },
        minLength: 0,
        classes: {
            "ui-autocomplete": "highlight"
        }
    }).on("focus", function () {
        var attr = $(this).attr('readonly');
        if (typeof attr === 'undefined' || attr === false) {
            $(this).autocomplete("search", '');
        }
    });
}

function deactiveJobTitle() {
    if ($("#job_title").data("ui-autocomplete")) {
        $("#job_title").autocomplete("destroy");
    }
}


