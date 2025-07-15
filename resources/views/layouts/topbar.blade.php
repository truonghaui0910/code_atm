<style>
/* Money Notification System - Complete CSS */

/* Money Dropdown Container */
.money-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    width: 400px;
    background: white;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    z-index: 1000;
    max-height: 450px;
    overflow: hidden;
}

/* Dropdown Header */
.money-dropdown-header {
    padding: 15px 20px;
    border-bottom: 1px solid #e3e6f0;
    background: #f8f9fc;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.money-dropdown-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #5a5c69;
}

/* Mark as Read Button */
.btn-mark-read {
    background: #1cc88a;
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-mark-read:hover {
    background: #17a673;
    transform: scale(1.05);
}

.btn-mark-read:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(28, 200, 138, 0.25);
}

/* Loading State */
.money-loading {
    text-align: center;
    padding: 20px;
    color: #6c757d;
    display: none;
}

/* Content Container */
.money-content {
    max-height: 350px;
    overflow-y: auto;
}

/* Custom Scrollbar */
.money-content::-webkit-scrollbar {
    width: 6px;
}

.money-content::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.money-content::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.money-content::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Money Notification Items */
.money-notify-item {
    padding: 15px 20px;
    border-bottom: 1px solid #f1f3f4;
    display: block;
    text-decoration: none;
    color: #5a5c69;
    transition: background-color 0.2s ease;
}

.money-notify-item:hover {
    background-color: #f8f9fc;
    text-decoration: none;
    color: #5a5c69;
}

.money-notify-item:last-child {
    border-bottom: none;
}

.money-notify-item.unread-money {
    background-color: #eef9f5;
    border-left: 4px solid #1cc88a;
}

/* Notification Content Layout */
.money-notify-content {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

/* Money Icon */
.money-notify-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #1cc88a, #17a673);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(28, 200, 138, 0.2);
}

.money-notify-icon i {
    color: white;
    font-size: 16px;
}

/* Notification Details */
.money-notify-details {
    flex: 1;
    min-width: 0; /* Cho phép text wrap */
}

.money-notify-text {
    font-size: 14px;
    line-height: 1.4;
    margin-bottom: 4px;
    word-wrap: break-word;
    white-space: normal;
    color: #5a5c69;
}

.money-notify-time {
    font-size: 12px;
    color: #6c757d;
    margin: 0;
}

/* New Badge */
.money-notify-badge {
    flex-shrink: 0;
    margin-left: 8px;
    align-self: flex-start;
}

.money-notify-badge .badge {
    font-size: 10px;
    padding: 4px 8px;
    border-radius: 12px;
}

/* Empty State */
.money-empty {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.money-empty i {
    font-size: 32px;
    margin-bottom: 10px;
    color: #dee2e6;
    display: block;
}

/* Legacy Styles for Compatibility */
.unread-money {
    background-color: #eef9f5 !important;
    border-left: 4px solid #1cc88a;
}

.notify-icon-money {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #e8f5e8;
    border-radius: 50%;
    flex-shrink: 0;
}

.notify-details .notify-content {
    line-height: 1.4;
    word-wrap: break-word;
}

.unread-indicator {
    flex-shrink: 0;
}

/* Dropdown Menu Fallback */
#money-dropdown .dropdown-item:hover {
    background-color: #f1f3f4;
}

#money-dropdown .notify-item {
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s ease;
}

#money-dropdown .notify-item:last-child {
    border-bottom: none;
}

#money-dropdown .notify-item:hover {
    background-color: #f8f9fa;
}
@keyframes fadeInDropdown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.money-notify-item {
    animation: slideInItem 0.3s ease-out;
}

