
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
            <li class="list-inline-item dropdown notification-list">
                <a id="openNotifyMoney" class="nav-link dropdown-toggle arrow-none waves-light waves-effect"  onclick="loadNotifyMoney()" role="button"
                   aria-haspopup="false" aria-expanded="false">
                    <i class="ti-money noti-icon"></i>
                    <span id="notify-count" class="notify-badge ">10</span>
                </a>
            </li>

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
