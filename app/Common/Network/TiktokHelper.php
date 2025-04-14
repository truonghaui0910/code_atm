<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Network;

use Log;

class TiktokHelper {

    public static function getPlaylistData($playlistID) {
        $curl = "curl --location 'https://api22-normal-c-alisg.tiktokv.com/aweme/v1/music/list/?mc_id=$playlistID&cursor=0&count=100&sound_page_scene=0&iid=7298774040435672837&device_id=7298773065894200838&ac=wifi&channel=googleplay&aid=1180&app_name=trill&version_code=320004&version_name=32.0.4&device_platform=android&os=android&ab_version=32.0.4&ssmix=a&device_type=SM-S901N&device_brand=samsung&language=vi&os_api=28&os_version=9&openudid=de29072515c9aef4&manifest_version_code=320004&resolution=720*1280&dpi=320&update_version_code=320004&_rticket=1699602336366&is_pad=0&current_region=VN&app_type=normal&sys_region=VN&mcc_mnc=45204&timezone_name=Asia%2FHo_Chi_Minh&carrier_region_v2=452&residence=VN&app_language=vi&carrier_region=VN&ac2=wifi5g&uoo=0&op_region=VN&timezone_offset=25200&build_number=32.0.4&host_abi=arm64-v8a&locale=vi-VN&region=VN&ts=1699602335&=' \
--header 'Host: api22-normal-c-alisg.tiktokv.com' \
--header 'x-tt-multi-sids: 6532062020072636417%3A1ae8988e02c1f72debdb4010ceb44db2' \
--header 'sdk-version: 2' \
--header 'x-tt-token: 041ae8988e02c1f72debdb4010ceb44db2016b762a31dfb4a9978c301697d8167b96c5004eb7a77bfb0ecaa7a62b05cadbda0e92249908159790d17d7a2c54c5be5a07f2c1ac610bb4978ca4ba438b1c9c0d406f479be7b0339aaff9b0924591b0fc2-CkBiZmQyNGY3ZDYzZDJkOTJlYWU1YWNjMjAyNGM5NzA2ODgyNDI4MDNmYzFjOTUwMmE2ZjdkNjE1ZmRmYTMwZmI3-2.0.0' \
--header 'x-ss-req-ticket: 1699602336369' \
--header 'multi_login: 1' \
--header 'passport-sdk-version: 19' \
--header 'x-tt-dm-status: login=1;ct=1;rt=1' \
--header 'x-vc-bdturing-sdk-version: 2.3.4.i18n' \
--header 'x-tt-store-region: vn' \
--header 'x-tt-store-region-src: uid' \
--header 'user-agent: com.ss.android.ugc.trill/320004 (Linux; U; Android 9; vi_VN; SM-S901N; Build/PQ3A.190605.003;tt-ok/3.12.13.4-tiktok)' \
--header 'Cookie: odin_tt=40b1c655a53f124d4424f6554109be230c3c9117ead94cce4cff4606cf8a1a0419849e7658a7875c62eeded4503dbe377c7f653949e7e2a15b3c27f8f0736f1fa7d03d83f5f65033df84a9e4b888599b'";
        $datas = shell_exec($curl);
        return $datas;
    }

}
