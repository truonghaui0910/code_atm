<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Z1VideoClaim extends Model {

    use Sortable;

    protected $table = "z1_video_claim";
    public $timestamps = false;
    public $sortable = ['id', 'views', 'status'];

}
