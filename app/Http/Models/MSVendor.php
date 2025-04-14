<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class MSVendor extends Model {

    use Sortable;

    protected $table = "ms_vendor";
    public $timestamps = false;

}
