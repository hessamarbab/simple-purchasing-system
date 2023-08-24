<?php

namespace App\Enums;

enum OrderStatusEnum:string
{
    case RESERVED = 'reserved';
    case PERFORMED = 'performed';
    case FAILED = 'failed';
}
