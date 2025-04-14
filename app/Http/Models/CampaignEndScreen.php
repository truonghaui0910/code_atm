<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CampaignEndScreen extends Model {

    use Sortable;

    protected $table = "campaign_endscreen";
    public $timestamps = false;

}
