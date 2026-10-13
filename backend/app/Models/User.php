<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\RoleNameEnum;
use App\Traits\UserStampsTrait;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['role_id', 'name', 'username', 'email', 'password', 'is_locked', 'created_by', 'updated_by'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, UserStampsTrait;

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
            'is_locked' => 'boolean',
        ];
    }

    /**
     * Get the role associated with the user.
     *
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Determine if access is permitted.
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        return (bool) $this->is_locked;
    }

    /**
     * Determine if the user has one of the given roles.
     *
     * @param RoleNameEnum ...$roles
     * @return bool
     */
    public function hasRole(RoleNameEnum ...$roles): bool
    {
        if (!$this->role) {
            return false;
        }

        $roleNames = array_map(
            static fn(RoleNameEnum $role) => $role->value,
            $roles
        );

        return in_array($this->role->code, $roleNames, true);
    }
}
