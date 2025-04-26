<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Automusic.win">
    <meta name="author" content="Victorteam">
    <meta name="us" content="<?php echo isset($user_login) ? $user_login->user_name : ''; ?>">
    <meta name="rs" content="<?php echo $is_admin_calendar;?>">

    <link rel="shortcut icon" href="images/favicon.ico">

    <title>Automusic</title>
    <base href="{{ asset('') }}">


    <!-- DataTables -->
<!--    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>-->
    <link href="assets/plugins/fullcalendar/css/fullcalendar.min.css" rel="stylesheet" />
    <link href="assets/plugins/summernote/summernote.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/tablednd.css" type="text/css" />
    <link href="assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/searchpanes/2.0.0/css/searchPanes.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/select/1.3.4/css/select.bootstrap4.min.css" />
    <!-- Responsive datatable examples -->
    <link href="assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <link href="assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
    <link href="assets/plugins/custombox/dist/custombox.min.css" rel="stylesheet" />
    <link href="assets/plugins/tablesaw/dist/tablesaw.css" rel="stylesheet" />
    <link href="assets/plugins/ion-rangeslider/ion.rangeSlider.css" rel="stylesheet" />
    <link href="assets/plugins/ion-rangeslider/ion.rangeSlider.skinFlat.css" rel="stylesheet" />

    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="assets/css/style.css" rel="stylesheet" type="text/css">
    <link href="css/image-picker.css" rel="stylesheet">
    <link href="css/bootstrapColorPicker.css" rel="stylesheet">
    <!--<link href="assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.css" rel="stylesheet">-->
    <!--<link href="css/jquery-ui.min.css" rel="stylesheet">-->
    <link href="https://code.jquery.com/ui/1.8.23/themes/base/jquery-ui.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/jstree/style.css" rel="stylesheet" type="text/css" />
    <!--select search-->
    <link href="css/bootstrap-select.min.css" rel="stylesheet">
    <!-- X-editable css -->
    <link type="text/css" href="assets/plugins/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css"
        rel="stylesheet">
    <!--textarea highlight-->
    <link href="assets/plugins/textarea-highlight/jquery.highlight-within-textarea.css" rel="stylesheet"
        type="text/css" />
    <link href="css/daterangepicker.css" rel="stylesheet">
    <link href="css/style.css?version=1.151" rel="stylesheet" type="text/css">

    <script src="assets/js/modernizr.min.js"></script>

    <script src="js/avatar-handlers.js?v=1.2"></script>

    <style>
        
    </style>

</head>

