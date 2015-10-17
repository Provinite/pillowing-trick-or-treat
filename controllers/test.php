<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

    public function apientry() {
        $this->session->set_userdata('state', 'myState');
        echo "<pre>Auth URL: " . $this->deviantartapi->authorizationUrl('myState') . "\n";
        echo '<a href="' . $this->deviantartapi->authorizationUrl('myState') . '">Auth Link</a>' . "\n";
    }

    public function apireturn() {
        $code = $this->input->get('code');
        $this->deviantartapi->setAuthCode($code);
        $this->deviantartapi->requestToken();
        $result = $this->deviantartapi->whoami();
        $result = $result['result'];
        echo "<pre>";
        echo "\n";
        echo "Logged in as: <img src=\"" . $result->usericon . "\" />" . $result->username;
        echo "\n";
        print_r($result);
    }

    public function apiview() {
        echo "<pre>";
        echo "Watching? ";
        print_r($this->deviantartapi->userIsWatching("pillowing-pile"));
    }

    public function populateJunkData() {

    }

}

/* end of file test.php */
/* location ./trickortreat/controllers/test.php */