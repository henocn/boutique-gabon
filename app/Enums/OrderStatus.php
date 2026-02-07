<?php

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case Processed = 'processed';
    case Unreachable = 'unreachable';
    case Validated = 'validated';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
}
