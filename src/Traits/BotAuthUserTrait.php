<?php

namespace ZetRider\BotAuth\Traits;

use ZetRider\BotAuth\Models\BotAuth as BotAuthModel;

trait BotAuthUserTrait
{
    /**
     * botAuth logins
     * @return App\BotAuth
     */
    public function botAuth() {
        return $this->hasMany(BotAuthModel::class);
    }
}