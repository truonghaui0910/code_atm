<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
class AccountReup extends Model {
    use Sortable;
    protected $table = "accountreup";
    public $timestamps = false;
//    protected $fillable = [ 'title' ];
//    public $sortable = ['id','channel_name', 'original_url', 'url', 
//        'total_video','video_from','video_to','total_filter_video','cool_down','last_execute_time','number_video_success'];
    

}
