<?php

namespace Vendor\Customer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'google_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // Don't cast password to 'hashed' - Passport needs raw hash value
            // Password should already be hashed when stored
        ];
    }

    /**
     * Get the user settings for the customer.
     */
    public function setting()
    {
        return $this->hasOne(CustomerSetting::class);
    }

    /**
     * Find the user instance for the given username (email).
     * Required by Passport password grant.
     * Must be static method.
     */
    public static function findForPassport($username)
    {
        return static::where('email', $username)->first();
    }

    /**
     * Validate the password for the given user.
     * Required by Passport password grant.
     */
    public function validateForPassportPasswordGrant($password)
    {
        // Check if user has password
        if (!$this->password) {
            return false;
        }

        // Get raw password value (before casting)
        $hashedPassword = $this->getOriginal('password') ?? $this->password;

        // Verify password using Hash facade
        return \Hash::check($password, $hashedPassword);
    }
}
