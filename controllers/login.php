<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
    public function index() {
        $state = bin2hex(random_bytes(8));
        $this->session->set_userdata('state', $state);
        if ($this->input->get('return_url'))
            $this->session->set_userdata('return_url', $this->input->get('return_url'));
        else
            $this->session->set_userdata('return_url', 'test');
        $link = $this->deviantartapi->authorizationUrl($state);
        redirect($link);
        die();
    }

    public function apireturn() {
        if ($this->input->get('error') !== false && $this->input->get('code') === false) {
            //user declined
        } elseif ($this->session->userdata('state') == $this->input->get('state') &&
            $this->input->get('state') != '' &&
            $this->input->get('state') !== false &&
            $this->input->get('code') !== false) {
            $result = array();
            try {
                $this->deviantartapi->setAuthCode($this->input->get('code'));
                $this->deviantartapi->requestToken();
                $result = $this->deviantartapi->whoami();
            } catch (Exception $e) {
                //there was a problem connecting to DA. Let the user know nicely
            }
            $username = $result['result']->username;
            $uuid = $result['result']->userid;
            $usericon = $result['result']->usericon;

            $user = new User();
            $user->setUuid($uuid);
            $user->setUsername($username);
            $user->setIcon($usericon);
            if ($this->input->valid_ip($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $user->setIpAddress($_SERVER["HTTP_X_FORWARDED_FOR"]);
            } else {
                $user->setIpAddress("0.0.0.0");
            }
            $user->setAccessToken($this->deviantartapi->getAccessToken());
            $user->setAuthCode($this->deviantartapi->getAuthCode());
            $user->setRefreshToken($this->deviantartapi->getRefreshToken());

            $this->userdao->save($user);

            $this->session->set_userdata('username', $username);
            $this->session->set_userdata('uuid', $uuid);
            $this->session->set_userdata('usericon', $usericon);
            redirect($this->session->userdata('return_url'));
            die();
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        if ($this->input->get('return_url') !== false)
            redirect($this->input->get('return_url'));
        else
            redirect('test');
        die();
    }

    public function apicheck() {
        echo "<pre>";
        print_r($this->session->all_userdata());
        echo "\n\n";
        print_r($this->userdao->get($this->session->userdata('uuid')));
    }
}