<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Strikes extends Model {

    use Sortable;

    protected $table = "strikes";
    public $timestamps = false;


}
