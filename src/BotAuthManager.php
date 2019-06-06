<?php

namespace ZetRider\BotAuth;

use ZetRider\BotAuth\Providers\VkontakteProvider;
use ZetRider\BotAuth\Providers\TelegramProvider;
use ZetRider\BotAuth\Providers\FacebookProvider;

use InvalidArgumentException;
use Illuminate\Support\Manager;

class BotAuthManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No driver.');
    }

    /**
     * Create Vkontekte driver.
     *
     * @return ZetRider\BotAuth\Providers\VkontakteProvider
     */
    protected function createVkontakteDriver()
    {
        $config = config('botauth.vkontakte') ?? [];

        $api_secret      = $config['api_secret'] ?? '';
        $api_token       = $config['api_token'] ?? '';
        $api_confirm     = $config['api_confirm'] ?? '';
        $api_user_fields = $config['api_user_fields'] ?? [];

        return new VkontakteProvider($api_secret, $api_token, $api_confirm, $api_user_fields);
    }

    /**
     * Create Telegram driver.
     *
     * @return ZetRider\BotAuth\Providers\TelegramProvider
     */
    protected function createTelegramDriver()
    {
        $config = config('botauth.telegram') ?? [];

        $api_token = $config['api_token'] ?? '';
        $proxy     = $config['proxy'] ?? '';

        return new TelegramProvider($api_token, $proxy);
    }

    /**
     * Create Facebook driver.
     *
     * @return ZetRider\BotAuth\Providers\FacebookProvider
     */
    protected function createFacebookDriver()
    {
        $config = config('botauth.facebook') ?? [];

        $api_secret      = $config['api_secret'] ?? '';
        $api_token       = $config['api_token'] ?? '';
        $api_confirm     = $config['api_confirm'] ?? '';
        $api_user_fields = $config['api_user_fields'] ?? [];

        return new FacebookProvider($api_secret, $api_token, $api_confirm, $api_user_fields);
    }

}
