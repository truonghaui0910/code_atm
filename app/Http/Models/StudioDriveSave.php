<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class StudioDriveSave extends Model {

    use Sortable;

    protected $table = "studio_drive_save";
    public $timestamps = false;

}
