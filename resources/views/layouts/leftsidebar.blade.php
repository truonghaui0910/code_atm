<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <!--- Divider -->
        <div id="sidebar-menu">
            <ul>
                <li class="menu-title">Main</li>

                <li>
                    <a href="/dashboard" class="waves-effect waves-primary"><i class="ti-home"></i><span>Dashboard</span></a>
                </li>
                @if(!in_array('22',explode(",", $user_login->role)))
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect waves-primary"><i class="ti-user"></i><span> Channel</span> <span class="menu-arrow"></span> </a>
                    <ul class="list-unstyled">
                        <li><a href="/channelmanagement/v2" class="waves-effect waves-primary"><span> Channel Management </span></a></li>
                     
                        <li><a href="/channel/epid" class="waves-effect waves-primary"><span> Channel Epid </span></a></li>
                     
                        <li><a href="/autochannel" class="waves-effect waves-primary"><span> Channel Auto</span></a></li>
                    </ul>
                </li>
<!--                <li>
                    <a href="/channelmanagement" class="waves-effect waves-primary"><i class="ti-user"></i><span>Channel Management</span></a>
                </li>
                <li>
                    <a href="/autochannel" class="waves-effect waves-primary"><i class="ti-briefcase"></i><span>Auto Channel</span></a>
                </li>-->
                @endif
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect waves-primary"><i class="fa fa-database"></i><span> Databases</span> <span class="menu-arrow"></span> </a>
                    <ul class="list-unstyled">
                        <li><a href="/boom" class="waves-effect waves-primary"><span> Boom </span></a></li>
                      
                        <li><a href="/album" class="waves-effect waves-primary"><span> Album </span></a></li>
                     
                        <li><a href="/studio/drive" class="waves-effect waves-primary"><span> Drive Studio </span></a></li>
                        <li><a href="/noclaim" class="waves-effect waves-primary"><span>Noclaim Database</span></a></li>
                        <li><a href="/shorts" class="waves-effect waves-primary"><span> Shorts </span></a></li>
                        <li><a href="/intros" class="waves-effect waves-primary"><span> Intros </span></a></li>
                    </ul>
                </li>
<!--                <li>
                    <a href="/boom" class="waves-effect waves-primary"><i class="fa fa-bomb" style="font-size: 25px;"></i><span>Boom Database</span></a>
                </li>
                <li>
                    <a href="/noclaim" class="waves-effect waves-primary"><i class="fa fa-database" ></i><span>Noclaim Database</span></a>
                </li>-->
                <li>
                    <a href="/branding" class="waves-effect waves-primary"><i class="fa fa-image fa-fw"></i><span>Branding</span></a>
                </li>
<!--                <li>
                    <a href="/intros" class="waves-effect waves-primary"><i class="fa fa-video-camera fa-fw"></i><span>Intros</span></a>
                </li>
                <li>
                    <a href="/shorts" class="waves-effect waves-primary"><i class="fa fa-mobile fa-fw" style="font-size: 25px;"></i><span>Shorts</span></a>
                </li>-->
