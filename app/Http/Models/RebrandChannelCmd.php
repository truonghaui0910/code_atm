<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
class RebrandChannelCmd extends Model {
    use Sortable;
    protected $table = "rebrand_channel_cmd";
    public $timestamps = false;    
}
