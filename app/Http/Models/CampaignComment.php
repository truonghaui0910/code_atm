<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CampaignComment extends Model {

    use Sortable;

    protected $table = "campaign_comment";
    public $timestamps = false;

}
