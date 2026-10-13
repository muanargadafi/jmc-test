<?php

namespace App\Enums;

use Illuminate\Support\Str;

/**
 * Enum RoleNameEnum
 *
 * Represents predefined role names/identifiers available within the application.
 */
enum RoleNameEnum: string
{
    case ADMIN = 'admin';
    case OPERATOR = 'operator';

    /**
     * Get a human-readable title for the role name value.
     *
     * @return string
     */
    public function title(): string
    {
        return Str::headline($this->value);
    }
}
