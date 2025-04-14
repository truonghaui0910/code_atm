<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Intro extends Model {

    use Sortable;

    protected $table = "intro";
    public $timestamps = false;

}
