<?php

namespace ZetRider\BotAuth;

use ZetRider\BotAuth\Events\MessageNewEvent;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

abstract class AbstractProvider
{
    /**
     * Http Client
     *
     * @var GuzzleHttp\Client
     */
    protected $http_client;

    /**
     * Provider slug
     *
     * @return string
     */
    abstract public function getProviderSlug();

    /**
     * Get Callback Response From Bot
     *
     * @return array
     */
    abstract public function getCallbackResponse();

    /**
     * Get the user
     *
     * @return array
     *         int array[id]
     *         string array[login]
     *         string array[first_name]
     *         string array[last_name]
     *         string array[photo]
     *         array  array[raw]
     */
    abstract public function getUser();

    /**
     * Get text
     *
     * @return string
     */
    abstract public function getText();

    /**
     * Send answer
     *
     * @param  string $message
     * @return string
     */
    abstract public function sendMessage($message);

    /**
     * Callback
     *
     * @return exception|self
     */
    abstract public function callback();

    /**
     * User found
     *
     * @return string
     */
    protected function eventUserFound() {
        event(new MessageNewEvent($this));
    }

    /**
     * Get Http Client
     *
     * @return GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        if (!$this->http_client)
        {
            $params = [
                'http_errors' => false,
            ];
            if (method_exists($this, 'getProxy') and !empty($this->getProxy()))
            {
                $params['proxy'] = $this->getProxy();
            }
            $this->http_client = new Client($params);
        }

        return $this->http_client;
    }

    /**
     * Get Gizzle Post Key
     *
     * @return string
     */
    protected function getGuzzlePostKey()
    {
        return (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';
    }

    /**
     * Get Gizzle Post Key
     *
     * @param  Guzzle response $response
     * @return array
     */
    protected function getGuzzleResponseArray($response)
    {
        $body = (String) $response->getBody();
        return json_decode($body, true) ?? [];
    }
}
