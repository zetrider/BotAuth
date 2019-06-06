<?php

namespace ZetRider\BotAuth\Providers;

use ZetRider\BotAuth\AbstractProvider;

class VkontakteProvider extends AbstractProvider
{
    /**
     * Provider slug
     *
     * @var string
     */
    protected $provider_slug = 'vkontakte';

    /**
     * VK Api Version
     *
     * @var string
     */
    protected $api_version;

    /**
     * VK Api Endpoint
     *
     * @var string
     */
    protected $api_endpoint;

    /**
     * VK Api Secret
     *
     * @var string
     */
    protected $api_secret;

    /**
     * VK Api Token
     *
     * @var string
     */
    protected $api_token;

    /**
     * VK Server Confirm Code
     *
     * @var string
     */
    protected $api_confirm;

    /**
     * VK Api User Fields
     *
     * @var array
     */
    protected $api_user_fields;

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
    public function __construct($api_secret, $api_token, $api_confirm, $api_user_fields)
    {
        $this->api_version       = '5.95';
        $this->api_endpoint      = 'https://api.vk.com/method';
        $this->api_secret        = $api_secret;
        $this->api_token         = $api_token;
        $this->api_confirm       = $api_confirm;
        $this->api_user_fields   = $api_user_fields;
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
     * Get Api Verson
     *
     * @return string
     */
    protected function getApiVersion()
    {
        return $this->api_version;
    }

    /**
     * Get Api Secret
     *
     * @return string
     */
    protected function getApiSecret()
    {
        return $this->api_secret;
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
     * Get Api Confirm Code
     *
     * @return string
     */
    protected function getApiConfirm()
    {
        return $this->api_confirm;
    }

    /**
     * Get Api User Fields
     *
     * @return string
     */
    protected function getApiUserFields()
    {
        $fields    = $this->api_user_fields ?? [];
        $important = [
            'id',
            'screen_name',
            'first_name',
            'last_name',
            'photo_max_orig',
        ];

        return array_unique(array_merge($fields, $important));
    }

    /**
     * Get Api Endpoint
     *
     * @return string
     */
    protected function getApiEndpoint()
    {
        return $this->api_endpoint;
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
     * Check secret key
     *
     * @param  array $data from callback
     * @return bool
     */
    protected function isAllowedRequest($data)
    {
        return (!empty($data) and $data['secret'] == $this->getApiSecret());
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
        $params['v'] = $this->getApiVersion();
        $params['access_token'] = $this->getApiToken();

        $endpoint = $this->getApiEndpoint() . '/' . $method;
        $response = $this->getHttpClient()->request('POST', $endpoint, [
            $this->getGuzzlePostKey() => $params
        ]);

        return $this->getGuzzleResponseArray($response);
    }

    /**
     * Send message
     *
     * @param  string $message
     * @return array
     */
    public function sendMessage($message)
    {
        $data    = $this->getCallbackResponse();
        $peer_id = $data['object']['peer_id'];

        return $this->apiRequest('messages.send', [
            'peer_id'   => $peer_id,
            'message'   => $message,
            'random_id' => str_replace('.', '', microtime(true)),
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

        if (isset($data['object']['text']))
        {
            return $data['object']['text'];
        }

        return '';
    }

    /**
     * Get user
     *
     * @return array
     */
    protected function apiGetUser($user_id)
    {
        $get = $this->apiRequest('users.get', [
            'user_ids' => $user_id,
            'fields'   => implode(',', $this->getApiUserFields()),
            'language' => config('app.locale', 'en'),
        ]);
        $response = $get['response'] ?? [];

        if (!empty($response) and is_array($response[0]))
        {
            return $response[0];
        }

        return [];
    }

    /**
     * Set User Data
     *
     * @param  int $user_id
     * @return array
     */
    protected function setUserData($user_id)
    {
        $data = $this->apiGetUser($user_id);

        if (count($data))
        {
            $this->user_data = $data;
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
            $user['login']      = $data['screen_name'] ?? '';
            $user['first_name'] = $data['first_name'] ?? '';
            $user['last_name']  = $data['last_name'] ?? '';
            $user['photo']      = $data['photo_max_orig'] ?? '';
            $user['raw']        = $data;
        }

        return $user;
    }

    /**
     * Callback event confirmation
     * @return void
     */
    protected function callbackEventConfirmation()
    {
        echo $this->getApiConfirm();
    }

    /**
     * Callback event message_new
     *
     * @return void
     */
    protected function callbackEventMessageNew()
    {
        $data    = $this->getCallbackResponse();
        $from_id = $data['object']['from_id'];

        $this->setUserData($from_id);
        $this->eventUserFound();

        echo 'ok';
    }

    /**
     * Callback
     *
     * @return exception|self
     */
    public function callback()
    {
        $data = $this->getCallbackResponse();

        if (!$this->isAllowedRequest($data))
        {
            Abort(401);
        }

        $event = $data['type'];

        switch ($event)
        {
            case 'confirmation':
                $this->callbackEventConfirmation();
                break;
            case 'message_new':
                $this->callbackEventMessageNew();
                break;
            default:
                Abort(406, 'Unsupported event');
                break;
        }

        return $this;
    }
}
