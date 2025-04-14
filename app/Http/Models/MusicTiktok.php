<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
class MusicTiktok extends Model {
    use Sortable;
    protected $table = "music_tiktok";
    public $timestamps = false;
//    protected $fillable = [ 'title' ];
    public $sortable = ['id','rank','pre_rank','change','velocity'];
    
}
