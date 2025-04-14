<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class MooncoinValues extends Model {

    use Sortable;

    protected $table = "mooncoin_values";
    public $timestamps = false;

}
