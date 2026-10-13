<?php

namespace App\Enums;

use Illuminate\Support\Str;

/**
 * Enum AbilityEnum
 *
 * Represents predefined abilities/permissions available within the application.
 */
enum AbilityEnum: string
{
    case BARANG_MASUK_MANAGE = 'barang-masuk.manage';
    case MASTER_DATA_MANAGE = 'master-data.manage';
    case USERS_MANAGE = 'users.manage';

    /**
     * Get a human-readable title for the ability value.
     *
     * @return string
     */
    public function title(): string
    {
        return Str::headline($this->value);
    }
}
