<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CardEndsConfig extends Model {

    use Sortable;

    protected $table = "card_ends_config";
    public $timestamps = false;

}
