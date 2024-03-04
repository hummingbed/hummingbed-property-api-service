<?php

namespace App\Enums;

enum PropertyStatusEnum: string
{
    case SOLD = 'sold';
    case SALE = 'on sale';
    case HOLD = 'on hold';
}