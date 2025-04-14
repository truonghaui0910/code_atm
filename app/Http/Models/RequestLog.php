<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class RequestLog extends Model {

    use Sortable;

    protected $table = "request_log";
    public $timestamps = false;

}
