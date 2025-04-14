<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class TiktokPlaylist extends Model {

    use Sortable;

    protected $table = "tiktok_playlist";
    public $timestamps = false;

}
