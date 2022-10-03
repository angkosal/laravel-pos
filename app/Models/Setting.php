<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'pos_settings';

    protected $fillable = [
        'key',
        'value',
    ];
}
