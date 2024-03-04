<?php

namespace App\Enums;

enum PropertyTypeEnum: string
{
    case SELF_CONTAINED = 'self-contained';
    case ONE_BEDROOM_FLAT = 'one-bedroom-flat';
    case TWO_BEDROOM_FLAT = 'two-bedroom-flat';
    case THREE_BEDROOM_FLAT = 'three-bedroom-flat';
    case BUNGALOW = 'bungalow';
    case DUPLEX = 'duplex';
}