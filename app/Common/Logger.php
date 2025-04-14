<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Locker
 *
 * @author hoabt2
 */

namespace App\Common;

class Logger {

    public static function logUpload($message) {
        error_log('[' . Utils::timeToStringGmT7(time()) . "] [pid " . getmypid() . "] " . $message . PHP_EOL, 3, "/home/automusic.win/log/error-upload.log");
    }

}
