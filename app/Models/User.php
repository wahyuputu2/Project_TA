<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Tambahkan method ini ke app/Models/User.php

public function store()
{
    return $this->hasOne(Store::class);
}

public function isSeller(): bool
{
    return $this->role === 'seller';
}

public function isSuperAdmin(): bool
{
    return $this->role === 'superadmin';
}

public function hasStore(): bool
{
    return $this->store !== null;
}

}