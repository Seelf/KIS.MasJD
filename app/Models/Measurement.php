<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    protected $fillable = [
        'node_id',
        'key',
        'value',
        'info_timestamp',
        'message_timestamp',
        'message_type',
        'json_type',
    ];

    protected $casts = [
        'info_timestamp' => 'datetime',
        'message_timestamp' => 'datetime',
    ];
}
