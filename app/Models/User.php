<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'reset_token',
        'role_id',
        'parent_id',
        'phone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['full_name'];


    public function getFullNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function children() { 
        return $this->hasMany(Self::class, 'parent_id', 'id'); 
    }

    public function parent() { 
        return $this->belongsTo(Self::class, 'parent_id', 'id'); 
    }

    public function bank() {
        return $this->belongsTo(Bank::class);
    }
}
