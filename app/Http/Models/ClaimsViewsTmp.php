<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ClaimsViewsTmp extends Model {

    use Sortable;

    protected $table = "claims_views_tmp";
    public $timestamps = false;

}
