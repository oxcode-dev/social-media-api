<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Notifications\ResetPasswordNotification;

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
        'bio',
        'avatar',
    ];

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

    public static function search($query)
    {
        $relations = ['products', 'addresses', 'orders'];

        return empty($query) ? static::query()->with($relations)
            : static::with($relations)
                ->where('first_name', 'like', '%'.$query.'%')
                ->where('last_name', 'like', '%'.$query.'%')
                // ->orWhere('source', 'like', '%'.$query.'%')
                ->orWhere('email', 'like', '%'.$query.'%');
    }


    public function generatePin($digits = 4): string
    {
        $i = 0; //counter
        $pin = ''; //our default pin is blank.

        while ($i < $digits) {
            //generate a random number between 0 and 9.
            $pin .= random_int(0, 9);
            $i++;
        }

        return $pin;
    }

    public function sendNewUserNotification($token = null)
    {
        $result = $this->generatePin(5);

        $this['otp'] = $result;

        // $this->notify(new NewUserNotification($this));

        OtpCode::where('email', $this->email)->delete();

        OtpCode::create([
            'code' => $result,
            'email' => $this->email,
            'expires_at' => now()->addMinutes(5),
        ]);

        return $result;
    }

    public function sendPasswordResetNotification($token = null)
    {
        $result = $this->generatePin(5);

        $this->notify(new ResetPasswordNotification($result));

        OtpCode::where('email', $this->email)->delete();

        OtpCode::create([
            'code' => $result,
            'email' => $this->email,
            'expires_at' => now()->addMinutes(5),
        ]);

        return $result;
    }
}
