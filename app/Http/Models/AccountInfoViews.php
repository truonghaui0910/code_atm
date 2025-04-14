<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class AccountInfoViews extends Model {

    use Sortable;

    protected $table = "accountinfo_views";
    public $timestamps = false;

}
