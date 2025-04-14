<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class TiktokCharts extends Model {

    use Sortable;

    protected $table = "tiktok_chart";
    public $timestamps = false;
    public $sortable = ['id','time_on_chart','day_report','rank_c_us','rank_c_gb','rank_c_ca','rank_c_mx','rank_c_au','rank_c_jp','rank_c_es',
        'rank_c_fr','rank_c_de','rank_c_kr','rank_c_br','rank_c_in','rank_c_id','rank_c_it','rank_c_nl','rank_c_ph','rank_c_ru','rank_c_se',
        'rank_c_th','rank_c_tr','rank_c_vn'];

}
