<?php

namespace App\Enums;

enum ListingTypeEnum: string
{

    case OPEN_LISTING = 'open listing';
    case SELL_LISTING = 'sell listing';
    case EXCLUSIVE_AGENCY_LISTING = 'exclusive agency listing';
    case NET_LISTING = 'net listing';
}