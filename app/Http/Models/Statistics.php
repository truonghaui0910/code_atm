<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Statistics extends Model {

    use Sortable;

    protected $table = "statistics";
    public $timestamps = false;

}
