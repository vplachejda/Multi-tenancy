<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentedProduction extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'rental_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
