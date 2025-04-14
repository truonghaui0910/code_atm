<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [ 'downloader/update','/downloader/get-data','/channel/store','apilogin','api/*','/zip',
        '/api/video/delete','add/orfium/api','api/add/channel','/moonshots/analytic/update','/moonshots/alarm/add','callback/card',
        '/callback/brand','/callback/brandnew','/callback/upload','/callback/wakeup',
        '/callback/api','/callback/comment','callback/commentauto',
        '/callback/login','/callback/channel/create','/callback/fake','/callback/pass/change','/callback/*',
        'moonseo/notification/*','/studio/drive/*','/ld/*','/bom/spotify/*','/album/status/update'
        //
    ];
}
