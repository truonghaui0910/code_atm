<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CampaignClaimRevTmp extends Model {

    use Sortable;

    protected $table = "campaign_claim_rev_tmp";
    public $timestamps = false;
    protected $fillable = ['artist', 'song_name', 'revenue', 'period', 'isrc'];

}
