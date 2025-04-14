<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class VideoInfo extends Model {

    use Sortable;

    protected $table = "video_info";
    public $timestamps = false;

}
