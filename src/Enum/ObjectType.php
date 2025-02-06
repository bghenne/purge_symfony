<?php

namespace App\Enum;

enum ObjectType : string
{
    case ELIGIBLE = 'ELIGIBLE';
    case PURGED = 'PURGEE';
    case ALERT = 'ALERT';

    public static function values(): array
    {
        $values = [];

        foreach (self::cases() as $case) {
            $values[$case->name] = $case->value;
        }

        return $values;
    }

}
