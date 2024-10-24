<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // Импортируйте интерфейс
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements MustVerifyEmail // Реализуйте интерфейс
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'products',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'products' => 'array', 
    ];
    public function chemicals()
    {
        return $this->belongsToMany(Chemical::class, 'chemical_user');
    }
    
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    /**
     * Automatically hash the password when setting it.
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    /**
     * Verify the user's password.
     */
    public function verifyPassword($password)
    {
        return Hash::check($password, $this->password);
    }

    /**
     * Generate a new token.
     */
    public function generateToken($name = 'token-name')
    {
        return $this->createToken($name)->plainTextToken;
    }

    /**
     * Mark the user's email address as verified.
     */
    public function markEmailAsVerified()
    {
        $this->email_verified_at = now();
        $this->save();
    }

    /**
     * Check if the user's email is verified.
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }
}
