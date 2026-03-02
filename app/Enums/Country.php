<?php

namespace App\Enums;

enum Country: string
{
    case Togo = 'togo';
    case Congo = 'congo';
    case CentralAfrica = 'centrafrique';

    public function label(): string
    {
        return match ($this) {
            self::Togo => 'Togo',
            self::Congo => 'Congo',
            self::CentralAfrica => 'Centrafrique',
        };
    }

    public function flag(): string
    {
        return match ($this) {
            self::Togo => '🇹🇬',
            self::Congo => '🇨🇬',
            self::CentralAfrica => '🇨🇫',
        };
    }

    public static function options(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }
}
