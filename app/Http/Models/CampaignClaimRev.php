<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CampaignClaimRev extends Model {

    use Sortable;

    protected $table = "campaign_claim_rev";
    public $timestamps = false;

}
