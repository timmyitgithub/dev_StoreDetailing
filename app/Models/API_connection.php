<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class API_connection extends Model
{
    use HasFactory;
    protected $table = 'a_p_i_connections';
    protected $fillable = [
        'connection_name',
        'type_auth',
        'link_connect',
        'client_id',
        'token',
        'scopes',
        'retailer',
        'active',
    ];
}
