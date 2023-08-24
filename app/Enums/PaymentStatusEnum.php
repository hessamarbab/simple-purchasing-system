<?php

namespace App\Enums;

enum PaymentStatusEnum:string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
}
