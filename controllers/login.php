<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
    public function index() {
        $state = bin2hex(random_bytes(8));
        $this->session->set_userdata('state', $state);
        if ($this->input->get('return_url'))
            $this->session->set_userdata('return_url', $this->input->get('return_url'));
        else
            $this->session->set_userdata('return_url', '');
        $link = $this->deviantartapi->authorizationUrl($state);
        redirect($link);
        die();
    }

    public function apireturn() {
        if ($this->input->get('error') !== false && $this->input->get('code') === false) {
            $this->session->set_userdata('error_message', "In order to use the site, you'll need to log in with DeviantArt and accept the access request.");
            redirect($this->session->userdata('return_url'));
            die();
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
                if ($result['responseCode'] >= 400) {
                    throw new Exception();
                }
                usleep(500000);
                $watching = $this->deviantartapi->userIsWatching('pillowing-pile');
            } catch (DeviantartRateLimitException $e) {
                $this->session->set_userdata('error_message', "Sorry, it looks like DeviantArt has gotten overloaded with our requests. Please try again in about ten minutes.");
                redirect($this->session->userdata('return_url'));
                die();
            } catch (Exception $e) {
                $this->session->set_userdata('error_message', "Sorry, it looks like there was a problem logging you in. Please try logging in again in a few minutes.");
                redirect($this->session->userdata('return_url'));
                die();
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

            $user->setTokenIssued($this->deviantartapi->getTokenIssued()->format('Y-m-d H:i:s'));

            try {
                $this->userdao->save($user);
            } catch (Exception $e) {
                $this->session->set_userdata('error_message', "Sorry, it looks like there was a problem connecting up to DeviantArt. Please try logging in again in a few minutes.");
                redirect($this->session->userdata('return_url'));
                die();
            }

            $this->session->set_userdata('username', $username);
            $this->session->set_userdata('uuid', $uuid);
            $this->session->set_userdata('usericon', $usericon);
            $this->session->set_userdata('is_watching', $watching);
            $this->session->set_userdata('ip_address', $user->getIpAddress());
            redirect($this->session->userdata('return_url'));
            die();
        } elseif ($this->session->userdata('state') !== false && $this->session->userdata('state') != $this->input->get('state')) {
            $this->session->set_userdata('error_message', "Sorry, it looks like there was a problem connecting up to DeviantArt. Please try logging in again in a few minutes.");
            redirect('');
            die();
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        if ($this->input->get('return_url') !== false)
            redirect($this->input->get('return_url'));
        else
            redirect('');
        die();
    }
}