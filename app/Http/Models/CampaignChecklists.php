<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CampaignChecklists extends Model {

    use Sortable;

    protected $table = "campaign_checklists";
    public $timestamps = false;

}
