<?php

namespace App\Enums;

enum StatusEnum: int
{
    case Active = 1;
    case Inactive = 2;

    public function label(): string
    {
        return match($this) {
            self::Active => 'Activo',
            self::Inactive => 'Inactivo',
        };
    }
}
