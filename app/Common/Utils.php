<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utils
 *
 * @author hoabt2
 */

namespace App\Common;

use Illuminate\Support\Facades\Config;
use Log;

class Utils {

    //2023/02/28
    public static function countInArray($string, $arr) {
        $count = 0;
        foreach ($arr as $data) {
            if (self::containString($data, $string)) {
                $count++;
            }
        }
        return $count;
    }

    public static function randomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randstring;
    }

    public static function encodeData($data) {
        return urlencode(base64_encode(json_encode($data)));
    }

    public static function decodeData($data) {
        return json_decode(base64_decode(urldecode($data)));
    }

    public static function log($job, $response) {
        $arrTmp = explode(";;", $job->log);
        if (count($arrTmp) > Config::get('config.max_rows_log')) {
            array_shift($arrTmp);
        }
        $arrTmp[] = $response;
        $job->log = implode(";;", $arrTmp);
    }

    public static function getUniqueKey($key) {
        $index = 0;
        $path = __DIR__ . "/index$key";
        if (!file_exists($path)) {
            file_put_contents($path, $index, LOCK_EX);
            return $index;
        }
        $fp = fopen($path, "r+");
        while (!flock($fp, LOCK_EX)) {  // acquire an exclusive lock
            usleep(100);
        }
        $indexS = fread($fp, filesize($path));
        $index = intval(trim($indexS));
        $index = $index + 1;
        fseek($fp, 0);
        fwrite($fp, "$index");
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        return $index;
    }

    public static function getUniqueKey_old1($key) {
        $index = 0;
        $path = __DIR__ . "\index$key";
        if (!file_exists($path)) {
            file_put_contents($path, $index, LOCK_EX);
            return $index;
        }
        $fp = fopen($path, "r+");
        while (!flock($fp, LOCK_EX)) {  // acquire an exclusive lock
            usleep(100);
        }
        $indexS = file_get_contents($path);
        $index = intval(trim($indexS));
        $index = $index + 1;
        fwrite($fp, "$index");
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        return $index;
    }

    public static function containString($data, $need_find) {
        if (strpos($data, $need_find) !== false) {
            return true;
        }
        return false;
    }

    //hàm check array string có nằm trong string khác không
    public static function containArrayString($bigString, $arrayNeedFind) {
        foreach ($arrayNeedFind as $needFind) {
            if (strpos(strtolower($bigString), strtolower($needFind)) !== false) {
                return true;
            }
        }
        return false;
    }

    public static function compare($value1, $value2, $operator, $isDateTime = false) {
        if (!isset($value1) || !isset($value2) || !isset($operator)) {
            return true;
        }

        $result = false;
        switch ($operator) {
            case ">":
                $result = $value1 > $value2;
                break;
            case "<":
                $result = $value1 < $value2;
                break;
            case "=":
                if ($isDateTime) {
                    if (preg_match("/20\d\d00/", $value1) || preg_match("/20\d\d00/", $value2)) {
                        $value1 = substr($value1, 0, -3);
                        $value2 = substr($value2, 0, -3);
                    }
                }
                $result = $value1 == $value2;
                break;
        }
        return $result;
    }

    public static function calcTime($totalAdded, $rate) {
        $now = time();
        $bias = Config::get('config.bias_next_time_run');
        $eachVideoTime = Config::get('config.time_each_video');
        $tmp = $totalAdded * $rate * $eachVideoTime + $bias;
        $nextTime = $now + rand(intval(0.8 * $tmp), $tmp);
        return $nextTime;
    }

    public static function calcTypeVideo($totalPriority, $totalNormal, $rate) {
        if ($totalPriority < 1 && $totalNormal < 1) {
            return 0;
        }
        if ($totalPriority < 1) {
            return 2;
        }
        if ($totalNormal < 1) {
            return 1;
        }
        if (floatval($totalPriority) / floatval($totalNormal) >= $rate) {
            return 1;
        }
        return 2;
    }

    public static function getKeyYoutube() {
        $indexKey = Utils::getUniqueKey("KeyYoutube");
        error_log("KeyYoutube: " . $indexKey);
        $keyArray = array(
            'AIzaSyAyuqFw77GTWBfU5dGXngOE11BDP1sXB9w',
            'AIzaSyC95ZNtWtIe7cHtWtI5LbIgzRDbPS-MrHc',
            'AIzaSyDi3g2JCPv185kqwoz21ZKS3MFU_s1kDH4',
            'AIzaSyBN8EoEt-JL_uLx_W5XXenziFeqEdaFGIM',
            'AIzaSyD-fndzEmQ0sZFOIp0IXEAiirXJ-5GSz7s',
            'AIzaSyAjODh9gpvikiDqWGMDaY5FikUqZAy6TBs',
            'AIzaSyBdReFOVpjRlT2F-zcqgrvx244W3YBnOuk',
            'AIzaSyB2CO7acoWNYCJEXTLEOb7ufjSAYgT3MAI',
            'AIzaSyBKIYeY3jzxCfo-tWudDtRCTNMMNZ13HF0',
            'AIzaSyCQyjMfsXKQwgPd25DFDK7EUEbHivov7Zs',
            'AIzaSyDStp-xG79OZPE36tjcW24bQsm-nnHBdfY',
            'AIzaSyB4tsFCoPX45bzJhUszyruC3TA11rv4_qw',
            'AIzaSyBP-cfMVcngu5CkG7VQx98JWs7LcJNleYA',
            'AIzaSyBMy3E-ahmn9BC-bCqO2O-_VD-tdywo-74',
            'AIzaSyDxEnH3EX_QKRWyaZv1cbAiSy3TATcB-us',
            'AIzaSyCmBtjyLvJ1OzrC3MphYyMYem4qmzUPl3Q',
            'AIzaSyCn-yW-JU-jttUN_j3SeAdniHA-KJaZ2v4',
            'AIzaSyB5V6a6-wn2WClcQB6o288lTY2ldTyA4GI',
            'AIzaSyD-3n1ScAQ4I1ZFSl6asaAs-10tX8T3JXA',
            'AIzaSyB7CdQGXJMKFvCYOK4V99Mx0MjgE32HtdQ',
            'AIzaSyCWfOPNeHjzJXFE4Gu5bg1hUHtfIddu0IA',
            'AIzaSyA60mmUlZLdyFngW52717EENDrLC_BeRQI',
            'AIzaSyAg_li3yPN3CcPb32etMP7Cx3Q1AiuydZU',
            'AIzaSyBknhHY8ahflWdtO18V_G4aYZj3Xtb24D0',
            'AIzaSyCEw0ex0bc9YSrj4uPSSceTgUa2hl0GoSs');
        return $keyArray[$indexKey % 25];
    }

    public static function shortNumber2Number($shortNumber) {
        if (strpos(strtoupper($shortNumber), "K") != false) {
            $shortNumber = rtrim($shortNumber, "kK");
            return floatval($shortNumber) * 1000;
        } else if (strpos(strtoupper($shortNumber), "M") != false) {
            $shortNumber = rtrim($shortNumber, "mM");
            return floatval($shortNumber) * 1000000;
        } else if (strpos(strtoupper($shortNumber), "B") != false) {
            $shortNumber = rtrim($shortNumber, "bB");
            return floatval($shortNumber) * 1000000000;
        } else if (strpos(strtoupper($shortNumber), "T") != false) {
            $shortNumber = rtrim($shortNumber, "tT");
            return floatval($shortNumber) * 1000000000000;
        } else {
            return floatval($shortNumber);
        }
    }

    public static function number2ShortNumber($num) {
        if ($num > 1000) {
            $x = round($num);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = array('K', 'M', 'B', 'T');
            $x_count_parts = count($x_array) - 1;
            $x_display = $x;
            $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
            $x_display .= $x_parts[$x_count_parts - 1];

            return $x_display;
        }
        return $num;
    }

    public static function getNumberFromText($string_number) {
        $result = 0;
        $temp = str_replace("views", "", $string_number);
        $temp = str_replace("view", "", $temp);
        $temp = str_replace("videos", "", $temp);
        $temp = str_replace("video", "", $temp);
        $temp = str_replace("dislikes", "", $temp);
        $temp = str_replace("dislike", "", $temp);
        $temp = str_replace("likes", "", $temp);
        $temp = str_replace("like", "", $temp);
        $temp = str_replace("subscribers", "", $temp);
        $temp = str_replace(",", "", $temp);
        if (self::containString("$temp", "K") || self::containString("$temp", "M") || self::containString("$temp", "B")) {
            $temp = self::shortNumber2Number($temp);
        } else {
            $temp = str_replace(".", "", $temp);
        }
        if (is_numeric(trim($temp))) {
            $result = intval(trim($temp));
        }
        return $result;
    }

    public static function validateDataInRange($data, $arr) {
        if (in_array($data, $arr)) {
            return 1;
        }
        return 0;
    }

    public static function isUnicode($string) {
        if (strlen($string) != strlen(utf8_decode($string))) {
            return 1;
        }
        return 0;
    }

    public static function write($fileName, $content) {
        $myfile = fopen($fileName, "w") or die("Unable to open file!");
        fwrite($myfile, $content);
        fclose($myfile);
    }

    public static function countDayLeft($dateString) {
        $date = strtotime($dateString);
        if (time() < $date) {
            return "This campaign has not started yet";
        } else {
            $timeLeft = $date + 90 * 86400;
            if ($timeLeft < time()) {
                return "0 day left";
            } else {
                return round(($timeLeft - time()) / 86400) . ' days left';
            }
        }
    }

    public static function countDuaDateHour($time) {

        if (time() > $time) {
            return 0;
        } else {
            $remain = $time - time();
            $timeMinute = floor($remain / 60);
            $timeHour = floor($timeMinute / 60);
            $timeDay = floor($timeHour / 24);
            $timeMonth = floor($timeDay / 30);
            $timeYear = floor($timeMonth / 12);
            if ($timeMinute == 0) {
                return 0;
            } else if ($timeMinute > 0 && $timeMinute < 60) {
                return $timeMinute . ($timeMinute == 1 ? ' minute' : ' minutes');
            } else if ($timeHour > 0 && $timeHour < 24) {
                return $timeHour . ($timeHour == 1 ? ' hour' : ' hours');
            } else if ($timeDay > 0 && $timeDay <= 30) {
                return $timeDay . ($timeDay == 1 ? ' day' : ' days');
            } else if ($timeMonth > 0 && $timeMonth <= 12) {
                return $timeMonth . ($timeMonth == 1 ? ' month' : ' months');
            } else if ($timeYear > 0) {
                return $timeYear . ($timeYear == 1 ? ' year' : ' years');
            }
        }
    }

    //chuyển số giờ thành các đơn vị khác
    public static function convertHours($hours) {
        if ($hours < 1) {
            return ['value' => $hours * 60, 'unit' => 'minutes'];
        } else {
            return ['value' => $hours, 'unit' => 'hours'];
        }
//        elseif ($hours < 24) {
//            return ['value' => $hours, 'unit' => 'hours'];
//        } 
//        elseif ($hours < 24 * 7) {
//            return ['value' => $hours / 24, 'unit' => 'days'];
//        } elseif ($hours < 24 * 30) {
//            return ['value' => $hours / (24 * 7), 'unit' => 'weeks'];
//        } else {
//            return ['value' => $hours / (24 * 30), 'unit' => 'months'];
//        }
    }

    //chuyển các đơn vị khác thành giờ
    public static function convertToHours($value, $unit) {
        switch (strtolower($unit)) {
            case 'minutes':
                return $value / 60;
            case 'hours':
                return $value;
            case 'days':
                return $value * 24;
            case 'weeks':
                return $value * 24 * 7;
            case 'months':
                return $value * 24 * 30; // Trung bình một tháng là 30 ngày
            default:
                throw new Exception("Unit invalid: $unit");
        }
    }

    //convert YYYYMMDD -> MM-DD-YYYY
    public static function stringTimeToDate($time) {
        return substr($time, 4, 2) . '-' . substr($time, 6, 2) . '-' . substr($time, 0, 4);
    }

    public static function calcTimeText($time) {
        if ($time == null) {
            return "Unknown";
        }
        $timeMinute = floor((time() - $time) / 60);
        $timeHour = floor($timeMinute / 60);
        $timeDay = floor($timeHour / 24);
        $timeMonth = floor($timeDay / 30);
        $timeYear = floor($timeMonth / 12);
        if ($timeMinute == 0) {
            return "Just now";
        } else if ($timeMinute > 0 && $timeMinute < 60) {
            return $timeMinute . ' minutes ago';
        } else if ($timeHour > 0 && $timeHour < 24) {
            return $timeHour . ($timeHour == 1 ? ' hour ago' : ' hours ago');
        } else if ($timeDay > 0 && $timeDay <= 30) {
            return $timeDay . ($timeDay == 1 ? ' day ago' : ' days ago');
        } else if ($timeMonth > 0 && $timeMonth <= 12) {
            return $timeMonth . ($timeMonth == 1 ? ' month ago' : ' months ago');
        } else if ($timeYear > 0) {
            return $timeYear . ($timeYear == 1 ? ' year ago' : ' years ago');
        }
    }

    public static function parseChannelId($url) {
        $parsed = parse_url(rtrim($url, '/'));
        if (isset($parsed['path']) && preg_match('/^\/channel\/(([^\/])+?)$/', $parsed['path'], $matches)) {
            return $matches[1];
        }
        return null;
    }

    //convert string time GMT+7 thành timestamp
    public static function stringToTimeGmT7($string) {
        return strtotime("$string GMT+07:00");
    }

    //convert time thành string time GMT+7
    public static function timeToStringGmT7($time, $slash = "-") {
        return gmdate("Y" . $slash . "m" . $slash . "d H:i:s", $time + (7 * 3600));
    }

    //convert YYYYMMDD -> YYYY/MM/DD
    public static function convertToViewDate($date) {
        if ($date != null && $date != "") {
            return substr($date, 0, 4) . '/' . substr($date, 4, 2) . '/' . substr($date, 6, 2);
        }
        return '';
    }

    public static function slugify($text, string $divider = '-') {
        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public static function userCode2userName($userCode) {
        $pos = strripos($userCode, '_');
        return substr($userCode, 0, $pos);
    }

    //kiểm tra url có exists không
    public static function urlExists($url) {
        $file_headers = @get_headers($url);
        if (!$file_headers || self::containString($file_headers[0], "404")) {
            $exists = false;
        } else {
            $exists = true;
        }
        return $exists;
    }

    public static function getDriveID($gdriveurl) {
        $filter1 = preg_match('/drive\.google\.com\/open\?id\=(.*)/', $gdriveurl, $fileid1);
        $filter2 = preg_match('/drive\.google\.com\/file\/d\/(.*?)\//', $gdriveurl, $fileid2);
        $filter3 = preg_match('/drive\.google\.com\/uc\?id\=(.*?)\&/', $gdriveurl, $fileid3);
        $filter4 = preg_match('/drive\.usercontent\.google\.com\/uc\?id\=(.*?)\&/', $gdriveurl, $fileid4);
        if ($filter1) {
            $fileid = $fileid1[1];
        } else if ($filter2) {
            $fileid = $fileid2[1];
        } else if ($filter3) {
            $fileid = $fileid3[1];
        } else if ($filter4) {
            $fileid = $fileid4[1];
        } else {
            $fileid = null;
        }

        return($fileid);
    }

    public static function getDriveID2($gdriveurl) {
        $fileid = null;
        $type = null;

        // Kiểm tra URL của file
        if (preg_match('/drive\.google\.com\/open\?id\=(.*)/', $gdriveurl, $match)) {
            $fileid = $match[1];
            $type = "file";
        } elseif (preg_match('/drive\.google\.com\/file\/d\/(.*?)\//', $gdriveurl, $match)) {
            $fileid = $match[1];
            $type = "file";
        } elseif (preg_match('/drive\.google\.com\/uc\?id\=(.*?)\&?/', $gdriveurl, $match)) {
            $fileid = $match[1];
            $type = "file";
        } elseif (preg_match('/drive\.usercontent\.google\.com\/uc\?id\=(.*?)\&?/', $gdriveurl, $match)) {
            $fileid = $match[1];
            $type = "file";
        }
        // Kiểm tra URL của folder
        elseif (preg_match('/drive\.google\.com\/drive\/folders\/(.*?)($|\?)/', $gdriveurl, $match)) {
            $fileid = $match[1];
            $type = "folder";
        }

        // Trả về JSON
        return (object) [
                    "type" => $type ?? "unknown",
                    "drive_id" => $fileid ?? null
        ];
    }

    public static function getUserFromUserCode($userCode) {
        $pos = strripos($userCode, '_');
        return substr($userCode, 0, $pos);
    }

    //chuyển array string thành string endline
    public static function array2String($data) {
        if ($data == null || $data == "") {
            return "";
        }
        $result = implode(PHP_EOL, json_decode($data));
        return $result;
    }

    public static function textArea2Array($text) {
        if ($text == null || $text == "") {
            return [];
        }
        return explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($text)));
    }

    public static function parseUrl($url) {
        if (self::containString($url, "drive.google.com")) {
            return (object) array("name" => self::getTitleUrl($url), "link" => $url, "icon" => "/images/brand/icons8-google-drive-48.png");
        }
        if (self::containString($url, "docs.google.com/document")) {
            return (object) array("name" => self::getTitleUrl($url), "link" => $url, "icon" => "/images/brand/icons8-google-docs-48.png");
        }
        if (self::containString($url, "docs.google.com/spreadsheets")) {
            return (object) array("name" => self::getTitleUrl($url), "link" => $url, "icon" => "/images/brand/icons8-google-sheets-48.png");
        }
        if (self::containString($url, "youtube.com") || self::containString($url, "youtu.be")) {
            return (object) array("name" => self::getTitleUrl($url), "link" => $url, "icon" => "/images/brand/icons8-youtube-48.png");
        }
        if (self::containString($url, "facebook.com")) {
            return (object) array("name" => "Facebook", "link" => $url, "icon" => "/images/brand/icons8-facebook-48.png");
        }
        if (self::containString($url, "spotify.com")) {
            return (object) array("name" => "Spotify", "link" => $url, "icon" => "/images/brand/icons8-spotify-48.png");
        }
        if (self::containString($url, "trello.com")) {
            return (object) array("name" => self::getTitleUrl($url), "link" => $url, "icon" => "/images/brand/icons8-trello-48.png");
        }
        if (self::containString($url, "deezer.com")) {
            return (object) array("name" => self::getTitleUrl($url), "link" => $url, "icon" => "/images/brand/icons8-deezer-48.png");
        }
        return (object) array("name" => "", "link" => $url, "icon" => "");
    }

    public static function isUrl($text) {
        if (self::containString($text, "http://") || self::containString($text, "https://")) {
            return 1;
        }
        return 0;
    }

    public static function makeNiceUrl($url) {
        $info = self::parseUrl($url);
//        Log::info("info:".json_encode($info));
        if ($info->name != "") {
            $word = "<a target='_blank' href='$url'><img class='web-icon' src='$info->icon'> <span>$info->name</span></a>";
        } else {
            return $url;
        }
        return $word;
    }

    public static function detectCommentUrl($string) {

        $cleaned_text = strip_tags($string, '<p><img><span><div>');
//        Log::info('cleaned_text: ' . $cleaned_text);
        $pattern = '/https?:\/\/[^\s<]+/';

        // Thay thế mỗi đường link trong đoạn mã HTML bằng đoạn mã HTML mới chứa đường link trả về
        $updated_html = preg_replace_callback($pattern, function($matches) {
            return self::makeNiceUrl($matches[0]);
        }, $cleaned_text);
        return $updated_html;
//        preg_match_all('/<p>(.*?)<\/p>/', $string, $matches);
//        $paragraphs = $matches[0];
//        Log::info(json_encode($paragraphs));
//        foreach($paragraphs as $html){
////            $pattern = '/<a\s[^>]*>(.*?)<\/a>/';
////            preg_match($pattern, $html, $matches);
////            if(count($matches)==2){
////            $innerHtml = $matches[1];
////            Log::info('link: '.$innerHtml);
////            }
//            $cleaned_text = strip_tags($html, '<p><span>');
//            Log::info('cleaned_text: '.$cleaned_text);
//        }
//        $result = [];
//        $arraySource = explode("\n", trim($string));
//        foreach ($arraySource as $line) {
//            $newWords = [];
//            $words = explode(" ", $line);
//            foreach ($words as $word) {
////                $check = self::parseUrl($word);
//////                Log::info("check ".json_encode($check));
////                if ($check->name != "") {
////                    $word = "<iframe class='iframe-url' src='$check->link'>$check->name</iframe>";
////                }
//
//                if (self::isUrl($word)) {
//                    $info = self::parseUrl($word);
//                    if ($info->name != "") {
//                        $word = "<a target='_blank' href='$word'><img class='web-icon' src='$info->icon'> <span>$info->name</span></a>";
//                    }
//                }
//                $newWords[] = $word;
//            }
//            $tmp = implode(" ", $newWords);
////            Log::info("tmp $tmp");
//            $result[] = $tmp;
//        }
//        return implode("<br>", $result);
    }

    public static function getTitleUrl($url) {
        $result = null;
        try {

            $str = @file_get_contents($url);
            if ($str === FALSE) {
                return "";
            }
            if (strlen($str) > 0) {
                $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
                preg_match("/\<title\>(.*)\<\/title\>/i", $str, $title); // ignore case
                if (count($title) == 2) {
                    $result = $title[1];
                }
            }
        } catch (Exception $exc) {
            Log::info($exc->getTraceAsString());
        }

        return $result;
    }

    //xóa tất cả phần tử tìm thấy trong mảng
    public static function deleteValueArray($deletes, $array) {
        foreach ($array as $key => $element) {
            foreach ($deletes as $deleteText) {
                if ($element == $deleteText) {
                    unset($array[$key]);
                }
            }
        }
        return array_values($array);
    }

    public static function generateRandomHash($length = 16) {
        return bin2hex(random_bytes($length / 2));
    }

    public static function generateRandomString($length = 16) {
        $today = strtotime(date("m/d/Y H:i:s"));
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
            $randomStringDate = $randomString . $today;
        }

        return $randomStringDate;
    }

    public static function getFirstNameLowercase($name) {
        // Tách tên thành mảng các từ
        $words = explode(" ", $name);

        // Lấy từ đầu tiên và chuyển thành chữ thường
        return strtolower($words[0]);
    }

    //random ngày sinh
    public static function randomBirthday($startYear = 1990, $endYear = 2005) {
        // Tạo timestamp từ ngày đầu tiên của năm bắt đầu và ngày cuối cùng của năm kết thúc
        $start = strtotime("$startYear-01-01");
        $end = strtotime("$endYear-12-31");

        // Lấy một timestamp ngẫu nhiên trong khoảng này
        $randomTimestamp = mt_rand($start, $end);

        // Trả về ngày theo định dạng Y-m-d
        return date("Y-m-d", $randomTimestamp);
    }

    //hàm sinh ra mail recovery từ email có sẵn sử dụng được, mail này vẫn nhận được mail bình thường
    public static function genRecovery($sourceEmail) {
        $used = rand(0, 200);
        $emailTmp = str_replace("@gmail.com", "", $sourceEmail);
        $emailTmp = str_replace(".", "", $emailTmp);
        $emailTmp = trim($emailTmp);
        $pos = self::getPos(strlen($emailTmp), $used);
        $arr_index = explode(",", $pos);
        $cnt = 0;
        foreach ($arr_index as $index) {
            $emailTmp = substr_replace($emailTmp, ".", $index + $cnt, 0);
            $cnt++;
        }
        $emailTmp = $emailTmp . "@gmail.com";
        return $emailTmp;
    }

    public static function getPos($leng, $index) {
        $firstArray = [];
        for ($i = 1; $i < $leng; $i += 1) {
            $firstArray[] = "$i";
        }
        $arrRs = $firstArray;
        $arrTmp = $firstArray;
        if ($index < count($arrTmp)) {
            return $arrTmp[$index];
        }
        $index -= count($arrTmp);
        for ($i = 1; $i < $leng; $i += 1) {
            $arrTmp = self::genByArray($arrTmp, $leng);
            if ($index < count($arrTmp)) {
                return $arrTmp[$index];
            }
            $index -= count($arrTmp);
        }
        return null;
    }

    public static function genByArray($arrStart, $leng) {
        $arrTmpRS = [];
        foreach ($arrStart as $tmp) {
            $a = explode(",", $tmp);
            $last = end($a);
            for ($i = $last + 1; $i < $leng; $i += 1) {
                $arrTmpRS[] = $tmp . "," . $i;
            }
        }
        return $arrTmpRS;
    }

    public static function getSpotifyIdFromUrl($url) {
        // Tách URL để lấy đoạn ID
        $pattern = "/playlist\/([a-zA-Z0-9]+)(\?|$)/";
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1]; // ID nằm ở nhóm 1
        }
        return null; // Trả về null nếu không tìm thấy
    }

    public static function extractYoutubeChannelId($input) {
        // Loại bỏ khoảng trắng
        $channel = trim($input);

        // Pattern cho channel ID: bắt đầu bằng "UC" và theo sau là 22 ký tự chữ và số
        $channelIdPattern = '/^UC[a-zA-Z0-9_-]{22}$/';

        // Pattern cho channel ID có phần mở rộng như /about, /videos, etc.
        $channelIdExtendedPattern = '/^(UC[a-zA-Z0-9_-]{22})\/.*$/';

        // Kiểm tra nếu input đã là channel ID
        if (preg_match($channelIdPattern, $channel)) {
            return $channel;
        }

        // Kiểm tra nếu input là channel ID có phần mở rộng (/about, /videos, etc.)
        if (preg_match($channelIdExtendedPattern, $channel, $matches)) {
            return $matches[1];
        }

        // Kiểm tra URL
        $urlPatterns = [
            // URL dạng: https://www.youtube.com/channel/UCNxa69PEwsxx3-hgSM44KEA
            '/youtube\.com\/channel\/(UC[a-zA-Z0-9_-]{22})/',
            // URL dạng: https://youtube.com/channel/UCNxa69PEwsxx3-hgSM44KEA
            '/youtube\.com\/channel\/(UC[a-zA-Z0-9_-]{22})/',
            // URL dạng: www.youtube.com/channel/UCNxa69PEwsxx3-hgSM44KEA
            '/youtube\.com\/channel\/(UC[a-zA-Z0-9_-]{22})/',
            // URL với các tham số khác: https://www.youtube.com/channel/UCNxa69PEwsxx3-hgSM44KEA?view_as=subscriber
            '/youtube\.com\/channel\/(UC[a-zA-Z0-9_-]{22})(\?|\&)/',
            // URL với phần mở rộng: https://www.youtube.com/channel/UCNxa69PEwsxx3-hgSM44KEA/about
            '/youtube\.com\/channel\/(UC[a-zA-Z0-9_-]{22})\/[a-zA-Z0-9_-]+/'
        ];

        foreach ($urlPatterns as $pattern) {
            if (preg_match($pattern, $channel, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public static function sanitizeFilename($filename) {
        // Remove or replace invalid characters
        $lastName = preg_replace('/[^\w\s\-\.\(\)]/u', '_', $filename);
        $lastName = preg_replace('/\s+/', ' ', $lastName);
        return trim($lastName);
    }

}
