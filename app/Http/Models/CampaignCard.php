<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CampaignCard extends Model {

    use Sortable;

    protected $table = "campaign_card";
    public $timestamps = false;

}
