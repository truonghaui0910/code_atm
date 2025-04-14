<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Tasks extends Model {

    use Sortable;

    protected $table = "tasks";
    public $timestamps = false;

}
