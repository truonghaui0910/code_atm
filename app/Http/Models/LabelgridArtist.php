<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class LabelgridArtist extends Model {

    use Sortable;

    protected $table = "labelgrid_artist";
    public $timestamps = false;

}
