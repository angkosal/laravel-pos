<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'logo_path',
        'description',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function products(){
        return $this->hasMany(Product::class);
    }
}
