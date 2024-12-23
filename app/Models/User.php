<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name', 'last_name', 'email', 'password',
        'permissions', 'trial_ends_at',
        'company_id', 'tenant_id', 'role_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    public function myStoryTrainings()
    {
        return $this->hasMany(MyStoryTraining::class);
    }

    public function learningPathMedals()
    {
        return $this->hasMany(LearningPathMedal::class);
    }

    public function learningPathUsers()
    {
        return $this->hasMany(LearningPathUser::class);
    }

    public function rentedProductions()
    {
        return $this->hasMany(RentedProduction::class);
    }

    public function asyncSessions()
    {
        return $this->hasMany(AsyncSession::class);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByCompanyAndTenant($query, $companyId, $tenantId)
    {
        return $query->where('company_id', $companyId)->where('tenant_id', $tenantId);
    }
}
