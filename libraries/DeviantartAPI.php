<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('DEVIANTART_CODE_SUCCESS', '200');
define('DEVIANTART_CODE_CLIENT_ERROR', '400');
define('DEVIANTART_CODE_RATE_LIMIT_EXCEEDED', '429');
define('DEVIANTART_CODE_SERVER_ERROR', '500');
define('DEVIANTART_CODE_MAINTENANCE', '503');

class DeviantartAPI {

    private $apiBaseUrl;
    private $apiClientSecret;
    private $apiClientId;
    private $apiScope;

    private $accessToken = null;
    private $refreshToken = null;
    private $authCode = null;


    public function __construct($data) {
        $this->apiBaseUrl = $data['api_base'];
        $this->apiClientSecret = $data['oauth_secret'];
        $this->apiClientId = $data['oauth_client_id'];
        $this->apiScope = $data['oauth_default_scope'];
    }

    //Setter for the oauth access token
    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;
    }

    //Setter for the oauth auth code
    public function setAuthCode($authCode) {
        $this->authCode = $authCode;
    }

    //helper function, check if we have a token at all
    private function hasToken() {
        return $this->accessToken == null;
    }

    //helper function, check if our connection and token are working
    public function placebo() {

    }

    //helper function, makes an API call. Automatically refreshes tokens when appropriate
    private function makeCall($url) {

    }


    public function refreshToken() {

    }

    //create an authorization url to send our user out to
    public function authorizationUrl($returnUri, $state) {
        return DeviantartAPI::$apiBaseUrl .
            'authorize?response_type=code' . '
            &scope=' . DeviantartAPI::$apiScope .
            '&client_id=' . DeviantartAPI::$apiClientId .
            '&redirect_uri=' . $returnUri .
            '&state=' & $state;
    }

}

/* end of file DeviantartAPI.php */