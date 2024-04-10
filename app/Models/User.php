<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'last_name',
        'middle_name',
        'dob',
        'balance',
        'commission',
        'tx_pin',
        'username',
        'email',
        'phone',
        'country',
        'state',
        'local_gov',
        'address',
        'bvn',
        'nin',
        'status',
        'isAggregator',
        'role',
        'gender',
        'image',
        'device_model',
        'device_id',
        'email_verified_at',
        'bvn_verified_at',
        'password',
        // You don't need to include remember_token and timestamps as they are automatically handled by Eloquent.
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'nin',
        'bvn','tx_pin'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'bvn_verified_at'=>'date',
        'password' => 'hashed',
    ];
}