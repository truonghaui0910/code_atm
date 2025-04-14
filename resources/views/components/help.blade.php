@extends('layouts.master')

@section('content')
<div class="col-md-12">
    <div class="card-box">
        <!--<h4 class="header-title m-t-0 m-b-30">Tabs Bordered</h4>-->
        <!--<div class="tabs-vertical-env">-->

        <ul class="nav nav-tabs tabs-bordered">
            <li class="nav-item">
                <a href="#home-b1" data-toggle="tab" aria-expanded="true" class="nav-link active">
                    API
                </a>
            </li>
            <li class="nav-item">
                <a href="#profile-b1" data-toggle="tab"  aria-expanded="false" class="nav-link">
                    MoonShots
                </a>
            </li>
            <li class="nav-item">
                <a href="#moonaz-b1" data-toggle="tab"  aria-expanded="false" class="nav-link">
                    Moonaz.net
                </a>
            </li>
            <li class="nav-item">
                <a href="#tiktok-b1" data-toggle="tab"  aria-expanded="false" class="nav-link">
                    Tiktok
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="home-b1">
                <h3>Instructions to activate api</h3>
                <h4>1: Orfium Api</h4>
                <p>Go to the website on the browser you have logged into the channel <a href="https://videomanager.orfium.com/auth/" target="_blank">https://videomanager.orfium.com/auth/</a></p>
                <h4>2: Googla Api</h4>
                <!--<p>Step 1: Download Tool Googla: <a href="https://drive.google.com/file/d/1fkYB4wcaYt5E8KETloL9HKi3zlOMBEz_/view?usp=sharing" target="_blank">Download Googla</a></p>-->
                <p>Step 1: Download Tool Googla: <a href="https://drive.google.com/file/d/1IUyhKmA8omuP2QWIEskyX-zNcKhrNJOe/view?usp=sharing" target="_blank">Download Googla</a></p>
                <p>Step 2: Extract googla.zip</p>
                <!--<p><b>Step 3: Run as administrator init.cmd</b> <img width="300px" src="https://automusic.win/images/runas.png"/></p>-->
                <p>Step 3: Run setup1.exe <img width="400px" src="https://automusic.win/images/run_tool.PNG"/></p>
                <p>Lưu ý: Chỉ cần chạy file setup1.exe 1 lần duy nhất, và giữ ở máy không tắt đi, khi nào reset máy thì chạy lại từ Step 3</p>
                <p>Step 4: Ấn nút COPY LINK và paste vào trình duyệt đang được đăng nhập kênh.
                    <button data-toggle="tooltip" data-placement="top" data-original-title="Click here to ADD API" type="button" class="rediect btn btn-success btn-import-releases m-r-5">COPY LINK</button>
                </p>
            </div>
            <div class="tab-pane fade" id="profile-b1">
                <h3>Hướng dẫn sử dụng tool MoonShots (Công cụ quản lý profile)</h3>
                <p>Bước 1: Download Tool MoonShots : <a href="https://drive.google.com/file/d/1A_1-YkS_jsU5gBmq33eLD7yBRht69F9y/view?usp=sharing" target="_blank">Tool MoonShots</a> 
                    <br>Chức năng Update để cập nhật phiên bản mới nhất <span class="color-red">(Lưu ý: phải tắt hết Moonshots đang chạy trước khi bấm Update)</span> <i class="fa fa-download btn-update-gologin color-red" data-toggle="tooltip" data-placement="top" data-original-title="Update MoonShots"></i>
                    
                </p>
                <p>Bước 2: Giải nén và chạy file setup.exe</p>
                <p>Bước 3: Vào menu <a href="https://automusic.win/channelmanagement" target="_blank">Channel Management</a></p>
                <p>Bước 4: Để đăng nhập vào kênh, bấm nút Gologin <img width="200px" src="https://automusic.win/images/gologin.png"/>, Hệ thống sẽ tự động mở 1 trình duyệt lên,sử dụng mail và password đã được cung cấp đăng nhập vào gmail như bình thường.</p>
                <p>Bước 5: Sau khi đăng nhập thành công, vào gmail.com -> Google Setting -> Security -> 2-Step Verification -> Sử dụng key để active như bình thường</p>
                <p>Bước 6: Tạo OTP KEY.Làm theo ảnh <span class="color-red"><b>6.1</b></span><img width="500px" src="https://automusic.win/images/otpkey1.jpg"/>
                    <span class="color-red"><b>6.2</b></span><img width="500px" src="https://automusic.win/images/otpkey21.jpg"/>
                    <br><span class="color-red"><b>6.3</b></span><img width="500px" src="https://automusic.win/images/otpkey3.jpg"/>
                    <span class="color-red"><b>6.4</b></span><img width="500px" src="https://automusic.win/images/otpkey4.jpg"/></p>
                <!--<p>Bước 6: Sau khi đăng nhập thành công, bấm nút <b>Commit Gologin</b> để lưu lại profile vừa đăng nhập, Bước 6 bắt buộc phải thực hiện khi thực hiện Bước 4 lần đầu tiên.</p>-->
                <p>Bước 7: Nhập OTP KEY lấy được ở bước 6.4 <img width="500px" src="https://automusic.win/images/otpkey6.jpg"/> </p>
                <p>Bước 8: Lấy OPT đăng nhập.Bấm vào nút <b>Get Code Login</b>, nếu thành công sẽ có thông báo và hệ thống sẽ tự động copy 6 số vào clipboard, chỉ cần paste vào ô nhập OTP, rồi bấm nút <b>Verify</b> (Lưu ý, sau khi thực hiện Bước 7, hệ thống sẽ tự động <b>Get Code Login</b> nên ko cần thực hiện Bước 8, thực hiện Bước 8 khi cần Verify  ) </p>
                <img width="500px" src="https://automusic.win/images/otpkey5.jpg"/>
            </div>
            <div class="tab-pane fade" id="moonaz-b1">
                <h4>Yêu cầu mọi người gắn Video Intro này vào tất cả các video upload trên các Kênh của mọi người.</h4>
                <p>Mọi người có thể sử dụng 1 trong các Style dưới đây để ghép. Một số kênh Main sẽ được cấp video riêng!</p>
                <!--<h5>Style riêng cho các Kênh Main:</h5>-->
                <p><a href="https://drive.google.com/file/d/1K4ayoLiH7J0iHmPwRn_EEiPF1Jtk0jae/view?usp=share_link" target="_blank">Link Video Style 1</a></p>
                <p><a href="https://drive.google.com/file/d/17VFKXuQWiP-DZzNkcjaB8fjNGx0vnU6C/view?usp=share_link" target="_blank">Link Video Style 2</a></p>
                <h5>Nội dung cần ghi trong mô tả: </h5>
                <p>1. Free ringtones and wallpapers: https://bit.ly/free_wallpapers_</p>
                <p>2. Unlimited ringtones and wallpapers: https://bit.ly/_free_ringtones</p>
                <p>3. Explore ringtones and wallpapers: https://bit.ly/free_wallpapers_</p>
                <p>4. Download ringtones and wallpapers: https://bit.ly/_free_ringtones</p>
                <p>6. Down/Up ringtones and wallpapers: https://bit.ly/free_wallpapers_</p>

  
            </div>
            <div class="tab-pane fade" id="tiktok-b1">
                <h3>Chorme extensions Tiktok</h3>
                <p>Bước 1: Download <a href="https://drive.google.com/file/d/1ChbZt-XMXoQIgJRlbIELZX0c6aY69NVI/view?usp=sharing" target="_blank">Tiktok extensions</a></p>
                <p>Bước 2: Giải nén file tiktok_reup_extension.zip</p>
                <p>Bước 3: Vào trang  <a href="chrome://extensions/" target="_blank">chrome://extensions/</a></p>
                <p>Bước 4: Làm như hình <img width="500px" src="https://automusic.win/images/setup_extention.png"/></p>
                <p>Bước 5: Kiểm tra nếu có extension như hình là thành công <img width="500px" src="https://automusic.win/images/setup_extention_check.png"/></p>
  
            </div>
        </div>
        <!--</div>-->
    </div>
</div> 

<!--<p>Step 6: Run setup2.exe</p>
<p>Step 7: Choose channel</p>-->
<!--<p>Step 5: Choose channel</p>
<p>Step 6: Run setup2.exe</p>
<p>Step 7: Choose channel</p>-->
@endsection

@section('script')
<script type="text/javascript">
    $('.btn-update-gologin').click(function (e) {
        e.preventDefault();
        window.open("AutoProfile://update", "_blank");
    });
    $(".rediect").click(function (e) {
        e.preventDefault();
        $.ajax({
            type: "GET",
            url: "/redirect/add/api",
            data: {},
            dataType: 'json',
            success: function (data) {
                console.log(data);
//                window.location.href='https://acc.autoplaylists.win/add-youtube-cams.php?sv='+data.net;
                copyToClipboard('https://acc.autoplaylists.win/add-youtube-cams.php?sv=' + data.net);
            },
            error: function (data) {
                console.log(data);
            }
        });
    });

</script>
@endsection

