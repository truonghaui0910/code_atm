<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class BomArtist extends Model {

    use Sortable;

    protected $table = "bom_artists";
    public $timestamps = false;

}
