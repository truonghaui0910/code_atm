<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ChannelTags extends Model {

    use Sortable;

    protected $table = "channel_tags";
    public $timestamps = false;

}
