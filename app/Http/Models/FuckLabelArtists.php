<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class FuckLabelArtists extends Model {

    use Sortable;

    protected $table = "fuck_label_artists";
    public $timestamps = false;

}
