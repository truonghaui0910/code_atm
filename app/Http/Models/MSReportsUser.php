<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class MSReportsUser extends Model {

    use Sortable;

    protected $table = "ms_reports_user";
    public $timestamps = false;
    public $sortable = ["period"];

}
