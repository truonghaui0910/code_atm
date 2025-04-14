<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Moonaz extends Model {

    use Sortable;

    protected $table = "moonaz";
    public $timestamps = false;
//    public $sortable = ["created", "clicked"];

}
