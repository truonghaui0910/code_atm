<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class VideoDaily extends Model {

    use Sortable;

    protected $table = "video_daily";
    public $timestamps = false;

}
