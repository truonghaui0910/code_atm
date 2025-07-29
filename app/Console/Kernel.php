<?php

namespace App\Console;

use App\Console\Commands\SunoLyricSync;
use App\Console\Commands\UpdateEmailCountsCommand;
use App\Http\Controllers\BomController;
use App\Http\Controllers\ChannelManagementController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UpdateEmailCountsCommand::class,
        SunoLyricSync::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('command:email_count')->everyFiveMinutes()->withoutOverlapping(10);
        $schedule->command('command:suno_lyric_sync')->everyFiveMinutes()->withoutOverlapping(10);
        $bom = new BomController();
        $bom->musicToText();
        $bom->makeLyricTimestamp();
        $accountInfo = new ChannelManagementController();
        $accountInfo->syncEmailFromMaking();
        $bom->syncToSoundhex();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands() {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

}
