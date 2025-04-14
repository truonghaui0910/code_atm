<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Campaign extends Model {

    use Sortable;

    protected $table = "campaign";
    public $timestamps = false;
    public $sortable = ['username'];

}
