<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CampaignJobs extends Model {

    use Sortable;

    protected $table = "campaign_jobs";
    public $timestamps = false;

}
