<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Network;

use Log;

class BitlyHelper {

    public static function crawStats($token, $date, $bitlyLink) {
        $url = "https://api-ssl.bitly.com/v4/bitlinks/bit.ly/$bitlyLink/clicks/summary?unit=day&units=1&unit_reference=" . $date . "T16%3A59%3A59-0700";
        return ProxyHelper::bitly($token, $url);
    }
    public static function crawStatsMotnh($token, $date, $bitlyLink) {
        $url = "https://api-ssl.bitly.com/v4/bitlinks/bit.ly/$bitlyLink/clicks/summary?unit=month&units=1&unit_reference=" . $date . "T16%3A59%3A59-0700";
        return ProxyHelper::bitly($token, $url);
    }

    public static function crawGroups($token) {
        $url = "https://api-ssl.bitly.com/v4/groups";
        return ProxyHelper::bitly($token, $url);
    }

    public static function crawLinks($token, $groupId) {
        $result = [];
        $next = null;
        $url = "https://api-ssl.bitly.com/v4/groups/$groupId/bitlinks";
        $datas = ProxyHelper::bitly($token, $url);
        if (!empty($datas->links)) {
            $result = array_merge($result, $datas->links);
        }
        if (!empty($datas->pagination->next)) {
            $next = $datas->pagination->next;
        }
        $page = 2;
        while (true) {
            if ($next == null) {
                error_log("crawLinks $page break");
                break;
            } else {
                $url = "https://api-ssl.bitly.com/v4/groups/$groupId/bitlinks?page=$page&req_src=next&search_after=page$page";
                error_log("crawLinks $page url=$url");
                $datas = ProxyHelper::bitly($token, $url);
                if (!empty($datas->links)) {
                    $result = array_merge($result, $datas->links);
                }
                $next = null;
                if (!empty($datas->pagination->next)) {
                    $next = $datas->pagination->next;
                    if ($next != null) {
                        $page = $datas->pagination->page + 1;
                    }
                }
            }
        }
        return $result;
    }

    public static function create($token, $data) {
        $url = "https://api-ssl.bitly.com/v4/bitlinks";
        return ProxyHelper::bitly($token, $url, "POST", $data);
    }

    public static function createCustom($token, $data) {
        $url = "https://api-ssl.bitly.com/v4/custom_bitlinks";
        return ProxyHelper::bitly($token, $url, "POST", $data);
    }

}
