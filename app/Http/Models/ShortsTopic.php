<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ShortsTopic extends Model {

    use Sortable;

    protected $table = "shorts_topic";
    public $timestamps = false;
    public $sortable = ['id'];

}
