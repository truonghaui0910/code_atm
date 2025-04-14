<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
class SpotifyMusicPlaylist extends Model {
    use Sortable;
    protected $table = "spotifymusicplaylist";
    public $timestamps = false;
    

}
