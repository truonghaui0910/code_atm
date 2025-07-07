<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class AccountInfoMaking extends Model {

    use Sortable;

    protected $table = "accountinfo_making";
    public $timestamps = false;

}
