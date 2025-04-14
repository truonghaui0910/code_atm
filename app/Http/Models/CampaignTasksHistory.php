<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CampaignTasksHistory extends Model {

    use Sortable;

    protected $table = "campaign_tasks_history";
    public $timestamps = false;

}
