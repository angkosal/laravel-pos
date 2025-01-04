<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
  
    use HasFactory;
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'avatar',
        'user_id',
    ];

    public function getAvatarUrl()
    {
        return Storage::url($this->avatar);
    }
}
