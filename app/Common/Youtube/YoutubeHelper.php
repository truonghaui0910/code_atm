<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Youtube;

use App\Common\Network\ProxyHelper;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Http\Models\Proxy;
use Google_Client;
use Google_Service_YouTube;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Log;

/**
 * Description of YoutubeHelper
 *
 * @author hoabt2
 */
class YoutubeHelper {

    //put your code here
    public static function processMutiSource($links) {
        $separate = Config::get('config.separate_text');
        $arrTmp = explode($separate, $links);
        $arrResult = array();
        foreach ($arrTmp as $link) {
            $arrResult[] = self::processSource($link);
        }
        return json_encode($arrResult);
    }

    public static function processSource($link) {
        //0: keyword
        //1: playlist,channel
        //2: video
        $type = 0;
        if (strpos($link, "youtu.be") !== FALSE) {
            $type = 2;
            $regexYoutubeId = "/youtu.be\/(?:(.+?)\?|(.+)$)/";
            preg_match($regexYoutubeId, $link, $matches);
            if (count($matches) > 0) {
                $link = $matches[count($matches) - 1];
            }
        }
        if (strpos($link, "youtube.") !== FALSE) {
            //url
            $type = 1;
            if (strpos($link, "/user/") !== FALSE) {
                $response = RequestHelper::get($link, 1);
                $regexChannelId = "/name='channel_ids'\\s+value=\"(.+?)\"/";
                preg_match($regexChannelId, $response, $matches);
                if (count($matches) == 2) {
                    $link = preg_replace("/UC/i", "UU", $matches[1], 1);
                }
            } else {
                $regexPLChannelId = "/(?:list=|channel\/)(?:(.+?)&|(.+)$)/";
                preg_match($regexPLChannelId, $link, $matches);
                if (count($matches) > 1) {
                    $link = preg_replace("/UC/i", "UU", $matches[count($matches) - 1], 1);
                } else {
                    $regexVideoId = "/v=(?:(.+?)&|(.+)$)/";
                    preg_match($regexVideoId, $link, $matches);
                    $type = 2;
                    if (count($matches) > 0) {
                        $link = $matches[count($matches) - 1];
                    }
                }
            }
        }
        return array("type" => $type, "id" => $link);
    }

    private static function findVid($data, $pattern) {
        preg_match_all($pattern, $data, $matchers);
        $arrTmp = array();
        if (isset($matchers) && count($matchers) > 1) {
            $arrTmp = array_unique($matchers[1]);
        }
        return $arrTmp;
    }

    private static function findNextLink($data, $pattern, $isFirst = false) {
        preg_match_all($pattern, $data, $matchers);
        if (count($matchers[1]) > 0) {
            if ($isFirst) {
                return "https://m.youtube.com/" . $matchers[1][0];
            } else {
                return isset($matchers[1][1]) ? "https://m.youtube.com/" . $matchers[1][1] : null;
            }
        }
        return null;
    }

    public static function getVideoDieFromPlaylist($playlistId, $maxPage = 100) {
        $url = "https://m.youtube.com/playlist?list=" . $playlistId;
        $arrResult = array();
        $isFirst = true;
        $countPage = 0;
        $regex = "/watch\?list=.+v=(.*?)(&|\")/i";
        $regexNext = '/(playlist\?list=.+&amp;ctoken=.+?)\"/i';
        $indexThumbnail = 0;
        while (isset($url)) {
            $response = RequestHelper::get($url, 1);
            $indexThumbnail = strpos($response, "no_thumbnail-", $indexThumbnail);
            while ($indexThumbnail !== FALSE) {
                $indexFirstA = strripos(substr($response, 0, $indexThumbnail), "<a");
                $indexLastA = strpos($response, "</a>", $indexFirstA);
                $resTmp = substr($response, $indexFirstA, $indexLastA - $indexFirstA);
                $arrResult = array_merge($arrResult, self::findVid($resTmp, $regex));
                $indexThumbnail = strpos($response, "no_thumbnail-", $indexThumbnail + 13);
            }
            if (++$countPage == $maxPage) {
                break;
            }
            $url = self::findNextLink($response, $regexNext, $isFirst);
            $isFirst = false;
        }
        return $arrResult;
    }

    public static function getVideoFromPlaylist($playlistId, $maxPage = 100) {
        $url = "https://m.youtube.com/playlist?list=" . $playlistId;
        $arrResult = array();
        $isFirst = true;
        $countPage = 0;
        $regex = "/watch\?list=.+v=(.*?)(&|\")/i";
        $regexNext = '/(playlist\?list=.+&amp;ctoken=.+?)\"/i';

        while (isset($url)) {
            error_log("count: $countPage");
            $response = RequestHelper::get($url, 1);
            $arrResult = array_merge($arrResult, self::findVid($response, $regex));
            if (++$countPage == $maxPage) {
                break;
            }
            $url = self::findNextLink($response, $regexNext, $isFirst);
            $isFirst = false;
        }
        return $arrResult;
    }

    public static function checkVideoInPlaylist($videoSearch, $playlistId) {
        $index = -1;
        $url = "https://m.youtube.com/playlist?list=" . $playlistId;
        $arrResult = array();
        $isFirst = true;
        $countPage = 0;
        $regex = "/watch\?list=.+v=(.*?)&/i";
        $regexNext = '/(playlist\?list=.+&amp;ctoken=.+?)\"/i';
        $indexBias = 0;
        while (isset($url)) {
            $response = RequestHelper::get($url, 1);
            $arrResult = self::findVid($response, $regex);
            $index = array_search($videoSearch, $arrResult);
            if ($index === FALSE) {
                $index = -1;
            } else {
                $index += $indexBias;
                break;
            }
            $indexBias += count($arrResult);
            $url = self::findNextLink($response, $regexNext, $isFirst);
            $isFirst = false;
        }
        return $index;
    }

    public static function searchVideo($keyword, $maxPage = 10, $uploaded_type = 1) {
        /*
         * uploaded
         * 1 '': all
         * 2 d: today
         * 3 w: this week
         * 4 m: this month
         */
        $uploaded = '';
        switch ($uploaded_type) {
            case 1:$uploaded = '';
                break;
            case 2:$uploaded = 'd';
                break;
            case 3:$uploaded = 'w';
                break;
            case 4:$uploaded = 'm';
                break;
            default :
                $uploaded = '';
                break;
        }
        $keyword = str_replace(" ", "+", $keyword);
        $uploaded = '';
        $url = "https://m.youtube.com/results?uploaded=$uploaded&sp=EgIQAVAU&q=" . $keyword . "&submit=Search";
        $regex = "/watch\?v=(.+?)&/i";
        $regexNext = "/(results\?.*action_continuation.+?)\"/i";
        $isFirst = true;
        $arrResult = array();
        $countPage = 0;
        while (isset($url)) {
            $response = RequestHelper::get($url, 1);
            $arrResult = array_merge($arrResult, self::findVid($response, $regex));
            if (++$countPage == $maxPage) {
                break;
            }
            $url = self::findNextLink($response, $regexNext, $isFirst);
            $isFirst = false;
        }
        return $arrResult;
    }

    public static function getPlaylistInfo($playlistId) {
        $patternTitle = "/pl-header-title.*?>(.+?)</is";
        $patternDetails = "/pl-header-details.+?<\/a>.*?<li>([\d\.]*).+?<\/li>.*?<li>([\d\.,]*).+?<\/li>/i";
        $response = RequestHelper::get("https://www.youtube.com/playlist?list=$playlistId", 0);
        $number_video = 0;
        $number_views = 0;
        $playlistIdNew = $playlistId;
        $channelName = "";
        $playlistName = "";
        $status = 2; //not exists
        if (strpos($response, $playlistId) !== FALSE) {
            $status = 0;
            preg_match($patternDetails, $response, $matchers);
            // preg_match($patternTitle, $response, $matcherTitles);
            if (count($matchers) > 1) {
                $status = 1;
                $tmp = str_replace(",", "", $matchers[1]);
                $tmp = str_replace(".", "", $tmp);
                if (is_numeric($tmp)) {
                    $number_video = intval($tmp);
                }
                $tmp = str_replace(",", "", $matchers[2]);
                $tmp = str_replace(".", "", $tmp);
                if (is_numeric($tmp)) {
                    $number_views = intval($tmp);
                }
            }
        }
        return array("status" => $status, "id" => $playlistId, "channelName" => $channelName, "playlistName" => $playlistName, "numberVideo" => $number_video, "numberView" => $number_views);
    }

    public static function getVideoInfoInPlayList($id) {
        $urlPage = "https://content.googleapis.com/youtube/v3/playlistItems?id=$id&part=snippet&key=" . Utils::getKeyYoutube();
        $data = file_get_contents($urlPage);
        $data = json_decode($data);
    }

    public static function getChannelInfo($channelId, $urlClient = null) {
        if (isset($urlClient)) {
            $response = RequestHelper::getWithIp("https://m.youtube.com/channel/$channelId/about", $urlClient, 1);
        } else {
            $response = RequestHelper::get("https://m.youtube.com/channel/$channelId/about", 1);
//            Log::info("Response:  ".$response);
        }
        $status = 0;
        $subscribes = 0;
        $views = 0;
        $channelName = '';
        $date = 0;
        $regex = "";
        if (strpos($response, "youtube") !== FALSE) {
            if (strpos($response, $channelId) === FALSE) {
                $status = 0;
            } else {
                $status = 1;
                preg_match("/([\d,]+)\s+views/", $response, $matches);
                if (isset($matches[1])) {
                    $views = $matches[1];
                    $views = str_replace(",", "", $views);
                }
                preg_match("/([\d,]+)\s+subscribers/", $response, $matches);
                if (isset($matches[1])) {
                    $subscribes = $matches[1];
                    $subscribes = str_replace(",", "", $subscribes);
                }
                preg_match("/Joined\s+(.+)/", $response, $matches);
                if (isset($matches[1])) {
                    if ($matches[1] != null && $matches[1] != '') {
                        $date = strtotime(trim($matches[1]));
                    }
                }
                try {
                    $firstIndex = strpos($response, "id=\"searchForm\"");
                    $secondIndex = strpos($response, "alt=\"avatar\"");
                    $channelNameContainText = substr($response, $firstIndex, $secondIndex - $firstIndex);
                    $channelNameContainText = str_replace("\r", "", $channelNameContainText);
                    $channelNameContainText = str_replace("\n", "", $channelNameContainText);
                    preg_match("/border-top:1px.+>(.+)<\/div>/", $channelNameContainText, $matches);

                    if (isset($matches[1])) {
                        $channelName = $matches[1];
                    }
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                }
            }
        } else {
            $status = 1;
        }
        return array("status" => $status, "subscribes" => $subscribes, "views" => $views, 'date' => $date, 'channelName' => $channelName);
    }

