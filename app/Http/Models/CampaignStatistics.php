<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CampaignStatistics extends Model {

    use Sortable;

    protected $table = "campaign_statistics";
    public $timestamps = false;
    public $sortable = ['id', 'campaign_name','genre','views','views_official','views_lyric','views_compi','views_tiktok','wake_position','campaign_start_date'];

}
