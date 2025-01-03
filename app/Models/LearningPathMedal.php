<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPathMedal extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'medal_name', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