<!--                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect waves-primary"><i class="ti-music-alt"></i><span> Make Music </span> <span class="menu-arrow"></span> </a>
                    <ul class="list-unstyled">
                        <li><a href="/musicmanagement" class="waves-effect waves-primary"><span>Music Management</span></a></li>  
                        <li><a href="/musicconfig" class="waves-effect waves-primary"><span>Music Config</span></a></li>
                        @if(in_array('11',explode(",", $user_login->role)) || in_array('1',explode(",", $user_login->role))|| in_array('20',explode(",", $user_login->role)))
                        <li><a href="/lyricconfig" class="waves-effect waves-primary"><span> Lyric Config</span></a></li>
                        @endif
                    </ul>
                </li>-->

                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect waves-primary"><i class="ti-harddrives"></i><span> Music Source</span> <span class="menu-arrow"></span> </a>
                    <ul class="list-unstyled">
                        <li><a target="_blank" href="http://source.automusic.win/spotify/cates" class="waves-effect waves-primary"><span> Spotify Cates </span></a></li>
                        <li><a href="/musicspotify" class="waves-effect waves-primary"><span> Spotify Music </span></a></li>
                        <li><a href="/spotifycharts?type=regional&country=global" class="waves-effect waves-primary"><span> Spotify Charts </span></a></li>
                        <li><a href="/chartmetricSpotifyPlaylist" class="waves-effect waves-primary"><span> Spotify Playlist </span></a></li>
                        <li><a href="/musictiktok" class="waves-effect waves-primary"><span> Tiktok Music </span></a></li>
                        <li><a href="/tiktokcharts" class="waves-effect waves-primary"><span> Tiktok Charts </span></a></li>
                        <li><a href="/showlyricdownload" class="waves-effect waves-primary"><span> Download Lyrics</span></a></li>

                    </ul>
                </li>
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect waves-primary"><i class="ti-share"></i><span> Promos</span> <span class="menu-arrow"></span> </a>
                    <ul class="list-unstyled">
                        <!--<li><a href="/campaign" class="waves-effect waves-primary"><span>Promo Campaigns V1</span></a></li>-->
                        <li><a href="/campaign2" class="waves-effect waves-primary"><span>Promo Campaigns</span></a></li>
                        @if(in_array('20',explode(",", $user_login->role)) || in_array('1',explode(",", $user_login->role)) || in_array('11',explode(",", $user_login->role)))
                        @if(!in_array('22',explode(",", $user_login->role)))
                        <li><a href="/claim" class="waves-effect waves-primary"><span>Claims Campaigns</span></a></li>
                        @if($is_admin_music)
                        <!--<li><a href="/360promo" class="waves-effect waves-primary"><span>360 Promo</span></a></li>-->
                        <li><a href="/360promo2" class="waves-effect waves-primary"><span>360 Promo V2</span></a></li>
                        @endif
                        <li><a href="/bitly" class="waves-effect waves-primary"><span>Bitly/MoonAz</span></a></li>
                        <li><a href="/boomvip" class="waves-effect waves-primary"><span>Boom Vip</span></a></li>
                        @endif
                        @endif
                        <!--<li><a href="/money" class="waves-effect waves-primary"><span>Moonshots Reports</span></a></li>-->
                        <li><a href="/tableau" class="waves-effect waves-primary"><span>Views Reports</span></a></li>
                    </ul>
                </li>
                <li>
                    <a href="/calendar" class="waves-effect waves-primary"><i class=" ti-calendar"></i><span>Calendar</span></a>
                </li>
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect waves-primary"><i class="fa fa-bar-chart-o"></i><span> Bassteam</span> <span class="menu-arrow"></span> </a>
                    <ul class="list-unstyled">
                        <a href="/money" class="waves-effect waves-primary"><span>Bassteam Reports</span></a>
                        @if($is_supper_admin)
                        <a href="/mooncoin" class="waves-effect waves-primary"><span>MoonCoin</span></a>
                        @endif
                    </ul>
                </li>
<!--                <li>
                    <a href="/money" class="waves-effect waves-primary"><i class="fa fa-bar-chart-o"></i><span>Bassteam Reports</span></a>
                </li>-->
                @if($is_supper_admin)
                <li>
                    <a href="/financial" class="waves-effect waves-primary"><i class="fa fa-usd" style="font-size: 25px;"></i><span>Moonshots Reports</span></a>
                </li>
                @endif

                @if(in_array('17',explode(",", $user_login->role)) || in_array('1',explode(",", $user_login->role)))
                <!--                <li>
                                    <a href="/videoclaim" class="waves-effect waves-primary"><i class="mdi mdi-account" style="font-size: 20px"></i><span> Import video claims</span></a>
                                </li>-->
                @endif


            </ul>

            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

