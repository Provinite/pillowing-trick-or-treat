<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//HTTP response codes
define('DEVIANTART_CODE_SUCCESS', '200');
define('DEVIANTART_CODE_CLIENT_ERROR', '400');
define('DEVIANTART_CODE_RATE_LIMIT_EXCEEDED', '429');
define('DEVIANTART_CODE_SERVER_ERROR', '500');
define('DEVIANTART_CODE_MAINTENANCE', '503');

//Error strings
define('DEVIANTART_ERR_STR_INVALID', 'invalid_request');    //general error
define('DEVIANTART_ERR_STR_UNAUTHORIZED', 'unauthorized');  //access denied to endpoint or resource(s)
define('DEVIANTART_ERR_STR_UNVERIFIED', 'unverified');      //email address must be verified to access endpoint
define('DEVIANTART_ERR_STR_SERVER', 'server_error');        //server error, try again later
define('DEVIANTART_ERR_STR_VERSION', 'version_error');      //the requested version does not exist
class DeviantartAPI {

    //Configuration members
    private $apiBaseUrl;
    private $apiAuthBaseUrl;
    private $apiClientSecret;
    private $apiClientId;
    private $apiScope;
    private $maxRetries;
    private $retryDelay;
    private $tokenSource;
    private $returnUri;
    private $lastCall = null;

    //oauth members
    private $accessToken = null;
    private $refreshToken = null;
    private $authCode = null;
    private $tokenIssued = null;

    //our CI instance
    private $CI;

    public function getAccessToken() {
        return $this->accessToken;
    }

    public function getRefreshToken() {
        return $this->refreshToken;
    }

    public function getAuthCode() {
        return $this->authCode;
    }

    public function getTokenIssued() {
        return $this->tokenIssued;
    }

    public function __construct($data) {
        $this->apiBaseUrl = $data['api_base'];
        $this->apiClientSecret = $data['oauth_secret'];
        $this->apiClientId = $data['oauth_client_id'];
        $this->apiScope = $data['oauth_default_scope'];
        $this->maxRetries = $data['max_retry_attempts'];
        $this->retryDelay = $data['retry_delay'];
        $this->tokenSource = $data['token_source'];
        $this->returnUri = $data['return_uri'];
        $this->apiAuthBaseUrl = $data['api_auth_base'];

        $this->CI =& get_instance();

        if ($data['token_source'] == 'session') {
            $CI =& get_instance();
            $this->accessToken = $this->CI->session->userdata('deviantart_api_access_token');
            $this->refreshToken = $this->CI->session->userdata('deviantart_api_refresh_token');
            $this->authCode = $this->CI->session->userdata('deviantart_api_authorization_code');
            $this->tokenIssued = $this->CI->session->userdata('deviantart_api_token_issed');

            if ($this->accessToken === false) {
                $this->accessToken = null;
            }
            if ($this->refreshToken === false) {
                $this->refreshToken = null;
            }
            if ($this->authCode === false) {
                $this->authCode = null;
            }
            if ($this->tokenIssued === false) {
                $this->tokenIssued = null;
            }
        }
    }

    //Setter for the oauth access token
    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;

