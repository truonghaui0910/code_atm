<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
class MusicStorage extends Model {
    use Sortable;
    protected $table = "music_storage";
    public $timestamps = false;
    protected $fillable = ['topic', 'artists', 'title', 'link'];    
}
