<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportCSVHistory extends Model
{
    protected $table = 'import_csv_histories';
    public $timestamps = false;

    protected $fillable = [
        'file_name',
        'row_count',
        'imported_at',
    ];
}
