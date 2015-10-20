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
            $watching = false;
            try {
                $this->deviantartapi->setAuthCode($this->input->get('code'));
                $this->deviantartapi->requestToken();
                usleep(500000);
                $result = $this->deviantartapi->whoami();
                usleep(500000);
                $watching = $this->deviantartapi->userIsWatching('pillowing-pile');
            } catch (Exception $e) {
                //there was a problem connecting to DA. Let the user know nicely
                die("Is big problem.");
            }
            $username = $result['result']->username;
            $uuid = $result['result']->userid;
            $usericon = $result['result']->usericon;

            $user = new User();
            $user->setUuid($uuid);
            $user->setUsername($username);
            $user->setIcon($usericon);
            $user->setIsWatching($watching);

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
            $this->session->set_userdata('is_watching', $watching);
            $this->session->set_userdata('ip_address', $user->getIpAddress());
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
}