<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
class Spotifymusic extends Model {
    use Sortable;
    protected $table = "spotifymusic";
    public $timestamps = false;
//    protected $fillable = [ 'title' ];
//    public $sortable = ['id','chanel_name', 'note', 'video_count', 
//        'subscriber_count','view_count','increasing','status','cool_down','last_execute_time','number_video_success','chanel_create_date','status_oauth','group_channel_id'];
    

}
