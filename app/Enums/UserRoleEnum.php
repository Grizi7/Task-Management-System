<?php
namespace App\Enums;

enum UserRoleEnum: int
{
    case user = 0;
    case manager = 1;

    public function label(): string
    {
        return match ($this) {
            UserRoleEnum::user => 'user',
            UserRoleEnum::manager => 'manager',
        };
    }
}
