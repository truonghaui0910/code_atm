<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class TiktokPlaylistSong extends Model {

    use Sortable;

    protected $table = "tiktok_playlist_songs";
    public $timestamps = false;

}
