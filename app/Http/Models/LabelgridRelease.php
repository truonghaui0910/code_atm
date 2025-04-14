<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class LabelgridRelease extends Model {

    use Sortable;

    protected $table = "labelgrid_release";
    public $timestamps = false;

}
