<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class BomAlbum extends Model {

    use Sortable;

    protected $table = "bom_albums";
    public $timestamps = false;

}
