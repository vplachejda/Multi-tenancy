<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'company_id'];

    public function company()
    {
        return $this->belongsTo(company::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
