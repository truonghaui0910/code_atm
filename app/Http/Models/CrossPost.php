<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CrossPost extends Model {

    use Sortable;

    protected $table = "cross_post";
    public $timestamps = false;
    public $sortable = ['id','views','daily_views','publish_date'];

}
