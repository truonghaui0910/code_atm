<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class MusicSource extends Model {

    use Sortable;

    protected $table = "music_source";
    public $timestamps = false;


}