    public static function download($url, $extension) {
        $now = time();
        $tmpInt = rand(1, 100);
        $nameVid = "$now-$tmpInt.$extension";
        $filePath = PATH_DOWNLOAD . $nameVid;
        try {
            $commandDownload = "youtube-dl --ffmpeg-location /xcxcx/ffmpeg -o  $filePath '$url'";
            if (strpos($url, 'youtube') !== false) {
                $commandDownload = "youtube-dl --ffmpeg-location /xcxcx/ffmpeg -f mp4 -o  $filePath '$url'";
            }
            shell_exec($commandDownload);
            if (!Utils::checkFileOK($filePath)) {
                unlink($filePath);
                throw new Exception("Blocked Video");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return $filePath;
    }

    public static function processSourceFacebook($link) {

        $response = RequestHelper::get("https://graph.facebook.com/v2.12/" . $link . "/videos?limit=1000&access_token=EAAAAUaZA8jlABAPLUcwlqxF8Ho2oHqPO11KE07jApl6e4ZB1YO53p8T0GlM0XZCYlTzTqIrHZBYI5xUEypSLZAJPN4zeFepMSI5d2IUvOsSnmRdoWIgVqCf1hQTFWEXiawNYK7IHHjHMFzZBhpzcyNHZCKHZBWSxmPvvcA8EnvV1iAZDZD", 1);
        return $response;
    }

    public static function processSourceDailyMotion($source) {
//        1:user
//        2:video
        $type = 1;
        $id = "";
        if (strpos($source, "/video/") !== FALSE) {
            $type = 2;
        }
        if ($type == 1) {
            preg_match("/https:\/\/www.dailymotion.com\/(.+)/", $source, $matches);
            if (isset($matches[1])) {
                $id = $matches[1];
            }
        } else {
            preg_match("/https:\/\/www.dailymotion.com\/video\/(.+)/", $source, $matches);
            if (isset($matches[1])) {
                $id = $matches[1];
            }
        }
        return array("type" => $type, "id" => $id);
    }

    public static function getListVideoDailyMotion($userId, $page) {
        $response = RequestHelper::get("https://api.dailymotion.com/user/" . $userId . "/videos?fields=description,duration,id,tags,thumbnail_url,title,&page=" . $page . "&limit=100", 1);
        return $response;
    }

    public static function getVideoInfoDailyMotion($videoId) {
        $response = RequestHelper::get("https://api.dailymotion.com/video/" . $videoId . "?fields=description,duration,id,tags,thumbnail_url,title", 1);
        return $response;
    }

    public static function processLink($link) {
        //0: khac
        //1: playlist
        //2:channel
        //3:video
        $type = 0;
        $id = $link;
        if (strpos($link, "youtube.") !== FALSE || strpos($link, "youtu.be") !== FALSE) {
            $regexPLChannelId = "/(?:channel\/)(?:(.+?)&|(.+)$)/";
            preg_match($regexPLChannelId, $link, $matches);
            if (count($matches) > 1) {
                $type = 2;
                $id = $matches[count($matches) - 1];
            }
            if ($type == 0) {
                $regexPLChannelId = "/(?:list=)(?:(.+?)&|(.+)$)/";
                preg_match($regexPLChannelId, $link, $matches);
                if (count($matches) > 1) {
                    $type = 1;
                    $id = $matches[count($matches) - 1];
                }
            }
            if ($type == 0) {
                $regexVideoId = "/watch\?v=([a-z|A-Z|0-9|\.|\_-]+)/";
                preg_match($regexVideoId, $link, $matches);
                if (count($matches) > 0) {
                    $type = 3;
                    $id = $matches[count($matches) - 1];
                }
            }
            if ($type == 0) {
                $regexVideoId = "/youtu.be\/([a-z|A-Z|0-9|\.|\_-]+)/";
                preg_match($regexVideoId, $link, $matches);
                if (count($matches) > 0) {
                    $type = 3;
                    $id = $matches[count($matches) - 1];
                }
            }
            if ($type == 0) {
                $regexVideoId = "/v=(?:(.+?)&|(.+)$)/";
                preg_match($regexVideoId, $link, $matches);
                if (count($matches) > 0) {
                    $type = 3;
                    $id = $matches[count($matches) - 1];
                }
            }
            if ($type == 0) {
                $regexVideoId = "/shorts\/([a-zA-Z0-9-_]+)/";
                preg_match($regexVideoId, $link, $matches);
                Log::info(json_encode($matches));
                if (count($matches) > 0) {
                    $type = 3;
                    $id = $matches[count($matches) - 1];
                }
            }
            if ($type == 0) {
                $regexVideoId = "/youtu.be\/(.*)/";
                preg_match($regexVideoId, $link, $matches);
                $type = 3;
                if (count($matches) > 0) {
                    $id = $matches[count($matches) - 1];
                }
            }
        }
        return array("type" => $type, "data" => $id);
    }

    public static function getInfoVideoByYoutubeDl($videoUrl) {
        $info = '';
        try {
            $command = "youtube-dl --skip-download --print-json $videoUrl";
            $info = shell_exec($command);
        } catch (Exception $ex) {
            error_log('Error getInfo:' . $ex->getMessage());
        }
        return $info;
    }

    // <editor-fold defaultstate="collapsed" desc="CHANNEL">
    public static function getChannelInfoV2Old($channelId, $urlClient = null) {
        if (isset($urlClient)) {
            $response = RequestHelper::getWithIp("https://m.youtube.com/channel/$channelId/about", $urlClient, 1);
        } else {
            $response = RequestHelper::get("https://m.youtube.com/channel/$channelId/about", 1);
//            Log::info("Response:  " . $response);
        }
        $status = 0;
        $subscribes = 0;
        $views = 0;
        $channelName = '';
        $date = 0;
        preg_match("/\"contents\"/", $response, $matches);
        if (count($matches) > 0) {
            //video con song
            $status = 1;
            preg_match("/<div id=\"initial-data\"><!--(.*)--><\/div><script/", $response, $matches2);
            if (isset($matches2[1])) {
                $window = $matches2[1];
//                Log::info($window);
                preg_match("/\"subscriberCountText\":{\"runs\":\[{\"text\":\"([^\"]+)\"/", $window, $matchesSub);
//                Log::info(json_encode($matchesSub));
                if (isset($matchesSub[1])) {
                    $subscribes = Utils::shortNumber2Number(rtrim($matchesSub[1], 'subcribers'));
                }
                preg_match("/\"viewCountText\":{\"runs\":\[{\"text\":\"([^\"]+)\"/", $window, $matchesvView);
//                Log::info(json_encode($matchesvView));
                if (isset($matchesvView[1])) {
                    $views = str_replace(",", "", $matchesvView[1]);
                }
                preg_match("/\"joinedDateText\":{\"runs\":\[{\"text\":\"Joined\s+\"},{\"text\":\"([a-zA-Z0-9\s,]+)\"/", $window, $matchesJoin);
//                Log::info(json_encode($matchesJoin));
                if (isset($matchesJoin[1])) {
                    $date = strtotime(trim($matchesJoin[1]));
                }
                preg_match("/\"header\":{\"c4TabbedHeaderRenderer\":{\"channelId\":\"[a-zA-Z0-9\-\_\=\+]+\",\"title\":\"([^\"]+)\"/", $window, $matchesName);
//                Log::info(json_encode($matchesName));
                if (isset($matchesName[1])) {
                    $channelName = trim($matchesName[1]);
                }
            }
        }
        return array("status" => $status, "subscribes" => $subscribes, "views" => $views, 'date' => $date, 'channelName' => $channelName);
    }

    public static function getChannelInfoV2($channelId, $test = 0) {
//        if (isset($urlClient)) {
//            $response = RequestHelper::getWithIp("https://m.youtube.com/channel/$channelId/about", $urlClient, 1);
//        } else {
//            $response = RequestHelper::get("https://m.youtube.com/channel/$channelId/about", 1);
//        }
        $status = 0;
        $link = "https://m.youtube.com/channel/$channelId/about";
        if (Utils::containString($channelId, "@") || Utils::containString($channelId, "/c/")) {
            $link = "https://m.youtube.com/$channelId/about";
        }
        $response = ProxyHelper::get($link, 1);
//        Log::info("response $response");
//        dd($response);
        if ($response == null || $response == "") {
            //lỗi không scan được
            $status = 2;
        }
//        Log::info($response);
        $out = preg_replace_callback("(\\\\x([0-9a-f]{2}))i", function($a) {
            return chr(hexdec($a[1]));
        }, $response
        );
        $content = "";
        preg_match_all("/<script(.*?)<\/script>/", $out, $matches0);
        if (count($matches0) > 0) {
            $arrays = $matches0[1];
            foreach ($arrays as $data) {
                if (Utils::containString($data, "var ytInitialData =")) {
                    preg_match("/var\s+ytInitialData\s+=\s+'(.*?)';/", $out, $matches1);
                    $content = str_replace('\\\\', '\\', $matches1[1]);
                }
            }
        }

        $subscribers = 0;
        $views = 0;
        $channelName = '';
        $date = 0;
        $avatar = "";
        $banner = "";
        $channel = "";
        $videoCountText = 0;
        $handle = "";
        preg_match("/\"contents\"/", $content, $matches);
        if ($test == 1) {
            Utils::write("save/channel.txt", $content);
        }
        if (count($matches) > 0) {
            $data = json_decode($content);
            $status = 1;
//            preg_match("/\"subscriberCountText\":{\"runs\":\[{\"text\":\"([^\"]+)\"/", $content, $matchesSub);
//            if (isset($matchesSub[1])) {
//                $subscribes = Utils::shortNumber2Number(rtrim($matchesSub[1], 'subcribers'));
//            }
//            if (!empty($data->header->c4TabbedHeaderRenderer->subscriberCountText->runs[0]->text)) {
//                $subscribes = Utils::shortNumber2Number(rtrim($data->header->c4TabbedHeaderRenderer->subscriberCountText->runs[0]->text, 'subcribers'));
//            }
//            preg_match("/\"viewCountText\":{\"runs\":\[{\"text\":\"([^\"]+)\"/", $content, $matchesvView);
//            if (isset($matchesvView[1])) {
//                $views = Utils::getNumberFromText($matchesvView[1]);
//            }
            if (!empty($data->onResponseReceivedEndpoints[0]->showEngagementPanelEndpoint
                            ->engagementPanel->engagementPanelSectionListRenderer
                            ->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->aboutChannelRenderer->metadata
                            ->aboutChannelViewModel)) {
                $about = $data->onResponseReceivedEndpoints[0]
                        ->showEngagementPanelEndpoint->engagementPanel->engagementPanelSectionListRenderer
                        ->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->aboutChannelRenderer->metadata
                        ->aboutChannelViewModel;
                if (!empty($about->viewCountText)) {
                    $views = Utils::getNumberFromText($about->viewCountText);
                }
                if (!empty($about->subscriberCountText)) {
                    $subscribers = Utils::getNumberFromText($about->subscriberCountText);
                }
                if (!empty($about->channelId)) {
                    $channel = $about->channelId;
                }
                if (!empty($about->videoCountText)) {
                    $videoCountText = Utils::getNumberFromText($about->videoCountText);
                }
                if (!empty($about->joinedDateText->content)) {
                    $tmpDate = $about->joinedDateText->content;
                    $date = strtotime(trim(str_replace("Joined", "", $tmpDate)));
                }
            }

            if (!empty($data->metadata->channelMetadataRenderer->title)) {
                $channelName = $data->metadata->channelMetadataRenderer->title;
            }
            if (!empty($data->metadata->channelMetadataRenderer->avatar->thumbnails[0]->url)) {
                $avatar = $data->metadata->channelMetadataRenderer->avatar->thumbnails[0]->url;
            }
            if (!empty($data->header->pageHeaderRenderer->content->pageHeaderViewModel->banner->imageBannerViewModel->image->sources)) {
                $count = count($data->header->pageHeaderRenderer->content->pageHeaderViewModel->banner->imageBannerViewModel->image->sources);
                $banner = $data->header->pageHeaderRenderer->content->pageHeaderViewModel->banner->imageBannerViewModel->image->sources[$count - 1]->url;
            }
            if (!empty($data->header->pageHeaderRenderer->content->pageHeaderViewModel->metadata->contentMetadataViewModel->metadataRows[0]->metadataParts[0]->text->content)) {
                $handle = $data->header->pageHeaderRenderer->content->pageHeaderViewModel->metadata->contentMetadataViewModel->metadataRows[0]->metadataParts[0]->text->content;
            }

//            if (!empty($data->onResponseReceivedEndpoints[0]->showEngagementPanelEndpoint
//                            ->engagementPanel->engagementPanelSectionListRenderer
//                            ->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->aboutChannelRenderer->metadata
//                            ->aboutChannelViewModel->viewCountText)) {
//                $views = Utils::getNumberFromText($data->onResponseReceivedEndpoints[0]
//                                ->showEngagementPanelEndpoint->engagementPanel->engagementPanelSectionListRenderer
//                                ->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->aboutChannelRenderer->metadata
//                                ->aboutChannelViewModel->viewCountText);
//            }
//            if (!empty($data->onResponseReceivedEndpoints[0]->showEngagementPanelEndpoint
//                            ->engagementPanel->engagementPanelSectionListRenderer
//                            ->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->aboutChannelRenderer->metadata
//                            ->aboutChannelViewModel->subscriberCountText)) {
//                $subscribes = Utils::getNumberFromText($data->onResponseReceivedEndpoints[0]
//                                ->showEngagementPanelEndpoint->engagementPanel->engagementPanelSectionListRenderer
//                                ->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->aboutChannelRenderer->metadata
//                                ->aboutChannelViewModel->subscriberCountText);
//            }
//            preg_match("/\"joinedDateText\":{\"runs\":\[{\"text\":\"Joined\s+\"},{\"text\":\"([a-zA-Z0-9\s,]+)\"/", $content, $matchesJoin);
//            if (isset($matchesJoin[1])) {
//                $date = strtotime(trim($matchesJoin[1]));
//            }
//            preg_match("/\"header\":{\"c4TabbedHeaderRenderer\":{\"channelId\":\"[a-zA-Z0-9\-\_\=\+]+\",\"title\":\"([^\"]+)\"/", $content, $matchesName);
//            if (isset($matchesName[1])) {
//                $channelName = trim($matchesName[1]);
//            }
//            if (!empty($data->header->c4TabbedHeaderRenderer->avatar->thumbnails)) {
//                $count = count($data->header->c4TabbedHeaderRenderer->avatar->thumbnails);
//                $avatar = $data->header->c4TabbedHeaderRenderer->avatar->thumbnails[$count - 1]->url;
//            }
//            if (!empty($data->header->c4TabbedHeaderRenderer->tvBanner->thumbnails)) {
//                $count = count($data->header->c4TabbedHeaderRenderer->tvBanner->thumbnails);
//                $banner = $data->header->c4TabbedHeaderRenderer->tvBanner->thumbnails[$count - 1]->url;
//            }
//            if (!empty($data->header->c4TabbedHeaderRenderer->channelId)) {
//                $channelId = $data->header->c4TabbedHeaderRenderer->channelId;
//            }
        }
        return array("status" => $status,
            "subscribers" => $subscribers,
            "views" => $views,
            'date' => $date,
            'handle' => $handle,
            'channelId' => $channel,
            'channelName' => $channelName,
            "avatar" => $avatar,
            "banner" => $banner
        );
    }

    public static function getChanelByHandle($link, $test = 0) {
        $response = ProxyHelper::get($link, 2);
        $token = null;
        $out = preg_replace_callback("(\\\\x([0-9a-f]{2}))i", function($a) {
            return chr(hexdec($a[1]));
        }, $response
        );

        $content = "";
        preg_match_all("/<script(.*?)<\/script>/", $out, $matches0);
        if (count($matches0) > 0) {
            $arrays = $matches0[1];
            foreach ($arrays as $data) {
                if (Utils::containString($data, "var ytInitialData =")) {
                    $data .= 'xxxyyyzzz';
                    preg_match("/var\s+ytInitialData\s+=\s+(.*?);xxxyyyzzz/", $data, $matches1);
                    $content = str_replace('', '\\', $matches1[1]);
                }
            }
        } else {
            error_log("getChanelByHandle $link not matches");
            error_log($content);
        }
        preg_match("/\"contents\"/", $content, $matches);
        if ($test == 1) {
            Utils::write("save/getChanelByHandle.txt", $content);
        }
        if (count($matches) > 0) {
            $token = null;
            $datas = json_decode($content);
        }
    }

    // </editor-fold>
    //// <editor-fold defaultstate="collapsed" desc="PLAYLIST">
    public static function getPlaylistInfoV3Old($playlistId) {
        //status =2 chưa check dc
        //status =1 playlist còn sống
        //status =0 playlist đã chết
        $response = RequestHelper::get("https://m.youtube.com/playlist?list=$playlistId", 1);
        if ($response == null || $response == "") {
            Log::info('PID[' . getmypid() . ']' . "https://m.youtube.com/playlist?list=$playlistId resonse null");
        }
        $number_video = 0;
        $number_views = 0;
        $channelName = "";
        $playlistName = "";
        $status = 0; //not exists
        preg_match("/<div id=\"initial-data\"><!--(.*)--><\/div><script/", $response, $matches2);
        if (isset($matches2[1])) {
            $window = $matches2[1];
//            Log::info($window);
            $datas = json_decode($window);
            if (!empty($datas->contents)) {
                $status = 1;
                $playlistId = $datas->header->playlistHeaderRenderer->playlistId;
                $playlistName = $datas->header->playlistHeaderRenderer->title->runs[0]->text;
                $channelName = $datas->header->playlistHeaderRenderer->ownerText->runs[0]->text;
                $viewstr = $datas->header->playlistHeaderRenderer->viewCountText->runs[0]->text;
                $number_views = Utils::getNumberFromText(trim($viewstr));
                $videostr = $datas->header->playlistHeaderRenderer->numVideosText->runs[0]->text;
                $number_video = Utils::getNumberFromText(trim($videostr));
            }
        } else {
            $status = 2;
        }


//        Log::info('PID[' . getmypid() . ']' . "$playlistId $status");
        return array("status" => $status, "id" => $playlistId, "channelName" => $channelName, "playlistName" => $playlistName, "numberVideo" => $number_video, "numberView" => $number_views);
    }

    public static function getPlaylistInfoV3($playlistId) {
        //status =2 chưa check dc
        //status =1 playlist còn sống
        //status =0 playlist đã chết
        $response = ProxyHelper::get("https://m.youtube.com/playlist?list=$playlistId", 1);
        if ($response == null || $response == "") {
            Log::info('PID[' . getmypid() . ']' . "https://m.youtube.com/playlist?list=$playlistId resonse null");
        }
        $out = preg_replace_callback("(\\\\x([0-9a-f]{2}))i", function($a) {
            return chr(hexdec($a[1]));
        }, $response
        );
        $content = "";
        preg_match_all("/<script(.*?)<\/script>/", $out, $matches0);
        if (count($matches0) > 0) {
            $arrays = $matches0[1];
            foreach ($arrays as $data) {
                if (Utils::containString($data, "var ytInitialData =")) {
                    preg_match("/var\s+ytInitialData\s+=\s+'(.*?)';/", $out, $matches1);
                    $content = str_replace('\\\\', '\\', $matches1[1]);
                }
            }
        }

        $number_video = 0;
        $number_views = 0;
        $channelName = "";
        $playlistName = "";
        $status = 0; //not exists
        $datas = json_decode($content);
        if (!empty($datas->contents)) {
            $status = 1;
            if (!empty($datas->header->playlistHeaderRenderer->playlistId)) {
                $playlistId = $datas->header->playlistHeaderRenderer->playlistId;
            }
            if (!empty($datas->header->playlistHeaderRenderer->title->runs[0]->text)) {

                $playlistName = $datas->header->playlistHeaderRenderer->title->runs[0]->text;
            }
            if (!empty($datas->header->playlistHeaderRenderer->ownerText->runs[0]->text)) {

                $channelName = $datas->header->playlistHeaderRenderer->ownerText->runs[0]->text;
            }
            if (!empty($datas->header->playlistHeaderRenderer->viewCountText->runs[0]->text)) {
                $viewstr = $datas->header->playlistHeaderRenderer->viewCountText->runs[0]->text;
                $number_views = Utils::getNumberFromText(trim($viewstr));
            }
            if (!empty($datas->header->playlistHeaderRenderer->numVideosText->runs[0]->text)) {
                $videostr = $datas->header->playlistHeaderRenderer->numVideosText->runs[0]->text;
                $number_video = Utils::getNumberFromText(trim($videostr));
            }
        } else {
            $status = 2;
        }



//        Log::info('PID[' . getmypid() . ']' . "$playlistId $status");
        return array("status" => $status, "id" => $playlistId, "channelName" => $channelName, "playlistName" => $playlistName, "numberVideo" => $number_video, "numberView" => $number_views);
    }

    //lấy danh sách video trong playlist dùng proxy
    public static function getPlaylistOld($playlistId, $numberGet, $pageToken = "", $ua = null, $retries = 3) {

        $list_video_id = array();
        $list_video_name = array();
        $list_date = array();
//        $proxy_port = 333;
//        $proxy_ip = "usa.rotating.proxyrack.net";
//        $loginpassw = "dunndealpr:0ddf2c-02b7b2-7c80d6-6958a3-468cdb";
//        $proxy_ip = "zproxy.lum-superproxy.io";
//        $proxy_port = 22225;
//        $loginpassw = "lum-customer-c_308cb727-zone-static-ip-158.46.169.208:gmpqodpltr2c";

        $proxy = Proxy::inRandomOrder()->first();
        $proxy->update_time = gmdate("d-m-Y H:i:s", time() + 7 * 3600);
        $proxy->count = $proxy->count + 1;
        $proxy->save();
        $proxy_ip = $proxy->ip;
        $proxy_port = $proxy->port;
        $loginpassw = "$proxy->user:$proxy->pass";

        $arrUserAgent = ["Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36", "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36", "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:54.0) Gecko/20100101 Firefox/73.0", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:61.0) Gecko/20100101 Firefox/73.0", "Mozilla/5.0 (X11; Linux i586; rv:31.0) Gecko/20100101 Firefox/73.0", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 Safari/605.1.15", "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36 Edg/80.0.361.50", "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36 OPR/66.0.3515.95", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36 OPR/66.0.3515.95", "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36 OPR/66.0.3515.95", "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36 Vivaldi/2.10", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36 Vivaldi/2.10", "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36 Vivaldi/2.10", "Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 YaBrowser/19.7.3.172 Yowser/2.5 Safari/537.36", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 YaBrowser/19.6.0.1583 Yowser/2.5 Safari/537.36"];
        if (!isset($ua)) {
            $ua = $arrUserAgent[rand(0, count($arrUserAgent) - 1)];
        }
        $curl = null;
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://content.googleapis.com/youtube/v3/playlistItems?maxResults=$numberGet&"
                . "part=snippet&pageToken=$pageToken&playlistId=$playlistId&key=AIzaSyAa8yy0GdcGPHdtD083HiGGx_S0vMPScDM",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "x-origin: https://explorer.apis.google.com",
                    "user-agent: " . $ua,
                    "x-referer: https://explorer.apis.google.com",
                    "accept: */*",
                    "referer: https://explorer.apis.google.com",
                    "Host: content.googleapis.com",
                    "Accept-Encoding: gzip, deflate",
                    "Connection: keep-alive",
                    "cache-control: no-cache"
                ),
            ));
            curl_setopt($curl, CURLOPT_PROXYPORT, $proxy_port);
            curl_setopt($curl, CURLOPT_PROXYTYPE, 'HTTPS');
            curl_setopt($curl, CURLOPT_PROXY, $proxy_ip);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $loginpassw);
            $response = curl_exec($curl);
