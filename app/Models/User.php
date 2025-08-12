<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int|null $skpd_id
 * @property-read array $metadata
 * @property-read \Illuminate\Support\Collection $roles
 *
 * @method bool hasRole(string|array|\Spatie\Permission\Contracts\Role $roles, ?string $guard = null)
 * @method bool hasAnyRole(array|string ...$roles)
 * @method bool hasAllRoles(array|string $roles)
 * @method \Illuminate\Support\Collection getRoleNames()
 * @method \Illuminate\Support\Collection getPermissionNames()
 * @method bool hasPermissionTo(string|int|\Spatie\Permission\Contracts\Permission $permission, ?string $guardName = null)
 * @mixin \Spatie\Permission\Traits\HasRoles
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'skpd_id',
        'metadata'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
            'metadata' => 'array',
        ];
    }



    public function getMetadataAttribute($value)
    {
        if (is_null($value))
            return [];
        return json_decode($value, true) ?: [];
    }

    public function skpd()
    {
        return $this->belongsTo(MasterSKPD::class, 'skpd_id');
    }
}
