<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ClaimsViews extends Model {

    use Sortable;

    protected $table = "claims_views";
    public $timestamps = false;

}
