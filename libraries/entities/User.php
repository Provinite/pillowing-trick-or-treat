<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User implements iEntity {

    private $uuid = null;
    private $username = null;
    private $authCode = null;
    private $accessToken = null;
    private $refreshToken = null;
    private $tokenIssued = null;
    private $ipAddress = null;
    private $icon = null;
    private $isWatching = null;

    public function getIdField() {
        return "uuid";
    }

    public function getId() {
        return $this->getUuid();
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function setIsWatching($isWatching)
    {
        $this->isWatching = $isWatching;
    }

    public function getIsWatching()
    {
        return $this->isWatching;
    }



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