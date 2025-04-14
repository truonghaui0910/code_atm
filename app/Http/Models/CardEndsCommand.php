<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CardEndsCommand extends Model {

    use Sortable;

    protected $table = "card_ends_command";
    public $timestamps = false;

}
