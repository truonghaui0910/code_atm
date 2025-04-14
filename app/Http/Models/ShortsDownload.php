<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ShortsDownload extends Model {

    use Sortable;

    protected $table = "shorts_download";
    public $timestamps = false;
    public $sortable = ['id','views'];

}
