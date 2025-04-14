<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Notification extends Model {

    use Sortable;

    protected $table = "notification";
    public $timestamps = false;

}
