<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class BitlyGroup extends Model {

    use Sortable;

    protected $table = "bitly_group";
    public $timestamps = false;

}
