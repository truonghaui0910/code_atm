<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class AthenaTableauReport extends Model {

    use Sortable;

    protected $table = "athena_tableau_report";
    public $timestamps = false;
    public $sortable = [];

}
