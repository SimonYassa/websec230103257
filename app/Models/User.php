<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'credit',
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
        'credit' => 'float',
    ];

    /**
     * Get all purchases made by the user
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Add credit to user account
     */
    public function addCredit($amount)
    {
        $this->credit += $amount;
        $this->save();
        return $this->credit;
    }



    /**
     * Check if user has enough credit
     */
    public function hasEnoughCredit($amount)
    {
        return $this->credit >= $amount;
    }

    /**
     * Deduct credit from user account
     */
    public function deductCredit($amount)
    {
        if (!$this->hasEnoughCredit($amount)) {
            return false;
        }
        
        $this->credit -= $amount;
        $this->save();
        return true;
    }

    /**
     * Check if user is a customer
     */
    public function isCustomer()
    {
        return $this->hasRole('Customer');
    }

    /**
     * Check if user is an employee
     */
    public function isEmployee()
    {
        return $this->hasRole('Employee');
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }
}

