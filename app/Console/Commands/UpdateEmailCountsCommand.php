<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Log;

class UpdateEmailCountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:email_count';

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
//            Log::info('Commands UpdateEmailCounts job started at: ' . Carbon::now('Asia/Ho_Chi_Minh'));
            
            // Bước 1: Lấy email counts từ account_info
            $emailCounts = DB::table('accountinfo')
                ->where('del_status', 0)
                ->select('note as email')
                ->selectRaw('COUNT(*) as channel_count')
                ->whereNotNull("otp_key")
                ->whereNotNull("note")
                ->groupBy('note')
                ->get();

//            Log::info('Commands Found ' . $emailCounts->count() . ' unique emails');

            // Bước 2: Truncate bảng cũ
            DB::table('accountinfo_email_count')->truncate();

            // Bước 3: Insert batch để tối ưu performance
            $batchSize = 1000;
            $chunks = $emailCounts->chunk($batchSize);

            foreach ($chunks as $chunk) {
                $data = $chunk->map(function($item) {
                    return [
                        'email' => $item->email,
                        'channel_count' => $item->channel_count,
                        'updated_at' => Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString()
                    ];
                })->toArray();

                DB::table('accountinfo_email_count')->insert($data);
            }

            Log::info('Commands UpdateEmailCounts job completed successfully at: ' . Carbon::now('Asia/Ho_Chi_Minh'));
            
        } catch (Exception $e) {
            Log::error('Commands UpdateEmailCounts job failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