@keyframes slideInItem {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
/* Responsive Design */
@media (max-width: 768px) {
    .money-dropdown {
        width: 320px;
        right: -50px;
        max-height: 400px;
    }
    
    .money-dropdown-header {
        padding: 12px 15px;
    }
    
    .money-dropdown-title {
        font-size: 14px;
    }
    
    .btn-mark-read {
        width: 28px;
        height: 28px;
    }
    
    .money-notify-item {
        padding: 12px 15px;
    }
    
    .money-notify-text {
        font-size: 13px;
    }
    
    .money-notify-icon {
        width: 35px;
        height: 35px;
    }
    
    .money-notify-icon i {
        font-size: 14px;
    }
    
    .money-content {
        max-height: 300px;
    }

}

@media (max-width: 480px) {
    .money-dropdown {
        width: 280px;
        right: -80px;
    }
    
    .money-notify-content {
        gap: 8px;
    }
    
    .money-notify-text {
        font-size: 12px;
    }
    
    .money-notify-time {
        font-size: 11px;
    }
}

/* Animation Effects */
.money-dropdown {
    animation: fadeInDropdown 0.2s ease-out;
}

/* Fix scroll cho notification modal */
#dialog_list_notify .modal-body {
    height: 80vh;
    overflow: hidden;
}

#dialog_list_notify .row {
    height: 100%;
}

/* Cột trái - cố định */
#dialog_list_notify .col-xl-3 {
    height: 100%;
    overflow-y: auto;
}

/* Cột phải - cho phép scroll */
#dialog_list_notify .col-xl-9 {
    height: 100%;
    overflow-y: auto;
    padding-right: 15px; /* Tránh scrollbar che nội dung */
}

/* Card-box phải scroll được */
#dialog_list_notify .col-xl-9 .card-box {
    height: auto !important; /* Bỏ height cố định */
    max-height: none !important;
    overflow: visible !important;
}

/* Panel body bên trong */
#dialog_list_notify .panel-body {
    overflow: visible !important;
}

/* Table responsive */
#dialog_list_notify .table-responsive {
    overflow: visible !important;
    height: auto !important;
}

/* Button Read all */
#dialog_list_notify #btn-read-all {
    position: sticky;
    top: 5px;
    z-index: 10;
    background: white;
    padding: 5px 0;
    margin-bottom: 10px;
}

</style>
<div class="topbar" >

    <!-- LOGO -->
    <div class="topbar-left" style="height: 70px;">
        <div class="text-center">
            <a href="/" class="logo">
                <img src="images/logo1.png" style="width: 40px">
                <span><img src="images/logo3.png" style="width: 140px;margin-top: -17px;"></span></a>
                <!--<img src="images/logomusic.png" style="width: 175px">-->
        </div>
    </div>

    <!-- Button mobile view to collapse sidebar menu -->
    <nav class="navbar-custom">

        <ul class="list-inline float-right mb-0">

            <li class="list-inline-item notification-list hide-phone">
                <a class="nav-link waves-light waves-effect" href="javascript:void(0);" id="btn-fullscreen">
                    <i class="mdi mdi-crop-free noti-icon"></i>
                </a>
            </li>

@if($is_admin_music)
<li class="list-inline-item notification-list" style="position: relative;">
    <a id="openNotifyMoney" class="nav-link arrow-none waves-light waves-effect" href="javascript:void(0);">
        <i class="ti-money noti-icon"></i>
        <span id="notify-money-count" class="notify-badge disp-none">0</span>
    </a>
    
    <!-- Custom dropdown -->
    <div id="money-dropdown-custom" class="money-dropdown">
        <!-- Header -->
        <div class="money-dropdown-header">
            <h5 class="money-dropdown-title">Payment Notifications</h5>
            <button id="btn-read-all-money" class="btn-mark-read" title="Mark all as read">
                <i class="fa fa-check"></i>
            </button>
        </div>

        <!-- Loading -->
        <div id="money_loading" class="money-loading">
            <i class="fa fa-spinner fa-spin"></i> Loading...
        </div>

        <!-- Content -->
        <div id="money-noti-data" class="money-content">
            <!-- Content sẽ load vào đây -->
        </div>
    </div>
