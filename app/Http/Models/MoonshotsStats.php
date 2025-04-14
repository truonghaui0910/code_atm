<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class MoonshotsStats extends Model {

    use Sortable;

    protected $table = "moonshots_stats";
    public $timestamps = false;

}