//            error_log($response);
            if (!isset($response) || $response == "") {
                if ($retries < 1) {
                    echo("Error getPlaylistRD:--- ");
                } else {
                    echo("Retry getPlaylistRD:--- ");
                    return self::getPlaylist($playlistId, $pageToken, $ua, --$retries);
                }
            }
            curl_close($curl);
            $datas = json_decode($response);
            if (!empty($datas->items)) {
                if (isset($datas->nextPageToken)) {
                    $pageToken = $datas->nextPageToken;
                } else {
                    $pageToken = null;
                }
                foreach ($datas->items as $item) {
                    array_push($list_video_id, $item->snippet->resourceId->videoId);
                    array_push($list_video_name, $item->snippet->title);
                    array_push($list_date, str_replace("-", "", substr($item->snippet->publishedAt, 0, 10)));
                }
            }
        } catch (Exception $e) {
            try {
                if (isset($curl)) {
                    curl_close($curl);
                }
            } catch (Exception $exc) {
                
            }
            if ($retries < 1) {
                echo("Error getPlaylistRD: " . $e->getMessage());
            } else {
                echo("Retry getPlaylistRD: " . $e->getMessage());
                return self::getPlaylist($playlistId, $pageToken, $ua, --$retries);
            }
        }
        return array("list_video_id" => $list_video_id, "list_video_name" => $list_video_name, "list_date" => $list_date);
    }

    //lấy danh sach video trong playlist không dùng proxy (numberGet=0 get all)
    public static function getPlaylist($playlistId, $get = 50, $pageToken = "", $retries = 3) {
//        error_log("getPlaylist");
        $list_video_id = array();
        $list_video_name = array();
        $list_date = array();
        $curl = null;
        $numberGet = 50;
        try {
            if ($get == 0) {
                $numberGet = 50;
            } else {
                $numberGet = $get;
            }
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://content.googleapis.com/youtube/v3/playlistItems?maxResults=$numberGet&"
                . "part=snippet&pageToken=$pageToken&playlistId=$playlistId&key=AIzaSyAbhCFK1TTj8ONgUUuCp8AO_Gv3nBnD0Tc",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET"
            ));
            $response = curl_exec($curl);

            if (!isset($response) || $response == "") {
                if ($retries < 1) {
                    echo("Error getPlaylist:--- ");
                } else {
                    echo("Retry getPlaylist:--- ");
                    return self::getPlaylist($playlistId, $pageToken, --$retries);
                }
            }
            curl_close($curl);
            $datas = json_decode($response);
            if (!empty($datas->items)) {
                if (isset($datas->nextPageToken)) {
                    $pageToken = $datas->nextPageToken;
                } else {
                    $pageToken = null;
                }
                foreach ($datas->items as $item) {
                    array_push($list_video_id, $item->snippet->resourceId->videoId);
                    array_push($list_video_name, $item->snippet->title);
                    array_push($list_date, str_replace("-", "", substr($item->snippet->publishedAt, 0, 10)));
                }
            }
            if ($get != 0) {
                if (count($list_video_id) < $get) {
                    $more = $get - count($list_video_id);
                    if (isset($pageToken) && !empty($datas->items)) {
                        $arrTmp = self::getPlaylist($playlistId, $more, $pageToken);
                        $list_video_id = array_merge($list_video_id, $arrTmp["list_video_id"]);
                        $list_video_name = array_merge($list_video_name, $arrTmp["list_video_name"]);
                        $list_date = array_merge($list_date, $arrTmp["list_date"]);
//                $list_video = array_merge($list_video, $arrTmp["list_video"]);
                    }
                }
            } else {
                if (isset($pageToken) && !empty($datas->items)) {
                    $arrTmp = self::getPlaylist($playlistId, 0, $pageToken);
                    $list_video_id = array_merge($list_video_id, $arrTmp["list_video_id"]);
                    $list_video_name = array_merge($list_video_name, $arrTmp["list_video_name"]);
                    $list_date = array_merge($list_date, $arrTmp["list_date"]);
//                $list_video = array_merge($list_video, $arrTmp["list_video"]);
                }
            }
        } catch (Exception $e) {
            try {
                if (isset($curl)) {
                    curl_close($curl);
                }
            } catch (Exception $exc) {
                
            }
            if ($retries < 1) {
                echo("Error getPlaylist: " . $e->getMessage());
            } else {
                echo("Retry getPlaylist: " . $e->getMessage());
                return self::getPlaylist($playlistId, $pageToken, --$retries);
            }
        }
        return array("list_video_id" => $list_video_id, "list_video_name" => $list_video_name, "list_date" => $list_date);
    }

    public static function getListPLaylistWakeupHappy($channelId) {
        $response = ProxyHelper::get("https://www.youtube.com/channel/$channelId/playlists", 2);

        $out = preg_replace_callback("(\\\\x([0-9a-f]{2}))i", function($a) {
            return chr(hexdec($a[1]));
        }, $response
        );

        $content = "";
        preg_match_all("/<script(.*?)<\/script>/", $out, $matches0);
        if (count($matches0) > 0) {
            $arrays = $matches0[1];
            foreach ($arrays as $data) {
                if (Utils::containString($data, "var ytInitialData =")) {
                    preg_match("/var\s+ytInitialData\s+=\s+(.*?);/", $out, $matches1);
                    $content = str_replace('\\\\', '\\', $matches1[1]);
                }
            }
        }
//        Utils::write("save/$channelId-pll.txt", $content);
        $datas = json_decode($content);
        $playlists = [];
        if (!empty($datas->contents->twoColumnBrowseResultsRenderer->tabs)) {


            $tabs = $datas->contents->twoColumnBrowseResultsRenderer->tabs;
            if (!empty($tabs)) {
                foreach ($tabs as $tab) {
                    if (!empty($tab->tabRenderer->title) && $tab->tabRenderer->title == 'Playlists') {
                        if (!empty($tab->tabRenderer->content->sectionListRenderer->contents)) {
                            $playlistsContents = $tab->tabRenderer->content->sectionListRenderer->contents;
//                            Utils::write('playlistsContents.txt', json_encode($playlistsContents));
                            $createdPlaylists = [];
                            foreach ($playlistsContents as $playlistContent) {
                                if (!empty($playlistContent->itemSectionRenderer->contents[0]->shelfRenderer->title->runs[0]->text) && $playlistContent->itemSectionRenderer->contents[0]->shelfRenderer->title->runs[0]->text == 'Created playlists') {
                                    if (!empty($playlistContent->itemSectionRenderer->contents[0]->shelfRenderer->content->horizontalListRenderer->items)) {
                                        $createdPlaylists = $playlistContent->itemSectionRenderer->contents[0]->shelfRenderer->content->horizontalListRenderer->items;
                                    }
                                } else if (!empty($playlistContent->itemSectionRenderer->contents[0]->gridRenderer->items)) {
                                    $createdPlaylists = $playlistContent->itemSectionRenderer->contents[0]->gridRenderer->items;
                                }
//                            Log::info("Created playlists:" . count($createdPlaylists));
                                foreach ($createdPlaylists as $createdPlaylist) {
                                    if (!empty($createdPlaylist->gridPlaylistRenderer->playlistId)) {
                                        $tmp = [];
                                        $tmp["playlistId"] = $createdPlaylist->gridPlaylistRenderer->playlistId;
                                        $tmp["playlistName"] = $createdPlaylist->gridPlaylistRenderer->title->runs[0]->text;
                                        $tmp["videoCountText"] = $createdPlaylist->gridPlaylistRenderer->videoCountText->runs[0]->text;
                                        $tmp["publishedTimeText"] = !empty($createdPlaylist->gridPlaylistRenderer->publishedTimeText->simpleText) ? $createdPlaylist->gridPlaylistRenderer->publishedTimeText->simpleText : "";
                                        $tmp["thumbnail"] = $createdPlaylist->gridPlaylistRenderer->thumbnail->thumbnails[count($createdPlaylist->gridPlaylistRenderer->thumbnail->thumbnails) - 1]->url;
                                        $playlists[] = (object) $tmp;
                                    }
//                                $playlistObject = (object) [
//                                            "playlistId" => !empty($createdPlaylist->gridPlaylistRenderer->playlistId)?$createdPlaylist->gridPlaylistRenderer->playlistId:"",
//                                            "playlistName" => $createdPlaylist->gridPlaylistRenderer->title->runs[0]->text,
//                                            "videoCountText" => $createdPlaylist->gridPlaylistRenderer->videoCountText->runs[0]->text,
//                                            "publishedTimeText" => !empty($createdPlaylist->gridPlaylistRenderer->publishedTimeText->simpleText) ? $createdPlaylist->gridPlaylistRenderer->publishedTimeText->simpleText : "",
//                                            "thumbnail" => $createdPlaylist->gridPlaylistRenderer->thumbnail->thumbnails[count($createdPlaylist->gridPlaylistRenderer->thumbnail->thumbnails) - 1]->url,
//                                ];
//                                $playlists[] = $playlistObject;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $playlists;
    }

    public static function getPlaylistRD($video_id) {

        $list_video_id = array();
        $list_video_name = array();
        $youtube = "https://m.youtube.com";
        $response = ProxyHelper::get("$youtube/watch?v=$video_id&list=RDAMVM$video_id", 1);
        $out = preg_replace_callback("(\\\\x([0-9a-f]{2}))i", function($a) {
            return chr(hexdec($a[1]));
        }, $response
        );
        $content = "";
        preg_match_all("/<script(.*?)<\/script>/", $out, $matches0);
        if (count($matches0) > 0) {
            $arrays = $matches0[1];
            foreach ($arrays as $data) {
                if (Utils::containString($data, "var ytInitialData =")) {
                    preg_match("/var\s+ytInitialData\s+=\s+'(.*?)';/", $out, $matches1);
                    $content = str_replace('\\\\', '\\', $matches1[1]);
                }
            }
        }
//        Utils::write("rdresponse$u_type.txt", $content);
        $datas = json_decode($content);
        if (!empty($datas->contents->singleColumnWatchNextResults->playlist)) {
            $temps = $datas->contents->singleColumnWatchNextResults->playlist->playlist->contents;
            if (count($temps) > 0) {
                foreach ($temps as $temp) {
                    array_push($list_video_id, $temp->playlistPanelVideoRenderer->videoId);
                    array_push($list_video_name, $temp->playlistPanelVideoRenderer->title->runs[0]->text);
                }
            }
        }
        return array("list_video_id" => $list_video_id, "list_video_name" => $list_video_name);
    }

    public static function getPlaylistHtml($playlist_id, $is_get_title = 0, $limit = 0) {
        $list_video_id = array();
        $list_video_name = array();

        $response = ProxyHelper::get("https://www.youtube.com/playlist?list=$playlist_id", 2);
        $out = preg_replace_callback("(\\\\x([0-9a-f]{2}))i", function($a) {
            return chr(hexdec($a[1]));
        }, $response
        );

        $content = "";
        preg_match_all("/<script(.*?)<\/script>/", $out, $matches0);
        if (count($matches0) > 0) {
            $arrays = $matches0[1];
            foreach ($arrays as $data) {
                if (Utils::containString($data, "var ytInitialData =")) {
                    preg_match("/var\s+ytInitialData\s+=\s+(.*?);/", $out, $matches1);
                    $content = str_replace('\\\\', '\\', $matches1[1]);
                }
            }
        }
//        Utils::write("content.txt", $content);
        $datas = json_decode($content);
        if (!empty($datas->contents->twoColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->playlistVideoListRenderer->contents)) {
            $list_playlist = $datas->contents->twoColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->playlistVideoListRenderer->contents;
//            Log::info("********************" . count($list_playlist));
            if (count($list_playlist) > 0) {
                foreach ($list_playlist as $video) {
                    $title = !empty($video->playlistVideoRenderer->title->simpleText) ? $video->playlistVideoRenderer->title->simpleText : "";
                    if ($title == "") {
                        $title = !empty($video->playlistVideoRenderer->title->runs[0]->text) ? $video->playlistVideoRenderer->title->runs[0]->text : "";
                    }
                    if ($title != "") {
                        array_push($list_video_id, $video->playlistVideoRenderer->videoId);
                        array_push($list_video_name, $title);
                    }
                    if ($limit != 0) {
                        if (count($list_video_id) >= $limit) {
                            break;
                        }
                    }
                }
            }
        }
        if ($is_get_title == 0) {
            return $list_video_id;
        }
        return array("list_video_id" => $list_video_id, "list_video_name" => $list_video_name);
    }

    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="VIDEO">
    //truongpv ham lay thong tin video v2 (dùng mobile)
    public static function getVideoInfoV2($videoId, $urlClient = null) {
        if (isset($urlClient)) {
            $response = RequestHelper::getWithIp("https://youtube.com/watch?v=$videoId", $urlClient, 1);
        } else {
            $response = RequestHelper::getUrl("https://youtube.com/watch?v=$videoId", 0);
        }
        $status = 0;
        $videoLength = 0;
        $title = "";
        $like = 0;
        $dislike = 0;
        $views = 0;
        $publishDateText = "";
        $publishDate = 0;
        $channelId = "";
        $channelName = "";
        $songnames = [];
        $artists = [];
        $albums = [];
        $licenses = [];
        $writers = [];
        $comment = 0;
        $countSong = 0;
        $out = preg_replace_callback("(\\\\x([0-9a-f]{2}))i", function($a) {
            return chr(hexdec($a[1]));
        }, $response
        );
        Utils::write("response.txt", $response);
        $content = "";
        preg_match_all("/<script(.*?)<\/script>/", $out, $matches0);
        if (count($matches0) > 0) {
            $arrays = $matches0[1];
            foreach ($arrays as $data) {
                if (Utils::containString($data, "var ytInitialData =")) {
                    $data .= 'xxxyyyzzz';
                    preg_match("/var\s+ytInitialData\s+=\s+'(.*?)';xxxyyyzzz/", $data, $matches1);
                    $content = str_replace('\\\\', '\\', $matches1[1]);
                }
            }
        }
//        Log::info($content);
//        Utils::write("video.txt", $content);
        preg_match("/\"contents\"/", $content, $matches);
        if (count($matches) > 0) {
            $status = 1;
            $datas = json_decode($content);
            if (!empty($datas->contents->singleColumnWatchNextResults->results->results->contents)) {
                $temps = $datas->contents->singleColumnWatchNextResults->results->results->contents;
                foreach ($temps as $temp) {
                    if (!empty($temp->itemSectionRenderer->contents[0]->slimVideoMetadataRenderer)) {
                        $info = $temp->itemSectionRenderer->contents[0]->slimVideoMetadataRenderer;
                    }
                    if (!empty($temp->commentSectionRenderer->header->commentSectionHeaderRenderer->countText->runs[1]->text)) {
                        $comment_tmp = trim($temp->commentSectionRenderer->header->commentSectionHeaderRenderer->countText->runs[1]->text);
                        preg_match('/\d+/', $comment_tmp, $matches_tmp);
                        if (count($matches_tmp) > 0) {
                            $comment = intval($matches_tmp[0]);
                        }
                    }
                }
                if (!empty($info->title->runs[0]->text)) {
                    $title = $info->title->runs[0]->text;
                }
                if (!empty($info->expandedSubtitle->runs[0]->text)) {
                    $views_tmp = $info->expandedSubtitle->runs[0]->text;
                    $views = Utils::getNumberFromText(trim($views_tmp));
                }
                if (!empty($info->dateText->runs[0]->text)) {
                    $publishDateText = $info->dateText->runs[0]->text;
                    $publishDateText = trim(str_replace("Published on", "", str_replace("Premiered", "", $publishDateText)));
                    $publishDate = strtotime($publishDateText);
                }

                if (!empty($info->buttons[0]->slimMetadataToggleButtonRenderer->button->toggleButtonRenderer->defaultText->runs[0]->text)) {
                    $like = $info->buttons[0]->slimMetadataToggleButtonRenderer->button->toggleButtonRenderer->defaultText->runs[0]->text;
                    $like = Utils::shortNumber2Number(trim($like));
                }

                if (!empty($info->buttons[1]->slimMetadataToggleButtonRenderer->button->toggleButtonRenderer->defaultText->runs[0]->text)) {
                    $dislike = $info->buttons[1]->slimMetadataToggleButtonRenderer->button->toggleButtonRenderer->defaultText->runs[0]->text;
                    $dislike = Utils::shortNumber2Number(trim($dislike));
                }
                if (!empty($info->owner->slimOwnerRenderer->channelName)) {
                    $channelName = $info->owner->slimOwnerRenderer->channelName;
                }
                if (!empty($info->owner->slimOwnerRenderer->channelUrl)) {
                    $channelId = $info->owner->slimOwnerRenderer->channelUrl;
                    $channelId = Utils::parseChannelId($channelId);
                }

                if (!empty($info->metadataRowContainer->metadataRowContainerRenderer->rows)) {
                    $info_song = $info->metadataRowContainer->metadataRowContainerRenderer->rows;
                    for ($i = 0; $i < count($info_song); $i++) {
                        if (!empty($info_song[$i]->metadataRowRenderer->title->runs[0]->text)) {
                            if (strtolower($info_song[$i]->metadataRowRenderer->title->runs[0]->text) == "song") {
                                if (!empty($info_song[$i]->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                    $songnames[] = $info_song[$i]->metadataRowRenderer->contents[0]->runs[0]->text;
                                    $countSong++;
                                }
                            }
                            if (strtolower($info_song[$i]->metadataRowRenderer->title->runs[0]->text) == "artist") {
                                if (!empty($info_song[$i]->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                    $artists[] = $info_song[$i]->metadataRowRenderer->contents[0]->runs[0]->text;
                                }
                            }
                            if (strtolower($info_song[$i]->metadataRowRenderer->title->runs[0]->text) == "album") {
                                if (!empty($info_song[$i]->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                    $albums[] = $info_song[$i]->metadataRowRenderer->contents[0]->runs[0]->text;
                                }
                            }
                            if (strtolower($info_song[$i]->metadataRowRenderer->title->runs[0]->text) == "licensed to youtube by") {
                                if (!empty($info_song[$i]->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                    $licenses[] = $info_song[$i]->metadataRowRenderer->contents[0]->runs[0]->text;
                                }
                            }
                            if (strtolower($info_song[$i]->metadataRowRenderer->title->runs[0]->text) == "writers") {
                                if (!empty($info_song[$i]->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                    $writers[] = $info_song[$i]->metadataRowRenderer->contents[0]->runs[0]->text;
                                }
                            }
                        }
                    }
                }
            }
//                Log::info(array("status" => $status, "title" => $title, "length" => $videoLength, "like" => $like, "dislike" => $dislike, "view" => $views, "publish_date" => $publishDate, "channelName" => $channelName));
        }
        return array("status" => $status, "title" => $title, "length" => $videoLength,
            "like" => $like, "dislike" => $dislike, "view" => $views,
            "publish_date" => $publishDate, "channelId" => $channelId, "channelName" => $channelName,
            "song_name" => json_encode($songnames), "artists" => json_encode($artists), "album" => json_encode($albums),
            "license" => json_encode($licenses), "writers" => json_encode($writers), "comment" => $comment, "countSong" => $countSong);
    }

    public static function getVideoInfoHtmlDesktop($videoId, $test = 0) {
        $response = ProxyHelper::get("https://youtube.com/watch?v=$videoId", 2);
        $status = 0;
        $videoLength = 0;
        $title = "";
        $like = 0;
        $dislike = 0;
        $views = 0;
        $publishDateText = "";
        $publishDate = 0;
        $channelId = "";
        $channelName = "";
        $songnames = [];
        $artists = [];
        $albums = [];
        $licenses = [];
        $writers = [];
        $comment = 0;
        $countSong = 0;
        $nextVideo = "";
        $channel_sub = 0;
        $description = "";

        $out = preg_replace_callback("(\\\\x([0-9a-f]{2}))i", function($a) {
            return chr(hexdec($a[1]));
        }, $response
        );
        Utils::write("save/video_full.txt", $response);
        preg_match("/<meta itemprop=\"duration\" content=\"([^\"]+)/", $response, $mat);
        if (count($mat) > 1) {
            Log::info(json_encode($mat));
            $tmpDur = $mat[1];
            $tmpDur = str_replace("PT", "", $tmpDur);
            $tmpDur = str_replace("S", "", $tmpDur);
            $arr = explode("M", $tmpDur);
            if (count($arr) == 2) {
                $videoLength = $arr[0] * 60 + $arr[1];
            }
        }
        $content = "";
        preg_match_all("/<script(.*?)<\/script>/", $out, $matches0);
        if (count($matches0) > 0) {
            $arrays = $matches0[1];
            foreach ($arrays as $data) {
                if (Utils::containString($data, "var ytInitialData =")) {
                    $data .= 'xxxyyyzzz';
                    preg_match("/var\s+ytInitialData\s+=\s+(.*?);xxxyyyzzz/", $data, $matches1);
                    $content = str_replace('', '\\', $matches1[1]);
                }
            }
        } else {
            error_log("getVideoInfoHtmlDesktop $videoId not matches0");
        }
        preg_match("/\"contents\"/", $content, $matches);
//        $content = str_replace("/\\", "/\\\\", $content);
        if ($test == 1) {
            Utils::write("save/video.txt", $content);
        }
        if (count($matches) > 0) {
            $datas = json_decode($content);
            if (!empty($datas->contents->twoColumnWatchNextResults->results->results->contents)) {
//                $status = 1;
                $temps = $datas->contents->twoColumnWatchNextResults->results->results->contents;
                foreach ($temps as $temp) {
                    if (!empty($temp->videoPrimaryInfoRenderer)) {
                        $info = $temp->videoPrimaryInfoRenderer;

                        if (!empty($info->title->runs[0]->text)) {
                            $title = $info->title->runs[0]->text;
                        }

                        if (!empty($info->viewCount->videoViewCountRenderer->viewCount->simpleText)) {
                            $views_tmp = $info->viewCount->videoViewCountRenderer->viewCount->simpleText;
                            $views = Utils::getNumberFromText(trim($views_tmp));
                        }
                        if (!empty($info->dateText->simpleText)) {
                            $publishDateText = $info->dateText->simpleText;
                            $publishDateText = trim(str_replace("Published on", "", str_replace("Premiered", "", $publishDateText)));
                            $publishDate = strtotime($publishDateText);
                        }

                        if (!empty($info->videoActions->menuRenderer->topLevelButtons[0]->segmentedLikeDislikeButtonRenderer->likeButton->toggleButtonRenderer->defaultText->simpleText)) {
                            $like = Utils::shortNumber2Number($info->videoActions->menuRenderer->topLevelButtons[0]->segmentedLikeDislikeButtonRenderer->likeButton->toggleButtonRenderer->defaultText->simpleText);
                        }
                        if (!empty($info->videoActions->menuRenderer->topLevelButtons[1]->toggleButtonRenderer->defaultText->simpleText)) {
                            $dislike = intval($info->videoActions->menuRenderer->topLevelButtons[1]->toggleButtonRenderer->defaultText->simpleText);
                        }
                    }
                    if (!empty($temp->videoSecondaryInfoRenderer)) {
                        $info = $temp->videoSecondaryInfoRenderer;
                        if (!empty($info->owner->videoOwnerRenderer->title->runs[0]->text)) {
                            $channelName = $info->owner->videoOwnerRenderer->title->runs[0]->text;
                        }
                        if (!empty($info->owner->videoOwnerRenderer->navigationEndpoint->browseEndpoint->browseId)) {
                            $channelId = $info->owner->videoOwnerRenderer->navigationEndpoint->browseEndpoint->browseId;
                        }
                        if (!empty($info->owner->videoOwnerRenderer->subscriberCountText->simpleText)) {
                            $channel_sub = Utils::getNumberFromText($info->owner->videoOwnerRenderer->subscriberCountText->simpleText);
                        }
                        if (!empty($info->attributedDescription->content)) {
                            $description = $info->attributedDescription->content;
                        }
                        if (!empty($info->metadataRowContainer->metadataRowContainerRenderer->rows)) {
                            $lists = $info->metadataRowContainer->metadataRowContainerRenderer->rows;
                            foreach ($lists as $list) {
                                if (!empty($list->metadataRowRenderer->title->simpleText)) {
                                    if (strtolower($list->metadataRowRenderer->title->simpleText) == "song") {
                                        if (!empty($list->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                            $songnames[] = $list->metadataRowRenderer->contents[0]->runs[0]->text;
                                            $countSong++;
                                        } else if (!empty($list->metadataRowRenderer->contents[0]->simpleText)) {
                                            $songnames[] = $list->metadataRowRenderer->contents[0]->simpleText;
                                        }
                                    }
                                    if (strtolower($list->metadataRowRenderer->title->simpleText) == "artist") {
                                        if (!empty($list->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                            $artists[] = $list->metadataRowRenderer->contents[0]->runs[0]->text;
                                        } else if (!empty($list->metadataRowRenderer->contents[0]->simpleText)) {
                                            $artists[] = $list->metadataRowRenderer->contents[0]->simpleText;
                                        }
                                    }
                                    if (strtolower($list->metadataRowRenderer->title->simpleText) == "album") {
                                        if (!empty($list->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                            $albums[] = $list->metadataRowRenderer->contents[0]->runs[0]->text;
                                        } else if (!empty($list->metadataRowRenderer->contents[0]->simpleText)) {
                                            $albums[] = $list->metadataRowRenderer->contents[0]->simpleText;
                                        }
                                    }
                                    if (strtolower($list->metadataRowRenderer->title->simpleText) == "licensed to youtube by") {
                                        if (!empty($list->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                            $licenses[] = $list->metadataRowRenderer->contents[0]->runs[0]->text;
                                        } else if (!empty($list->metadataRowRenderer->contents[0]->simpleText)) {
                                            $licenses[] = $list->metadataRowRenderer->contents[0]->simpleText;
                                        }
                                    }
                                    if (strtolower($list->metadataRowRenderer->title->simpleText) == "writers") {
                                        if (!empty($list->metadataRowRenderer->contents[0]->runs[0]->text)) {
                                            $writers[] = $list->metadataRowRenderer->contents[0]->runs[0]->text;
                                        } else if (!empty($list->metadataRowRenderer->contents[0]->simpleText)) {
                                            $writers[] = $list->metadataRowRenderer->contents[0]->simpleText;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (!empty($temp->itemSectionRenderer)) {
                        $info = $temp->itemSectionRenderer;
                        if (!empty($info->contents[0]->commentsEntryPointHeaderRenderer->commentCount->simpleText)) {
                            $comment = Utils::shortNumber2Number($info->contents[0]->commentsEntryPointHeaderRenderer->commentCount->simpleText);
                        }
                    }
                }
            }
            //lấy next video
            if (!empty($datas->contents->twoColumnWatchNextResults->autoplay->autoplay->sets[0]->autoplayVideo->watchEndpoint->videoId)) {
                $nextVideo = $datas->contents->twoColumnWatchNextResults->autoplay->autoplay->sets[0]->autoplayVideo->watchEndpoint->videoId;
            }

            //lấy thông tin claim
            if (!empty($datas->engagementPanels)) {
                foreach ($datas->engagementPanels as $tmp) {
                    if (!empty($tmp->engagementPanelSectionListRenderer->content->structuredDescriptionContentRenderer->items)) {
                        $tmp2s = $tmp->engagementPanelSectionListRenderer->content->structuredDescriptionContentRenderer->items;
                        foreach ($tmp2s as $tmp2) {
                            if (!empty($tmp2->videoDescriptionMusicSectionRenderer->carouselLockups)) {
                                $tmp3s = $tmp2->videoDescriptionMusicSectionRenderer->carouselLockups;
                                if (count($tmp3s) == 1) {
                                    //1 bai hat
                                    if (!empty($tmp3s[0]->carouselLockupRenderer->infoRows)) {
                                        $tmp4s = $tmp3s[0]->carouselLockupRenderer->infoRows;
                                        foreach ($tmp4s as $tmp4) {
                                            if (!empty($tmp4->infoRowRenderer->title->simpleText)) {
                                                $check = $tmp4->infoRowRenderer->title->simpleText;
                                                if (strtolower($check) == 'song') {
                                                    if (!empty($tmp4->infoRowRenderer->defaultMetadata->runs[0]->text)) {
                                                        $songnames[] = $tmp4->infoRowRenderer->defaultMetadata->runs[0]->text;
                                                    } elseif (!empty($tmp4->infoRowRenderer->defaultMetadata->simpleText)) {
                                                        $songnames[] = $tmp4->infoRowRenderer->defaultMetadata->simpleText;
                                                    }
                                                } elseif (strtolower($check) == 'artist') {
                                                    if (!empty($tmp4->infoRowRenderer->defaultMetadata->run[0]->text)) {
                                                        $artists[] = $tmp4->infoRowRenderer->defaultMetadata->run[0]->text;
                                                    } elseif (!empty($tmp4->infoRowRenderer->defaultMetadata->simpleText)) {
                                                        $artists[] = $tmp4->infoRowRenderer->defaultMetadata->simpleText;
                                                    }
                                                } elseif (strtolower($check) == 'album') {
                                                    if (!empty($tmp4->infoRowRenderer->defaultMetadata->simpleText)) {
                                                        $albums[] = $tmp4->infoRowRenderer->defaultMetadata->simpleText;
                                                    }
                                                } elseif (strtolower($check) == 'writers') {
                                                    if (!empty($tmp4->infoRowRenderer->defaultMetadata->simpleText)) {
                                                        $writers[] = $tmp4->infoRowRenderer->defaultMetadata->simpleText;
                                                    }
                                                } elseif (strtolower($check) == 'licenses') {
                                                    if (!empty($tmp4->infoRowRenderer->expandedMetadata->simpleText)) {
                                                        $licenses[] = $tmp4->infoRowRenderer->expandedMetadata->simpleText;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    //nhieu bai hat
                                    foreach ($tmp3s as $tmp3) {
                                        if (!empty($tmp3->carouselLockupRenderer->videoLockup->compactVideoRenderer->title->runs[0]->text)) {
                                            $songnames[] = $tmp3->carouselLockupRenderer->videoLockup->compactVideoRenderer->title->runs[0]->text;
                                        } elseif (!empty($tmp3->carouselLockupRenderer->videoLockup->compactVideoRenderer->title->simpleText)) {
                                            $songnames[] = $tmp3->carouselLockupRenderer->videoLockup->compactVideoRenderer->title->simpleText;
                                        }
                                        if (!empty($tmp3->carouselLockupRenderer->infoRows)) {
                                            $tmp4s = $tmp3->carouselLockupRenderer->infoRows;
                                            foreach ($tmp4s as $tmp4) {
                                                if (!empty($tmp4->infoRowRenderer->title->simpleText)) {
                                                    $check = $tmp4->infoRowRenderer->title->simpleText;
                                                    if (strtolower($check) == 'artist') {
                                                        if (!empty($tmp4->infoRowRenderer->defaultMetadata->runs[0]->text)) {
                                                            $artists[] = $tmp4->infoRowRenderer->defaultMetadata->runs[0]->text;
                                                        } elseif (!empty($tmp4->infoRowRenderer->defaultMetadata->simpleText)) {
                                                            $artists[] = $tmp4->infoRowRenderer->defaultMetadata->simpleText;
                                                        }
                                                    } elseif (strtolower($check) == 'album') {
                                                        if (!empty($tmp4->infoRowRenderer->defaultMetadata->simpleText)) {
                                                            $albums[] = $tmp4->infoRowRenderer->defaultMetadata->simpleText;
                                                        }
                                                    } elseif (strtolower($check) == 'writers') {
                                                        if (!empty($tmp4->infoRowRenderer->defaultMetadata->simpleText)) {
                                                            $writers[] = $tmp4->infoRowRenderer->defaultMetadata->simpleText;
                                                        }
                                                    } elseif (strtolower($check) == 'licenses') {
                                                        if (!empty($tmp4->infoRowRenderer->expandedMetadata->simpleText)) {
                                                            $licenses[] = $tmp4->infoRowRenderer->expandedMetadata->simpleText;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            error_log("getVideoInfoHtmlDesktop $videoId not matches");
        }
        if ($title != "") {
            $status = 1;
        }
        return array("status" => $status, "video_id" => $videoId, "title" => $title, "length" => $videoLength,
            "like" => $like, "dislike" => $dislike, "view" => $views,
            "publish_date" => $publishDate, "channelId" => $channelId, "channelName" => $channelName,
            "song_name" => json_encode($songnames), "artists" => json_encode($artists), "album" => json_encode($albums),
            "license" => json_encode($licenses), "writers" => json_encode($writers),
            "comment" => $comment, "countSong" => $countSong, "next_video" => $nextVideo, "channel_sub" => $channel_sub,
            "description" => $description);
    }

    public static function getListShortVideo($link, $test = 0) {
        $response = ProxyHelper::get($link, 2);
        $status = 0;
        $token = null;
        $visitorData = null;
        $results = [];
        $out = preg_replace_callback("(\\\\x([0-9a-f]{2}))i", function($a) {
            return chr(hexdec($a[1]));
        }, $response
        );
        preg_match("/<meta itemprop=\"duration\" content=\"([^\"]+)/", $response, $mat);
        if (count($mat) > 1) {
            $tmpDur = $mat[1];
            $tmpDur = str_replace("PT", "", $tmpDur);
            $tmpDur = str_replace("S", "", $tmpDur);
            $arr = explode("M", $tmpDur);
            if (count($arr) == 2) {
                $videoLength = $arr[0] * 60 + $arr[1];
            }
        }
        $content = "";
        preg_match_all("/<script(.*?)<\/script>/", $out, $matches0);
        if (count($matches0) > 0) {
            $arrays = $matches0[1];
            foreach ($arrays as $data) {
                if (Utils::containString($data, "var ytInitialData =")) {
                    $data .= 'xxxyyyzzz';
                    preg_match("/var\s+ytInitialData\s+=\s+(.*?);xxxyyyzzz/", $data, $matches1);
                    $content = str_replace('', '\\', $matches1[1]);
                }
            }
        } else {
            error_log("getListShortVideo $link not matches0");
        }
        preg_match("/\"contents\"/", $content, $matches);
//        $content = str_replace("/\\", "/\\\\", $content);
        if ($test == 1) {
            Utils::write("save/short.txt", $content);
        }
        if (count($matches) > 0) {
            $datas = json_decode($content);
            if (!empty($datas->responseContext->webResponseContextExtensionData->ytConfigData->visitorData)) {
                $visitorData = $datas->responseContext->webResponseContextExtensionData->ytConfigData->visitorData;
            }
            if (!empty($datas->contents->twoColumnBrowseResultsRenderer->tabs)) {
                $tabs = $datas->contents->twoColumnBrowseResultsRenderer->tabs;
                foreach ($tabs as $tab) {
                    if (!empty($tab->tabRenderer->title)) {
                        if (strtoupper($tab->tabRenderer->title) == 'SHORTS') {
                            if (!empty($tab->tabRenderer->content->richGridRenderer->contents)) {
                                $shorts = $tab->tabRenderer->content->richGridRenderer->contents;
                                foreach ($shorts as $short) {
                                    if (!empty($short->richItemRenderer->content->reelItemRenderer)) {
                                        $info = $short->richItemRenderer->content->reelItemRenderer;
                                        $results[] = (object) [
                                                    "video_id" => $info->videoId,
                                                    "title" => !empty($info->headline->simpleText) ? $info->headline->simpleText : "",
                                                    "views" => Utils::getNumberFromText(trim($info->viewCountText->simpleText)),
                                                    "thumb" => $info->thumbnail->thumbnails[0]->url
                                        ];
                                    } else if (!empty($short->continuationItemRenderer->continuationEndpoint->continuationCommand->token)) {
                                        $token = $short->continuationItemRenderer->continuationEndpoint->continuationCommand->token;
                                    }
                                }
                            }
                        }
                    }
                }
            }
//            error_log("NEXT token= $token");
            //find next page nếu có token.
            $n = 0;
            while (true) {
                if ($token != null) {
                    error_log("runing " . $n++);
                    $input = (object) [
                                "context" => (object) [
                                    "client" => (object) [
                                        "visitorData" => $visitorData,
                                        "clientName" => "WEB",
                                        "clientVersion" => "2.20221118.01.00",
                                        "osName" => "Windows",
                                        "osVersion" => "10.0",
                                        "originalUrl" => $link,
                                        "platform" => "DESKTOP",
                                        "timeZone" => "Asia/Bangkok",
                                        "browserName" => "Firefox",
                                        "browserVersion" => "106.0"
                                    ]
                                ],
                                "continuation" => $token
                    ];
//                    Utils::write("save/short_input.txt", json_encode($input));
//                    $data = ProxyHelper::youtube($input);
                    $json = shell_exec('curl --location --request POST \'https://www.youtube.com/youtubei/v1/browse?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8&prettyPrint=false\' \
                                        --header \'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36\' \
                                        --header \'Content-Type: application/json\' \
                                        --data-raw \'' . json_encode($input) . '\'');
//                    Utils::write("save/short_output$n.txt",$json);
                    $token = null;
                    $data = json_decode($json);
                    if (!empty($data->onResponseReceivedActions[0]->appendContinuationItemsAction->continuationItems)) {
                        $shorts = $data->onResponseReceivedActions[0]->appendContinuationItemsAction->continuationItems;
                        foreach ($shorts as $short) {
                            if (!empty($short->richItemRenderer->content->reelItemRenderer)) {
                                $info = $short->richItemRenderer->content->reelItemRenderer;
                                $results[] = (object) [
                                            "video_id" => $info->videoId,
                                            "title" => !empty($info->headline->simpleText) ? $info->headline->simpleText : "",
                                            "views" => Utils::getNumberFromText(trim($info->viewCountText->simpleText)),
                                            "thumb" => $info->thumbnail->thumbnails[0]->url
                                ];
                            } else if (!empty($short->continuationItemRenderer->continuationEndpoint->continuationCommand->token)) {
                                $token = $short->continuationItemRenderer->continuationEndpoint->continuationCommand->token;
                            }
                        }
                    }
                } else {
                    error_log("break");
                    break;
                }
            }
            error_log("end while");
        } else {
            error_log("getVideoInfoHtmlDesktop $link not matches");
        }
        return $results;
    }

    public static function searchVideoByApi($searchText, $ua = null, $retries = 3) {
//        $proxy_port = 333;
//        $proxy_ip = "usa.rotating.proxyrack.net";
//        $loginpassw = "dunndealpr:0ddf2c-02b7b2-7c80d6-6958a3-468cdb";
//        $proxy_ip = "zproxy.lum-superproxy.io";
//        $proxy_port = 22225;
//        $loginpassw = "lum-customer-c_308cb727-zone-static-ip-158.46.169.208:gmpqodpltr2c";

        $proxy = Proxy::orderBy("count")->first();
        $proxy->update_time = gmdate("Y-m-d H:i:s", time() + 7 * 3600);
        $proxy->count = $proxy->count + 1;
        $proxy->save();
        $proxy_ip = $proxy->ip;
        $proxy_port = $proxy->port;
        $loginpassw = "$proxy->user:$proxy->pass";

        $arrUserAgent = ["Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36", "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36", "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:54.0) Gecko/20100101 Firefox/73.0", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:61.0) Gecko/20100101 Firefox/73.0", "Mozilla/5.0 (X11; Linux i586; rv:31.0) Gecko/20100101 Firefox/73.0", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 Safari/605.1.15", "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36 Edg/80.0.361.50", "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36 OPR/66.0.3515.95", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36 OPR/66.0.3515.95", "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36 OPR/66.0.3515.95", "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36 Vivaldi/2.10", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36 Vivaldi/2.10", "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 Safari/537.36 Vivaldi/2.10", "Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 YaBrowser/19.7.3.172 Yowser/2.5 Safari/537.36", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.106 YaBrowser/19.6.0.1583 Yowser/2.5 Safari/537.36"];
        if (!isset($ua)) {
            $ua = $arrUserAgent[rand(0, count($arrUserAgent) - 1)];
        }
        $curl = null;
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://content-youtube.googleapis.com/youtube/v3/search?q=" . urlencode($searchText) . "&part=snippet&type=video&key=AIzaSyAa8yy0GdcGPHdtD083HiGGx_S0vMPScDM",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "x-origin: https://explorer.apis.google.com",
                    "user-agent: " . $ua,
                    "x-referer: https://explorer.apis.google.com",
                    "accept: */*",
                    "referer: https://explorer.apis.google.com",
                    "Host: content.googleapis.com",
                    "Accept-Encoding: gzip, deflate",
                    "Connection: keep-alive",
                    "cache-control: no-cache"
                ),
            ));
            curl_setopt($curl, CURLOPT_PROXYPORT, $proxy_port);
            curl_setopt($curl, CURLOPT_PROXYTYPE, 'HTTPS');
            curl_setopt($curl, CURLOPT_PROXY, $proxy_ip);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $loginpassw);
            $response = curl_exec($curl);
            Utils::write("videoSearch.txt", $response);
            if (!isset($response) || $response == "") {
                if ($retries < 1) {
                    echo("Error searchVideoByApi:--- ");
                } else {
                    echo("Retry searchVideoByApi:--- ");
                    return self::searchVideoByApi($searchText);
                }
            }
            curl_close($curl);
            $datas = json_decode($response);
            $videoId = "";
            $title = "";
            if (!empty($datas->items) && count($datas->items) > 0) {
                $item = $datas->items[0];
                if (!empty($item->id->videoId)) {
                    $videoId = $item->id->videoId;
                }
                if (!empty($item->snippet->title)) {
                    $title = $item->snippet->title;
                }
            }
        } catch (Exception $e) {
            try {
                if (isset($curl)) {
                    curl_close($curl);
                }
            } catch (Exception $exc) {
                
            }
            if ($retries < 1) {
                echo("Error searchVideoByApi: " . $e->getMessage());
            } else {
                echo("Retry searchVideoByApi: " . $e->getMessage());
                return self::searchVideoByApi($searchText);
            }
        }
        return array("video_id" => $videoId, "title" => $title);
    }

    //lấy thông tin của người comment
    public static function getInfoComment($videoId, $test = 0) {
        $response = ProxyHelper::get("https://youtube.com/watch?v=$videoId", 2);
        $token = null;
        $out = preg_replace_callback("(\\\\x([0-9a-f]{2}))i", function($a) {
            return chr(hexdec($a[1]));
        }, $response
        );

        $content = "";
        preg_match_all("/<script(.*?)<\/script>/", $out, $matches0);
        if (count($matches0) > 0) {
            $arrays = $matches0[1];
            foreach ($arrays as $data) {
                if (Utils::containString($data, "var ytInitialData =")) {
                    $data .= 'xxxyyyzzz';
                    preg_match("/var\s+ytInitialData\s+=\s+(.*?);xxxyyyzzz/", $data, $matches1);
                    $content = str_replace('', '\\', $matches1[1]);
                }
            }
        } else {
            error_log("getInfoComment $videoId not matches0");
            error_log($content);
        }
        preg_match("/\"contents\"/", $content, $matches);
//        if ($test == 1) {
//            Utils::write("save/short.txt", $content);
//        }
        if (count($matches) > 0) {
            $token = null;
            $datas = json_decode($content);
            if (!empty($datas->contents->twoColumnWatchNextResults->results->results->contents)) {
                $contents = $datas->contents->twoColumnWatchNextResults->results->results->contents;
                foreach ($contents as $data) {
                    if (!empty($data->itemSectionRenderer->contents[0]->continuationItemRenderer->continuationEndpoint->continuationCommand->token)) {
                        $token = $data->itemSectionRenderer->contents[0]->continuationItemRenderer->continuationEndpoint->continuationCommand->token;
                    }
                }
            }
        } else {
            error_log("getInfoComment $videoId not matches");
            return 0;
        }
        if ($token == null) {
            return 0;
        }
        $i = 0;
        $result = [];
        while (true) {
            if ($token != null && count($result) <= 1000) {
                $i++;
                error_log("$i $token");
                $comments = shell_exec('curl \'https://www.youtube.com/youtubei/v1/next?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8&prettyPrint=false\' \
                                -H \'authority: www.youtube.com\' \
                                -H \'accept: */*\' \
                                -H \'accept-language: vi-VN,vi;q=0.9,fr-FR;q=0.8,fr;q=0.7,en-US;q=0.6,en;q=0.5\' \
                                -H \'authorization: SAPISIDHASH 1670438520_d0233ab6a970062660657695695eadd043651f78\' \
                                -H \'content-type: application/json\' \
                                -H \'cookie: VISITOR_INFO1_LIVE=LjOVkvLQRDE; GPS=1; PREF=f6=40000000&tz=Asia.Bangkok; YSC=8Gc4-sQ2sHY; HSID=AZFIajj2SZM3S7wDW; SSID=ATJTD1j6mdhVVOZQA; APISID=a2BVEjY6ooS7Xg63/AQJkz6QsdAzhfFZ6E; SAPISID=eZ_ENNHexf-bYaQ3/A6EJqTpBALOCiUbf9; __Secure-1PAPISID=eZ_ENNHexf-bYaQ3/A6EJqTpBALOCiUbf9; __Secure-3PAPISID=eZ_ENNHexf-bYaQ3/A6EJqTpBALOCiUbf9; LOGIN_INFO=AFmmF2swRgIhANwwvk5SWqD8b3F9KCWhH3J8PeT3cjiBq8G25YRVjRjyAiEA1QAVxOrBvFbH2RkzEy5_iY-b66r1-7BLHztgsj5fODs:QUQ3MjNmeElUaUU4WWUyeW1YVzJvZURsVUlkN3NtQng2aGNZWDhuMVB3dEFMOTdqTXZJQnNfVmZEcTdfYko0bjhtRGxkY3kyQjNYaUw3WmdfRXdSSm5tYVgxdDRhSTNQenZZV3lPX0hTaURaaGpvVnVXaEZPVVZOYjY3Zk9Wd3l5UkVENWc1cDNTaE4yeU5GVEpULXk4ek5BMWdENXpLQ3V3; YTSESSION-1ttomog=ACrRUPoYFQprMn8vq6IVG6SGDtEPu15aLLJLLWz3kSmor2g+hkj7DkhRTPwOw9iWsD/oaEkDHgWtil1ZFbcPrxhjhSxREvt0S62sKWLEBbi8tBzbqw61q+8v; SID=RAjUxfcwMf5rfS4lxwPUzI5tzu6jcBrbvzD3eWpc5OwgQFZeXlYX_7oXtsRA5hpRbeOafQ.; __Secure-1PSID=RAjUxfcwMf5rfS4lxwPUzI5tzu6jcBrbvzD3eWpc5OwgQFZeolwMgYQI4l-PPhihP095nQ.; __Secure-3PSID=RAjUxfcwMf5rfS4lxwPUzI5tzu6jcBrbvzD3eWpc5OwgQFZelL1KEIkOgb2vMKw46U6N1A.; CONSISTENCY=ADecB4vQXj7fcSyDANLy9wl3c7OV59nA7FIxmIonfQepChJxTls7vM_ToTleNyB7TbHamZPoJLpRV6xZj9mpGmeEvocgpjQ2wTrvnU5o-dnTYBlbz0ANPCJPrqzyTrv2g0kKxCvX2zuW5zMbR9F0H4Ix; SIDCC=AIKkIs29OZ9kNneAuncvWYNDWAwNvjt8qJNk9TF4MY9yyzyQHnT2407js9v5jpGwdJAaA37h; __Secure-1PSIDCC=AIKkIs1g4GPXw0_cIZ1agsqkpjH8JPZfBsMw8yqOogtaN-N9Y6GguPD298BdN3M7KktqHBDr2w; __Secure-3PSIDCC=AIKkIs32THHDF-cO91tFEDMwehTDQwAm7b6AhlA1rqhpg5c9HEDCP_K_R3kAASDShueeicN06Q\' \
                                -H \'origin: https://www.youtube.com\' \
                                -H \'referer: https://www.youtube.com/watch?v=' . $videoId . '\' \
                                -H \'sec-ch-ua: "Not?A_Brand";v="8", "Chromium";v="108", "Google Chrome";v="108"\' \
                                -H \'sec-ch-ua-arch: "x86"\' \
                                -H \'sec-ch-ua-bitness: "64"\' \
                                -H \'sec-ch-ua-full-version: "108.0.5359.95"\' \
                                -H \'sec-ch-ua-full-version-list: "Not?A_Brand";v="8.0.0.0", "Chromium";v="108.0.5359.95", "Google Chrome";v="108.0.5359.95"\' \
                                -H \'sec-ch-ua-mobile: ?0\' \
                                -H \'sec-ch-ua-model;\' \
                                -H \'sec-ch-ua-platform: "Windows"\' \
                                -H \'sec-ch-ua-platform-version: "14.0.0"\' \
                                -H \'sec-ch-ua-wow64: ?0\' \
                                -H \'sec-fetch-dest: empty\' \
                                -H \'sec-fetch-mode: same-origin\' \
                                -H \'sec-fetch-site: same-origin\' \
                                -H \'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36\' \
                                -H \'x-client-data: CIe2yQEIpLbJAQjBtskBCKmdygEIyPTKAQiTocsBCIurzAEIxcnMAQjF4cwBCI/pzAEI9vHMAQjM+cwBCN35zAEI5frMAQik+8wBCPD/zAEIh4HNAQjGg80B\' \
                                -H \'x-goog-authuser: 0\' \
                                -H \'x-goog-visitor-id: CgtMak9Wa3ZMUVJERSj0vMOcBg%3D%3D\' \
                                -H \'x-origin: https://www.youtube.com\' \
                                -H \'x-youtube-bootstrap-logged-in: true\' \
                                -H \'x-youtube-client-name: 1\' \
                                -H \'x-youtube-client-version: 2.20221207.01.00\' \
                                --data-raw \'{"context":{"client":{"hl":"vi","gl":"VN","remoteHost":"2402:800:61ae:a210:813c:3e24:e68f:2109","deviceMake":"","deviceModel":"","visitorData":"CgtMak9Wa3ZMUVJERSj0vMOcBg%3D%3D","userAgent":"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36,gzip(gfe)","clientName":"WEB","clientVersion":"2.20221207.01.00","osName":"Windows","osVersion":"10.0","originalUrl":"https://www.youtube.com/watch?v=' . $videoId . '","platform":"DESKTOP","clientFormFactor":"UNKNOWN_FORM_FACTOR","configInfo":{"appInstallData":"CPS8w5wGELbgrgUQsoj-EhCJ6K4FELiQ_hIQh92uBRC82q4FEP_krgUQ4tSuBRD8lv4SELjUrgUQieGuBRC_hv4SEIeR_hIQuIuuBRCLyq4FENrjrgUQkfj8EhDYvq0F"},"userInterfaceTheme":"USER_INTERFACE_THEME_DARK","timeZone":"Asia/Bangkok","browserName":"Chrome","browserVersion":"108.0.0.0","acceptHeader":"text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9","deviceExperimentId":"ChxOekUzTkRRM09EYzVOek0yTVRJeE1URTRPQT09EPS8w5wG","screenWidthPoints":1920,"screenHeightPoints":485,"screenPixelDensity":1,"screenDensityFloat":1,"utcOffsetMinutes":420,"connectionType":"CONN_CELLULAR_4G","memoryTotalKbytes":"8000000","mainAppWebInfo":{"graftUrl":"https://www.youtube.com/watch?v=' . $videoId . '","pwaInstallabilityStatus":"PWA_INSTALLABILITY_STATUS_CAN_BE_INSTALLED","webDisplayMode":"WEB_DISPLAY_MODE_BROWSER","isWebNativeShareAvailable":true}},"user":{"lockedSafetyMode":false},"request":{"useSsl":true,"consistencyTokenJars":[{"encryptedTokenJarContents":"ADecB4vQXj7fcSyDANLy9wl3c7OV59nA7FIxmIonfQepChJxTls7vM_ToTleNyB7TbHamZPoJLpRV6xZj9mpGmeEvocgpjQ2wTrvnU5o-dnTYBlbz0ANPCJPrqzyTrv2g0kKxCvX2zuW5zMbR9F0H4Ix"}],"internalExperimentFlags":[]},"clickTracking":{"clickTrackingParams":"CLsCELsvGAMiEwj-94azlOj7AhXcxEwCHeo7CjY="},"adSignalsInfo":{"params":[{"key":"dt","value":"1670438516527"},{"key":"flash","value":"0"},{"key":"frm","value":"0"},{"key":"u_tz","value":"420"},{"key":"u_his","value":"8"},{"key":"u_h","value":"1080"},{"key":"u_w","value":"1920"},{"key":"u_ah","value":"1032"},{"key":"u_aw","value":"1920"},{"key":"u_cd","value":"24"},{"key":"bc","value":"31"},{"key":"bih","value":"485"},{"key":"biw","value":"1903"},{"key":"brdim","value":"0,0,0,0,1920,0,1920,1032,1920,485"},{"key":"vis","value":"1"},{"key":"wgl","value":"true"},{"key":"ca_type","value":"image"}],"bid":"ANyPxKof6aluEsD2rctCXN5sBqjoiR8J1uFsEusM-RDlcw_iMxE0rVTOm0MgOMaivNjQs_KEgcXCCG9eQieT6i9T9CXaBagloQ"}},"continuation":"' . $token . '"}\' \
                                --compressed');
                $token = null;

                if ($test == 1) {
                    Utils::write("save/comment$i.txt", $comments);
                }

                $dataComment = json_decode($comments);
                $listComments = [];
                if (!empty($dataComment->onResponseReceivedEndpoints[1]->reloadContinuationItemsCommand->continuationItems)) {
                    $listComments = $dataComment->onResponseReceivedEndpoints[1]->reloadContinuationItemsCommand->continuationItems;
                } else if (!empty($dataComment->onResponseReceivedEndpoints[0]->appendContinuationItemsAction->continuationItems)) {
                    $listComments = $dataComment->onResponseReceivedEndpoints[0]->appendContinuationItemsAction->continuationItems;
                }
                if (count($listComments) > 0) {
                    foreach ($listComments as $cmt) {
                        $channel = [];

                        if (!empty($cmt->commentThreadRenderer->comment->commentRenderer->authorEndpoint->browseEndpoint->browseId)) {
                            $channel['channel_id'] = $cmt->commentThreadRenderer->comment->commentRenderer->authorEndpoint->browseEndpoint->browseId;
                        }
                        if (!empty($cmt->commentThreadRenderer->comment->commentRenderer->authorText->simpleText)) {
                            $channel['name'] = $cmt->commentThreadRenderer->comment->commentRenderer->authorText->simpleText;
                        }
                        if (!empty($cmt->commentThreadRenderer->comment->commentRenderer->authorThumbnail->thumbnails)) {
                            foreach ($cmt->commentThreadRenderer->comment->commentRenderer->authorThumbnail->thumbnails as $thumb) {
                                if ($thumb->width == 176) {
                                    $channel['thumb'] = $thumb->url;
                                }
                            }
                        }
                        if (isset($channel['name'])) {
                            $result[] = (object) $channel;
                        }
                        //lấy token
                        if (!empty($cmt->continuationItemRenderer->continuationEndpoint->continuationCommand->token)) {
                            $token = $cmt->continuationItemRenderer->continuationEndpoint->continuationCommand->token;
                        }
                    }
                }
            } else {
                error_log("break");
                break;
            }
        }
        return $result;
    }

    //check view dung key
    public static function getStatics($ids) {
        // <editor-fold defaultstate="collapsed" desc="OLD">
//        $jData = null;
//        try {
//            $curl = curl_init();
//            $url = "https://youtube.googleapis.com/youtube/v3/videos?part=statistics&id=$ids&key=AIzaSyAbhCFK1TTj8ONgUUuCp8AO_Gv3nBnD0Tc";
////            error_log($url);
//            curl_setopt_array($curl, array(
//                CURLOPT_URL => $url,
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_ENCODING => '',
//                CURLOPT_MAXREDIRS => 10,
//                CURLOPT_TIMEOUT => 0,
//                CURLOPT_FOLLOWLOCATION => true,
//                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                CURLOPT_CUSTOMREQUEST => 'GET',
//                CURLOPT_HTTPHEADER => array(
//                    'Accept: application/json'
//                ),
//            ));
//            $response = curl_exec($curl);
//
//            $jData = json_decode($response);
//        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
//        } finally {
//            curl_close($curl);
//        }
//        return $jData;
// </editor-fold>
//        error_log('getStatics');
        $temp = ProxyHelper::get("https://youtube.googleapis.com/youtube/v3/videos?part=statistics&id=$ids&key=AIzaSyAbhCFK1TTj8ONgUUuCp8AO_Gv3nBnD0Tc");
//        Log::info($temp);
        $res = json_decode($temp);
        if (!empty($res->error)) {
            $temp = ProxyHelper::get("https://youtube.googleapis.com/youtube/v3/videos?part=statistics&id=$ids&key=AIzaSyAbhCFK1TTj8ONgUUuCp8AO_Gv3nBnD0Tc");
            return json_decode($temp);
        }
        return json_decode($temp);
    }

    public static function getVideoInfoByYoutubeDlp($videoId) {
        shell_exec("/home/tools/env/bin/python /home/tools/get-cookies.py");
        $cmd = "sudo yt-dlp --cookies cookies.txt -J $videoId";
        Log::info($cmd);
        $data = shell_exec($cmd);
        return $data;
    }

    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="ATHENA">
    public static function queryAthena($db, $query, $queryId = null, $nextToken = null) {
        error_log("queryAthena $queryId $nextToken");
        $result = [];
        if ($nextToken == null) {
            $req = (object) [
                        "db" => $db,
                        "query" => $query
            ];
            $url = "http://athena.autoseo.win/query";
        } else {
            $req = (object) [
                        "QueryId" => $queryId,
                        "NextToken" => $nextToken
            ];
            $url = "http://athena.autoseo.win/query-more";
        }
        $res = RequestHelper::callAPI("POST", $url, $req);
        if (!empty($res->rs)) {
            $result = array_merge($result, $res->rs);
            if (!empty($res->NextToken)) {
                $nextToken = $res->NextToken;
            } else {
                $nextToken = null;
            }
            if (!empty($res->QueryId)) {
                $queryId = $res->QueryId;
            } else {
                $queryId = null;
            }

            if ($nextToken != null && $queryId != null) {
                $tmp = self::queryAthena($db, $query, $queryId, $nextToken);
                $result = array_merge($result, $tmp);
            }
        }
        return $result;
    }

// </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="API CHECK">
    public static function updateMetaOfChannelNew($channel) {
        try {
            $client = new Google_Client();
            $client->setClientId($channel->client_id);
            $client->setClientSecret($channel->client_secret);
            $client->setScopes("https://www.googleapis.com/auth/youtube");
            $client->setRedirectUri("http://api.reupnet.info/apiReupTube/auth_multi.php");
            $client->setAccessType("offline");
            $client->setApprovalPrompt("force");
            $client->setAccessToken(urldecode($channel->chanel_code));
            $youtube = new Google_Service_YouTube($client);
            $streamsResponse = $youtube->channels->listChannels('snippet,statistics', array('mine' => 'true'));
//        Log::info(json_encode($streamsResponse));
            foreach ($streamsResponse['items'] as $streamItem) {
                $channelName = $streamItem['snippet']['title'];
                $channel->channel_name = $channelName;
                $statistics = $streamItem['statistics'];
                $channel->views = $statistics->viewCount;
                $channel->subscribes = $statistics->subscriberCount;
                $channel->status = 1;
                $channel->status_api = 1;
//            $channel->last_update_time = Utils::getGmt7("m/d/Y H:i:s");
//            $channel->save();
                Log::info("updateMetaOfChannelNew $channelName $statistics->viewCount $statistics->subscriberCount");
                break;
            }
        } catch (\Google_Service_Exception $ex) {
            Log::info("$channel->chanel_id " . $ex->getMessage());
        }
    }

    public static function checkApiStatus() {
//        set_time_limit(0);
        $processName = "channel-scan";
        if (ProcessUtils::isfreeProcess($processName)) {
            ProcessUtils::lockProcess($processName);
            $time = time();
            DB::enableQueryLog();
            $channels = Channel::where('status_api', 1)->whereRaw("$time - UNIX_TIMESTAMP(STR_TO_DATE(last_update_time,'%m/%d/%Y %H:%i:%s')) > 86400 or last_update_time is null")->get(["id", "user_name", "channel_id", "channel_code", "client_id", "client_secret"]);
//            Log::info(count($channels));
//            Log:info(DB::getQueryLog());
            $i = 0;
            $count = count($channels);
            foreach ($channels as $channel) {
                $i++;
                error_log("$i/$count checkApiStatus $channel->channel_id");
                try {
                    self::updateMetaOfChannelNew($channel);
                } catch (\Exception $exc) {
                    $channel->log = $exc->getMessage();
                    $channel->status = 0;
                    if (Utils::containString($channel->log, "Connection timed out")) {
                        $channel->status = 1;
                    }
                    if (Utils::containString($channel->log, "\"domain\": \"usageLimits\"")) {
                        $channel->status_api = 6; //tao api khac, add lai kenh
                        $channel->status = 2;
                    }
                    if (Utils::containString($channel->log, "invalid_grant") && Utils::containString($channel->log, "Bad Request")) {

                        $channel->status_api = 7; //email die
                    }
                    if (Utils::containString($channel->log, "The OAuth client was disabled")) {

                        $channel->status_api = 5; //key die, //add lai kenh
                    }
                    if (Utils::containString($channel->log, "invalid_request")) {

                        $channel->status_api = 8; //email die
                    }
                    if (Utils::containString($channel->log, "deleted_client")) {

                        $channel->status_api = 9;  //tao api va add lai kenh
                    }
                    if (Utils::containString($channel->log, "internal_failure")) {

                        $channel->status_api = 10; //email die
                    }
                    if (Utils::containString($channel->log, "invalid_grant") && Utils::containString($channel->log, "Account has been deleted")) {

                        $channel->status_api = 11; //account die
                    }
                    if (Utils::containString($channel->log, "Enable it by visiting")) {

                        $channel->status_api = 12; //email die, //add lai kenh
                    }
                    if (Utils::containString($channel->log, "invalid_grant") && Utils::containString($channel->log, "Token has been expired or revoked")) {
                        $channel->status_api = 13;  //add lai kenh
                    }
                    if (Utils::containString($channel->log, "authError")) {

                        $channel->status_api = 14; //add lai kenh
                    }
                    $channel->last_update_time = Utils::getGmt7("m/d/Y H:i:s");
                    $channel->save();
                }
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            error_log("process $processName is running");
        }
    }

    public static function deleteVideo($channel, $videoId) {
        Log::info("deleteVideo $channel->chanel_id $videoId");
        $request = (object) [
                    "video_id" => $videoId,
                    "default_language" => "en",
                    "url_redirect" => "https://videomanager.orfium.com/auth",
                    "client_id" => "841180336951-3hk8i45arjftt6rmskftvc518nurlv1u.apps.googleusercontent.com",
                    "client_secret" => "YMmwipN2NaUAYgch6recQ8zf",
                    "channel_code" => $channel->channel_code_orfium
        ];
        $result = RequestHelper::callAPI("POST", "http://65.21.108.148/pll/video/delete/", $request);
        Log::info("deleteVideo $channel->chanel_id $videoId " . json_encode($result));
        return $result->status;
    }

// </editor-fold>
}
