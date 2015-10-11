<?php
class User {
    private $username;
    private $authToken;
    private $id;

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setAuthToken($authToken) {
        $this->authToken = $authToken;
    }

    public function getAuthToken() {
        return $this->authToken;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function dump() {
        return $this->username . ' - ' . $this->authToken . ' - ' . $this->id;
    }
}