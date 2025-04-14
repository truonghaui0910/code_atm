<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class MusicHexa extends Model {

    use Sortable;

    protected $table = "music_hexa";
    public $timestamps = false;

}
