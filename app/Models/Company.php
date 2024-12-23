<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
