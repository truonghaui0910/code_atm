<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class MusicConfig extends Model {

    use Sortable;

    protected $table = "music_config";
    public $timestamps = false;
    public $sortable = ['id', 'channel_name', 'last_execute_time', 'choose_video_number', 'status'];

}
