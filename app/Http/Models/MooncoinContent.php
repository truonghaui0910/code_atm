<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class MooncoinContent extends Model {

    use Sortable;

    protected $table = "mooncoin_content";
    public $timestamps = false;

}
