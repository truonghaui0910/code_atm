<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class RebrandChannel extends Model {

    use Sortable;

    protected $table = "rebrand_channel";
    public $timestamps = false;

}
