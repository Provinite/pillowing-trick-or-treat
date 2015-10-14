<?php
class User {
    private $uuid = null;
    private $username = null;
    private $authCode = null;
    private $accessToken = null;
    private $refreshToken = null;
    private $tokenIssued = null;
    private $ipAddress = null;

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setTokenIssued($accessTokenIssued)
    {
        $this->tokenIssued = $accessTokenIssued;
    }

    public function getTokenIssued()
    {
        return $this->tokenIssued;
    }

    public function setAuthCode($authCode)
    {
        $this->authCode = $authCode;
    }

    public function getAuthCode()
    {
        return $this->authCode;
    }

    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getUuid() {
        return $this->uuid;
    }

    public function setUuid($uuid) {
        $this->uuid = $uuid;
    }
}