<body class="fixed-left" style="padding-right: 0px !important">
    <!-- Begin page -->
    <div id="wrapper" class="forced enlarged">

        <!-- Top Bar Start -->
        @include('layouts.topbar')
        <!-- Top Bar End -->

        <!-- Left Sidebar Start -->
        @include('layouts.leftsidebar')
        <!-- Left Sidebar End -->

        <!-- Start right content -->
        <div class="content-page">
            <!-- Start content -->
            <div class="content">
                <div class="container-fluid">
                    @yield('content')

                </div>
                <!-- end container -->
            </div>
            <!-- end content -->

            <footer class="footer">
                2019 © VictorTeam <span class="hide-phone">- Automusic</span>
            </footer>

        </div>
        <!-- End right content -->

        <!-- Right Sidebar -->
        @include('layouts.rightsidebar')
        <!-- /Right-bar -->

    </div>
    <!-- END wrapper -->

    <script>
        var resizefunc = [];
    </script>

    <!-- Plugins  -->
    
    <script src="assets/js/jquery.min.js"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>-->
    <!--<script src="https://code.jquery.com/ui/1.8.23/jquery-ui.js"></script>-->
    <script src="assets/js/popper.min.js"></script><!-- Popper for Bootstrap -->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/detect.js"></script>
    <script src="assets/js/fastclick.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/jquery.blockUI.js"></script>
    <script src="assets/js/waves.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/jquery.nicescroll.js"></script>
    <script src="assets/js/jquery.scrollTo.min.js"></script>
    <script src="assets/plugins/switchery/switchery.min.js"></script>
    <script src="assets/plugins/summernote/summernote.min.js?v=1.01"></script>
    
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>-->

    <!-- Notification js -->
    <script src="assets/plugins/notifyjs/dist/notify.min.js"></script>
    <script src="assets/plugins/notifications/notify-metro.js"></script>

    <!-- Modal-Effect -->
    <script src="assets/plugins/custombox/dist/custombox.min.js"></script>
    <script src="assets/plugins/custombox/dist/legacy.min.js"></script>

    <!-- Counter Up  -->
    <script src="assets/plugins/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="assets/plugins/counterup/jquery.counterup.min.js"></script>

    <!--flot-chart-->
    <script src="assets/plugins/flot-chart/jquery.flot.js"></script>
    <script src="assets/plugins/flot-chart/jquery.flot.time.js"></script>
    <script src="assets/plugins/flot-chart/jquery.flot.tooltip.min.js"></script>
    <script src="assets/plugins/flot-chart/jquery.flot.resize.js"></script>
    <script src="assets/plugins/flot-chart/jquery.flot.pie.js"></script>
    <script src="assets/plugins/flot-chart/jquery.flot.selection.js"></script>
    <script src="assets/plugins/flot-chart/jquery.flot.stack.js"></script>
    <script src="assets/plugins/flot-chart/jquery.flot.crosshair.js"></script>
    <!--<script src="assets/pages/jquery.flot.init.js"></script>-->

    <!--custom javascript-->
    <script src="js/plugin-group-channel.js?v=1.5"></script>
    <script src="js/user-select-plugin.js?v=1.01"></script>
    <script src="js/script.js?version=63"></script>
    <script src="js/calendar.js?v=1.47"></script>
    <script src="js/validator.js?v=1.0"></script>

    <!-- Page js  -->
    <!--<script src="assets/pages/jquery.dashboard.js"></script>-->
    <script src="assets/plugins/notifyjs/dist/notify.min.js"></script>
    <script src="assets/plugins/notifications/notify-metro.js"></script>
    <!-- Custom main Js -->
    <script src="assets/js/jquery.core.js?v=4"></script>
    <script src="assets/js/jquery.app.js"></script>

    <script src="js/bootbox.min.js"></script>
    <script src="js/colorPicker.js"></script>
    <script src="js/image-picker.min.js"></script>

    <!-- Required datatable js -->
    <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchpanes/2.0.0/js/dataTables.searchPanes.min.js">
    </script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchpanes/2.0.0/js/searchPanes.bootstrap4.min.js">
    </script>
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.4/js/dataTables.select.min.js"></script>
    <!-- Responsive examples -->
    <script src="assets/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="assets/plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script src="assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="assets/plugins/jstree/jstree.min.js"></script>
    <script src="assets/plugins/ion-rangeslider/ion.rangeSlider.min.js"></script>
    <script src="js/Chart.bundle.min.js"></script>
    <script src="js/xteam_fireworks.min.js"></script>

    <!--daterange picker-->
    <script src="https://colorlib.com/polygon/vendors/moment/min/moment.min.js"></script>
    <script src="js/daterangepicker.js"></script>

    <!--multiple date picker-->
    <link rel="stylesheet" href="assets/plugins/multiple-datepicker/flatpickr.min.css">
    <script src="assets/plugins/multiple-datepicker/flatpickr.js"></script>

    <!--fullcalendar-->
    <script src='assets/plugins/fullcalendar/js/fullcalendar.min.js'></script>
    <script src="assets/pages/jquery.fullcalendar.js?v=1.70"></script>

    
    <!--<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>-->


    <!--select search-->
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/defaults-en_US.js"></script>

    <!--<script src="../plugins/moment/moment.js"></script>-->
    <!--xedit able-->
    <script type="text/javascript" src="assets/plugins/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js">
    </script>
    <script type="text/javascript" src="assets/plugins/textarea-highlight/jquery.highlight-within-textarea.js"></script>
    <script type="text/javascript" src="assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
    <script type="text/javascript" src="assets/pages/jquery.xeditable.js?v=12"></script>
    <script src="js/jquery.tablednd.js?v=1"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
    <!--<script src="https://cdn.jsdelivr.net/npm/ckeditor5-classic-free-full-feature@35.4.1/build/ckeditor.min.js"></script>-->
    <!--<script src="https://cdn.tiny.cloud/1/6cd4pk7q3tqyoz2l6bx1xm5h9cwfazqqtjgw1bto7jn858oc/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>-->
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/0.9.1/jquery.tablednd.js"
        integrity="sha256-d3rtug+Hg1GZPB7Y/yTcRixO/wlI78+2m08tosoRn7A=" crossorigin="anonymous"></script>-->
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            let notificationsEnabled = false;

            function enableNotifications() {
                notificationsEnabled = true;
                document.removeEventListener('click', enableNotifications);
                document.removeEventListener('mousemove', enableNotifications);
                document.removeEventListener('keydown', enableNotifications);
                document.removeEventListener('scroll', enableNotifications);
            }

            document.addEventListener('click', enableNotifications);
            document.addEventListener('mousemove', enableNotifications);
            document.addEventListener('keydown', enableNotifications);
            document.addEventListener('scroll', enableNotifications);

            function playSound() {
                if (notificationsEnabled) {
                    var audio = new Audio('ping.mp3');
                    audio.play().catch(function(error) {
                        console.log("Error playing sound:", error);
                    });
                }
            }

            // Example function to simulate receiving a new message
            function simulateNewMessage() {
                if (notificationsEnabled) {
                    console.log("New message received!");
                    playSound();
                }
            }

            if (Notification.permission !== 'granted') {
                Notification.requestPermission();
            }

            //            Pusher.logToConsole = true;
            var pusher = new Pusher('854fabf2c9e05a3e8cec', {
                cluster: 'ap1'
            });
            var channel = pusher.subscribe('chat-channel');
            var us = $("meta[name='us']").attr('content');
            //            logger('tabCount',localStorage.tabCount);
            channel.bind('chat-event', function(data) {
                logger('chat-event', data);
                if (data.receive.includes(us)) {
                    if (data.data.message != "") {
                        chatNotify(`#${data.data.job_id} ${data.data.title}`, data.data.message, data.owner
                            .avatar, data.data.redirect, data.data.noti_id);
                        simulateNewMessage();
                    }
                    if (data.data.type == 1) {
                        //comment
                        appendComment(data.data.comment);
                        listNotify();
                    } else if (data.data.type == 2) {
                        //notify
                        listNotify();
                    } else if (data.data.type == 3) {
                        //image,delete
                        listComment(data.data.job_id);
                    }else if (data.data.type == 4) {
                        //check claim
                        editCampaign(data.data.job_id,0);
                        $(".btn-check-claim").html(`<i class="fa fa-refresh"></i>`);
                    }else if (data.data.type == 5) {
                        showRewardNotification({
                            avatar: data.data.avatar,
                            name: data.data.name,
                            position: data.data.position,
                            content: data.data.content,
                            theme: 'royal',
                            onClose: function() {

                            }
                        });
                    }
                }
            });

            listNotify();
            $('[data-plugin-one="switchery"]').each(function(idx, obj) {
                new Switchery($(this)[0], $(this).data());
            });
            
            $("#btn-read-all").click(function(){
                $.ajax({
                    type: "GET",
                    url: "/readAllNotify",
                    data: {
                        status: status,
                        group: groups
                    },
                    dataType: 'json',
                    success: function(data) {
                        $.Notification.autoHideNotify("success", 'top center', "Notify", `Mark as read ${data}`);
                        loadNotify(0);
                    },
                    error: function(data) {}
                });
            });
            var status = [];
            var groups = [];
            var initNotify = (function init() {
                if (localStorage.noti_group) {
                    groups = JSON.parse(localStorage.noti_group);
                    $.each(groups, function(key, value) {
                        $(`#filter-group-${value}`).addClass("notify-active");
                    });
                }
                if (localStorage.noti_status) {
                    status = JSON.parse(localStorage.noti_status);
                    $.each(status, function(key, value) {
                        $(`#filter-status-${value}`).addClass("notify-active");
                    });
                }
                return init;
            }()); //auto-run
            changeStatus = function(value) {
                if (status.includes(value)) {
                    status = removeItemOnce(status, value);
                    $(`#filter-status-${value}`).removeClass("notify-active");
                } else {
                    status.push(value);
                    $(`#filter-status-${value}`).addClass("notify-active");
                }
                localStorage.setItem("noti_status", JSON.stringify(status));
                loadNotify(0);
            };
            changeGroup = function(value) {
                if (groups.includes(value)) {
                    groups = removeItemOnce(groups, value);
                    $(`#filter-group-${value}`).removeClass("notify-active");
                } else {
                    groups.push(value);
                    $(`#filter-group-${value}`).addClass("notify-active");
                }
                localStorage.setItem("noti_group", JSON.stringify(groups));
                loadNotify(0);
            };
            notiUpdateStatus = function(id) {
                $.ajax({
                    type: "GET",
                    url: "/notiUpdateStatus",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(data) {
                        loadNotify(0);
                    },
                    error: function(data) {}
                });
            };
            notiUpdateRead = function(id) {
                $.ajax({
                    type: "GET",
                    url: "/notiUpdateRead",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(data) {
                        loadNotify(0);
                        if(data.redirect!=null){
                            window.open(data.redirect,'_blank');
                        }
                    },
                    error: function(data) {}
                });
            };
            var isShowDialogNotify = 0;
            loadNotify = function(show = 1) {
                $("#openNotify").addClass("disabled");
                $("#notify_loading").show();
                $.ajax({
                    type: "GET",
                    url: "/getNotify",
                    data: {
                        status: status,
                        group: groups
                    },
                    dataType: 'json',
                    success: function(data) {
                        $("#notify_loading").hide();
                        logger("notify", data);
                        if (data.count > 0) {
                            $("#notify-count").html(data.count);
                            $("#notify-count").css({
                                "display": "flex"
                            });
                        } else {
                            $("#notify-count").hide();
                        }
                        var html = `<table class="table table-hover mails m-0"  style="border-collapse: inherit;table-layout: fixed">
                                            <tbody>`;
                        $.each(data.noti, function(key, value) {
                            var platform =
                                `<a onclick="notiUpdateRead(${value.id})" class="cur-poiter">${value.platform}</a>`;
                            if (value.action_type === 'redirect') {
                                platform =
                                    `<a target="_blank" href="${value.action}" >${value.platform}</a>`;
                            }
                            var content =
                                `<a onclick="notiUpdateRead(${value.id})" class="cur-poiter">${value.content}</a>`;
                            if (value.action_type === 'redirect') {
                                content =
                                    `<a target="_blank" href="${value.action}" >${value.content}</a>`;
                            }
                            html += `<tr class="${value.unread}">
                                                            <td style="width:5%">
                                                                <i class="fa fa-circle m-l-5 ${value.group_color}"></i>
                                                            </td>
                                                            <td style="width:10%" >
                                                                ${platform}
                                                            </td>
                                                            <td style="width:50%" class="hidden-xs text-wrap " data-toggle="tooltip" data-placement="top" data-original-title="">
                                                                ${content}
                                                            </td><td style="width:12%">`;
                            @if ($is_admin_music)
                                if (value.platform == 'automusic') {
                                    html += `${value.username}`;
                                }
                            @endif

                            html += `</td><td style="width:12%" class="text-right">${value.created}</td>
                                                            <td style="width:10%" class="text-right" onclick='notiUpdateStatus(${value.id})'>${value.status_badge}</td>
                                                        </tr>`;
                        });
                        html += `</tbody></table>`;
                        $("#table-noti-data").html(html);
                        $('[data-toggle="tooltip"]').tooltip();
                        if (data.noti.length === 0) {
                            $("#table-noti-data").html("No Data");
                        }
                        $("#openNotify").removeClass("disabled");
                        if (show === 1) {
                            $('#dialog_list_notify').modal({
                                backdrop: false
                            });
                        }
                    },
                    error: function(data) {}
                });
            };
            loadNotify(0);
            setInterval(function() {
                loadNotify(0);
            }, 30000);
            numberToShorten = function(num) {
                num = num.toString().replace(/[^0-9.]/g, '');
                if (num < 1000) {
                    return num;
                }
                let si = [{
                        v: 1E3,
                        s: "K"
                    },
                    {
                        v: 1E6,
                        s: "M"
                    },
                    {
                        v: 1E9,
                        s: "B"
                    },
                    {
                        v: 1E12,
                        s: "T"
                    },
                    {
                        v: 1E15,
                        s: "P"
                    },
                    {
                        v: 1E18,
                        s: "E"
                    }
                ];
                let index;
                for (index = si.length - 1; index > 0; index--) {
                    if (num >= si[index].v) {
                        break;
                    }
                }
                return (num / si[index].v).toFixed(2).replace(/\.0+$|(\.[0-9]*[1-9])0+$/, "$1") + si[index].s;
            };
            shortenToNumber = function(val) {
                var multiplier = val.substr(-1).toLowerCase();
                if (multiplier == "k")
                    return parseFloat(val) * 1000;
                else if (multiplier == "m")
                    return parseFloat(val) * 1000000;
                else if (multiplier == "b")
                    return parseFloat(val) * 1000000000;
                else if (multiplier == "t")
                    return parseFloat(val) * 1000000000000;
                else if (multiplier == "p")
                    return parseFloat(val) * 1000000000000000;
                else if (multiplier == "e")
                    return parseFloat(val) * 1000000000000000000;
            };
            genColor = function(percent) {
                var colors = ["bg-danger", "btn-s", "bg-warning", "bg-success", "bg-info", "btn-t"];
                var color = colors[4];
                if (percent < 50) {
                    color = colors[0];
                } else if (percent >= 50 && percent < 70) {
                    color = colors[1];
                } else if (percent >= 70 && percent < 95) {
                    color = colors[2];
                } else if (percent >= 95 && percent < 120) {
                    color = colors[3];
                } else if (percent >= 120) {
                    color = colors[5];
                }
                return color;
            };
            $('.notify_scroll').slimScroll({
                height: '400px',
                position: 'right',
                size: "5px",
                color: '#98a6ad',
                wheelStep: 30
            });
            //setInterval(function(){getNotification();}, 60000);
            function getNotification() {
                //bật thông báo trong giờ hành chính
                const d = new Date();
                let hour = d.getHours();
                if (hour < 8 || hour >= 18) {
                    return;
                }
                var username = '{{ $user_login->user_name }}';
                $.ajax({
                    type: "GET",
                    url: "getNotification",
                    data: {
                        "username": username
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.length > 0) {
                            $.each(data, function(key, value) {
                                notify(value.message, value.href,
                                    "https://automusic.win/avatar/" + value.user_send +
                                    ".jpg");
                            });
                        }

                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            }



            //        var firework = Xteam.fireworkShow('#fire', 100);
            //    $('#checkTree').jstree({
            //        'core' : {
            //            'themes' : {
            //                'responsive': false
            //            }
            //        },
            //        'types' : {
            //            'default' : {
            //                'icon' : 'fa fa-folder'
            //            },
            //            'file' : {
            //                'icon' : 'fa fa-file'
            //            }
            //        },
            //        'plugins' : ['types', 'checkbox']
            //    });
            $('.search_select').selectpicker();

            function initTable() {
                $("#data-table-group-channel").DataTable().clear().destroy();
                $('#data-table-group-channel').DataTable({
                    searching: true,
                    ordering: false,
                    processing: true,
                    select: true,
                    responsive: true,
                    "lengthMenu": [
                        [5, 10, 25],
                        [5, 10, 25]
                    ],
                    //                            serverSide: true,
                    "ajax": {
                        "url": '/ajaxGetGroupChannel',
                        "type": "GET",
                        "dataSrc": ""
                    },
                    "columns": [{
                            "data": "id"
                        },
                        {
                            "data": "group_name"
                        },
                        {
                            "data": "id"
                        }
                    ],
                    "columnDefs": [{
                        "targets": 2,
                        "data": "download_link",
                        "render": function(data, type, row, meta) {
                            return '<button class="btn btn-danger waves-effect waves-light btn-sm btn-ssm m-b-5 btn-del" onclick="deleteGroup(\'' +
                                data + '\')" value="' + data +
                                '"><i class="fa fa-trash"></i> Delete</button>';
                        }
                    }]
                });
            }
            $('.btn_add_group_channel').click(function() {
                $('#dialog').modal({
                    backdrop: false
                });
                initTable();
            });
            deleteGroup = function(id) {
                $.ajax({
                    type: "GET",
                    url: "/ajaxDelGroupChannel",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $.Notification.notify(data.status, 'top center', '', data.content[0]);
                        initTable();
                    },
                    error: function(data) {}
                });
            }

            $('.btn-save-group-channel').click(function(e) {
                e.preventDefault();
                var group_name = $("#group_name_add").val();
                $.ajax({
                    type: "GET",
                    url: "/ajaxAddGroupChannel",
                    data: {
                        groupName: group_name
                    },
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                        $.Notification.notify(data.status, 'top center', '', data.content[0]);
                        $("#data-table-group-channel").DataTable().clear().destroy();
                        initTable();
                        var noGroupOption = $('#targetGroup option[value="0"]');

                        // Thêm các nhóm từ dữ liệu API
                        $.each(data.groups, function(index, group) {
                            // Tạo option mới
                            var newOption = $('<option></option>');

                            // Thiết lập thuộc tính cho option
                            newOption.val(group.id);
                            newOption.text(group.group_name);

                            // Thêm option vào sau option trước đó
                            if (index === 0) {
                                // Option đầu tiên sẽ được thêm sau NO_GROUP
                                newOption.insertAfter(noGroupOption);
                            } else {
                                // Các option tiếp theo sẽ được thêm sau option cuối cùng vừa thêm
                                var lastAddedOption = $('#targetGroup option[value="' + data.groups[index-1].id + '"]');
                                newOption.insertAfter(lastAddedOption);
                            }
                        });
                        $("#targetGroup").selectpicker('refresh');
                        $('#group_channel_1').groupDropdown('refresh');
                        $('#group_channel_2').groupDropdown('refresh');
                        
                    },
                    error: function(data) {
                        //                    console.log('Error:', data);
                    }
                });
            });
            var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome');
            if (is_chrome > -1) {
                $(".logo").css({
                    "line-height": "71px"
                });
            } else {
                $(".logo").css({
                    "line-height": "70px"
                });
            }
            $("#cbbLimit").change(function() {
                $("#limit").val($(this).val());
                $('#formSearchChannel').submit();
            });
            $('.counter').counterUp({
                delay: 100,
                time: 1200
            });

            function changeStateMenu(stage) {
                $.get('stagemenu' + stage, function(data) {});
            }

            showhideCheckbox = function(id) {
                //        $('.div_' + id).hide();
                $('#' + id).change(function() {
                    if (this.checked)
                        $('.div_' + id).fadeIn('slow');
                    else
                        $('.div_' + id).fadeOut('fast');
                });
            };
            showhideSelect = function(id) {
                $('.' + id).change(function() {
                    var value = $('.' + id).val();
                    $("." + id + " option").each(function() {
                        //            debugger;
                        if (value == $(this).val()) {
                            $('.div_' + id + '_' + value).fadeIn('slow');
                        } else {
                            $('.div_' + id + '_' + $(this).val()).fadeOut('fast');
                        }
                    });
                });
            };
            showhideRadio = function(id) {
                $('.' + id).change(function() {
                    $('.' + id).each(function(i, obj) {
                        var value = $(this).attr('value');
                        if ($(this).is(':checked')) {
                            $('.div_' + id + '_' + value).fadeIn('slow');
                        } else {
                            $('.div_' + id + '_' + value).fadeOut('fast');
                        }
                    });
                });
            };
            var arrCheckbox = ['sort_video', 'filter_video', 'ck_filter_date', 'ck_filter_time', 'ck_filter_view',
                'ck_filter_like', 'ck_filter_dislike',
                'ck_title_add_begin', 'ck_title_add_end', 'ck_title_replace', 'ck_song_title_replace',
                'ck_auto_increament',
                'ck_des_add_begin', 'ck_des_add_end', 'ck_des_replace',
                'ck_tag_add_begin', 'ck_tag_add_end', 'ck_tag_replace',
                'ck_style_text', 'ck_style_title_song', 'ck_style_lyric', 'claim', 'link_type_mix',
                'add_budget',
                'ck_auto_job'
            ];
            for (var i = 0; i < arrCheckbox.length; i++) {
                showhideCheckbox(arrCheckbox[i]);
            }
            var arrSelect = ['music_type', 'source_type', 'bg_type', 'ck_replace_title',
                'ck_replace_des', 'ck_replace_tag', 'action_command', 'background_type',
                'type_reup', 'brand_type', 'cam_type', 'channel_type', 'source_wakeup_type',
                'submit_type'
            ];
            for (var i = 0; i < arrSelect.length; i++) {
                showhideSelect(arrSelect[i]);
            }
            var arrRadio = ['radio_endscreen', 'radio_currency', 'radio-payment-type', 'radio_campaign_type'];
            for (var i = 0; i < arrRadio.length; i++) {
                showhideRadio(arrRadio[i]);
            }

            copyInputValue = function(id) {
                navigator.clipboard.writeText($("#" + id).val());
                $.Notification.notify('success', 'top center', 'Notification', 'Copied');
            }
            copyToClipboard = function(element) {
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(element).select();
                document.execCommand("copy");
                $temp.remove();
                //notify sau khi copy text thanh cong
                $.Notification.notify('success', 'top center', 'Notification', 'Copied: ' + element);
            };
            copyText = function(text) {
                navigator.clipboard.writeText(text);
                $.Notification.notify('success', 'top center', 'Notification', 'Copied: ' + text);
            }
            copyToClipboardNewLine = function(element) {
                var $temp = $("<textarea>");
                var brRegex = /<br\s*[\/]?>/gi;
                $("body").append($temp);
                $temp.val(element.replace(brRegex, "\r\n")).select();
                document.execCommand("copy");
                $temp.remove();
                $.Notification.notify('success', 'top center', 'Notification', 'Copied');
            };
            
            // Helper function to copy text to clipboard
            copyToClipboard2 = function (text) {
                const tempInput = document.createElement('input');
                tempInput.value = text;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
            };

            // Helper function to show the copied effect
            showCopiedEffect =function (element) {
                element.addClass('copied');
                setTimeout(() => {
                    element.removeClass('copied');
                }, 2000);
            };            
            $('#select_all').change(function() {
                var checkboxes = $(this).closest('.table').find('.checkbox-multi');
                checkboxes.prop('checked', $(this).is(':checked'));
            });
            $("#cbbLimit").change(function() {
                $("#limit").val($(this).val());
                $('#btnSearch').click();
            });
            openFilter = function() {
                $('.filter-class').fadeIn('slow');
                //    $('.filter-class').addClass("disp-none")
            };
            $(".loading").hide();
            $(document).ajaxStart(function() {
                $(".loading").show();
            }).ajaxStop(function() {
                $(".loading").hide();
            });
            getReportPeriodRevDetailAdmin = function(period) {
                $("#report_user_rev_content").html("");
                $("#report_user_rev_content_detail").html("");
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
                                        <td><button id="btn${value.user_name}" type="button" onclick='getReportUserRevDetail("${value.period}","${value.user_name}")' class="btn btn-outline-info btn-sm"> Detail</button></td>
                                </tr>`;
                        });
                        html += '</table>';
                        $("#report_user_rev_content").html(html);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            };
            getReportUserRevDetailAdmin = function(period, username) {
                $("#report_user_rev_content_detail").html("");
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
                            `<table class="table"><tr><td><strong>PROMO: $${data.camTypeMoney.PROMOS.toFixed(2)}</strong></td><td class="text-right"><strong>SUBMISSION: $${data.camTypeMoney.SUBMISSION.toFixed(2)}</strong></td></tr><table>`;
                        $("#report_user_rev_content_detail").html(html);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            };
            getClaimReportRevDetailAdmin = function(period) {
                $("#report_user_rev_content").html("");
                $("#report_user_rev_content_detail").html("");
                var $this = $("#btn" + period);
                var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
                if ($this.html() !== loadingText) {
                    $this.data('original-text', $this.html());
                    $this.html(loadingText);
                }
                $.ajax({
                    type: "GET",
                    url: "/getClaimReportRevDetail",
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
                                        <td><button id="btn${value.user_name}" type="button" onclick='getClaimReportUserRevDetail("${value.period}","${value.user_name}")' class="btn btn-outline-info btn-sm"> Detail</button></td>
                                </tr>`;
                        });
                        html += '</table>';
                        $("#report_user_rev_content_claim").html(html);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            };
            getClaimReportUserRevDetailAdmin = function(period, username) {
                $("#report_user_rev_content_detail").html("");
                var $this = $("#btn" + username);
                var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
                if ($this.html() !== loadingText) {
                    $this.data('original-text', $this.html());
                    $this.html(loadingText);
                }
                $.ajax({
                    type: "GET",
                    url: "/getClaimReportRevDetail",
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
                                        <th class='text-center'>Money Views</th>
                                        <th class='text-center'>Mix</th>
                                        <th class='text-center'>Lyric</th>
                                        <th class='text-center'>Total</th>
                                        <th class='text-center'>Money</th>`;
                        var i = 0;
                        $.each(data.campaigns, function(key, value) {
                            i = i + 1;
                            //                                <td>${number_format(value.views_money_user, 0, '.', ',')}/${ number_format(value.views_money, 0, '.', ',')}</td>
                            //                                <td>${number_format(value.views_mix_user, 0, '.', ',')}/${ number_format(value.views_mix, 0, '.', ',')}</td>
                            //                                <td>${number_format(value.views_lyric_user, 0, '.', ',')}/${ number_format(value.views_lyric, 0, '.', ',')}</td>
                            //                                <td>${number_format(value.views_short_user, 0, '.', ',')}/${ number_format(value.views_short, 0, '.', ',')}</td>
                            html +=
                                `<tr>
                                            <td scope="row">${i}</td>
                                            <td class='text-left'>${value.username}</td>
                                            <td>${value.campaign_id}</td> 
                                            <td><div class="td-info cur-poiter"><span class="font-13 color-green">${value.views_money_user_per}%</span><div class="td-daily"><span>${number_format(value.views_money_user, 0, '.', ',')}</span><span>${ number_format(value.views_money, 0, '.', ',')}</span></div></div></td>
                                            <td><div class="td-info cur-poiter"><span class="font-13 color-green">${value.views_mix_user_per}%</span><div class="td-daily"><span>${number_format(value.views_mix_user, 0, '.', ',')}</span><span>${ number_format(value.views_mix, 0, '.', ',')}</span></div></div></td>
                                            <td><div class="td-info cur-poiter"><span class="font-13 color-green">${value.views_lyric_user_per}%</span><div class="td-daily"><span>${number_format(value.views_lyric_user, 0, '.', ',')}</span><span>${ number_format(value.views_lyric, 0, '.', ',')}</span></div></div></td>
                                            <td><div class="td-info cur-poiter"><span class="font-13 color-green">${value.views_total_user_per}%</span><div class="td-daily"><span>${number_format(value.views_total_user, 0, '.', ',')}</span><span>${ number_format(value.views_total, 0, '.', ',')}</span></div></div></td>
                                            <td><div class="color-green"><b>$${number_format(value.money, 0, '.', ',')}</b></div><b>$${number_format(value.total_money, 0, '.', ',')}</b></td></tr>`;
                        });
                        html += '</table>';
                        $("#report_user_rev_content_detail_claim").html(html);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            };
            activeTableCopy = function(){
                $(".table-copy").on("click", "td", function() {
                    var text = $(this).text();
                    copyText(text);
                });
            }
            activeTableCopy();
                    // Function to show notification
            showNotification = function (message, type = 'info') {
            // Create notification element if it doesn't exist
            if ($('#notification-container').length === 0) {
                $('body').append(
                    '<div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>'
                );
            }

            // Generate a unique ID for this notification
            const id = 'notification-' + Date.now();

            // Create the notification HTML
            let bgColor = '';
            let icon = '';

            switch (type) {
                case 'success':
                    bgColor = '#38b000';
                    icon = '<i class="fas fa-check-circle mr-2"></i>';
                    break;
                case 'error':
                    bgColor = '#ef233c';
                    icon = '<i class="fas fa-exclamation-circle mr-2"></i>';
                    break;
                case 'warning':
                    bgColor = '#fb8500';
                    icon = '<i class="fas fa-exclamation-triangle mr-2"></i>';
                    break;
                case 'info':
                default:
                    bgColor = '#3a86ff';
                    icon = '<i class="fas fa-info-circle mr-2"></i>';
                    break;
            }

            const notificationHTML = `
            <div id="${id}" class="notification" style="
                background-color: ${bgColor};
                color: white;
                padding: 15px 20px;
                border-radius: 4px;
                margin-bottom: 10px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                display: flex;
                align-items: center;
                justify-content: space-between;
                min-width: 300px;
                opacity: 0;
                transform: translateX(100px);
                transition: all 0.3s ease;
            ">
                <div>${icon}${message}</div>
                <button class="close-btn" style="
                    background: none;
                    border: none;
                    color: white;
                    font-size: 16px;
                    cursor: pointer;
                    margin-left: 10px;
                    padding: 0;
                ">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

            // Add the notification to the container
            $('#notification-container').append(notificationHTML);

            // Show the notification with animation
            setTimeout(() => {
                $(`#${id}`).css({
                    'opacity': '1',
                    'transform': 'translateX(0)'
                });
            }, 10);

            // Set up the close button
            $(`#${id} .close-btn`).click(function() {
                removeNotification(id);
            });

            // Auto-remove after 5 seconds
            setTimeout(() => {
                removeNotification(id);
            }, 5000);
        };

        // Function to remove a notification with animation
        removeNotification = function(id) {
            $(`#${id}`).css({
                'opacity': '0',
                'transform': 'translateX(100px)'
            });

            setTimeout(() => {
                $(`#${id}`).remove();
            }, 300);
        };
        







// Ví dụ cách sử dụng:

//showRewardNotification({
//  avatar: 'https://automusic.win/images/avatar/hieumusic.jpg',
//  name: 'Nguyễn Văn A',
//  position: 'Nhân viên xuất sắc',
//  content: 'Xuất sắc hoàn thành dự án ABC với thành tích vượt 150% chỉ tiêu. Đồng thời hỗ trợ đồng nghiệp một cách nhiệt tình và hiệu quả.',
//  theme: 'royal', // Có thể chọn: 'gold', 'royal', 'neon', 'rainbow'
//  onClose: function() {
//    console.log('Modal đã đóng');
//  }
//});

//khóa DevTools
@if(!$is_admin_music)
(function() {
    const threshold = 160;
    function detectDevTools() {
        if (window.outerWidth - window.innerWidth > threshold || window.outerHeight - window.innerHeight > threshold) {
            window.location.href = 'https://moonseo.app/images/stop.png';
        }
    }

    setInterval(detectDevTools, 1000); // Kiểm tra mỗi giây một lần
})();
@endif


        });
    </script>
    @yield('script')
    @yield('scriptChart')
    <!--@yield('scriptJstree')-->

</body>

</html>
