<?php

namespace App\Http\Controllers;

use App\Common\Youtube\YoutubeHelper;
use App\Common\Utils;
use App\Http\Models\AccountInfo;
use App\Http\Models\Campaign;
use App\Http\Models\CampaignStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use Validator;

class SongMatchController extends Controller {

    public function __construct() {
        // Có thể thêm middleware authentication nếu cần
    }

    /**
     * API để match songs với campaign_statistics và insert vào bảng campaign
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function matchSongs(Request $request) {
        Log::info('|SongMatchController.matchSongs|request=' . json_encode($request->all()));

        // Validate input
        $validator = Validator::make($request->all(), [
                    'video_id' => 'required|string',
                    'channel_id' => 'required|string',
                    'duration' => 'required|string',
                    'songs' => 'required|array|min:1',
                    'songs.*.artist' => 'required|string',
                    'songs.*.song_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                        'status' => 'error',
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                            ], 400);
        }

        $video_id = trim($request->video_id);
        $channel_id = trim($request->channel_id);
        $songs = $request->songs;

        try {
            // Lấy thông tin channel
            $accountInfo = AccountInfo::where('chanel_id', $channel_id)->first();
            if (!$accountInfo) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'Channel not found'
                                ], 404);
            }

            // Lấy thông tin video từ YouTube
            $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id, 1);
            if ($videoInfo['status'] == 0) {
                // Retry nếu lần đầu thất bại
                for ($t = 0; $t < 3; $t++) {
                    $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video_id);
                    if ($videoInfo['status'] == 1) {
                        break;
                    }
                }
            }
            if ($videoInfo['status'] == 0) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'Cannot get video information from YouTube'
                                ], 400);
            }
            // Xác định video_type dựa trên độ dài video
            $videoLength = Utils::timeToSeconds(trim($request->duration));
            if ($videoLength == 0) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'Cannot get video duration'
                                ], 400);
            }
            $videoType = ($videoLength >= 420) ? 5 : 2;  // 7 phút = 420 giây, 5=mix, 2=lyric

            $curr = time();
            $count = 0;
            $matchedSongs = [];
            $errors = [];

            // Lặp qua từng bài hát trong danh sách
            foreach ($songs as $song) {
                $artist = trim($song['artist']);
                $song_name = trim($song['song_name']);

                // Tìm kiếm trong bảng campaign_statistics
                $campaignStatics = CampaignStatistics::where('type', 2)
                        ->whereIn('status', [1, 4])
                        ->where('claim_artist', $artist)
                        ->where('claim_song_name', $song_name)
                        ->get();

                if ($campaignStatics->count() > 0) {
                    foreach ($campaignStatics as $campaignStatic) {
                        // Kiểm tra trùng lặp campaign_id và video_id
                        $check = Campaign::where('video_id', $video_id)
                                ->where('campaign_id', $campaignStatic->id)
                                ->first();

                        if ($check) {
                            // Cập nhật record đã tồn tại
                            $check->log = $check->log . PHP_EOL . gmdate('Y/m/d H:i:s', time() + 7 * 3600) . " update auto from song match API";
                            $check->is_match_claim = 1;
                            $check->video_type = $videoType;
                            $check->number_claim = count($songs);
                            $check->log_claim = 'Matched via API';
                            $check->is_claim = 1;
                            $check->save();
                            $count++;

                            $matchedSongs[] = [
                                'artist' => $artist,
                                'song_name' => $song_name,
                                'campaign_id' => $campaignStatic->id,
                                'action' => 'updated',
                                'video_type' => $videoType,
                                'video_length' => $videoLength
                            ];
                        } else {
                            // Tạo record mới
                            $campaign = new Campaign();
                            $campaign->campaign_id = $campaignStatic->id;
                            $campaign->username = Utils::getUserFromUserCode($accountInfo->user_name);
                            $campaign->is_bommix = 0;
                            $campaign->campaign_name = $campaignStatic->campaign_name;
                            $campaign->channel_id = $channel_id;
                            $campaign->channel_name = $videoInfo['channelName'];
                            $campaign->video_type = $videoType;
                            $campaign->video_id = $video_id;
                            $campaign->video_title = $videoInfo['title'];
                            $campaign->views_detail = '[]';
                            $campaign->status = $videoInfo['status'];
                            $campaign->create_time = gmdate('Y/m/d H:i:s', $curr + 7 * 3600);
                            $campaign->update_time = gmdate('Y/m/d H:i:s', $curr + 7 * 3600);
                            $campaign->publish_date = $videoInfo['publish_date'];
                            $campaign->insert_date = gmdate('Ymd', $curr + 7 * 3600);
                            $campaign->insert_time = gmdate('H:i:s', $curr + 7 * 3600);
                            $campaign->status_confirm = 1;
                            $campaign->is_match_claim = 1;
                            $log = gmdate('Y/m/d H:i:s', time() + 7 * 3600) . " add new auto from song match API";
                            $campaign->log = $log;
                            $campaign->log = $campaign->log . PHP_EOL . gmdate('Y/m/d H:i:s', time() + 7 * 3600) . ' system confirm 3';
                            $campaign->is_claim = 1;
                            $campaign->number_claim = count($songs);
                            $campaign->log_claim = 'Matched via API';
                            $campaign->save();
                            $count++;

                            $matchedSongs[] = [
                                'artist' => $artist,
                                'song_name' => $song_name,
                                'campaign_id' => $campaignStatic->id,
                                'action' => 'created',
                                'video_type' => $videoType,
                                'video_length' => $videoLength
                            ];
                        }
                    }
                } else {
                    $errors[] = [
                        'artist' => $artist,
                        'song_name' => $song_name,
                        'message' => 'No matching campaign found'
                    ];
                }
            }

            return response()->json([
                        'status' => 'success',
                        'message' => "Processed $count campaigns successfully",
                        'data' => [
                            'video_id' => $video_id,
                            'channel_id' => $channel_id,
                            'total_songs' => count($songs),
                            'matched_count' => $count,
                            'matched_songs' => $matchedSongs,
                            'errors' => $errors
                        ]
            ]);
        } catch (\Exception $e) {
            Log::error('SongMatchController.matchSongs error: ' . $e->getTraceAsString());
            return response()->json([
                        'status' => 'error',
                        'message' => 'Internal server error: ' . $e->getMessage()
                            ], 500);
        }
    }

}
