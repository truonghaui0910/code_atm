<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class AutoWakeupHappy extends Model {

    use Sortable;

    protected $table = "auto_wakeup_happy";
    public $timestamps = false;

}
