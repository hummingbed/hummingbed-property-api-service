<?php

namespace App\Helpers;

use Carbon\Carbon;

class Utils
{
    public static function getCurrentDatetime(): string
    {
        $now = Carbon::now('Africa/Lagos');
        return $now->toDateTimeString();
    }
}