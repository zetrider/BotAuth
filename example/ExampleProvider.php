<?php

namespace App\Helpers\BotAuth;

use ZetRider\BotAuth\AbstractProvider;

class ExampleProvider extends AbstractProvider
{
    /**
     * Provider slug
     *
     * @var string
     */
    protected $provider_slug = 'example';

    /**
     * Get Provider Slug
     *
     * @return string
     */
    public function getProviderSlug()
    {
        return $this->provider_slug;
    }

    /**
     * Get Callback Response From Bot
     *
     * @return array
     */
    public function getCallbackResponse()
    {
        return [];
    }

    /**
     * Get the user
     *
     * @param  string  $token
     * @return array
     *         int array[id]
     *         string array[login]
     *         string array[first_name]
     *         string array[last_name]
     *         string array[photo]
     *         array  array[raw]
     */
    public function getUser()
    {
        return [];
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return '';
    }

    /**
     * Send answer
     *
     * @param  string $message
     * @return array
     */
    public function sendMessage($message)
    {
        return null;
    }

    /**
     * Callback
     *
     * @return exception|self
     */
    public function callback()
    {
        dd('Hi');
        return $this;
    }
}
