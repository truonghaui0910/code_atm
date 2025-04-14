<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class OauthGoogle extends Model {

    use Sortable;

//    protected $table = "oauth_google";
    protected $table = "oauthtable";
    public $timestamps = false;
    public $sortable = ['id', 'client_id', 'status', 'create_time', 'count'];

}
