<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Bom extends Model {

    use Sortable;

    protected $table = "bom";
    public $timestamps = false;

}
