<?php

namespace ZetRider\BotAuth\Providers;

use ZetRider\BotAuth\AbstractProvider;

class TelegramProvider extends AbstractProvider
{
    /**
     * Provider slug
     *
     * @var string
     */
    protected $provider_slug = 'telegram';

    /**
     * TG Api Endpoint
     *
     * @var string
     */
    protected $api_endpoint;

    /**
     * TG Api Token
     *
     * @var string
     */
    protected $api_token;

    /**
     * TG Api Token
     *
     * @var string
     */
    protected $proxy;

    /**
     * Callback Response
     *
     * @var array
     */
    protected $callback_response;

    /**
     * User data
     *
     * @var array
     */
    protected $user_data;

    /**
     * Create
     *
     * @return void
     */
    public function __construct($api_token, $proxy)
    {
        $this->api_endpoint      = 'https://api.telegram.org/bot';
        $this->api_endpoint_file = 'https://api.telegram.org/file/bot';
        $this->api_token         = $api_token;
        $this->proxy             = $proxy;
        $this->callback_response = [];
        $this->user_data         = [];
    }

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
     * Get Api Token
     *
     * @return string
     */
    protected function getApiToken()
    {
        return $this->api_token;
    }
    /**
     * Get Proxy
     *
     * @return string|array
     */
    protected function getProxy()
    {
        return $this->proxy;
    }

    /**
     * Get Api Endpoint
     *
     * @return string
     */
    protected function getApiEndpoint()
    {
        return $this->api_endpoint.$this->getApiToken();
    }

    /**
     * Get Api Endpoint File
     *
     * @return string
     */
    protected function getApiEndpointFile()
    {
        return $this->api_endpoint_file.$this->getApiToken();
    }

    /**
     * Get User Data
     *
     * @return array
     */
    protected function getUserData()
    {
        return $this->user_data;
    }

    /**
     * Get Callback Response From Bot
     *
     * @return array
     */
    public function getCallbackResponse()
    {
        if (is_array($this->callback_response) and !empty($callback_response))
        {
            return $callback_response;
        }

        $data  = [];
        $input = file_get_contents('php://input');

        if ($input and $json = json_decode($input, true))
        {
            $data = $json;
        }

        $this->callback_response = $data;

        return $data;
    }

    /**
     * Send request to api
     *
     * @param  string $method https://vk.com/dev/methods
     * @param  array $params of method
     * @return array
     */
    protected function apiRequest($method, $params = [])
    {
        $endpoint = $this->getApiEndpoint() . '/' . $method;
        $response = $this->getHttpClient()->request('POST', $endpoint, [
            $this->getGuzzlePostKey() => $params
        ]);

        return $this->getGuzzleResponseArray($response);
    }

    /**
     * Send answer
     *
     * @param  string $message
     * @return array
     */
    public function sendMessage($message)
    {
        $data    = $this->getCallbackResponse();
        $chat_id = $data['message']['chat']['id'] ?? 0;

        return $this->apiRequest('sendMessage', [
            'chat_id' => $chat_id,
            'text'    => $message,
        ]);
    }

   /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        $data = $this->getCallbackResponse();

        if (isset($data['message']['text']))
        {
            return $data['message']['text'];
        }

        return '';
    }

    /**
     * Get user photo
     *
     * @return string
     */
    protected function apiGetUserPhoto($user_id)
    {
        $url = '';
        $get = $this->apiRequest('getUserProfilePhotos', ['user_id' => $user_id]);

        $file_id = $get['result']['photos']['0']['0']['file_id'] ?? null;

        if ($file_id)
        {
            $get  = $this->apiRequest('getFile', ['file_id' => $file_id]);
            $path = $get['result']['file_path'] ?? '';

            if (!empty($path))
            {
                $url = $this->getApiEndpointFile() . '/' . $path;
            }
        }

        return $url;
    }

    /**
     * Set User Data
     *
     * @return array
     */
    protected function setUserData()
    {
        $data = $this->getCallbackResponse();

        if (count($data))
        {
            $this->user_data = $data['message']['from'] ?? [];
        }

        return $this->user_data;
    }

    /**
     * Get User Data
     *
     * @return array
     */
    public function getUser()
    {
        $user = [];
        $data = $this->getUserData();

        if (count($data))
        {
            $user['id']         = $data['id'] ?? 0;
            $user['login']      = $data['username'] ?? '';
            $user['first_name'] = $data['first_name'] ?? '';
            $user['last_name']  = $data['last_name'] ?? '';
            $user['photo']      = $this->apiGetUserPhoto($data['id']);
            $user['raw']        = $data;
        }

        return $user;
    }

    /**
     * Callback event message_new
     *
     * @return void
     */
    protected function callbackEventMessageNew()
    {
        $this->setUserData();
        $this->eventUserFound();
    }

    /**
     * Callback
     *
     * @return exception|self
     */
    public function callback()
    {
        $data = $this->getCallbackResponse();

        $this->callbackEventMessageNew();

        return $this;
    }
}
