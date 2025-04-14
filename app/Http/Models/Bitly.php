<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Bitly extends Model {

    use Sortable;

    protected $table = "bitly";
    public $timestamps = false;
    public $sortable = ["created", "clicked"];

}