</li>
@endif
            <li class="list-inline-item dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none waves-light waves-effect" data-toggle="dropdown" href="#" role="button"
                   aria-haspopup="false" aria-expanded="false">
                    <i class="fa fa-calendar noti-icon"></i>
                    <span id="calendar-count" class="notify-badge disp-none">0</span>
                </a>
                <div id="calendar-dropdown" class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg" aria-labelledby="Preview" style="z-index: 100">
                    <!-- item-->
                    <div class="dropdown-item noti-title" style="width: 280px">
                        <h5 class="font-16"><span class="float-right"><span class="font-13 text-muted">Show read </span>
                                <input id="ck_show_read" type="checkbox" data-plugin-one="switchery" data-color="#45af49" data-size="small"/></span>Calendar</h5>
                    </div>


                    <div id="calendar-notify-list" class="div_scroll_50">
                        <a href="javascript:readNotify(1496);" class="dropdown-item notify-item">
                            <div class="d-flex justify-content-center">
                                <div><img data-toggle="tooltip" data-placement="top" title="" class="notify-icon img-cover" src="/images/avatar/truongpv.jpg" data-original-title="truongpv"></div>
                                <div style="width:80%" class="notify-details font-bold position-relative">
                                    <div>#1496 Finished job</div>
                                    <small class="text-muted">3 hours ago</small>
                                </div>
                                <div class="div-dropdown-action dropdown" style="z-index:1001;">
                                    <span class="btn-circle btn-circle-cus btn-action-notify dropdown-item dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-check"></i>
                                    </span>
                                    <div class="dropdown-menu sub-aciton-menu" style="z-index:9999">
                                        <span class="dropdown-item sub-aciton-item" href="javascript:void(1496);"><i class="fa fa-check"></i> Mark as read</span>
                                        <span class="dropdown-item sub-aciton-item" href="javascript:void(1496);"><i class="fa fa-times"></i> Remove</span>
                                    </div>
                                </div>
                            </div>
                        </a>

                    </div>


                </div>
            </li>
            <li class="list-inline-item dropdown notification-list">
                <a id="openNotify" class="nav-link dropdown-toggle arrow-none waves-light waves-effect"  onclick="loadNotify()" role="button"
                   aria-haspopup="false" aria-expanded="false">
                    <i class=" ti-bell noti-icon"></i>
                    <span id="notify-count" class="notify-badge disp-none">0</span>
                </a>
            </li>
            <li class="list-inline-item dropdown notification-list">
                <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                   aria-haspopup="false" aria-expanded="false">
                    <img src="{{$user_login->avatar}}" onerror="this.onerror=null;this.src='images/default-avatar.png';" alt="user" class="rounded-circle img-cover">
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown " aria-labelledby="Preview">
                    <!-- item-->
                    <div class="dropdown-item noti-title">
                        <h5 class="text-overflow"><small>                        
                                @if(isset($user_login))
                                {{$user_login->user_name}}
                                @endif</small> </h5>
                    </div>

                    <!-- item-->
                    <a href="/help" class="dropdown-item notify-item">
                        <i class="ti-help-alt"></i> <span>Help</span>
                    </a>
                    <!-- item-->
                    <a href="logout" class="dropdown-item notify-item">
                        <i class="mdi mdi-logout"></i> <span>Logout</span>
                    </a>

                </div>
            </li>

        </ul>

        <ul class="list-inline menu-left mb-0">
            <li class="float-left">
                <button class="button-menu-mobile open-left waves-light waves-effect">
                    <i class="mdi mdi-menu"></i>
                </button>
            </li>
            <li class="float-left hide-phone">
                <a href="dashboard#promo-video-checker" class="btn-quick mr-2" >PROMO</a>
            </li>
            <li class="float-left hide-phone">
                <a href="dashboard#channel-confirm" class="btn-quick mr-2">CHANNEL CONFIRM</a>
            </li>
            <li class="float-left hide-phone">
                <a href="dashboard#submit-promo-claim" class="btn-quick mr-2">SUBMIT</a>
            </li>
            <li class="float-left hide-phone">
                <a href="dashboard#campaign-task" class="btn-quick mr-2">TASK</a>
            </li>

        </ul>

    </nav>

</div>

@include('dialog.notification.list_notification')
