<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class BrandManager extends Model {

    use Sortable;

    protected $table = "rebrand_manager";
    public $timestamps = false;
     public $sortable = ['id'];

}
