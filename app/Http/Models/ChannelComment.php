<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ChannelComment extends Model {

    use Sortable;

    protected $table = "channel_comment";
    public $timestamps = false;

}
