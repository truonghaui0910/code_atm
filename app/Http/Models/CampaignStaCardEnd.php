<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CampaignStaCardEnd extends Model {

    use Sortable;

    protected $table = "campaign_sta_card_end";
    public $timestamps = false;

}
