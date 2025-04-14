<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class MSReports extends Model {

    use Sortable;

    protected $table = "ms_reports";
    public $timestamps = false;
    public $sortable = ["period"];

}
