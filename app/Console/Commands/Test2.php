<?php

namespace App\Console\Commands;

use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Http\Controllers\BomController;
use App\Http\Models\AccountInfo;
use App\Http\Models\Bom;
use App\Http\Models\MooncoinContent;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Log;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use function response;

class Test2 extends Command {

    /**
     * The name and signature of the console command.
     *
     * cam = campaign_id1,campaign_id2
     * dis = Orchard, Indiy...
     * channels = channel_id1,channel_id2
     */
    protected $signature = 'app:test2 {fn} {param}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        set_time_limit(0);
        error_log("run commnad");
        $functionName = $this->argument('fn');
        $param = $this->argument('param');
        if (isset($param)) {
            if (method_exists($this, $functionName)) {
                $this->$functionName($param); // Gọi hàm dựa vào tham số
            } else {
                $this->error("Hàm '$functionName' không tồn tại.");
            }
        } else {
            if (method_exists($this, $functionName)) {
                $this->$functionName(); // Gọi hàm dựa vào tham số
            } else {
                $this->error("Hàm '$functionName' không tồn tại.");
            }
        }
    }

    //
    public function deleteVideo() {
        DB::enableQueryLog();
        $datas = DB::select("select * from z_delete_video");
        foreach ($datas as $data) {
            error_log($data->video_id);
            $taskLists = [];
            $login = (object) [
                        "script_name" => "profile",
                        "func_name" => "login",
                        "params" => []
            ];
            $taskLists[] = $login;
            $deleteVideo = (object) [
                        "script_name" => "video",
                        "func_name" => "del_video",
                        "params" => [
                            (object) [
                                "name" => "video_id",
                                "type" => "string",
                                "value" => $data->video_id,
                            ]
                        ]
            ];
            $taskLists[] = $deleteVideo;
            $req = (object) [
                        "gmail" => $data->gmail,
                        "task_list" => json_encode($taskLists),
                        "run_time" => 0,
                        "type" => 69,
                        "piority" => 10
            ];

            $res = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
            error_log('job_id ' . $res->job_id);
            error_log(json_encode($taskLists));
        }
    }

    public function epidData() {
        // <editor-fold defaultstate="collapsed" desc="data">
        $dataChannel = [
                [
                'Channel_Link' => 'UC8OxbSlbE-eFk53QS06EgNA',
                'Subscribers' => 33900,
                'New_Subscribers' => 50000,
                'Epic_Views' => 150000,
                'New_Epic_Views' => 5000000,
                'Sangs_Note' => '2024/11/25'
            ],
                [
                'Channel_Link' => 'UCTx2NDbKyCNPWmjBvuMdRCA',
                'Subscribers' => 1530,
                'New_Subscribers' => 2720,
                'Epic_Views' => 30000,
                'New_Epic_Views' => 170000,
                'Sangs_Note' => '2024/11/25'
            ],
                [
                'Channel_Link' => 'UCTvR8q5-IxGu8pSWCi7Owqg',
                'Subscribers' => 44400,
                'New_Subscribers' => 49500,
                'Epic_Views' => 70000,
                'New_Epic_Views' => 526000,
                'Sangs_Note' => '2024/11/25'
            ],
                [
                'Channel_Link' => 'UCoPbdk1mzH7pOaVTaMatzGQ',
                'Subscribers' => 3800,
                'New_Subscribers' => 6700,
                'Epic_Views' => 15000,
                'New_Epic_Views' => 400000,
                'Sangs_Note' => '2024/11/25'
            ],
                [
                'Channel_Link' => 'UCLJQWOVbX6i0xZXJolLrTiA',
                'Subscribers' => 1863,
                'New_Subscribers' => 3274,
                'Epic_Views' => 900000,
                'New_Epic_Views' => 1500000,
                'Sangs_Note' => '2024/11/25'
            ],
                [
                'Channel_Link' => 'UCe0qV7yQE11x-DhmOeVvY5g',
                'Subscribers' => 1130,
                'New_Subscribers' => 4017,
                'Epic_Views' => 600000,
                'New_Epic_Views' => 1600000,
                'Sangs_Note' => '2024/12/5'
            ],
                [
                'Channel_Link' => 'UCQOOPCuMJR5rL7BcDKDkxYw',
                'Subscribers' => 523,
                'New_Subscribers' => 1100,
                'Epic_Views' => 840000,
                'New_Epic_Views' => 1900000,
                'Sangs_Note' => '2024/12/5'
            ],
                [
                'Channel_Link' => 'UCDJtFK5VDmN7AJ6uTtbzXwA',
                'Subscribers' => 470,
                'New_Subscribers' => 730,
                'Epic_Views' => 600000,
                'New_Epic_Views' => 1100000,
                'Sangs_Note' => '2024/12/5'
            ],
                [
                'Channel_Link' => 'UCln37Gv1LV-CTN6At55iwRg',
                'Subscribers' => 18000,
                'New_Subscribers' => 18582,
                'Epic_Views' => 219000,
                'New_Epic_Views' => 307000,
                'Sangs_Note' => '2024/12/5'
            ],
                [
                'Channel_Link' => 'UCiU6eJYi81RNHjPz8_Fhc_Q',
                'Subscribers' => 1050,
                'New_Subscribers' => 8729,
                'Epic_Views' => 118000,
                'New_Epic_Views' => 1700000,
                'Sangs_Note' => '2024/12/5'
            ],
                [
                'Channel_Link' => 'UCqgQXZx1cm0IfZw7jQMk_jQ',
                'Subscribers' => 41000,
                'New_Subscribers' => 42500,
                'Epic_Views' => 103000,
                'New_Epic_Views' => 171000,
                'Sangs_Note' => '2024/12/5'
            ],
                [
                'Channel_Link' => 'UCjv-bu_zou7bEXxe1G-cJhw',
                'Subscribers' => 400,
                'New_Subscribers' => 7300,
                'Epic_Views' => 200000,
                'New_Epic_Views' => 3060000,
                'Sangs_Note' => '2024/12/13'
            ],
                [
                'Channel_Link' => 'UCWz_T6jAq7ThWpDdrZSSTVw',
                'Subscribers' => 1920,
                'New_Subscribers' => 3000,
                'Epic_Views' => 2400000,
                'New_Epic_Views' => 3200000,
                'Sangs_Note' => '2024/12/13'
            ],
                [
                'Channel_Link' => 'UCnbdww-v_PX0vyGu4KOkSdg',
                'Subscribers' => 264,
                'New_Subscribers' => 4400,
                'Epic_Views' => 300000,
                'New_Epic_Views' => 4000000,
                'Sangs_Note' => '2024/12/13'
            ],
                [
                'Channel_Link' => 'UCx6jjPvu_jD-d7nNrcExM3g',
                'Subscribers' => 9670,
                'New_Subscribers' => 10900,
                'Epic_Views' => 100000,
                'New_Epic_Views' => 216000,
                'Sangs_Note' => '2024/12/13'
            ],
                [
                'Channel_Link' => 'UCa0qEo7jU8vTs2R5YctTOvw',
                'Subscribers' => 4320,
                'New_Subscribers' => 4900,
                'Epic_Views' => 100000,
                'New_Epic_Views' => 186000,
                'Sangs_Note' => '2024/12/13'
            ],
                [
                'Channel_Link' => 'UCLK2ALYvglA-bqEyc8xXKhg',
                'Subscribers' => 904,
                'New_Subscribers' => 1100,
                'Epic_Views' => 421000,
                'New_Epic_Views' => 540000,
                'Sangs_Note' => '2024/12/13'
            ],
                [
                'Channel_Link' => 'UC4a26M8KQ6hT5PFonXVpwhQ',
                'Subscribers' => 2660,
                'New_Subscribers' => 3700,
                'Epic_Views' => 100000,
                'New_Epic_Views' => 250000,
                'Sangs_Note' => '2024/12/13'
            ],
                [
                'Channel_Link' => 'UCYTjW1nrkTeKy6Uxq64TMZA',
                'Subscribers' => 243,
                'New_Subscribers' => 8800,
                'Epic_Views' => 100000,
                'New_Epic_Views' => 3000000,
                'Sangs_Note' => '2024/12/13'
            ],
                [
                'Channel_Link' => 'UCXyLtVLrxGKmSitEML7q4zQ',
                'Subscribers' => 286,
                'New_Subscribers' => 669,
                'Epic_Views' => 530000,
                'New_Epic_Views' => 850000,
                'Sangs_Note' => '2024/12/13'
            ],
                [
                'Channel_Link' => 'UCZWLE6VkqVwHWbWzV3GqKNA',
                'Subscribers' => 429,
                'New_Subscribers' => 682,
                'Epic_Views' => 600000,
                'New_Epic_Views' => 932000,
                'Sangs_Note' => '2024/12/13'
            ],
                [
                'Channel_Link' => 'UCA6HMsLNDcyPGaewv-Ks-mg',
                'Subscribers' => 3420,
                'New_Subscribers' => 5650,
                'Epic_Views' => 71000,
                'New_Epic_Views' => 440000,
                'Sangs_Note' => '2024/12/13'
            ],
                [
                'Channel_Link' => 'UC3y6S3AwwcuXiZSgqi9JX5Q',
                'Subscribers' => 293,
                'New_Subscribers' => 4001,
                'Epic_Views' => 103000,
                'New_Epic_Views' => 1300000,
                'Sangs_Note' => '2024/12/13'
            ],
                [
                'Channel_Link' => 'UCqKg6IkCsZe_sw7fxkxTVsg',
                'Subscribers' => 15700,
                'New_Subscribers' => 18500,
                'Epic_Views' => 100000,
                'New_Epic_Views' => 250000,
                'Sangs_Note' => '2025/1/9'
            ],
                [
                'Channel_Link' => 'UC3aCnqydZuKq9scdocsVtZg',
                'Subscribers' => 246,
                'New_Subscribers' => 2160,
                'Epic_Views' => 160000,
                'New_Epic_Views' => 1900000,
                'Sangs_Note' => '2025/1/9'
            ],
                [
                'Channel_Link' => 'UC3Eth-5ce1qbIe6q-DFcwHw',
                'Subscribers' => 2960,
                'New_Subscribers' => 3830,
                'Epic_Views' => 100000,
                'New_Epic_Views' => 377000,
                'Sangs_Note' => '2025/1/9'
            ],
                [
                'Channel_Link' => 'UCCwPaCdtvgOfxCi1OoD10dA',
                'Subscribers' => 10500,
                'New_Subscribers' => 12300,
                'Epic_Views' => 100000,
                'New_Epic_Views' => 125000,
                'Sangs_Note' => '2025/1/9'
            ],
                [
                'Channel_Link' => 'UC2zQ9lHJmaIMrkp9OXH67jg',
                'Subscribers' => 473,
                'New_Subscribers' => 1444,
                'Epic_Views' => 188000,
                'New_Epic_Views' => 687000,
                'Sangs_Note' => '2025/1/9'
            ],
                [
                'Channel_Link' => 'UCk4yfRdRvzNhCQ-CjDAT1mw',
                'Subscribers' => 125,
                'New_Subscribers' => 144,
                'Epic_Views' => 100000,
                'New_Epic_Views' => 130000,
                'Sangs_Note' => '2025/1/9'
            ],
                [
                'Channel_Link' => 'UC2heBunvQsaG0UXibDnWxKw',
                'Subscribers' => 170,
                'New_Subscribers' => 3300,
                'Epic_Views' => 112000,
                'New_Epic_Views' => 1500000,
                'Sangs_Note' => '2025/1/9'
            ],
                [
                'Channel_Link' => 'UC-v1Kmnlvk2xw_UThIi3TwA',
                'Subscribers' => 1000,
                'New_Subscribers' => 108000,
                'Epic_Views' => 328000,
                'New_Epic_Views' => 4100000,
                'Sangs_Note' => '2025/1/9'
            ],
                [
                'Channel_Link' => 'UC4u86Spq9k_g24JOHPSTB5Q',
                'Subscribers' => 750,
                'New_Subscribers' => 50000,
                'Epic_Views' => 353000,
                'New_Epic_Views' => 16000000,
                'Sangs_Note' => '2025/1/9'
            ],
                [
                'Channel_Link' => 'UCWYCxK7I36ZeGV63DJ0VQSQ',
                'Subscribers' => 6339,
                'New_Subscribers' => 8780,
                'Epic_Views' => 150000,
                'New_Epic_Views' => 715000,
                'Sangs_Note' => '2025/1/9'
            ],
                [
                'Channel_Link' => 'UC1LDRlCmaQFjPSN995TM9tA',
                'Subscribers' => 243,
                'New_Subscribers' => 75000,
                'Epic_Views' => 200000,
                'New_Epic_Views' => 5300000,
                'Sangs_Note' => '2025/1/9'
            ],
                [
                'Channel_Link' => 'UCeQaCBMNJhev1masTyhIsDw',
                'Subscribers' => 521,
                'New_Subscribers' => 1000,
                'Epic_Views' => 400000,
                'New_Epic_Views' => 1000000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UCZ0jQKqzaq8V68cMyhN6OKw',
                'Subscribers' => 357,
                'New_Subscribers' => 578,
                'Epic_Views' => 690000,
                'New_Epic_Views' => 1000000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UCZyNjrnsnVMrZvxeJTvuebA',
                'Subscribers' => 860,
                'New_Subscribers' => 2200,
                'Epic_Views' => 300000,
                'New_Epic_Views' => 1000000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UCloB3YxT6LFBI69TrAek2-Q',
                'Subscribers' => 3940,
                'New_Subscribers' => 5400,
                'Epic_Views' => 100000,
                'New_Epic_Views' => 174000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UC0elYfhrJV0LKuwfL40lGQw',
                'Subscribers' => 410,
                'New_Subscribers' => 4400,
                'Epic_Views' => 230000,
                'New_Epic_Views' => 2400000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UC0XS-JILqXQJ6iRYCs-Escw',
                'Subscribers' => 430,
                'New_Subscribers' => 4600,
                'Epic_Views' => 280000,
                'New_Epic_Views' => 1700000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UC3C6g_2n0oQhRKchReaunCA',
                'Subscribers' => 430,
                'New_Subscribers' => 4200,
                'Epic_Views' => 443000,
                'New_Epic_Views' => 3100000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UC1BbTzGj4FR0URpYFzDIjeg',
                'Subscribers' => 370,
                'New_Subscribers' => 658,
                'Epic_Views' => 290000,
                'New_Epic_Views' => 534000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UC_Htdt1x86ofJpIQwBmCUVg',
                'Subscribers' => 331,
                'New_Subscribers' => 10500,
                'Epic_Views' => 200000,
                'New_Epic_Views' => 6300000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UC15OhsmxSiqIMVIBBRLdj2w',
                'Subscribers' => 316,
                'New_Subscribers' => 867,
                'Epic_Views' => 200000,
                'New_Epic_Views' => 552900,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UC2q5JYtt-5w81js8FHrL9xw',
                'Subscribers' => 636,
                'New_Subscribers' => 3400,
                'Epic_Views' => 290000,
                'New_Epic_Views' => 1700000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UCUkNo6mCHgDxjSADWdK_Uaw',
                'Subscribers' => 5596,
                'New_Subscribers' => 29654,
                'Epic_Views' => 1300000,
                'New_Epic_Views' => 8400000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UCWjFhYRNqYL5vZl2u1xoHVw',
                'Subscribers' => 241,
                'New_Subscribers' => 826,
                'Epic_Views' => 143000,
                'New_Epic_Views' => 482000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UCgfe-B_lEB7Im5ISo7c-A2Q',
                'Subscribers' => 333,
                'New_Subscribers' => 1036,
                'Epic_Views' => 140000,
                'New_Epic_Views' => 402000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UC0AU0a1_mDIrrtOuPQxOCgQ',
                'Subscribers' => 229,
                'New_Subscribers' => 241,
                'Epic_Views' => 112000,
                'New_Epic_Views' => 123000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UC7h3oznMrHXlBamKRL0BTwA',
                'Subscribers' => 6600,
                'New_Subscribers' => 8600,
                'Epic_Views' => 100000,
                'New_Epic_Views' => 271000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UC4TBr5346N7LsuXR3cT9b-Q',
                'Subscribers' => 855,
                'New_Subscribers' => 24000,
                'Epic_Views' => 440000,
                'New_Epic_Views' => 1100000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UCLRYYDxqqdmN5HHLrIndIBA',
                'Subscribers' => 375,
                'New_Subscribers' => 26000,
                'Epic_Views' => 300000,
                'New_Epic_Views' => 1200000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UCTjlh5ea9Xb5AFH5GMfuWKQ',
                'Subscribers' => 957,
                'New_Subscribers' => 3300,
                'Epic_Views' => 114000,
                'New_Epic_Views' => 393000,
                'Sangs_Note' => '2025/1/11'
            ],
                [
                'Channel_Link' => 'UCLCKBBjvArDHsN-yCvGxbkg',
                'Subscribers' => 1300,
                'New_Subscribers' => 15000,
                'Epic_Views' => 667000,
                'New_Epic_Views' => 6100000,
                'Sangs_Note' => '2025/2/3'
            ],
                [
                'Channel_Link' => 'UC2hqF4zt3WvlkZDP97kgleA',
                'Subscribers' => 1700,
                'New_Subscribers' => 4000,
                'Epic_Views' => 582000,
                'New_Epic_Views' => 1800000,
                'Sangs_Note' => '2025/2/3'
            ],
                [
                'Channel_Link' => 'UC1sP_ZiVDh2TrKBHqPWTm0A',
                'Subscribers' => 480,
                'New_Subscribers' => 1400,
                'Epic_Views' => 535000,
                'New_Epic_Views' => 580000,
                'Sangs_Note' => '2025/2/3'
            ],
                [
                'Channel_Link' => 'UCJFH6SZ0347g4KKYSFJz66Q',
                'Subscribers' => 9900,
                'New_Subscribers' => 12000,
                'Epic_Views' => 820000,
                'New_Epic_Views' => 2000000,
                'Sangs_Note' => '2025/2/3'
            ],
                [
                'Channel_Link' => 'UCfwi7wmNbSCqG52vuLwuw3w',
                'Subscribers' => 738,
                'New_Subscribers' => 2200,
                'Epic_Views' => 636000,
                'New_Epic_Views' => 874000,
                'Sangs_Note' => '2025/2/3'
            ],
                [
                'Channel_Link' => 'UCZoxUZM9A3SVS04_4ovxzTg',
                'Subscribers' => 447,
                'New_Subscribers' => 460,
                'Epic_Views' => 253000,
                'New_Epic_Views' => 254000,
                'Sangs_Note' => '2025/2/3'
            ],
                [
                'Channel_Link' => 'UCbEkZ-_iN9uoPOWbeTc8LTw',
                'Subscribers' => 212,
                'New_Subscribers' => 223,
                'Epic_Views' => 110000,
                'New_Epic_Views' => 109000,
                'Sangs_Note' => '2025/2/3'
            ],
                [
                'Channel_Link' => 'UC_xHDkU-ZBKs2a7f_R1qKvg',
                'Subscribers' => 208,
                'New_Subscribers' => 209,
                'Epic_Views' => 128000,
                'New_Epic_Views' => 121000,
                'Sangs_Note' => '2025/2/3'
            ],
                [
                'Channel_Link' => 'UCGqXW7qPhqFiD0EWorDwU7g',
                'Subscribers' => 273,
                'New_Subscribers' => 415,
                'Epic_Views' => 116000,
                'New_Epic_Views' => 263000,
                'Sangs_Note' => '2025/2/3'
            ],
                [
                'Channel_Link' => 'UCTQHeqX6gGH8dbPEGNbSC3g',
                'Subscribers' => 660,
                'New_Subscribers' => 3300,
                'Epic_Views' => 560000,
                'New_Epic_Views' => 1420000,
                'Sangs_Note' => '2025/2/3'
            ],
                [
                'Channel_Link' => 'UClqejVr4b7oUQeWB4glz9Dg',
                'Subscribers' => 712,
                'New_Subscribers' => 2300,
                'Epic_Views' => 564000,
                'New_Epic_Views' => 1210000,
                'Sangs_Note' => '2025/2/3'
            ],
                [
                'Channel_Link' => 'UCj950CUDgAZak8PE9OXInnw',
                'Subscribers' => 204,
                'New_Subscribers' => 330,
                'Epic_Views' => 145000,
                'New_Epic_Views' => 241000,
                'Sangs_Note' => '2025/2/3'
            ],
                [
                'Channel_Link' => 'UC097u1al_Q4rSwtn7KX12Eg',
                'Subscribers' => 370,
                'New_Subscribers' => 2432,
                'Epic_Views' => 147000,
                'New_Epic_Views' => 996000,
                'Sangs_Note' => '2025/2/3'
            ],
                [
                'Channel_Link' => 'UCybnOFoVmeje9-WKMG08vUw',
                'Subscribers' => 831,
                'New_Subscribers' => 6000,
                'Epic_Views' => 287000,
                'New_Epic_Views' => 2100000,
                'Sangs_Note' => '2025/2/3'
            ]
        ];
// </editor-fold>

        $datas = AccountInfo::where("epid_status", "approved")->get();
        foreach ($datas as $data) {
            foreach ($dataChannel as $dt) {
                if ($data->chanel_id == $dt['Channel_Link']) {
                    error_log($data->chanel_id);
                    $data->epid_time = strtotime($dt['Sangs_Note']);
                    $data->setExtraValue("sub_approved", $dt['Subscribers']);
                    $data->setExtraValue("view_approved", $dt['Epic_Views']);
                    $data->subscriber_count = $dt['New_Subscribers'];
                    $data->view_count = $dt['New_Epic_Views'];
                    $data->calculateRewards();
                    break;
                }
            }
        }
    }

    public function epidAw() {

        $channelData = [
                ["channel_id" => "UC8OxbSlbE-eFk53QS06EgNA", "aw" => "L3_Boom_Epic", "money" => 300000, "mooncoin" => 2],
                ["channel_id" => "UCTx2NDbKyCNPWmjBvuMdRCA", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCTvR8q5-IxGu8pSWCi7Owqg", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCoPbdk1mzH7pOaVTaMatzGQ", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCLJQWOVbX6i0xZXJolLrTiA", "aw" => "L2_Boom_Epic", "money" => 100000, "mooncoin" => 1],
                ["channel_id" => "UCe0qV7yQE11x-DhmOeVvY5g", "aw" => "L2_Boom_Epic", "money" => 100000, "mooncoin" => 1],
                ["channel_id" => "UCQOOPCuMJR5rL7BcDKDkxYw", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCDJtFK5VDmN7AJ6uTtbzXwA", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCln37Gv1LV-CTN6At55iwRg", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCiU6eJYi81RNHjPz8_Fhc_Q", "aw" => "L2_Boom_Epic", "money" => 100000, "mooncoin" => 1],
                ["channel_id" => "UCqgQXZx1cm0IfZw7jQMk_jQ", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCjv-bu_zou7bEXxe1G-cJhw", "aw" => "L2_Boom_Epic", "money" => 100000, "mooncoin" => 1],
                ["channel_id" => "UCWz_T6jAq7ThWpDdrZSSTVw", "aw" => "L2_Boom_Epic", "money" => 100000, "mooncoin" => 1],
                ["channel_id" => "UCnbdww-v_PX0vyGu4KOkSdg", "aw" => "L2_Boom_Epic", "money" => 100000, "mooncoin" => 1],
                ["channel_id" => "UCx6jjPvu_jD-d7nNrcExM3g", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCa0qEo7jU8vTs2R5YctTOvw", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCLK2ALYvglA-bqEyc8xXKhg", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC4a26M8KQ6hT5PFonXVpwhQ", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCYTjW1nrkTeKy6Uxq64TMZA", "aw" => "L2_Boom_Epic", "money" => 100000, "mooncoin" => 1],
                ["channel_id" => "UCXyLtVLrxGKmSitEML7q4zQ", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCZWLE6VkqVwHWbWzV3GqKNA", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCA6HMsLNDcyPGaewv-Ks-mg", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC3y6S3AwwcuXiZSgqi9JX5Q", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCqKg6IkCsZe_sw7fxkxTVsg", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC3aCnqydZuKq9scdocsVtZg", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UC3Eth-5ce1qbIe6q-DFcwHw", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCCwPaCdtvgOfxCi1OoD10dA", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC2zQ9lHJmaIMrkp9OXH67jg", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCk4yfRdRvzNhCQ-CjDAT1mw", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC2heBunvQsaG0UXibDnWxKw", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UC-v1Kmnlvk2xw_UThIi3TwA", "aw" => "L2_Boom_Epic", "money" => 100000, "mooncoin" => 1],
                ["channel_id" => "UC4u86Spq9k_g24JOHPSTB5Q", "aw" => "L6_Boom_Epic", "money" => 700000, "mooncoin" => 3.5],
                ["channel_id" => "UCWYCxK7I36ZeGV63DJ0VQSQ", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UC1LDRlCmaQFjPSN995TM9tA", "aw" => "L3_Boom_Epic", "money" => 200000, "mooncoin" => 2],
                ["channel_id" => "UCeQaCBMNJhev1masTyhIsDw", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCZ0jQKqzaq8V68cMyhN6OKw", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCZyNjrnsnVMrZvxeJTvuebA", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCloB3YxT6LFBI69TrAek2-Q", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC0elYfhrJV0LKuwfL40lGQw", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UC0XS-JILqXQJ6iRYCs-Escw", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UC3C6g_2n0oQhRKchReaunCA", "aw" => "L2_Boom_Epic", "money" => 100000, "mooncoin" => 1],
                ["channel_id" => "UC1BbTzGj4FR0URpYFzDIjeg", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC_Htdt1x86ofJpIQwBmCUVg", "aw" => "L3_Boom_Epic", "money" => 200000, "mooncoin" => 2],
                ["channel_id" => "UC15OhsmxSiqIMVIBBRLdj2w", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC2q5JYtt-5w81js8FHrL9xw", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCUkNo6mCHgDxjSADWdK_Uaw", "aw" => "L4_Boom_Epic", "money" => 300000, "mooncoin" => 2.5],
                ["channel_id" => "UCWjFhYRNqYL5vZl2u1xoHVw", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCgfe-B_lEB7Im5ISo7c-A2Q", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC0AU0a1_mDIrrtOuPQxOCgQ", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC7h3oznMrHXlBamKRL0BTwA", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC4TBr5346N7LsuXR3cT9b-Q", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCLRYYDxqqdmN5HHLrIndIBA", "aw" => "L1_Boom_Epic", "money" => 50000, "mooncoin" => 0.5],
                ["channel_id" => "UCTjlh5ea9Xb5AFH5GMfuWKQ", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCLCKBBjvArDHsN-yCvGxbkg", "aw" => "L3_Boom_Epic", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC2hqF4zt3WvlkZDP97kgleA", "aw" => "L1_Boom_Epic", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC1sP_ZiVDh2TrKBHqPWTm0A", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCJFH6SZ0347g4KKYSFJz66Q", "aw" => "L2_Boom_Epic", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCfwi7wmNbSCqG52vuLwuw3w", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCZoxUZM9A3SVS04_4ovxzTg", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCbEkZ-_iN9uoPOWbeTc8LTw", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC_xHDkU-ZBKs2a7f_R1qKvg", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCGqXW7qPhqFiD0EWorDwU7g", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCTQHeqX6gGH8dbPEGNbSC3g", "aw" => "L1_Boom_Epic", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UClqejVr4b7oUQeWB4glz9Dg", "aw" => "L1_Boom_Epic", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCj950CUDgAZak8PE9OXInnw", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UC097u1al_Q4rSwtn7KX12Eg", "aw" => "Không Đạt KPI", "money" => 0, "mooncoin" => 0],
                ["channel_id" => "UCybnOFoVmeje9-WKMG08vUw", "aw" => "L1_Boom_Epic", "money" => 0, "mooncoin" => 0]
        ];
        foreach ($channelData as $idx => $data) {
            $accountInfo = AccountInfo::where("chanel_id", $data["channel_id"])->first();
            $json = $accountInfo->epid_extra_data;
            $aw = $data["aw"];
            $moonCoin = MooncoinContent::where("content_description", $aw)->where("status", 1)->first();
            if ($moonCoin) {
                $json["rewards"]["id"] = $moonCoin->id;
                $json["rewards"]["name"] = $moonCoin->content_description;
                $json["rewards"]["timestamp"] = time();
                $json["rewards"]["time"] = Utils::timeToStringGmT7(time());
                $json["rewards"]["subs"] = $accountInfo->subscriber_count;
                $json["rewards"]["views"] = $accountInfo->view_count;
                if ($data["mooncoin"] == 0) {
                    $json["rewards"]["moon_coin"] = $moonCoin->moon_value;
                    $json["rewards"]["cash"] = $moonCoin->money;
                    $json["rewards_given"] = false;
                } else {
                    $json["rewards"]["moon_coin"] = $data["mooncoin"];
                    $json["rewards"]["cash"] = $data["money"];
                    $json["rewards_given"] = true;
                }
            } else {
                $json["rewards"]["id"] = 0;
                $json["rewards"]["name"] = "KPI is not achieved";
                $json["rewards"]["moon_coin"] = 0;
                $json["rewards"]["cash"] = 0;
                $json["rewards"]["timestamp"] = time();
                $json["rewards"]["time"] = Utils::timeToStringGmT7(time());
                $json["rewards"]["subs"] = $accountInfo->subscriber_count;
                $json["rewards"]["views"] = $accountInfo->view_count;
                $json["rewards_given"] = false;
            }
            $accountInfo->epid_extra_data = $json;
            $accountInfo->save();
            error_log("$idx $accountInfo->id");
        }
    }

    public function updateUserToHub() {
        $users = ["truongpv_1515486846"
            , "hoadev_1492490931"
            , "huymusic_1527129950"
            , "manhmusic_1554824317"
            , "sangmusic_1568953186"
            , "hiepmusic_1596599107"
            , "ketmusic_1596599202"
            , "quynhanhmusic_1607051529"
            , "nhungmusic_1607051554"
            , "thuymusic_1607051628"
            , "jamesmusic_1638329193"
            , "quocgiangmusic_1649304730"
            , "darrell_1651207695"
            , "tungtt_1659410238"
            , "chrismusic_1638329193"
            , "hieumusic_1527129950"
            , "uyenmusic_1527129950"
            , "yenmusic_1527129950"
            , "nhimusic_1527129950"
            , "hanmusic_1527129950"
            , "hangmusic_1527129950"];
        $channels = AccountInfo::where("del_status", 1)->whereRaw("chanel_id NOT LIKE ?", ['%@%'])->whereNotNull("gologin")->whereIn("user_name", $users)->get();
        $total = count($channels);
        $i = 0;
        foreach ($channels as $channel) {
            $i++;
            $username = Utils::getUserFromUserCode($channel->user_name);
            $url = "http://api-magicframe.automusic.win/hub/fix-update/$channel->chanel_id/$username";
            error_log("$i/$total $url");
            RequestHelper::callAPI2("GET", $url, []);
        }
    }

    public function updateUserToArtistSystem() {

        try {
            // Lấy danh sách users từ bảng users
            $users = DB::table('users')
                    ->select('user_name as email', 'password_plaintext as password')
                    ->where('status', '1')
                    ->where('role', 'LIKE', '%26%')
                    ->where('description', '=', 'admin')
                    ->get();

            if ($users->isEmpty()) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'No users found in database',
                            'data' => null
                ]);
            }

            $results = [];
            $successCount = 0;
            $errorCount = 0;

            foreach ($users as $user) {
                try {
                    // Chuẩn bị data để gửi
                    $postData = json_encode([
                        'email' => "$user->email" . "@moonshots.vn",
                        'password' => $user->password // Hoặc password mặc định nếu cần
                    ]);

                    // Khởi tạo cURL
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://d76c-27-66-179-222.ngrok-free.app/api/signup',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30, // Tăng timeout cho an toàn
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $postData,
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json'
                        ),
                    ));

                    $response = curl_exec($curl);
                    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    $error = curl_error($curl);
                    curl_close($curl);

                    if ($error) {
                        Log::error("CURL Error for {$user->email}: " . $error);
                        $results[] = [
                            'email' => $user->email,
                            'status' => 'error',
                            'message' => 'CURL Error: ' . $error
                        ];
                        $errorCount++;
                    } else {
                        $responseData = json_decode($response, true);

                        if ($httpCode >= 200 && $httpCode < 300) {
                            error_log("Success signup for {$user->email}");
                            $results[] = [
                                'email' => $user->email,
                                'status' => 'success',
                                'message' => 'User synced successfully',
                                'response' => $responseData
                            ];
                            $successCount++;
                        } else {
                            Log::error("API Error for {$user->email}: HTTP {$httpCode} - " . $response);
                            $results[] = [
                                'email' => $user->email,
                                'status' => 'error',
                                'message' => "HTTP {$httpCode}: " . ($responseData['message'] ?? 'Unknown error'),
                                'response' => $responseData
                            ];
                            $errorCount++;
                        }
                    }

                    // Tạm dừng giữa các request để tránh spam API
                    usleep(500000); // 0.5 giây
                } catch (Exception $e) {
                    Log::error("Exception for {$user->email}: " . $e->getMessage());
                    $results[] = [
                        'email' => $user->email,
                        'status' => 'error',
                        'message' => 'Exception: ' . $e->getMessage()
                    ];
                    $errorCount++;
                }
            }

            return response()->json([
                        'status' => 'completed',
                        'message' => "Sync completed. Success: {$successCount}, Errors: {$errorCount}",
                        'summary' => [
                            'total_users' => count($users),
                            'success_count' => $successCount,
                            'error_count' => $errorCount
                        ],
                        'details' => $results
            ]);
        } catch (Exception $e) {
            Log::error("syncUsersToExternalSystem error: " . $e->getMessage());
            return response()->json([
                        'status' => 'error',
                        'message' => 'Error processing sync: ' . $e->getMessage(),
                        'data' => null
            ]);
        }
    }

    public function testSunoLyric($id) {
        $bom = new BomController();
        return $bom->getSunoLyrics($id);
    }

    public function test() {
        $data = Bom::where("id", 24089)->first();
        error_log("makeLyricTimestamp $data->id saved lyric_text to https://cdn.soundhex.com/api/v1/timestamp/$data->local_id");
        $lyricPro = json_decode($data->lyric_pro);
        $lyricSyncText = (json_encode($lyricPro->lyricSync));
        $lyricText = "";
        foreach ($lyricPro->lyricSync as $line) {
            $lyricText .= $line->line . PHP_EOL;
        }
        $dataCdn = (object) [
                    "lyric" => $lyricText,
                    "lyric_sync" => $lyricSyncText,
                    "id" => $data->local_id
        ];
        error_log("dataCdn " . json_encode($dataCdn));
        $rs = RequestHelper::callAPI2("PUT", "https://cdn.soundhex.com/api/v1/timestamp/$data->local_id/", $dataCdn, array('Content-Type: application/json'), 10000);
        if (isset($rs->id)) {
            $data->is_real_lyric = 1;
            $data->save();
        }
        error_log("rs Cnd " . json_encode($rs));
    }

}
