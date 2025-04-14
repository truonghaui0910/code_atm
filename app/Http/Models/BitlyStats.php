<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class BitlyStats extends Model {

    use Sortable;

    protected $table = "bitly_stats";
    public $timestamps = false;

}
