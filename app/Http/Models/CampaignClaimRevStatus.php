<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CampaignClaimRevStatus extends Model {

    use Sortable;

    protected $table = "campaign_claim_rev_status";
    public $timestamps = false;

}
