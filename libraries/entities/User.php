<?php
class User {
    private $username;
    private $authToken;
    private $uuid;

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

    public function setAuthToken($authToken) {
        $this->authToken = $authToken;
    }

    public function getAuthToken() {
        return $this->authToken;
    }

    public function dump() {
        return $this->username . ' - ' . $this->authToken . ' - ' . $this->id;
    }
}