        if ($this->tokenSource == 'session') {
            $this->CI->session->set_userdata('deviantart_api_access_token', $accessToken);
        }
    }

    public function setRefreshToken($refreshToken) {
        $this->refreshToken = $refreshToken;

        if ($this->tokenSource == 'session') {
            $this->CI->session->set_userdata('deviantart_api_refresh_token', $refreshToken);
        }
    }

    //Setter for the oauth auth code
    public function setAuthCode($authCode) {
        $this->authCode = $authCode;

        if ($this->tokenSource == 'session') {
            $this->CI->session->set_userdata('deviantart_api_authorization_code', $authCode);
        }
    }

    public function setTokenIssued($tokenIssued) {
        $this->tokenIssued = $tokenIssued;
        if ($this->tokenSource == 'session') {
            $this->CI->session->set_userdata('deviantart_api_token_issed', $tokenIssued);
        }
    }

    /**
     * @return bool Whether or not an access token is available
     */
    private function hasToken() {
        return $this->accessToken !== null;
    }

    /**
     * @return bool Whether or not a refresh token is available
     */
    private function hasRefreshToken() {
        return $this->refreshToken !== null;
    }

    /**
     * @return bool Whether or not an authorization code is available
     */
    private function hasAuthCode() {
        return $this->authCode !== null;
    }

    public function placebo() {
        $result = $this->makeCall("placebo", "GET", array(), false, true);
        return (!$this->isError($result) && $result['result']->status == "success");
    }

    public function getLastCall() {
        return $this->lastCall;
    }

    /**
     * @param string $endpoint
     * @param string $method
     * @param array $payload
     * @param bool $validateToken
     * @param bool $sendToken
     * @param bool $sendClientId
     * @param bool $sendClientSecret
     * @param bool $isAuth
     * @return array 'response_code' => the HTTP response code, 'result' => the deserialized JSON result
     * @throws NoAuthCodeException
     * @throws DeviantartConnectionException
     * @throws DeviantartRateLimitException
     * @throws AuthGenericException
     */
    public function makeCall($endpoint, $method, $payload = array(), $validateToken = true, $sendToken = true, $sendClientId = true, $sendClientSecret = true, $isAuth = false) {
        usleep(random_int(10000, 30000) * 100);
        if ($validateToken) {  //make a secure call, we'll need an auth token
            if (!$this->hasToken()) { //no token
                if (!$this->hasRefreshToken()) { //no refresh token
                    if (!$this->hasAuthCode()) { //no auth code
                        //we have no authentication credentials, fatal error, no way to proceed
                        throw new NoAuthCodeException("No authorization code available.");
                    } else {
                        //we have an auth code, try to get an access token
                        try {
                            $this->requestToken();
                        } catch (DeviantartConnectionException $e) {
                            throw $e;
                        } catch (NoAuthCodeException $e) {
                            throw $e;
                        }
                    }
                } else {
                    //we have a refresh token, use it
                    try {
                        $this->refreshToken();
                    } catch (AuthTokenRefreshException $e) {
                        //refresh failed, fatal error, no way to proceed
                        throw new NoAuthCodeException("Reauthorization required.");
                    }
                }
            } else { //have a token, check if its expired
                $cur = new DateTime();
                $cur->modify('-50 minutes');
                if ($this->tokenIssued < $cur) {
                    $this->refreshToken();
                }
            }
        }

        $ch = curl_init();

        if ($sendToken) {
            $payload['access_token'] = $this->accessToken;
        }
        if ($sendClientId) {
            $payload['client_id'] = $this->apiClientId;
        }
        if ($sendClientSecret) {
            $payload['client_secret'] = $this->apiClientSecret;
        }

        $url = '';
        if ($isAuth) {
            $url = $this->apiAuthBaseUrl . $endpoint;
        } else {
            $url = $this->apiBaseUrl . $endpoint;
        }

        $querystring = '';
        $lastCall = array('method' => 'GET');

        if (count($payload > 0)) {
            foreach ($payload as $key => $value) {
                $value = urlencode($value);
                $querystring = $querystring . $key . '=' . $value . '&';
            }
            $querystring = rtrim($querystring, '&');
            if ($method == "GET") {
                $url = $url . '?' . $querystring;
            }
            if ($method == "POST") {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $querystring);
                $lastCall['payload'] = $querystring;
            }
        }

        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
            $lastCall['method'] = 'POST';
        }

        $lastCall['attempt'] = 0;

        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_URL, $url);
        $lastCall['url'] = $url;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $lastCall['responseCode'] = $responseCode;
        $lastCall['data'] = $result;
        $this->lastCall = $lastCall;

        if ($responseCode == 429) {
            throw new DeviantartRateLimitException("Rate Limit Exceeded");
        }

        //if we run into problems, go ahead and retry with a delay
        if ($responseCode == 500 || curl_errno($ch)) {
            $attempt = 0;
            while ($attempt <= $this->maxRetries && ($responseCode == 500 || curl_errno($ch))) {
                usleep(1000000 * $this->retryDelay);
                $lastCall['attempt']++;
                $attempt++;
                $result = curl_exec($ch);
                $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                $lastCall['responseCode'] = $responseCode;
                $lastCall['data'] = $result;
                $this->lastCall = $lastCall;

            }
        }

        if ($responseCode == 500 || curl_errno($ch)) {
            curl_close($ch);
            throw new DeviantartConnectionException("Unknown connection error.");
        }

        if ($responseCode == 429) {
            throw new DeviantartRateLimitException("Rate Limit Exceeded");
        }


        curl_close($ch);

        return array('response_code' => $responseCode, 'result' => json_decode($result));
    }

    //helper function, attempt to refresh our token
    public function refreshToken() {
        $payload = array();

        $payload['grant_type'] = 'refresh_token';
        $payload['refresh_token'] = $this->refreshToken;

        $result = $this->makeCall("token", "POST", $payload, false, false, true, true, true);
        if (!$this->isError($result)) {
            $this->setAccessToken($result['result']->access_token);
            $this->setRefreshToken($result['result']->refresh_token);
            $this->setTokenIssued(new DateTime());
        }
    }

    //helper function, request a token with our auth code
    public function requestToken() {
        $payload = array();

        $payload['grant_type'] = 'authorization_code';
        $payload['code'] = $this->authCode;
        $payload['redirect_uri'] = $this->returnUri;

        $result = $this->makeCall("token", "POST", $payload, false, false, true, true, true);
        if (!$this->isError($result)) {
            $this->setAccessToken($result['result']->access_token);
            $this->setRefreshToken($result['result']->refresh_token);
            $this->setTokenIssued(new DateTime());
        } else {
            print_r($result);
            throw new AuthGenericException("Authorization failed");
        }
    }

    //create an authorization url to send our user out to for (re)authorization
    public function authorizationUrl($state) {
        return $this->apiAuthBaseUrl .
            'authorize?response_type=code' .
            '&scope=' . $this->apiScope .
            '&client_id=' . $this->apiClientId .
            '&redirect_uri=' . $this->returnUri .
            '&state=' . $state;
    }

    public function isError($result) {
        return $result['response_code'] >= 400;
    }

    public function whoami() {
        return $this->makeCall("user/whoami", "POST");
    }

    //check if a user is watching another
    public function userIsWatching($username) {
        $result = $this->makeCall("user/friends/watching/$username", "GET", array());
        if ($this->isError($result)) {
            throw new DeviantartConnectionException();
        }
        return $result['result']->watching;
    }

}

class NoAuthCodeException extends Exception { };
class DeviantartRateLimitException extends Exception { };
class AuthTokenRefreshException extends Exception { };
class AuthGenericException extends Exception { };
class DeviantartConnectionException extends Exception { };

/* end of file DeviantartAPI.php */