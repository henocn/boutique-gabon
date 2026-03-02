<?php

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case Remind = 'remind';
    case Unreachable = 'unreachable';
    case Processing = 'processing';
    case Processed = 'processed';
    case Validated = 'validated';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::New => 'Nouvelle',
            self::Remind => 'À rappeler',
            self::Unreachable => 'Injoignable',
            self::Processing => 'Programmée',
            self::Processed => 'Traitée',
            self::Validated => 'Validée',
            self::Delivered => 'Livrée',
            self::Cancelled => 'Annulée',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::New => 'bg-danger',
            self::Remind => 'bg-warning',
            self::Unreachable => 'bg-secondary',
            self::Processing => 'bg-info',
            self::Processed => 'bg-success',
            self::Validated => 'bg-primary',
            self::Delivered => 'bg-success',
            self::Cancelled => 'bg-dark',
        };
    }
}
