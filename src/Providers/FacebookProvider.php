<?php

namespace ZetRider\BotAuth\Providers;

use ZetRider\BotAuth\AbstractProvider;

class FacebookProvider extends AbstractProvider
{
    /**
     * Provider slug
     *
     * @var string
     */
    protected $provider_slug = 'facebook';

    /**
     * FB Api Version
     *
     * @var string
     */
    protected $api_version;

    /**
     * FB Api Endpoint
     *
     * @var string
     */
    protected $api_endpoint;

    /**
     * FB Api Secret
     *
     * @var string
     */
    protected $api_secret;

    /**
     * FB Api Token
     *
     * @var string
     */
    protected $api_token;

    /**
     * FB Api Confirm
     *
     * @var string
     */
    protected $api_confirm;

    /**
     * FB Api User Fields
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
        $this->api_version       = 'v3.3';
        $this->api_endpoint      = 'https://graph.facebook.com';
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
     * Get Api Version
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
     * Get Api Confirm
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
            'name',
            'first_name',
            'last_name',
            'profile_pic',
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
        return $this->api_endpoint . '/' . $this->getApiVersion();
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
     * Get User Data
     *
     * @return array
     */
    protected function getUserData()
    {
        return $this->user_data;
    }

    /**
     * Check secret key
     *
     * @param  array $data from callback
     * @return bool
     */
    protected function isAllowedRequest($data)
    {
        $headers            = getallheaders();
        $header_signature   = $headers['X-Hub-Signature'] ?? '';
        $raw_post_data      = file_get_contents('php://input');
        $expected_signature = hash_hmac('sha1', $raw_post_data, $this->getApiSecret());
        $signature          = '';

        if (strlen($header_signature) == 45 AND substr($header_signature, 0, 5) == 'sha1=')
        {
            $signature = substr($header_signature, 5);
        }

        return hash_equals($signature, $expected_signature);
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
        $params['access_token'] = $this->getApiToken();

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
        $data = $this->getCallbackResponse();

        $messaging   = $data['entry'][0]['messaging'][0];
        $sender_psid = $messaging['sender']['id'];

        return $this->apiRequest('me/messages', [
            'recipient' => [
                'id' => $sender_psid
            ],
            'message' => [
                'text' => $message,
            ],
        ]);
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        $data      = $this->getCallbackResponse();
        $messaging = $data['entry'][0]['messaging'][0];

        if (isset($messaging['message']['text']))
        {
            return $messaging['message']['text'];
        }

        return '';
    }

    /**
     * Set User Data
     *
     * @return array
     */
    protected function setUserData()
    {
        $data        = $this->getCallbackResponse();
        $messaging   = $data['entry'][0]['messaging'][0];
        $sender_psid = $messaging['sender']['id'];

        $params = [
            'fields' => implode(',', $this->getApiUserFields()),
            'access_token' => $this->getApiToken(),
        ];
        $endpoint = $this->getApiEndpoint() . '/' . $sender_psid . '?' . http_build_query($params);
        $response = $this->getHttpClient()->request('GET', $endpoint);
        $user     = $this->getGuzzleResponseArray($response);

        if (count($user))
        {
            $this->user_data = $user;
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
            $user['id']         = $data['id'] ?? 0; // it's PSID! not public id. TODO
            $user['login']      = '';
            $user['first_name'] = $data['first_name'] ?? '';
            $user['last_name']  = $data['last_name'] ?? '';
            $user['photo']      = $data['profile_pic'] ?? '';
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
        if ($_REQUEST['hub_verify_token'] == $this->getApiConfirm())
        {
            echo $_REQUEST['hub_challenge'];
        }
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

        $hub_mode = $_REQUEST['hub_mode'] ?? '';
        $object   = $data['object'] ?? '';

        // Need get method
        if ($hub_mode == 'subscribe')
        {
            $this->callbackEventConfirmation();
        }
        elseif (!$this->isAllowedRequest($data))
        {
            Abort(401);
        }
        elseif ($object == 'page')
        {
            $this->callbackEventMessageNew();
        }

        return $this;
    }
}
