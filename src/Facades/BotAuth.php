<?php

namespace ZetRider\BotAuth\Facades;

use Illuminate\Support\Facades\Facade;

class BotAuth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'BotAuth';
    }
}