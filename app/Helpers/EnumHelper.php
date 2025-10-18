<?php

namespace App\Helpers;

class EnumHelper
{
    public static function toArray(string $enumClass): array
    {
        if (!enum_exists($enumClass)) {
            return [];
        }

        return array_map(fn($case) => [
            'name' => $case->name,
            'value' => $case->value,
        ], $enumClass::cases());
    }
}
