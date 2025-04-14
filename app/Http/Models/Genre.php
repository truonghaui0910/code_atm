<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Genre extends Model {

    use Sortable;

    protected $table = "genre";
    public $timestamps = false;

}
