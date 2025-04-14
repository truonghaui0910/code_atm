<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class AthenaTableau extends Model {

    use Sortable;

    protected $table = "athena_tableau";
    public $timestamps = false;
    public $sortable = ['views', 'views_athena', 'suggested_traffic_views', 'watch_time_hours','video_type'];

}
