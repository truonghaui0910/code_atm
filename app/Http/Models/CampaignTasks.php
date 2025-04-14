<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CampaignTasks extends Model {

    use Sortable;

    protected $table = "campaign_tasks";
    public $timestamps = false;

}
