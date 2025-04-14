<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class BomGroups extends Model {

    use Sortable;

    protected $table = "bom_groups";
    public $timestamps = false;

}
