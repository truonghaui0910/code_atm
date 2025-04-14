<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Noclaim extends Model {

    use Sortable;

    protected $table = "noclaim";
    public $timestamps = false;

}
