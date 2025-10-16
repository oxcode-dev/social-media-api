<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasApiTokens, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'phone',
    ];

    // 'id' => fake()->uuid(),
    //         'username' => fake()->userName(),
    //         'first_name' => fake()->firstName(),
    //         'last_name' => fake()->lastName(),
    //         'email' => fake()->unique()->safeEmail(),
    //         'phone' => fake()->unique()->phoneNumber(),
    //         'bio' => fake()->realText(),
    //         'avatar' => fake()->imageUrl(),
    //         'email_verified_at' => now(),
    //         'password' => static::$password ??= 'password',
    //         'remember_token' => Str::random(10),
    //         'two_factor_secret' => Str::random(10),
    //         'two_factor_recovery_codes' => Str::random(10),
    //         'two_factor_confirmed_at' => now(),

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }
}
